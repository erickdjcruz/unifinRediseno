<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetSolicitudEtapaAnterior extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            //GET
            'retrieve' => array(
                'reqType' => 'GET',
                //request type
                'noLoginRequired' => false,
                'path' => array('getSolicitudEtapaPrevia','?'),
                //endpoint variables
                'pathVars' => array('method','id_solicitud'),
                'method' => 'get_etapa_previa',
                'shortHelp' => 'Servicio que obtiene la etapa anterior antes de la modificación de una solicitud',
                'longHelp' => '',
            ),
        );
    }

    public function get_etapa_previa($api, $args)
    {
        $id_solicitud = $args['id_solicitud'];

        try {
            global $app_list_strings, $db;
            // Inicialización de resultado
            $resultado = [];

            $query = "SELECT id_c,tct_etapa_ddw_c ,estatus_c FROM opportunities_cstm WHERE idsolicitud_c = '{$id_solicitud}';";

            //tct_etapa_ddw_c = 'R'
            
            /*
            tct_etapa_ddw_c = 
            'SI'=solicitud inicial
            'P' = integracion expediente
            'PA' =- CV- cancelada por vigencia 
            estatus_c = 'K'// cancelada
            estatus_c = 'CV'// cancelada por vigencia
            */
            $sol = $db->query($query); 

            $etapa = "";
            $subetapa = "";

            while ($row = $db->fetchByAssoc($sol)) {
                $etapa = $row['tct_etapa_ddw_c'];
                $subetapa = $row['estatus_c'];
            }

            //$GLOBALS['log']->fatal("etapa: " . $etapa.'   --- subetapa: '.$subetapa);
            $aplica = false;
            if($etapa == 'R') {
                $aplica = true;
            }else if (($etapa == 'SI' && $subetapa == 'K')||($etapa == 'P' && $subetapa == 'K')||($etapa == 'PA' && $subetapa == 'CV')){
                $aplica = true;
            }
            //$GLOBALS['log']->fatal("aplica: " . $aplica);
            if(!$aplica){
                $resultado['success'] = true;
                $resultado['id_solicitud'] = $id_solicitud;
                $resultado['etapa'] = null;
                $resultado['subetapa'] = null;
                $resultado['desc_etapa'] = null;
                $resultado['desc_subetapa'] = null;

            }else{

                //Generar consulta a BD
                $query = "SELECT
                (
                    SELECT before_value_string
                    FROM opportunities_audit
                    WHERE parent_id = 
                        (SELECT id_c FROM opportunities_cstm WHERE idsolicitud_c = '{$id_solicitud}')
                    AND field_name = 'tct_etapa_ddw_c'
                    ORDER BY date_created DESC
                    LIMIT 1
                ) AS etapa_anterior,
                (
                    SELECT before_value_string
                    FROM opportunities_audit
                    WHERE parent_id = 
                        (SELECT id_c FROM opportunities_cstm WHERE idsolicitud_c = '{$id_solicitud}')
                    AND field_name = 'estatus_c'
                    ORDER BY date_created DESC
                    LIMIT 1
                ) AS estatus_anterior;";
                
                $last_etapa = $db->query($query); 

                $etapa = "";
                $subetapa = "";
                $etapa_txt = "";
                $subetapa_txt = "";

                while ($row = $db->fetchByAssoc($last_etapa)) {
                    $etapa = $row['etapa_anterior'];
                    $subetapa = $row['estatus_anterior'];
                }

                if (isset($app_list_strings['tct_etapa_ddw_c_list'])) {
                    $etapa_txt = $app_list_strings['tct_etapa_ddw_c_list'][$etapa];
                }
                if (isset($app_list_strings['estatus_c_operacion_list'])) {

                    $subetapa_txt = $app_list_strings['estatus_c_operacion_list'][$subetapa];
                }

                $resultado['success'] = true;
                $resultado['id_solicitud'] = $id_solicitud;
                $resultado['etapa'] = ($etapa === "") ? null : $etapa;
                $resultado['subetapa'] = ($subetapa === "") ? null : $subetapa;
                $resultado['desc_etapa'] = ($etapa_txt === "") ? null : $etapa_txt;
                $resultado['desc_subetapa'] = ($subetapa_txt === "") ? null : $subetapa_txt;
            }
            
            return $resultado;

        } catch (Exception $e) {
            $resultado['success'] = false;
            $resultado['codeerror'] = 401;
            $resultado['messageerror'] = $e->getMessage();

            $GLOBALS['log']->fatal("Error: " . $e->getMessage());

            return $resultado;
        }
    }
}

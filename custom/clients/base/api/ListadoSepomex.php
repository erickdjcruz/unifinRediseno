<?php
/**
 * @author: Salvador Lopez
 * @comments: Obtener listado de sepomex con estructura de estados relacionados con ciudades y municipios
 */

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class ListadoSepomex extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'ListadoSepomexAPI' => array(
                'reqType' => 'GET',
                'noLoginRequired' => true,
                'path' => array('ListadoSepomex'),
                'pathVars' => array(''),
                'method' => 'getListadoSepomex',
                'shortHelp' => 'Obtiene información de Sepomex para recuperar lista de Estados, Ciudades y Municipios relacionados entre si',
            ),
            'retrieve_rango_cp' => array(
                'reqType' => 'GET',
                'noLoginRequired' => true,
                'path' => array('GetRangoCP', '?'),
                'pathVars' => array('module', 'cp'),
                'method' => 'getEstadoyRangoCP',
                'shortHelp' => 'Obtiene estado con base a los primeros 2 digitos del CP',
                'longHelp' => '',
            ),
        );
    }


    public function getListadoSepomex($api, $args)
    {
        global $db;
        $response = array();

        try
        {
            $data = array();
            $qSepomex = "SELECT estado, id_estado, ciudad, id_ciudad, municipio, id_municipio FROM dir_sepomex WHERE deleted = 0";

            $queryResult = $db->query($qSepomex);
            
            while ($row = $db->fetchByAssoc($queryResult)) {
                if( !isset( $data[$row['estado']] )){
                    $data[$row['estado']] = array( "id_estado"=> $row["id_estado"], "ciudades" => array() );
                }

                if( !isset( $data[$row['estado']]['ciudades'][$row['ciudad']] )){
                    $data[$row['estado']]['ciudades'][$row['ciudad']] = array( "id_ciudad"=> $row["id_ciudad"], "municipios" => array());
                }

                $municipios_ids = array_column($data[$row['estado']]['ciudades'][$row['ciudad']]['municipios'], 'id_municipio');

                if (!in_array($row['id_municipio'], $municipios_ids)) {
                    $data[$row['estado']]['ciudades'][$row['ciudad']]['municipios'][] = [
                        'municipio' => $row['municipio'],
                        'id_municipio' => $row['id_municipio']
                    ];
                }

            }

            $response['status'] = "OK";
            $response['detail'] = $data;

            return $response;

        }catch (Exception $e){
            $response['status'] = "ERROR";
            $response['detail'] = "Error: ".$e;
            
            return $response;
        }

    }

    public function getEstadoyRangoCP($api, $args){

        $cp = $args['cp'];

        $data = array();
  
        $query = "SELECT distinct estado, id_estado
        FROM dir_sepomex
        WHERE codigo_postal lIKE '{$cp}%'";
  
        $result = $GLOBALS['db']->query($query);
  
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $data[$row['id_estado']] = $row['estado'];
          
        }

        $response['status'] = "OK";
        $response['detail'] = $data;

        return $response;
  
  
  
      }

}

<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetEstatusWHRobina extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GetEstatusWHRobinaAPI' => array(
                'reqType' => 'GET',
                'noLoginRequired' => true,
                'path' => array('GetEstatusWHRobinaError'),
                'pathVars' => array('module'),
                'method' => 'consultaAuditoriaRobina',
                'shortHelp' => 'Obtiene si hubo error en tiempo para la consulta CIEC.',
            ),
        );
    }

    public function consultaAuditoriaRobina($api, $args)
    {
        global $db;
        $response = [];

        try {
            $id = isset($args['idCuenta']) ? $args['idCuenta'] : null;

            if (!empty($id)) {

                $selectError = "SELECT * FROM robina_auditoria_peticiones 
                WHERE rfc = ( 
                SELECT rfc_c FROM accounts_cstm WHERE id_c = '{$id}' ) 
                AND crm_status_process = 'Recibido error'
                ORDER BY date_entered DESC  LIMIT 1; ";

                $queryE = $db->query($selectError);

                if ($queryE->num_rows > 0) {
                    $response['Error'] = true;
                }else{
                    
                    $selectCuentaWH = "SELECT id 
                        FROM robina_auditoria_peticiones 
                        WHERE rfc = (
                            SELECT rfc_c 
                            FROM accounts_cstm 
                            WHERE id_c = '{$id}'
                        ) 
                        AND crm_status_process = 'Alerta' 
                        LIMIT 1; ";

                    $queryResult = $db->query($selectCuentaWH);

                    if ($queryResult->num_rows > 0) {
                        while ($row = $db->fetchByAssoc($queryResult)) {
                            $idwha = $row['id'];
                            $update = "UPDATE robina_auditoria_peticiones SET crm_status_process = 'Alerta Vista' WHERE id = '{$idwha}'";
                            $GLOBALS['log']->fatal($update);
                            $db->query($update);
                            $response['Alerta'] = true;
                        }
                    } else {
                        $response['Alerta'] = false;
                    }
                }
            } else {
                $response['Alerta'] = false;
            }

            return $response;

        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}

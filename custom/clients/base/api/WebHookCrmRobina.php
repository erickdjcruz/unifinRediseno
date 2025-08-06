<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class WebHookCrmRobina extends SugarApi
{

    public function registerApiRest()
    {
        return array(
            //POST
            'setInfoProcessRobina' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('WebHookCrmRobina'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'processDataRobina',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza inserciones de robina con proceso completado)',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
        );

    }

    public function processDataRobina($api, $args){
        global $sugar_config,$db;
        $GLOBALS['log']->fatal( "--------------- Respuesta robina webhook -----------" );
        $ticket = '';
        $resultado=[];
        //$GLOBALS['log']->fatal('payload: ');
        //$GLOBALS['log']->fatal(print_r($args, true));
        $jsonargs = json_encode($args, true);
        // Verifica si existe el campo 'status_code' en la estructura esperada
        $statusCode = isset($args['response']['status_code']) ? $args['response']['status_code'] : null;
        $alertType  = isset($args['response']['alert_type']) ? $args['response']['alert_type'] : null;
        $url = $args['webhookEndpoint']['url'];
        $id_response = isset($args['id']) ? $args['id'] : null;
        $ticket = isset($args['response']['id']) ? $args['response']['id'] : null;
        $rfc = isset($args['response']['taxpayer']['id']) ? $args['response']['taxpayer']['id'] : null;
        $GLOBALS['log']->fatal('statusCode: '. $statusCode);
        // Insertar en tabla de auditoría
        $insert = "";
        $fecha = date('Y-m-d H:i:s');

        if ($alertType == 'early_warning') {
            $GLOBALS['log']->fatal("Alert type is early_warning. Insertando auditoría...");
            $rfc = isset($args['response']['credentials']['rfc']) ? $args['response']['credentials']['rfc'] : null;
            $insert = "INSERT INTO robina_auditoria_peticiones (
                id, date_entered, id_response, ticket, rfc, 
                response_status_robina, crm_status_process, url, json_robina_wh
            ) VALUES (
                uuid(), '{$fecha}', '{$id_response}', '{$ticket}', '{$rfc}',
                '{$statusCode}', 'Alerta', '{$url}', '{$jsonargs}'
            )";
            //$GLOBALS['log']->fatal('insert error audit: '.$insert);
            $db->query($insert);
            $resultado['detail'] = 'Registro insertado con early_warning';
            return $resultado;
        }
                
        if ($statusCode == 'T01') {
            $rfc = isset($args['response']['taxpayer']['id']) ? $args['response']['taxpayer']['id'] : null;
            $GLOBALS['log']->fatal('ticket: '. $ticket);
            $GLOBALS['log']->fatal('rfc: '. $rfc);

            // Insertar en tabla de auditoría
            $insert = "
                INSERT INTO robina_auditoria_peticiones 
                (id,date_entered,id_response,ticket,rfc,response_status_robina,crm_status_process,url,json_robina_wh)
                VALUES (
                    uuid(),
                    '{$fecha}',
                    '{$id_response}',
                    '{$ticket}',
                    '{$rfc}',
                    '{$statusCode}',
                    'Recibido',
                    '{$url}',
                    '{$jsonargs}'
                )
            ";
        
            $resultado['detail'] = 'Recibido correctamente-estatus T01';

            $query = "SELECT * from pr_procesos_robina WHERE ticket = '{$ticket}' and rfc = '{$rfc}'";
            //$GLOBALS['log']->fatal('query: '. $query);
            $queryResult = $db->query($query);
            $res_tickets = [];

            //$GLOBALS['log']->fatal(print_r($queryResult, true));
            //$GLOBALS['log']->fatal($queryResult->num_rows);
            if($queryResult->num_rows > 0){
                while ($row = $db->fetchByAssoc($queryResult)) {
                    $res_tickets[] = $row;
                }
                $iddb = $res_tickets[0]['id'];
                $GLOBALS['log']->fatal('iddb: '.$iddb);
                $beanRobina = BeanFactory::retrieveBean('pr_Procesos_Robina', $iddb, array('disable_row_level_security' => true));
                // Usa status_code en una condición
                if ($statusCode === 'T01') {
                    // Lógica si es T01
                    $resultado['detail'] = 'Recibido:'. $statusCode;
                    $beanRobina->estatus_procesado = 'Recibido';
                    $beanRobina->estatus_robina = $statusCode;
                    $beanRobina->save();
                } else {
                    $resultado['detail'] = 'Recibido incompleto: ' . $statusCode;
                    $beanRobina->estatus_procesado = 'Recibido incompleto';
                    $beanRobina->estatus_robina = $statusCode;
                    $beanRobina->save();
                }
            }else{
                $GLOBALS['log']->fatal('No se encontraron datos por procesar');
                $resultado['detail'] = 'No se encontraron datos por procesar';    
            }
        } else {
            $GLOBALS['log']->fatal('Estatus no valido');
            $resultado['detail'] = 'Estatus no valido';
            $insert = "
                INSERT INTO robina_auditoria_peticiones (id,date_entered,id_response,ticket,rfc,response_status_robina,crm_status_process,url,json_robina_wh)
                VALUES (
                    uuid(),
                    '{$fecha}',
                    '{$id_response}',
                    '{$ticket}',
                    '{$rfc}',
                    '{$statusCode}',
                    'Recibido error',
                    '{$url}',
                    '{$jsonargs}'
                )
            ";
        }
        $db->query($insert);

        return $resultado;

    }
}

?>

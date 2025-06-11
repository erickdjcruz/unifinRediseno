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
        $GLOBALS['log']->fatal( "Respuesta robina" );

        $ticket = '';
        $resultado=[];

        // Verifica si existe el campo 'status_code' en la estructura esperada
        $statusCode = isset($args['response']['status_code']) ? $args['response']['status_code'] : null;
        $GLOBALS['log']->fatal('statusCode: '. $statusCode);
        
        if ($statusCode) {
            $ticket = isset($args['response']['id']) ? $args['response']['id'] : null;
            $rfc = isset($args['response']['taxpayer']['id']) ? $args['response']['taxpayer']['id'] : null;
        
            $resultado['detail'] = 'Recibido correctamente-estatus T01';

            $query = "SELECT * from pr_Procesos_Robina WHERE ticket = '{$ticket}' and rfc = '{$rfc}'";
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
        }
        
        return $resultado;

    }
}

?>

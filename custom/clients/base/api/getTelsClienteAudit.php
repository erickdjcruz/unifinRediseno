<?php
/**
 * Created by erick.cruz@tactos.com.mx.
 * 15/01/2025
 */

 if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class getTelsClienteAudit extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'TelAuditEndpoint' => array(
                //request type
                'reqType' => 'GET',
                //set authentication
                'noLoginRequired' => false,
                //endpoint path
                'path' => array('getTelsClienteAudit','?'),
                //endpoint variables
                'pathVars' => array('','idcliente'),
                //method to call
                'method' => 'validaTelefono',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Servicio para obtener los datos historicos de cambios realizados a telefonos de un cliente.',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            )
        );
    }

    public function validaTelefono($api, $args)
    {
        $inputData = $args;
        global $sugar_config, $db; 
        
        $idcliente = $args['idcliente'];
        $GLOBALS['log']->fatal('idcliente.'.$idcliente);
        $list_idtels = [];
        $list_parentid = [];
        $response = [];

        $sql_idtels = "SELECT tel.id
        FROM tel_telefonos AS tel INNER JOIN accounts_tel_telefonos_1_c as acctel 
        ON tel.id = acctel.accounts_tel_telefonos_1tel_telefonos_idb
        WHERE acctel.accounts_tel_telefonos_1accounts_ida = '$idcliente' AND 
        tel.deleted = 0 AND acctel.deleted = 0";
        $GLOBALS['log']->fatal("sql_idtels: " . $sql_idtels );

        $results = $db->query($sql_idtels);
        if( $results->num_rows > 0 ){

            while ($row = $db->fetchByAssoc($results)) {
                array_push($list_idtels, $row['id'] );
            }

            $valorestels = array_map(function($valor) {
                return "'" . addslashes($valor) . "'";
            }, $list_idtels);
            
            // Convertir a string
            $valString1 = implode(',', $valorestels);

            $sql_idparents = "SELECT telaudit.parent_id ,telaudit.field_name as campo_modificacion
            FROM tel_telefonos_audit telaudit
            WHERE telaudit.field_name = 'telefono' AND
            telaudit.parent_id in ($valString1)
            order by telaudit.date_created";
            $GLOBALS['log']->fatal("sql_idparents: " . $sql_idparents );

            $results1 = $db->query($sql_idparents);

            if( $results1->num_rows > 0 ){

                while ($row = $db->fetchByAssoc($results1)) {
                    array_push($list_parentid, $row['parent_id'] );
                }

                $valoresparents = array_map(function($valor) {
                    return "'" . addslashes($valor) . "'";
                }, $list_parentid);

                // Convertir a string
                $valString2 = implode(',', $valoresparents);

                $sql = "SELECT * FROM (SELECT telaudit.date_created as fecha_modificacion, telaudit.created_by as id_usuario,
                u.user_name as usuario , telaudit.parent_id as idtelefono, telaudit.field_name as campo_modificacion, 
                telaudit.before_value_string as valor_previo, telaudit.after_value_string as valor_posterior,
                audit_events.type as evento, audit_events.source
                FROM tel_telefonos_audit telaudit 
                INNER JOIN audit_events ON telaudit.event_id = audit_events.id
                LEFT JOIN users u ON telaudit.created_by = u.id
                WHERE telaudit.field_name = 'telefono' AND
                telaudit.parent_id in ($valString2)
                order by telaudit.date_created
                ) as A , 
                (SELECT u.user_name as usuario_plataforma , telaudit.field_name as campo_modificacion , 
                telaudit.parent_id
                FROM tel_telefonos_audit telaudit 
                LEFT JOIN users u ON telaudit.created_by = u.id
                WHERE telaudit.field_name = 'plataforma' AND  
                telaudit.parent_id  in ($valString2)
                order by telaudit.date_created
                ) as B
                WHERE A.idtelefono = B.parent_id";

                $GLOBALS['log']->fatal("sql: " . $sql );
                try{
                    $resultsF = $db->query($sql); // Ejecutar la consulta

                    if( $resultsF->num_rows > 0 ){
                        // Iterar por los resultados y almacenarlos en el arreglo
                        while ($row = $db->fetchByAssoc($resultsF)) {
                            $response[] = [
                                'fecha_modificacion' => $row['fecha_modificacion'],
                                'id_usuario' => $row['id_usuario'],
                                'usuario' => $row['usuario'],
                                'plataforma' => $row['usuario_plataforma'],
                                'idtelefono' => $row['idtelefono'],
                                'campo_modificacion' => $row['campo_modificacion'],
                                'valor_previo' => $row['valor_previo'],
                                'valor_posterior' => $row['valor_posterior'],
                                'evento' => $row['evento'],
                                // 'source' => json_decode($row['source']),
                            ];
                        }
                        
                        // Registrar en el log el número de resultados obtenidos
                        $GLOBALS['log']->fatal("Número de resultados: " . count($response));
                    }
                } catch (Exception $ex) {
                    $GLOBALS['log']->fatal("Exception " . $ex);
                    $estado = 400;
                    $response = [
                        'error' => true,
                        'message' => 'Ocurrió un error al procesar la consulta. Por favor, revisa los logs para más detalles.'
                    ];
                }
            }
        }

        return $response;
    }
}
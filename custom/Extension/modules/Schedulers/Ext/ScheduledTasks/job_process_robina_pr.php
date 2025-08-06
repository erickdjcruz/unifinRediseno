<?php
require_once 'include/TimeDate.php';
array_push($job_strings, 'job_process_robina_pr');

function job_process_robina_pr()
{
    global $db, $timedate;

    // Find webhooks that don't have matching tickets yet
    $query = "WITH ranked_data AS (
    SELECT 
        p.id AS proceso_id, p.name,p.id_cuenta,p.date_entered,
        p.rfc,  p.ticket,p.estatus_procesado,
        w.id AS auditoria_id, w.ticket AS auditoria_ticket,
        w.response_status_robina, w.crm_status_process,
        w.date_entered AS auditoria_date_entered,
        ROW_NUMBER() OVER (
        PARTITION BY p.ticket 
        ORDER BY w.date_entered DESC
        ) AS rn
    FROM pr_procesos_robina p
    LEFT JOIN robina_auditoria_peticiones w 
        ON p.ticket = w.ticket 
    WHERE w.id IS NOT NULL 
        AND p.estatus_procesado <> 'Procesado'
        AND w.crm_status_process = 'Recibido'
        AND w.response_status_robina = 'T01'
    )
    SELECT *
    FROM ranked_data
    WHERE rn = 1
    ORDER BY auditoria_date_entered DESC;";
    
    $result = $db->query($query);
    //$GLOBALS['log']->fatal(print_r($result,true));
    // if ($result->num_rows > 0) {    
        while ($row = $db->fetchByAssoc($result)) {
            $iddb = $row['proceso_id'];
            $ticket = $row['auditoria_ticket'];
            $GLOBALS['log']->fatal('iddb: '.$iddb);
            $statusCode = $row['response_status_robina'];
            $beanRobina = BeanFactory::retrieveBean('pr_Procesos_Robina', $iddb, array('disable_row_level_security' => true));
            // Usa status_code en una condición
            if ($statusCode === 'T01') {
                // Lógica si es T01
                $resultado['detail'] = 'Recibido:'. $statusCode;
                $beanRobina->estatus_procesado = 'Recibido';
                $beanRobina->estatus_robina = $statusCode;
                $beanRobina->save();

                $querytickets = "SELECT id FROM robina_auditoria_peticiones where ticket = '{$ticket}';";
                $restickets = $db->query($querytickets);
                while ($rowrobina = $db->fetchByAssoc($restickets)) {
                    $idrobinaaudi = $rowrobina['id'];
                    //$updatetickets = "UPDATE robina_auditoria_peticiones set crm_status_process = 'Procesado' where id = '{$idrobinaaudi}';";
                    //$db->query($updatetickets);
                    $GLOBALS['log']->fatal('iddb: '.$idrobinaaudi);
                    $beanRobina = BeanFactory::retrieveBean('pr_Procesos_Robina', $idrobinaaudi, array('disable_row_level_security' => true));
                    // Usa status_code en una condición
                    //$resultado['detail'] = 'Recibido:'. $statusCode;
                    $beanRobina->estatus_procesado = 'Recibido';
                    $beanRobina->estatus_robina = $statusCode;
                    $beanRobina->save();
                }

            } else {
                $resultado['detail'] = 'Recibido incompleto: ' . $statusCode;
                $beanRobina->estatus_procesado = 'Recibido incompleto';
                $beanRobina->estatus_robina = $statusCode;
                $beanRobina->save();
            }
        }
    //}
    return true;
}
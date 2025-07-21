<?php
array_push($job_strings, 'job_generation_center_po');

function job_generation_center_po()
{
    try {
        $GLOBALS['log']->fatal('************ Entra Job Reasignación Generation Center PO **************');

        global $db, $app_list_strings;
        //$lider_id = $app_list_strings['lider_generation_center_list']['Ricardo Gerardo'];
         // Obtener el primer valor de la lista lider_generation_center_list
        $lider_gc_list = $app_list_strings['lider_generation_center_list'];
        $keys = array_keys($lider_gc_list);
        $primer_id_asignado = $keys[0]; // Primer key
        $id_lider = $app_list_strings['lider_generation_center_list'][$primer_id_asignado];
 
        $query = "SELECT p.id
            FROM prospects p 
            INNER JOIN prospects_cstm pc ON p.id = pc.id_c
            WHERE p.deleted = 0 
            AND pc.estatus_po_c = '1'
            AND p.assigned_user_id <> '{$id_lider}'
            AND pc.excluye_campana_c = 0
            AND p.date_entered > ('2024-10-01 00:00:00')
            AND p.date_entered < (NOW() - INTERVAL 1 DAY)
            AND (
                -- No tienen llamadas ni reuniones
                (
                NOT EXISTS (
                    SELECT 1 FROM calls c
                    WHERE c.parent_id = p.id
                    AND c.parent_type = 'Prospects'
                    AND c.deleted = 0
                )
                AND NOT EXISTS (
                    SELECT 1 FROM meetings m
                    WHERE m.parent_id = p.id
                    AND m.parent_type = 'Prospects'
                    AND m.deleted = 0
                )
                )
                -- O tienen llamadas/reuniones, pero no 'Held' ni recientes
                OR (
                NOT EXISTS (
                    SELECT 1 FROM calls c
                    WHERE c.parent_id = p.id
                    AND c.parent_type = 'Prospects'
                    AND c.deleted = 0
                    AND (
                        c.status = 'Held' OR
                        c.date_start > (NOW() - INTERVAL 1 DAY)
                    )
                )
                AND NOT EXISTS (
                    SELECT 1 FROM meetings m
                    WHERE m.parent_id = p.id
                    AND m.parent_type = 'Prospects'
                    AND m.deleted = 0
                    AND (
                        m.status = 'Held' OR
                        m.date_start > (NOW() - INTERVAL 1 DAY)
                    )
                )
                )
            )
            ORDER BY p.date_entered DESC
            limit 10;";
        
        $result = $db->query($query);
        $rows = [];
        while ($row = $db->fetchByAssoc($result)) {
            $rows[] = $row;
        }

        foreach ($rows as $row) {
            $update = "UPDATE prospects SET assigned_user_id = '{$id_lider}' WHERE id = '{$row['id']}'";
            $db->query($update);
            $GLOBALS['log']->fatal("Prospecto reasignado PO gerente GC: {$row['id']} - id:".$id_lider);
            require_once("custom/clients/base/api/SendEmailPO.php");
            $apiSendEmailPO = new SendEmailPO();
            $body = array(
                'id_po' => $row['id'],
                'id_lider_gc' => $id_lider
            );
            //ENVIA CORREO DE NOTIFICACION AL LIDER DE GENERATION CENTER
            $response = $apiSendEmailPO->notificaReasignacionLiderGenerationCenterPO(null, $body);
        }

        return true;

    } catch (Exception $e) {
        $GLOBALS['log']->fatal("Error JOB GEneration Center: ");
        $GLOBALS['log']->fatal("Error: " . $e->getMessage());
    }
}

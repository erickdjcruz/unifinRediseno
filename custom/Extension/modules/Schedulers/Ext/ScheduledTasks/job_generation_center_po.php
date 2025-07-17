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
        WHERE p.deleted <> 1 
        AND pc.estatus_po_c = '1'
        AND p.assigned_user_id <> '{$id_lider}'
        AND NOT EXISTS (
            SELECT 1 FROM calls c
            WHERE c.parent_id = p.id
                AND c.parent_type = 'Prospects'
                AND c.deleted <> 1
                AND c.status <> 'Held'
                AND c.date_entered > NOW() - INTERVAL 1 DAY
        )
        AND NOT EXISTS (
            SELECT 1 FROM meetings m
            WHERE m.parent_id = p.id
                AND m.parent_type = 'Prospects'
                AND m.deleted <> 1
                AND m.status <> 'Held'
                AND m.date_entered > NOW() - INTERVAL 1 DAY
        )
        ORDER BY p.date_entered DESC
        LIMIT 30;";
        
        $result = $db->query($query);
        $rows = [];
        while ($row = $db->fetchByAssoc($result)) {
            $rows[] = $row;
        }

        foreach ($rows as $row) {
            $update = "UPDATE prospects SET assigned_user_id = '{$id_lider}' WHERE id = '{$row['id']}'";
            $db->query($update);
            $GLOBALS['log']->fatal("Prospecto reasignado PO gerente GC: {$row['id']} - id:".$id_lider);
        }

        return true;

    } catch (Exception $e) {
        $GLOBALS['log']->fatal("Error JOB GEneration Center: ");
        $GLOBALS['log']->fatal("Error: " . $e->getMessage());
    }
}

<?php
array_push($job_strings, 'job_generation_center_po');

function job_generation_center_po()
{
    try {
        $GLOBALS['log']->fatal('Entra Job Reasignación Generation Center PO');

        global $db, $app_list_strings;
        $lider_id = $app_list_strings['lider_generation_center_list']['Ricardo Gerardo'];
        $query = "SELECT id FROM prospects 
        WHERE deleted <> 1 
        and assigned_user_id <> '{$lider_id}' 
        and id in (select parent_id from calls where status <> 'Held' 
        and parent_type = 'Prospects' 
        and deleted <> 1 
        and date_entered > now() - interval 1 day) 
        or id in (select parent_id from meetings where status <> 'Held' 
        and parent_type = 'Prospects' 
        and deleted <> 1 
        and date_entered > now() - interval 1 day))";
        $result = $db->query($query);
        $rows = $db->fetchByAssoc($result);
        $countPO = count($rows);

        for ($current = 0; $current < $countPO; $current++) {
            $beanPO = BeanFactory::retrieveBean('Prospects', $rows[$current]['id']);
            $beanPO->assigned_user_id = $lider_id;
            $beanPO->save();
        }

        return true;

    } catch (Exception $e) {
        $GLOBALS['log']->fatal("Error: " . $e->getMessage());
    }
}

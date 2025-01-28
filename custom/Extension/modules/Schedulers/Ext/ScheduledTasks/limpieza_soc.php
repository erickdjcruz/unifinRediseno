<?php

array_push($job_strings, 'limpieza_soc');
function limpieza_soc()
{
    global $db;
    $error = false;
        
    //Inicia ejecución
    $GLOBALS['log']->fatal('Job limpieza_soc: Inicia');

    $sql = "SELECT id,parent_id , date_created ,date_add(NOW(), INTERVAL -6 MONTH) insix, field_name , before_value_string ,
    after_value_string FROM accounts_audit aa 
    WHERE field_name = 'alianza_soc_chk_c' AND after_value_string = 1 AND date_add(NOW(), INTERVAL -6 MONTH) >= date_created
    AND parent_id NOT IN (
        SELECT parent_id FROM accounts_audit aa WHERE field_name = 'alianza_soc_chk_c'AND after_value_string = 0
        AND date_created > date_add(NOW(), INTERVAL -6 MONTH)
        )
    ORDER BY date_created DESC";
    $result = $db->query($sql);
    while ($row = $db->fetchByAssoc($result)) {
        $bean = null;
        $GLOBALS['log']->fatal('Cuenta SOC-atrasada: '. $row['parent_id']);
        $bean = BeanFactory::retrieveBean('Accounts', $row['parent_id'] ,array('disable_row_level_security' => true));
        $bean->alianza_soc_chk_c = 0;
        $bean->save();
    }
  
    $GLOBALS['log']->fatal('Job limpieza_soc: Fin');
    return true;
}
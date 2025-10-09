<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class AccountHooksBuro
{
    public function setBanderaBuroCredito($bean = null, $event = null, $args = null)
    {
        global $current_user, $db;
        
        //if ($_SESSION['platform'] != 'base' && !empty($bean->fetched_row['id'])) {
        $GLOBALS['log']->fatal("fetchedrow_Id".empty($bean->fetched_row['id']);
        if ($_SESSION['platform'] != 'base' && !$args['isUpdate']) {
            $GLOBALS['log']->fatal("*** Cuenta nueva detectada, estableciendo bandera para Buró ***");
            $idCuenta = $bean->id;
            
            $query =  "UPDATE tct02_resumen_cstm SET crear_direccion_buro_c = 1 WHERE id_c = '{$idCuenta}'";
            $db->query($query);

        }else{
            return;
        }
    }
}
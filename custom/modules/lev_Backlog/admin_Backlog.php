<?php

class class_Backlog_Admin
{
    function func_Backlog_Admin($bean = null, $event = null, $args = null)
    {
        //SOLO SE EJECUTA EN LA CREACIÓN DEL BACKLOG
        if (!$args['isUpdate']) {
            if ($bean->account_id_c != '') {
                $rel_cuenta = BeanFactory::retrieveBean('Accounts', $bean->account_id_c);
                $check_lumo = $rel_cuenta->lumo_c;
                //$GLOBALS['log']->fatal("BACKLOG - ACTUALIZA LUMO SI ESTA ACTIVO EN LA CUENTA RELACIONADA: " . $bean->account_id_c . " LUMO: " . $check_lumo);
                if ($check_lumo == 1) {
                    $bean->lumo_cuentas_c = 1;
                    // $GLOBALS['log']->fatal("LUMO BACKLOG ". $bean->lumo_cuentas_c);
                }
            }
        }

        //$GLOBALS['log']->fatal("BACKLOG - account_id_c " . $bean->account_id_c );
        global $app_list_strings;        

        if ($bean->account_id_c != '') {
          $rel_acc = BeanFactory::retrieveBean('Accounts', $bean->account_id_c);
          $valorOrigen = $app_list_strings['origen_lead_list'][$rel_acc->origen_cuenta_c];
          //$GLOBALS['log']->fatal("cuenta_origen: " . $rel_acc->origen_cuenta_c ."" . $valorOrigen );
          $bean->origen_cuenta_c = $valorOrigen;
        }

        //Actualiza rango Backlog
        global $app_list_strings;
        $valorMonto = $bean->monto_prospecto_c + $bean->monto_credito_c + $bean->monto_rechazado_c + $bean->monto_sin_solicitud_c + $bean->monto_con_solicitud_c; //$bean->monto_comprometido;
        $listaRangos = $app_list_strings['rango_bl_list'];
        $rangoEncontrado = '';
        $bandera = 0;
        //Recorriendo lista de rangos
        foreach ($listaRangos as $key => $value) {
            //array_push($cuentas_email,$listaRangos[$key]);
            if($bandera == 0){
              $valoresEntre = explode(" ", $key);
              if(count($valoresEntre) == 2){
                if($valorMonto >= $valoresEntre[0] && $valorMonto <= $valoresEntre[1]){
                  $rangoEncontrado = $key;
                  $bandera = 1;
                }
              }elseif(count($valoresEntre) == 1) {
                $rangoEncontrado = $valoresEntre[0];
              }
            }
        }
        $bean->rango_bl_c = ($valorMonto=='') ? '' : $rangoEncontrado; //Valida suma de montosº
        $bean->rango_bl_c = ($bean->estatus_operacion_c=='1') ? '' : $rangoEncontrado; //Valida estatus de operación: Cancelado = 1

    }
}

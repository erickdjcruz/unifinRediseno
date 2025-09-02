<?php
/**
 * Created by Levementum.
 * User: jgarcia@levementum.com
 * Date: 3/8/2016
 * Time: 10:40 AM
 */
//require_once("custom/Levementum/UnifinAPI.php");
require_once("custom/clients/base/api/ConsultaID.php");
class backlog_hooks {

    public function setFormatName($bean = null, $event = null, $args = null){

        // if($bean->lev_backlog_opportunitiesopportunities_ida == null) {
            $account = BeanFactory::retrieveBean('Accounts', $bean->account_id_c);
            $api="";
            $args=[];
            $args['categoriaID']=1;
            if(empty($bean->numero_de_backlog)) {
                $callApi = new ConsultaID();
                $numeroDeFolio = $callApi->RecuperaID($api, $args);
                $bean->numero_de_backlog = $numeroDeFolio['Id'];
            }
            //BackLog Mes Año – FolioBacklog - Cliente
            $bean->name = 'BackLog ' . $bean->mes . " " . $bean->anio . " - " . $bean->numero_de_backlog . " - " . $account->name;
        // }
    }

    public function setComentarios($bean = null, $event = null, $args = null){

        global $current_user;
        $todayDate = date("n/j/Y", strtotime("now"));
        if(!empty($bean->comentario)){
            $bean->description .= "\r\n" . $current_user->first_name . " " . $current_user->last_name . " - " . $todayDate . ": " . $bean->comentario;
            $bean->comentario = "";
        }
    }

    public function setMontosMultiEtapa($bean = null, $event = null, $args = null)
    {
        global $current_user;
        $privilegio=$current_user->admin_backlog_c;
        $adminBL = (strpos($bean->comentarios_c, '-adminbl-') !== false) ? true: false; //Indica si fue editado desde vista de administración backlog

        if($bean->producto_c != "2" && !$adminBL){
            //Solo se ejecuta al insertar
            if (empty($bean->fetched_row['id'])) {
                // $GLOBALS['log']->fatal('Es nuevo backlog');
                // $GLOBALS['log']->fatal('Actualiza Valores');
                $bean->monto_con_solicitud_c = 0;
                $bean->ri_con_solicitud_c = 0;
                $bean->monto_credito_c = 0;
                $bean->ri_credito_c = 0;
                $bean->monto_rechazado_c = 0;
                $bean->ri_rechazada_c = 0;
                $bean->monto_final_comprometido_c = $bean->monto_comprometido;  //Backlog
                $bean->ri_final_comprometida = $bean->renta_inicial_comprometida;
                $bean->monto_prospecto_c = 0;
                $bean->ri_prospecto_c = 0;
                //$GLOBALS['log']->fatal('Valida Si Linea Disponible >= Backlog - Renta Inicial'.$bean->monto_original.'-'.$bean->monto_final_comprometido.'-'.$bean->ri_final_comprometida_c);
                //Si Linea Disponible >= Backlog - Renta Inicial
                if ($bean->monto_original >= ($bean->monto_final_comprometido_c - $bean->ri_final_comprometida_c)) {
                    // $GLOBALS['log']->fatal('cumple condicion backlog >=- Renta Inicial');
                    // $GLOBALS['log']->fatal('Actualiza Valores');
                    $bean->monto_sin_solicitud_c = $bean->monto_final_comprometido_c;
                    $bean->ri_sin_solicitud_c = $bean->ri_final_comprometida_c;
                    $bean->etapa_c = '1';
                    $bean->etapa_preliminar_c='1';

                } else {
                    //$GLOBALS['log']->fatal('No cumple condicion');
                    $bean = backlog_hooks::ActualizaValores($bean);
                }

                $bean->etapa_preliminar_c=$bean->etapa_c;

            } else {
                //$GLOBALS['log']->fatal('Si no existe registro, calcula valores-'.$bean->monto_final_comprometido_c.'-'.$bean->monto_con_solicitud_c);
                //Si no existe registro, calcula valores
                $montoTMP = $bean->monto_final_comprometido_c - $bean->monto_con_solicitud_c;
                if ($montoTMP < 0) {
                    $montoTMP = 0;
                }
                $RITMP = $montoTMP * ((float)$bean->porciento_ri/100);
                $GLOBALS['log']->fatal('Si Linea Disponible >= montoTMP - RITMP'.'-'.$montoTMP.'-'.$RITMP.'-'.$bean->monto_original);
                //Si Linea Disponible >= montoTMP - RITMP
                if ($bean->monto_original >= ($montoTMP - $RITMP)) {
                    //$GLOBALS['log']->fatal('Si Linea Disponible >= montoTMP - RITMP asigna valores');
                    //Actualiza
                    $bean->monto_sin_solicitud_c = $montoTMP;
                    $bean->ri_sin_solicitud_c = $RITMP;
                    $bean->etapa_c = '1';
                } else {
                    $bean = backlog_hooks::ActualizaValores($bean);
                }
            }

        }

        $bean->comentarios_c = str_replace('-adminbl-','',$bean->comentarios_c);
        //Elimina valores negativos
        $monto_sin_solicitud_c = ($monto_sin_solicitud_c <= 0) ? 0 : $monto_sin_solicitud_c;
        $monto_con_solicitud_c = ($monto_con_solicitud_c <= 0) ? 0: $monto_con_solicitud_c;

    }
    public function ActualizaValores($bean = null, $event = null, $args = null){
        //$GLOBALS['log']->fatal('Entra a Actualiza Valores- SugarQuery');
        $bean->monto_sin_solicitud_c = $bean->monto_original / (1-($bean->porciento_ri/100));
        $bean->ri_sin_solicitud_c = $bean->porciento_ri > 0 ? $bean->monto_sin_solicitud_c * ($bean->porciento_ri/100) : 0 ;
        $monto_faltante_c = $bean->monto_final_comprometido_c - $bean->monto_sin_solicitud_c - $bean->monto_con_solicitud_c;

        //SugarQuery
        //$GLOBALS['log']->fatal('Entra SugarQuery');
        $fechaActual = date("Y-m-d");
        $fechatreinta = strtotime('-3 month',strtotime($fechaActual));
        $fechatreinta = date('Y-m-d',$fechatreinta);
        $consultaSolicitudes = new SugarQuery();
        $consultaSolicitudes->select(array('name','date_entered','tipo_producto_c','estatus_c','tct_etapa_ddw_c'));
        $consultaSolicitudes->from(BeanFactory::newBean('Opportunities'));
        //Condiciones
        $consultaSolicitudes->where()
           ->equals('tipo_producto_c','1')
           ->dateBetween('date_entered',array($fechatreinta,$fechaActual))
           ->equals('account_id',$bean->account_id_c);
        //Ejecuta consulta
        //$GLOBALS['log']->fatal('Recupera solicitudes de Leasing 3 months ago');
        $result = $consultaSolicitudes->execute();

        //Iterar resultado
        $totalC = count($result);
        //Recorre arreglo
        //$GLOBALS['log']->fatal('Evalua solicitudes existentes');
        if($totalC > 0){
            //$GLOBALS['log']->fatal('Itera solicitudes existentes de la consulta'.$totalC);
            $credito=false;
            $rechazada=false;
            $prospecto=false;
            $devuelta=false;
            for ($i = 0; $i < $totalC ; $i++){
                $GLOBALS['log']->fatal('Itera Registros'.$i.'estatus: '.$result[$i]['estatus_c'].' etapa: '.$result[$i]['tct_etapa_ddw_c'].' '.$result[$i]['name']);
               if($result[$i]['tct_etapa_ddw_c']=="C"){
                   $GLOBALS['log']->fatal('Itera Solcitudes de Credito');
                   $credito=true;
               }
               if($result[$i]['estatus_c']=="R" ||$result[$i]['estatus_c']=="CM"){
                   $GLOBALS['log']->fatal('IteraSolcitudes de Rechazada');
                   $rechazada=true;
               }
               if($result[$i]['tct_etapa_ddw_c']=="SI" ||$result[$i]['tct_etapa_ddw_c']=="P"){
                   $GLOBALS['log']->fatal('Itera Etapa');
                   $prospecto=true;
               }
               if($result[$i]['estatus_c']=="DP"){
                   $devuelta=true;
               }
               if ($credito == false && $rechazada == false && !$devuelta &&  $prospecto == false ) {
                   $prospecto=true;
               }
            }
            $bean->monto_prospecto_c = 0;
            $bean->ri_prospecto_c = 0;
            $bean->monto_credito_c = 0;
            $bean->ri_credito_c = 0;
            $bean->monto_rechazado_c = 0;
            $bean->ri_rechazada_c = 0;
            if($devuelta){
                $bean->etapa_c= '5';
            }elseif ($credito==true){
                $GLOBALS['log']->fatal('Solcitudes de Credito');
                $bean->monto_credito_c= $monto_faltante_c;
                $bean->ri_credito_c = $bean->monto_credito_c * ($bean->porciento_ri/100);
                $bean->etapa_c= '4';
            }elseif ($credito==false && $rechazada==true){
                $GLOBALS['log']->fatal('Solcitudes Rechazada');
                $bean->monto_rechazado_c= $monto_faltante_c;
                $bean->ri_rechazada_c = $bean->monto_rechazado_c * ($bean->porciento_ri/100);
                $bean->etapa_c= '2';
            }elseif ($credito==false && $rechazada==false && $prospecto==true){
                $GLOBALS['log']->fatal('Solcitudes de Prospecto');
                $bean->monto_prospecto_c= $monto_faltante_c;
                $bean->ri_prospecto_c = $bean->monto_prospecto_c * ($bean->porciento_ri/100);
                $bean->etapa_c= '3';
            }
        }else{
             $GLOBALS['log']->fatal('No existen solicitudes de la consulta');
             $bean->monto_prospecto_c= $monto_faltante_c;
             $bean->etapa_c= '3';
        }
        return $bean;
    }
}

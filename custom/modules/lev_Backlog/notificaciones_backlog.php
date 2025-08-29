<?php

require_once("custom/clients/base/api/SendEmailBacklog.php");

class class_Backlog_Notificacion
{
    function func_Backlog_Notificacion($bean, $event, $arguments)
    {
        $apiSendEmailBacklog = new SendEmailBacklog();
        $montoComprometidoAnterior = $bean->fetched_row['monto_comprometido'];
        $montoComprometidoNuevo = $bean->monto_comprometido;
        $fechaCompromisoAnterior = $bean->fetched_row['fecha_compromiso_c'];
        $fechaCompromisoNuevo = $bean->fecha_compromiso_c;
        $tipificacionRiesgoAnterior = $bean->fetched_row['tipificacion_riesgo_c'];
        $tipificacionRiesgoNuevo = $bean->tipificacion_riesgo_c;
        $estatusBacklogAnterior = $bean->fetched_row['estatus_backlog_c'];
        $estatusBacklogNuevo = $bean->estatus_backlog_c;     
        $motivoDeclinacion = $bean->motivo_declinacion_c;     
    
        //DETECTA CAMBIO DE CAMPOS PARA NOTIFICACION
        if ($montoComprometidoAnterior != $montoComprometidoNuevo || $fechaCompromisoAnterior != $fechaCompromisoNuevo || $tipificacionRiesgoAnterior != $tipificacionRiesgoNuevo) {
            $GLOBALS['log']->fatal("Cambio detectado en monto_comprometido: de $montoComprometidoAnterior a $montoComprometidoNuevo");            
            $GLOBALS['log']->fatal("Cambio detectado en fecha_compromiso_c: de $fechaCompromisoAnterior a $fechaCompromisoNuevo");
            $GLOBALS['log']->fatal("Cambio detectado en tipificacion_riesgo_c: de $tipificacionRiesgoAnterior a $tipificacionRiesgoNuevo");
            
            $bodyCambio = array(
                'tipo' => 'cambio',
                'nombre_backlog' => $bean->name,
                'id_backlog' => $bean->id,
                'id_cuenta' => $bean->account_id_c,
                'id_asesor' => $bean->assigned_user_id,
                'monto_comp_anterior' => $montoComprometidoAnterior,
                'monto_comp_nuevo' => $montoComprometidoNuevo,
                'fecha_comp_anterior' => $fechaCompromisoAnterior,
                'fecha_comp_nuevo' => $fechaCompromisoNuevo,
                'tipificacion_anterior' => $tipificacionRiesgoAnterior,
                'tipificacion_nuevo' => $tipificacionRiesgoNuevo,
            );
            //ENVIA CORREO DE NOTIFICACION
            $response = $apiSendEmailBacklog->notificaCorreoDirectorBacklog(null, $bodyCambio);
        }
        //DETECTA CAMBIO DE ESTATUS DECLINADA PARA NOTIFICACION
        if ($estatusBacklogAnterior != $estatusBacklogNuevo && $estatusBacklogNuevo == '2') {
            $GLOBALS['log']->fatal("Cambio detectado en estatus_backlog_c: de $estatusBacklogAnterior a $estatusBacklogNuevo");
            
            $bodyBaja = array(
                'tipo' => 'baja',
                'nombre_backlog' => $bean->name,
                'id_backlog' => $bean->id,
                'id_cuenta' => $bean->account_id_c,
                'id_asesor' => $bean->assigned_user_id,
                'monto_comp_nuevo' => $montoComprometidoNuevo,
                'fecha_comp_nuevo' => $fechaCompromisoNuevo,
                'tipificacion_nuevo' => $tipificacionRiesgoNuevo,
                'motivo_declinacion' => $motivoDeclinacion,
            );
            //ENVIA CORREO DE NOTIFICACION
            $response = $apiSendEmailBacklog->notificaCorreoDirectorBacklog(null, $bodyBaja);
        }
    }
}

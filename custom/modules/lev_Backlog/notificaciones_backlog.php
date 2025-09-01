<?php

require_once("custom/clients/base/api/SendEmailBacklog.php");

class class_Backlog_Notificacion
{
    function func_Backlog_Notificacion($bean, $event, $arguments)
    {
        $apiSendEmailBacklog = new SendEmailBacklog();

        $montoComprometidoAnterior = (float)($bean->fetched_row['monto_comprometido'] ?? 0);
        $montoComprometidoNuevo    = (float)($bean->monto_comprometido ?? 0);
        $fechaCompromisoAnterior   = (string)($bean->fetched_row['fecha_compromiso_c'] ?? '');
        $fechaCompromisoNuevo      = (string)($bean->fecha_compromiso_c ?? '');
        $estatusBacklogAnterior    = (string)($bean->fetched_row['estatus_backlog_c'] ?? '');
        $estatusBacklogNuevo       = (string)($bean->estatus_backlog_c ?? '');
        
        //DETECTA CAMBIO DE CAMPOS PARA NOTIFICACION
        if ($montoComprometidoAnterior != $montoComprometidoNuevo || $fechaCompromisoAnterior != $fechaCompromisoNuevo ) {
            $GLOBALS['log']->fatal("Cambio detectado en monto_comprometido: de $montoComprometidoAnterior a $montoComprometidoNuevo");            
            $GLOBALS['log']->fatal("Cambio detectado en fecha_compromiso_c: de $fechaCompromisoAnterior a $fechaCompromisoNuevo");
            
            $bodyCambio = array(
                'tipo' => 'cambio',
                'nombre_backlog' => $bean->name,
                'id_backlog' => $bean->id,
                'id_cuenta' => $bean->account_id_c,
                'id_asesor' => $bean->assigned_user_id
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
                'id_asesor' => $bean->assigned_user_id
            );
            //ENVIA CORREO DE NOTIFICACION
            $response = $apiSendEmailBacklog->notificaCorreoDirectorBacklog(null, $bodyBaja);
        }
    }
}

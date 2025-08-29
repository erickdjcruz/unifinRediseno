<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class reactiva_bkl extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'reactiva_bkl' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('reactiva_bkl'),
                'pathVars' => array('method'),
                'method' => 'autorizaEnvioCorreo',
                'shortHelp' => 'Envía correo a dirctor para solicitar aprobacion de reactivación',
            ),
        );
    }

    public function autorizaEnvioCorreo($api, $args){
        $motivo = $args['mensaje'];
        $idRegistro = $args["idRegistro"];
        $beanBkl = BeanFactory::retrieveBean('lev_Backlog', $idRegistro, array('disable_row_level_security' => true));
        $idAsesor = $beanBkl->assigned_user_id;
        $beanAsesor = BeanFactory::retrieveBean('Users', $idAsesor, array('disable_row_level_security' => true));
        $id_director_comercial = $this->getIdDirectorComercial($beanAsesor);
        if($id_director_comercial != ""){
            $info_comercial = $this->getInfoUser($id_director_comercial);
            $name_comercial = $info_comercial['name'];
            $email_comercial = $info_comercial['email'];
        }
        $bodyCorreo = $this->buildBodyEnviaPeticionAutorizacionDirector( $name_comercial, $idRegistro, $beanBkl->name, $motivo);
        if(!empty($email_comercial)){
            $this->sendEmailPeticionAutorizacionDirector($email_comercial,$bodyCorreo,$beanBkl->name, $idRegistro, $id_director_comercial, $motivo);
            return array(
                "status" => "success",
                "msj" => "Se ha enviado el correo"
            );
        }else{
            return array(
                "status" => "info",
                "msj" => "El director comercial no cuenta con un email válido"
            );
        }
    }

    public function getIdDirectorComercial($beanAsesor){
        $equipo_principal_asesor = $beanAsesor->equipo_c;
        $id_comercial = "";
        $qGetDirectorComercial = "SELECT id_c,posicion_operativa_c,uc.equipos_c FROM users u 
        INNER JOIN users_cstm uc 
        ON u.id = uc.id_c
        AND uc.posicion_operativa_c LIKE '%^1^%' AND uc.equipos_c LIKE '%^{$equipo_principal_asesor}^%'
        WHERE u.status = 'Active' AND u.deleted=0";
        $resultadoComercial = $GLOBALS['db']->query($qGetDirectorComercial);
        if ($resultadoComercial->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($resultadoComercial)) {
                $id_comercial = $row['id_c'];
            }
        }
        return $id_comercial;
    }

    public function getInfoUser($id_user){
        $beanUser = BeanFactory::retrieveBean('Users', $id_user, array('disable_row_level_security' => true));
        $emailUser = $beanUser->email1;
        $first_name = $beanUser->first_name;
        $last_name = $beanUser->last_name;
        $user = [];
        $user['name'] =  $first_name." ".$last_name;
        $user['email'] = $emailUser;
        return $user;
    }

    public function buildBodyEnviaPeticionAutorizacionDirector($nombreDirectorComercial, $idRegistro, $nombreRegistro, $motivo){
		$beanBkl = BeanFactory::getBean('lev_Backlog', $idRegistro, array('disable_row_level_security' => true));
		$idCliente = $beanBkl->account_id_c;
		$beanCte = BeanFactory::getBean('Accounts', $idCliente, array('disable_row_level_security' => true));
        $asesor = $beanBkl->assigned_user_name;
		$cliente = $beanBkl->cliente;
		$solicitud = $beanBkl->numero_de_solicitud;
		$monto = $beanBkl->monto_c;
		$prometido = $beanBkl->monto_comprometido;
		$fecha = $beanBkl->fecha_compromiso_c;
		$origen = $beanCte->origen_cuenta_c;
        $linkbkl = $GLOBALS['sugar_config']['site_url'].'/#lev_Backlog/'.$idRegistro;
        $htmlLink = '<b><a id="linkbkl" href="'.$linkbkl.'">Ver detalle en CRM</a></b>';
        $aceptabkl = $GLOBALS['sugar_config']['site_url'].'/#lev_Backlog/layout/reactivacionBacklog?accion=aceptar&id='.$idRegistro;
        $htmlAcepta = '<b><a id="aceptabkl" href="'.$aceptabkl.'">Aprobar</a></b>';
        $rechazabkl = $GLOBALS['sugar_config']['site_url'].'/#lev_Backlog/layout/reactivacionBacklog?accion=rechazar&id='.$idRegistro;
        $htmlRechaza = '<b><a id="rechazabkl" href="'.$rechazabkl.'">Rechazar</a></b>';
        $mailHTML = '<p align="justify"><font face="verdana" color="#635f5f">Hola '.$nombreDirectorComercial.',<br><br>
            El asesor a tu cargo, '.$asesor.', solicita tu visto bueno para reactivar la operación del cliente '.$cliente.',<br>
            (ID '.$solicitud.'), actualmente Declinada.<br><br>
            Motivo breve del asesor: '.$motivo.'<br>
			Datos de referencia:<br>
			Estatus actual: Declinada<br>
            Monto autorizado/preautorizado: '.$monto.'<br>
            Monto prometido: '.$prometido.'<br>
			Fecha prometida: '.$fecha.'<br>
			Origen: '.$origen.'<br>
			Puedes autorizar o rechazar la reactivación aquí:<br>
			'.$htmlAcepta.' | '.$htmlRechaza.' | '.$htmlLink.'<br>
			Esta acción quedará registrada en el historial. Cualquier cambio notificará al asesor y a ti.<br>
			Si tienes alguna duda contactar a:<br>
			Equipo CRM<br>
			Inteligencia de Negocios<br>
			Tel.: (55)5249 5800 Ext.5737 y 5677<br>
            <br><br>Atentamente Unifin</font></p>
            <br><br><img border="0" id="bannerUnifin" src="https://www.unifin.com.mx/ri/front/img/logo.png">
            <br><span style="font-size:8.5pt;color:#757b80">____________________________________________</span>
            <p class="MsoNormal" style="text-align: justify;">
              <span style="font-size: 7.5pt; font-family: \'Arial\',sans-serif; color: #212121;">
                Este correo electrónico y sus anexos pueden contener información CONFIDENCIAL para uso exclusivo de su destinatario. Si ha recibido este correo por error, por favor, notifíquelo al remitente y bórrelo de su sistema.
                Las opiniones expresadas en este correo son las de su autor y no son necesariamente compartidas o apoyadas por UNIFIN, quien no asume aquí obligaciones ni se responsabiliza del contenido de este correo, a menos que dicha información sea confirmada por escrito por un representante legal autorizado.
                No se garantiza que la transmisión de este correo sea segura o libre de errores, podría haber sido viciada, perdida, destruida, haber llegado tarde, de forma incompleta o contener VIRUS.
                Asimismo, los datos personales, que en su caso UNIFIN pudiera recibir a través de este medio, mantendrán la seguridad y privacidad en los términos de la Ley Federal de Protección de Datos Personales; para más información consulte nuestro <a href="https://www.unifin.com.mx/aviso-de-privacidad" target="_blank">Aviso de Privacidad</a>  publicado en <a href="http://www.unifin.com.mx/" target="_blank">www.unifin.com.mx</a>
              </span>
            </p>';
        return $mailHTML;
    }

    public function sendEmailPeticionAutorizacionDirector($emailDirector, $body_correo, $nombre, $idRegistro, $id_director_comercial, $motivo){
        try{
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject('Solicitud de VoBo: Creación de nuevo PO – '.$nombreCuenta);
            $body = trim($body_correo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            $mailer->addRecipientsTo(new EmailIdentity($emailDirector, $emailDirector));
            $result = $mailer->send();
            $this->setIdDirectorComercialParaAprobacion($idRegistro, $id_director_comercial, $motivo);
        } catch (Exception $e){
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar el correo electrónico");
            $GLOBALS['log']->fatal($e->getMessage());
        }
    }

    public function setIdDirectorComercialParaAprobacion($idRegistro, $idDirectorComercial, $motivo){
        $beanBkl = BeanFactory::getBean('lev_Backlog', $idRegistro, array('disable_row_level_security' => true));
        $beanBkl->aprobador_reactivacion_c = $idDirectorComercial;
        $beanBkl->motivo_reactivacion_c = $motivo;
		$beanBkl->fecha_sol_reactivacion_c = date("Y-m-d H:i:s");
        $beanBkl->save();
    }
}

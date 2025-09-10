<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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

            'notificaAccionReactivacionBL' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('notificaReactivaBL'),
                'pathVars' => array(''),
                'method' => 'notificaReactivaBL',
                'shortHelp' => 'Envía notificación por email a respectivos usuarios para aprobacion o rechazo',
            ),
        );
    }

    public function notificaReactivaBL($api, $args)
    {
        $GLOBALS['log']->fatal("---------- notificaReactivacion BL -----------");
        global $db, $current_user;

        $idRegistro            = isset($args['idRegistro']) ? $args['idRegistro'] : '';
        $motivo                = isset($args['motivo_declinacion_c']) ? $args['motivo_declinacion_c'] : '';
        $aprueba               = isset($args['aprueba_reactivacion_c']) ? $args['aprueba_reactivacion_c'] : '';
        $fecha_actualizacion   = isset($args['fecha_reactivacion_c']) ? $args['fecha_reactivacion_c'] : '';
        $fecha_actualizacion_neg = isset($args['fecha_reactivacion_neg_c']) ? $args['fecha_reactivacion_neg_c'] : '';
        $estatus               = isset($args['estatus_backlog_c']) ? $args['estatus_backlog_c'] : '';

        $response = [];
        $response['status'] = '';
        $response['description'] = '';

        $GLOBALS['log']->fatal("aprueba" . $aprueba);
        $GLOBALS['log']->fatal("estatus" . $estatus);
        $GLOBALS['log']->fatal("fecha_actualizacion" . $fecha_actualizacion);
        $GLOBALS['log']->fatal("fecha_actualizacion_neg" . $fecha_actualizacion_neg);

        $dt = new DateTime($fecha_actualizacion);
        $dt1 = new DateTime($fecha_actualizacion_neg);

        $id_bl = $idRegistro;

        $beanBL = BeanFactory::retrieveBean('lev_Backlog', $idRegistro, array('disable_row_level_security' => true));
        $GLOBALS['log']->fatal("Backlog-name:" . $beanBL->name);
        $GLOBALS['log']->fatal("Backlog id:" . $beanBL->id);
        $GLOBALS['log']->fatal("Backlog estatus:" . $beanBL->estatus_backlog_c);

        $idAsesor = $beanBL->assigned_user_id;
        /********************************************/
        $beanAsesor = BeanFactory::retrieveBean('Users', $idAsesor, array('disable_row_level_security' => true));
        $id_director_comercial = $this->getIdDirectorComercial($beanAsesor);
        if ($id_director_comercial != "") {
            $info_comercial = $this->getInfoUser($id_director_comercial);
            $name_comercial = $info_comercial['name'];
            $email_comercial = $info_comercial['email'];
        }
        /*****************************************************/
        $beanBL->aprueba_reactivacion_c = $aprueba;

        //$beanBL->fecha_sol_reactivacion_c = $fechasolicitud;
        if ($aprueba == 'ACEPTAR') {
            $accion = 'Aceptada';

            $beanBL->fecha_reactivacion_c = $dt->format("Y-m-d H:i:s");;
            $beanBL->aprobador_reactivacion_c = '';
            $beanBL->motivo_reactivacion_c = '';
            $beanBL->motivo_declinacion_c = '';
            $beanBL->fecha_sol_reactivacion_c = '';
            $beanBL->estatus_backlog_c = $estatus;
            //$query = "UPDATE lev_backlog_cstm set fecha_reactivacion_c = '{$fecha_actualizacion}' WHERE id_c = '{$beanBL->id}'";
            //$result = $db->query($query);
        }
        if ($aprueba == 'RECHAZAR') {
            $accion = 'Rechazada';

            $beanBL->fecha_reactivacion_neg_c = $dt1->format("Y-m-d H:i:s");;

            //$query = "UPDATE lev_backlog_cstm set fecha_reactivacion_neg_c = '{$fecha_actualizacion_neg}' WHERE id_c = '{$beanBL->id}'";
            //$result = $db->query($query);
        }

        $GLOBALS['log']->fatal("Backlog antes save- api estatus:" . $beanBL->estatus_backlog_c);
        $GLOBALS['log']->fatal("Backlog fecha_reactivacion_neg_c:" . $beanBL->fecha_reactivacion_neg_c);

        global $current_user, $sugar_config, $app_list_strings;


        //Recupera BL
        $link_bl = $GLOBALS['sugar_config']['site_url'] . '/#lev_Backlog/' . $id_bl;
        $nbacklog = $beanBL->numero_de_backlog;
        $id_sol = '';

        /*
        if($beanBL->lev_backlog_opportunitiesopportunities_ida != null) {
            if ($beanBL->load_relationship($lev_backlog_opportunitiesopportunities_ida)) {
                //Fetch related record IDs
                $relatedBeans = $bean->$link->get();
                //$GLOBALS['log']->fatal(print_r($relatedBeans, true));
                $id_sol = $relatedBeans->id;
                $GLOBALS['log']->fatal(print_r($id_sol, true));
            }
        }*/

        //Recupera BL
        $beanSol = BeanFactory::retrieveBean('Opportunities', $id_sol, array('disable_row_level_security' => true));
        $link_sol = $GLOBALS['sugar_config']['site_url'] . '/#Opportunities/' . $id_sol;

        //Recupera Asesor asignado
        $id_asesor = $beanBL->assigned_user_id;
        $beanAsesor = BeanFactory::retrieveBean('Users', $id_asesor, array('disable_row_level_security' => true));
        $correoAsesor = $beanAsesor->email1;
        $nombre_asesor = $beanAsesor->first_name . " " . $beanAsesor->last_name;

        try {
            $GLOBALS['log']->fatal("datos de envio");
            $GLOBALS['log']->fatal($nombre_asesor);
            $GLOBALS['log']->fatal($nbacklog);
            $GLOBALS['log']->fatal($link_bl);
            $GLOBALS['log']->fatal($link_sol);
            $GLOBALS['log']->fatal($id_sol);
            $GLOBALS['log']->fatal($accion);
            //Define correo
            $body_correo = $this->buildBodyRespuestaReasignacion($nombre_asesor, $nbacklog, $link_bl, $link_sol, $id_sol, $accion);
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject('UNIFIN CRM: Notificación reactivación Backlog ' . $accion);
            $mailer->addAttachment(new \EmbeddedImage('Copia_de_Recurso-2unileasingazulLOW', 'custom/images_email/Copia_de_Recurso-2unileasingazulLOW.png', 'Copia_de_Recurso-2unileasingazulLOW'), "Copia_de_Recurso-2unileasingazulLOW");
            $body = trim($body_correo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            $GLOBALS['log']->fatal("emails" . $correoAsesor . " - " . $email_comercial);
            //Agrega destinatarios
            $mailer->addRecipientsTo(new EmailIdentity($correoAsesor, $email_comercial));

            $result = $mailer->send();
            $response['status'] = '200';
            $response['description'] = 'Se generó envío de correo';
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar el correo electrónico");
            $GLOBALS['log']->fatal(print_r($e, true));
            $response['status'] = '500';
            $response['description'] = $e;
        }

        try {
            $beanBL->save();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal(print_r($e, true));
        }
        return $response;
    }

    public function autorizaEnvioCorreo($api, $args)
    {

        $idRegistro = $args["idRegistro"];
        $motivo = $args['motivo_reactivacion_c'];
        $aprueba = $args["aprueba_reactivacion_c"];
        $fechasolicitud = $args["fecha_sol_reactivacion_c"];

        $GLOBALS['log']->fatal("fechasolicitud reactivacion backlog" . $fechasolicitud);

        $beanBkl = BeanFactory::retrieveBean('lev_Backlog', $idRegistro, array('disable_row_level_security' => true));
        $idAsesor = $beanBkl->assigned_user_id;

        $beanAsesor = BeanFactory::retrieveBean('Users', $idAsesor, array('disable_row_level_security' => true));
        $correoAsesor = $beanAsesor->email1;
        $nombre_asesor = $beanAsesor->first_name . " " . $beanAsesor->last_name;

        $id_director_comercial = $this->getIdDirectorComercial($beanAsesor);
        if ($id_director_comercial != "") {
            $info_comercial = $this->getInfoUser($id_director_comercial);
            $name_comercial = $info_comercial['name'];
            $email_comercial = $info_comercial['email'];
        }

        $beanAcc = BeanFactory::retrieveBean('Accounts', $beanBkl->account_id_c, array('disable_row_level_security' => true));
        $cuenta = $beanAcc->name;

        $beanOpp = BeanFactory::retrieveBean('Opportunities', $beanBkl->lev_backlog_opportunitiesopportunities_ida, array('disable_row_level_security' => true));
        $numsol = $beanOpp->idsolicitud_c;

        $beanBkl->motivo_reactivacion_c = $motivo;
        $beanBkl->aprueba_reactivacion_c = $aprueba;
        $beanBkl->fecha_sol_reactivacion_c = $fechasolicitud;
        $beanBkl->aprobador_reactivacion_c = $id_director_comercial;
        $beanBkl->save();

        $bodyCorreo = $this->buildBodyEnviaPeticionAutorizacionDirector($name_comercial, $idRegistro, $motivo);
        $GLOBALS['log']->fatal("email director" . $email_comercial);
        if (!empty($email_comercial)) {
            $this->sendEmailPeticionAutorizacionDirector($email_comercial, $bodyCorreo, $beanBkl->name, $cuenta, $numsol, $idRegistro, $id_director_comercial, $motivo);
            return array(
                "status" => "success",
                "msj" => "Se ha enviado el correo"
            );
        } else {
            return array(
                "status" => "info",
                "msj" => "El director comercial no cuenta con un email válido"
            );
        }
    }

    public function getIdDirectorComercial($beanAsesor)
    {
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

    public function getInfoUser($id_user)
    {
        $beanUser = BeanFactory::retrieveBean('Users', $id_user, array('disable_row_level_security' => true));
        $emailUser = $beanUser->email1;
        $first_name = $beanUser->first_name;
        $last_name = $beanUser->last_name;
        $user = [];
        $user['name'] =  $first_name . " " . $last_name;
        $user['email'] = $emailUser;
        return $user;
    }

    public function sendEmailPeticionAutorizacionDirector($emailDirector, $body_correo, $cuenta, $numsol, $idRegistro, $id_director_comercial, $motivo)
    {
        try {
            global $app_list_strings;
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject('VoBo requerido para reactivar operación — ' . $cuenta . ' - Solicitud: ' . $numsol);
            $body = trim($body_correo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            $mailer->addAttachment(new \EmbeddedImage('Copia_de_Recurso-2unileasingazulLOW', 'custom/images_email/Copia_de_Recurso-2unileasingazulLOW.png', 'Copia_de_Recurso-2unileasingazulLOW'), "Copia_de_Recurso-2unileasingazulLOW");

            $mailer->addRecipientsTo(new EmailIdentity($emailDirector));

            $listaEmailsCCPeticionAutorizacion = $app_list_strings['backlog_email_cc_peticion_autorizacion_list'];
            if (!empty($listaEmailsCCPeticionAutorizacion)) {
                foreach ($listaEmailsCCPeticionAutorizacion as $keyNombre => $email) {
                    $GLOBALS['log']->fatal("CC_PeticionAutorizacion: " . $email);
                    $mailer->addRecipientsCc(new EmailIdentity($email));
                }
            }

            $result = $mailer->send();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar el correo electrónico");
            $GLOBALS['log']->fatal($e->getMessage());
        }
    }

    public function buildBodyEnviaPeticionAutorizacionDirector($nombreDirectorComercial, $idRegistro, $motivo)
    {
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
        $linkbkl = $GLOBALS['sugar_config']['site_url'] . '/#lev_Backlog/' . $idRegistro;
        $htmlLink = '<b><a id="linkbkl" href="' . $linkbkl . '">Ver detalle en CRM</a></b>';
        $aceptabkl = $GLOBALS['sugar_config']['site_url'] . '/#lev_Backlog/layout/reactivacionBacklog?accion=aceptar&id=' . $idRegistro;
        $htmlAcepta = '<b><a id="aceptabkl" href="' . $aceptabkl . '">Aprobar</a></b>';
        $rechazabkl = $GLOBALS['sugar_config']['site_url'] . '/#lev_Backlog/layout/reactivacionBacklog?accion=rechazar&id=' . $idRegistro;
        $htmlRechaza = '<b><a id="rechazabkl" href="' . $rechazabkl . '">Rechazar</a></b>';
        $mailHTML = '<head>
            <title></title>
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
            <meta content="width=device-width, initial-scale=1.0" name="viewport"/><!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
            <style>
                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    padding: 0;
                }

                a[x-apple-data-detectors] {
                    color: inherit !important;
                    text-decoration: inherit !important;
                }

                #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                }

                p {
                    line-height: inherit
                }

                .desktop_hide,
                .desktop_hide table {
                    mso-hide: all;
                    display: none;
                    max-height: 0px;
                    overflow: hidden;
                }

                .image_block img+div {
                    display: none;
                }

                @media (max-width:620px) {
                    .mobile_hide {
                        display: none;
                    }

                    .row-content {
                        width: 100% !important;
                    }

                    .stack .column {
                        width: 100%;
                        display: block;
                    }

                    .mobile_hide {
                        min-height: 0;
                        max-height: 0;
                        max-width: 0;
                        overflow: hidden;
                        font-size: 0px;
                    }

                    .desktop_hide,
                    .desktop_hide table {
                        display: table !important;
                        max-height: none !important;
                    }

                    .row-1 .column-1 .block-1.paragraph_block td.pad>div,
                    .row-3 .column-1 .block-1.paragraph_block td.pad>div,
                    .row-5 .column-1 .block-1.paragraph_block td.pad>div {
                        text-align: center !important;
                        font-size: 14px !important;
                    }

                    .row-1 .column-1 .block-1.paragraph_block td.pad,
                    .row-3 .column-1 .block-1.paragraph_block td.pad,
                    .row-5 .column-1 .block-1.paragraph_block td.pad {
                        padding: 20px 35px !important;
                    }

                    .row-1 .column-1,
                    .row-3 .column-1,
                    .row-4 .column-1,
                    .row-5 .column-1 {
                        padding: 0 !important;
                    }
                }
            </style>
            </head>
            <body style="background-color: #e4e7e7; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
            <table border="0" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #e4e7e7;" width="100%">
                <tbody>
                    <tr>
                        <td>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #56adff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad">
                                                                        <div style="color:#041e41;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:6px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:justify;mso-line-height-alt:9px;"> </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:25px;padding-left:50px;padding-right:50px;padding-top:25px;">
                                                                        <div style="color:#041e41;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:justify;mso-line-height-alt:24px;">
                                                                            <p style="margin: 0; margin-bottom: 16px;">Hola! <strong>' . $nombreDirectorComercial . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">El asesor a tu cargo, ' . $asesor . ', solicita tu visto bueno para reactivar la operación del cliente ' . $cliente . ',<br>
                                                                                (ID ' . $solicitud . '), actualmente Declinada.<br><br>
                                                                                Motivo breve del asesor: <p>' . $motivo . '</p><br>
                                                                                Datos de referencia:<br>
                                                                                Estatus actual: Declinada<br>
                                                                                Monto autorizado/preautorizado: ' . $monto . '<br>
                                                                                Monto prometido: ' . $prometido . '<br>
                                                                                Fecha prometida: ' . $fecha . '<br>
                                                                                Origen: ' . $origen . '<br>
                                                                                Puedes autorizar o rechazar la reactivación aquí:<br>
                                                                                ' . $htmlLink . '<br>

                                                                                <table border="0" cellpadding="10" cellspacing="0" class="button_block block-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                <tr>
                                                                                    <td class="">
                                                                                        <div align="center" class="alignment"><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:43px;width:136px;v-text-anchor:middle;" arcsize="63%" stroke="false" fillcolor="#05aa6d"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px"><![endif]-->
                                                                                            <div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#05aa6d;border-radius:27px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;text-align:center;mso-border-alt:none;word-break:keep-all;"><span style="padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;">
                                                                                            <span style="word-break: break-word; line-height: 32px;">
                                                                                                <a id="linkPO" href="' .  $aceptabkl . '"><strong>Aceptar<br></strong></a>
                                                                                            </span>
                                                                                            </span></div><!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                                        </div>
                                                                                        </td>
                                                                                        <td class="pad">
                                                                                        <div align="center" class="alignment"><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:43px;width:136px;v-text-anchor:middle;" arcsize="63%" stroke="false" fillcolor="#e46962"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px"><![endif]-->
                                                                                            <div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#e46962;border-radius:27px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;text-align:center;mso-border-alt:none;word-break:keep-all;"><span style="padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;">
                                                                                            <span style="word-break: break-word; line-height: 32px;">
                                                                                                <a id="linkPO" href="' . $rechazabkl . '"><strong>Rechazar<br></strong></a>
                                                                                            </span>
                                                                                            </span></div><!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                </table>



                                                                                Esta acción quedará registrada en el historial. Cualquier cambio notificará al asesor y a ti.<br>
                                                                            </p>
                                                                            <br>
                                                                            <p style="margin: 0;">Si tienes alguna duda contactar a:<br>
                                                                            Equipo CRM<br>
                                                                            Inteligencia de Negocios<br>
                                                                            Tel.: (55)5249 5800 Ext.5737 y 5677<br></p>
                                                                            <p style="margin: 0;">Atentamente, UNIFIN.</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 15px; padding-left: 15px; padding-right: 15px; padding-top: 15px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="image_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:20px;width:100%;">
                                                                        <div align="center" class="alignment" style="line-height:10px"><img src="cid:Copia_de_Recurso-2unileasingazulLOW" style="display: block; height: auto; border: 0; max-width: 102px; width: 100%;" width="102"/></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #dde1e9; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:25px;padding-left:30px;padding-right:30px;padding-top:25px;">
                                                                        <div style="color:#000000;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:12px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:center;mso-line-height-alt:14.399999999999999px;">
                                                                            <p style="margin: 0;"><em>Información confidencial y exclusiva para uso interno de Unifin.</em></p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table><!-- End -->
            </body>';
        return $mailHTML;
    }

    public function buildBodyRespuestaReasignacion($nombre_asesor, $nbacklog, $link_bl, $link_sol, $id_sol, $accion)
    {
        $mailHTML = '<head>
            <title></title>
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
            <meta content="width=device-width, initial-scale=1.0" name="viewport"/><!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
            <style>
                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    padding: 0;
                }

                a[x-apple-data-detectors] {
                    color: inherit !important;
                    text-decoration: inherit !important;
                }

                #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                }

                p {
                    line-height: inherit
                }

                .desktop_hide,
                .desktop_hide table {
                    mso-hide: all;
                    display: none;
                    max-height: 0px;
                    overflow: hidden;
                }

                .image_block img+div {
                    display: none;
                }

                @media (max-width:620px) {
                    .mobile_hide {
                        display: none;
                    }

                    .row-content {
                        width: 100% !important;
                    }

                    .stack .column {
                        width: 100%;
                        display: block;
                    }

                    .mobile_hide {
                        min-height: 0;
                        max-height: 0;
                        max-width: 0;
                        overflow: hidden;
                        font-size: 0px;
                    }

                    .desktop_hide,
                    .desktop_hide table {
                        display: table !important;
                        max-height: none !important;
                    }

                    .row-1 .column-1 .block-1.paragraph_block td.pad>div,
                    .row-3 .column-1 .block-1.paragraph_block td.pad>div,
                    .row-5 .column-1 .block-1.paragraph_block td.pad>div {
                        text-align: center !important;
                        font-size: 14px !important;
                    }

                    .row-1 .column-1 .block-1.paragraph_block td.pad,
                    .row-3 .column-1 .block-1.paragraph_block td.pad,
                    .row-5 .column-1 .block-1.paragraph_block td.pad {
                        padding: 20px 35px !important;
                    }

                    .row-1 .column-1,
                    .row-3 .column-1,
                    .row-4 .column-1,
                    .row-5 .column-1 {
                        padding: 0 !important;
                    }
                }
            </style>
            </head>
            <body style="background-color: #e4e7e7; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
            <table border="0" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #e4e7e7;" width="100%">
                <tbody>
                    <tr>
                        <td>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #56adff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad">
                                                                        <div style="color:#041e41;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:6px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:justify;mso-line-height-alt:9px;"> </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:25px;padding-left:50px;padding-right:50px;padding-top:25px;">
                                                                        <div style="color:#041e41;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:justify;mso-line-height-alt:24px;">
                                                                            <p style="margin: 0; margin-bottom: 16px;">Hola! <strong>' . $nombre_asesor . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Te informamos que tu solicitud para la reactivación del Backlog <a id="linkSO" href="' . $link_bl . '"> <strong>' . $nbacklog . '</strong></a> del Cliente: <a id="linkSO" href="' . $link_so . '"> <strong>' . $id_sol . '</strong></a> ha sido <strong>' . $accion . '</strong> </p>
                                                                            <br/>
                                                                            <p style="margin: 0;">Atentamente, UNIFIN.</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 15px; padding-left: 15px; padding-right: 15px; padding-top: 15px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="image_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:20px;width:100%;">
                                                                        <div align="center" class="alignment" style="line-height:10px"><img src="cid:Copia_de_Recurso-2unileasingazulLOW" style="display: block; height: auto; border: 0; max-width: 102px; width: 100%;" width="102"/></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #cdd2d9;" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #dde1e9; color: #000; width: 600px; margin: 0 auto;" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                                                            <table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                <tr>
                                                                    <td class="pad" style="padding-bottom:25px;padding-left:30px;padding-right:30px;padding-top:25px;">
                                                                        <div style="color:#000000;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:12px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:center;mso-line-height-alt:14.399999999999999px;">
                                                                            <p style="margin: 0;"><em>Información confidencial y exclusiva para uso interno de Unifin.</em></p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table><!-- End -->
            </body>';
        return $mailHTML;
    }
}

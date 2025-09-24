<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
class SendEmailBacklog extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'SendEmailBacklog' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('notificaDirectorBacklog'),
                'pathVars' => array(''),
                'method' => 'notificaCorreoDirectorBacklog',
                'shortHelp' => 'Envía notificación por correo, un cambio de monto, fecha o baja del Backlog al Director',
            ),
        );
    }

    public function notificaCorreoDirectorBacklog($api, $args)
    {
        $GLOBALS['log']->fatal("---------- notificaCorreoDirectorBacklog -----------");
        global $app_list_strings;

        $tipo = isset($args['tipo']) ? $args['tipo'] : '';
        $nombre_backlog = isset($args['nombre_backlog']) ? $args['nombre_backlog'] : '';
        $id_backlog = isset($args['id_backlog']) ? $args['id_backlog'] : '';
        $id_cuenta = isset($args['id_cuenta']) ? $args['id_cuenta'] : '';
        $id_asesor = isset($args['id_asesor']) ? $args['id_asesor'] : '';
        $emailDirector = '';
        $nombreDirector = '';

        $GLOBALS['log']->fatal("tipo " . $tipo);
        $GLOBALS['log']->fatal("nombre_backlog " . $nombre_backlog);
        $GLOBALS['log']->fatal("id_backlog " . $id_backlog);
        $GLOBALS['log']->fatal("id_cuenta " . $id_cuenta);
        $GLOBALS['log']->fatal("id_asesor " . $id_asesor);        

        $response = [];
        $response['status'] = '';
        $response['description'] = '';
        //Link Backlog
        $link_backlog = $GLOBALS['sugar_config']['site_url'] . '/#lev_Backlog/' . $id_backlog;
        //Backlog
        $beanBL = BeanFactory::retrieveBean('lev_Backlog', $id_backlog, array('disable_row_level_security' => true));
        $monto_comp_anterior = number_format($beanBL->fetched_row['monto_comprometido'], 2, '.', ',');
        $monto_comp_nuevo = number_format($beanBL->monto_comprometido, 2, '.', ',');
        $fecha_comp_anterior = $beanBL->fetched_row['fecha_compromiso_c'];
        $fecha_comp_nuevo = $beanBL->fecha_compromiso_c;
        $tipificacion_anterior = $beanBL->fetched_row['tipificacion_riesgo_c'];
        $tipificacion_nuevo = $beanBL->tipificacion_riesgo_c;
        $motivo_declinacion = $beanBL->motivo_declinacion_c;

        $GLOBALS['log']->fatal("monto_comp_anterior " . $monto_comp_anterior);
        $GLOBALS['log']->fatal("monto_comp_nuevo " . $monto_comp_nuevo);
        $GLOBALS['log']->fatal("fecha_comp_anterior " . $fecha_comp_anterior);
        $GLOBALS['log']->fatal("fecha_comp_nuevo " . $fecha_comp_nuevo);
        $GLOBALS['log']->fatal("tipificacion_anterior " . $tipificacion_anterior);
        $GLOBALS['log']->fatal("tipificacion_nuevo " . $tipificacion_nuevo);
        $GLOBALS['log']->fatal("motivo_declinacion " . $motivo_declinacion);        
        
        //Asesor
        $beanAsesor = BeanFactory::retrieveBean('Users', $id_asesor, array('disable_row_level_security' => true));
        $nombreAsesor = $beanAsesor->first_name . " " . $beanAsesor->last_name;
        //Cuenta relacionada
        $beanCuenta = BeanFactory::retrieveBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));
        $nombreCuenta = $beanCuenta->name;
        //Director
        $id_director = $this->getIdDirectorComercial($beanAsesor);
        //Valida Director
        if ($id_director != "") {
            $beanDirector = BeanFactory::retrieveBean('Users', $id_director, array('disable_row_level_security' => true));
            $nombreDirector = $beanDirector->first_name . " " . $beanDirector->last_name;
            $emailDirector = $beanDirector->email1;
        }
        //Descripciones de las listas de valores
        $descripcionTipificacionAnterior = isset($app_list_strings['tipificacion_riesgo_list'][$tipificacion_anterior]) ? $app_list_strings['tipificacion_riesgo_list'][$tipificacion_anterior] : '';
        $descripcionTipificacionNuevo = isset($app_list_strings['tipificacion_riesgo_list'][$tipificacion_nuevo]) ? $app_list_strings['tipificacion_riesgo_list'][$tipificacion_nuevo] : '';
        $descripcionMotivoDeclinacion = isset($app_list_strings['backlog_motivo_declinacion_list'][$motivo_declinacion]) ? $app_list_strings['backlog_motivo_declinacion_list'][$motivo_declinacion] : '';

        try {
            //Define correo
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();

            if ($tipo === 'cambio') {
                $mailer->setSubject('Cambio en BL - ' . $nombre_backlog . ' por ' . $nombreAsesor . ' ');
                $body_correo = $this->buildBodyNotificaCambioBacklog(
                    $nombreDirector,
                    $nombreAsesor,
                    $nombre_backlog,
                    $link_backlog,
                    $nombreCuenta,
                    $monto_comp_anterior,
                    $monto_comp_nuevo,
                    $fecha_comp_anterior,
                    $fecha_comp_nuevo,
                    $descripcionTipificacionAnterior,
                    $descripcionTipificacionNuevo
                );
            }
            if ($tipo === 'baja') {
                $mailer->setSubject('Baja de BL - ' . $nombre_backlog . ' por ' . $nombreAsesor . ' ');
                $body_correo = $this->buildBodyNotificaBajaBacklog(
                    $nombreDirector,
                    $nombreAsesor,
                    $nombre_backlog,
                    $link_backlog,
                    $nombreCuenta,
                    $monto_comp_nuevo,
                    $fecha_comp_nuevo,
                    $descripcionTipificacionNuevo,
                    $descripcionMotivoDeclinacion
                );
            }

            $mailer->addAttachment(new \EmbeddedImage('Copia_de_Recurso-2unileasingazulLOW', 'custom/images_email/Copia_de_Recurso-2unileasingazulLOW.png', 'Copia_de_Recurso-2unileasingazulLOW'), "Copia_de_Recurso-2unileasingazulLOW");
            $body = trim($body_correo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            //Agrega destinatarios
            if ($emailDirector !== '') {
                $mailer->addRecipientsTo(new EmailIdentity($emailDirector, $nombreDirector));
                $result = $mailer->send();

                $response['status'] = '200';
                $response['description'] = 'Se generó envío de correo';
                $GLOBALS['log']->fatal("Se envió correo de notificación al Director... " . $nombreDirector . " - " . $emailDirector);
            } else {
                $response['status'] = '401';
                $response['description'] = 'No se encontró correo del Director a notificar';
            }
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Error enviando correo de notificación: " . $e->getMessage());
            $response['status'] = '500';
            $response['description'] = $e->getMessage();
        }
        return $response;
    }

    public function buildBodyNotificaCambioBacklog($nombreDirector, $nombreAsesor, $nombre_backlog, $link_backlog, $nombreCuenta, $monto_comp_anterior, $monto_comp_nuevo, $fecha_comp_anterior, $fecha_comp_nuevo, $descripcionTipificacionAnterior, $descripcionTipificacionNuevo)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Hola, <strong>' . $nombreDirector . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">El asesor <strong>' . $nombreAsesor . '</strong> actualizó el <a id="linkPO" href="' . $link_backlog . '"> <strong>' . $nombre_backlog . '</strong></a> del cliente <strong>' . $nombreCuenta . '</strong></p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Antes -> Después:</p>
                                                                            <br>
                                                                            <ul style="margin:0;padding-left:20px;">
                                                                                <li><strong>Monto Comprometido:</strong> ' . $monto_comp_anterior . ' → <strong>' . $monto_comp_nuevo . '</strong></li>
                                                                                <li><strong>Fecha Compromiso:</strong> ' . $fecha_comp_anterior . ' → <strong>' . $fecha_comp_nuevo . '</strong></li>
                                                                                <li><strong>Tipificación de riesgo:</strong> ' . $descripcionTipificacionAnterior . ' → <strong>' . $descripcionTipificacionNuevo . '</strong></li>
                                                                            </ul>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Validaciones aplicadas: Monto ≤ autorizado; Fecha en calendario.</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Ver detalle en CRM</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Si tienes alguna duda contactar a:</p>
                                                                            <p style="margin: 0;">Equipo CRM</p>
                                                                            <p style="margin: 0;">Inteligencia de Negocios</p>
                                                                            <p style="margin: 0;">T: (55)5249 5800 Ext.5737 y 5677</p>
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

    public function buildBodyNotificaBajaBacklog($nombreDirector, $nombreAsesor, $nombre_backlog, $link_backlog, $nombreCuenta, $monto_comp_nuevo, $fecha_comp_nuevo, $descripcionTipificacionNuevo, $descripcionMotivoDeclinacion)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Hola, <strong>' . $nombreDirector . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">El asesor <strong>' . $nombreAsesor . '</strong> dio de baja el <a id="linkPO" href="' . $link_backlog . '"> <strong>' . $nombre_backlog . '</strong></a> del cliente <strong>' . $nombreCuenta . '</strong></p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Últimos datos registrados:</p>
                                                                            <br>
                                                                            <ul style="margin:0;padding-left:20px;">
                                                                                <li><strong>Monto Comprometido:</strong> ' . $monto_comp_nuevo . '</strong></li>
                                                                                <li><strong>Fecha Compromiso:</strong> ' . $fecha_comp_nuevo . '</strong></li>
                                                                                <li><strong>Tipificación de riesgo:</strong> ' . $descripcionTipificacionNuevo . '</strong></li>
                                                                                <li><strong>Motivo de baja (si aplica):</strong> ' . $descripcionMotivoDeclinacion . '</strong></li>
                                                                            </ul>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Ver detalle en CRM</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Si tienes alguna duda contactar a:</p>
                                                                            <p style="margin: 0;">Equipo CRM</p>
                                                                            <p style="margin: 0;">Inteligencia de Negocios</p>
                                                                            <p style="margin: 0;">T: (55)5249 5800 Ext.5737 y 5677</p>
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
}

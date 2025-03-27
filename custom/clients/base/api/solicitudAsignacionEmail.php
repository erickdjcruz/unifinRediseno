<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class SolicitudAsignacionEmail extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'solicitudAsignacionEmail' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('solicitudAsignacionEmail'),
                'pathVars' => array(''),
                'method' => 'sendEmailAsignacionCuentas',
                'shortHelp' => 'Envía notificación por email a respectivos usuarios para solicitar asignación de cuentas',
            ),
            'voboDirectorRegionalCuentas' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('voboDirectorRegionalCuentas'),
                'pathVars' => array(''),
                'method' => 'voboAsignacionEmail',
                'shortHelp' => 'Envía correo a de VOBO del director regional para la Asignación de cuenta',
            ),
            'autorizaAsignacionCuenta' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('autorizaAsignacionCuenta'),
                'pathVars' => array(''),
                'method' => 'procesoAutorizaAsignacion',
                'shortHelp' => 'Envía correo a través de la aprobación del Director Regional',
            ),
            'rechazoAsignacionCuenta' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('rechazoAsignacionCuenta'),
                'pathVars' => array(''),
                'method' => 'procesoRechazoAsignacion',
                'shortHelp' => 'Envía correo a través del rechazo del Director Regional',
            ),
        );
    }

    public function sendEmailAsignacionCuentas($api, $args)
    {
        $GLOBALS['log']->fatal("...SendEmailAsignacionCuentas...");
        $idCuenta = $args['id_cuenta'];
        $idAsesorSolicita = $args['id_asesor_solicita'];
        $response = "";

        if (!empty($idCuenta)) {
            $beanAccount = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
            $nombreCuenta = $beanAccount->name;
        }

        if (!empty($idAsesorSolicita)) {
            $beanAsesorSolicita = BeanFactory::retrieveBean('Users', $idAsesorSolicita, array('disable_row_level_security' => true));
            $nombreAsesorSolicita = $beanAsesorSolicita->first_name . " " . $beanAsesorSolicita->last_name;
            $emailAsesorSolicita = $beanAsesorSolicita->email1;
            $idDirectorInformaA = $beanAsesorSolicita->reports_to_id;
        }

        if (!empty($idDirectorInformaA)) {
            $beanDirectorInformaA = BeanFactory::retrieveBean('Users', $idDirectorInformaA, array('disable_row_level_security' => true));
            $nombreDirectorInformaA = $beanDirectorInformaA->first_name . " " . $beanDirectorInformaA->last_name;
            $emailDirectorInformaA = $beanDirectorInformaA->email1;
        }
        $GLOBALS['log']->fatal("idCuenta: " . $idCuenta);
        $GLOBALS['log']->fatal("nombreCuenta: " . $nombreCuenta);
        $GLOBALS['log']->fatal("beanAsesorSolicita: " . $nombreAsesorSolicita . " " . $emailAsesorSolicita);
        $GLOBALS['log']->fatal("beanDirectorInformaA: " . $nombreDirectorInformaA . " " . $emailDirectorInformaA);

        // REASIGNACION DE CUENTA PRINCIPAL
        if (!empty($idCuenta) && !empty($idAsesorSolicita)) {
            $this->procesoReasignacionCuenta($idCuenta, $idAsesorSolicita);
        }

        //NOTIFICACION POR EMAIL
        $body_mail_asesor_solicita = $this->buildBodyNotificaAsesorAsignacion($nombreAsesorSolicita, $nombreCuenta);
        $body_mail_director_informa_a = $this->buildBodyNotificaDirectorInformaA($nombreDirectorInformaA, $nombreCuenta, $nombreAsesorSolicita);
        //ASESOR SOLICITA
        if (!empty($emailAsesorSolicita)) {
            $this->sendEmailAsesorCuentas(
                'Recarterización de clientes/prospectos ' . $nombreCuenta,
                $body_mail_asesor_solicita,
                $emailAsesorSolicita,
                $nombreAsesorSolicita
            );
            $response .= "<br>Se envió notificación a: <b>" . $nombreAsesorSolicita . "</b> ";
        }
        //DIRECTOR INFORMA A
        if (!empty($emailDirectorInformaA)) {
            $this->sendEmailAsesorCuentas(
                'Reasignación de cliente/prospecto ' . $nombreCuenta,
                $body_mail_director_informa_a,
                $emailDirectorInformaA,
                $nombreDirectorInformaA
            );
            $response .= "y a <b>" . $nombreDirectorInformaA . "</b>, de la cuenta <b>" . $nombreCuenta . "</b>";
        }

        return $response;
    }

    public function buildBodyNotificaAsesorAsignacion($nombre_asesor_solicita, $nombre_cuenta)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombre_asesor_solicita . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Tu solicitud de reasignación del Cliente/Prospecto <strong>' . $nombre_cuenta . '</strong> fue autorizada y ya se ha asignado a tu usuario, por favor confirma que así sea.</p>
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

    public function buildBodyNotificaDirectorInformaA($nombreDirectorInformaA, $nombre_cuenta, $nombreAsesorSolicita)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombreDirectorInformaA . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Le notificamos que la solicitud de reasignación del cliente/Prospecto <strong>' . $nombre_cuenta . '</strong> fue autorizada y se encuentra a nombre del usuario <strong>' . $nombreAsesorSolicita . '.</strong></p>
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

    public function sendEmailAsesorCuentas($subject, $bodyMail, $emailDestinatario, $nombreDestinatario)
    {
        try {
            global $app_list_strings;
            //LISTA DE CORREOS PARA COPIA AL DIRECTOR ASIGNACION
            $listaEmailsCCDirectorAsignacion = $app_list_strings['copia_informa_director_asignacion_list'];
            $arr_emails = array();
            foreach ($listaEmailsCCDirectorAsignacion as $key => $email) {
                array_push($arr_emails, $email);
            }

            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailer->setSubject($subject);
            $mailer->addAttachment(new \EmbeddedImage('Copia_de_Recurso-2unileasingazulLOW', 'custom/images_email/Copia_de_Recurso-2unileasingazulLOW.png', 'Copia_de_Recurso-2unileasingazulLOW'), "Copia_de_Recurso-2unileasingazulLOW");
            $mailer->setHtmlBody(trim($bodyMail));
            $mailer->addRecipientsTo(new EmailIdentity($emailDestinatario, $nombreDestinatario));

            if (count($arr_emails) > 0) {
                //LISTA DE CORREOS CON COPIA AL DIRECTOR ASIGNACION
                for ($i = 0; $i < count($arr_emails); $i++) {
                    $GLOBALS['log']->fatal("AGREGANDO CORREOS CON COPIA AL DIRECTOR ASIGNACION: " . $arr_emails[$i]);
                    $mailer->addRecipientsCc(new EmailIdentity($arr_emails[$i], $arr_emails[$i]));
                }
            }
            $GLOBALS['log']->fatal("ENVIANDO CORREO A: " . $emailDestinatario);
            $mailer->send();

            return true; //Éxito en el envío

        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar el correo electrónico a " . $emailDestinatario);
            $GLOBALS['log']->fatal(print_r($e, true));

            return false; //Fallo en el envío
        }
    }

    public function procesoReasignacionCuenta($idCuenta, $idAsesorSolicita)
    {
        // REASIGNACION DE CUENTA PRINCIPAL
        if (!empty($idCuenta) && !empty($idAsesorSolicita)) {
            // BUSQUEDA DE PRODUCTO LEASING DE LA CUENTA PRINCIPAL
            $selectProductoLeasing = "SELECT up.id as idProductoLeasing, up.assigned_user_id
            FROM accounts a
            INNER JOIN accounts_uni_productos_1_c ap ON a.id = ap.accounts_uni_productos_1accounts_ida
            INNER JOIN uni_productos up ON up.id = ap.accounts_uni_productos_1uni_productos_idb
            INNER JOIN uni_productos_cstm upc ON upc.id_c = up.id
            WHERE a.id = '{$idCuenta}' AND up.tipo_producto = '1' AND up.deleted = '0'";

            $resultpl = $GLOBALS['db']->fetchOne($selectProductoLeasing);

            if ($resultpl && !empty($resultpl['idProductoLeasing'])) {
                $idProductoLeasing = $resultpl['idProductoLeasing'];
                $assignedUserProducto = $resultpl['assigned_user_id'];

                $GLOBALS['log']->fatal("idProductoLeasing (Cuenta Principal): " . $idProductoLeasing);

                // REASIGNACION SOLO SI ES NECESARIO
                if ($assignedUserProducto !== $idAsesorSolicita) {
                    $beanUniProductos = BeanFactory::retrieveBean('uni_Productos', $idProductoLeasing, array('disable_row_level_security' => true));
                    $beanUniProductos->estatus_atencion = '1';
                    $beanUniProductos->assigned_user_id = $idAsesorSolicita;
                    $beanUniProductos->save();
                }
            }

            // REASIGNACION DE LA CUENTA PRINCIPAL SOLO SI ES NECESARIO
            $selectCuenta = "SELECT user_id_c FROM accounts_cstm WHERE id_c = '{$idCuenta}'";
            $resultCuenta = $GLOBALS['db']->fetchOne($selectCuenta);

            if ($resultCuenta && $resultCuenta['user_id_c'] !== $idAsesorSolicita) {
                $beanCuentas = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
                $beanCuentas->tct_status_atencion_ddw_c = 'Atendido';
                $beanCuentas->user_id_c = $idAsesorSolicita;
                $beanCuentas->save();
            }

            // BUSQUEDA DE CUENTAS HIJAS RELACIONADAS
            $selectRelacionesHijas  = "SELECT rrc.account_id1_c as idRelacionCuentaHija
            FROM rel_relaciones rr
            INNER JOIN rel_relaciones_accounts_1_c rra ON rra.rel_relaciones_accounts_1rel_relaciones_idb = rr.id
            INNER JOIN rel_relaciones_cstm rrc ON rrc.id_c = rr.id 
            WHERE rra.rel_relaciones_accounts_1accounts_ida = '{$idCuenta}' AND rr.deleted = 0";

            $relacionesResult = $GLOBALS['db']->query($selectRelacionesHijas);

            while ($rowRelacion = $GLOBALS['db']->fetchByAssoc($relacionesResult)) {
                $idRelacionCuentaHija = $rowRelacion['idRelacionCuentaHija'];
                $GLOBALS['log']->fatal("idRelacionCuentaHija: " . $idRelacionCuentaHija);

                // REASIGNACION DE LA CUENTA HIJA SOLO SI ES NECESARIO
                $selectCuentaHija = "SELECT user_id_c FROM accounts_cstm WHERE id_c = '{$idRelacionCuentaHija}'";
                $resultCuentaHija = $GLOBALS['db']->fetchOne($selectCuentaHija);

                if ($resultCuentaHija && $resultCuentaHija['user_id_c'] !== $idAsesorSolicita) {
                    $beanCuentasHija = BeanFactory::retrieveBean('Accounts', $idRelacionCuentaHija, array('disable_row_level_security' => true));
                    $beanCuentasHija->tct_status_atencion_ddw_c = 'Atendido';
                    $beanCuentasHija->user_id_c = $idAsesorSolicita;
                    $beanCuentasHija->save();
                }

                // BUSQUEDA DEL PRODUCTO LEASING DE LA CUENTA HIJA
                $selectProductoLeasingHija = "SELECT up.id as idProductoLeasing, up.assigned_user_id
                FROM accounts a
                INNER JOIN accounts_uni_productos_1_c ap ON a.id = ap.accounts_uni_productos_1accounts_ida
                INNER JOIN uni_productos up ON up.id = ap.accounts_uni_productos_1uni_productos_idb
                INNER JOIN uni_productos_cstm upc ON upc.id_c = up.id
                WHERE a.id = '{$idRelacionCuentaHija}' AND up.tipo_producto = '1' AND up.deleted = '0'";

                $resultplHija = $GLOBALS['db']->fetchOne($selectProductoLeasingHija);

                if ($resultplHija && !empty($resultplHija['idProductoLeasing'])) {
                    $idProductoLeasingHija = $resultplHija['idProductoLeasing'];
                    $assignedUserProductoHija = $resultplHija['assigned_user_id'];

                    $GLOBALS['log']->fatal("idProductoLeasing (Cuenta Hija): " . $idProductoLeasingHija);

                    // REASIGNACION SOLO SI ES NECESARIO
                    if ($assignedUserProductoHija !== $idAsesorSolicita) {
                        $beanUniProductoHija = BeanFactory::retrieveBean('uni_Productos', $idProductoLeasingHija, array('disable_row_level_security' => true));
                        $beanUniProductoHija->estatus_atencion = '1';
                        $beanUniProductoHija->assigned_user_id = $idAsesorSolicita;
                        $beanUniProductoHija->save();
                    }
                }
            }

            // BUSQUEDA DE LEAD RELACIONADO A LA CUENTA
            $selectRelacionLead  = "SELECT id as idLead, assigned_user_id 
            FROM leads WHERE account_id = '{$idCuenta}' AND deleted = '0'";

            $leadRelResult = $GLOBALS['db']->fetchOne($selectRelacionLead);

            if ($leadRelResult && !empty($leadRelResult['idLead'])) {
                $idLead = $leadRelResult['idLead'];
                $assignedUserLead = $leadRelResult['assigned_user_id'];

                $GLOBALS['log']->fatal("idLead (Lead Relacionado a Cuenta): " . $idLead);

                // REASIGNACION SOLO SI ES NECESARIO
                if ($assignedUserLead !== $idAsesorSolicita) {
                    $updateLeadAsesor = "
                        UPDATE leads
                        SET assigned_user_id = '{$idAsesorSolicita}'
                        WHERE id = '{$idLead}'
                    ";
                    $GLOBALS['db']->query($updateLeadAsesor);
                    //Establece nuevo registro en tabla de auditoria
                    $this->insertAuditRecord('leads', $idLead, 'assigned_user_id', 'id', $assignedUserLead, $idAsesorSolicita);
                }

                // BUSQUEDA DE PUBLICO OBJETIVO (PO) RELACIONADO AL LEAD
                $selectRelacionPO  = "SELECT p.id as idPO, p.assigned_user_id
                FROM prospects_leads_1_c pl
                INNER JOIN prospects p ON p.id = pl.prospects_leads_1prospects_ida AND p.deleted = 0
                INNER JOIN prospects_cstm pc ON pc.id_c = p.id
                WHERE pl.prospects_leads_1leads_idb = '{$idLead}'";

                $poRelResult = $GLOBALS['db']->fetchOne($selectRelacionPO);

                if ($poRelResult && !empty($poRelResult['idPO'])) {
                    $idPO = $poRelResult['idPO'];
                    $assignedUserPO = $poRelResult['assigned_user_id'];

                    $GLOBALS['log']->fatal("idPO (PO Relacionado a Lead de la Cuenta): " . $idPO);

                    // REASIGNACION SOLO SI ES NECESARIO
                    if ($assignedUserPO !== $idAsesorSolicita) {
                        $updatePOAsesor = "
                            UPDATE prospects
                            SET assigned_user_id = '{$idAsesorSolicita}'
                            WHERE id = '{$idPO}'
                        ";
                        $GLOBALS['db']->query($updatePOAsesor);
                        //Establece nuevo registro en tabla de auditoria
                        $this->insertAuditRecord('prospects', $idPO, 'assigned_user_id', 'id', $assignedUserPO, $idAsesorSolicita);
                    }
                }
            }
        }
    }

    public function insertAuditRecord($module, $parent_id, $field_name, $data_type, $before_value, $after_value)
    {
        global $current_user;
        $idAudit = create_guid();
        $eventAudit = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        $sqlInsert = "INSERT INTO {$module}_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
        VALUES ('{$idAudit}', '{$parent_id}', '{$date}', '{$current_user->id}', '{$field_name}', '{$data_type}', '{$before_value}', '{$after_value}', '', '', '{$eventAudit}', '{$date}')";

        $GLOBALS['db']->query($sqlInsert);
    }

    public function voboAsignacionEmail($api, $args)
    {
        $GLOBALS['log']->fatal("...voboAsignacionEmail...");
        $idCuenta = $args['id_cuenta'];
        $idAsesorSolicita = $args['id_asesor_solicita'];
        $idAsesorAnterior = $args['id_asesor_anterior'];
        $esDiferenteRegion = $args['es_diferente_region'] === 'true' ? 1: 0;
        $esEjecutivoEstrategiaComercial = $args['es_ejecutivo_estrategia'] === 'true' ? 1: 0;
        $response = "";
        $linkAutorizaMismaRegion = $GLOBALS['sugar_config']['site_url'] . '/?entryPoint=solicitudAsignacionRegion&accion=aceptar&id=' . $idCuenta;
        $linkRechazoMismaRegion = $GLOBALS['sugar_config']['site_url'] . '/?entryPoint=solicitudAsignacionRegion&accion=rechazar&id=' . $idCuenta;
        $linkAutorizaDiferenteRegion = $GLOBALS['sugar_config']['site_url'] . '/?entryPoint=solicitudAsignacionDifRegion&accion=aceptar&id=' . $idCuenta;
        $linkRechazoDiferenteRegion = $GLOBALS['sugar_config']['site_url'] . '/?entryPoint=solicitudAsignacionDifRegion&accion=rechazar&id=' . $idCuenta;

        if (!empty($idCuenta)) {
            $beanAccount = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
            $nombreCuenta = $beanAccount->name;
        }
        if (!empty($idAsesorSolicita)) {
            $beanAsesorSolicita = BeanFactory::retrieveBean('Users', $idAsesorSolicita, array('disable_row_level_security' => true));
            $nombreAsesorSolicita = $beanAsesorSolicita->first_name . " " . $beanAsesorSolicita->last_name;
        }
        if (!empty($idAsesorAnterior)) {
            $beanAsesorAnterior = BeanFactory::retrieveBean('Users', $idAsesorAnterior, array('disable_row_level_security' => true));
            $nombreAsesorAnterior = $beanAsesorAnterior->first_name . " " . $beanAsesorAnterior->last_name;
        }
        //SE ACTUALIZA DATOS DE CONTROL ASIGNACION
        if (!empty($idCuenta)) {
            $beanResumen = BeanFactory::retrieveBean('tct02_Resumen', $idCuenta, array('disable_row_level_security' => true));
            $beanResumen->id_asesor_solicita_c = $idAsesorSolicita;
        }

        //Datos nombres de cuentas relacionadas para email
        $selectRelacionesHijas  = "SELECT name from accounts where id in ( 
            SELECT rrc.account_id1_c as idRelacionCuentaHija
            FROM rel_relaciones rr
            INNER JOIN rel_relaciones_accounts_1_c rra ON rra.rel_relaciones_accounts_1rel_relaciones_idb = rr.id
            INNER JOIN rel_relaciones_cstm rrc ON rrc.id_c = rr.id 
            WHERE rra.rel_relaciones_accounts_1accounts_ida = '{$idCuenta}' AND rr.deleted = 0)";
        $relacionesResult = $GLOBALS['db']->query($selectRelacionesHijas);
        $relacionesHijas = []; // Inicializar arreglo
        while ($rowRelacion = $GLOBALS['db']->fetchByAssoc($relacionesResult)) {
            $relacionesHijas[] = $rowRelacion['name']; // Almacenar cada nombre en el arreglo
        }

        $GLOBALS['log']->fatal("...¿Es_Diferente_Region?... " . $esDiferenteRegion);
        //VALIDA SI ES EL PROCESO DE DIFERENTE REGION
        if ($esDiferenteRegion === 1) {
            //VALIDA SI ES PARA EL EJECUTIVO DE ESTRATEGIA COMERCIAL - RICARDO GERARDO
            $GLOBALS['log']->fatal("...PROCESO DIFERENTE REGION...");
            if ($esEjecutivoEstrategiaComercial === 1) {
                $GLOBALS['log']->fatal("...CON EJECUTIVO ESTRATEGIA COMERCIAL... ". $esEjecutivoEstrategiaComercial);
                //LISTA DE CORREO EJECUTIVO DE ESTRATEGIA COMERCIAL
                global $app_list_strings;
                $listaIdEjEstrategia = $app_list_strings['ids_aprobador_reasignacion_director_list'];
                foreach ($listaIdEjEstrategia as $key => $idAprobadorEjecutivoEC) {
                    $GLOBALS['log']->fatal("...ListIdAprobadorEjecutivoEC... " . $idAprobadorEjecutivoEC);
                    if (!empty($idAprobadorEjecutivoEC)) {
                        $beanEjecutivoEstrategia = BeanFactory::retrieveBean('Users', $idAprobadorEjecutivoEC, array('disable_row_level_security' => true));
                        $nombreEjecutivoEstrategiaComercial = $beanEjecutivoEstrategia->first_name . " " . $beanEjecutivoEstrategia->last_name;
                        $emailEjecutivoEstrategiaComercial = $beanEjecutivoEstrategia->email1;
                    }
                    $GLOBALS['log']->fatal("...NombreEjecutivoEstrategiaComercial... " . $nombreEjecutivoEstrategiaComercial);
                    while ($rowRelacion = $GLOBALS['db']->fetchByAssoc($relacionesResult)) {
                        $relacionesHijas[] = $rowRelacion['name']; // Almacenar cada nombre en el arreglo
                    }
                    //PLANTILLA DE EMAIL PARA VOBO EJECUTIVO ESTRATEGIA COMERCIAL
                    $body_mail_vobo_eec = $this->buildBodyEmailVoBo($nombreEjecutivoEstrategiaComercial, $nombreAsesorSolicita, $nombreCuenta, $nombreAsesorAnterior, $linkAutorizaDiferenteRegion, $linkRechazoDiferenteRegion, $relacionesHijas);
                    $GLOBALS['log']->fatal("...EmailEjecutivoEstrategiaComercial... " . $emailEjecutivoEstrategiaComercial);
                    //EMAIL A EJECUTIVO ESTRATEGIA COMERCIAL
                    if (!empty($emailEjecutivoEstrategiaComercial)) {
                        $this->sendEmailAsesorCuentas(
                            'Aprobación Recarterización de clientes/prospectos ' . $nombreCuenta,
                            $body_mail_vobo_eec,
                            $emailEjecutivoEstrategiaComercial,
                            $nombreEjecutivoEstrategiaComercial
                        );
                        $response .= "<br>Se envió notificación al Ejecutivo de Estrategia Comercial: <b>" . $nombreEjecutivoEstrategiaComercial . "</b>, para VoBo de la Asignación de la cuenta <b>" . $nombreCuenta . "</b>";
                    }
                    //GUARDA EL ID DEL APROBADOR
                    $beanResumen->id_director_region_aprobar_c = $idAprobadorEjecutivoEC;
                }

            } else {
                //OBTIENE EL ID DEL DIRECTOR REGIONAL DEL USUARIO QUIEN SOLICITA
                $id_director_regional_dr = $this->getIdDirectorRegional($beanAsesorSolicita);
                $GLOBALS['log']->fatal("...ID_DIR_REGIONAL... " . $id_director_regional_dr);
                if (!empty($id_director_regional_dr)) {
                    //INFORMACION DEL DIRECTOR REGIONAL
                    $beanDirRegionalDR = BeanFactory::retrieveBean('Users', $id_director_regional_dr, array('disable_row_level_security' => true));
                    $nombreDirRegionalDR = $beanDirRegionalDR->first_name . " " . $beanDirRegionalDR->last_name;
                    $emailDirRegionalDR = $beanDirRegionalDR->email1;
                }
                $GLOBALS['log']->fatal("...NombreDirRegionalDR... " . $nombreDirRegionalDR);
                //PLANTILLA DE EMAIL PARA VOBO DIRECTOR REGIONAL
                $body_mail_vobo_dr = $this->buildBodyEmailVoBo($nombreDirRegionalDR, $nombreAsesorSolicita, $nombreCuenta, $nombreAsesorAnterior, $linkAutorizaDiferenteRegion, $linkRechazoDiferenteRegion, $relacionesHijas);
                $GLOBALS['log']->fatal("...EmailDirRegionalDR... " . $emailDirRegionalDR);
                //EMAIL A DIRECTOR REGIONAL
                if (!empty($emailDirRegionalDR)) {
                    $this->sendEmailAsesorCuentas(
                        'Aprobación Recarterización de clientes/prospectos ' . $nombreCuenta,
                        $body_mail_vobo_dr,
                        $emailDirRegionalDR,
                        $nombreDirRegionalDR
                    );
                    $response .= "<br>Se envió notificación al Director Regional: <b>" . $nombreDirRegionalDR . "</b>, para VoBo de la Asignación de la cuenta <b>" . $nombreCuenta . "</b>";
                }
                //GUARDA EL ID DEL APROBADOR
                $beanResumen->id_director_region_aprobar_c = $id_director_regional_dr;
            }

        } else {
            /**************************************************** PROCESO MISMA REGION **************************************************** */
            $GLOBALS['log']->fatal("...PROCESO MISMA REGION...");
            //OBTIENE EL ID DEL DIRECTOR REGIONAL DEL USUARIO QUIEN SOLICITA
            $id_director_regional = $this->getIdDirectorRegional($beanAsesorSolicita);
            $GLOBALS['log']->fatal("...Id_Director_Regional... " . $id_director_regional);
            if (!empty($id_director_regional)) {
                //INFORMACION DEL DIRECTOR REGIONAL
                $beanDirRegional = BeanFactory::retrieveBean('Users', $id_director_regional, array('disable_row_level_security' => true));
                $nombreDirRegional = $beanDirRegional->first_name . " " . $beanDirRegional->last_name;
                $emailDirRegional = $beanDirRegional->email1;
            }
            $GLOBALS['log']->fatal("...NombreDirRegional... " . $nombreDirRegional);
            //PLANTILLA DE EMAIL PARA VOBO DIRECTOR REGIONAL
            $body_mail_vobo = $this->buildBodyEmailVoBo($nombreDirRegional, $nombreAsesorSolicita, $nombreCuenta, $nombreAsesorAnterior, $linkAutorizaMismaRegion, $linkRechazoMismaRegion, $relacionesHijas);
            $GLOBALS['log']->fatal("...EmailDirRegional... " . $emailDirRegional);
            //EMAIL A DIRECTOR REGIONAL
            if (!empty($emailDirRegional)) {
                $this->sendEmailAsesorCuentas(
                    'Aprobación Recarterización de clientes/prospectos ' . $nombreCuenta,
                    $body_mail_vobo,
                    $emailDirRegional,
                    $nombreDirRegional
                );
                $response .= "<br>Se envió notificación al Director Regional: <b>" . $nombreDirRegional . "</b>, para VoBo de la Asignación de la cuenta <b>" . $nombreCuenta . "</b>";
            }
            //GUARDA EL ID DEL APROBADOR
            $beanResumen->id_director_region_aprobar_c = $id_director_regional;
        }

        //SE ACTUALIZA DATOS DE CONTROL ASIGNACION
        if (!empty($idCuenta)) {
            $beanResumen->asignacion_activa_c = 1;
            $beanResumen->save();
        }

        return $response;
    }

    public function getIdDirectorRegional($beanAsesor)
    {
        $equipo_principal_asesor = $beanAsesor->equipo_c;
        $id_regional = "";
        $qGetDirectorRegional = "SELECT id_c,posicion_operativa_c,uc.equipo_c FROM users u 
        INNER JOIN users_cstm uc 
        ON u.id = uc.id_c
        AND uc.posicion_operativa_c LIKE '%^2^%' AND uc.equipo_c = '{$equipo_principal_asesor}'
        WHERE u.status = 'Active' AND u.deleted=0";

        $resultadoRegional = $GLOBALS['db']->query($qGetDirectorRegional);

        if ($resultadoRegional->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($resultadoRegional)) {
                $id_regional = $row['id_c'];
            }
        }
        return $id_regional;
    }

    public function buildBodyEmailVoBo($nombre_aprobador, $nombre_asesor_solicta, $nombre_cuenta, $nombre_asesor_anterior, $linkAutoriza, $linkRechazo, $cuentasHijas)
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
                                                                        <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombre_aprobador . '</strong></p>
                                                                        <p style="margin: 0; margin-bottom: 16px;">Tu asesor, <strong>' . $nombre_asesor_solicta . ',</strong> solicita la reasignación del Cliente Prospecto: <strong>' . $nombre_cuenta . '.</strong>, actualmente asignado a <strong>' . $nombre_asesor_anterior . '.</strong></p> 
                                                                        <p>Los contactos relacionados son:</p>
                                                                        <ul>';
                                                                        for ($i = 0; $i < count($cuentasHijas); $i++) {
                                                                            $auxHTML = '<li> ' . $cuentasHijas[$i] . "</li>";
                                                                        }
                                                                        $mailHTML = $mailHTML . $auxHTML . '</ul>';
                                                                        $mailHTML = $mailHTML . '<p style="margin: 0; margin-bottom: 16px;">Indica tu decisión a continuación:</p>

                                                                        <table border="0" cellpadding="10" cellspacing="0" class="button_block block-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                            <tr>
                                                                                <td class="">
                                                                                    <div align="center" class="alignment"><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:43px;width:136px;v-text-anchor:middle;" arcsize="63%" stroke="false" fillcolor="#05aa6d"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px"><![endif]-->
                                                                                        <div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#05aa6d;border-radius:27px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;text-align:center;mso-border-alt:none;word-break:keep-all;"><span style="padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;">
                                                                                        <span style="word-break: break-word; line-height: 32px;">
                                                                                            <a id="linkAccount" href="' . $linkAutoriza . '"><strong>Aceptar<br></strong></a>
                                                                                        </span>
                                                                                        </span></div><!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                                    </div>
                                                                                    </td>
                                                                                    <td class="pad">
                                                                                    <div align="center" class="alignment"><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:43px;width:136px;v-text-anchor:middle;" arcsize="63%" stroke="false" fillcolor="#e46962"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px"><![endif]-->
                                                                                        <div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#e46962;border-radius:27px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:16px;text-align:center;mso-border-alt:none;word-break:keep-all;"><span style="padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;">
                                                                                        <span style="word-break: break-word; line-height: 32px;">
                                                                                            <a id="linkAccount" href="' . $linkRechazo . '"><strong>Rechazar<br></strong></a>
                                                                                        </span>
                                                                                        </span></div><!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <br/>
                                                                        <p style="margin: 0;">Si tienes alguna duda contactar a:</p>
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

    public function procesoAutorizaAsignacion($api, $args)
    {
        $GLOBALS['log']->fatal("...procesoAutorizaAsignacion...");
        $idCuenta = $args['id_cuenta'];
        $idAsesorSolicita = $args['id_asesor_solicita'];
        $comentarioDirectorRegional = $args['comentarios'];
        $response = [];
        $response['status'] = '';
        $response['description'] = '';

        if (!empty($idCuenta)) {
            $beanAccount = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
            $nombreCuenta = $beanAccount->name;
            $idAsesorAnterior = $beanAccount->user_id_c;
        }
        if (!empty($idAsesorSolicita)) {
            $beanAsesorSolicita = BeanFactory::retrieveBean('Users', $idAsesorSolicita, array('disable_row_level_security' => true));
            $nombreAsesorSolicita = $beanAsesorSolicita->first_name . " " . $beanAsesorSolicita->last_name;
            $emailAsesorSolicita = $beanAsesorSolicita->email1;
        }
        if (!empty($idAsesorAnterior)) {
            $beanAsesorAnterior = BeanFactory::retrieveBean('Users', $idAsesorAnterior, array('disable_row_level_security' => true));
            $nombreAsesorAnterior = $beanAsesorAnterior->first_name . " " . $beanAsesorAnterior->last_name;
            $emailAsesorAnterior = $beanAsesorAnterior->email1;
        }

        //NOTIFICA LA REASIGNACION DE LA CUENTA AL ASESOR ANTERIOR
        $body_mail_notifica_asesor_anterior = $this->buildBodyNotificaAsesorAnterior($nombreAsesorSolicita, $nombreCuenta);
        //EMAIL AL ASESOR ANTERIOR
        if (!empty($emailAsesorAnterior)) {
            $success1 = $this->sendEmailAsesorCuentas(
                'Reasignación de cliente/prospecto ' . $nombreCuenta,
                $body_mail_notifica_asesor_anterior,
                $emailAsesorAnterior,
                $nombreAsesorAnterior
            );

            if ($success1) {
                $response['status'] = '200';
                $response['description'] .= "<br>Se envió notificación de Reasignación al Asesor Anterior: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            } else {
                $response['status'] = '500';
                $response['description'] .= "<br>Error al enviar notificación de Reasignación al Asesor Anterior: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            }
        }

        //PROCESO DE REASIGNACION DE LA CUENTA
        if (!empty($idCuenta) && !empty($idAsesorSolicita)) {
            $this->procesoReasignacionCuenta($idCuenta, $idAsesorSolicita);
        }

        //NOTIFICA AL ASESOR SOLICITA QUE SE AUTORIZO LA ASIGNACION
        $body_mail_autoriza_asignacion = $this->buildBodyNotificaAutorizacionAsesorAsignacion($nombreAsesorSolicita, $nombreCuenta, $comentarioDirectorRegional);
        //EMAIL AL ASESOR SOLICITA
        if (!empty($emailAsesorSolicita)) {
            $success2 = $this->sendEmailAsesorCuentas(
                'Recarterización de clientes/prospectos ' . $nombreCuenta,
                $body_mail_autoriza_asignacion,
                $emailAsesorSolicita,
                $nombreAsesorSolicita
            );

            if ($success2) {
                $response['status'] = '200';
                $response['description'] .= "<br>Se envió notificación de Asignación a: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            } else {
                $response['status'] = '500';
                $response['description'] .= "<br>Error al enviar notificación de Asignación a: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            }
        }

        //SE ACTUALIZA DATOS DE CONTROL ASIGNACION
        if (!empty($idCuenta)) {
            $beanResumen = BeanFactory::retrieveBean('tct02_Resumen', $idCuenta, array('disable_row_level_security' => true));
            $beanResumen->asignacion_activa_c = 0;
            $beanResumen->save();
        }

        return $response;
    }

    public function buildBodyNotificaAutorizacionAsesorAsignacion($nombre_asesor_solicita, $nombre_cuenta, $comentario_del_director)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombre_asesor_solicita . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Tu solicitud de reasignación del Cliente/Prospecto <strong>' . $nombre_cuenta . '</strong> fue autorizada y ya se ha asignado a tu usuario, por favor confirma que así sea.</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Comentario del Director:</p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">' . $comentario_del_director . '</p>
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

    public function buildBodyNotificaAsesorAnterior($nombre_asesor_solicita, $nombre_cuenta)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombre_asesor_solicita . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">
                                                                                Me han solicitado la reasignación del prospecto <strong>' . $nombre_cuenta . '</strong> ya que no ha tenido actividad en el CRM en los últimos 30 días. 
                                                                                Por políticas de la empresa es válido que se haga la reasignación, este correo es para informarte del cambio y estés al tanto de la situación. 
                                                                                Además adjunto el párrafo correspondiente.
                                                                            </p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Re-Carterización de Leads y Prospectos</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">“El prospecto se queda a nombre del promotor que lo trajo”</p>
                                                                            <ul>
                                                                                <li>Que hayan pasado más de 30 días desde que se cargó el último papel en CRM y desde que se dio de alta al prospecto.</li>
                                                                                <li>Que no haya actividad del propietario actual en CRM en los últimos 30 días.</li>
                                                                            </ul>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Si otro promotor trae el expediente completo, podrá solicitar la reasignación de un prospecto ATENDIDO si cumple:</p>
                                                                            <ul>
                                                                                <li>Que suba el expediente completo en los 3 próximos días a la reasignación, en caso contrario, el prospecto será devuelto al asesor que lo estaba atendiendo</li>
                                                                                <li>Que la cuenta no esté referenciada</li>
                                                                            </ul>
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

    public function procesoRechazoAsignacion($api, $args)
    {
        $GLOBALS['log']->fatal("...procesoRechazoAsignacion...");
        $idCuenta = $args['id_cuenta'];
        $idAsesorSolicita = $args['id_asesor_solicita'];
        $comentarioDirectorRegional = $args['comentarios'];
        $response = [];
        $response['status'] = '';
        $response['description'] = '';

        if (!empty($idCuenta)) {
            $beanAccount = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
            $nombreCuenta = $beanAccount->name;
        }
        if (!empty($idAsesorSolicita)) {
            $beanAsesorSolicita = BeanFactory::retrieveBean('Users', $idAsesorSolicita, array('disable_row_level_security' => true));
            $nombreAsesorSolicita = $beanAsesorSolicita->first_name . " " . $beanAsesorSolicita->last_name;
            $emailAsesorSolicita = $beanAsesorSolicita->email1;
        }

        //NOTIFICA AL ASESOR SOLICITA QUE SE RECHAZO LA ASIGNACION
        $body_mail_rechazo_asignacion = $this->buildBodyNotificaRechazoAsesorAsignacion($nombreAsesorSolicita, $nombreCuenta, $comentarioDirectorRegional);
        //EMAIL AL ASESOR SOLICITA
        if (!empty($emailAsesorSolicita)) {
            $success = $this->sendEmailAsesorCuentas(
                'Recarterización de clientes/prospectos ' . $nombreCuenta,
                $body_mail_rechazo_asignacion,
                $emailAsesorSolicita,
                $nombreAsesorSolicita
            );

            if ($success) {
                $response['status'] = '200';
                $response['description'] .= "<br>Se envió notificación de Rechazo Asignación a: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            } else {
                $response['status'] = '500';
                $response['description'] .= "<br>Error al enviar notificación de Rechazo Asignación a: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
            }
        }

        //SE ACTUALIZA DATOS DE CONTROL ASIGNACION
        if (!empty($idCuenta)) {
            $beanResumen = BeanFactory::retrieveBean('tct02_Resumen', $idCuenta, array('disable_row_level_security' => true));
            $beanResumen->asignacion_activa_c = 0;
            $beanResumen->save();
        }

        return $response;
    }

    public function buildBodyNotificaRechazoAsesorAsignacion($nombre_asesor_solicita, $nombre_cuenta, $comentario_del_director)
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
                                                                            <p style="margin: 0; margin-bottom: 16px;">Estimado/a, <strong>' . $nombre_asesor_solicita . '</strong></p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">
                                                                                Me han solicitado la reasignación del prospecto <strong>' . $nombre_cuenta . '</strong> ya que no ha tenido actividad en el CRM en los últimos 30 días. 
                                                                                Por politicas de la empresa es válido que se haga la reasignación, este correo es para informarte del cambio y estés al tanto de la situación. 
                                                                                Además, adjunto el párrafo correspondiente.
                                                                            </p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Comentario del Director:</p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">' . $comentario_del_director . '</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Re-Carterización de Leads y Prospectos</p>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">“El prospecto se queda a nombre del promotor que lo trajo”</p>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Si otro promotor desea gestionar un prospecto, podrá solicitar la reasignación si se cumple lo siguiente:</p>
                                                                            <ul>
                                                                                <li>Que hayan pasado más de 30 días desde que se cargó el último papel en CRM y desde que se dio de alta al prospecto.</li>
                                                                                <li>Que no haya actividad del propietario actual en CRM en los últimos 30 días.</li>
                                                                            </ul>
                                                                            <br>
                                                                            <p style="margin: 0; margin-bottom: 16px;">Si otro promotor trae el expediente completo, podrá solicitar la reasignación de un prospecto ATENDIDO si cumple:</p>
                                                                            <ul>
                                                                                <li>Que suba el expediente completo en los 3 próximos días a la reasignación, en caso contrario, el prospecto será devuelto al asesor que lo estaba atendiendo</li>
                                                                                <li>Que la cuenta no esté referenciada</li>
                                                                            </ul>
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
}

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
        );
    }

    public function sendEmailAsignacionCuentas($api, $args)
    {
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
        //REASIGNACION DE CUENTA                        
        if (!empty($idCuenta)) {
            //BUSQUEDA DE ID DEL TIPO DE PRODUCTO LEASING
            $selectProductoLeasing = "SELECT up.id as idProductoLeasing
            FROM accounts a
            inner join accounts_uni_productos_1_c ap on a.id = ap.accounts_uni_productos_1accounts_ida
            inner join uni_productos up on up.id = ap.accounts_uni_productos_1uni_productos_idb
            inner join uni_productos_cstm upc on upc.id_c = up.id
            and a.id = '{$idCuenta}' and up.tipo_producto = '1' and up.deleted = '0'";            
            $result = $GLOBALS['db']->fetchOne($selectProductoLeasing);
        }

        if ($result && !empty($result['idProductoLeasing'])) {
            $idProductoLeasing = $result['idProductoLeasing'];
        }
        
        $GLOBALS['log']->fatal("idProductoLeasing: " . $idProductoLeasing);
        $GLOBALS['log']->fatal("idAsesorSolicita: " . $idAsesorSolicita);

        if (!empty($idProductoLeasing)) {
            //SE REASIGNA LA CUENTA/PRODUCTO AL ASESOR SOLICITA EN UNI_PRODUCTOS
            $updateProductoAsesor = "
                UPDATE uni_productos
                SET estatus_atencion = '1', assigned_user_id = '{$idAsesorSolicita}'
                WHERE id = '{$idProductoLeasing}'
            ";
            $GLOBALS['db']->query($updateProductoAsesor);
        }
        if (!empty($idCuenta)) {
            //SE REASIGNA LA CUENTA/PRODUCTO AL ASESOR SOLICITA EN CUENTAS
            $updateCuentaAsesor = "
                UPDATE accounts_cstm
                SET tct_status_atencion_ddw_c = 'Atendido', user_id_c = '{$idAsesorSolicita}'
                WHERE id_c = '{$idCuenta}'
            ";
            $GLOBALS['db']->query($updateCuentaAsesor);
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
            $response .= "<br>Se envió notificación a: " . $nombreAsesorSolicita . ", de la cuenta " . $nombreCuenta;
        }
        //DIRECTOR INFORMA A
        if (!empty($emailDirectorInformaA)) {
            $this->sendEmailAsesorCuentas(
                'Reasignación de cliente/prospecto ' . $nombreCuenta,
                $body_mail_director_informa_a,
                $emailDirectorInformaA,
                $nombreDirectorInformaA
            );
            $response .= "<br>Se envió notificación a: " . $nombreDirectorInformaA . ", de la cuenta " . $nombreCuenta;
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
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailer->setSubject($subject);
            $mailer->addAttachment(new \EmbeddedImage('Copia_de_Recurso-2unileasingazulLOW', 'custom/images_email/Copia_de_Recurso-2unileasingazulLOW.png', 'Copia_de_Recurso-2unileasingazulLOW'), "Copia_de_Recurso-2unileasingazulLOW");
            $mailer->setHtmlBody(trim($bodyMail));
            $mailer->addRecipientsTo(new EmailIdentity($emailDestinatario, $nombreDestinatario));

            $GLOBALS['log']->fatal("ENVIANDO CORREO A: " . $emailDestinatario);
            $mailer->send();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar el correo electrónico a " . $emailDestinatario);
            $GLOBALS['log']->fatal(print_r($e, true));
        }
    }
}

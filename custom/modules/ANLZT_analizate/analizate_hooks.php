<?php
/**
 * Created by PhpStorm.
 * User: Adrian Arauz
 * Date: 24/02/20
 * Time: 11:36 AM
 */
require_once 'include/SugarPHPMailer.php';
require_once 'include/utils/file_utils.php';

class analizate_hooks  {
    public function EnvioMail($bean = null, $event = null, $args = null) {
      /*
        //Notificaciones
        if($bean->tipo_registro_cuenta_c == '2'|| $bean->tipo_registro_cuenta_c == '3'|| $bean->tipo_registro_cuenta_c == '4'){
            //Notificación Cliente
            if (!$args['isUpdate'] && $bean->estado==1) {
                global $app_list_strings;
                //Valor de la lista en posicion 3 corresponde a Cliente
                $urlFinanciera = $app_list_strings['analizate_url_list'][3];
                $cuenta = BeanFactory::retrieveBean('Accounts', $bean->anlzt_analizate_accountsaccounts_ida);
                //$GLOBALS['log']->fatal($bean->anlzt_analizate_accountsaccounts_ida);
                if(!isset($cuenta->id)){
                  $cuenta = BeanFactory::retrieveBean('Leads', $bean->leads_anlzt_analizate_1leads_ida,array('disable_row_level_security' => true));
                  //$GLOBALS['log']->fatal('==Valida registro lead, valida reg analizate==');
                  //$GLOBALS['log']->fatal($bean->leads_anlzt_analizate_1leads_ida);
                }
                $correo = $cuenta->email1;
                $full_name = $cuenta->name;
                $rfc = $cuenta->rfc_c;
                $idCuenta = $cuenta->id;
                //$GLOBALS['log']->fatal($correo);
                //$GLOBALS['log']->fatal($full_name);
                //$GLOBALS['log']->fatal($rfc);
                 //Conversion de tipo de persona (regimen fiscal)
                $regimen=isset($cuenta->tipodepersona_c) ? $cuenta->tipodepersona_c : $cuenta->regimen_fiscal_c ;
                $tipopersona='';
                //$GLOBALS['log']->fatal('==Valida regimen Fiscal analizate_hooks==');
                switch($regimen){
                  case 'Persona Fisica':
                    $tipopersona='PF';
                    break;
                  case 'Persona Fisica con Actividad Empresarial':
                    $tipopersona='PFAE';
                    break;
                  case 'Persona Moral':
                    $tipopersona='PM';
                    break;
                  case '1':
                    $tipopersona='PF';
                    break;
                  case '2':
                    $tipopersona='PFAE';
                    break;
                  case '3':
                    $tipopersona='PM';
                    break;
                }

            //$GLOBALS['log']->fatal('Envio de correo a' . $full_name . '');
            //$GLOBALS['log']->fatal('>>>>' . $url . '<<<<');

            $mailHTML = '
              <font face="verdana" color="#635f5f">
                Estimado cliente <b>' . $full_name . ' :</b>
                <p style="text-align:justify;">
                  <br>Como parte del servicio continuo a nuestros clientes, nos acercamos a ti con el propósito de <b>cumplir con los requerimientos establecidos por el Servicio de Administración Tributaria (SAT),</b> derivado de las medidas que buscan fortalecer las herramientas tecnológicas para simplificar el cumplimiento de las normas tributarias.
                  <br>

                  <br>El SAT informó que a partir del 1º de enero del presente año, entró en vigor la <b>nueva versión del CFDI 4.0,</b> estableciendo como <b>fecha límite de convivencia con la versión anterior el 30 de junio de 2022.</b>
                  <br>

                  <br>Derivado de lo anterior, <b>necesitamos actualizar los datos del CFDI de nuestros clientes,</b> de tal forma que UNIFIN pueda <b>continuar emitiendo facturas con validez fiscal.</b>
                  <br>

                  <br>UNIFIN pone a tu disposición <b>el siguiente enlace,</b> mediante el cual, a través de tu <b>clave CIEC</b> podrás <b>actualizar de forma precisa y oportuna los datos</b> solicitados en la legislación tributaria.
                  <br>

                  <center>
                    <a id="downloadErrors" href="'. $urlFinanciera.'&UUID='. base64_encode($idCuenta). '&RFC_CIEC=' .base64_encode($rfc). '&MAIL=' .base64_encode($correo).'&TP='.base64_encode($tipopersona).'">DAR CLICK AQUÍ</a>
                  </center>

                  <br><br>Para UNIFIN es importante llevar a cabo este proceso con total <b>seguridad y transparencia;</b> si tuvieras cualquier duda relacionada con el mismo, no dudes en contactarnos al <b>Centro de Atención al Cliente al 800 211 9000</b> donde con gusto te atenderemos.
                </p>
                <br>Atentamente
              </font>
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

                //$GLOBALS['log']->fatal($mailHTML);
                //$GLOBALS['log']->fatal($correo);
                try{
                    $mailer = MailerFactory::getSystemDefaultMailer();
                    $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
                    $mailer->setSubject("Actualiza tu información de facturación (CFDI 4.0)");
                    $body = trim($mailHTML);
                    $mailer->setHtmlBody($body);
                    $mailer->clearRecipients();
                    $mailer->addRecipientsTo(new EmailIdentity($correo, $full_name));
                    //Agrega copia
                    $urlFinanciera = isset($app_list_strings['analizate_notifica_bcc_list']) ? $app_list_strings['analizate_notifica_bcc_list'] : [];
                    //$GLOBALS['log']->fatal($urlFinanciera);
                    foreach ($urlFinanciera as $nombre => $correoBcc) {
                        if(!empty($correoBcc)){
                            //Agrega
                            //$GLOBALS['log']->fatal($nombre. "=>". $correoBcc);
                            $mailer->addRecipientsBcc(new EmailIdentity($correoBcc, $nombre));
                        }
                    }
                    $result = $mailer->send();
                }catch (Exception $e){
                    $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ".$correo);
                    $GLOBALS['log']->fatal("Exception ".$e);
                }
            }
        }else{
            //Notificaciones Proveedor
            if (!$args['isUpdate'] && $bean->estado==1) {
                global $app_list_strings;
                //Valor de la lista en posicion 1 corresponde a Financiera, 2 a Credit
                $urlFinanciera = $app_list_strings['analizate_url_list'][$bean->empresa];
                //$GLOBALS['log']->fatal('Entra LH de Analizate');
                //file_put_contents($archivo, $texto_archivo, FILE_APPEND | LOCK_EX);
                $cuenta = BeanFactory::retrieveBean('Accounts', $bean->anlzt_analizate_accountsaccounts_ida);
                $correo = $cuenta->email1;
                $full_name = $cuenta->name;
                $rfc = $cuenta->rfc_c;
                $idCuenta = $cuenta->id;

                //$GLOBALS['log']->fatal('Envio de correo a' . $full_name . '');
                //$GLOBALS['log']->fatal('>>>>' . $url . '<<<<');

                $mailHTML = '
                  <font face="verdana" color="#635f5f">
                    Estimado proveedor <b>' . $full_name . ' :</b>
                    <p>
                      <br>Para UNIFIN FINANCIERA SAB DE CV es importante llevar a cabo el proceso de alta como proveedor con total seguridad y transparencia, por lo cual solicitamos proporcionar tus datos en el siguiente link:
                      <br><a id="downloadErrors" href="'. $urlFinanciera.'&UUID='. base64_encode($idCuenta). '&RFC_CIEC=' .base64_encode($rfc). '&MAIL=' .base64_encode($correo).'">Da Click Aquí</a>
                      <br><br>Por favor para cualquier comentario dirígete al comprador que te contacto o bien al correo: compras1@unifin.com.mx
                    </p>
                    <br>Atentamente
                  </font>
                  <font face="verdana" color="#133A6E">
                    <br><b>Dirección de Compras</b>
                    <br><b>UNIFIN FINANCIERA, SA.B. DE C.V.</b>
                  </font>
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


                //$GLOBALS['log']->fatal($mailHTML);
                //$GLOBALS['log']->fatal($correo);
                try{
                    $mailer = MailerFactory::getSystemDefaultMailer();
                    $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
                    $mailer->setSubject("Información sobre el registro en el Portal de Analízate");
                    $body = trim($mailHTML);
                    $mailer->setHtmlBody($body);
                    $mailer->clearRecipients();
                    $mailer->addRecipientsTo(new EmailIdentity($correo, $full_name));
                    //Agrega copia
                    $urlFinanciera = isset($app_list_strings['analizate_notifica_bcc_list']) ? $app_list_strings['analizate_notifica_bcc_list'] : [];
                    //$GLOBALS['log']->fatal($urlFinanciera);
                    foreach ($urlFinanciera as $nombre => $correoBcc) {
                        if(!empty($correoBcc)){
                            //Agrega
                            //$GLOBALS['log']->fatal($nombre. "=>". $correoBcc);
                            $mailer->addRecipientsBcc(new EmailIdentity($correoBcc, $nombre));
                        }
                    }
                    $result = $mailer->send();
                }catch (Exception $e){
                    $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ".$correo);
                    $GLOBALS['log']->fatal("Exception ".$e);
                }
            }
            //Correo para estado 6 INCORRECTA
            if ($bean->estado==6) {
                global $app_list_strings;
                //Valor de la lista en posicion 1 corresponde a Financiera, 2 a Credit
                $urlFinanciera = $app_list_strings['analizate_url_list'][$bean->empresa];
                //$GLOBALS['log']->fatal('Entra LH de Analizate');
                //file_put_contents($archivo, $texto_archivo, FILE_APPEND | LOCK_EX);
                $cuenta = BeanFactory::retrieveBean('Accounts', $bean->anlzt_analizate_accountsaccounts_ida);
                $correo = $cuenta->email1;
                $full_name = $cuenta->name;
                $rfc = $cuenta->rfc_c;
                $idCuenta = $cuenta->id;

                //$GLOBALS['log']->fatal('Envio de correo a' . $full_name . '');
                //$GLOBALS['log']->fatal('>>>>' . $url . '<<<<');

                $mailHTML = '
                  <font face="verdana" color="#635f5f">
                    ' . $full_name . ' :</b>
                    <p>
                      <br>Se ha detectado un problema al realizar el resgistro con el portal Analízate. Le solicitamos ingrese de nuevo a través del siguiente enlace para llevar a cabo el registro nuevamente.
                      <br><a id="downloadErrors" href="'. $urlFinanciera.'&UUID='. base64_encode($idCuenta). '&RFC_CIEC=' .base64_encode($rfc). '&MAIL=' .base64_encode($correo).'">Da Click Aquí</a>
                      <br><br>Por favor para cualquier comentario dirígete al comprador que te contacto o bien al correo: compras1@unifin.com.mx
                    </p>
                    <br>Atentamente
                  </font>
                  <font face="verdana" color="#133A6E">
                    <br><b>Dirección de Compras</b>
                    <br><b>UNIFIN FINANCIERA, SA.B. DE C.V.</b>
                  </font>
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


                //$GLOBALS['log']->fatal($mailHTML);
                //$GLOBALS['log']->fatal($correo);

                try{
                    $mailer = MailerFactory::getSystemDefaultMailer();
                    $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
                    $mailer->setSubject("Información sobre el registro en el Portal de Analízate");
                    $body = trim($mailHTML);
                    $mailer->setHtmlBody($body);
                    $mailer->clearRecipients();
                    $mailer->addRecipientsTo(new EmailIdentity($correo, $full_name));
                    //Agrega copia
                    $urlFinanciera = isset($app_list_strings['analizate_notifica_bcc_list']) ? $app_list_strings['analizate_notifica_bcc_list'] : [];
                    //$GLOBALS['log']->fatal($urlFinanciera);
                    foreach ($urlFinanciera as $nombre => $correoBcc) {
                        if(!empty($correoBcc)){
                            //Agrega
                            //$GLOBALS['log']->fatal($nombre. "=>". $correoBcc);
                            $mailer->addRecipientsBcc(new EmailIdentity($correoBcc, $nombre));
                        }
                    }
                    $result = $mailer->send();
                }catch (Exception $e){
                    $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ".$correo);
                    $GLOBALS['log']->fatal("Exception ".$e);
                }
            }
        }
      */
    }

    public function ActualizaRobina($bean = null, $event = null, $args = null) {
        //Valida si existe información de Robina
        if(!empty($bean->json_robina_c)){
            //Recupera cuenta
            //$GLOBALS['log']->fatal($bean->json_robina_c);
            $jsonObject = [];
            $cambios = 0;
            $guardarCambios = 0;
            try{
                $jsonObject = json_decode($bean->json_robina_c);
                //Fecha: Inicio Operaciones
                if(isset($jsonObject->startedOperationsAt) && !empty($jsonObject->startedOperationsAt)){
                    $inicioOperacionesString = strtotime($jsonObject->startedOperationsAt);
                    $inicioOperacionesDate = date('Y-m-d',$inicioOperacionesString);
                    $cambios++;
                    //$GLOBALS['log']->fatal($inicioOperacionesDate);
                }
                //Fecha: Inicio Régimen
                if(isset($jsonObject->taxRegimes[0]->startDate) && !empty($jsonObject->taxRegimes[0]->startDate)){
                    $inicioRegimenString = strtotime($jsonObject->taxRegimes[0]->startDate);
                    $inicioRegimenDate = date('Y-m-d',$inicioRegimenString);
                    $cambios++;
                    //$GLOBALS['log']->fatal($inicioRegimenDate);
                }
                //Fecha: Fin Régimen
                if(isset($jsonObject->taxRegimes[0]->startDate) && !empty($jsonObject->taxRegimes[0]->startDate)){
                    $finRegimenString = strtotime($jsonObject->taxRegimes[0]->startDate);
                    $finRegimenDate = date('Y-m-d',$finRegimenString);
                    $cambios++;
                    //$GLOBALS['log']->fatal($finRegimenDate);
                }
                //Fecha: Último cambio Edo
                if(isset($jsonObject->statusUpdatedAt) && !empty($jsonObject->statusUpdatedAt)){
                    $ultimoCambioEdoString = strtotime($jsonObject->statusUpdatedAt);
                    $ultimoCambioEdoDate = date('Y-m-d',$ultimoCambioEdoString);
                    $cambios++;
                    //$GLOBALS['log']->fatal($ultimoCambioEdoDate);
                }
                //Status
                if(isset($jsonObject->status) && !empty($jsonObject->status)){
                    $status = $jsonObject->status;
                    $cambios++;
                    //$GLOBALS['log']->fatal($status);
                }
            } catch (Exception $e) {
                $GLOBALS['log']->fatal($e->getMessage());
            }
            if($cambios>0){
                $beanCuenta = BeanFactory::retrieveBean('Accounts', $bean->anlzt_analizate_accountsaccounts_ida, array('disable_row_level_security' => true));
                //Valida cambios
                if(isset($inicioOperacionesDate) && !empty($inicioOperacionesDate) && $beanCuenta->inicio_operaciones_c!= $inicioOperacionesDate ){
                    $beanCuenta->inicio_operaciones_c = $inicioOperacionesDate;
                    $guardarCambios ++;
                    // $GLOBALS['log']->fatal('GC 1');
                }
                if(isset($inicioRegimenDate) && !empty($inicioRegimenDate) && $beanCuenta->inicio_regimen_c!= $inicioRegimenDate ){
                    $beanCuenta->inicio_regimen_c = $inicioRegimenDate;
                    $guardarCambios ++;
                    // $GLOBALS['log']->fatal('GC 2');
                }
                if(isset($finRegimenDate) && !empty($finRegimenDate) && $beanCuenta->fin_regimen_c!= $finRegimenDate ){
                    $beanCuenta->fin_regimen_c = $finRegimenDate;
                    $guardarCambios ++;
                    // $GLOBALS['log']->fatal('GC 3');
                }
                if(isset($ultimoCambioEdoDate) && !empty($ultimoCambioEdoDate) && $beanCuenta->ultimo_cambio_edo_c!= $ultimoCambioEdoDate ){
                    $beanCuenta->ultimo_cambio_edo_c = $ultimoCambioEdoDate;
                    $guardarCambios ++;
                    // $GLOBALS['log']->fatal('GC 4');
                }
                if(isset($status) && !empty($status) && strtoupper($beanCuenta->estatus_padron_c)!= strtoupper($status) ){
                    $beanCuenta->estatus_padron_c = $status;
                    $guardarCambios ++;
                    // $GLOBALS['log']->fatal('GC 5');
                }

                if($guardarCambios>0){
                    $GLOBALS['log']->fatal('Guarda cambios Robina');
                    $beanCuenta->save();

                }
            }

        }

    }
}

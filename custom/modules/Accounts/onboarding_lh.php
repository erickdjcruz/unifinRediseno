<?php
// ECB 30/09/2025 Onboarding
class onboarding_c
{
    function onboarding_f($bean, $event, $arguments)
    {
        global $db , $sugar_config, $app_list_strings;
        
        // Campos a monitorear para Persona Moral
        $camposRazonSocial = array(
            'razonsocial_c',
            'denominacion_c', 
            'regimen_capital_c'
        );
        
        // Verificar si hay cambios en los campos de razón social
        $cambiosDetectados = false;
        $datosAuditoria = array();
        
        if ($bean->tipodepersona_c == 'Persona Moral') {
            foreach ($camposRazonSocial as $campo) {
                if ($bean->fetched_row[$campo] != $bean->$campo) {
                    $cambiosDetectados = true;
                    break;
                }
            }
        }
        
        // Si se detectaron cambios, generar JSON de auditoría
        if ($cambiosDetectados && $args['isUpdate']) {
            $datosAuditoria = $this->generarJsonAuditoria($bean);
			$jsons = $datosAuditoria['json'];
            //$bean->json_audit_c = $datosAuditoria['json'];
            //$bean->save();
			$query = "UPDATE accounts_cstm SET `json_audit_c` = '{$jsons}' WHERE id_c = '{$bean->id}'";
        	$result = $db->query($query);			
        }
        
        // Si hay cambios y la plataforma es vitaOnboarding, enviar correo
        if ($cambiosDetectados && $_SESSION['platform'] == 'vitaOnboaarding') {
            $this->enviarCorreoNotificacion($bean, $datosAuditoria['nombre_cuenta']);
        }else if($cambiosDetectados){
            // Validar RFC via API
            $this->validarRFC($bean);
			$this->enviarCorreoNotificacion($bean, $datosAuditoria['nombre_cuenta']);
		}
    }
    
    /**
     * Genera el JSON de auditoría con los cambios detectados
     */
    private function generarJsonAuditoria($bean)
    {
        $fechaActual = date('Y-m-d H:i:s');
        $nombreCuenta = $bean->name;
        
        if ($bean->tipodepersona_c == 'Persona Moral') {
            $jsonAuditoria = array(
                "tipo" => "Persona Moral",
                "razon_social_actual" => $bean->fetched_row['razonsocial_c'] ?? '',
                "razon_social_por_actualizar" => $bean->razonsocial_c,
                "denominacion_actual" => $bean->fetched_row['denominacion_c'] ?? '',
                "denominacion_por_actualizar" => $bean->denominacion_c,
                "regimen_capital_actual" => $bean->fetched_row['regimen_capital_c'] ?? '',
                "regimen_capital_por_actualizar" => $bean->regimen_capital_c,
                "primer_nombre_actual" => " ",
                "primer_nombre_por_actualizar" => " ",
                "paterno_actual" => " ",
                "paterno_por_actualizar" => " ",
                "materno_actual" => " ",
                "materno_por_actualizar" => " ",
                "nombre_actual" => $bean->fetched_row['name'] ?? '',
                "nombre_por_actualizar" => $bean->name,
                "fecha_cambio" => $fechaActual,
                "plataforma" => $_SESSION['platform'] ?? 'base'
            );
        } else {
            // Para Persona Física
            $jsonAuditoria = array(
                "tipo" => "Persona Física",
                "razon_social_actual" => " ",
                "razon_social_por_actualizar" => " ",
                "denominacion_actual" => " ",
                "denominacion_por_actualizar" => " ",
                "regimen_capital_actual" => " ",
                "regimen_capital_por_actualizar" => " ",
                "primer_nombre_actual" => $bean->fetched_row['primernombre_c'] ?? '',
                "primer_nombre_por_actualizar" => $bean->primernombre_c,
                "paterno_actual" => $bean->fetched_row['apellidopaterno_c'] ?? '',
                "paterno_por_actualizar" => $bean->apellidopaterno_c,
                "materno_actual" => $bean->fetched_row['apellidomaterno_c'] ?? '',
                "materno_por_actualizar" => $bean->apellidomaterno_c,
                "nombre_actual" => $bean->fetched_row['name'] ?? '',
                "nombre_por_actualizar" => $bean->name,
                "fecha_cambio" => $fechaActual,
                "plataforma" => $_SESSION['platform'] ?? 'base'
            );
        }
        
        return array(
            'json' => json_encode($jsonAuditoria, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'nombre_cuenta' => $nombreCuenta
        );
    }
    
    /**
     * Envía correo de notificación
     */
    private function enviarCorreoNotificacion($bean, $nombreCuenta)
    {
        global $app_list_strings;
        
        $body_correo = '<p align="justify"><font face="verdana" color="#635f5f">
            Se ha detectado un cambio de nombre de la cuenta <b>' . $nombreCuenta . '</b>.<br>
            <br>Atentamente Unifin</font></p>
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
        
        $mailer = MailerFactory::getSystemDefaultMailer();
        $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
        $mailer->setSubject('Actualización de datos');
        $body = trim($body_correo);
        $mailer->setHtmlBody($body);
        $mailer->clearRecipients();        
        $lista = $app_list_strings['emais_juridico_aprobacion_rs_list'];
        if (!empty($lista)) {
            foreach ($lista as $keyNombre => $email) {
                $mailer->addRecipientsTo(new EmailIdentity($email));
            }
        }
        $lista_oculta = $app_list_strings['emais_ocultos_aprobacion_rs_list'];
        if (!empty($lista_oculta)) {
            foreach ($lista_oculta as $keyNombre => $email) {
                $mailer->addRecipientsBcc(new EmailIdentity($email));
            }
        }        
        return $mailer->send();
    }
    
    /**
     * Valida el RFC via API
     */
    private function validarRFC($bean)
    {
        global $sugar_config;
        
        $curl = curl_init();
        $rfc = $bean->rfc_c;
        $url = $sugar_config['onboarding_url'].'validate-rfc/'.$rfc.'/';
        $token = $sugar_config['onboarding_token'];
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token '.$token
            ),
        ));
        
        $result = curl_exec($curl);
        $response = json_decode($result, true);
        curl_close($curl);
        
        if($response['exists']) {
            // Comentado: Actualización de datos via API
            /*
            $curl = curl_init();
            $url = $sugar_config['onboarding_url'].'update-client/'.$rfc.'/';
            
            if($bean->tipodepersona_c == 'Persona Moral') {
                $business_name = $bean->denominacion_c;
                $business_entity = $bean->regimen_capital_c;
                $body = json_encode([
                    "business_name" => $business_name,
                    "business_entity" => $business_entity
                ]);
            } else {
                $name = $bean->primernombre_c;
                $last_name = $bean->apellidopaterno_c;
                $mother_last_name = $bean->apellidomaterno_c;
                $body = json_encode([
                    "name" => $name,
                    "last_name" => $last_name,
                    "mother_last_name" => $mother_last_name
                ]);
            }
            
            $token = $sugar_config['onboarding_token'];
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token '.$token,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            */
            
            // Re-enviar correo después de la validación (si es necesario)
            $this->enviarCorreoNotificacion($bean, $bean->name);
        }
    }
}
<?php
// ECB 30/09/2025 Onboarding
class onboarding_c
{
    function onboarding_f($bean, $event, $arguments)
    {
		if($_SESSION['platform'] == 'api') {
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
				$curl = curl_init();
				$url = $sugar_config['onboarding_url'].'update-client/'.$rfc.'/';
				if($bean->tipodepersona_c == 'Persona Moral') {
					$business_name = $bean->denominacion_c;
					$business_entity = $bean->regimen_capital_c;
					$body = json_encode([
						"business_name" => $business_name,
						"business_entity" => $business_entity
					]);
				}
				else {
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
				global $app_list_strings;
				$nombreCuenta = $bean->name;
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
				$mailer->addRecipientsBcc(new EmailIdentity('ccarral@unifin.com.mx', 'Cristian Carral'));
				$lista = $app_list_strings['emais_juridico_aprobacion_rs_list'];
				if (!empty($lista)) {
					foreach ($lista as $keyNombre => $email) {
						$mailer->addRecipientsTo(new EmailIdentity($email));
					}
				}
				$result = $mailer->send();
			}
		}
    }
}
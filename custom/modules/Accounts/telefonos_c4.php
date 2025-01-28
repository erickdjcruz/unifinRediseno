<?php
// ECB 07/01/2022 Consulta teléfonos en c4
class telefonos_c4
{
    function telefonos_c4($bean, $event, $arguments)
    {
		$procede = 0;
		$telefonos = array();
        foreach ($bean->account_telefonos as $a_telefono) {
			if(!empty($a_telefono['telefono'])) {
				array_push($telefonos, $a_telefono['telefono']);
				$procede = 1;
			}
		}
		if($procede) {
			global $sugar_config;
			$url = $sugar_config['c4'].'/C4/list/';
			$content = json_encode(array("telefonos" => $telefonos));
			try{

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_ENCODING, '');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
				$result = curl_exec($ch);
				$response = json_decode($result, true);
				$bean->load_relationship('accounts_tel_telefonos_1');
				foreach($response['data'] as $telefono) {
					if(!empty($telefono['Telefono'])) {
						foreach ($bean->accounts_tel_telefonos_1->getBeans() as $a_telefono) {
							if($a_telefono->telefono == $telefono['Telefono']) {
								$tel_telefono = BeanFactory::getBean('Tel_Telefonos', $a_telefono->id);
								$tel_telefono->estatus_telefono_c = '['.json_encode($telefono).']';
								$tel_telefono->save();
							}
						}
					}
				}

			}catch (Exception $exception) {
				$GLOBALS['log']->fatal($exception->getMessage());
				$this->setErrorLogFailRequest( "C4/list", $bean, $url, $content, $exception->getMessage() );
			}
			
		}
    }

	public function setErrorLogFailRequest( $endpoint , $bean, $url, $request, $response ){

        $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Unics");
        require_once("custom/clients/base/api/ErrorLogApi.php");
        if( $bean == '' ){
            $id_bean = '';
        }else{
            $id_bean = $bean->id;
        }
        $apiErrorLog = new ErrorLogApi();
        $args = array(
          "integration"=> "Teléfonos: ".$endpoint,
          "system"=> "C4",
          "parent_type"=> "Accounts",
          "parent_id"=> $id_bean,
          "endpoint"=> $url,
          "request"=> $request,
          "response"=> $response
        );
        $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
  
    }
}
<?php
// ECB 29/03/2022 Envía a Quantico Proveedores
class proveedor_quantico
{
    function proveedor_quantico($bean, $event, $arguments)
    {
		if(($bean->esproveedor_c || $bean->tipo_registro_cuenta_c == 5) && !$bean->proveedor_quantico_c) {
			if($bean->tipodepersona_c == "Persona Fisica") $regimen = 1;
			if($bean->tipodepersona_c == "Persona Fisica con Actividad Empresarial") $regimen = 2;
			if($bean->tipodepersona_c == "Persona Moral") $regimen = 3;
			if($regimen == 3) {
				$arreglo = array(
					"CRMId" => $bean->id,
					"Name" => $bean->name,
					"RFC" => $bean->rfc_c,
					"Email" => $bean->email1,
					"PersonTypeExternalId" => $regimen
				);
			}
			else {
				$arreglo = array(
					"CRMId" => $bean->id,
					"Name" => $bean->primernombre_c,
					"MiddleName" => $bean->apellidopaterno_c,
					"LastName" => $bean->apellidomaterno_c,
					"RFC" => $bean->rfc_c,
					"Email" => $bean->email1,
					"PersonTypeExternalId" => $regimen
				);
			}
			global $sugar_config;
			$url = $sugar_config['quantico_url_base'].'/Suppliers_API/rest/SupplierAPI/CreateSupplierInQuantico/';
			$content = json_encode($arreglo);
			$GLOBALS['log']->fatal('SOLICITUD PROVEEDOR QUANTICO');
			$GLOBALS['log']->fatal($content);
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
			$GLOBALS['log']->fatal('RESPUESTA PROVEEDOR QUANTICO');
			$GLOBALS['log']->fatal($response);
			if($response['IsValid']){
				$bean->proveedor_quantico_c = 1;
			}else{
				//Envía error de petición a bitáciora de errores
				$GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Quantico");
				require_once("custom/clients/base/api/ErrorLogApi.php");
				$apiErrorLog = new ErrorLogApi();
				$args = array(
					"integration"=> "Quantico: SupplierAPI/CreateSupplierInQuantico",
					"system"=> "Quantico",
					"parent_type"=> "Accounts",
					"parent_id"=> $bean->id,
					"endpoint"=> $url,
					"request"=> $content,
					"response"=> json_encode($response)
				);
				$responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
			}
		}
    }
}
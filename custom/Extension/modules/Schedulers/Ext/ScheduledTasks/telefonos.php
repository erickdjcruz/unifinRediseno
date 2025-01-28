<?php
    array_push($job_strings, 'telefonos');

    function telefonos()
    {
        //ECB 19/01/2022 Actualiza el estatus de teléfonos de trabajo con el servicio de C4
		$telefonos = array();
        $query="select distinct a.phone_work from leads a, leads_cstm b where a.id = b.id_c and a.phone_work is not null and a.phone_work <> '' and b.o_estatus_telefono_c is null and a.deleted = 0 limit 500";
        $results = $GLOBALS['db']->query($query);
        while($row = $GLOBALS['db']->fetchByAssoc($results)) {
			array_push($telefonos, trim($row['phone_work'], '"'));
		}
		if($telefonos) {
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
				$query="select a.id, a.phone_work from leads a, leads_cstm b where a.id = b.id_c and a.phone_work is not null and a.phone_work <> '' and b.o_estatus_telefono_c is null and a.deleted = 0";
				$results = $GLOBALS['db']->query($query);
				while($row = $GLOBALS['db']->fetchByAssoc($results)) {
					foreach($response['data'] as $telefono) {
						if(!empty($telefono['Telefono'])) {
							if($row['phone_work'] == $telefono['Telefono']) {
								$lead = $row['id'];
								$respuesta = '['.json_encode($telefono).']';
								$queryUpdate="update leads_cstm set o_estatus_telefono_c = '$respuesta' where id_c = '$lead'";
								$resultUpdate = $GLOBALS['db']->query($queryUpdate);
							}
						}
					}
				}

			}catch (Exception $exception) {
				$GLOBALS['log']->fatal($exception->getMessage());
				setErrorLogFailRequest( "Leads","C4/list", '', $url, $content, $exception->getMessage() );
			}
			
		}
        //ECB 20/01/2022 Actualiza el estatus de teléfonos de casa con el servicio de C4
		$telefonos = array();
        $query="select distinct a.phone_home from leads a, leads_cstm b where a.id = b.id_c and a.phone_home is not null and a.phone_home <> '' and b.c_estatus_telefono_c is null and a.deleted = 0 limit 500";
        $results = $GLOBALS['db']->query($query);
        while($row = $GLOBALS['db']->fetchByAssoc($results)) {
			array_push($telefonos, trim($row['phone_home'], '"'));
		}
		if($telefonos) {
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
				$query="select a.id, a.phone_home from leads a, leads_cstm b where a.id = b.id_c and a.phone_home is not null and a.phone_home <> '' and b.c_estatus_telefono_c is null and a.deleted = 0";
				$results = $GLOBALS['db']->query($query);
				while($row = $GLOBALS['db']->fetchByAssoc($results)) {
					foreach($response['data'] as $telefono) {
						if(!empty($telefono['Telefono'])) {
							if($row['phone_home'] == $telefono['Telefono']) {
								$lead = $row['id'];
								$respuesta = '['.json_encode($telefono).']';
								$queryUpdate="update leads_cstm set c_estatus_telefono_c = '$respuesta' where id_c = '$lead'";
								$resultUpdate = $GLOBALS['db']->query($queryUpdate);
							}
						}
					}
				}

			}catch (Exception $exception) {
				$GLOBALS['log']->fatal($exception->getMessage());
				setErrorLogFailRequest( "Leads","C4/list", '', $url, $content, $exception->getMessage() );
			}
			
		}
        //ECB 20/01/2022 Actualiza el estatus de teléfonos de celular con el servicio de C4
		$telefonos = array();
        $query="select distinct a.phone_mobile from leads a, leads_cstm b where a.id = b.id_c and a.phone_mobile is not null and a.phone_mobile <> '' and b.m_estatus_telefono_c is null and a.deleted = 0 limit 500";
        $results = $GLOBALS['db']->query($query);
        while($row = $GLOBALS['db']->fetchByAssoc($results)) {
			array_push($telefonos, trim($row['phone_mobile'], '"'));
		}
		if($telefonos) {
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
				$query="select a.id, a.phone_mobile from leads a, leads_cstm b where a.id = b.id_c and a.phone_mobile is not null and a.phone_mobile <> '' and b.m_estatus_telefono_c is null and a.deleted = 0";
				$results = $GLOBALS['db']->query($query);
				while($row = $GLOBALS['db']->fetchByAssoc($results)) {
					foreach($response['data'] as $telefono) {
						if(!empty($telefono['Telefono'])) {
							if($row['phone_mobile'] == $telefono['Telefono']) {
								$lead = $row['id'];
								$respuesta = '['.json_encode($telefono).']';
								$queryUpdate="update leads_cstm set m_estatus_telefono_c = '$respuesta' where id_c = '$lead'";
								$resultUpdate = $GLOBALS['db']->query($queryUpdate);
							}
						}
					}
				}

			}catch (Exception $exception) {
				$GLOBALS['log']->fatal($exception->getMessage());
				setErrorLogFailRequest( "Leads","C4/list", '', $url, $content, $exception->getMessage() );
			}
			
		}
        //ECB 21/01/2022 Actualiza el estatus de teléfonos de cuentas con el servicio de C4
		$telefonos = array();
        $query="select distinct a.telefono from tel_telefonos a, tel_telefonos_cstm b where a.id = b.id_c and a.telefono is not null and a.telefono <> '' and b.estatus_telefono_c is null and a.deleted = 0 limit 500";
        $results = $GLOBALS['db']->query($query);
        while($row = $GLOBALS['db']->fetchByAssoc($results)) {
			array_push($telefonos, trim($row['telefono'], '"'));
		}
		if($telefonos) {
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
				$query="select a.id, a.telefono from tel_telefonos a, tel_telefonos_cstm b where a.id = b.id_c and a.telefono is not null and a.telefono <> '' and b.estatus_telefono_c is null and a.deleted = 0";
				$results = $GLOBALS['db']->query($query);
				while($row = $GLOBALS['db']->fetchByAssoc($results)) {
					foreach($response['data'] as $telefono) {
						if(!empty($telefono['Telefono'])) {
							if($row['telefono'] == $telefono['Telefono']) {
								$cuenta = $row['id'];
								$respuesta = '['.json_encode($telefono).']';
								$queryUpdate="update tel_telefonos_cstm set estatus_telefono_c = '$respuesta' where id_c = '$cuenta'";
								$resultUpdate = $GLOBALS['db']->query($queryUpdate);
							}
						}
					}
				}

			}catch (Exception $exception) {
				$GLOBALS['log']->fatal($exception->getMessage());
				setErrorLogFailRequest( "Accounts","C4/list", '', $url, $content, $exception->getMessage() );
			}
			
		}
        return true;
    }

	function setErrorLogFailRequest( $module,$endpoint , $bean, $url, $request, $response ){

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
          "parent_type"=> $module,
          "parent_id"=> $id_bean,
          "endpoint"=> $url,
          "request"=> $request,
          "response"=> $response
        );
        $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
  
    }

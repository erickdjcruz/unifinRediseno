<?php

class crm_robina_class
{

    public function procesa_ticket_robina_function($bean = null, $event = null, $args = null)
    {
        require_once("custom/Levementum/UnifinAPI.php");
        global $sugar_config,$db;

        if($bean->estatus_procesado == 'Recibido' && $bean->rfc != ''){
            $GLOBALS['log']->fatal("********* Webhook Recibido *************");
            $GLOBALS['log']->fatal("RFC:".$bean->rfc);
            $query = "SELECT * from accounts_cstm WHERE id_c = '{$bean->id_cuenta}'";
            //$GLOBALS['log']->fatal("QUERYS:".$query);
            $regacc = $db->query($query);
            $accs = [];
            //$GLOBALS['log']->fatal(print_r($regacc, true));
            while ($row = $db->fetchByAssoc($regacc)) {
                //$GLOBALS['log']->fatal(print_r($row, true));
                $accs[] = $row;
            }
            //$GLOBALS['log']->fatal(print_r($accs, true));
            $idCliente = $accs[0]['id_c'];
            $GLOBALS['log']->fatal("idCliente: ".$idCliente);

            $beanAccount = BeanFactory::retrieveBean('Accounts', $idCliente , array('disable_row_level_security' => true));
            //      $idCliente = $args['idCliente'];
            
            $rfc = $bean->rfc;
            $date_issued = $bean->fecha_emision;
            $vigencia = gmdate("Y-m-d");
            //$vigencia = $date_issued;
            
            $url_token_robina = $sugar_config['regimenes_sat_url'].'/auth/login/token';
            $user = $sugar_config['regimenes_sat_user'];
            $password = $sugar_config['regimenes_sat_password'];

            $url_alfresco = $sugar_config['alfresco_url_cfdi'].'/rest/cfdi/uploadDocumentExpDig';

            $response = array();
            $response['robina'] = "";
            $response['quantico_csf'] = "";
            $response['quantico_validator'] = "";
            $response['alfresco'] = "";

            $instanciaAPI = new UnifinAPI();
            $responseToken = $instanciaAPI->postSimilarityToken( $url_token_robina, $user, $password  );

            if( !empty($responseToken) ){
                $token = $responseToken['access_token'];
                
                ///tax-status/retrieve-pdf/{rfc} -- http://192.168.150.231:5471/auth/login/token
                $url_digital_csf = $sugar_config['regimenes_sat_url'].'/tax-status/retrieve-pdf/'.$rfc;
                //$GLOBALS['log']->fatal("Inicia petición Robina CSF: ".$url_digital_csf);
                $responseCSF_base64=$this->callDigitalVal($url_digital_csf, $token );
                file_put_contents('custom/csf/csf_'.$rfc.'.pdf', $responseCSF_base64);
                $b64CSFVal = chunk_split(base64_encode(file_get_contents('custom/csf/csf_'.$rfc.'.pdf')));

                //file_put_contents('custom/csf/csforiginal.pdf', chunk_split($base64_CSF));
                //file_put_contents('custom/csf/csf1.pdf', $responseCSF_base64);            
                $response['robina']= "Validación digital de CSF generada correctamente";
                //$GLOBALS['log']->fatal( "emptyb64_csf: " . !empty($responseCSF_base64) );
                if( !empty($responseCSF_base64) ){
                    //Envia petición hacia alfresco
                    $body_request_alfresco = $this->createBodyRequestAlfresco( $idCliente, $b64CSFVal, $rfc.'.pdf', $date_issued );
                    $GLOBALS['log']->fatal( print_r($body_request_alfresco,true) );
                    $GLOBALS['log']->fatal( print_r($url_alfresco,true) );
                
                    $response_upload_alfresco = $this->callUploadDocument( $url_alfresco, $body_request_alfresco );
                    
                    $GLOBALS['log']->fatal( "Respuesta upload Alfresco:" );
                    //GLOBALS['log']->fatal( print_r($body_request_alfresco,true) );
                    $GLOBALS['log']->fatal( print_r($response_upload_alfresco,true) );
                    $response['alfresco'] = $response_upload_alfresco['resultDescription'];

                    if( !empty( $response_upload_alfresco['data']['folio'] ) ){
                        $GLOBALS['log']->fatal('Alfresco: El folio obtenido es: '.$response_upload_alfresco['data']['folio']);
                        $this->generaAnalizate( $idCliente ,$response_upload_alfresco['data']['folio'] );
                    }
                }

                $url_digital_val = $sugar_config['regimenes_sat_url'].'/tax-status/retrieve-digital-val-pdf/'.$rfc;
                $GLOBALS['log']->fatal("Inicia petición Robina - digitalval: ".$url_digital_val);
                $response_base64=$this->callDigitalVal($url_digital_val, $token );
                //$GLOBALS['log']->fatal( "response_base64: " . !empty($response_base64) );
                if( !empty($response_base64) ){
                    file_put_contents('custom/csf/validator_'.$rfc.'.pdf', $response_base64);
                    $response['robina']= "Validación digital de CSF generada correctamente";

                    //Envía Constancia de Situación Fiscal hacia Quantico
                    $url_expediente = $sugar_config['quantico_expediente_url'].'/Expedient_CS/rest/QuanticoDocuments/QuanticoUploadDocument';
                    //$vigencia = "2023-06-22"; 

                    /*
                    $body_request_quantico = $this->createBodyRequest( $idCliente, "CSF", $base64_CSF, $vigencia );

                    $GLOBALS['log']->fatal("Petición quantico: ".$url_expediente);
                    $GLOBALS['log']->fatal("ID Cliente: ".$idCliente);
                    $response_upload_csf = $this->callUploadDocument( $url_expediente, $body_request_quantico );

                    $GLOBALS['log']->fatal( "Respuesta upload CSF:" );
                    $GLOBALS['log']->fatal( print_r($response_upload_csf,true) );

                    $response['quantico_csf']= $response_upload_csf['Message'];
                    */
                    //Envía Validación Digital hacia Quantico
                    $b64Val = chunk_split(base64_encode(file_get_contents('custom/csf/validator_'.$rfc.'.pdf')));
                    // recupera pdf validador digital-base64
                    $body_request_quantico_validator = $this->createBodyRequest( $idCliente, "ValDigital", $b64Val, $vigencia );
                    // envio quantico validador
                    $response_upload_valDig = $this->callUploadDocument( $url_expediente, $body_request_quantico_validator );
                    $GLOBALS['log']->fatal( print_r($body_request_quantico_validator,true) );
                    $GLOBALS['log']->fatal("Petición quantico: ".$url_expediente);
                    $GLOBALS['log']->fatal( "Respuesta upload Validación Digital:" );
                    //$GLOBALS['log']->fatal($url_expediente);
                    //$GLOBALS['log']->fatal( print_r($body_request_quantico_validator,true) );
                    $GLOBALS['log']->fatal( print_r($response_upload_valDig,true) );
                    
                    $response['quantico_validator']= $response_upload_valDig['Message'];
                }

                $bean->estatus_procesado = 'Procesado';
            }else{
                $bean->estatus_procesado = 'Procesado con error';
            }
            $bean->save();
        }
    }

    public function generaAnalizate( $idCliente, $folio ){

        $basePath = '/rest/cfdi/downloadDocumentExpDig/'.$folio;
        $fechaActual = gmdate("Y-m-d H:i:s");

        $beanAnlzt = BeanFactory::newBean('ANLZT_analizate');

        $beanAnlzt->anlzt_analizate_accountsaccounts_ida = $idCliente;
        $beanAnlzt->load_relationship('anlzt_analizate_accounts');
        $beanAnlzt->anlzt_analizate_accounts->add($idCliente);

        $beanAnlzt->url_documento = $basePath;
        $beanAnlzt->empresa = '1';//Financiera
        $beanAnlzt->tipo = '2';//Documento
        $beanAnlzt->fecha_actualizacion = $fechaActual;
        $beanAnlzt->save();
        $GLOBALS['log']->fatal('Registro Analizate generado: '.$beanAnlzt->id);
    }

    public function callDigitalVal( $url, $token ){

        try{
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token
                ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);

            return $response;

        }catch(Exception $e){

            $this->setErrorLogFailRequest('Validación Digital', "retrieve-digital-val-pdf", '', $url, '', $e->getMessage() );

        }
        

    }

    public function createBodyRequest( $idCliente,$tipo, $base64, $vigencia ){

        $doc = ( $tipo == "CSF" ) ? "CONSTANCIA_SITUACION_FISCAL" : "VALIDACION_DIGITAL_CSF";

        return array(
            "ClientGUID"=> $idCliente,
            "DocumentReference"=> $doc,
            "FileBase64"=> $base64,
            "FechaCreacion"=> $vigencia
        );
    }

    public function createBodyRequestAlfresco( $idCliente, $base64 , $nombreDoc, $date_issued){
        $tipoPersona = 'Cliente';
        if($idCliente){
            $account = BeanFactory::retrieveBean('Accounts', $idCliente, array('disable_row_level_security' => true));
            switch ($account->tipo_registro_cuenta_c) {
                case "5":
                    $tipoPersona = 'Proveedor';
                    break;
                case "3":
                    $tipoPersona = 'Cliente';
                    break;
                case "2":
                    $tipoPersona = 'Prospecto';
                    break;
            }
        }
        
        return array(
            "typeDocument" => "CEDULA_FISCAL",
            "fileName" => $nombreDoc,
            "platform" => "clarivia",
            "company" => "Financiera",
            "content" => $base64,
            "cliente" => $idCliente,
            "date_issued" => $date_issued,
            "tipoCuenta" => $tipoPersona
        );
    }

    public function callUploadDocument ( $url, $body ){
        global $current_user;

        $body_string = json_encode($body);

        try{
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body_string,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
        
                return json_decode($response, true);

        }catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            //$this->setErrorLogFailRequest( "ActualizaSolicitud", '', $host, json_encode($fields), $e->getMessage() );
            $this->setErrorLogFailRequest( "Alfresco","UploadDocumentAlfesco", '', $url, $body_string, $e->getMessage() );

        }
  

    }

    /*
     * Elimina archivo de la ruta especificada
     * @param String $file_name, Nombre de archivo con su ruta completa
     * */
    public function deleteFile($file_name){
        unlink($file_name);

    }

    public function setErrorLogFailRequest( $integration,$endpoint, $bean, $url, $request, $response ){

        $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Unics");
        require_once("custom/clients/base/api/ErrorLogApi.php");
        if( $bean == '' ){
            $id_bean = '';
        }else{
            $id_bean = $bean->id;
        }
        $apiErrorLog = new ErrorLogApi();
        $args = array(
          "integration"=> $integration . " " . $endpoint,
          "system"=> "Unics",
          "parent_type"=> "Accounts",
          "parent_id"=> $id_bean,
          "endpoint"=> $url,
          "request"=> $request,
          "response"=> $response
        );
        $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
  
    }

}
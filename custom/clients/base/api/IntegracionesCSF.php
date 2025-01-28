<?php
/**
 * Created by Salvador Lopez.
 * User: salvadorlopez
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class IntegracionesCSF extends SugarApi
{

    /**
     * Registro de todas las rutas para consumir los servicios del API
     *
     */
    public function registerApiRest()
    {
        return array(
            //GET
            'setInfoCSF' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('IntegracionesCSF'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'integraCSF',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza peticiones hacia Alfresco, Quantico y Robina con los datos de la CSF (Constancia de Situación Fiscal)',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

        );

    }

    public function integraCSF($api, $args){

        require_once("custom/Levementum/UnifinAPI.php");
        global $sugar_config;

        $idCliente = $args['idCliente'];
        $rfc = $args['rfc'];
        $base64_CSF = $args['base64'];
        $date_issued = $args['vigencia'];
        $vigencia = gmdate("Y-m-d");
        
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

        //Envia petición hacia alfresco
        $body_request_alfresco = $this->createBodyRequestAlfresco( $idCliente, $base64_CSF, $rfc.'.pdf', $date_issued );
        $response_upload_alfresco = $this->callUploadDocument( $url_alfresco, $body_request_alfresco );
        
        $GLOBALS['log']->fatal( "Respuesta upload Alfresco:" );
        $GLOBALS['log']->fatal( print_r($response_upload_alfresco,true) );
        $response['alfresco'] = $response_upload_alfresco['resultDescription'];

        if( !empty( $response_upload_alfresco['data']['folio'] ) ){
            $GLOBALS['log']->fatal('Alfresco: El folio obtenido es: '.$response_upload_alfresco['data']['folio']);
            $this->generaAnalizate( $idCliente ,$response_upload_alfresco['data']['folio'] );
        }

        if( !empty($responseToken) ){
            $token = $responseToken['access_token'];
            $url_digital_val = $sugar_config['regimenes_sat_url'].'/tax-status/retrieve-digital-val-pdf/'.$rfc;
            $GLOBALS['log']->fatal("Inicia petición Robina: ".$url_digital_val);
            $response_base64=$this->callDigitalVal($url_digital_val, $token );

            if( !empty($response_base64) ){
                file_put_contents('custom/csf/validator.pdf', $response_base64);
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
                $b64Val = chunk_split(base64_encode(file_get_contents('custom/csf/validator.pdf')));
                $body_request_quantico_validator = $this->createBodyRequest( $idCliente, "ValDigital", $b64Val, $vigencia );
                $response_upload_valDig = $this->callUploadDocument( $url_expediente, $body_request_quantico_validator );

                $GLOBALS['log']->fatal( "Respuesta upload Validación Digital:" );
                //$GLOBALS['log']->fatal($url_expediente);
                //$GLOBALS['log']->fatal( print_r($body_request_quantico_validator,true) );
                //$GLOBALS['log']->fatal( print_r($response_upload_valDig,true) );
                
                $response['quantico_validator']= $response_upload_valDig['Message'];

            }
            
            //$GLOBALS['log']->fatal( print_r($response,true) );
            //$GLOBALS['log']->fatal( $response_base64 );

        }

        return $response;

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

?>

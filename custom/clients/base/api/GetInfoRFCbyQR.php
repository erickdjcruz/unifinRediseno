<?php
/**
 * Created by PhpStorm.
 * User: salvadorlopez
 * Date: 01/06/2020
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetInfoRFCbyQR extends SugarApi
{

    /**
     * Registro de todas las rutas para consumir los servicios del API
     *
     */
    public function registerApiRest()
    {
        return array(
            //GET
            'retrieve' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('GetInfoRFCbyQR'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'getInfoByImageRFC',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que obtiene información de RFC a través de QR',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

            'getInfoCSF' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('GetInfoRFCbyCSF'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'getInfoByCSF',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que obtiene información de RFC a través de Constancia de Situación Fiscal',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),


        );

    }

    /**
     * Obtiene información correspondiente a RFC a partir de imagen QR
     **
     * @param array $api
     * @param array $args Array con los parámetros enviados para su procesamiento
     * @return array $response Array información relacionada con el RFC pasado como QR
     */
    public function getInfoByImageRFC($api, $args){
        global $sugar_config;

        $base64_img=$args['file'];
        $url=$sugar_config['url_scan_qr'];

        $imagen_rfc=$this->generateImage($base64_img);

        $file_name_with_full_path=$imagen_rfc;

        $cFile=$this->prepareImageToParameter($file_name_with_full_path);

        $post = array('file'=> $cFile);

        $response=$this->callScanQR($url,$post);

        if ($response[0]['Código de error']!="" && $response[0]['Código de error']!=null){
            $this->deleteFile($file_name_with_full_path);
        }else{
            //Se agrega nuevo elemento a la respuesta para guardar ruta del QR creado
            $response[0]['path_img_qr']=$file_name_with_full_path;
        }

       // $this->deleteFile($file_name_with_full_path);

        return $response;

    }


    public function getInfoByCSF( $api, $args ){

        require_once("custom/Levementum/UnifinAPI.php");
        $GLOBALS['log']->fatal("SERVICIO CSF");

        $base64_pdf=$args['file'];
        $url_csf=$sugar_config['regimenes_sat_url'].'/tax-status/upload/';
        $url_token = $sugar_config['regimenes_sat_url'].'/auth/login/token';
        $user = $sugar_config['regimenes_sat_user'];
        $password = $sugar_config['regimenes_sat_password'];

        $file_pdf=$this->generateFilePDF($base64_pdf);

        $instanciaAPI = new UnifinAPI();
        $responseToken = $instanciaAPI->postSimilarityToken( $url_token, $user, $password  );

        if( !empty($responseToken) ){
            $token = $responseToken['access_token'];
            $response=$this->callValidateCSF($url_csf, $token , $file_pdf);

        }

        $rfc =  $response['rfc'] ?? null;
        $GLOBALS['log']->fatal( "rfc:".$rfc );
        
        if($rfc != null){
           // Fechas dinámicas
            $from = date('Y-m-01\T00:00:00', strtotime('-1 year'));
            $to = date('Y-m-01\T00:00:00');
        
            $url_ticket = $sugar_config['regimenes_sat_url'].'/orders/place-order';
            
            $body = json_encode([
                "options" => [
                    "period" => [
                        "to" => $to,
                        "from" => $from
                    ]
                ],
                "taxpayer" => $rfc,
                "extractor" => "tax_status"
            ]);

            $GLOBALS['log']->fatal($url_ticket);
            $GLOBALS['log']->fatal($body);
            $response1 = $this->callCreateTicket($url_ticket, $token, $body);
            $GLOBALS['log']->fatal( 'creo ticket por archivo' );
            //$GLOBALS['log']->fatal( print_r($response,true) );
            $GLOBALS['log']->fatal( 'ticket:'.$response1['id'] );
            //$response = json_decode($response, true);
            
            if (isset($response1['detail'][0]['msg']) && $response1['detail'][0]['msg'] === 'value is not a valid dict') {
                $response1['codeerror'] = 400;
                $response1['messageerror'] = 'No se encontraron datos del RFC';
                $GLOBALS['log']->fatal('Error crear ticket');
                //return $response1; // Termina aquí y regresa el error
            }
            $ticket = '';

            // Validar y extraer datos
            if (!empty($response1['id']) && !empty($response1['createdAt'])) {
                $ticket = $response1['id'];
                $response['ticket'] = $ticket;
                $GLOBALS['log']->fatal( 'ticket: '. $ticket);
            }else{
                $response1['error'] = 'Ticket no generado';
                $response1['error_code'] = '401';
            }
        }else{
            $response1['error'] = 'RFC no encontrado';
            $response1['error_code'] = '401';
        }

        return $response;
    }

    /*
     * Función que toma una imagen codificada en base64 y la convierte en una imagen real dentro de una ruta temporal
     * @param String $img, Imagen codificada en base64
     * @return String $file, Nombre de la imagen 'real' generada
     * */
    public function generateImage($img){

        $folderPath = "custom/qr/";

        $image_parts = explode(";base64,", $img);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath .'QR_RFC_'. uniqid() . '.png';

        file_put_contents($file, $image_base64);

        return $file;

    }

    public function generateFilePDF( $base64 ){
        $folderPath = "custom/csf/";
        
        //Se realiza explode ya que la cadena viene como: data:application/pdf;base64,JVBER...
        $pdf_base64 = explode(";base64,", $base64);

        $str_base64 = base64_decode($pdf_base64[1]);

        //Se genera el archivo pdf con el string obtenido
        $archivo = $folderPath .'CSFC_'. uniqid() . '.pdf';

        file_put_contents($archivo, $str_base64);

        return $archivo;


    }

    /*
     * Función que toma un archivo con su ruta y lo codifica en su respectivo formato para enviarlo como parámetro tipo 'File' a un servicio que espera un multipart/form-data
     * @param String $file_name_with_full_path, Ruta del archivo a procesar
     * @return String $cFile, Archivo codificado para enviar como parámetro a servicio
     * */
    public function prepareImageToParameter($file_name_with_full_path){
        $basePath = str_replace("custom/clients/base/api","",__DIR__);
        //$GLOBALS['log']->fatal($basePath.$file_name_with_full_path);

        if (function_exists('curl_file_create')) { // php 5.5+
            $cFile = curl_file_create($basePath . $file_name_with_full_path);
        } else { //
            $cFile = realpath($basePath . $file_name_with_full_path);
        }

        return $cFile;
    }

    /*
     * Función realiza petición a servicio que se expone para scanear un código QR a través de una imagen pasada como parámetro
     * @param String $url, Endpoint a consumir
     * @return array $body, Arreglo con la definición de los parámetros que recibe el endpoint
     * */
    public function callScanQR($url,$body){
        $GLOBALS['log']->fatal($url);
        $GLOBALS['log']->fatal($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $result=curl_exec ($ch);
        $GLOBALS['log']->fatal($result);
        curl_close ($ch);

        return json_decode($result, true);

    }

    public function callValidateCSF( $url, $token, $file ){
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
            CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($file)),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    /*
     * Elimina archivo de la ruta especificada
     * @param String $file_name, Nombre de archivo con su ruta completa
     * */
    public function deleteFile($file_name){
        unlink($file_name);

    }

    public function callCreateTicket( $url, $token, $body ){
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
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

}

?>

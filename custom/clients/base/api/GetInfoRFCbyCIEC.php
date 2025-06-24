<?php
/**
 * Created by PhpStorm.
 * User: erickdejesus
 * Date: 2025-04-08
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetInfoRFCbyCIEC extends SugarApi
{

    /**
     * Registro de todas las rutas para consumir los servicios del API
     *
     */
    public function registerApiRest()
    {
        return array(
            //POST
            'retrieve' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('GetLastTicketbyCIEC'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'getInfoCreateTicket',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que obtiene información de RFC a través de Robina por CIEC',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

            //POST
            'retrieveTicketCiec' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('CreateTicketCIEC'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'CreateTicket',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que crea ticket CIEC',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

            //GET
            'getTicket' => array(
                //request type
                'reqType' => 'GET',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('GetTicketCIEC','?'),
                //endpoint variables
                'pathVars' => array('method','rfc'),
                //method to call
                'method' => 'getInfoTicketCIEC',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que obtiene información del  ticket CIEC',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),

            //POST
            'getInfoCSF' => array(
                //request type
                'reqType' => 'POST',
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('GetInfoRFCbyCIEC'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'getInfoByCIEC',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Método que realiza petición a servicio externo que obtiene información de RFC a través de Constancia de Situación Fiscal',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
        );
    }

    public function getInfoByCIEC( $api, $args ){

        require_once("custom/Levementum/UnifinAPI.php");
        global $sugar_config;
        $GLOBALS['log']->fatal("SERVICIO getDAta CIEC");

        $rfc = $args['rfc'] ?? null;

        // Validación inicial del RFC
        if (empty($rfc)) {
            return [
                'success' => false,
                'codeerror' => 400,
                'messageerror' => "RFC no proporcionado",
                'data' => []
            ];
        }

        
        $url_token = $sugar_config['regimenes_sat_url'].'/auth/login/token';
        $user = $sugar_config['regimenes_sat_user'];
        $password = $sugar_config['regimenes_sat_password'];

        $instanciaAPI = new UnifinAPI();
        $responseToken = $instanciaAPI->postSimilarityToken( $url_token, $user, $password  );

        $token = $responseToken['access_token'] ?? null;
        $GLOBALS['log']->fatal("TOKEN: " . print_r($token, true));
        $GLOBALS['log']->fatal("RFC: $rfc");

        // Diccionario de estados traducidos
        $status_dict = [
            't' => 'Terminado',
            'p' => 'Pendiente',
            'i' => 'Acceso por contrasena y e.firma portable | RFC/CIEC inválido(s)',
            'r' => 'Descarga parcial de Facturas',
            'f' => 'Tiempo de espera excedido.'
        ];

        // Inicialización de resultado
        $resultado = [
            'success' => false,
            'codeerror' => 500,
            'messageerror' => 'Error no especificado',
            'data' => []
        ];
   
        if( !empty($token) ){
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
            $response = $this->callCreateTicket($url_ticket, $token, $body);
            $GLOBALS['log']->fatal( 'creo ticket' );
            //$GLOBALS['log']->fatal( print_r($response,true) );
            //$response = json_decode($response, true);
            
            if (isset($response['detail'][0]['msg']) && $response['detail'][0]['msg'] === 'value is not a valid dict') {
                $resultado['codeerror'] = 400;
                $resultado['messageerror'] = 'No se encontraron datos del RFC';
                $GLOBALS['log']->fatal('Error crear ticket');
                return $resultado; // Termina aquí y regresa el error
            }
            $ticket = '';
            $createdAt = '';

            // Validar y extraer datos
            if (!empty($response['id']) && !empty($response['createdAt'])) {
                $ticket = $response['id'];
                $GLOBALS['log']->fatal( 'ticket: '. $ticket);
                //$createdAt = $response['createdAt'];

                //$url_csf=$sugar_config['regimenes_sat_url'].'/webhook-requests/retrieve/'.$ticket;
                $url_csf=$sugar_config['regimenes_sat_url'].'/orders/retrieve/'.$ticket;
                $pendiente = true;
                $maxTries = 10;
                $try = 0;

                while ($pendiente && $try < $maxTries) {
                    $try++;
                    $GLOBALS['log']->fatal('url_csf - retrieve:'.$url_csf );
                    $response=$this->callGetTicketCIEC($url_csf, $token );                    
                    //$GLOBALS['log']->fatal( print_r($response,true) );                    
                    // Decodificar el JSON a un arreglo
                    //$latestItem = json_decode($response, true);
                    $status = $response['is_json_data'] ?? null;
                    $GLOBALS['log']->fatal("Status final recibido:". $status);
                    if (!$status) {
                        // Opcional: esperar antes de siguiente intento para evitar saturar la API
                        sleep(10);
                    }else {
                        $pendiente = false;
                    }
                }

                if(!$pendiente){
                    // Última llamada para obtener el resultado final
                    $url_csf = $sugar_config['regimenes_sat_url'] . '/tax-status/retrieve/' . $rfc;
                    $GLOBALS['log']->fatal('url_csf - retrieve: '.$url_csf );
                    $response = $this->callValidateCSF($url_csf, $token);

                    $response['ticket'] = $ticket;

                    //$GLOBALS['log']->fatal( print_r($response,true) );                    
                    if (isset($response['detail'][0]['msg']) && $response['detail'][0]['msg'] === 'value is not a valid dict') {
                        $resultado['success'] = false;
                        $resultado['codeerror'] = 403;
                        $resultado['messageerror'] = 'No se encontraron datos del RFC';
                        $GLOBALS['log']->fatal('Error consulta de RFC');
                        return $resultado; // Termina aquí y regresa el error
                    }
                    
                    if (isset($response['detail']) && $response['detail'] === 'No se encontraron constancias, por favor asegurese de haber realizado la petición de descarga previamente.') {
                        $resultado['success'] = false;
                        $resultado['codeerror'] = 403;
                        $resultado['messageerror'] = 'No se encontraron constancias, por favor asegurese de haber realizado la petición de descarga previamente.';
                        $GLOBALS['log']->fatal('No se encontraron constancias, por favor asegurese de haber realizado la petición de descarga previamente.');
                        return $resultado; // Termina aquí y regresa el error
                    }
                    $GLOBALS['log']->fatal("Respuesta final-completa-regresa data");
                    //$resultado['codeerror'] = 0;
                    //$resultado['messageerror'] = 'Consulta realizada correctamente';
                    //$resultado['data'] = json_decode($response, true);
                    $resultado = $response;
                    $resultado['success'] = true;
                } else {
                    $resultado['success'] = false;
                    $resultado['codeerror'] = 204;
                    $resultado['messageerror'] = "Demasiado tiempo de espera al recuperar información del RFC.";
                }                
            } else {
                $resultado['success'] = false;
                $resultado['codeerror'] = 204;
                $resultado['messageerror'] = "Respuesta sin datos válidos de ID o createdAt";
            }
        } else {
            $resultado['success'] = false;
            $resultado['codeerror'] = 401;
            $resultado['messageerror'] = "No se pudo obtener el token o respuesta inválida";
        }

        $GLOBALS['log']->fatal("Respuesta",$resultado);
        
        return $resultado;
    }

    /**
     * Obtiene información correspondiente a RFC a partir de imagen QR
     **
     * @param array $api
     * @param array $args Array con los parámetros enviados para su procesamiento
     * @return array $response Array información relacionada con el RFC pasado como QR
     */

    public function getInfoCreateTicket( $api, $args ){

        require_once("custom/Levementum/UnifinAPI.php");
        $GLOBALS['log']->fatal("SERVICIO CIEC");

        $rfc=$args['rfc'];
        $url_ciec=$sugar_config['regimenes_sat_url'].'orders/list-all?taxpayer.id='.$rfc;
        $url_token = $sugar_config['regimenes_sat_url'].'/auth/login/token';
        $user = $sugar_config['regimenes_sat_user'];
        $password = $sugar_config['regimenes_sat_password'];

        $instanciaAPI = new UnifinAPI();
        $responseToken = $instanciaAPI->postSimilarityToken( $url_token, $user, $password  );

        if( !empty($responseToken) ){
            $token = $responseToken['access_token'];
            $GLOBALS['log']->fatal("SERVICIO CIEC: ".$url_ciec);
            $response=$this->callValidateCIEC($url_ciec, $token );
            
            $GLOBALS['log']->fatal($response);
            $data = json_decode($response, true);
            $GLOBALS['log']->fatal($data);
            $status_dict = [
                't' => 'Terminado',
                'p' => 'Pendiente',
                'i' => 'Acceso por contrasena y e.firma portable | RFC/CIEC inválido(s)',
                'r' => 'Descarga parcial de Facturas',
                'f' => 'Tiempo de espera excedido.'
            ];

            // Inicializa la respuesta
            $response = [];

            if (isset($data['totalItems']) && $data['totalItems'] == 0) {
                $response = [
                    "totalItems" => 0,
                    "member" => []
                ];
            } else {
                $latestItem = null;
                $latestDate = null;

                foreach ($data['member'] as $item) {
                    if (!isset($item['updatedAt']) || empty($item['updatedAt'])) {
                        continue; // Salta si no hay updatedAt
                    }
            
                    $updatedAt = $item['updatedAt'];
            
                    if (is_null($latestDate) || strtotime($updatedAt) > strtotime($latestDate)) {
                        $latestItem = $item;
                        $latestDate = $updatedAt;
                    }
                }

                if ($latestItem) {
                    $periodFrom = $latestItem['options']['period']['from'] ?? null;
                    $periodTo = $latestItem['options']['period']['to'] ?? null;
                    $itemId = $latestItem['id'];
                    $createdAt = $latestItem['createdAt'] ?? null;
                    $updatedAt = $latestItem['updatedAt'] ?? null;

                    $statusCode = $latestItem['status_robina']['status'] ?? null;
                    $statusTranslated = $status_dict[$statusCode] ?? 'Desconocido';

                    $response = [
                        "totalItems" => $data['totalItems'],
                        "latestItemId" => $itemId,
                        "period_from" => $periodFrom,
                        "period_to" => $periodTo,
                        "createdAt" => $createdAt,
                        "updatedAt" => $updatedAt,
                        "status_robina" => $statusTranslated
                    ];
                } else {
                    $response = [
                        "message" => "No se encontró ningún item válido."
                    ];
                }
            }
        }
        return $response;
    }

    public function CreateTicket( $api, $args ){
        require_once("custom/Levementum/UnifinAPI.php");
        global $sugar_config;
        
        $rfc = $args['rfc']; // Asegúrate de que 'rfc' esté en los argumentos
    
        // Fechas dinámicas
        $from = date('Y-m-01T00:00:00', strtotime('-1 year'));
        $to = date('Y-m-01T00:00:00');
    
        $url_ticket = $sugar_config['regimenes_sat_url'].'/orders/place-order';
        $url_token = $sugar_config['regimenes_sat_url'].'/auth/login/token';
        $user = $sugar_config['regimenes_sat_user'];
        $password = $sugar_config['regimenes_sat_password'];
    
        $instanciaAPI = new UnifinAPI();
        $responseToken = $instanciaAPI->postSimilarityToken( $url_token, $user, $password  );
        
        $GLOBALS['log']->fatal($responseToken);
        $GLOBALS['log']->fatal($rfc);
        $body = json_encode([
            "options" => [
                "period" => [
                    "from" => $from,
                    "to" => $to
                ]
            ],
            "taxpayer" => $rfc,
            "extractor" => "tax_situations"
        ]);
        $GLOBALS['log']->fatal($body);
        if (!empty($responseToken)) {
            $token = $responseToken['access_token'];
            $response = $this->callCreateTicket($url_ticket, $token, $body);
            
            $GLOBALS['log']->fatal($response);
            $response = json_decode($response, true);
            $GLOBALS['log']->fatal($response);
            // Validar y extraer datos
            if (!empty($response['id']) && !empty($response['createdAt'])) {
                return [
                    "id" => $response['id'],
                    "createdAt" => $response['createdAt']
                ];
            } else {
                return [
                    "error" => "Respuesta sin datos válidos de ID o createdAt",
                    "response_raw" => $response
                ];
            }
        }
    
        return [
            "error" => "No se pudo obtener el token o respuesta inválida"
        ];
    }
        

    public function getInfoTicketCIEC( $api, $args ){

        require_once("custom/Levementum/UnifinAPI.php");
        $GLOBALS['log']->fatal("SERVICIO CSF Info Ticket");

        $ticket = $args['ticket']; // Asegúrate de que 'rfc' esté en los argumentos
        $url_csf=$sugar_config['regimenes_sat_url'].'/orders/retrieve/'.$ticket;
        $url_token = $sugar_config['regimenes_sat_url'].'/auth/login/token';
        $user = $sugar_config['regimenes_sat_user'];
        $password = $sugar_config['regimenes_sat_password'];

        $instanciaAPI = new UnifinAPI();
        $responseToken = $instanciaAPI->postSimilarityToken( $url_token, $user, $password  );

        if( !empty($responseToken) ){
            $token = $responseToken['access_token'];
            $response=$this->callGetTicketCIEC($url_csf, $token );
            
            $GLOBALS['log']->fatal($response);
            // Decodificar el JSON a un arreglo
            $latestItem = json_decode($response, true);
            $GLOBALS['log']->fatal($latestItem);
            // Diccionario de estados traducidos
            $status_dict = [
                "t" => "Terminado",
                "p" => "Pendiente",
                "e" => "Error"
            ];

            // Extraer valores con validación
            $periodFrom = $latestItem['options']['period']['from'] ?? null;
            $periodTo = $latestItem['options']['period']['to'] ?? null;
            $itemId = $latestItem['id'] ?? null;
            $createdAt = $latestItem['createdAt'] ?? null;
            $updatedAt = $latestItem['updatedAt'] ?? null;
            $statusCode = $latestItem['status_robina']['status'] ?? null;
            $statusTranslated = $status_dict[$statusCode] ?? 'Desconocido';

            // Resultado
            $resultado = [
                'id' => $itemId,
                'periodFrom' => $periodFrom,
                'periodTo' => $periodTo,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'statusCode' => $statusCode,
                'statusTranslated' => $statusTranslated
            ];
        }

        return $resultado;
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

    public function callValidateCIEC( $url, $token ){
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

        return json_decode($response, true);
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

    public function callGetTicketCIEC( $url, $token ){
        $curl = curl_init();

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
                'Authorization: Bearer ' . $token,
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
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
            $GLOBALS['log']->fatal($e);
        }
    }

    public function callValidateCSF( $url, $token ){
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

        return json_decode($response, true);
    }
    
}

?>

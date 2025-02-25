<?php

/**
 * Created by Tactos.
 * User: JG
 * Date: 30/06/20
 * Time: 06:42 PM
 * config_override
 * $sugar_config['url_scan_qr'] = 'http://192.168.150.95:18090/scan';
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('custom/clients/base/api/GetDireccionesCP.php');
use Sugarcrm\Sugarcrm\Util\Uuid;

class getDireccionCPQR extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'retrieve' => array(
                'reqType' => 'GET',
                'noLoginRequired' => true,
                'path' => array('DireccionesQR', '?', '?', '?', '?','?','?'),
                'pathVars' => array('module', 'cp', 'indice', 'colonia_rfc', 'ciudad_rfc','entidad_rfc','ciudad_csf'),
                'method' => 'getAddressByCPQR',
                'shortHelp' => 'Método GET para obtener información relacionada al Código Postal.',
                'longHelp' => 'y compara que la colonia y cuidad exista. En caso contrario la agrega como nueva',
            ),

        );

    }

    public function getAddressByCPQR($api, $args)
    {
        //$GLOBALS['log']->fatal("*****DIRECCIONES QR*****");
        //$GLOBALS['log']->fatal(print_r($args,true));
        $colonia_QR = ($args['colonia_rfc']=='_') ? ' ' : $args['colonia_rfc'];
        $cod_postal=$args['cp'];
        $ciudad_QR = $args['ciudad_rfc'];
        $estado_QR = $args['entidad_rfc'];
        
        //Se obtiene ciudad a través de CSF
        $ciudad_csf = ($args['ciudad_csf']=='_') ? ' ' : $args['ciudad_csf'];
        $call_api = new GetDireccionesCP();
        $resultado = $call_api->getAddressByCP($api, $args);
        //$GLOBALS['log']->fatal( print_r($resultado,true) );
        $arr_colonias = $resultado['colonias'];
        $pais_id = intval(substr($resultado['idCP'], 0, 3));
        $estado_id = intval(substr($resultado['idCP'], 3, 3));
        $municipio_id = intval(substr($resultado['idCP'], 6, 3));

        $arr_estado = $resultado['estados'];
        $arr_municipio = $resultado['municipios'];
        $arr_ciudades = $resultado['ciudades'];
        $arr_pais = $resultado['paises'];
        $id_pais = $arr_pais[0]['idPais'];
        $pais_val= $arr_pais[0]['namePais'];
        
        $colonia_existe = false;
        $ciudad_existe = false;
        $municipio_existe = false;

        $existe_dato = false;
        
        $aux = null;
        $arrin=null;
        
        
        
        $auxindex = $this->searchForId($colonia_QR, $arr_colonias,'nameColonia');
        //$GLOBALS['log']->fatal('auxindex1',$auxindex);
        if( $auxindex >= 0){

            $colonia_existe = true;

            $arrin = array( $auxindex => $arr_colonias[$auxindex]);
            $aux = array( 'colonias'=> $arrin);
            $arr_colonias = $aux;

            unset($resultado['colonias']);
            $arr_colonias['colonias'][0] = $arr_colonias['colonias'][$auxindex];
            if($auxindex != 0) unset($arr_colonias['colonias'][$auxindex]);
            $resultado = array_replace($resultado, $arr_colonias);
        }
        
        //$auxindex = array_search($estado_QR,$arr_estado,false);
        $auxindex = $this->searchForId($estado_QR, $arr_estado,'nameEstado');
        //$GLOBALS['log']->fatal('searchForId',$estado_QR,$arr_estado,$auxindex);
        if( $auxindex != '-1'){
            $arrin = array( $auxindex => $arr_estado[$auxindex], );
            $aux = array( 'estados'=> $arrin);
            $arr_estado = $aux;
            $estado_id = isset($arr_estado['estados'][$auxindex]['idEstado']) ? intval(substr($arr_estado['estados'][$auxindex]['idEstado'],-3)) : 0 ;
            $estado_val = $arr_estado[$auxindex]['nameEstado'];
            $arr_estado['estados'][0] = $arr_estado['estados'][$auxindex];
            if($auxindex != 0) unset($arr_estado['estados'][$auxindex]);
            unset($resultado['estados']);
            $resultado = array_replace($resultado, $arr_estado);        
        }else{
            $estado_id = $arr_estado[0]['idEstado'];
            $estado_val= $arr_estado[0]['nameEstado'];
        }
        
        //$auxindex = array_search($ciudad_QR,$arr_municipio,false);
        $auxindex = $this->searchForId($ciudad_QR, $arr_municipio,'nameMunicipio');
        //$GLOBALS['log']->fatal('auxindex',$auxindex);
        //$GLOBALS['log']->fatal('arr_municipio',$arr_municipio);
        //$GLOBALS['log']->fatal('searchForId',$ciudad_QR,$arr_municipio,$auxindex);
        if( $auxindex != '-1' && $auxindex >=0){

            $municipio_existe = true;
            
            $arrin = array( $auxindex => $arr_municipio[$auxindex], );
            $aux = array( 'municipios'=> $arrin);
            $arr_municipio = $aux;
            $municipio_id = isset($arr_municipio['municipios'][$auxindex]['idMunicipio']) ? intval(substr($arr_municipio['municipios'][$auxindex]['idMunicipio'],-3)) : 0 ;
            $municipio = $arr_municipio[$auxindex]['nameMunicipio'];

            $arr_municipio['municipios'][0] = $arr_municipio['municipios'][$auxindex];
            if($auxindex != 0) unset($arr_municipio['municipios'][$auxindex]);
            unset($resultado['municipios']); 
            $resultado = array_replace($resultado, $arr_municipio);        
        }else{
            $municipio_id = $arr_municipio[0]['idMunicipio'];
            $municipio= $arr_municipio[0]['nameMunicipio'];
        }

        $auxindex = $this->searchForId($ciudad_csf, $arr_ciudades,'nameCiudad');
        //$GLOBALS['log']->fatal('auxindex1',$auxindex);
        if( $auxindex >= 0){

            $ciudad_existe = true;

            $arrin = array( $auxindex => $arr_ciudades[$auxindex]);
            $aux = array( 'ciudades'=> $arrin);
            $arr_ciudades = $aux;

            unset($resultado['ciudades']);
            $arr_ciudades['ciudades'][0] = $arr_ciudades['ciudades'][$auxindex];
            if($auxindex != 0) unset($arr_ciudades['ciudades'][$auxindex]);
            $resultado = array_replace($resultado, $arr_ciudades);
        }

        if(!$colonia_existe)
        {
            $GLOBALS['log']->fatal("NO EXISTE COLONIA, SE PROCEDE A INSERTAR");
            
            //$data = $this->buildBodyRequest( 'colonia', $pais_id , $estado_id, $municipio_id, $colonia_QR, $cod_postal, '', '');
           
            $data = $this->insertSepomex( 'colonia',$cod_postal, $id_pais ,$pais_val, $estado_id,$estado_val, $municipio_id, $municipio,'',$colonia_QR , '', '');
            //$data = $this->buildBodyRequest( 'colonia', $pais_id , $estado_id, $municipio_id, $colonia_QR, $cod_postal, '', '');
            //$result=$this->insertColonia($pais_id,$estado_id,$municipio_id,$cod_postal,$colonia_QR);
            //$result = $this->insertDataDireccion( '/direccion/insertColonia', $data );
            /*$GLOBALS['log']->fatal('$data:'.$data);
            require_once("custom/clients/base/api/CheckSaveSepomex.php");
            $call_api_sepomex = new CheckSaveSepomex();
            $result = $call_api_sepomex->saveRecord('saveRecordSepomex', $data);
            $GLOBALS['log']->fatal('$result',$result);
            */

            if( $result['result'] != 'OK' ){

                $queryColonia = "SELECT * FROM dir_sepomex WHERE codigo_postal='{$cod_postal}' AND colonia = '{$colonia_QR}'";
                $resultQ = $GLOBALS['db']->query($queryColonia);

                if( $resultQ->num_rows > 0 ){

                    $existe_dato = true;

                }

            }
        }

        if(!$ciudad_existe)
        {
            $GLOBALS['log']->fatal('NO EXISTE CIUDAD, SE PROCEDE A INSERTAR');
            $data = $this->buildBodyRequest( 'ciudad', $pais_id , $estado_id, $municipio_id, $colonia_QR, $cod_postal, $ciudad_csf, $municipio);
            
            $result = $this->insertDataDireccion( '/direccion/insertCiudad', $data );

            if( !empty($result['name']) ){

                $queryCiudad = "Select * from dire_ciudad where name = '{$ciudad_csf}'";
                $resultC = $GLOBALS['db']->query($queryCiudad);

                if( $resultC->num_rows > 0 ){

                    $existe_dato = true;

                }

            }
            
        }

        if(!$municipio_existe)
        {
            $GLOBALS['log']->fatal('NO EXISTE MUNICIPIO, SE PROCEDE A INSERTAR');
            //$result=$this->insertMunicipio($pais_id,$estado_id, $ciudad_QR);
            $data = $this->buildBodyRequest( 'municipio', $pais_id , $estado_id, $municipio_id, $colonia_QR, $cod_postal, $ciudad_csf, $municipio);
            
            $result = $this->insertDataDireccion( '/direccion/insertMunicipio', $data );

            if( !empty($result['name']) ){

                $queryMunicipio = "Select * from dire_municipio where name = '{$ciudad_QR}'";
                $resultM = $GLOBALS['db']->query($queryMunicipio);

                if( $resultM->num_rows > 0 ){
                    $existe_dato = true;
                }

            }
            
        }

        if( $existe_dato ){

            $GLOBALS['log']->fatal( "Se insertó dato, se vuelven a cargar datos" );
            $resultado = $this->getAddressByCPQR($api, $args);
        }

        return $resultado;
    }

    public function searchForId($id, $array , $busqueda) {
        foreach ($array as $key => $val) {
            if ($val[$busqueda] === $id) {
                return $key;
            }
        }
        return -1;
    }

    public function buildBodyRequest( $dato, $pais, $estado, $idMunicipio, $colonia, $cp, $ciudad, $municipio ){
        $data = null;
        switch( $dato ){
            case 'colonia':
                $data = json_encode(
                    array(
                        "idPais" => $pais,
                        "idEstado" => $estado,
                        "idMunicipio" => $idMunicipio,
                        "colonia" => $colonia,
                        "cp" => $cp
                    )
                );
            break;

            case 'ciudad':
                $data = json_encode(
                    array(
                        "idPais" => $pais,
                        "idEstado" => $estado,
                        "ciudad" => $ciudad,
                    )
                );
            break;

            case 'municipio':
                $data = json_encode(
                    array(
                        "idPais" => $pais,
                        "idEstado" => $estado,
                        "municipio" => $municipio,
                    )
                );
            break;

        }

        return $data;
    }

    public function buildBodyRequestSepomex( $dato, $cp,$id_pais, $pais,$id_estado, $estado, $id_municipio,$municipio ,$id_colonia,$colonia, $id_ciudad,$ciudad){
        $data = null;
        
        $data = json_encode(
            array(
            "cp"=>$cp,
            "pais"=>$id_pais,
            "labelPais"=>$pais,
            "estado"=>$id_estado,
            "labelEstado"=>$estado,
            //"ciudad"=>$id_ciudad,
            //"labelCiudad"=>$ciudad,
            "municipio"=>$id_municipio,
            "labelMunicipio"=>$municipio,
            //"colonia":colonia, únicamente se inserta la etiqueta, ya que el id no se conoce
            "labelColonia"=>$colonia
            )
        );
        return $data;
    }   

    public function insertSepomex( $dato, $cp,$id_pais, $pais,$id_estado, $estado, $id_municipio,$municipio ,$id_colonia,$colonia, $id_ciudad,$ciudad){
        global $current_user;
        $new_id_sep=Uuid::uuid1();
        $id_user=$current_user->id;
        $current_date=TimeDate::getInstance()->nowDb();
        $id_colonia = Uuid::uuid1();
        $name=$pais ." ".$cp." ".$estado." ".$colonia;//labelPais CP Estado Colonia
        $qinsertRecordSepomex="INSERT INTO `dir_sepomex` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `pais`, `id_pais`, `codigo_postal`, `estado`, `id_estado`, `ciudad`, `id_ciudad`, `municipio`, `id_municipio`, `colonia`, `id_colonia`) VALUES ('{$new_id_sep}', '{$name}', '{$current_date}', '{$current_date}', '{$id_user}', '{$id_user}', '0', '{$pais}', '{$id_pais}', '{$cp}', '{$estado}', '{$id_estado}','{$ciudad}', '{$id_ciudad}', '{$municipio}', '{$id_municipio}', '{$colonia}', '{$id_colonia}');";
        $GLOBALS['db']->query($qinsertRecordSepomex);
        return true;

    }        

    public function insertDataDireccion( $endpoint, $data){
        global $sugar_config;
        $host = $sugar_config['url_uniclick_direcciones'];
        $url = $host . $endpoint;
        $timeout = 500;
        $error_report = FALSE;

        $headers = array(
            'Content-Type:application/json',
        );
        
        $GLOBALS['log']->fatal("BODY REQUEST");
        $GLOBALS['log']->fatal( print_r($data,true) );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        try {
            $response = curl_exec($curl);
            $GLOBALS['log']->fatal("respuesta servicio \n" . $response);
            $GLOBALS['log']->fatal( json_decode($response, true) );
            curl_close($curl);

            return json_decode($response, true);
        } catch (Exception $ex) {
            $GLOBALS['log']->fatal("Error al ejecutar servicio ". $url . $ex);
        }

    }
}

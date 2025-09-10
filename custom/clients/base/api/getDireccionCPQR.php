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
use Symfony\Component\Validator\Constraints\Length;

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
        $colonia_QR = ($args['colonia_rfc']=='') ? ' ' : $args['colonia_rfc'];
        $cod_postal=$args['cp'];
        $ciudad_QR = $args['ciudad_rfc'];
        $estado_QR = $args['entidad_rfc'];
        //Se obtiene ciudad a través de CSF
        $ciudad_csf = ($args['ciudad_csf']=='') ? ' ' : $args['ciudad_csf'];
        //$call_api = new GetDireccionesCP();
        //$resultado = $call_api->getAddressByCP($api, $args);
        //$GLOBALS['log']->fatal( print_r($resultado,true) );
        
        $estado_QR = $estado_QR ?? "";
        $ciudad_QR = $ciudad_QR ?? "";
        $colonia_QR = $colonia_QR ?? "";
        $ciudad_csf = $ciudad_csf ?? "";

        $estadon = $this->normalizeText($estado_QR);
        $municipion = $this->normalizeText($ciudad_QR);
        $colonian = $this->normalizeText($colonia_QR);
        $ciudadn = $this->normalizeText($ciudad_csf);
        $GLOBALS['log']->fatal('cod_postal',$cod_postal );
        //$GLOBALS['log']->fatal('estadon',$estadon );
        //$GLOBALS['log']->fatal('municipion',$municipion);
        //$GLOBALS['log']->fatal('colonian',$colonian);
        //$GLOBALS['log']->fatal('ciudadn',$ciudadn);

        $pais='México';
        $id_pais = '2';
        
        $id_sepomex = '';

        $columns = ["estado","ciudad","municipio","colonia"];
        $datos = [$estadon , $ciudadn , $municipion , $colonian];
        $result = $this->consultaSepomex($columns , $datos , $cod_postal);

        /*$query = "SELECT id, pais, id_pais, estado, id_estado, ciudad, id_ciudad, municipio, id_municipio  FROM dir_sepomex 
        WHERE codigo_postal = '$cod_postal' 
        AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
            estado, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$estadon' 
        AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
            ciudad, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$ciudadn'
        AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
            municipio, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$municipion'
        AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
            colonia, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$colonian' 
        order by id desc;";
        */
        //$result = $GLOBALS['db']->query($query);
        if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $prow = $row;
            $id_sepomex = $row['id'];
            $GLOBALS['log']->fatal('id_sepomex',$id_sepomex);
        }else {
            //Busqueda Estado
            $columns = ["estado"];
            $datos = [$estadon ];
            $result = $this->consultaSepomex($columns , $datos , $cod_postal);

            if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                // estado
                // Búsqueda sin colonia
                $columns = ["estado","ciudad","municipio"];
                $datos = [$estadon , $ciudadn , $municipion];
                $result = $this->consultaSepomex($columns , $datos , $cod_postal);
          
                if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                    $GLOBALS['log']->fatal('sin colonia');
                    $id_pais = $row['id_pais'];
                    $id_estado = $row['id_estado'];
                    $id_municipio = $row['id_municipio'];
                    $id_ciudad = $row['id_ciudad'];
                    $id_colonia = Uuid::uuid1();
                }else{
                    // Búsqueda sin ciudad
                    $columns = ["estado","municipio","colonia"];
                    $datos = [$estadon , $municipion , $colonian ];
                    $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                    
                    if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                        $GLOBALS['log']->fatal('sin ciudad');
                        $id_pais = $row['id_pais'];
                        $id_estado = $row['id_estado'];
                        $id_municipio = $row['id_municipio'];
                        $id_ciudad = Uuid::uuid1();
                        $id_colonia = $row['id_colonia'];
                    }else{
                        // Búsqueda sin municipio
                        $GLOBALS['log']->fatal('sin municipio');
                        $columns = ["estado","ciudad","colonia"];
                        $datos = [$estadon , $ciudadn , $colonian ];
                        $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                        
                        if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                            $GLOBALS['log']->fatal('sin municipio');
                            $id_pais = $row['id_pais'];
                            $id_estado = $row['id_estado'];
                            $id_municipio = Uuid::uuid1();
                            $id_ciudad = $row['id_ciudad'];
                            $id_colonia = $row['id_colonia'];
                        }else{
                            // Búsqueda sin ciudad y sin colonia
                            $columns = ["estado","municipio"];
                            $datos = [$estadon , $municipion ];
                            $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                            
                            if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                                $GLOBALS['log']->fatal('sin ciudad,sin colonia');
                                $id_pais = $row['id_pais'];
                                $id_estado = $row['id_estado'];
                                $id_municipio = $row['id_municipio'];
                                $id_ciudad = Uuid::uuid1();
                                $id_colonia = Uuid::uuid1();
                            } else {
                                // Buscar sin  municipio y sin colonia
                                $columns = ["estado","ciudad"];
                                $datos = [$estadon , $ciudadn ];
                                $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                                
                                if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                                    $GLOBALS['log']->fatal('sin municipio,sin colonia');
                                    $id_pais = $row['id_pais'];
                                    $id_estado = $row['id_estado'];
                                    $id_municipio = Uuid::uuid1();
                                    $id_ciudad = $row['id_ciudad'];
                                    $id_colonia = Uuid::uuid1();
                                } else {
                                    $GLOBALS['log']->fatal('sin municipio, sin ciudad,sin colonia');
                                    $id_pais = $row['id_pais'];
                                    $id_estado = $row['id_estado'];
                                    $id_municipio = Uuid::uuid1();
                                    $id_ciudad = Uuid::uuid1();
                                    $id_colonia = Uuid::uuid1();
                                }
                            }
                        }
                    }
                }
            }else{
                $id_estado = Uuid::uuid1();
                // sin estado
                // Búsqueda sin colonia
                $columns = ["ciudad","municipio"];
                $datos = [ $ciudadn , $municipion];
                $result = $this->consultaSepomex($columns , $datos , $cod_postal);
          
                if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                    $GLOBALS['log']->fatal('sin colonia');
                    $id_pais = $row['id_pais'];
                    $id_municipio = $row['id_municipio'];
                    $id_ciudad = $row['id_ciudad'];
                    $id_colonia = Uuid::uuid1();
                }else{
                    // Búsqueda sin ciudad
                    $columns = ["municipio","colonia"];
                    $datos = [$municipion , $colonian ];
                    $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                    
                    if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                        $GLOBALS['log']->fatal('sin ciudad');
                        $id_pais = $row['id_pais'];
                        $id_municipio = $row['id_municipio'];
                        $id_ciudad = Uuid::uuid1();
                        $id_colonia = $row['id_colonia'];
                    }else{
                        // Búsqueda sin municipio
                        $columns = ["ciudad","colonia"];
                        $datos = [$ciudadn , $colonian ];
                        $result = $this->consultaSepomex($columns , $datos , $cod_postal);
                        
                        if ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                            $GLOBALS['log']->fatal('sin municipio');
                            $id_pais = $row['id_pais'];
                            $id_municipio = Uuid::uuid1();
                            $id_ciudad = $row['id_ciudad'];
                            $id_colonia = $row['id_colonia'];
                        }else{
                            $GLOBALS['log']->fatal('sin municipio, sin ciudad,sin colonia');
                            $id_pais = $row['id_pais'];
                            $id_municipio = Uuid::uuid1();
                            $id_ciudad = Uuid::uuid1();
                            $id_colonia = Uuid::uuid1();
                        }
                    }
                }
            }
            $existe_dato = true;
            $id_sepomex = Uuid::uuid1();
            $GLOBALS['log']->fatal('id_sepomex',$id_sepomex);
            $name = "$pais $cod_postal $estado_QR $colonia_QR";

            $estado_QR  = ($estado_QR == '_') ? '' : $estado_QR;
            $ciudad_csf  = ($ciudad_csf == '_') ? '' : $ciudad_csf;
            $ciudad_QR  = ($ciudad_QR == '_') ? '' : $ciudad_QR;
            $colonia_QR  = ($colonia_QR == '_') ? '' : $colonia_QR;
            
            // Insertar en dir_sepomex
            $insert_query = "INSERT IGNORE INTO dir_sepomex (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, pais, id_pais, codigo_postal, estado, id_estado, ciudad, id_ciudad, municipio, id_municipio, colonia, id_colonia, team_id, team_set_id) 
                            VALUES ('$id_sepomex', '$name', NOW(), NOW(), '{$GLOBALS['current_user']->id}', '{$GLOBALS['current_user']->id}', '', 0, '$pais', '$id_pais', '$cod_postal', '$estado_QR', '$id_estado', '$ciudad_csf', '$id_ciudad', '$ciudad_QR', '$id_municipio', '$colonia_QR', '$id_colonia', 1, 1)";
            $GLOBALS['log']->fatal('insert_query',$insert_query);
            $GLOBALS['db']->query($insert_query);
        
        }

        if( $existe_dato ){

            $GLOBALS['log']->fatal( "Se insertó dato, se vuelven a cargar datos" );
            //$resultado = $this->getAddressByCPQR($api, $args);
            $query = "SELECT id, name, codigo_postal , pais, id_pais, estado, id_estado, ciudad, id_ciudad, municipio, id_municipio, colonia, id_colonia FROM dir_sepomex WHERE id = '$id_sepomex'";
            //$GLOBALS['log']->fatal('query',$query);
            $result = $GLOBALS['db']->query($query);
            $prow = $GLOBALS['db']->fetchByAssoc($result);
        }
        //$GLOBALS['log']->fatal('result',$result);
        $resultado = $this->obtenerDatosSepomex($prow);

        return $resultado;
    }

    public function consultaSepomex($filtros , $data , $cod_postal){

        $query = "SELECT id, name, codigo_postal , pais, id_pais, estado, id_estado, ciudad, id_ciudad, municipio, id_municipio, colonia, id_colonia  FROM dir_sepomex 
        WHERE deleted = 0 
        AND codigo_postal = '$cod_postal' ";
        
        for ($i = 0; $i < count($filtros); $i++) {
            switch ($filtros[$i]) {
                case "estado":
                    $query = $query. "AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                estado, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$data[$i]' ";
                    break;
                case "ciudad":
                    $query = $query. "AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                ciudad, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$data[$i]' ";
                    break;
                case "municipio":
                    $query = $query. "AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                municipio, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$data[$i]' ";
                    break;
                case "colonia":
                    $query = $query. "AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                colonia, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = '$data[$i]' ";
                    break;
            }
        }
        $query = $query. " order by id desc;";

        //$GLOBALS['log']->fatal('query',$query);
        $result = $GLOBALS['db']->query($query);
        //$GLOBALS['log']->fatal('result',$result);
        return $result;
    }

    public function obtenerDatosSepomex($row) {
        //$GLOBALS['log']->fatal('row',$row);
        $data = [
            "paises" => [],
            "municipios" => [],
            "estados" => [],
            "colonias" => [],
            "ciudades" => [],
            "ciudades_metadata" => [],
            "idCP" => "",
            "nameCP" => "",
            "indice" => "0"
        ];
    
        //while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            //$GLOBALS['log']->fatal('row',$row);
            $data['paises'][] = ["idPais" => "2", "namePais" => $row['pais']];
            $data['municipios'][] = ["idMunicipio" => $row['id_municipio'], "nameMunicipio" => $row['municipio']];
            $data['estados'][] = ["idEstado" => $row['id_estado'], "nameEstado" => $row['estado']];
            $data['colonias'][] = [
                "idColonia" => $row['id_colonia'],
                "nameColonia" => $row['colonia'],
                "idCodigoPostal" => $row['id_codigo_postal'],
                "idMunicipio" => $row['id_municipio']
            ];
            $data['ciudades'][] = ["idCiudad" => $row['id_ciudad'], "nameCiudad" => $row['ciudad']];
            $data['ciudades_metadata'][$row['id_ciudad']] = [
                "estado_id" => $row['id_estado'],
                "id" => $row['id_ciudad'],
                "name" => $row['ciudad'],
                "pais_id" => "2"
            ];
            $data['idCP'] = $row['id'];
            $data['nameCP'] = $row['codigo_postal'];
        //}
    
        return $data;
    }
    

    public function normalizeText($text) {
        return strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT', $text));
    }

    function normalize_text_py($text) {
        // Normaliza el texto eliminando acentos y convirtiéndolo a mayúsculas
        if (!$text) return "";
        $search = ['Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú'];
        $replace = ['A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u'];
        return strtoupper(str_replace($search, $replace, $text));
    }

    public function searchForId($id, $array, $busqueda) {
        // Convertir la búsqueda y el ID a mayúsculas y sin acentos
        $id_normalizado = mb_strtoupper($this->removeAccents($id));
    
        foreach ($array as $key => $val) {
            // Normalizar el valor del array antes de comparar
            $valor_normalizado = mb_strtoupper($this->removeAccents($val[$busqueda] ?? ''));
    
            if ($valor_normalizado === $id_normalizado) {
                return $key;
            }
        }
        return -1;
    }
    
    // Función auxiliar para eliminar acentos
    private function removeAccents($string) {
        return transliterator_transliterate('NFD; [:Nonspacing Mark:] Remove; NFC;', $string);
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

    public function insertSepomex( $cp,$id_pais, $pais,$id_estado, $estado, $id_municipio,$municipio ,$id_colonia,$colonia, $id_ciudad,$ciudad){
        global $current_user;
        $new_id_sep=Uuid::uuid1();
        $id_user=$current_user->id;
        $current_date=TimeDate::getInstance()->nowDb();
        $name=$pais ." ".$cp." ".$estado." ".$colonia;//labelPais CP Estado Colonia
        /*$qinsertRecordSepomex="INSERT INTO `dir_sepomex` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, 
        `pais`, `id_pais`, `codigo_postal`, `estado`, `id_estado`, `ciudad`, `id_ciudad`, `municipio`, `id_municipio`, `colonia`, `id_colonia`) VALUES 
        ('{$new_id_sep}', '{$name}', '{$current_date}', '{$current_date}', '{$id_user}', '{$id_user}', '0', 
        '{$pais}', '{$id_pais}', '{$cp}', '{$estado}', '{$id_estado}','{$ciudad}', '{$id_ciudad}', '{$municipio}', '{$id_municipio}', '{$colonia}', '{$id_colonia}');";
        $GLOBALS['db']->query($qinsertRecordSepomex);
        */
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

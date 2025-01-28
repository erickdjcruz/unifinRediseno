<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/9/2015
 * Time: 11:14 AM
 */

ini_set('error_reporting', E_ERROR);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);

require_once("custom/Levementum/DropdownValuesHelper.php");

// JSR Cambios para ip dinámica
require_once('config_override.php');

//$GLOBALS['bpm_url'] = "192.168.20.222:8080";
//$GLOBALS['esb_url'] = "192.168.20.222:8081";
//$GLOBALS['unifin_url'] = "200.52.66.204";
global $sugar_config;

$GLOBALS['bpm_url'] = $sugar_config['bpm_url'];
$GLOBALS['esb_url'] = $sugar_config['esb_url'];
$GLOBALS['unifin_url'] = $sugar_config['unifin_url'];

// JSR Cambios para ip dinámica

class UnifinAPI
{
    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to make a GET call to a webservice*/
    public function unifingetCall($host)
    {
        global $current_user;
        try {
            // Login
            $url = $host;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4000);
            $result = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :  $host");

            if ($http_status != 200) {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : $host unifinGetCall ERROR:  $http_status ");
            }
            // Get response as an object
            $response = json_decode($result, true);

            return $response;
        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinGetCall ERROR: " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to make a POST call to a webservice*/
    public function unifinPostCall($host, $fields)
    {
        global $current_user;
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

        try {
            // Login
            $url = $host;
            $fields_string = '';
            $fields_string = json_encode($fields);
            //print the json encode to the sugarlog
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :JSON ENCODED  $url - " . print_r($fields_string, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($fields_string))
            );
            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            if ($http_status != 200) {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " STATUS " . " <" . $current_user->user_name . "> : $http_status ");
            }
            // Get response as an object
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : result -- " . print_r($result, true));

            $response = json_decode($result, true);

            return $response;
        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinPostCall ERROR: " . $e->getMessage());
        }
    }

    public function unifinPutCall($host, $fields)
    {
        global $current_user;
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        try {
            $fields_string = '';
            $fields_string = json_encode($fields);
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :JSON ENCODED  $host - " . print_r($fields_string, true));
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($fields_string))
            );
            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            if ($http_status != 200) {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " STATUS " . " <" . $current_user->user_name . "> : $http_status ");
            }
            // Get response as an object
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : result -- " . print_r($result, true));

            $response = json_decode($result, true);

            return $response;
        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinPutCall ERROR: " . $e->getMessage());
        }
    }

    public function listaNegra($nombre, $segundoNombre = null, $apellidoPaterno, $apellidoMaterno = null, $esFisica = null, $razonsocial = null)
    {
        global $current_user;
        try {
            if ($esFisica != 'Persona Moral') {
                $esFisica = 'true';
                $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ValidaListaNegra?bEsFisica=$esFisica&sNombreUno=$nombre&sNombreDos=$segundoNombre&sApellidoP=$apellidoPaterno&sApellidoM=$apellidoMaterno";
            } else {
                $esFisica = 'false';
                $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ValidaListaNegra?bEsFisica=$esFisica&sNombreUno=$razonsocial&sNombreDos=&sApellidoP=&sApellidoM=";
            }

            $time_start = microtime(true);
            $listaNegra = $this->unifingetCall($host);
            $time_end = microtime(true);
            @$time = $time_end - $time_start;
            $coincidencias = array();
            $coincidenciasListaNegra = 0;
            $coincidenciasPEP = 0;
            //starts array for coincidencias. we only need PEP and lista negra from the response
            $coincidencias['PEP'] = 0;
            $coincidencias['listaNegra'] = 0;
            foreach ($listaNegra['UNI2_CTE_011_PEP_ListaNegraResult']['ListaNegra'] as $field => $value) {
                if ($value['ListaNegra'] == "S") {
                    $coincidencias['listaNegra'] = $coincidenciasListaNegra + 1;
                }
                if ($value['Pep'] == "S") {
                    $coincidencias['PEP'] = $coincidenciasPEP + 1;
                }
            }
            return $coincidencias;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to generate a folio number in sugar by consuming the ObtieneFolio Rest service*/
    public function generarFolios($tipoFolio, $bean = null)
    {
        global $current_user;
        try {
            $flagUpdateProspectoCliente = false;
            /*Este bloque de codigo es para cuando el ESB no responde*/
            switch ($tipoFolio) {
                case 1:
                    $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/consultaFolio?sTabla=ctCliente";
                    $flagUpdateProspectoCliente = true;
                    break;
                case 2:
                    $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/consultaFolio?sTabla=crSolicitudes";
                    break;
                case 3:
                    $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/consultaFolio?sTabla=ctProspecto";
                    break;
            }
            $folio = $this->unifingetCall($host);
            $folio = intval($folio['UNI2_UTL_001_traeFolioResult']);


            if ($flagUpdateProspectoCliente) {

                $fields = array(
                    "idCliente" => $folio,
                    "guid" => $bean->id
                );
                //$resp = $this->unifingetCall($GLOBALS['esb_url']."/crm/rest/updateProspectoACliente");
                $resp = $this->unifinPostCall($GLOBALS['esb_url'] . "/crm/rest/updateProspectoACliente", $fields);
            }
            /*Termina bloque*/

            //$host = "http://" . $GLOBALS['esb_url'] ."/rest/unics/obtieneFolio?tipoFolio=$tipoFolio";
            //$time_start = microtime(true);
            //$folio = $this->unifingetCall($host);
            //$time_end = microtime(true);
            //$time = $time_end - $time_start;
            return $folio;

        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            $this->setErrorLogFailRequest( $host, $bean->id, $host, json_encode($fields), $e->getMessage() );
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to call Rest service Iniciar Proceso*/
    public function liberacionLista($registro, $listaNegra, $listaPEP, $idCliente, $tipoPersona, $user_name, $persona)
    {
        try {
            $host = 'http://' . $GLOBALS['bpm_url'] . '/uni2/rest/bpm/iniciar-proceso';
            //CarlosZargoza
            //Cambios de parametros para iniciar el proceso de oficial de Cumplimiento

            //Si el idCliente viene en nulo, se convierte a 0
            if ($idCliente == null) {
                $idCliente = 0;
            }
            global $sugar_config, $current_user;
            $fields = array(
                'usuarioAutenticado' => $user_name,
                'nombreProceso' => 'idOficialCumplimiento',
                'guidPersona' => $registro,
                'nombrePersona' => $persona,
                'listaNegra' => $listaNegra,
                'listaNegativa' => 0,
                'listaPEP' => $listaPEP,
                'oficialCumplimientoAsignado' => $sugar_config['oficial'],
                'promotor' => $user_name,
                'idCliente' => $idCliente
            );

            $time_start = microtime(true);
            $liberacion = $this->unifinpostCall($host, $fields);
            global $db;
            $query = " UPDATE accounts_cstm SET id_process_c = {$liberacion['processInstanceId']} WHERE id_c = '{$registro}'";
            $queryResult = $db->query($query);

            $time_end = microtime(true);
            $time = $time_end - $time_start;

        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to call Rest service InsertaClienteCompleto */
    public function insertarClienteCompleto($objecto)
    {
        $GLOBALS['log']->fatal("llamada a API con el id cliente: " . $objecto->idcliente_c);
        if (intval($objecto->idcliente_c) > 0) {
            try {
                global $current_user;
                //methods to form the array of fields require to call the service. We need Direcciones,Telefonos,Correos, and related Contact Info
                $direcciones = $this->getDirecciones($objecto->id);
                $telefonos = $this->getTelefonos($objecto->id);
                $correos = $this->getCorreos($objecto->id);
                $personaRelacionada = $this->getRelacion($objecto->id);

                //class to convert string values from dropdowns to Integers since we need int values for the service
                $IntValue = new DropdownValuesHelper();
                $RegimenFiscal = $IntValue->getTipodepersonaInt($objecto->tipodepersona_c);
                $RegimenConyugal = $IntValue->getEstadoCivilInt($objecto->estadocivil_c);
                $TipoCliente = $IntValue->getTipoCliente($objecto->tipo_registro_cuenta_c, $objecto->estatus_c, $objecto->esproveedor_c, $objecto->tipo_relacion_c, $objecto->cedente_factor_c, $objecto->deudor_factor_c);
                $_ClntFechaNacimiento = $RegimenFiscal == 3 ? $objecto->fechaconstitutiva_c : $objecto->fechadenacimiento_c;
                /***CVV INICIO***/
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaClienteCompleto';
                $cleanValues = array();
                $fields = array(
                    "oClienteCompleto" => array(
                        "oPersona" => array(
                            "_IdCliente" => intval($objecto->idcliente_c),
                            "_ClntFechaAlta" => date("d-m-Y"),
                            "_IdRegimenFiscal" => $RegimenFiscal,
                            "_ClntRfc" => $objecto->rfc_c,
                            "_ClntFechaNacimiento" => $_ClntFechaNacimiento == "" ? null : $_ClntFechaNacimiento,
                            "_actividadEconomica" => $objecto->actividadeconomica_c,
                            "_sectorEconomico" => $objecto->sectoreconomico_c,
                            "_IdRegimenConyugal" => $RegimenConyugal,
                            "_ClntApellidoPaterno" => $objecto->apellidopaterno_c,
                            "_ClntApellidoMaterno" => $objecto->apellidomaterno_c,
                            "_ClntSegundoNombre" => $objecto->segundonombre_c,
                            "_ClntNombre" => $objecto->primernombre_c,
                            "_ClntSexo" => ($RegimenFiscal == 3 ? null : (($RegimenFiscal != 3 and empty($objecto->genero_c)) ? null : $objecto->genero_c)),
                            "_ClntCurp" => (empty($objecto->curp_c) ? "" : $objecto->curp_c),
                            "_ClntRazonSocial" => $objecto->razonsocial_c,
                            "_ClntIndicadorEstado" => $objecto->estatus_persona_c,
                            "_IdZonaGeografica" => intval($objecto->zonageografica_c),
                            "_usuarioDominio" => $current_user->user_name == "uni2crm" ? $objecto->assigned_user_name : $current_user->user_name,
                            "_IdPaisNacionalidad" => intval($objecto->pais_nacimiento_c) == 0 ? 2 : intval($objecto->pais_nacimiento_c),
                            "_ClntEstadoNacimiento" => intval($objecto->estado_nacimiento_c) == 0 ? 10 : intval($objecto->estado_nacimiento_c),
                            "_ClntPorcentajeIVA" => floatval($objecto->iva_c),
                            "_IdRegimenPatrimonial" => intval($objecto->regimenpatrimonial_c),
                            "_ClntTipoCliente" => intval($TipoCliente),
                            "_IdProfesion" => intval($objecto->profesion_c),
                            "_ClntIFEPasaporte" => $objecto->ifepasaporte_c,
                            "_ClntUltimaModificacion" => date("d-m-Y"),
                            "_IdNacionalidad" => intval($objecto->nacionalidad_c),
                            "_subSectorEconomico" => $objecto->subsectoreconomico_c,
                            /*"_ctrfComisionPactadaReferencia" => 45,
                            "_ctrfIdDistribuidorReferencia" => 20,
                            "_ctrfIdAgenciaReferencia" => 952,
                            "_ctrfIdVendedor" => 12,*/
                            "_promotorDominioLeasing" => $IntValue->getUserName($objecto->user_id_c),
                            "_promotorDominioFactoraje" => $IntValue->getUserName($objecto->user_id1_c),
                            "_promotorDominioCredit" => $IntValue->getUserName($objecto->user_id2_c),
                            "_guidCliente" => $objecto->id
                        ),
                        "oDireccion" => $direcciones,
                        "oComunicacion" => $telefonos,
                        "oCorreo" => $correos,
                        "oContacto" => array(),
                        "oContacto" => $personaRelacionada
                    )
                );

                $time_start = microtime(true);
                $cliente = $this->unifinpostCall($host, $fields);
                $time_end = microtime(true);
                $time = $time_end - $time_start;
                /***CVV INICIO***/
                if ($cliente['UNI2_CTE_030_InsertaClienteCompletoResult']['bResultado'] == true) {
                    //Actualizamos el registro a tipo Cliente
                    $tipo_registro = (($objecto->tipo_registro_cuenta_c == '1' || $objecto->tipo_registro_cuenta_c == '4' || $objecto->tipo_registro_cuenta_c == '5') ? $objecto->tipo_registro_cuenta_c : '3');
                    //$objecto->tipo_registro_cuenta_c = $tipo_registro;
                    $objecto->sincronizado_unics_c = '1';
                    global $db;
                    //$query = " UPDATE accounts_cstm SET tipo_registro_cuenta_c = '$tipo_registro', sincronizado_unics_c = '1' WHERE id_c = '{$objecto->id}'";
                    /*
                    * F. Javier G. Solar 13/07/2018
                     * se seleccionan los checks de Proveedor, Cedente
                        Factoraje o Deudor Factoraje se realiza el proceso de envió de cuenta al sistema UNICS, solo si no se ha
                        realizado.
                         Conservar los campos que sean obligatorios de acuerdo a la opción
                        seleccionada
                    */
                    $query = " UPDATE accounts_cstm SET sincronizado_unics_c = '1' WHERE id_c = '{$objecto->id}'";
                    $queryResult = $db->query($query);

                    //CVV se actualiza el campo sincronizado_unics de las direcciones del cliente
                    foreach ($direcciones as $key => $Direccion) {
                        $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Direccion " . print_r($Direccion, 1));
                        $query = " UPDATE dire_direccion_cstm SET sincronizado_unics_c = '1' WHERE id_c = '{$Direccion['_guidDireccion']}'";
                        $queryResult = $db->query($query);

                    }
                    //CVV se actualiza el campo sincronizado_unics de los telefonos del cliente
                    foreach ($telefonos as $key => $Telefono) {
                        $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Telefono " . print_r($Telefono, 1));
                        $query = " UPDATE tel_telefonos_cstm SET sincronizado_unics_c = '1' WHERE id_c = '{$Telefono['_guidComunicacion']}'";
                        $queryResult = $db->query($query);
                    }

                    //CVV se actualiza el campo sincronizado_unics de las personas contacto
                    foreach ($personaRelacionada as $key => $Contacto) {
                        $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Contacto " . print_r($Contacto, 1));
                        $query = " UPDATE accounts_cstm SET sincronizado_unics_c = '1' WHERE id_c = '{$Contacto['_guidCliente']}'";
                        $queryResult = $db->query($query);
                    }

                    //$this->usuarioProveedores($objecto);


                    //Valida y crea relaciones
                    $this->enviaRelaciones($objecto->id);
                }
                /***CVV FIN***/
            } catch (Exception $e) {
                error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());

                $this->setErrorLogFailRequest( "InsertaClienteCompleto", $objecto, $host, json_encode($fields), $e->getMessage() );
            }
        }
    }

    /*** ALI INICIO ***/
    public function InsertaPersona($objecto)
    {
        try {
            global $current_user;
            //class to convert string values from dropdowns to Integers since we need int values for the service
            $IntValue = new DropdownValuesHelper();
            $RegimenFiscal = $IntValue->getTipodepersonaInt($objecto->tipodepersona_c);
            $RegimenConyugal = $IntValue->getEstadoCivilInt($objecto->estadocivil_c);
            $TipoCliente = $IntValue->getTipoCliente($objecto->tipo_registro_cuenta_c, $objecto->estatus_c, $objecto->esproveedor_c, $objecto->tipo_relacion_c, $objecto->cedente_factor_c, $objecto->deudor_factor_c);
            $_ClntFechaNacimiento = $RegimenFiscal == 3 ? $objecto->fechaconstitutiva_c : $objecto->fechadenacimiento_c;
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaPersona";

            $cleanValues = array();

            if ($objecto->idcliente_c == '' || $objecto->idcliente_c == '0') {
                $numeroDeFolio = $this->generarFolios(1, $objecto);
                $objecto->idcliente_c = $numeroDeFolio;
            }
            if ($objecto->idcliente_c != '' && $objecto->idcliente_c != '0') {
                $fields = array(
                    "oPersona" => array(
                        "_IdCliente" => intval($objecto->idcliente_c),
                        "_ClntFechaAlta" => date("d-m-Y"),
                        "_IdRegimenFiscal" => $RegimenFiscal,
                        "_ClntRfc" => $objecto->rfc_c,
                        "_ClntFechaNacimiento" => $_ClntFechaNacimiento == "" ? null : $_ClntFechaNacimiento,
                        "_actividadEconomica" => $objecto->actividadeconomica_c,
                        "_sectorEconomico" => $objecto->sectoreconomico_c,
                        "_IdRegimenConyugal" => $RegimenConyugal,
                        "_ClntApellidoPaterno" => $objecto->apellidopaterno_c,
                        "_ClntApellidoMaterno" => $objecto->apellidomaterno_c,
                        "_ClntSegundoNombre" => $objecto->segundonombre_c,
                        "_ClntNombre" => $objecto->primernombre_c,
                        "_ClntSexo" => ($RegimenFiscal == 3 ? null : (($RegimenFiscal != 3 and empty($objecto->genero_c)) ? null : $objecto->genero_c)),
                        "_ClntCurp" => (empty($objecto->curp_c) ? "" : $objecto->curp_c),
                        "_ClntRazonSocial" => $objecto->razonsocial_c,
                        "_ClntIndicadorEstado" => $objecto->estatus_persona_c,
                        "_IdZonaGeografica" => intval($objecto->zonageografica_c),
                        "_usuarioDominio" => $current_user->user_name == "uni2crm" ? $objecto->assigned_user_name : $current_user->user_name,
                        "_IdPaisNacionalidad" => intval($objecto->pais_nacimiento_c) == 0 ? 2 : intval($objecto->pais_nacimiento_c),
                        "_ClntEstadoNacimiento" => intval($objecto->estado_nacimiento_c) == 0 ? 10 : intval($objecto->estado_nacimiento_c),
                        "_ClntPorcentajeIVA" => floatval($objecto->iva_c),
                        "_IdRegimenPatrimonial" => intval($objecto->regimenpatrimonial_c),
                        "_ClntTipoCliente" => intval($TipoCliente),
                        "_IdProfesion" => intval($objecto->profesion_c),
                        "_ClntIFEPasaporte" => $objecto->ifepasaporte_c,
                        "_ClntUltimaModificacion" => date("d-m-Y"),
                        "_IdNacionalidad" => intval($objecto->nacionalidad_c),
                        "_subSectorEconomico" => $objecto->subsectoreconomico_c,
                        /*"_ctrfComisionPactadaReferencia" => 45,
                        "_ctrfIdDistribuidorReferencia" => 20,
                        "_ctrfIdAgenciaReferencia" => 952,
                        "_ctrfIdVendedor" => 12,*/
                        "_promotorDominioLeasing" => $IntValue->getUserName($objecto->user_id_c),
                        "_promotorDominioFactoraje" => $IntValue->getUserName($objecto->user_id1_c),
                        "_promotorDominioCredit" => $IntValue->getUserName($objecto->user_id2_c),
                        "_guidCliente" => $objecto->id
                    )
                );

                $time_start = microtime(true);
                $cliente = $this->unifinpostCall($host, $fields);
                $time_end = microtime(true);
                $time = $time_end - $time_start;

                if ($cliente['UNI2_CTE_001_InsertaPersonaResult']['bResultado'] == true) {
                    //Actualizamos el registro a tipo Cliente
                    $tipo_registro = (($objecto->tipo_registro_cuenta_c == '4' || $objecto->tipo_registro_cuenta_c == '5') ? $objecto->tipo_registro_cuenta_c : '3');
                    /*
                        AF - 2018/08/14
                        Omite actualización de tipo de registro
                    */
                    //$objecto->tipo_registro_cuenta_c = $tipo_registro;
                    $objecto->sincronizado_unics_c = '1';
                    global $db;
                    $query = " UPDATE accounts_cstm SET idcliente_c = '{$objecto->idcliente_c}', /*tipo_registro_cuenta_c = '$tipo_registro', */sincronizado_unics_c = '1' WHERE id_c = '{$objecto->id}'";
                    $queryResult = $db->query($query);

                    //$this->usuarioProveedores($objecto);
                }
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }


    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to get all of the Direcciones module Information that is related to the Persona in question
     * this method is used to form the array for insertarClienteCompleto method*/
    public function getDirecciones($relatedId, $currentDireccion = null, $elimina = 0)
    {
        global $db, $current_user;
        $query = "
SELECT dire_direccion.id AS direccionId, accounts.id AS accountId, dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_pais_ida AS paisId, dire_direccion_dire_estado_c.id AS EstadoId
,dire_direccion_dire_municipio_c.dire_direccion_dire_municipiodire_municipio_ida AS MunicipioId, dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_ciudad_ida AS CiudadId
,dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_codigopostal_ida AS CodigoPostalId, dire_direccion.tipodedireccion, dire_direccion.calle, dire_direccion.numext
,dire_codigopostal.name AS codigoPostal, dire_ciudad.name AS Ciudad, dire_colonia.name AS Colonia, dire_direccion.inactivo, dire_direccion.numint, dire_direccion.secuencia, accounts_cstm.idcliente_c
,dire_pais.name AS Pais, dire_estado.name AS Estado, dire_direccion.indicador, dire_estado.id AS EstadoId, dire_pais.Id AS PaisId
FROM dire_direccion
LEFT JOIN accounts_dire_direccion_1_c ON accounts_dire_direccion_1_c.accounts_dire_direccion_1dire_direccion_idb = dire_direccion.id AND
accounts_dire_direccion_1_c.deleted = 0
LEFT JOIN dire_direccion_dire_pais_c ON dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_direccion_idb = dire_direccion.id AND dire_direccion_dire_pais_c.deleted = 0
LEFT JOIN dire_direccion_dire_estado_c ON dire_direccion_dire_estado_c.dire_direccion_dire_estadodire_direccion_idb = dire_direccion.id AND dire_direccion_dire_estado_c.deleted = 0
LEFT JOIN dire_direccion_dire_municipio_c ON dire_direccion_dire_municipio_c.dire_direccion_dire_municipiodire_direccion_idb = dire_direccion.id AND dire_direccion_dire_municipio_c.deleted = 0
LEFT JOIN dire_direccion_dire_ciudad_c ON dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_direccion_idb = dire_direccion.id AND dire_direccion_dire_ciudad_c.deleted = 0
LEFT JOIN dire_direccion_dire_codigopostal_c ON dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_direccion_idb = dire_direccion.id AND dire_direccion_dire_codigopostal_c.deleted = 0
LEFT JOIN dire_direccion_dire_colonia_c ON dire_direccion_dire_colonia_c.dire_direccion_dire_coloniadire_direccion_idb = dire_direccion.id AND dire_direccion_dire_colonia_c.deleted = 0
LEFT JOIN dire_codigopostal ON dire_codigopostal.id = dire_direccion_dire_codigopostal_c.dire_direccion_dire_codigopostaldire_codigopostal_ida AND dire_codigopostal.deleted = 0
LEFT JOIN dire_ciudad ON dire_ciudad.id = dire_direccion_dire_ciudad_c.dire_direccion_dire_ciudaddire_ciudad_ida AND dire_ciudad.deleted = 0
LEFT JOIN dire_colonia ON dire_colonia.id = dire_direccion_dire_colonia_c.dire_direccion_dire_coloniadire_colonia_ida AND dire_colonia.deleted = 0
LEFT JOIN dire_pais ON dire_pais.id = dire_direccion_dire_pais_c.dire_direccion_dire_paisdire_pais_ida AND dire_pais.deleted = 0
LEFT JOIN dire_estado ON dire_estado.id = dire_direccion_dire_estado_c.dire_direccion_dire_estadodire_estado_ida AND dire_estado.deleted = 0
LEFT JOIN accounts ON accounts.id = accounts_dire_direccion_1_c.accounts_dire_direccion_1accounts_ida AND accounts.deleted = 0
INNER JOIN accounts_cstm ON accounts_cstm.id_c = accounts.id
WHERE accounts.id = '{$relatedId}' AND accounts.deleted = 0";

        //this condition is for when we want only one direccion
        if ($currentDireccion != null) {
            $query .= "  AND dire_direccion.id = '{$currentDireccion}'";
        }

        $queryResult = $db->query($query);
        $direcciones = array();
        $IntValue = new DropdownValuesHelper();

        while ($row = $db->fetchByAssoc($queryResult)) {

            $estadoId = $IntValue->getEstadoId($row['EstadoId']);
            $municipioId = $IntValue->getMunicipioId($row['MunicipioId']);
            $ciudadId = $IntValue->getCiudadId($row['CiudadId']);
            $codigoPostalId = $IntValue->getCodigoPostalId($row['CodigoPostalId']);

            $estado = '';
            if ($row["inactivo"] == 0) {
                $estado = "A";
            } else {
                $estado = "I";
            }
            $tipoDireccion = (explode("^", $row['tipodedireccion']));
            foreach ($tipoDireccion as $index => $value) {
                if ($value == '') {
                    continue;
                }
                if ($value != '') {
                    $direccionId = $value;
                    break;
                }
            }

            //Form the Direcciones array with all of the info we gathered from the database and dropdowns
            /***CVV INCIO***/
            if ($elimina == 1) {
                $direcciones[] = array(
                    "_IdCliente" => intval($row['idcliente_c']),
                    "_IdDrccConsecutivo" => intval($row['secuencia']),
                    "_IdPais" => intval($row['PaisId']),
                    "_IdEstado" => intval($estadoId),
                    "_IdMunicipio" => intval($municipioId),
                    "_IdCiudad" => intval($ciudadId),
                    "_IdCodigoPostal" => intval($codigoPostalId),
                    "_IdTipoDireccion" => intval($direccionId),
                    "_DrccCalle" => $row['calle'],
                    "_DrccNumeroExterior" => $row['numext'],
                    "_DrccNumeroInterior" => $row['numint'],
                    "_DrccCodigoPostal" => $row['codigoPostal'],
                    "_DrccCiudad" => $row['Ciudad'],
                    "_DrccColonia" => $row['Colonia'],
                    "_DrccIndicadorEstado" => $estado,
                    "_DrccIndicadores" => intval($row['indicador']),
                    "_guidDireccion" => $row['direccionId'],
                    "_deletedCRM" => 1
                );
            } else {
                $direcciones[] = array(
                    "_IdCliente" => intval($row['idcliente_c']),
                    "_IdDrccConsecutivo" => intval($row['secuencia']),
                    "_IdPais" => intval($row['PaisId']),
                    "_IdEstado" => intval($estadoId),
                    "_IdMunicipio" => intval($municipioId),
                    "_IdCiudad" => intval($ciudadId),
                    "_IdCodigoPostal" => intval($codigoPostalId),
                    "_IdTipoDireccion" => intval($direccionId),
                    "_DrccCalle" => $row['calle'],
                    "_DrccNumeroExterior" => $row['numext'],
                    "_DrccNumeroInterior" => $row['numint'],
                    "_DrccCodigoPostal" => $row['codigoPostal'],
                    "_DrccCiudad" => $row['Ciudad'],
                    "_DrccColonia" => $row['Colonia'],
                    "_DrccIndicadorEstado" => $estado,
                    "_DrccIndicadores" => intval($row['indicador']),
                    "_guidDireccion" => $row['direccionId'],
                    "_deletedCRM" => 0
                );
            }
            /***CVV FIN**/
        }
        return $direcciones;
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to get all of the information needed from Telefonos to form the array needed in
     * insertarClienteCompleto method*/
    public function getTelefonos($relatedId, $currentTelefono = null)
    {
        try {
            global $db, $current_user;
            $query = <<<SQL
SELECT tel_telefonos.*, accounts.id AS accountId, accounts_cstm.idcliente_c FROM tel_telefonos
INNER JOIN accounts_tel_telefonos_1_c ON accounts_tel_telefonos_1_c.accounts_tel_telefonos_1tel_telefonos_idb = tel_telefonos.id AND accounts_tel_telefonos_1_c.deleted = 0
INNER JOIN accounts ON accounts.id = accounts_tel_telefonos_1_c.accounts_tel_telefonos_1accounts_ida AND accounts.deleted = 0
INNER JOIN accounts_cstm ON accounts_cstm.id_c = accounts.id
WHERE accounts.id = '{$relatedId}'
SQL;
            //this condition is for when we want only one telefono
            if ($currentTelefono != null) {
                $query .= "  AND tel_telefonos.id = '{$currentTelefono}'";
            }

            $queryResult = $db->query($query);
            $DrccConsecutivo = $this->getSecuenciaDireccionFiscal($relatedId);
            $tel = array();
            if (mysqli_num_rows($queryResult) == 0) {
                return $tel = array();
            } else {
                while ($row = $db->fetchByAssoc($queryResult)) {
                    $telStatus = '';
                    if ($row['estatus'] == 'Activo') {
                        $telStatus = 'A';
                    } else {
                        $telStatus = 'I';
                    }
                    /***CVV INCIO***/
                    $tel[] = array(
                        "_IdCliente" => intval($row['idcliente_c']),
                        "_IdDrccConsecutivo" => intval($DrccConsecutivo),
                        "_IdCmncConsecutivo" => intval($row['secuencia']),
                        "_IdTipoComunicacion" => intval($row['tipotelefono']),
                        "_CmncLada" => $row['pais'],
                        "_CmncDescripcion" => $row['telefono'],
                        "_CmncNumeroAdicional" => $row['extension'],
                        "_CmncIndicadorEstado" => $telStatus,
                        "_guidComunicacion" => $row['id']
                    );
                    /***CVV FIN***/
                }
                return $tel;
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :getTelefonos Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to get all of the information needed from the related contact to form the array needed in
     * insertarClienteCompleto method*/
    public function getContacto($contactId)
    {
        global $db;
        $query = <<<SQL
SELECT id_c, apellidopaterno_c, apellidomaterno_c, primernombre_c, idcliente_c
FROM accounts_cstm
WHERE id_c = '{$contactId}'
SQL;
        $queryResult = $db->query($query);
        $contactoInfo = array();
        while ($row = $db->fetchByAssoc($queryResult)) {
            /***CVV INICIO ***/
            $contactoInfo = array(
                "_IdCliente" => intval($row['idcliente_c']),
                "_ClntApellidoPaterno" => $row['apellidopaterno_c'],
                "_ClntApellidoMaterno" => $row['apellidomaterno_c'],
                "_ClntNombre" => $row['primernombre_c'],
                "_guidCliente" => $row['id_c']
            );
            /***CVV FIN***/
        }
        return $contactoInfo;
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to get the related contact and form all of the info needed in the insertarClienteCompleto method
     * for the related contact information. this method uses other methods to get the information needed for telefonos,related contact, and correos for the contact related*/
    public function getRelacion($relatedId)
    {
        global $db;
        $query = <<<SQL
SELECT account_id1_c FROM rel_relaciones_cstm
INNER JOIN rel_relaciones_accounts_c ON rel_relaciones_accounts_c.rel_relaciones_accountsrel_relaciones_idb = rel_relaciones_cstm.id_c AND rel_relaciones_accounts_c.deleted = 0
INNER JOIN accounts ON accounts.id = rel_relaciones_accounts_c.rel_relaciones_accountsaccounts_ida AND accounts.deleted = 0
INNER JOIN rel_relaciones ON rel_relaciones.id = rel_relaciones_cstm.id_c AND rel_relaciones.deleted = 0
WHERE accounts.id = '{$relatedId}' AND rel_relaciones.relaciones_activas LIKE '%Contacto%' AND account_id1_c <> 'null'
SQL;

        $queryResult = $db->query($query);
        if (mysqli_num_rows($queryResult) == 0) {
            return $contactos_internos = array();
        } else {
            $index = 0;
            while ($row = $db->fetchByAssoc($queryResult)) {
                $relacion = $row['account_id1_c'];
                //starts the array formation for the related contact info for the contacts found in query above
                $contactos_internos[$index]['oPersona'] = $this->getContacto($relacion);
                //get information for each telefono's contact found. we use the getTelefonos method to form the array
                $contactos_internos[$index]['oComunicacion'] = $this->getTelefonos($relacion);
                //get information for each correo's contact found. we use the getCorreos method to form the array
                $contactos_internos[$index]['oCorreo'] = $this->getCorreos($relacion);
                $contactos_internos[$index]['_guidRelacion'] = $relacion;
                $index++;
            }
            return $contactos_internos;
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to get the e-mails of Personas related to the Persona in question. This method is
     * use in the insertarClienteCompleto method to form the array needed to make the REST call*/
    /***CVV INICIO***/
    public function getCorreos($relatedId, $currentMail = null)
    {
        try {
            global $db, $current_user;
            $query = <<<SQL
SELECT accounts_cstm.id_c AS accountId, email_addresses.email_address, email_addresses.id AS EmailId, accounts_cstm.idcliente_c, invalid_email, opt_out, email_addr_bean_rel.date_created
FROM accounts_cstm
INNER JOIN email_addr_bean_rel ON email_addr_bean_rel.bean_id = accounts_cstm.id_c AND email_addr_bean_rel.deleted = 0
INNER JOIN email_addresses ON email_addresses.id = email_addr_bean_rel.email_address_id AND email_addresses.deleted = 0
WHERE accounts_cstm.id_c = '{$relatedId}'
SQL;
            //this condition is for when we want only one Mail
            if ($currentMail != null) {
                $query .= "  AND email_addresses.id = '{$currentMail}'";
            }
            $query .= " ORDER BY email_addr_bean_rel.date_created";

            /***CVV FIN***/
            $queryResult = $db->query($query);
            $DrccConsecutivo = $this->getSecuenciaDireccionFiscal($relatedId);
            $secuencia = 0;
            $correos = array();
            if (mysqli_num_rows($queryResult) == 0) {
                return $correos = array();
            } else {
                while ($row = $db->fetchByAssoc($queryResult)) {
                    $secuencia++;
                    if ($row['invalid_email'] == 0) {
                        $IndicadorEstado = "A";
                    } elseif ($row['invalid_email'] == 1) {
                        $IndicadorEstado = "I";
                    }

                    if ($row['opt_out'] == 0) {
                        //Email Personal
                        $TipoComunicacion = "5";
                    }
                    if ($row['opt_out'] == 1) {
                        //Email Laboral
                        $TipoComunicacion = "6";
                    }
                    /***CVV INCIO***/
                    $correos[] = array(
                        "_IdCliente" => intval($row['idcliente_c']),
                        "_IdDrccConsecutivo" => intval($DrccConsecutivo),
                        "_IdCmncConsecutivo" => intval($secuencia),
                        "_IdTipoComunicacion" => intval($TipoComunicacion),
                        "_CmncDescripcion" => $row['email_address'],
                        "_CmncIndicadorEstado" => $IndicadorEstado,
                        "_guidComunicacion" => $row['EmailId']
                    );
                    /***CVV FIN***/
                }
                return $correos;
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : getCorreos Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function listaNegraCoincidencias($nombre, $segundoNombre = null, $apellidoPaterno, $apellidoMaterno = null, $esFisica = null)
    {
        global $current_user;
        try {
            if ($esFisica != 'Persona Moral') {
                $esFisica = 'true';
            } else {
                $esFisica = 'false';
            }
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ValidaListaNegra?bEsFisica=$esFisica&sNombreUno=$nombre&sNombreDos=$segundoNombre&sApellidoP=$apellidoPaterno&sApellidoM=$apellidoMaterno";

            $time_start = microtime(true);
            $coincidencias = $this->unifingetCall($host);
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            return $coincidencias;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function listaNegraDetalle($idPersona)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/DetallePersonaPLD?IdPersona={$idPersona}";

            $time_start = microtime(true);
            $detalle = $this->unifingetCall($host);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return $detalle;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function creaRelacion($object)
    {
        try {
            /*** ALI INICIO ***/
            global $db;
            global $current_user;
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/CreaRelacion";
            $host1 = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/consultaFolio?sTabla=ctRelacionesCliente";

            $listTipoRelacion = explode(",", $object->relaciones_activas);
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " : Lista de relaciones " . $listTipoRelacion);
            $IntValue = new DropdownValuesHelper();

            //Obtienes los id_cliente de la personas que se estan asociando
            $query = <<<SQL
			SELECT cliente.idcliente_c id_cliente, relacionado.idcliente_c id_relacionado
FROM rel_relaciones_accounts_c rel
  inner join accounts_cstm cliente on rel.rel_relaciones_accountsaccounts_ida = cliente.id_c
  inner join rel_relaciones_cstm rel2 on rel2.id_c=rel.rel_relaciones_accountsrel_relaciones_idb
  inner join accounts_cstm relacionado on rel2.account_id1_c = relacionado.id_c
where rel.deleted = 0
      and rel.rel_relaciones_accountsrel_relaciones_idb = '{$object->id}';
SQL;
            $queryResult = $db->query($query);
            $row = $db->fetchByAssoc($queryResult);
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " : Consulta en UnifinApI " . $query);
            foreach ($listTipoRelacion as $Relacion) {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " : Relacion en foreach " . $Relacion);
                $IdTipoRelacion = $IntValue->getIdTipoRelacion(str_replace('^', '', $Relacion));
                if ($IdTipoRelacion != 0) {

                    //Obtiene el folio de la relación para UNICS
                    $folio = $this->unifingetCall($host1);

                    $fields = array(
                        "oRelacionCliente" => array(
                            "_relacionCliente" => array(
                                "_gruidRelacionesCliente" => $object->id,
                                "idCliente" => intval($row['id_cliente']),
                                "idClienteRelacionado" => intval($row['id_relacionado']),
                                "idPuesto" => intval($object->puesto),
                                "idRelacion" => null,
                                "idRelacionCliente" => intval($folio['UNI2_UTL_001_traeFolioResult']),
                                "idTipoContacto" => ($IdTipoRelacion == 2048 ? intval($IntValue->getIdTipoContacto($object->tipodecontacto)) : null),
                                "idTipoRelacion" => $IdTipoRelacion,
                                "rlclIndicardorEstado" => "A",
                                "rlclUltimaModificacion" => $object->date_modified,
                                "usuarioDominioInserto" => $current_user->user_name,
                                "usuarioDominoActualizo" => $current_user->user_name
                            ),
                            "_valoreAccionista" => array(
                                "_idRelacionCliente" => ($IdTipoRelacion == 8192 ? intval($folio['UNI2_UTL_001_traeFolioResult']) : null),
                                "_rcacMiembro" => ($IdTipoRelacion == 8192 ? intval($object->miembrodecomite) : null),
                                "_rcacMontoAccionista" => ($IdTipoRelacion == 8192 ? floatval($object->montodeparticipacion) : null),
                                "_rcacParticipacion" => ($IdTipoRelacion == 8192 ? $object->porcentaje_participacion_c : null)
                            )
                        )
                    );
                    $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : campos relacion " . print_r($fields, true));
                    $relacion = $this->unifinpostCall($host, $fields);
                }
            }

            /***  ALI FIN ***/
            return $relacion['UNI2_CTE_007_CreaRelacionResult']['bResultado'];
        } catch (Exception $e) {
            return 0;
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /***CVV INICIO***/
    //
    public function ActualizaRelacion($object)
    {
        try {
            global $db;
            global $current_user;
            //$host = "http://200.52.66.204/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaRelacion";
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaRelacion";
            //Obtienes los id_cliente de la personas que se estan asociando
            $query = <<<SQL
                SELECT cliente.idcliente_c id_cliente, relacionado.idcliente_c id_relacionado, rel.deleted deleted
FROM rel_relaciones_accounts_c rel
  inner join accounts_cstm cliente on rel.rel_relaciones_accountsaccounts_ida = cliente.id_c
  inner join rel_relaciones_cstm rel2 on rel2.id_c=rel.rel_relaciones_accountsrel_relaciones_idb
  inner join accounts_cstm relacionado on rel2.account_id1_c = relacionado.id_c
  and rel.rel_relaciones_accountsrel_relaciones_idb = '{$object->id}';
SQL;
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " : Consulta en UnifinApI " . $query);
            $queryResult = $db->query($query);
            $row = $db->fetchByAssoc($queryResult);
            $IntValue = new DropdownValuesHelper();
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> Fila de resultado" . print_r($row, true));
            $relacionesActivas = str_replace('^', '', $object->relaciones_activas);
            if ($row['deleted'] == 1) $relacionesActivas = '';
            $ContieneContacto = strpos($relacionesActivas, 'Contacto') >= 0 ? true : false;
            $ContieneAccionista = strpos($relacionesActivas, 'Accionista') >= 0 ? true : false;
            //Las relaciones de Beneficiario no se sincronizan con UNICS
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> Contenido de relaciones Activas:" . print_r(strpos($relacionesActivas, 'Beneficiario'), true));
            if (strpos($relacionesActivas, 'Beneficiario')) {
                $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> Se identificó relacion Beneficiario en:" . print_r($relacionesActivas, true));
                if (strpos($relacionesActivas, 'Beneficiario,')) {
                    $relacionesActivas = str_replace('Beneficiario,', '', $relacionesActivas);
                } elseif (strpos($relacionesActivas, ',Beneficiario')) {
                    $relacionesActivas = str_replace(',Beneficiario', '', $relacionesActivas);
                } else {
                    $relacionesActivas = str_replace('Beneficiario', '', $relacionesActivas);
                }
            } else {
                $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> NO Se identificó relacion Beneficiario en:" . print_r($relacionesActivas, true));
            }
            //Quita propietario Real && Proveedor de los recursos L,FF,CA,CS
            $GLOBALS['log']->fatal("relacionesActivas: " . $relacionesActivas);
            $relacionesActivas = str_replace(',Propietario Real', '', $relacionesActivas);
            $relacionesActivas = str_replace('Propietario Real,', '', $relacionesActivas);
            $relacionesActivas = str_replace(',Proveedor de Recursos L', '', $relacionesActivas);
            $relacionesActivas = str_replace(',Proveedor de Recursos F', '', $relacionesActivas);
            $relacionesActivas = str_replace(',Proveedor de Recursos CA', '', $relacionesActivas);
            $relacionesActivas = str_replace(',Proveedor de Recursos CS', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos L,', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos F,', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos CA,', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos CS,', '', $relacionesActivas);
            $relacionesActivas = str_replace('Propietario Real', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos L', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos F', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos CA', '', $relacionesActivas);
            $relacionesActivas = str_replace('Proveedor de Recursos CS', '', $relacionesActivas);


            $fields = array(
                "oActualizaRelacion" => array(
                    "IdCliente" => intval($row['id_cliente']),
                    "IdClienteRelacionado" => intval($row['id_relacionado']),
                    "Relaciones" => $relacionesActivas,
                    "TipoContacto" => $ContieneContacto ? $IntValue->getIdTipoContacto($object->tipodecontacto) : null,
                    "UsuarioDominio" => $current_user->user_name,
                    "GuidRelacion" => $object->id,
                    "ValoresAccionista" => $ContieneAccionista ? array(
                        "_rcacParticipacion" => ($object->porcentaje_participacion_c != "") ? $object->porcentaje_participacion_c : "0",
                        "_rcacMiembro" => $object->miembrodecomite ? "S" : "N",
                        "_rcacMontoAccionista" => $object->montodeparticipacion
                    ) : null
                )
            );
            /*** ALI FIN ***/
            $relacion = $this->unifinpostCall($host, $fields);

            return $relacion['UNI2_CTE_008_ActualizaRelacionResult']['bResultado'];
        } catch (Exception $e) {
            return 0;
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function insertaDireccion($object)
    {
        global $db, $current_user;
        $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaDireccion";
        $fields = $this->getDirecciones($object->accounts_dire_direccion_1accounts_ida, $object->id);
        //getDireciones return an array of arrays but for this service we only need one array
        foreach ($fields as $key => $value) {
            //***CVV INICIO***/
            $direcciones = array("oDireccion" => array($value));
            $time_start = microtime(true);
            $direccion = $this->unifinpostCall($host, $direcciones);
            //***CVV FIN***/

            /*CARLOSZARAGOZA
             * Actualiza el campo de sincronizado con unics.
             * fecha: 24 08 2015
            */
            try {
                if ($direccion['UNI2_CTE_003_InsertaDireccionResult']['bResultado'] == true) {
                    $fieldUnicsSincronize = <<<SQL
                        update dire_direccion_cstm set sincronizado_unics_c = '1'  where id_c = '{$value['_guidDireccion']}';
SQL;
                    $queryResult = $db->query($fieldUnicsSincronize);
                }
            } catch (Exception $e) {
                error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());

            }
            /*CARLOSZARAGOZA*/
            $time_end = microtime(true);
            $time = $time_end - $time_start;
        }
    }

    public function actualizaDireccion($object)
    {
        global $db, $current_user;
        $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaDireccion";
        $fields = $this->getDirecciones($object->accounts_dire_direccion_1accounts_ida, $object->id);
        //getDireciones return an array of arrays but for this service we only need one array
        foreach ($fields as $key => $value) {
            $direcciones = array("oDireccion" => array($value));
            $time_start = microtime(true);
            $GLOBALS['log']->fatal($direcciones);
            $direccion = $this->unifinpostCall($host, $direcciones);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $GLOBALS['log']->fatal($direccion);
            /*CARLOSZARAGOZA
            * Actualiza el campo de sincronizado con unics.
            * fecha: 24 08 2015
            */
            try {
                if ($direccion['UNI2_CTE_004_ActualizaDireccionResult']['bResultado'] == true) {
                    $fieldUnicsSincronize = <<<SQL
                        update dire_direccion_cstm set sincronizado_unics_c = '1' where id_c = '{$value['_guidDireccion']}';
SQL;
                    $queryResult = $db->query($fieldUnicsSincronize);
                }
            } catch (Exception $e) {
                error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            }
            /*CARLOSZARAGOZA*/
        }
    }

    public function eliminaDireccion($object)
    {
        global $db, $current_user;
        $elimina = 1;
        $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaDireccion";
        $fields = $this->getDirecciones($object->accounts_dire_direccion_1accounts_ida, $object->id, $elimina);
        //getDireciones return an array of arrays but for this service we only need one array
        foreach ($fields as $key => $value) {
            $direcciones = array("oDireccion" => array($value));
            $time_start = microtime(true);
            $direccion = $this->unifinpostCall($host, $direcciones);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $GLOBALS['log']->fatal($direccion);
            /*CARLOSZARAGOZA
            * Actualiza el campo de sincronizado con unics.
            * fecha: 24 08 2015
            */
            try {
                if ($direccion['UNI2_CTE_004_ActualizaDireccionResult']['bResultado'] == true) {
                    $fieldUnicsSincronize = <<<SQL
                        update dire_direccion_cstm set sincronizado_unics_c = '1' where id_c = '{$value['_guidDireccion']}';
SQL;
                    $queryResult = $db->query($fieldUnicsSincronize);
                }
            } catch (Exception $e) {
                error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            }
            /*CARLOSZARAGOZA*/
        }
    }

    public function actualizaPersonaUNICS($object)
    {
        try {
            //class to convert string values from dropdowns to Integers since we need int values for the service
            global $current_user;
            $IntValue = new DropdownValuesHelper();
            $RegimenFiscal = $IntValue->getTipodepersonaInt($object->tipodepersona_c);
            $RegimenConyugal = $IntValue->getEstadoCivilInt($object->estadocivil_c);
            $TipoCliente = $IntValue->getTipoCliente($object->tipo_registro_cuenta_c, $object->estatus_c, $object->esproveedor_c, $object->tipo_relacion_c, $object->cedente_factor_c, $object->deudor_factor_c);
            $_ClntFechaNacimiento = $RegimenFiscal == 3 ? $object->fechaconstitutiva_c : $object->fechadenacimiento_c;

            $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaPersona';
            $fields = array(
                "oPersona" => array(
                    "_IdCliente" => intval($object->idcliente_c),
                    "_IdRegimenFiscal" => $RegimenFiscal,
                    "_ClntRfc" => $object->rfc_c,
                    "_ClntFechaNacimiento" => $_ClntFechaNacimiento == "" ? null : $_ClntFechaNacimiento,
                    "_actividadEconomica" => $object->actividadeconomica_c,
                    "_sectorEconomico" => $object->sectoreconomico_c,
                    "_IdRegimenConyugal" => $RegimenConyugal,
                    "_ClntApellidoPaterno" => $object->apellidopaterno_c,
                    "_ClntApellidoMaterno" => $object->apellidomaterno_c,
                    "_ClntSegundoNombre" => $object->segundonombre_c,
                    "_ClntNombre" => $object->primernombre_c,
                    "_ClntSexo" => ($RegimenFiscal == 3 ? null : (($RegimenFiscal != 3 and empty($object->genero_c)) ? null : $object->genero_c)),
                    "_ClntCurp" => $object->curp_c,
                    "_ClntRazonSocial" => $object->razonsocial_c,
                    "_ClntIndicadorEstado" => $object->estatus_persona_c,
                    "_IdZonaGeografica" => intval($object->zonageografica_c),
                    "_IdPaisNacionalidad" => intval($object->pais_nacimiento_c) == 0 ? 2 : intval($object->pais_nacimiento_c),
                    "_ClntEstadoNacimiento" => intval($object->estado_nacimiento_c) == 0 ? 10 : intval($object->estado_nacimiento_c),
                    "_ClntPorcentajeIVA" => floatval($object->iva_c),
                    "_IdRegimenPatrimonial" => intval($object->regimenpatrimonial_c),
                    "_ClntTipoCliente" => intval($TipoCliente),
                    "_IdProfesion" => intval($object->profesion_c),
                    "_ClntIFEPasaporte" => $object->ifepasaporte_c,
                    "_ClntUltimaModificacion" => date("d-m-Y"),
                    "_IdNacionalidad" => intval($object->nacionalidad_c),
                    "_subSectorEconomico" => $object->subsectoreconomico_c,
                    /*"_ctrfComisionPactadaReferencia" => 45,
                    "_ctrfIdDistribuidorReferencia" => 20,
                    "_ctrfIdAgenciaReferencia" => 952,
                    "_ctrfIdVendedor" => 12,*/
                    "_usuarioDominio" => $current_user->user_name,
                    "_promotorDominioLeasing" => $IntValue->getUserName($object->user_id_c),
                    "_promotorDominioFactoraje" => $IntValue->getUserName($object->user_id1_c),
                    "_promotorDominioCredit" => $IntValue->getUserName($object->user_id2_c),
                    "_guidCliente" => $object->id
                )
            );
            //***CVV FIN***/
            $Actualizarpersona = $this->unifinpostCall($host, $fields);
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());

            $this->setErrorLogFailRequest( "Account", $object, $host, json_encode($fields), $e->getMessage() );
        }

        //$this->usuarioProveedores($object);
    }

    public function insertaComunicación($object, $estado)
    {
        global $db, $current_user;
        try {
            if ($estado == 'insertando') {
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaComunicacion';
            } elseif ($estado == 'actualizando') {
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaComunicacion';
            }

            $fields = $this->getTelefonos($object->accounts_tel_telefonos_1accounts_ida, $object->id);
            foreach ($fields as $key => $value) {
                $Telefonos = array("oComunicacion" => array($value));
                $comunicacion = $this->unifinpostCall($host, $Telefonos);
                //todo hay que agregar la actualizacion por registro en la sincronizacion del resultado
                try {
                    $estatus_unics = '0';
                    if ($estado == 'insertando') {
                        $estatus_unics = $comunicacion['UNI2_CTE_005_InsertaComunicacionResult']['bResultado'] ? '1' : '0';
                    } elseif ($estado == 'actualizando') {
                        $estatus_unics = $comunicacion['UNI2_CTE_006_ActualizaComunicacionResult']['bResultado'] ? '1' : '0';
                    }
                    $fieldUnicsSincronize = <<<SQL
                        update tel_telefonos_cstm set sincronizado_unics_c = '$estatus_unics'  where id_c = '{$value['_guidComunicacion']}';
SQL;

                    $queryResult = $db->query($fieldUnicsSincronize);
                } catch (Exception $e) {
                    error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                    $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    //***CVV INICIO***/
    public function insertaCorreos($object, $estado)
    {
        global $current_user;
        try {
            if ($estado == 'insertando') {
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaComunicacion';
            } elseif ($estado == 'actualizando') {
                $host = 'http:/' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaComunicacion';
            }
            $fields = $this->getCorreos($object->bean_id, $object->email_address_id);
            foreach ($fields as $key => $value) {
                $Correos = array("oComunicacion" => array($value));
                $comunicacion = $this->unifinpostCall($host, $Correos);
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    //***CVV FIN***/

    public function insertaPLD($object, $estado)
    {
        try {
            global $current_user;
            if ($estado == 'insertando') {
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/InsertaPLD';
            } elseif ($estado == 'actualizando') {
                $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaPLD';
            }
            $fields = array(
                "clientePLD" => array(
                    "CuestionarioPLD" => array(
                        "_idCliente" => intval($object->idcliente_c),
                        "_ctPLDPoliticamenteExpuesto" => intval($object->ctpldpoliticamenteexpuesto_c) == 0 ? "N" : "S",
                        "_ctPLDRelacionadoArticulo" => intval($object->ctpldrelacionadoarticulo_c) == 0 ? "N" : "S",
                        "_ctPLDidProveedorRecursosClienteActua" => intval($object->ctpldidproveedorrecursosclie_c),
                        "_ctPLDidProveedorRecursosSon" => intval($object->ctpldidproveedorrecursosson_c),
                        "_ctPLDNoSerieFIEL" => intval($object->ctpldnoseriefiel_c) == 0 ? "NA" : $object->ctpldnoseriefiel_c,
                        "_ctPLDOrigenRecursoCliente" => intval($object->ctpldorigenrecursocliente_c),
                        "_ctPLDFuncionesPublicas" => intval($object->ctpldfuncionespublicas_c) == 0 ? false : true,
                        "_ctPLDFuncionesPublicasCargo" => $object->ctpldfuncionespublicascargo_c == null ? "" : $object->ctpldfuncionespublicascargo_c,
                        "_ctPLDConyuge" => intval($object->ctpldconyuge_c) == 0 ? false : true,
                        "_ctPLDConyugeCargo" => $object->ctpldconyugecargo_c == null ? "" : $object->ctpldconyugecargo_c,
                        "_ctPLDAccionistas" => intval($object->ctpldaccionistas_c) == 0 ? false : true,
                        "_ctPLDAccionistasCargo" => $object->ctpldaccionistascargo_c == null ? "" : $object->ctpldaccionistascargo_c,
                        "_ctPLDAccionistasConyuge" => intval($object->ctpldaccionistasconyuge_c) == 0 ? false : true,
                        "_ctPLDAccionistasConyugeCargo" => $object->ctpldaccionistasconyugecargo_c == null ? "" : $object->ctpldaccionistasconyugecargo_c
                    ),
                    "Transaccionalidad" => array(
                        "Fecha" => date("d-m-Y"),
                        "MontoAproximado" => 1,
                        "IMCuenta" => (isset($object->imcuenta_c) && $object->imcuenta_c) ? true : false,
                        "IMCheque" => (isset($object->imcheque_c) && $object->imcheque_c) ? true : false,
                        "IMEfectivo" => (isset($object->imefectivo_c) && $object->imefectivo_c) ? true : false,
                        "IMOtro" => (isset($object->imotro_c) && $object->imotro_c) ? true : false,
                        "UsuarioDominio" => $current_user->user_name,
                        "idCliente" => intval($object->idcliente_c),
                        "Importe" => 1,
                        "IMOtroDesc" => $object->imotrodesc_c == null ? "" : $object->imotrodesc_c,
                        "PagoAnticipado" => intval($object->pagoanticipado_c) == 0 ? false : true
                    )
                )
            );
            $pld = $this->unifinpostCall($host, $fields);
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function ClienteconContratosActivos($id)
    {
        global $current_user;
        try {
            $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/VerificaClienteTieneContrato?IdCliente=' . $id;
            $contratosActivos = $this->unifingetCall($host);
            return $contratosActivos;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function obtenerTareaAssignadas($userName)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['bpm_url'] . "/uni2/rest/bpm/obtener-tareas-asignadas?usuario=$userName&incluirVariables=true";
            $tareaAssignadas = $this->unifingetCall($host);
            return $tareaAssignadas;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function completarTarea($usuarioAutenticado, $idTarea, $idCliente, $valorListaNegra, $valorPEP, $txtOficialCommentario)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['bpm_url'] . "/uni2/rest/bpm/completar-tarea";
            $fields = array(
                "usuarioAutenticado" => $usuarioAutenticado,
                "idTarea" => $idTarea,
                "listaNegra" => $valorListaNegra,
                "listaPEP" => $valorPEP,
                "listaNegativa" => 0,
                "descripcionListas" => $txtOficialCommentario,
            );
            $tarea = $this->unifinpostCall($host, $fields);
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function CalculaCURP($primernombre, $apellidoP, $apellidoM, $fechaNacimiento, $genero, $pais, $estado)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/CreaCurpPersona";
            $fields = array(
                "CurpRequest" => array(
                    "nombre" => $primernombre,
                    "paterno" => $apellidoP,
                    "materno" => $apellidoM,
                    "fechaNacimiento" => $fechaNacimiento,
                    "sexo" => $genero,
                    "idPais" => $pais,
                    "idEstado" => $estado
                ),
            );
            $curp = $this->unifinpostCall($host, $fields);
            return $curp;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function validaRFCPersonaFisica($fechaNacimiento, $primernombre, $apellidoP, $apellidoM, $genero, $pais, $estado)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/CreaRfcPersonaFisica";
            $fields = array(
                "RfcRequest" => array(
                    "nombre" => $primernombre,
                    "paterno" => $apellidoP,
                    "materno" => $apellidoM,
                    "fechaNacimiento" => $fechaNacimiento,
                    "sexo" => null,
                    "idPais" => null,
                    "idEstado" => null
                ),
            );
            $rfc = $this->unifinpostCall($host, $fields);
            return $rfc;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            $this->setErrorLogFailRequest( "CreaRfcPersonaFisica", '', $host, json_encode($fields), $e->getMessage() );
        }
    }

    public function validaRFCPersonaMoral($fechaConstitutiva, $razonsocial)
    {
        global $current_user;
        try {

            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/CreaRfcPersonaMoral";
            $fields = array(
                "RfcRequest" => array(
                    "razonSocial" => $razonsocial,
                    "fechaConstitutiva" => $fechaConstitutiva,
                ),
            );
            $rfc = $this->unifinpostCall($host, $fields);
            return $rfc;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            $this->setErrorLogFailRequest( "CreaRfcPersonaMoral", '', $host, json_encode($fields), $e->getMessage() );
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 7/1/2015 Description: Obtener numero de secuencia de la direccion fiscal relacionada*/
    public function getSecuenciaDireccionFiscal($relatedId)
    {
        try {
            global $db, $current_user;
            //error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Ingreso a getSecuenciaDireccionFiscal ");
            $query = <<<SQL
SELECT secuencia, indicador FROM dire_direccion
LEFT JOIN accounts_dire_direccion_1_c ON accounts_dire_direccion_1_c.accounts_dire_direccion_1dire_direccion_idb = dire_direccion.id AND accounts_dire_direccion_1_c.deleted = 0
WHERE accounts_dire_direccion_1_c.accounts_dire_direccion_1accounts_ida = '{$relatedId}' AND dire_direccion.deleted = 0 AND indicador in (2,3,7) LIMIT 1
SQL;

            $queryResult = $db->query($query);
            $FiscalSecuencia = '';
            if (mysqli_num_rows($queryResult) == 0) {
                //error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Sin dirección fiscal registrada! ");
                $FiscalSecuencia = 1;
            } else {
                while ($row = $db->fetchByAssoc($queryResult)) {
                    $FiscalSecuencia = $row['secuencia'];
                    //error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Error: " . print_r($row,true));
                }
            }
            return $FiscalSecuencia;
        } catch (Exception $e) {
            //error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/11/2015 Description: Method to call Rest service Iniciar Proceso*/
    public function obtenSolicitudCredito($opportunidad)
    {
        global $current_user;
        global $sugar_config;
        try {
            //Variables para factoraje, listas:
            $tipo_tasa_list[1] = "FIJA";
            $tipo_tasa_list[2] = "VARIABLE";

            $instrumento_list[1] = "TIIE EN CURVA";
            $instrumento_list[4] = "LIBOR";
            $instrumento_list[5] = "TIIE FLAT";
            $instrumento_list[6] = "TIIE";
            $instrumento_list[7] = "CERO";
            $instrumento_list[8] = "CETES";
            /*
            * @author Carlos Zaragoza
            * @date 17-11-2015
            * @brief Coloca multiactivo para montarlo en la firma para el BPM
            *
             * */
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : es_multiactivo_c " . $opportunidad['es_multiactivo_c']);
            if ($opportunidad['es_multiactivo_c'] == 1) {
                $lista = str_replace('^', '', $opportunidad['multiactivo_c']);
                $lista = explode(',', $lista);
                $idActivoPrincipal = "";
                $indexActivoPrincipal = "";
                $nombreActivoPrincipal = "";
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : lista " . print_r($lista, true));
                //$GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : lista 0 " . $lista[0]);

                /*
                    Fecha: 2017-10-12
                    Ajuste: Habilitación de listas para obtención de variables

                */
                //Obtener valor de 1er multiactivo_c
                $valueList = $lista[0];
                global $app_list_strings;

                //Obtener Key,Label de Lista idactivo_list
                $idactivo_list = array();
                $idactivo_list = $app_list_strings['idactivo_list'];

                //1.- Recuperar $indexActivoPrincipal
                $indexActivoPrincipal = array_search($valueList, $idactivo_list);

                //2.- Recuperar $idActivoPrincipal
                $idActivoPrincipal = $app_list_strings['idActivoPrincipal_list'][$indexActivoPrincipal];

                //3.- Recuperar $nombreActivoPrincipal
                $nombreActivoPrincipal = $app_list_strings['nombreActivoPrincipal_list'][$indexActivoPrincipal];

                //Valida print log
                $GLOBALS['log']->fatal("Valor seleccionado: " . $valueList . " | indexActivoPrincipal: " . $indexActivoPrincipal . " | idActivoPrincipal: " . $idActivoPrincipal . " | nombreActivoPrincipal: " . $nombreActivoPrincipal);

                /*
                switch ($lista[0]) {
                    case "AUTOS":
                        $idActivoPrincipal = 97;
                        $indexActivoPrincipal = "000100030001";
                        $nombreActivoPrincipal = "TRANSPORTE TERRESTRE AUTOS";
                        break;
                    case "CAMIONES":
                        $idActivoPrincipal = 819;
                        $indexActivoPrincipal = "000100030002";
                        $nombreActivoPrincipal = "TRANSPORTE TERRESTRE CAMIONES";
                        break;
                    case "BARCOS":
                        $idActivoPrincipal = 34;
                        $indexActivoPrincipal = "000100020001";
                        $nombreActivoPrincipal = "TRANSPORTE MARITIMO BARCO";
                        break;
                    case "OTROS":
                        $idActivoPrincipal = 36282; //6999;
                        $indexActivoPrincipal = "000200070107";
                        $nombreActivoPrincipal = "OTROS";
                        break;
                    case "MAQUINARIA":
                        $idActivoPrincipal = 1304; //1357;
                        $indexActivoPrincipal = "0002";
                        $nombreActivoPrincipal = "MAQUINARIA";
                        break;
                    case "EQUIPO":
                        $idActivoPrincipal = 2287;
                        $indexActivoPrincipal = "0003";
                        $nombreActivoPrincipal = "EQUIPO";
                        break;
                    case "EQUIPO DE TRANSPORTE":
                        $idActivoPrincipal = 1;
                        $indexActivoPrincipal = "0001";
                        $nombreActivoPrincipal = "TRANSPORTE";
                        break;
                    case "MOTOS":
                        $idActivoPrincipal = 985;
                        $indexActivoPrincipal = "000100030005";
                        $nombreActivoPrincipal = "TRANSPORTE TERRESTRE MOTOCICLETAS";
                        break;
                    case "AVIONES":
                        $idActivoPrincipal = 3;
                        $indexActivoPrincipal = "000100010001";
                        $nombreActivoPrincipal = "TRANSPORTE AEREO AVION";
                        break;
                    case "EQUIPO DE COMPUTO":
                        $idActivoPrincipal = 2658;
                        $indexActivoPrincipal = "00030006";
                        $nombreActivoPrincipal = "EQUIPO COMPUTO";
                        break;
                }
*/
                // Se asigna el primer item de la lista como activo principal para UNI2
                $opportunidad['id_activo_c'] = $idActivoPrincipal;
                $opportunidad['index_activo_c'] = $indexActivoPrincipal;

                /*if (empty($opportunidad->multiactivo_c)) {
                    $indexActivoPrincipal = null;
                }*/

                array_shift($lista);
                //$GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : lista sin 1 " . print_r($lista, true));

                $multiactivos = array(
                    "idActivoPrincipal" => $idActivoPrincipal,
                    "indexActivoPrincipal" => $indexActivoPrincipal,
                    "nombreActivoPrincipal" => $nombreActivoPrincipal,
                    "multiactivos" => $lista
                );
                $multiactivos = json_encode($multiactivos);
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : multiactivos " . $multiactivos);
            }

            $opportunidad['riesgo'] = $opportunidad['riesgo'] == "Bajo" ? "MN" : "MY";

            if (trim($opportunidad['tipo_persona']) == "Persona Fisica") {
                $opportunidad['tipo_persona'] = "PF";
            }
            if (trim($opportunidad['tipo_persona']) == "Persona Moral") {
                $opportunidad['tipo_persona'] = "PM";
            }
            if (trim($opportunidad['tipo_persona']) == "Persona Fisica con Actividad Empresarial") {
                $opportunidad['tipo_persona'] = "PFAE";
            }

            //CVV - 01/03/2016 - Para CA cambia el regimen fiscal de la operacion dependiendo del plan financiero
            if ($opportunidad['tipo_producto_c'] == 3 && $opportunidad['tipo_persona'] != "PM") {
                if ($opportunidad['plan_financiero_c'] == 1 || $opportunidad['plan_financiero_c'] == 3 || $opportunidad['plan_financiero_c'] == 4
                    || $opportunidad['plan_financiero_c'] == 6 || $opportunidad['plan_financiero_c'] == 7) {
                    $opportunidad['tipo_persona'] = "PF";
                }
                if ($opportunidad['plan_financiero_c'] == 2 || $opportunidad['plan_financiero_c'] == 5 || $opportunidad['plan_financiero_c'] == 8) {
                    $opportunidad['tipo_persona'] = "PFAE";
                }
            }

            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Regimen fiscal asignado: " . $opportunidad['tipo_persona']);
            $opportunidad['enviar_por_correo_c'] = $opportunidad['enviar_por_correo_c'] == 1 ? true : false;
            $opportunidad['correo'] = $opportunidad['correo'] == null ? "" : $opportunidad['correo'];

            switch ($opportunidad['tipo_producto_c']) {
                case 1:
                    $opportunidad['tipo_producto_c'] = "LEASING";
                    break;
                case 4:
                    $opportunidad['tipo_producto_c'] = "FACTORAJE";
                    break;
                case 3:
                    $opportunidad['tipo_producto_c'] = "CREDITO AUTOMOTRIZ";
                    break;
                case 2:
                    $opportunidad['tipo_producto_c'] = "CREDITO SIMPLE";
                    break;
                case 5:
                    $opportunidad['tipo_producto_c'] = "LINEA CREDITO SIMPLE";
                    break;
                case 7:
                    $opportunidad['tipo_producto_c'] = "SOS";
                    break;
            }

            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : user_name " . $opportunidad['user_name']);
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : usuario_bo_c " . $opportunidad['usuario_bo_c']);

            $backoffice = false;
            if (strlen($opportunidad['usuario_bo_c']) == 0 || $opportunidad['usuario_bo_c'] == '^^') {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Es para un usuario");
                if ($opportunidad['user_name'] != "") {
                    $usuarioProceso = $opportunidad['user_name'] == $current_user->user_name ? $current_user->user_name : $opportunidad['user_name'];
                } else {
                    $usuarioProceso = $current_user->user_name;
                }
            } else {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Es para un backoffice");
                $backoffice = true;
            }

            $host = 'http://' . $GLOBALS['bpm_url'] . '/uni2/rest/bpm/iniciar-proceso';

            $monto = $opportunidad['monto_c'];//($opportunidad['monto_gpo_emp_c'] != 0) ? $opportunidad['monto_gpo_emp_c'] : $opportunidad['monto_c'];
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : TIPO_PRODUCTO " . $opportunidad['tipo_producto_c']);
            $productoFinancieroId = ($opportunidad['producto_financiero_c'] == 0 || $opportunidad['producto_financiero_c'] == 41 || $opportunidad['producto_financiero_c'] == 42) ? "" : $opportunidad['producto_financiero_c'];
            //CVV - 29/03/2016 - Se crea el arreglo con los campos que aplican para todos los productos
            $fields = array(
                "usuarioAutenticado" => $current_user->user_name,
                "nombreProceso" => "solicitudDeCredito",
                "idSolicitud" => 0 + $opportunidad['idsolicitud_c'],
                "guidPersona" => $opportunidad['account_id'],
                "guidOperacionCRM" => $opportunidad['opportunity_id'],
                "idCliente" => 0 + $opportunidad['idcliente_c'],
                "nombreCliente" => $opportunidad['nombre_cliente'],
                "correoCliente" => $opportunidad['correo'],
                "riesgoPersona" => $opportunidad['riesgo'],
                "tipoPersona" => $opportunidad['tipo_persona'],
                "nombreProducto" => $opportunidad['tipo_producto_c'],
                "tipoOperacion" => $opportunidad['tipo_de_operacion_c'],
                "monto" => 0 + $monto,
                "montoPropuesta" => $opportunidad['monto_c'],
                "montoAOperar" => 0 + $opportunidad['amount'],
                "listaNegra" => 0 + $opportunidad['lista_negra_c'],
                "listaPEP" => 0 + $opportunidad['pep_c'],
                "promotor" => $usuarioProceso,
                "plazo" => 0 + $opportunidad['plazo_c'],
                "requiereEnviarCorreo" => $opportunidad['enviar_por_correo_c'],
                "comision" => $opportunidad['porcentaje_ca_c'],
                "pagoInicialCotizacion" => 0,
                "pagoMensual" => 0 + $opportunidad['ca_pago_mensual_c'],
                "idProductoFinanciero" => $productoFinancieroId

            );

            //Si la Solicitud es de Credito SOS recalcula el valor del campo Riesgo a "Mayor".
            //          if ($opportunidad['tipo_producto_c']=="SOS"){  # PREGUNTAR SI SE CAMBIA POR PRODUCTO FINANCIERO
            if (($opportunidad['tipo_producto_c'] == "CREDITO SIMPLE" && $opportunidad['producto_financiero_c'] == 40) || $opportunidad['negocio_c'] == '11') {
                //Setea valores constantes para la invocación de IniciaProceso
                //$GLOBALS['log']->fatal("Setea valores de la solicitud SOS");
                $fields['riesgoPersona'] = "MY";
                $fields['tasa'] = 0;
                $fields['vrc'] = 0;
                $fields['vri'] = 0;
                $fields['rentaInicial'] = 1;
                $fields['comision'] = 0;
            }

            //CVV - 29/03/2016 -  Se agregan los items que aplican para Leasing y CA
            if ($opportunidad['tipo_producto_c'] == "LEASING" || $opportunidad['tipo_producto_c'] == "CREDITO AUTOMOTRIZ") {
                $fields['idActivo'] = 0 + $opportunidad['id_activo_c'];
                $fields['indexActivo'] = $opportunidad['index_activo_c'];
                $fields['cadenaMultiActivos'] = $multiactivos;
                $fields['idCotizacion'] = $opportunidad['idcot_bpm_c'];
                $fields['tasa'] = 0 + $opportunidad['ca_tasa_c'];
                //$fields['rentaInicial'] = $opportunidad['porcentaje_renta_inicial_c'];
                $fields['vrc'] = $opportunidad['vrc_c'];
                $fields['vri'] = $opportunidad['vri_c'];
                $fields['depositoGarantiaCotizacion'] = $opportunidad['deposito_garantia_c'];

                if (empty($opportunidad['idcot_bpm_c']) || ($opportunidad['idcot_bpm_c'] == "NULL")) {
                    unset($fields['idCotizacion']);
                }

                //En Leasing se calcula el riesgo neto
                if ($opportunidad['tipo_producto_c'] == "LEASING") {
                    $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Se asigna RI de operacion no de CF " . print_r($opportunidad['porciento_ri_c'], 1));
                    $fields['rentaInicial'] = $opportunidad['porciento_ri_c'];
                    $fields['monto'] = $opportunidad['monto_c'] * (1 - ($opportunidad['porciento_ri_c'] / 100));
                }

                if ($opportunidad['tipo_producto_c'] == "CREDITO AUTOMOTRIZ") {
                    $fields['rentaInicial'] = $opportunidad['porcentaje_renta_inicial_c'];
                    //agregamos los 3 parametros para el cotizador
                    $fields['engancheAutomovil'] = 0 + $opportunidad['ca_importe_enganche_c']; //LEASING : RENTA INICIA Y PARA CA. : ENGANCHE  Importe enganche Renta inicial
                    //$fields['pagoMensual'] = 0+$opportunidad['ca_pago_mensual_c'];
                    $fields['valorAutomovil'] = 0 + $opportunidad['ca_valor_auto_iva_c'];
                    // CVV Se eliminan algunos campos que no aplican para CA
                    unset($fields['vrc']);
                    unset($fields['vri']);
                    unset($fields['depositoGarantiaCotizacion']);
                    //unset($fields['rentaInicial']);// quitamos condiciones que on aplican para CA
                    unset($fields['pagoInicialCotizacion']);
                }
            }

            //CVV - 29/03/2016 -  Se agregan los items que aplican para Factoraje
            if ($opportunidad['tipo_producto_c'] == "FACTORAJE") {
                switch ($opportunidad['f_tipo_factoraje_c']) {
                    case 1:
                        $opportunidad['f_tipo_factoraje_c'] = "COBRANZA_DELEGADA_CON_RECURSOS";
                        break;
                    case 2:
                        $opportunidad['f_tipo_factoraje_c'] = "COBRANZA_DIRECTA_CON_RECURSOS";
                        break;
                    case 3:
                        $opportunidad['f_tipo_factoraje_c'] = "COBRANZA_DIRECTA_SIN_RECURSOS";
                        break;
                    case 4:
                        $opportunidad['f_tipo_factoraje_c'] = "PROVEEDORES";
                        break;
                }

                $fields['aforo'] = 0 + $opportunidad['f_aforo_c'];
                $fields['documentosADescontar'] = 0 + $opportunidad['f_documento_descontar_c'];
                $fields['comentariosFactoraje'] = $opportunidad['f_comentarios_generales_c'] == null ? "" : $opportunidad['f_comentarios_generales_c'];
                $fields['tipoFactoraje'] = $opportunidad['f_tipo_factoraje_c'];
                $fields['tasa'] = 0 + $opportunidad['puntos_sobre_tasa_c'];
                $fields['instrumentoOrdinario'] = $instrumento_list[$opportunidad['instrumento_c']];
                $fields['puntosSobreTasa'] = $opportunidad['puntos_sobre_tasa_c'];
                $fields['puntosTasaMoratorio'] = $opportunidad['puntos_tasa_moratorio_c'];
                $fields['tipoTasaOrdinario'] = $tipo_tasa_list[$opportunidad['tipo_tasa_ordinario_c']];
                $fields['tipoTasaMoratorio'] = $tipo_tasa_list[$opportunidad['tipo_tasa_moratorio_c']];
                $fields['instrumentoMoratorio'] = $instrumento_list[$opportunidad['instrumento_moratorio_c']];
                $fields['factorMoratorio'] = $opportunidad['factor_moratorio_c'];
                $fields['carteraDescontar'] = $opportunidad['cartera_descontar_c'] == null ? "" : $opportunidad['cartera_descontar_c'];
                $fields['tasaFijaOrdinario'] = 0 + $opportunidad['tasa_fija_ordinario_c'];
                $fields['tasaFijaMoratorio'] = 0 + $opportunidad['tasa_fija_moratorio_c'];
                $fields['rentaInicial'] = "0";
                $fields['depositoGarantiaCotizacion'] = "0";
                $fields['vrc'] = "0";
                $fields['vri'] = "0";
            }

            if ($opportunidad['producto_financiero_c'] == '43') {
                //$fields['idCotizacion'] = $opportunidad['idcot_bpm_c'];
                $fields['depositoGarantiaCotizacion'] = $opportunidad['deposito_garantia_c'];
                $lista = str_replace('^', '', $opportunidad['multiactivo_c']);
                $lista = explode(',', $lista);
                $multiactivos = array(
                    "idActivoPrincipal" => "97",
                    "indexActivoPrincipal" => "000100030001",
                    "nombreActivoPrincipal" => "TRANSPORTE TERRESTRE AUTOS",
                    "multiactivos" => $lista
                );
                $multiactivos = json_encode($multiactivos);
                $fields['idActivo'] = 97;
                $fields['indexActivo'] = '000100030001';
                $fields['cadenaMultiActivos'] = $multiactivos;
                $fields['tasa'] = 10;
                $fields['vrc'] = 5;
                $fields['vri'] = 10;
                $fields['rentaInicial'] = 1;
                $fields['plazo'] = 1;
                $fields['comision'] = 1;
                $fields['monto'] = $opportunidad['monto_c'] * (1 - ($opportunidad['porciento_ri_c'] / 100));
            }

            if( $opportunidad['tipo_producto_c'] == "LEASING" && $opportunidad['negocio_c'] == '3')
            {
                $fields['arrendamientoEstructurado']= true;
            }else{
              $fields['arrendamientoEstructurado']= false;
            }

            //CVV - 29/03/2016 - Si el proceso es para un BO se elimina el item de promotor y se agrega el grupo de asignación
            if ($backoffice) {
                unset($fields['promotor']);
                $fields['grupoAsignacion'] = str_replace('^', '', $opportunidad['usuario_bo_c']);
            }

            //CVV - 29/03/2016 - En caso de ser una solicitud de ratificación/incremento
            if ($opportunidad['tipo_de_operacion_c'] == "RATIFICACION_INCREMENTO") {
                $fields['idLineaRelacionada'] = $opportunidad['id_linea_credito_c'];
                $fields['montoIncremento'] = $opportunidad['monto_ratificacion_increment_c'];
                /* PEndiente por funcionalidad uni2
                $fields['montoPropuesta'] = $opportunidad['monto_gpo_emp_c'];
                $fields['montoAOperar'] = $opportunidad['monto_gpo_emp_c'];*/
            }

            //Se valida si el Administrador de Cartera esta activo en el config y a nivel de solicitud/usuario
            if ($sugar_config['service_admin_cartera'] == true){

                if ($opportunidad['admin_cartera_c'] == 1) {

                    $fields['operacionEspecial'] = true;
                    $fields['montoRiesgoEmpresarial'] = $opportunidad['monto_gpo_emp_c'];
                    $fields['tipoSolicitud'] = $opportunidad['tipo_sol_admin_cartera_c'];
                    $fields['diasVencido'] = $opportunidad['cartera_dias_vencido_c'];
                    $fields['rentaInicial'] = empty($fields['rentaInicial']) ? 0 : $fields['rentaInicial'];

                } else {
                    $fields['operacionEspecial'] = false;
                    $fields['montoRiesgoEmpresarial'] = 0;
                    $fields['tipoSolicitud'] = 0;
                    $fields['diasVencido'] = 0;
                }
            }

            $time_start = microtime(true);
            if (($opportunidad['estatus_c'] != 'K' && $opportunidad['tipo_producto_c'] == 1 && $opportunidad['vobo_dir_c'] == true) || ($opportunidad['estatus_c'] != 'K' && $opportunidad['tipo_producto_c'] != 1)) {
                $resultado = $this->unifinpostCall($host, $fields);
            }
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return $resultado;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function getActivoSubActivo($id = null)
    {
        global $current_user;
        try {
            if (!empty($id)) {
                $host = "http://" . $GLOBALS['bpm_url'] . "/uni2/rest/activos/consultaHijos?idPadre={$id}&isCotizable=false";
            } else {
                $host = "http://" . $GLOBALS['bpm_url'] . "/uni2/rest/activos/consultaHijos?idPadre=&isCotizable=false";
            }

            $time_start = microtime(true);
            $result = $this->unifingetCall($host);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return $result;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function cancelaOppBpm($idSolicitud, $usuarioAutenticado)
    {
        global $current_user;
        try {
            //$host = "http://104.130.165.92:8080/uni2/rest/bpm/cancelar-solicitud-credito";
            $host = "http://" . $GLOBALS['bpm_url'] . "/uni2/rest/bpm/cancelar-solicitud-credito";
            $fields = array(
                "idSolicitud" => $idSolicitud
            , "usuarioAutenticado" => $usuarioAutenticado
            );
            $resultado = $this->unifinpostCall($host, $fields);
            return $resultado;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function correosDeCliente($fields)
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/ActualizaCorreos";
            $resultado = $this->unifinpostCall($host, $fields);
            return $resultado;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());

            $this->setErrorLogFailRequest( "ActualizaCorreos", '', $host, json_encode($fields), $e->getMessage() );
        }
    }

    public function generarBacklogFolio()
    {
        global $current_user;
        try {
            $host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsUtilerias/WsRest/Uni2UtlServices.svc/Uni2/consultaFolio?sTabla=crmBacklog";

            $folio = $this->unifingetCall($host);
            $folio = intval($folio['UNI2_UTL_001_traeFolioResult']);
            //$time_end = microtime(true);
            //$time = $time_end - $time_start;
            return $folio;

        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /** BEGIN CUSTOMIZATION: mhenaro@unifin.com.mx 01.04.2016 Description: Method to call Rest service Actualiza Solicitud */
    public function actualizaSolicitudCredito($objeto)
    {
        try {
            global $db, $current_user;
            $host = 'http://' . $GLOBALS['unifin_url'] . '/Uni2WsCr/WsRest/Uni2CrService.svc/Uni2/ActualizaSolicitud';

            // CVV Obtiene los datos del clientes
            $query = <<<SQL
                                   SELECT acstm.idcliente_c, acstm.tipodepersona_c as tipo_persona
                                   FROM accounts_opportunities aop
                INNER JOIN accounts_cstm acstm ON acstm.id_c = aop.account_id
                                   WHERE aop.opportunity_id = '{$objeto->id}' AND aop.deleted = 0
SQL;
            $queryResult = $db->query($query);
            while ($row = $db->fetchByAssoc($queryResult)) {
                if (trim($row['tipo_persona']) == "Persona Fisica") {
                    $regimen = 1;
                }
                if (trim($row['tipo_persona']) == "Persona Moral") {
                    $regimen = 3;
                }
                if (trim($row['tipo_persona']) == "Persona Fisica con Actividad Empresarial") {
                    $regimen = 2;
                }

                $idliente = intval($row['idcliente_c']);
            }

            if ($objeto->tipo_producto_c == 3 && $objeto->tipo_persona != "PM") {
                switch ($objeto->plan_financiero_c) {
                    case 1:
                    case 3:
                    case 4:
                    case 6:
                    case 7:
                        $regimen = 1;
                        break;
                    case 2:
                    case 5:
                    case 8:
                        $regimen = 2;
                        break;
                }
            }

            $tipo_factoraje = 0;
            switch ($objeto->f_tipo_factoraje_c) {
                case "COBRANZA_DELEGADA_CON_RECURSOS":
                    $tipo_factoraje = 1;
                    break;
                case "COBRANZA_DIRECTA_CON_RECURSOS":
                    $tipo_factoraje = 2;
                    break;
                case "COBRANZA_DIRECTA_SIN_RECURSOS":
                    $tipo_factoraje = 3;
                    break;
                case "PROVEEDORES":
                    $tipo_factoraje = 4;
                    break;
            }

            $activo_previo = array();
            $Condiciones = array();
            foreach ($objeto->condiciones_financieras as $Condicion) {

                switch ($Condicion['idactivo']) {
                    case '000100030001': // AUTOS
                        $idUnicsActivo = 50000;
                        break;
                    case '0001': // TRANSPORTE
                        $idUnicsActivo = 20546;
                        break;
                    case '000100030002': // CAMIONES
                        $idUnicsActivo = 55479;
                        break;
                    case '000100020001': // BARCOS
                        $idUnicsActivo = 59677;
                        break;
                    case '0002000701070061': // OTROS
                        $idUnicsActivo = 19793;
                        break;
                    case '0003': // PRODUCCION  (EQUIPO)
                        $idUnicsActivo = 19736;
                        break;
                    case '000100030005': // MOTOS
                        $idUnicsActivo = 65085;
                        break;
                    case '000100010001': // AVIONES
                        $idUnicsActivo = 19738;
                        break;
                    case '00030006': // EQUIPO DE COMPUTO
                        $idUnicsActivo = 14731;
                        break;
                    default:
                        $idUnicsActivo = 50000;
                        break;
                }

                $activo_previo[$Condicion['idactivo']] += 1;
                $condicion['consecutivo'] = $activo_previo[$Condicion['idactivo']];

                $plazos = explode("_", $Condicion['plazo']);

                $cond = array(
                    "IdNodo" => $idUnicsActivo,
                    "CnfnConsecutivo" => intval($activo_previo[$Condicion['idactivo']]), //$Condicion['consecutivo'],
                    "CnfnPlazoMin" => intval($plazos[0]),//intval($Condicion['plazo_minimo']),
                    "CnfnPlazoMax" => intval($plazos[1]),//intval($Condicion['plazo_maximo']),
                    "CnfnTasaMin" => $Condicion['tasa_minima'],
                    "CnfnTasaMax" => $Condicion['tasa_maxima'],
                    "CnfnVRCMin" => $Condicion['vrc_minimo'],
                    "CnfnVRCMax" => $Condicion['vrc_maximo'],
                    "CnfnVRIMin" => $Condicion['vri_minimo'],
                    "CnfnVRIMax" => $Condicion['vri_maximo'],
                    "CnfnDeposito" => $Condicion['deposito_en_garantia'] ? $Condicion['deposito_en_garantia'] : 0,
                    "CnfnComisionMin" => $Condicion['comision_minima'],
                    "CnfnComisionMax" => $Condicion['comision_maxima'],
                    "CnfnRentaInicialMin" => $Condicion['renta_inicial_minima'],
                    "CnfnRentaInicialMax" => $Condicion['renta_inicial_maxima'],
                    "CnfnParticular" => $Condicion['uso_particular'] ? $Condicion['uso_particular'] : 0,
                    "CnfnEmpresarial" => $Condicion['uso_empresarial'] ? $Condicion['uso_empresarial'] : 0,
                    "CnfnNuevo" => $Condicion['activo_nuevo'] ? $Condicion['activo_nuevo'] : 0,
                    "CnfnUsado" => $Condicion['activo_usado'] ? $Condicion['activo_usado'] : 0,
                    "CnfnGarantias" => 0,
                    "CnfnGarantiasDescripcion" => "",
                    "CnfnCliente" => 0,
                    "CnfnContado" => 0,
                    "CnfnFinanciado" => 0,
                    "CnfnDescripcion" => "",
                    "CnfnPorcentajeAforo" => 0.0,
                    "IdInstrumentoOrdinario" => 1,
                    "IdTipoTasaOrdinario" => 1,
                    "CnfnTasaFijaOrdinario" => 0.0,
                    "CnfnPuntosSobreTasaOrdinaria" => 0.0,
                    "CnfnFactorTasaOrdinarios" => 0.0,
                    "IdInstrumentoMoratorio" => 1,
                    "IdTipoTasaMoratorio" => 1,
                    "CnfnTasaFijaMoratorio" => 0.0,
                    "CnfnFactorInteresMoratorios" => 0.0,
                    "CnfnPuntosSobreTasaMoratoria" => 0.0,
                    "CnfnPorcentajeComision" => 0.0,
                    "CnfnCalculoSobre" => 1,
                    "CnfnTipoComision" => 1
                );
                array_push($Condiciones, $cond);

                //$GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : CondicionFinanciera " . print_r($cond, true));
            }

            //$GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Arreglo completo de condiciones: " . print_r($Condiciones, true));

            $IntValue = new DropdownValuesHelper();

            $fields = array(
                "solicitudC" => array(
                    "Solicitud" => array(
                        "IdSolicitud" => intval($objeto->idsolicitud_c),
                        "IdCliente" => $idliente,
                        "SlctComentarios" => $objeto->f_comentarios_generales_c,
                        "usuarioDominio" => $current_user->user_name,
                        "PromotorDominio" => $IntValue->getUserName($objeto->assigned_user_id),
                        "SlctPagoMensual" => floatval($objeto->ca_pago_mensual_c),
                        "SlctImporteCredito" => floatval($objeto->monto_c),
                        "IdProductoFinanciero" => intval($objeto->tipo_producto_c),//$producto,
                        "SlctTipoFactoraje" => $tipo_factoraje,
                        "IdRegimenFiscal" => $regimen,
                        "SlctTipoCartera" => $objeto->cartera_descontar_c,
                        "SlctDocumentos" => intval($objeto->f_documento_descontar_c),
                        "SlctTIIE" => 0.0,
                        "SlctPuntos" => 0.0,
                        "SlctTipoComision" => 0,
                        "SlctTipoPropuesta" => $objeto->tipo_de_operacion_c == "RATIFICACION_INCREMENTO" ? 2 : 1,
                        "IdLineaCredito" => intval($objeto->id_linea_credito_c)
                    ),
                    "Condiciones" => $Condiciones
                )
            );

            if ($fields['solicitudC']['Solicitud']['SlctTipoPropuesta'] == 1) {
                unset($fields['solicitudC']['Solicitud']['IdLineaCredito']);
            }

            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : MHE -- JSON Actualiza Solicitud " . print_r($fields, true));
            $time_start = microtime(true);
            $this->unifinpostCall($host, $fields);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            $this->setErrorLogFailRequest( "ActualizaSolicitud", '', $host, json_encode($fields), $e->getMessage() );

        }

    }

    public function usuarioProveedores($account)
    {
        IF (($account->tipo_registro_cuenta_c == '5' || $account->esproveedor_c) && (empty($account->alta_proveedor_c) ? 0 : $account->alta_proveedor_c) == 0) {
            global $app_list_strings, $db, $current_user;
            $host = 'http://' . $GLOBALS['esb_url'] . '/uni2/rest/registraProveedorUni2';

            $tipoProveedor = 'BIENES';
            $paisConstitucion = '';
            $estadoConstitucion = '';

            $list = $app_list_strings['tipo_proveedor_list'];
            if (isset($list)) {
                $tipo_proveedor = str_replace('^', '', $account->tipo_proveedor_c);
                $tipos = explode(',', $tipo_proveedor);
                foreach ($tipos as $tipo) {
                    $tipoProveedor = ($list[$tipo] == '' ? 'BIENES' : $list[$tipo]);
                    if ($tipoProveedor == 'BIENES') {
                        break;
                    }
                }
            }

            $list = $app_list_strings['paises_list'];
            if (isset($list)) {
                $paisConstitucion = $list[$account->pais_nacimiento_c];
            }

            $list = $app_list_strings['estados_list'];
            if (isset($list)) {
                $estadoConstitucion = $list[$account->estado_nacimiento_c];
            }
            if (($timestamp = strtotime($account->tipodepersona_c == 'Persona Moral' ? $account->fechaconstitutiva_c : $account->fechadenacimiento_c)) === false) {
                $timestamp = strtotime("now");
            }

            $fields = array(
                "rfcProveedor" => $account->rfc_c,
                "guid" => $account->id,
                "email" => $account->emailAddress->getPrimaryAddress($account),
                "primerNombreRazonSocial" => $account->tipodepersona_c == 'Persona Moral' ? $account->razonsocial_c : $account->primernombre_c . ' ' . $account->apellidopaterno_c . ' ' . $account->apellidomaterno_c,
                "anioNacimiento" => intval(date('Y', $timestamp)),
                //"mesNacimiento" => intval(date('n', $timestamp)),
                //"diaNacimiento" => intval(date('j', $timestamp)),
                "tipoProveedor" => $tipoProveedor,
                "tipoPersona" => $account->tipodepersona_c,
                "paisConstitucion" => ucfirst(strtolower($paisConstitucion)),
                "estadoConstitucion" => ucfirst(strtolower($estadoConstitucion))
            );

            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <CVV LOG> : JSON para usuarios de proveedores" . print_r($fields, 1));

            try {
                $proveedor = $this->unifinpostCall($host, $fields);
                if (strpos($proveedor, 'exitosamente')) {
                    $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <CVV LOG> : Respuesta de servicio para usuarios de proveedores" . print_r($proveedor, 1));
                    $query = " UPDATE accounts_cstm SET alta_proveedor_c = '1' WHERE id_c = '{$account->id}'";
                    $queryResult = $db->query($query);
                }
            } catch (Exception $e) {
                error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            }
        }
    }

    /*
        AF- 2018-10-19
        Habilita funcionalidad para envíar relaciones no creadas previamente, para cuentas que no existían en unics
    */
    public function enviaRelaciones($id_account)
    {
        try {
            //Retrieve account
            $GLOBALS['log']->fatal('Valida relaciones - Recupera cuenta');
            $account = BeanFactory::getBean('Accounts', $id_account);

            //retrieve all related records
            $GLOBALS['log']->fatal('Valida relaciones - Recupera relaciones');
            $account->load_relationship('rel_relaciones_accounts_1');

            foreach ($account->rel_relaciones_accounts_1->getBeans() as $relacion) {
                if ($relacion->sincronizado_unics_c != 1) {
                    $GLOBALS['log']->fatal('Valida relaciones - Envía relación: ' . $relacion->id);
                    $rel = BeanFactory::getBean('Rel_Relaciones', $relacion->id);
                    $rel->save();
                }
            }
        } catch (Exception $e) {
            $GLOBALS['log']->fatal('Valida relaciones - Error:');
            $GLOBALS['log']->fatal($e);
        }

    }

    public function postMambu($host, $fields, $auth)
    {

        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/vnd.mambu.v2+json',
                'Authorization: Basic ' . $auth)
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;

    }

    public function postMambuUniclick($host, $fields, $auth)
    {
        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'x-api-key: '.$auth
            )
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;

    }

    public function postUNICS($host, $fields)
    {

        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;

    }

    public function getMambu($host, $auth)
    {

        $url = $host;
        $ch = curl_init();
        $GLOBALS['log']->fatal('INICIA Funcion getMambu');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/vnd.mambu.v1+json',
                'Authorization: Basic ' . $auth)
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);
        $GLOBALS['log']->fatal('END Funcion getMambu');
        return $response;
    }

    public function updateMambuCB($host, $fields, $auth)
    {
        $url = $host;
        $fields_string = json_encode($fields);
        $GLOBALS['log']->fatal('Entra Funcion updateMambuCB');
        $GLOBALS['log']->fatal($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/vnd.mambu.v1+json',
                'Authorization: Basic ' . $auth)
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);
        $GLOBALS['log']->fatal('END Funcion updateMambuCB');
        return $response;
    }

    public function postCBMambu($host, $fields, $auth)
    {

        $url = $host;
        $fields_string = json_encode($fields);
        $GLOBALS['log']->fatal('Entra Funcion postCBMambu');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/vnd.mambu.v2+json',
                'Authorization: Basic ' . $auth)
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);
        $GLOBALS['log']->fatal('END Funcion postCBMambu');
        return $response;

    }

    public function postidcorto($host, $fields)
    {

        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;

    }

    public function postCBunics($host, $fields)
    {

        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;

    }

    public function postQuantico($host,$body,$auth_encode)
    {
        try {
            $url = $host;
            $fields_string = json_encode($body);
            $GLOBALS['log']->fatal('Entra Funcion postQuantico, body:');
            $GLOBALS['log']->fatal(":JSON ENCODED  $url - " . print_r($fields_string, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $auth_encode)
            );
            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $response = json_decode($result, true);
            $GLOBALS['log']->fatal('END Funcion postQuantico');
            return $response;
        } catch (Exception $exception) {

        }

    }

    public function getQuanticoCF($host,$auth_encode)
    {
        try {
            $url = $host;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $auth_encode)
            );

            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);
            $GLOBALS['log']->fatal('FIN PETICIÓN QUANTICO CF');

            return $response;
        } catch (Exception $exception) {

        }

    }

    public function getQuanticoCFListaValores($host,$auth_encode,$nombre_lista)
    {
        try {
            $url = $host;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $auth_encode)
            );

            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);
            $GLOBALS['log']->fatal('FIN PETICIÓN  GetAssetRequest '.$nombre_lista);

            return $response;
        } catch (Exception $exception) {

        }

    }

    public function postUNION($host, $fields)
    {

        $url = $host;
        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = json_decode($result, true);

        return $response;
    }

    public function getDWHREUS($host)
    {
        try {
            $url = $host;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json')
            );

            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);
            //$GLOBALS['log']->fatal('FIN PETICIÓN DWH REUS');

            return $response;

        } catch (Exception $exception) {
            $GLOBALS['log']->fatal($exception->getMessage());
            $this->setErrorLogFailRequest( "Reus", '', $host, '', $exception->getMessage() );
        }
    }

    public function postSimilarityToken($url,$user,$pwd){
        $curl = curl_init();
        
        try {

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=password&username='.$user.'&password='.$pwd,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                    ),
            ));
        
            $result = curl_exec($curl);
            $curl_info = curl_getinfo($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);

            return $response;

        } catch (Exception $exception) {
            $GLOBALS['log']->fatal('Error '.$url);
            $GLOBALS['log']->fatal(print_r($exception,true));
            
        }

    }

    public function postCallSimilarity($host, $fields, $token)
    {
        global $current_user;
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

        try {
            // Login
            $url = $host;
            $fields_string = '';
            $fields_string = json_encode($fields);
            //print the json encode to the sugarlog
            $GLOBALS['log']->fatal("Api Similarity");
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :JSON ENCODED  $url - " . print_r($fields_string, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' .$token)
            );
            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            if ($http_status != 200) {
                $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " STATUS " . " <" . $current_user->user_name . "> : $http_status ");
            }
            // Get response as an object
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : result -- " . print_r($result, true));

            $response = json_decode($result, true);

            return $response;
        } catch (Exception $e) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinPostCall ERROR: " . $e->getMessage());
        }
    }

    public function getInfoRegimenesSAT($host,$token)
    {
        global $current_user;
        try {
            $url = $host;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token)
            );

            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);

            return $response;
        } catch (Exception $exception) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinPostCall ERROR: " . $exception->getMessage());
        }

    }

    public function postOnboardingPO($host, $token, $body)
    {
        global $current_user;
        try {
            $url = $host;
            $request_body = json_encode($body);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Authorization: Token ' . $token
                )
            );

            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result, true);

            return $response;
        } catch (Exception $exception) {
            $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : unifinPostCall ERROR: " . $exception->getMessage());
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
          "integration"=> "Unics: ".$endpoint,
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

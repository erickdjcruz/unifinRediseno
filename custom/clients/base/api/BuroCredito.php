<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once("custom/Levementum/UnifinAPI.php");
class BuroCredito extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'ClientesSinBuroCredito' => array(
                'reqType' => 'GET',
                'path' => array('ClientesSinBuroCredito'),
                'pathVars' => array(''),
                'method' => 'getClientesSinBuroCredito',
                'shortHelp' => 'Obtiene cuentas tipo cliente que no cuentan con el check de Buró de Crédito',
            ),
            'ClientesBuroCredito' => array(
                'reqType' => 'GET',
                'path' => array('ClientesBuroCredito'),
                'pathVars' => array(''),
                'method' => 'getClientesBuroCredito',
                'shortHelp' => 'Obtiene cuentas tipo cliente que se encuentran marcadas para seguimiento de Buró de Crédito (campo seguimiento_bc_c)',
            ),
            'BorrarBuroCredito' => array(
                'reqType' => 'POST',
                'path' => array('BorrarClienteBuroCredito'),
                'pathVars' => array(''),
                'method' => 'deleteClientesBuroCredito',
                'shortHelp' => 'Elimina del seguimiento de buró de crédito la cuenta enviada como parámetro',
            ),
            'AddBuroCredito' => array(
                'reqType' => 'POST',
                'path' => array('AgregarClienteBuroCredito'),
                'pathVars' => array(''),
                'method' => 'addClienteBuroCredito',
                'shortHelp' => 'Agrega a seguimiento de buró de crédito la cuenta enviada como parámetro',
            ),
        );
    }

    public function getClientesSinBuroCredito($api, $args){
        $nameFiltro = $args['q'];
        $response = array();
        //Obtener todas las cuentas tipo Cliente que no tengan marcado el check de buró de Crédito (campo en resumen)
        $sqlQuery = "SELECT a.name, a.id ,ac.tipo_registro_cuenta_c, ac.subtipo_registro_cuenta_c, ac.rfc_c, rc.seguimiento_bc_c FROM accounts a 
INNER JOIN accounts_cstm ac ON a.id =ac.id_c
INNER JOIN tct02_resumen_cstm rc ON ac.id_c = rc.id_c
WHERE a.deleted = 0
AND ac.tipo_registro_cuenta_c = '3'
AND a.name LIKE '%".$nameFiltro."%'
AND (rc.seguimiento_bc_c = 0 OR rc.seguimiento_bc_c IS NULL);";

        $result = $GLOBALS['db']->query($sqlQuery);

        while($row = $GLOBALS['db']->fetchByAssoc($result)) {
			array_push($response,$row);
		}
        return $response;
    }

    public function getClientesBuroCredito($api, $args){
        $response = array();
        //Obtener todas las cuentas tipo Cliente que no tengan marcado el check de buró de Crédito (campo en resumen)
        $sqlQuery = "SELECT a.name, a.id ,ac.tipo_registro_cuenta_c, ac.subtipo_registro_cuenta_c, ac.rfc_c, rc.seguimiento_bc_c FROM accounts a 
INNER JOIN accounts_cstm ac ON a.id =ac.id_c
INNER JOIN tct02_resumen_cstm rc ON ac.id_c = rc.id_c
WHERE a.deleted = 0
AND ac.tipo_registro_cuenta_c = '3'
AND rc.seguimiento_bc_c = 1 ";

        $result = $GLOBALS['db']->query($sqlQuery);

        while($row = $GLOBALS['db']->fetchByAssoc($result)) {
			array_push($response,$row);
		}
        return $response;
    }

    public function deleteClientesBuroCredito($api, $args){

        $idCliente = $args['idCliente'];

        $beanResumen = BeanFactory::getBean('tct02_Resumen', $idCliente);

        $beanResumen->seguimiento_bc_c = 0;

        $beanResumen->save();

        $beanCliente = BeanFactory::getBean('Accounts', $idCliente);

        return array(
            "msg"=>"El Cliente ". $beanCliente->name." ha sido removido del seguimiento de Buró de Crédito",
            "id"=> $idCliente
        );


    }

    public function addClienteBuroCredito($api, $args){
        //Establece bandera de seguimiento
        $idCliente = $args['idCliente'];
        $beanResumen = BeanFactory::getBean('tct02_Resumen', $idCliente);
        $beanCliente = BeanFactory::getBean('Accounts', $idCliente);
        $beanResumen->seguimiento_bc_c = 1;
        $beanResumen->save();

        $response = array();

        //Obtiene direcciones del cliente para que, en caso de tener dirección fiscal, dicha dirección se establece con el nuevo tipo "Buró de Crédito"
        //y se observa en la nueva sección dentro de Cuentas
        $beanDireccionFiscal = $this->getDireFiscal($idCliente);

        if ($beanDireccionFiscal != "") {
            //Se genera nueva Dirección Buró de Crédito solo si el Cliente no cuenta con una
            if (!$this->tieneDireBuroCredito($idCliente)) {
                $beanNuevaDireccionBuro = BeanFactory::newBean('dire_Direccion');

                $beanNuevaDireccionBuro->name = $beanDireccionFiscal->name;
                /*
                $beanNuevaDireccionBuro->dire_direccion_dire_codigopostaldire_codigopostal_ida = $beanDireccionFiscal->dire_direccion_dire_codigopostaldire_codigopostal_ida;
                $beanNuevaDireccionBuro->dire_direccion_dire_municipiodire_municipio_ida = $beanDireccionFiscal->dire_direccion_dire_municipiodire_municipio_ida;
                $beanNuevaDireccionBuro->dire_direccion_dire_estadodire_estado_ida = $beanDireccionFiscal->dire_direccion_dire_estadodire_estado_ida;
                $beanNuevaDireccionBuro->dire_direccion_dire_paisdire_pais_ida = $beanDireccionFiscal->dire_direccion_dire_paisdire_pais_ida;
                $beanNuevaDireccionBuro->dire_direccion_dire_coloniadire_colonia_ida = $beanDireccionFiscal->dire_direccion_dire_coloniadire_colonia_ida;
                $beanNuevaDireccionBuro->dire_direccion_dire_ciudaddire_ciudad_ida = $beanDireccionFiscal->dire_direccion_dire_ciudaddire_ciudad_ida;
                */
                $beanNuevaDireccionBuro->dir_sepomex_dire_direcciondir_sepomex_ida = $beanDireccionFiscal->dir_sepomex_dire_direcciondir_sepomex_ida;

                $beanNuevaDireccionBuro->calle = $beanDireccionFiscal->calle;
                $beanNuevaDireccionBuro->numext = $beanDireccionFiscal->numext;
                $beanNuevaDireccionBuro->numint = $beanDireccionFiscal->numint;
                $beanNuevaDireccionBuro->indicador = '64'; //Buró de Crédito

                $beanNuevaDireccionBuro->accounts_dire_direccion_1accounts_ida = $beanDireccionFiscal->accounts_dire_direccion_1accounts_ida;

                $beanNuevaDireccionBuro->save();

                $response = array(
                    "msg" => "El Cliente " . $beanCliente->name . " se ha establecido para seguimiento de Buró de Crédito",
                    "id_direccion" => $beanNuevaDireccionBuro->id
                );
            } else {
                $response = array(
                    "msg" => "El Cliente " . $beanCliente->name . " se ha establecido para seguimiento de Buró de Crédito",
                    "id_direccion" => "Ya cuenta con dirección Buró de Crédito previa"
                );
            }
        } else {
            $response = array(
                "msg" => "El Cliente " . $beanCliente->name . " se ha establecido para seguimiento de Buró de Crédito",
                "id_direccion" => ""
            );
        }

        return $response;
    }

    public function getDireFiscal( $idCliente ){
        $beanCliente = BeanFactory::getBean('Accounts', $idCliente);

        $direccion_fiscal = "";
        //Obtiene las direcciones relacionadas para detectar la Fiscal y poder armar el cuerpo de la notificación
        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
        if ($beanCliente->load_relationship('accounts_dire_direccion_1')) {
            $relatedDirecciones = $beanCliente->accounts_dire_direccion_1->getBeans();

            if (!empty($relatedDirecciones)) {
                        
                foreach ($relatedDirecciones as $direccion) {
                    //Valida si tiene dirección fiscal
                    $indicador = $direccion->indicador;
                    if( in_array($indicador,$indicador_direcciones_fiscales) && !$direccion->inactivo ){
                        $GLOBALS['log']->fatal("La dirección fiscal encontrada es: ".$direccion->id);   
                        $direccion_fiscal = $direccion;

                        //Se aplica break para salir del ciclo al encontrar la dirección fiscal
                        break;
                    }
                    
                }
            }
        }

        return $direccion_fiscal;

    }

    public function tieneDireBuroCredito( $idCliente ){

        $beanCliente = BeanFactory::getBean('Accounts', $idCliente);

        $direccion_buro = false;
        
        if ($beanCliente->load_relationship('accounts_dire_direccion_1')) {
            $relatedDirecciones = $beanCliente->accounts_dire_direccion_1->getBeans();

            if (!empty($relatedDirecciones)) {
                        
                foreach ($relatedDirecciones as $direccion) {
                    //Valida si tiene dirección fiscal
                    $indicador = $direccion->indicador;
                    if( $indicador == '64' && !$direccion->inactivo ){
                        $GLOBALS['log']->fatal("La dirección Buró de Crédito encontrada es: ".$direccion->id);   
                        $direccion_buro = true;
                        //Se aplica break para salir del ciclo al encontrar la dirección de buró
                        break;
                    }
                    
                }
            }
        }

        return $direccion_buro;

    }

}


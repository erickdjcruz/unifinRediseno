<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class class_dir_buro
{
    function func_dir_buro_credito($bean, $event, $args)
    {
        $GLOBALS['log']->fatal("*** Valida dirección de Buró si se agrega por api o dirección fiscal *** ");
        //Incializa variables
        global $app_list_strings;
        //$GLOBALS['log']->fatal( print_r($bean,true) );
        $id_direccion = $bean->id;
        $localidad = $bean->localidad_c;
        $indicador = $bean->indicador;
        
        $list_indicadores_map = $app_list_strings['dir_indicador_map_list'];
        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
        //Valida si tiene dirección fiscal
        $GLOBALS['log']->fatal( 'diredireccion-bean_id'.$bean->id );
        $GLOBALS['log']->fatal( 'dire localidad'.$localidad );
        
        if( in_array($indicador,$indicador_direcciones_fiscales) ){

            $GLOBALS['log']->fatal("*** Es fiscal *** ");
            if ($bean->load_relationship('accounts_dire_direccion_1')) {
                $relatedDirecciones = $bean->accounts_dire_direccion_1->getBeans();

                if (!empty($relatedDirecciones)) {
                    foreach ($relatedDirecciones as $direccion) {
                        $id_cuenta = $direccion->id;
                        $GLOBALS['log']->fatal("ID_CUENTA_REL_DIRECCION " . $direccion->id);
                    }
                }
            }
            $GLOBALS['log']->fatal("** IdCLiente:".$id_cuenta);
            $apiDireccionBuroCredito = new BuroCredito();
            $body = array(
                'idCliente' => $id_cuenta,
                'localidad' => $localidad
            );
            $response = $apiDireccionBuroCredito->addClienteBuroCredito(null, $body);
            $GLOBALS['log']->fatal("Dirección de Buró creada exitosamente.");

        //}else if( $_SESSION['platform'] !== 'base' &&  $indicador == '64' ){
        }
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

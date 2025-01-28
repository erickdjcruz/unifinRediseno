<?php
/*/**
 * Created by Eduardo Carrasco Beltrán
 * Date: 11/07/2023
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class ConvertirPO extends SugarApi
{

    public function registerApiRest()
    {
        return array(
            'altaCuentaPO' => array(
                'reqType' => 'POST',
                'path' => array('convertirPO'),
                'pathVars' => array(''),
                'method' => 'altaPO',
                'shortHelp' => 'Genera Cuenta tipo persona desde un registro de Público Objetivo',
            ),
        );
    }

	public function altaPO($api, $args) {

        $idPO = $args["idPO"];
        $idCuenta = $args["idCuenta"];

        if( !isset($idPO) || !isset($idCuenta) ){
            $response = array(
                "status" => 400,
                "detalle" => "La petición es incorrecta, asegúrese de establecer idPO y idCuenta",
                "idCuentaPO" => ""
            );

        }else{

            $beanPO = BeanFactory::retrieveBean('Prospects', $idPO, array('disable_row_level_security' => true));
            $beanCuenta = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));

            if ((!empty($beanPO) && !is_null($beanPO)) && (!empty($beanCuenta) && !is_null($beanCuenta)) ) {
                $relacionesCuenta = $this->getRelacionesCuenta($beanCuenta);

                $idCuentaRecuperada = $this->validaExistenciaPOenCuenta( $beanPO->email1, $beanPO->rfc_c );
                
                /*
                   Valida Origen de Cuenta-Cliente,
                   Si;
                    - Estatus atención = Atendido, no hay afectación en origen
                    - Estatus atención != Atendido, actualiza origen con valor de PO
                    estatus_atencion; 1= Atendido, 2=Desatendido
                */
                $queryE = "select u.id, u.tipo_producto, u.estatus_atencion
                    from uni_productos u
                    	inner join accounts_uni_productos_1_c au on au.accounts_uni_productos_1uni_productos_idb = u.id
                    where au.deleted=0
                    	and u.deleted=0
                    	and u.tipo_producto=1
                    	and au.accounts_uni_productos_1accounts_ida='{$idCuenta}'
                    limit 1
                    ;";
                $estadoAtencion = $GLOBALS['db']->query($queryE);
                $desatendido = true;
                while ($row = $GLOBALS['db']->fetchByAssoc($estadoAtencion)) {
                  if($row['estatus_atencion'] == '1'){
                      $desatendido = false;
                  }
                }
                if($desatendido){
                    $beanCuenta->origen_cuenta_c = $beanPO->origen_c;
                    $beanCuenta->detalle_origen_c = $beanPO->detalle_origen_c;
                    $beanCuenta->save();
                }
                
                //El PO ya existe en cuentas
                if( $idCuentaRecuperada != '' ){
                    $GLOBALS['log']->fatal('CUENTA RECUPERADA A PARTIR DEL PO: '.$idCuentaRecuperada);
                    //Valida que la cuenta recuperada existe como relación en la cuenta
                    $idExisteCuentapoEnRelacion = $this->validaCuentapoEnRelacion( $relacionesCuenta, $idCuentaRecuperada );

                    if( $idExisteCuentapoEnRelacion == "" ){
                        $GLOBALS['log']->fatal('LA CUENTA ENCONTRADA NO EXISTE COMO RELACION: '.$idCuentaRecuperada);
                        //Crea Relación entre la cuenta encontrada y el id de la cuenta
                        $beanRelacion = BeanFactory::newBean("Rel_Relaciones");
                        $beanRelacion->rel_relaciones_accounts_1accounts_ida = $idCuenta; //Establece campo de relacion
                        $beanRelacion->relaciones_activas = '^Contacto^';
                        $beanRelacion->tipodecontacto = 'Promocion';
                        $beanRelacion->account_id1_c = $idCuentaRecuperada;

                        $beanRelacion->save();

                    }else{
                        //Actualiza la relación con relacion activa: Contacto, tipodecontacto: Promoción
                        $beanRelacion =  BeanFactory::retrieveBean('Rel_Relaciones', $idExisteCuentapoEnRelacion, array('disable_row_level_security' => true));

                        if( $beanRelacion->relaciones_activas != "" ){
                            $beanRelacion->relaciones_activas = $beanRelacion->relaciones_activas . ",^Contacto^";
                        }else{
                            $beanRelacion->relaciones_activas = "^Contacto^";
                        }
                        $beanRelacion->tipodecontacto = 'Promocion';

                        $beanRelacion->save();
                    }
                    //Se establece campo relacionado con la cuenta a la que se le genera relación de la Persona recuperada
                    $beanPO->estatus_po_c = '3';
                    $beanPO->account_id2_c = $idCuentaRecuperada;
                    $beanPO->save();

                    $response = array(
                        "status" => 200,
                        "detalle" => "El registro de PO ya se encuentra registrado como Cuenta",
                        "idCuentaPO" => $idCuentaRecuperada
                    );
                    

                }else{//El PO no existe en cuentas
                    $idCuentaPersonaGenerada = $this->generaCuentaDesdePO($beanPO);
                    
                    $GLOBALS['log']->fatal('CUENTA CREADA DESDE PO: '.$idCuentaPersonaGenerada);

                    //Crea Relación entre la cuenta encontrada y el id de la cuenta
                    $beanRelacion = BeanFactory::newBean("Rel_Relaciones");
                    $beanRelacion->rel_relaciones_accounts_1accounts_ida = $idCuenta; //Establece campo de relacion
                    $beanRelacion->relaciones_activas = '^Contacto^';
                    $beanRelacion->tipodecontacto = 'Promocion';
                    $beanRelacion->account_id1_c = $idCuentaPersonaGenerada;

                    $beanRelacion->save();

                    //Una vez generada la cuenta, el PO se establece Convertido y se establece campo relacionado con la cuenta a la que se le genera relación de la Persona recién creadsa
                    $beanPO->estatus_po_c = '3';
                    $beanPO->account_id2_c = $idCuentaPersonaGenerada;
                    $beanPO->save();

                    $response = array(
                        "status" => 200,
                        "detalle" => "El registro de PO se ha convertido a Cuenta correctamente",
                        "idCuentaPO" => $idCuentaPersonaGenerada
                    );

                }
                /*if(!empty($beanPO->account_id2_c)){
                    $beanContacto = BeanFactory::retrieveBean('Accounts', $beanPO->account_id2_c, array('disable_row_level_security' => true));
                    if( $beanContacto->origen_cuenta_c=='1' || empty($beanContacto->origen_cuenta_c) || $beanContacto->detalle_origen_c=='12' || $beanContacto->detalle_origen_c=='80' ){ // 12:Leasing - 80:Organico
                        $beanContacto->origen_cuenta_c = $beanPO->origen_c;
                        $beanContacto->detalle_origen_c = $beanPO->detalle_origen_c;
                        $beanContacto->save();
                    }
                }*/

            }else{
                $response = array(
                    "status" => 404,
                    "detalle" => "Los valores establecidos para idPO o idCuenta no son valores válidos",
                    "idCuentaPO" => ""
                );
            }
        }
			
		return $response;
	}

    public function getRelacionesCuenta( $beanCuenta ){
        $beanCuenta->load_relationship('rel_relaciones_accounts_1');

        return $beanCuenta->rel_relaciones_accounts_1->getBeans();
    
    }

    public function validaExistenciaPOenCuenta( $email, $rfc ){

        $accounts_bean = BeanFactory::newBean('Accounts');
        $accounts_bean->disable_row_level_security = true;
        
        $sql = new SugarQuery();
        $sql->select(array('id'));
        $sql->from( $accounts_bean );
        
        if( $rfc != "" ){
            $sql->where()->equals('rfc_c', $rfc);
        }else{
            $sql->where()->equals('email1', $email);
        }

        $result = $sql->execute();

        if( !empty($result) ){
            return $result[0]['id'];
        }else{
            return '';
        }
        
    }

    public function generaCuentaDesdePO( $beanPO ){

        $newCuentaBean = BeanFactory::newBean("Accounts");

        $newCuentaBean->tipo_registro_cuenta_c = '4';//Se establece como tipo Persona
        $newCuentaBean->primernombre_c = $beanPO->nombre_c;
        $newCuentaBean->apellidopaterno_c = $beanPO->apellido_paterno_c;
        $newCuentaBean->apellidomaterno_c = $beanPO->apellido_materno_c;
        $newCuentaBean->email1 = $beanPO->email1;

        $newCuentaBean->save();

        return $newCuentaBean->id;

    }

    public function validaCuentapoEnRelacion( $relacionesCuenta, $idCuentaPO ){

        $idRelacionEncontrada = "";
        foreach($relacionesCuenta as $rel){
            
            if( $rel->account_id1_c == $idCuentaPO ){
                $idRelacionEncontrada = $rel->id;
            }
        }

        return $idRelacionEncontrada;

    }
}

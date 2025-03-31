<?php
require_once("custom/Levementum/DropdownValuesHelper.php");
require_once("custom/Levementum/UnifinAPI.php");
require_once('config_override.php');

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class AsignacionAsesores extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'POSTAsignacionAsesores' => array(
                'reqType' => 'POST',
                'path' => array('AsignacionAsesores'),
                'pathVars' => array(''),
                'method' => 'asignarCuentas',
                'shortHelp' => 'Obtener registros de promotores',
            ),
        );
    }

    public function asignarCuentas($api, $args)
    {
	  try {
        global $db, $current_user;
        $actualizados = array();
        $no_actualizados = array();
        $reAsignado = $args['data']['reAssignado'];
        $product = $args['data']['producto_seleccionado'];
        //Eliminando saltos de línea
        $product = str_replace("\r", "", $product);
        $product = str_replace("É", "E", $product);
        $promoActual = $args['data']['promoActual'];
        $optRadio = $args['data']['optBl'];
        $nombreArchivo = $args['data']['nombreArchivo'];
        $statusProducto = $args['data']['status_producto'];
        $idProducto = $args['data']['producto_seleccionado_id'];
        $batch = isset($args['data']['batch']) ? $args['data']['batch'] : false;
        $listaCuentas = [];
        $main_array = [];
        $main_array['actualizados'] = [];
        $main_array['no_actualizados'] = [];
        $GLOBALS['log']->fatal("cuentas " . print_r($args['data']['seleccionados'], true));
        $listaUsuarios = $this->getUsersDetail("'".$reAsignado."'");
        if ($product == "LEASING") {
            $user_field = "user_id_c"; //user_id_c = promotorleasing_c
        } else if ($product == "FACTORAJE") {
            $user_field = "user_id1_c"; //user_id1_c = promotorfactoraje_c
        } else if ($product == "CREDITO AUTOMOTRIZ") {
            $user_field = "user_id2_c"; //user_id2_c = promotorcredit_c
        } else if ($product == "FLEET") {
            $user_field = "user_id6_c";
        } else if ($product == "UNICLICK") {
            $user_field = "user_id7_c";
        } else if ($product == "UNILEASE") {
            $user_field = "user_id7_c";
        } else if ($product == "RM") {
            $user_field = "user_id8_c";
        }
        $IntValue = new DropdownValuesHelper();
        $callApi = new UnifinAPI();
        foreach ($args['data']['seleccionados'] as $row => $value) {
			$modulo = $value['modulo'];
            $cuenta = $value['id'];
			$reAsignado = $value['nuevo'];
			$promoActual = $value['viejo'];
			if ($modulo == "Cuenta") {
				$account = BeanFactory::retrieveBean('Accounts', $cuenta, array('disable_row_level_security' => true));
				if ($account == null || $user_field == null || $reAsignado == null || $promoActual == null) {
					array_push($no_actualizados, $cuenta);
				} else {
					/****************************Re-Asigna Fecha y Re-Asigna Asesor en UNI_PRODUCTOS*****************/
					if ($account->load_relationship('accounts_uni_productos_1')) {
						$uniProducto = $account->accounts_uni_productos_1->getBeans($account->id, array('disable_row_level_security' => true));
						$fechaReAsignaAsesor = date("Y-m-d"); //Fecha de Hoy
						foreach ($uniProducto as $asignaFecha) {
							$GLOBALS['log']->fatal("Producto: ".$product);
							switch ($product) {
								case 'LEASING':
									if ($asignaFecha->tipo_producto == '1') {  //Leasing
										// $GLOBALS['log']->fatal("Leasing UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										//Valida que sea 9 no viable para desmarcar los campos no Viable por producto
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
												}
										}
										# estatus_atencion
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 1);
												$GLOBALS['log']->fatal("Leasing  Reasignado Prospecto" . " " . $regla1 . " Regla2 " . $regla2 . " Regala3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 1);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 1);
												$GLOBALS['log']->fatal("Leasing  Reasignado Cliente " . " " . $regla1 . " Regla2 " . $regla2 . "Regla 3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									if ($asignaFecha->tipo_producto == '2' || $asignaFecha->tipo_producto == '9' || $asignaFecha->tipo_producto == '7') {  //Crédito Simple - Unilease - SOS
										// $GLOBALS['log']->fatal("Leasing UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
									}
									break;
								case 'CREDITO AUTOMOTRIZ':
									if ($asignaFecha->tipo_producto == '3') { //Credito-Automotriz
										// $GLOBALS['log']->fatal("Credito UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
											}
										}
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 3);
												$GLOBALS['log']->fatal("CA  Reasignado Prospecto" . " " . $regla1 . " Regla 2" . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 3);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 3);
												$GLOBALS['log']->fatal("CA  Reasignado Cliente " . " " . $regla1 . " Regla 2  " . $regla2 . " Regla 3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									break;
								case 'FACTORAJE':
									if ($asignaFecha->tipo_producto == '4') { //Factoraje
										// $GLOBALS['log']->fatal("Factoraje UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
											}
										}
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 4);
												$GLOBALS['log']->fatal("F  Reasignado Prospecto" . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 4);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 4);
												$GLOBALS['log']->fatal("F  Reasignado Cliente " . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									break;
								case 'FLEET':
									if ($asignaFecha->tipo_producto == '6') { //Fleet
										// $GLOBALS['log']->fatal("Fleet UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
											}
										}
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 6);
												$GLOBALS['log']->fatal("Fleet  Reasignado Prospecto" . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 6);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 6);
												$GLOBALS['log']->fatal("Fleet  Reasignado Cliente " . " " . $regla1 . " Regla2" . $regla2 . " Regla 3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									break;
								case 'UNICLICK':
									if ($asignaFecha->tipo_producto == '8') { //Uniclick
										// $GLOBALS['log']->fatal("Uniclick UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
											}
										}
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 8);
												$GLOBALS['log']->fatal("uniclick  Reasignado Prospecto" . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 8);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 8);
												$GLOBALS['log']->fatal("uniclick  Reasignado Cliente " . " " . $regla1 . " Regla2 " . $regla2 . " Regla3" . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									break;
								case 'UNILEASE':
									if ($asignaFecha->tipo_producto == '8') { //Uniclick
										// $GLOBALS['log']->fatal("Uniclick UniProductos - Reasignado");
										$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
										$asignaFecha->assigned_user_id = $reAsignado;
										if ($promoActual=="cc736f7a-4f5f-11e9-856a-a0481cdf89eb"){
											if ($asignaFecha->no_viable==1) {
												$asignaFecha->no_viable=0;
												$asignaFecha->no_viable_razon="";
												$asignaFecha->no_viable_razon_fp="";
												$asignaFecha->no_viable_quien="";
												$asignaFecha->no_viable_porque="";
												$asignaFecha->no_viable_producto="";
												$asignaFecha->no_viable_razon_cf="";
												$asignaFecha->no_viable_otro_c="";
												$asignaFecha->no_viable_razon_ni="";
											}
										}
										if ($asignaFecha->estatus_atencion == '2') {
											/** diferente de Activo */ #1-5 y 3 es cliente
											if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
												$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
												$regla2 = $this->SolicitudCredito($account->id);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 8);
												$GLOBALS['log']->fatal("unilease  Reasignado Prospecto" . " " . $regla1 . " Regla2" . $regla2 . " Regla3" . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											} elseif ($account->tipo_registro_cuenta_c == 3) {
												$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
												$regla2 = $this->getAnexos($account->idcliente_c, 8);
												$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
												$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 8);
												$GLOBALS['log']->fatal("unilease  Reasignado Cliente " . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
												if ($regla1 || $regla2 || $regla3) {
													$asignaFecha->estatus_atencion = 1;
												}
											}
										}
									}
									break;
							}
							/** Excepcion para actualiza SOS cuando es tipo producto LEASINGS */
							if ($asignaFecha->tipo_producto == '7' && $product == 'LEASING') {
								$GLOBALS['log']->fatal("Actualizar SOS");
								$asignaFecha->fecha_asignacion_c = $fechaReAsignaAsesor;
								$asignaFecha->assigned_user_id = $reAsignado;
								if ($asignaFecha->estatus_atencion == '2') {
									/** diferente de Activo */ #1-5 y 3 es cliente
									if ($account->tipo_registro_cuenta_c == 1 || $account->tipo_registro_cuenta_c == 2) {
										$regla1 = $this->MeetOrCallValid(30, $account->id, $reAsignado);
										$regla2 = $this->SolicitudCredito($account->id);
										$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
										$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 1);
										$GLOBALS['log']->fatal("sos  Reasignado Prospecto" . " " . $regla1 . " REgla2 " . $regla2 . " Regla3 " . $regla3);
										if ($regla1 || $regla2 || $regla3) {
											$asignaFecha->estatus_atencion = 1;
										}
									} elseif ($account->tipo_registro_cuenta_c == 3) {
										$regla1 = $this->MeetOrCallValid(90, $account->id, $reAsignado);
										$regla2 = $this->getAnexos($account->idcliente_c, 1);
										$idEmpresarial = $account->parent_id != "" ? $account->parent_id : $account->id;
										$regla3 = $this->grupoEmpresarial($idEmpresarial, $account->id, 1);
										$GLOBALS['log']->fatal("sos  Reasignado Cliente " . " " . $regla1 . " Regla2 " . $regla2 . " Regla3 " . $regla3);
										if ($regla1 || $regla2 || $regla3) {
											$asignaFecha->estatus_atencion = 1;
										}
									}
								}
							}
							$asignaFecha->save();
						}
					}
					switch ($product) {
						case 'LEASING':
							$account->user_id_c = $reAsignado;
							break;
						case 'CREDITO AUTOMOTRIZ':
							$account->user_id2_c = $reAsignado;
							break;
						case 'FACTORAJE':
							$account->user_id1_c = $reAsignado;
							break;
						case 'FLEET':
							$account->user_id6_c = $reAsignado;
							break;
						case 'UNICLICK':
							$account->user_id7_c = $reAsignado;
							break;
						case 'UNILEASE':
							$account->user_id7_c = $reAsignado;
							break;
						case 'RM':
							$account->user_id8_c = $reAsignado;
							break;
					}
					//Guarda cuenta CRM
					$account->save();
					//Agrega cuenta para sincronizar con Quantico
					//Sólo aplica para producto leasing
					if($product == 'LEASING'){
						$cuentaQ = [
							'ClientId' => $account->id,
							'AdviserId' => $listaUsuarios[$reAsignado],
							'ProductId' => "41",
							'ProductTypeId' => "1"
						];
						$listaCuentas[] = $cuentaQ;
					}
					array_push($actualizados, $account->id);
					// Funcionalidad para notificar al promotor reasigando
					$User = new User();
					$User->retrieve($reAsignado);
					$para = $User->email1;
					if ($para != null) {
						if ($User->status != 'Active') {
							$reAsignado = '';
						} else {
							if ($User->optout_c == 1) {
								$reAsignado = '5cd93b08-6f5a-11e8-8553-00155d963615';
							}
						}
						$jefeAsignado = '';
						$jefe = $User->reports_to_id;
						$User->retrieve($jefe);
						//Establecer id a nuevo jefe solo si éste es Activo
						if ($User->status == 'Active') {
							if ($User->optout_c != 1) {
								$jefeAsignado = $User->email1;
							}
						} else {
							$jefeAsignado = '';
						}
						$User->retrieve($promoActual);
						if ($User->status == 'Active') {
							if ($User->optout_c == 1) {
								$promoActual = '5cd93b08-6f5a-11e8-8553-00155d963615';
							}
						} else {
							$promoActual = '';
						}
						$jefeActual = '';
						$jefe = $User->reports_to_id;
						$User->retrieve($jefe);
						//Establecer id a jefe Actual solo si éste es Activo
						if ($User->status == 'Active') {
							if ($User->optout_c != 1) {
								$jefeActual = $User->email1;
							}
						} else {
							$jefeActual = '';
						}
						$idrm = $account->user_id8_c;
						$UserRM = new User();
						$UserRM->retrieve($idrm);
						$paraRM = $UserRM->email1;
						$notifica = BeanFactory::newBean('TCT2_Notificaciones');
						$notifica->name = $para . ' ' . date("Y-m-d H:i:s");
						$notifica->created_by = $promoActual;
						$notifica->assigned_user_id = $reAsignado;
						$notifica->tipo = 'Cambio Promotor';
						$notifica->tct2_notificaciones_accountsaccounts_ida = $cuenta;
						$notifica->jefe_anterior_c = $jefeActual;
						$notifica->jefe_nuevo_c = $jefeAsignado;
						$notifica->actual_c = $promoActual;
						$notifica->emailrm_c = $paraRM;
						$notifica->save();
						$notId = $notifica->id;
						global $db;
						$query = "update tct2_notificaciones set created_by = '$promoActual' where id = '$notId'";
						$result = $db->query($query);
					}
					//Restablece usuario por asignar
					$reAsignado = $args['data']['reAssignado'];
					//Actualiza Oportunidades
					if ($product == 'LEASING') $producto = 1;
					if ($product == 'CREDITO AUTOMOTRIZ') $producto = 3;
					if ($product == 'FACTORAJE') $producto = 4;
					if ($product == 'FLEET') $producto = 6;
					if ($product == 'UNICLICK') $producto = 8;
					if ($product == 'UNILEASE') $producto = 9;
					if ($product == 'RM') $producto = 11; # Validar si se debe reasignar las oportunidades
					$usr_bean = BeanFactory::retrieveBean("Users", $reAsignado, array('disable_row_level_security' => true));
					$query = <<<SQL
	UPDATE opportunities
	INNER JOIN accounts_opportunities ON accounts_opportunities.opportunity_id = opportunities.id AND accounts_opportunities.deleted = 0
	INNER JOIN accounts ON accounts.id = accounts_opportunities.account_id AND accounts.deleted = 0
	INNER JOIN opportunities_cstm cs ON opportunities.id = cs.id_c
	SET opportunities.assigned_user_id = '{$reAsignado}',
	opportunities.team_id = '{$usr_bean->default_team}',
	opportunities.team_set_id = '{$usr_bean->team_set_id}'
	WHERE accounts.id = '{$cuenta}' AND cs.tipo_producto_c = '{$producto}'
	SQL;
					$queryResult = $db->query($query);
					/*   $queryUpdateTeams = "UPDATE opportunities
						 INNER JOIN accounts_opportunities ON accounts_opportunities.opportunity_id = opportunities.id AND accounts_opportunities.deleted = 0
						 INNER JOIN users ON opportunities.assigned_user_id = users.id
						 SET
							 opportunities.team_id = users.team_set_id,
							 opportunities.team_set_id = concat(left(users.team_set_id, 33),'af0')
						 WHERE accounts_opportunities.account_id ='" . $value . "';";
					   $resultUpdateTeams = $db->query($queryUpdateTeams);*/
					//Actualizar el usuario asignado a registros de Backlog relacionados a las cuentas
					//Obtener Backlogs de la cuenta que sean de meses futuros
	//                $usr_prd_principal=$current_user->tipodeproducto_c;
					//    if ($product == 'LEASING') {
					$anio_actual = date("Y");
					$mes_actual = intval(date("n"));
					$hoy = date("d");
					$condicion = '';
					if ($optRadio == 'siguientes') {
						$condicion = "AND ((b.anio = year(NOW()) and b.mes > month(NOW())) OR b.anio > year(NOW()))";
					} else {
						$condicion = " AND ((b.anio = year(NOW()) and b.mes >= month(NOW())) OR b.anio > year(NOW()))";
					}
					if ($producto == '1') {
						$sos_condicion = " OR cstm.producto_c='7'";
					}
					$bl_cuenta = "SELECT b.id, b.mes,b.description
	FROM
	  lev_backlog b
	  INNER JOIN lev_backlog_cstm cstm
	  ON cstm.id_c=b.id
	WHERE
	  b.account_id_c = '{$cuenta}'" . $condicion . "
	  AND (cstm.producto_c='{$producto}' " . $sos_condicion . ") AND deleted = 0";
					$result_bl_cuentas = $db->query($bl_cuenta);
					if ($result_bl_cuentas->num_rows > 0 && $result_bl_cuentas != null) {
						//Recupera nuevo usuario asignado
						$User = new User();
						$User->retrieve($reAsignado);
						while ($row = $db->fetchByAssoc($result_bl_cuentas)) {
							$bl = BeanFactory::retrieveBean("lev_Backlog", $row['id'], array('disable_row_level_security' => true));
							if ($bl != null) {
								//Actualiza valores
								$bl->assigned_user_id = $reAsignado;
								$bl->description = $row['description'] . ' \n UNI2CRM - ' . $hoy . '/' . $mes_actual . '/' . $anio_actual . ': BL Reasignado a promotor ' . $IntValue->getUserName($reAsignado);
								$bl->equipo = $User->equipo_c;
								$bl->region = $User->region_c;
								$bl->save();
							}
						}
					}
	//                }
					$query = <<<SQL
	select CASE WHEN idcliente_c > 0 THEN idcliente_c ELSE 0 END idCliente from accounts_cstm where id_c = '{$cuenta}'
	SQL;
					$idCliente = $db->getone($query);
					//Peticiones para actualizar uni2
					if (intval($idCliente) > 0) {
						$GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :  Se sincronizaran promtores con UNICS ");
						if ($product == "LEASING") {
							$host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/AsignaPromotor?idCliente=" . $idCliente . "&usuarioPromotorLeasing=" . $IntValue->getUserName($reAsignado) . "&usuarioDominio=" . $current_user->user_name;
						} else if ($product == "FACTORAJE") {
							$host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/AsignaPromotor?idCliente=" . $idCliente . "&usuarioPromotorFactoring=" . $IntValue->getUserName($reAsignado) . "&usuarioDominio=" . $current_user->user_name;
						} else if ($product == "CREDITO AUTOMOTRIZ") {
							$host = "http://" . $GLOBALS['unifin_url'] . "/Uni2WsClnt/WsRest/Uni2ClntService.svc/Uni2/AsignaPromotor?idCliente=" . $idCliente . "&usuarioPromotorCredit=" . $IntValue->getUserName($reAsignado) . "&usuarioDominio=" . $current_user->user_name;
						}
						$callApi->unifingetCall($host);
					} else {
						$GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :  La persona a reasignar no cuenta con IdCliente: " . print_r($idCliente, 1));
					}
					// BUSQUEDA DE CUENTAS HIJAS RELACIONADAS
					$selectRelacionesHijas  = "SELECT rrc.account_id1_c as idRelacionCuentaHija
					FROM rel_relaciones rr
					INNER JOIN rel_relaciones_accounts_1_c rra ON rra.rel_relaciones_accounts_1rel_relaciones_idb = rr.id
					INNER JOIN rel_relaciones_cstm rrc ON rrc.id_c = rr.id 
					WHERE rra.rel_relaciones_accounts_1accounts_ida = '{$cuenta}' AND rr.deleted = 0";
					$relacionesResult = $GLOBALS['db']->query($selectRelacionesHijas);
					while ($rowRelacion = $GLOBALS['db']->fetchByAssoc($relacionesResult)) {
						$idRelacionCuentaHija = $rowRelacion['idRelacionCuentaHija'];
						$GLOBALS['log']->fatal("idRelacionCuentaHija: " . $idRelacionCuentaHija);
						// REASIGNACION DE LA CUENTA HIJA SOLO SI ES NECESARIO
						$selectCuentaHija = "SELECT user_id_c FROM accounts_cstm WHERE id_c = '{$idRelacionCuentaHija}'";
						$resultCuentaHija = $GLOBALS['db']->fetchOne($selectCuentaHija);
						if ($resultCuentaHija && $resultCuentaHija['user_id_c'] !== $reAsignado) {
							$GLOBALS['log']->fatal("ACTUALIZACION EN updateRelCuentaHija ");
							$updateRelCuentaHija = "
								UPDATE accounts_cstm
								SET tct_status_atencion_ddw_c = 'Atendido', user_id_c = '{$reAsignado}'
								WHERE id_c = '{$idRelacionCuentaHija}'
							";
							$GLOBALS['db']->query($updateRelCuentaHija);
						}
						// BUSQUEDA DEL PRODUCTO LEASING DE LA CUENTA HIJA
						$selectProductoLeasingHija = "SELECT up.id as idProductoLeasing, up.assigned_user_id
						FROM accounts a
						INNER JOIN accounts_uni_productos_1_c ap ON a.id = ap.accounts_uni_productos_1accounts_ida
						INNER JOIN uni_productos up ON up.id = ap.accounts_uni_productos_1uni_productos_idb
						INNER JOIN uni_productos_cstm upc ON upc.id_c = up.id
						WHERE a.id = '{$idRelacionCuentaHija}' AND up.tipo_producto = '1' AND up.deleted = '0'";
						$resultplHija = $GLOBALS['db']->fetchOne($selectProductoLeasingHija);
						if ($resultplHija && !empty($resultplHija['idProductoLeasing'])) {
							$idProductoLeasingHija = $resultplHija['idProductoLeasing'];
							$assignedUserProductoHija = $resultplHija['assigned_user_id'];
							$GLOBALS['log']->fatal("idProductoLeasing (Cuenta Hija): " . $idProductoLeasingHija);
							// REASIGNACION SOLO SI ES NECESARIO
							if ($assignedUserProductoHija !== $reAsignado) {
								$GLOBALS['log']->fatal("ACTUALIZACION EN updateProductoAsesorHija ");
								$updateProductoAsesorHija = "
									UPDATE uni_productos
									SET estatus_atencion = '1', assigned_user_id = '{$reAsignado}'
									WHERE id = '{$idProductoLeasingHija}'
								";
								$GLOBALS['db']->query($updateProductoAsesorHija);
							}
						}
					}
					// BUSQUEDA DE LEAD RELACIONADO A LA CUENTA
					$selectRelacionLead  = "SELECT id as idLead, assigned_user_id 
					FROM leads WHERE account_id = '{$cuenta}' AND deleted = '0'";
					$leadRelResult = $GLOBALS['db']->fetchOne($selectRelacionLead);
					if ($leadRelResult && !empty($leadRelResult['idLead'])) {
						$idLead = $leadRelResult['idLead'];
						$assignedUserLead = $leadRelResult['assigned_user_id'];
						$GLOBALS['log']->fatal("idLead (Lead Relacionado a Cuenta): " . $idLead);
						// REASIGNACION SOLO SI ES NECESARIO
						if ($assignedUserLead !== $reAsignado) {
							$GLOBALS['log']->fatal("ACTUALIZACION EN updateLeadAsesor ");
							$updateLeadAsesor = "
								UPDATE leads
								SET assigned_user_id = '{$reAsignado}'
								WHERE id = '{$idLead}'
							";
							$GLOBALS['db']->query($updateLeadAsesor);
						}

						// BUSQUEDA DE PUBLICO OBJETIVO (PO) RELACIONADO AL LEAD
						$selectRelacionPO  = "SELECT p.id as idPO, p.assigned_user_id
						FROM prospects_leads_1_c pl
						INNER JOIN prospects p ON p.id = pl.prospects_leads_1prospects_ida AND p.deleted = 0
						INNER JOIN prospects_cstm pc ON pc.id_c = p.id
						WHERE pl.prospects_leads_1leads_idb = '{$idLead}'";
						$poRelResult = $GLOBALS['db']->fetchOne($selectRelacionPO);
						if ($poRelResult && !empty($poRelResult['idPO'])) {
							$idPO = $poRelResult['idPO'];
							$assignedUserPO = $poRelResult['assigned_user_id'];
							$GLOBALS['log']->fatal("idPO (PO Relacionado a Lead de la Cuenta): " . $idPO);
							// REASIGNACION SOLO SI ES NECESARIO
							if ($assignedUserPO !== $reAsignado) {
								$GLOBALS['log']->fatal("ACTUALIZACION EN updatePOAsesor ");
								$updatePOAsesor = "
									UPDATE prospects
									SET assigned_user_id = '{$reAsignado}'
									WHERE id = '{$idPO}'
								";
								$GLOBALS['db']->query($updatePOAsesor);
							}
						}
					}
				}
			}
			//Actualiza Publico Objetivo
			if ($modulo == "PO") {
				$prospect = BeanFactory::retrieveBean('Prospects', $cuenta, array('disable_row_level_security' => true));
				if ($prospect == null || $reAsignado == null || $promoActual == null) {
					array_push($no_actualizados, $cuenta);
				} else {
					$prospect->assigned_user_id = $reAsignado;
					$prospect->save();
				}
			}
			//Actualiza Leads
			if ($modulo == "Lead") {
				$lead = BeanFactory::retrieveBean('Leads', $cuenta, array('disable_row_level_security' => true));
				if ($lead == null || $reAsignado == null || $promoActual == null) {
					array_push($no_actualizados, $cuenta);
				} else {
					$lead->assigned_user_id = $reAsignado;
					$lead->save();
				}
			}
        }
        $main_array['actualizados'] = $actualizados;
        $main_array['no_actualizados'] = $no_actualizados;
        //Genera actualización Quantico
        $GLOBALS['log']->fatal('Previo');
        if(!$batch && !empty($listaCuentas) ){
          $asignaQuantico = $this->reasignaQuantico($listaCuentas);
        }
        //Genera archivo de errores
        if (count($no_actualizados) > 0 && $nombreArchivo != null && !empty($nombreArchivo)) {
            $fichero = 'custom/errores_reasignacion/' . $nombreArchivo . '.txt';
            $texto_archivo = '';
            for ($i = 0;
                 $i < count($no_actualizados);
                 $i++) {
                $texto_archivo .= $no_actualizados[$i] . "\n";
            }
// Escribir los contenidos en el fichero,
//// usando la bandera FILE_APPEND para añadir el contenido al final del fichero
/// // y la bandera LOCK_EX para evitar que cualquiera escriba en el fichero al mismo tiempo
            file_put_contents($fichero, $texto_archivo, FILE_APPEND | LOCK_EX);
        }
	  } //try
	  catch (Exception $e) {
        array_push($main_array['no_actualizados'],$cuenta);
		$GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <".$current_user->user_name."> : Error al reasignar la cuenta ".$cuenta." ".$e->getMessage());
        return $main_array;
      }
      return $main_array;
    }

    public function MeetOrCallValid($numDays, $idAccount, $nuevoAsesor)
    {
        /** Recupera Reuniones menores a ciertos dias segun el caso */
        $today = date("Y-m-d", strtotime("+1 days"));
        $lastDays = date('Y-m-d', strtotime("-{$numDays} days"));
        $callORmeeting = false;
        $queryMeetings = "select * from meetings where status='Held'
                          AND  assigned_user_id='{$nuevoAsesor}'
                          AND parent_id='{$idAccount}'
                          AND parent_type='Accounts'
                           AND CONVERT_TZ(date_entered,'+00:00',@@global.time_zone)  >= '{$lastDays}'
                          AND CONVERT_TZ(date_entered,'+00:00',@@global.time_zone)  <= '{$today}'
                          AND deleted=0";
        $queryCalls = "select * from calls where status='Held'
                          AND  assigned_user_id='{$nuevoAsesor}'
                          AND parent_id='{$idAccount}'
                          AND parent_type='Accounts'
                          AND CONVERT_TZ(date_entered,'+00:00',@@global.time_zone)  >= '{$lastDays}'
                          AND CONVERT_TZ(date_entered,'+00:00',@@global.time_zone)  <= '{$today}'
                          AND deleted=0";
        $resultM = $GLOBALS['db']->query($queryMeetings);
        $resultC = $GLOBALS['db']->query($queryCalls);
        if ($resultM->num_rows > 0 || $resultC->num_rows > 0) {
            $callORmeeting = true;
        }
        return $callORmeeting;
    }

    public function getAnexos($idCorto, $idProducto)
    {
        global $sugar_config;
        $host = $sugar_config['url_executeStoredCRM'];
        $url = $host . '/unifinMiddleware/middlewareUni2/executeStoredCRM';
        $timeout = 500;
        $error_report = FALSE;
        $headers = array(
            'Content-Type:application/json',
        );
        $data = json_encode(
            array(
                "idCliente" => $idCorto,
                "idProducto" => $idProducto
            )
        );
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
        $resultado=false;
        try {
            $response = curl_exec($curl);
            $GLOBALS['log']->fatal("respuesta servicio Anexo\n" . $response);
            //$GLOBALS['log']->fatal("respuesta servicio Anexo\n" . $response['respStore']);
            $responseArray= json_decode($response,true);
            $GLOBALS['log']->fatal("Respuesta response Anexos Areglos " .  print_r($responseArray,true));
            $GLOBALS['log']->fatal("Respuesta response Anexos valor " .  ($responseArray['respStore']==1 && $responseArray['respStore']!="")?'verdadero':'falso error');
            curl_close($curl);
            $resultado=($responseArray['respStore']==1 && $responseArray['respStore']!="")?true:false;
        } catch (Exception $ex) {
            $GLOBALS['log']->fatal("Error al " . $ex);
        }
        return $resultado;
    }

    public function grupoEmpresarial($idEmpresarial, $idCuenta, $idProducto)
    {
        $existeAtendido = false;
        $buscagrupo = "select a.id ,a.name, a.parent_id ,u.name,u.estatus_atencion,u.tipo_producto from accounts a
INNER JOIN accounts_uni_productos_1_c r
ON r.accounts_uni_productos_1accounts_ida=a.id
  INNER JOIN uni_productos u
ON u.id=r.accounts_uni_productos_1uni_productos_idb
  AND u.tipo_producto='{$idProducto}'
  AND u.estatus_atencion='1'
where (a.parent_id='{$idEmpresarial}' OR a.id='{$idEmpresarial}')
      and a.id!='{$idCuenta}'";
        $resultEmp = $GLOBALS['db']->query($buscagrupo);
        if ($resultEmp->num_rows > 0) {
            $existeAtendido = true;
        }
        return $existeAtendido;
    }

    public function SolicitudCredito($idCuenta)
    {
        $existeOpp = false;
        $getOpportunities = "select * from accounts_opportunities rel
INNER JOIN opportunities op
on op.id=rel.opportunity_id
INNER JOIN opportunities_cstm cstm
on cstm.id_c=op.id
where rel.account_id='{$idCuenta}'
      AND cstm.tct_etapa_ddw_c='C'";
        $resultOpp = $GLOBALS['db']->query($getOpportunities);
        if ($resultOpp->num_rows > 0) {
            $existeOpp = true;
        }
        return $existeOpp;
    }
    
    public function reasignaQuantico($listaCuentas)
    {
        // URL del endpoint
        global $sugar_config;
        $quanticoHost = $sugar_config['quantico_url_base'];
        $user = $sugar_config['quantico_usr'];
        $pwd = $sugar_config['quantico_psw'];
        $auth_encode = base64_encode($user . ':' . $pwd);
        $endpoint = $quanticoHost."/CreditRequestIntegration/rest/CreditRequestApi/UpdateAdviserCreditRequest";
        // Convertir los datos a formato JSON
        $jsonData = json_encode($listaCuentas);
        $GLOBALS['log']->fatal("Petición de endpoit: ".$endpoint);
        $GLOBALS['log']->fatal($jsonData);
        // Configurar la solicitud cURL
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type: application/json',
          'Authorization: Basic ' . $auth_encode
        ]);
        // Ejecutar la solicitud cURL y obtener la respuesta
        $response = curl_exec($ch);
        // Verificar si hay errores en la solicitud cURL
        if (curl_errno($ch)) {
          $GLOBALS['log']->fatal('Error al realizar la solicitud cURL: ' . curl_error($ch));
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        // Imprimir la respuesta de la API
        $GLOBALS['log']->fatal("Respuesta de endpoit: ".$endpoint);
        $GLOBALS['log']->fatal($response);
        return true;
    }
    
    public function getUsersDetail($listaUsuarios)
    {
        $usuarios = [];
        $queryU = "SELECT id_c, id_active_directory_c FROM users_cstm WHERE id_c IN ($listaUsuarios)";
        $resultU = $GLOBALS['db']->query($queryU);
        while ($row = $GLOBALS['db']->fetchByAssoc($resultU)) {
            $usuarios[$row['id_c']] = $row['id_active_directory_c'];
        }
        return $usuarios;
    }
}

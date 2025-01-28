<?php
/*/**
 * Created by Eduardo Carrasco Beltrán
 * Date: 11/07/2023
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class altaCuentaUnifin extends SugarApi
{

    public function registerApiRest()
    {
        return array(
            'altaCuentaUnifin' => array(
                'reqType' => 'POST',
                'path' => array('altaCuentaUnifin'),
                'pathVars' => array(''),
                'method' => 'altaCuenta',
                'shortHelp' => 'Consumo para dar de alta Cuentas de Creditaria',
            ),
        );
    }

	public function altaCuenta($api, $args) {
		global $db;
		$rfc = $args['rfc_c'];
		$regimen_fiscal_c = $args['regimen_fiscal_c'];
		if($regimen_fiscal_c == 3) $nombre = $args['nombre_empresa_c'];
		else $nombre = $args['nombre_c']." ".$args['apellido_paterno_c']." ".$args['apellido_materno_c'];
        require_once("custom/clients/base/api/cleanName.php");
        $apiCleanName = new cleanName();
        $body = array('name'=>$nombre);
        $response = $apiCleanName->getCleanName(null,$body);
        if($response['status']=='200') $cleanName = $response['cleanName'];
		$response = array();
		$idCuenta = '';
		try {
			if($rfc) $query = "select a.id from accounts a, accounts_cstm ac where a.id = ac.id_c and a.deleted = 0 and ac.rfc_c = '{$rfc}'";
			else $query = "select id from accounts where deleted = 0 and clean_name = '{$cleanName}'";
			$queryResult = $db->query($query);
			$idCuentas = $db->fetchByAssoc($queryResult);
			$idCuenta = $idCuentas['id'];
			if($idCuenta) {
				//Actualiza Cuenta
        $this->actualizaCuenta($idCuenta,$args);
				$response['statusCode']='200';
				$response['message']='Registro procesado de forma correcta';
				$response['id']=$idCuenta;
			}
			else {
				//Actualiza Lead
				if($rfc) $query = "select a.id from leads a, leads_cstm ac where a.id = ac.id_c and a.deleted = 0 and ac.rfc_c = '{$rfc}'";
				else $query = "select a.id from leads a, leads_cstm ac where a.id = ac.id_c and a.deleted = 0 and ac.clean_name_c = '{$cleanName}'";
				$queryResult = $db->query($query);
				$idLeads = $db->fetchByAssoc($queryResult);
				$idLead = $idLeads['id'];
				if($idLead) $beanLead = BeanFactory::retrieveBean('Leads', $idLead, array('disable_row_level_security' => true));
				else {
					$beanLead = BeanFactory::newBean('Leads');
					$beanLead->regimen_fiscal_c = $regimen_fiscal_c;
				}
				if($args['usuario']) {
					$beanLead->created_by = $args['usuario'];
					$beanLead->assigned_user_id = $args['usuario'];
				}
				if($args['nombre_empresa_c']) $beanLead->nombre_empresa_c = $args['nombre_empresa_c'];
				if($args['nombre_c']) $beanLead->nombre_c = $args['nombre_c'];
				if($args['apellido_paterno_c']) $beanLead->apellido_paterno_c = $args['apellido_paterno_c'];
				if($args['apellido_materno_c']) $beanLead->apellido_materno_c = $args['apellido_materno_c'];
				if($args['genero_c']) $beanLead->genero_c = $args['genero_c'];
				if($args['origen_c']) $beanLead->origen_c = $args['origen_c'];
				if($args['rfc_c']) $beanLead->rfc_c = $args['rfc_c'];
				if($args['email']) $beanLead->email1 = $args['email'];
				if($args['phone_work']) $beanLead->phone_work = $args['phone_work'];
				if($args['phone_home']) $beanLead->phone_home = $args['phone_home'];
				if($args['phone_mobile']) $beanLead->phone_mobile = $args['phone_mobile'];
				if($args['ventas_anuales_c']) $beanLead->ventas_anuales_c = $args['ventas_anuales_c'];
				$beanLead->save();
				$idLead = $beanLead->id;
				//Convierte Lead
				require_once("custom/clients/base/api/check_duplicateAccounts.php");
				$filter_arguments = array("id" => $idLead);
				$callApi = new check_duplicateAccounts();
				$convert = $callApi->validation_process(null,$filter_arguments);
				$idCuenta = $convert["idCuenta"];
        $this->actualizaCuenta($idCuenta,$args);
				if($idCuenta) {
					$response['statusCode']='200';
					$response['message']='Registro procesado de forma correcta';
					$response['id']=$idCuenta;
				}
				else {
					$response['statusCode']='400';
					$response['message']=$convert["mensaje"];;
				}
			}
        } catch (Exception $e) {
            $response['statusCode']='400';
            $response['message']=$e->getMessage();
        }
		return $response;
	}

  public function actualizaCuenta($idCuenta,$args){
      $beanCuenta = BeanFactory::retrieveBean('Accounts', $idCuenta, array('disable_row_level_security' => true));
      if($args['usuario']) {
        $beanCuenta->created_by = $args['usuario'];
        $beanCuenta->assigned_user_id = $args['usuario'];
      }
      if($args['nombre_empresa_c']) $beanCuenta->razonsocial_c = $args['nombre_empresa_c'];
      if($args['nombre_comercial_c']) $beanCuenta->nombre_comercial_c = $args['nombre_comercial_c'];
      if($args['nombre_c']) $beanCuenta->primernombre_c = $args['nombre_c'];
      if($args['apellido_paterno_c']) $beanCuenta->apellidopaterno_c = $args['apellido_paterno_c'];
      if($args['apellido_materno_c']) $beanCuenta->apellidomaterno_c = $args['apellido_materno_c'];
      if($args['fechadenacimiento_c']) $beanCuenta->fechadenacimiento_c = $args['fechadenacimiento_c'];
      if($args['genero_c']) $beanCuenta->genero_c = $args['genero_c'];
      if($args['origen_cuenta_c']) $beanCuenta->origen_cuenta_c = $args['origen_cuenta_c'];
      if($args['fechaconstitutiva_c']) $beanCuenta->fechaconstitutiva_c = $args['fechaconstitutiva_c'];
      if($args['rfc_c']) $beanCuenta->rfc_c = $args['rfc_c'];
      if($args['nacionalidad_c']) $beanCuenta->nacionalidad_c = $args['nacionalidad_c'];
      if($args['tct_pais_expide_rfc_c']) $beanCuenta->tct_pais_expide_rfc_c = $args['tct_pais_expide_rfc_c'];
      if($args['pais_nacimiento_c']) $beanCuenta->pais_nacimiento_c = $args['pais_nacimiento_c'];
      if($args['estado_nacimiento_c']) $beanCuenta->estado_nacimiento_c = $args['estado_nacimiento_c'];
      if($args['zonageografica_c']) $beanCuenta->zonageografica_c = $args['zonageografica_c'];
      if($args['estadocivil_c']) $beanCuenta->estadocivil_c = $args['estadocivil_c'];
      if($args['regimenpatrimonial_c']) $beanCuenta->regimenpatrimonial_c = $args['regimenpatrimonial_c'];
      if($args['email']) $beanCuenta->email1 = $args['email'];
      if($args['actividadeconomica_c']) $beanCuenta->actividadeconomica_c = $args['actividadeconomica_c'];
      if($args['ventas_anuales_c']) $beanCuenta->ventas_anuales_c = $args['ventas_anuales_c'];
      if($args['tct_ano_ventas_ddw_c']) $beanCuenta->tct_ano_ventas_ddw_c = $args['tct_ano_ventas_ddw_c'];
      if($args['activo_fijo_c']) $beanCuenta->activo_fijo_c = $args['activo_fijo_c'];
      if($args['promotorleasing_c']) $beanCuenta->user_id_c = $args['promotorleasing_c'];
      if($args['promotoruniclick_c']) $beanCuenta->user_id7_c = $args['promotoruniclick_c'];
      if($args['parent_name']) $beanCuenta->parent_id = $args['parent_name'];
      if($args['situacion_gpo_empresarial_c']) $beanCuenta->situacion_gpo_empresarial_c = $args['situacion_gpo_empresarial_c'];
      $beanCuenta->save();
  }
}

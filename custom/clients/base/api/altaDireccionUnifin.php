<?php
/**
 * Created by Eduardo Carrasco Beltrán
 * Date: 11/07/2023
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class altaDireccionUnifin extends SugarApi
{

	public function registerApiRest()
	{
		return array(
			'altaDireccionUnifin' => array(
				'reqType' => 'POST',
				'path' => array('altaDireccionUnifin'),
				'pathVars' => array(''),
				'method' => 'altaDireccion',
				'shortHelp' => 'Consumo para dar de alta Direcciones de Creditaria',
			),
		);
	}

	public function altaDireccion($api, $args)
	{
		try {
			global $db;
			global $app_list_strings;
			$response = array();
			$idCuenta = $args['accounts_dire_direccion_1accounts_ida'];
			$idSepomex = $args['dir_sepomex_dire_direcciondir_sepomex_ida'];
			$cp = $args['codigo_postal_c'];

			if ($idCuenta && !$idSepomex && !$cp) {
				$GLOBALS['log']->fatal("VALIDACION SOLO CUENTA");
				//Consulta Direcciones
				$query = "SELECT * from dir_sepomex_dire_direccion_c 
				where deleted = 0 and id in 
				(select accounts_dire_direccion_1dire_direccion_idb 
				from accounts_dire_direccion_1_c 
				where deleted = 0 
				and accounts_dire_direccion_1accounts_ida = '{$idCuenta}')";
				$queryResult = $db->query($query);
				$pos = 0;
				while ($row = $db->fetchByAssoc($queryResult)) {
					$response[$pos] = $row;
					$pos++;
				}
			}
			if(!$idCuenta && !$idSepomex && $cp) {
				$GLOBALS['log']->fatal("VALIDACION SOLO CP");
				//Consulta CP
				require_once("custom/clients/base/api/GetDireccionesCP.php");
				$apiDire = new GetDireccionesCP();
				$body = array('cp'=>$cp);
				$response = $apiDire->getAddressByCP(null,$body);
			}
			if ($idCuenta && ($idSepomex || $cp)) {
				$GLOBALS['log']->fatal("VALIDACION SOLO CUENTA Y ID_SEPOMEX");
				//Valida Dirección
				$error = 0;
				$duplicado = 0;
				$cDuplicado = 0;
				$cDireccionFiscal = 0;
				$cDireccionAdmin = 0;

				$query = "SELECT * from dir_sepomex_dire_direccion_c 
				where deleted = 0 and id in 
				(select accounts_dire_direccion_1dire_direccion_idb 
				from accounts_dire_direccion_1_c 
				where deleted = 0 
				and accounts_dire_direccion_1accounts_ida = '{$idCuenta}')";
				$queryResult = $db->query($query);

				if ($args['indicador']) $indicador = $app_list_strings['dir_Indicador_list'][$args['indicador']];

				while ($row = $db->fetchByAssoc($queryResult)) {
					$duplicado = 0;
					$beanDire = BeanFactory::retrieveBean('dire_Direccion', $row['id'], array('disable_row_level_security' => true));
					$indicadores = $app_list_strings['dir_Indicador_list'][$beanDire->indicador];
					if ($cp == $beanDire->dire_direccion_dire_codigopostal_name) $duplicado++;
					// if ($args['pais_c'] && $args['pais_c'] == $beanDire->dire_direccion_dire_paisdire_pais_ida) $duplicado++;
					// if ($args['estado_c'] && $args['estado_c'] == $beanDire->dire_direccion_dire_estadodire_estado_ida) $duplicado++;
					// if ($args['municipio_c'] && $args['municipio_c'] == $beanDire->dire_direccion_dire_municipiodire_municipio_ida) $duplicado++;
					// if ($args['ciudad_c'] && $args['ciudad_c'] == $beanDire->dire_direccion_dire_ciudaddire_ciudad_ida) $duplicado++;
					// if ($args['colonia_c'] && $args['colonia_c'] == $beanDire->dire_direccion_dire_coloniadire_colonia_ida) $duplicado++;
					if ($args['calle'] && strtoupper($args['calle']) == strtoupper($beanDire->calle)) $duplicado++;
					if ($args['numext'] && strtoupper($args['numext']) == strtoupper($beanDire->numext)) $duplicado++;
					if ($args['inactivo'] && $args['inactivo'] == $beanDire->inactivo) $duplicado++;
					if ($args['indicador'] && preg_match("/Fiscal/i", $indicador) && preg_match("/Fiscal/i", $indicadores) && !$beanDire->inactivo) $cDireccionFiscal++;
					if ($args['indicador'] && preg_match("/Administración/i", $indicador) && preg_match("/Administración/i", $indicadores) && !$beanDire->inactivo) $cDireccionAdmin++;
					if ($duplicado == 9) $cDuplicado++;
				}
				if ($cDuplicado >= 1) {
					$response['statusCode'] = '400';
					$response['message'] = 'La dirección ya existe, favor de validar.';
					$error = 1;
				}
				if ($cDireccionFiscal >= 1) {
					$response['statusCode'] = '400';
					$response['message'] = 'No se pueden agregar múltiples direcciones fiscales, favor de validar.';
					$error = 1;
				}
				if ($cDireccionAdmin >= 1) {
					$response['statusCode'] = '400';
					$response['message'] = 'No se pueden agregar múltiples direcciones de Administración, favor de validar.';
					$error = 1;
				}
				if (!$error) {
					//Crea Dirección
					if ($args['id']) {
						$beanDir = BeanFactory::retrieveBean('dire_Direccion', $args['id'], array('disable_row_level_security' => true));
					}
					if (!isset($beanDir->id)) {
						$beanDir = BeanFactory::newBean('dire_Direccion');
					}
					if ($args['usuario'] && !$args['id']) {
						$beanDir->created_by = $args['usuario'];
						$beanDir->assigned_user_id = $args['usuario'];
					}
					if ($cp != '') {
						$query = "select id from dire_codigopostal where name = '{$cp}'";
						$queryResult = $db->query($query);
						$row = $db->fetchByAssoc($queryResult);
						$beanDir->dire_direccion_dire_codigopostaldire_codigopostal_ida = $row['id'];
					}
					if ($args['tipodedireccion']) $beanDir->tipodedireccion = $args['tipodedireccion'];
					if ($args['indicador']) $beanDir->indicador = $args['indicador'];
					// if ($args['pais_c']) $beanDir->dire_direccion_dire_paisdire_pais_ida = $args['pais_c'];
					// if ($args['estado_c']) $beanDir->dire_direccion_dire_estadodire_estado_ida = $args['estado_c'];
					// if ($args['municipio_c']) $beanDir->dire_direccion_dire_municipiodire_municipio_ida = $args['municipio_c'];
					// if ($args['ciudad_c']) $beanDir->dire_direccion_dire_ciudaddire_ciudad_ida = $args['ciudad_c'];
					// if ($args['colonia_c']) $beanDir->dire_direccion_dire_coloniadire_colonia_ida = $args['colonia_c'];
					if ($args['calle']) $beanDir->calle = $args['calle'];
					if ($args['numext']) $beanDir->numext = $args['numext'];
					if ($args['numint']) $beanDir->numint = $args['numint'];
					if ($args['principal']) $beanDir->principal = $args['principal'];
					if ($args['inactivo']) $beanDir->inactivo = $args['inactivo'];
					if ($args['accounts_dire_direccion_1accounts_ida']) $beanDir->accounts_dire_direccion_1accounts_ida = $args['accounts_dire_direccion_1accounts_ida'];
					if ($args['dir_sepomex_dire_direcciondir_sepomex_ida']) $beanDir->dir_sepomex_dire_direcciondir_sepomex_ida = $args['dir_sepomex_dire_direcciondir_sepomex_ida'];
					$beanDir->save();
					$response['statusCode'] = '200';
					$response['message'] = 'Registro procesado de forma correcta';
					$response['id'] = $beanDir->id;
				}
			}
		} catch (Exception $e) {
			$response['statusCode'] = '400';
			$response['message'] = $e->getMessage();
		}
		return $response;
	}
}
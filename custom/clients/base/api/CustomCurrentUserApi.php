<?php

/*
    AF - 23/04/2018
    Extensión de output(campos custom) para CurrentUserApi

*/
require_once("clients/base/api/CurrentUserApi.php");

class CustomCurrentUserApi extends CurrentUserApi
{

    /**
     * Retrieves the current user info
     *
     * @param $api
     * @param $args
     * @return array
     */
    public function retrieveCurrentUser(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();
        global $sugar_config;

        $userArray = parent::retrieveCurrentUser($api, $args);
        $userArray['current_user']['cac_c'] = $current_user->cac_c;
        $userArray['current_user']['empresa_c'] = $current_user->empresa_c;
        $userArray['current_user']['equipo_c'] = $current_user->equipo_c;
        $userArray['current_user']['esexterno_c'] = $current_user->esexterno_c;
        $userArray['current_user']['iniciales_c'] = $current_user->iniciales_c;
        $userArray['current_user']['producto_c'] = $current_user->producto_c;
        $userArray['current_user']['puesto_c'] = $current_user->puesto_c;
        $userArray['current_user']['subpuesto_c'] = $current_user->subpuesto_c;
        $userArray['current_user']['puestousuario_c'] = $current_user->puestousuario_c;
        $userArray['current_user']['region_c'] = $current_user->region_c;
        $userArray['current_user']['tipodeproducto_c'] = $current_user->tipodeproducto_c;
        // Se agregan al arreglo de usuario los campos nuevos
        $userArray['current_user']['tct_altaproveedor_chk_c'] = $current_user->tct_altaproveedor_chk_c;
        $userArray['current_user']['tct_alta_clientes_chk_c'] = $current_user->tct_alta_clientes_chk_c;
        $userArray['current_user']['tct_alta_cd_chk_c'] = $current_user->tct_alta_cd_chk_c;
        $userArray['current_user']['ext_c'] = $current_user->ext_c;
        $userArray['current_user']['productos_c'] = $current_user->productos_c;
        $userArray['current_user']['tct_propietario_real_chk_c'] = $current_user->tct_propietario_real_chk_c;
        $userArray['current_user']['tct_alta_credito_simple_chk_c'] = $current_user->tct_alta_credito_simple_chk_c;
        $userArray['current_user']['tct_vetar_usuarios_chk_c'] = $current_user->tct_vetar_usuarios_chk_c;
        $userArray['current_user']['tct_no_contactar_chk_c'] = $current_user->tct_no_contactar_chk_c;
        $userArray['current_user']['agente_telefonico_c'] = $current_user->agente_telefonico_c;
        $userArray['current_user']['deudor_factoraje_c'] = $current_user->deudor_factoraje_c;
		$userArray['current_user']['id_active_directory_c'] = $current_user->id_active_directory_c;
        $userArray['current_user']['cuenta_especial_c'] = $current_user->cuenta_especial_c;
        $userArray['current_user']['depurar_leads_c'] = $current_user->depurar_leads_c;
        $userArray['current_user']['tct_cancelar_ref_cruzada_chk_c'] = $current_user->tct_cancelar_ref_cruzada_chk_c;
        $userArray['current_user']['valida_vta_cruzada_c'] = $current_user->valida_vta_cruzada_c;
        $userArray['current_user']['multilinea_c'] = $current_user->multilinea_c;
        $userArray['current_user']['responsable_oficina_chk_c'] = $current_user->responsable_oficina_chk_c;
        $userArray['current_user']['excluir_precalifica_c'] = $current_user->excluir_precalifica_c;
        $userArray['current_user']['admin_cartera_c'] = $current_user->admin_cartera_c;
        $userArray['current_user']['config_admin_cartera'] = $sugar_config['service_admin_cartera'];
        $userArray['current_user']['access_hours_c'] = $current_user->access_hours_c;
        $userArray['current_user']['reset_leadcancel_c'] = $current_user->reset_leadcancel_c;
    	$userArray['current_user']['tct_id_uni2_txf_c'] = $current_user->tct_id_uni2_txf_c;
    	$userArray['current_user']['bloqueo_credito_c'] = $current_user->bloqueo_credito_c;
    	$userArray['current_user']['bloqueo_cumple_c'] = $current_user->bloqueo_cumple_c;
        $userArray['current_user']['tct_id_uni2_txf_c'] = $current_user->tct_id_uni2_txf_c;
        $userArray['current_user']['posicion_operativa_c'] = $current_user->posicion_operativa_c;
        $userArray['current_user']['limite_asignacion_lm_c'] = $current_user->limite_asignacion_lm_c;
        $userArray['current_user']['gestion_lm_c'] = $current_user->gestion_lm_c;
        $userArray['current_user']['portal_proveedores_c'] = $current_user->portal_proveedores_c;
        $userArray['current_user']['editar_backlog_chk_c'] = $current_user->editar_backlog_chk_c;
        $userArray['current_user']['bloqueo_cuentas_c'] = $current_user->bloqueo_cuentas_c;
        $userArray['current_user']['admin_backlog_c'] = $current_user->admin_backlog_c;
        $userArray['current_user']['excluye_valida_c'] = $current_user->excluye_valida_c;
        $userArray['current_user']['llamada_genesys_c'] = $current_user->llamada_genesys_c;
        $userArray['current_user']['equipos_c'] = $current_user->equipos_c;
		$userArray['current_user']['lenia_c'] = $current_user->lenia_c;
        $userArray['current_user']['habilita_envio_tc_c'] = $current_user->habilita_envio_tc_c;
        $userArray['current_user']['admin_seguros_c'] = $current_user->admin_seguros_c;
        $userArray['current_user']['seguimiento_seguros_c'] = $current_user->seguimiento_seguros_c;
        $userArray['current_user']['reasignacion_po_c'] = $current_user->reasignacion_po_c;
        $userArray['current_user']['asignacion_po_c'] = $current_user->asignacion_po_c;
        $userArray['current_user']['cancelar_casos_c'] = $current_user->cancelar_casos_c;
        $userArray['current_user']['seguimiento_bc_c'] = $current_user->seguimiento_bc_c;
        $userArray['current_user']['define_origen_po_c'] = $current_user->define_origen_po_c;
        return $userArray;
    }
}

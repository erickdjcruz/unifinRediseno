<?php
// created: 2024-05-20 10:34:39
$viewdefs['Accounts']['base']['view']['record'] = array (
  'buttons' => 
  array (
    0 => 
    array (
      'type' => 'button',
      'name' => 'bloquea_cuenta',
      'label' => 'Bloquear Cuenta',
      'css_class' => 'btn-danger hidden',
      'events' => 
      array (
        'click' => 'button:bloquea_cuenta:click',
      ),
    ),
    1 => 
    array (
      'type' => 'button',
      'name' => 'desbloquea_cuenta',
      'label' => 'Desbloquear Cuenta',
      'css_class' => 'btn-success hidden',
      'events' => 
      array (
        'click' => 'button:desbloquea_cuenta:click',
      ),
    ),
    2 => 
    array (
      'type' => 'button',
      'name' => 'aprobar_noviable',
      'label' => 'Confirmar Bloqueo',
      'css_class' => 'btn-success hidden',
      'events' => 
      array (
        'click' => 'button:aprobar_noviable:click',
      ),
    ),
    3 => 
    array (
      'type' => 'button',
      'name' => 'desaprobar_noviable',
      'label' => 'Rechazar Bloqueo',
      'css_class' => 'btn-danger hidden',
      'events' => 
      array (
        'click' => 'button:desaprobar_noviable:click',
      ),
    ),
    4 => 
    array (
      'type' => 'button',
      'name' => 'reactivar_noviable',
      'label' => 'Reactivar Cuenta',
      'css_class' => 'btn-success hidden',
      'events' => 
      array (
        'click' => 'button:reactivar_noviable:click',
      ),
    ),
    5 => 
    array (
      'type' => 'button',
      'name' => 'cancel_button',
      'label' => 'LBL_CANCEL_BUTTON_LABEL',
      'css_class' => 'btn-invisible btn-link',
      'showOn' => 'edit',
    ),
    6 => 
    array (
      'type' => 'button',
      'name' => 'rfc_qr_button',
      'label' => 'Leer CSF',
      'css_class' => 'btn_rfc_qr btn btn-primary',
      'showOn' => 'edit',
      'events' => 
      array (
        'click' => 'button:btn_rfc:click',
      ),
    ),
    7 => 
    array (
      'type' => 'rowaction',
      'event' => 'button:save_button:click',
      'name' => 'save_button',
      'label' => 'LBL_SAVE_BUTTON_LABEL',
      'css_class' => 'btn btn-primary',
      'showOn' => 'edit',
      'acl_action' => 'edit',
    ),
    8 => 
    array (
      'type' => 'actiondropdown',
      'name' => 'main_dropdown',
      'primary' => true,
      'css_class' => 'noEdit',
      'showOn' => 'view',
      'buttons' => 
      array (
        0 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:edit_button:click',
          'name' => 'edit_button',
          'label' => 'LBL_EDIT_BUTTON_LABEL',
          'acl_action' => 'edit',
        ),
        1 => 
        array (
          'type' => 'shareaction',
          'name' => 'share',
          'label' => 'LBL_RECORD_SHARE_BUTTON',
          'acl_action' => 'view',
        ),
        2 => 
        array (
          'type' => 'divider',
        ),
        3 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:verificar_cambios:click',
          'name' => 'verificar_cambios',
          'label' => 'Verificar cambios',
          'acl_action' => 'view',
        ),
        4 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:historical_summary_button:click',
          'name' => 'historical_summary_button',
          'label' => 'LBL_HISTORICAL_SUMMARY',
          'acl_action' => 'view',
        ),
        5 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:audit_button:click',
          'name' => 'audit_button',
          'label' => 'LNK_VIEW_CHANGE_LOG',
          'acl_action' => 'view',
        ),
        6 => 
        array (
          'name' => 'alta_po',
          'type' => 'rowaction',
          'label' => 'Alta de PO',
          'event' => 'button:alta_po:click',
        ),
        7 => 
        array (
          'type' => 'divider',
        ),
        8 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:dynamics_button:click',
          'name' => 'dynamics_365',
          'label' => 'Enviar a Dynamics 365',
          'acl_action' => 'view',
        ),
        9 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:cotizador_button:click',
          'name' => 'cotizador',
          'label' => 'Cotizador',
          'acl_action' => 'view',
        ),
        10 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:expediente_button:click',
          'name' => 'expediente',
          'label' => 'Expediente',
          'acl_action' => 'view',
        ),
        11 => 
        array (
          'type' => 'rowaction',
          'event' => 'button:convierte_lead:click',
          'name' => 'conviertelead',
          'label' => 'Convertir a Prospecto',
          'acl_action' => 'view',
          'class' => 'jcmx1',
        ),
        12 => 
        array (
          'name' => 'send_survey',
          'type' => 'rowaction',
          'label' => 'Send Survey',
          'acl_action' => 'send_survey',
          'event' => 'button:send_survey:click',
        ),
        13 => 
        array (
          'name' => 'send_poll',
          'type' => 'rowaction',
          'label' => 'Send Poll',
          'acl_action' => 'send_poll',
          'event' => 'button:send_poll:click',
        ),
        14 => 
        array (
          'name' => 'get_account_asesor',
          'type' => 'rowaction',
          'label' => 'Quiero esta Cuenta',
          'event' => 'button:get_account_asesor:click',
        ),
        15 => 
        array (
          'name' => 'send_account_asesor',
          'type' => 'rowaction',
          'label' => 'Enviar Cuenta a',
          'event' => 'button:send_account_asesor:click',
        ),
        16 => 
        array (
          'name' => 'negociador_quantico',
          'type' => 'rowaction',
          'label' => 'Negociador Quantico',
          'event' => 'button:open_negociador_quantico:click',
        ),
        17 => 
        array (
          'name' => 'portal_proveedores',
          'type' => 'rowaction',
          'label' => 'Enviar al portal de proveedor',
          'event' => 'button:enviar_portal_proveedores:click',
        ),
        18 => 
        array (
          'name' => 'proveedor_quantico',
          'type' => 'rowaction',
          'label' => 'Tarea IE Proveedor Quantico',
          'event' => 'button:proveedor_quantico:click',
        ),
        19 => 
        array (
          'name' => 'solicitar_ciec',
          'type' => 'rowaction',
          'label' => 'Solicitar CIEC',
          'event' => 'button:solicitar_ciec:click',
        ),
      ),
    ),
    9 => 
    array (
      'name' => 'sidebar_toggle',
      'type' => 'sidebartoggle',
    ),
  ),
  'panels' => 
  array (
    0 => 
    array (
      'name' => 'panel_header',
      'label' => 'LBL_PANEL_HEADER',
      'header' => true,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'picture',
          'type' => 'avatar',
          'size' => 'large',
          'dismiss_label' => true,
          'readonly' => true,
        ),
        1 => 
        array (
          'name' => 'name',
        ),
      ),
    ),
    1 => 
    array (
      'newTab' => true,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL8',
      'label' => 'LBL_RECORDVIEW_PANEL8',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_vista360',
          'label' => 'Vista 360',
          'studio' => 'visible',
          'span' => 12,
        ),
      ),
    ),
    2 => 
    array (
      'newTab' => false,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL26',
      'label' => 'LBL_RECORDVIEW_PANEL26',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'readonly' => false,
          'name' => 'regimen_capital_c',
          'label' => 'LBL_REGIMEN_CAPITAL',
        ),
        1 => 
        array (
        ),
        2 => 
        array (
          'readonly' => false,
          'name' => 'valid_cambio_razon_social_c',
          'label' => '',
          'css_class' => 'hidden',
        ),
        3 => 
        array (
          'readonly' => false,
          'name' => 'json_audit_c',
          'studio' => 'visible',
          'label' => 'LBL_JSON_AUDIT',
        ),
        4 => 
        array (
          'readonly' => false,
          'name' => 'cambio_nombre_c',
          'label' => 'LBL_CAMBIO_NOMBRE',
        ),
        5 => 
        array (
          'readonly' => false,
          'name' => 'cambio_dirfiscal_c',
          'label' => 'LBL_CAMBIO_DIRFISCAL',
        ),
        6 => 
        array (
          'readonly' => false,
          'name' => 'enviar_mensaje_c',
          'label' => 'LBL_ENVIAR_MENSAJE',
        ),
        7 => 
        array (
          'readonly' => false,
          'name' => 'omitir_caso',
          'label' => 'LBL_OMITIR_CASO',
          'studio' => 'visible',
          'dismiss_label' => true,
        ),
        8 => 
        array (
          'readonly' => false,
          'name' => 'usr_aprueba_rechaza_c',
          'studio' => 'visible',
          'label' => 'LBL_USR_APRUEBA_RECHAZA',
        ),
        9 => 
        array (
          'readonly' => false,
          'name' => 'fecha_aprueba_rechaza_c',
          'label' => 'LBL_FECHA_APRUEBA_RECHAZA',
        ),
        10 => 
        array (
          'readonly' => false,
          'name' => 'json_direccion_audit_c',
          'studio' => 'visible',
          'label' => 'LBL_JSON_DIRECCION_AUDIT',
        ),
        11 => 
        array (
          'readonly' => false,
          'name' => 'direccion_actualizada_api_c',
          'label' => 'LBL_DIRECCION_ACTUALIZADA_API',
        ),
        12 => 
        array (
          'readonly' => false,
          'name' => 'regimenes_fiscal_sat_c',
          'studio' => 'visible',
          'label' => 'LBL_REGIMENES_FISCAL_SAT',
          'span' => 12,
        ),
        13 => 
        array (
          'name' => 'geocode_status',
          'licenseFilter' => 
          array (
            0 => 'MAPS',
          ),
        ),
      ),
    ),
    3 => 
    array (
      'name' => 'panel_body',
      'label' => 'LBL_RECORD_BODY',
      'columns' => 3,
      'labelsOnTop' => true,
      'placeholders' => true,
      'newTab' => true,
      'panelDefault' => 'expanded',
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'control_dynamics_365_c',
          'label' => 'LBL_CONTROL_DYNAMICS_365_C',
        ),
        1 => 
        array (
          'name' => 'id_cpp_365_chk_c',
          'label' => 'LBL_ID_CPP_365_CHK',
        ),
        2 => 
        array (
          'name' => 'account_tipoSubtipo',
          'studio' => 'visible',
          'label' => 'LBL_ACCOUNT_TIPOSUBTIPO',
          'readonly' => true,
          'dismiss_label' => true,
          'span' => 12,
        ),
        3 => 
        array (
          'name' => 'rfc_qr',
          'label' => 'LBL_RFC_QR',
          'dismiss_label' => true,
          'studio' => 'visible',
          'span' => 12,
        ),
        4 => 
        array (
          'name' => 'path_img_qr_c',
          'label' => 'LBL_PATH_IMG_QR',
          'span' => 12,
        ),
        5 => 
        array (
          'name' => 'nivel_digitalizacion_c',
          'label' => 'LBL_NIVEL_DIGITALIZACION_C',
        ),
        6 => 
        array (
          'name' => 'tipo_registro_cuenta_c',
          'label' => 'LBL_TIPO_REGISTRO_CUENTA',
        ),
        7 => 
        array (
          'name' => 'subtipo_registro_cuenta_c',
          'label' => 'LBL_SUBTIPO_REGISTRO_CUENTA',
        ),
        8 => 
        array (
          'name' => 'tct_prioridad_ddw_c',
        ),
        9 => 
        array (
          'name' => 'tct_homonimo_chk_c',
          'label' => 'LBL_TCT_HOMONIMO_CHK',
        ),
        10 => 
        array (
          'name' => 'blank_space',
          'label' => 'LBL_BLANK_SPACE',
        ),
        11 => 
        array (
          'name' => 'esproveedor_c',
          'label' => 'LBL_ESPROVEEDOR',
        ),
        12 => 
        array (
          'name' => 'cedente_factor_c',
          'label' => 'LBL_CEDENTE_FACTOR',
        ),
        13 => 
        array (
          'name' => 'deudor_factor_c',
          'label' => 'LBL_DEUDOR_FACTOR',
        ),
        14 => 
        array (
          'name' => 'tct_no_contactar_chk_c',
          'label' => 'LBL_TCT_NO_CONTACTAR_CHK',
        ),
      ),
    ),
    4 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL17',
      'label' => 'LBL_RECORDVIEW_PANEL17',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'cuenta_productos',
          'label' => 'LBL_CUENTA_PRODUCTOS',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
      ),
    ),
    5 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL19',
      'label' => 'LBL_RECORDVIEW_PANEL19',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_uni_productos',
          'studio' => 'visible',
          'label' => 'LBL_ACCOUNT_UNI_PRODUTOS',
          'dismiss_label' => true,
          'span' => 12,
        ),
      ),
    ),
    6 => 
    array (
      'newTab' => false,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL25',
      'label' => 'LBL_RECORDVIEW_PANEL25',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_disposiciones',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
      ),
    ),
    7 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL16',
      'label' => 'LBL_RECORDVIEW_PANEL16',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'readonly' => true,
          'name' => 'compania_account_c',
          'label' => 'LBL_COMPANIA_ACCOUNT',
          'span' => 12,
        ),
        1 => 
        array (
          'readonly' => false,
          'name' => 'fecha_bloqueo_origen_c',
          'label' => 'LBL_FECHA_BLOQUEO_ORIGEN',
        ),
        2 => 
        array (
        ),
        3 => 
        array (
          'name' => 'origen_cuenta_c',
          'studio' => 'visible',
          'label' => 'LBL_ORIGEN_CUENTA_C',
        ),
        4 => 
        array (
          'name' => 'detalle_origen_c',
          'label' => 'LBL_DETALLE_ORIGEN_C',
        ),
        5 => 
        array (
          'name' => 'prospeccion_propia_c',
          'label' => 'LBL_PROSPECCION_PROPIA_C',
          'span' => 12,
        ),
        6 => 
        array (
          'name' => 'referenciador_c',
          'studio' => 'visible',
          'label' => 'LBL_REFERENCIADOR',
          'span' => 12,
        ),
        7 => 
        array (
          'name' => 'referido_cliente_prov_c',
          'studio' => 'visible',
          'label' => 'LBL_REFERIDO_CLIENTE_PROV',
          'span' => 12,
        ),
        8 => 
        array (
          'name' => 'referenciado_agencia_c',
          'label' => 'LBL_REFERENCIADO_AGENCIA',
          'span' => 12,
        ),
        9 => 
        array (
          'name' => 'tct_referenciado_dir_rel_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_REFERENCIADO_DIR_REL',
          'span' => 12,
        ),
        10 => 
        array (
          'name' => 'tct_origen_busqueda_txf_c',
          'label' => 'LBL_TCT_ORIGEN_BUSQUEDA_TXF',
        ),
        11 => 
        array (
          'name' => 'tct_origen_base_ddw_c',
          'label' => 'LBL_TCT_ORIGEN_BASE_DDW',
        ),
        12 => 
        array (
          'name' => 'medio_detalle_origen_c',
          'label' => 'LBL_MEDIO_DETALLE_ORIGEN_C',
        ),
        13 => 
        array (
          'name' => 'punto_contacto_origen_c',
          'label' => 'LBL_PUNTO_CONTACTO_ORIGEN_C',
        ),
        14 => 
        array (
          'name' => 'evento_c',
          'label' => 'LBL_EVENTO',
        ),
        15 => 
        array (
        ),
        16 => 
        array (
          'name' => 'camara_c',
          'label' => 'LBL_CAMARA',
        ),
        17 => 
        array (
        ),
        18 => 
        array (
          'name' => 'como_se_entero_c',
          'label' => 'LBL_COMO_SE_ENTERO',
        ),
        19 => 
        array (
          'name' => 'cual_c',
          'label' => 'LBL_CUAL',
        ),
        20 => 
        array (
          'name' => 'tct_origen_ag_tel_rel_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_ORIGEN_AG_TEL_REL',
          'initial_filter' => 'filterAgentesTelefonicosTemplate',
          'initial_filter_label' => 'LBL_FILTER_USER_BY_PUESTO',
          'filter_populate' => 
          array (
            'puestousuario_c' => 
            array (
              0 => '27',
              1 => '31',
            ),
          ),
        ),
        21 => 
        array (
          'name' => 'tct_que_promotor_rel_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_QUE_PROMOTOR_REL',
        ),
        22 => 
        array (
          'readonly' => false,
          'name' => 'codigo_expo_c',
          'label' => 'LBL_CODIGO_EXPO',
        ),
        23 => 
        array (
          'name' => 'alianza_soc_chk_c',
          'label' => 'LBL_ALIANZA_SOC_CHK',
        ),
      ),
    ),
    8 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL11',
      'label' => 'LBL_RECORDVIEW_PANEL11',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'id_uniclick_c',
          'label' => 'LBL_ID_UNICLICK',
        ),
        1 => 
        array (
          'name' => 'estatus_persona_c',
          'label' => 'LBL_ESTATUS_PERSONA',
        ),
        2 => 
        array (
          'name' => 'tipodepersona_c',
          'studio' => 'visible',
          'label' => 'LBL_TIPODEPERSONA',
        ),
        3 => 
        array (
          'name' => 'primernombre_c',
          'label' => 'LBL_PRIMERNOMBRE',
        ),
        4 => 
        array (
          'name' => 'apellidopaterno_c',
          'label' => 'LBL_APELLIDOPATERNO',
        ),
        5 => 
        array (
          'name' => 'apellidomaterno_c',
          'label' => 'LBL_APELLIDOMATERNO',
        ),
        6 => 
        array (
          'name' => 'denominacion_c',
          'label' => 'LBL_DENOMINACION',
          'span' => 12,
        ),
        7 => 
        array (
          'name' => 'razonsocial_c',
          'label' => 'LBL_RAZONSOCIAL',
          'span' => 12,
        ),
        8 => 
        array (
          'name' => 'nombre_comercial_c',
          'label' => 'LBL_NOMBRE_COMERCIAL',
          'span' => 12,
        ),
        9 => 
        array (
          'name' => 'account_regimenes_fiscal',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
        10 => 
        array (
          'name' => 'fechadenacimiento_c',
          'label' => 'LBL_FECHADENACIMIENTO',
        ),
        11 => 
        array (
          'name' => 'genero_c',
          'studio' => 'visible',
          'label' => 'LBL_GENERO',
        ),
        12 => 
        array (
          'name' => 'parent_name',
        ),
        13 => 
        array (
          'name' => 'fechaconstitutiva_c',
          'label' => 'LBL_FECHACONSTITUTIVA',
        ),
        14 => 
        array (
          'readonly' => false,
          'name' => 'situacion_gpo_empresarial_c',
          'label' => 'LBL_SITUACION_GPO_EMPRESARIAL',
        ),
        15 => 
        array (
          'readonly' => false,
          'name' => 'situacion_gpo_empresa_txt_c',
          'studio' => 'visible',
          'label' => 'LBL_SITUACION_GPO_EMPRESA_TXT_C',
        ),
        16 => 
        array (
          'name' => 'rfc_c',
          'label' => 'LBL_RFC',
        ),
        17 => 
        array (
          'type' => 'button',
          'name' => 'generar_rfc_c',
          'label' => 'LBL_GENERAR_RFC',
        ),
        18 => 
        array (
          'name' => 'nacionalidad_c',
          'label' => 'LBL_NACIONALIDAD',
        ),
        19 => 
        array (
          'name' => 'tct_pais_expide_rfc_c',
          'label' => 'LBL_TCT_PAIS_EXPIDE_RFC',
        ),
        20 => 
        array (
          'name' => 'pais_nacimiento_c',
          'studio' => 'visible',
          'label' => 'LBL_PAIS_NACIMIENTO',
        ),
        21 => 
        array (
          'name' => 'estado_nacimiento_c',
          'studio' => 'visible',
          'label' => 'LBL_ESTADO_NACIMIENTO',
        ),
        22 => 
        array (
          'name' => 'account_paises_estados',
          'studio' => 'visible',
          'label' => ' ',
          'span' => 12,
        ),
        23 => 
        array (
          'name' => 'zonageografica_c',
          'studio' => 'visible',
          'label' => 'LBL_ZONAGEOGRAFICA',
        ),
        24 => 
        array (
          'name' => 'ifepasaporte_c',
          'label' => 'LBL_IFEPASAPORTE',
        ),
        25 => 
        array (
          'name' => 'curp_c',
          'label' => 'LBL_CURP',
        ),
        26 => 
        array (
          'type' => 'button',
          'name' => 'generar_curp_c',
          'label' => 'LBL_GENERAR_CURP',
        ),
        27 => 
        array (
          'name' => 'estadocivil_c',
          'studio' => 'visible',
          'label' => 'LBL_ESTADOCIVIL',
        ),
        28 => 
        array (
          'name' => 'regimenpatrimonial_c',
          'studio' => 'visible',
          'label' => 'LBL_REGIMENPATRIMONIAL',
        ),
        29 => 
        array (
          'name' => 'profesion_c',
          'studio' => 'visible',
          'label' => 'LBL_PROFESION',
        ),
        30 => 
        array (
          'name' => 'puesto_cuenta_c',
          'label' => 'LBL_PUESTO_CUENTA_C',
        ),
        31 => 
        array (
          'name' => 'email',
          'span' => 12,
        ),
        32 => 
        array (
          'name' => 'referenciabancaria_c',
          'label' => 'LBL_REFERENCIABANCARIA',
        ),
        33 => 
        array (
        ),
        34 => 
        array (
          'name' => 'tipo_relacion_c',
          'label' => 'LBL_TIPO_RELACION',
          'span' => 12,
        ),
        35 => 
        array (
          'name' => 'reus_c',
          'label' => 'LBL_REUS',
        ),
        36 => 
        array (
          'name' => 'referencia_bancaria_c',
          'label' => 'LBL_REFERENCIA_BANCARIA_C',
        ),
        37 => 
        array (
          'name' => 'tct_prospecto_contactado_chk_c',
          'label' => 'LBL_TCT_PROSPECTO_CONTACTADO_CHK',
        ),
        38 => 
        array (
          'name' => 'show_panel_c',
          'label' => 'LBL_SHOW_PANEL',
        ),
        39 => 
        array (
          'name' => 'apoderado_nombre_c',
          'label' => 'LBL_APODERADO_NOMBRE_C',
          'span' => 12,
        ),
        40 => 
        array (
          'name' => 'apoderado_apaterno_c',
          'label' => 'LBL_APODERADO_APATERNO_C',
        ),
        41 => 
        array (
          'name' => 'apoderado_amaterno_c',
          'label' => 'LBL_APODERADO_AMATERNO_C',
        ),
        42 => 
        array (
          'name' => 'cuenta_especial_c',
          'label' => 'LBL_CUENTA_ESPECIAL',
          'span' => 12,
        ),
        43 => 
        array (
          'name' => 'alta_portal_proveedor_chk_c',
          'label' => 'LBL_ALTA_PORTAL_PROVEEDOR_CHK',
          'css_class' => 'hidden',
        ),
        44 => 
        array (
        ),
      ),
    ),
    9 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL23',
      'label' => 'LBL_RECORDVIEW_PANEL23',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 'website',
        1 => 
        array (
          'name' => 'no_website_c',
          'label' => 'LBL_NO_WEBSITE',
        ),
        2 => 
        array (
          'name' => 'facebook',
          'comment' => 'The facebook name of the company',
          'label' => 'LBL_FACEBOOK',
        ),
        3 => 
        array (
          'name' => 'linkedin_c',
          'label' => 'LBL_LINKEDIN',
        ),
        4 => 
        array (
          'name' => 'twitter',
        ),
        5 => 
        array (
        ),
      ),
    ),
    10 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL22',
      'label' => 'LBL_RECORDVIEW_PANEL22',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'tipo_proveedor_compras_c',
          'label' => 'LBL_TIPO_PROVEEDOR_COMPRAS',
        ),
        1 => 
        array (
          'readonly' => false,
          'name' => 'vendor_c',
          'label' => 'LBL_VENDOR',
        ),
        2 => 
        array (
          'name' => 'tipo_proveedor_c',
          'label' => 'LBL_TIPO_PROVEEDOR',
        ),
        3 => 
        array (
          'readonly' => false,
          'name' => 'codigo_vendor_c',
          'label' => 'LBL_CODIGO_VENDOR_C',
        ),
        4 => 
        array (
          'name' => 'alta_proveedor_c',
          'label' => 'LBL_ALTA_PROVEEDOR_C',
        ),
        5 => 
        array (
        ),
        6 => 
        array (
          'name' => 'iva_c',
          'label' => 'LBL_IVA',
        ),
        7 => 
        array (
          'name' => 'es_referenciador_c',
          'label' => 'LBL_ES_REFERENCIADOR_C',
        ),
      ),
    ),
    11 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL21',
      'label' => 'LBL_RECORDVIEW_PANEL21',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_clasf_sectorial',
          'studio' => 'visible',
          'label' => 'LBL_ACCOUNT_CLASF_SECTORIAL',
          'span' => 12,
        ),
        1 => 
        array (
          'name' => 'tct_macro_sector_ddw_c',
          'label' => 'LBL_TCT_MACRO_SECTOR_DDW',
        ),
        2 => 
        array (
          'name' => 'sectoreconomico_c',
          'label' => 'LBL_SECTORECONOMICO',
        ),
        3 => 
        array (
          'name' => 'subsectoreconomico_c',
          'studio' => 'visible',
          'label' => 'LBL_SUBSECTORECONOMICO',
        ),
        4 => 
        array (
          'name' => 'actividadeconomica_c',
          'studio' => 'visible',
          'label' => 'LBL_ACTIVIDADECONOMICA',
        ),
      ),
    ),
    12 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL12',
      'label' => 'LBL_RECORDVIEW_PANEL12',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'ventas_anuales_c',
          'label' => 'LBL_VENTAS_ANUALES',
        ),
        1 => 
        array (
          'name' => 'tct_ano_ventas_ddw_c',
          'label' => 'LBL_TCT_ANO_VENTAS_DDW',
        ),
        2 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'activo_fijo_c',
          'label' => 'LBL_ACTIVO_FIJO',
        ),
        3 => 
        array (
          'readonly' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'potencial_cuenta_c',
          'label' => 'LBL_POTENCIAL_CUENTA',
        ),
        4 => 
        array (
          'name' => 'total_empleados_c',
          'label' => 'LBL_TOTAL_EMPLEADOS_C',
          'readonly' => true,
        ),
        5 => 
        array (
          'name' => 'empleados_c',
          'label' => 'LBL_EMPLEADOS',
        ),
        6 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'tct_depositos_promedio_c',
          'label' => 'LBL_TCT_DEPOSITOS_PROMEDIO_C',
          'span' => 12,
        ),
        7 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'tct_prom_cheques_cur_c',
          'label' => 'LBL_TCT_PROM_CHEQUES_CUR_C',
        ),
        8 => 
        array (
          'name' => 'dates_account_statements',
          'studio' => 'visible',
          'label' => 'LBL_TCT_DATES_ACCOUNT_STATEMENTS_C',
          'type' => 'dates_account_statements',
        ),
        9 => 
        array (
          'name' => 'tct_dates_acc_statements_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_DATES_ACC_STATEMENTS_C',
          'span' => 12,
        ),
        10 => 
        array (
          'name' => 'potencial_autos',
          'studio' => 'visible',
          'label' => 'LBL_POTENCIAL_AUTOS',
          'span' => 12,
        ),
      ),
    ),
    13 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL13',
      'label' => 'LBL_RECORDVIEW_PANEL13',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'promotorleasing_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORLEASING',
          'initial_filter' => 'filterPromotorTemplate',
          'initial_filter_label' => 'LBL_FILTER_PROMOTOR_TEMPLATE',
          'filter_populate' => 
          array (
            'tipodeproducto_c' => '1',
          ),
        ),
        1 => 
        array (
          'name' => 'promotorfactoraje_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORFACTORAJE',
          'initial_filter' => 'filterPromotorTemplate',
          'initial_filter_label' => 'LBL_FILTER_PROMOTOR_TEMPLATE',
          'filter_populate' => 
          array (
            'tipodeproducto_c' => '4',
          ),
        ),
        2 => 
        array (
          'name' => 'promotorcredit_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORCREDIT',
          'initial_filter' => 'filterPromotorTemplate',
          'initial_filter_label' => 'LBL_FILTER_PROMOTOR_TEMPLATE',
          'filter_populate' => 
          array (
            'tipodeproducto_c' => '3',
          ),
        ),
        3 => 
        array (
          'name' => 'promotorfleet_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORFLEET',
          'initial_filter' => 'filterPromotorTemplate',
          'initial_filter_label' => 'LBL_FILTER_PROMOTOR_TEMPLATE',
          'filter_populate' => 
          array (
            'tipodeproducto_c' => '6',
          ),
        ),
        4 => 
        array (
          'name' => 'promotoruniclick_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORUNICLICK_C',
        ),
        5 => 
        array (
          'name' => 'promotorrm_c',
          'studio' => 'visible',
          'label' => 'LBL_PROMOTORRM_C',
          'initial_filter' => 'filterPromotorTemplate',
          'initial_filter_label' => 'LBL_FILTER_PROMOTOR_TEMPLATE',
          'filter_populate' => 
          array (
            'tipodeproducto_c' => '11',
          ),
        ),
      ),
    ),
    14 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL10',
      'label' => 'LBL_RECORDVIEW_PANEL10',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'nivel_satisfaccion_c',
          'label' => 'LBL_NIVEL_SATISFACCION',
        ),
        1 => 
        array (
          'name' => 'fecha_leasing_c',
          'label' => 'LBL_FECHA_LEASING',
        ),
        2 => 
        array (
          'name' => 'comenta_leasing_c',
          'studio' => 'visible',
          'label' => 'LBL_COMENTA_LEASING',
          'span' => 12,
        ),
        3 => 
        array (
          'name' => 'nivel_satisfaccion_factoring_c',
          'label' => 'LBL_NIVEL_SATISFACCION_FACTORING',
        ),
        4 => 
        array (
          'name' => 'fecha_factoraje_c',
          'label' => 'LBL_FECHA_FACTORAJE',
        ),
        5 => 
        array (
          'name' => 'comenta_factoraje_c',
          'studio' => 'visible',
          'label' => 'LBL_COMENTA_FACTORAJE',
          'span' => 12,
        ),
        6 => 
        array (
          'name' => 'nivel_satisfaccion_ca_c',
          'label' => 'LBL_NIVEL_SATISFACCION_CA',
        ),
        7 => 
        array (
          'name' => 'fecha_ca_c',
          'label' => 'LBL_FECHA_CA',
        ),
        8 => 
        array (
          'name' => 'comenta_ca_c',
          'studio' => 'visible',
          'label' => 'LBL_COMENTA_CA',
          'span' => 12,
        ),
      ),
    ),
    15 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL14',
      'label' => 'LBL_RECORDVIEW_PANEL14',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_telefonos',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
      ),
    ),
    16 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL15',
      'label' => 'LBL_RECORDVIEW_PANEL15',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_direcciones',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
      ),
    ),
    17 => 
    array (
      'newTab' => false,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL27',
      'label' => 'LBL_RECORDVIEW_PANEL27',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'account_direccion_buro_credito',
          'studio' => 'visible',
          'label' => ' ',
          'span' => 12,
        ),
      ),
    ),
    18 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL18',
      'label' => 'LBL_RECORDVIEW_PANEL18',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'accounts_analizate',
          'studio' => 'visible',
          'label' => 'LBL_ACCOUNTS_ANALIZATE',
          'span' => 12,
        ),
      ),
    ),
    19 => 
    array (
      'newTab' => false,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL24',
      'label' => 'LBL_RECORDVIEW_PANEL24',
      'columns' => 2,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'readonly' => false,
          'name' => 'emision_csf_c',
          'label' => 'LBL_EMISION_CSF',
          'span' => 12,
        ),
        1 => 
        array (
          'name' => 'accounts_analizate_clientes',
          'studio' => 'visible',
          'label' => 'LBL_ACCOUNTS_ANALIZATE_CLIENTES',
          'span' => 12,
        ),
      ),
    ),
    20 => 
    array (
      'newTab' => true,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL1',
      'label' => 'LBL_RECORDVIEW_PANEL1',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'ctpldnoseriefiel_c',
          'label' => 'LBL_CTPLDNOSERIEFIEL',
          'span' => 12,
        ),
        1 => 
        array (
          'name' => 'tct_cpld_pregunta_u1_ddw_c',
          'label' => 'LBL_TCT_CPLD_PREGUNTA_U1_DDW',
        ),
        2 => 
        array (
          'name' => 'tct_cpld_pregunta_u2_txf_c',
          'label' => 'LBL_TCT_CPLD_PREGUNTA_U2_TXF',
        ),
        3 => 
        array (
          'name' => 'tct_cpld_pregunta_u3_ddw_c',
          'label' => 'LBL_TCT_CPLD_PREGUNTA_U3_DDW',
        ),
        4 => 
        array (
          'name' => 'tct_cpld_pregunta_u4_txf_c',
          'label' => 'LBL_TCT_CPLD_PREGUNTA_U4_TXF',
        ),
        5 => 
        array (
          'name' => 'tct_fedeicomiso_chk_c',
          'label' => 'LBL_TCT_FEDEICOMISO_CHK',
          'span' => 12,
        ),
        6 => 
        array (
          'name' => 'accounts_tct_pld',
          'studio' => 'visible',
          'dismiss_label' => true,
          'span' => 12,
        ),
        7 => 
        array (
          'name' => 'tct_nuevo_pld_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_NUEVO_PLD_C',
          'span' => 12,
        ),
      ),
    ),
    21 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL4',
      'label' => 'LBL_RECORDVIEW_PANEL4',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'ctpldfuncionespublicas_c',
          'label' => 'LBL_CTPLDFUNCIONESPUBLICAS',
        ),
        1 => 
        array (
          'name' => 'ctpldfuncionespublicascargo_c',
          'label' => 'LBL_CTPLDFUNCIONESPUBLICASCARGO',
        ),
        2 => 
        array (
          'name' => 'tct_dependencia_pf_c',
          'label' => 'LBL_TCT_DEPENDENCIA_PF',
        ),
        3 => 
        array (
          'name' => 'tct_periodo_pf1_c',
          'label' => 'LBL_TCT_PERIODO_PF1',
        ),
        4 => 
        array (
          'name' => 'tct_fecha_ini_pf_c',
          'label' => 'LBL_TCT_FECHA_INI_PF',
        ),
        5 => 
        array (
          'name' => 'tct_fecha_fin_pf_c',
          'label' => 'LBL_TCT_FECHA_FIN_PF',
        ),
      ),
    ),
    22 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL5',
      'label' => 'LBL_RECORDVIEW_PANEL5',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'ctpldconyuge_c',
          'label' => 'LBL_CTPLDCONYUGE',
        ),
        1 => 
        array (
          'name' => 'ctpldconyugecargo_c',
          'label' => 'LBL_CTPLDCONYUGECARGO',
        ),
        2 => 
        array (
          'name' => 'tct_nombre_pf_peps_c',
          'label' => 'LBL_TCT_NOMBRE_PF_PEPS',
        ),
        3 => 
        array (
          'name' => 'tct_cargo2_pf_c',
          'label' => 'LBL_TCT_CARGO2_PF',
        ),
        4 => 
        array (
          'name' => 'tct_dependencia2_pf_c',
          'label' => 'LBL_TCT_DEPENDENCIA2_PF',
        ),
        5 => 
        array (
          'name' => 'tct_periodo2_pf_c',
          'label' => 'LBL_TCT_PERIODO2_PF',
        ),
        6 => 
        array (
          'name' => 'tct_fecha_ini2_pf_c',
          'label' => 'LBL_TCT_FECHA_INI2_PF',
        ),
        7 => 
        array (
          'name' => 'tct_fecha_fin2_pf_c',
          'label' => 'LBL_TCT_FECHA_FIN2_PF',
        ),
      ),
    ),
    23 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL7',
      'label' => 'LBL_RECORDVIEW_PANEL7',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'ctpldaccionistasconyuge_c',
          'label' => 'LBL_CTPLDACCIONISTASCONYUGE',
        ),
        1 => 
        array (
          'name' => 'tct_socio2_pm_c',
          'label' => 'LBL_TCT_SOCIO2_PM',
        ),
        2 => 
        array (
          'name' => 'ctpldaccionistasconyugecargo_c',
          'label' => 'LBL_CTPLDACCIONISTASCONYUGECARGO',
        ),
        3 => 
        array (
          'name' => 'tct_nombre_pm_c',
          'label' => 'LBL_TCT_NOMBRE_PM',
        ),
        4 => 
        array (
          'name' => 'tct_cargo_pm_c',
          'label' => 'LBL_TCT_CARGO_PM',
        ),
        5 => 
        array (
          'name' => 'tct_dependencia2_pm_c',
          'label' => 'LBL_TCT_DEPENDENCIA2_PM_C',
        ),
        6 => 
        array (
          'name' => 'tct_periodo2_pm_c',
          'label' => 'LBL_TCT_PERIODO2_PM',
          'span' => 12,
        ),
        7 => 
        array (
          'name' => 'tct_fecha_ini2_pm_c',
          'label' => 'LBL_TCT_FECHA_INI2_PM',
        ),
        8 => 
        array (
          'name' => 'tct_fecha_fin2_pm_c',
          'label' => 'LBL_TCT_FECHA_FIN2_PM',
        ),
      ),
    ),
    24 => 
    array (
      'newTab' => false,
      'panelDefault' => 'collapsed',
      'name' => 'LBL_RECORDVIEW_PANEL6',
      'label' => 'LBL_RECORDVIEW_PANEL6',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'ctpldaccionistas_c',
          'label' => 'LBL_CTPLDACCIONISTAS',
        ),
        1 => 
        array (
          'name' => 'tct_socio_pm_c',
          'label' => 'LBL_TCT_SOCIO_PM',
        ),
        2 => 
        array (
          'name' => 'ctpldaccionistascargo_c',
          'label' => 'LBL_CTPLDACCIONISTASCARGO',
        ),
        3 => 
        array (
          'name' => 'tct_dependencia_pm_c',
          'label' => 'LBL_TCT_DEPENDENCIA_PM',
        ),
        4 => 
        array (
          'name' => 'tct_periodo_pm_c',
          'label' => 'LBL_TCT_PERIODO_PM',
          'span' => 12,
        ),
        5 => 
        array (
          'name' => 'tct_fecha_ini_pm_c',
          'label' => 'LBL_TCT_FECHA_INI_PM',
        ),
        6 => 
        array (
          'name' => 'tct_fecha_fin_pm_c',
          'label' => 'LBL_TCT_FECHA_FIN_PM',
        ),
      ),
    ),
    25 => 
    array (
      'newTab' => true,
      'panelDefault' => 'expanded',
      'name' => 'LBL_RECORDVIEW_PANEL2',
      'label' => 'LBL_RECORDVIEW_PANEL2',
      'columns' => 2,
      'labelsOnTop' => 1,
      'placeholders' => 1,
      'fields' => 
      array (
        0 => 
        array (
          'name' => 'tct_fedeicomiso_c1_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C1_TXF',
        ),
        1 => 
        array (
          'name' => 'tct_fedeicomiso_c2_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C2_TXF',
        ),
        2 => 
        array (
          'name' => 'tct_fedeicomiso_c3_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C3_TXF',
        ),
        3 => 
        array (
          'name' => 'tct_fedeicomiso_c4_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C4_TXF',
        ),
        4 => 
        array (
          'name' => 'tct_fedeicomiso_c5_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C5_TXF',
        ),
        5 => 
        array (
          'name' => 'tct_fedeicomiso_c6_dat_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C6_DAT',
        ),
        6 => 
        array (
          'name' => 'tct_fedeicomiso_c7_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C7_TXF',
        ),
        7 => 
        array (
          'name' => 'tct_fedeicomiso_c8_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C8_TXF',
        ),
        8 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'tct_fedeicomiso_c9_cur_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C9_CUR',
        ),
        9 => 
        array (
          'name' => 'tct_fedeicomiso_c10_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C10_DDW',
        ),
        10 => 
        array (
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'name' => 'tct_fedeicomiso_c12_cur_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C12_CUR',
        ),
        11 => 
        array (
          'name' => 'tct_fedeicomiso_c13_int_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C13_INT',
        ),
        12 => 
        array (
          'name' => 'tct_fedeicomiso_c14_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C14_DDW',
        ),
        13 => 
        array (
          'name' => 'tct_fedeicomiso_c14_2_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C14_2_TXF',
        ),
        14 => 
        array (
          'name' => 'tct_fedeicomiso_c15_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C15_DDW',
          'span' => 12,
        ),
        15 => 
        array (
          'name' => 'tct_fedeicomiso_c16_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C16_DDW',
        ),
        16 => 
        array (
          'name' => 'tct_fedeicomiso_c16_2_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C16_2_TXF',
        ),
        17 => 
        array (
          'name' => 'tct_fedeicomiso_c17_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C17_DDW',
        ),
        18 => 
        array (
          'name' => 'tct_fedeicomiso_c17_2_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C17_2_TXF',
        ),
        19 => 
        array (
          'name' => 'tct_fedeicomiso_c18_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C18_DDW',
        ),
        20 => 
        array (
          'name' => 'tct_fedeicomiso_c18_2_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C18_2_TXF',
        ),
        21 => 
        array (
          'name' => 'tct_fedeicomiso_c19_txf_c',
          'label' => 'LBL_TCT_FEDEICOMISO_C19_TXF',
          'span' => 12,
        ),
        22 => 
        array (
          'name' => 'tct_fedeicomiso_c20_msl_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C20_MSL',
        ),
        23 => 
        array (
          'name' => 'tct_fedeicomiso_c21_ddw_c',
          'studio' => 'visible',
          'label' => 'LBL_TCT_FEDEICOMISO_C21_DDW',
        ),
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'useTabs' => true,
  ),
);
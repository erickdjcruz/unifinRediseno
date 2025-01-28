<?php
$viewdefs['Users'] = 
array (
  'base' => 
  array (
    'view' => 
    array (
      'record' => 
      array (
        'buttons' => 
        array (
          0 => 
          array (
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => 
            array (
              'click' => 'button:cancel_button:click',
            ),
          ),
          1 => 
          array (
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
          ),
          2 => 
          array (
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
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
                'type' => 'pdfaction',
                'name' => 'download-pdf',
                'label' => 'LBL_PDF_VIEW',
                'action' => 'download',
                'acl_action' => 'view',
              ),
              3 => 
              array (
                'type' => 'pdfaction',
                'name' => 'email-pdf',
                'label' => 'LBL_PDF_EMAIL',
                'action' => 'email',
                'acl_action' => 'view',
              ),
              4 => 
              array (
                'type' => 'divider',
              ),
              5 => 
              array (
                'type' => 'manage-subscription',
                'name' => 'manage_subscription_button',
                'label' => 'LBL_MANAGE_SUBSCRIPTIONS',
                'showOn' => 'view',
                'value' => 'edit',
              ),
              6 => 
              array (
                'type' => 'vcard',
                'name' => 'vcard_button',
                'label' => 'LBL_VCARD_DOWNLOAD',
                'acl_action' => 'edit',
              ),
              7 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:reset_preferences:click',
                'name' => 'reset_preferences',
                'label' => 'LBL_RESET_PREFERENCES',
                'acl_action' => 'edit',
              ),
              8 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:reset_password:click',
                'name' => 'reset_password',
                'label' => 'LBL_PASSWORD_USER_RESET',
                'acl_module' => 'Users',
                'acl_action' => 'admin',
              ),
              9 => 
              array (
                'type' => 'divider',
              ),
              10 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:historical_summary_button:click',
                'name' => 'historical_summary_button',
                'label' => 'LBL_HISTORICAL_SUMMARY',
                'acl_action' => 'view',
              ),
              11 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:duplicate_button:click',
                'name' => 'duplicate_button',
                'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                'acl_module' => 'Users',
                'acl_action' => 'create',
              ),
              12 => 
              array (
                'type' => 'divider',
              ),
              13 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:delete_button:click',
                'name' => 'delete_button',
                'label' => 'LBL_DELETE_BUTTON_LABEL',
                'acl_module' => 'Users',
                'acl_action' => 'delete',
              ),
            ),
          ),
          3 => 
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
            'header' => true,
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'picture',
                'type' => 'avatar',
                'size' => 'large',
                'dismiss_label' => true,
              ),
              1 => 
              array (
                'name' => 'full_name',
                'type' => 'fullname',
                'fields' => 
                array (
                  0 => 'first_name',
                  1 => 'last_name',
                ),
                'dismiss_label' => true,
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'panel_body',
            'label' => 'LBL_USER_INFORMATION',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => 
            array (
              0 => 'status',
              1 => 
              array (
                'name' => 'license_type',
                'type' => 'enum',
                'required' => true,
              ),
              2 => 
              array (
                'name' => 'email',
                'span' => 12,
              ),
              3 => 'user_name',
              4 => 'email_link_type',
              5 => 
              array (
                'name' => 'mail_credentials',
                'type' => 'email-credentials',
                'span' => 12,
              ),
              6 => 
              array (
                'name' => 'address',
                'type' => 'fieldset',
                'css_class' => 'address',
                'label' => 'LBL_ADDRESS',
                'idm_mode_disabled' => true,
                'fields' => 
                array (
                  0 => 
                  array (
                    'name' => 'address_street',
                    'css_class' => 'address_street',
                    'placeholder' => 'LBL_ADDRESS_STREET',
                  ),
                  1 => 
                  array (
                    'name' => 'address_city',
                    'css_class' => 'address_city',
                    'placeholder' => 'LBL_ADDRESS_CITY',
                  ),
                  2 => 
                  array (
                    'name' => 'address_state',
                    'css_class' => 'address_state',
                    'placeholder' => 'LBL_ADDRESS_STATE',
                  ),
                  3 => 
                  array (
                    'name' => 'address_postalcode',
                    'css_class' => 'address_zip',
                    'placeholder' => 'LBL_ADDRESS_POSTALCODE',
                  ),
                  4 => 
                  array (
                    'name' => 'address_country',
                    'css_class' => 'address_country',
                    'placeholder' => 'LBL_ADDRESS_COUNTRY',
                  ),
                ),
                'span' => 12,
              ),
              7 => 
              array (
                'name' => 'is_admin',
                'label' => 'LBL_USER_TYPE',
                'type' => 'user-type',
                'options' => 'user_type_bool_dom',
                'optionInfo' => 
                array (
                  0 => 'LBL_REGULAR_DESC',
                  1 => 'LBL_ADMIN_DESC',
                ),
              ),
              8 => 
              array (
              ),
            ),
          ),
          2 => 
          array (
            'columns' => 2,
            'name' => 'panel_body_2',
            'label' => 'LBL_EMPLOYEE_INFORMATION',
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => 
            array (
              0 => 'reports_to_name',
              1 => 'phone_mobile',
              2 => 
              array (
                'name' => 'business_center_name',
                'span' => 12,
              ),
              3 => 'description',
              4 => 
              array (
                'readonly' => false,
                'name' => 'iniciales_c',
                'label' => 'LBL_INICIALES',
              ),
              5 => 
              array (
                'readonly' => false,
                'name' => 'fecha_baja_c',
                'label' => 'LBL_FECHA_BAJA',
              ),
              6 => 
              array (
                'name' => 'obj_objetivos_users_name',
                'label' => 'LBL_OBJ_OBJETIVOS_USERS_FROM_OBJ_OBJETIVOS_TITLE',
              ),
              7 => 
              array (
                'readonly' => false,
                'name' => 'ext_c',
                'label' => 'LBL_EXT',
              ),
              8 => 
              array (
                'readonly' => false,
                'name' => 'puestousuario_c',
                'label' => 'LBL_PUESTOUSUARIO',
              ),
              9 => 
              array (
                'readonly' => false,
                'name' => 'subpuesto_c',
                'label' => 'LBL_SUBPUESTO',
              ),
              10 => 
              array (
                'readonly' => false,
                'name' => 'no_empleado_c',
                'label' => 'LBL_NO_EMPLEADO',
              ),
              11 => 
              array (
                'readonly' => false,
                'name' => 'region_c',
                'label' => 'LBL_REGION',
              ),
              12 => 
              array (
                'readonly' => false,
                'name' => 'tct_team_address_txf_c',
                'label' => 'LBL_TCT_TEAM_ADDRESS_TXF_C',
              ),
              13 => 
              array (
                'readonly' => false,
                'name' => 'equipo_c',
                'label' => 'LBL_EQUIPO',
              ),
              14 => 
              array (
                'readonly' => false,
                'name' => 'equipos_c',
                'label' => 'LBL_EQUIPOS_C',
              ),
              15 => 
              array (
                'readonly' => false,
                'name' => 'tipodeproducto_c',
                'label' => 'LBL_TIPODEPRODUCTO',
              ),
              16 => 
              array (
                'readonly' => false,
                'name' => 'productos_c',
                'label' => 'LBL_PRODUCTOS',
              ),
              17 => 
              array (
                'readonly' => false,
                'name' => 'tct_id_uni2_txf_c',
                'label' => 'LBL_TCT_ID_UNI2_TXF',
              ),
              18 => 
              array (
                'readonly' => false,
                'name' => 'tct_id_unics_txf_c',
                'label' => 'LBL_TCT_ID_UNICS_TXF',
              ),
              19 => 
              array (
                'readonly' => false,
                'name' => 'id_active_directory_c',
                'label' => 'LBL_ID_ACTIVE_DIRECTORY_C',
              ),
              20 => 
              array (
                'readonly' => false,
                'name' => 'tct_altaproveedor_chk_c',
                'label' => 'LBL_TCT_ALTAPROVEEDOR_CHK',
              ),
              21 => 
              array (
                'readonly' => false,
                'name' => 'tct_alta_clientes_chk_c',
                'label' => 'LBL_TCT_ALTA_CLIENTES_CHK',
              ),
              22 => 
              array (
                'readonly' => false,
                'name' => 'tct_alta_cd_chk_c',
                'label' => 'LBL_TCT_ALTA_CD_CHK_C',
              ),
              23 => 
              array (
                'readonly' => false,
                'name' => 'cac_c',
                'label' => 'LBL_CAC',
              ),
              24 => 
              array (
                'readonly' => false,
                'name' => 'optout_c',
                'label' => 'LBL_OPTOUT',
              ),
              25 => 
              array (
                'readonly' => false,
                'name' => 'aut_caratulariesgo_c',
                'label' => 'LBL_AUT_CARATULARIESGO',
              ),
              26 => 
              array (
                'readonly' => false,
                'name' => 'tct_propietario_real_chk_c',
                'label' => 'LBL_TCT_PROPIETARIO_REAL_CHK',
              ),
              27 => 
              array (
                'readonly' => false,
                'name' => 'tct_vetar_usuarios_chk_c',
                'label' => 'LBL_TCT_VETAR_USUARIOS_CHK_C',
              ),
              28 => 
              array (
                'readonly' => false,
                'name' => 'tct_alta_credito_simple_chk_c',
                'label' => 'LBL_TCT_ALTA_CREDITO_SIMPLE_CHK',
              ),
              29 => 
              array (
                'readonly' => false,
                'name' => 'deudor_factoraje_c',
                'label' => 'LBL_DEUDOR_FACTORAJE',
              ),
              30 => 
              array (
                'readonly' => false,
                'name' => 'agente_telefonico_c',
                'label' => 'LBL_AGENTE_TELEFONICO',
              ),
              31 => 
              array (
                'readonly' => false,
                'name' => 'depurar_leads_c',
                'label' => 'LBL_DEPURAR_LEADS',
              ),
              32 => 
              array (
                'readonly' => false,
                'name' => 'cuenta_especial_c',
                'label' => 'LBL_CUENTA_ESPECIAL',
              ),
              33 => 
              array (
                'readonly' => false,
                'name' => 'responsable_oficina_chk_c',
                'label' => 'LBL_RESPONSABLE_OFICINA_CHK',
              ),
              34 => 
              array (
                'readonly' => false,
                'name' => 'notifica_fiscal_c',
                'label' => 'LBL_NOTIFICA_FISCAL',
              ),
              35 => 
              array (
                'readonly' => false,
                'name' => 'admin_cartera_c',
                'label' => 'LBL_ADMIN_CARTERA_C',
              ),
              36 => 
              array (
                'readonly' => false,
                'name' => 'excluir_precalifica_c',
                'label' => 'LBL_EXCLUIR_PRECALIFICA',
              ),
              37 => 
              array (
                'readonly' => false,
                'name' => 'multilinea_c',
                'label' => 'LBL_MULTILINEA_C',
              ),
              38 => 
              array (
                'readonly' => false,
                'name' => 'reset_leadcancel_c',
                'label' => 'LBL_RESET_LEADCANCEL_C',
              ),
              39 => 
              array (
                'readonly' => false,
                'name' => 'valida_vta_cruzada_c',
                'label' => 'LBL_VALIDA_VTA_CRUZADA',
              ),
              40 => 
              array (
                'readonly' => false,
                'name' => 'tct_cancelar_ref_cruzada_chk_c',
                'label' => 'LBL_TCT_CANCELAR_REF_CRUZADA_CHK',
              ),
              41 => 
              array (
                'readonly' => false,
                'name' => 'tct_no_contactar_chk_c',
                'label' => 'LBL_TCT_NO_CONTACTAR_CHK',
              ),
              42 => 
              array (
                'readonly' => false,
                'name' => 'bloqueo_credito_c',
                'label' => 'LBL_BLOQUEO_CREDITO_C',
              ),
              43 => 
              array (
                'readonly' => false,
                'name' => 'bloqueo_cumple_c',
                'label' => 'LBL_BLOQUEO_CUMPLE_C',
              ),
              44 => 
              array (
                'readonly' => false,
                'name' => 'bloqueo_cuentas_c',
                'label' => 'LBL_BLOQUEO_CUENTAS',
              ),
              45 => 
              array (
                'readonly' => false,
                'name' => 'portal_proveedores_c',
                'label' => 'LBL_PORTAL_PROVEEDORES',
              ),
              46 => 
              array (
                'readonly' => false,
                'name' => 'editar_backlog_chk_c',
                'label' => 'LBL_EDITAR_BACKLOG_CHK',
              ),
              47 => 
              array (
                'readonly' => false,
                'name' => 'admin_backlog_c',
                'label' => 'LBL_ADMIN_BACKLOG',
              ),
              48 => 
              array (
                'readonly' => false,
                'name' => 'excluye_valida_c',
                'label' => 'LBL_EXCLUYE_VALIDA',
              ),
              49 => 
              array (
                'readonly' => false,
                'name' => 'lenia_c',
                'label' => 'LBL_LENIA',
              ),
              50 => 
              array (
                'readonly' => false,
                'name' => 'habilita_envio_tc_c',
                'label' => 'LBL_HABILITA_ENVIO_TC',
              ),
              51 => 
              array (
                'readonly' => false,
                'name' => 'admin_seguros_c',
                'label' => 'LBL_ADMIN_SEGUROS',
              ),
              52 => 
              array (
                'readonly' => false,
                'name' => 'seguimiento_seguros_c',
                'label' => 'LBL_SEGUIMIENTO_SEGUROS',
              ),
              53 => 
              array (
                'readonly' => false,
                'name' => 'reasignacion_po_c',
                'label' => 'LBL_REASIGNACION_PO',
              ),
              54 => 
              array (
                'readonly' => false,
                'name' => 'asignacion_po_c',
                'label' => 'LBL_ASIGNACION_PO',
              ),
              55 => 
              array (
                'readonly' => false,
                'name' => 'cancelar_casos_c',
                'label' => 'LBL_CANCELAR_CASOS',
              ),
              56 => 
              array (
                'readonly' => false,
                'name' => 'seguimiento_bc_c',
                'label' => 'LBL_SEGUIMIENTO_BC',
              ),
              57 => 
              array (
                'readonly' => false,
                'name' => 'vacaciones_inicio_c',
                'label' => 'LBL_VACACIONES_INICIO',
              ),
              58 => 
              array (
                'readonly' => false,
                'name' => 'vacaciones_fin_c',
                'label' => 'LBL_VACACIONES_FIN',
              ),
              59 => 
              array (
                'readonly' => false,
                'name' => 'vacaciones_detalle_c',
                'studio' => 'visible',
                'label' => 'LBL_VACACIONES_DETALLE',
              ),
              60 => 
              array (
                'readonly' => false,
                'name' => 'nombre_completo_c',
                'label' => 'LBL_NOMBRE_COMPLETO_C',
              ),
              61 => 
              array (
                'readonly' => false,
                'name' => 'posicion_operativa_c',
                'label' => 'LBL_POSICION_OPERATIVA',
              ),
              62 => 
              array (
                'readonly' => false,
                'name' => 'limite_asignacion_lm_c',
                'label' => 'LBL_LIMITE_ASIGNACION_LM',
              ),
              63 => 
              array (
                'readonly' => false,
                'name' => 'contraseniaactual_c',
                'label' => 'LBL_CONTRASENIAACTUAL_C',
              ),
              64 => 
              array (
                'readonly' => false,
                'name' => 'nuevacontrasenia_c',
                'label' => 'LBL_NUEVACONTRASENIA_C',
              ),
              65 => 
              array (
                'readonly' => false,
                'name' => 'confirmarnuevacontrasenia_c',
                'label' => 'LBL_CONFIRMARNUEVACONTRASENIA_C',
              ),
              66 => 
              array (
                'readonly' => false,
                'name' => 'gestion_lm_c',
                'label' => 'LBL_GESTION_LM',
              ),
              67 => 
              array (
                'readonly' => false,
                'name' => 'mfa_enable_c',
                'label' => 'LBL_MFA_ENABLE',
              ),
              68 => 
              array (
                'readonly' => false,
                'name' => 'define_origen_po_c',
                'label' => 'LBL_DEFINE_ORIGEN_PO',
              ),
            ),
          ),
        ),
        'templateMeta' => 
        array (
          'maxColumns' => '2',
          'useTabs' => true,
        ),
      ),
    ),
  ),
);

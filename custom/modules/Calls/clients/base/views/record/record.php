<?php
$viewdefs['Calls'] = 
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
            'type' => 'actiondropdown',
            'name' => 'save_dropdown',
            'primary' => true,
            'switch_on_click' => true,
            'showOn' => 'edit',
            'buttons' => 
            array (
              0 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:save_button:click',
                'name' => 'save_button',
                'label' => 'LBL_SAVE_BUTTON_LABEL',
                'css_class' => 'btn btn-primary',
                'acl_action' => 'edit',
              ),
            ),
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
                'type' => 'editrecurrencesbutton',
                'event' => 'button:edit_recurrence_button:click',
                'name' => 'edit_recurrence_button',
                'label' => 'LBL_EDIT_ALL_RECURRENCES',
                'acl_action' => 'edit',
              ),
              2 => 
              array (
                'type' => 'shareaction',
                'name' => 'share',
                'label' => 'LBL_RECORD_SHARE_BUTTON',
                'acl_action' => 'view',
              ),
              3 => 
              array (
                'type' => 'pdfaction',
                'name' => 'download-pdf',
                'label' => 'LBL_PDF_VIEW',
                'action' => 'download',
                'acl_action' => 'view',
              ),
              4 => 
              array (
                'type' => 'pdfaction',
                'name' => 'email-pdf',
                'label' => 'LBL_PDF_EMAIL',
                'action' => 'email',
                'acl_action' => 'view',
              ),
              5 => 
              array (
                'type' => 'divider',
              ),
              8 => 
              array (
                'type' => 'rowaction',
                'event' => 'button:delete_button:click',
                'name' => 'delete_button',
                'label' => 'LBL_DELETE_BUTTON_LABEL',
                'acl_action' => 'delete',
              ),
              9 => 
              array (
                'type' => 'deleterecurrencesbutton',
                'name' => 'delete_recurrence_button',
                'label' => 'LBL_REMOVE_ALL_RECURRENCES',
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
                'readonly' => true,
              ),
              1 => 'name',
              2 => 
              array (
                'name' => 'favorite',
                'label' => 'LBL_FAVORITE',
                'type' => 'favorite',
                'readonly' => true,
                'dismiss_label' => true,
              ),
              3 => 
              array (
                'name' => 'follow',
                'label' => 'LBL_FOLLOW',
                'type' => 'follow',
                'readonly' => true,
                'dismiss_label' => true,
              ),
              4 => 
              array (
                'name' => 'status',
                'type' => 'event-status',
                'enum_width' => 'auto',
                'dropdown_width' => 'auto',
                'dropdown_class' => 'select2-menu-only',
                'container_class' => 'select2-menu-only',
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'tct_conferencia_chk_c',
                'label' => 'LBL_TCT_CONFERENCIA_CHK',
              ),
              1 => 
              array (
              ),
              2 => 
              array (
                'name' => 'duration',
                'type' => 'duration',
                'label' => 'LBL_START_AND_END_DATE_DETAIL_VIEW',
                'dismiss_label' => true,
                'inline' => true,
                'show_child_labels' => true,
                'fields' => 
                array (
                  0 => 
                  array (
                    'name' => 'date_start',
                    'time' => 
                    array (
                      'step' => 15,
                    ),
                    'readonly' => false,
                  ),
                  1 => 
                  array (
                    'type' => 'label',
                    'default_value' => 'LBL_START_AND_END_DATE_TO',
                  ),
                  2 => 
                  array (
                    'name' => 'date_end',
                    'time' => 
                    array (
                      'step' => 15,
                      'duration' => 
                      array (
                        'relative_to' => 'date_start',
                      ),
                    ),
                    'readonly' => false,
                  ),
                ),
                'related_fields' => 
                array (
                  0 => 'duration_hours',
                  1 => 'duration_minutes',
                ),
                'span' => 9,
              ),
              3 => 
              array (
                'name' => 'repeat_type',
                'related_fields' => 
                array (
                  0 => 'repeat_parent_id',
                  1 => 'rset',
                ),
                'span' => 3,
              ),
              4 => 
              array (
                'name' => 'recurrence',
                'type' => 'recurrence',
                'inline' => true,
                'show_child_labels' => true,
                'fields' => 
                array (
                  0 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT_INTERVAL',
                    'name' => 'repeat_interval',
                    'type' => 'enum',
                    'options' => 'repeat_interval_number',
                    'required' => true,
                    'default' => 1,
                  ),
                  1 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT_ON',
                    'name' => 'repeat_dow',
                    'type' => 'repeat-dow',
                    'options' => 'dom_cal_day_of_week',
                    'isMultiSelect' => true,
                  ),
                  2 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT_ON',
                    'name' => 'repeat_selector',
                    'type' => 'enum',
                    'options' => 'repeat_selector_dom',
                    'default' => 'None',
                  ),
                  3 => 
                  array (
                    'label' => ' ',
                    'name' => 'repeat_days',
                    'type' => 'repeat-days',
                    'options' => 
                    array (
                      '' => '',
                    ),
                    'isMultiSelect' => true,
                    'dropdown_class' => 'recurring-date-dropdown',
                    'container_class' => 'recurring-date-container select2-choices-pills-close',
                  ),
                  4 => 
                  array (
                    'label' => ' ',
                    'name' => 'repeat_ordinal',
                    'type' => 'enum',
                    'options' => 'repeat_ordinal_dom',
                  ),
                  5 => 
                  array (
                    'label' => ' ',
                    'name' => 'repeat_unit',
                    'type' => 'enum',
                    'options' => 'repeat_unit_dom',
                  ),
                  6 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT',
                    'name' => 'repeat_end_type',
                    'type' => 'enum',
                    'options' => 'repeat_end_types',
                    'default' => 'Until',
                  ),
                  7 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT_UNTIL_DATE',
                    'name' => 'repeat_until',
                    'type' => 'repeat-until',
                  ),
                  8 => 
                  array (
                    'label' => 'LBL_CALENDAR_REPEAT_COUNT',
                    'name' => 'repeat_count',
                    'type' => 'repeat-count',
                  ),
                ),
                'span' => 12,
              ),
              5 => 'direction',
              6 => 
              array (
                'name' => 'reminders',
                'type' => 'fieldset',
                'inline' => true,
                'equal_spacing' => true,
                'show_child_labels' => true,
                'fields' => 
                array (
                  0 => 'reminder_time',
                  1 => 'email_reminder_time',
                ),
              ),
              7 => 
              array (
                'name' => 'description',
                'rows' => 3,
                'span' => 12,
              ),
              8 => 
              array (
                'name' => 'parent_name',
              ),
              9 => 
              array (
                'readonly' => false,
                'name' => 'detalle_c',
                'label' => 'LBL_DETALLE',
              ),
              10 => 
              array (
                'name' => 'invitees',
                'type' => 'participants',
                'label' => 'LBL_INVITEES',
                'fields' => 
                array (
                  0 => 'name',
                  1 => 'accept_status_calls',
                  2 => 'picture',
                  3 => 'email',
                ),
                'span' => 12,
              ),
              11 => 
              array (
                'name' => 'assigned_user_name',
                'readonly' => true,
              ),
              12 => 
              array (
                'name' => 'producto_c',
                'label' => 'LBL_PRODUCTO_C',
                'readonly' => true,
              ),
              13 => 
              array (
                'name' => 'evento_campana_c',
                'label' => 'LBL_EVENTO_CAMPANA',
              ),
              14 => 
              array (
                'name' => 'campana_rel_c',
                'studio' => 'visible',
                'label' => 'LBL_CAMPANA_REL',
              ),
              15 => 
              array (
                'name' => 'tct_resultado_llamada_ddw_c',
                'label' => 'LBL_TCT_RESULTADO_LLAMADA_DDW',
              ),
              16 => 
              array (
                'name' => 'detalle_resultado_c',
                'label' => 'LBL_DETALLE_RESULTADO',
              ),
              17 => 
              array (
                'name' => 'cuenta_existente_c',
                'studio' => 'visible',
                'label' => 'LBL_CUENTA_EXISTENTE',
              ),
              18 => 
              array (
                'name' => 'padres_c',
                'label' => 'LBL_PADRES',
              ),
              19 => 
              array (
                'name' => 'calls_persona_relacion',
                'label' => 'Persona con quien se atiende la llamada',
                'studio' => 'visible',
              ),
              20 => 
              array (
                'name' => 'persona_relacion_c',
                'label' => 'LBL_PERSONA_RELACION_C',
              ),
              21 => 
              array (
                'name' => 'calls_meeting_call',
                'studio' => 'visible',
                'dismiss_label' => true,
                'span' => 12,
              ),
              22 => 
              array (
                'name' => 'tct_calificacion_conferencia_c',
                'label' => 'LBL_TCT_CALIFICACION_CONFERENCIA',
              ),
              23 => 
              array (
                'name' => 'tct_conferencia_fecha_dat_c',
                'label' => 'LBL_TCT_CONFERENCIA_FECHA_DAT',
              ),
              24 => 
              array (
                'name' => 'tct_fecha_seguimiento_dat_c',
                'label' => 'LBL_TCT_FECHA_SEGUIMIENTO_DAT',
              ),
              25 => 
              array (
                'name' => 'lic_licitaciones_calls_1_name',
                'label' => 'LBL_LIC_LICITACIONES_CALLS_1_FROM_LIC_LICITACIONES_TITLE',
                'readonly' => true,
              ),
              26 => 
              array (
                'name' => 'accounts_calls_1_name',
              ),
              27 => 
              array (
                'name' => 'leads_calls_1_name',
              ),
              28 => 
              array (
                'name' => 'tct_call_issabel_c',
                'label' => 'LBL_TCT_CALL_ISSABEL_C',
              ),
              29 => 
              array (
                'name' => 'tct_call_from_issabel_c',
                'label' => 'LBL_TCT_CALL_FROM_ISSABEL_C',
              ),
              30 => 
              array (
                'readonly' => true,
                'name' => 'origen_po_c',
                'label' => 'LBL_ORIGEN_PO_C',
              ),
              31 => 
              array (
                'readonly' => true,
                'name' => 'po_origen_rel_c',
                'studio' => 'visible',
                'label' => 'LBL_PO_ORIGEN_REL_C',
              ),
            ),
          ),
          2 => 
          array (
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'columns' => 2,
            'hide' => true,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'date_entered_by',
                'readonly' => true,
                'inline' => true,
                'type' => 'fieldset',
                'label' => 'LBL_DATE_ENTERED',
                'fields' => 
                array (
                  0 => 
                  array (
                    'name' => 'date_entered',
                  ),
                  1 => 
                  array (
                    'type' => 'label',
                    'default_value' => 'LBL_BY',
                  ),
                  2 => 
                  array (
                    'name' => 'created_by_name',
                  ),
                ),
              ),
              1 => 
              array (
                'name' => 'date_modified_by',
                'readonly' => true,
                'inline' => true,
                'type' => 'fieldset',
                'label' => 'LBL_DATE_MODIFIED',
                'fields' => 
                array (
                  0 => 
                  array (
                    'name' => 'date_modified',
                  ),
                  1 => 
                  array (
                    'type' => 'label',
                    'default_value' => 'LBL_BY',
                  ),
                  2 => 
                  array (
                    'name' => 'modified_by_name',
                  ),
                ),
              ),
            ),
          ),
        ),
        'templateMeta' => 
        array (
          'useTabs' => false,
        ),
      ),
    ),
  ),
);

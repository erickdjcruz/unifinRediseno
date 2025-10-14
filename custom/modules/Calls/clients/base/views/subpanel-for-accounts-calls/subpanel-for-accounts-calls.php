<?php
// created: 2022-04-06 12:40:09
$viewdefs['Calls']['base']['view']['subpanel-for-accounts-calls'] = array (
  'panels' => 
  array (
    0 => 
    array (
      'name' => 'panel_header',
      'label' => 'LBL_PANEL_1',
      'fields' => 
      array (
        0 => 
        array (
          'label' => 'LBL_LIST_SUBJECT',
          'enabled' => true,
          'default' => true,
          'link' => true,
          'name' => 'name',
        ),
        1 => 
        array (
          'name' => 'tct_resultado_llamada_ddw_c',
          'label' => 'LBL_TCT_RESULTADO_LLAMADA_DDW',
          'enabled' => true,
          'default' => true,
        ),
        2 => 
        array (
          'label' => 'LBL_STATUS',
          'enabled' => true,
          'default' => true,
          'name' => 'status',
          'type' => 'event-status',
          'readonly' => true,
          'css_class' => 'full-width',
        ),
        3 => 
        array (
          'name' => 'detalle_c',
          'label' => 'LBL_DETALLE',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        4 => 
        array (
          'name' => 'date_start',
          'label' => 'LBL_LIST_DATE',
          'type' => 'datetimecombo-colorcoded',
          'completed_status_value' => 'Held',
          'enabled' => true,
          'default' => true,
          'css_class' => 'overflow-visible',
          'readonly' => true,
          'related_fields' => 
          array (
            0 => 'status',
          ),
        ),
        5 => 
        array (
          'label' => 'LBL_DATE_END',
          'enabled' => true,
          'default' => true,
          'name' => 'date_end',
          'css_class' => 'overflow-visible',
        ),
        6 => 
        array (
          'name' => 'origen_po_c',
          'label' => 'LBL_ORIGEN_PO_C',
          'enabled' => true,
          'readonly' => true,
          'default' => true,
        ),
        7 => 
        array (
          'name' => 'parent_name',
          'label' => 'LBL_LIST_RELATED_TO',
          'enabled' => true,
          'id' => 'PARENT_ID',
          'link' => true,
          'sortable' => false,
          'default' => true,
        ),
        8 => 
        array (
          'name' => 'assigned_user_name',
          'target_record_key' => 'assigned_user_id',
          'target_module' => 'Employees',
          'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
          'enabled' => true,
          'default' => true,
        ),
      ),
    ),
  ),
  'rowactions' => 
  array (
    'actions' => 
    array (
      0 => 
      array (
        'type' => 'rowaction',
        'css_class' => 'btn',
        'tooltip' => 'LBL_PREVIEW',
        'event' => 'list:preview:fire',
        'icon' => 'fa-eye',
        'acl_action' => 'view',
      ),
      2 => 
      array (
        'type' => 'unlink-action',
        'icon' => 'fa-chain-broken',
        'label' => 'LBL_UNLINK_BUTTON',
      ),
    ),
  ),
  'type' => 'subpanel-list',
);
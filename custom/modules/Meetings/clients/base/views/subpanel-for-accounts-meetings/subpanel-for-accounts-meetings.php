<?php
// created: 2024-02-19 20:42:59
$viewdefs['Meetings']['base']['view']['subpanel-for-accounts-meetings'] = array (
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
          'name' => 'name',
          'label' => 'LBL_LIST_SUBJECT',
          'enabled' => true,
          'default' => true,
          'link' => true,
        ),
        1 => 
        array (
          'name' => 'status',
          'label' => 'LBL_LIST_STATUS',
          'enabled' => true,
          'default' => true,
          'type' => 'event-status',
          'css_class' => 'full-width',
        ),
        2 => 
        array (
          'name' => 'date_start',
          'label' => 'LBL_LIST_DATE',
          'type' => 'datetimecombo-colorcoded',
          'completed_status_value' => 'Held',
          'css_class' => 'overflow-visible',
          'enabled' => true,
          'default' => true,
          'readonly' => true,
          'related_fields' => 
          array (
            0 => 'status',
          ),
        ),
        3 => 
        array (
          'name' => 'date_end',
          'label' => 'LBL_DATE_END',
          'css_class' => 'overflow-visible',
          'enabled' => true,
          'default' => true,
        ),
        4 => 
        array (
          'name' => 'assigned_user_name',
          'target_record_key' => 'assigned_user_id',
          'target_module' => 'Employees',
          'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
          'enabled' => true,
          'default' => true,
        ),
        5 => 
        array (
          'name' => 'origen_po_c',
          'label' => 'LBL_ORIGEN_PO',
          'enabled' => true,
          'readonly' => true,
          'default' => true,
        ),
        6 => 
        array (
          'name' => 'check_in_time_c',
          'label' => 'LBL_CHECK_IN_TIME',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        7 => 
        array (
          'name' => 'check_in_platform_c',
          'label' => 'LBL_CHECK_IN_PLATFORM_C',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        8 => 
        array (
          'name' => 'check_in_latitude_c',
          'label' => 'LBL_CHECK_IN_LATITUDE',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        9 => 
        array (
          'name' => 'check_in_longitude_c',
          'label' => 'LBL_CHECK_IN_LONGITUDE',
          'enabled' => true,
          'readonly' => false,
          'default' => true,
        ),
        10 => 
        array (
          'name' => 'minuta_reunion_status_c',
          'label' => 'LBL_MINUTA_REUNION_STATUS',
          'enabled' => true,
          'readonly' => false,
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
        'tooltip' => 'LBL_PREVIEW',
        'event' => 'list:preview:fire',
        'icon' => 'sicon-preview',
        'acl_action' => 'view',
      ),
      1 => 
      array (
        'type' => 'rowaction',
        'event' => 'list:start_meeting:fire',
        'name' => 'btn-start-meeting',
        'label' => 'Iniciar Reunión',
        'acl_action' => 'view',
      ),
      2 => 
      array (
        'type' => 'unlink-action',
        'name' => 'unlink_button',
        'icon' => 'sicon-unlink',
        'label' => 'LBL_UNLINK_BUTTON',
      ),
      3 => 
      array (
        'type' => 'closebutton',
        'icon' => 'sicon-close',
        'name' => 'record-close',
        'label' => 'LBL_CLOSE_BUTTON_TITLE',
        'closed_status' => 'Held',
        'acl_action' => 'edit',
      ),
    ),
  ),
  'type' => 'subpanel-list',
);
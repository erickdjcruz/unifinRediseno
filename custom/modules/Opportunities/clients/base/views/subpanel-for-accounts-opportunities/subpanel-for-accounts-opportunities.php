<?php
// created: 2025-11-11 16:18:12
$viewdefs['Opportunities']['base']['view']['subpanel-for-accounts-opportunities'] = array (
  'type' => 'subpanel-list',
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
          'label' => 'LBL_LIST_OPPORTUNITY_NAME',
          'enabled' => true,
          'default' => true,
          'link' => true,
        ),
        1 => 
        array (
          'name' => 'date_closed',
          'label' => 'LBL_DATE_CLOSED',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'total_revenue_line_items',
            1 => 'closed_revenue_line_items',
          ),
          'type' => 'date-cascade',
          'disable_field' => 
          array (
            0 => 'total_revenue_line_items',
            1 => 'closed_revenue_line_items',
          ),
        ),
        2 => 
        array (
          'name' => 'sales_status',
          'label' => 'LBL_SALES_STATUS',
          'enabled' => true,
          'default' => true,
          'readonly' => true,
        ),
        3 => 
        array (
          'name' => 'sales_stage',
          'label' => 'LBL_LIST_SALES_STAGE',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'total_revenue_line_items',
            1 => 'closed_revenue_line_items',
          ),
          'type' => 'enum-cascade',
          'disable_field' => 
          array (
            0 => 'total_revenue_line_items',
            1 => 'closed_revenue_line_items',
          ),
        ),
        4 => 
        array (
          'name' => 'service_start_date',
          'label' => 'LBL_SERVICE_START_DATE',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'service_open_revenue_line_items',
          ),
          'type' => 'date-cascade',
          'disable_field' => 'service_open_revenue_line_items',
        ),
        5 => 
        array (
          'name' => 'service_duration',
          'label' => 'LBL_SERVICE_DURATION',
          'enabled' => true,
          'default' => false,
          'related_fields' => 
          array (
            0 => 'service_duration_value',
            1 => 'service_duration_unit',
            2 => 'service_open_flex_duration_rlis',
          ),
          'type' => 'fieldset-cascade',
          'inline' => true,
          'show_child_labels' => false,
          'css_class' => 'service-duration-field',
          'fields' => 
          array (
            0 => 
            array (
              'name' => 'service_duration_value',
              'label' => 'LBL_SERVICE_DURATION_VALUE',
            ),
            1 => 
            array (
              'name' => 'service_duration_unit',
              'label' => 'LBL_SERVICE_DURATION_UNIT',
            ),
          ),
          'orderBy' => 'service_duration_unit',
          'disable_field' => 'service_open_flex_duration_rlis',
        ),
        6 => 
        array (
          'name' => 'amount',
          'label' => 'LBL_LIKELY',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'amount',
            1 => 'currency_id',
            2 => 'base_rate',
          ),
          'currency_format' => true,
          'type' => 'currency',
          'currency_field' => 'currency_id',
          'base_rate_field' => 'base_rate',
        ),
        7 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'assigned_user_id',
          ),
          'id' => 'ASSIGNED_USER_ID',
          'link' => true,
          'target_record_key' => 'assigned_user_id',
          'target_module' => 'Employees',
        ),
        8 => 
        array (
          'name' => 'forecasted_likely',
          'label' => 'LBL_FORECASTED_LIKELY',
          'enabled' => true,
          'default' => true,
          'related_fields' => 
          array (
            0 => 'currency_id',
            1 => 'base_rate',
          ),
          'currency_format' => true,
        ),
      ),
    ),
  ),
);
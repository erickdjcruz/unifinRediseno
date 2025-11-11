<?php
$viewdefs['Opportunities'] = 
array (
  'base' => 
  array (
    'view' => 
    array (
      'list' => 
      array (
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
                'related_fields' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                  2 => 'included_revenue_line_items',
                ),
                'link' => true,
              ),
              1 => 
              array (
                'name' => 'account_name',
                'label' => 'LBL_LIST_ACCOUNT_NAME',
                'enabled' => true,
                'default' => true,
                'related_fields' => 
                array (
                  0 => 'account_id',
                ),
                'id' => 'ACCOUNT_ID',
                'link' => true,
                'sortable' => true,
              ),
              2 => 
              array (
                'name' => 'date_closed',
                'label' => 'LBL_DATE_CLOSED',
                'enabled' => true,
                'default' => true,
                'type' => 'date-cascade',
                'disable_field' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                ),
              ),
              3 => 
              array (
                'name' => 'sales_status',
                'label' => 'LBL_SALES_STATUS',
                'enabled' => true,
                'default' => true,
                'type' => 'enum',
              ),
              4 => 
              array (
                'name' => 'sales_stage',
                'label' => 'LBL_LIST_SALES_STAGE',
                'enabled' => true,
                'default' => true,
                'type' => 'enum-cascade',
                'disable_field' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                ),
              ),
              5 => 
              array (
                'name' => 'service_start_date',
                'label' => 'LBL_SERVICE_START_DATE',
                'enabled' => true,
                'default' => false,
                'related_fields' => 
                array (
                  0 => 'service_open_revenue_line_items',
                ),
                'type' => 'date-cascade',
                'disable_field' => 'service_open_revenue_line_items',
              ),
              6 => 
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
              7 => 
              array (
                'name' => 'is_escalated',
                'label' => 'LBL_ESCALATED',
                'enabled' => true,
                'default' => false,
                'badge_label' => 'LBL_ESCALATED',
                'warning_level' => 'important',
                'type' => 'badge',
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
                'comment' => 'Rollup of included RLIs on the Opportunity',
                'readonly' => true,
                'span' => 6,
              ),
              9 => 
              array (
                'name' => 'lost',
                'label' => 'LBL_LOST',
                'enabled' => true,
                'default' => true,
                'related_fields' => 
                array (
                  0 => 'currency_id',
                  1 => 'base_rate',
                ),
                'currency_format' => true,
                'comment' => 'Rollup of lost RLIs on the Opportunity',
                'readonly' => true,
              ),
              10 => 
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
              11 => 
              array (
                'name' => 'opportunity_type',
                'label' => 'LBL_TYPE',
                'enabled' => true,
                'default' => true,
              ),
              12 => 
              array (
                'name' => 'lead_source',
                'label' => 'LBL_LEAD_SOURCE',
                'enabled' => true,
                'default' => true,
              ),
              13 => 
              array (
                'name' => 'next_step',
                'label' => 'LBL_NEXT_STEP',
                'enabled' => true,
                'default' => true,
              ),
              14 => 
              array (
                'name' => 'created_by_name',
                'label' => 'LBL_CREATED',
                'enabled' => true,
                'default' => true,
                'id' => 'CREATED_BY',
                'link' => true,
                'readonly' => true,
                'sortable' => true,
              ),
              15 => 
              array (
                'name' => 'team_name',
                'label' => 'LBL_LIST_TEAM',
                'enabled' => true,
                'default' => false,
                'type' => 'teamset',
              ),
              16 => 
              array (
                'name' => 'assigned_user_name',
                'label' => 'LBL_LIST_ASSIGNED_USER',
                'enabled' => true,
                'default' => true,
                'related_fields' => 
                array (
                  0 => 'assigned_user_id',
                ),
                'id' => 'ASSIGNED_USER_ID',
                'link' => true,
                'sortable' => true,
              ),
              17 => 
              array (
                'name' => 'modified_by_name',
                'label' => 'LBL_MODIFIED',
                'enabled' => true,
                'default' => true,
                'id' => 'MODIFIED_USER_ID',
                'link' => true,
                'readonly' => true,
                'sortable' => true,
              ),
              18 => 
              array (
                'name' => 'date_entered',
                'label' => 'LBL_DATE_ENTERED',
                'enabled' => true,
                'default' => true,
                'readonly' => true,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);

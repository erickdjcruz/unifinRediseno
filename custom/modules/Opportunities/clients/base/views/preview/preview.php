<?php
$viewdefs['Opportunities'] = 
array (
  'base' => 
  array (
    'view' => 
    array (
      'preview' => 
      array (
        'templateMeta' => 
        array (
          'maxColumns' => 1,
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
              1 => 
              array (
                'name' => 'name',
                'related_fields' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                  2 => 'included_revenue_line_items',
                ),
              ),
              2 => 
              array (
                'name' => 'favorite',
                'label' => 'LBL_FAVORITE',
                'type' => 'favorite',
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
                'name' => 'is_escalated',
                'type' => 'badge',
                'badge_label' => 'LBL_ESCALATED',
                'warning_level' => 'important',
                'dismiss_label' => true,
              ),
              5 => 
              array (
                'name' => 'renewal',
                'type' => 'renewal',
                'dismiss_label' => true,
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labels' => true,
            'placeholders' => true,
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'account_name',
                'related_fields' => 
                array (
                  0 => 'account_id',
                ),
              ),
              1 => 
              array (
                'name' => 'date_closed',
                'type' => 'date-cascade',
                'label' => 'LBL_LIST_DATE_CLOSED',
                'disable_field' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                ),
                'related_fields' => 
                array (
                  0 => 'date_closed_timestamp',
                ),
              ),
              2 => 
              array (
                'name' => 'service_start_date',
                'type' => 'date-cascade',
                'label' => 'LBL_SERVICE_START_DATE',
                'disable_field' => 'service_open_revenue_line_items',
                'related_fields' => 
                array (
                  0 => 'service_open_revenue_line_items',
                ),
              ),
              3 => 
              array (
                'name' => 'service_duration',
                'type' => 'fieldset-cascade',
                'label' => 'LBL_SERVICE_DURATION',
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
                'related_fields' => 
                array (
                  0 => 'service_duration_value',
                  1 => 'service_duration_unit',
                  2 => 'service_open_flex_duration_rlis',
                ),
                'disable_field' => 'service_open_flex_duration_rlis',
              ),
              4 => 
              array (
                'name' => 'amount',
                'type' => 'currency',
                'label' => 'LBL_LIKELY',
                'related_fields' => 
                array (
                  0 => 'amount',
                  1 => 'currency_id',
                  2 => 'base_rate',
                ),
                'currency_field' => 'currency_id',
                'base_rate_field' => 'base_rate',
                'span' => 6,
              ),
              5 => 
              array (
                'name' => 'tag',
              ),
              6 => 
              array (
                'name' => 'sales_status',
                'label' => 'LBL_SALES_STATUS',
                'default' => true,
                'enabled' => true,
                'type' => 'enum',
              ),
              7 => 
              array (
                'name' => 'sales_stage',
                'type' => 'enum-cascade',
                'label' => 'LBL_SALES_STAGE',
                'disable_field' => 
                array (
                  0 => 'total_revenue_line_items',
                  1 => 'closed_revenue_line_items',
                ),
              ),
              8 => 
              array (
                'name' => 'forecasted_likely',
                'comment' => 'Rollup of included RLIs on the Opportunity',
                'readonly' => true,
                'related_fields' => 
                array (
                  0 => 'currency_id',
                  1 => 'base_rate',
                ),
                'label' => 'LBL_FORECASTED_LIKELY',
              ),
              9 => 
              array (
                'name' => 'lost',
                'comment' => 'Rollup of lost RLIs on the Opportunity',
                'readonly' => true,
                'related_fields' => 
                array (
                  0 => 'currency_id',
                  1 => 'base_rate',
                ),
                'label' => 'LBL_LOST',
              ),
            ),
          ),
          2 => 
          array (
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'placeholders' => true,
            'columns' => 2,
            'fields' => 
            array (
              0 => 'next_step',
              1 => 'opportunity_type',
              2 => 'renewal_parent_name',
              3 => 'lead_source',
              4 => 'campaign_name',
              5 => 
              array (
                'name' => 'description',
              ),
              6 => 'assigned_user_name',
              7 => 'team_name',
              8 => 
              array (
                'name' => 'date_entered_by',
                'readonly' => true,
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
              9 => 
              array (
                'name' => 'date_modified_by',
                'readonly' => true,
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
      ),
    ),
  ),
);

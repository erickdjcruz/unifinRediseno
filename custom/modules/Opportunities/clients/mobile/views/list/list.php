<?php
$viewdefs['Opportunities'] = 
array (
  'mobile' => 
  array (
    'view' => 
    array (
      'list' => 
      array (
        'panels' => 
        array (
          0 => 
          array (
            'label' => 'LBL_PANEL_DEFAULT',
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
                'sortable' => false,
              ),
              2 => 
              array (
                'name' => 'opportunity_type',
                'label' => 'LBL_TYPE',
                'enabled' => true,
                'default' => false,
              ),
              3 => 
              array (
                'name' => 'lead_source',
                'label' => 'LBL_LEAD_SOURCE',
                'enabled' => true,
                'default' => false,
              ),
              4 => 
              array (
                'name' => 'next_step',
                'label' => 'LBL_NEXT_STEP',
                'enabled' => true,
                'default' => false,
              ),
              5 => 
              array (
                'name' => 'sales_stage',
                'label' => 'LBL_LIST_SALES_STAGE',
                'enabled' => true,
                'default' => false,
                'width' => '10',
              ),
              6 => 
              array (
                'name' => 'date_closed',
                'label' => 'LBL_DATE_CLOSED',
                'enabled' => true,
                'default' => false,
              ),
              7 => 
              array (
                'name' => 'date_entered',
                'label' => 'LBL_DATE_ENTERED',
                'enabled' => true,
                'default' => false,
                'readonly' => true,
              ),
              8 => 
              array (
                'name' => 'created_by_name',
                'label' => 'LBL_CREATED',
                'enabled' => true,
                'default' => false,
                'id' => 'CREATED_BY',
                'link' => true,
                'readonly' => true,
              ),
              9 => 
              array (
                'name' => 'team_name',
                'label' => 'LBL_LIST_TEAM',
                'enabled' => true,
                'default' => false,
                'id' => 'TEAM_ID',
                'link' => true,
                'sortable' => false,
              ),
              10 => 
              array (
                'name' => 'assigned_user_name',
                'label' => 'LBL_LIST_ASSIGNED_USER',
                'enabled' => true,
                'default' => false,
                'related_fields' => 
                array (
                  0 => 'assigned_user_id',
                ),
                'id' => 'ASSIGNED_USER_ID',
                'link' => true,
              ),
              11 => 
              array (
                'name' => 'modified_by_name',
                'label' => 'LBL_MODIFIED',
                'enabled' => true,
                'default' => false,
                'id' => 'MODIFIED_USER_ID',
                'link' => true,
                'readonly' => true,
              ),
              12 => 
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
              13 => 
              array (
                'name' => 'service_start_date',
                'label' => 'LBL_SERVICE_START_DATE',
                'enabled' => true,
                'default' => true,
              ),
              14 => 
              array (
                'name' => 'sales_status',
                'label' => 'LBL_SALES_STATUS',
                'enabled' => true,
                'default' => true,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);

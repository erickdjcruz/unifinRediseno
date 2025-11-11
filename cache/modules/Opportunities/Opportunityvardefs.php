<?php 
 $GLOBALS["dictionary"]["Opportunity"]=array (
  'table' => 'opportunities',
  'color' => 'red',
  'icon' => 'sicon-opportunity-lg',
  'audited' => true,
  'escalatable' => true,
  'activity_enabled' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'duplicate_merge' => true,
  'comment' => 'An opportunity is the target of selling activities',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => true,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Unique identifier',
      'mandatory_fetch' => true,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_OPPORTUNITY_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => '255',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.65,
      ),
      'comment' => 'Name of the opportunity',
      'merge_filter' => 'selected',
      'importable' => 'required',
      'required' => true,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'group' => 'created_by_name',
      'comment' => 'Date record created',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'group' => 'modified_by_name',
      'comment' => 'Date record last modified',
      'enable_range_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'sortable' => true,
      ),
      'studio' => 
      array (
        'portaleditview' => false,
      ),
      'options' => 'date_range_search_dom',
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'massupdate' => false,
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_MODIFIED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'group' => 'modified_by_name',
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'modified_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_MODIFIED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
      ),
    ),
    'modified_by_name' => 
    array (
      'name' => 'modified_by_name',
      'vname' => 'LBL_MODIFIED',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'rname' => 'full_name',
      'table' => 'users',
      'id_name' => 'modified_user_id',
      'module' => 'Users',
      'link' => 'modified_user_link',
      'duplicate_merge' => 'disabled',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_CREATED',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => false,
      'dbType' => 'id',
      'group' => 'created_by_name',
      'comment' => 'User who created record',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'type' => 'id',
        'aggregations' => 
        array (
          'created_by' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_CREATED_BY_ME',
          ),
        ),
      ),
      'processes' => 
      array (
        'types' => 
        array (
          'RR' => false,
          'ALL' => true,
        ),
      ),
    ),
    'created_by_name' => 
    array (
      'name' => 'created_by_name',
      'vname' => 'LBL_CREATED',
      'type' => 'relate',
      'reportable' => false,
      'link' => 'created_by_link',
      'rname' => 'full_name',
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'created_by',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'massupdate' => false,
      'duplicate_on_record_copy' => 'no',
      'readonly' => true,
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'comment' => 'Full text of the note',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.59,
      ),
      'rows' => 6,
      'cols' => 80,
      'duplicate_on_record_copy' => 'always',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Record deletion indicator',
    ),
    'created_by_link' => 
    array (
      'name' => 'created_by_link',
      'type' => 'link',
      'relationship' => 'opportunities_created_by',
      'vname' => 'LBL_CREATED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'modified_user_link' => 
    array (
      'name' => 'modified_user_link',
      'type' => 'link',
      'relationship' => 'opportunities_modified_user',
      'vname' => 'LBL_MODIFIED_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'side' => 'right',
    ),
    'activities' => 
    array (
      'name' => 'activities',
      'type' => 'link',
      'relationship' => 'opportunity_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'opportunity_type' => 
    array (
      'name' => 'opportunity_type',
      'vname' => 'LBL_TYPE',
      'type' => 'enum',
      'options' => 'opportunity_type_dom',
      'len' => '255',
      'audited' => true,
      'comment' => 'Type of opportunity (ex: Existing, New)',
      'merge_filter' => 'enabled',
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'rname' => 'name',
      'id_name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_NAME',
      'type' => 'relate',
      'table' => 'accounts',
      'join_name' => 'accounts',
      'isnull' => true,
      'module' => 'Accounts',
      'dbType' => 'varchar',
      'link' => 'accounts',
      'len' => '255',
      'source' => 'non-db',
      'unified_search' => true,
      'required' => true,
      'importable' => 'required',
      'related_fields' => 
      array (
        0 => 'account_id',
      ),
      'exportable' => true,
      'export_link_type' => 'one',
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_ID',
      'id_name' => 'account_id',
      'type' => 'relate',
      'link' => 'accounts',
      'rname' => 'id',
      'source' => 'non-db',
      'audited' => true,
      'dbType' => 'id',
      'module' => 'Accounts',
      'massupdate' => false,
      'label' => 'LBL_ACCOUNT_NAME',
    ),
    'campaign_id' => 
    array (
      'name' => 'campaign_id',
      'comment' => 'Campaign that generated lead',
      'vname' => 'LBL_CAMPAIGN_ID',
      'rname' => 'id',
      'type' => 'id',
      'dbType' => 'id',
      'table' => 'campaigns',
      'isnull' => true,
      'module' => 'Campaigns',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
    ),
    'campaign_name' => 
    array (
      'name' => 'campaign_name',
      'rname' => 'name',
      'id_name' => 'campaign_id',
      'vname' => 'LBL_CAMPAIGN',
      'type' => 'relate',
      'related_fields' => 
      array (
        0 => 'campaign_id',
      ),
      'link' => 'campaign_opportunities',
      'isnull' => true,
      'table' => 'campaigns',
      'module' => 'Campaigns',
      'source' => 'non-db',
      'studio' => 
      array (
        'mobile' => false,
      ),
    ),
    'campaign_opportunities' => 
    array (
      'name' => 'campaign_opportunities',
      'type' => 'link',
      'vname' => 'LBL_CAMPAIGN_OPPORTUNITY',
      'relationship' => 'campaign_opportunities',
      'source' => 'non-db',
    ),
    'lead_source' => 
    array (
      'name' => 'lead_source',
      'vname' => 'LBL_LEAD_SOURCE',
      'type' => 'enum',
      'options' => 'lead_source_dom',
      'len' => '50',
      'comment' => 'Source of the opportunity',
      'merge_filter' => 'enabled',
    ),
    'amount' => 
    array (
      'name' => 'amount',
      'vname' => 'LBL_LIKELY',
      'type' => 'currency',
      'dbType' => 'currency',
      'comment' => 'Unconverted amount of the opportunity',
      'importable' => 'required',
      'duplicate_merge' => 'enabled',
      'required' => false,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'audited' => false,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'convertToBase' => true,
      'showTransactionalAmount' => true,
      'massupdate' => false,
      'hidemassupdate' => false,
      'comments' => 'Unconverted amount of the opportunity',
      'duplicate_merge_dom_value' => '1',
      'merge_filter' => 'disabled',
      'calculated' => true,
      'formula' => 'rollupConditionalSum($revenuelineitems, "likely_case", "sales_stage", forecastSalesStages(true, false))',
      'enforced' => true,
      'readonly' => true,
    ),
    'amount_usdollar' => 
    array (
      'name' => 'amount_usdollar',
      'vname' => 'LBL_AMOUNT_USDOLLAR',
      'type' => 'currency',
      'group' => 'amount',
      'dbType' => 'currency',
      'disable_num_format' => true,
      'duplicate_merge' => '0',
      'comment' => 'Formatted amount of the opportunity',
      'studio' => 
      array (
        'wirelesslistview' => false,
        'wirelesseditview' => false,
        'wirelessdetailview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
        'editview' => false,
        'detailview' => false,
        'quickcreate' => false,
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($amount), currencyDivide($amount, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'date_closed' => 
    array (
      'name' => 'date_closed',
      'vname' => 'LBL_DATE_CLOSED',
      'type' => 'date',
      'comment' => 'Expected or actual date the oppportunity will close',
      'audited' => false,
      'importable' => 'false',
      'required' => false,
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
      'related_fields' => 
      array (
      ),
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
      'massupdate' => false,
      'hidemassupdate' => true,
      'comments' => 'Expected or actual date the oppportunity will close',
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'calculated' => false,
    ),
    'date_closed_timestamp' => 
    array (
      'name' => 'date_closed_timestamp',
      'vname' => 'LBL_DATE_CLOSED_TIMESTAMP',
      'type' => 'ulong',
      'studio' => 
      array (
        'formula' => true,
        'related' => true,
        'recordview' => false,
        'listview' => false,
        'detailview' => false,
        'searchview' => false,
        'createview' => false,
        'editField' => false,
      ),
      'reportable' => false,
      'workflow' => false,
      'massupdate' => false,
      'enforced' => true,
      'calculated' => true,
      'formula' => 'timestamp($date_closed)',
      'importable' => false,
    ),
    'next_step' => 
    array (
      'name' => 'next_step',
      'vname' => 'LBL_NEXT_STEP',
      'type' => 'varchar',
      'len' => '100',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.74,
      ),
      'comment' => 'The next step in the sales process',
      'merge_filter' => 'enabled',
      'massupdate' => true,
    ),
    'sales_stage' => 
    array (
      'name' => 'sales_stage',
      'vname' => 'LBL_SALES_STAGE',
      'type' => 'enum',
      'options' => 'sales_stage_dom',
      'default' => 'Prospecting',
      'len' => '255',
      'comment' => 'Indication of progression towards closure',
      'merge_filter' => 'disabled',
      'importable' => 'false',
      'audited' => true,
      'required' => false,
      'massupdate' => false,
      'hidemassupdate' => true,
      'comments' => 'Indication of progression towards closure',
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'reportable' => false,
      'calculated' => false,
      'dependency' => false,
      'visibility_grid' => 
      array (
      ),
    ),
    'sales_status' => 
    array (
      'name' => 'sales_status',
      'vname' => 'LBL_SALES_STATUS',
      'type' => 'enum',
      'options' => 'sales_status_dom',
      'len' => '255',
      'readonly' => true,
      'duplicate_merge' => 'disabled',
      'studio' => true,
      'audited' => true,
      'reportable' => true,
      'massupdate' => true,
      'importable' => true,
      'default' => 'New',
      'hidemassupdate' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'dependency' => false,
      'visibility_grid' => 
      array (
      ),
    ),
    'probability' => 
    array (
      'name' => 'probability',
      'vname' => 'LBL_PROBABILITY',
      'type' => 'int',
      'dbType' => 'double',
      'audited' => false,
      'formula' => 'getDropdownValue("sales_probability_dom",$sales_stage)',
      'calculated' => true,
      'enforced' => true,
      'workflow' => false,
      'comment' => 'The probability of closure',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
        'max' => 100,
      ),
      'merge_filter' => 'disabled',
      'massupdate' => false,
      'hidemassupdate' => false,
      'comments' => 'The probability of closure',
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'reportable' => false,
      'enable_range_search' => false,
      'min' => false,
      'max' => false,
      'disable_num_format' => '',
      'studio' => false,
    ),
    'best_case' => 
    array (
      'name' => 'best_case',
      'vname' => 'LBL_BEST',
      'dbType' => 'currency',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => false,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'convertToBase' => true,
      'showTransactionalAmount' => true,
      'massupdate' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'calculated' => true,
      'formula' => 'rollupConditionalSum($revenuelineitems, "best_case", "sales_stage", forecastSalesStages(true, false))',
      'enforced' => true,
      'readonly' => true,
      'enable_range_search' => false,
    ),
    'worst_case' => 
    array (
      'name' => 'worst_case',
      'vname' => 'LBL_WORST',
      'dbType' => 'currency',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => false,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'convertToBase' => true,
      'showTransactionalAmount' => true,
      'massupdate' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'calculated' => true,
      'formula' => 'rollupConditionalSum($revenuelineitems, "worst_case", "sales_stage", forecastSalesStages(true, false))',
      'enforced' => true,
      'readonly' => true,
      'enable_range_search' => false,
    ),
    'commit_stage' => 
    array (
      'name' => 'commit_stage',
      'vname' => 'LBL_COMMIT_STAGE_FORECAST',
      'type' => 'enum',
      'len' => '50',
      'comment' => 'Forecast commit ranges: Include, Likely, Omit etc.',
      'function' => 'getCommitStageDropdown',
      'function_bean' => 'Forecasts',
      'formula' => '',
      'calculated' => false,
      'related_fields' => 
      array (
      ),
      'audited' => false,
      'massupdate' => false,
      'hidemassupdate' => true,
      'options' => '',
      'comments' => 'Forecast commit ranges: Include, Likely, Omit etc.',
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'dependency' => false,
      'visibility_grid' => 
      array (
      ),
    ),
    'forecasted_likely' => 
    array (
      'name' => 'forecasted_likely',
      'vname' => 'LBL_FORECASTED_LIKELY',
      'type' => 'currency',
      'dbType' => 'currency',
      'comment' => 'Rollup of included RLIs on the Opportunity',
      'readonly' => true,
      'massupdate' => false,
      'importable' => false,
      'duplicate_merge' => false,
      'required' => false,
      'options' => 'numeric_range_search_dom',
      'enable_range_search' => true,
      'audited' => false,
      'formula' => 'rollupSum($revenuelineitems, "forecasted_likely")',
      'calculated' => true,
      'enforced' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'convertToBase' => true,
      'showTransactionalAmount' => true,
    ),
    'lost' => 
    array (
      'name' => 'lost',
      'vname' => 'LBL_LOST',
      'type' => 'currency',
      'dbType' => 'currency',
      'comment' => 'Rollup of lost RLIs on the Opportunity',
      'readonly' => true,
      'massupdate' => false,
      'importable' => false,
      'duplicate_merge' => false,
      'required' => false,
      'enable_range_search' => true,
      'audited' => false,
      'formula' => 'rollupConditionalSum($revenuelineitems, "likely_case", "sales_stage", forecastOnlySalesStages(false,true,false))',
      'calculated' => true,
      'enforced' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'convertToBase' => true,
      'showTransactionalAmount' => true,
    ),
    'commit_stage_cascade' => 
    array (
      'name' => 'commit_stage_cascade',
      'vname' => 'LBL_COMMIT_STAGE',
      'type' => 'varchar',
      'len' => '50',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'closed_won_revenue_line_items' => 
    array (
      'name' => 'closed_won_revenue_line_items',
      'vname' => 'LBL_CLOSED_WON_RLIS',
      'type' => 'int',
      'formula' => 'countConditional($revenuelineitems, "sales_stage", forecastOnlySalesStages(true, false, false))',
      'calculated' => true,
      'enforced' => true,
      'studio' => false,
      'workflow' => false,
      'reportable' => true,
      'importable' => false,
      'audited' => false,
      'massupdate' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'enable_range_search' => false,
      'min' => false,
      'max' => false,
      'disable_num_format' => '',
    ),
    'service_start_date' => 
    array (
      'name' => 'service_start_date',
      'vname' => 'LBL_SERVICE_START_DATE',
      'type' => 'date',
      'comment' => 'Service start date field.',
      'related_fields' => 
      array (
      ),
      'studio' => 
      array (
        'calculated' => false,
      ),
      'audited' => false,
      'massupdate' => false,
      'hidemassupdate' => true,
      'comments' => 'Service start date field.',
      'importable' => 'false',
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'calculated' => false,
      'enable_range_search' => false,
    ),
    'service_open_revenue_line_items' => 
    array (
      'name' => 'service_open_revenue_line_items',
      'vname' => 'LBL_CLOSED_RLIS',
      'type' => 'int',
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
    ),
    'service_start_date_cascade' => 
    array (
      'name' => 'service_start_date_cascade',
      'type' => 'date',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'total_revenue_line_items' => 
    array (
      'name' => 'total_revenue_line_items',
      'vname' => 'LBL_TOTAL_RLIS',
      'type' => 'int',
      'formula' => 'count($revenuelineitems)',
      'calculated' => true,
      'enforced' => true,
      'studio' => false,
      'workflow' => false,
      'reportable' => true,
      'importable' => false,
      'audited' => false,
      'massupdate' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'enable_range_search' => false,
      'min' => false,
      'max' => false,
      'disable_num_format' => '',
    ),
    'closed_revenue_line_items' => 
    array (
      'name' => 'closed_revenue_line_items',
      'vname' => 'LBL_CLOSED_RLIS',
      'type' => 'int',
      'formula' => 'countConditional($revenuelineitems, "sales_stage", forecastOnlySalesStages(true, true, false))',
      'calculated' => true,
      'enforced' => true,
      'studio' => false,
      'workflow' => false,
      'reportable' => true,
      'importable' => false,
      'audited' => false,
      'massupdate' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'enable_range_search' => false,
      'min' => false,
      'max' => false,
      'disable_num_format' => '',
    ),
    'included_revenue_line_items' => 
    array (
      'name' => 'included_revenue_line_items',
      'vname' => 'LBL_INCLUDED_RLIS',
      'type' => 'int',
      'formula' => 'countConditional($revenuelineitems,"commit_stage", forecastIncludedCommitStages())',
      'calculated' => true,
      'enforced' => true,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
    ),
    'renewal_opportunities' => 
    array (
      'name' => 'renewal_opportunities',
      'type' => 'link',
      'relationship' => 'renewals_opportunities',
      'module' => 'Opportunities',
      'bean_name' => 'Opportunity',
      'source' => 'non-db',
      'vname' => 'LBL_RENEWAL_OPPORTUNITIES',
      'side' => 'left',
    ),
    'renewal_parent' => 
    array (
      'name' => 'renewal_parent',
      'type' => 'link',
      'relationship' => 'renewals_opportunities',
      'module' => 'Opportunities',
      'bean_name' => 'Opportunity',
      'link_type' => 'one',
      'source' => 'non-db',
      'vname' => 'LBL_RENEWAL_PARENT',
      'side' => 'right',
    ),
    'renewal_parent_id' => 
    array (
      'name' => 'renewal_parent_id',
      'vname' => 'LBL_PARENT_RENEWAL_OPPORTUNITY_ID',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'audited' => true,
    ),
    'renewal_parent_name' => 
    array (
      'name' => 'renewal_parent_name',
      'rname' => 'name',
      'id_name' => 'renewal_parent_id',
      'vname' => 'LBL_RENEWAL_PARENT',
      'type' => 'relate',
      'isnull' => 'true',
      'module' => 'Opportunities',
      'table' => 'opportunities',
      'massupdate' => false,
      'source' => 'non-db',
      'link' => 'renewal_parent',
      'unified_search' => true,
      'importable' => 'true',
    ),
    'widget_sales_stage' => 
    array (
      'name' => 'widget_sales_stage',
      'vname' => 'LBL_WIDGET_SALES_STAGE',
      'type' => 'widget',
      'multiline' => false,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'source' => 'non-db',
      'console' => 
      array (
        'name' => 'sales_stage',
        'label' => 'LBL_WIDGET_SALES_STAGE',
        'type' => 'fieldset-cascade',
        'show_child_labels' => false,
        'fields' => 
        array (
          0 => 
          array (
            'name' => 'sales_stage',
            'label' => 'LBL_SALES_STAGE',
            'type' => 'enum-colorcoded-fore-bkgd',
          ),
        ),
        'disable_field' => 
        array (
          0 => 'total_revenue_line_items',
          1 => 'closed_revenue_line_items',
        ),
      ),
    ),
    'widget_date_closed' => 
    array (
      'name' => 'widget_date_closed',
      'vname' => 'LBL_WIDGET_DATE_CLOSED',
      'type' => 'widget',
      'multiline' => false,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'source' => 'non-db',
      'console' => 
      array (
        'name' => 'date_closed',
        'label' => 'LBL_WIDGET_DATE_CLOSED',
        'type' => 'relative-date',
      ),
    ),
    'widget_amount' => 
    array (
      'name' => 'widget_amount',
      'vname' => 'LBL_WIDGET_AMOUNT',
      'type' => 'widget',
      'multiline' => true,
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'source' => 'non-db',
      'console' => 
      array (
        'name' => 'amount',
        'label' => 'LBL_WIDGET_AMOUNT',
        'type' => 'boxplot',
        'related_fields' => 
        array (
          0 => 'best_case',
          1 => 'worst_case',
        ),
      ),
    ),
    'sales_stage_cascade' => 
    array (
      'name' => 'sales_stage_cascade',
      'vname' => 'LBL_SALES_STAGE',
      'type' => 'enum',
      'options' => 'sales_stage_dom',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'date_closed_cascade' => 
    array (
      'name' => 'date_closed_cascade',
      'type' => 'date',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'external_users' => 
    array (
      'name' => 'external_users',
      'type' => 'link',
      'relationship' => 'external_users_opportunities',
      'module' => 'ExternalUsers',
      'bean_name' => 'ExternalUser',
      'source' => 'non-db',
      'vname' => 'LBL_EXTERNAL_USERS',
    ),
    'accounts' => 
    array (
      'name' => 'accounts',
      'type' => 'link',
      'relationship' => 'accounts_opportunities',
      'source' => 'non-db',
      'link_type' => 'one',
      'module' => 'Accounts',
      'bean_name' => 'Account',
      'vname' => 'LBL_ACCOUNTS',
    ),
    'contacts' => 
    array (
      'name' => 'contacts',
      'type' => 'link',
      'relationship' => 'opportunities_contacts',
      'source' => 'non-db',
      'module' => 'Contacts',
      'bean_name' => 'Contact',
      'rel_fields' => 
      array (
        'contact_role' => 
        array (
          'type' => 'enum',
          'options' => 'opportunity_relationship_type_dom',
        ),
      ),
      'vname' => 'LBL_CONTACTS',
      'populate_list' => 
      array (
        'account_id' => 'account_id',
        'account_name' => 'account_name',
      ),
    ),
    'contact_role' => 
    array (
      'name' => 'contact_role',
      'type' => 'enum',
      'studio' => 'false',
      'source' => 'non-db',
      'massupdate' => false,
      'vname' => 'LBL_OPPORTUNITY_ROLE',
      'options' => 'opportunity_relationship_type_dom',
      'link' => 'contacts',
      'rname_link' => 'contact_role',
    ),
    'tasks' => 
    array (
      'name' => 'tasks',
      'type' => 'link',
      'relationship' => 'opportunity_tasks',
      'source' => 'non-db',
      'vname' => 'LBL_TASKS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'opportunity_notes',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'opportunity_messages',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'escalations' => 
    array (
      'name' => 'escalations',
      'type' => 'link',
      'relationship' => 'opportunity_escalations',
      'module' => 'Escalations',
      'bean_name' => 'Escalation',
      'source' => 'non-db',
      'vname' => 'LBL_ESCALATIONS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'opportunity_meetings',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'opportunity_calls',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_opportunities_rel',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
    ),
    'archived_emails' => 
    array (
      'name' => 'archived_emails',
      'type' => 'link',
      'link_class' => 'ArchivedEmailsBeanLink',
      'link' => 'contacts',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
      'module' => 'Emails',
      'link_type' => 'many',
      'relationship' => '',
      'hideacl' => true,
      'readonly' => true,
    ),
    'documents' => 
    array (
      'name' => 'documents',
      'type' => 'link',
      'relationship' => 'documents_opportunities',
      'source' => 'non-db',
      'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quotes_opportunities',
      'source' => 'non-db',
      'vname' => 'LBL_QUOTES',
    ),
    'project' => 
    array (
      'name' => 'project',
      'type' => 'link',
      'relationship' => 'projects_opportunities',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'leads' => 
    array (
      'name' => 'leads',
      'type' => 'link',
      'relationship' => 'opportunity_leads',
      'source' => 'non-db',
      'vname' => 'LBL_LEADS',
    ),
    'campaigns' => 
    array (
      'name' => 'campaigns',
      'type' => 'link',
      'relationship' => 'campaignlog_created_opportunities',
      'module' => 'CampaignLog',
      'bean_name' => 'CampaignLog',
      'source' => 'non-db',
      'vname' => 'LBL_CAMPAIGNS',
      'reportable' => false,
    ),
    'contracts' => 
    array (
      'name' => 'contracts',
      'type' => 'link',
      'vname' => 'LBL_CONTRACTS',
      'relationship' => 'contracts_opportunities',
      'source' => 'non-db',
      'populate_list' => 
      array (
        'account_id' => 'account_id',
        'account_name' => 'account_name',
      ),
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'vname' => 'LBL_RLI',
      'relationship' => 'opportunities_revenuelineitems',
      'source' => 'non-db',
      'workflow' => true,
    ),
    'forecastworksheets' => 
    array (
      'name' => 'forecastworksheets',
      'type' => 'link',
      'relationship' => 'forecastworksheets_opportunities',
      'vname' => 'LBL_FORECAST_WORKSHEET',
      'module' => 'ForecastWorksheets',
      'bean_name' => 'ForecastWorksheet',
      'source' => 'non-db',
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'vname' => 'LBL_PRODUCTS',
      'relationship' => 'opportunities_products',
      'source' => 'non-db',
    ),
    'ai_opp_conv_score_absolute' => 
    array (
      'name' => 'ai_opp_conv_score_absolute',
      'vname' => 'LBL_AI_CONV_SCORE_ABSOLUTE',
      'type' => 'decimal',
      'len' => 10,
      'precision' => 8,
      'reportable' => true,
      'importable' => false,
      'readonly' => true,
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'ai_opp_conv_bin_accuracy' => 
    array (
      'name' => 'ai_opp_conv_bin_accuracy',
      'vname' => 'LBL_AI_CONV_BIN_ACCURACY',
      'type' => 'decimal',
      'len' => 10,
      'precision' => 8,
      'reportable' => true,
      'importable' => false,
      'readonly' => true,
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'ai_opp_conv_multiplier' => 
    array (
      'name' => 'ai_opp_conv_multiplier',
      'vname' => 'LBL_AI_CONV_MULTIPLIER',
      'type' => 'decimal',
      'len' => 10,
      'precision' => 8,
      'reportable' => true,
      'importable' => false,
      'readonly' => true,
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'purchasedlineitems' => 
    array (
      'name' => 'purchasedlineitems',
      'type' => 'link',
      'vname' => 'LBL_PURCHASED_LINE_ITEMS',
      'relationship' => 'purchasedlineitem_renewal_opp',
      'module' => 'PurchasedLineItems',
      'bean_name' => 'PurchasedLineItem',
      'source' => 'non-db',
    ),
    'service_duration_value' => 
    array (
      'name' => 'service_duration_value',
      'vname' => 'LBL_SERVICE_DURATION_VALUE',
      'type' => 'int',
      'min' => '1',
      'len' => '5',
      'required' => false,
      'studio' => false,
      'massupdate' => false,
      'importable' => false,
      'comment' => 'Value of the service duration, if service duration is 4 Months the value is 4',
    ),
    'service_duration_unit' => 
    array (
      'name' => 'service_duration_unit',
      'vname' => 'LBL_SERVICE_DURATION_UNIT',
      'type' => 'enum',
      'options' => 'service_duration_unit_dom',
      'len' => 50,
      'audited' => false,
      'studio' => false,
      'massupdate' => false,
      'importable' => false,
      'comment' => 'Service duration unit: day, month, or year',
    ),
    'service_duration_value_cascade' => 
    array (
      'name' => 'service_duration_value_cascade',
      'type' => 'int',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'service_duration_unit_cascade' => 
    array (
      'name' => 'service_duration_unit_cascade',
      'vname' => 'LBL_SERVICE_DURATION_UNIT',
      'type' => 'enum',
      'options' => 'service_duration_unit_dom',
      'source' => 'non-db',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'studio' => false,
      'importable' => false,
      'massupdate' => false,
    ),
    'service_open_flex_duration_rlis' => 
    array (
      'name' => 'service_open_flex_duration_rlis',
      'vname' => 'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS',
      'type' => 'int',
      'studio' => false,
      'workflow' => false,
      'reportable' => false,
      'importable' => false,
      'default' => 0,
    ),
    'geocode_status' => 
    array (
      'studio' => false,
      'name' => 'geocode_status',
      'vname' => 'LBL_MAPS_GEOCODE_STATUS',
      'label' => 'LBL_MAPS_GEOCODE_STATUS',
      'type' => 'geocodestatus',
      'len' => 255,
      'comment' => '',
      'dbType' => 'varchar',
      'licenseFilter' => 
      array (
        0 => 'MAPS',
      ),
      'readonly' => true,
      'reportable' => false,
    ),
    'mkto_sync' => 
    array (
      'name' => 'mkto_sync',
      'vname' => 'LBL_MKTO_SYNC',
      'type' => 'bool',
      'default' => '0',
      'comment' => 'Should the Lead be synced to Marketo',
      'massupdate' => true,
      'audited' => true,
      'duplicate_merge' => true,
      'reportable' => true,
      'importable' => 'true',
    ),
    'mkto_id' => 
    array (
      'name' => 'mkto_id',
      'vname' => 'LBL_MKTO_ID',
      'comment' => 'Associated Marketo Lead ID',
      'type' => 'int',
      'default' => NULL,
      'audited' => true,
      'mass_update' => false,
      'duplicate_merge' => true,
      'reportable' => true,
      'importable' => 'false',
    ),
    'ai_opp_close_week_scores' => 
    array (
      'name' => 'ai_opp_close_week_scores',
      'vname' => 'LBL_AI_OPP_CLOSE_WEEK_SCORES',
      'type' => 'json',
      'dbType' => 'longtext',
      'reportable' => true,
      'importable' => false,
      'readonly' => true,
      'duplicate_merge' => 'disabled',
      'studio' => false,
    ),
    'renewal' => 
    array (
      'name' => 'renewal',
      'vname' => 'LBL_RENEWAL',
      'type' => 'bool',
      'default' => 0,
      'comment' => 'Indicates whether the opportunity is a renewal',
    ),
    'ai_opp_conv_score_enum' => 
    array (
      'name' => 'ai_opp_conv_score_enum',
      'vname' => 'LBL_AI_CONV_SCORE_CLASSIFICATION_FIELD',
      'type' => 'enum',
      'options' => 'ai_conv_score_classification_dropdown',
      'default_value' => '',
      'reportable' => true,
      'audited' => true,
      'importable' => false,
      'listview' => true,
      'readonly' => true,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
      'displayParams' => 
      array (
        'icon' => 
        array (
          'type' => 'sicon-sugar-predict',
          'tooltip' => 'LBL_PREDICT_TOOLTIP',
        ),
      ),
    ),
    'following' => 
    array (
      'massupdate' => false,
      'name' => 'following',
      'vname' => 'LBL_FOLLOWING',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Is user following this record',
      'studio' => 'false',
      'link' => 'following_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'following_link' => 
    array (
      'name' => 'following_link',
      'type' => 'link',
      'relationship' => 'opportunities_following',
      'source' => 'non-db',
      'vname' => 'LBL_FOLLOWING',
      'reportable' => false,
    ),
    'my_favorite' => 
    array (
      'massupdate' => false,
      'name' => 'my_favorite',
      'vname' => 'LBL_FAVORITE',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Favorite for the user',
      'studio' => 
      array (
        'list' => false,
        'recordview' => false,
        'basic_search' => false,
        'advanced_search' => false,
      ),
      'link' => 'favorite_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'favorite_link' => 
    array (
      'name' => 'favorite_link',
      'type' => 'link',
      'relationship' => 'opportunities_favorite',
      'source' => 'non-db',
      'vname' => 'LBL_FAVORITE',
      'reportable' => false,
      'workflow' => false,
      'full_text_search' => 
      array (
        'type' => 'favorites',
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'favorite_link' => 
          array (
            'type' => 'MyItems',
            'options' => 
            array (
              'field' => 'user_favorites',
            ),
          ),
        ),
      ),
    ),
    'tag' => 
    array (
      'name' => 'tag',
      'vname' => 'LBL_TAGS',
      'type' => 'tag',
      'link' => 'tag_link',
      'source' => 'non-db',
      'module' => 'Tags',
      'relate_collection' => true,
      'studio' => 
      array (
        'portal' => false,
        'base' => 
        array (
          'popuplist' => false,
          'popupsearch' => false,
        ),
        'mobile' => 
        array (
          'wirelesseditview' => true,
          'wirelessdetailview' => true,
        ),
      ),
      'massupdate' => true,
      'exportable' => true,
      'sortable' => false,
      'rname' => 'name',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'tag_link' => 
    array (
      'name' => 'tag_link',
      'type' => 'link',
      'vname' => 'LBL_TAGS_LINK',
      'relationship' => 'opportunities_tags',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'commentlog' => 
    array (
      'name' => 'commentlog',
      'vname' => 'LBL_COMMENTLOG',
      'type' => 'collection',
      'displayParams' => 
      array (
        'type' => 'commentlog',
        'fields' => 
        array (
          0 => 'entry',
          1 => 'date_entered',
          2 => 'created_by_name',
        ),
        'max_num' => 100,
      ),
      'links' => 
      array (
        0 => 'commentlog_link',
      ),
      'order_by' => 'date_entered:asc',
      'source' => 'non-db',
      'module' => 'CommentLog',
      'studio' => 
      array (
        'listview' => false,
        'recordview' => true,
        'previewview' => true,
        'wirelesseditview' => false,
        'wirelessdetailview' => true,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
      ),
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
      ),
    ),
    'commentlog_link' => 
    array (
      'name' => 'commentlog_link',
      'type' => 'link',
      'vname' => 'LBL_COMMENTLOG_LINK',
      'relationship' => 'opportunities_commentlog',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'locked_fields' => 
    array (
      'name' => 'locked_fields',
      'vname' => 'LBL_LOCKED_FIELDS',
      'type' => 'locked_fields',
      'link' => 'locked_fields_link',
      'source' => 'non-db',
      'module' => 'pmse_BpmProcessDefinition',
      'relate_collection' => true,
      'studio' => false,
      'massupdate' => false,
      'exportable' => false,
      'sortable' => false,
      'rname' => 'pro_locked_variables',
      'collection_fields' => 
      array (
        0 => 'pro_locked_variables',
      ),
      'full_text_search' => 
      array (
        'enabled' => false,
        'searchable' => false,
      ),
      'hideacl' => true,
    ),
    'locked_fields_link' => 
    array (
      'name' => 'locked_fields_link',
      'type' => 'link',
      'vname' => 'LBL_LOCKED_FIELDS_LINK',
      'relationship' => 'opportunities_locked_fields',
      'source' => 'non-db',
      'exportable' => false,
      'duplicate_merge' => 'disabled',
    ),
    'sync_key' => 
    array (
      'is_sync_key' => true,
      'name' => 'sync_key',
      'vname' => 'LBL_SYNC_KEY',
      'type' => 'varchar',
      'enforced' => '',
      'required' => false,
      'massupdate' => false,
      'readonly' => true,
      'default' => NULL,
      'isnull' => true,
      'no_default' => false,
      'comments' => 'External default id of the remote integration record',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'merge_filter' => 'disabled',
      'duplicate_on_record_copy' => 'no',
      'audited' => true,
      'reportable' => true,
      'unified_search' => false,
      'calculated' => false,
      'len' => '100',
      'size' => '20',
      'studio' => 
      array (
        'recordview' => true,
        'wirelessdetailview' => true,
        'listview' => false,
        'wirelesseditview' => false,
        'wirelesslistview' => false,
        'wireless_basic_search' => false,
        'wireless_advanced_search' => false,
        'portallistview' => false,
        'portalrecordview' => false,
        'portaleditview' => false,
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO_ID',
      'group' => 'assigned_user_name',
      'type' => 'id',
      'reportable' => false,
      'isnull' => 'false',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'User ID assigned to record',
      'duplicate_merge' => 'disabled',
      'mandatory_fetch' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'assigned_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_ASSIGNED_TO_ME',
          ),
        ),
      ),
    ),
    'assigned_user_name' => 
    array (
      'name' => 'assigned_user_name',
      'link' => 'assigned_user_link',
      'vname' => 'LBL_ASSIGNED_TO',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'assigned_user_id',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
      'related_fields' => 
      array (
        0 => 'assigned_user_id',
      ),
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'opportunities_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'duplicate_merge' => 'enabled',
      'id_name' => 'assigned_user_id',
      'table' => 'users',
      'side' => 'right',
    ),
    'team_id' => 
    array (
      'name' => 'team_id',
      'vname' => 'LBL_TEAM_ID',
      'group' => 'team_name',
      'reportable' => false,
      'dbType' => 'id',
      'type' => 'team_list',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'Team ID for the account',
    ),
    'team_set_id' => 
    array (
      'name' => 'team_set_id',
      'rname' => 'id',
      'id_name' => 'team_set_id',
      'vname' => 'LBL_TEAM_SET_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => 'false',
      'dbType' => 'id',
      'duplicate_on_record_copy' => 'always',
    ),
    'acl_team_set_id' => 
    array (
      'name' => 'acl_team_set_id',
      'vname' => 'LBL_TEAM_SET_SELECTED_ID',
      'type' => 'id',
      'audited' => true,
      'studio' => false,
      'isnull' => true,
      'duplicate_on_record_copy' => 'always',
      'reportable' => false,
    ),
    'team_count' => 
    array (
      'name' => 'team_count',
      'rname' => 'team_count',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'join_name' => 'ts1',
      'table' => 'teams',
      'type' => 'relate',
      'required' => 'true',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_count_link',
      'massupdate' => false,
      'dbType' => 'int',
      'source' => 'non-db',
      'importable' => 'false',
      'reportable' => false,
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'studio' => 'false',
      'hideacl' => true,
    ),
    'team_name' => 
    array (
      'name' => 'team_name',
      'db_concat_fields' => 
      array (
        0 => 'name',
        1 => 'name_2',
      ),
      'sort_on' => 'tj.name',
      'join_name' => 'tj',
      'rname' => 'name',
      'id_name' => 'team_id',
      'vname' => 'LBL_TEAMS',
      'type' => 'relate',
      'required' => 'true',
      'table' => 'teams',
      'isnull' => 'true',
      'module' => 'Teams',
      'link' => 'team_link',
      'massupdate' => true,
      'dbType' => 'varchar',
      'source' => 'non-db',
      'custom_type' => 'teamset',
      'studio' => 
      array (
        'portallistview' => false,
        'portalrecordview' => false,
      ),
      'duplicate_on_record_copy' => 'always',
      'exportable' => true,
      'fields' => 
      array (
        0 => 'acl_team_set_id',
      ),
    ),
    'acl_team_names' => 
    array (
      'name' => 'acl_team_names',
      'table' => 'teams',
      'module' => 'Teams',
      'vname' => 'LBL_TEAM_SET_SELECTED_TEAMS',
      'rname' => 'name',
      'id_name' => 'acl_team_set_id',
      'source' => 'non-db',
      'link' => 'team_link',
      'type' => 'relate',
      'custom_type' => 'teamset',
      'exportable' => true,
      'studio' => false,
      'massupdate' => false,
      'hideacl' => true,
    ),
    'team_link' => 
    array (
      'name' => 'team_link',
      'type' => 'link',
      'relationship' => 'opportunities_team',
      'vname' => 'LBL_TEAMS_LINK',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'Team',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'studio' => 'false',
      'side' => 'right',
    ),
    'team_count_link' => 
    array (
      'name' => 'team_count_link',
      'type' => 'link',
      'relationship' => 'opportunities_team_count_relationship',
      'link_type' => 'one',
      'module' => 'Teams',
      'bean_name' => 'TeamSet',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
      'reportable' => false,
      'studio' => 'false',
      'side' => 'right',
    ),
    'teams' => 
    array (
      'name' => 'teams',
      'type' => 'link',
      'relationship' => 'opportunities_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'currency_id' => 
    array (
      'name' => 'currency_id',
      'dbType' => 'id',
      'vname' => 'LBL_CURRENCY_ID',
      'type' => 'currency_id',
      'function' => 'getCurrencies',
      'function_bean' => 'Currencies',
      'required' => false,
      'reportable' => false,
      'default' => '-99',
    ),
    'base_rate' => 
    array (
      'name' => 'base_rate',
      'vname' => 'LBL_CURRENCY_RATE',
      'type' => 'text',
      'dbType' => 'decimal',
      'len' => '26,6',
      'readonly' => true,
    ),
    'currency_name' => 
    array (
      'name' => 'currency_name',
      'rname' => 'name',
      'id_name' => 'currency_id',
      'vname' => 'LBL_CURRENCY_NAME',
      'type' => 'relate',
      'link' => 'currencies',
      'isnull' => true,
      'table' => 'currencies',
      'module' => 'Currencies',
      'source' => 'non-db',
      'studio' => false,
      'duplicate_merge' => 'disabled',
      'function' => 'getCurrencies',
      'function_bean' => 'Currencies',
      'massupdate' => false,
    ),
    'currency_symbol' => 
    array (
      'name' => 'currency_symbol',
      'rname' => 'symbol',
      'id_name' => 'currency_id',
      'vname' => 'LBL_CURRENCY_SYMBOL',
      'type' => 'relate',
      'link' => 'currencies',
      'isnull' => true,
      'table' => 'currencies',
      'module' => 'Currencies',
      'source' => 'non-db',
      'studio' => false,
      'duplicate_merge' => 'disabled',
      'function' => 'getCurrencySymbols',
      'function_bean' => 'Currencies',
      'massupdate' => false,
    ),
    'currencies' => 
    array (
      'name' => 'currencies',
      'type' => 'link',
      'relationship' => 'opportunities_currencies',
      'source' => 'non-db',
      'vname' => 'LBL_CURRENCIES',
    ),
    'is_escalated' => 
    array (
      'name' => 'is_escalated',
      'vname' => 'LBL_IS_ESCALATED',
      'type' => 'bool',
      'default' => '0',
      'readonly' => true,
      'displayParams' => 
      array (
        'type' => 'badge',
        'badge_label' => 'LBL_ESCALATED',
        'warning_level' => 'important',
      ),
      'comment' => 'Is this escalated?',
      'studio' => 
      array (
        'previewview' => false,
        'recorddashletview' => false,
        'portalrecordview' => false,
        'portallistview' => false,
        'mobile' => 
        array (
          'wirelesseditview' => false,
          'wirelessdetailview' => false,
        ),
      ),
    ),
    'dri_workflows' => 
    array (
      'name' => 'dri_workflows',
      'vname' => 'LBL_DRI_WORKFLOWS',
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'left',
      'bean_name' => 'DRI_Workflow',
      'relationship' => 'dri_workflow_opportunities',
      'module' => 'DRI_Workflows',
    ),
    'dri_workflow_template_id' => 
    array (
      'name' => 'dri_workflow_template_id',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => true,
      'type' => 'enum',
      'placeholder' => 'LBL_SELECT_SMART_GUIDE_TEMPLATE_PLACEHOLDER',
      'options' => NULL,
      'dbType' => 'id',
      'processes' => true,
      'studio' => false,
      'function' => 
      array (
        'name' => 'listSmartGuideTemplatesByModule',
        'params' => 
        array (
          0 => 'Opportunities',
        ),
      ),
    ),
    'dri_workflow_template_name' => 
    array (
      'name' => 'dri_workflow_template_name',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'importable' => 'true',
      'massupdate' => false,
      'studio' => false,
      'source' => 'non-db',
      'type' => 'relate',
      'rname' => 'name',
      'table' => 'dri_workflow_templates',
      'id_name' => 'dri_workflow_template_id',
      'sort_on' => 'name',
      'module' => 'DRI_Workflow_Templates',
      'link' => 'dri_workflow_template_link',
    ),
    'dri_workflow_template_link' => 
    array (
      'name' => 'dri_workflow_template_link',
      'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
      'reportable' => false,
      'source' => 'non-db',
      'type' => 'link',
      'side' => 'right',
      'bean_name' => 'DRI_Workflow_Template',
      'relationship' => 'opportunity_dri_workflow_templates',
      'module' => 'DRI_Workflow_Templates',
    ),
    'perform_sugar_action' => 
    array (
      'name' => 'perform_sugar_action',
      'vname' => 'LBL_PERFORM_SUGAR_ACTION',
      'type' => 'bool',
      'studio' => false,
      'massupdate' => false,
      'reportable' => false,
      'default' => 0,
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_opportunities_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_opportunities_del_d_m',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_modified',
        2 => 'id',
      ),
    ),
    'deleted' => 
    array (
      'name' => 'idx_opportunities_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_opportunities_del_d_e',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'date_entered',
        2 => 'id',
      ),
    ),
    'name_del' => 
    array (
      'name' => 'idx_opportunities_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_opp_name',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'name',
        2 => 'date_modified',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_opportunity_next_step',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'next_step',
        2 => 'date_modified',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_opp_del_renewal_pid',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'renewal_parent_id',
        2 => 'id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_opportunities_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_opportunities_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_opportunities' => 
    array (
      'name' => 'idx_opportunities_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_opportunities' => 
    array (
      'name' => 'idx_opportunities_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
    'idx_opportunities_cjtpl_id' => 
    array (
      'name' => 'idx_opportunities_cjtpl_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'dri_workflow_template_id',
      ),
    ),
  ),
  'relationships' => 
  array (
    'opportunities_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'opportunity_activities' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Activities',
      'rhs_table' => 'activities',
      'rhs_key' => 'id',
      'rhs_vname' => 'LBL_ACTIVITY_STREAM',
      'relationship_type' => 'many-to-many',
      'join_table' => 'activities_users',
      'join_key_lhs' => 'parent_id',
      'join_key_rhs' => 'activity_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_calls' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_meetings' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_tasks' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Tasks',
      'rhs_table' => 'tasks',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_notes' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_emails' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Emails',
      'rhs_table' => 'emails',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunity_leads' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Leads',
      'rhs_table' => 'leads',
      'rhs_key' => 'opportunity_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_revenuelineitems' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'RevenueLineItems',
      'rhs_table' => 'revenue_line_items',
      'rhs_key' => 'opportunity_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunity_messages' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'renewals_opportunities' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'renewal_parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunity_escalations' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Escalations',
      'rhs_table' => 'escalations',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunities_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Opportunities',
      'user_field' => 'created_by',
    ),
    'opportunities_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Opportunities',
      'user_field' => 'created_by',
    ),
    'opportunities_tags' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Opportunities',
      'dynamic_subpanel' => true,
    ),
    'opportunities_commentlog' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunities_locked_fields' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Opportunities',
    ),
    'opportunities_audit' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'opportunities_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_teams' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'opportunities_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunities_currencies' => 
    array (
      'lhs_module' => 'Currencies',
      'lhs_table' => 'currencies',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'currency_id',
      'relationship_type' => 'one-to-many',
    ),
    'opportunity_dri_workflow_templates' => 
    array (
      'relationship_type' => 'one-to-many',
      'lhs_key' => 'id',
      'lhs_module' => 'DRI_Workflow_Templates',
      'lhs_table' => 'dri_workflow_templates',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'dri_workflow_template_id',
    ),
  ),
  'duplicate_check' => 
  array (
    'enabled' => true,
    'FilterDuplicateCheck' => 
    array (
      'filter_template' => 
      array (
        0 => 
        array (
          '$and' => 
          array (
            0 => 
            array (
              'name' => 
              array (
                '$starts' => '$name',
              ),
            ),
            1 => 
            array (
              'sales_status' => 
              array (
                '$not_equals' => 'Closed Lost',
              ),
            ),
            2 => 
            array (
              'sales_status' => 
              array (
                '$not_equals' => 'Closed Won',
              ),
            ),
            3 => 
            array (
              'accounts.id' => 
              array (
                '$equals' => '$account_id',
              ),
            ),
          ),
        ),
      ),
      'ranking_fields' => 
      array (
        0 => 
        array (
          'in_field_name' => 'name',
          'dupe_field_name' => 'name',
        ),
      ),
    ),
  ),
  'optimistic_locking' => true,
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
  ),
  'acls' => 
  array (
    'SugarACLLockedFields' => true,
    'SugarACLStatic' => true,
  ),
  'favorites' => true,
  'templates' => 
  array (
    'basic' => 'basic',
    'following' => 'following',
    'favorite' => 'favorite',
    'taggable' => 'taggable',
    'commentlog' => 'commentlog',
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
    'assignable' => 'assignable',
    'team_security' => 'team_security',
    'currency' => 'currency',
    'escalatable' => 'escalatable',
    'customer_journey_parent' => 'customer_journey_parent',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
    0 => 'revenuelineitems',
    1 => 'forecastworksheets',
  ),
);
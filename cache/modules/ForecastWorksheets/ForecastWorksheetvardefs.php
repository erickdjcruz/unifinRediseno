<?php 
 $GLOBALS["dictionary"]["ForecastWorksheet"]=array (
  'table' => 'forecast_worksheets',
  'studio' => false,
  'acls' => 
  array (
    'SugarACLForecastWorksheets' => true,
    'SugarACLLockedFields' => true,
  ),
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
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.55,
      ),
      'required' => true,
      'importable' => 'required',
      'duplicate_merge' => 'enabled',
      'merge_filter' => 'selected',
      'duplicate_on_record_copy' => 'always',
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
        'boost' => 0.5,
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
      'relationship' => 'forecastworksheets_created_by',
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
      'relationship' => 'forecastworksheets_modified_user',
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
      'relationship' => 'forecastworksheet_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ACCOUNT_ID',
      'type' => 'id',
      'group' => 'parent_name',
      'required' => false,
      'reportable' => false,
      'audited' => false,
      'comment' => 'Account ID of the parent of this account',
      'studio' => false,
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_TYPE',
      'type' => 'parent_type',
      'dbType' => 'varchar',
      'group' => 'parent_name',
      'options' => 'parent_type_display',
      'len' => '255',
      'comment' => 'Sugar module the Worksheet is associated with',
      'studio' => false,
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'parent_type' => 'record_type_display',
      'type_name' => 'parent_type',
      'id_name' => 'parent_id',
      'vname' => 'LBL_NAME',
      'type' => 'parent',
      'group' => 'parent_name',
      'source' => 'non-db',
      'options' => 'parent_type_display',
      'studio' => true,
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'parent_id',
        1 => 'parent_type',
        2 => 'parent_deleted',
        3 => 'name',
      ),
    ),
    'opportunity_id' => 
    array (
      'name' => 'opportunity_id',
      'vname' => 'LBL_OPPORTUNITY_ID',
      'type' => 'id',
      'audited' => false,
      'studio' => false,
      'link' => 'opportunity',
    ),
    'opportunity_name' => 
    array (
      'name' => 'opportunity_name',
      'id_name' => 'opportunity_id',
      'module' => 'Opportunities',
      'vname' => 'LBL_OPPORTUNITY_NAME',
      'type' => 'relate',
      'dbType' => 'varchar',
      'len' => '255',
      'studio' => false,
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'opportunity_id',
      ),
      'rname' => 'name',
      'link' => 'opportunity',
      'formula' => 'related($opportunity, "name")',
      'enforced' => true,
      'calculated' => true,
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'id_name' => 'account_id',
      'module' => 'Accounts',
      'vname' => 'LBL_ACCOUNT_NAME',
      'type' => 'relate',
      'dbType' => 'varchar',
      'len' => '255',
      'studio' => false,
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'account_id',
      ),
      'rname' => 'name',
      'link' => 'account',
      'formula' => 'related($account, "name")',
      'enforced' => true,
      'calculated' => true,
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_ID',
      'type' => 'id',
      'audited' => false,
      'studio' => false,
    ),
    'campaign_id' => 
    array (
      'name' => 'campaign_id',
      'vname' => 'LBL_CAMPAIGN_ID',
      'type' => 'id',
      'audited' => false,
      'studio' => false,
    ),
    'campaign_name' => 
    array (
      'name' => 'campaign_name',
      'id_name' => 'campaign_id',
      'rname' => 'name',
      'vname' => 'LBL_CAMPAIGN',
      'type' => 'relate',
      'dbType' => 'varchar',
      'len' => '255',
      'module' => 'Campaigns',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'campaign_id',
      ),
      'link' => 'campaign',
      'formula' => 'related($campaign, "name")',
      'enforced' => true,
      'calculated' => true,
    ),
    'product_template_id' => 
    array (
      'name' => 'product_template_id',
      'vname' => 'LBL_PRODUCT_TEMPLATE_ID',
      'type' => 'id',
      'audited' => false,
      'studio' => false,
    ),
    'product_template_name' => 
    array (
      'name' => 'product_template_name',
      'id_name' => 'product_template_id',
      'rname' => 'name',
      'vname' => 'LBL_PRODUCT',
      'type' => 'relate',
      'dbType' => 'varchar',
      'len' => '255',
      'module' => 'ProductTemplates',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'product_template_id',
      ),
      'link' => 'template',
      'formula' => 'related($template, "name")',
      'enforced' => true,
      'calculated' => true,
    ),
    'category_id' => 
    array (
      'name' => 'category_id',
      'vname' => 'LBL_CATEGORY',
      'type' => 'id',
      'required' => false,
      'reportable' => true,
    ),
    'category_name' => 
    array (
      'name' => 'category_name',
      'id_name' => 'category_id',
      'rname' => 'name',
      'vname' => 'LBL_CATEGORY_NAME',
      'type' => 'relate',
      'module' => 'ProductCategories',
      'dbType' => 'varchar',
      'len' => '255',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'category_id',
      ),
      'link' => 'category',
      'formula' => 'related($category, "name")',
      'enforced' => true,
      'calculated' => true,
    ),
    'sales_status' => 
    array (
      'name' => 'sales_status',
      'vname' => 'LBL_SALES_STATUS',
      'type' => 'enum',
      'options' => 'sales_status_dom',
      'len' => '255',
      'sortable' => true,
      'audited' => true,
    ),
    'likely_case' => 
    array (
      'name' => 'likely_case',
      'vname' => 'LBL_LIKELY',
      'dbType' => 'currency',
      'type' => 'currency',
      'len' => '26,6',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
      ),
      'audited' => false,
      'studio' => false,
      'align' => 'right',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'base_rate',
        1 => 'currency_id',
        2 => 'best_case',
        3 => 'worst_case',
      ),
      'convertToBase' => true,
      'skip_preferred_conversion' => true,
    ),
    'best_case' => 
    array (
      'name' => 'best_case',
      'vname' => 'LBL_BEST',
      'dbType' => 'currency',
      'type' => 'currency',
      'len' => '26,6',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
      ),
      'audited' => false,
      'studio' => false,
      'align' => 'right',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'base_rate',
        1 => 'currency_id',
      ),
      'convertToBase' => true,
      'skip_preferred_conversion' => true,
    ),
    'worst_case' => 
    array (
      'name' => 'worst_case',
      'vname' => 'LBL_WORST',
      'dbType' => 'currency',
      'type' => 'currency',
      'len' => '26,6',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
      ),
      'audited' => false,
      'studio' => false,
      'align' => 'right',
      'sortable' => true,
      'related_fields' => 
      array (
        0 => 'base_rate',
        1 => 'currency_id',
      ),
      'convertToBase' => true,
      'skip_preferred_conversion' => true,
    ),
    'date_closed' => 
    array (
      'name' => 'date_closed',
      'vname' => 'LBL_DATE_CLOSED',
      'type' => 'date',
      'audited' => false,
      'comment' => 'Expected or actual date the oppportunity will close',
      'importable' => 'required',
      'required' => true,
      'enable_range_search' => true,
      'sortable' => true,
      'options' => 'date_range_search_dom',
      'studio' => false,
      'related_fields' => 
      array (
        0 => 'date_closed_timestamp',
      ),
    ),
    'date_closed_timestamp' => 
    array (
      'name' => 'date_closed_timestamp',
      'vname' => 'LBL_DATE_CLOSED_TIMESTAMP',
      'type' => 'ulong',
      'studio' => false,
    ),
    'sales_stage' => 
    array (
      'name' => 'sales_stage',
      'vname' => 'LBL_SALES_STAGE',
      'type' => 'enum',
      'options' => 'sales_stage_dom',
      'len' => '255',
      'audited' => false,
      'comment' => 'Indication of progression towards closure',
      'merge_filter' => 'enabled',
      'importable' => 'required',
      'sortable' => true,
      'required' => true,
      'studio' => false,
      'related_fields' => 
      array (
        0 => 'probability',
      ),
    ),
    'probability' => 
    array (
      'name' => 'probability',
      'vname' => 'LBL_OW_PROBABILITY',
      'type' => 'int',
      'dbType' => 'double',
      'audited' => false,
      'comment' => 'The probability of closure',
      'validation' => 
      array (
        'type' => 'range',
        'min' => 0,
        'max' => 100,
      ),
      'merge_filter' => 'enabled',
      'sortable' => true,
      'studio' => false,
      'formula' => 'getDropdownValue("sales_probability_dom",$sales_stage)',
      'calculated' => true,
      'enforced' => true,
      'related_fields' => 
      array (
        0 => 'sales_stage',
      ),
    ),
    'commit_stage' => 
    array (
      'name' => 'commit_stage',
      'vname' => 'LBL_FORECAST',
      'type' => 'enum',
      'len' => '50',
      'comment' => 'Forecast commit ranges: Include, Likely, Omit etc.',
      'sortable' => true,
      'studio' => false,
      'function' => 'getCommitStageDropdown',
      'function_bean' => 'Forecasts',
      'formula' => 'forecastCommitStage($probability)',
      'calculated' => true,
      'related_fields' => 
      array (
        0 => 'probability',
      ),
    ),
    'draft' => 
    array (
      'name' => 'draft',
      'vname' => 'LBL_DRAFT',
      'default' => 0,
      'type' => 'int',
      'comment' => 'Is A Draft Version',
      'studio' => false,
    ),
    'next_step' => 
    array (
      'name' => 'next_step',
      'vname' => 'LBL_NEXT_STEP',
      'type' => 'varchar',
      'len' => '100',
      'comment' => 'The next step in the sales process',
      'merge_filter' => 'enabled',
    ),
    'lead_source' => 
    array (
      'name' => 'lead_source',
      'vname' => 'LBL_LEAD_SOURCE',
      'type' => 'enum',
      'options' => 'lead_source_dom',
      'len' => '50',
      'comment' => 'Source of the product',
      'sortable' => true,
      'merge_filter' => 'enabled',
    ),
    'product_type' => 
    array (
      'name' => 'product_type',
      'vname' => 'LBL_TYPE',
      'type' => 'enum',
      'options' => 'opportunity_type_dom',
      'len' => '255',
      'audited' => true,
      'comment' => 'Type of product ( from opportunities opportunity_type ex: Existing, New)',
      'merge_filter' => 'enabled',
    ),
    'list_price' => 
    array (
      'name' => 'list_price',
      'vname' => 'LBL_LIST_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => true,
      'sortable' => true,
      'align' => 'right',
      'comment' => 'List price of product ("List" in Quote)',
    ),
    'cost_price' => 
    array (
      'name' => 'cost_price',
      'vname' => 'LBL_COST_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => true,
      'sortable' => true,
      'align' => 'right',
      'comment' => 'Product cost ("Cost" in Quote)',
    ),
    'discount_price' => 
    array (
      'name' => 'discount_price',
      'vname' => 'LBL_DISCOUNT_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => true,
      'sortable' => true,
      'align' => 'right',
      'comment' => 'Discounted price ("Unit Price" in Quote)',
    ),
    'discount_amount' => 
    array (
      'name' => 'discount_amount',
      'vname' => 'LBL_TOTAL_DISCOUNT_AMOUNT',
      'type' => 'currency',
      'options' => 'discount_amount_class_dom',
      'len' => '26,6',
      'precision' => 6,
      'sortable' => true,
      'align' => 'right',
      'comment' => 'Discounted amount',
    ),
    'quantity' => 
    array (
      'name' => 'quantity',
      'vname' => 'LBL_QUANTITY',
      'type' => 'int',
      'len' => 5,
      'comment' => 'Quantity in use',
      'sortable' => true,
      'align' => 'right',
      'default' => 1,
    ),
    'total_amount' => 
    array (
      'name' => 'total_amount',
      'vname' => 'LBL_CALCULATED_LINE_ITEM_AMOUNT',
      'reportable' => false,
      'sortable' => true,
      'align' => 'right',
      'type' => 'currency',
    ),
    'parent_deleted' => 
    array (
      'name' => 'parent_deleted',
      'default' => 0,
      'type' => 'int',
      'comment' => 'Is Parent Deleted',
      'studio' => false,
      'source' => 'non-db',
    ),
    'opportunity' => 
    array (
      'name' => 'opportunity',
      'type' => 'link',
      'relationship' => 'forecastworksheets_opportunities',
      'source' => 'non-db',
      'vname' => 'LBL_OPPORTUNITY',
    ),
    'account' => 
    array (
      'name' => 'account',
      'type' => 'link',
      'relationship' => 'forecastworksheets_accounts',
      'source' => 'non-db',
      'vname' => 'LBL_ACCOUNTS',
    ),
    'campaign' => 
    array (
      'name' => 'campaign',
      'type' => 'link',
      'relationship' => 'forecastworksheets_campaigns',
      'source' => 'non-db',
      'vname' => 'LBL_CAMPAIGN',
    ),
    'template' => 
    array (
      'name' => 'template',
      'type' => 'link',
      'relationship' => 'forecastworksheets_templates',
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCT',
    ),
    'category' => 
    array (
      'name' => 'category',
      'type' => 'link',
      'relationship' => 'forecastworksheets_categories',
      'source' => 'non-db',
      'vname' => 'LBL_CATEGORY',
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
      'relationship' => 'forecastworksheets_following',
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
      'relationship' => 'forecastworksheets_favorite',
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
      'relationship' => 'forecastworksheets_locked_fields',
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
      'relationship' => 'forecastworksheets_assigned_user',
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
      'relationship' => 'forecastworksheets_team',
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
      'relationship' => 'forecastworksheets_team_count_relationship',
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
      'relationship' => 'forecastworksheets_teams',
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
      'relationship' => 'forecastworksheets_currencies',
      'source' => 'non-db',
      'vname' => 'LBL_CURRENCIES',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_forecastworksheets_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_forecast_worksheets_del_d_m',
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
      'name' => 'idx_forecast_worksheets_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_forecast_worksheets_del_d_e',
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
      'name' => 'idx_forecast_worksheets_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_worksheets_parent',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_id',
        1 => 'parent_type',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_worksheets_assigned_del_time_draft_parent_type',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'assigned_user_id',
        2 => 'draft',
        3 => 'date_closed_timestamp',
        4 => 'parent_type',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_forecastworksheet_commit_stage',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'commit_stage',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_forecastworksheet_sales_stage',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'sales_stage',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_forecastworksheet_aid_del_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'account_id',
        1 => 'deleted',
        2 => 'id',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_forecastworksheet_oppid_del_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'opportunity_id',
        1 => 'deleted',
        2 => 'id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_forecast_worksheets_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_forecast_worksheets_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_forecast_worksheets' => 
    array (
      'name' => 'idx_forecast_worksheets_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_forecast_worksheets' => 
    array (
      'name' => 'idx_forecast_worksheets_acl_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'acl_team_set_id',
        1 => 'deleted',
      ),
    ),
  ),
  'relationships' => 
  array (
    'forecastworksheets_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheet_activities' => 
    array (
      'lhs_module' => 'ForecastWorksheets',
      'lhs_table' => 'forecast_worksheets',
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
      'relationship_role_column_value' => 'ForecastWorksheets',
    ),
    'forecastworksheets_accounts' => 
    array (
      'lhs_module' => 'Accounts',
      'lhs_table' => 'accounts',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'account_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_opportunities' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'opportunity_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_campaigns' => 
    array (
      'lhs_module' => 'Campaigns',
      'lhs_table' => 'campaigns',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'campaign_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_templates' => 
    array (
      'lhs_module' => 'ProductTemplates',
      'lhs_table' => 'product_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'product_template_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_categories' => 
    array (
      'lhs_module' => 'ProductCategories',
      'lhs_table' => 'product_categories',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'category_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'ForecastWorksheets',
      'user_field' => 'created_by',
    ),
    'forecastworksheets_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'ForecastWorksheets',
      'user_field' => 'created_by',
    ),
    'forecastworksheets_locked_fields' => 
    array (
      'lhs_module' => 'ForecastWorksheets',
      'lhs_table' => 'forecast_worksheets',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'ForecastWorksheets',
    ),
    'forecastworksheets_audit' => 
    array (
      'lhs_module' => 'ForecastWorksheets',
      'lhs_table' => 'forecast_worksheets',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'forecast_worksheets_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_teams' => 
    array (
      'lhs_module' => 'ForecastWorksheets',
      'lhs_table' => 'forecast_worksheets',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'forecastworksheets_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'forecastworksheets_currencies' => 
    array (
      'lhs_module' => 'Currencies',
      'lhs_table' => 'currencies',
      'lhs_key' => 'id',
      'rhs_module' => 'ForecastWorksheets',
      'rhs_table' => 'forecast_worksheets',
      'rhs_key' => 'currency_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'ignore_templates' => 
  array (
    0 => 'taggable',
    1 => 'commentlog',
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
    'TeamSecurity' => true,
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
          'name' => 
          array (
            '$starts' => '$name',
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
  'favorites' => true,
  'templates' => 
  array (
    'basic' => 'basic',
    'following' => 'following',
    'favorite' => 'favorite',
    'lockable_fields' => 'lockable_fields',
    'integrate_fields' => 'integrate_fields',
    'audit' => 'audit',
    'assignable' => 'assignable',
    'team_security' => 'team_security',
    'currency' => 'currency',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);
<?php 
 $GLOBALS["dictionary"]["ProductBundle"]=array (
  'table' => 'product_bundles',
  'archive' => false,
  'comment' => 'Quote groups',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_NAME',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
      'comment' => 'Unique identifier',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => false,
      'default' => '0',
      'reportable' => false,
      'comment' => 'Record deletion indicator',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date record created',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date record last modified',
    ),
    'modified_user_id' => 
    array (
      'name' => 'modified_user_id',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'reportable' => true,
      'comment' => 'User who last modified record',
    ),
    'created_by' => 
    array (
      'name' => 'created_by',
      'rname' => 'user_name',
      'id_name' => 'modified_user_id',
      'vname' => 'LBL_ASSIGNED_TO',
      'type' => 'assigned_user_name',
      'table' => 'users',
      'isnull' => 'false',
      'dbType' => 'id',
      'comment' => 'User who created record',
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => '255',
      'comment' => 'Name of the group',
    ),
    'bundle_stage' => 
    array (
      'name' => 'bundle_stage',
      'vname' => 'LBL_BUNDLE_STAGE',
      'type' => 'varchar',
      'len' => '255',
      'comment' => 'Processing stage of the group (ex: Draft)',
    ),
    'description' => 
    array (
      'name' => 'description',
      'vname' => 'LBL_DESCRIPTION',
      'type' => 'text',
      'comment' => 'Group description',
    ),
    'taxrate_id' => 
    array (
      'name' => 'taxrate_id',
      'vname' => 'LBL_TAXRATE_ID',
      'type' => 'id',
    ),
    'tax' => 
    array (
      'name' => 'tax',
      'vname' => 'LBL_TAX',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Tax rate applied to items in the group',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'taxrate_id',
        3 => 'new_sub',
      ),
    ),
    'tax_usdollar' => 
    array (
      'name' => 'tax_usdollar',
      'vname' => 'LBL_TAX_USDOLLAR',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Total tax for all items in group in USD',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(and(isNumeric($tax), not(equal($tax, 0))), currencyDivide($tax, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'total' => 
    array (
      'name' => 'total',
      'vname' => 'LBL_TOTAL',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Total amount for all items in the group',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'new_sub',
        3 => 'tax',
        4 => 'shipping',
      ),
      'formula' => 'currencyAdd(
                $new_sub,
                "0"
            )',
      'calculated' => true,
      'enforced' => true,
    ),
    'total_usdollar' => 
    array (
      'name' => 'total_usdollar',
      'vname' => 'LBL_TOTAL_USDOLLAR',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Total amount for all items in the group in USD',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($total), currencyDivide($total, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'subtotal_usdollar' => 
    array (
      'name' => 'subtotal_usdollar',
      'vname' => 'LBL_SUBTOTAL_USDOLLAR',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Group total minus tax and shipping in USD',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($subtotal), currencyDivide($subtotal, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'shipping_usdollar' => 
    array (
      'name' => 'shipping_usdollar',
      'vname' => 'LBL_SHIPPING',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Shipping charge for group in USD',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($shipping), currencyDivide($shipping, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'deal_tot' => 
    array (
      'name' => 'deal_tot',
      'vname' => 'LBL_DEAL_TOT',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'discount amount',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'rollupCurrencySum($products, "deal_calc")',
      'calculated' => true,
      'enforced' => true,
    ),
    'deal_tot_usdollar' => 
    array (
      'name' => 'deal_tot_usdollar',
      'vname' => 'LBL_DEAL_TOT',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'discount amount',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($deal_tot), currencyDivide($deal_tot, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'new_sub' => 
    array (
      'name' => 'new_sub',
      'vname' => 'LBL_NEW_SUB',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Group total minus discount and tax and shipping',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'currencySubtract(
                rollupCurrencySum($products, "subtotal"),
                rollupCurrencySum($products, "deal_calc")
             )',
      'enforced' => true,
      'calculated' => true,
    ),
    'new_sub_usdollar' => 
    array (
      'name' => 'new_sub_usdollar',
      'vname' => 'LBL_NEW_SUB',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Group total minus discount and tax and shipping',
      'studio' => 
      array (
        'mobile' => false,
      ),
      'readonly' => true,
      'is_base_currency' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($new_sub), currencyDivide($new_sub, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'subtotal' => 
    array (
      'name' => 'subtotal',
      'vname' => 'LBL_SUBTOTAL',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Group total minus tax and shipping',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'rollupCurrencySum($products, "subtotal")',
      'calculated' => true,
      'enforced' => true,
    ),
    'taxable_subtotal' => 
    array (
      'name' => 'taxable_subtotal',
      'vname' => 'LBL_TAXABLE_SUBTOTAL',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Rollup of all products marked as Taxable',
      'formula' => 'rollupConditionalSum($products, "total_amount", "tax_class", "Taxable")',
      'calculated' => true,
      'enforced' => true,
    ),
    'shipping' => 
    array (
      'name' => 'shipping',
      'vname' => 'LBL_SHIPPING',
      'type' => 'currency',
      'len' => '26,6',
      'disable_num_format' => true,
      'comment' => 'Shipping charge for group',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
    ),
    'taxrate' => 
    array (
      'name' => 'taxrate',
      'type' => 'link',
      'relationship' => 'product_bundle_taxrate',
      'module' => 'TaxRates',
      'bean_name' => 'TaxRate',
      'source' => 'non-db',
    ),
    'products' => 
    array (
      'name' => 'products',
      'type' => 'link',
      'relationship' => 'product_bundle_product',
      'module' => 'Products',
      'bean_name' => 'Product',
      'source' => 'non-db',
      'rel_fields' => 
      array (
        'product_index' => 
        array (
          'type' => 'integer',
        ),
      ),
      'vname' => 'LBL_PRODUCTS',
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'product_bundle_quote',
      'module' => 'Quotes',
      'bean_name' => 'Quote',
      'source' => 'non-db',
      'rel_fields' => 
      array (
        'bundle_index' => 
        array (
          'type' => 'integer',
        ),
      ),
      'relationship_fields' => 
      array (
        'bundle_index' => 'bundle_index',
      ),
      'vname' => 'LBL_QUOTES',
    ),
    'product_bundle_notes' => 
    array (
      'name' => 'product_bundle_notes',
      'type' => 'link',
      'relationship' => 'product_bundle_note',
      'module' => 'ProductBundleNotes',
      'bean_name' => 'ProductBundleNote',
      'source' => 'non-db',
      'rel_fields' => 
      array (
        'note_index' => 
        array (
          'type' => 'integer',
        ),
      ),
      'vname' => 'LBL_NOTES',
    ),
    'product_bundle_items' => 
    array (
      'name' => 'product_bundle_items',
      'type' => 'collection',
      'vname' => 'LBL_PRODUCT_BUNDLES',
      'links' => 
      array (
        0 => 'products',
        1 => 'product_bundle_notes',
      ),
      'source' => 'non-db',
      'order_by' => 'position:asc',
      'hideacl' => true,
    ),
    'position' => 
    array (
      'massupdate' => false,
      'name' => 'position',
      'type' => 'integer',
      'studio' => false,
      'vname' => 'LBL_QUOTE_BUNDLE_POSITION',
      'importable' => false,
      'source' => 'non-db',
      'link' => 'quotes',
      'rname_link' => 'bundle_index',
    ),
    'default_group' => 
    array (
      'name' => 'default_group',
      'type' => 'bool',
      'studio' => false,
      'vname' => 'LBL_QUOTE_BUNDLE_DEFAULT_GROUP',
      'importable' => false,
      'default' => false,
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
      'relationship' => 'productbundles_team',
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
      'relationship' => 'productbundles_team_count_relationship',
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
      'relationship' => 'productbundles_teams',
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
      'relationship' => 'productbundles_currencies',
      'source' => 'non-db',
      'vname' => 'LBL_CURRENCIES',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'procuct_bundlespk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_products_bundles',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_product_bundles_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'team_set_product_bundles' => 
    array (
      'name' => 'idx_product_bundles_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_product_bundles' => 
    array (
      'name' => 'idx_product_bundles_acl_tmst_id',
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
    'product_bundle_taxrate' => 
    array (
      'rhs_module' => 'ProductBundles',
      'rhs_table' => 'product_bundles',
      'rhs_key' => 'taxrate_id',
      'lhs_module' => 'TaxRates',
      'lhs_table' => 'taxrates',
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-many',
    ),
    'productbundles_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'ProductBundles',
      'rhs_table' => 'product_bundles',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'productbundles_teams' => 
    array (
      'lhs_module' => 'ProductBundles',
      'lhs_table' => 'product_bundles',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'productbundles_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'ProductBundles',
      'rhs_table' => 'product_bundles',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'productbundles_currencies' => 
    array (
      'lhs_module' => 'Currencies',
      'lhs_table' => 'currencies',
      'lhs_key' => 'id',
      'rhs_module' => 'ProductBundles',
      'rhs_table' => 'product_bundles',
      'rhs_key' => 'currency_id',
      'relationship_type' => 'one-to-many',
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
  ),
  'templates' => 
  array (
    'team_security' => 'team_security',
    'currency' => 'currency',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
    0 => 'quotes',
  ),
);
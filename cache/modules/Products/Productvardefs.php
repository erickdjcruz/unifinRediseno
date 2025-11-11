<?php 
 $GLOBALS["dictionary"]["Product"]=array (
  'table' => 'products',
  'color' => 'purple',
  'icon' => 'sicon-quoted-line-items-lg',
  'optimistic_locking' => true,
  'audited' => true,
  'activity_enabled' => true,
  'unified_search' => true,
  'full_text_search' => true,
  'unified_search_default_enabled' => true,
  'comment' => 'The user (not Admin)) view of a Product definition; an instance of a product',
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
      'dbType' => 'varchar',
      'type' => 'name',
      'len' => '255',
      'unified_search' => true,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 0.8,
      ),
      'comment' => 'Name of the product',
      'reportable' => true,
      'importable' => 'required',
      'required' => true,
      'formula' => 'ifElse(equal($product_template_name, ""), $name, $product_template_name)',
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
        'boost' => 0.4,
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
      'relationship' => 'products_created_by',
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
      'relationship' => 'products_modified_user',
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
      'relationship' => 'product_activities',
      'vname' => 'LBL_ACTIVITY_STREAM',
      'link_type' => 'many',
      'module' => 'Activities',
      'bean_name' => 'Activity',
      'source' => 'non-db',
    ),
    'revenuelineitem_id' => 
    array (
      'name' => 'revenuelineitem_id',
      'type' => 'id',
      'vname' => 'LBL_REVENUELINEITEM_ID',
      'required' => false,
      'reportable' => false,
      'comment' => 'Associated Revenue Line Item that served as the source.',
    ),
    'revenuelineitem_name' => 
    array (
      'name' => 'revenuelineitem_name',
      'rname' => 'name',
      'id_name' => 'revenuelineitem_id',
      'vname' => 'LBL_REVENUELINEITEM_NAME',
      'type' => 'relate',
      'link' => 'revenuelineitems',
      'isnull' => 'true',
      'module' => 'RevenueLineItems',
      'dbType' => 'varchar',
      'len' => '255',
      'source' => 'non-db',
      'studio' => 'visible',
      'massupdate' => true,
      'audited' => false,
      'hidemassupdate' => false,
      'duplicate_merge' => 'enabled',
      'duplicate_merge_dom_value' => 1,
      'merge_filter' => 'disabled',
      'calculated' => false,
    ),
    'product_template_id' => 
    array (
      'name' => 'product_template_id',
      'type' => 'id',
      'vname' => 'LBL_PRODUCT_TEMPLATE_ID',
      'required' => false,
      'reportable' => false,
      'related_fields' => 
      array (
        0 => 'service',
        1 => 'service_start_date',
        2 => 'service_end_date',
        3 => 'renewable',
        4 => 'service_duration_value',
        5 => 'service_duration_unit',
      ),
      'comment' => 'Product (in Admin Products) from which this product is derived (in user Products)',
    ),
    'product_template_name' => 
    array (
      'name' => 'product_template_name',
      'rname' => 'name',
      'id_name' => 'product_template_id',
      'vname' => 'LBL_PRODUCT_TEMPLATE',
      'join_name' => 'templates',
      'type' => 'relate',
      'link' => 'product_templates_link',
      'table' => 'product_templates',
      'isnull' => 'true',
      'module' => 'ProductTemplates',
      'dbType' => 'varchar',
      'len' => '255',
      'source' => 'non-db',
      'studio' => 
      array (
        'editview' => false,
        'detailview' => false,
        'quickcreate' => false,
      ),
      'auto_populate' => true,
      'populate_list' => 
      array (
        'name' => 'name',
        'category_id' => 'category_id',
        'category_name' => 'category_name',
        'mft_part_num' => 'mft_part_num',
        'list_price' => 'list_price',
        'cost_price' => 'cost_price',
        'discount_price' => 'discount_price',
        'list_usdollar' => 'list_usdollar',
        'cost_usdollar' => 'cost_usdollar',
        'discount_usdollar' => 'discount_usdollar',
        'tax_class' => 'tax_class',
        'weight' => 'weight',
        'type_id' => 'type_id',
        'type_name' => 'type_name',
        'manufacturer_id' => 'manufacturer_id',
        'manufacturer_name' => 'manufacturer_name',
        'currency_id' => 'currency_id',
        'base_rate' => 'base_rate',
        'service_start_date' => 'service_start_date',
        'service_end_date' => 'service_end_date',
        'service_duration_value' => 
        array (
          0 => 'service_duration_value',
          1 => 'catalog_service_duration_value',
        ),
        'service_duration_unit' => 
        array (
          0 => 'service_duration_unit',
          1 => 'catalog_service_duration_unit',
        ),
        'renewable' => 'renewable',
        'service' => 'service',
      ),
    ),
    'lock_duration' => 
    array (
      'name' => 'lock_duration',
      'vname' => 'LBL_LOCK_DURATION',
      'type' => 'bool',
      'default' => 0,
      'studio' => false,
      'enforced' => true,
      'calculated' => true,
      'massupdate' => false,
      'formula' => 'related($product_templates_link, "lock_duration")',
      'comment' => 'Turn on or off a user ability to directly edit the duration field',
    ),
    'account_id' => 
    array (
      'name' => 'account_id',
      'type' => 'id',
      'vname' => 'LBL_ACCOUNT_ID',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'readonly' => true,
      'comment' => 'Account this product is associated with',
    ),
    'contact_id' => 
    array (
      'name' => 'contact_id',
      'type' => 'id',
      'vname' => 'LBL_CONTACT_ID',
      'group' => 'contact_name',
      'required' => false,
      'reportable' => false,
      'audited' => true,
      'comment' => 'Contact this product is associated with',
    ),
    'subtotal' => 
    array (
      'name' => 'subtotal',
      'vname' => 'LBL_SUBTOTAL',
      'type' => 'currency',
      'len' => '26,6',
      'default' => '0',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'discount_price',
        3 => 'quantity',
        4 => 'service_duration_value',
        5 => 'service_duration_unit',
        6 => 'catalog_service_duration_value',
        7 => 'catalog_service_duration_unit',
      ),
      'formula' => '
                ifElse(
                    and(isNumeric($discount_price), isNumeric($quantity)),
                    ifElse(
                        and(
                            isNumeric($service_duration_value),
                            isNumeric($catalog_service_duration_value),
                            not(equal($service_duration_unit, "")),
                            not(equal($catalog_service_duration_unit, ""))
                        ),
                        prorateValue(
                            multiply($discount_price, $quantity),
                            number($service_duration_value),
                            $service_duration_unit,
                            number($catalog_service_duration_value),
                            $catalog_service_duration_unit
                        ),
                        multiply($discount_price, $quantity)
                    ),
                    0
                )
            ',
      'calculated' => true,
      'enforced' => true,
      'comment' => 'The total of the line item before any discounts are applied, taking proration into consideration',
      'additionalCalculationTriggerFields' => 
      array (
        0 => 'discount_amount',
        1 => 'discount_select',
      ),
    ),
    'total_amount' => 
    array (
      'name' => 'total_amount',
      'default' => '0.00',
      'formula' => '
                ifElse(and(isNumeric(toString($quantity)), isNumeric(toString($discount_price))),
                    currencySubtract(
                        ifElse(isNumeric($subtotal), $subtotal, multiply($discount_price, $quantity)),
                        ifElse(equal($discount_select, "1"),
                            currencyMultiply(ifElse(isNumeric($subtotal), $subtotal, multiply($discount_price, $quantity)), currencyDivide($discount_amount, 100)),
                            ifElse(greaterThan($quantity, 0), ifElse(isNumeric(toString($discount_amount)),
                                ifElse(greaterThan(0, $discount_price), negate($discount_amount), $discount_amount), 0),
                            ifElse(isNumeric(toString($discount_amount)), negate($discount_amount), 0))
                        )
                    ),
                    ""
                )
            ',
      'calculated' => true,
      'enforced' => true,
      'vname' => 'LBL_CALCULATED_LINE_ITEM_AMOUNT',
      'reportable' => false,
      'type' => 'currency',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'quantity',
        3 => 'discount_price',
        4 => 'discount_select',
        5 => 'discount_amount',
        6 => 'subtotal',
      ),
    ),
    'contact_name' => 
    array (
      'name' => 'contact_name',
      'rname' => 'name',
      'source' => 'non-db',
      'len' => '510',
      'group' => 'contact_name',
      'vname' => 'LBL_CONTACT_NAME',
      'reportable' => false,
      'id_name' => 'contact_id',
      'join_name' => 'contacts',
      'type' => 'relate',
      'module' => 'Contacts',
      'link' => 'contact_link',
      'table' => 'contacts',
    ),
    'type_id' => 
    array (
      'name' => 'type_id',
      'vname' => 'LBL_TYPE',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'comment' => 'Product type (ex: hardware, software)',
    ),
    'quote_id' => 
    array (
      'name' => 'quote_id',
      'type' => 'id',
      'vname' => 'LBL_QUOTE_ID',
      'required' => false,
      'reportable' => false,
      'comment' => 'If product created via Quote, this is quote ID',
    ),
    'manufacturer_id' => 
    array (
      'name' => 'manufacturer_id',
      'vname' => 'LBL_MANUFACTURER',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
      'massupdate' => false,
      'comment' => 'Manufacturer of product',
    ),
    'manufacturer_name' => 
    array (
      'name' => 'manufacturer_name',
      'rname' => 'name',
      'id_name' => 'manufacturer_id',
      'type' => 'relate',
      'vname' => 'LBL_MANUFACTURER_NAME',
      'join_name' => 'manufacturers',
      'link' => 'manufacturers',
      'table' => 'manufacturers',
      'isnull' => 'true',
      'source' => 'non-db',
      'module' => 'Manufacturers',
      'dbType' => 'varchar',
      'len' => '255',
      'massupdate' => false,
      'related_fields' => 
      array (
        0 => 'manufacturer_id',
      ),
    ),
    'category_id' => 
    array (
      'name' => 'category_id',
      'vname' => 'LBL_CATEGORY',
      'type' => 'id',
      'group' => 'category_name',
      'reportable' => true,
      'comment' => 'Product category',
    ),
    'category_name' => 
    array (
      'name' => 'category_name',
      'rname' => 'name',
      'id_name' => 'category_id',
      'vname' => 'LBL_CATEGORY_NAME',
      'join_name' => 'categories',
      'type' => 'relate',
      'link' => 'product_categories_link',
      'table' => 'product_categories',
      'isnull' => 'true',
      'module' => 'ProductCategories',
      'dbType' => 'varchar',
      'len' => '255',
      'source' => 'non-db',
      'studio' => 
      array (
        'editview' => false,
        'detailview' => false,
        'quickcreate' => false,
      ),
    ),
    'mft_part_num' => 
    array (
      'name' => 'mft_part_num',
      'vname' => 'LBL_MFT_PART_NUM',
      'type' => 'varchar',
      'len' => '50',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => true,
        'boost' => 1.15,
      ),
      'comment' => 'Manufacturer part number',
    ),
    'cost_price' => 
    array (
      'name' => 'cost_price',
      'vname' => 'LBL_COST_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => true,
      'comment' => 'Product cost ("Cost" in Quote)',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
    ),
    'discount_amount_signed' => 
    array (
      'name' => 'discount_amount_signed',
      'vname' => 'LBL_DISCOUNT_AMOUNT_SIGNED',
      'type' => 'currency',
      'len' => '26,6',
      'default' => '0',
      'audited' => true,
      'comment' => 'Discounted Amount Signed',
      'formula' => 'subtract(ifElse(isNumeric($subtotal), $subtotal, multiply($discount_price, $quantity)), $total_amount)',
      'calculated' => true,
      'studio' => false,
      'enforced' => true,
    ),
    'discount_price' => 
    array (
      'name' => 'discount_price',
      'vname' => 'LBL_DISCOUNT_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'default' => '0',
      'audited' => true,
      'comment' => 'Discounted price ("Unit Price" in Quote)',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
    ),
    'discount_amount' => 
    array (
      'name' => 'discount_amount',
      'vname' => 'LBL_DISCOUNT_AMOUNT',
      'dbType' => 'currency',
      'type' => 'discount-amount',
      'len' => '26,6',
      'default' => '0',
      'precision' => '6',
      'comment' => 'Discounted amount',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'discount_select',
      ),
    ),
    'discount_rate_percent' => 
    array (
      'name' => 'discount_rate_percent',
      'formula' => 'ifElse(isNumeric($discount_price), ifElse(equal($discount_price, 0), 0, multiply(divide($discount_amount, $discount_price), 100)), 0)',
      'calculated' => true,
      'enforced' => true,
      'vname' => 'LBL_DISCOUNT_RATE',
      'reportable' => false,
      'type' => 'decimal',
      'precision' => 2,
      'len' => '26,2',
    ),
    'discount_amount_usdollar' => 
    array (
      'name' => 'discount_amount_usdollar',
      'vname' => 'LBL_DISCOUNT_RATE_USDOLLAR',
      'type' => 'currency',
      'currency_id' => '-99',
      'len' => '26,6',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'formula' => 'ifElse(isNumeric($discount_amount), currencyDivide($discount_amount, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'discount_select' => 
    array (
      'name' => 'discount_select',
      'vname' => 'LBL_DISCOUNT_AS_PERCENT',
      'type' => 'bool',
      'default' => true,
      'reportable' => false,
    ),
    'deal_calc' => 
    array (
      'name' => 'deal_calc',
      'vname' => 'LBL_DISCOUNT_TOTAL',
      'type' => 'currency',
      'len' => '26,6',
      'group' => 'deal_calc',
      'comment' => 'deal_calc',
      'formula' => 'ifElse(equal($discount_select, "1"),
                            currencyMultiply(ifElse(isNumeric($subtotal), $subtotal, multiply($discount_price, $quantity)), currencyDivide($discount_amount, 100)),
                            ifElse(isNumeric($discount_amount_signed), $discount_amount_signed, 0)
                        )',
      'calculated' => true,
      'enforced' => true,
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
        2 => 'discount_price',
        3 => 'quantity',
        4 => 'discount_amount',
        5 => 'subtotal',
      ),
    ),
    'deal_calc_usdollar' => 
    array (
      'name' => 'deal_calc_usdollar',
      'vname' => 'LBL_DISCOUNT_TOTAL_USDOLLAR',
      'type' => 'currency',
      'currency_id' => '-99',
      'len' => '26,6',
      'group' => 'deal_calc',
      'comment' => 'deal_calc_usdollar',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($deal_calc), currencyDivide($deal_calc, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'cost_usdollar' => 
    array (
      'name' => 'cost_usdollar',
      'vname' => 'LBL_COST_USDOLLAR',
      'dbType' => 'decimal',
      'group' => 'cost_price',
      'type' => 'currency',
      'currency_id' => '-99',
      'len' => '26,6',
      'comment' => 'Cost expressed in USD',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($cost_price), currencyDivide($cost_price, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'discount_usdollar' => 
    array (
      'name' => 'discount_usdollar',
      'vname' => 'LBL_DISCOUNT_USDOLLAR',
      'dbType' => 'decimal',
      'group' => 'discount_price',
      'type' => 'currency',
      'currency_id' => '-99',
      'len' => '26,6',
      'comment' => 'Discount price expressed in USD',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'formula' => 'divide($discount_price, $base_rate)',
      'calculated' => true,
      'enforced' => true,
    ),
    'list_usdollar' => 
    array (
      'name' => 'list_usdollar',
      'vname' => 'LBL_LIST_USDOLLAR',
      'dbType' => 'decimal',
      'type' => 'currency',
      'currency_id' => '-99',
      'group' => 'list_price',
      'len' => '26,6',
      'comment' => 'List price expressed in USD',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($list_price), currencyDivide($list_price, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'options' => 'product_status_dom',
      'default' => '',
      'len' => 100,
      'audited' => true,
      'comment' => 'Product status (ex: Quoted, Ordered, Shipped)',
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
      ),
    ),
    'book_value_usdollar' => 
    array (
      'name' => 'book_value_usdollar',
      'vname' => 'LBL_BOOK_VALUE_USDOLLAR',
      'group' => 'book_value',
      'type' => 'currency',
      'len' => '26,6',
      'comment' => 'Book value expressed in USD',
      'studio' => 
      array (
        'editview' => false,
        'mobile' => false,
      ),
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
      'formula' => 'ifElse(isNumeric($book_value), currencyDivide($book_value, $base_rate), "")',
      'calculated' => true,
      'enforced' => true,
    ),
    'quotes' => 
    array (
      'name' => 'quotes',
      'type' => 'link',
      'relationship' => 'quote_products',
      'vname' => 'LBL_QUOTE',
      'source' => 'non-db',
    ),
    'revenuelineitems' => 
    array (
      'name' => 'revenuelineitems',
      'type' => 'link',
      'relationship' => 'products_revenuelineitems',
      'vname' => 'LBL_REVENUELINEITEM',
      'source' => 'non-db',
      'workflow' => false,
    ),
    'date_closed' => 
    array (
      'name' => 'date_closed',
      'vname' => 'LBL_DATE_CLOSED',
      'type' => 'date',
      'audited' => true,
      'comment' => 'Expected or actual date the product (for opportunity) will close',
      'enable_range_search' => true,
      'options' => 'date_range_search_dom',
    ),
    'date_closed_timestamp' => 
    array (
      'name' => 'date_closed_timestamp',
      'vname' => 'LBL_DATE_CLOSED_TIMESTAMP',
      'type' => 'ulong',
      'reportable' => false,
      'studio' => false,
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
      'comment' => 'The next step in the sales process',
      'merge_filter' => 'enabled',
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
      'isnull' => 'true',
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
      'link' => 'campaign_products',
      'isnull' => 'true',
      'table' => 'campaigns',
      'module' => 'Campaigns',
      'source' => 'non-db',
      'studio' => 
      array (
        'mobile' => false,
      ),
    ),
    'campaign_products' => 
    array (
      'name' => 'campaign_products',
      'type' => 'link',
      'vname' => 'LBL_CAMPAIGN_PRODUCT',
      'relationship' => 'campaign_products',
      'source' => 'non-db',
    ),
    'related_products' => 
    array (
      'name' => 'related_products',
      'type' => 'link',
      'relationship' => 'product_product',
      'source' => 'non-db',
      'vname' => 'LBL_RELATED_PRODUCTS',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'type' => 'link',
      'relationship' => 'product_notes',
      'source' => 'non-db',
      'vname' => 'LBL_NOTES',
    ),
    'messages' => 
    array (
      'name' => 'messages',
      'type' => 'link',
      'relationship' => 'product_messages',
      'source' => 'non-db',
      'vname' => 'LBL_MESSAGES',
    ),
    'documents' => 
    array (
      'name' => 'documents',
      'type' => 'link',
      'relationship' => 'documents_products',
      'source' => 'non-db',
      'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    ),
    'contracts' => 
    array (
      'name' => 'contracts',
      'type' => 'link',
      'vname' => 'LBL_CONTRACTS',
      'relationship' => 'contracts_products',
      'link_type' => 'one',
      'source' => 'non-db',
    ),
    'quote_name' => 
    array (
      'name' => 'quote_name',
      'rname' => 'name',
      'id_name' => 'quote_id',
      'join_name' => 'quotes',
      'type' => 'relate',
      'link' => 'quotes',
      'table' => 'quotes',
      'isnull' => 'true',
      'module' => 'Quotes',
      'dbType' => 'varchar',
      'len' => '255',
      'vname' => 'LBL_QUOTE_NAME',
      'source' => 'non-db',
      'comment' => 'Quote Name',
    ),
    'opportunity_id' => 
    array (
      'name' => 'opportunity_id',
      'type' => 'id',
      'vname' => 'LBL_OPPORTUNITY_ID',
      'required' => false,
      'isnull' => true,
      'reportable' => false,
      'comment' => 'The opportunity id for the line item entry',
    ),
    'opportunity_name' => 
    array (
      'name' => 'opportunity_name',
      'rname' => 'name',
      'id_name' => 'opportunity_id',
      'vname' => 'LBL_OPPORTUNITY_NAME',
      'join_name' => 'opportunities',
      'type' => 'relate',
      'link' => 'opportunities',
      'table' => 'opportunities',
      'isnull' => 'true',
      'module' => 'Opportunities',
      'source' => 'non-db',
      'comment' => 'The opportunity name associated with the opportunity_id',
      'auto_populate' => true,
      'populate_list' => 
      array (
        'account_id' => 'account_id',
        'account_name' => 'account_name',
      ),
    ),
    'opportunities' => 
    array (
      'name' => 'opportunities',
      'type' => 'link',
      'relationship' => 'opportunities_products',
      'source' => 'non-db',
      'link_type' => 'one',
      'module' => 'Opportunities',
      'bean_name' => 'Opportunity',
      'vname' => 'LBL_OPPORTUNITIES',
    ),
    'type_name' => 
    array (
      'name' => 'type_name',
      'rname' => 'name',
      'id_name' => 'type_id',
      'vname' => 'LBL_PRODUCT_TYPE',
      'join_name' => 'types',
      'type' => 'relate',
      'link' => 'product_types_link',
      'table' => 'product_types',
      'isnull' => 'true',
      'module' => 'ProductTypes',
      'importable' => 'false',
      'dbType' => 'varchar',
      'len' => '255',
      'source' => 'non-db',
    ),
    'account_link' => 
    array (
      'name' => 'account_link',
      'type' => 'link',
      'relationship' => 'products_accounts',
      'link_class' => 'AccountLink',
      'vname' => 'LBL_ACCOUNT',
      'link_type' => 'one',
      'module' => 'Accounts',
      'bean_name' => 'Account',
      'source' => 'non-db',
    ),
    'product_categories_link' => 
    array (
      'name' => 'product_categories_link',
      'type' => 'link',
      'relationship' => 'product_categories',
      'vname' => 'LBL_PRODUCT_CATEGORIES',
      'link_type' => 'one',
      'module' => 'ProductCategories',
      'bean_name' => 'ProductCategory',
      'source' => 'non-db',
    ),
    'product_templates_link' => 
    array (
      'name' => 'product_templates_link',
      'type' => 'link',
      'relationship' => 'product_templates',
      'vname' => 'LBL_PRODUCT_TEMPLATES',
      'link_type' => 'one',
      'module' => 'ProductTemplates',
      'bean_name' => 'ProductTemplate',
      'source' => 'non-db',
    ),
    'product_types_link' => 
    array (
      'name' => 'product_types_link',
      'type' => 'link',
      'relationship' => 'product_types',
      'vname' => 'LBL_PRODUCT_TYPES',
      'link_type' => 'one',
      'module' => 'ProductTypes',
      'bean_name' => 'ProductType',
      'source' => 'non-db',
    ),
    'contact_link' => 
    array (
      'name' => 'contact_link',
      'type' => 'link',
      'relationship' => 'contact_products',
      'vname' => 'LBL_CONTACT',
      'link_type' => 'one',
      'module' => 'Contacts',
      'bean_name' => 'Contact',
      'source' => 'non-db',
      'duplicate_merge' => 'disabled',
    ),
    'pli_addons_link' => 
    array (
      'name' => 'pli_addons_link',
      'type' => 'link',
      'relationship' => 'product_pli_addons',
      'vname' => 'LBL_ADD_ON_TO',
      'link_type' => 'one',
      'module' => 'PurchasedLineItems',
      'bean_name' => 'PurchasedLineItem',
      'source' => 'non-db',
    ),
    'parent_rli_link' => 
    array (
      'name' => 'parent_rli_link',
      'type' => 'link',
      'relationship' => 'products_parent_rli',
      'vname' => 'LBL_REVENUELINEITEM',
      'link_type' => 'one',
      'module' => 'RevenueLineItems',
      'bean_name' => 'RevenueLineItem',
      'source' => 'non-db',
      'workflow' => true,
    ),
    'account_name' => 
    array (
      'name' => 'account_name',
      'rname' => 'name',
      'id_name' => 'account_id',
      'vname' => 'LBL_ACCOUNT_NAME',
      'join_name' => 'accounts',
      'type' => 'relate',
      'link' => 'account_link',
      'table' => 'accounts',
      'module' => 'Accounts',
      'source' => 'non-db',
      'readonly' => true,
    ),
    'emails' => 
    array (
      'name' => 'emails',
      'type' => 'link',
      'relationship' => 'emails_products_rel',
      'module' => 'Emails',
      'bean_name' => 'Email',
      'source' => 'non-db',
      'vname' => 'LBL_EMAILS',
      'studio' => 
      array (
        'formula' => false,
      ),
    ),
    'projects' => 
    array (
      'name' => 'projects',
      'type' => 'link',
      'relationship' => 'projects_products',
      'source' => 'non-db',
      'vname' => 'LBL_PROJECTS',
    ),
    'product_bundles' => 
    array (
      'name' => 'product_bundles',
      'type' => 'link',
      'relationship' => 'product_bundle_product',
      'module' => 'ProductBundles',
      'bean_name' => 'ProductBundle',
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
    'position' => 
    array (
      'massupdate' => false,
      'name' => 'position',
      'type' => 'integer',
      'studio' => false,
      'source' => 'non-db',
      'vname' => 'LBL_PRODUCT_POSITION',
      'importable' => false,
      'link' => 'product_bundles',
      'rname_link' => 'product_index',
    ),
    'calls' => 
    array (
      'name' => 'calls',
      'type' => 'link',
      'relationship' => 'product_calls',
      'module' => 'Calls',
      'bean_name' => 'Call',
      'source' => 'non-db',
      'vname' => 'LBL_CALLS',
    ),
    'meetings' => 
    array (
      'name' => 'meetings',
      'type' => 'link',
      'relationship' => 'product_meetings',
      'module' => 'Meetings',
      'bean_name' => 'Meeting',
      'source' => 'non-db',
      'vname' => 'LBL_MEETINGS',
    ),
    'manufacturers' => 
    array (
      'name' => 'manufacturers',
      'type' => 'link',
      'relationship' => 'product_manufacturers',
      'vname' => 'LBL_MANUFACTURERS',
      'link_type' => 'one',
      'module' => 'Manufacturers',
      'bean_name' => 'Manufacturer',
      'source' => 'non-db',
    ),
    'parent_rli_id' => 
    array (
      'name' => 'parent_rli_id',
      'comment' => 'RLI this product was generated from',
      'vname' => 'LBL_REVENUELINEITEM_ID',
      'rname' => 'id',
      'type' => 'id',
      'dbType' => 'id',
      'is_null' => true,
      'reportable' => false,
      'massupdate' => false,
      'table' => 'revenue_line_items',
      'module' => 'RevenueLineItems',
      'duplicate_merge' => 'disabled',
    ),
    'parent_rli_name' => 
    array (
      'name' => 'parent_rli_name',
      'rname' => 'name',
      'id_name' => 'parent_rli_id',
      'vname' => 'LBL_REVENUELINEITEM',
      'type' => 'relate',
      'save' => true,
      'isnull' => true,
      'link' => 'parent_rli_link',
      'table' => 'revenue_line_items',
      'module' => 'RevenueLineItems',
      'source' => 'non-db',
      'studio' => false,
    ),
    'add_on_to_id' => 
    array (
      'name' => 'add_on_to_id',
      'comment' => 'Purchased line item that this is an add-on to',
      'vname' => 'LBL_ADD_ON_TO_ID',
      'rname' => 'id',
      'type' => 'id',
      'dbType' => 'id',
      'table' => 'purchased_line_items',
      'isnull' => 'true',
      'module' => 'PurchasedLineItems',
      'reportable' => false,
      'massupdate' => false,
      'duplicate_merge' => 'disabled',
    ),
    'add_on_to_name' => 
    array (
      'name' => 'add_on_to_name',
      'rname' => 'name',
      'id_name' => 'add_on_to_id',
      'vname' => 'LBL_ADD_ON_TO',
      'type' => 'relate',
      'save' => true,
      'link' => 'pli_addons_link',
      'isnull' => 'true',
      'table' => 'purchased_line_items',
      'module' => 'PurchasedLineItems',
      'source' => 'non-db',
      'massupdate' => false,
      'copyFromPurchasedLineItem' => 
      array (
        'product_template_id' => 'product_template_id',
        'product_template_name' => 'product_template_name',
        'service_end_date' => 'service_end_date',
      ),
      'copyFromProductTemplate' => 
      array (
        'name' => 'name',
        'category_id' => 'category_id',
        'category_name' => 'category_name',
        'mft_part_num' => 'mft_part_num',
        'list_price' => 'list_price',
        'cost_price' => 'cost_price',
        'discount_price' => 'discount_price',
        'list_usdollar' => 'list_usdollar',
        'cost_usdollar' => 'cost_usdollar',
        'discount_usdollar' => 'discount_usdollar',
        'currency_id' => 'currency_id',
        'base_rate' => 'base_rate',
        'tax_class' => 'tax_class',
        'weight' => 'weight',
        'manufacturer_id' => 'manufacturer_id',
        'manufacturer_name' => 'manufacturer_name',
        'type_id' => 'type_id',
        'type_name' => 'type_name',
        'renewable' => 'renewable',
        'service' => 'service',
        'service_duration_value' => 'catalog_service_duration_value',
        'service_duration_unit' => 'catalog_service_duration_unit',
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
      'relationship' => 'products_following',
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
      'relationship' => 'products_favorite',
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
      'relationship' => 'products_tags',
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
      'relationship' => 'products_commentlog',
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
      'relationship' => 'products_locked_fields',
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
      'relationship' => 'products_assigned_user',
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
      'relationship' => 'products_team',
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
      'relationship' => 'products_team_count_relationship',
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
      'relationship' => 'products_teams',
      'bean_filter_field' => 'team_set_id',
      'rhs_key_override' => true,
      'source' => 'non-db',
      'vname' => 'LBL_TEAMS',
      'link_class' => 'TeamSetLink',
      'studio' => 'false',
      'reportable' => false,
      'side' => 'left',
    ),
    'asset_number' => 
    array (
      'name' => 'asset_number',
      'vname' => 'LBL_ASSET_NUMBER',
      'type' => 'varchar',
      'len' => 50,
      'comment' => 'Asset tag number of sales item in use',
    ),
    'book_value' => 
    array (
      'name' => 'book_value',
      'vname' => 'LBL_BOOK_VALUE',
      'type' => 'currency',
      'len' => '26,6',
      'comment' => 'Book value of sales item in use',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
    ),
    'book_value_date' => 
    array (
      'name' => 'book_value_date',
      'vname' => 'LBL_BOOK_VALUE_DATE',
      'type' => 'date',
      'comment' => 'Date of book value for sales item in use',
    ),
    'date_purchased' => 
    array (
      'name' => 'date_purchased',
      'vname' => 'LBL_DATE_PURCHASED',
      'type' => 'date',
      'comment' => 'Date sales item purchased',
    ),
    'date_support_expires' => 
    array (
      'name' => 'date_support_expires',
      'vname' => 'LBL_DATE_SUPPORT_EXPIRES',
      'type' => 'date',
      'comment' => 'Support expiration date',
    ),
    'date_support_starts' => 
    array (
      'name' => 'date_support_starts',
      'vname' => 'LBL_DATE_SUPPORT_STARTS',
      'type' => 'date',
      'comment' => 'Support start date',
    ),
    'list_price' => 
    array (
      'name' => 'list_price',
      'vname' => 'LBL_LIST_PRICE',
      'type' => 'currency',
      'len' => '26,6',
      'audited' => true,
      'comment' => 'List price of sales item',
      'related_fields' => 
      array (
        0 => 'currency_id',
        1 => 'base_rate',
      ),
    ),
    'pricing_factor' => 
    array (
      'name' => 'pricing_factor',
      'vname' => 'LBL_PRICING_FACTOR',
      'type' => 'int',
      'group' => 'pricing_formula',
      'len' => 4,
      'comment' => 'Variable pricing factor depending on pricing_formula',
    ),
    'pricing_formula' => 
    array (
      'name' => 'pricing_formula',
      'vname' => 'LBL_PRICING_FORMULA',
      'type' => 'varchar',
      'len' => 100,
      'comment' => 'Pricing formula (ex: Fixed, Markup over Cost)',
    ),
    'quantity' => 
    array (
      'name' => 'quantity',
      'vname' => 'LBL_QUANTITY',
      'type' => 'decimal',
      'len' => 12,
      'precision' => 2,
      'comment' => 'Quantity in use',
      'default' => 1.0,
    ),
    'serial_number' => 
    array (
      'name' => 'serial_number',
      'vname' => 'LBL_SERIAL_NUMBER',
      'type' => 'varchar',
      'len' => 50,
      'comment' => 'Serial number of sales item in use',
    ),
    'renewable' => 
    array (
      'name' => 'renewable',
      'vname' => 'LBL_RENEWABLE',
      'type' => 'bool',
      'comment' => 'Indicates whether the sales item is renewable (e.g. a service)',
      'related_fields' => 
      array (
        0 => 'service_duration',
        1 => 'service_start_date',
        2 => 'service_end_date',
        3 => 'service',
      ),
    ),
    'service' => 
    array (
      'name' => 'service',
      'vname' => 'LBL_SERVICE',
      'type' => 'bool',
      'default' => 0,
      'comment' => 'Indicates whether the sales item is a service or a product',
      'related_fields' => 
      array (
        0 => 'renewable',
        1 => 'service_duration',
        2 => 'service_start_date',
        3 => 'service_end_date',
      ),
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
      'comment' => 'Service Duration unit like Year(s), Month(s) or Day(s)',
    ),
    'catalog_service_duration_value' => 
    array (
      'name' => 'catalog_service_duration_value',
      'vname' => 'LBL_CATALOG_SERVICE_DURATION_VALUE',
      'type' => 'int',
      'min' => '1',
      'len' => '5',
      'required' => false,
      'studio' => false,
      'massupdate' => false,
      'readonly' => true,
      'related_fields' => 
      array (
        0 => 'service_duration_multiplier',
      ),
      'comment' => 'Stores a Product Catalog item\'s Service Duration Value, used for duration comparisons',
    ),
    'catalog_service_duration_unit' => 
    array (
      'name' => 'catalog_service_duration_unit',
      'vname' => 'LBL_CATALOG_SERVICE_DURATION_UNIT',
      'type' => 'enum',
      'options' => 'service_duration_unit_dom',
      'len' => 50,
      'audited' => false,
      'studio' => false,
      'massupdate' => false,
      'readonly' => true,
      'related_fields' => 
      array (
        0 => 'service_duration_multiplier',
      ),
      'comment' => 'Stores a Product Catalog item\'s Service Duration Unit, used for duration comparisons',
    ),
    'service_duration_multiplier' => 
    array (
      'name' => 'service_duration_multiplier',
      'vname' => 'LBL_SERVICE_DURATION_MULTIPLIER',
      'type' => 'decimal',
      'studio' => false,
      'calculated' => true,
      'enforced' => true,
      'formula' => '
            ifElse(
                and(
                    isNumeric($service_duration_value),
                    isNumeric($catalog_service_duration_value),
                    or(equal($service_duration_unit, "year"), equal($service_duration_unit, "month"), equal($service_duration_unit, "day")),
                    or(equal($catalog_service_duration_unit, "year"), equal($catalog_service_duration_unit, "month"), equal($catalog_service_duration_unit, "day"))
                ),
                divide(
                    ifElse(
                        equal($service_duration_unit, "year"),
                        multiply($service_duration_value, 365),
                        ifElse(
                            equal($service_duration_unit, "month"),
                            multiply($service_duration_value, divide(365,12)),
                            ifElse(
                                equal($service_duration_unit, "day"),
                                $service_duration_value,
                                ""
                            )
                        )
                    ),
                    ifElse(
                        equal($catalog_service_duration_unit, "year"),
                        multiply($catalog_service_duration_value, 365),
                        ifElse(
                            equal($catalog_service_duration_unit, "month"),
                            multiply($catalog_service_duration_value, divide(365,12)),
                            ifElse(
                                equal($catalog_service_duration_unit, "day"),
                                $catalog_service_duration_value,
                                ""
                            )
                        )
                    )
                ),
                1
            )',
      'related_fields' => 
      array (
        0 => 'catalog_service_duration_value',
        1 => 'catalog_service_duration_unit',
      ),
      'comment' => 'Stores a multiplier based on the ratio of this sales item\'s duration to another duration (such as a Product Template\'s)',
    ),
    'service_end_date' => 
    array (
      'name' => 'service_end_date',
      'vname' => 'LBL_SERVICE_END_DATE',
      'type' => 'service-enddate',
      'dbType' => 'date',
      'comment' => 'End date of the service',
      'related_fields' => 
      array (
        0 => 'service_duration',
        1 => 'service_start_date',
        2 => 'renewable',
        3 => 'service',
      ),
    ),
    'service_start_date' => 
    array (
      'name' => 'service_start_date',
      'vname' => 'LBL_SERVICE_START_DATE',
      'type' => 'date',
      'comment' => 'Start date of the service',
      'related_fields' => 
      array (
        0 => 'service_duration',
        1 => 'service_end_date',
        2 => 'renewable',
        3 => 'service',
      ),
      'validation' => 
      array (
        'type' => 'isbefore',
        'compareto' => 'service_end_date',
        'datatype' => 'date',
      ),
    ),
    'support_contact' => 
    array (
      'name' => 'support_contact',
      'vname' => 'LBL_SUPPORT_CONTACT',
      'type' => 'varchar',
      'len' => 50,
      'comment' => 'Contact for support purposes',
    ),
    'support_description' => 
    array (
      'name' => 'support_description',
      'vname' => 'LBL_SUPPORT_DESCRIPTION',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Description of sales item for support purposes',
    ),
    'support_name' => 
    array (
      'name' => 'support_name',
      'vname' => 'LBL_SUPPORT_NAME',
      'type' => 'varchar',
      'len' => 50,
      'comment' => 'Name of sales item for support purposes',
    ),
    'support_term' => 
    array (
      'name' => 'support_term',
      'vname' => 'LBL_SUPPORT_TERM',
      'type' => 'varchar',
      'len' => 100,
      'comment' => 'Term (length) of support contract',
    ),
    'tax_class' => 
    array (
      'name' => 'tax_class',
      'vname' => 'LBL_TAX_CLASS',
      'type' => 'enum',
      'options' => 'tax_class_dom',
      'len' => 100,
      'comment' => 'Tax classification (ex: Taxable, Non-taxable)',
      'default' => 'Taxable',
    ),
    'vendor_part_num' => 
    array (
      'name' => 'vendor_part_num',
      'vname' => 'LBL_VENDOR_PART_NUM',
      'type' => 'varchar',
      'len' => 50,
      'comment' => 'Vendor part number',
    ),
    'website' => 
    array (
      'name' => 'website',
      'vname' => 'LBL_URL',
      'type' => 'varchar',
      'len' => 255,
      'comment' => 'Sales item URL',
    ),
    'weight' => 
    array (
      'name' => 'weight',
      'vname' => 'LBL_WEIGHT',
      'type' => 'decimal',
      'len' => '12,2',
      'precision' => 2,
      'comment' => 'Weight of the sales item',
    ),
    'renewal' => 
    array (
      'name' => 'renewal',
      'vname' => 'LBL_RENEWAL',
      'type' => 'bool',
      'default' => 0,
      'readonly' => true,
      'comment' => 'Indicates whether this line item is a renewal',
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
      'relationship' => 'products_currencies',
      'source' => 'non-db',
      'vname' => 'LBL_CURRENCIES',
    ),
  ),
  'indices' => 
  array (
    'id' => 
    array (
      'name' => 'idx_products_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    'date_modified' => 
    array (
      'name' => 'idx_products_del_d_m',
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
      'name' => 'idx_products_id_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'deleted',
      ),
    ),
    'date_entered' => 
    array (
      'name' => 'idx_products_del_d_e',
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
      'name' => 'idx_products_name_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'name',
        1 => 'deleted',
      ),
    ),
    0 => 
    array (
      'name' => 'idx_prod_user_dc_timestamp',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'id',
        1 => 'assigned_user_id',
        2 => 'date_closed_timestamp',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_product_quantity',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'quantity',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_product_contact',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'contact_id',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_product_account',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'account_id',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_product_opp',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'opportunity_id',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_product_quote',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'quote_id',
      ),
    ),
    6 => 
    array (
      'name' => 'idx_product_rli',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'revenuelineitem_id',
      ),
    ),
    7 => 
    array (
      'name' => 'idx_product_del_created_by_pt_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'created_by',
        2 => 'product_template_id',
      ),
    ),
    8 => 
    array (
      'name' => 'idx_del_p_rli_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'parent_rli_id',
      ),
    ),
    'sync_key' => 
    array (
      'name' => 'idx_products_skey',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'sync_key',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_products_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
    'team_set_products' => 
    array (
      'name' => 'idx_products_tmst_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'team_set_id',
        1 => 'deleted',
      ),
    ),
    'acl_team_set_products' => 
    array (
      'name' => 'idx_products_acl_tmst_id',
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
    'products_modified_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'modified_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_created_by' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'created_by',
      'relationship_type' => 'one-to-many',
    ),
    'product_activities' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
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
      'relationship_role_column_value' => 'Products',
    ),
    'product_notes' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Products',
    ),
    'product_messages' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Messages',
      'rhs_table' => 'messages',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Products',
    ),
    'opportunities_products' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'opportunity_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_accounts' => 
    array (
      'lhs_module' => 'Accounts',
      'lhs_table' => 'accounts',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'account_id',
      'relationship_type' => 'one-to-many',
    ),
    'product_categories' => 
    array (
      'lhs_module' => 'ProductCategories',
      'lhs_table' => 'product_categories',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'category_id',
      'relationship_type' => 'one-to-many',
    ),
    'product_templates' => 
    array (
      'lhs_module' => 'ProductTemplates',
      'lhs_table' => 'product_templates',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'product_template_id',
      'relationship_type' => 'one-to-many',
    ),
    'product_types' => 
    array (
      'lhs_module' => 'ProductTypes',
      'lhs_table' => 'product_types',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'type_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_revenuelineitems' => 
    array (
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'revenuelineitem_id',
      'lhs_module' => 'RevenueLineItems',
      'lhs_table' => 'revenue_line_items',
      'lhs_key' => 'id',
      'relationship_type' => 'one-to-one',
    ),
    'product_calls' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Calls',
      'rhs_table' => 'calls',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Products',
    ),
    'product_meetings' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Meetings',
      'rhs_table' => 'meetings',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Products',
    ),
    'product_manufacturers' => 
    array (
      'lhs_module' => 'Manufacturers',
      'lhs_table' => 'manufacturers',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'manufacturer_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_parent_rli' => 
    array (
      'lhs_module' => 'RevenueLineItems',
      'lhs_table' => 'revenue_line_items',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'parent_rli_id',
      'relationship_type' => 'one-to-many',
    ),
    'product_pli_addons' => 
    array (
      'lhs_module' => 'PurchasedLineItems',
      'lhs_table' => 'purchased_line_items',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'add_on_to_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'Products',
      'user_field' => 'created_by',
    ),
    'products_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Products',
      'user_field' => 'created_by',
    ),
    'products_tags' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Tags',
      'rhs_table' => 'tags',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tag_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'tag_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Products',
      'dynamic_subpanel' => true,
    ),
    'products_commentlog' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'CommentLog',
      'rhs_table' => 'commentlog',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'commentlog_rel',
      'join_key_lhs' => 'record_id',
      'join_key_rhs' => 'commentlog_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'Products',
    ),
    'products_locked_fields' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'pmse_BpmProcessDefinition',
      'rhs_table' => 'pmse_bpm_process_definition',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'locked_field_bean_rel',
      'join_key_lhs' => 'bean_id',
      'join_key_rhs' => 'pd_id',
      'relationship_role_column' => 'bean_module',
      'relationship_role_column_value' => 'Products',
    ),
    'products_audit' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'id',
      'rhs_module' => 'Audit',
      'rhs_table' => 'products_audit',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_team_count_relationship' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'team_sets',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'team_set_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_teams' => 
    array (
      'lhs_module' => 'Products',
      'lhs_table' => 'products',
      'lhs_key' => 'team_set_id',
      'rhs_module' => 'Teams',
      'rhs_table' => 'teams',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'team_sets_teams',
      'join_key_lhs' => 'team_set_id',
      'join_key_rhs' => 'team_id',
    ),
    'products_team' => 
    array (
      'lhs_module' => 'Teams',
      'lhs_table' => 'teams',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'team_id',
      'relationship_type' => 'one-to-many',
    ),
    'products_currencies' => 
    array (
      'lhs_module' => 'Currencies',
      'lhs_table' => 'currencies',
      'lhs_key' => 'id',
      'rhs_module' => 'Products',
      'rhs_table' => 'products',
      'rhs_key' => 'currency_id',
      'relationship_type' => 'one-to-many',
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
              'opportunity_id' => 
              array (
                '$equals' => '$opportunity_id',
              ),
            ),
            1 => 
            array (
              'name' => 
              array (
                '$starts' => '$name',
              ),
            ),
          ),
        ),
      ),
      'ranking_fields' => 
      array (
        0 => 
        array (
          'in_field_name' => 'opportunity_id',
          'dupe_field_name' => 'opportunity_id',
        ),
        1 => 
        array (
          'in_field_name' => 'name',
          'dupe_field_name' => 'name',
        ),
      ),
    ),
  ),
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
    'sales_item' => 'sales_item',
    'currency' => 'currency',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
    0 => 'product_bundles',
  ),
);
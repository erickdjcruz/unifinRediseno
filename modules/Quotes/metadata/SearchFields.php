<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
$searchFields['Quotes'] =
    [
        'name' => ['query_type' => 'default'],
        'account_name' => ['query_type' => 'default'],
        'date_quote_expected_closed' => ['query_type' => 'default', 'operator' => '='],
        'amount' => ['query_type' => 'default', 'db_field' => ['total']],
        'quote_stage' => ['query_type' => 'default', 'options' => 'quote_stage_dom', 'template_var' => 'QUOTE_STAGE_OPTIONS', 'options_add_blank' => true],
        'current_user_only' => ['query_type' => 'default', 'db_field' => ['assigned_user_id'], 'my_items' => true, 'vname' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
        'assigned_user_id' => ['query_type' => 'default'],
        'quote_type' => ['query_type' => 'default', 'options' => 'quote_type_dom', 'template_var' => 'TYPE_OPTIONS', 'options_add_blank' => true],
        'quote_num' => ['query_type' => 'default', 'operator' => 'in'],
        'favorites_only' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
			                    WHERE sugarfavorites.deleted=0 
			                        and sugarfavorites.module = \'Quotes\'
			                        and sugarfavorites.assigned_user_id = {0}',
            'db_field' => ['id']],
        'open_only' => [
            'query_type' => 'default',
            'db_field' => ['quote_stage'],
            'operator' => 'not in',
            'closed_values' => ['Closed Lost', 'Closed Accepted', 'Closed Dead'],
            'type' => 'bool',
        ],
        //Range Search Support
        'range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_quote_expected_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_quote_expected_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_quote_expected_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_quote_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_quote_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_quote_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_order_shipped' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_order_shipped' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_order_shipped' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_original_po_date' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_original_po_date' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_original_po_date' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],

        'range_total_usdollar' => ['query_type' => 'default', 'enable_range_search' => true],
        'start_range_total_usdollar' => ['query_type' => 'default', 'enable_range_search' => true],
        'end_range_total_usdollar' => ['query_type' => 'default', 'enable_range_search' => true],
        'range_quote_num' => ['query_type' => 'default', 'enable_range_search' => true],
        'start_range_quote_num' => ['query_type' => 'default', 'enable_range_search' => true],
        'end_range_quote_num' => ['query_type' => 'default', 'enable_range_search' => true],
        //Range Search Support
    ];

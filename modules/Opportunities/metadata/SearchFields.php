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
$searchFields['Opportunities'] =
    [
        'name' => ['query_type' => 'default'],
        'account_name' => ['query_type' => 'default', 'db_field' => ['accounts.name']],
        'amount' => ['query_type' => 'default'],
        'next_step' => ['query_type' => 'default'],
        'probability' => ['query_type' => 'default'],
        'lead_source' => ['query_type' => 'default', 'operator' => '=', 'options' => 'lead_source_dom', 'template_var' => 'LEAD_SOURCE_OPTIONS'],
        'opportunity_type' => ['query_type' => 'default', 'operator' => '=', 'options' => 'opportunity_type_dom', 'template_var' => 'TYPE_OPTIONS'],
        'sales_stage' => ['query_type' => 'default', 'operator' => '=', 'options' => 'sales_stage_dom', 'template_var' => 'SALES_STAGE_OPTIONS', 'options_add_blank' => true],
        'current_user_only' => ['query_type' => 'default', 'db_field' => ['assigned_user_id'], 'my_items' => true, 'vname' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
        'assigned_user_id' => ['query_type' => 'default'],
        'favorites_only' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
			                    WHERE sugarfavorites.deleted=0 
			                        and sugarfavorites.module = \'Opportunities\'
			                        and sugarfavorites.assigned_user_id = \'{0}\'',
            'db_field' => ['id']],
        'open_only' => [
            'query_type' => 'default',
            'db_field' => ['sales_stage'],
            'operator' => 'not in',
            'closed_values' => ['Closed Won', 'Closed Lost'],
            'type' => 'bool',
        ],

        //Range Search Support
        'range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],

        'range_date_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_closed' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],

        'range_amount' => ['query_type' => 'default', 'enable_range_search' => true],
        'start_range_amount' => ['query_type' => 'default', 'enable_range_search' => true],
        'end_range_amount' => ['query_type' => 'default', 'enable_range_search' => true],

        'range_forecasted_likely' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'start_range_forecasted_likely' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'end_range_forecasted_likely' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'range_commit_stage' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'start_range_commit_stage' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'end_range_commit_stage' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'range_lost' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'start_range_lost' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
        'end_range_lost' => [
            'query_type' => 'default',
            'enable_range_search' => true,
        ],
    ];

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

$searchFields['RevenueLineItems'] = [
    'name' => [
        'query_type' => 'default',
        'force_unifiedsearch' => true,
    ],
    'account_name' => ['query_type' => 'default', 'db_field' => ['accounts.name']],
    'opportunity_name' => ['query_type' => 'default', 'db_field' => ['opportunities.name']],
    'best_case' => [
        'query_type' => 'default',
    ],
    'likely_case' => [
        'query_type' => 'default',
    ],
    'worst_case' => [
        'query_type' => 'default',
    ],
    'probability' => [
        'query_type' => 'default',
    ],
    'sales_stage' => [
        'query_type' => 'default',
        'options' => 'sales_stage_dom',
        'template_var' => 'SALES_STAGE_OPTIONS',
        'options_add_blank' => true,
    ],
    'type_id' => [
        'query_type' => 'default',
        'options' => 'product_type_dom',
        'template_var' => 'TYPE_OPTIONS',
    ],
    'category_id' => [
        'query_type' => 'default',
        'options' => 'products_cat_dom',
        'template_var' => 'CATEGORY_OPTIONS',
    ],
    'manufacturer_id' => [
        'query_type' => 'default',
        'options' => 'manufacturer_dom',
        'template_var' => 'MANUFACTURER_OPTIONS',
    ],
    'favorites_only' => [
        'query_type' => 'format',
        'operator' => 'subquery',
        'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
                                        WHERE sugarfavorites.deleted=0 
                                            and sugarfavorites.module = \'RevenueLineItems\'
                                            and sugarfavorites.assigned_user_id = \'{0}\'',
        'db_field' => [
            'id',
        ],
    ],
    //Range Search Support
    'range_date_entered' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'start_range_date_entered' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'end_range_date_entered' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'range_date_modified' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'start_range_date_modified' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'end_range_date_modified' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'range_date_closed' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'start_range_date_closed' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    'end_range_date_closed' => [
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true,
    ],
    //Range Search Support

];

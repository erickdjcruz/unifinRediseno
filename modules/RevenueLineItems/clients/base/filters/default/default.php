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
$viewdefs['RevenueLineItems']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'opportunity_name' => [],
        'account_name' => [],
        'sales_stage' => [],
        'probability' => [],
        'date_closed' => [],
        'commit_stage' => [],
        'product_template_name' => [],
        'category_name' => [],
        'worst_case' => [],
        'likely_case' => [],
        'best_case' => [],
        'date_entered' => [],
        'date_modified' => [],
        'tag' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        'assigned_user_name' => [],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],

    ],
];

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

$viewdefs['Purchases']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'quicksearch_field' => [
        'name',
    ],
    'quicksearch_priority' => 2,
    'fields' => [
        'name' => [],
        'account_name' => [],
        'product_template_name' => [],
        'total_revenue' => [],
        'total_quantity' => [],
        'start_date' => [],
        'end_date' => [],
        'service' => [],
        'assigned_user_name' => [],
        'renewable' => [],
        'category_name' => [],
        'type_name' => [],
        'team_name' => [],
        'date_entered' => [],
        'date_modified' => [],
        'tag' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
    ],
];

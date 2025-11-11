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

$module_name = '<module_name>';
$viewdefs[$module_name]['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'first_name' => [],
        'last_name' => [],
        'address_city' => [
            'dbFields' => [
                'primary_address_city',
                'alt_address_city',
            ],
            'vname' => 'LBL_CITY',
            'type' => 'text',
        ],
        'created_by_name' => [],
        'do_not_call' => [],
        'email' => [],
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

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
$viewdefs['PurchasedLineItems']['base']['filter']['basic'] = [
    'create' => true,
    'quicksearch_field' => ['name'],
    'quicksearch_priority' => 1,
    'filters' => [
        [
            'id' => 'all_records',
            'name' => 'LBL_LISTVIEW_FILTER_ALL',
            'filter_definition' => [],
            'editable' => false,
        ],
        [
            'id' => 'assigned_to_me',
            'name' => 'LBL_ASSIGNED_TO_ME',
            'filter_definition' => [
                '$owner' => '',
            ],
            'editable' => false,
        ],
        [
            'id' => 'recently_viewed',
            'name' => 'LBL_RECENTLY_VIEWED',
            'filter_definition' => [
                '$tracker' => '-7 day',
            ],
            'editable' => false,
        ],
        [
            'id' => 'recently_created',
            'name' => 'LBL_NEW_RECORDS',
            'filter_definition' => [
                'date_entered' => [
                    '$dateRange' => 'last_7_days',
                ],
            ],
            'editable' => false,
        ],
        [
            'id' => 'favorites',
            'name' => 'LBL_FAVORITES',
            'filter_definition' => [
                '$favorite' => '',
            ],
            'editable' => false,
        ],
        [
            'id' => 'add_on_plis',
            'name' => 'LBL_PLI_ADDONS',
            'filter_definition' => [
                [
                    'account_id' => [
                        '$in' => [],
                    ],
                ],
            ],
            'editable' => true,
            'is_template' => true,
        ],
    ],
];

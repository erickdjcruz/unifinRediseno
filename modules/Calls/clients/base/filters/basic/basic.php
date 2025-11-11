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
$viewdefs['Calls']['base']['filter']['basic'] = [
    'create' => true,
    'quicksearch_field' => ['name'],
    'quicksearch_priority' => 1,
    'quicksearch_split_terms' => false,
    'filters' => [
        [
            'id' => 'all_records',
            'name' => 'LBL_LISTVIEW_FILTER_ALL',
            'filter_definition' => [],
            'editable' => false,
        ],
        [
            'id' => 'assigned_to_me',
            'name' => 'LBL_LIST_MY_CALLS',
            'filter_definition' => [
                '$owner' => '',
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
            'id' => 'recently_viewed',
            'name' => 'LBL_RECENTLY_VIEWED',
            'filter_definition' => [
                '$tracker' => '-7 DAY',
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
            'id' => 'my_scheduled_calls',
            'name' => 'LBL_MY_SCHEDULED_CALLS',
            'filter_definition' => [
                [
                    '$owner' => '',
                ],
                [
                    'status' => [
                        '$in' => ['Planned'],
                    ],
                ],
            ],
            'editable' => false,
        ],
        [
            'id' => 'calls_attending',
            'name' => 'LBL_GUEST_CALLS',
            'filter_definition' => [
                [
                    '$guest' => '',
                ],
            ],
            'editable' => false,
        ],
        [
            'id' => 'scheduled_calls_attending',
            'name' => 'LBL_GUEST_SCHEDULED_CALLS',
            'filter_definition' => [
                [
                    '$guest' => '',
                ],
                [
                    'status' => [
                        '$in' => ['Planned'],
                    ],
                ],
            ],
            'editable' => false,
        ],
        [
            'id' => 'available_items',
            'name' => 'LBL_AVAILABLE_CALLS',
            'filter_definition' => [
                [
                    'is_customer_journey_activity' => [
                        '$equals' => 0,
                    ],
                ],
                [
                    'status' => [
                        '$not_in' => ['Not Held'],
                    ],
                ],
            ],
            'editable' => true,
            'is_template' => true,
        ],
    ],
];

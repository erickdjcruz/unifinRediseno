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

$viewdefs['Leads']['base']['filter']['basic'] = [
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
            'id' => 'favorites',
            'name' => 'LBL_FAVORITES',
            'filter_definition' => [
                '$favorite' => '',
            ],
            'editable' => false,
        ],
        [
            'id' => 'mql_leads',
            'name' => 'LBL_LIST_MQL_LEADS',
            'filter_definition' => [
                'status' => [
                    '$in' => [
                        'New',
                        'Assigned',
                    ],
                ],
                '$owner' => '',
            ],
            'filter_template' => [
                'status' => [
                    '$in' => [
                        'New',
                        'Assigned',
                    ],
                ],
                '$owner' => '',
            ],
            'editable' => false,
            'is_template' => true,
        ],
        [
            'id' => 'new_mql_leads',
            'name' => 'LBL_LIST_NEW_MQL_LEADS',
            'filter_definition' => [
                'status' => [
                    '$in' => [
                        'New',
                    ],
                ],
                'date_entered' => [
                    '$dateRange' => 'last_30_days',
                ],
            ],
            'filter_template' => [
                'status' => [
                    '$in' => [
                        'New',
                    ],
                ],
                'date_entered' => [
                    '$dateRange' => 'last_30_days',
                ],
            ],
            'editable' => false,
            'is_template' => true,
        ],
        [
            'id' => 'sql_leads',
            'name' => 'LBL_LIST_SQL_LEADS',
            'filter_definition' => [
                'status' => [
                    '$in' => [
                        'In Process',
                    ],
                ],
                '$owner' => '',
            ],
            'filter_template' => [
                'status' => [
                    '$in' => [
                        'In Process',
                    ],
                ],
                '$owner' => '',
            ],
            'editable' => false,
            'is_template' => true,
        ],
        [
            'id' => 'todays_leads',
            'name' => 'LBL_LIST_TODAY_LEADS',
            'filter_definition' => [
                'status' => [
                    '$in' => [
                        'New',
                    ],
                ],
                'date_entered' => [
                    '$dateRange' => 'today',
                ],
            ],
            'filter_template' => [
                'status' => [
                    '$in' => [
                        'New',
                    ],
                ],
                'date_entered' => [
                    '$dateRange' => 'today',
                ],
            ],
            'editable' => false,
            'is_template' => true,
        ],
        [
            'id' => 'my_follow_up_leads',
            'name' => 'LBL_LIST_MY_LEADS',
            'filter_definition' => [
                'status' => [
                    '$in' => [
                        'Assigned',
                        'In Process',
                    ],
                ],
                '$owner' => '',
            ],
            'filter_template' => [
                'status' => [
                    '$in' => [
                        'Assigned',
                        'In Process',
                    ],
                ],
                '$owner' => '',
            ],
            'editable' => false,
            'is_template' => true,
        ],
    ],
];

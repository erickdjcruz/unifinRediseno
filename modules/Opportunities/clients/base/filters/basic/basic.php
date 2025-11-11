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

$viewdefs['Opportunities']['base']['filter']['basic'] = [
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
            'id' => 'assigned_to_me',
            'name' => 'LBL_TOP10_OPPORTUNITIES_MY_OPP',
            'filter_definition' => [
                '$owner' => '',
            ],
            'editable' => false,
        ],
        [
            'id' => 'this_quarter_opportunities',
            'name' => 'LBL_THIS_QUARTER_OPPORTUNITIES',
            'filter_definition' => [
                'date_closed' => [
                    '$dateRange' => 'this_quarter',
                ],
            ],
            'filter_template' => [
                'date_closed' => [
                    '$dateRange' => 'this_quarter',
                ],
            ],
            'editable' => false,
            'is_template' => true,
        ], [
            'id' => 'my_open_existing_business',
            'name' => 'LBL_MY_OPEN_EXISTING_BUSINESS',
            'filter_definition' => [
                'opportunity_type' => [
                    '$in' => [
                        'Existing Business',
                    ],
                ],
                'sales_stage' => [
                    '$not_in' => [
                        'Closed Won',
                        'Closed Lost',
                    ],
                ],
                '$owner' => '',
            ],
            'filter_template' => [
                'opportunity_type' => [
                    '$in' => [
                        'Existing Business',
                    ],
                ],
                'sales_stage' => [
                    '$not_in' => [
                        'Closed Won',
                        'Closed Lost',
                    ],
                ],
                '$owner' => '',
            ],
            'editable' => false,
            'is_template' => true,
        ], [
            'id' => 'my_open_opportunities',
            'name' => 'LBL_MY_OPEN_OPPORTUNITIES',
            'filter_definition' => [
                '$owner' => '',
                'sales_stage' => [
                    '$not_in' => [
                        'Closed Won',
                        'Closed Lost',
                    ],
                ],
            ],
            'filter_template' => [
                '$owner' => '',
                'sales_stage' => [
                    '$not_in' => [
                        'Closed Won',
                        'Closed Lost',
                    ],
                ],
            ],
            'editable' => false,
            'is_template' => true,
        ],
    ],
];

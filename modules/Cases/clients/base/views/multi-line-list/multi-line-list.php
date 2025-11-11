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
$viewdefs['Cases']['base']['view']['multi-line-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'case_number',
                    'label' => 'LBL_AGENT_WORKBENCH_NUMBER',
                    'width' => 'xsmall',
                    'subfields' => [
                        [
                            'name' => 'case_number',
                            'label' => 'LBL_AGENT_WORKBENCH_NUMBER',
                            'default' => true,
                            'enabled' => true,
                            'readonly' => true,
                            'link' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_AGENT_WORKBENCH_PRIORITY_STATUS',
                    'width' => 'small',
                    'subfields' => [
                        [
                            'name' => 'priority',
                            'label' => 'LBL_LIST_PRIORITY',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'enum',
                        ],
                        [
                            'name' => 'status',
                            'label' => 'LBL_WIDGET_STATUS',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'case-status',
                            'widget_name' => 'widget_status',
                        ],
                    ],
                ],
                [
                    'name' => 'follow_up_datetime',
                    'label' => 'LBL_AGENT_WORKBENCH_FOLLOW_UP',
                    'width' => 'medium',
                    'subfields' => [
                        [
                            'name' => 'follow_up_datetime',
                            'label' => 'LBL_WIDGET_FOLLOW_UP_DATETIME',
                            'default' => true,
                            'enabled' => true,
                            'readonly' => true,
                            'type' => 'follow-up-datetime-colorcoded',
                            'widget_name' => 'widget_follow_up_datetime',
                            'color_code_classes' => [
                                'overdue' => 'expired',
                                'in_a_day' => 'soon-expired',
                                'more_than_a_day' => 'white black-text',
                            ],
                        ],
                        [
                            'name' => 'follow_up_datetime',
                            'label' => 'LBL_FOLLOW_UP_DATETIME',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'datetimecombo',
                        ],
                    ],
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_AGENT_WORKBENCH_SUBJECT_DESCRIPTION',
                    'width' => 'xlarge',
                    'subfields' => [
                        [
                            'name' => 'name',
                            'label' => 'LBL_LIST_SUBJECT',
                            'link' => false,
                            'default' => true,
                            'enabled' => true,
                        ],
                        [
                            'name' => 'description',
                            'default' => true,
                            'enabled' => true,
                            'readonly' => true,
                            'sortable' => false,
                        ],
                    ],
                ],
                [
                    'name' => 'business_center',
                    'label' => 'LBL_BUSINESS_CENTER',
                    'width' => 'small',
                    'subfields' => [
                        [
                            'name' => 'business_center_name',
                            'label' => 'LBL_BUSINESS_CENTER',
                            'link' => false,
                            'default' => true,
                            'enabled' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT',
                    'width' => 'medium',
                    'subfields' => [
                        [
                            'name' => 'account_name',
                            'label' => 'LBL_LIST_ACCOUNT_NAME',
                            'module' => 'Accounts',
                            'id' => 'ACCOUNT_ID',
                            'ACLTag' => 'ACCOUNT',
                            'related_fields' => ['account_id'],
                            'link' => false,
                            'default' => true,
                            'enabled' => true,
                        ],
                        [
                            'name' => 'service_level',
                            'label' => 'LBL_SERVICE_LEVEL',
                            'type' => 'enum',
                            'enum_module' => 'Accounts',
                            'link' => false,
                            'default' => true,
                            'enabled' => true,
                            'readonly' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'width' => 'small',
                    'subfields' => [
                        [
                            'name' => 'assigned_user_name',
                            'label' => 'LBL_ASSIGNED_TO_NAME',
                            'id' => 'ASSIGNED_USER_ID',
                            'link' => false,
                            'default' => true,
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'collectionOptions' => [
        'params' => [
            'order_by' => 'follow_up_datetime',
            'nulls_last' => true,
        ],
    ],
    'filterDef' => [
        [
            'status' => [
                '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
            ],
            '$owner' => '',
        ],
    ],
    'defaults' => [
        'order_by_primary' => 'follow_up_datetime:asc',
        'order_by_secondary' => '',
        'freeze_first_column' => true,
    ],
];

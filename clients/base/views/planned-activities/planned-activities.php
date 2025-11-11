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

$viewdefs['base']['view']['planned-activities'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PLANNED_ACTIVITIES_DASHLET',
            'description' => 'LBL_PLANNED_ACTIVITIES_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => '10',
                'date' => 'today',
                'visibility' => 'user',
            ],
            'preview' => [
                'limit' => '10',
                'date' => 'today',
                'visibility' => 'user',
            ],
            'filter' => [
                'module' => [
                    'Accounts',
                    'Bugs',
                    'Cases',
                    'Contacts',
                    'Home',
                    'Leads',
                    'Opportunities',
                    'Prospects',
                    'RevenueLineItems',
                ],
                'view' => 'record',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'actiondropdown',
                'no_default_action' => true,
                'icon' => 'sicon-plus',
                'buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'createRecord',
                        'params' => [
                            'link' => 'meetings',
                            'module' => 'Meetings',
                        ],
                        'label' => 'LBL_SCHEDULE_MEETING',
                        'acl_action' => 'create',
                        'acl_module' => 'Meetings',
                        'name' => 'schedule_meeting',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'createRecord',
                        'params' => [
                            'link' => 'calls',
                            'module' => 'Calls',
                        ],
                        'label' => 'LBL_SCHEDULE_CALL',
                        'acl_action' => 'create',
                        'acl_module' => 'Calls',
                        'name' => 'log_call',
                    ],
                ],
            ],
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'toggleClicked',
                        'label' => 'LBL_DASHLET_MINIMIZE',
                        'event' => 'minimize',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'date',
                    'label' => 'LBL_DASHLET_CONFIGURE_FILTERS',
                    'type' => 'enum',
                    'options' => 'planned_activities_filter_options',
                ],
                [
                    'name' => 'visibility',
                    'label' => 'LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY',
                    'type' => 'enum',
                    'options' => 'planned_activities_visibility_options',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'planned_activities_limit_options',
                ],
            ],
        ],
    ],
    'tabs' => [
        [
            'active' => true,
            'filter_applied_to' => 'date_start',
            'filters' => [
                'status' => ['$not_in' => ['Held', 'Not Held']],
            ],
            'link' => 'meetings',
            'module' => 'Meetings',
            'order_by' => 'date_start:asc',
            'record_date' => 'date_start',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-remove',
                    'css_class' => 'btn btn-mini',
                    'event' => 'planned-activities:close-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PLANNED_ACTIVITIES_DASHLET_HELD_ACTIVITY',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'unlink-action',
                    'icon' => 'sicon-unlink',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ],
            ],
            'include_child_items' => true,
            'invitation_actions' => [
                'name' => 'accept_status_users',
                'type' => 'invitation-actions',
            ],
            'overdue_badge' => [
                'name' => 'date_start',
                'type' => 'overdue-badge',
            ],
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_start',
            ],
        ],
        [
            'filter_applied_to' => 'date_start',
            'filters' => [
                'status' => ['$not_in' => ['Held', 'Not Held']],
            ],
            'link' => 'calls',
            'module' => 'Calls',
            'order_by' => 'date_start:asc',
            'record_date' => 'date_start',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-remove',
                    'css_class' => 'btn btn-mini',
                    'event' => 'planned-activities:close-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PLANNED_ACTIVITIES_DASHLET_HELD_ACTIVITY',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'unlink-action',
                    'icon' => 'sicon-unlink',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ],
            ],
            'include_child_items' => true,
            'invitation_actions' => [
                'name' => 'accept_status_users',
                'type' => 'invitation-actions',
            ],
            'overdue_badge' => [
                'name' => 'date_start',
                'type' => 'overdue-badge',
            ],
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_start',
            ],
        ],
    ],
];

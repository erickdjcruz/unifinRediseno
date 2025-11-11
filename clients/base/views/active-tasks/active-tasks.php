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

$viewdefs['base']['view']['active-tasks'] = [
    'dashlets' => [
        [
            'label' => 'LBL_ACTIVE_TASKS_DASHLET',
            'description' => 'LBL_ACTIVE_TASKS_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => 10,
                'visibility' => 'user',
            ],
            'preview' => [
                'limit' => 10,
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
                            'module' => 'Tasks',
                            'link' => 'tasks',
                        ],
                        'label' => 'LBL_CREATE_TASK',
                        'acl_action' => 'create',
                        'acl_module' => 'Tasks',
                        'name' => 'create_task',
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
                    'name' => 'visibility',
                    'label' => 'LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY',
                    'type' => 'enum',
                    'options' => 'tasks_visibility_options',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'tasks_limit_options',
                ],
            ],
        ],
    ],
    'tabs' => [
        [
            'active' => true,
            'filters' => [
                'status' => ['$not_in' => ['Completed', 'Deferred', 'Not Applicable']],
                'date_due' => ['$lte' => 'today'],
            ],
            'label' => 'LBL_ACTIVE_TASKS_DASHLET_DUE_NOW',
            'link' => 'tasks',
            'module' => 'Tasks',
            'order_by' => 'date_due:asc',
            'record_date' => 'date_due',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-remove',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
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
            'overdue_badge' => [
                'name' => 'date_due',
                'type' => 'overdue-badge',
                'css_class' => 'pull-right',
            ],
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_due',
            ],
        ],
        [
            'filters' => [
                'status' => ['$not_in' => ['Completed', 'Deferred', 'Not Applicable']],
                'date_due' => ['$gt' => 'today'],
            ],
            'label' => 'LBL_ACTIVE_TASKS_DASHLET_UPCOMING',
            'link' => 'tasks',
            'module' => 'Tasks',
            'order_by' => 'date_due:asc',
            'record_date' => 'date_due',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-remove',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
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
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_due',
            ],
        ],
        [
            'filters' => [
                'status' => ['$not_in' => ['Completed', 'Deferred', 'Not Applicable']],
                'date_due' => ['$is_null' => ''],
            ],
            'label' => 'LBL_ACTIVE_TASKS_DASHLET_TODO',
            'link' => 'tasks',
            'module' => 'Tasks',
            'order_by' => 'date_entered:asc',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-remove',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
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
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
            ],
        ],
    ],
    'visibility_labels' => [
        'user' => 'LBL_ACTIVE_TASKS_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_ACTIVE_TASKS_DASHLET_GROUP_BUTTON_LABEL',
    ],
];

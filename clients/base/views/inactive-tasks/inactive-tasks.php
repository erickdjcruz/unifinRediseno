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

$viewdefs['base']['view']['inactive-tasks'] = [
    'dashlets' => [
        [
            'label' => 'LBL_INACTIVE_TASKS_DASHLET',
            'description' => 'LBL_INACTIVE_TASKS_DASHLET_DESCRIPTION',
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
                        'name' => 'edit_button',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                        'name' => 'refresh_button',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'toggleClicked',
                        'label' => 'LBL_DASHLET_MINIMIZE',
                        'event' => 'minimize',
                        'name' => 'close_button',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                        'name' => 'remove_button',
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
                'status' => ['$equals' => 'Deferred'],
            ],
            'label' => 'LBL_INACTIVE_TASKS_DASHLET_DEFERRED',
            'link' => 'tasks',
            'module' => 'Tasks',
            'order_by' => 'date_modified:desc',
            'record_date' => 'date_modified',
            'row_actions' => [
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
                'date_modified',
            ],
        ],
        [
            'filters' => [
                'status' => ['$equals' => 'Completed'],
            ],
            'label' => 'LBL_INACTIVE_TASKS_DASHLET_COMPLETED',
            'link' => 'tasks',
            'module' => 'Tasks',
            'order_by' => 'date_modified:desc',
            'record_date' => 'date_modified',
            'row_actions' => [
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
                'date_modified',
            ],
        ],
    ],
    'visibility_labels' => [
        'user' => 'LBL_INACTIVE_TASKS_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_INACTIVE_TASKS_DASHLET_GROUP_BUTTON_LABEL',
    ],
];

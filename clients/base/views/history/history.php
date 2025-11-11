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

$viewdefs['base']['view']['history'] = [
    'dashlets' => [
        [
            'label' => 'LBL_HISTORY_DASHLET',
            'description' => 'LBL_HISTORY_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => '10',
                'filter' => '7',
                'visibility' => 'user',
            ],
            'preview' => [
                'limit' => '10',
                'filter' => '7',
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
                        'action' => 'archiveEmail',
                        'params' => [
                            'link' => 'emails',
                            'module' => 'Emails',
                        ],
                        'label' => 'LBL_CREATE_ARCHIVED_EMAIL',
                        'acl_action' => 'create',
                        'acl_module' => 'Emails',
                        'name' => 'create_archived_email',
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
                    'name' => 'filter',
                    'label' => 'LBL_DASHLET_CONFIGURE_FILTERS',
                    'type' => 'enum',
                    'options' => 'history_filter_options',
                ],
                [
                    'name' => 'visibility',
                    'label' => 'LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY',
                    'type' => 'enum',
                    'options' => 'history_visibility_options',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'history_limit_options',
                ],
            ],
        ],
    ],
    'filter' => [
        [
            'name' => 'filter',
            'label' => 'LBL_FILTER',
            'type' => 'enum',
            'options' => 'history_filter_options',
        ],
    ],
    'tabs' => [
        [
            'active' => true,
            'filter_applied_to' => 'date_start',
            'filters' => [
                'status' => ['$in' => ['Held', 'Not Held']],
            ],
            'link' => 'meetings',
            'module' => 'Meetings',
            'order_by' => 'date_start:desc',
            'record_date' => 'date_start',
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
            'include_child_items' => true,
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_start',
            ],
        ],
        [
            'filter_applied_to' => 'date_entered',
            'filters' => [
                'state' => [
                    '$in' => ['Archived'],
                ],
            ],
            'labels' => [
                'singular' => 'LBL_HISTORY_DASHLET_EMAIL_SINGULAR',
                'plural' => 'LBL_HISTORY_DASHLET_EMAIL_PLURAL',
            ],
            'link' => 'archived_emails',
            'module' => 'Emails',
            'order_by' => 'date_entered:desc',
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
                'date_entered',
            ],
        ],
        [
            'filter_applied_to' => 'date_start',
            'filters' => [
                'status' => ['$in' => ['Held', 'Not Held']],
            ],
            'link' => 'calls',
            'module' => 'Calls',
            'order_by' => 'date_start:desc',
            'record_date' => 'date_start',
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
            'include_child_items' => true,
            'fields' => [
                'name',
                'assigned_user_id',
                'assigned_user_name',
                'date_start',
            ],
        ],
    ],
    'visibility_labels' => [
        'user' => 'LBL_HISTORY_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_HISTORY_DASHLET_GROUP_BUTTON_LABEL',
    ],
];

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
return [
    'metadata' => [
        'buttons' => [
            [
                'name' => 'clear',
                'type' => 'button',
                'label' => 'LBL_CLOSE_BUTTON_LABEL',
                'css_class' => 'btn btn-primary clear-button hidden',
                'events' => [
                    'click' => 'button:clear_button:click',
                ],
            ],
        ],
        'tabs' => [
            // TAB 1 Search
            [
                'icon' => [
                    'image' => '<i class="sicon sicon-search"></i>',
                ],
                'name' => 'LBL_SEARCH',
                'components' => [
                    [
                        'layout' => 'omnichannel-search',
                    ],
                ],
            ],
            // TAB 2 Contacts
            [
                'icon' => [
                    'module' => 'Contacts',
                ],
                'module' => 'Contacts',
                'name' => 'LBL_CONTACT',
                'dashlets' => [
                    [
                        'view' => [
                            'type' => 'dashablerecord',
                            'module' => 'Contacts',
                            'tabs' => [
                                [
                                    'active' => true,
                                    'label' => 'LBL_MODULE_NAME_SINGULAR',
                                    'link' => '',
                                    'module' => 'Contacts',
                                ],
                                [
                                    'active' => false,
                                    'label' => 'LBL_MODULE_NAME_SINGULAR',
                                    'link' => 'accounts',
                                    'module' => 'Accounts',
                                ],
                                [
                                    'active' => false,
                                    'link' => 'opportunities',
                                    'module' => 'Opportunities',
                                    'order_by' => [
                                        'field' => 'date_closed',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'sales_status',
                                        'amount',
                                        'date_closed',
                                    ],
                                ],
                                [
                                    'active' => false,
                                    'link' => 'billing_quotes',
                                    'module' => 'Quotes',
                                    'label' => 'LBL_QUOTES_BILL_TO',
                                    'order_by' => [
                                        'field' => 'date_quote_expected_closed',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'quote_stage',
                                        'date_quote_expected_closed',
                                        'total',
                                    ],
                                ],
                            ],
                            'tab_list' => [
                                'Contacts',
                                'accounts',
                                'opportunities',
                                'billing_quotes',
                            ],
                        ],
                        'context' => [
                            'module' => 'Contacts',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'dashlet-searchable-kb-list',
                            'label' => 'LBL_DASHLET_KB_SEARCH_NAME',
                            'data_provider' => 'Categories',
                            'config_provider' => 'KBContents',
                            'root_name' => 'category_root',
                            'extra_provider' => [
                                'module' => 'KBContents',
                                'field' => 'category_id',
                            ],
                        ],
                        'context' => [
                            'module' => 'KBContents',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'dashlet-console-list',
                            'module' => 'Cases',
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 8,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'activity-timeline',
                            'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            'module' => 'Contacts',
                            'custom_toolbar' => [
                                'buttons' => [
                                    [
                                        'type' => 'actiondropdown',
                                        'no_default_action' => true,
                                        'icon' => 'sicon-plus',
                                        'buttons' => [
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'composeEmail',
                                                'params' => [
                                                    'link' => 'emails',
                                                    'module' => 'Emails',
                                                ],
                                                'label' => 'LBL_COMPOSE_EMAIL_BUTTON_LABEL2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Emails',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'calls',
                                                    'module' => 'Calls',
                                                ],
                                                'label' => 'LBL_SCHEDULE_CALL2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Calls',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'meetings',
                                                    'module' => 'Meetings',
                                                ],
                                                'label' => 'LBL_SCHEDULE_MEETING2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Meetings',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'notes',
                                                    'module' => 'Notes',
                                                ],
                                                'label' => 'LBL_CREATE_NOTE_OR_ATTACHMENT2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Notes',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'tasks',
                                                    'module' => 'Tasks',
                                                ],
                                                'label' => 'LBL_CREATE_TASK2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Tasks',
                                            ],
                                        ],
                                    ],
                                    [
                                        'type' => 'dashletaction',
                                        'css_class' => 'btn btn-invisible dashlet-toggle minify',
                                        'icon' => 'sicon-chevron-up',
                                        'action' => 'toggleMinify',
                                        'tooltip' => 'LBL_DASHLET_TOGGLE',
                                        'disallowed_layouts' => [
                                            [
                                                'name' => 'omnichannel-dashboard',
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
                                                'action' => 'reloadData',
                                                'label' => 'LBL_DASHLET_REFRESH_LABEL',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'removeClicked',
                                                'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                                'name' => 'remove_button',
                                                'disallowed_layouts' => [
                                                    [
                                                        'name' => 'omnichannel-dashboard',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'context' => [
                            'module' => 'Contacts',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 8,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'purchase-history',
                            'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                            'linked_account_field' => 'account_id',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 16,
                    ],
                    [
                        'view' => [
                            'type' => 'active-subscriptions',
                            'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                            'linked_subscriptions_account_field' => 'account_id',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 16,
                    ],
                ],
            ],
            // Tab 3 Account
            [
                'icon' => [
                    'module' => 'Accounts',
                ],
                'module' => 'Accounts',
                'name' => 'LBL_ACCOUNT',
                'dashlets' => [
                    [
                        'view' => [
                            'type' => 'dashablerecord',
                            'module' => 'Accounts',
                            'tabs' => [
                                [
                                    'active' => true,
                                    'label' => 'LBL_MODULE_NAME_SINGULAR',
                                    'link' => '',
                                    'module' => 'Accounts',
                                ],
                                [
                                    'active' => false,
                                    'link' => 'contacts',
                                    'module' => 'Contacts',
                                    'order_by' => [
                                        'field' => 'name',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'title',
                                        'email',
                                        'phone_work',
                                    ],
                                ],
                                [
                                    'active' => false,
                                    'link' => 'opportunities',
                                    'module' => 'Opportunities',
                                    'order_by' => [
                                        'field' => 'date_closed',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'sales_status',
                                        'amount',
                                        'date_closed',
                                    ],
                                ],
                                [
                                    'active' => false,
                                    'link' => 'quotes',
                                    'module' => 'Quotes',
                                    'order_by' => [
                                        'field' => 'date_quote_expected_closed',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'quote_stage',
                                        'date_quote_expected_closed',
                                        'total',
                                    ],
                                ],
                            ],
                            'tab_list' => [
                                'Accounts',
                                'contacts',
                                'opportunities',
                                'quotes',
                            ],
                        ],
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'module' => 'Accounts',
                            'type' => 'activity-timeline',
                            'label' => 'LBL_ACTIVITY_TIMELINE_DASHLET',
                        ],
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'commentlog-dashlet',
                            'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                        ],
                        'context' => [
                            'module' => 'Accounts',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 8,
                    ],
                    [
                        'view' => [
                            'type' => 'dashlet-console-list',
                            'module' => 'Cases',
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 8,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'purchase-history',
                            'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                            'linked_account_field' => 'id',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 16,
                    ],
                    [
                        'view' => [
                            'type' => 'active-subscriptions',
                            'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                            'linked_subscriptions_account_field' => 'id',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 16,
                    ],
                ],
            ],
            // Tab 4 Lead
            [
                'icon' => [
                    'module' => 'Leads',
                ],
                'module' => 'Leads',
                'name' => 'LBL_LEAD',
                'dashlets' => [
                    [
                        'view' => [
                            'type' => 'dashablerecord',
                            'module' => 'Leads',
                            'tabs' => [
                                [
                                    'active' => true,
                                    'label' => 'LBL_MODULE_NAME_SINGULAR',
                                    'link' => '',
                                    'module' => 'Leads',
                                ],
                            ],
                        ],
                        'context' => [
                            'module' => 'Leads',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'module' => 'Leads',
                            'type' => 'activity-timeline',
                            'label' => 'LBL_ACTIVITY_TIMELINE_DASHLET',
                        ],
                        'context' => [
                            'module' => 'Leads',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'commentlog-dashlet',
                            'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                        ],
                        'context' => [
                            'module' => 'Leads',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 8,
                    ],
                    [
                        'view' => [
                            'name' => 'active-tasks',
                            'label' => 'LBL_ACTIVE_TASKS_DASHLET',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 6,
                        'y' => 8,
                        'autoPosition' => false,
                    ],
                ],
            ],
            // TAB 5 Cases
            [
                'icon' => [
                    'module' => 'Cases',
                ],
                'module' => 'Cases',
                'name' => 'LBL_CASE',
                'dashlets' => [
                    [
                        'view' => [
                            'type' => 'dashablerecord',
                            'module' => 'Cases',
                            'tabs' => [
                                [
                                    'active' => true,
                                    'label' => 'LBL_MODULE_NAME_SINGULAR',
                                    'link' => '',
                                    'module' => 'Cases',
                                ],
                                [
                                    'active' => false,
                                    'link' => 'tasks',
                                    'module' => 'Tasks',
                                    'order_by' => [
                                        'field' => 'date_entered',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'assigned_user_name',
                                        'date_entered',
                                    ],
                                ],
                                [
                                    'active' => false,
                                    'link' => 'contacts',
                                    'module' => 'Contacts',
                                    'order_by' => [
                                        'field' => 'date_entered',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'name',
                                        'assigned_user_name',
                                        'date_entered',
                                    ],
                                ],
                                [
                                    'active' => false,
                                    'link' => 'documents',
                                    'module' => 'Documents',
                                    'order_by' => [
                                        'field' => 'active_date',
                                        'direction' => 'desc',
                                    ],
                                    'limit' => 5,
                                    'fields' => [
                                        'document_name',
                                        'active_date',
                                    ],
                                ],
                            ],
                            'tab_list' => [
                                'Cases',
                                'tasks',
                                'contacts',
                                'documents',
                            ],
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'dashablerecord',
                            'module' => 'Accounts',
                            'tabs' => [
                                [
                                    'module' => 'Accounts',
                                    'link' => 'accounts',
                                ],
                            ],
                            'tab_list' => [
                                'accounts',
                            ],
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 8,
                        'x' => 0,
                        'y' => 8,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'dashlet-searchable-kb-list',
                            'label' => 'LBL_DASHLET_KB_SEARCH_NAME',
                            'data_provider' => 'Categories',
                            'config_provider' => 'KBContents',
                            'root_name' => 'category_root',
                            'extra_provider' => [
                                'module' => 'KBContents',
                                'field' => 'category_id',
                            ],
                        ],
                        'context' => [
                            'module' => 'KBContents',
                        ],
                        'width' => 6,
                        'height' => 6,
                        'x' => 6,
                        'y' => 0,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'commentlog-dashlet',
                            'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 5,
                        'x' => 6,
                        'y' => 6,
                        'autoPosition' => false,
                    ],
                    [
                        'view' => [
                            'type' => 'activity-timeline',
                            'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            'module' => 'Cases',
                            'custom_toolbar' => [
                                'buttons' => [
                                    [
                                        'type' => 'actiondropdown',
                                        'no_default_action' => true,
                                        'icon' => 'sicon-plus',
                                        'buttons' => [
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'composeEmail',
                                                'params' => [
                                                    'link' => 'emails',
                                                    'module' => 'Emails',
                                                ],
                                                'label' => 'LBL_COMPOSE_EMAIL_BUTTON_LABEL2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Emails',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'calls',
                                                    'module' => 'Calls',
                                                ],
                                                'label' => 'LBL_SCHEDULE_CALL2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Calls',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'meetings',
                                                    'module' => 'Meetings',
                                                ],
                                                'label' => 'LBL_SCHEDULE_MEETING2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Meetings',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'notes',
                                                    'module' => 'Notes',
                                                ],
                                                'label' => 'LBL_CREATE_NOTE_OR_ATTACHMENT2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Notes',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'messages',
                                                    'module' => 'Messages',
                                                ],
                                                'label' => 'LBL_CREATE_MESSAGE2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Messages',
                                            ],
                                            [
                                                'type' => 'dashletaction',
                                                'action' => 'createRecord',
                                                'params' => [
                                                    'link' => 'tasks',
                                                    'module' => 'Tasks',
                                                ],
                                                'label' => 'LBL_CREATE_TASK2',
                                                'acl_action' => 'create',
                                                'acl_module' => 'Tasks',
                                            ],
                                        ],
                                    ],
                                    [
                                        'type' => 'dashletaction',
                                        'css_class' => 'dashlet-toggle btn btn-invisible minify',
                                        'icon' => 'sicon-chevron-up',
                                        'action' => 'toggleMinify',
                                        'tooltip' => 'LBL_DASHLET_MINIMIZE',
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
                                                'action' => 'reloadData',
                                                'label' => 'LBL_DASHLET_REFRESH_LABEL',
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
                        ],
                        'context' => [
                            'module' => 'Cases',
                        ],
                        'width' => 6,
                        'height' => 5,
                        'x' => 6,
                        'y' => 11,
                        'autoPosition' => false,
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_OMNICHANNEL_DASHBOARD',
    'id' => '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7',
];

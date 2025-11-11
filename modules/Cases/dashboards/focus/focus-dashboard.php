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
        'dashlets' => [
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
                                'tooltip' => 'LBL_CREATE_BUTTON_LABEL',
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
                                'css_class' => 'btn btn-invisible',
                                'icon' => 'sicon-refresh',
                                'action' => 'reloadData',
                                'tooltip' => 'LBL_DASHLET_REFRESH_LABEL',
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
                'width' => 7,
                'height' => 16,
                'x' => 0,
                'y' => 0,
                'autoposition' => false,
            ],
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
                            'label' => 'LBL_MODULE_NAME_SINGULAR',
                            'link' => 'accounts',
                            'module' => 'Accounts',
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
                        'accounts',
                        'tasks',
                        'contacts',
                        'documents',
                    ],
                ],
                'context' => [
                    'module' => 'Cases',
                ],
                'width' => 5,
                'height' => 10,
                'x' => 7,
                'y' => 0,
                'autoposition' => false,
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
                'width' => 5,
                'height' => 6,
                'x' => 7,
                'y' => 10,
                'autoposition' => false,
            ],
        ],
    ],
    'name' => 'LBL_CASES_FOCUS_DRAWER_DASHBOARD',
    'id' => 'c290ef46-7606-11e9-9129-f218983a1c3e',
];

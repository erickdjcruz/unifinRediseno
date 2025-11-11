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

$viewdefs['Accounts']['base']['view']['activity-timeline'] = [
    'dashlets' => [
        [
            'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
            'description' => 'LBL_ACTIVITY_TIMELINE_DASHLET_DESCRIPTION',
            'config' => ['module' => 'Accounts'],
            'preview' => ['module' => 'Accounts'],
            'filter' => [
                'view' => 'record',
                'module' => [
                    'Accounts',
                ],
            ],
        ],
    ],
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
];

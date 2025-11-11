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

$viewdefs['Notifications']['base']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'last_state' => [
        'id' => 'record_view',
        'defaults' => [
            'show_more' => 'more',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'header' => true,
            'fields' => [
                [
                    'name' => 'severity',
                    'type' => 'severity',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'name',
                ],
                [
                    'name' => 'is_read',
                    'type' => 'read',
                    'dismiss_label' => true,
                    'mark_as_read' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'description',
                    'type' => 'html',
                    'span' => 12,
                ],
                [
                    'name' => 'parent_name',
                ],
                [
                    'name' => 'assigned_user_name',
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_PANEL_ADVANCED',
            'hide' => true,
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

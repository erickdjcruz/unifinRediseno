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

$viewdefs['Cases']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'status',
                'priority',
                [
                    'name' => 'follow_up_datetime',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_FOLLOW_UP',
                            'css_class' => 'activity-label',
                        ],
                        'follow_up_datetime',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'primary_contact_name',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_PRIMARY_CONTACT_NAME',
                            'css_class' => 'activity-label',
                        ],
                        [
                            'name' => 'primary_contact_name',
                            'show_avatar' => true,
                        ],
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-body',
            'fields' => [
                [
                    'name' => 'description',
                    'settings' => [
                        'max_display_chars' => 10000,
                        'collapsed' => false,
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_attachments',
            'css_class' => 'panel-attachments mt-2',
            'fields' => [
                [
                    'name' => 'attachment_list',
                    'type' => 'multi-attachments',
                    'module' => 'Notes',
                    'modulefield' => 'filename',
                    'link' => 'attachments',
                ],
            ],
        ],
    ],
];

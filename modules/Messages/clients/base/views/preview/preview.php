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
$viewdefs['Messages']['base']['view']['preview'] = [
    'templateMeta' => [
        'maxColumns' => 1,
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                'name',
            ],
        ],
        [
            'name' => 'panel_body',
            'fields' => [
                'date_start',
                'date_end',
                'parent_name',
                'channel_type',
                'status',
                'conversation_link',
                [
                    'name' => 'conversation',
                    'rows' => 5,
                    'type' => 'conversation',
                    'settings' => [
                        'pagination' => true,
                        'per_page' => 10,
                    ],
                    'span' => 12,
                ],
                'direction',
                [
                    'name' => 'invitees',
                    'type' => 'participants',
                    'label' => 'LBL_INVITEES',
                    'fields' => [
                        'name',
                        'accept_status_messages',
                        'picture',
                        'email',
                    ],
                    'related_fields' => [
                        'date_start',
                        'date_end',
                    ],
                    'max_num' => 20,
                    'span' => 12,
                ],
                [
                    'name' => 'description',
                    'rows' => 5,
                    'span' => 12,
                ],
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
                'team_name',
                'assigned_user_name',
            ],
        ],
        [
            'name' => 'panel_hidden',
            'hide' => true,
            'fields' => [
                [
                    'name' => 'date_entered_by',
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
                [
                    'name' => 'date_modified_by',
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
            ],
        ],
    ],
];

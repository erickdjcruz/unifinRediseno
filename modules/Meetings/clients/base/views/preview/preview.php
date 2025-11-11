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
$viewdefs['Meetings']['base']['view']['preview'] = [
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
                [
                    'name' => 'status',
                    'type' => 'event-status',
                    'enum_width' => 'auto',
                    'dropdown_width' => 'auto',
                    'dropdown_class' => 'select2-menu-only',
                    'container_class' => 'select2-menu-only',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'fields' => [
                [
                    'name' => 'duration',
                    'type' => 'duration',
                    'label' => 'LBL_START_AND_END_DATE_DETAIL_VIEW',
                    'dismiss_label' => false,
                    'inline' => false,
                    'show_child_labels' => true,
                    'fields' => [
                        [
                            'name' => 'date_start',
                            'time' => [
                                'step' => 15,
                            ],
                            'readonly' => false,
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_START_AND_END_DATE_TO',
                        ],
                        [
                            'name' => 'date_end',
                            'time' => [
                                'step' => 15,
                                'duration' => [
                                    'relative_to' => 'date_start',
                                ],
                            ],
                            'readonly' => false,
                        ],
                    ],
                    'span' => 9,
                    'related_fields' => [
                        'duration_hours',
                        'duration_minutes',
                    ],
                ],
                [
                    'name' => 'repeat_type',
                    'span' => 3,
                    'related_fields' => [
                        'repeat_parent_id',
                    ],
                    'readonly' => true,
                ],
                'location',
                [
                    'name' => 'description',
                    'span' => 12,
                    'rows' => 3,
                ],
                [
                    'name' => 'type',
                ],
                'parent_name',
                [
                    'name' => 'password',
                    'span' => 12,
                ],
                [
                    'name' => 'invitees',
                    'type' => 'participants',
                    'label' => 'LBL_INVITEES',
                    'span' => 12,
                    'fields' => [
                        'name',
                        'accept_status_meetings',
                        'picture',
                        'email',
                    ],
                ],
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
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

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
$viewdefs['DRI_Workflow_Templates']['base']['view']['create'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_RECORD_HEADER',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'width' => 42,
                    'height' => 42,
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                'name',
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
            ],
        ],
        'buttons' => [
            [
                'type' => 'button',
                'name' => 'cancel_button',
                'label' => 'LBL_CANCEL_BUTTON_LABEL',
                'css_class' => 'btn-invisible btn-link',
                'showOn' => 'edit',
                'events' => [
                    'click' => 'button:cancel_button:click',
                ],
            ],
            [
                'type' => 'rowaction',
                'event' => 'button:save_button:click',
                'name' => 'save_button',
                'label' => 'LBL_SAVE_BUTTON_LABEL',
                'css_class' => 'btn btn-primary',
                'showOn' => 'edit',
                'acl_action' => 'edit',
            ],
            [
                'name' => 'sidebar_toggle',
                'type' => 'sidebartoggle',
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => [
                'copied_template_name',
                '',
                'active',
                'available_modules',
                [
                    'name' => 'stage_numbering',
                    'type' => 'toggle',
                    'css_class' => 'horizontal-vertical',
                ],
                'update_assignees',
                [
                    'name' => 'active_limit',
                    'label' => 'LBL_ACTIVE_LIMIT',
                ],
                'disabled_stage_actions',
                'disabled_activity_actions',
                'assignee_rule',
                'target_assignee',
                'cancel_action',
                'not_applicable_action',
                'points',
                'related_activities',
                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                    'span' => 12,
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
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
                    'span' => 6,
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
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
                    'span' => 6,
                ],
                [
                    'name' => 'team_name',
                    'span' => 12,
                ],
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
    ],
    'templateMeta' => [
        'useTabs' => false,
    ],
];

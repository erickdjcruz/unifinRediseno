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
$viewdefs['CJ_Forms']['base']['view']['record'] = [
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
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'CJ_Forms',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
                    'acl_action' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
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
        [
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'trigger_label',
                    'type' => 'cj-forms-title',
                    'label' => 'LBL_TRIGGER_TITLE',
                    'label_description' => 'LBL_TRIGGER_DESCRIPTION',
                    'dismiss_label' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'main_trigger_type',
                    'label' => 'LBL_MAIN_TRIGGER_TYPE',
                    'placeholder' => 'LBL_MAIN_TRIGGER_TYPE_PLACEHOLDER',
                    'no_required_placeholder' => true,
                ],
                [
                    'name' => 'active',
                    'label' => 'LBL_ACTIVE',
                ],
                [
                    'name' => 'parent_name',
                    'related_fields' => [
                        'parent_type',
                    ],
                ],
                [
                    'span' => 6,
                ],
                [
                    'name' => 'smart_guide_template_name',
                    'type' => 'cj-template-trigger',
                    'label' => 'LBL_SMART_GUIDE_TEMPLATE',
                    'placeholder' => 'LBL_SMART_GUIDE_TEMPLATE_PLACEHOLDER',
                    'no_required_placeholder' => true,
                ],
                [
                    'span' => 6,
                ],
                [
                    'name' => 'action_type',
                    'type' => 'action-type',
                    'label' => 'LBL_ACTION_TYPE',
                ],
                [
                    'name' => 'trigger_event',
                    'label' => 'LBL_TRIGGER_EVENT',
                ],
                [
                    'name' => 'action_trigger_type',
                    'label' => 'LBL_ACTION_TRIGGER_TYPE',
                ],
                [
                    'name' => 'ignore_errors',
                    'label' => 'LBL_IGNORE_ERRORS',
                ],
                [
                    'name' => 'relationship',
                    'label' => 'LBL_RELATIONSHIP',
                    'type' => 'relationship',
                    'related_fields' => [
                        'activity_module',
                    ],
                    'span' => 12,
                ],
                [
                    'span' => 6,
                ],
                [
                    'name' => 'display_activity_rsa_icon',
                ],
                [
                    'name' => 'module_trigger',
                    'label' => 'LBL_MODULE_TRIGGER',
                    'placeholder' => 'LBL_MODULE_TRIGGER_PLACEHOLDER',
                    'no_required_placeholder' => true,
                ],
                [
                    'name' => 'field_trigger',
                    'label' => 'LBL_FIELD_TRIGGER',
                    'no_required_placeholder' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'target_action_label',
                    'type' => 'cj-forms-title',
                    'label' => 'LBL_TARGET_ACTION_TITLE',
                    'label_description' => 'LBL_TARGET_ACTION_DESCRIPTION',
                    'dismiss_label' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'target_action',
                    'type' => 'cj-target-action',
                    'dismiss_label' => true,
                    'span' => 12,
                ],
                [
                    'name' => 'team_name',
                ],
                [
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
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'hidden_panel',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL4',
            'label' => 'LBL_RECORDVIEW_PANEL4',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'populate_fields',
                    'type' => 'cj-populate-fields',
                    'label' => 'LBL_POPULATE_FIELDS',
                    'span' => 12,
                ],
            ],
        ],
        [
            'newTab' => false,
            'panelDefault' => 'expanded',
            'name' => 'LBL_RECORDVIEW_PANEL_EMAIL_FIELDS',
            'label' => 'LBL_RECORDVIEW_PANEL_EMAIL_FIELDS',
            'columns' => 2,
            'labelsOnTop' => 1,
            'placeholders' => 1,
            'fields' => [
                [
                    'name' => 'email_templates_name',
                ],
                [
                    'name' => 'select_to_email_address',
                    'type' => 'select-to-email-address',
                    'span' => 12,
                ],
            ],
        ],
    ],

    'dependencies' => [
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['main_trigger_type'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'smart_guide_template_name',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'parent_name',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'trigger_event',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'action_type',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'action_trigger_type',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'module_trigger',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'field_trigger',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
            ],
        ],
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['main_trigger_type', 'action_type', 'parent_type', 'action_type'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'smart_guide_template_name',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'display_activity_rsa_icon',
                        'value' => 'and(and(and(and(not(equal($action_type,"view_record")), and(not(equal($action_trigger_type,"automatic_create")),not(equal($action_trigger_type,"automatic_update")))),not(equal($parent_type,"DRI_Workflow_Templates"))),not(equal($parent_type,"DRI_SubWorkflow_Templates"))), equal($main_trigger_type, "smart_guide_to_sugar_action"))',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'trigger_event',
                        'value' => 'and(equal($main_trigger_type, "smart_guide_to_sugar_action"), not(equal($main_trigger_type, "")))',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'action_type',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'action_trigger_type',
                        'value' => 'and(equal($main_trigger_type, "smart_guide_to_sugar_action"), or(equal($action_type, "create_record"), equal($action_type, "update_record")))',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'relationship',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'ignore_errors',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'populate_fields',
                        'value' => 'and(equal($main_trigger_type, "smart_guide_to_sugar_action"), or(equal($action_type, "create_record"), equal($action_type, "update_record")))',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'select_to_email_address',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'parent_name',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'parent_type',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'email_templates_name',
                        'value' => 'equal($main_trigger_type, "smart_guide_to_sugar_action")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'module_trigger',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'target_action',
                        'value' => 'equal($main_trigger_type, "sugar_action_to_smart_guide")',
                    ],
                ],
            ],
        ],
    ],

    'templateMeta' => [
        'useTabs' => false,
    ],
];

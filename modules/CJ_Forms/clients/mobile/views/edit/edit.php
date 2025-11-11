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
$viewdefs['CJ_Forms']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                'name',
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
                    'name' => 'smart_guide_template_name',
                    'type' => 'cj-template-trigger',
                    'label' => 'LBL_SMART_GUIDE_TEMPLATE',
                    'placeholder' => 'LBL_SMART_GUIDE_TEMPLATE_PLACEHOLDER',
                    'no_required_placeholder' => true,
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
                'display_activity_rsa_icon',
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
                'team_name',
                'date_modified',
                'modified_by_name',
                'date_entered',
                'created_by_name',
                [
                    'name' => 'populate_fields',
                    'type' => 'cj-populate-fields',
                    'label' => 'LBL_POPULATE_FIELDS',
                    'span' => 12,
                ],
                'email_templates_name',
                [
                    'name' => 'select_to_email_address',
                    'type' => 'select-to-email-address',
                    'span' => 12,
                ],
            ],
        ],
    ],
];

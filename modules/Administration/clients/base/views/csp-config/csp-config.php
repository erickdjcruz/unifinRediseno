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
$viewdefs['Administration']['base']['view']['csp-config'] = [
    'template' => 'record',
    'label' => 'LBL_CSP_TITLE',
    'saveMessage' => 'LBL_CSP_SETTING_SAVED',
    'templateMeta' => [
        'useTabs' => true,
    ],
    'panels' => [
        [
            'name' => 'panel_1',
            'label' => 'LBL_CSP_BASIC',
            'columns' => 1,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'csp_default_src',
                    'type' => 'text',
                    'label' => 'LBL_CSP_TRUSTED_DOMAINS',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_frame_ancestors',
                    'type' => 'text',
                    'label' => 'LBL_CSP_TRUSTED_PARENT_DOMAINS',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
            ],
            'helpLabels' => [
                [
                    'text' => 'LBL_CSP_SETTING_HELP_TEXT_CONTENT',
                    'css_class' => 'unstyled',
                ],
            ],
        ],
        [
            'name' => 'panel_2',
            'label' => 'LBL_CSP_ADVANCED',
            'columns' => 1,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'csp_connect_src',
                    'type' => 'text',
                    'label' => 'connect-src',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_font_src',
                    'type' => 'text',
                    'label' => 'font-src',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_form_action',
                    'type' => 'text',
                    'label' => 'form-action',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_frame_src',
                    'type' => 'text',
                    'label' => 'frame-src',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_script_src',
                    'type' => 'text',
                    'label' => 'script-src',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
                [
                    'name' => 'csp_style_src',
                    'type' => 'text',
                    'label' => 'style-src',
                    'span' => 12,
                    'labelSpan' => 4,
                ],
            ],
            'helpLabels' => [
                [
                    'text' => 'LBL_CSP_SETTING_HELP_TEXT_CONTENT_ADVANCED',
                    'css_class' => 'unstyled',
                ],
            ],
        ],
    ],
];

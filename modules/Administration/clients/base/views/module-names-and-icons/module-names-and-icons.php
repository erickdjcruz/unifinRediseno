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
$viewdefs['Administration']['base']['view']['module-names-and-icons'] = [
    'label' => 'LBL_RENAME_TABS',
    'language_field' => [
        [
            'name' => 'language_selection',
            'type' => 'enum',
            'label' => 'LBL_LANGUAGE_SELECTOR',
            'options' => get_all_languages(),
            'template' => 'edit',

        ],
    ],
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'name' => 'save_button',
                    'label' => 'LBL_SAVE_BUTTON_LABEL',
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'module_name',
                    'label' => 'LBL_MODULE',
                    'template' => 'detail',
                ],
                [
                    'name' => 'module_singular',
                    'label' => 'LBL_SINGULAR_LABEL',
                    'type' => 'text',
                    'template' => 'edit',
                    'required' => true,
                ],
                [
                    'name' => 'module_plural',
                    'label' => 'LBL_PLURAL_LABEL',
                    'type' => 'text',
                    'template' => 'edit',
                    'required' => true,
                ],
                [
                    'name' => 'module_display',
                    'label' => 'LBL_MODULE_DISPLAY',
                    'type' => 'fieldset',
                    'template' => 'edit',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'module_display_type',
                            'label' => 'LBL_MODULE_DISPLAY_TYPE',
                            'type' => 'enum',
                            'template' => 'edit',
                            'options' => 'module_display_type_dom',
                            'required' => true,
                        ],
                        [
                            'name' => 'module_icon',
                            'label' => 'LBL_MODULE_ICON',
                            'type' => 'enum',
                            'options' => 'module_icons_dom',
                            'template' => 'edit',
                            'formatOptions' => 'icon',
                            'required' => true,
                        ],
                        [
                            'name' => 'module_abbreviation',
                            'label' => 'LBL_MODULE_ABBREVIATION',
                            'template' => 'edit',
                            'type' => 'text',
                            'len' => 2,
                            'required' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'module_color',
                    'label' => 'LBL_MODULE_COLOR',
                    'options' => 'module_colors_dom',
                    'type' => 'enum',
                    'template' => 'edit',
                    'formatOptions' => 'color',
                    'required' => true,
                ],
            ],
        ],
    ],
];

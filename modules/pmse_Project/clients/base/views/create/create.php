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

$viewdefs['pmse_Project']['base']['view']['create'] = [
    'template' => 'record',
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'name' => 'restore_button',
            'type' => 'button',
            'label' => 'LBL_RESTORE',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'select',
            'events' => [
                'click' => 'button:restore_button:click',
            ],
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'switch_on_click' => true,
            'showOn' => 'create',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'name' => 'save_open_design',
                    'label' => 'LBL_PMSE_SAVE_DESIGN_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:save_open_design:click',
                    ],
                ],
                [
                    'type' => 'rowaction',
                    'name' => 'save_button',
                    'label' => 'LBL_SAVE_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:save_button:click',
                    ],
                ],
            ],
        ],
        // @TODO: Remove this entire array, and reset the showOn property in the
        // above array to ['create', 'select'] once SC implements support for
        // array values in showOn, scheduled for SC-5667.
        [
            'type' => 'actiondropdown',
            'name' => 'dupecheck_dropdown',
            'primary' => true,
            'switch_on_click' => true,
            'showOn' => 'select',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'name' => 'save_open_design',
                    'label' => 'LBL_PMSE_SAVE_DESIGN_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:save_open_design:click',
                    ],
                ],
                [
                    'type' => 'rowaction',
                    'name' => 'save_button',
                    'label' => 'LBL_SAVE_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:save_button:click',
                    ],
                ],
            ],
        ],
        [
            'name' => 'duplicate_button',
            'type' => 'button',
            'label' => 'LBL_IGNORE_DUPLICATE_AND_SAVE',
            'primary' => true,
            'showOn' => 'duplicate',
            'events' => [
                'click' => 'button:save_button:click',
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];

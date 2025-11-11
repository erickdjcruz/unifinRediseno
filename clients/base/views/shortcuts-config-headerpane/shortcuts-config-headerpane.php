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

$viewdefs['base']['view']['shortcuts-config-headerpane'] = [
    'type' => 'list-headerpane',
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'LBL_SHORTCUT_CONFIG_HEADERPANE',
        ],
    ],
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'css_class' => 'btn-invisible btn-link',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'name' => 'main_dropdown',
            'type' => 'actiondropdown',
            'primary' => true,
            'buttons' => [
                [
                    'name' => 'save_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_SAVE_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:save_button:click',
                    ],
                ],
                [
                    'name' => 'restore_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_SHORTCUT_RESTORE',
                    'events' => [
                        'click' => 'button:restore_button:click',
                    ],
                ],
            ],
        ],
    ],
];

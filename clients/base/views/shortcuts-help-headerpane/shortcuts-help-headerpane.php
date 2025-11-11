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

$viewdefs['base']['view']['shortcuts-help-headerpane'] = [
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'LBL_KEYBOARD_SHORTCUTS_HELP_TITLE',
        ],
    ],
    'buttons' => [
        [
            'name' => 'configure_button',
            'type' => 'button',
            'label' => ' ',
            'icon' => 'sicon-settings',
            'events' => [
                'click' => 'button:configure_button:click',
            ],
        ],
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'primary' => true,
            'label' => 'LBL_CLOSE_BUTTON_LABEL',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
    ],
];

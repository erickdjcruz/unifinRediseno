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
$viewdefs['base']['view']['change-password'] = [
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'name' => 'confirm_button',
            'type' => 'button',
            'label' => 'LBL_CONFIRM',
            'css_class' => 'btn btn-primary',
            'events' => [
                'click' => 'button:confirm_button:click',
            ],
        ],
    ],
    'fields' => [
        [
            'name' => 'current_password',
            'type' => 'password',
            'placeholder' => 'LBL_CURRENT_PASSWORD',
            'css_class' => 'my-2',
            'required' => true,
        ],
        [
            'name' => 'new_password',
            'type' => 'password',
            'placeholder' => 'LBL_NEW_PASSWORD1',
            'css_class' => 'my-2',
            'required' => true,
        ],
        [
            'name' => 'new_password_confirm',
            'type' => 'password',
            'placeholder' => 'LBL_NEW_PASSWORD2',
            'css_class' => 'my-2',
            'required' => true,
        ],
        [
            'name' => 'name_field',
            'type' => 'text',
            'css_class' => 'hp',
            'placeholder' => 'LBL_HONEYPOT',
        ],
    ],
];

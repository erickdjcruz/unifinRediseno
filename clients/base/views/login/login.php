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

$viewdefs['base']['view']['login'] = [
    'action' => 'edit',
    'buttons' => [
        [
            'name' => 'login_button',
            'type' => 'button',
            'label' => 'LBL_LOGIN_BUTTON_LABEL',
            'primary' => true,
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'username',
                    'type' => 'username',
                    'placeholder' => 'LBL_LIST_USER_NAME', //LBL_LOGIN_USERNAME not translating properly across languages so using this for 6.x parity
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'placeholder' => 'LBL_PASSWORD',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
            ],
        ],
    ],
];

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

$viewdefs['portal']['view']['resetpassword'] = [
    'buttons' => [
        [
            'name' => 'reset_password_button',
            'type' => 'button',
            'label' => 'LBL_PORTAL_RESET_PASSWORD',
            'primary' => true,
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'password1',
                    'type' => 'password',
                    'placeholder' => 'LBL_PORTAL_LOGIN_PASSWORD',
                    'required' => true,
                    'no_required_placeholder' => true,
                ],
                [
                    'name' => 'password2',
                    'type' => 'password',
                    'placeholder' => 'LBL_PORTAL_REENTER_PASSWORD',
                    'required' => true,
                    'no_required_placeholder' => true,
                ],
            ],
        ],
    ],
];

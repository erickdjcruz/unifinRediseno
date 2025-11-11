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

$viewdefs['portal']['view']['signup'] = [
    'action' => 'list',
    'buttons' => [
        [
            'name' => 'signup_button',
            'type' => 'button',
            'label' => 'LBL_SIGNUP_BUTTON_LABEL',
            'primary' => true,
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'first_name',
                    'type' => 'varchar',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_FIRST_NAME',
                ],
                [
                    'name' => 'last_name',
                    'type' => 'varchar',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_LAST_NAME',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'company_name',
                    'type' => 'varchar',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_COMPANY_NAME',
                ],
                [
                    'name' => 'email',
                    'type' => 'email-text',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_EMAIL',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'user_name',
                    'type' => 'varchar',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_USER_NAME',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_PASSWORD',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'password1',
                    'type' => 'password',
                    'placeholder' => 'LBL_PORTAL_SIGNUP_PASSWORD1',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
            ],
        ],
    ],
];

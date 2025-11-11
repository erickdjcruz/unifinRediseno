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

$viewdefs['portal']['view']['login'] = [
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
                    'placeholder' => 'LBL_PORTAL_LOGIN_USERNAME',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'placeholder' => 'LBL_PORTAL_LOGIN_PASSWORD',
                    'no_required_placeholder' => true,
                    'required' => true,
                ],
                [
                    'name' => 'preferred_language',
                    'type' => 'language',
                    'options' => 'available_language_dom',
                    'required' => false,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];

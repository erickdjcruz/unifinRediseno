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

$viewdefs['base']['view']['consent-wizard-page'] = [
    'title' => 'LBL_WIZ_USER_COOKIE_CONSENT',
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-link btn-invisible',
        ],
        [
            'name' => 'continue_button',
            'type' => 'button',
            'label' => 'LBL_CONTINUE',
            'primary' => true,
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'columns' => 2,
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'cookie_consent',
                    'type' => 'bool',
                    'label' => 'LBL_USER_CLICK_FOR_COOKIE_CONSENT',
                    'required' => true,
                ],
            ],
        ],
    ],
];

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

$viewdefs['base']['view']['user-wizard-page'] = [
    'title' => 'LBL_WIZ_USER_PROFILE_TITLE',
    'message' => 'LBL_SETUP_USER_INFO',
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'first_name',
                    'label' => 'LBL_WIZ_FIRST_NAME',
                ],
                [
                    'name' => 'last_name',
                    'label' => 'LBL_WIZ_LAST_NAME',
                    'required' => true,
                ],
                [
                    'name' => 'email',
                    'type' => 'email-text',
                    'label' => 'LBL_WIZ_EMAIL',
                    'required' => true,
                    // disable this field only for user wizard view
                    // because email field is complex and has personal save/update server behavior
                    'idm_mode_disabled' => true,
                ],
                [
                    'name' => 'phone_work',
                    'type' => 'phone',
                    'label' => 'LBL_LIST_PHONE',
                ],
            ],
        ],
    ],
];

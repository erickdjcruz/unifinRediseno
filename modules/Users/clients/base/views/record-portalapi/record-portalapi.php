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
$viewdefs['Users']['base']['view']['record-portalapi'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'full_name',
                    'type' => 'fullname',
                    'fields' => ['first_name', 'last_name'],
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_USER_INFORMATION',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'fields' => [
                'status',
                [],
                'user_name',
                [
                    'name' => 'email',
                    'required' => false,
                ],
                [
                    'name' => 'portal_only',
                    'label' => 'LBL_USER_TYPE',
                    'type' => 'user-type',
                    'options' => 'user_type_portal_bool_dom',
                    'optionInfo' => [
                        true => 'LBL_PORTAL_ONLY_DESC',
                    ],
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_password',
            'label' => 'LBL_PASSWORD',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'portal_user_password',
                    'type' => 'portal-change-password',
                ],
            ],
        ],
    ],
];

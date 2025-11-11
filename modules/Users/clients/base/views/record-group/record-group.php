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
$viewdefs['Users']['base']['view']['record-group'] = [
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
                    'name' => 'is_group',
                    'label' => 'LBL_USER_TYPE',
                    'type' => 'user-type',
                    'options' => 'user_type_group_bool_dom',
                    'optionInfo' => [
                        true => 'LBL_GROUP_DESC',
                    ],
                    'readonly' => true,
                ],
            ],
        ],
    ],
];

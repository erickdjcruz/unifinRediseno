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
$viewdefs['Users']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_LIST_NAME',
                    'type' => 'fullname',
                    'fields' => [
                        'first_name',
                        'last_name',
                    ],
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                    'link' => true,
                ],
                [
                    'name' => 'user_name',
                    'sortable' => true,
                ],
                [
                    'name' => 'title',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'department',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'email',
                    'label' => 'LBL_EMAIL',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'phone_work',
                    'default' => true,
                    'enabled' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'status',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'license_type',
                    'type' => 'enum',
                    'readonly' => true,
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'is_admin',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
            ],
        ],
    ],
];

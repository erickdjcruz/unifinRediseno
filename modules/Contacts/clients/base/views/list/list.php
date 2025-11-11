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


$viewdefs['Contacts']['base']['view']['list'] = [
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_1',
                'fields' => [
                    [
                        'name' => 'name',
                        'type' => 'fullname',
                        'fields' => [
                            'salutation',
                            'first_name',
                            'last_name',
                        ],
                        'link' => true,
                        'label' => 'LBL_LIST_NAME',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'title',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'account_name',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'email',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'phone_mobile',
                        'enabled' => true,
                        'default' => true,
                        'selected' => false,
                    ],
                    [
                        'name' => 'phone_work',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'phone_other',
                        'enabled' => true,
                        'default' => true,
                        'selected' => false,
                    ],
                    [
                        'name' => 'assistant_phone',
                        'enabled' => true,
                        'default' => true,
                        'selected' => false,
                    ],
                    [
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_LIST_ASSIGNED_USER',
                        'id' => 'ASSIGNED_USER_ID',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'date_modified',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'date_entered',
                        'enabled' => true,
                        'default' => true,
                        'readonly' => true,
                    ],
                ],
            ],
    ],
];

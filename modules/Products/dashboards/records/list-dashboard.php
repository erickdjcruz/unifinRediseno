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

return [
    'metadata' => [
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'TPL_DASHLET_MY_MODULE',
                                'display_columns' => [
                                    'name',
                                    'billing_address_country',
                                    'billing_address_city',
                                ],
                            ],
                            'context' => [
                                'module' => 'Accounts',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'TPL_DASHLET_MY_MODULE',
                                'display_columns' => [
                                    'name',
                                    'account_name',
                                    'email',
                                    'phone_work',
                                ],
                            ],
                            'context' => [
                                'module' => 'Contacts',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_QUOTED_LINE_ITEMS_LIST_DASHBOARD',
    'id' => '5d6736ec-7b52-11e9-a00e-f218983a1c3e',
];

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
    'name' => 'LBL_QUOTES_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f718c-13cb-11eb-87ec-acde48001122',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    // Row 1
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Quotes',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Quotes',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Quotes',
                            ],
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Quotes',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'billing_accounts',
                                        'module' => 'Accounts',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'opportunities',
                                        'module' => 'Opportunities',
                                    ],
                                ],
                                'tab_list' => [
                                    'billing_accounts',
                                    'opportunities',
                                ],
                                'base_module' => 'Quotes',
                            ],
                            'context' => [
                                'module' => 'Quotes',
                            ],
                            'width' => 6,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'view' => [
                                'type' => 'purchase-history',
                                'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                                'linked_account_field' => 'billing_account_id',
                            ],
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'active-subscriptions',
                                'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                                'linked_subscriptions_account_field' => 'billing_account_id',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

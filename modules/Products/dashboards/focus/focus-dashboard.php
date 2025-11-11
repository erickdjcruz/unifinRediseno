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
    'name' => 'LBL_PRODUCTS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f6a84-13cb-11eb-b1ec-acde48001122',
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
                                'module' => 'Products',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Products',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Products',
                            ],
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'purchase-history',
                                'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                                'linked_account_field' => 'account_id',
                            ],
                            'width' => 6,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Products',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'account_link',
                                        'module' => 'Accounts',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'quotes',
                                        'module' => 'Quotes',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'opportunities',
                                        'module' => 'Opportunities',
                                    ],
                                ],
                                'tab_list' => [
                                    'account_link',
                                    'quotes',
                                    'opportunities',
                                ],
                                'base_module' => 'Products',
                            ],
                            'context' => [
                                'module' => 'Products',
                            ],
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'active-subscriptions',
                                'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                                'linked_subscriptions_account_field' => 'account_id',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

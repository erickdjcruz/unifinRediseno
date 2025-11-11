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
    'name' => 'LBL_REVENUE_LINE_ITEMS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f7ca4-13cb-11eb-9a0c-acde48001122',
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
                                'module' => 'RevenueLineItems',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'RevenueLineItems',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'RevenueLineItems',
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
                                'module' => 'RevenueLineItems',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'opportunities',
                                        'module' => 'Opportunities',
                                    ],
                                    [
                                        'active' => false,
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
                                ],
                                'tab_list' => [
                                    'opportunities',
                                    'account_link',
                                    'quotes',
                                ],
                            ],
                            'context' => [
                                'module' => 'RevenueLineItems',
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

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
        'dashlets' => [
            [
                'view' => [
                    'type' => 'product-catalog-dashlet',
                    'label' => 'LBL_PRODUCT_CATALOG_DASHLET_NAME',
                ],
                'context' => [
                    'module' => 'ProductTemplates',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'product-quick-picks-dashlet',
                    'label' => 'LBL_PRODUCT_QUICK_PICKS_DASHLET_NAME',
                ],
                'context' => [
                    'module' => 'ProductTemplates',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            [
                'view' => [
                    'type' => 'dashablerecord',
                    'label' => 'LBL_RELATED_RECORDS',
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
                'width' => 12,
                'x' => 0,
                'y' => 12,
            ],
            [
                'view' => [
                    'module' => 'Quotes',
                    'type' => 'activity-timeline',
                    'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                ],
                'context' => [
                    'module' => 'Quotes',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 18,
            ],
            [
                'view' => [
                    'type' => 'purchase-history',
                    'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                    'linked_account_field' => 'billing_account_id',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 24,
            ],
        ],
    ],
    'name' => 'LBL_QUOTES_RECORD_DASHBOARD',
    'id' => '5d671fae-7b52-11e9-92e0-f218983a1c3e',
];

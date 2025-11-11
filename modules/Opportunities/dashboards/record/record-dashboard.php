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
    'id' => '5d671a22-7b52-11e9-b2bc-f218983a1c3e',
    'name' => 'LBL_OPPORTUNITIES_RECORD_DASHBOARD',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'product-catalog-dashlet',
                                'label' => 'LBL_PRODUCT_CATALOG_DASHLET_NAME',
                            ],
                            'context' => [
                                'module' => 'ProductTemplates',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'product-quick-picks-dashlet',
                                'label' => 'LBL_PRODUCT_QUICK_PICKS_DASHLET_NAME',
                            ],
                            'context' => [
                                'module' => 'ProductTemplates',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Opportunities',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'accounts',
                                        'module' => 'Accounts',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'quotes',
                                        'module' => 'Quotes',
                                        'fields' => [
                                            'quote_num',
                                            'name',
                                            'billing_account_name',
                                            'quote_stage',
                                            'total',
                                            'total_usdollar',
                                            'date_quote_expected_closed',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'limit' => 5,
                                    ],
                                ],
                                'tab_list' => [
                                    'accounts',
                                ],
                            ],
                            'context' => [
                                'module' => 'Opportunities',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'module' => 'Opportunities',
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Opportunities',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'purchase-history',
                                'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                                'linked_account_field' => 'account_id',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

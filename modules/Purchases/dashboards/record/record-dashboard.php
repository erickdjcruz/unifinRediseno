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
                    'type' => 'activity-timeline',
                    'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                ],
                'context' => [
                    'module' => 'Purchases',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'dashablerecord',
                    'module' => 'Purchases',
                    'label' => 'LBL_RELATED_ACCOUNT',
                    'tabs' => [
                        [
                            'module' => 'Accounts',
                            'link' => 'accounts',
                        ],
                    ],
                    'tab_list' => [
                        'accounts',
                    ],
                ],
                'context' => [
                    'module' => 'Purchases',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'purchase-history',
                    'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                    'linked_account_field' => 'account_id',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            [
                'view' => [
                    'type' => 'active-subscriptions',
                    'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                    'linked_subscriptions_account_field' => 'account_id',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 12,
            ],
        ],
    ],
    'name' => 'LBL_PURCHASES_RECORD_DASHBOARD',
];

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
                'width' => 12,
                'rows' => [
                    [
                        [
                            'view' => [
                                'module' => 'Accounts',
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
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
                                'type' => 'purchase-history',
                                'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                                'linked_account_field' => 'id',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'active-subscriptions',
                                'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
                                'linked_subscriptions_account_field' => 'id',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'opportunity-metrics',
                                'label' => 'LBL_DASHLET_OPPORTUNITY_NAME',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'casessummary',
                                'label' => 'LBL_DASHLET_CASES_SUMMARY_NAME',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_ACCOUNTS_RECORD_DASHBOARD',
];

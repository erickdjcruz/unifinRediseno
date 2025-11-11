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
$viewdefs['Opportunities']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_LIST_OPPORTUNITY_NAME',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'account_name',
                    'link' => true,
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'sales_status',
                    'readonly' => true,
                ],
                [
                    'name' => 'sales_stage',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_closed',
                    'label' => 'LBL_DATE_CLOSED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'related_fields' => [
                        'date_closed_timestamp',
                    ],
                ],
                [
                    'name' => 'probability',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'amount',
                    'type' => 'currency',
                    'label' => 'LBL_LIKELY',
                    'related_fields' => [
                        'amount',
                        'currency_id',
                        'base_rate',
                    ],
                    'readonly' => true,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'opportunity_type',
                    'label' => 'LBL_TYPE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'lead_source',
                    'label' => 'LBL_LEAD_SOURCE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'next_step',
                    'label' => 'LBL_NEXT_STEP',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'team_name',
                    'type' => 'teamset',
                    'label' => 'LBL_LIST_TEAM',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_USER',
                    'id' => 'ASSIGNED_USER_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'forecasted_likely',
                    'comment' => 'Rollup of included RLIs on the Opportunity',
                    'readonly' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'label' => 'LBL_FORECASTED_LIKELY',
                    'span' => 6,
                ],
                [
                    'name' => 'commit_stage',
                    'type' => 'enum-cascade',
                    'disable_field' => 'closed_won_revenue_line_items',
                    'disable_positive' => true,
                    'related_fields' => [
                        0 => 'probability',
                        1 => 'closed_won_revenue_line_items',
                    ],
                    'span' => 6,
                ],
                [
                    'name' => 'lost',
                    'label' => 'LBL_LOST',
                    'enabled' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'readonly' => true,
                    'currency_format' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];

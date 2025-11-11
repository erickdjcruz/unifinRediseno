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

$viewdefs['Opportunities']['base']['view']['list'] = [
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
                        'included_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'account_name',
                    'link' => true,
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_closed',
                    'type' => 'date-cascade',
                    'label' => 'LBL_DATE_CLOSED',
                    'enabled' => true,
                    'default' => true,
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'sales_status',
                    'label' => 'LBL_SALES_STATUS',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'enum',
                ],
                [
                    'name' => 'sales_stage',
                    'type' => 'enum-cascade',
                    'label' => 'LBL_LIST_SALES_STAGE',
                    'enabled' => true,
                    'default' => true,
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'service_start_date',
                    'type' => 'date-cascade',
                    'label' => 'LBL_SERVICE_START_DATE',
                    'disable_field' => 'service_open_revenue_line_items',
                    'related_fields' => [
                        'service_open_revenue_line_items',
                    ],
                    'default' => false,
                ],
                [
                    'name' => 'service_duration',
                    'type' => 'fieldset-cascade',
                    'label' => 'LBL_SERVICE_DURATION',
                    'inline' => true,
                    'show_child_labels' => false,
                    'css_class' => 'service-duration-field',
                    'fields' => [
                        [
                            'name' => 'service_duration_value',
                            'label' => 'LBL_SERVICE_DURATION_VALUE',
                        ],
                        [
                            'name' => 'service_duration_unit',
                            'label' => 'LBL_SERVICE_DURATION_UNIT',
                        ],
                    ],
                    'orderBy' => 'service_duration_unit',
                    'related_fields' => [
                        'service_duration_value',
                        'service_duration_unit',
                        'service_open_flex_duration_rlis',
                    ],
                    'disable_field' => 'service_open_flex_duration_rlis',
                    'default' => false,
                ],
                [
                    'name' => 'is_escalated',
                    'label' => 'LBL_ESCALATED',
                    'badge_label' => 'LBL_ESCALATED',
                    'warning_level' => 'important',
                    'type' => 'badge',
                    'enabled' => true,
                    'default' => false,
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
                    'comment' => 'Rollup of lost RLIs on the Opportunity',
                    'readonly' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'label' => 'LBL_LOST',
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
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'opportunity_type',
                    'label' => 'LBL_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'lead_source',
                    'label' => 'LBL_LEAD_SOURCE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'next_step',
                    'label' => 'LBL_NEXT_STEP',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'probability',
                    'label' => 'LBL_PROBABILITY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'sortable' => true,
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
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];

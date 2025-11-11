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

$viewdefs['Opportunities']['base']['view']['preview'] = [
    'templateMeta' => [
        'maxColumns' => 1,
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'related_fields' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                        'included_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'is_escalated',
                    'type' => 'badge',
                    'badge_label' => 'LBL_ESCALATED',
                    'warning_level' => 'important',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'renewal',
                    'type' => 'renewal',
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'account_name',
                    'related_fields' => [
                        'account_id',
                    ],
                ],
                [
                    'name' => 'date_closed',
                    'type' => 'date-cascade',
                    'label' => 'LBL_LIST_DATE_CLOSED',
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                    'related_fields' => [
                        'date_closed_timestamp',
                    ],
                ],
                [
                    'name' => 'service_start_date',
                    'type' => 'date-cascade',
                    'label' => 'LBL_SERVICE_START_DATE',
                    'disable_field' => 'service_open_revenue_line_items',
                    'related_fields' => [
                        0 => 'service_open_revenue_line_items',
                    ],
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
                    'related_fields' => [
                        'service_duration_value',
                        'service_duration_unit',
                        'service_open_flex_duration_rlis',
                    ],
                    'disable_field' => 'service_open_flex_duration_rlis',
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
                    'span' => 6,
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                ],
                [
                    'name' => 'commit_stage',
                ],
                [
                    'name' => 'tag',
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
                    'label' => 'LBL_SALES_STAGE',
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
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
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'placeholders' => true,
            'columns' => 2,
            'fields' => [
                'next_step',
                'opportunity_type',
                'renewal_parent_name',
                'lead_source',
                'campaign_name',
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

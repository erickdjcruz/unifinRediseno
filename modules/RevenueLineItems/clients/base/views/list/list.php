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


// ENT/ULT only fields
$fields = [
    [
        'name' => 'name',
        'link' => true,
        'label' => 'LBL_LIST_NAME',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'opportunity_name',
        'enabled' => true,
        'default' => true,
        'sortable' => true,
    ],
    [
        'name' => 'account_name',
        'readonly' => true,
        'enabled' => true,
        'default' => true,
        'sortable' => true,
    ],
    [
        'name' => 'sales_stage',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'probability',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'date_closed',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'commit_stage',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'product_template_name',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'category_name',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'quantity',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'worst_case',
        'type' => 'currency',
        'related_fields' => [
            'currency_id',
            'base_rate',
            'total_amount',
            'quantity',
            'discount_amount',
            'discount_price',
        ],
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'likely_case',
        'type' => 'currency',
        'related_fields' => [
            'currency_id',
            'base_rate',
            'total_amount',
            'quantity',
            'discount_amount',
            'discount_price',
        ],
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'best_case',
        'type' => 'currency',
        'related_fields' => [
            'currency_id',
            'base_rate',
            'total_amount',
            'quantity',
            'discount_amount',
            'discount_price',
        ],
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'assigned_user_name',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'date_modified',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'date_entered',
        'enabled' => true,
        'default' => true,
    ],
    'service' => [
        'name' => 'service',
        'label' => 'LBL_SERVICE',
    ],
    'service_start_date' => [
        'name' => 'service_start_date',
        'label' => 'LBL_SERVICE_START_DATE',
        'type' => 'date',
    ],
    'service_end_date' => [
        'name' => 'service_end_date',
        'label' => 'LBL_SERVICE_END_DATE',
        'type' => 'service-enddate',
        'default' => false,
    ],
    'add_on_to_name' => [
        'name' => 'add_on_to_name',
        'type' => 'add-on-to',
        'default' => false,
    ],
    [
        'name' => 'service_duration',
        'type' => 'fieldset',
        'css_class' => 'service-duration-field',
        'label' => 'LBL_SERVICE_DURATION',
        'inline' => true,
        'default' => false,
        'show_child_labels' => false,
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
        ],
    ],
    'renewable' => [
        'name' => 'renewable',
        'label' => 'LBL_RENEWABLE',
        'type' => 'bool',
    ],
];

$viewdefs['RevenueLineItems']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => $fields,
        ],
    ],
];

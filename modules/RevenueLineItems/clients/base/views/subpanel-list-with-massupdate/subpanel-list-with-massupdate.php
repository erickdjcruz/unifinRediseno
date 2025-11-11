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
        'sortable' => false,
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'account_name',
        'readonly' => true,
        'sortable' => false,
        'enabled' => true,
        'default' => true,
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
        'sortable' => false,
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'category_name',
        'sortable' => false,
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
        'showTransactionalAmount' => true,
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
        'showTransactionalAmount' => true,
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
        'showTransactionalAmount' => true,
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'quote_name',
        'label' => 'LBL_ASSOCIATED_QUOTE',
        'related_fields' => ['quote_id'],
        // this is a hack to get the quote_id field loaded
        'readonly' => true,
        'bwcLink' => true,
        'enabled' => true,
        'default' => true,
    ],
    [
        'name' => 'assigned_user_name',
        'sortable' => false,
        'enabled' => true,
        'default' => true,
    ],
];

$viewdefs['RevenueLineItems']['base']['view']['subpanel-list-with-massupdate'] = [
    'type' => 'subpanel-list',
    'favorite' => true,
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => $fields,
        ],
    ],
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'massdelete_button',
                'type' => 'button',
                'label' => 'LBL_DELETE',
                'acl_action' => 'delete',
                'primary' => true,
                'events' => [
                    'click' => 'list:massdelete:fire',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'icon' => 'sicon-edit',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
        ],
    ],
];

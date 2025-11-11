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

$popupMeta = [
    'moduleMain' => 'PurchasedLineItems',
    'varName' => 'PLI',
    'orderBy' => 'name',
    'whereClauses' => [
        'name' => 'purchased_line_items.name',
    ],
    'searchInputs' => [
        'name',
    ],
    'listviewdefs' => [
        'NAME' => [
            'width' => '25',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true,
        ],
        'PURCHASE_NAME' => [
            'width' => '20',
            'label' => 'LBL_PURCHASE',
            'id' => 'PURCHASE_ID',
            'module' => 'Purchases',
            'link' => true,
            'default' => true,
        ],
        'DATE_CLOSED' => [
            'width' => '10',
            'label' => 'LBL_DATE_CLOSED',
            'default' => true,
        ],
        'PRODUCT_TEMPLATE_NAME' => [
            'width' => '20',
            'label' => 'LBL_PRODUCT_TEMPLATE',
            'id' => 'PRODUCT_TEMPLATE_ID',
            'module' => 'ProductTemplates',
            'link' => true,
            'default' => true,
        ],
        'REVENUE' => [
            'width' => '10',
            'label' => 'LBL_REVENUE',
            'default' => true,
        ],
        'TOTAL_AMOUNT' => [
            'width' => '10',
            'label' => 'LBL_CALCULATED_LINE_ITEM_AMOUNT',
            'default' => true,
        ],
        'QUANTITY' => [
            'width' => '10',
            'label' => 'LBL_QUANTITY',
            'default' => true,
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '10',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'link' => true,
            'default' => false,
        ],
        'DATE_ENTERED' => [
            'width' => '10',
            'default' => true,
            'label' => 'LBL_DATE_ENTERED',
        ],
        'START_DATE' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_START_DATE',
        ],
        'END_DATE' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_END_DATE',
        ],
        'SERVICE' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_SERVICE',
        ],
        'RENEWABLE' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_RENEWABLE',
        ],
        'TEAM_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_TEAM_NAME',
            'id' => 'TEAM_ID',
            'module' => 'Teams',
            'link' => true,
            'default' => false,
        ],
        'DATE_MODIFIED' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_DATE_MODIFIED',
        ],
    ],
    'searchdefs' => [
        'name',
    ],
];

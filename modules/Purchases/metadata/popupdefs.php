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
    'moduleMain' => 'Purchase',
    'varName' => 'PURCHASE',
    'orderBy' => 'name',
    'whereClauses' => [
        'name' => 'purchases.name',
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
        'ACCOUNT_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'id' => 'ACCOUNT_ID',
            'module' => 'Accounts',
            'link' => true,
            'default' => true,
        ],
        'PRODUCT_TEMPLATE_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_PRODUCT_TEMPLATE_NAME',
            'id' => 'PRODUCT_TEMPLATE_ID',
            'module' => 'ProductTemplates',
            'link' => true,
            'default' => true,
        ],
        'START_DATE' => [
            'width' => '10',
            'default' => true,
            'label' => 'LBL_START_DATE',
        ],
        'END_DATE' => [
            'width' => '10',
            'default' => true,
            'label' => 'LBL_END_DATE',
        ],
        'SERVICE' => [
            'width' => '10',
            'default' => true,
            'label' => 'LBL_SERVICE',
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '10',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'link' => false,
            'default' => false,
        ],
        'RENEWABLE' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_RENEWABLE',
        ],
        'CATEGORY_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_CATEGORY_NAME',
            'id' => 'CATEGORY_ID',
            'module' => 'ProductCategories',
            'link' => true,
            'default' => false,
        ],
        'TYPE_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_TYPE_NAME',
            'id' => 'TYPE_ID',
            'module' => 'ProductTypes',
            'link' => true,
            'default' => false,
        ],
        'TEAM_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_TEAM_NAME',
            'id' => 'TEAM_ID',
            'module' => 'Teams',
            'link' => true,
            'default' => false,
        ],
        'DATE_ENTERED' => [
            'width' => '10',
            'default' => false,
            'label' => 'LBL_DATE_ENTERED',
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

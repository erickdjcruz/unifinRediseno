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
$dictionary['ProductCategory'] = [
    'favorites' => false,
    'table' => 'product_categories',
    'archive' => false,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'comment' => 'Used to categorize products in the product catalog',
    'fields' => [
        'list_order' => [
            'name' => 'list_order',
            'vname' => 'LBL_LIST_ORDER',
            'type' => 'int',
            'validation' => ['type' => 'range', 'min' => 0],
            'len' => '4',
            'comment' => 'Order within list',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_NAME',
            'type' => 'id',
            'comment' => 'Parent category of this item; used for multi-tiered categorization',
            'reportable' => false,
        ],
        'parent_category' => [
            'name' => 'parent_category',
            'type' => 'link',
            'relationship' => 'member_categories',
            'module' => 'ProductCategories',
            'bean_name' => 'ProductCategory',
            'source' => 'non-db',
            'vname' => 'LBL_PARENT_CATEGORY',
            'side' => 'right',
            'link_type' => 'one',
        ],
        'categories' => [
            'name' => 'categories',
            'type' => 'link',
            'relationship' => 'member_categories',
            'module' => 'ProductCategories',
            'bean_name' => 'ProductCategory',
            'source' => 'non-db',
            'vname' => 'LBL_CATEGORIES',
        ],
        'parent_name' => [
            'name' => 'parent_name',
            'rname' => 'name',
            'id_name' => 'parent_id',
            'vname' => 'LBL_PARENT_CATEGORY',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'ProductCategories',
            'table' => 'product_categories',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'parent_category',
            'unified_search' => true,
            'importable' => 'true',
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'source' => 'non-db',
        ],
        'forecastworksheet' => [
            'name' => 'forecastworksheet',
            'type' => 'link',
            'relationship' => 'forecastworksheets_categories',
            'source' => 'non-db',
            'vname' => 'LBL_FORECAST_WORKSHEET',
        ],
    ],
    'acls' => ['SugarACLProduct' => true],
    'indices' => [
        ['name' => 'idx_producttemplate_id_parent_name', 'type' => 'index', 'fields' => ['id', 'parent_id', 'name', 'deleted']],
        [
            'name' => 'idx_id_name_list_order',
            'type' => 'index',
            'fields' => [
                'id',
                'name',
                'list_order',
            ],
        ],
    ],
    'relationships' => [
        'member_categories' => [
            'lhs_module' => 'ProductCategories',
            'lhs_table' => 'product_categories',
            'lhs_key' => 'id',
            'rhs_module' => 'ProductCategories',
            'rhs_table' => 'product_categories',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'uses' => [
        'default',
        'assignable',
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
];

VardefManager::createVardef(
    'ProductCategories',
    'ProductCategory'
);

//boost value for full text search
$dictionary['ProductCategory']['fields']['name']['full_text_search']['boost'] = 0.79;
$dictionary['ProductCategory']['fields']['description']['full_text_search']['boost'] = 0.38;

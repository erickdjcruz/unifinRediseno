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
$dictionary['product_bundle_product'] = [
    'table' => 'product_bundle_product',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
        'bundle_id' => [
            'name' => 'bundle_id',
            'type' => 'id',
        ],
        'product_id' => [
            'name' => 'product_id',
            'type' => 'id',
        ],
        'product_index' => [
            'name' => 'product_index',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'prod_bundl_prodpk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_pbp_bundle',
            'type' => 'index',
            'fields' => [
                'bundle_id',
            ],
        ],
        [
            'name' => 'idx_pbp_bq',
            'type' => 'alternate_key',
            'fields' => [
                'product_id',
                'bundle_id',
            ],
        ],
    ],
    'relationships' => [
        'product_bundle_product' => [
            'lhs_module' => 'ProductBundles',
            'lhs_table' => 'product_bundles',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'product_bundle_product',
            'join_key_lhs' => 'bundle_id',
            'join_key_rhs' => 'product_id',
            'true_relationship_type' => 'one-to-many',
        ],
    ],
];

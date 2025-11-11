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
$dictionary['TaxRate'] = [
    'table' => 'taxrates',
    'archive' => false,
    'favorites' => false,
    'fields' => [
        'value' => [
            'name' => 'value',
            'vname' => 'LBL_VALUE',
            'type' => 'decimal',
            'dbType' => 'decimal2',
            'len' => '26,6',
            'importable' => 'required',
            'required' => true,
            'massupdate' => true,
        ],
        'list_order' => [
            'name' => 'list_order',
            'vname' => 'LBL_LIST_ORDER',
            'type' => 'int',
            'len' => '4',
            'importable' => 'required',
            'required' => true,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'dbType' => 'varchar',
            'len' => 100,
            'options' => 'taxrate_status_dom',
            'importable' => 'required',
            'required' => true,
        ],
        'product_bundles' => [
            'name' => 'product_bundles',
            'type' => 'link',
            'relationship' => 'product_bundle_taxrate',
            'module' => 'ProductBundles',
            'bean_name' => 'ProductBundle',
            'source' => 'non-db',
        ],
        'quotes' => [
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'taxrate_quotes',
            'vname' => 'LBL_TAXRATE',
            'source' => 'non-db',
        ],
    ],
    'relationships' => [
        'taxrate_quotes' => [
            'lhs_module' => 'TaxRates',
            'lhs_table' => 'taxrates',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'taxrate_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => ['aclModule' => 'Quotes', 'allowUserRead' => true]],
];

VardefManager::createVardef(
    'TaxRates',
    'TaxRate',
    [
        'default',
    ]
);

$dictionary['TaxRate']['fields']['tag']['massupdate'] = false;

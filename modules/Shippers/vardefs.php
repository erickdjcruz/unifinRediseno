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
$dictionary['Shipper'] = [
    'table' => 'shippers',
    'archive' => false,
    'favorites' => false,
    'fields' => [
        'list_order' => [
            'name' => 'list_order',
            'vname' => 'LBL_LIST_ORDER',
            'type' => 'int',
            'len' => '4',
            'importable' => 'required',
        ],
        'default_cost' => [
            'name' => 'default_cost',
            'vname' => 'LBL_DEFAULT_COST',
            'type' => 'currency',
            'len' => '26,6',
            'audited' => true,
            'comment' => 'Default cost (Shown)',
        ],
        'default_cost_usdollar' => [
            'name' => 'default_cost_usdollar',
            'vname' => 'LBL_DEFAULT_COST_USDOLLAR',
            'type' => 'decimal',
            'len' => '26,6',
            'studio' => [
                'editview' => false,
                'mobile' => false,
            ],
            'readonly' => true,
            'is_base_currency' => true,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'shipper_status_dom',
            'dbType' => 'varchar',
            'len' => 100,
            'importable' => 'required',
        ],
        'quotes' => [
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'shipper_quotes',
            'vname' => 'LBL_QUOTES',
            'source' => 'non-db',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => ['aclModule' => 'Products', 'allowUserRead' => true]],
    'relationships' => [
        'shipper_quotes' => [
            'lhs_module' => 'Shippers',
            'lhs_table' => 'shippers',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes', 'rhs_table' => 'quotes', 'rhs_key' => 'shipper_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'uses' => [
        'basic',
    ],
];

VardefManager::createVardef(
    'Shippers',
    'Shipper'
);

$dictionary['Shipper']['fields']['tag']['massupdate'] = false;

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

$dictionary['Category'] = [
    'comment' => 'Category module',
    'table' => 'categories',
    'audited' => false,
    'activity_enabled' => false,
    'favorites' => false,
    'optimistic_locking' => false,
    'unified_search' => true,
    'full_text_search' => false,
    'unified_search_default_enabled' => true,
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'root' => [
            'name' => 'root',
            'type' => 'id',
            'comment' => 'Root ID',
            'isnull' => true,
            'required' => false,
        ],
        'lft' => [
            'name' => 'lft',
            'type' => 'int',
            'comment' => 'Left node index',
            'isnull' => false,
            'required' => true,
        ],
        'rgt' => [
            'name' => 'rgt',
            'type' => 'int',
            'comment' => 'Right node index',
            'isnull' => false,
            'required' => true,
        ],
        'lvl' => [
            'name' => 'lvl',
            'type' => 'int',
            'comment' => 'Node level',
            'isnull' => false,
            'required' => true,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '255',
            'comment' => 'Category name',
            'required' => true,
        ],
        'is_external' => [
            'name' => 'is_external',
            'vname' => 'LBL_IS_EXTERNAL',
            'type' => 'bool',
            'isnull' => 'true',
            'comment' => 'External category flag',
            'default' => 0,
            'duplicate_on_record_copy' => 'no',
        ],
    ],
    'relationships' => [],
    'indices' => [],
    'duplicate_check' => [
        'enabled' => false,
    ],
    'uses' => [
        'basic',
        'external_source',
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
    'portal_visibility' => [
        'class' => 'Categories',
    ],
];

VardefManager::createVardef(
    'Categories',
    'Category'
);

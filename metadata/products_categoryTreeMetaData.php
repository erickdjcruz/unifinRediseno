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

$dictionary['products_category_tree'] = [
    'table' => 'category_tree',
    'fields' => [
        'self_id' => [
            'name' => 'self_id',
            'type' => 'id',
        ],
        'node_id' => [
            'name' => 'node_id',
            'type' => 'int',
            'auto_increment' => true,
            'required' => true,
            'isnull' => false,
        ],
        'parent_node_id' => [
            'name' => 'parent_node_id',
            'type' => 'int',
            'default' => '0',
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'len' => '36',
        ],
    ],
    'indices' => [
        [
            'name' => 'categorytreepk',
            'type' => 'primary',
            'fields' => [
                'node_id',
            ],
        ],
        [
            'name' => 'idx_categorytree',
            'type' => 'index',
            'fields' => [
                'self_id',
            ],
        ],
    ],
];

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

$dictionary['forecast_tree'] = [
    'table' => 'forecast_tree',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'len' => 50,
            'required' => true,
        ],
        'hierarchy_type' => [
            'name' => 'hierarchy_type',
            'type' => 'varchar',
            'len' => 25,
            'required' => true,
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
            'default' => null,
            'required' => false,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'default' => null,
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'forecast_tree_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'forecast_tree_idx_user_id',
            'type' => 'index',
            'fields' => [
                'user_id',
            ],
        ],
    ],
];

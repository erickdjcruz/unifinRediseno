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

$dictionary['roles_modules'] = [
    'table' => 'roles_modules',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'role_id' => [
            'name' => 'role_id',
            'type' => 'id',
        ],
        'module_id' => [
            'name' => 'module_id',
            'type' => 'id',
        ],
        'allow' => [
            'name' => 'allow',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
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
        ],
    ],
    'indices' => [
        [
            'name' => 'roles_modulespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_module_id',
            'type' => 'index',
            'fields' => [
                'module_id',
            ],
        ],
    ],
];

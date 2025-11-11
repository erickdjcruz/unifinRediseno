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
$dictionary['audit_events'] = [
    'table' => 'audit_events',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'type' => [
            'name' => 'type',
            'type' => 'char',
            'len' => 10,
            'required' => true,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
        ],
        'module_name' => [
            'name' => 'module_name',
            'type' => 'varchar',
            'len' => 100,
            'required' => true,
        ],
        'source' => [
            'name' => 'source',
            'type' => 'json',
            'dbType' => 'text',
            'required' => false,
        ],
        'impersonated_by' => [
            'name' => 'impersonated_by',
            'type' => 'id',
            'required' => false,
        ],
        'date_created' => [
            'name' => 'date_created',
            'type' => 'datetime',
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_aud_eve_id',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_aud_eve_ptd',
            'type' => 'index',
            'fields' => [
                'parent_id',
                'type',
                'date_created',
            ],
        ],
        [
            'name' => 'idx_modulename',
            'type' => 'index',
            'fields' => [
                'module_name',
            ],
        ],
    ],
];

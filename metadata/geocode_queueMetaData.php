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

$dictionary['geocode_queue'] = [
    'table' => 'geocode_queue',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'type' => 'id',
            'comment' => 'FK to various beans\'s tables',
        ],
        'geocode_id' => [
            'name' => 'geocode_id',
            'type' => 'id',
            'comment' => 'geocode module id',
        ],
        'bean_module' => [
            'name' => 'bean_module',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'bean\'s Module',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'date_created' => [
            'name' => 'date_created',
            'type' => 'datetime',
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_geocode_queue_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_geocode_queue_bean_id',
            'type' => 'index',
            'fields' => [
                'bean_id',
            ],
        ],
        [
            'name' => 'idx_beans_bean_id',
            'type' => 'index',
            'fields' => [
                'bean_module',
                'bean_id',
            ],
        ],
        [
            'name' => 'idx_geocode_id',
            'type' => 'index',
            'fields' => [
                'geocode_id',
            ],
        ],
    ],
    'relationships' => [
    ],
];

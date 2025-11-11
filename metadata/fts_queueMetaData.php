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

$dictionary['fts_queue'] = [
    'table' => 'fts_queue',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'dbType' => 'id',
            'type' => 'varchar',
            'len' => '36',
            'comment' => 'FK to various beans\'s tables',
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
        'processed' => [
            'name' => 'processed',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_fts_queue_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_beanid',
            'type' => 'index',
            'fields' => [
                'bean_id',
            ],
        ],
        [
            'name' => 'idx_beanmodule_processed',
            'type' => 'index',
            'fields' => [
                'bean_module',
                'processed',
            ],
        ],
    ],
    'relationships' => [
    ],
];

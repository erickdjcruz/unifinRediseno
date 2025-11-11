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

$dictionary['metadata_cache'] = [
    'table' => 'metadata_cache',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'len' => '255',
        ],
        'data' => [
            'name' => 'data',
            'type' => 'longblob',
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
            'name' => 'matadata_cache_primary',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_type_datemodified',
            'type' => 'index',
            'fields' => [
                'type',
                'date_modified',
            ],
        ],
    ],
];

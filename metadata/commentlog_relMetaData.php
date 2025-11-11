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

$dictionary['commentlog_rel'] = [
    'table' => 'commentlog_rel',
    'relationships' => [],
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'record_id' => [
            'name' => 'record_id',
            'type' => 'id',
            'required' => true,
        ],
        'commentlog_id' => [
            'name' => 'commentlog_id',
            'type' => 'id',
            'required' => true,
        ],
        'module' => [
            'name' => 'module',
            'type' => 'varchar',
            'len' => 100,
            'required' => false,
            'readonly' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'commentlog_relpk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'commentlog_record_relpk',
            'type' => 'index',
            'fields' => ['record_id'],
        ],
        [
            'name' => 'commentlog_commentlog_relpk',
            'type' => 'index',
            'fields' => ['commentlog_id'],
        ],
    ],
];

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

$dictionary['session_history'] = [
    'table' => 'session_history',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'session_id' => [
            'name' => 'session_id',
            'type' => 'varchar',
            'len' => '100',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'type' => 'datetime',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'last_request_time' => [
            'name' => 'last_request_time',
            'type' => 'datetime',
        ],
        'session_type' => [
            'name' => 'session_type',
            'type' => 'varchar',
            'len' => '100',
        ],
        'is_violation' => [
            'name' => 'is_violation',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
        'num_active_sessions' => [
            'name' => 'num_active_sessions',
            'type' => 'int',
            'default' => '0',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'session_historypk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
    ],
];

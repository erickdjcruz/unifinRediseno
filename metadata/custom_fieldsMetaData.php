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

$dictionary['custom_fields'] = [
    'table' => 'custom_fields',
    'fields' => [
        'bean_id' => [
            'name' => 'bean_id',
            'type' => 'id',
        ],
        'set_num' => [
            'name' => 'set_num',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
        ],
        'field0' => [
            'name' => 'field0',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field1' => [
            'name' => 'field1',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field2' => [
            'name' => 'field2',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field3' => [
            'name' => 'field3',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field4' => [
            'name' => 'field4',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field5' => [
            'name' => 'field5',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field6' => [
            'name' => 'field6',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field7' => [
            'name' => 'field7',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field8' => [
            'name' => 'field8',
            'type' => 'varchar',
            'len' => '255',
        ],
        'field9' => [
            'name' => 'field9',
            'type' => 'varchar',
            'len' => '255',
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
            'name' => 'idx_beanid_set_num',
            'type' => 'index',
            'fields' => [
                'bean_id',
                'set_num',
            ],
        ],
    ],
];

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
$dictionary['ChangeTimer'] = [
    'table' => 'changetimers',
    'audited' => false,
    'fields' => [
        'parent_type' => [
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'len' => '255',
            'required' => true,
            'options' => 'changetimer_parent_type',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'field_name' => [
            'name' => 'field_name',
            'vname' => 'LBL_FIELD_NAME',
            'type' => 'varchar',
            'len' => 100,
        ],
        'value_string' => [
            'name' => 'value_string',
            'vname' => 'LBL_VALUE',
            'type' => 'varchar',
        ],
        'from_datetime' => [
            'name' => 'from_datetime',
            'vname' => 'LBL_FROM_DATETIME',
            'type' => 'datetime',
        ],
        'to_datetime' => [
            'name' => 'to_datetime',
            'vname' => 'LBL_TO_DATETIME',
            'type' => 'datetime',
        ],
        'hours' => [
            'name' => 'hours',
            'vname' => 'LBL_HOURS',
            'type' => 'decimal',
            'len' => '12,2',
            'precision' => 2,
            'audited' => false,
            'readonly' => true,
            'massupdate' => false,
        ],
        'business_hours' => [
            'name' => 'business_hours',
            'vname' => 'LBL_BUSINESS_HOURS',
            'type' => 'decimal',
            'len' => '12,2',
            'precision' => 2,
            'audited' => false,
            'readonly' => true,
            'massupdate' => false,
        ],
        'cases' => [
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'cases_changetimers',
            'source' => 'non-db',
            'vname' => 'LBL_CASES',
        ],
    ],
    'relationships' => [
    ],
    'indices' => [
        [
            'name' => 'idx_parent',
            'type' => 'index',
            'fields' => [
                'parent_id',
                'parent_type',
            ],
        ],
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
        'lockable_fields',
    ],
    'uses' => [
        'default',
    ],
];

VardefManager::createVardef('ChangeTimers', 'ChangeTimer');

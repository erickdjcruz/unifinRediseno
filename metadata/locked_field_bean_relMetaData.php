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

$dictionary['locked_field_bean_rel'] = [
    'table' => 'locked_field_bean_rel',
    'relationships' => [
    ],
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'pd_id' => [
            'name' => 'pd_id',
            'type' => 'id',
            'required' => true,
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'type' => 'id',
            'required' => true,
        ],
        'bean_module' => [
            'name' => 'bean_module',
            'type' => 'varchar',
            'len' => 100,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'locked_fields_bean_relpk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_locked_fields_rel_pdid_beanid',
            'type' => 'index',
            'fields' => [
                'pd_id',
                'bean_id',
            ],
        ],
        [
            'name' => 'idx_locked_field_bean_rel_del_bean_module_beanid',
            'type' => 'index',
            'fields' => [
                'bean_module',
                'deleted',
            ],
        ],
        [
            'name' => 'idx_locked_field_bean_rel_beanid_del_bean_module',
            'type' => 'index',
            'fields' => [
                'bean_id',
                'deleted',
                'bean_module',
            ],
        ],
    ],
];

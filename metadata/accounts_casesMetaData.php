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

$dictionary['accounts_cases'] = [
    'table' => 'accounts_cases',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'account_id' => [
            'name' => 'account_id',
            'type' => 'id',
        ],
        'case_id' => [
            'name' => 'case_id',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'required' => false,
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'accounts_casespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_acc_case_acc',
            'type' => 'index',
            'fields' => [
                'account_id',
            ],
        ],
        [
            'name' => 'idx_acc_acc_case',
            'type' => 'index',
            'fields' => [
                'case_id',
            ],
        ],
    ],
];

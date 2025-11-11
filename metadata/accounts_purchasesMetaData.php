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

$dictionary['accounts_purchases'] = [
    'table' => 'accounts_purchases',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'account_id' => [
            'name' => 'account_id',
            'type' => 'id',
        ],
        'purchase_id' => [
            'name' => 'purchase_id',
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
            'name' => 'accounts_purchasespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_acc_purchase_acc',
            'type' => 'index',
            'fields' => [
                'account_id',
            ],
        ],
        [
            'name' => 'idx_acc_acc_purchase',
            'type' => 'index',
            'fields' => [
                'purchase_id',
            ],
        ],
    ],
];

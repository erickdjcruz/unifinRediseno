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
$dictionary['users_last_import'] = ['table' => 'users_last_import'
    , 'fields' => [
        ['name' => 'id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'assigned_user_id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'bean_type', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'bean_id', 'type' => 'varchar', 'len' => '36',]
        , ['name' => 'date_modified', 'type' => 'datetime']
        , ['name' => 'deleted', 'required' => false, 'type' => 'bool', 'len' => '1'],
    ], 'indices' => [
        ['name' => 'users_last_importpk', 'type' => 'primary', 'fields' => ['id']]
        , ['name' => 'idx_user_id', 'type' => 'index', 'fields' => ['assigned_user_id']],
    ],
];

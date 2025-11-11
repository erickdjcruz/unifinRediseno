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

$dictionary['teams'] = ['table' => 'teams'
    , 'fields' => [
        ['name' => 'id', 'type' => 'varchar', 'len' => '36',]
        , ['name' => 'name', 'type' => 'varchar', 'len' => '128',]
        , ['name' => 'date_entered', 'type' => 'datetime', 'len' => '',]
        , ['name' => 'date_modified', 'type' => 'datetime', 'len' => '',]
        , ['name' => 'modified_user_id', 'type' => 'varchar', 'len' => '36',]
        , ['name' => 'created_by', 'type' => 'varchar', 'len' => '36',]
        , ['name' => 'private', 'type' => 'bool', 'len' => '1', 'default' => '0']
        , ['name' => 'description', 'type' => 'text', 'len' => '',]
        , ['name' => 'date_modified', 'type' => 'datetime']
        , ['name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0'],
    ], 'indices' => [
        ['name' => 'teamspk', 'type' => 'primary', 'fields' => ['id']],
    ],
];

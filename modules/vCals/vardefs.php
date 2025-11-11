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

// Create the indexes
$dictionary['vCal'] = ['table' => 'vcals'
    , 'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'len' => 100,
        ],
        'source' => [
            'name' => 'source',
            'type' => 'varchar',
            'len' => 100,
        ],
        'content' => [
            'name' => 'content',
            'type' => 'text',
        ],


    ]
    , 'indices' => [
        ['name' => 'vcalspk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_vcal', 'type' => 'index', 'fields' => ['type', 'user_id', 'source']],
    ],

];

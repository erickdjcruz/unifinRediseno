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

$dictionary['RecordList'] = [
    'table' => 'record_list',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'assigned_user_id' => [
            'name' => 'assigned_user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'module_name' => [
            'name' => 'module_name',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'len' => '50',
            'required' => true,
            'reportable' => false,
        ],
        'records' => [
            'name' => 'records',
            'vname' => 'LBL_RECORD_LIST',
            'type' => 'longtext',
            'required' => true,
            'reportable' => false,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
        ],
    ],
    'indices' => [
        [
            'name' => 'record_list_id',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
    ], /* end indices */
];

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
$dictionary['CustomQuery'] = ['table' => 'custom_queries',
    'comment' => 'Stores the query used in Advanced Reports'
    , 'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_NAME',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifer',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => true,
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ],
        'created_by' => [
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'comment' => 'User ID who created record',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_NAME',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Name of the custom query',
            'importable' => 'required',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Full description of the custom query',
        ],
        'custom_query' => [
            'name' => 'custom_query',
            'vname' => 'LBL_CUSTOMQUERY',
            'type' => 'text',
            'comment' => 'The SQL statement',
        ],
        'query_type' => [
            'name' => 'query_type',
            'vname' => 'LBL_QUERY_TYPE',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'The type of query (unused)',
        ],
        'list_order' => [
            'name' => 'list_order',
            'vname' => 'LBL_LIST_ORDER',
            'type' => 'int',
            'len' => '4',
            'comment' => 'The relative order of this query (unused)',
        ],
        'query_locked' => [
            'name' => 'query_locked',
            'vname' => 'LBL_QUERY_LOCKED',
            'type' => 'bool',
            'dbType' => 'varchar',
            'len' => '3',
            'default' => '0',
            'comment' => 'Indicates whether query body (the SQL statement) can be changed',
        ],
    ],
    'acls' => ['SugarACLAdminOnly' => ['allowUserRead' => true]],
    'indices' => [
        ['name' => 'custom_queriespk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_customqueries', 'type' => 'index', 'fields' => ['name', 'deleted']],
    ],
];

VardefManager::createVardef('CustomQueries', 'CustomQuery', [
    'team_security',
]);

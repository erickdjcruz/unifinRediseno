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

$dictionary['ExternalUser'] = [
    'table' => 'external_users',
    'fields' => [
        'user_name' => [
            'name' => 'user_name',
            'vname' => 'LBL_USER_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Username of the external user',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'external_user_status_dom',
            'len' => 100,
            'comment' => 'Status of the external user',
        ],
        'external_app' => [
            'name' => 'external_app',
            'type' => 'varchar',
            'len' => 255,
            'vname' => 'LBL_EXTERNAL_APPLICATION',
            'comment' => 'External application which creates the external user',
        ],
        'external_id' => [
            'name' => 'external_id',
            'type' => 'varchar',
            'len' => 255,
            'vname' => 'LBL_EXTERNAL_ID',
            'comment' => 'External id of the external user',
        ],
        'parent_type' => [
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_name',
            'options' => 'parent_type_display',
            'len' => '255',
            'required' => true,
            'reportable' => false,
            'comment' => 'Sugar module the External User is associated with',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
            'comment' => 'The ID of the Sugar item specified in parent_type',
        ],
        'parent_name' => [
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'source' => 'non-db',
            'options' => 'record_type_display_notes',
            'studio' => true,
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'external_users_accounts',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
        ],
        'cases' => [
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'external_users_cases',
            'module' => 'Cases',
            'bean_name' => 'aCase',
            'source' => 'non-db',
            'vname' => 'LBL_CASES',
        ],
        'bugs' => [
            'name' => 'bugs',
            'type' => 'link',
            'relationship' => 'external_users_bugs',
            'module' => 'Bugs',
            'bean_name' => 'Bug',
            'source' => 'non-db',
            'vname' => 'LBL_BUGS',
        ],
        'opportunities' => [
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'external_users_opportunities',
            'module' => 'Opportunities',
            'bean_name' => 'Opportunity',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES',
        ],
        'quotes' => [
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'external_users_quotes',
            'module' => 'Quotes',
            'bean_name' => 'Quote',
            'source' => 'non-db',
            'vname' => 'LBL_QUOTES',
        ],
        'notes' => [
            'name' => 'notes',
            'type' => 'link',
            'relationship' => 'external_user_notes',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ],
    ],
    'ignore_templates' => [
        'external_user',
        'lockable_fields',
    ],
    'uses' => [
        'person',
        'external_source',
        'assignable',
        'team_security',
        'taggable',
    ],
    'acls' => [
        'SugarACLAdminOnly' => [
            'allowUserRead' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_external_users_user_name',
            'type' => 'index',
            'fields' => ['user_name'],
        ],
        [
            'name' => 'idx_external_users_external_id',
            'type' => 'index',
            'fields' => ['external_id'],
        ],
    ],
    'relationships' => [
        'external_user_notes' => [
            'lhs_module' => 'ExternalUsers',
            'lhs_table' => 'external_users',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'external_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'duplicate_check' => [
        'enabled' => true,
        'FilterDuplicateCheck' => [
            'filter_template' => [
                ['external_id' => ['$equals' => '$external_id']],
            ],
            'ranking_fields' => [
                ['in_field_name' => 'external_id', 'dupe_field_name' => 'external_id'],
            ],
        ],
    ],
];

VardefManager::createVardef('ExternalUsers', 'ExternalUser');

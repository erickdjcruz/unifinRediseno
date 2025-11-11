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

$dictionary['WorkFlowTriggerShell'] = ['table' => 'workflow_triggershells'
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
            'default' => '0',
            'reportable' => false,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
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
        ],
        'field' => [
            'name' => 'field',
            'vname' => 'LBL_FIELD',
            'type' => 'varchar',
            'len' => '50',
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_type_dom',
            'len' => 100,
        ],
        'frame_type' => [
            'name' => 'frame_type',
            'vname' => 'LBL_FRAME_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_frame_type_dom',
            'len' => 15,
        ],
        'eval' => [
            'name' => 'eval',
            'vname' => 'LBL_EVAL',
            'type' => 'text',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'show_past' => [
            'name' => 'show_past',
            'vname' => 'LBL_SHOW_PAST',
            'type' => 'bool',
            'default' => '0',
        ],
        'rel_module' => [
            'name' => 'rel_module',
            'vname' => 'LBL_REL_MODULE',
            'type' => 'varchar',
            'len' => '255',
            'required' => false,
        ],
        'rel_module_type' => [
            'name' => 'rel_module_type',
            'vname' => 'LBL_RELATED_TYPE',
            'type' => 'enum',
            'options' => 'wflow_relfilter_type_dom',
            'len' => 10,
        ],
        'parameters' => [
            'name' => 'parameters',
            'vname' => 'LBL_EXT1',
            'type' => 'varchar',
            'len' => '255',
            'required' => false,
        ],
        'past_triggers' => [
            'name' => 'past_triggers',
            'type' => 'link',
            'relationship' => 'past_triggers',
            'module' => 'Expressions',
            'bean_name' => 'Expression',
            'source' => 'non-db',
        ],
        'future_triggers' => [
            'name' => 'future_triggers',
            'type' => 'link',
            'relationship' => 'future_triggers',
            'module' => 'Expressions',
            'bean_name' => 'Expression',
            'source' => 'non-db',
        ],
        'expressions' => [
            'name' => 'expressions',
            'type' => 'link',
            'relationship' => 'trigger_expressions',
            'module' => 'Expressions',
            'bean_name' => 'Expression',
            'source' => 'non-db',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => true],
    'indices' => [
        ['name' => 'triggershell_k', 'type' => 'primary', 'fields' => ['id']],

    ]
    , 'relationships' => [
        'past_triggers' => ['lhs_module' => 'WorkFlowTriggerShells', 'lhs_table' => 'workflow_triggershells', 'lhs_key' => 'id',
            'rhs_module' => 'Expressions', 'rhs_table' => 'expressions', 'rhs_key' => 'parent_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'past_trigger', 'relationship_type' => 'one-to-many'],

        'future_triggers' => ['lhs_module' => 'WorkFlowTriggerShells', 'lhs_table' => 'workflow_triggershells', 'lhs_key' => 'id',
            'rhs_module' => 'Expressions', 'rhs_table' => 'expressions', 'rhs_key' => 'parent_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'future_trigger', 'relationship_type' => 'one-to-many'],

        'trigger_expressions' => ['lhs_module' => 'WorkFlowTriggerShells', 'lhs_table' => 'workflow_triggershells', 'lhs_key' => 'id',
            'rhs_module' => 'Expressions', 'rhs_table' => 'expressions', 'rhs_key' => 'parent_id',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'expression', 'relationship_type' => 'one-to-many'],
    ],

];

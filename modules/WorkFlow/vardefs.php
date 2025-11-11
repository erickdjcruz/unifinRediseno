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
$dictionary['WorkFlow'] = ['table' => 'workflow'
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
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '50',
        ],
        'base_module' => [
            'name' => 'base_module',
            'vname' => 'LBL_BASE_MODULE',
            'type' => 'varchar',
            'len' => '50',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'bool',
            'default' => '0',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_type_dom',
            'len' => 100,
        ],
        'fire_order' => [
            'name' => 'fire_order',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_fire_order_dom',
            'len' => 100,
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
        ],
        'record_type' => [
            'name' => 'record_type',
            'vname' => 'LBL_RECORD_TYPE',
            'type' => 'enum',
            'required' => true,
            'options' => 'wflow_record_type_dom',
            'len' => 100,
        ],
        'list_order_y' => [
            'name' => 'list_order_y',
            'vname' => 'LBL_LISTORDER_Y',
            'type' => 'int',
            'len' => '3',
            'default' => '0',
        ],
        'triggers' => [
            'name' => 'triggers',
            'type' => 'link',
            'relationship' => 'workflow_triggers',
            'module' => 'WorkFlowTriggerShells',
            'bean_name' => 'WorkFlowTriggerShell',
            'source' => 'non-db',
        ],
        'trigger_filters' => [
            'name' => 'trigger_filters',
            'type' => 'link',
            'relationship' => 'workflow_trigger_filters',
            'module' => 'WorkFlowTriggerShells',
            'bean_name' => 'WorkFlowTriggerShell',
            'source' => 'non-db',
        ],
        'alerts' => [
            'name' => 'alerts',
            'type' => 'link',
            'relationship' => 'workflow_alerts',
            'module' => 'WorkFlowAlertShells',
            'bean_name' => 'WorkFlowAlertShell',
            'source' => 'non-db',
        ],
        'actions' => [
            'name' => 'actions',
            'type' => 'link',
            'relationship' => 'workflow_actions',
            'module' => 'WorkFlowActionShells',
            'bean_name' => 'WorkFlowActionShell',
            'source' => 'non-db',
        ],
    ],
    'acls' => ['SugarACLDeveloperOrAdmin' => true],
    'indices' => [
        ['name' => 'workflow_k', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_workflow', 'type' => 'index', 'fields' => ['name', 'deleted']],
        ['name' => 'idx_workflow_type', 'type' => 'index', 'fields' => ['type']],
        ['name' => 'idx_workflow_base_module', 'type' => 'index', 'fields' => ['base_module']],
    ]

    , 'relationships' => [
        'workflow_triggers' => ['lhs_module' => 'WorkFlow', 'lhs_table' => 'workflow', 'lhs_key' => 'id',
            'rhs_module' => 'WorkFlowTriggerShells', 'rhs_table' => 'workflow_triggershells', 'rhs_key' => 'parent_id',
            'relationship_role_column' => 'frame_type',
            'relationship_role_column_value' => 'Primary', 'relationship_type' => 'one-to-many'],
        'workflow_trigger_filters' => ['lhs_module' => 'WorkFlow', 'lhs_table' => 'workflow', 'lhs_key' => 'id',
            'rhs_module' => 'WorkFlowTriggerShells', 'rhs_table' => 'workflow_triggershells', 'rhs_key' => 'parent_id',
            'relationship_role_column' => 'frame_type',
            'relationship_role_column_value' => 'Secondary', 'relationship_type' => 'one-to-many'],
        'workflow_alerts' => ['lhs_module' => 'WorkFlow', 'lhs_table' => 'workflow', 'lhs_key' => 'id',
            'rhs_module' => 'WorkFlowAlertShells', 'rhs_table' => 'workflow_alertshells', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'],
        'workflow_actions' => ['lhs_module' => 'WorkFlow', 'lhs_table' => 'workflow', 'lhs_key' => 'id',
            'rhs_module' => 'WorkFlowActionShells', 'rhs_table' => 'workflow_actionshells', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'],
    ],


];

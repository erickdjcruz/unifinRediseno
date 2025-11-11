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

$vardefs = [
    'fields' => [
        'locked_fields' => [
            'name' => 'locked_fields',
            'vname' => 'LBL_LOCKED_FIELDS',
            'type' => 'locked_fields',
            'link' => 'locked_fields_link',
            'source' => 'non-db',
            'module' => 'pmse_BpmProcessDefinition',
            'relate_collection' => true,
            'studio' => false,
            'massupdate' => false,
            'exportable' => false,
            'sortable' => false,
            'rname' => 'pro_locked_variables',
            'collection_fields' => ['pro_locked_variables'],
            'full_text_search' => [
                'enabled' => false,
                'searchable' => false,
            ],
            // This field should not show in the field matrix for roles
            'hideacl' => true,
        ],
        'locked_fields_link' => [
            'name' => 'locked_fields_link',
            'type' => 'link',
            'vname' => 'LBL_LOCKED_FIELDS_LINK',
            'relationship' => strtolower($module) . '_locked_fields',
            'source' => 'non-db',
            'exportable' => false,
            'duplicate_merge' => 'disabled',
        ],
    ],
    'relationships' => [
        strtolower($module) . '_locked_fields' => [
            'lhs_module' => $module,
            'lhs_table' => $table_name,
            'lhs_key' => 'id',
            'rhs_module' => 'pmse_BpmProcessDefinition',
            'rhs_table' => 'pmse_bpm_process_definition',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'locked_field_bean_rel',
            'join_key_lhs' => 'bean_id',
            'join_key_rhs' => 'pd_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => $module,
        ],
    ],
    'acls' => [
        'SugarACLLockedFields' => true,
    ],
];

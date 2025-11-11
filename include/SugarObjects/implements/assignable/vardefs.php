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
        'assigned_user_id' => [
            'name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO_ID',
            'group' => 'assigned_user_name',
            'type' => 'id',
            'reportable' => false,
            'isnull' => 'false',
            'audited' => true,
            'duplicate_on_record_copy' => 'always',
            'comment' => 'User ID assigned to record',
            'duplicate_merge' => 'disabled',
            'mandatory_fetch' => true,
            'massupdate' => false,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                'aggregations' => [
                    'assigned_user_id' => [
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_ASSIGNED_TO_ME',
                    ],
                ],
            ],
        ],
        'assigned_user_name' => [
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO',
            'rname' => 'full_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'duplicate_on_record_copy' => 'always',
            'sort_on' => ['last_name'],
            'exportable' => true,
            'related_fields' => [
                'assigned_user_id',
            ],
        ],
        'assigned_user_link' => [
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => strtolower($module) . '_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'id_name' => 'assigned_user_id',
            'table' => 'users',
            'side' => 'right',
        ],
    ],
    'indices' => [
        'assigned_user_id' => [
            'name' => 'idx_' . strtolower($table_name) . '_assigned_del',
            'type' => 'index',
            'fields' => ['assigned_user_id', 'deleted'],
        ],
    ],
    'relationships' => [
        strtolower($module) . '_assigned_user' => ['lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => $module, 'rhs_table' => strtolower($table_name), 'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'],
    ]];

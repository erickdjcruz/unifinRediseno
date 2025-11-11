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
$dictionary['Tracker'] = [
    'table' => 'tracker',
    'archive' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'int',
            'len' => '11',
            'isnull' => 'false',
            'auto_increment' => true,
            'readonly' => true,
            'reportable' => true,
        ],
        'monitor_id' => [
            'name' => 'monitor_id',
            'vname' => 'LBL_MONITOR_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
            'isnull' => 'false',
        ],
        'module_name' => [
            'name' => 'module_name',
            'vname' => 'LBL_MODULE_NAME',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ],
        'item_id' => [
            'name' => 'item_id',
            'vname' => 'LBL_ITEM_ID',
            'type' => 'id',
            'isnull' => 'false',
        ],
        'item_summary' => [
            'name' => 'item_summary',
            'vname' => 'LBL_ITEM_SUMMARY',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ],
        'team_id' => [
            'name' => 'team_id',
            'vname' => 'LBL_TEAM_ID',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_LAST_ACTION',
            'type' => 'datetime',
            'isnull' => 'false',
        ],
        'action' => [
            'name' => 'action',
            'vname' => 'LBL_ACTION',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ],
        'session_id' => [
            'name' => 'session_id',
            'vname' => 'LBL_SESSION_ID',
            'type' => 'id',
            'isnull' => 'true',
            'exportable' => false,
        ],
        'visible' => [
            'name' => 'visible',
            'vname' => 'LBL_VISIBLE',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ],
        'assigned_user_link' => [
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'tracker_user_id',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ],
        'monitor_id_link' => [
            'name' => 'monitor_id_link',
            'type' => 'link',
            'relationship' => 'tracker_monitor_id',
            'vname' => 'LBL_MONITOR_ID',
            'link_type' => 'one',
            'module' => 'TrackerPerfs',
            'bean_name' => 'TrackerPerf',
            'source' => 'non-db',
        ],
    ],

    //indices
    'indices' => [
        [
            'name' => 'tracker_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_tracker_iid',
            'type' => 'index',
            'fields' => [
                'item_id',
            ],
        ],
        [
            // shortened name to comply with Oracle length restriction
            'name' => 'idx_tracker_userid_vis_id',
            'type' => 'index',
            'fields' => [
                'user_id',
                'visible',
                'id',
            ],
        ],
        [
            // shortened name to comply with Oracle length restriction
            'name' => 'idx_tracker_userid_itemid_vis',
            'type' => 'index',
            'fields' => [
                'user_id',
                'item_id',
                'visible',
            ],
        ],
        [
            'name' => 'idx_tracker_userid_del_vis',
            'type' => 'index',
            'fields' => [
                'user_id',
                'deleted',
                'visible',
            ],
        ],
        [
            'name' => 'idx_tracker_monitor_id',
            'type' => 'index',
            'fields' => [
                'monitor_id',
            ],
        ],
        [
            'name' => 'idx_tracker_date_modified',
            'type' => 'index',
            'fields' => [
                'date_modified',
            ],
        ],
        [
            'name' => 'idx_trckr_mod_uid_dtmod_item',
            'type' => 'index',
            'fields' => [
                'module_name',
                'user_id',
                'date_modified',
                'item_id',
            ],
        ],
        [
            'name' => 'idx_userid_module_itemid_summary_datemodified',
            'type' => 'index',
            'fields' => [
                'user_id',
                'module_name',
                'item_id',
                'item_summary',
                'date_modified',
            ],
        ],
    ],

    //relationships
    'relationships' => [
        'tracker_monitor_id' => [
            'lhs_module' => 'TrackerPerfs', 'lhs_table' => 'tracker_perf', 'lhs_key' => 'monitor_id',
            'rhs_module' => 'Trackers', 'rhs_table' => 'tracker', 'rhs_key' => 'monitor_id',
            'relationship_type' => 'one-to-one',
        ],
    ],
    'acls' => ['SugarACLStatic' => true],
];
if (!isset($dictionary['tracker_sessions']['fields'])) {
    require 'modules/Trackers/tracker_sessionsMetaData.php';
}
if (!isset($dictionary['tracker_perf']['fields'])) {
    require 'modules/Trackers/tracker_perfMetaData.php';
}
if (!isset($dictionary['tracker_queries']['fields'])) {
    require 'modules/Trackers/tracker_queriesMetaData.php';
}

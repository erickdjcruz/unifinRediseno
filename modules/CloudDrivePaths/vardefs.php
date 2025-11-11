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
$dictionary['CloudDrivePath'] = [
    'table' => 'cloud_drive_paths',
    'archive' => false,
    'color' => 'pacific',
    'icon' => 'sicon-cloud',
    'audited' => false,
    'activity_enabled' => false,
    'full_text_search' => false,
    'unified_search_default_enabled' => false,
    'duplicate_merge' => false,
    'comment' => 'CloudDrivePaths is used to save record paths for CloudDrive',
    'fields' => [
        'record_id' => [
            'name' => 'record_id',
            'vname' => 'LBL_RECORD',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'readonly' => true,
        ],
        'path_module' => [
            'name' => 'path_module',
            'vname' => 'LBL_MODULE',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'readonly' => true,
        ],
        'path' => [
            'name' => 'path',
            'vname' => 'LBL_CLOUD_PATH',
            'type' => 'text',
            'required' => false,
            'readonly' => true,
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'len' => '255',
            'required' => false,
            'readonly' => true,
        ],
        'is_root' => [
            'name' => 'is_root',
            'vname' => 'LBL_IS_ROOT',
            'type' => 'bool',
            'default' => 0,
        ],
        'is_shared' => [
            'name' => 'is_shared',
            'vname' => 'LBL_IS_SHARED',
            'type' => 'bool',
            'default' => 0,
        ],
        'folder_id' => [
            'name' => 'folder_id',
            'vname' => 'LBL_FOLDER_ID',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'readonly' => true,
        ],
        'drive_id' => [
            'name' => 'drive_id',
            'vname' => 'LBL_DRIVE_ID',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'readonly' => true,
        ],
        'site_id' => [
            'name' => 'site_id',
            'vname' => 'LBL_SITE_ID',
            'type' => 'varchar',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'readonly' => true,
        ],
    ],
    'duplicate_check' => [
        'enabled' => false,
    ],
];

VardefManager::createVardef('CloudDrivePaths', 'CloudDrivePath');

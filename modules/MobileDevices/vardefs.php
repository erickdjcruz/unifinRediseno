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
$dictionary['MobileDevice'] = [
    'table' => 'mobile_devices',
    'audited' => false,
    'activity_enabled' => false,
    'unified_search' => false,
    'full_text_search' => false,
    'unified_search_default_enabled' => false,
    'duplicate_merge' => false,
    'fields' => [
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
            'required' => false,
        ],
        'device_id' => [
            'name' => 'device_id',
            'vname' => 'LBL_DEVICE_ID',
            'type' => 'varchar',
            'len' => 256,
            'required' => true,
        ],
        'device_platform' => [
            'name' => 'device_platform',
            'vname' => 'LBL_DEVICE_PLATFORM',
            'type' => 'varchar',
            'len' => 32,
            'required' => true,
        ],
    ],
    'relationships' => [
    ],
    'indices' => [
        [
            'name' => 'idx_assigned_device_id',
            'type' => 'index',
            'fields' => [
                'assigned_user_id',
                'device_id',
                'device_platform',
                'deleted',
            ],
        ],
    ],
    'uses' => [
        'basic',
        'assignable',
    ],
    'acls' => [
        'SugarACLOwnerWrite' => true,
    ],
    'visibility' => [
        'OwnerOrAdminVisibility' => true,
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
];

VardefManager::createVardef('MobileDevices', 'MobileDevice');

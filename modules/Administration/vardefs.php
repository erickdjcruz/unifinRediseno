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
$dictionary['Administration'] = [
    'table' => 'config',
    'archive' => false,
    'comment' => 'System table containing system-wide definitions',
    'color' => 'teal',
    'icon' => 'sicon-settings-lg',
    'hidden_to_role_assignment' => true,
    'fields' => [
        'category' => [
            'name' => 'category',
            'vname' => 'LBL_LIST_SYMBOL',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'Settings are grouped under this category; arbitraily defined based on requirements',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_NAME',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'The name given to the setting',
        ],
        'value' => [
            'name' => 'value',
            'vname' => 'LBL_LIST_RATE',
            'type' => 'text',
            'comment' => 'The value given to the setting',
        ],
        'platform' => [
            'name' => 'platform',
            'vname' => 'LBL_LIST_PLATFORM',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'Which platform to send this back with vai the api',
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_config_cat',
            'type' => 'index',
            'fields' => [
                'category',
            ],
        ],
    ],
    'acls' => ['SugarACLDeveloperForTarget' => ['allowUserRead' => true], 'SugarACLStatic' => false],
];

$dictionary['UpgradeHistory'] = [
    'table' => 'upgrade_history',
    'archive' => false,
    'comment' => 'Tracks Sugar upgrades made over time; used by Upgrade Wizard and Module Loader',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifier',
        ],
        'filename' => [
            'name' => 'filename',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Cached filename containing the upgrade scripts and content',
        ],
        'md5sum' => [
            'name' => 'md5sum',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'The MD5 checksum of the upgrade file',
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'len' => '30',
            'comment' => 'The upgrade type (module, patch, etc)',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'The status of the upgrade (ex:  "installed")',
        ],
        'version' => [
            'name' => 'version',
            'type' => 'varchar',
            'len' => '64',
            'comment' => 'Version as contained in manifest file',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'len' => '255',
        ],
        'description' => [
            'name' => 'description',
            'type' => 'text',
        ],
        'id_name' => [
            'name' => 'id_name',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'The unique id of the module',
        ],
        'manifest' => [
            'name' => 'manifest',
            'type' => 'longtext',
            'comment' => 'A serialized copy of the manifest file.',
        ],
        'patch' => [
            'name' => 'patch',
            'type' => 'text',
            'comment' => 'A serialized copy of the patch applied to the package during installation',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date of create or module load',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date of create or module load',
        ],
        'published_date' => [
            'name' => 'published_date',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Package published date from manifest. Saved as is.',
        ],
        'uninstallable' => [
            'name' => 'uninstallable',
            'type' => 'bool',
            'len' => '1',
            'default' => '1',
            'comment' => 'Is package uninstallable?',
        ],
        'enabled' => [
            'name' => 'enabled',
            'type' => 'bool',
            'len' => '1',
            'default' => '1',
        ],
        'process_status' => [
            'name' => 'process_status',
            'type' => 'text',
            'comment' => 'Package status data in JSON: installation progress and related values',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
            'comment' => 'Record deletion indicator',
        ],
    ],
    'indices' => [
        [
            'name' => 'upgrade_history_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'upgrade_history_md5_uk',
            'type' => 'unique',
            'fields' => ['md5sum'],
        ],
    ],
];

$dictionary['SessionManager'] = ['table' => 'session_active', 'archive' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],

        'session_id' => [
            'name' => 'session_id',
            'type' => 'varchar',
            'len' => '100',
        ],
        'last_request_time' => [
            'name' => 'last_request_time',
            'type' => 'datetime',
        ],
        'session_type' => [
            'name' => 'session_type',
            'type' => 'varchar',
            'len' => '100',
        ],
        'is_violation' => [
            'name' => 'is_violation',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
        'num_active_sessions' => [
            'name' => 'num_active_sessions',
            'type' => 'int',
            'default' => '0',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'type' => 'datetime',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        ['name' => 'session_active_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        ['name' => 'idx_session_id',
            'type' => 'unique',
            'fields' => ['session_id']],
    ],
];

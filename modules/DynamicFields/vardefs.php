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
$dictionary['FieldsMetaData'] = [
    'table' => 'fields_meta_data',
    'archive' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'reportable' => false,
        ],
        'name' => ['name' => 'name', 'vname' => 'COLUMN_TITLE_NAME', 'type' => 'varchar', 'len' => '255'],
        'vname' => ['name' => 'vname', 'type' => 'varchar', 'vname' => 'COLUMN_TITLE_LABEL', 'len' => '255'],
        'comments' => ['name' => 'comments', 'type' => 'varchar', 'vname' => 'COLUMN_TITLE_LABEL', 'len' => '255'],
        'help' => ['name' => 'help', 'type' => 'varchar', 'vname' => 'COLUMN_TITLE_LABEL', 'len' => '255'],
        'custom_module' => ['name' => 'custom_module', 'type' => 'varchar', 'len' => '255',],
        'type' => ['name' => 'type', 'vname' => 'COLUMN_TITLE_DATA_TYPE', 'type' => 'varchar', 'len' => '255'],
        'len' => ['name' => 'len', 'vname' => 'COLUMN_TITLE_MAX_SIZE', 'type' => 'int', 'len' => '11', 'required' => false, 'validation' => ['type' => 'range', 'min' => 1, 'max' => 255],],
        'required' => ['name' => 'required', 'type' => 'bool', 'default' => '0'],
        'default_value' => ['name' => 'default_value', 'type' => 'varchar', 'len' => '255',],
        'date_modified' => ['name' => 'date_modified', 'type' => 'datetime'],
        'deleted' => ['name' => 'deleted', 'type' => 'bool', 'default' => '0', 'reportable' => false],
        'audited' => ['name' => 'audited', 'type' => 'bool', 'default' => '0'],
        'massupdate' => ['name' => 'massupdate', 'type' => 'bool', 'default' => '0'],
        'duplicate_merge' => ['name' => 'duplicate_merge', 'type' => 'short', 'default' => '0'],
        'reportable' => ['name' => 'reportable', 'type' => 'bool', 'default' => '1'],
        'importable' => ['name' => 'importable', 'type' => 'varchar', 'len' => '255'],
        'ext1' => ['name' => 'ext1', 'type' => 'varchar', 'len' => '255', 'default' => ''],
        'ext2' => ['name' => 'ext2', 'type' => 'varchar', 'len' => '255', 'default' => ''],
        'ext3' => ['name' => 'ext3', 'type' => 'varchar', 'len' => '255', 'default' => ''],
        'ext4' => ['name' => 'ext4', 'type' => 'text'],
        'autoinc_next' => ['name' => 'autoinc_next', 'type' => 'int', 'default' => ''],
    ],
    'indices' => [
        ['name' => 'fields_meta_datapk', 'type' => 'primary', 'fields' => ['id']],
        [
            'name' => 'idx_fields_meta_data_custom_module_name',
            'type' => 'unique',
            'fields' => [
                'custom_module',
                'name',
            ],
        ],
    ],
];

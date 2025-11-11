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
        'sync_key' => [
            'is_sync_key' => true,
            'name' => 'sync_key',
            'vname' => 'LBL_SYNC_KEY',
            'type' => 'varchar',
            'enforced' => '',
            'required' => false,
            'massupdate' => false,
            'readonly' => true,
            'default' => null,
            'isnull' => true,
            'no_default' => false,
            'comments' => 'External default id of the remote integration record',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'merge_filter' => 'disabled',
            'duplicate_on_record_copy' => 'no',
            'audited' => true,
            'reportable' => true,
            'unified_search' => false,
            'calculated' => false,
            'len' => '100',
            'size' => '20',
            'studio' => [
                'recordview' => true,
                'wirelessdetailview' => true,
                'listview' => false,
                'wirelesseditview' => false,
                'wirelesslistview' => false,
                'wireless_basic_search' => false,
                'wireless_advanced_search' => false,
                'portallistview' => false,
                'portalrecordview' => false,
                'portaleditview' => false,
            ],
        ],
    ],
    'indices' => [
        'sync_key' => [
            'name' => 'idx_' . strtolower($table_name) . '_skey',
            'type' => 'unique',
            'fields' => ['sync_key'],
        ],
    ],
];

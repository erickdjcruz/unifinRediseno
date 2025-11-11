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

$dictionary['GeocodeJob'] = [
    'table' => 'geocode_job',
    'archive' => false,
    'audited' => false,
    'activity_enabled' => false,
    'reassignable' => false,
    'duplicate_merge' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
            'comment' => 'Unique identifier',
            'mandatory_fetch' => true,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            'studio' => [
                'portaleditview' => false, // Bug58408 - hide from Portal edit layout
            ],
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'massupdate' => false,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
            ],
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
                'sortable' => true,
            ],
            'studio' => [
                'portaleditview' => false, // Bug58408 - hide from Portal edit layout
            ],
            'options' => 'date_range_search_dom',
            'duplicate_on_record_copy' => 'no',
            'readonly' => true,
            'massupdate' => false,
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
        'status' => [
            'required' => true,
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'false',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => false,
            'merge_filter' => 'disabled',
            'len' => '64',
            'size' => '64',
        ],
        'processed_entity_success_count' => [
            'required' => true,
            'name' => 'processed_entity_success_count',
            'vname' => 'LBL_PROCESSED_ENTITY_SUCCESS',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'false',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => false,
            'merge_filter' => 'disabled',
            'len' => '64',
            'size' => '64',
        ],
        'processed_entity_failed_count' => [
            'required' => true,
            'name' => 'processed_entity_failed_count',
            'vname' => 'LBL_PROCESSED_ENTITY_FAILED',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'false',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => false,
            'merge_filter' => 'disabled',
            'len' => '64',
            'size' => '64',
        ],
        'addresses_data' => [
            'name' => 'addresses_data',
            'vname' => 'LBL_ADDRESSES_DATA',
            'type' => 'json',
            'dbType' => 'longtext',
            'comment' => '',
        ],
        'geocode_result' => [
            'name' => 'geocode_result',
            'vname' => 'LBL_GEOCODE_RESULT',
            'type' => 'json',
            'dbType' => 'longtext',
            'comment' => '',
        ],
    ],
    'indices' => [
        'id' => [
            'name' => 'idx_geocode_job_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        'date_modified' => [
            'name' => 'idx_geocode_job_del_d_m',
            'type' => 'index',
            'fields' => ['deleted', 'date_modified', 'id'],
        ],
        'deleted' => [
            'name' => 'idx_geocode_job_id_del',
            'type' => 'index',
            'fields' => ['id', 'deleted'],
        ],
        'date_entered' => [
            'name' => 'idx_geocode_job_del_d_e',
            'type' => 'index',
            'fields' => ['deleted', 'date_entered', 'id'],
        ],
        'status' => [
            'name' => 'idx_geocode_job_status',
            'type' => 'index',
            'fields' => ['status', 'id'],
        ],
    ],
    'relationships' => [],
    'optimistic_locking' => true,
    'ignore_templates' => [
        'integrate_fields',
        'default',
    ],
    'portal_visibility' => [],
    'uses' => [],
];

VardefManager::createVardef('GeocodeJob', 'GeocodeJob');

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

$dictionary['DataArchiver'] = [
    'table' => 'data_archivers',
    'archive' => false,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'full_text_search' => false,
    'duplicate_merge' => true,
    'fields' => [
        'filter_module_name' => [
            'name' => 'filter_module_name',
            'vname' => 'LBL_MODULE_FIELD',
            'type' => 'enum',
            'function' => 'getArchiveModuleList',
            'function_bean' => 'DataArchiver',
            'required' => true,
            'comment' => 'Module associated with the archived data',
        ],
        'filter_def' => [
            'name' => 'filter_def',
            'vname' => 'LBL_FILTER_DEF_FIELD',
            'type' => 'text',
            'required' => true,
            'comment' => 'The filter definitions defined for the archive',
        ],
        'process_type' => [
            'name' => 'process_type',
            'vname' => 'LBL_PROCESS_TYPE_FIELD',
            'type' => 'enum',
            'function' => 'getProcessTypes',
            'function_bean' => 'DataArchiver',
            'required' => true,
            'defaultToBlank' => true,
            'comment' => 'Whether the data should be truncated or archived',
        ],
        'active' => [
            'name' => 'active',
            'vname' => 'LBL_ACTIVE_FIELD',
            'type' => 'bool',
            'default' => true,
            'comment' => 'Whether the definition is active or not',
        ],
        'archive_runs' => [
            'name' => 'archive_runs',
            'type' => 'link',
            'relationship' => 'archiver_runs',
            'link_type' => 'many',
            'source' => 'non-db',
            'vname' => 'LBL_ARCHIVE_RUNS_FIELD',
            'duplicate_merge' => 'disabled',
        ],
    ],
    'relationships' => [
        'archiver_runs' => [
            'lhs_module' => 'DataArchiver',
            'lhs_table' => 'data_archivers',
            'lhs_key' => 'id',
            'rhs_module' => 'ArchiveRuns',
            'rhs_table' => 'archive_runs',
            'rhs_key' => 'archiver_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
];

VardefManager::createVardef('DataArchiver', 'DataArchiver');

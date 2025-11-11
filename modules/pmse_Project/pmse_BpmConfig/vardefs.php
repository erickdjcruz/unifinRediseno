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

$dictionary['pmse_BpmConfig'] = [
    'table' => 'pmse_bpm_config',
    'archive' => false,
    'audited' => false,
    'activity_enabled' => false,
    'reassignable' => false,
    'duplicate_merge' => true,
    'fields' => [
        'cfg_status' => [
            'required' => true,
            'name' => 'cfg_status',
            'vname' => 'step type',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => 'ACTIVE',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'len' => '20',
            'size' => '20',
        ],
        'cfg_value' => [
            'required' => true,
            'name' => 'cfg_value',
            'vname' => 'script to be executed as part of this ',
            'type' => 'text',
            'massupdate' => false,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'size' => '20',
            'rows' => '4',
            'cols' => '20',
        ],
    ],
    'relationships' => [],
    'optimistic_locking' => true,
    'unified_search' => true,
    'ignore_templates' => [
        'taggable',
        'lockable_fields',
        'commentlog',
    ],
    'portal_visibility' => [
        'class' => 'PMSE',
    ],
    'uses' => [
        'basic',
        'assignable',
    ],
];

VardefManager::createVardef('pmse_BpmConfig', 'pmse_BpmConfig');

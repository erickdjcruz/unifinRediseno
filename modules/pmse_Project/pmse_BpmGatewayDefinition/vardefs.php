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

$dictionary['pmse_BpmGatewayDefinition'] = [
    'table' => 'pmse_bpm_gateway_definition',
    'archive' => false,
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'reassignable' => false,
    'fields' => [
        'prj_id' => [
            'required' => true,
            'name' => 'prj_id',
            'vname' => 'Project id',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
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
            'len' => '36',
            'size' => '36',
        ],
        'pro_id' => [
            'required' => true,
            'name' => 'pro_id',
            'vname' => 'Process id',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
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
            'len' => '36',
            'size' => '36',
        ],
        'execution_mode' => [
            'required' => true,
            'name' => 'execution_mode',
            'vname' => 'script to be executed',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => 'DEFAULT',
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
            'len' => '10',
            'size' => '10',
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

VardefManager::createVardef('pmse_BpmGatewayDefinition', 'pmse_BpmGatewayDefinition');

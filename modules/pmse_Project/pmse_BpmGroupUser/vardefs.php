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

$dictionary['pmse_BpmGroupUser'] = [
    'table' => 'pmse_bpm_group_user',
    'archive' => false,
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'reassignable' => false,
    'fields' => [
        'user_id' => [
            'required' => true,
            'name' => 'user_id',
            'vname' => 'User Identifier',
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

VardefManager::createVardef('pmse_BpmGroupUser', 'pmse_BpmGroupUser');

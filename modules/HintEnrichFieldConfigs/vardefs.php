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
$dictionary['HintEnrichFieldConfig'] = [
    'table' => 'hint_enrich_field_config',
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => false,
    'fields' => [
        'config_data' => [
            'name' => 'config_data',
            'vname' => 'LBL_HINT_ENRICH_FIELD_CONFIG_DATA',
            'type' => 'text',
            'required' => true,
        ],
        'created' => [
            'name' => 'created',
            'vname' => 'LBL_HINT_ENRICH_FIELD_CREATED',
            'type' => 'datetime',
            'required' => true,
        ],
        'synced' => [
            'name' => 'synced',
            'vname' => 'LBL_HINT_ENRICH_FIELD_SYNCED',
            'type' => 'bool',
            'required' => true,
        ],
    ],
    'indices' => [],
    'relationships' => [],
    'optimistic_locking' => false,
    'unified_search' => false,
    'full_text_search' => false,
];

\VardefManager::createVardef('HintEnrichFieldConfigs', 'HintEnrichFieldConfig', ['basic', 'assignable']);

$dictionary['HintEnrichFieldConfig']['fields']['id']['vname'] = 'LBL_HINT_ENRICH_FIELD_USER_ID';

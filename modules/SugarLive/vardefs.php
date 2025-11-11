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

$dictionary['SugarLive'] = [
    'table' => 'sugar_live',
    'comment' => 'SugarLive is required for module name and routing',
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => false,
    'fields' => [
        'enabled_modules' => [
            'name' => 'enabled_modules',
            'vname' => 'LBL_LIST_ENABLED_MODULES',
            'type' => 'varchar',
        ],
    ],
];

VardefManager::createVardef('SugarLive', 'SugarLive');

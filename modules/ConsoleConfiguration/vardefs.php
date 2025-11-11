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

$dictionary['ConsoleConfiguration'] = [
    'table' => 'console_configuration',
    'comment' => 'ConsoleConfiguration is required for module name and routing',
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => false,
    'fields' => [
        'enabled_modules' => [
            'name' => 'enabled_modules',
            'vname' => 'LBL_LIST_ENABLED_MODULES',
            'type' => 'varchar',
        ],
        'order_by_primary' => [
            'required' => true,
            'name' => 'order_by_primary',
            'vname' => 'LBL_CONSOLE_SORT_ORDER_PRIMARY',
            'type' => 'enum',
            'options' => '',
        ],
        'order_by_secondary' => [
            'name' => 'order_by_secondary',
            'vname' => 'LBL_CONSOLE_SORT_ORDER_SECONDARY',
            'type' => 'enum',
            'options' => '',
        ],
        'filter_def' => [
            'name' => 'filter_def',
            'vname' => 'LBL_CONSOLE_FILTER',
            'type' => 'text',
        ],
        'freeze_first_column' => [
            'name' => 'freeze_first_column',
            'vname' => 'LBL_FREEZE_FIRST_COLUMN',
            'type' => 'bool',
            'default' => true,
            'comment' => 'Decides if the first column should be frozen',
        ],
    ],
];

VardefManager::createVardef('ConsoleConfiguration', 'ConsoleConfiguration');

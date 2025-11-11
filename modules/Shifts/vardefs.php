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

$dictionary['Shift'] = [
    'table' => 'shifts',
    'color' => 'green',
    'icon' => 'sicon-scheduler',
    'audited' => true,
    'activity_enabled' => false,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => false,
    'fields' => [
        'timezone' => [
            'name' => 'timezone',
            'vname' => 'LBL_TIMEZONE',
            'type' => 'enum',
            'options' => 'timezone_dom',
            'comment' => 'Time Zone in which this Shift operates',
            'required' => true,
            'audited' => true,
        ],
        'date_start' => [
            'name' => 'date_start',
            'vname' => 'LBL_CALENDAR_START_DATE',
            'type' => 'date',
            'dbType' => 'date',
            'comment' => 'Date in which shift is schedule to (or did) start',
            'required' => true,
            'validation' => ['type' => 'isbefore', 'compareto' => 'date_end', 'blank' => false],
            'audited' => true,
            'massupdate' => false,
        ],
        'date_end' => [
            'name' => 'date_end',
            'vname' => 'LBL_CALENDAR_END_DATE',
            'type' => 'date',
            'dbType' => 'date',
            'comment' => 'Date is which shift is scheduled to (or did) end',
            'required' => true,
            'audited' => true,
            'massupdate' => false,
        ],
        'shifts_users' => [
            'name' => 'shifts_users',
            'type' => 'link',
            'relationship' => 'shifts_users',
            'source' => 'non-db',
            'vname' => 'LBL_SHIFT_USERS_TITLE',
            'module' => 'Users',
            'bean_name' => 'User',
        ],
    ],
    'relationships' => [
    ],
    'uses' => ['basic', 'assignable', 'team_security', 'business_hours'],
];

VardefManager::createVardef('Shifts', 'Shift');

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

$viewdefs['ShiftExceptions']['base']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'start_date',
                    'label' => 'LBL_CALENDAR_START_DATE',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'end_date',
                    'label' => 'LBL_CALENDAR_END_DATE',
                    'default' => true,
                    'enabled' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'shift_exception_type',
                    'label' => 'LBL_TYPE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'timezone',
                    'label' => 'LBL_TIMEZONE',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'all_day',
                    'label' => 'LBL_ALL_DAY',
                    'default' => false,
                    'enabled' => true,
                    'type' => 'all-day',
                    'readonly' => true,
                ],
                [
                    'name' => 'enabled',
                    'label' => 'LBL_ENABLED',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => false,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'default' => false,
                    'enabled' => true,
                    'readonly' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'modified_by_name',
                    'label' => 'LBL_MODIFIED',
                    'default' => false,
                    'enabled' => true,
                    'readonly' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'default' => false,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];

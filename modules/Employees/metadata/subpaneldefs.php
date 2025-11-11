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

$layout_defs['Employees'] = [
    // default subpanel provided by this SugarBean
    'subpanel_setup' => [
        'shifts' => [
            'order' => 31,
            'sort_by' => 'name',
            'sort_order' => 'asc',
            'module' => 'Shifts',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'shifts',
            'refresh_page' => 1,
            'top_buttons' => [],
            'title_key' => 'LBL_SHIFTS_SUBPANEL_TITLE',
        ],
        'shifts_exceptions' => [
            'order' => 32,
            'sort_by' => 'name',
            'sort_order' => 'asc',
            'module' => 'ShiftExceptions',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'shift_exceptions',
            'refresh_page' => 1,
            'top_buttons' => [],
            'title_key' => 'LBL_SHIFTS_EXCEPTIONS_SUBPANEL_TITLE',
        ],
    ],
];

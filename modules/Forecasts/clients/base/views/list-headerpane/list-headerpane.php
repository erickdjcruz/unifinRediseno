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

$viewdefs['Forecasts']['base']['view']['list-headerpane'] = [
    'tree' => [
        [
            'type' => 'reportingUsers',
            'acl_action' => 'is_manager',
        ],
    ],
    'timeperiod' => [
        [
            'name' => 'selectedTimePeriod',
            'label' => 'LBL_TIMEPERIOD_NAME',
            'type' => 'timeperiod',
            'css_class' => 'forecastsTimeperiod',
            'dropdown_class' => 'topline-timeperiod-dropdown',
            'dropdown_width' => 'auto',
            'view' => 'edit',
            // options are set dynamically in the view
            'default' => true,
            'enabled' => true,
        ],
    ],
    'header-datapoints' => [
        [
            'name' => 'likely_case',
            'label' => 'LBL_COMMITMENT',
            'type' => 'header-datapoint',
            'click_to_edit' => false,
        ],
        [
            'name' => 'quota',
            'label' => 'LBL_QUOTA',
            'type' => 'header-quotapoint',
        ],
    ],
    'buttons' => [
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];

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
$viewdefs['Users']['base']['view']['copy-content-items'] = [
    'fields' => [
        'dashboards_module_select' => [
            'name' => 'dashboards_module_select',
            'type' => 'module-enum',
            'view_template' => 'edit',
        ],
        'dashboards_select' => [
            'name' => 'dashboards_select',
            'type' => 'hybrid-select',
            'select_module' => 'Dashboards',
            'view_template' => 'edit',
            'placeholder' => 'LBL_SELECT_DASHBOARDS',
        ],
        'filters_select' => [
            'name' => 'filters_select',
            'type' => 'hybrid-select',
            'select_module' => 'Filters',
            'view_template' => 'edit',
            'placeholder' => 'LBL_SELECT_FILTERS',
        ],
    ],
];

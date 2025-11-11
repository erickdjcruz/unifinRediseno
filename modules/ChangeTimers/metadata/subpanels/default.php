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
$subpanel_layout = [
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopCreateButton'],
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'ChangeTimers'],
    ],
    'where' => '',
    'list_fields' => [
        'field_name' => [
            'name' => 'field_name',
            'vname' => 'LBL_FIELD_NAME',
            'default' => true,
        ],
        'value_string' => [
            'name' => 'value_string',
            'vname' => 'LBL_VALUE',
            'default' => true,
        ],
        'from_datetime' => [
            'name' => 'from_datetime',
            'vname' => 'LBL_FROM_DATETIME',
            'default' => true,
        ],
        'to_datetime' => [
            'name' => 'to_datetime',
            'vname' => 'LBL_TO_DATETIME',
            'default' => true,
        ],
        'hours' => [
            'name' => 'hours',
            'vname' => 'LBL_HOURS',
            'default' => true,
        ],
        'business_hours' => [
            'name' => 'business_hours',
            'vname' => 'LBL_BUSINESS_HOURS',
            'default' => true,
        ],
    ],
];

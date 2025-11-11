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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'DataPrivacy'],
    ],
    'where' => '',
    'list_fields' => [
        'dataprivacy_number' => [
            'name' => 'dataprivacy_number',
            'vname' => 'LBL_LIST_NUMBER',
            'default' => true,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_SUBJECT',
            'default' => true,
        ],
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_LIST_TYPE',
            'default' => true,
        ],
        'priority' => [
            'name' => 'priority',
            'vname' => 'LBL_LIST_PRIORITY',
            'default' => true,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_LIST_STATUS',
            'default' => true,
        ],
        'date_due' => [
            'name' => 'date_due',
            'vname' => 'LBL_LIST_DATE_DUE',
            'default' => true,
        ],
        'date_closed' => [
            'name' => 'date_closed',
            'vname' => 'LBL_LIST_DATE_CLOSED',
            'default' => true,
        ],
        'assigned_user_name' => [
            'name' => 'assigned_user_name',
            'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'default' => true,
        ],
    ],
];

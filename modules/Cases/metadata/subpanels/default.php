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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Cases'],
    ],

    'where' => '',

    'fill_in_additional_fields' => true,

    'list_fields' => [
        'case_number' => [
            'vname' => 'LBL_LIST_NUMBER',
            'width' => '6%',
        ],

        'name' => [
            'vname' => 'LBL_LIST_SUBJECT',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '40%',
        ],
        'account_name' => [
            'vname' => 'LBL_LIST_ACCOUNT_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'module' => 'Accounts',
            'width' => '31%',
            'target_record_key' => 'account_id',
            'target_module' => 'Accounts',
        ],
        'status' => [
            'vname' => 'LBL_LIST_STATUS',
            'width' => '10%',
        ],
        'date_entered' => [
            'vname' => 'LBL_LIST_DATE_CREATED',
            'width' => '15%',
        ],
        'assigned_user_name' => [
            'name' => 'assigned_user_name',
            'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'assigned_user_id',
            'target_module' => 'Employees',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Cases',
            'width' => '4%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Cases',
            'width' => '5%',
        ],

    ],
];

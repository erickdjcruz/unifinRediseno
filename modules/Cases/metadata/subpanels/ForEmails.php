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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Cases'],
    ],

    'where' => '',


    'list_fields' => [
        'case_number' => [
            'vname' => 'LBL_LIST_NUMBER',
            'width' => '6%',
        ],

        'name' => [
            'vname' => 'LBL_LIST_SUBJECT',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '30%',
        ],
        'reply_to_status' => [
            'usage' => 'query_only',
            'force_exists' => true,
        ],
        'assigned_user_name' => [
            'vname' => 'LBL_LIST_ASSIGNED',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '30%',
        ],
        'account_name' => [
            'module' => 'Accounts',
            'widget_class' => 'SubPanelDetailViewLink',
            'vname' => 'LBL_LIST_ACCOUNT_NAME',
            'width' => '30%',
        ],
        'status' => [
            'vname' => 'LBL_LIST_STATUS',
            'width' => '10%',
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

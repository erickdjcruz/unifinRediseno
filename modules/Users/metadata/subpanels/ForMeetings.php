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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Users'],
    ],

    'where' => '',


    'list_fields' => [
        'accept_status_name' => [
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'width' => '11%',
            'sortable' => false,
        ],
        'm_accept_status_fields' => [
            'usage' => 'query_only',
        ],
        'accept_status_id' => [
            'usage' => 'query_only',
        ],
        'first_name' => [
            'usage' => 'query_only',
        ],
        'last_name' => [
            'usage' => 'query_only',
        ],
        'name' => [
            'vname' => 'LBL_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_module' => 'Employees',
            'module' => 'Users',
            'width' => '25%',
        ],
        'user_name' => [
            'vname' => 'LBL_LIST_USER_NAME',
            'width' => '25%',
        ],
        'email' => [
            'vname' => 'LBL_LIST_EMAIL',
            'width' => '25%',
            'widget_class' => 'SubPanelEmailLink',
            'sortable' => false,
        ],
        'phone_work' => [
            'vname' => 'LBL_LIST_PHONE',
            'width' => '21%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButtonMeetings',
            'module' => 'Users',
            'width' => '4%',
            'linked_field' => 'users',
        ],
    ],
];

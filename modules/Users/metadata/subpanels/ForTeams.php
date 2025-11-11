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
        'first_name' => [
            'usage' => 'query_only',
        ],
        'last_name' => [
            'usage' => 'query_only',
        ],
        'name' => [
            'vname' => 'LBL_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'module' => 'Users',
            'width' => '25%',
        ],
        'user_name' => [
            'vname' => 'LBL_LIST_USER_NAME',
            'width' => '25%',
        ],
        'upline' => [
            'vname' => 'LBL_LIST_MEMBERSHIP',
            'width' => '15%',
            'sortable' => false,
        ],
        'email' => [
            'vname' => 'LBL_LIST_EMAIL',
            'width' => '25%',
            'sortable' => false,
            'sortable' => false,
        ],
        'phone_work' => [
            'vname' => 'LBL_LIST_PHONE',
            'width' => '21%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Users',
            'width' => '4%',
            'linked_field' => 'users',
        ],
    ],
];

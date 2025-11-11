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


$layout_defs['Users'] = [
    // default subpanel provided by this SugarBean
    'subpanel_setup' => [
        'holidays' => [
            'order' => 30,
            'sort_by' => 'holiday_date',
            'sort_order' => 'asc',
            'module' => 'Holidays',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'holidays',
            'refresh_page' => 1,
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate', 'view' => 'UsersQuickCreate',],
            ],
            'title_key' => 'LBL_USER_HOLIDAY_SUBPANEL_TITLE',
        ],
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
        'shift_exceptions' => [
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
    'default_subpanel_define' => [
        'subpanel_title' => 'LBL_DEFAULT_SUBPANEL_TITLE',
        'sort_by' => 'name',
        'sort_order' => 'asc',
        'top_buttons' => [
            ['widget_class' => 'SubPanelTopCreateButton'],
            ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Users', 'mode' => 'MultiSelect'],
        ],
        'list_fields' => [
            'Users' => [
                'columns' => [
                    [
                        'name' => 'first_name',
                        'usage' => 'query_only',
                    ],
                    [
                        'name' => 'last_name',
                        'usage' => 'query_only',
                    ],
                    [
                        'name' => 'name',
                        'vname' => 'LBL_LIST_NAME',
                        'widget_class' => 'SubPanelDetailViewLink',
                        'module' => 'Users',
                        'width' => '25%',
                    ],
                    [
                        'name' => 'user_name',
                        'vname' => 'LBL_LIST_USER_NAME',
                        'width' => '25%',
                    ],
                    [
                        'name' => 'email1',
                        'vname' => 'LBL_LIST_EMAIL',
                        'width' => '25%',
                    ],
                    [
                        'name' => 'phone_work',
                        'vname' => 'LBL_LIST_PHONE',
                        'width' => '21%',
                    ],
                    [
                        'name' => 'nothing',
                        'widget_class' => 'SubPanelRemoveButton',
                        'module' => 'Users',
                        'width' => '4%',
                        'linked_field' => 'users',
                    ],
                ],
            ],
        ],
    ],
];
$layout_defs['UserRoles'] = [
    // sets up which panels to show, in which order, and with what linked_fields
    'subpanel_setup' => [
        'aclroles' => [
            'top_buttons' => [['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'ACLRoles', 'mode' => 'MultiSelect'],],
            'order' => 20,
            'sort_by' => 'name',
            'sort_order' => 'asc',
            'module' => 'ACLRoles',
            'refresh_page' => 1,
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'aclroles',
            'add_subpanel_data' => 'role_id',
            'title_key' => 'LBL_ROLES_SUBPANEL_TITLE',
        ],
    ],
];
global $current_user;
if ($current_user->isAdminForModule('Users')) {
    $layout_defs['UserRoles']['subpanel_setup']['aclroles']['subpanel_name'] = 'admin';
} else {
    $layout_defs['UserRoles']['subpanel_setup']['aclroles']['top_buttons'] = [];
}

$layout_defs['UserEAPM'] = [
    'subpanel_setup' => [
        'eapm' => [
            'order' => 30,
            'module' => 'EAPM',
            'sort_order' => 'asc',
            'sort_by' => 'name',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'eapm',
            'add_subpanel_data' => 'assigned_user_id',
            'title_key' => 'LBL_EAPM_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateButton'],
            ],
        ],

    ],
];
$layout_defs['UsersHolidays']['subpanel_setup']['holidays'] = $layout_defs['Users']['subpanel_setup']['holidays'];

//remove the administrator create button holiday for the user admin only
if (!empty($_REQUEST['record'])) {
    $user_id = $_REQUEST['record'];
    $db = DBManagerFactory::getConnection();
    $sql = 'SELECT is_admin FROM users WHERE id = ?';
    $is_admin = $db->executeQuery($sql, [$user_id])->fetchOne();
    if (!is_admin($current_user) && ($current_user->isAdminForModule('Users')) && ($is_admin == 1)) {
        $layout_defs['Users']['subpanel_setup']['holidays']['top_buttons'] = [];
    }
}

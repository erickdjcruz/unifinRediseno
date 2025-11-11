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

$module_name = 'pmse_Project';
$viewdefs[$module_name]['base']['view']['dashlet-processes'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PMSE_PROCESS_DEFINITIONS_DASHLET',
            'description' => 'LBL_PMSE_PROCESS_DEFINITIONS_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => 10,
                'visibility' => 'user',
            ],
            'preview' => [
                'limit' => 10,
                'visibility' => 'user',
            ],
            'filter' => [
                'module' => [
                    //'Accounts',
                    //'Bugs',
                    //'Cases',
                    //'Contacts',
                    'Home',
                    //'Leads',
                    //'Opportunities',
                    //'Prospects',
                    //'RevenueLineItems',
                ],
                'view' => 'record',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'actiondropdown',
                'no_default_action' => true,
                'icon' => 'sicon-plus',
                'buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'createRecord',
                        'params' => [
                            'module' => 'pmse_Project',
                            'link' => '#pmse_Project',
                        ],
                        'label' => 'LNK_PMSE_PROCESS_DEFINITIONS_NEW_RECORD',
                        'acl_action' => 'create',
                        'acl_module' => 'pmse_Project',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'importRecord',
                        'params' => [
                            'module' => 'pmse_Project',
                            'link' => '#pmse_Project/layout/project-import',
                        ],
                        'label' => 'LNK_PMSE_PROCESS_DEFINITIONS_IMPORT_RECORD',
                        'acl_action' => 'create',
                        'acl_module' => 'pmse_Project',
                    ],
                ],
            ],
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ],
//                    array(
//                        'type' => 'dashletaction',
//                        'action' => 'toggleClicked',
//                        'label' => 'LBL_DASHLET_MINIMIZE',
//                        'event' => 'minimize',
//                    ),
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'visibility',
                    'label' => 'LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY',
                    'type' => 'enum',
                    'options' => 'tasks_visibility_options',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'tasks_limit_options',
                ],
            ],
        ],
    ],
//    'filter' => array(
//        array(
//            'name' => 'filter',
//            'label' => 'LBL_FILTER',
//            'type' => 'enum',
//            'options' => 'history_filter_options'
//        ),
//    ),
    'tabs' => [
        [
            'active' => true,
            'filters' => [
                'prj_status' => ['$equals' => 'ACTIVE'],
            ],
            'label' => 'LBL_PMSE_PROCESS_DEFINITIONS_ENABLED',
            'link' => 'pmse_Project',
            'module' => 'pmse_Project',
            'order_by' => 'date_entered:desc',
            'record_date' => 'date_entered',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:designer:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DESIGN',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:delete-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DELETE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon sicon-download',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:download:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_EXPORT',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-hide',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:disable-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DISABLE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-info',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:description-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_DESCRIPTION',
                    'acl_action' => 'edit',
                ],
            ],
            'fields' => [
                'name',
                'prj_module',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'description',
            ],
        ],
        [
            'filters' => [
                'prj_status' => ['$equals' => 'INACTIVE'],
            ],
            'label' => 'LBL_PMSE_PROCESS_DEFINITIONS_DISABLED',
            'link' => 'pmse_Project',
            'module' => 'pmse_Project',
            'order_by' => 'date_entered:desc',
            'record_date' => 'date_entered',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:designer:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DESIGN',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:delete-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DELETE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon sicon-download',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:download:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_EXPORT',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-preview',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:enable-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_ENABLE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-info',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-processes:description-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_DESCRIPTION',
                    'acl_action' => 'edit',
                ],
            ],
            'fields' => [
                'name',
                'prj_module',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'description',
            ],
        ],
    ],
    'visibility_labels' => [
        'user' => 'LBL_ACTIVE_TASKS_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_ACTIVE_TASKS_DASHLET_GROUP_BUTTON_LABEL',
    ],
];

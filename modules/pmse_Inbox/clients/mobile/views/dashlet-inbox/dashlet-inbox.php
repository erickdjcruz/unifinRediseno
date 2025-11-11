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

$module_name = 'pmse_Inbox';
$viewdefs[$module_name]['mobile']['view']['dashlet-inbox'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PMSE_PROCESSES_DASHLET',
            'description' => 'LBL_PMSE_PROCESSES_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => 10,
                'date' => 'true',
                'visibility' => 'user',
            ],
            'preview' => [
                'limit' => 10,
                'date' => 'true',
                'visibility' => 'user',
            ],
            'filter' => [
                'module' => [
                    'Home',
                ],
                'view' => 'record',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
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
    'tabs' => [
        [
            'active' => true,
            'filter_applied_to' => 'in_time',
            'filters' => [
                'assignment_method' => 'static',
                'visibility' => 'regular_user',
            ],
            'label' => 'LBL_PMSE_MY_PROCESSES',
            'link' => 'pmse_Inbox',
            'module' => 'pmse_Inbox',
            'order_by' => 'date_entered:asc',
            'record_date' => 'cas_due_date',
            'include_child_items' => true,
            'overdue_badge' => [
                'name' => 'cas_due_date',
                'type' => 'overdue-badge',
                'css_class' => 'pull-right',
            ],
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'cas_due_date',
            ],
        ],
        [
            'filter_applied_to' => 'in_time',
            'filters' => [
                'assignment_method' => 'selfservice',
                'visibility' => 'selfservice',
            ],
            'label' => 'LBL_PMSE_SELF_SERVICE_PROCESSES',
            'link' => 'pmse_Inbox',
            'module' => 'pmse_Inbox',
            'order_by' => 'date_entered:asc',
            'record_date' => 'date_entered',
            'include_child_items' => true,
            'fields' => [
                'name',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'cas_due_date',
                'cas_assignment_method',
            ],
        ],
    ],
];

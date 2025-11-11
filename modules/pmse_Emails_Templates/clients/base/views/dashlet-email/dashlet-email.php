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


$viewdefs['pmse_Emails_Templates']['base']['view']['dashlet-email'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PMSE_EMAIL_TEMPLATES_DASHLET',
            'description' => 'LBL_PMSE_EMAIL_TEMPLATES_DASHLET_DESCRIPTION',
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
                    'Home',
                    'pmse_Emails_Templates',
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
                            'module' => 'pmse_Emails_Templates',
                            'link' => '#pmse_Emails_Templates',
                        ],
                        'label' => 'LNK_PMSE_EMAIL_TEMPLATES_NEW_RECORD',
                        'acl_action' => 'create',
                        'acl_module' => 'pmse_Emails_Templates',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'importRecord',
                        'params' => [
                            'module' => 'pmse_Emails_Templates',
                            'link' => '#pmse_Emails_Templates/layout/emailtemplates-import',
                        ],
                        'label' => 'LNK_PMSE_EMAIL_TEMPLATES_IMPORT_RECORD',
                        'acl_action' => 'importRecord',
                        'acl_module' => 'pmse_Emails_Templates',
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
    'filter' => [
        [
            'name' => 'filter',
            'label' => 'LBL_FILTER',
            'type' => 'enum',
            'options' => 'history_filter_options',
        ],
    ],
    'tabs' => [
        [
            'active' => true,
            'filters' => [],
            'label' => 'LBL_PMSE_EMAIL_TEMPLATES_DASHLET',
            'link' => 'LBL_PMSE_EMAIL_TEMPLATES_DASHLET',
            'module' => 'pmse_Emails_Templates',
            'order_by' => 'date_entered:desc',
            'record_date' => 'date_entered',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-email:edit:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_EDIT_BUTTON',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-email:delete-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DELETE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon sicon-download',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-email:download:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_EXPORT',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-info',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-email:description-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_DESCRIPTION',
                    'acl_action' => 'edit',
                ],
            ],
            'fields' => [
                'name',
                'base_module',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'description',
            ],
        ],
    ],
];

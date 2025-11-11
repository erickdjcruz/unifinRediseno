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

$module_name = 'pmse_Business_Rules';
$viewdefs[$module_name]['base']['view']['dashlet-businessrules'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PMSE_BUSINESS_RULES_DASHLET',
            'description' => 'LBL_PMSE_BUSINESS_RULES_DASHLET_DESCRIPTION',
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
                    'pmse_Business_Rules',

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
                            'module' => 'pmse_Business_Rules',
                            'link' => '#pmse_Business_Rules',
                        ],
                        'label' => 'LNK_PMSE_BUSINESS_RULES_NEW_RECORD',
                        'acl_action' => 'create',
                        'acl_module' => 'pmse_Business_Rules',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'importRecord',
                        'params' => [
                            'module' => 'pmse_Business_Rules',
                            'link' => '#pmse_Business_Rules/layout/businessrules-import',
                        ],
                        'label' => 'LNK_PMSE_BUSINESS_RULES_IMPORT_RECORD',
                        'acl_module' => 'pmse_Business_Rules',
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

    'tabs' => [
        [
            'active' => true,
            'filters' => [
                'rst_type' => ['$not_in' => ['multiple']],
            ],
            'label' => 'LBL_PMSE_BUSINESS_RULES_SINGLE_HIT',
            'link' => 'pmse_Business_Rules',
            'module' => 'pmse_Business_Rules',
            'order_by' => 'date_entered:desc',
            'record_date' => 'date_entered',
            'row_actions' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-edit',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-businessrules:businessRulesLayout:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_EDIT_BUTTON',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-close',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-businessrules:delete-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_DELETE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon sicon-download',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-businessrules:download:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_PMSE_LABEL_EXPORT',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-info',
                    'css_class' => 'btn btn-mini',
                    'event' => 'dashlet-businessrules:description-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_DESCRIPTION',
                    'acl_action' => 'edit',
                ],
            ],
            'fields' => [
                'name',
                'rst_module',
                'assigned_user_name',
                'assigned_user_id',
                'date_entered',
                'description',
            ],
        ],
    ],
];

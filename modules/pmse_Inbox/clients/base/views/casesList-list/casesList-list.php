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
$viewdefs[$module_name]['base']['view']['casesList-list'] = [
    'template' => 'flex-list',
    'favorite' => false,
    'following' => false,
    'selection' => [],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'icon' => 'sicon-preview',
                'event' => 'list:preview:fire',
                'css_class' => 'overflow-visible',
                'tooltip' => 'Status',
            ],
            [
                'type' => 'rowaction',
                'name' => 'History',
                'label' => 'LBL_PMSE_LABEL_HISTORY',
                'event' => 'case:history',
                'css_class' => 'overflow-visible',
            ],
            [
                'type' => 'rowaction',
                'name' => 'viewNotes',
                'label' => 'LBL_PMSE_LABEL_NOTES',
                'event' => 'case:notes',
                'css_class' => 'overflow-visible',
            ],
            [
                'type' => 'reassignbutton',
                'name' => 'reassignButton',
                'label' => 'LBL_PMSE_LABEL_REASSIGN',
                'event' => 'case:reassign',
                'css_class' => 'overflow-visible',
            ],
            [
                'type' => 'executebutton',
                'name' => 'executeButton',
                'label' => 'LBL_PMSE_LABEL_EXECUTE',
                'event' => 'case:execute',
            ],
            [
                'type' => 'cancelcasebutton',
                'name' => 'cancelButton',
                'label' => 'LBL_PMSE_LABEL_CANCEL',
                'event' => 'list:cancelCase:fire',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'cas_id',
                    'label' => 'LBL_CAS_ID',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
                [
                    'name' => 'pro_title',
                    'label' => 'LBL_PROCESS_DEFINITION_NAME',
                    'type' => 'pmse-link',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'cas_title',
                    'label' => 'LBL_RECORD_NAME',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'pmse-link',
                    'link' => true,
                    'sortable' => false,
                ],
                [
                    'name' => 'cas_status',
                    'label' => 'LBL_STATUS',
                    'type' => 'event-status-pmse',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'prj_run_order',
                    'label' => 'LBL_PROJECT_RUN_ORDER',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'cas_create_date',
                    'readonly' => true,
                ],
                [
                    'label' => 'LBL_OWNER',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'assigned_user_full_name',
                    'readonly' => true,
                    'link' => false,
                ],
                [
                    'name' => 'cas_user_id_full_name',
                    'label' => 'LBL_ACTIVITY_OWNER',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
                [
                    'name' => 'prj_user_id_full_name',
                    'label' => 'LBL_PROCESS_OWNER',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
            ],
        ],
    ],
    'orderBy' => [
        // Default sort for cases list view
        'field' => 'cas_id',
        'direction' => 'desc',
    ],
];

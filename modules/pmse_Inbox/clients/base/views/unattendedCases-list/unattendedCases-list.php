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
$viewdefs[$module_name]['base']['view']['unattendedCases-list'] = [
    'template' => 'flex-list',
    'favorite' => false,
    'following' => false,
    'selection' => [
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'label' => 'LBL_PMSE_LABEL_REASSIGN',
                'event' => 'list:reassign:fire',
                'acl_action' => 'view',
            ],
        ],
    ],
    'last_state' => [
        'id' => 'record-list',
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
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                    'type' => 'pmse-link',
                ],
                [
                    'name' => 'cas_title',
                    'label' => 'LBL_RECORD_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                    'type' => 'pmse-link',
                    'sortable' => false,
                ],
                [
                    'name' => 'cas_status',
                    'label' => 'LBL_STATUS',
                    'default' => false,
                    'enabled' => false,
                    'link' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_OWNER',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
                [
                    'name' => 'cas_user_full_name',
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
                [
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_entered',
                    'readonly' => true,
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'cas_id',
        'direction' => 'desc',
    ],
];

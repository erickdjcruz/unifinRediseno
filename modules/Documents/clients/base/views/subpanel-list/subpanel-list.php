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
$viewdefs['Documents']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'document_name',
                    'label' => 'LBL_LIST_DOCUMENT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'filename',
                    'label' => 'LBL_LIST_FILENAME',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'category_id',
                    'label' => 'LBL_LIST_CATEGORY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'doc_type',
                    'label' => 'LBL_LIST_DOC_TYPE',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'status_id',
                    'label' => 'LBL_LIST_STATUS',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'active_date',
                    'label' => 'LBL_LIST_ACTIVE_DATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'is_shared',
                    'label' => 'LBL_IS_SHARED',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
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
                'icon' => 'sicon-edit',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'unlink-action',
                'name' => 'unlink_button',
                'icon' => 'sicon-unlink',
                'label' => 'LBL_UNLINK_BUTTON',
            ],
            [
                'type' => 'send-docusign',
                'name' => 'send-docusign',
                'icon' => 'sicon-preview',
                'label' => 'LBL_SEND_TO_DOCUSIGN_BUTTON',
                'event' => 'list:senddocusignrow:fire',
            ],
            [
                'type' => 'send-docusign',
                'name' => 'send-docusign-template',
                'icon' => 'sicon-preview',
                'label' => 'LBL_SEND_TO_DOCUSIGN_TEMPLATE_BUTTON',
                'event' => 'list:senddocusigntemplaterow:fire',
            ],
        ],
    ],
];

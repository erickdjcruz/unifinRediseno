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
$viewdefs['Quotes']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_LIST_QUOTE_NAME',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'target_record_key' => 'account_id',
                    'target_module' => 'Accounts',
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'account_name',
                    'sortable' => false,
                    'readonly' => true,
                ],
                [
                    'label' => 'LBL_LIST_AMOUNT_USDOLLAR',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'total_usdollar',
                    'readonly' => true,
                ],
                [
                    'name' => 'date_quote_expected_closed',
                    'label' => 'LBL_LIST_DATE_QUOTE_EXPECTED_CLOSED',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'target_record_key' => 'assigned_user_id',
                    'target_module' => 'Employees',
                    'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                ],
            ],
        ],
    ],
    // Disable inline edit for now because of INT-197
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
                'acl_action' => 'edit',
                'event' => 'list:editrow:fire',
            ],
            [
                'type' => 'pdfaction',
                'name' => 'download-pdf',
                'label' => 'LBL_PDF_VIEW',
                'action' => 'download',
                'acl_action' => 'view',
            ],
            [
                'type' => 'unlink-action',
                'name' => 'unlink_button',
                'icon' => 'sicon-trash',
                'label' => 'LBL_UNLINK_BUTTON',
            ],
        ],
    ],
];

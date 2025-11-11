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
$viewdefs['Emails']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'from_collection',
                    'type' => 'from',
                    'label' => 'LBL_LIST_FROM_ADDR',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'sortable' => false,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'name',
                    'enabled' => true,
                    'default' => true,
                    'link' => 'true',
                    'readonly' => true,
                    'width' => 'xlarge',
                    'related_fields' => [
                        'total_attachments',
                        'state',
                    ],
                ],
                [
                    'name' => 'state',
                    'label' => 'LBL_LIST_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_sent',
                    'label' => 'LBL_LIST_DATE_COLUMN',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'target_record_key' => 'assigned_user_id',
                    'target_module' => 'Employees',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'parent_name',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                ],
                [
                    'name' => 'mailbox_name',
                    'enabled' => true,
                    'default' => false,
                    'link' => false,
                    'sortable' => false,
                ],
            ],
        ],
    ],
];

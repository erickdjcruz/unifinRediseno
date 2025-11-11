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
$viewdefs['Emails']['mobile']['view']['edit'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    /* Fetch the following fields along with other fields displayed on the list view */
                    'related_fields' => [
                        'my_favorite',
                        'following',
                        'mailbox_name',
                        'total_attachments',
                    ],
                ],
                [
                    'name' => 'date_sent',
                    'label' => 'LBL_DATE',
                ],
                [
                    'name' => 'from_collection',
                    'type' => 'email-sender',
                    'label' => 'LBL_FROM',
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'address_type',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'to_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_TO_ADDRS',
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'address_type',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'cc_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_CC',
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'address_type',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'bcc_collection',
                    'type' => 'email-recipients',
                    'label' => 'LBL_BCC',
                    'max_num' => -1,
                    'fields' => [
                        'email_address_id',
                        'email_address',
                        'address_type',
                        'parent_type',
                        'parent_id',
                        'parent_name',
                        'invalid_email',
                        'opt_out',
                    ],
                ],
                [
                    'name' => 'attachments_collection',
                    'type' => 'email-attachments',
                    'label' => 'LBL_ATTACHMENTS',
                    'max_num' => -1,
                    'fields' => [
                        'filename',
                        'file_size',
                        'file_source',
                        'file_mime_type',
                        'file_ext',
                        'upload_id',
                    ],
                ],
                [
                    'name' => 'description_html',
                    'type' => 'htmleditable',
                    'label' => 'LBL_EMAIL_BODY',
                    'related_fields' => [
                        'description',
                    ],
                ],
                [
                    'name' => 'state',
                    'label' => 'LBL_LIST_STATUS',
                ],
                'parent_name',
                'assigned_user_name',
                'team_name',
                'tag',
            ],
        ],
    ],
];

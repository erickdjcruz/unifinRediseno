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

return [
    'metadata' => [
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_EMAILS',
                                'display_columns' => [
                                    'from_collection',
                                    'name',
                                    'state',
                                    'assigned_user_name',
                                    'parent_name',
                                    'mailbox_name',
                                ],
                                'filter_id' => 'assigned_to_me',
                            ],
                            'context' => [
                                'module' => 'Emails',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_NOTES_DASHLETNAME',
                                'display_columns' => [
                                    'name',
                                    'contact_name',
                                    'parent_name',
                                    'created_by_name',
                                    'date_modified',
                                    'date_entered',
                                ],
                                'filter_id' => 'assigned_to_me',
                            ],
                            'context' => [
                                'module' => 'Notes',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_EMAILS_RECORD_DASHBOARD',
];

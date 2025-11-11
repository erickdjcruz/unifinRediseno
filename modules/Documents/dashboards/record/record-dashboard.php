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
                'width' => 12,
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Documents',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Documents',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Documents',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Documents',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Documents',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'accounts',
                                        'module' => 'Accounts',
                                        'limit' => 5,
                                        'fields' => [
                                            'name',
                                            'billing_address_city',
                                            'billing_address_country',
                                            'phone_office',
                                            'assigned_user_name',
                                            'email',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                    ],
                                ],
                                'tab_list' => [
                                    'accounts',
                                ],
                            ],
                            'context' => [
                                'module' => 'Documents',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_DOCUMENTS',
                                'display_columns' => [
                                    'document_name',
                                    'filename',
                                    'doc_type',
                                    'category',
                                    'last_rev_create_date',
                                ],
                                'filter_id' => 'assigned_to_me',
                            ],
                            'context' => [
                                'module' => 'Documents',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Documents',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'related_docs',
                                        'module' => 'Documents',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Documents',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'name' => 'LBL_DOCUMENTS_RECORD_DASHBOARD',
];

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
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Contracts',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Contracts',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'opportunities',
                                        'module' => 'Opportunities',
                                    ],
                                ],
                                'tab_list' => [
                                    'opportunities',
                                ],
                            ],
                            'context' => [
                                'module' => 'Contracts',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'commentlog-dashlet',
                                'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'Contracts',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contracts_documents',
                                        'module' => 'Documents',
                                        'limit' => 5,
                                        'fields' => [
                                            'document_name',
                                            'filename',
                                            'category_id',
                                            'doc_type',
                                            'status_id',
                                            'active_date',
                                        ],
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'notes',
                                        'module' => 'Notes',
                                        'limit' => 5,
                                        'fields' => [
                                            'name',
                                            'contact_name',
                                            'parent_name',
                                            'created_by_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts',
                                        'module' => 'Contacts',
                                        'limit' => 5,
                                        'fields' => [
                                            'name',
                                            'title',
                                            'account_name',
                                            'email',
                                            'phone_work',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'quotes',
                                        'module' => 'Quotes',
                                        'limit' => 5,
                                        'fields' => [
                                            'quote_num',
                                            'name',
                                            'billing_account_name',
                                            'quote_stage',
                                            'total',
                                            'total_usdollar',
                                            'date_quote_expected_closed',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                    ],
                                ],
                                'tab_list' => [
                                    'contracts_documents',
                                    'notes',
                                    'contacts',
                                    'quotes',
                                ],
                            ],
                            'context' => [
                                'module' => 'Contracts',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'purchase-history',
                                'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
                                'linked_account_field' => 'account_id',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_CONTRACTS_RECORD_DASHBOARD',
];

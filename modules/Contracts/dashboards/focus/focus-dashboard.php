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
    'name' => 'LBL_CONTRACTS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64eda38-13cb-11eb-9399-acde48001122',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    // Row 1
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Contracts',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Contracts',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Contracts',
                            ],
                            'width' => 6,
                        ],
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
                            'width' => 6,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Contracts',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'accounts',
                                        'module' => 'Accounts',
                                    ],
                                ],
                                'tab_list' => [
                                    'accounts',
                                ],
                            ],
                            'context' => [
                                'module' => 'Contracts',
                            ],
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'Contracts',
                                'tabs' => [
                                    [
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contracts_documents',
                                        'module' => 'Documents',
                                        'type' => 'list',
                                        'fields' => [
                                            'document_name',
                                            'filename',
                                            'category_id',
                                            'doc_type',
                                            'status_id',
                                            'active_date',
                                        ],
                                        'limit' => 5,
                                        'relate' => true,
                                    ],
                                    [
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'notes',
                                        'module' => 'Notes',
                                        'type' => 'list',
                                        'fields' => [
                                            'name',
                                            'contact_name',
                                            'parent_name',
                                            'created_by_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'limit' => 5,
                                        'relate' => true,
                                    ],
                                    [
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts',
                                        'module' => 'Contacts',
                                        'type' => 'list',
                                        'fields' => [
                                            'name',
                                            'title',
                                            'account_name',
                                            'email',
                                            'phone_mobile',
                                            'phone_work',
                                            'phone_other',
                                            'assistant_phone',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'limit' => 5,
                                        'relate' => true,
                                    ],
                                    [
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'quotes',
                                        'module' => 'Quotes',
                                        'type' => 'list',
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
                                        'limit' => 5,
                                        'relate' => true,
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
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

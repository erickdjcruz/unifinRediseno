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
    'name' => 'LBL_PROSPECTS_LISTS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f94be-13cb-11eb-b93f-acde48001122',
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
                                'module' => 'ProspectLists',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'ProspectLists',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'ProspectLists',
                            ],
                            'width' => 6,
                        ],
                        [
                            'context' => [
                                'module' => 'ProspectLists',
                            ],
                            'view' => [
                                'label' => 'LBL_RELATED_ACCOUNTS',
                                'type' => 'dashablerecord',
                                'module' => 'ProspectLists',
                                'tabs' => [
                                    [
                                        'type' => 'list',
                                        'module' => 'Accounts',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'accounts',
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
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                ],
                                'tab_list' => [
                                    'accounts',
                                ],
                                'base_module' => 'ProspectLists',
                            ],
                            'width' => 6,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'context' => [
                                'module' => 'ProspectLists',
                            ],
                            'view' => [
                                'label' => 'LBL_RELATED_CONTACTS',
                                'type' => 'dashablerecord',
                                'module' => 'ProspectLists',
                                'tabs' => [
                                    [
                                        'type' => 'list',
                                        'module' => 'Contacts',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts',
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
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                ],
                                'tab_list' => [
                                    'contacts',
                                ],
                                'base_module' => 'ProspectLists',
                            ],
                            'width' => 6,
                        ],
                        [
                            'context' => [
                                'module' => 'ProspectLists',
                            ],
                            'view' => [
                                'label' => 'LBL_RELATED_LEADS_TARGETS',
                                'type' => 'dashablerecord',
                                'module' => 'ProspectLists',
                                'tabs' => [
                                    [
                                        'type' => 'list',
                                        'module' => 'Leads',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'leads',
                                        'fields' => [
                                            'name',
                                            'status',
                                            'account_name',
                                            'phone_work',
                                            'email',
                                            'assigned_user_name',
                                            'date_modified',
                                            'date_entered',
                                        ],
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                    [
                                        'type' => 'list',
                                        'module' => 'Prospects',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'prospects',
                                        'fields' => [
                                            'name',
                                            'title',
                                            'email',
                                            'phone_work',
                                            'date_entered',
                                        ],
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                ],
                                'tab_list' => [
                                    'leads',
                                    'prospects',
                                ],
                                'base_module' => 'ProspectLists',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

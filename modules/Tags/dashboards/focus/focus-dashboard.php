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
    'name' => 'LBL_TAGS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f864a-13cb-11eb-8283-acde48001122',
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
                                'module' => 'Tags',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Tags',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Tags',
                            ],
                            'width' => 6,
                            'height' => 8,
                        ],
                        [
                            'view' => [
                                'label' => 'LBL_MY_FAVORITE_TAGS',
                                'type' => 'dashablelist',
                                'display_columns' => [
                                    'name',
                                    'created_by_name',
                                    'assigned_user_name',
                                    'date_modified',
                                    'date_entered',
                                ],
                                'module' => 'Tags',
                                'filter_id' => 'favorites',
                            ],
                            'context' => [
                                'module' => 'Tags',
                            ],
                            'width' => 6,
                            'height' => 16,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'context' => [
                                'module' => 'Tags',
                            ],
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'Tags',
                                'tabs' => [
                                    [
                                        'type' => 'list',
                                        'module' => 'Accounts',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'accounts_link',
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
                                        'limit' => 5,
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                    [
                                        'type' => 'list',
                                        'module' => 'Contacts',
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'contacts_link',
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
                                        'limit' => 5,
                                        'relate' => true,
                                        'include_child_items' => false,
                                    ],
                                ],
                                'tab_list' => [
                                    'accounts_link',
                                    'contacts_link',
                                ],
                                'base_module' => 'Tags',
                            ],
                            'width' => 6,
                            'height' => 8,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

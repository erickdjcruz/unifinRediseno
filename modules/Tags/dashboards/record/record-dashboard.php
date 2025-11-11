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
        'dashlets' => [
            [
                'view' => [
                    'type' => 'dashablerecord',
                    'label' => 'LBL_RELATED_RECORDS',
                    'module' => 'Tags',
                    'tabs' => [
                        [
                            'active' => true,
                            'label' => 'LBL_MODULE_NAME',
                            'link' => 'accounts_link',
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
                        [
                            'active' => false,
                            'label' => 'LBL_MODULE_NAME',
                            'link' => 'contacts_link',
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
                    ],
                    'tab_list' => [
                        'accounts_link',
                        'contacts_link',
                    ],
                    'base_module' => 'Tags',
                ],
                'context' => [
                    'module' => 'Tags',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'dashablelist',
                    'label' => 'LBL_MY_FAVORITE_TAGS',
                    'display_columns' => [
                        'name',
                        'created_by_name',
                        'assigned_user_name',
                        'date_modified',
                        'date_entered',
                    ],
                    'filter_id' => 'favorites',
                ],
                'context' => [
                    'module' => 'Tags',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            [
                'view' => [
                    'type' => 'dashablelist',
                    'label' => 'LBL_MY_TAGS',
                    'display_columns' => [
                        'name',
                        'created_by_name',
                        'assigned_user_name',
                        'date_modified',
                        'date_entered',
                    ],
                    'filter_id' => 'assigned_to_me',
                ],
                'context' => [
                    'module' => 'Tags',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 12,
            ],
        ],
    ],
    'name' => 'LBL_TAGS_RECORD_DASHBOARD',
];

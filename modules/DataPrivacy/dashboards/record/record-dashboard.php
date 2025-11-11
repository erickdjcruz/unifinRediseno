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
                            [
                                'view' => [
                                    'type' => 'activity-timeline',
                                    'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                                ],
                                'context' => [
                                    'module' => 'DataPrivacy',
                                ],
                                'width' => 12,
                            ],
                        ],
                        [
                            'view' => [
                                'type' => 'marked-for-erasure-dashlet',
                                'label' => 'LBL_MARKED_FOR_ERASURE_TITLE',
                                'custom_toolbar' => [
                                    'buttons' => [
                                        [
                                            'dropdown_buttons' => [
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'editClicked',
                                                    'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                                                ],
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'removeClicked',
                                                    'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'DataPrivacy',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME',
                                        'link' => 'leads',
                                        'module' => 'Leads',
                                        'limit' => 5,
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
                                        'link' => 'prospects',
                                        'module' => 'Prospects',
                                        'limit' => 5,
                                        'fields' => [
                                            'name',
                                            'title',
                                            'email',
                                            'phone_work',
                                            'date_entered',
                                        ],
                                    ],
                                    [
                                        'active' => false,
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
                                    'leads',
                                    'contacts',
                                    'prospects',
                                    'accounts',
                                ],
                            ],
                            'context' => [
                                'module' => 'DataPrivacy',
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
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_DATA_PRIVACY_RECORD_DASHBOARD',
];

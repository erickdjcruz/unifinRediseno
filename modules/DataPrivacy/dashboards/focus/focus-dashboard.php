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
    'name' => 'LBL_DATA_PRIVACY_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64ede5c-13cb-11eb-b30a-acde48001122',
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
                                'module' => 'DataPrivacy',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'DataPrivacy',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'DataPrivacy',
                            ],
                            'width' => 6,
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
                            'width' => 6,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'label' => 'LBL_RELATED_RECORDS',
                                'module' => 'DataPrivacy',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'leads',
                                        'module' => 'Leads',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'contacts',
                                        'module' => 'Contacts',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'prospects',
                                        'module' => 'Prospects',
                                    ],
                                    [
                                        'active' => false,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => 'accounts',
                                        'module' => 'Accounts',
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
                            'width' => 6,
                        ],
                        [
                            'view' => [
                                'type' => 'commentlog-dashlet',
                                'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

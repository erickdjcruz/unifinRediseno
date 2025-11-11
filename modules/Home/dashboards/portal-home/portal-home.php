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
    'name' => 'LBL_PORTAL_HOME',
    'id' => '0ca2d773-0bb3-4bf3-ae43-68569968af57',
    'metadata' => [
        'css_class' => 'portal-home-dashboard',
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_PORTAL_DASHBOARD_ALL_OPEN_CASES',
                                'filter_id' => 'open_issues',
                                'limit' => 15,
                                'custom_toolbar' => [
                                    'buttons' => [
                                        [
                                            'dropdown_buttons' => [
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'editClicked',
                                                    'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                                                    'name' => 'edit_button',
                                                ],
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'removeClicked',
                                                    'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                                    'name' => 'remove_button',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Cases',
                            ],
                            'width' => 4,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_PORTAL_DASHBOARD_RECENT_CASES',
                                'filter_id' => 'recently_resolved',
                                'limit' => 15,
                                'custom_toolbar' => [
                                    'buttons' => [
                                        [
                                            'dropdown_buttons' => [
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'editClicked',
                                                    'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                                                    'name' => 'edit_button',
                                                ],
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'removeClicked',
                                                    'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                                    'name' => 'remove_button',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Cases',
                            ],
                            'width' => 4,
                        ],
                        [
                            'view' => [
                                'type' => 'dashlet-nestedset-list',
                                'label' => 'LBL_DASHLET_CATEGORIES_NAME',
                                'data_provider' => 'Categories',
                                'config_provider' => 'KBContents',
                                'root_name' => 'category_root',
                                'extra_provider' => [
                                    'module' => 'KBContents',
                                    'field' => 'category_id',
                                ],
                                'custom_toolbar' => [
                                    'buttons' => [
                                        [
                                            'dropdown_buttons' => [
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'editClicked',
                                                    'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                                                    'name' => 'edit_button',
                                                ],
                                                [
                                                    'type' => 'dashletaction',
                                                    'action' => 'removeClicked',
                                                    'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                                    'name' => 'remove_button',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'KBContents',
                            ],
                            'width' => 4,
                            'height' => 8,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
];

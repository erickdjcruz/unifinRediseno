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

$viewdefs['base']['layout']['sidebar-nav'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'sidebar-nav-item-group',
                'name' => 'sidebar-nav-item-group-controls',
                'css_class' => 'grow-0 shrink-0',
                'components' => [
                    [
                        'view' => [
                            'name' => 'expand-menu',
                            'type' => 'sidebar-nav-item-expand',
                            'icon' => 'sicon-hamburger-lg',
                            'event' => 'sidebar-nav:expand:toggle',
                            'track' => 'click:sidebar-nav',
                        ],
                    ],
                ],
            ],
        ],
        [
            'layout' => [
                'type' => 'sidebar-nav-item-group',
                'name' => 'sidebar-nav-item-group-top',
                'css_class' => 'grow-0 shrink-0',
                'components' => [
                    [
                        'view' => 'sidebar-nav-item-module',
                        'context' => [
                            'module' => 'Home',
                        ],
                    ],
                    [
                        'view' => [
                            'name' => 'quick-create',
                            'type' => 'sidebar-nav-item-quickcreate',
                            'icon' => 'sicon-plus-lg',
                            'label' => 'LBL_QUICK_CREATE_TITLE',
                            'secondary-action' => false,
                            'flyoutComponents' => [
                                [
                                    'view' => 'sidebar-nav-flyout-header',
                                    'title' => 'LBL_QUICK_CREATE_TITLE',
                                ],
                                [
                                    'view' => 'sidebar-quickcreate',
                                ],
                            ],
                            'track' => 'click:quick-create',
                        ],
                    ],
                ],
            ],
        ],
        [
            'layout' => [
                'type' => 'sidebar-nav-item-group-modules',
                'css_class' => 'flex-grow flex-shrink min-h-[2.5rem]',
            ],
        ],
        [
            'layout' => [
                'type' => 'sidebar-nav-item-group',
                'name' => 'sidebar-nav-item-group-bottom',
                'css_class' => 'grow-0 shrink-0',
                'components' => [
                    [
                        'view' => [
                            'name' => 'doc-merge',
                            'type' => 'sidebar-nav-item-docmerge',
                            'icon' => 'sicon-document-lg',
                            'label' => 'LBL_DOCUMENT_MERGE_FOOTER',
                        ],
                        'context' => [
                            'module' => 'DocumentMerges',
                        ],
                    ],
                    [
                        'view' => [
                            'name' => 'omnichannel',
                            'type' => 'sidebar-nav-item-omnichannel',
                            'label' => 'LBL_OMNICHANNEL',
                            'showClose' => false,
                            'flyoutComponents' => [
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'css_class' => 'min-w-40',
                                        'actions' => [
                                            [
                                                'event' => 'omnichannel:config',
                                                'label' => 'LBL_OMNICHANNEL_CONFIG_LAYOUT',
                                                'icon' => 'sicon-settings',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'view' => [
                            'name' => 'help',
                            'type' => 'sidebar-nav-item-help',
                            'icon' => 'sicon-help-lg',
                            'label' => 'LBL_HELP',
                            'secondary-action' => false,
                            'route' => false,
                            'flyoutComponents' => [
                                [
                                    'layout' => 'sidebar-help',
                                ],
                            ],
                            'track' => 'click:help',
                        ],
                    ],
                    [
                        'view' => [
                            'name' => 'sugar-logo',
                            'type' => 'sidebar-nav-item-sugarcrm',
                            'icon' => 'sicon-sugar-logo-24',
                            'label' => 'LBL_GOTO_SUGARCRM_COM',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

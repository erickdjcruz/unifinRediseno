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

$viewdefs['base']['layout']['recipients-list-composite'] = [
    'css_class'=> 'flex-list-layout flex flex-col group/records h-full overflow-hidden',
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'css_class' => 'h-full',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8 flex flex-col overflow-hidden h-[calc(100%-55px)]',
                            'components' => [
                                [
                                    'view' => 'docusign-recipient-selection-headerpane',
                                ],
                                [
                                    'view' => 'mixed-selection-list-context',
                                ],
                                [
                                    'layout' => [
                                        'css_class' => 'flex flex-col h-full',
                                        'type' => 'filterpanel',
                                        'availableToggles' => [],
                                        'filter_options' => [
                                            'stickiness' => false,
                                        ],
                                        'components' => [
                                            [
                                                'layout' => 'filter',
                                                'loadModule' => 'Filters',
                                                'xmeta' => [
                                                    'components' => [
                                                        [
                                                            'view' => 'filter-module-dropdown-selection-list',
                                                        ],
                                                        [
                                                            'view' => 'filter-filter-dropdown',
                                                        ],
                                                        [
                                                            'view' => 'filter-quicksearch',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            [
                                                'view' => 'filter-rows',
                                            ],
                                            [
                                                'view' => 'filter-actions',
                                            ],
                                            [
                                                'layout' => [
                                                    'css_class' => 'paginated-flex-list',
                                                    'components' => [
                                                        [
                                                            'view' => 'docusign-recipients-multi-selection-list',
                                                            'primary' => true,
                                                        ],
                                                        [
                                                            'view' => [
                                                                'name' => 'list-pagination',
                                                                'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

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

$viewdefs['base']['layout']['multi-selection-list-link'] = [
    'css_class' => 'flex-list-layout flex flex-col group/records h-full overflow-hidden',
    'type' => 'multi-selection-list',
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
                                    'view' => 'selection-headerpane',
                                ],
                                [
                                    'view' => 'selection-list-context',
                                ],
                                [
                                    'layout' => [
                                        'css_class' => 'multi-selection-flex flex flex-col h-[calc(100%-78px)]',
                                        'type' => 'filterpanel',
                                        'availableToggles' => [],
                                        'filter_options' => [
                                            'stickiness' => false,
                                        ],
                                        'components' => [
                                            [
                                                'layout' => 'filter',
                                                'loadModule' => 'Filters',
                                            ],
                                            [
                                                'view' => 'filter-rows',
                                            ],
                                            [
                                                'view' => 'filter-actions',
                                            ],
                                            [
                                                'view' => 'mass-link',
                                            ],
                                            [
                                                'layout' => [
                                                    'css_class' => 'paginated-flex-list',
                                                    'components' => [
                                                        [
                                                            'view' => 'multi-selection-list-link',
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
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'preview-pane',
                            'css_class' => 'preview-pane',
                            'components' => [
                                [
                                    'layout' => 'preview',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

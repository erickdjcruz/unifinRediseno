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

$viewdefs['DocuSignEnvelopes']['base']['layout']['templates-list-composite'] = [
    'css_class'=> 'flex-list-layout flex templates-list flex-col h-full',
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
                                    'view' => 'template-selection-headerpane',
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
                                                'layout' => 'templates-filter',
                                                'loadModule' => 'Filters',
                                            ],
                                            [
                                                'view' => 'templates-list',
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

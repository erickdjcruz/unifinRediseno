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

$viewdefs['Reports']['base']['layout']['report-side-drawer'] = [
    'css_class' => 'h-full overflow-x-hidden pl-4',
    'components' => [
        [
            'layout' => [
                'type' => 'base',
                'css_class' => 'flex flex-col report-side-drawer overflow-hidden h-full',
                'name' => 'column-holder',
                'components' => [
                    [
                        'view' => 'report-side-drawer-headerpane',
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'row-holder',
                            'css_class' => 'flex flex-row overflow-hidden pb-4 h-[calc(100%-9rem)]',
                            'components' => [
                                [
                                    'layout' => [
                                        'type' => 'base',
                                        'name' => 'list-side',
                                        'css_class' => 'w-3/5 bg-[--primary-content-background] ml-0 rounded-md overflow-y-auto overflow-x-hidden',
                                        'components' => [
                                            [
                                                'view' => 'report-side-drawer-list-headerpane',
                                            ],
                                            [
                                                'layout' => 'drillthrough-list',
                                                'xmeta' => [
                                                    'components' => [
                                                        [
                                                            'view' => 'massupdate',
                                                        ],
                                                        [
                                                            'view' => 'massaddtolist',
                                                        ],
                                                        [
                                                            'view' => 'recordlist',
                                                            'primary' => true,
                                                            'xmeta' => [
                                                                'favorite' => true,
                                                            ],
                                                        ],
                                                        [
                                                            'view' => 'list-bottom',
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
                                        'name' => 'list-side',
                                        'css_class' => 'w-2/5 overflow-y-hidden overflow-x-hidden h-full flex',
                                        'components' => [
                                            [
                                                'layout' => 'report-side-drawer-pane',
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

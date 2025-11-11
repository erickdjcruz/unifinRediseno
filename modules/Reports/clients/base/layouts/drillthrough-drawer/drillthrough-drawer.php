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

$viewdefs['Reports']['base']['layout']['drillthrough-drawer'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8',
                            'components' => [
                                [
                                    'view' => 'drillthrough-headerpane',
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
                            'name' => 'dashboard-pane',
                            'css_class' => 'dashboard-pane',
                            'components' => [
                                [
                                    'layout' => 'drillthrough-pane',
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
                                    'xmeta' => [
                                        'editable' => true,
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

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
$viewdefs['Administration']['base']['layout']['portaltheme-config'] = [
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
                            'css_class' => 'portal-preview main-pane span8',
                            'components' => [
                                [
                                    'view' => 'portaltheme-config-header',
                                ],
                                [
                                    'layout' => 'portal-preview',
                                    'primary' => true,
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'side-pane',
                            'css_class' => 'admin-config',
                            'components' => [
                                [
                                    'view' => 'portaltheme-config',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

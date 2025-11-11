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
$viewdefs['Opportunities']['base']['layout']['create'] = [
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
                            'css_class' => 'main-pane overflow-y-auto span8',
                            'components' => [
                                [
                                    'view' => 'create',
                                ],
                                [
                                    'layout' => 'subpanels-create',
                                ],
                            ],
                        ],
                    ],
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'preview-pane',
                            'components' => [
                                [
                                    'layout' => 'create-preview',
                                ],
                            ],
                        ],
                    ],
                ],
                'last_state' => [
                    'id' => 'create-default',
                ],
            ],
        ],
    ],
];

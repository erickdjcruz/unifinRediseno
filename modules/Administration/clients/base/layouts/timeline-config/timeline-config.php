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
$viewdefs['Administration']['base']['layout']['timeline-config'] = [
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'components' => [
                    [
                        'layout' => [
                            'type' => 'simple',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8 admin-config overflow-y-auto',
                            'components' => [
                                [
                                    'view' => 'timeline-config-header',
                                ],
                                [
                                    'view' => 'timeline-config',
                                    'primary' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

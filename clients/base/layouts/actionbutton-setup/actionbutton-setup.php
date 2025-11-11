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

$viewdefs['base']['layout']['actionbutton-setup'] = [
    'name' => 'actionbutton-setup',
    'type' => 'actionbutton-setup',
    'span' => 12,
    'actions' => [
        'assign-record' => 'LBL_ACTIONBUTTON_ASSIGN_RECORD',
        'compose-email' => 'LBL_ACTIONBUTTON_COMPOSE_EMAIL',
        'create-record' => 'LBL_ACTIONBUTTON_CREATE_RECORD',
        'document-merge' => 'LBL_ACTIONBUTTON_DOCUMENT_MERGE',
        'open-url' => 'LBL_ACTIONBUTTON_OPEN_URL',
        'run-report' => 'LBL_ACTIONBUTTON_RUN_REPORT',
        'update-record' => 'LBL_ACTIONBUTTON_UPDATE_RECORD',
    ],
    'components' => [
        [
            'layout' => [
                'type' => 'default',
                'name' => 'sidebar',
                'last_state' => [
                    'id' => 'create-default',
                ],
                'components' => [
                    [
                        'layout' => [
                            'type' => 'base',
                            'name' => 'main-pane',
                            'css_class' => 'main-pane span8 overflow-y-auto',
                            'components' => [
                                ['view' => 'actionbutton-headerpane'],
                                [
                                    'layout' => [
                                        'type' => 'base',
                                        'name' => 'base',
                                        'components' => [
                                            ['view' => 'actionbutton-tabs'],
                                            [
                                                'layout' => [
                                                    'type' => 'base',
                                                    'name' => 'base',
                                                    'components' => [
                                                        ['view' => 'actionbutton-properties'],
                                                        ['layout' => 'actionbutton-actions'],
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
                            'name' => 'side-pane',
                            'css_class' => 'dashboard ab-dashboard-top',
                            'components' => [
                                ['view' => 'actionbutton-side-headerpane'],
                                [
                                    'layout' => [
                                        'type' => 'base',
                                        'name' => 'base',
                                        'components' => [
                                            ['view' => 'actionbutton-side-tabs'],
                                            ['layout' => 'actionbutton-display-settings'],
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

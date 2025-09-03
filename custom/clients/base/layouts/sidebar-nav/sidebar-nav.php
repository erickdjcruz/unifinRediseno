<?php

global $current_user;
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
                'css_class' => 'flex-grow-0 flex-shrink-0',
                'components' => [
                    [
                        'view' => [
                            'name' => 'quantico-nav-item',
                            'type' => 'sidebar-nav-item-module',
                            'icon' => 'sicon-document-lg',
                            'display_type' => 'abbreviation',
                            'abbreviation' => 'Qu',
                            'label' => 'Quantico',
                            'secondary_action' => true,
                            //'route' => 'bwc/index.php?entryPoint=ListaTareasQuantico&idActiveDirectory=' . $current_user->id_active_directory_c,
                            'route' => 'bwc/index.php?entryPoint=ListaTareasQuantico&idActiveDirectory=AD_REPLACE',
                            'template' => 'sidebar-nav-item',
                            'flyoutComponents' => [
                                // [
                                //     'view' => 'sidebar-nav-flyout-header',
                                //     'title' => 'LBL_GREETINGS_C',
                                // ],
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'actions' => [
                                            [
                                                //'route' => '#bwc/index.php?entryPoint=ListaTareasQuantico&idActiveDirectory='.$current_user->id_active_directory_c,
                                                'route' => '#bwc/index.php?entryPoint=ListaTareasQuantico&idActiveDirectory=AD_REPLACE',
                                                'label' => 'Lista de Tareas',
                                                'icon' => 'sicon-data-table',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'actions' => [
                                            [
                                                //'route' => '#bwc/index.php?entryPoint=operacionesCRM&idActiveDirectory=' . $current_user->id_active_directory_c,
                                                'route' => '#bwc/index.php?entryPoint=operacionesCRM&idActiveDirectory=AD_REPLACE',
                                                'label' => 'Gestión de firmas',
                                                'icon' => 'sicon-data-table',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'actions' => [
                                            [
                                                //'route' => '#bwc/index.php?entryPoint=CotizadorQuantico&idActiveDirectory=' . $current_user->id_active_directory_c,
                                                'route' => '#bwc/index.php?entryPoint=CotizadorQuantico&idActiveDirectory=AD_REPLACE',
                                                'label' => 'Cotizador Quantico',
                                                'icon' => 'sicon-data-table',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'actions' => [
                                            [
                                                //'route' => '#bwc/index.php?entryPoint=HistorialCotizador&idActiveDirectory=' . $current_user->id_active_directory_c,
                                                'route' => '#bwc/index.php?entryPoint=HistorialCotizador&idActiveDirectory=AD_REPLACE',
                                                'label' => 'Historial de cotizaciones',
                                                'icon' => 'sicon-data-table',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'view' => [
                                        'type' => 'sidebar-nav-flyout-actions',
                                        'actions' => [
                                            [
                                                //'route' => '#bwc/index.php?entryPoint=OpMasivasRsQuantico&idActiveDirectory=' . $current_user->id_active_directory_c,
                                                'route' => '#bwc/index.php?entryPoint=OpMasivasRsQuantico&idActiveDirectory=AD_REPLACE',
                                                'label' => 'Operaciones Masivas Rs',
                                                'icon' => 'sicon-data-table',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    // [
                    //     'view' => [
                    //         'name' => 'sidebar-nav-item-expediente',
                    //         'type' => 'sidebar-nav-item-module',
                    //         'icon' => 'sicon-data-table',
                    //         'display_type' => 'abbreviation',
                    //         'abbreviation' => 'Qu',
                    //         'label' => 'Expediente Uniclick',
                    //         //'secondary_action' => true,
                    //         'route' => 'bwc/index.php?entryPoint=ExpedienteUniclick',
                    //         'template' => 'sidebar-nav-item',
                    //     ],
                    // ],
                    [
                        'view' => [
                            'name' => 'sidebar-nav-item-expediente',
                            'type' => 'sidebar-nav-item-module',
                            'icon' => 'sicon-quote-lg',
                            'display_type' => 'abbreviation',
                            'abbreviation' => 'Co',
                            'label' => 'Cotizador IPAD',
                            'route' => 'bwc/index.php?entryPoint=CotizadorProspectos',
                            //'secondary_action' => true,
                            'template' => 'sidebar-nav-item',
                        ],
                    ]
                ],
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

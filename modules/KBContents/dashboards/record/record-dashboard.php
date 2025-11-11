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
return [
    'metadata' => [
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'KBContents',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'attachments',
                                'label' => 'LBL_DASHLET_ATTACHMENTS_NAME',
                                'limit' => '5',
                                'auto_refresh' => '0',
                            ],
                            'context' => [
                                'module' => 'Notes',
                                'link' => 'notes',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashlet-searchable-kb-list',
                                'label' => 'LBL_DASHLET_KB_SEARCH_NAME',
                                'data_provider' => 'Categories',
                                'config_provider' => 'KBContents',
                                'root_name' => 'category_root',
                                'extra_provider' => [
                                    'module' => 'KBContents',
                                    'field' => 'category_id',
                                ],
                            ],
                            'context' => [
                                'module' => 'KBContents',
                            ],
                            'width' => 12,
                            'height' => 4,
                            'x' => 0,
                            'y' => 0,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'kbs-dashlet-localizations',
                                'label' => 'LBL_DASHLET_LOCALIZATIONS_NAME',
                            ],
                            'context' => [
                                'module' => 'KBContents',
                                'filter' => [
                                    'module' => [
                                        'KBContents',
                                    ],
                                    'view' => 'record',
                                ],
                            ],
                            'width' => 12,
                            'height' => 4,
                            'x' => 0,
                            'y' => 0,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'kbs-dashlet-usefulness',
                                'label' => 'LBL_DASHLET_USEFULNESS_NAME',
                            ],
                            'context' => [
                                'module' => 'KBContents',
                            ],
                            'width' => 12,
                            'height' => 4,
                            'x' => 0,
                            'y' => 0,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_KBCONTENTS_RECORD_DASHBOARD',
];

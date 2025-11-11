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

$viewdefs['base']['view']['dashablerecord'] = [
    'template' => 'record',
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_RECORDVIEW_NAME',
            'description' => 'LBL_DASHLET_RECORDVIEW_DESCRIPTION',
            'filter' => [
                'view' => 'record',
                'blacklist' => [
                    'module' => [
                        'Home',
                    ],
                ],
            ],
            'config' => [],
            // FIXME: see if it's safe to erase this
            'preview' => [
                'module' => 'Accounts',
                'label' => 'LBL_MODULE_NAME',
            ],
        ],
    ],
    // Used for configuration rather than the actual dashlet appearance
    'panels' => [
        [
            'name' => 'dashlet_settings',
            'columns' => 1,
            'placeholders' => true,
            'fields' => [
//                [
//                    'name' => 'module',
//                    'type' => 'enum',
//                    'label' => 'LBL_DASHLET_RECORDVIEW_BASE_RECORD_TYPE',
//                    'span' => 6,
//                    'sort_alpha' => true,
//                ],
                [
                    'name' => 'tab_list',
                    'label' => 'LBL_DASHLET_RECORDVIEW_TABS',
                    'type' => 'enum',
                    'span' => 6,
                    'isMultiSelect' => true,
                    'sort_alpha' => true,
                    'ordered' => true,
                ],
            ],
        ],
    ],
    'listsettings' => [
        'panels' => [
            [
                'name' => 'dashlet_settings',
                'columns' => 2,
                'placeholders' => true,
                'fields' => [
                    [
                        'name' => 'fields',
                        'label' => 'LBL_COLUMNS',
                        'type' => 'enum',
                        'isMultiSelect' => true,
                        'ordered' => true,
                        'span' => 12,
                        'hasBlank' => true,
                        'options' => ['' => ''],
                    ],
                    [
                        'name' => 'freeze_first_column',
                        'label' => 'LBL_DASHLET_FREEZE_FIRST_COLUMN',
                        'type' => 'bool',
                        'span' => 12,
                        'default' => true,
                        'showOnConfig' => 'allowFreezeFirstColumn',
                    ],
                    [
                        'name' => 'limit',
                        'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                        'type' => 'enum',
                        'options' => 'dashlet_limit_options',
                        'span' => 12,
                    ],
                    [
                        'name' => 'auto_refresh',
                        'label' => 'Auto Refresh',
                        'type' => 'enum',
                        'options' => 'sugar7_dashlet_auto_refresh_options',
                        'span' => 12,
                    ],
                ],
            ],
        ],
    ],
];

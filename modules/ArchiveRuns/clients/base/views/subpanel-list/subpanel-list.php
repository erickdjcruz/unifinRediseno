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
$viewdefs['ArchiveRuns']['base']['view']['subpanel-list'] = [
    'template' => 'flex-list',
    'sticky_resizable_columns' => true,
    'favorite' => false,
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_DATE_OF_ARCHIVE_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_of_archive',
                ],
                [
                    'label' => 'LBL_PROCESS_TYPE_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'process_type',
                ],
                [
                    'label' => 'LBL_MODULE_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'source_module',
                ],
                [
                    'label' => 'LBL_FILTER_DEF_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'filter_def',
                    'type' => 'filter-def',
                    'sortable' => false,
                ],
                [
                    'label' => 'LBL_NUM_PROCESSED_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'num_processed',
                ],
                [
                    'label' => 'LBL_SOURCE_FIELD',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'created_by',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
            ],
        ],
    ],
];

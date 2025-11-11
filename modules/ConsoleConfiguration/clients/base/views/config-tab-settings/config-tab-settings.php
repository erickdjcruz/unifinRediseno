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
$viewdefs['ConsoleConfiguration']['base']['view']['config-tab-settings'] = [
    'label' => 'LBL_MODULE_NAME',
    'panels' => [
        [
            'label' => 'LBL_CONSOLE_SORT_ORDER_DEFAULT',
            'fields' => [
                [
                    'name' => 'order_by_primary_group',
                    'label' => 'LBL_CONSOLE_SORT_ORDER_PRIMARY',
                    'type' => 'fieldset',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'order_by_primary',
                            'label' => 'LBL_CONSOLE_SORT_ORDER_PRIMARY',
                            'type' => 'enum',
                        ],
                        [
                            'name' => 'order_by_primary_direction',
                            'label' => 'LBL_DIRECTION',
                            'type' => 'sort-order-selector',
                            'default' => 'desc',
                            'dependencyField' => 'order_by_primary',
                        ],
                    ],
                ],
                [
                    'name' => 'order_by_secondary_group',
                    'label' => 'LBL_CONSOLE_SORT_ORDER_SECONDARY',
                    'type' => 'fieldset',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'order_by_secondary',
                            'label' => 'LBL_CONSOLE_SORT_ORDER_SECONDARY',
                            'type' => 'enum',
                        ],
                        [
                            'name' => 'order_by_secondary_direction',
                            'label' => 'LBL_DIRECTION',
                            'type' => 'sort-order-selector',
                            'default' => 'desc',
                            'dependencyField' => 'order_by_secondary',
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'LBL_FREEZE_FIRST_COLUMN',
            'fields' => [
                [
                    'name' => 'freeze_first_column',
                    'type' => 'freeze-first-column',
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'label' => 'LBL_CONSOLE_FILTER',
            'fields' => [
                [
                    'name' => 'filter_def',
                    'dismiss_label' => true,
                    'type' => 'filter-field',
                ],
            ],
        ],
        [
            'label' => 'LBL_PREVIEW',
            'fields' => [
                [
                    'name' => 'preview-table',
                    'type' => 'preview-table',
                ],
            ],
        ],
        [
            'fields' => [
                [
                    'name' => 'directions',
                    'vname' => 'LBL_CONSOLE_DIRECTIONS',
                    'type' => 'directions',
                ],
            ],
        ],
    ],
];

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
$viewdefs['Metrics']['base']['view']['record-content-tabs'] = [
    'settings' => [
        [
            'name' => 'panel_body',
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => [
                [
                    'type' => 'context-module',
                    'name' => 'context-module',
                    'label' => 'LBL_CONTEXT_MODULE',
                    'readonly' => true,
                ],
                [
                    'type' => 'teamset',
                    'name' => 'team_name',
                    'label' => 'LBL_TEAMS',
                ],
                [
                    'type' => 'enum',
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                ],
                [
                    'type' => 'filter-field',
                    'name' => 'filter-def',
                    'label' => 'LBL_METRIC_FILTER',
                    'span' => 12,
                ],
                [
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION',
                    'type' => 'textarea',
                    'rows' => 5,
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_SHOW_MORE',
            'hide' => true,
            'columns' => 1,
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'tag',
                    'type' => 'tag',
                    'label' => 'LBL_TAGS',
                    'span' => 12,
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                    'type' => 'relate',
                ],
            ],
        ],
    ],
    'list_layout' => [
        [
            'label' => 'LBL_METRIC_SORT_ORDER_DEFAULT',
            'fields' => [
                [
                    'name' => 'order_by_primary_group',
                    'label' => 'LBL_METRIC_SORT_ORDER_PRIMARY',
                    'type' => 'fieldset',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'order_by_primary',
                            'label' => 'LBL_METRIC_SORT_ORDER_PRIMARY',
                            'type' => 'enum',
                            'css_class' => 'metric-tab-enum',
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
                    'label' => 'LBL_METRIC_SORT_ORDER_SECONDARY',
                    'type' => 'fieldset',
                    'inline' => true,
                    'fields' => [
                        [
                            'name' => 'order_by_secondary',
                            'label' => 'LBL_METRIC_SORT_ORDER_SECONDARY',
                            'type' => 'enum',
                            'css_class' => 'metric-tab-enum',
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
            'label' => 'LBL_PREVIEW',
            'css_class' => 'overflow-auto',
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
                    'vname' => 'LBL_METRIC_DIRECTIONS',
                    'type' => 'directions',
                ],
            ],
        ],
    ],
];

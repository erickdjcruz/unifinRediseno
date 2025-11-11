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

$viewdefs['base']['view']['saved-reports-chart'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_SAVED_REPORTS_CHART',
            'description' => 'LBL_DASHLET_SAVED_REPORTS_CHART_DESC',
            'config' => [

            ],
            'preview' => [

            ],
            'filter' => [
                'blacklist' => [
                    'module' => 'Administration',
                ],
                'view' => 'DEPRECATED_DASHLET',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'dashletaction',
                'css_class' => 'btn btn-invisible dashlet-toggle minify',
                'icon' => 'sicon-chevron-up',
                'action' => 'toggleMinify',
                'tooltip' => 'LBL_DASHLET_TOGGLE',
            ],
            [
                'alwaysOnDisplay' => true,
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'viewReportClicked',
                        'label' => 'LBL_DASHLET_CONFIG_VIEW_REPORT',
                        'alwaysOnDisplay' => true,
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                        'alwaysOnDisplay' => true,
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                ],
            ],
        ],
    ],
    'dashlet_config_panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'saved_report',
                    'label' => 'LBL_REPORT_SELECT',
                    'type' => 'relate',
                    'id_name' => 'saved_report_id',
                    'module' => 'Reports',
                    'rname' => 'name',
                    'initial_filter' => 'with_charts',
                    'initial_filter_label' => 'LBL_FILTER_WITH_CHARTS',
                    'filter_populate' => [
                        'chart_type' => 'none',
                    ],
                ],
                [
                    'name' => 'auto_refresh',
                    'label' => 'LBL_REPORT_AUTO_REFRESH',
                    'type' => 'enum',
                    'options' => 'sugar7_dashlet_reports_auto_refresh_options',
                ],
                [
                    'name' => 'chart_type',
                    'label' => 'LBL_CHART_CONFIG_CHART_TYPE',
                    'type' => 'enum',
                    'default' => 'group by chart',
                    'sort_alpha' => true,
                    'ordered' => true,
                    'searchBarThreshold' => -1,
                    'options' => 'chart_types',
                ],

                [
                ],

                [
                    'name' => 'show_title',
                    'label' => 'LBL_CHART_CONFIG_SHOW_TOTAL',
                    'type' => 'bool',
                    'default' => 0,
                ],

                [
                    'name' => 'show_legend',
                    'label' => 'LBL_CHART_LEGEND_OPEN',
                    'type' => 'bool',
                    'default' => 1,
                ],

                [
                    'name' => 'x_label_options',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_CHART_CONFIG_SHOW_XAXIS_LABEL',
                    'toggle' => 'show_x_label',
                    'dependent' => 'x_axis_label',
                    'fields' => [
                        [
                            'name' => 'show_x_label',
                            'type' => 'bool',
                            'default' => 0,
                        ],
                        [
                            'name' => 'x_axis_label',
                        ],
                    ],
                ],

                [
                    'name' => 'tickDisplayMethods',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => true,
                    'label' => 'LBL_CHART_CONFIG_TICK_DISPLAY',
                    'css_class' => 'fieldset-wrap',
                    'fields' => [
                        [
                            'name' => 'wrapTicks',
                            'text' => 'LBL_CHART_CONFIG_TICK_WRAP',
                            'type' => 'bool',
                            'default' => true,
                        ],
                        [
                            'name' => 'staggerTicks',
                            'text' => 'LBL_CHART_CONFIG_TICK_STAGGER',
                            'type' => 'bool',
                            'default' => true,
                        ],
                        [
                            'name' => 'rotateTicks',
                            'text' => 'LBL_CHART_CONFIG_TICK_ROTATE',
                            'type' => 'bool',
                            'css_class' => 'disabled',
                            'default' => true,
                        ],
                    ],
                ],

                [
                    'name' => 'y_label_options',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_CHART_CONFIG_SHOW_YAXIS_LABEL',
                    'toggle' => 'show_y_label',
                    'dependent' => 'y_axis_label',
                    'fields' => [
                        [
                            'name' => 'show_y_label',
                            'type' => 'bool',
                            'default' => 0,
                        ],
                        [
                            'name' => 'y_axis_label',
                        ],
                    ],
                ],

                [
                ],

                [
                    'name' => 'showValues',
                    'label' => 'LBL_CHART_CONFIG_VALUE_PLACEMENT',
                    'type' => 'enum',
                    'default' => false,
                    'options' => 'd3_value_placement',
                ],

                [
                    'name' => 'groupDisplayOptions',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_CHART_CONFIG_BAR_CHART_OPTIONS',
                    'css_class' => 'fieldset-wrap',
                    'fields' => [
                        [
                            'name' => 'allowScroll',
                            'text' => 'LBL_CHART_CONFIG_ALLOW_SCROLLING',
                            'type' => 'bool',
                            'default' => 1,
                        ],
                        [
                            'name' => 'stacked',
                            'text' => 'LBL_CHART_CONFIG_STACK_DATA',
                            'type' => 'bool',
                            'default' => 1,
                        ],
                        [
                            'name' => 'hideEmptyGroups',
                            'text' => 'LBL_CHART_CONFIG_HIDE_EMPTY_GROUPS',
                            'type' => 'bool',
                            'default' => 1,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'chart' => [
        'name' => 'chart',
        'label' => 'LBL_CHART',
        'type' => 'chart',
        'view' => 'detail',
        'customLegend' => true,
    ],
];

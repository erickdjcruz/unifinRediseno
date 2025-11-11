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

$viewdefs['base']['view']['report-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_REPORT_DASHLET',
            'description' => 'LBL_REPORT_DASHLET_DESC',
            'config' => [],
            'preview' => [],
            'filter' => [
                'blacklist' => [
                    'module' => 'Administration',
                ],
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'dashletaction',
                'css_class' => 'dashlet-toggle btn btn-invisible minify',
                'icon' => 'sicon-chevron-up',
                'action' => 'toggleMinify',
                'tooltip' => 'LBL_DASHLET_MINIMIZE',
            ],
            [
                'custom_dashlet_icon' => 'sicon-kebab',
                'custom_dashlet_label' => 'LBL_LISTVIEW_ACTIONS',
                'name' => 'userActions',
                'alwaysOnDisplay' => true,
                'acl_module' => 'Reports',
                'acl_action' => 'view',
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'viewReport',
                        'label' => 'LBL_REPORT_DASHLET_CONFIG_VIEW_REPORT',
                        'alwaysOnDisplay' => true,
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'resetDashletDefaultSettings',
                        'label' => 'LBL_REPORT_DASHLET_RESET_DEFAULT',
                        'alwaysOnDisplay' => true,
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshResults',
                        'label' => 'LBL_REPORT_DASHLET_REFRESH_RESULTS',
                        'alwaysOnDisplay' => true,
                    ],
                    [
                        'type' => 'divider',
                        'useSpan' => true,
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'downloadChart',
                        'label' => 'LBL_REPORT_DASHLET_DOWNLOAD_CHART',
                        'alwaysOnDisplay' => true,
                    ],
                ],
            ],
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                        'name' => 'remove_button',
                    ],
                ],
            ],
        ],
    ],
    'dashlet_config_panels' => [
        [
            'name' => 'general',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'label' => 'LBL_HINT_GENERAL',
            'fields' => [
                [
                    'name' => 'saved_report',
                    'label' => 'LBL_REPORT_SELECT',
                    'type' => 'relate',
                    'id_name' => 'reportId',
                    'module' => 'Reports',
                    'rname' => 'name',
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [
                    'name' => 'defaultView',
                    'label' => 'LBL_REPORTS_DASHLET_DEFAULT_VIEW',
                    'type' => 'enum',
                    'options' => [
                        'chart' => translate('LBL_CHART'),
                        'list' => translate('LBL_REPORTS_DASHLET_DATATABLE'),
                        'filters' => translate('LBL_FILTER'),
                    ],
                    'default' => 'chart',
                    'required' => true,
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                    'cell_css_class' => 'report-dashlet-fixed-margin',
                ],
                [
                    'name' => 'autoRefresh',
                    'label' => 'LBL_REPORT_AUTO_REFRESH',
                    'type' => 'enum',
                    'options' => 'sugar7_dashlet_reports_auto_refresh_options',
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [],
                [
                    'name' => 'intelligent',
                    'label' => 'LBL_REPORTS_DASHLET_INTELLIGENT',
                    'type' => 'bool',
                    'span' => 4,
                ],
                [
                    'name' => 'linkedFields',
                    'label' => 'LBL_REPORTS_DASHLET_RELATED_LINK',
                    'type' => 'enum',
                    'options' => ['' => ''],
                    'required' => true,
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                    'cell_css_class' => 'report-dashlet-fixed-left',
                ],
            ],
        ],
        [
            'name' => 'chart',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'label' => 'LBL_CHART',
            'fields' => [
                [
                    'name' => 'chartType',
                    'label' => 'LBL_REPORTS_CHART_TYPE',
                    'type' => 'enum',
                    'default' => 'treemapF',
                    'sort_alpha' => true,
                    'ordered' => true,
                    'searchBarThreshold' => -1,
                    'options' => 'chart_types',
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [
                    'type' => 'divider',
                    'category' => 'chart_options',
                    'span' => 12,
                    'useSpan' => true,
                ],
                [
                    'type' => 'category-title',
                    'value' => 'LBL_EMAIL_COMPOSE_OPTIONS',
                    'category' => 'chart_options',
                    'span' => 12,
                ],
                [
                    'name' => 'primaryChartOrderSet',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_REPORTS_DASHLET_PRIMARY_CHART_SORT',
                    'css_class' => 'fieldset-wrap',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'primaryChartColumn',
                            'type' => 'enum',
                            'simulate_label' => true,
                            'fieldsetClasses' => 'report-dashlet-group report-dashlet-dropdown',
                            'options' => [],
                        ],
                        [
                            'name' => 'primaryChartColumnInfo',
                            'type' => 'show-help-button',
                            'popupTitle' => 'LBL_REPORTS_DASHLET_PRIMARY_CHART_SORT',
                            'simulate_label' => true,
                            'fieldsetClasses' => 'report-dashlet-group report-info-popup',
                        ],
                        [
                            'name' => 'primaryChartOrder',
                            'type' => 'sortorder',
                            'simulate_label' => true,
                            'default' => 'asc',
                            'fieldsetClasses' => 'report-dashlet-group report-chart-order',
                        ],
                    ],
                ],
                [
                    'name' => 'secondaryChartOrderSet',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_REPORTS_DASHLET_SECONDARY_CHART_SORT',
                    'css_class' => 'fieldset-wrap',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'secondaryChartColumn',
                            'type' => 'enum',
                            'simulate_label' => true,
                            'fieldsetClasses' => 'report-dashlet-group report-dashlet-dropdown',
                            'options' => [],
                        ],
                        [
                            'name' => 'secondaryChartColumnInfo',
                            'type' => 'show-help-button',
                            'popupTitle' => 'LBL_REPORTS_DASHLET_SECONDARY_CHART_SORT',
                            'simulate_label' => true,
                            'fieldsetClasses' => 'report-dashlet-group report-info-popup',
                        ],
                        [
                            'name' => 'secondaryChartOrder',
                            'type' => 'sortorder',
                            'simulate_label' => true,
                            'default' => 'asc',
                            'fieldsetClasses' => 'report-dashlet-group report-chart-order',
                        ],
                    ],
                ],
                [
                    'name' => 'showValues',
                    'label' => 'LBL_REPORTS_CHART_CONFIG_VALUE_PLACEMENT',
                    'type' => 'enum',
                    'default' => false,
                    'options' => 'd3_value_placement',
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [],
                [
                    'type' => 'divider',
                    'category' => 'chart_labels',
                    'span' => 12,
                    'useSpan' => true,
                ],
                [
                    'type' => 'category-title',
                    'value' => 'LBL_REPORTS_DASHLET_LABELS',
                    'category' => 'chart_labels',
                    'span' => 12,
                ],
                [
                    'name' => 'chartDisplayOptions',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'css_class' => 'fieldset-wrap',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'showTitle',
                            'text' => 'LBL_REPORTS_CHART_CONFIG_SHOW_TOTAL',
                            'type' => 'bool',
                            'default' => false,
                        ],
                        [
                            'name' => 'showLegend',
                            'text' => 'LBL_REPORTS_CHART_LEGEND_OPEN',
                            'type' => 'bool',
                            'default' => false,
                        ],
                    ],
                ],
                [
                    'name' => 'x_label_options',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_REPORTS_CHART_CONFIG_SHOW_YAXIS_LABEL',
                    'toggle' => 'showXLabel',
                    'dependent' => 'xAxisLabel',
                    'fields' => [
                        [
                            'name' => 'showXLabel',
                            'type' => 'bool',
                            'default' => false,
                        ],
                        [
                            'name' => 'xAxisLabel',
                            'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                        ],
                    ],
                ],
                [
                    'name' => 'y_label_options',
                    'type' => 'fieldset',
                    'inline' => true,
                    'show_child_labels' => false,
                    'label' => 'LBL_REPORTS_CHART_CONFIG_SHOW_XAXIS_LABEL',
                    'toggle' => 'showYLabel',
                    'dependent' => 'yAxisLabel',
                    'fields' => [
                        [
                            'name' => 'showYLabel',
                            'type' => 'bool',
                            'default' => false,
                        ],
                        [
                            'name' => 'yAxisLabel',
                            'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'list',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'label' => 'LBL_REPORTS_DASHLET_DATATABLE',
            'fields' => [
                [
                    'name' => 'sortColumnList',
                    'label' => 'LBL_REPORTS_DASHLET_SORT_ORDER',
                    'type' => 'enum',
                    'options' => [],
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [
                    'name' => 'sortOrderList',
                    'type' => 'sortorder',
                    'simulate_label' => true,
                    'default' => 'asc',
                    'cell_css_class' => 'report-dashlet-sortorder-margin',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_REPORTS_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'dashlet_limit_options',
                    'css_class' => 'report-dashlet-group report-dashlet-dropdown',
                ],
                [],
                [
                    'name' => 'showTotalRecordCount',
                    'label' => 'LBL_REPORTS_DASHLET_SHOW_COUNT',
                    'default' => false,
                    'type' => 'bool',
                    'span' => 3,
                ],
            ],
        ],
        [
            'name' => 'filter',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'label' => 'LBL_REPORTS_DASHLET_RUNTIME_FILTERS',
            'custom_view' => 'report-dashlet-filter',
            'span' => 6,
            'fields' => [],
        ],
    ],
];

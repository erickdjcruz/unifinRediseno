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
$viewdefs['Forecasts']['base']['view']['pipeline-metrics'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PIPELINE_METRICS_DASHLET_NAME',
            'description' => 'LBL_PIPELINE_METRICS_DASHLET_DESC',
            'config' => [
                'module' => 'Forecasts',
            ],
            'preview' => [
                'module' => 'Forecasts',
            ],
        ],
    ],
    'panels' => [
        'dashlet_settings' => [
            'name' => 'dashlet_settings',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                'metrics' => [
                    'name' => 'metrics',
                    'label' => 'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS',
                    'type' => 'enum',
                    'span' => 12,
                    'isMultiSelect' => true,
                    'maximumSelectionSize' => 9,
                    'ordered' => true,
                    'required' => true,
                    'options' => [],
                ],
                'refresh_interval' => [
                    'name' => 'refresh_interval',
                    'label' => 'LBL_REPORT_AUTO_REFRESH',
                    'type' => 'enum',
                    'span' => 12,
                    'options' => 'sugar7_dashlet_reports_auto_refresh_options',
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
                'type' => 'dashletaction',
                'css_class' => 'toggle-metric-definitions-btn btn btn-invisible mr-1',
                'icon' => 'sicon-help',
                'action' => 'toggleMetricDefinitions',
                'tooltip' => 'LBL_HELP',
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
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
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
];

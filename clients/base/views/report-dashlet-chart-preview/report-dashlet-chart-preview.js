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
/**
 * @class View.Views.Base.ReportDashletChartPreviewView
 * @alias SUGAR.App.view.views.BaseReportDashletChartPreviewView
 * @extends View.View
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this._chartTypeMapping = {
            'pieF': 'pie-chart-skeleton-loader',
            'donutF': 'donut-chart-skeleton-loader',
            'funnelF': 'funnel-chart-skeleton-loader',
            'treemapF': 'treemap-chart-skeleton-loader',
            'lineF': 'line-chart-skeleton-loader',
            'hBarF': 'h-bar-grouped-chart-skeleton-loader',
            'vBarF': 'v-bar-grouped-chart-skeleton-loader',
            'vGBarF': 'v-bar-chart-skeleton-loader',
            'hGBarF': 'h-bar-chart-skeleton-loader',
        };

        const chartType = this.model.get('chartType');

        this._labelCharts = [
            'hGBarF',
            'vGBarF',
            'vBarF',
            'hBarF',
            'lineF'
        ];
        this._chartType = this._chartTypeMapping[chartType] || 'v-bar-chart-skeleton-loader';
        this._useLabels = _.includes(this._labelCharts, chartType);

        this._dashletTitle = this.model.get('label');
        this._showXLabel = this.model.get('showXLabel');
        this._showYLabel = this.model.get('showYLabel');
        this._showLegend = this.model.get('showLegend');
        this._showTotal = this.model.get('showTitle');

        // line charts have inversed axes
        if (_.includes(['vBarF', 'vGBarF', 'lineF'], chartType)) {
            const showLabel = this._showXLabel;

            this._showXLabel = this._showYLabel;
            this._showYLabel = showLabel;
        }
    },

    /**
     * Refresh the UI
     */
    refreshPreview: function() {
        this._initProperties();

        this.render();
    },
})

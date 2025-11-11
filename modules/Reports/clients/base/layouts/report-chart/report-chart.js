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
 * @class View.Layouts.Base.Reports.ReportChartLayout
 * @alias SUGAR.App.view.layouts.BaseReportsReportChartLayout
 * @extends View.Layouts.Base.Layout
 */
 ({
    /**
     * Check if we can display the panel
     */
    isValid: function() {
        return this.model.get('chart_type') !== 'none';
    },

    /**
     * Get the title for the panel
     */
    getTitle: function() {
        const titleMapping = {
            none: 'LBL_NO_CHART',
            hBarF: 'LBL_HORIZ_BAR',
            hGBarF: 'LBL_HORIZ_GBAR',
            vBarF: 'LBL_VERT_BAR',
            vGBarF: 'LBL_VERT_GBAR',
            pieF: 'LBL_PIE',
            funnelF: 'LBL_FUNNEL',
            lineF: 'LBL_LINE',
            donutF: 'LBL_DONUT',
            treemapF: 'LBL_TREEMAP',
        };

        const chartType = this.model.get('chart_type');
        const chartLabel = titleMapping[chartType];

        let label = app.lang.get(chartLabel, 'Reports');
        label = label ? label : 'LBL_CHART';

        return label;
    },
})

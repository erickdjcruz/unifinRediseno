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
 * View for managing the help component's header bar.
 *
 * @class View.Views.Base.ForecastsMetricsInfoView
 * @alias SUGAR.App.view.layouts.BaseForecastsMetricsInfoView
 * @extends View.View
 */
({

    /**
     * List of the metric boxes
     */
    guide: [],

    /**
     * @inheritdoc
     */
    _render: function() {
        this._formatHelpText();
        this._super('_render');
    },

    /**
     * Helper function to clean and formate the help text with the correct(in case renamed)
     * Forecast Stage field name and Commit Stage Values ('Include', 'Exclude' and 'Upside')
     *
     * @private
     */
    _formatHelpText: function() {
        this.guide = app.metadata.getView('Forecasts','forecast-metrics')['forecast-metrics'];
        this.guide.forEach(ele => {
            let commitStage = app.lang.getAppListStrings(ele.commitStageDom)[ele.commitStageDomOption];
            ele.helpText = app.lang.getModString(
                ele.helpText, 'Forecasts',
                {
                    forecastStage: app.lang.get('LBL_COMMIT_STAGE_FORECAST', 'Opportunities'),
                    commitStageValue: commitStage,
                });
        });
    }
})

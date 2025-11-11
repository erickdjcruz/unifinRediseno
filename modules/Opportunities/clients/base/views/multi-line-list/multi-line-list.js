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
 * @class View.Views.Base.Opportunities.MultiLineListView
 * @alias SUGAR.App.view.views.BaseOpportunitiesMultiLineListView
 * @extends View.Views.Base.MultiLineListView
 */
({
    /**
     * Opportunities sales_status can be customized to included multiple values
     * @override
     */
    setFilterDef: function(options) {
        var meta = options.meta || {};
        // if filterDef exists in meta
        if (meta.filterDef) {
            // perform actions as per the parent class method
            this._super('setFilterDef', [options]);
            return;
        }
        var closedWon = ['Closed Won'];
        var closedLost = ['Closed Lost'];
        var forecastCfg = app.metadata.getModule('Forecasts', 'config');
        if (forecastCfg && forecastCfg.is_setup) {
            closedWon = forecastCfg.sales_stage_won;
            closedLost = forecastCfg.sales_stage_lost;
        }

        var notIn = _.union(closedWon, closedLost);
        var filterDef = [
            {
                sales_status: {
                    $not_in: notIn,
                },
                $owner: '',
            },
        ];
        options.context.get('collection').filterDef = filterDef;
        options.context.get('collection').defaultFilterDef = filterDef;
    }
})

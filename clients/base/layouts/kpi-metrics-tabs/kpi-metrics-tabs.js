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
 * @class View.Layouts.Base.KpiMetricsTabsLayout
 * @alias SUGAR.App.view.layouts.BaseKpiMetricsTabsLayout
 * @extends View.Layout
 */
({
    className: 'kpi-metrics-tabs',

    events: {
    },

    initialize: function(options) {
        this._super('initialize', [options]);
        this.context.metrics = [];
        this.context.set('noCollectionField', true);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        //Tells the count of all the records returned for the multiline list view
        // ( based on the filter) of the console tabs has changed
        this.listenTo(this.collection, 'data:sync:complete', this._handleRecordsСountChange);
    },

    /**
     * Show the count of all the records returned for the multiline list view
     * ( based on the filter) of the console tabs
     *
     * @private
     */
    _handleRecordsСountChange: function() {
        //ToDo: must be determined according to the data of a specific metric
        this.context.number = this.collection.models.length;
        this.render();
    },

    /**
     * Get the Metrics Data from the server
     *
     * @inheritdoc
     */
    loadData: function() {
        let url = app.api.buildURL('Metrics', 'visible', null, {
            metric_context: this.layout.meta.metric_context,
            metric_module: this.layout.meta.metric_module,
            fields: 'id,name,metric_module,filter_def,labels,viewdefs,order_by_primary,order_by_secondary,' +
                'order_by_primary_direction,order_by_secondary_direction,freeze_first_column'
        });
        let options = {};
        // breaking out options as a proper object to allow for bind
        options.success = _.bind(function(data) {
            this.context.metrics = data;

            if (data.length === 0) {
                let tabsComp = this.getComponent('metric-tabs');
                if (tabsComp) {
                    tabsComp.hasVisibleMetrics = false;
                }
            }

            this.collection.trigger('data:sync:complete');
        }, this);

        app.api.call('read', url, null, options);
    }
})

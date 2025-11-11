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
 * @class View.Views.Base.Forecasts.InfoView
 * @alias SUGAR.App.view.views.BaseForecastsInfoView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     *
     */
    initialize: function(options) {
        if (app.lang.direction === 'rtl') {
            options.template = app.template.getView('info.info-rtl', 'Forecasts');

            // reverse the datapoints
            options.meta.datapoints.reverse();
        }

        this._super("initialize", [options]);

        // Use the next commit model as this view's model
        this.model = this.context.get('nextCommitModel');
    },

    /**
     * @inheritdoc
     *
     */
    bindDataChange: function(){
        this.listenTo(this.context, 'forecasts:worksheet:totals:initialized', this._handleWorksheetTotalsInitialized);
        this.listenTo(this.context, 'forecasts:commit-models:loaded', this._handleCommitModelsLoaded);
        this.listenTo(this.context, 'button:cancel_button:click', this._handleCancelClicked);
        this.listenTo(this.context, 'change:selectedUser', this.loadIncludeData);
        this.listenTo(this.context, 'change:selectedTimePeriod',  this.loadIncludeData);
        this.listenTo(this.context, 'change:forecastType',  this.loadIncludeData);
    },

    /**
     * @inheritdoc
     *
     */
    loadData: function() {
        this._super('loadData');
        this.loadIncludeData();
    },

    /**
     * Loads the included Opp and RLI data for the currently viewed forecast.
     */
    loadIncludeData: function() {
        if (this.context.get('forecastType') === 'Direct') {
            let totals = {};
            let forecastConfig = app.metadata.getModule('Forecasts').config;
            let oppFields = {};

            _.each(this.meta.datapoints, function(datapoint) {
                oppFields[datapoint.name] = datapoint.total_field || datapoint.name;
            });

            let oppRequest = this.getRequest('Opportunities', oppFields);
            let data = {
                'requests': [oppRequest]
            };

            if (forecastConfig.forecast_by === 'RevenueLineItems') {
                let rliFields = {};
                _.each(this.meta.datapoints, function(datapoint) {
                    rliFields[datapoint.name] = datapoint.name;
                });

                let rliRequests = this.getRequest('RevenueLineItems', rliFields);
                data.requests.push(rliRequests);
            }

            let url = app.api.buildURL('bulk');
            let callbacks = {
                success: _.bind(function(data) {
                    let totals = {};
                    _.each(this.meta.datapoints, function(datapoint) {
                        totals[datapoint.name] = _.first(data).contents.metrics[datapoint.name].values.sum;
                    });

                    if (forecastConfig.forecast_by === 'RevenueLineItems') {
                        _.each(this.meta.datapoints, function(datapoint) {
                            totals['rli_' + datapoint.name] = data[1].contents.metrics[datapoint.name].values.sum;
                        });
                    }

                    this.syncedTotals = totals;
                    this._syncDatapointValues();
                    this.context.trigger('forecasts:worksheet:totals', totals, 'test');
                },this)
            };
            app.api.call('create', url, data, callbacks);
        }
    },

    /**
     * Gets the request used in a bulk request for a module specific metric
     * request
     *
     * @param {string} module
     * @param {string} fields
     * @return {Object} An object that represents an api request for the bulk
     *      api.
     */
    getRequest: function(module, fields) {
        let selectedUser = this.context.get('selectedUser');
        let url = app.api.buildURL('Forecasts/metrics');
        url = url.substr(5);
        let metrics = this.buildMetrics(fields);
        let data = {
            'filter': [],
            'module': module,
            'user_id': selectedUser ? selectedUser.id : '',
            'time_period': this.context.get('selectedTimePeriod'),
            'type': this.context.get('forecastType'),
            'metrics': metrics
        };

        return {
            'url': url,
            'method': 'POST',
            'data': data
        };
    },
    /**
     * Returns the metrics for an array of fields
     *
     * @param {Array} fields
     * @return Array
     */
    buildMetrics: function(fields) {

        let metrics = [];
        _.each(fields, function(field, name) {
            metrics.push(this.buildMetric(name, field));
        }, this);

        return metrics;
    },

    /**
     * Returns the metric which filters on included commit stages
     *
     * @param {string} name
     * @param {string} sumField
     * @return {Object}
     */
    buildMetric: function(name, sumField) {

        let includeStages = app.metadata.getModule('Forecasts').config.commit_stages_included || ['include'];
        let filter = [
            {
                'commit_stage': {
                    '$in': includeStages
                }
            }
        ];
        return {
            'name': name,
            'filter': filter,
            'sum_fields': sumField
        };
    },

    /**
     * Handles when the layout's commit models have been loaded
     *
     * @private
     */
    _handleCommitModelsLoaded: function() {
        this._syncDatapointValues();
    },

    /**
     * Handles when the totals of the worksheet records are initially
     * loaded and calculated
     *
     * @param {Object} totals
     * @private
     */
    _handleWorksheetTotalsInitialized: function(totals) {
        this.syncedTotals = totals;
        this._syncDatapointValues();
    },

    /**
     * Takes the last committed model (if applicable) and determines if this values should
     * be used at the synced/baseline values for the datapoint fields
     * or initiate that value at 0
     *
     * @private
     */
    _syncDatapointValues: function() {
        // Get the last commit model
        let lastCommitModel = this.context.get('lastCommitModel');

        // Sync any last committed datapoint values if necessary
        let valuesToSync = {};
        if (lastCommitModel instanceof Backbone.Model) {
            _.each(this.meta.datapoints, function(datapoint) {
                valuesToSync[datapoint.name] = lastCommitModel.get(datapoint.name);
            }, this);
        } else if (this.syncedTotals) {
            _.each(this.meta.datapoints, function(datapoint) {
                valuesToSync[datapoint.name] = 0;
            }, this);
        }

        this._setNextCommitModel(valuesToSync);
    },

    /**
     * Set next committed model data for the datapoint fields
     *
     * @param {Object} valuesToSync
     * @private
     */
    _setNextCommitModel: function(valuesToSync) {
        // Get the next commit model
        let nextCommitModel = this.context.get('nextCommitModel');
        nextCommitModel.setSyncedAttributes(valuesToSync);
        nextCommitModel.set(valuesToSync);
    },

    /**
     * Handles when the edit cancel button is clicked in the Forecasts view
     * @private
     */
    _handleCancelClicked: function() {
        // Revert the next commit model's attributes
        let nextCommitModel = this.context.get('nextCommitModel');
        if (nextCommitModel instanceof Backbone.Model) {
            nextCommitModel.revertAttributes();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
})

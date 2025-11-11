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
 * @class View.Views.Base.ForecastsForecastMetricsView
 * @alias SUGAR.App.view.layouts.BaseForecastsForecastMetricsView
 * @extends View.View
 */
({
    className: 'inline-block forecast-metrics-container',

    metrics: [],

    /**
     * Key name for the last state
     */
    lastStateKey: 'Forecasts:last-metric',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (_.isUndefined(options.meta[options.name])) {
            options.meta = app.metadata.getView(options.module, options.name);
        }

        if (app.lang.direction === 'rtl') {
            // reverse the forecast-metrics
            options.meta['forecast-metrics'].reverse();
        }

        this._super('initialize', [options]);

        this.metrics = this.buildMetrics();

        //Create a new model to store the metric values.
        this.model = new Backbone.Model();

        const lastMetric = app.user.lastState.get(this.lastStateKey) || [];
        this.listenTo(this.model, 'change:active', this._handleActiveMetricsChange);
        this.setActiveMetrics(lastMetric);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(this.context, 'change:selectedUser', this.loadData);
        this.listenTo(this.context, 'change:selectedTimePeriod',  this.loadData);
        this.listenTo(this.context, 'change:forecastType',  this.loadData);
        this.listenTo(this.context, 'opportunities:record:saved',  this.loadData);
        this.listenTo(this.layout.layout, 'filter:apply', this.loadData);
        let filterComp = this.layout.layout.getComponent('filter') || {};
        if (!_.isUndefined(filterComp)) {
            this.listenTo(filterComp, 'filter:apply', this.loadData);
            this.listenTo(filterComp, 'filter:change:filter', this.loadData);
        }
    },

    /**
     * Sets the active metric on the model
     */
    setActiveMetrics: function(activeMetrics) {
        if (_.isEmpty(activeMetrics)) {
            activeMetrics = [];
            let defaultMetrics = _.filter(this.meta['forecast-metrics'], function(metric) {
                return metric.isDefaultFilter || false;
            }, this);

            _.each(defaultMetrics, function(defaultMetric) {
                activeMetrics.push(defaultMetric.name);
            }, this);
        }

        this.model.set('lastActive', app.user.lastState.get(this.lastStateKey) || []);
        this.model.set('active', activeMetrics);
    },

    /**
     * Tells the parent layout that the selected active metric has changed
     *
     * @private
     */
    _handleActiveMetricsChange: function() {
        // For now, we allow one metric to be selected. In the future, this may
        // change to allow multiple
        let activeMetrics = [];
        let active = this.model.get('active');

        if (_.isArray(active)) {
            _.each(active, function(a) {
                if (_.has(this.metrics, a)) {
                    activeMetrics.push(this.metrics[a]);
                }
            }, this);
        } else {
            activeMetrics.push(this.metrics[active]);
        }

        this.layout.layout.trigger('forecast:metric:active', activeMetrics);
        this.render();

        if (this.lastStateKey) {
            app.user.lastState.set(this.lastStateKey, active);
        }
    },

    /**
     * Loads data from forecasts metrics api.
     */
    loadData: function() {
        this.toggleLoader(true);
        let url = app.api.buildURL('Forecasts/metrics');
        let selectedUser = this.context.get('selectedUser');
        let data = {
            'filter': this.getListFilter(),
            'module': this.layout.layout.meta.context.listViewModule || 'Opportunities',
            'user_id': selectedUser ? selectedUser.id : '',
            'time_period': this.context.get('selectedTimePeriod'),
            'type': this.context.get('forecastType'),
            'metrics': this.metrics
        };
        let callbacks = {
            success: _.bind(function(data) {
                let activeMetricNames =
                    _.isArray(this.model.get('active')) ? this.model.get('active') : [this.model.get('active')];
                let activeMetricsCount = 0;
                _.each(data.metrics, function(metric) {
                    if (_.includes(activeMetricNames, metric.name)) {
                        activeMetricsCount += metric.values.count;
                    }
                    this.model.set(`${metric.name}_count`, metric.values.count);
                    this.model.set(metric.name, metric.values.sum);
                },this);
                this.layout.layout.trigger('metric:count:fetched', activeMetricsCount);
                app.events.trigger('metric:data:ready');
            },this),
            complete: _.bind(function() {
                this.toggleLoader(false);
            },this)
        };
        app.api.call('create', url, data, callbacks);
    },

    /**
     * Builds metric defintions out of forecast-metrics metadata
     *
     * @return array
     */
    buildMetrics: function() {
        let metrics = {};
        _.each(this.meta['forecast-metrics'], function(metric) {
            metrics[metric.name] = this.buildMetric(metric);
        },this);
        return metrics;
    },
    /**
     * Returns a single metric from metric meta data.
     * @param metric
     *
     * @return object
     */
    buildMetric: function(metric) {
        return {
            'name': metric.name,
            'filter': metric.filter,
            'sum_fields': metric.sumFields
        };
    },

    /**
     * This method will return an array of filters from the list view.
     *
     * @return array
     */
    getListFilter: function() {
        let filterComp = this.layout.layout.getComponent('filter') || {};
        if (!_.isEmpty(filterComp)) {
            return filterComp.collection.filterDef || [];
        }
        return [];
    },

    /**
     * Show/Hide metric item SVG-loader
     */
    toggleLoader: function(show) {
        this.$el.find('.forecast-metric').toggleClass('metric-skeleton-loader', show);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
})

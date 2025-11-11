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
 * @class View.Views.Base.Forecasts.PipelineMetricsView
 * @alias SUGAR.App.view.views.ForecastsPipelineMetricsView
 * @extends View.View
 */
({
    plugins: ['Dashlet'],

    className: 'pipeline-metrics',

    events: {
        'click .metric-descriptions-close': 'toggleMetricDefinitions'
    },

    /**
     * Contains the metadata for the set of available metrics by metric name
     */
    availableMetrics: {},

    /**
     * Contains the subset of available metrics that are configured to be shown
     * via dashlet config
     */
    metrics: {},

    /**
     * List of calculated metrics keys
     */
    calculatedMetrics: [
        'quota',
        'commitment',
        'quota_coverage',
        'gap_quota',
        'pct_won_quota',
        'quota_gap_coverage',
        'commitment_coverage',
        'gap_commitment',
        'commitment_gap_coverage',
        'pct_won_commitment',
        'forecast_coverage',
        'gap_forecast',
        'forecast_gap_coverage',
        'pct_won_forecast'
    ],

    /**
     * Boolean storing whether Forecasts are available to the user
     */
    _forecastsIsAvailable: false,

    /**
     * Boolean storing true if this instance represents the configuration view
     */
    _isConfig: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._forecastsIsAvailable = this._checkForecastsAvailability();
        this._isConfig = this.meta.config;
    },

    /**
     * Checks to make sure the user has access to Forecasts
     *
     * @return {bool} true if Forecasts has been set up and the user has access to it
     * @private
     */
    _checkForecastsAvailability: function() {
        let forecastsConfig = app.metadata.getModule('Forecasts', 'config') || {};
        return forecastsConfig.is_setup && app.acl.hasAccess('read', 'Forecasts');
    },

    /**
     * Implements the initDashlet function of the Dashlet plugin. Initializes
     * any necessary dashlet configurations
     */
    initDashlet: function() {
        this.availableMetrics = this.getAvailableMetrics();
        this.metrics = this.getSelectedMetrics();

        if (this._isConfig) {
            this._initDashletConfig();
        } else {
            this._initDashletDisplay();
        }
    },

    /**
     * Initializes the dashlet configuration view
     *
     * @private
     */
    _initDashletConfig: function() {
        // Load the metrics options
        let configFields = _.get(this.dashletConfig, ['panels', 'dashlet_settings', 'fields']);
        let metricsFieldDef = _.findWhere(configFields, {name: 'metrics'});
        if (metricsFieldDef) {
            metricsFieldDef.options = {};
            _.each(this.availableMetrics, function(def, name) {
                metricsFieldDef.options[name] = app.lang.get(def.label || '', 'Forecasts');
            }, this);
        }

        this.layout.before('dashletconfig:save', this._validateConfig, this);
        this.listenTo(this.settings, 'change:metrics', this._handleConfigMetricsChange);
    },

    /**
     * Initilizes the dashlet main view
     *
     * @private
     */
    _initDashletDisplay: function() {
        this._startAutoRefresh();

        let appContext = app.controller.context;
        if (appContext.get('module') === 'Forecasts' && appContext.get('layout') === 'records') {
            let updateContext = () => {
                let selectedUser = appContext.get('selectedUser');
                this.context.set({
                    selectedUserId: selectedUser ? selectedUser.id : app.user.id,
                    selectedUserType: appContext.get('forecastType'),
                    selectedTimePeriodId: appContext.get('selectedTimePeriod')
                });
            };
            updateContext();
            this.listenTo(appContext,'filter:selectedUser:changed filter:selectedTimePeriod:changed', updateContext);
            this.listenTo(appContext, 'forecasts:refreshList opportunities:record:saved', this.loadData);
        } else {
            this.context.set({
                selectedUserId: app.user.get('id'),
                selectedUserType: app.user.get('is_manager') ? 'Rollup' : 'Direct',
                selectedTimePeriodId: appContext.get('selectedTimePeriod') || '',
            });

            if (!appContext.get('selectedTimePeriod')) {
                let self = this;
                // get the current timeperiod
                const url = app.api.buildURL('TimePeriods', 'current', {}, {});
                app.api.call('read', url, null, {
                    success: function(data) {
                        if (_.isEmpty(data)) {
                            return;
                        }

                        self.context.set({
                            selectedTimePeriodId: data.id,
                        });
                        self.loadData();
                    },
                    error: function(err) {
                        app.logger.error('Cannot get the current timeperiod: ' + JSON.stringify(err));
                    }
                });
            }
        }

        this.listenTo(this.context, 'change:selectedUserId change:selectedUserType change:selectedTimePeriodId',
            this.loadData);
    },

    /**
     * Handles when the user changes the configuration option that determines
     * the set of metrics shown on the dashlet
     *
     * @private
     */
    _handleConfigMetricsChange: function() {
        let template = app.template.getView('pipeline-metrics.metric-descriptions', this.module);
        this.$el.find('.metric-descriptions').replaceWith(template({
            metrics: _.pick(this.availableMetrics, this.settings.get('metrics'))
        }));
    },

    /**
     * Validates the values entered into the dashlet configuration when saving
     *
     * @return {boolean} true if the config settings are valid; false otherwise
     * @private
     */
    _validateConfig: function() {
        let result = true;

        // Validate the metrics field
        let metricsField = this.getField('metrics');
        if (metricsField) {
            metricsField.$el.removeClass('error');
            if (_.isEmpty(this.settings.get('metrics'))) {
                metricsField.$el.addClass('error');
                app.alert.show('dashlet_pipeline_invalid_config', {
                    level: 'warning',
                    messages: app.lang.get('LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_REQUIRED', 'Forecasts'),
                });
                result = false;
            }
        }

        return result;
    },

    /**
     * Initializes the timer that refreshes dashlet data at the interval
     * defined in dashlet configuration
     *
     * @private
     */
    _startAutoRefresh: function() {
        this._stopAutoRefresh();
        let refreshInterval = (this.settings.get('refresh_interval') || 0) * 60000;
        if (refreshInterval > 0) {
            this._autoRefreshId = setInterval(_.bind(this.loadData, this), refreshInterval);
        }
    },

    /**
     * Cancels the auto-refresh interval timer
     *
     * @private
     */
    _stopAutoRefresh: function() {
        if (this._autoRefreshId) {
            clearInterval(this._autoRefreshId);
            this._autoRefreshId = null;
        }
    },

    /**
     * Returns the full set of metrics definitions available to this dashlet
     *
     * @return {Object} a map of {metric name} => {metric definition}
     */
    getAvailableMetrics: function() {
        let metrics = this._getAvailableForecastMetrics();

        _.each(this.calculatedMetrics, key => {
            metrics[key] = {
                name: key,
                label: app.lang.get(`LBL_METRIC_LABEL_${key.toUpperCase()}`, this.module),
                helpText: app.lang.get(`LBL_METRIC_HELP_${key.toUpperCase()}`, this.module)
            };
        }, this);

        return metrics;
    },

    /**
     * Returns the metrics from the forecast-metrics metadata formatted as
     * needed for this dashlet
     *
     * @private
     */
    _getAvailableForecastMetrics: function() {
        let availableForecastMetrics = {};

        // Pre-format the labels of the forecast-metrics to include the correct
        // labels based on current field and dom option labels
        let metrics = _.get(app.metadata.getView('Forecasts', 'forecast-metrics'), 'forecast-metrics') || {};
        let forecastStage = app.lang.get('LBL_COMMIT_STAGE_FORECAST', 'Opportunities');
        _.each(metrics, function(metric) {
            metric.helpText = app.lang.get(metric.helpText, 'Forecasts', {
                forecastStage: forecastStage,
                commitStageValue: app.lang.getAppListStrings(metric.commitStageDom)[metric.commitStageDomOption] || ''
            });
            metric.label = app.lang.get(metric.label, 'Forecasts');
            availableForecastMetrics[metric.name] = metric;
        }, this);

        return availableForecastMetrics;
    },

    /**
     * Returns the set of metrics definitions selected for this dashlet
     *
     * @return {Object} a map of {metric name} => {metric definition}
     */
    getSelectedMetrics: function() {
        let selectedMetrics = this.settings.get('metrics') || [];
        if (_.isEmpty(selectedMetrics)) {
            let configFields = _.get(this.dashletConfig, ['panels', 'dashlet_settings', 'fields']);
            let metricsFieldDef = _.findWhere(configFields, {name: 'metrics'});
            let maxMetrics = metricsFieldDef && metricsFieldDef.maximumSelectionSize || 1;
            selectedMetrics = _.first(_.keys(this.availableMetrics), maxMetrics);
            this.settings.set('metrics', selectedMetrics);
        }

        return _.pick(this.availableMetrics, selectedMetrics);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!this._forecastsIsAvailable) {
            this.tplName = 'noaccess';
            this.template = app.template.getView(`pipeline-metrics.${this.tplName}`, this.module);
        }
        this._super('_render');
    },

    /**
     * Toggles the visibility of the metric definitions container
     */
    toggleMetricDefinitions: function() {
        let metricsContainer = this.$el.find('.metric-descriptions-container');
        if (metricsContainer.hasClass('hide')) {
            metricsContainer.css('top', this.el.offsetTop);
            metricsContainer.removeClass('hide');
            this.layout.$el.find('.toggle-metric-definitions-btn .sicon').addClass('text-[--sicon-hover-color]');
        } else {
            metricsContainer.addClass('hide');
            this.layout.$el.find('.toggle-metric-definitions-btn .sicon').removeClass('text-[--sicon-hover-color]');
        }
    },

    /**
     * Loads the metrics data for the dashlet
     *
     * @param options
     */
    loadData: function(options) {
        if (this._isConfig || !this._forecastsIsAvailable || _.keys(this.metrics).length === 0) {
            if (options && options.complete) {
                options.complete();
            }
            return;
        }

        this._loadMetrics(options && options.complete);
    },

    /**
     * Loads all of the metrics configured for this dashlet
     *
     * @param {Function} callback optional callback to run when all metrics are loaded
     * @private
     */
    _loadMetrics: function(callback) {
        _.each(this._activeMetricRequests, function(request) {
            app.api.abortRequest(request.uid);
        });
        this._activeMetricRequests = {};

        _.each(this.metrics, function(metric) {
            this._loadMetric(metric, callback);
        }, this);
    },

    /**
     * Loads a single metric
     * @param metric
     * @param {Function} callback optional callback to run when all metrics are loaded
     * @private
     */
    _loadMetric: function(metric, callback) {
        metric.loading = true;
        this.$el.find(`.plm-${metric.name}`).addClass('metric-skeleton-loader');

        // Build the request arguments
        let args = {
            user_id: this.context.get('selectedUserId'),
            type: this.context.get('selectedUserType'),
            time_period: this.context.get('selectedTimePeriodId'),
            metrics: [metric.name]
        }

        // Build the request callbacks
        let callbacks = {
            success: (metricData) => {
                this._handleMetricLoadSuccess(metric, metricData);
            },
            error: () => {
                this._handleMetricLoadError(metric);
            },
            complete: (request) => {
                this._handleMetricLoadComplete(metric, request, callback);
            }
        };

        let url = app.api.buildURL('Forecasts/metrics/named');
        let request = app.api.call('create', url, args, callbacks);
        this._activeMetricRequests[request.uid] = request;
    },

    /**
     * Handles when a single metric has been calculated
     *
     * @param metric
     * @param metricData
     * @private
     */
    _handleMetricLoadSuccess: function(metric, metricData) {
        let metricResults = metricData && metricData[metric.name] || {};
        switch (metricResults.type) {
            case 'currency':
                metric.results = this._formatCurrencyMetricResult(metricResults.value || 0);
                break;
            case 'ratio':
                metric.results = this._formatRatioMetricResult(metricResults.value || 0);
                break;
            case 'float':
                metric.results = this._formatFloatMetricResult(metricResults.value || 0);
                break;
            default:
                metric.results = this._formatNumberMetricResult(metricResults.value || 0);
        }
    },

    /**
     * Handles when a single metric encounters an error while being calculated
     * @param {Object} metric the metric that produced the loading error
     * @private
     */
    _handleMetricLoadError: function(metric) {
        metric.results = this._formatCurrencyMetricResult(0);
    },

    /**
     * Handles when a single metric has completely finished loading
     *
     * @param {Object} metric the loaded metric
     * @param {Object} request the request object used to load the Object
     * @param {Function} callback optional callback function to run when all
     *                            metrics have been loaded
     * @private
     */
    _handleMetricLoadComplete: function(metric, request, callback) {
        metric.loading = false;
        delete this._activeMetricRequests[request.uid];
        if (_.isEmpty(this._activeMetricRequests) && _.isFunction(callback)) {
            callback();
        }
        this._rerenderMetric(metric);
    },

    /**
     * Formats the results of a currency metric
     * @param {number} amount the currency amount to format
     * @return {Object} an object containing the formatted metric information
     * @private
     */
    _formatCurrencyMetricResult: function(amount) {
        amount = _.isFinite(amount) ? amount : 0;
        let systemCurrency = app.currency.getBaseCurrencyId();
        let userPrefCurrency = app.user.getPreference('currency_id');
        if (systemCurrency === userPrefCurrency) {
            return {
                value: app.currency.formatAmountLocale(amount, systemCurrency, 0),
                tooltip: app.currency.formatAmountLocale(amount, systemCurrency)
            };
        } else {
            let convertedAmount = app.currency.convertAmount(amount, systemCurrency, userPrefCurrency);
            return {
                value: app.currency.formatAmountLocale(amount, systemCurrency, 0),
                convertedValue: app.currency.formatAmountLocale(convertedAmount, userPrefCurrency, 0),
                tooltip: `${app.currency.formatAmountLocale(amount, systemCurrency)} | ` +
                    `${app.currency.formatAmountLocale(convertedAmount, userPrefCurrency)}`
            };
        }
    },

    /**
     * Formats the results of a 'ratio' metric
     * @param {number} value the ratio value to format
     * @return {Object} an object containing the formatted metric information: value & tooltip
     * @private
     */
    _formatRatioMetricResult: function(value) {
        let percentage = (value || 0) * 100;
        return {
            value: `${app.utils.formatNumber(percentage, 0, 0)}%`,
            tooltip: `${app.utils.formatNumberLocale(percentage)}%`
        };
    },

    /**
     * Formats the results of a 'float' metric
     * @param {number} value the float value to format
     * @return {Object} an object containing the formatted metric information: value & tooltip
     * @private
     */
    _formatFloatMetricResult: function(value) {
        let outputValue = (value) ? `${app.utils.formatNumber(value, 0, 1)}` : 0;

        return {
            value: `${outputValue}x`,
            tooltip: `${app.utils.formatNumberLocale(value)}x`
        };
    },

    /**
     * Formats the results of a 'number' metric
     * @param {number} value the number value to format
     * @return {Object} an object containing the formatted metric information: value & tooltip
     * @private
     */
    _formatNumberMetricResult: function(value) {
        return {
            value: `${app.utils.formatNumber(value, 0, 0)}`,
            tooltip: `${app.utils.formatNumberLocale(value)}`
        };
    },

    /**
     * Re-renders the given metric in the dashlet
     *
     * @param {Object} metric the metric to re-render
     * @private
     */
    _rerenderMetric: function(metric) {
        let metricTemplate = app.template.getView('pipeline-metrics.metric', this.module);
        if (_.isFunction(metricTemplate)) {
            this.$el.find(`.plm-${metric.name}`).replaceWith(metricTemplate(metric));
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.layout) {
            this.layout.offBefore('dashletconfig:save', this._validateConfig, this);
        }
        this.stopListening();
        this._stopAutoRefresh();
        this._super('_dispose');
    }
})

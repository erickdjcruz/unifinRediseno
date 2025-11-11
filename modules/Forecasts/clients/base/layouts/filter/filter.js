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
 * @class View.Layouts.Base.Forecasts.FilterLayout
 * @alias SUGAR.App.view.layouts.BaseForecastsFilterLayout
 * @extends View.Layouts.Base.FilterLayout
 */
({
    extendsFrom: 'BaseFilterLayout',

    /**
     * A list of activeMetrics
     *
     * @property {Array}
     */
    activeMetrics: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.activeMetrics = [];
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(
            this.layout.context,
            'filter:selectedTimePeriod:changed filter:selectedUser:changed',
            this._retriggerFilter
        );
        this.listenTo(this.layout, 'forecast:metric:active', this._handleActiveMetricsChange);
    },

    /**
     * Sets the activeMetrics class property when the metrics are changed
     * @param array activeMetrics list of active metrics
     * @private
     */
    _handleActiveMetricsChange: function(activeMetrics) {
        this.activeMetrics = activeMetrics;
        this._retriggerFilter();
    },

    /**
     * Gets the query string from the quicksearch bar and triggers the filter:apply event to reapply the filters
     * @private
     */
    _retriggerFilter: function() {
        let query = this.$('input.search-name').val() || '';
        this.trigger('filter:apply', query, undefined, {showAlerts: false});
    },

    /**
     * @inheritdoc
     *
     * Passes in Forecast-specific data to the filter API
     */
    _getCollectionParams() {
        let forecastContext = this.layout.context;
        let selectedUser = forecastContext.get('selectedUser') || {};

        return {
            user_id: selectedUser.id || '',
            type: forecastContext.get('forecastType') || '',
            time_period: forecastContext.get('selectedTimePeriod') || '',
            metrics: this.activeMetrics
        };
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
})

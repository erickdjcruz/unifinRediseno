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
 * @class View.Fields.Base.Forecasts.forecastMetricField
 * @alias SUGAR.App.view.fields.BaseForecastsForecastMetricField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * Attach toggle active function to clicking a metric
     */
    events: {
        'click .forecast-metric': 'toggleActive'
    },
    /**
     * Holds if the users currency doesn't match the system default
     */
    alternateCurrency: undefined,
    /**
     * Determines if field is active
     */
    active: false,

    /**
     *  Holds the count of records for this metric
     */
    recordCount: 0,

    /**
     * Holds the currency converted value of the metric
     */
    convertedValue: 0,

    /**
     * Holds the currency converted value of the metric
     */
    convertedValueString: '0.00',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.alternateCurrency = (app.user.getPreference('currency_id') !== app.currency.getBaseCurrencyId());
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.formatAlternateCurrency();
        this.recordCount = this.model.get(`${this.name}_count`);
        let activeMetric = this.model.get('active');
        this.active = (_.isArray(activeMetric)) ? _.includes(activeMetric, this.name) : (activeMetric === this.name);
        this._super('_render');
    },

    /**
     * Formats alternateCurrency to the users perfered currecny string.
     */
    formatAlternateCurrency: function() {
        if (this.alternateCurrency) {
            let userCurrencyID = app.user.getPreference('currency_id');
            this.convertedValue = app.currency.convertAmount(
                this.model.get(this.name),
                app.currency.getBaseCurrencyId(),
                userCurrencyID
            );

            this.convertedValueString = app.currency.formatAmountLocale(
                this.convertedValue,
                app.user.getPreference('currency_id')
            );
        }
    },

    /**
     * Switches the active metric when a metric is clicked.
     */
    toggleActive: function() {
        if (!this.active) {
            this.model.set('lastActive', app.user.lastState.get(this.view.lastStateKey) || []);
            this.model.set('active', this.name);
        }
    }
})

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
 * @class View.Views.Base.ForecastsListHeaderpaneView
 * @alias SUGAR.App.view.layouts.BaseForecastsListHeaderpaneView
 * @extends View.Views.Base.ListHeaderpaneView
 */
({
    extendsFrom: 'HeaderpaneView',

    plugins: ['FieldErrorCollection'],

    /**
     * If Forecasts' data sync is complete and we can render buttons
     * @type Boolean
     */
    forecastSyncComplete: false,

    /**
     * Holds the prefix string that is rendered before the same of the user
     * @type String
     */
    forecastWorksheetLabel: '',

    /**
     * Timeperiod model
     */
    tpModel: undefined,

    /**
     * Current quarter label id
     *
     * @type String
     */
    currentTimePeriodId: undefined,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.tpModel = new Backbone.Model();
        this._super('initialize', [options]);
        this.currentTimePeriodId = this.context.get('selectedTimePeriod');
        this.resetSelection(this.currentTimePeriodId);

        // Update label for worksheet
        let selectedUser = this.context.get('selectedUser');
        if (selectedUser) {
            this._title = this._getForecastWorksheetLabel(selectedUser);
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.tpModel.on('change', function(model) {
            let selectedTimePeriodId = model.get('selectedTimePeriod');
            this.context.trigger(
                'forecasts:timeperiod:changed',
                model,
                this.getField('selectedTimePeriod').tpTooltipMap[selectedTimePeriodId]);
        }, this);

        this.context.on('forecasts:timeperiod:canceled', function() {
            this.resetSelection(this.tpModel.previous('selectedTimePeriod'));
        }, this);

        this.layout.context.on('forecasts:sync:start', function() {
            this.forecastSyncComplete = false;
        }, this);

        this.layout.context.on('forecasts:sync:complete', function() {
            this.forecastSyncComplete = true;
        }, this);

        this.context.on('change:selectedUser', function(model, changed) {
            app.user.lastState.set('Forecasts:selected-user', changed);
            this._title = this._getForecastWorksheetLabel(changed);
            if (!this.disposed) {
                this.render();
            }
        }, this);

        this.context.on('plugin:fieldErrorCollection:hasFieldErrors', function(collection, hasErrors) {
            if(this.fieldHasErrorState !== hasErrors) {
                this.fieldHasErrorState = hasErrors;
            }
        }, this);

        this.context.on('button:print_button:click', function() {
            window.print();
        }, this);

        this._super('bindDataChange');
    },

    /**
     * Gets the current worksheet type
     * @return {string} Either "Rollup" or "Direct". Returns empty string if current user could not be found
     * @private
     */
    _getWorksheetType: function() {
        let selectedUser = this.context.get('selectedUser');
        if (!selectedUser) {
            return '';
        }
        return app.utils.getForecastType(selectedUser.is_manager, selectedUser.showOpps);
    },

    /**
     * Gets the correct language label dependent on "Rollup" vs "Direct" worksheet
     * @param {*} selectedUser The current user whose worksheet is being viewed, stored in this.context
     * @return {string}
     * @private
     */
    _getForecastWorksheetLabel: function(selectedUser) {
        return this._getWorksheetType() === 'Rollup' ?
            app.lang.get('LBL_RU_TEAM_FORECAST_HEADER', this.module, {name: selectedUser.full_name}) :
            app.lang.get('LBL_FDR_FORECAST_HEADER',
                this.module,
                {name: selectedUser.full_name}
            );
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        if(!this._title) {
            var user = this.context.get('selectedUser') || app.user.toJSON();
            this._title = user.full_name;
        }

        this._super('_renderHtml');

        this.listenTo(this.getField('selectedTimePeriod'), 'render', function() {
            this.markCurrentTimePeriod(this.tpModel.get('selectedTimePeriod'));
        }, this);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if(this.layout.context) {
            this.layout.context.off('forecasts:sync:start', null, this);
            this.layout.context.off('forecasts:sync:complete', null, this);
        }
        this.stopListening();
        this._super('_dispose');
    },

    /**
     * Sets the timeperiod to the selected timeperiod, used primarily for resetting
     * the dropdown on nav cancel
     *
     * @param String timeperiodId
     */
    resetSelection: function(timeperiodId) {
        this.tpModel.set({selectedTimePeriod: timeperiodId}, {silent: true});
        _.find(this.fields, function(field) {
            if (_.isEqual(field.name, 'selectedTimePeriod')) {
                field.render();
                return true;
            }
        });
    },

    /**
     * Get year and quarter
     *
     * @param {Object} d  timeperiodId
     * @return array
     */
    getQuarter: function(d) {
        d = d || app.date();
        const month = parseInt(d.format('MM'));
        let q = Math.floor((month - 1) / 3) + 1;
        let y = d.format('YYYY');
        return [y, q];
    },

    /**
     * Get month and year
     *
     * @param {Object} d  timeperiodId
     * @return string
     */
    getMonth: function(d) {
        d = d || app.date();
        return d.format('MMMM YYYY');
    },

    /**
     * Mark the current time period with 'Current' label
     *
     * @param String selectedTimePeriodId
     */
    markCurrentTimePeriod: function(selectedTimePeriodId) {
        let listTimePeriods = this.getField('selectedTimePeriod') ? this.getField('selectedTimePeriod').items : null;
        if (!listTimePeriods) {
            return;
        }

        let timePeriodInterval = app.metadata.getModule('Forecasts', 'config').timeperiod_leaf_interval;
        let currentTimePeriod = timePeriodInterval === 'Quarter' ? this.getQuarter().join(' Q') : this.getMonth();
        let currentTimePeriodId = _.findKey(listTimePeriods, item => item === currentTimePeriod);
        if (!currentTimePeriodId) {
            return;
        }

        let currentTimePeriodText = app.lang.get('LBL_CURRENT', this.module) +
            ' (' + listTimePeriods[currentTimePeriodId] + ')';
        listTimePeriods[currentTimePeriodId] = currentTimePeriodText;
        if (selectedTimePeriodId === currentTimePeriodId) {
            this.$('.quarter-picker .forecastsTimeperiod .select2-chosen').text(currentTimePeriodText);
        }
    }
})

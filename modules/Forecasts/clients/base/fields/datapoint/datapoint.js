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
 * Datapoints in the info pane for Forecasts
 *
 * @class View.Fields.Base.Forecasts.DatapointField
 * @alias SUGAR.App.view.fields.BaseForecastsDatapointField
 * @extends View.Fields.Base.BaseField
 */
({

    /**
     * Boolean track if current user is manager
     */
    isManager: '',

    /**
     * Arrow Colors
     */
    arrow: '',

    /**
     * The total we want to display
     */
    total: 0,

    /**
     * The total for RLIs to display
     */
    rliTotal: 0,

    /**
     * The last committed value for this datapoint
     */
    lastCommit: 0,

    /**
     * Can we actually display this field and have the data binding on it
     */
    hasAccess: true,

    /**
     * Do we have access from the ForecastWorksheet Level to show data here?
     */
    hasDataAccess: true,

    /**
     * What to show when we don't have access to the data
     */
    noDataAccessTemplate: undefined,

    /**
     * Holds the totals field name
     */
    total_field: '',

    cteTag: '.forecast-value-input',

    /**
     * When true, the field will not be editable
     */
    disableCTE: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['ClickToEdit']);
        this._super('initialize', [options]);

        this.total_field = this.total_field || this.name;

        this.hasAccess = app.utils.getColumnVisFromKeyMap(this.name, 'forecastsWorksheet');
        this.hasDataAccess = app.acl.hasAccess('read', 'ForecastWorksheets', app.user.get('id'), this.name);
        if (this.hasDataAccess === false) {
            this.noDataAccessTemplate = app.template.getField('base', 'noaccess')(this);
        }

        // before we try and render, lets see if we can actually render this field
        this.before('render', function() {
            return this.hasAccess;
        }, this);
        //if user resizes browser, adjust datapoint layout accordingly
        $(window).on('resize.datapoints', _.bind(this.resize, this));
        this.listenTo(this, 'render', function() {
            if (!this.hasAccess) {
                return false;
            }
            this.resize();
            return true;
        });
    },

    /**
     * @inheritdoc
     *
     * Formats the value as a number string
     */
    format: function(value) {
        if (this.tplName === 'edit') {
            return app.utils.formatNumberLocale(value);
        }
        return this._super('format', [value]);
    },

    /**
     * @inheritdoc
     *
     * Unformats the value from a number string
     */
    unformat: function(value) {
        let unformattedValue;
        if (this.tplName === 'edit') {
            unformattedValue = app.utils.unformatNumberStringLocale(value);
        } else {
            unformattedValue = app.currency.unformatAmountLocale(value);
        }

        if (_.isFinite(unformattedValue)) {
            var precision = this.def && this.def.precision || 6;
            return app.math.round(unformattedValue, precision, true);
        }

        return value;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.isManager = ('manager' === this.getUserCurrentRole());

        // Set the correct arrow style depending on the current Forecast state
        this.arrow = this._getArrowIconColorClass(this.model.get(this.name), this.model.getSynced(this.name));
        this.isRLIMode = (app.metadata.getModule('Opportunities', 'config').opps_view_by === 'RevenueLineItems');
        this.checkEditAccess();

        this._super('_render');
    },

    /**
     * If a user is viewing someone else's forecast page, it will disable the user's
     * ability to edit the commitment value.
     */
    checkEditAccess: function() {
        if (this.context && app.user && this.context.get('selectedUser') &&
            this.context.get('selectedUser').id === app.user.get('id')) {
            this.disableCTE = false;
        } else {
            this.disableCTE = true;
        }
    },

    /**
     * Check to see if the worksheet needs commit
     *
     * @deprecated since 12.0, this is no longer used
     */
    checkIfNeedsCommit: function() {
        // if the initial_total is an empty string (default value) don't run this
        if (!_.isEqual(this.initial_total, '') && app.math.isDifferentWithPrecision(this.total, this.initial_total)) {

            this.context.trigger('forecasts:worksheet:needs_commit', null);
        }
    },

    /**
     * Overwrite this to only place the placeholder if we actually have access to view it
     *
     * @return {*}
     */
    getPlaceholder: function() {
        if (this.hasAccess) {
            return this._super('getPlaceholder');
        }

        return '';
    },

    /**
     * Adjusts the CSS for the datapoint
     */
    adjustDatapointLayout: function() {
        if (this.hasAccess) {
            var parentMarginLeft = this.view.$('.topline .datapoints').css('margin-left'),
                parentMarginRight = this.view.$('.topline .datapoints').css('margin-right'),
                timePeriodWidth = this.view.$('.topline .span4').outerWidth(true),
                toplineWidth = this.view.$('.topline ').width(),
                collection = this.view.$('.topline div.pull-right').children('span'),
                collectionWidth = parseInt(parentMarginLeft) + parseInt(parentMarginRight);

            collection.each(function(index) {
                collectionWidth += $(this).children('div.datapoint').outerWidth(true);
            });

            //change width of datapoint div to span entire row to make room for more numbers
            if ((collectionWidth + timePeriodWidth) > toplineWidth) {
                this.view.$('.topline div.hr').show();
                this.view.$('.info .last-commit').find('div.hr').show();
                this.view.$('.topline .datapoints').removeClass('span8').addClass('span12');
                this.view.$('.info .last-commit .datapoints').removeClass('span8').addClass('span12');
                this.view.$('.info .last-commit .commit-date').removeClass('span4').addClass('span12');

            } else {
                this.view.$('.topline div.hr').hide();
                this.view.$('.info .last-commit').find('div.hr').hide();
                this.view.$('.topline .datapoints').removeClass('span12').addClass('span8');
                this.view.$('.info .last-commit .datapoints').removeClass('span12').addClass('span8');
                this.view.$('.info .last-commit .commit-date').removeClass('span12').addClass('span4');
                var lastCommitHeight = this.view.$('.info .last-commit .commit-date').height();
                this.view.$('.info .last-commit .datapoints div.datapoint').height(lastCommitHeight);
            }
            //adjust height of last commit datapoints
            let index = this.$el.index();
            let width = this.$('div.datapoint').innerWidth(); // USE innerWidth?
            let datapointLength = this.view.$('.info .last-commit .datapoints div.datapoint').length;
            let sel = this.view.$('.last-commit .datapoints div.datapoint:nth-child(' + index + ')');

            if (datapointLength > 2 && index <= 2 || datapointLength == 2 && index == 1) {
                // RTL was off 1px
                var widthMod = (app.lang.direction === 'rtl') ? 7 : 16;
                $(sel).width(width - widthMod);
            } else {
                // Minus 16 for padding-x 0.5rem (8px)
                $(sel).width(width - 16);
            }
        }
    },

    /**
     * Resizes the datapoint on window resize
     */
    resize: function() {
        //The resize event is fired many times during the resize process. We want to be sure the user has finished
        //resizing the window that's why we set a timer so the code should be executed only once
        if (this.resizeDetectTimer) {
            clearTimeout(this.resizeDetectTimer);
        }
        this.resizeDetectTimer = setTimeout(_.bind(function() {
            this.adjustDatapointLayout();
        }, this), 250);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (!this.hasAccess) {
            return;
        }

        this.listenTo(this.context, 'change:selectedUser change:selectedTimePeriod', function() {
            this.lastCommit = 0;
            this.total = 0;
            this.rliTotal = 0;
            this.arrow = '';
        });

        this.listenTo(this.context, 'forecasts:commit-models:loaded', this._handleCommitModelsLoaded);
        this.listenTo(this.context, 'forecasts:worksheet:totals', this._onWorksheetTotals);
        this.listenTo(this.context, 'forecasts:worksheet:committed', this._onWorksheetCommit);
        this.listenTo(this.model, `change:${this.name}`, this._handleValueChanged);
    },

    /**
     * Updates the last commited value for this datapoint when the last
     * commit model is loaded
     *
     * @private
     */
    _handleCommitModelsLoaded: function() {
        let lastCommitModel = this.context.get('lastCommitModel');
        if (lastCommitModel instanceof Backbone.Model) {
            this.lastCommit = lastCommitModel.get(this.name) || 0;
        }
        this.render();
    },

    /**
     * When the value is changed from its synced/initial value, signal the
     * context so it can enable the cancel/commit buttons properly
     *
     * @private
     */
    _handleValueChanged: function() {
        let syncedValue = this.model.getSynced(this.name);
        let change = this.model.changedAttributes()[this.name];
        if (!_.isEqual(syncedValue, change)) {
            this.context.trigger('forecasts:datapoint:changed');
        }

        // Render is normally bound to data change on the model from
        // bindDataChange in field.js, but since the model can change
        // throughout the life of this field we need to render here
        this.render();
    },

    /**
     * Collection Reset Handler
     * @param {Backbone.Collection} collection
     * @private
     *
     * @deprecated since 12.0 this is no longer used
     */
    _onCommitCollectionReset: function(collection) {
        // get the first line
        var model = _.first(collection.models);
        if (!_.isUndefined(model)) {
            this.initial_total = model.get(this.total_field);
            if (!this.disposed) {
                this.render();
            }
        }
    },

    /**
     * Worksheet Totals Handler
     * @param {Object} totals       The totals from the worksheet
     * @param {String} type         Which worksheet are we dealing with it
     * @private
     */
    _onWorksheetTotals: function(totals, type) {
        if (this.disposed) {
            return;
        }

        var field = this.total_field;
        if (type == 'manager') {
            // split off '_case'
            field = field.split('_')[0] + '_adjusted';
        }
        this.total = totals[field];
        this.rliTotal = totals['rli_' + field];
        this.render();
    },

    /**
     * What to do when the worksheet is committed
     *
     * @param {String} type     What type of worksheet was committed
     * @param {Object} forecast What was committed for the timeperiod
     * @private
     */
    _onWorksheetCommit: function(type, forecast) {
        if (this.disposed) {
            return;
        }
        this.arrow = '';
        this.render();
    },

    /**
     * Returns the CSS classes for an up or down arrow icon
     *
     * @param {String|Number} newValue the new value
     * @param {String|Number} oldValue the previous value
     * @return {String} css classes for up or down arrow icons, if the values didn't change, returns ''
     * @private
     */
    _getArrowIconColorClass: function(newValue, oldValue) {
        // Make sure newValue and oldValue are numbers. If not, default them
        // to 0
        let newValueParsed =  parseFloat(newValue);
        let newValueFinal = !_.isNaN(newValueParsed) ? newValueParsed : 0;
        let oldValueParsed = parseFloat(oldValue);
        let oldValueFinal = !_.isNaN(oldValueParsed) ? oldValueParsed : 0;

        // Convert the values to Bigs first before any comparisons are done
        let newValueBig = Big(newValueFinal);
        let oldValueBig = Big(oldValueFinal);

        // Determine which class the arrow icon should use
        let arrowIconClass = '';
        if (app.math.isDifferentWithPrecision(newValueBig, oldValueBig)) {
            arrowIconClass = newValueBig.gt(oldValueBig) ? ' sicon-arrow-up font-green' : ' sicon-arrow-down font-red';
        }
        return arrowIconClass;
    },

    /**
     * Gets user current role (the role under which the user is viewing the worksheet at the moment)
     * @return {string} "manager" or "seller"
     *
     */
    getUserCurrentRole: function() {
        let user = this.context.get('selectedUser') || app.user.toJSON();
        let forecastType = app.utils.getForecastType(user.is_manager, user.showOpps);

        return (forecastType === 'Direct') ? 'seller' : 'manager';
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        // Clear any listeners
        $(window).off('resize.datapoints');
        this.stopListening();

        // make sure we've cleared the resize timer before navigating away
        clearInterval(this.resizeDetectTimer);

        this._super('_dispose');
    }
})

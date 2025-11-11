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
 * @class View.Fields.Base.Forecasts.LastcommitField
 * @alias SUGAR.App.view.fields.BaseForecastsLastcommitField
 * @extends View.Fields.Base.BaseField
 */
({

    commit_date: undefined,

    points: [],

    events: {
        'click': 'triggerHistoryLog'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.points = [];

        // map what points we should display
        _.each(options.def.datapoints, function(point) {
            if (app.utils.getColumnVisFromKeyMap(point, 'forecastsWorksheet')) {
                this.points.push(point);
            }
        }, this);
    },

    /**
     * Toggles the commit history log
     */
    triggerHistoryLog: function() {
        this.$('#show_hide_history_log').toggleClass('sicon-chevron-down').toggleClass('sicon-chevron-up');
        this.context.trigger('forecast:commit_log:trigger');
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.collection.on('reset', function() {
            // Get the latest commit model
            var model = _.first(this.collection.models);

            if (!_.isUndefined(model)) {
                this.commit_date = model.get('date_modified');
                //FIXME: SS-2576 We should determine a better way to verify
                //server time and bowser having small differences in time
                let commitDate = app.date(this.commit_date);
                let currentTime = app.date();

                if (currentTime.isBefore(commitDate)) {
                    this.commit_date = currentTime.format(commitDate._f);
                }
            } else {
                this.commit_date = undefined;
            }

            if (!this.disposed) {
                this.render();
            }
            //Toggles the arrow to keep arrow consistent when switching type of
            //worksheet
            if (this.context.get('forecastType') === 'Direct') {
                this.$('#show_hide_history_log').toggleClass('sicon-chevron-down').toggleClass('sicon-chevron-up');
            }
        }, this);
    },

    /**
     * Processes a Forecast collection's models into datapoints
     * @param {Bean} model
     * @returns {Array}
     * @deprecated since 12.1.0, this is no longer used
     */
    processDataPoints: function(model) {
        var points = [],
            noAccessTemplate = app.template.getField('base', 'noaccess')(this);

        _.each(this.points, function(point) {
            // make sure we can view data for this point
            var point_data = {};
            if (app.acl.hasAccess('read', 'ForecastWorksheets', app.user.get('id'), point)) {
                point_data.value = model.get(point)
            } else {
                point_data.error = noAccessTemplate;
            }
            points.push(point_data);
        }, this);

        return points;
    }
})

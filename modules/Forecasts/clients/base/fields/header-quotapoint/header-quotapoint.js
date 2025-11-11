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
 * @class View.Fields.Base.Forecasts.QuotaHeaderQuotapointField
 * @alias SUGAR.App.view.fields.BaseForecastsHeaderQuotapointField
 * Field
 * @extends View.Fields.Base.BaseForecastsQuotapointField
 */

({
    extendsFrom: 'ForecastsQuotapointField',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('change:selectedUser', function(ctx, user) {
            //The context sometimes gets cleaned up from an asyncronous call.
            if (this.context) {
                this.selectedUser = user;

                // reload data when the selectedTimePeriod changes
                this.loadData({});
            }
        }, this);

        this.context.on('change:selectedTimePeriod', function(ctx, timePeriod) {
            //The context sometimes gets cleaned up from an asyncronous call.
            if (this.context) {
                this.selectedTimePeriod = timePeriod;

                // reload data when the selectedTimePeriod changes
                this.loadData({});
            }
        }, this);

        this.listenTo(this.context, 'forecasts:refreshList', this.loadData);

        this.loadData();
    },

    resize: function() {
        //We don't want to fire the parents resize function so we override it
        //with this empty function.
    }
})

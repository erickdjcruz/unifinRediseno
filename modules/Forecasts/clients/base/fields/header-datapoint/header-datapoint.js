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
 * @class View.Fields.Base.Forecasts.HeaderDatapointField
 * @alias SUGAR.App.view.fields.BaseForecastsHeaderDatapointField
 * @extends View.Fields.Base.BaseField
 */
({
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
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['ClickToEdit']);
        this._super('initialize', [options]);

        this.total_field = this.total_field || this.name;
        this.model = this.context.get('nextCommitModel');
        this.hasAccess = app.utils.getColumnVisFromKeyMap(this.name, 'forecastsWorksheet');
        this.hasDataAccess = app.acl.hasAccess('read', 'ForecastWorksheets', app.user.get('id'), this.name);
        if (this.hasDataAccess === false) {
            this.noDataAccessTemplate = app.template.getField('base', 'noaccess')(this);
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
})

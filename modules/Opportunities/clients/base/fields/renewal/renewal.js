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
 * @class View.Fields.Base.Opportunities.RenewalField
 * @alias SUGAR.App.view.fields.BaseOpportunitiesRenewalField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     *
     * This field doesn't support `showNoData`.
     */
    showNoData: false,
    month: '',
    year: '',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.def.readonly = true;
        var date = this.model.get('date_closed');
        this.month = app.date(date).format('MMMM');
        this.year = app.date(date).format('YYYY');
        this._super('initialize', [options]);
    },

    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:date_closed', function() {
            var date = this.model.get('date_closed');
            this.month = app.date(date).format('MMMM');
            this.year = app.date(date).format('YYYY');
            this.render();
        }, this);
    },
})

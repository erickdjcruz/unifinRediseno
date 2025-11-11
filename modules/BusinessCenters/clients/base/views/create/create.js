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
 * @class View.Views.Base.BusinessCenters.CreateView
 * @alias SUGAR.App.view.views.BusinessCentersCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._days = app.view.views.BaseBusinessCentersRecordView.prototype._days;
        this.model && this.model.addValidationTask(
            'all_open_hours_before_close_hours_' + this.cid,
            _.bind(this.validateBusinessHours, this)
        );
    },

    validateBusinessHours: function() {
        // to avoid duplicating substantial code, borrow the implementation from the record view
        var args = Array.prototype.slice.call(arguments);
        return app.view.views.BaseBusinessCentersRecordView.prototype.validateBusinessHours.apply(this, args);
    }
})

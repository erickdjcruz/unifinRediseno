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
 * @class View.Fields.Base.PurchasedLineItems.DatesNameField
 * @alias SUGAR.App.view.fields.BasePurchasedLineItemsDatesNameField
 * @extends View.Fields.Base.NameField
 */
({
    extendsFrom: 'NameField',

    /**
     * Format date according to user preferences. This is copied from the date field controller
     * @param rawValue
     * @return {string}
     * @private
     */
    _formatDate: function(rawValue) {
        let value = app.date(rawValue);

        if (!value.isValid()) {
            return '';
        }

        return value.formatUser(true);
    },

    /**
     * Formats the relevant date fields in addition to the base name field
     * @param value
     * @return {string}
     */
    format: function(value) {
        if (this.model && this.model.has('service_start_date') && this.model.has('service_end_date')) {
            this.serviceStartDate = this._formatDate(this.model.get('service_start_date'));
            this.serviceEndDate = this._formatDate(this.model.get('service_end_date'));
        }

        return this._super('format', [value]);
    }
})

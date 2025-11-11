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
 * @class View.Fields.Base.Products.DiscountField
 * @alias SUGAR.App.view.fields.BaseProductsDiscountField
 * @extends View.Fields.Base.CurrencyField
 */
({
    extendsFrom: 'DiscountField',

    /**
     * @inheritdoc
     *
     * Adds special code for QLI discount rendering on subpanels and Quotes record view
     */
    format: function(value) {
        if (app.utils.isTruthy(this.model.get(this.discountFieldName))) {
            return app.utils.formatNumberLocale(value);
        } else {
            //In edit mode hide the currency dropdown for the discount field
            this.hideCurrencyDropdown = this.tplName === 'edit' ? true : false;
            return this.formatForCurrency(value);
        }
    },

    /**
     * Formats the field to show correctly on Quotes record view and QLI subpanels
     *
     * @param value
     * @return {string|*}
     */
    formatForCurrency: function(value) {
        // Skipping the core currencyField call
        // app.view.Field.prototype.format.call(this, value);
        this._super('format', [value]);

        //Check if in 'Edit' mode
        if (this.tplName === 'edit') {
            //Display just currency value without currency symbol when entering edit mode for the first time
            //We want the correct value in input field corresponding to the currency in the dropdown
            //Example: Dropdown has Euro then display '100.00' instead of '$111.11'
            return app.utils.formatNumberLocale(value);
        }

        var transactionalCurrencyId = this.model.get(this.def.currency_field || 'currency_id');
        var convertedCurrencyId = transactionalCurrencyId;
        var origTransactionValue = value;

        // If necessary, do a conversion to the preferred currency. Otherwise,
        // just display the currency as-is.
        var preferredCurrencyId = this.getPreferredCurrencyId();
        if (preferredCurrencyId && preferredCurrencyId !== transactionalCurrencyId) {
            convertedCurrencyId = preferredCurrencyId;

            this.transactionValue = app.currency.formatAmountLocale(
                this.model.get(this.name) || 0,
                transactionalCurrencyId
            );

            value = app.currency.convertWithRate(
                value,
                this.model.get('base_rate'),
                app.metadata.getCurrency(preferredCurrencyId).conversion_rate
            );
        } else {
            // user preferred same as transactional, no conversion required
            this.transactionValue = '';
            convertedCurrencyId = transactionalCurrencyId;
            value = origTransactionValue;
        }
        return app.currency.formatAmountLocale(value, convertedCurrencyId);
    },

    /**
     * Determines the correct preferred currency ID to convert to depending on
     * the context this currency field is being displayed in
     * @return {string|undefined} the ID of the preferred currency if it exists
     */
    getPreferredCurrencyId: function() {
        // If this is a QLI subpanel, and the user has opted to show in their
        // preferred currency, use that currency. Otherwise, use the system currency.
        if (this.context.get('isSubpanel')) {
            if (app.user.getPreference('currency_show_preferred')) {
                return app.user.getPreference('currency_id');
            }
            return app.currency.getBaseCurrencyId();
        }

        // Get the preferred currency of the parent context or this context. For
        // Quotes record view, this will get the Quote's preferred currency
        var context = this.context.parent || this.context;
        return context.get('model').get('currency_id');
    }
})

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
 * @class View.Fields.Base.CurrenciesTextField
 * @alias App.view.fields.BaseCurrenciesTextField
 * @extends View.Fields.Base.TextField
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // If the ISO 4217 field is changed to a valid code, automatically fill the symbol
        if (this.name === 'symbol') {
            this.model.on('change:iso4217', (model, iso4217) => {
                if (this.action === 'edit' &&
                    iso4217 !== '' &&
                    model.get('id') !== '-99' &&
                    app.lang.getAppListKeys('iso_currency_symbol').includes(iso4217)
                ) {
                    this.model.set(this.name, app.lang.getAppListStrings('iso_currency_symbol')[iso4217]);
                }
            });
        }
    }
})

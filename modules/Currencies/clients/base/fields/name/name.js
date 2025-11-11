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
 * @class View.Fields.Base.CurrenciesNameField
 * @alias App.view.fields.BaseCurrenciesNameField
 * @extends View.Fields.Base.NameField
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // If the ISO 4217 field is changed to a valid code, automatically fill the currency name
        this.model.on('change:iso4217', (model, iso4217) => {
            if (this.action === 'edit' &&
                iso4217 !== '' &&
                model.get('id') !== '-99' &&
                app.lang.getAppListKeys('iso_currency_name').includes(iso4217)
            ) {
                this.model.set(this.name, app.lang.getAppListStrings('iso_currency_name')[iso4217]);
            }
        });

        // Set the system currency name
        this.listenTo(this.model, 'change:name', (model, name) => {
            if (name !== '' && model.get('id') === '-99') {
                model.set(this.name, app.lang.get('LBL_CURRENCY_DEFAULT', 'Currencies'));
            }
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
})

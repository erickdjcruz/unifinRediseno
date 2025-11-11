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
 * @class View.Views.Base.Currencies.DashablerecordView
 * @alias SUGAR.App.view.views.BaseCurrenciesDashablerecordView
 * @extends View.Views.Base.DashablerecordView
 */
({
    extendsFrom: 'DashablerecordView',
    isBase: false,

    /**
     * @inheritdoc
     */
    _setReadonlyFields: function() {
        this.isBaseCurrency(this.model);
        this._super('_setReadonlyFields');
    },

    /**
     * Checks to see if the model is the base currency
     * @param model
     */
    isBaseCurrency: function(model) {
        if (model && model.get('id') === app.currency.getBaseCurrencyId()) {
            this.isBase = true;
        } else {
            this.isBase = false;
        }
    }
})

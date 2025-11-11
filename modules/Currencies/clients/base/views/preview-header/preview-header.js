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
 * @class View.Views.Base.Currencies.PreviewHeaderView
 * @alias SUGAR.App.view.views.BaseCurrenciesPreviewHeaderView
 * @extends View.Views.Base.PreviewHeaderView
 */
({
    extends: 'PreviewHeaderView',
    isBase: false,

    /**
     * @inheritdoc
     * @override
     */
    triggerEdit: function() {
        //If this isn't the base currency, go ahead and display the edit view
        if (!this.isBase) {
            this._super('triggerEdit');
        }
    },

    /**
     * Checks to see if the model is the base currency
     * @param model
     */
    isBaseCurrency: function(model) {
        if (model && _.isFunction(model.get) && model.get('id') === app.currency.getBaseCurrencyId()) {
            this.isBase = true;
        } else {
            this.isBase = false;
        }
    },

    /**
     *  @inheritdoc
     */
    _render: function() {
        this.isBaseCurrency(this.context.get('model'));

        this._super('_render');
    }
})

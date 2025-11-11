
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
 * @class View.Views.Base.Quotes.ProductCatalogView
 * @alias SUGAR.App.view.views.QuotesProductCatalogView
 * @extends View.Views.Base.ProductCatalogView
 * @deprecated Use {@link View.Views.Base.ProductCatalogView} instead
 */
({
    extendsFrom: 'ProductCatalogView',

    initialize: function(options) {
        app.logger.warn('View.Views.Base.Quotes.ProductCatalogView is deprecated. Use ' +
            'View.Views.Base.ProductCatalogView instead');
        this._super('initialize', [options]);
    }
})

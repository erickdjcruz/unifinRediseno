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
 * @class View.Views.Base.Quotes.ProductCatalogDashletView
 * @alias SUGAR.App.view.views.QuotesProductCatalogDashletView
 * @extends View.Views.Base.ProductCatalogDashletView
 * @deprecated Use {@link View.Views.Base.ProductCatalogDashletView} instead
 */
({
    extendsFrom: 'ProductCatalogDashletView',

    initialize: function(options) {
        app.logger.warn('View.Views.Base.Quotes.ProductCatalogDashletView is deprecated. Use ' +
            'View.Views.Base.ProductCatalogDashletView instead');
        this._super('initialize', [options]);
    }
})

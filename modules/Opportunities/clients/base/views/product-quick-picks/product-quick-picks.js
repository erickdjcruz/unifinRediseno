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
 * @class View.Views.Base.Opportunities.ProductQuickPicksView
 * @alias SUGAR.App.view.views.OpportunitiesProductQuickPicksView
 * @extends View.Views.Base.ProductQuickPicksView
 * @deprecated Use {@link View.Views.Base.ProductQuickPicksView} instead
 */
({
    extendsFrom: 'ProductQuickPicksView',

    initialize: function(options) {
        app.logger.warn('View.Views.Base.Opportunities.ProductQuickPicksView is deprecated. Use ' +
            'View.Views.Base.ProductQuickPicksView instead');
        this._super('initialize', [options]);
    }
})

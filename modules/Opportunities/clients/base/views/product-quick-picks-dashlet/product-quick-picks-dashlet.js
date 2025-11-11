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
 * @class View.Views.Base.OpportunitiesProductQuickPicksDashletView
 * @alias SUGAR.App.view.views.BaseOpportunitiesProductQuickPicksDashletView
 * @extends View.Views.Base.ProductQuickPicksDashletView
 * @deprecated Use {@link View.Views.Base.ProductQuickPicksDashletView} instead
 */
({
    extendsFrom: 'ProductQuickPicksDashletView',

    initialize: function(options) {
        app.logger.warn('View.Views.Base.Opportunities.ProductQuickPicksDashletView is deprecated. Use ' +
            'View.Views.Base.ProductQuickPicksDashletView instead');
        this._super('initialize', [options]);
    }
})

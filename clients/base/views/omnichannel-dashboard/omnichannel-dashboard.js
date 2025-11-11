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
 * @class View.Views.Base.OmnichannelDashboardView
 * @alias SUGAR.App.view.views.BaseOmnichannelDashboardView
 * @extends View.Views.Base.TabbedDashboardView
 */
({
    extendsFrom: 'TabbedDashboardView',

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.template = app.template.getView('tabbed-dashboard');
    },

    /**
     * @inheritdoc
     *
     * Checks to see if we need to set the initial active tab
     *
     * @param {Object} [options] Tab options.
     * @private
     */
    _setTabs: function(options) {
        var omniDashboardLayout = this.layout && this.layout.layout;
        if (omniDashboardLayout && _.isNumber(omniDashboardLayout.initActiveTab)) {
            options.activeTab = omniDashboardLayout.initActiveTab;
            omniDashboardLayout.initActiveTab = null;
        }

        this._super('_setTabs', [options]);
        omniDashboardLayout.activeTab = options.activeTab;
    }
})

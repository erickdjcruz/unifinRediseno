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
 * The dashboard container of the Omnichannel console config.
 *
 * @class View.Layouts.Base.OmnichannelDashboardConfigLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelDashboardConfigLayout
 * @extends View.Layouts.Base.OmnichannelDashboardLayout
 */
({
    extendsFrom: 'OmnichannelDashboardLayout',

    /**
     * Flag to indicate if the open layout is a Configure Layout
     */
    configLayout: true,

    /**
     * Stores the default tab index to switch to when the config dashboard loads
     */
    initActiveTab: 1,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.context.set('config-layout', true);
        this.bindEvents();
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        this._super('_render');
        this._setDummyModels();
    },

    /**
     * Bind events
     */
    bindEvents: function() {
        this.context.on('dashboard:restore-tab:clicked', _.bind(this.restoreTabToDefault, this));
    },

    /**
     * Set empty models on the dashboard so all tabs are visible. We set
     * `dataFetched` on the models to True so that the dashlets render
     * their contents instead of "Loading" while trying to fetch data from
     * backend
     *
     * @private
     */
    _setDummyModels: function() {
        var dashboardModules = _.keys(this.moduleTabIndex);
        _.each(dashboardModules, function(module) {
            var model = app.data.createBean(module);
            model.dataFetched = true;
            var tabIndex = this.moduleTabIndex[module];
            this.setModel(tabIndex, model);
        }, this);
    },

    /**
     * @override
     *
     * Override so the dashboard isn't readonly in the config drawer.
     *
     * @return {Object} Context to set on layout
     * @private
     */
    _getContext: function() {
        return this.context.getChildContext({
            forceNew: true,
            layout: 'omnichannel',
            module: 'Dashboards'
        });
    },

    /**
     * @override
     *
     * Override to add the dashboard-fab from the Dashboards module to the
     * components list.
     *
     * @return {Array} List of components to render
     * @private
     */
    _getDashboardComponents: function() {
        var components = this._super('_getDashboardComponents');
        components.push({
            view: 'dashboard-fab',
            loadModule: 'Dashboards'
        });
        return components;
    },

    /**
     * Restore the specified tab to default metadata
     *
     * @param tabIndex the tab index to restore
     */
    restoreTabToDefault: function(tabIndex) {
        app.alert.show('restore_default_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESTORE_DEFAULT_TAB_CONFIRM', 'Dashboards'),
            onConfirm: _.bind(function() {
                var attributes = {
                    id: this._getOmniDashboardBeanId()
                };
                var params = {
                    dashboard: 'omnichannel',
                    tab_index: tabIndex
                };

                var url = app.api.buildURL('Dashboards', 'restore-tab-metadata', attributes, params);
                app.api.call('update', url, null, {
                    success: _.bind(function() {
                        this._getTabbedDashboard().fetchModel();
                    }, this)
                });
            }, this)
        });
    },

    /**
     * Get the bean ID for the omnichannel dashboard component
     *
     * @return string
     * @private
     */
    _getOmniDashboardBeanId: function() {
        var component = _.find(this._components, function(comp) {
            return comp.type && comp.type === 'dashboard' &&
                comp.model && comp.model.get('view_name') === 'omnichannel';
        });
        var model = component ? component.model : null;
        return model ? model.get('id') : '';
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.context.off('dashboard:restore-tab:clicked');
        this._super('_dispose');
    }
})

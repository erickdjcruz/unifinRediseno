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
 * @class View.Views.Base.Dashboards.DashboardFabView
 * @alias SUGAR.App.view.views.BaseDashboardsDashboardFabView
 * @extends View.Views.Base.DashboardFabView
 */
({
    /**
     * View to extend
     */
    extendsFrom: 'DashboardFabView',

    /**
     * Are we in a config layout?
     */
    configLayout: false,

    /**
     * @param options
     */
    initialize: function(options) {
        if (this._inConfigLayout(options)) {
            // Extend for omniconsole config specific events
            this.events = _.extend({}, this.events, {
                'click [name=restore_tab_button]': 'restoreTabClicked',
                'click [name=configure_summary_button]': 'openSugarLiveConfig'
            });
            this.configLayout = true;
        }

        this.events = _.extend({}, this.events, {
            'click [name=restore_dashlets_button]': 'handleRestoreDashletsClick'
        });
        this._super('initialize', [options]);
    },

    /**
    * Open the SugarLive Summary configuration drawer. On closing, the
    * SugarLive configuration panel should reopen.
    */
    openSugarLiveConfig: function() {
        var drawers = app.drawer._getDrawers() || {};
        var $topDrawer = drawers.$top || {};
        // get the console config component
        var configComponent = this.closestComponent('omnichannel-console-config');
        // if a drawer is already open in the background
        if (!_.isEmpty($topDrawer)) {
            if (!_.isUndefined(configComponent)) {
                // close the console config component
                configComponent.boundCloseImmediately();
            }
        }

        app.drawer.open({
            layout: 'config-drawer',
            context: {
                module: 'SugarLive'
            }
        }, function(model) {
            if (model && configComponent) {
                configComponent.inSync(true);
            }
        });
    },

    /**
     * @override
     *
     * This function updates button visibilities when the dashboard metadata
     * changes, or when switching tabs. Override base view to open the buttons
     * when switching tabs in the config drawer.
     */
    updateButtonVisibilities: function() {
        // If not in config layout, call base view function
        if (!this.configLayout) {
            return this._super('updateButtonVisibilities');
        }
        // In config drawer, the Add Dashlet button should be visible everywhere
        // except the search tab
        var activeTab = this._getActiveDashboardTab();
        this.toggleFabButton(['add_dashlet_button'], activeTab !== 0);

        // Set timeout to allow tab to render before opening buttons
        var self = this;
        setTimeout(function() {
            self.openFABs();
        }, 200);
    },

    /**
     * Util to get the current active dashboard tab.
     * @return {number}
     * @private
     */
    _getActiveDashboardTab: function() {
        return this.context.get('activeTab');
    },

    /**
     * Util method to determine if we are in a config layout. Used to allow
     * dashlet to render an empty record view for config displays
     *
     * @return {boolean} Whether we are in a config layout
     * @private
     */
    _inConfigLayout: function(options) {
        var context = options.context;
        while (context) {
            if (context.get('config-layout')) {
                return true;
            }
            context = context.parent;
        }
        return false;
    },

    /**
     * Get the omnichannel dashboard config component
     *
     * @return {Object} the component
     * @private
     */
    _getOmnichannelDashboardConfigComponent: function() {
        var component;
        if (this.configLayout) {
            component = this.closestComponent('omnichannel-dashboard-config');
        }
        return component;
    },

    /**
     * Handle restore tab button click
     */
    restoreTabClicked: function() {
        if (this.configLayout) {
            var component = this._getOmnichannelDashboardConfigComponent();
            if (component) {
                component.context.trigger('dashboard:restore-tab:clicked', this._getActiveDashboardTab());
            }
        }
    },

    /**
     * Handle restore dashlets button click
     */
    handleRestoreDashletsClick: function() {
        app.alert.show('restore_dashlet_confirmation', {
            level: 'confirmation',
            messages: app.lang.get('LBL_RESTORE_DEFAULT_PORTAL_DASHLETS_CONFIRM', 'Dashboards'),
            onConfirm: _.bind(function() {
                this.restoreDashlets();
            }, this)
        });
    },

    /**
     * Restores dashlets on the dashboard
     */
    restoreDashlets: function() {
        // get the dashboard component
        var component = this.closestComponent('dashboard');
        if (component) {
            component.layout.trigger('dashboard:restore_dashlets_button:click', component.context);
        }
    }
})

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
 * @class View.Views.Base.TabbedDashboardView
 * @alias SUGAR.App.view.views.BaseTabbedDashboardView
 * @extends View.View
 */
({
    className: 'tabbed-dashboard-pane bg-[--primary-content-background]',

    events: {
        'click [data-bs-toggle=tab]': 'tabClicked',
    },

    activeTab: 0,
    tabs: [],
    sticky: true,
    buttons: [],

    /**
     * Hash key for stickness.
     * @property {string}
     */
    lastStateKey: '',

    /**
     * Initialize this component.
     * @param {Object} options Initialization options.
     * @param {Object} options.meta Metadata.
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initTabs(options.meta);
        if (options.meta && !_.isUndefined(options.meta.sticky)) {
            this.sticky = options.meta.sticky;
        }
    },

    /**
     * Build the cache key for last visited tab.
     *
     * @return {string} hash key.
     */
    getLastStateKey: function() {
        if (!this.sticky) {
            return '';
        }

        if (this.lastStateKey) {
            return this.lastStateKey;
        }

        var modelId = this.model.get('id');
        this.lastStateKey = modelId ? modelId + '.' + 'last_tab' : '';
        return this.lastStateKey;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('tabbed-dashboard:update', this._setTabs, this);
        this.model.on('setMode', this._setMode, this);
    },

    /**
     * Check if a tab is dashboard.
     *
     * @param {number} tabIndex The tab's index
     * @return {bool} True if tab is a dashboard, otherwise false
     * @private
     */
    _isDashboardTab: function(tabIndex) {
        if (_.isEmpty(this.tabs)) {
            return true;
        }
        tabIndex = _.isUndefined(tabIndex) ? this.activeTab : tabIndex;

        var isStandardDashboard = !!this.tabs[tabIndex].dashlets;
        var isConsoleDashboard = !!(this.tabs[tabIndex].components && this.tabs[tabIndex].components.rows);

        return isStandardDashboard || isConsoleDashboard;
    },

    /**
     * Switch the active dashboard based on the clicked tab.
     * @param {Event} event Click event.
     */
    tabClicked: function(event) {
        var index = this.$(event.currentTarget).data('index');
        if (index === this.activeTab) {
            return;
        }
        // can't edit a non-dashboard tab or open a disabled tab
        if (!this.canSwitchTab(index) ||
            (this.model.mode === 'edit' && !this._isDashboardTab(index)) ||
            !this.isTabEnabled(index)) {
            event.stopPropagation();
            return;
        }

        let listComponent = null;
        const dashboardComponent = this.closestComponent('dashboard');
        if (dashboardComponent) {
            const dashboardMain = dashboardComponent
                .getComponent('dashboard-main');

            if (dashboardMain) {
                listComponent = dashboardMain
                    .getComponent('dashlet-main')
                    .getComponent('multi-line-list');
            }
        }

        const callback = () =>
            this.context.trigger('tabbed-dashboard:switch-tab', index);

        listComponent ?
            app.events.trigger('tabbed-dashboard:switch-tab-clicked', callback) :
            callback();
    },

    /**
     * Determine if anything is blocking a graceful tab switch
     *
     * @return boolean true if nothing blocking, false otherwise
     */
    canSwitchTab: function(index) {
        var components = [];

        if (app.sideDrawer && app.sideDrawer.isOpen()) {
            components.push(app.sideDrawer);
        }

        var omniDashboard = this._getOmnichannelDashboard();
        if (omniDashboard) {
            components.push(omniDashboard);
        }

        var blocked = _.find(components, function(component) {
            var switchTab = _.bind(this.switchTab, this, index);

            // return the first component that blocks tab switching
            return component.triggerBefore('tabbed-dashboard:switch-tab', {callback: switchTab}) === false;
        }, this);

        return _.isUndefined(blocked);
    },

    /**
     * Change active tab.
     * @param {number} tabIndex
     */
    switchTab: function(tabIndex) {
        if ((this.model.mode === 'edit' && !this._isDashboardTab(tabIndex)) ||
            !this.isTabEnabled(tabIndex)) {
            return;
        }
        this.context.trigger('tabbed-dashboard:switch-tab', tabIndex);
    },

    /**
     * Enable/disable a tab.
     * @param {number} index The tab index
     * @param {boolean} mode True to enable, false to disbale
     */
    setTabMode: function(index, mode) {
        if (this.tabs && this.tabs[index]) {
            this.tabs[index].enabled = mode;
        }
        var $tab = this.$('.tab[data-index="' + index + '"]');
        if ($tab) {
            mode ? $tab.removeClass('disabled') : $tab.addClass('disabled');
        }
    },

    /**
     * Check if tab is enabled.
     * @param {number} index The tab index
     * @return {boolean} True if enabled, otherwise false
     */
    isTabEnabled: function(index) {
        return !(this.tabs && this.tabs[index] && this.tabs[index].enabled === false);
    },

    /**
     * Handle button events.
     *
     * @param {string} state Button state
     * @private
     */
    _setMode: function(state) {
        if (_.isEmpty(this.tabs)) {
            return;
        }
        _.each(this.tabs, function(tab, index) {
            if (index !== this.activeTab && !this._isDashboardTab(index)) {
                var $tab = this.$('.tab[data-index="' + index + '"]');
                if (state === 'edit') {
                    // disable non-dahsboard tabs
                    $tab.addClass('disabled');
                } else if (state === 'view') {
                    // enable non-dahsboard tabs
                    $tab.removeClass('disabled');
                }
            }
        }, this);
    },

    /**
     * Initialize tabs.
     * @param {Object} [options={}] Tab options.
     * @private
     */
    _initTabs: function(options) {
        options = options || {};
        var lastStateKey = this.getLastStateKey();
        var lastVisitTab = lastStateKey ? app.user.lastState.get(lastStateKey) : 0;

        if (!_.isUndefined(options.activeTab)) {
            this.activeTab = options.activeTab;
            if (lastStateKey) {
                app.user.lastState.set(lastStateKey, this.activeTab);
            }
        } else if (!_.isUndefined(lastVisitTab)) {
            this.activeTab = lastVisitTab;
        }

        if (!_.isUndefined(options.tabs)) {
            this.tabs = options.tabs;
            this.context.set('tabs', this.tabs);
            this.context.set('activeTab', this.activeTab);
            this._initTabBadges();
        }

        if (!_.isUndefined(options.buttons)) {
            this.buttons = options.buttons;
        }
    },

    /**
     * Initialize tab badges.
     * @private
     */
    _initTabBadges: function() {
        var modelId = this.context.get('modelId');
        var configMeta = app.metadata.getModule('ConsoleConfiguration');

        if (this.tabs && configMeta) {
            _.each(this.tabs, function(tab) {
                if (!_.isUndefined(tab.badges)) {
                    _.each(tab.badges, function(badge) {
                        if (badge.type === 'record-count' && badge.module === 'Cases') {
                            badge.filter = _.union(
                                [
                                    {follow_up_datetime: _.first(_.pluck(badge.filter, 'follow_up_datetime'))}
                                ],
                                configMeta.config.filter_def[modelId][badge.module]
                            );
                        }
                    });
                }
            });
        }
    },

    /**
     * Set tab options, then re-render.
     * @param {Object} [options] Tab options.
     * @private
     */
    _setTabs: function(options) {
        this._initTabs(options);
        this.render();
    },

    /**
     * Get the side drawer
     *
     * @return {Object} The side drawer
     * @private
     */
    _getSideDrawer: function() {
        var dashboard = this.closestComponent('dashboard');
        if (dashboard) {
            var dmComponent = dashboard.getComponent('dashboard-main');

            return dmComponent.getComponent('side-drawer');
        }
        return null;
    },

    /**
     * Get the omnichannel dashboard
     *
     * @return {Object} The omnichannel dashboard
     * @private
     */
    _getOmnichannelDashboard: function() {
        return this.closestComponent('omnichannel-dashboard');
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (this.model.mode === 'edit') {
            this._setMode('edit');
        }
    },

    /**
     * Fetch the model if the model exists
     *
     * If new metadata is fetched, this will trigger 'change:metadata'. That will
     * then trigger 'tabbed-dashboard:update' to set tabs and render
     */
    fetchModel: function() {
        if (this.model) {
            this.model.fetch();
        }
    }
})

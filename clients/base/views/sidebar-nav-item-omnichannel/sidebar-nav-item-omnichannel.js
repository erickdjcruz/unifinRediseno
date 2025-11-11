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
 * @class View.Views.Base.SidebarNavItemOmnichannelView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemOmnichannelView
 * @extends View.Views.Base.SidebarNavItemView
 */
({
    extendsFrom: 'SidebarNavItemView',

    /**
     * Agent's current status.
     * @property {string}
     */
    status: 'logged-out',

    /**
     * List of browsers supported by AWS Connect CCP.
     * @property {Array}
     */
    supportedBrowsers: [
        'Chrome',
        'Firefox'
    ],

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.listenTo(app.events,
            'omnichannel:opened omnichannel:config:opened omnichannel:closed omnichannel:config:closed',
            this._determineActiveStatus);
    },

    /**
     * @inheritdoc
     * @private
     */
    _determineActiveStatus: function() {
        let sugarLiveIsOpen = app.omniConsole && app.omniConsole.isOpen();
        let sugarLiveConfigIsOpen = app.omniConsoleConfig && app.omniConsoleConfig.isOpen();
        let flyoutIsOpen = this.flyout && this.flyout.isOpen;
        this.setActive(sugarLiveIsOpen || sugarLiveConfigIsOpen || flyoutIsOpen);
    },

    /**
     * Opens the omnichannel drawer when primary button is clicked.
     * @param {Event} event the primary action click event
     */
    primaryActionOnClick: function(event) {
        this.layout.layout.collapse();
        this.openConsole();
    },

    /**
     * Opens the admin control flyout when the secondary button is clicked.
     * @param {Event} event the primary action click event
     */
    secondaryActionOnClick: function(event) {
        if (!this.flyout) {
            this.initPopover(this.$el, this._getFlyoutComponents(), this._getShowClose());
            this.flyout.$el.on('click', '[data-event="omnichannel:config"]', _.bind(this.openOptionLayout, this));
        }
        this.flyout.toggle();
    },

    /**
     * Opens console.
     */
    openConsole: function() {
        if (!this._checkBrowser()) {
            app.alert.show('omnichannel-unsupported-browser', {
                level: 'error',
                messages: app.lang.get('LBL_OMNICHANNEL_UNSUPPORTED_BROWSER')
            });
            return;
        }

        if (this._banOpenCcp()) {
            app.alert.show('finish_configuring', {
                level: 'warning',
                messages: app.lang.get('LBL_OMNICHANNEL_FINISH_CONFIGURING_BEFORE_OPENING_SUGARLIVE'),
            });
            return;
        }

        let console = this._getConsole();
        if (console) {
            console.open();
            console.$el.attr('data-ccp', true);
            this.$('.btn').removeClass('notification-pulse');
        }
    },

    /**
     * Opens Layout Configuration.
     */
    openOptionLayout: function() {
        let console = app.omniConsoleConfig;
        this.flyout.close();
        this.layout.layout.collapse();
        if (!console || !console.isOpen()) {
            console = this._getConfigConsole();
            if (console) {
                console.$el.attr('data-config', true);
                this.$('.config-menu').attr('data-mode', 'open');
                console.isConfigPaneExpanded = true;
                console.open();
            }
        }
    },

    /**
     * Sets button status.
     *
     * @param {string} status string: logged-out, logged-in, active-session
     */
    setStatus: function(status) {
        let icon = this.$el.find('.omnichannel-logged-in');
        if (status === 'logged-in' || status === 'active-session') {
            icon.removeClass('hidden');
        } else {
            icon.addClass('hidden');
        }
        this.status = status;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.isAvailable = this._isAvailable();
        this.secondaryAction = this.configAvailable = this._configAvailable();
        this.$el.toggleClass('hidden', !this.isAvailable);
        this._super('_render');
    },

    /**
     * Util to determine if SugarLive is available for this user
     *
     * @return {boolean} True if SugarLive should be available
     * @private
     */
    _isAvailable: function() {
        return app.api.isAuthenticated() &&
            app.user.hasSellServeLicense() &&
            app.user.isSetupCompleted() &&
            !!app.config.awsConnectInstanceName; // aws connect is configured
    },

    /**
     * Util to determine if SugarLive config should be available for the current user.
     *
     * @return {boolean} True if the user should be able to open SugarLive Config
     * @private
     */
    _configAvailable: function() {
        return app.user.get('type') === 'admin' && app.user.hasSellServeLicense();
    },

    /**
     * Checks if browser is supported by AWS Connect.
     * @return {boolean} True if its supported, false otherwise.
     */
    _checkBrowser: function() {
        let UA = navigator.userAgent;
        return !!_.find(this.supportedBrowsers, function(browserName) {
            return UA.indexOf(browserName) !== -1 &&
                // exclude Microsoft Edge ('Edg' for newer versions)
                UA.indexOf('Edg') === -1;
        });
    },

    /**
     * Checks if ban to open CCP-panel
     * @return {boolean}
     */
    _banOpenCcp: function() {
        let console = app.omniConsoleConfig;
        return (console && console.isConfigPaneExpanded);
    },

    /**
     * Creates omnichannel console if not yet.
     *
     * @return {View.Layout} The console
     * @private
     */
    _getConsole: function() {
        if (_.isUndefined(app.omniConsole)) {
            app.omniConsole = this._createConsole('omnichannel-console');
        } else if (this.status === 'logged-out') {
            let ccp = app.omniConsole.getComponent('omnichannel-ccp');
            ccp.loadCCP();
        }
        return app.omniConsole;
    },

    /**
     * Creates omnichannel console config drawer if not yet created.
     *
     * @return {View.Layout} The console
     * @private
     */
    _getConfigConsole: function() {
        if (_.isUndefined(app.omniConsoleConfig)) {
            app.omniConsoleConfig = this._createConsole(
                'omnichannel-console-config'
            );
        }
        return app.omniConsoleConfig;
    },

    /**
     * Create and initialize a new console of the given layout name, and bind
     * appropriate event listeners.
     *
     * @param layoutName name of layout to create
     * @return {View.Layout} newly created console
     * @private
     */
    _createConsole: function(layoutName) {
        let context = app.controller.context.getChildContext({forceNew: true, module: 'Dashboards'});
        // remove it from parent so that it won't get cleared when loading a new view
        app.controller.context.children.pop();
        let console = app.view.createLayout({
            type: layoutName,
            context: context
        });
        console.initComponents();
        console.loadData();
        console.render();
        this._bindConsoleListeners(console);
        $('#sidecar').append(console.$el);
        return console;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._unbindConsoleListeners(app.omniConsole);
        this._unbindConsoleListeners(app.omniConsoleConfig);
        if (this.flyout) {
            this.flyout.$el.off();
        }
        this._super('_dispose');
    },

    /**
     * Util to unbind event listeners on active console
     * @param console
     * @private
     */
    _unbindConsoleListeners: function(console) {
        if (!_.isUndefined(console)) {
            console.context.off('omnichannel:auth');
            console.off('omnichannel:message');
            console.off('omniconsole:open');
        }
    },

    /**
     * Show user notification if the console is closed when a message comes in
     *
     * @private
     */
    _notifyUser: function() {
        var omniConsole = this._getConsole();
        if (!omniConsole.isOpen()) {
            this.$el.addClass('omnichannel-pulse');
        }
    },

    /**
     * Clear notifications
     *
     * @private
     */
    _clearNotifications: function() {
        this.$el.removeClass('omnichannel-pulse');
    },

    /**
     * Bind listeners to the omnichannel-console layout
     *
     * @param {Layout} console - Omnichannel Console layout
     * @private
     */
    _bindConsoleListeners: function(console) {
        console.on('omnichannel:message', this._notifyUser, this);
        console.on('omniconsole:open', this._clearNotifications, this);
        console.context.on('omnichannel:auth', function(status) {
            this.setStatus(status);
        }, this);
    }
})

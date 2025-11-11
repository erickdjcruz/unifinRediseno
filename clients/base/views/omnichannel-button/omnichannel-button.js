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
 * 'Omnichannel' button.
 *
 * @class View.Views.Base.OmnichannelButtonView
 * @alias SUGAR.App.view.views.BaseOmnichannelButtonView
 * @extends View.View
 */
({
    className: 'omni-button flex items-center',

    events: {
        'click [data-action=omnichannel]': 'openConsole',
        'click .config-menu .option-layout-open': 'openOptionLayout',
    },

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

        var console = this._getConsole();
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
        var console = app.omniConsoleConfig;

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
        var currentStatus = this.status || 'logged-out';
        var button = this.$('.btn');
        button.removeClass(currentStatus);
        button.addClass(status);
        this.status = status;
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.isAvailable = this._isAvailable();
        this.configAvailable = this._configAvailable();

        this._super('_renderHtml');
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
        var UA = navigator.userAgent;
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
        var console = app.omniConsoleConfig;
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
            var ccp = app.omniConsole.getComponent('omnichannel-ccp');
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
        var context = app.controller.context.getChildContext({forceNew: true, module: 'Dashboards'});
        // remove it from parent so that it won't get cleared when loading a new view
        app.controller.context.children.pop();
        var console = app.view.createLayout({
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
            this.$('.btn').addClass('notification-pulse');
        }
    },

    /**
     * Clear notifications
     *
     * @private
     */
    _clearNotifications: function() {
        this.$('.btn').removeClass('notification-pulse');
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

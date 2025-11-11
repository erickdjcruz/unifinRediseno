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
 * The layout for the Omnichannel console.
 *
 * @class View.Layouts.Base.OmnichannelConsoleConfigLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelConsoleConfigLayout
 * @extends View.Layouts.Base.OmnichannelConsoleLayout
 */
({
    /**
     * Layout to extend
     */
    extendsFrom: 'OmnichannelConsoleLayout',

    /**
     * CSS classes added to layout
     */
    className: 'omni-console omni-console-config',

    /**
     * Indicates that its syncing metadata after config is modified
     * @property {boolean}
     */
    isSyncing: false,

    /**
     * @override
     *
     * Set 'config-layout' on context so child components can change behavior
     * accordingly
     *
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        // extend the events object to contain another click event for the same function. This gives us the open and
        // close of the omniconsole for free
        this.events = _.extend({}, this.events, {
            'click [name="add_dashlet_button"]': '_handleDashletToolbarActions',
        });
        this.context.set('config-layout', true);
        // This will override the current data-action=close event to trigger the closeAndDispose function
        this.events = _.extend({}, this.events, {
            'click [data-action=close]': 'closeAndDispose',
        });
        app.events.on('app:sync:complete', _.bind(function() {
            this.inSync(false);
        }, this), this);
    },

    /**
     * @inheritdoc
     *
     * @override
     */
    bindRouterEvents: function() {
        app.router.on('route', this.disposeOnRoute, this);
    },

    /**
     * @override
     *
     * Opens config layout, so the full console is visible. setMode is called
     * before sliding drawer up so the user only sees the full drawer appear
     * from the bottom.
     */
    open: function() {
        if (!this.isOpen()) {
            if (app.omniConsole && app.omniConsole.isOpen()) {
                app.omniConsole.close();
            }
            this.currentState = 'opening';
            this.isConfig = true;
            this.setKebabState('open');
            this.setMode('full');
            var animationEndHandler = _.bind(this.resizeCCP, this);
            this.$el.show('slide', {direction: 'down'}, this.animationLength, animationEndHandler);
            var $main = app.$contentEl.children().first();
            $main.on('drawer:add.omniConsole', this.boundCloseImmediately);
            this.currentState = 'idle';
            this.removeToolbarActionListener();

            app.events.trigger('omnichannel:config:opened');
        }
    },

    /**
     * @inheritdoc
     * @param onClose close callback function
     */
    close: function(onClose) {
        let wasOpen = this.isOpen();
        this._super('close', [onClose]);
        if (wasOpen) {
            app.events.trigger('omnichannel:config:closed');
        }
    },

    /**
     * It will trigger a resize on the contact control panel.
     */
    resizeCCP: function() {
        var ccp = this.getComponent('omnichannel-ccp');
        ccp.resize();
    },

    /**
     * Close and dispose the console.
     */
    closeAndDispose: function() {
        this.isConfig = false;
        this.isConfigPaneExpanded = false;
        this.setKebabState('init');
        this.close(_.bind(this.dispose, this));
    },

    /**
     * Called when a route event happens while on the omnichannel-console-config
     */
    disposeOnRoute: function() {
        // Sometimes a routing event occurs when the screen is reloaded (such as when we update the summary panel view
        // metadata. This check is to ensure that if the routing event is a simple refresh of the drawer we will not
        // dispose of the the drawer, but wait till metadata is refreshed for a proper open to happen.
        this.closeImmediately();
        if (app.router._currentFragment !== app.router._previousFragment || !this.inSync()) {
            this.dispose();
        } else {
            this.trigger('omniconfig:reopen');
            this.open();
            this.setKebabState('open');
        }
    },

    /**
     * Sets/Gets isSyncing flag
     *
     * @param {boolean} newState
     * @return {boolean}
     */
    inSync(newState) {
        if (!_.isUndefined(newState)) {
            this.isSyncing = newState;
        }
        return this.isSyncing;
    },

    /**
     * Sets the kebab button on the footer for SugarLive to either open or init
     * @param state
     */
    setKebabState: function(state) {
        // defensive check to ensure everything is defined in the chain as it should be
        var footer = this.$el.siblings('#footer');
        if (footer.length !== 0) {
            var configButton = footer.find('.config-menu');
            if (configButton.length !== 0) {
                configButton.attr('data-mode', state);
            }
        }
    },

    /**
     * @override
     *
     * Return an empty object as the config view has dummy data
     */
    getModelPrepopulateData: function() {
        return {};
    },

    /**
     * @override
     *
     * Override to simply re-open the config view
     */
    _handleClosedQuickcreateDrawer: function() {
        // if there are other drawers, follow regular drawer close behavior
        if (!app.drawer.count()) {
            this.open();
        }
    },

    /**
     * @override
     *
     * Override to prevent cache updates from the config view
     */
    _updateModeCache: function() {
    },

    /**
     * Unset context value on dispose
     * @private
     */
    _dispose: function() {
        // Grab the '.config-menu' item on the footer element. Change this back to 'init' data-mode so the
        // kebab is restored
        this.setKebabState('init');
        this.context.unset('config-layout');
        delete app.omniConsoleConfig;
        app.router.off('route', this.disposeOnRoute, this);
        app.events.off('app:sync:complete', null, this);
        this._super('_dispose');
    },
})

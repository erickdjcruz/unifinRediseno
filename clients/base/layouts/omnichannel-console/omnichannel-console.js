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
 * @class View.Layouts.Base.OmnichannelConsoleLayout
 * @alias SUGAR.App.view.layouts.BaseOmnichannelConsoleLayout
 * @extends View.Layout
 */
({
    /**
     * Css class for this component.
     * @property {string}
     */
    className: 'omni-console',

    /**
     * The current state of the console: 'opening', 'idle', 'closing', ''.
     * @property {string}
     */
    currentState: '',

    /**
     * Control omnichannel-console state when drawer closes.
     */
    isMinimized: null,

    /**
     * Stores settings for the omnichannel-console
     */
    settings: {
        inbound: {
            modeCacheKey: 'inbound-mode',
            defaultMode: 'full'
        },
        outbound: {
            modeCacheKey: 'outbound-mode',
            defaultMode: 'compact'
        }
    },

    /**
     * Stores the available modes that the console can be in
     */
    modes: {
        FULL: 'full',
        COMPACT: 'compact'
    },

    /**
     * The current mode of the console
     * @property {string}
     */
    currentMode: 'compact',

    /**
     * Size of console with ccp only.
     * The ccp itself can be 200px to a maximum of 320px wide and 400px to 465px tall according to:
     * https://github.com/amazon-connect/amazon-connect-streams
     *
     * @property {Object}
     */
    ccpSize: {
        width: 320,
        height: 540
    },

    /**
     * Height of the console header.
     * @property {number}
     */
    headerHeight: 28,

    /**
     * Event handlers.
     * @property {Object}
     */
    events: {
        // Closing the Omnichannel console
        'click [data-action=omnichannelClose]': 'closeClicked',
        // Toggling between compact and full modes
        'click [data-action=toggleMode]': '_toggleModeClicked',
        // Editing dashlet
        'click [data-dashletaction="editClicked"]': '_handleDashletToolbarActions',
        // Adding Interactions
        'click [data-dashletaction="composeEmail"], [data-dashletaction="createRecord"]': '_handleDashletToolbarActions'
    },

    /**
     * The omnichannel-dashboard-switch component
     */
    omniDashboardSwitch: null,

    /**
     * The omnichannel-ccp component
     */
    ccpComponent: null,

    /**
     * Fields from the AWS contact info to NOT pre-fill in when pre-populating models
     */
    prepopulateAwsContactDenyList: [
        'last_name',
        'name'
    ],

    /**
     * Attributes that should always be set in a model that is pre-filled from
     * the Omnichannel console
     */
    prepopulateAttributes: {
        no_success_label_link: true
    },

    /**
     * Length of drawer open/close animation
     */
    animationLength: 300,

    /**
     * Is there currently a Call or Message active?
     */
    isCallActive: false,

    /**
     * Is this the config mode or not
     */
    isConfig: false,

    /**
     * Did the user manually click the Close button to hide the console?
     * Or if false, Live was closed by some other means (drawer opened, etc.)
     */
    userManuallyClosedConsole: false,

    /**
     * Flag to store if the console is closed as the result of the user changing views.
     *
     * @property {boolean}
     */
    closedAfterRouteChange: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        $(window).on('resize.omniConsole', _.bind(this._resize, this));
        this.boundOpen = _.bind(this.open, this);
        this.boundCloseImmediately = _.bind(this.closeImmediately, this);
        this.sidebarNavWidth = '3.75rem';
        this.directionUC = (app.lang.direction === 'rtl') ? 'Right' : 'Left';
        this.direction = this.directionUC.toLowerCase();

        // Re-initialize the console's mode when AWS contacts change
        this.on('contact:view', this._initMode, this);
        this.on('contact:destroyed', this._initMode, this);
        app.events.once('app:login', this.closeOnLoginRedirect, this);

        this.bindRouterEvents();
    },

    /**
     * Bind specific router events for this layout
     */
    bindRouterEvents: function() {
        app.router.on('route', this.closeOnRouteChange, this);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        // Handle when the quickcreate drawer is closed
        this._getTopLevelContext().on('quickcreate-drawer:closed', this._handleClosedQuickcreateDrawer, this);
    },

    /**
     * Handler for dashlet toolbar action like edit or create record.
     * This listens for drawer:remove event to reopen the omniconsole
     *
     * @private
     */
    _handleDashletToolbarActions: function() {
        var drawers = app.drawer._getDrawers(true);
        // if a new drawer is about to open
        if (!_.isUndefined(drawers) && drawers.$next.length > 0) {
            var $main = app.$contentEl.children().first();
            // open console once the toolbar action drawer is closed
            $main.on('drawer:remove.omniConsole', this.boundOpen);
        }
    },

    /**
     * Gets the data to pre-populate a model with from the active dashboard
     *
     * @param {string} targetModule the module to get prepopulate data for
     * @return {Object} The attributes to pre-populate a model with
     */
    getModelPrepopulateData: function(targetModule) {
        var data = {};
        var ccp = this._getCCPComponent();
        var dashboardSwitch = this._getOmnichannelDashboardSwitch();
        if (ccp.activeContact && dashboardSwitch) {
            var activeContactInfo = ccp.getContactInfo(ccp.activeContact);
            data = _.extendOwn(
                data,
                _.omit(activeContactInfo, this.prepopulateAwsContactDenyList),
                dashboardSwitch.getModelPrepopulateData(ccp.getActiveContactId(), targetModule),
                this.prepopulateAttributes
            );
        }

        return data;
    },

    /**
     * Handles when the quick create drawer is closed, regardless of whether or
     * not a new record was created
     *
     * @param {Bean|undefined} createdModel the model created in the quick
     *                      create drawer if it was saved; undefined if the
     *                      quick create drawer was canceled
     * @private
     */
    _handleClosedQuickcreateDrawer: function(createdModel) {
        // Pass the created model to the omnichannel-dashboard-switch layout
        if (!_.isEmpty(createdModel)) {
            var ccp = this._getCCPComponent();
            var activeContactId = ccp.getActiveContactId();
            var dashboardSwitch = this._getOmnichannelDashboardSwitch();
            dashboardSwitch.setModel(activeContactId, createdModel, false);
            dashboardSwitch.postQuickCreate(activeContactId, createdModel);
        }

        // Re-open the Omnichannel console
        this.open();
    },

    /**
     * Get and return the omnichannel dashboard switch component
     *
     * @return {View.Layout}
     * @private
     */
    _getOmnichannelDashboardSwitch: function() {
        if (!this.omniDashboardSwitch) {
            this.omniDashboardSwitch = this.getComponent('omnichannel-dashboard-switch');
        }

        return this.omniDashboardSwitch;
    },

    /**
     * Get and return the omnichannel dashboard for the active contact
     *
     * @return {View.Layout}
     * @private
     */
    _getOmnichannelDashboard: function() {
        var ccp = this._getCCPComponent();
        var contactId = ccp.getActiveContactId();

        return this._getOmnichannelDashboardSwitch().getDashboard(contactId);
    },

    /**
     * Get and return the CCP component
     *
     * @return {View.View}
     * @private
     */
    _getCCPComponent: function() {
        if (!this.ccpComponent) {
            this.ccpComponent = this.getComponent('omnichannel-ccp');
        }

        return this.ccpComponent;
    },

    /**
     * Get relevant model data from the selected Contact, if there is one selected
     *
     * @return {Object}
     * @deprecated Since 11.1, the omnichannel-dashboard layout will handle
     *              gathering prepopulate data from the active dashboard
     */
    getContactModelDataForQuickcreate: function() {
        var dashboard = this._getOmnichannelDashboard();
        var tabModels = dashboard.tabModels;

        var data = {};

        // if there is no selected Contact, return empty
        if (!tabModels[dashboard.moduleTabIndex.Contacts]) {
            return data;
        }

        // these attributes will be deleted from data after retrieving them as the
        // model requires a different attribute name
        var modelAttributes = [
            'id',
            'name',
        ];

        var attributes = modelAttributes.concat([
            'account_id',
            'account_name',
        ]);

        var model = tabModels[dashboard.moduleTabIndex.Contacts];

        _.each(attributes, function(attr) {
            data[attr] = model.get(attr);
        });

        // update the attribute names so they're friendly for the new model
        data.primary_contact_id = data.id;
        data.primary_contact_name = data.name;

        // remove the attributes that were updated
        _.each(modelAttributes, function(attr) {
            delete data[attr];
        });

        return data;
    },

    /**
     * Open the console.
     */
    open: function() {
        // open the console if not yet
        if (!this.isOpen()) {
            app.events.on('app:view:change', this._setSize, this);
            app.events.on('sidebar:state:changed', this._setSize, this);

            $('#content').addClass('omniconsole-visible');
            this.userManuallyClosedConsole = false;
            this.closedAfterRouteChange = false;
            this.currentState = 'opening';
            this._initMode();
            this.$el.css({
                [this.direction]: this.sidebarNavWidth,
                display: ''
            });
            this.currentState = 'idle';
            this.isMinimized = false;
            var $main = app.$contentEl.children().first();
            $main.on('drawer:add.omniConsole', this.boundCloseImmediately);
            this.trigger('omniconsole:open');
            this.removeToolbarActionListener();

            app.events.trigger('omnichannel:opened');
        }
    },

    /**
     * Unsubscribe to dashlet toolbar events that open a new drawer.
     */
    removeToolbarActionListener: function() {
        var $main = app.$contentEl.children().first();
        $main.off('drawer:remove.omniConsole', this.boundOpen);
    },

    /**
     * Tell if the console is opened.
     * @return {boolean} True if open, false if not.
     */
    isOpen: function() {
        return this.currentState !== '';
    },

    /**
     * Checks if the console can be automatically reopened - used by the top drawer to check if it's correct
     * to reopen the console after the top drawer closes
     * @return {boolean}
     */
    canBeAutomaticallyReopened: function() {
        return !this.isOpen() && this.isMinimized && !this.userManuallyClosedConsole && !this.closedAfterRouteChange;
    },

    /**
     * Tell if console is expanded with dashboard and/or detail panel.
     *
     * @return {boolean} True if it's expanded, false otherwise
     * @deprecated Since 11.1, use getMode instead
     */
    isExpanded: function() {
        return this.getMode() !== this.modes.COMPACT;
    },

    /**
     * Check if the console is expanded to full mode and not minimized
     *
     * @return {boolean} True if expanded and not minimized
     */
    isExpandedAndMaximized: function() {
        return this.getMode() !== this.modes.COMPACT && !this.isMinimized;
    },

    /**
     * Close the console immediately.
     */
    closeImmediately: function() {
        // don't close in compact mode
        if (!this.$el || this.getMode() === 'compact') {
            return;
        }
        this.handleResetOnClose();
    },

    /**
     * Utility function to perform required clean up after the console is closed
     */
    handleResetOnClose: function() {
        this._resetPageElementsCssOnClose();
        this._offEvents();
    },

    /**
     * Handles closing the console when redirected to the login page
     */
    closeOnLoginRedirect: function() {
        this.closedAfterRouteChange = true;
        this.handleResetOnClose();
    },

    /**
     * On route change, close the console only if we're in full mode
     */
    closeOnRouteChange: function() {
        if (this.getMode() === this.modes.FULL) {
            // Opening and closing the top drawer changes the route, but we handle that separately
            if (!app.drawer.isOpening() && !app.drawer.isClosing()) {
                this.closedAfterRouteChange = true;
            }
            this.closeImmediately();
        }
    },

    /**
     * Expand/shrink console.
     *
     * @deprecated Since 11.1, use _initMode or setMode instead
     */
    toggle: function() {
        this._initMode();
    },

    /**
     * Handles the event of clicking either the minimize button or close button associated with omnichannel
     * Having this function allows the close() function to accept a callback
     */
    closeClicked: function() {
        this.isMinimized = true;
        this.userManuallyClosedConsole = true;
        this.closedAfterRouteChange = false;
        this.close();
    },

    /**
     * Resets page elements that console has touched while open
     * @private
     */
    _resetPageElementsCssOnClose: function() {
        var leftOffset = this.currentMode === this.modes.COMPACT ?
            `-${this.ccpSize.width}px` : `-${$(window).width()}px`;
        $('#content')
            .removeClass('omniconsole-visible')
            .css({
                [`margin${this.directionUC}`]: '',
                width: ''
            });
        $('.main-pane').css({
            marginLeft: '',
            width: ''
        });
        $('.main-pane .headerpane').css({
            marginLeft: '',
            width: ''
        });
        this.$el.css(this.direction, leftOffset);

        this.currentState = '';
        this.isMinimized = true;
    },

    /**
     * Close the console.
     * @param onClose callback function to be executed after animation completes
     */
    close: function(onClose) {
        if (!_.isFunction(onClose)) {
            onClose = null;
        }

        if (this.isOpen()) {
            this.currentState = 'closing';

            app.events.off('app:view:change', null, this);
            app.events.off('sidebar:state:changed', null, this);

            this.handleResetOnClose();

            app.events.trigger('omnichannel:closed');
        }
    },

    /**
     * Unsubscribe to events.
     * @private
     */
    _offEvents: function() {
        var $main = app.$contentEl.children().first();
        $main.off('drawer:add.omniConsole', this.boundCloseImmediately);
    },

    /**
     * Initializes the mode of the console based on its current state
     *
     * @private
     */
    _initMode: function() {
        // If there is an active contact, get the correct mode for the direction
        // of the contact. Otherwise, default to 'compact'
        var activeContactDirection = this._getActiveContactDirection();
        var mode = activeContactDirection ? this._getModeForDirection(activeContactDirection) : this.modes.COMPACT;

        this.isCallActive = !!this.getActiveContact();

        // let omnichannel-header know to toggle Compact/Full mode button on/off
        this.trigger('omniconsole:activeCall', this.isCallActive);
        // Apply the mode to the console
        this.setMode(mode);
    },

    /**
     * Returns the direction of the current/active contact session if there is
     * one
     *
     * @return {string|null} 'inbound' if the active contact session is an inbound call or a chat;
     *                       'outbound' if the active contact session is an outbound call;
     *                       null if there is no active contact session
     * @private
     */
    _getActiveContactDirection: function() {
        // Default direction is null
        var direction = null;

        // If we have an active contact session, update direction to reflect it
        var activeContact = this.getActiveContact();

        if (activeContact) {
            direction = activeContact.isInbound() ? 'inbound' : 'outbound';
        }

        return direction;
    },

    /**
     * Returns the Active Contact from the CCP Component
     *
     * @return {*} False or the active Contact
     */
    getActiveContact: function() {
        var ccp = this._getCCPComponent();
        return ccp && ccp.getActiveContact();
    },

    /**
     * Given a direction of a conversation, returns the correct mode for the
     * console based on the cached or default mode for that direction
     *
     * @param {string} direction, either 'inbound' or 'outbound'
     * @return {string|null} The correct mode of the console for the direction
     *                       or null if no mode exists for the directiom
     * @private
     */
    _getModeForDirection: function(direction) {
        var mode = null;

        // Get the settings for the given direction
        var directionSettings = this.settings && this.settings[direction];

        // Try to load the cached mode first
        if (directionSettings && directionSettings.modeCacheKey) {
            var modeCacheKey = app.user.lastState.key(directionSettings.modeCacheKey, this);
            mode = app.user.lastState.get(modeCacheKey);
        }

        // If no mode was cached, use the default mode
        if (_.isEmpty(mode) && (directionSettings && directionSettings.defaultMode)) {
            mode = directionSettings.defaultMode;
        }

        return mode;
    },

    /**
     * Handles when the toggleMode button is clicked to update the mode of the
     * console
     */
    _toggleModeClicked: function() {
        // Calculate what the new mode should be
        var newMode = this.getMode() === this.modes.FULL ? this.modes.COMPACT : this.modes.FULL;

        // Set the new mode of the console
        this.setMode(newMode);
    },

    /**
     * Set the current mode of the console
     *
     * @param {string} mode the mode of the console to set
     */
    setMode: function(mode) {
        // Set the new mode
        this.currentMode = mode;

        // Update the size of the console
        this._setSize(this.isOpen());

        // Update the cached mode value
        this._updateModeCache();

        // Notify the layout so other components can register the change
        this.trigger('omniconsole:mode:set', mode);
    },

    /**
     * Returns the current mode of the console
     *
     * @return {string} the current mode of the console
     */
    getMode: function() {
        return this.currentMode;
    },

    /**
     * Caches the current mode of the console for the direction of the active
     * contact session (inbound or outbound) if one exists
     *
     * @private
     */
    _updateModeCache: function() {
        var direction = this._getActiveContactDirection();
        if (direction) {
            var cacheKey = this.settings && this.settings[direction] && this.settings[direction].modeCacheKey;
            if (cacheKey) {
                app.user.lastState.set(app.user.lastState.key(cacheKey, this), this.currentMode);
            }
        }
    },

    /**
     * Set the size of the console.
     *
     * @param {boolean} animate if true, will animate the size change
     * @private
     */
    _setSize: function(animate) {
        if (_.isObject(animate) && animate.name === 'bwc') {
            return;
        }

        if (this.closedAfterRouteChange) {
            return;
        }

        var leftContentOffset = this.ccpSize.width;
        var $sidebar = $('.side.sidebar-content');
        var hasSidebar = !!$sidebar.length;
        var isSidebarCollapsed = $sidebar.hasClass('side-collapsed');
        var noSidebarOrSidebarIsCollapsed = !hasSidebar || (hasSidebar && isSidebarCollapsed);
        var areDrawersActive = !!app.drawer.count();
        var contentProps;
        var mainPaneProps;
        var headerpaneProps;
        var drawerMainPaneProps;
        var drawerHeaderpaneProps;
        var inactiveDrawerHeaderpaneProps;
        var mainPaneWidth;
        var $drawerSidebar;
        var hasDrawerSidebar;
        var $mainPane;
        var $activeDrawerMainPane;
        var $activeDrawerHeaderpane;
        var cssCalc100MinusLeftOffset = `calc(100% - ${leftContentOffset}px - ${this.sidebarNavWidth})`;
        var cssCalc100MinusLeftOffsetMinus34vw = `calc(100% - ${leftContentOffset}px - 34vw - ${this.sidebarNavWidth})`;
        const cssCalc100MinusLeftOffsetMain = `calc(100% - ${leftContentOffset}px - 34vw)`;

        contentProps = {
            [`margin${this.directionUC}`]: leftContentOffset,
            width: isSidebarCollapsed ? '100%' : cssCalc100MinusLeftOffset
        };
        $('#content').css(contentProps);

        if (hasSidebar && isSidebarCollapsed) {
            mainPaneWidth = cssCalc100MinusLeftOffset;
        } else if (noSidebarOrSidebarIsCollapsed) {
            mainPaneWidth = '100%';
        } else {
            mainPaneWidth = `calc(100% - 34vw)`;
        }
        mainPaneProps = {
            marginLeft: '',
            width: mainPaneWidth
        };
        $mainPane = $('#content .main-pane');
        $mainPane.css(mainPaneProps);

        headerpaneProps = {
            marginLeft: noSidebarOrSidebarIsCollapsed || areDrawersActive ? 0 : leftContentOffset,
            width: noSidebarOrSidebarIsCollapsed ? cssCalc100MinusLeftOffset : cssCalc100MinusLeftOffsetMinus34vw
        };
        if (areDrawersActive) {
            headerpaneProps.width = '100%';
        }
        $mainPane.find('.headerpane').css(headerpaneProps);

        // Set props for drawer elements if active
        if (areDrawersActive) {
            $drawerSidebar = $('.drawer.active .side.sidebar-content');
            hasDrawerSidebar = $drawerSidebar.length && !$drawerSidebar.hasClass('side-collapsed');

            drawerMainPaneProps = {
                marginLeft: leftContentOffset,
                width: hasDrawerSidebar ? cssCalc100MinusLeftOffsetMain : cssCalc100MinusLeftOffset
            };

            drawerHeaderpaneProps = {
                marginLeft: hasDrawerSidebar ? leftContentOffset : 0,
                width: hasDrawerSidebar ? cssCalc100MinusLeftOffsetMinus34vw : cssCalc100MinusLeftOffset
            };

            $activeDrawerMainPane = $('#drawers .drawer.active .main-pane');
            $activeDrawerMainPane.css(drawerMainPaneProps);

            $activeDrawerHeaderpane = $activeDrawerMainPane.find('.headerpane');
            $activeDrawerHeaderpane.css(drawerHeaderpaneProps);

            // reset the inactive drawer headerpane
            inactiveDrawerHeaderpaneProps = {
                marginLeft: this.currentState === 'opening' ? '320px' : '',
                width: this.currentState === 'opening' ? cssCalc100MinusLeftOffset : ''
            };
            $('#drawers .drawer.inactive .main-pane .headerpane').css(inactiveDrawerHeaderpaneProps);
        }

        if (animate || this.isConfig) {
            this.$el.css({
                [this.direction]: this.sidebarNavWidth,
                width: this.currentMode === this.modes.COMPACT ?
                    `${this.ccpSize.width}px` : `calc(100% - ${this.sidebarNavWidth})`
            });
        } else {
            this.$el.css('left', this.sidebarNavWidth);
        }
    },

    /**
     * Resize the console.
     * @private
     */
    _resize: _.throttle(function() {
        if (this.disposed) {
            return;
        }
        // resize the console if it is opened
        if (this.currentState === 'idle') {
            this._setSize(false);
        }
    }, 30),

    /**
     * Get top-level context for setting Quick Create models
     *
     * @return {Object} context
     * @private
     */
    _getTopLevelContext: function() {
        var context = this.context;
        while (context.parent) {
            context = context.parent;
        }
        return context;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off('resize.omniConsole');
        app.events.off('app:login', this.closeOnLoginRedirect, this);
        app.router.off('route', null, this);
        this._getTopLevelContext().off('quickcreate-drawer:closed', this._handleClosedQuickcreateDrawer, this);
        this._super('_dispose');
    },
})

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
 * @class View.Layouts.Base.SidebarNavItemGroupModulesLayout
 * @alias SUGAR.App.view.layouts.BaseSidebarNavItemGroupModulesLayout
 * @extends View.Layouts.Base.SidebarNavItemGroupLayout
 */
({
    extendsFrom: 'SidebarNavItemGroupLayout',

    className: `sidebar-nav-item-group sidebar-nav-item-group-modules border-t-[1px]
border-[--sidebar-nav-group-divider-color] flex flex-col overflow-hidden relative
group-[.expanded:not(.collapsing):not(.expanding)]/sidebar:overflow-y-auto`,

    /**
     * Stores the integer maximum number of pinned module items
     */
    maxPinned: null,

    /**
     * Stores a mapping of module name -> module nav item component
     */
    _moduleComponents: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (app.isSynced) {
            this.resetModuleList();
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(app.events, 'app:sync:complete', this.resetModuleList);
        this.listenTo(app.events, 'app:view:change', this._handleAppViewChange);
        this.listenTo(app.events, 'sidebar-nav:module:active:change', this.adjustMenuItems);
        $(window).on(`resize.${this.cid}`, _.debounce(_.bind(this._handleWindowResize, this), 10));
    },

    /**
     * Performs and necessary actions when the application view changes
     *
     * @private
     */
    _handleAppViewChange: function() {
        this._checkModuleComponentExists(this._getAppContextModule());
        this.adjustMenuItems();
    },

    /**
     * Performs any necessary actions when the browser window size changes
     *
     * @private
     */
    _handleWindowResize: function() {
        // We only need to adjust the menu items if the vertical height changed
        if (window.innerHeight !== this.lastWindowHeight) {
            this.adjustMenuItems();
            this.lastWindowHeight = window.innerHeight;
        }
    },

    /**
     * Adjust the menu items according to height of the div
     */
    adjustMenuItems: function() {
        // Get the number of pins to show
        let groupHeight = this.$el.height();
        let minGroupHeight = parseInt(this.$el.css('min-height'));
        let spacesForPins = Math.floor(groupHeight / minGroupHeight);
        let numberOfPins = Math.min(spacesForPins, this.maxPinned);

        // Pin the correct number of items for the space available
        let navItems = this.$el.find('> .sidebar-nav-item');
        let pinnedNavItems = navItems.slice(0, numberOfPins);
        navItems.removeClass('pinned');
        pinnedNavItems.addClass('pinned');

        // Make sure we have room to always show the active item
        let activeItem = navItems.filter('.active');
        let activeItemIsOutsidePins = activeItem.length && pinnedNavItems.filter(activeItem).length === 0;
        let limitedBySpace = numberOfPins === spacesForPins;
        if (activeItemIsOutsidePins && limitedBySpace) {
            navItems.slice(numberOfPins - 1, numberOfPins).removeClass('pinned');
        }
    },

    /**
     * Checks that the module item for a given module exists. and creates it if
     * it does not
     *
     * @param {string} module the module name
     * @private
     */
    _checkModuleComponentExists(module) {
        let moduleTabMap = app.metadata.getModuleTabMap();
        module = !_.isUndefined(moduleTabMap[module]) ? moduleTabMap[module] : module;

        if (!this._moduleComponents[module] && module !== 'Home') {
            this.addModuleItem(module);
        }
    },

    /**
     * Gets the current context module of the application
     *
     * @return {string} the current context module
     * @private
     */
    _getAppContextModule: function() {
        let module = app.controller.context.get('module');
        let drawerComponent = app.drawer.getActive();
        if (drawerComponent && drawerComponent.context.get('fromRouter')) {
            module = drawerComponent.context.get('module');
        }

        return module;
    },

    /**
     * Resets the list of module nav items displayed in the group based on
     * module metadata
     */
    resetModuleList: function() {
        this._disposeComponents();
        this._moduleComponents = {};
        this.maxPinned = this._getNumberPinned();

        _.each(this._getDefaultModules(), function(options, module) {
            this.addModuleItem(module, options);
        }, this);

        this._checkModuleComponentExists(this._getAppContextModule());
        this.adjustMenuItems();
    },

    /**
     * Returns the number of module items configured to be shown while the
     * sidebar is in collapsed mode. Allows values between 1 - 100 (inclusive)
     *
     * @return {int} the number of module items
     * @private
     */
    _getNumberPinned: function() {
        let numberPref = app.user.getPreference('number_pinned_modules');
        let numberPinned = _.isNumber(numberPref) ? numberPref : app.config.maxPinnedModules;
        return Math.floor(Math.min(Math.max(numberPinned, 1), 100));
    },

    /**
     * Returns the list of modules that are pinned
     *
     * @return {Array} the list of pinned modules
     * @private
     */
    _getPinnedModuleList: function() {
        return _.first(this._getDefaultModuleList(), this.maxPinned);
    },

    /**
     * Gets the full list of visible modules, minus the "Home" module
     *
     * @return {Array} the list of visible modules
     * @private
     */
    _getDefaultModuleList: function() {
        let modules = app.metadata.getModuleNames({filter: 'display_tab', access: 'read'});
        return _.without(modules, 'Home');
    },

    /**
     * Gets data about the set of module items to create in the modules group
     *
     * @return {Object} the set of module names and any custom metadata options
     *                  to apply to them
     * @private
     */
    _getDefaultModules: function() {
        let modules = {};
        let fullModuleList = app.metadata.getFullModuleList();

        // Get the list of modules to be shown as pinned in compact mode
        let pinnedModulesList = this._getPinnedModuleList();
        let pinnedModules = _.object(pinnedModulesList, pinnedModulesList);

        // Get the list of all default modules
        let defaultModuleList = this._getDefaultModuleList();

        _.each(_.union(pinnedModulesList, defaultModuleList), function(module) {
            if (fullModuleList[module]) {
                modules[module] = {
                    css_class: `default${pinnedModules[module] ? ' pinned' : ''}`
                };
            }
        }, this);

        return modules;
    },

    /**
     * Adds a module item to the group
     *
     * @param module
     * @param options
     */
    addModuleItem: function(module, options) {
        let fullModuleList = app.metadata.getFullModuleList();
        if (!fullModuleList[module]) {
            return;
        }

        let moduleItem = this._createModuleItem(module, options);
        this.addComponent(moduleItem);
        this._moduleComponents[module] = moduleItem;
        moduleItem.render();
    },

    /**
     * Adds a new sidebar-nav-item to the group representing the given module
     *
     * @param {string} module the name of the module
     * @param {Object} options any custom options to set in the item's metadata
     * @return {Backbone.View} the sidebar-nav-item view object
     */
    _createModuleItem: function(module, options) {
        return app.view.createView({
            type: 'sidebar-nav-item-module',
            module: module,
            meta: options || {},
            layout: this
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off(`resize.${this.cid}`);
        this.stopListening();
        this._super('_dispose');
    }
})

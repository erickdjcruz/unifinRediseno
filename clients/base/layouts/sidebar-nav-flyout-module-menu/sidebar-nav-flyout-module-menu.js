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
 * @class View.Layouts.Base.SidebarNavFlyoutModuleMenuLayout
 * @alias SUGAR.App.view.layouts.BaseSidebarNavFlyoutModuleMenuLayout
 * @extends View.Layout
 */
({
    className: 'sidebar-nav-flyout-module-menu min-w-[10rem] max-w-[15rem]',

    /**
     * Alternative endpoints that can be used when fetching collections. To use
     * these, include an 'endpoint' property in the collectionSettings metadata
     * that matches with a key in this object
     *
     * Example:
     *
     * 'recently_viewed' => [
     *    'modules' => 'all',
     *    'filter' => [
     *        '$tracker' => '-7 DAY',
     *    ],
     *    'endpoint' => 'recent'
     */
    endpoints: {
        recent: function(method, model, options, callbacks) {
            var url = app.api.buildURL('recent', 'read', options.attributes, options.params);
            app.api.call(method, url, null, callbacks, options.params);
        }
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initConfig();
        this._addMenuComponents();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        // Whenever the flyout is opened, update the lists of collection models
        this.listenTo(this.layout, 'popover:opened', this._updateCollections);

        this.listenTo(this, 'collection-toggle', this._handleCollectionToggled);
    },

    /**
     * Handles when a collection has been toggled to show more/less records.
     * Updates the collection's limit and re-fetches it
     *
     * @param {string} module this.module, passed by the actions menu
     * @param {Event} event the click event that initiated the toggle
     * @private
     */
    _handleCollectionToggled: function(module, event) {
        let menuItem = $(event.currentTarget).closest('[role="menuitem"]');
        let collectionName = menuItem.data('name');
        if (collectionName) {
            let toggleSettings = this._collectionSettings[collectionName].toggle;
            toggleSettings.toggled = !toggleSettings.toggled;
            app.user.lastState.set(toggleSettings.lastStateKey, toggleSettings.toggled);
            this._updateCollection(collectionName);
        }
    },

    /**
     * Initializes configuration for the module flyout menu
     *
     * @private
     */
    _initConfig: function() {
        this._collections = {};

        this._collectionSettings = this.meta && this.meta.collectionSettings || {};
        _.each(this._collectionSettings, function(settings, collectionName) {
            // Get the list of modules to fetch from
            if (_.isString(settings.modules) && settings.modules === 'all') {
                let allModules = app.metadata.getModuleNames({filter: 'visible', access: 'list'});
                let excludedModules = settings.excludedModules || [];
                settings.modules = _.without(allModules, ...excludedModules);
            } else if (_.isEmpty(settings.modules)) {
                settings.modules = [this.module];
            }

            // Get the list of field data to fetch. For conditional icons based
            // on record data, we need to also fetch that field
            settings.fields = ['id', 'name'];
            if (_.isObject(settings.icon)) {
                settings.fields.push(_.keys(settings.icon)[0]);
            }

            // Mark whether the collection's "Show more" is triggered
            if (_.isObject(settings.toggle)) {
                let lastStateKey = app.user.lastState.buildKey(collectionName, 'menu-collection-toggle', this.module);
                settings.toggle.lastStateKey = lastStateKey;
                settings.toggle.toggled = app.user.lastState.get(lastStateKey) || false;
            }
        }, this);
    },

    /**
     * Adds any necessary components for this module flyout menu
     *
     * @private
     */
    _addMenuComponents: function() {
        let menuComponents = this._getMenuComponents();
        _.each(menuComponents, function(component) {
            let newComponent = this.createComponentFromDef(component, null, this.module);
            this.addComponent(newComponent);
            newComponent.render();
        }, this);
    },

    /**
     * Gets the subcomponent metadata to be used in this module menu, including
     * various the lists of model collections
     *
     * @return {Array} the subcomponent metadata
     * @private
     */
    _getMenuComponents: function() {
        // Start with the static components
        let menuComponents = [
            {
                view: 'sidebar-nav-flyout-header',
                title: app.lang.getModuleName(this.module, {plural: true}),
            },
            {
                view: {
                    type: 'sidebar-nav-flyout-actions',
                    actions: this._getMenuActions()
                }
            }
        ];

        // For each collection we want to fetch based on the collection
        // settings, add an action list for it to the components
        _.each(this._collectionSettings, function(settings, collectionName) {
            menuComponents.push({
                view: {
                    type: 'sidebar-nav-flyout-actions',
                    name: collectionName,
                    actions: []
                },
            });
        }, this);

        return menuComponents;
    },

    /**
     * Returns the base actions should be displayed in the module item's flyout
     * action menu based on menu metadata for the module
     *
     * @return {Array} the module's menu action metadata
     * @private
     */
    _getMenuActions: function() {
        let moduleMeta = app.metadata.getModule(this.module) || {};
        return app.utils.deepCopy(moduleMeta.menu && moduleMeta.menu.header && moduleMeta.menu.header.meta || []);
    },

    /**
     * Updates the list of action items for each collection
     *
     * @private
     */
    _updateCollections: function() {
        _.each(_.keys(this._collectionSettings), function(collectionName) {
            this._updateCollection(collectionName);
        }, this);
    },

    /**
     * Updates the list of action items for a single collection
     *
     * @param {string} collectionName the name of the collection to update
     * @private
     */
    _updateCollection: function(collectionName) {
        let settings = this._collectionSettings[collectionName];

        let limit = settings.limit;
        if (_.isObject(settings.toggle) && settings.toggle.toggled && _.isNumber(settings.toggle.limit)) {
            limit = settings.toggle.limit;
        }

        if (limit > 0) {
            this.getCollection(collectionName).fetch({
                fields: settings.fields,
                filter: settings.filter,
                limit: limit,
                params: {
                    erased_fields: true
                },
                showAlerts: false,
                success: () => {
                    let actionsComp = this.getComponent(collectionName);
                    if (actionsComp) {
                        actionsComp.updateActions(this._createCollectionActions(collectionName));
                    }
                }
            });
        }
    },

    /**
     * Get the collection object associated with a particular collection. For
     * example, "favorites" or "recently_viewed"
     *
     * @param {string} collectionName The name of the collection to get
     * @return {Data.MixedBeanCollection} The collection of this module
     */
    getCollection: function(collectionName) {
        if (!this._collections[collectionName]) {
            let settings = this._collectionSettings[collectionName];

            if (settings.modules.length > 1) {
                this._collections[collectionName] = app.data.createMixedBeanCollection();
                this._collections[collectionName].module_list = settings.modules;
            } else {
                this._collections[collectionName] = app.data.createBeanCollection(settings.modules[0]);
            }

            if (settings.endpoint && this.endpoints[settings.endpoint]) {
                this._collections[collectionName].setOption('endpoint', this.endpoints[settings.endpoint]);
            }
        }

        return this._collections[collectionName];
    },

    /**
     * Given a collection, returns a list of record navigation actions
     * for the records in that collection, formatted to be placed in an actions
     * list
     *
     * @param {string} collectionName the name of the collection
     * @return {Array} the list of formatted record navigation actions
     * @private
     */
    _createCollectionActions: function(collectionName) {
        let collectionActions = [];
        let collectionSettings = this._collectionSettings[collectionName];
        let collection = this.getCollection(collectionName);

        if (!_.isEmpty(collection.models)) {
            collectionActions.push({type: 'divider'});

            // Create a route action for each model in the collection
            _.each(collection.models, function(model) {
                collectionActions.push(this._createCollectionModelAction(model, collectionName));
            }, this);

            // If necessary, create a toggle action for the collection
            if (_.isObject(collectionSettings.toggle)) {
                let showToggle = !collectionSettings.toggle.toggled &&
                    collection.length === collectionSettings.limit && collection.next_offset !== -1;
                let showUntoggle = collectionSettings.toggle.toggled && collection.length > collectionSettings.limit;

                if (showToggle || showUntoggle) {
                    collectionActions.push(this._createCollectionToggleAction(collectionName));
                }
            }
        }

        return collectionActions;
    },

    /**
     * Creates a menu action for a single module record in the given collection
     *
     * @param {Bean} model the model from the collection
     * @param {string} collectionName the name of the collection
     * @return {Object} the metadata for the menu action
     * @private
     */
    _createCollectionModelAction: function(model, collectionName) {
        let collectionSettings = this._collectionSettings[collectionName];
        let module = model.module === 'Dashboards' ? 'Home' : model.module;

        let icon = '';
        if (_.isString(collectionSettings.icon)) {
            icon = collectionSettings.icon;
        } else if (_.isObject(collectionSettings.icon)) {
            let field = _.keys(collectionSettings.icon)[0];
            let value = model.get(field);
            icon = collectionSettings.icon[field][value] || '';
        }

        return {
            icon: icon,
            label: model.get('name'),
            acl_action: 'list',
            acl_module: module,
            route: `#${app.router.buildRoute(module, model.get('id'))}`
        };
    },

    /**
     * Creates a menu action to expand/collapse the given collection
     *
     * @param {string} collectionName the name of the collection
     * @return {Object} the metadata for the menu action
     * @private
     */
    _createCollectionToggleAction: function(collectionName) {
        let collectionSettings = this._collectionSettings[collectionName];
        let label = collectionSettings.toggle.toggled ?
            collectionSettings.toggle.label_untoggle || 'LBL_SHOW_LESS' :
            collectionSettings.toggle.label_toggle || 'LBL_SHOW_MORE';

        return {
            name: collectionName,
            label: app.lang.get(label),
            event: `collection-toggle`,
            eventTarget: 'layout',
            preventClose: true
        };
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    },
})

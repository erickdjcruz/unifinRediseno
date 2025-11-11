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
 * @class View.Views.Base.SidebarNavItemModuleView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemModuleView
 * @extends View.Views.Base.SidebarNavItemView
 */
({
    extendsFrom: 'SidebarNavItemView',

    /**
     * @inheritdoc
     */
    id: function() {
        return `${this.options.module}_sidebar-nav-item`;
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._determineActiveStatus();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(app.events, 'app:view:change', this._determineActiveStatus);
    },

    /**
     * @inheritdoc
     * @private
     */
    _determineActiveStatus: function() {
        let module = app.controller.context.get('module');
        let drawerComponent = app.drawer.getActive();
        if (drawerComponent && drawerComponent.context.get('fromRouter')) {
            module = drawerComponent.context.get('module');
        }

        const moduleTabMap = app.metadata.getModuleTabMap();
        module = !_.isUndefined(moduleTabMap[module]) ? moduleTabMap[module] : module;

        let moduleIsActive = module === this.module;
        let flyoutIsOpen = this.flyout && this.flyout.isOpen;
        let shouldBeActive = moduleIsActive || flyoutIsOpen;
        if (shouldBeActive !== this.active) {
            this.setActive(shouldBeActive);
            app.events.trigger('sidebar-nav:module:active:change');
        }
    },

    /**
     * If metadata is not specified, uses default metadata for this particular
     * module
     *
     * @override
     */
    _initConfig: function() {
        let moduleMeta = app.isSynced ? app.metadata.getModule(this.module) || {} : {};
        let defaultViewMeta = app.metadata.getView(this.module, this.name) || {};
        let viewMeta = _.extend({}, defaultViewMeta, this.meta || {});
        let secondaryAction = false;
        if (moduleMeta.menu && moduleMeta.menu.header) {
            secondaryAction = this._filterActionsByAccess(moduleMeta.menu.header.meta).length > 0;
        }
        let defaults = {
            label: app.isSynced ? app.lang.getModuleName(this.module, {plural: true}) || '' : '',
            route: this._buildRoute(this.module, viewMeta),
            icon: moduleMeta.icon || '',
            displayType:  moduleMeta.display_type || 'icon',
            abbreviation: app.isSynced ? app.lang.getModuleIconLabel(this.module) : '',
            color: moduleMeta.color || 'ocean',
            secondaryAction: secondaryAction
        };

        this.label = viewMeta.label || defaults.label;
        this.route = `#${viewMeta.route || defaults.route}`;
        this.icon = viewMeta.icon || defaults.icon;
        this.secondaryAction = viewMeta.secondary_action || defaults.secondaryAction;
        this.abbreviation = viewMeta.abbreviation || defaults.abbreviation;
        this.color = viewMeta.color || defaults.color;
        this.displayType = viewMeta.display_type || defaults.displayType;
    },

    /**
     * Filters menu actions by ACLs for the current user.
     *
     * @param {Array} actionMeta The menu action metadata to check for access
     * @return {Array} the list of actions for which the user has access
     */
    _filterActionsByAccess: function(actionMeta) {
        let result = [];
        _.each(actionMeta, function(menuItem) {
            if (app.acl.hasAccess(menuItem.acl_action, menuItem.acl_module)) {
                result.push(menuItem);
            }
        });
        return result;
    },

    /**
     * @inheritdoc
     */
    _getFlyoutComponents: function() {
        return this.meta && this.meta.flyoutComponents || [{
            layout: 'sidebar-nav-flyout-module-menu'
        }];
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.useAbbreviation = this.displayType === 'abbreviation';
        this._super('_render');
    },

    /**
     * Generates route for the module sidebar icons.
     * Handles the case when user is not admin, then build a route to user's profile page
     *
     * @param {string} moduleName
     * @param {Object} viewMeta
     *
     * @private
     */
    _buildRoute: function(moduleName, viewMeta) {
        if (moduleName === 'Users') {
            let acls = app.user.getAcls();
            if (app.user.get('type') !== 'admin' && acls.Users.developer === 'no') {
                moduleName = `${this.module}/${app.user.id}`;
            }
        }

        return app.isSynced ? app.router.buildRoute(moduleName, viewMeta.routeId, viewMeta.routeAction) : '';
    }
})

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
 * @inheritdoc
 *
 * @class View.Views.Base.PortalThemeModuleListLayout
 * @alias SUGAR.App.view.layouts.BasePortalThemeModuleListLayout
 * @extends View.Layouts.Base.ModuleListLayout
 */
({
    extendsFrom: 'ModuleListLayout',

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        // Skip parent initialize method, as the app:sync:complete listener and
        // app:view:change listeners are not needed
        app.view.Layout.prototype.initialize.call(this, options);
        // Replace template with module-list template so appearance matches megamenu
        this.template = app.template.getLayout('module-list');
        this._resetMenu();
    },

    /**
     * @inheritdoc
     * @override
     *
     * Override parent to add portal-enabled modules rather than main app
     * modules
     * @private
     */
    _addDefaultMenus: function() {
        var url = app.api.buildURL('Administration/portalmodules', 'read');
        var successCallback = _.bind(this._addMenus, this);
        app.api.call('read', url, null, {
            success: successCallback
        });

    },

    /**
     * Util to serve as a callback once API returns portal-enabled modules. Adds
     * a menu dropdown for each module.
     *
     * @param moduleList List of modules to add to the megamenu
     * @private
     */
    _addMenus: function(moduleList) {
        _.each(moduleList, function(module) {
            this._addMenu(module, true);
        }, this);
        // Because this is called as an API success callback, we need to re-render
        // after adding each module-menu to the list
        this.render();
    },

    /**
     * @override
     *
     * Use list template from module-list layout so portal preview megamenu
     * matches its base component
     * @param component
     * @return {Object} module-list's 'list' template
     * @private
     */
    _getListTemplate: function(component) {
        return app.template.getLayout('module-list.list', component.module) ||
            app.template.getLayout('module-list.list');
    },

    /**
     * @inheritdoc
     * @override
     *
     * Overloading this because for portal theme preview we do not want to
     * add the active module to the megamenu, and we do not want to set any
     * module as "active" in the preview
     */
    _setActiveModule: function(module) {
    }
})

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
 * @class View.Layouts.Base.Users.SidebarNavFlyoutModuleMenuLayout
 * @alias SUGAR.App.view.layouts.BaseUsersSidebarNavFlyoutModuleMenuLayout
 * @extends View.Layouts.Base.SidebarNavFlyoutModuleMenuLayout
 */
({
    extendsFrom: 'SidebarNavFlyoutModuleMenuLayout',

    /**
     * @override
     */
    _getMenuActions: function() {
        let actions = this._super('_getMenuActions');
        let cloudConsoleLink = this._getCloudConsoleLink();
        if (app.config.idmModeEnabled) {
            actions.forEach(_.bind(function(menuItem, key) {
                if (menuItem.label === 'LNK_NEW_USER' && -1 === menuItem.route.indexOf('user_hint')) {
                    actions[key].route = `${cloudConsoleLink}&user_hint=${app.utils.createUserSrn(app.user.id)}`;
                }
            }, this));
        }
        return actions;
    },

    /**
     * Returns cloud console link in case of an IDM instance
     *
     * @return {string}
     * @private
     */
    _getCloudConsoleLink: function() {
        return app.utils.deepCopy(this.meta && this.meta.cloudConsoleLink || '');
    },
})

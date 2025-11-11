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
 * @class View.Views.Base.Users.SidebarNavFlyoutMenuView
 * @alias SUGAR.App.view.views.BaseUsersSidebarNavFlyoutMenuView
 * @extends View.Views.Base.SidebarNavFlyoutMenuView
 */
({
    extendsFrom: 'SidebarNavFlyoutMenuView',

    /**
     * @inheritdoc
     */
    _handleRouteItemClick: function(event) {
        let menuItem = event.target.closest('.megamenu-dropdown-item');
        if (
            app.config.idmModeEnabled &&
            (menuItem.getAttribute('data-navbar-menu-item') === 'LNK_NEW_USER')
        ) {
            app.alert.show('idm_create_user', {
                level: 'info',
                messages: app.lang
                    .get('ERR_CREATE_USER_FOR_IDM_MODE', 'Users')
                    .replace('{0}', menuItem.getAttribute('data-route'))
            });
        }
        this._super('_handleRouteItemClick', [event]);
    },
})

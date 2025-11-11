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
 * @class View.Layouts.Base.HomeSidebarNavFlyoutModuleMenuLayout
 * @alias SUGAR.App.view.layouts.BaseHomeSidebarNavFlyoutModuleMenuLayout
 * @extends View.Layout
 */
({
    extendsFrom: 'SidebarNavFlyoutModuleMenuLayout',

    /**
     * @inheritdoc
     */
    _getMenuActions: function() {
        const menu = this._super('_getMenuActions');
        return _.filter(menu, (v) =>
            !(v.route && v.route === '#activities' && !app.config.activityStreamsEnabled));
    },
})

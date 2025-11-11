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
 * @class View.Views.Base.SidebarHelpHeaderView
 * @alias SUGAR.App.view.views.BaseSidebarHelpHeaderView
 * @extends View.View
 */

({
    /**
     * @inheritdoc
     *
     * @private
     */
    _render: function() {
        this.title = this._getTitle('LBL_HELP_' + app.controller.context.get('layout').toUpperCase() + '_TITLE');
        this._super('_render');
    },

    /**
     * Fetches the title of the help modal.
     * If none exists, returns a default help title.
     *
     * @param {string} titleKey The modal title label.
     * @return {string} The converted title.
     * @private
     */
    _getTitle: function(titleKey) {
        let title = app.lang.get(titleKey, app.controller.context.get('module'), app.controller.context);
        return title === titleKey ? app.lang.get('LBL_HELP') : title;
    },
})

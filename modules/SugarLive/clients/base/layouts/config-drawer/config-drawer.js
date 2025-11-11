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
 * @class View.Layouts.Base.SugarLiveConfigDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseSugarLiveConfigDrawerLayout
 * @extends View.Layouts.Base.ConfigDrawerLayout
 */
({
    extendsFrom: 'BaseConfigDrawerLayout',

    /**
     * The list of modules this config is intended for.
     */
    enabledModules: ['Calls', 'Messages'],

    /**
     * Check if we have access to the module.
     * If yes, allow the first tab to be displayed.
     */
    loadData: function() {
        if (!this.checkAccess()) {
            this.blockModule();
            return;
        }
        this.context.set('activeTabIndex', 0);
        this.context.set('enabledModules', this.enabledModules);
    },

    /**
     * @override
     * To be able to configure the summary panel, we need at least the default values to exist.
     * This method needs to be overriden in order to allow operations.
     *
     * @return {boolean} True if we have the default metadata.
     */
    _checkConfigMetadata: function() {
        return !_.isEmpty(app.metadata.getView('', 'omnichannel-detail'));
    },
});

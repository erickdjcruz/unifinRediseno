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
 * @class View.Views.Base.Home.HelpletView
 * @alias SUGAR.App.view.views.BaseHomeHelpletView
 * @extends View.Views.Base.HelpletView
 */
({
    extendsFrom: 'HelpletView',

    /**
     * Console IDs mapped to the console label and support site module name
     */
    _consoleMap: {
        'c108bb4a-775a-11e9-b570-f218983a1c3e': {
            lang: 'LBL_AGENT_WORKBENCH',
            supportModule: 'ServiceConsole'
        },
    },

    /**
     * Method to fetch the help object from the app.help utility.
     */
    createHelpObject: function() {
        var helpUrl = {};
        // Console check
        var modelId = app.controller.context.get('modelId');
        if (this._consoleMap[modelId]) {
            helpUrl.plural_module_name = app.lang.get(this._consoleMap[modelId].lang);
        }
        this._super('createHelpObject', [helpUrl]);
    },

    /**
     * Consoles need to link to documentation differently
     *
     * @param {Object} params
     * @return {Object} params
     * @override
     */
    sanitizeUrlParams: function(params) {
        var modelId = app.controller.context.get('modelId');
        if (this._consoleMap[modelId]) {
            params.module = this._consoleMap[modelId].supportModule;
            delete params.route;
        }
        return params;
    }
})

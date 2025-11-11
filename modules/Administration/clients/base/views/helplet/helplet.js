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
 * A `helplet` is a view similar to a dashlet thats lives in the help
 * component.
 *
 * @class View.Views.Base.AdministrationHelpletView
 * @alias SUGAR.App.view.views.BaseAdministrationHelpletView
 * @extends View.View.HelpletView
 */
 ({
    extendsFrom: 'HelpletView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._productPageUrl = 'https://www.sugarcrm.com/crm/product_doc.php?';
        this._helpMeta = {};
    },

    /**
     * Build help meta depending on context
     */
    _computeHelpMeta: function() {
        const targetContext = app.controller.context;
        const route = targetContext.get('layout');

        if (route === 'maps-config' || route === 'maps-logger-config') {
            this._helpMeta.route = route;
            this._helpMeta.moduleName = 'MapsAdmin';
            this._helpMeta.label = 'LBL_SUGAR_MAPS';
        } else if (route === 'drive-path') {
            this._helpMeta.route = route;
            this._helpMeta.moduleName = 'CloudDriveAdmin';
            this._helpMeta.label = 'LBL_CLOUD_DRIVE';
        } else {
            this._helpMeta.route = route;
            this._helpMeta.label = null;
            this._helpMeta.moduleName = targetContext.get('module');
        };
    },

    /**
     * @inheritdoc
     */
    createHelpObject: function(langContext) {
        this._computeHelpMeta();
        if (this._helpMeta.moduleName === 'Administration') {
            this._super('createHelpObject', [langContext]);
        } else {
            this._createFeatureHelpMeta(langContext);
        }
    },

    /**
     * Create the help object for core applications
     */
    _createFeatureHelpMeta: function(langContext) {
        const helpUrl = _.extend({
            more_info_url: this._createMoreHelpLink(),
            more_info_url_close: '</a>'
        }, langContext);

        const moduleName = app.lang.get(this._helpMeta.label, 'Administration');
        const ctx = this.context.parent || this.context;

        this.helpObject = app.help.get(moduleName, ctx.get('layout'), helpUrl);
    },

    /**
     * @inheritdoc
     */
    _createMoreHelpLink: function() {
        const serverInfo = app.metadata.getServerInfo();
        const lang = app.lang.getLanguage();
        const products = app.user.getProductCodes().join(',');
        const module = this._helpMeta.moduleName;

        let params = {
            edition: serverInfo.flavor,
            version: serverInfo.version,
            lang,
            module,
            products,
        };

        this._productPageUrl += $.param(params);

        return '<a href="' + this._productPageUrl + '" target="_blank">';
    },
})

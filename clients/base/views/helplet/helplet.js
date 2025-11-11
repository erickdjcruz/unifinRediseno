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
 * TODO: SC-4808: Once MAR-2995 gets merged, we can instead have a collection of
 * helplet views that get iterated over in a helplet-list layout. This will
 * improve performance and provide better flexibility for managing independent
 * helplets.
 *
 * @class View.Views.Base.HelpletView
 * @alias SUGAR.App.view.views.BaseHelpletView
 * @extends View.View
 */
({
    /**
     * Holds the Object returned by `app.help.get()`. Example:
     * <pre><code>
     * {
     *    body: '',
     *    more_help: ''
     * }
     * </code></pre>
     *
     * @type {Object}
     */
    helpObject: {},

    /**
     * List of resources from meta
     */
    resources: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.createHelpObject();

        this.initResources();
    },

    /**
     * Initialize resources from meta
     */
    initResources: function() {
        if (this.meta && this.meta.resources) {
            this.resources = this.meta.resources;

            if (app.user.hasLicense('SUGAR_SELL_ESSENTIALS')) {
                this.resources = _.omit(this.resources, 'marketplace');
            }
        }
    },

    /**
     * Method to fetch the help object from the app.help utility.
     *
     * @param {Object|null} langContext Additional language to pass in the helpObject strings
     */
    createHelpObject: function(langContext) {
        var helpUrl = _.extend({
            more_info_url: this._createMoreHelpLink(),
            more_info_url_close: '</a>'
        }, langContext);
        var ctx = this.context.parent || this.context;
        this.helpObject = app.help.get(ctx.get('module'), ctx.get('layout'), helpUrl);
    },

    /**
     * Override this to sanitize and fix route elements if necessary
     *
     * @param {Object} params
     * @return {Object}
     */
    sanitizeUrlParams: function(params) {
        return params;
    },

    /**
     * Collects server version, language, module, and route and returns an HTML
     * link to be used in the template.
     *
     * @private
     * @return {string} The anchor tag for the 'More Help' link.
     */
    _createMoreHelpLink: function() {
        var serverInfo = app.metadata.getServerInfo();
        var lang = app.lang.getLanguage();
        var module = app.controller.context.get('module');
        var route = app.controller.context.get('layout');
        var products = app.user.getProductCodes().join(',');

        var params = {
            edition: serverInfo.flavor,
            version: serverInfo.version,
            lang: lang,
            module: module,
            route: route,
            products: products
        };

        if (params.route === 'records') {
            params.route = 'list';
        }

        if (params.route === 'bwc') {
            // Parse `action` URL param.
            var action = window.location.hash.match(/#bwc.*action=(\w*)/i);
            if (action && !_.isUndefined(action[1])) {
                params.action = action[1];
            }
        }

        params = this.sanitizeUrlParams(params);

        var url = 'https://www.sugarcrm.com/crm/product_doc.php?' + $.param(params);

        return '<a href="' + url + '" target="_blank">';
    }
})

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
 * The layout for the metrics-help component.
 *
 * @class View.Layouts.Base.ForecastsMetricsHelpLayout
 * @alias SUGAR.App.view.layouts.BaseForecastsMetricsHelpLayout
 * @extends View.Layouts.Base.HelpLayout
 */
({
    extendsFrom: 'HelpLayout',

    /**
     * URL to be used on the help button, this will navigate to SugarCRM Documention of Forecasts list view
     */
    helpUrl: '',

    /**
     * Initializes the popover plugin for the button given.
     *
     * @param {jQuery} button The jQuery button.
     * @override
     */
    _initPopover: function(button) {
        button.popover({
            title: this._getTitle('LBL_FILTER_GUIDE_TITLE'),
            content: _.bind(function() {
                return this.$el;
            }, this),
            container: '.metrics-help-button',
            html: true,
            template: '<div class="helpmodal metrics-help-modal overflow-hidden z-40 border border-solid ' +
                'border-[--border-color] rounded-md shadow-xl" data-modal="metrics-help">' +
                '<h3 class="popover-title popover-header"></h3>' +
                '<div class="popover-content !p-0 popover-body"></div>' +
                '</div>'
        });

        // reposition the popover when the window is resized
        $(window).on(`resize.${this.cid}`, _.debounce(_.bind(function() {
            if (this.button) {
                this.button.popover('show');
            }
        }, this),100));
    },

    /**
     * Collects server version, language, module, and route and returns an HTML
     * link to be used in the template.
     *
     * @private
     * @return {string} The anchor tag for the 'More Help' link.
     * @override
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

        return 'https://www.sugarcrm.com/crm/product_doc.php?' + $.param(params);
    },

    /**
     * Creates the helpObject if it has not yet been created for this.
     *
     * @override
     */
    _initHelpObject: function() {
        if (!this._helpObjectCreated) {
            this.helpUrl = this._createMoreHelpLink();
            this._helpObjectCreated = true;
        }
    },
    /**
     * Closes the Help modal if event target is outside of the Help modal.
     *
     * param {Object} evt jQuery event.
     * @override
     */
    closeOnOutsideClick: function(evt) {
        let target = $(evt.target);
        if (target.closest('.metrics-help-button').length === 0) {
            this.toggle(false);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off('resize');
        this._super('_dispose');
    }
})

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
 * @class View.Views.Base.MarketingExtrasView
 * @alias SUGAR.App.view.views.BaseMarketingExtrasView
 * @extends View.View
 */
({
    /**
     * The URL for marketing content
     */
    marketingContentUrl: '',

    /**
     * Have we forced a static fetch?
     */
    staticFetched: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.fetchMarketingContentUrl();

        app.events.on('app:locale:change', function() {
            app.router.refresh();
        }, this);
    },

    /**
     * Fetch the marketing content URL
     *
     * @param {boolean} forceStatic - flag to force retrieving static content URL
     */
    fetchMarketingContentUrl: function(forceStatic) {
        forceStatic = forceStatic || false;
        if (forceStatic) {
            this.staticFetched = true;
        }
        var language = app.user.getLanguage();
        var url = app.api.buildURL('login/marketingContentUrl', null, null, {
            selected_language: language,
            static: forceStatic
        });
        app.api.call('read', url, null, {
            success: _.bind(function(response) {
                this.marketingContentUrl = response;
                this.render();
            }, this),
        });
    },

    _render: function() {
        this._super('_render');
        try {
            this.$el.find('iframe').attr('src', this.marketingContentUrl);
        } catch (e) {
            app.logger.warn(e);
            if (!this.staticFetched) {
                this.fetchMarketingContentUrl(true);
            }
        }
    }
})

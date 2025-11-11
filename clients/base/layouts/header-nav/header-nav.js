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
 * @class View.Layouts.Base.HeaderNavLayout
 * @alias SUGAR.App.view.layouts.BaseHeaderNavLayout
 * @extends View.Layout
 */
({
    cache: null,

    /**
     * Listen to events to dispaly on login.
     * @param options
     */
    initialize: function(options) {
        this.cache = app[app.config.authStore || 'cache'];

        app.view.Layout.prototype.initialize.call(this, options);
        // Event listeners for showing and hiding the header-nav on
        // auth expiration
        app.events.on('app:login', this.hide, this);
        app.events.on('app:login:success', this.show, this);

        if (this.cache.has('ImpersonationFor')) {
            app.$rootEl.addClass('banner-shifted');
            $('#nprogress').addClass('banner-shifted');
        }
    },

    /**
     * Places all components within this layout inside nav-collapse div
     * @param component
     * @private
     */
    _placeComponent: function(component) {
        this.$el.find('.nav-collapse').append(component.$el);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // If we are authenticated show the top bar
        if (app.api.isAuthenticated() && (app.user.isSetupCompleted() || app.cache.has('ImpersonationFor'))) {
            this.show();
        } else {
            this.hide();
        }
    }
})

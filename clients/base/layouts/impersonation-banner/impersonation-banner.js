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
 * @class View.Layouts.Base.ImpersonationBanner
 * @alias SUGAR.App.view.layouts.BaseImpersonationBanner
 * @extends View.Layout
 */
({
    cache: null,

    /**
     * @param options
     */
    initialize: function(options) {
        this.cache = app[app.config.authStore || 'cache'];
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.cache.has('ImpersonationFor') && app.api.isAuthenticated()) {
            this.show();
        } else {
            this.hide();
        }
    },

})


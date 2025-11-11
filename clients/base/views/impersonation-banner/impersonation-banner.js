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
 * Indicate to sudoer(crm admin) that started impersonation session.
 *
 * @class View.Views.Base.ImpersonationBannerView
 * @alias SUGAR.App.view.views.ImpersonationBannerView
 * @extends View.View
 */
({
    sudoer: '',

    message: '',

    cache: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.cache = app[app.config.authStore || 'cache'];

        this._super('initialize', [options]);

        if (app.isSynced && !app.metadata.isSyncing()) {
            this.loadSudoer();
        }else {
            app.once('app:sync:complete', this.loadSudoer.bind(this));
        }
    },

    /**
     * Load Sudoer(admin that started impersonation session)
     */
    loadSudoer: function() {
        if (!this.cache.has('ImpersonationFor')) {
            return;
        }

        var url = app.api.buildURL(
            'Users',
            null,
            {id: this.cache.get('ImpersonationFor')},
            {fields: ['full_name', 'first_name']}
        );
        app.api.call('read', url, {},
            {
                success: function(res) {
                    this.sudoer = !_.isEmpty(res.first_name) ? res.first_name : res.full_name;
                    this._render();
                }.bind(this)
            }
        );

    },

    /**
     * @override
     * @private
     */
    _render: function() {

        if (!this.cache.has('ImpersonationFor')) {
            return ;
        }

        if ('' !== this.sudoer) {
            this.message = app.lang.get(
                'LBL_YOU_ARE_CURRENTLY_IMPERSONATING',
                null,
                {
                    sudoer: this.sudoer,
                    user: SUGAR.App.user.get('full_name'),
                }
            );
        }

        this._super('_render');
    }
})

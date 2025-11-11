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
 * @class View.Views.Base.FinishImpersonation
 * @alias SUGAR.App.view.views.FinishImpersonation
 * @extends View.View
 */
({

    cache: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.cache = app[app.config.authStore || 'cache'];
        this._super('initialize', [options]);
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        this.finishImpersonation();
    },

    /**
     * Finishing impersonation sesssion.
     */
    finishImpersonation: function() {
        let self = this;
        accessToken = this.cache.get('OriginAuthAccessToken');
        refreshToken = this.cache.get('OriginAuthRefreshToken');
        app.api.logout({
            complete: function(data) {
                self.cache.cut('ImpersonationFor');

                self.cache.set('AuthAccessToken', accessToken);
                self.cache.set('AuthRefreshToken', refreshToken);

                self.cache.cut('OriginAuthAccessToken');
                self.cache.cut('OriginAuthRefreshToken');

                if (!refreshToken) {
                    self.closeImpersonationWindow();
                    return;
                }

                payload = {
                    refresh: true,
                    access_token: accessToken,
                    refresh_token: refreshToken,
                };

                app.api.login(null, payload, {
                    success: function(data) {
                        // Have to login to BWC after admin token has been switched back
                        app.bwc.login(null, function() {
                            self.closeImpersonationWindow();
                        });
                    },
                    error: function(data) {
                        self.closeImpersonationWindow();
                    }
                });
            }
        });
    },

    closeImpersonationWindow: function() {
        if (window.opener) {
            window.close();
        } else {
            window.location.replace(this.getComeBackUrl());
        }
    },

    /**
     * Generate come back url.
     * @return {string}
     */
    getComeBackUrl: function() {
        return this.meta.comeBackUrl +
            '&user_hint=' +
            encodeURIComponent(app.utils.createUserSrn(app.cache.get('ImpersonationFor')));
    }

})

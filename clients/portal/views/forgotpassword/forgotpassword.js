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
 * Portal Forgot Password form view.
 *
 * @class View.Views.Portal.ForgotpasswordView
 * @alias SUGAR.App.view.views.PortalForgotpasswordView
 * @extends View.Views.Portal.FormView
 */
({
    extendsFrom: 'FormView',

    plugins: ['ErrorDecoration'],

    events: {
        'click [name=forgotPassword_button]': 'forgotPassword'
    },

    /**
     * Stores whether any Portal contact information has been configured
     */
    contactInfo: null,

    /**
     * @inheritdoc
     *
     * Grabs the Portal contact information to check if we should provide a link
     * to the contact info page
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        var contact = app.config.contactInfo || {};
        this.contactInfo = contact.contactPhone || contact.contactURL || contact.contactEmail;
        this.submitFunction = this.forgotPassword.bind(this);
    },

    /**
     * Gets the logo image for portal
     * @return string URL for the logo image for Portal
     */
    getLogoImage: function() {
        return app.config.logoURL || app.config.logomarkURL || app.metadata.getLogoUrl();
    },

    /**
     * Because we don't want any of the extra crap that stops it from rendering
     * @private
     */
    _render: function() {
        this.logoUrl = this.getLogoImage();
        app.view.View.prototype._render.call(this);
    },

    /**
     * Redirect to reset password confirmation page
     */
    forgotPassword: function() {
        var self = this;
        this.model.doValidate(this.getFields(null, this.model), function(isValid) {
            if (isValid) {
                var url = app.api.buildURL(
                    'password/resetemail',
                    '',
                    {},
                    {
                        platform: 'portal', username: self.model.get('username')
                    });
                app.api.call('read', url, null, {
                    success: function() {
                        app.router.navigate('#resetpwdconfirmation', {trigger: true});
                    },
                    error: function(error) {
                        app.alert.show('reset-email-fail', {
                            level: 'error',
                            title: app.lang.get(error.message),
                            autoClose: false
                        });
                    }
                });
            }
        });
    }
});

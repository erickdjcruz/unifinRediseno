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
 * Portal Reset Password form view.
 *
 * @class View.Views.Portal.ResetpasswordView
 * @alias SUGAR.App.view.views.PortalResetpasswordView
 * @extends View.Views.Portal.FormView
 */
({
    extendsFrom: 'FormView',

    plugins: ['ErrorDecoration'],

    events: {
        'click [name=reset_password_button]': 'resetPassword'
    },

    /**
     * Overrides the initialize function to add additional field validation
     * checks
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.addPasswordValidation();
        this.submitFunction = this.resetPassword.bind(this);
    },

    /**
     * Gets the logo image URL for portal
     * @return {string} The string containing the correct URL for the logo image
     */
    getLogoImage: function() {
        return app.config.logoURL || app.config.logomarkURL || app.metadata.getLogoUrl();
    },

    /**
     * Adds the logo URL to the view data
     * @protected
     */
    _render: function() {
        this.logoUrl = this.getLogoImage();
        app.view.View.prototype._render.call(this);
    },

    /**
     * Validate the passwords fields, and reset the password if valid
     */
    resetPassword: function() {
        var self = this;
        self.model.doValidate(self.getFields(null, self.model), function(isValid) {
            if (isValid) {
                app.alert.show('reset-password', {
                    level: 'process',
                    title: app.lang.get('LBL_SAVING'),
                    autoClose: false
                });

                // Build the data to send a password reset request to the server
                var url = app.api.buildURL('portal_password/reset/?platform=portal');
                var params = {
                    resetID: self.context.get('resetID'),
                    newPassword: self.model.get('password1')
                };

                // Initiate the password reset request
                app.api.call('update', url, params, {
                    success: function() {
                        app.alert.show('reset-password-success', {
                            level: 'success',
                            title: app.lang.get('LBL_PORTAL_PASSWORD_RESET_SUCCESS'),
                            autoClose: true,
                            autoCloseDelay: 5000
                        });
                        app.router.navigate('', {trigger: true});
                    },
                    error: function(error) {
                        app.alert.show('reset-password-fail', {
                            level: 'error',
                            title: app.lang.get(error.message),
                            autoClose: false
                        });
                    },
                    complete: function() {
                        app.alert.dismiss('reset-password');
                    }
                });
            }
        });
    },

    /**
     * Adds validation to check that the given password fields match each other
     * when they are filled
     */
    addPasswordValidation: function() {
        app.error.errorName2Keys.password_mismatch = 'LBL_PORTAL_PASSWORD_MISMATCH';
        var validateMatchingPasswords = function(fields, errors, callback) {
            var password1 = this.get('password1');
            var password2 = this.get('password2');
            if (password1 && password2) {
                if (password1 !== password2) {
                    app.alert.show('password_mismatch', {
                        level: 'error',
                        title: app.lang.get('LBL_PORTAL_PASSWORD_MISMATCH'),
                        autoClose: false
                    });

                    errors.password1 = errors.password1 || {};
                    errors.password2 = errors.password2 || {};
                    errors.password1.password_mismatch = true;
                    errors.password2.password_mismatch = true;
                } else {
                    var data = app.utils.validatePassword(password1);
                    if (password1 && !data.isValid) {
                        var errMsg = app.lang.get('LBL_PASSWORD_ENFORCE_TITLE');
                        if (data.error) {
                            errMsg +=  '<br><br>' + data.error;
                        }
                        app.alert.show('passwords_invalid', {
                            level: 'error',
                            messages: errMsg,
                        });
                        errors.password1 = errors.password1 || {};
                        errors.password2 = errors.password2 || {};
                        errors.password1.password_error = true;
                        errors.password2.password_error = true;
                    }
                }
            }
            callback(null, fields, errors);
        };
        this.model.addValidationTask('reset_password_' + this.cid,
            _.bind(validateMatchingPasswords, this.model));
    },

    /**
     * @inheritdoc
     *
     * Removes custom field validation created during initialization
     */
    _dispose: function() {
        this.model.removeValidationTask('reset_password_' + this.cid);
        this._super('_dispose');
    }
});

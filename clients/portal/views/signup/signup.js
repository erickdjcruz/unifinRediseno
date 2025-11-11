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
({
    extendsFrom: 'FormView',

    plugins: ['ErrorDecoration'],

    /**
     * Sign Up form view.
     * @class View.Views.SignupView
     * @alias SUGAR.App.view.views.SignupView
     */
    events: {
        'click [name=signup_button]': 'signup'
    },

    /**
     * Get the fields metadata from panels and declare a Bean with the metadata attached
     * @param meta
     * @private
     * @see View.Views.LoginView
     */
    _declareModel: function(meta) {
        meta = meta || {};

        var fields = {};
        _.each(_.flatten(_.pluck(meta.panels, "fields")), function(field) {
            fields[field.name] = field;
        });
        /**
         * Fields metadata needs to be converted to this format for App.data.declareModel
         *  {
          *     "first_name": { "name": "first_name", ... },
          *     "last_name": { "name": "last_name", ... },
          *      ...
          * }
         */
        app.data.declareModel('Signup', {fields: fields});
    },

    /**
     * @override
     * @param options
     */
    initialize: function(options) {
        // Declare a Bean so we can process field validation
        this._declareModel(options.meta);

        // Reprepare the context because it was initially prepared without metadata
        options.context.prepare(true);

        this._super('initialize', [options]);

        this.model.set({
            'email': [
                {
                    'email_address': '',
                    'primary_address': '1'
                }
            ]
        }, {silent: true});

        // FIXME: Enforce action `edit` on portal signup to avoid field render on
        // `bindDataChange`. This should be fixed when SC-3145.
        this.action = 'edit';
        this.addPasswordValidation();
        this.submitFunction = this.signup.bind(this);
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.logoUrl = app.metadata.getLogoUrl();
        this._super('_renderHtml');
        return this;
    },

    /**
     * For USA country only we need to display the State dropdown
     * FIXME TY-129 create a country fieldset and move this into it
     * properties.
     * @deprecated as of 9.2.0 since we don't support state dropdown on signup
     */
    toggleStateField: function() {
        app.logger.warn('View.Views.SignupView.toggleStateField is deprecated.');
        // Do nothing
    },

    /**
     * Basic cancel button
     * @deprecated as of 9.2.0 since we don't support cancel button on signup
     */
    cancel: function() {
        app.logger.warn('View.Views.SignupView.cancel is deprecated.');
        // Do nothing
    },

    /**
     * Prepares Signup API request payload based on form's model and language preferences
     * @return {Object} Signup API request payload.
     * @return {string} return.first_name The user's first name.
     * @return {string} return.last_name The user's last name.
     * @return {string} return.email The user's email address.
     * @return {string} return.portal_user_company_name The user's portal user company name.
     * @return {string} return.portal_name The user's portal name.
     * @return {string} return.portal_password The user's portal password.
     * @return {string} return.portal_password1 The user's portal password1.
     * @return {string} return.preferred_language The user's preferred language.
     * @private
     */
    _prepareRequestPayload: function() {
        var data = {
            first_name: this.model.get('first_name'),
            last_name: this.model.get('last_name'),
            email: this.model.get('email'),
            portal_user_company_name: this.model.get('company_name'),
            portal_name: this.model.get('user_name'),
            portal_password: this.model.get('password'),
            portal_password1: this.model.get('password1')
        };
        // Sets the preferred language based on the current loaded language. Can be undefined.
        var language = app.lang.getLanguage();
        if (language) {
            data.preferred_language = language;
        }
        return data;
    },

    /**
     * Handles Sign Up
     */
    signup: function() {
        var self = this;

        self.model.doValidate(this.getFields(null, this.model), function(isValid) {
            if (isValid) {
                app.$contentEl.hide();
                app.alert.show('signup', {level: 'process', title: app.lang.get('LBL_PORTAL_SIGNUP_PROCESS'), autoClose: false});

                var payload = self._prepareRequestPayload();
                app.api.signup(payload, null,
                    {
                        success: function(id) {
                            if (id) {
                                app.logger.warn('A Contact record is created. Contact Id:' + id);
                                app.router.navigate('#signup-success', {trigger: true});
                            }
                        },
                        error: function(err) {
                            app.alert.dismiss('signup');
                            app.$contentEl.show();
                            if (err && err.status === 403) {
                                app.alert.show('server-error', {
                                    level: 'error',
                                    messages: 'LBL_PORTAL_SIGNUP_USER_NAME_ERROR',
                                    title: app.lang.get('LBL_PORTAL_ERROR')
                                });
                            } else if (err && err.status === 422) {
                                app.alert.show('server-error', {
                                    level: 'error',
                                    messages: 'LBL_PORTAL_SIGNUP_EMAIL_ERROR',
                                    title: app.lang.get('LBL_PORTAL_ERROR')
                                });
                            } else if (err && err.status === 424) {
                                app.alert.show('server-error', {
                                    level: 'error',
                                    messages: 'LBL_PASSWORD_ENFORCE_TITLE',
                                    title: app.lang.get('LBL_PORTAL_ERROR')
                                });
                            } else {
                                app.alert.show('server-error', {
                                    level: 'error',
                                    messages: 'LBL_PORTAL_OFFLINE',
                                    title: app.lang.get('LBL_PORTAL_ERROR')
                                });
                            }
                        },
                        complete: function() {
                            app.alert.dismiss('signup');
                            app.$contentEl.show();
                        }
                    });
            }
        }, self);
    },

    /**
     * Adds validation to check that the given password meets password rules
     */
    addPasswordValidation: function() {
        app.error.errorName2Keys.password_error = 'LBL_PASSWORD_ENFORCE_TITLE';
        var validatePasswordRules = function(fields, errors, callback) {
            var password = this.get('password');
            var data = app.utils.validatePassword(password);
            if (password && !data.isValid) {
                var errMsg = app.lang.get('LBL_PASSWORD_ENFORCE_TITLE');
                if (data.error) {
                    errMsg +=  '<br><br>' + data.error;
                }
                app.alert.show('passwords_invalid', {
                    level: 'error',
                    messages: errMsg,
                });
                errors.password = errors.password || {};
                errors.password.password_error = true;
                errors.password1 = errors.password1 || {};
                errors.password1.password_error = true;
            }
            callback(null, fields, errors);
        };
        this.model.addValidationTask('password_rules_' + this.cid, _.bind(validatePasswordRules, this.model));
    },

    /**
     * @inheritdoc
     *
     * Removes custom field validation created during initialization
     */
    _dispose: function() {
        this.model.removeValidationTask('password_rules_' + this.cid);
        this._super('_dispose');
    }

})

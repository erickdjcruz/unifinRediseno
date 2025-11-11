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
 * @class View.Views.Base.ChangePasswordView
 * @alias SUGAR.App.view.views.BaseChangePasswordView
 * @extends View.View
 */
({
    className: 'background-image-stack bg-[size:1000px] bg-[--primary-content-background] h-full w-full flex' +
        ' flex-column items-center justify-center',

    plugins: [
        'ErrorDecoration',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initPasswordRequirements();
        this._initButtonEvents();
        this._initValidationFields();
        this.model = app.data.createBean(null);
    },

    /**
     * Initializes the list of password requirements for the template
     *
     * @private
     */
    _initPasswordRequirements: function() {
        this._requirements = {};
        let passwordSettings = app.config.passwordsetting || {};
        let requirementDefs = [
            {
                name: 'maxpwdlength',
                label: 'LBL_PASSWORD_MAX_LENGTH',
                value: passwordSettings.maxpwdlength
            },
            {
                name: 'minpwdlength',
                label: 'LBL_PASSWORD_MIN_LENGTH',
                value: passwordSettings.minpwdlength
            },
            {
                name: 'onenumber',
                label: 'LBL_PASSWORD_ONE_NUMBER',
                value: passwordSettings.onenumber
            },
            {
                name: 'onelower',
                label: 'LBL_PASSWORD_ONE_LOWERCASE',
                value: passwordSettings.onelower
            },
            {
                name: 'oneupper',
                label: 'LBL_PASSWORD_ONE_UPPERCASE',
                value: passwordSettings.oneupper
            },
            {
                name: 'onespecial',
                label: 'LBL_PASSWORD_ONE_SPECIAL_CHAR',
                value: passwordSettings.onespecial
            },
        ];

        _.each(requirementDefs, function(requirementDef) {
            if (app.utils.isPasswordRuleSet(requirementDef.value)) {
                this._requirements[requirementDef.name] = {
                    label: app.utils.formatString(app.lang.get(requirementDef.label), [requirementDef.value])
                };
            }
        }, this);

        if (_.isString(passwordSettings.customregex) && passwordSettings.customregex.length > 0) {
            let regexSettings = {
                label: app.utils.formatString(app.lang.get('LBL_PASSWORD_REGEX_REQUIREMENT'),
                    [passwordSettings.customregex])
            };
            if (passwordSettings.regexcomment) {
                regexSettings.sublabel = translate(passwordSettings.regexcomment);
            }

            this._requirements.customregex = regexSettings;
        }
    },

    /**
     * Initializes button click events
     *
     * @private
     */
    _initButtonEvents: function() {
        this.listenTo(this.context, 'button:cancel_button:click', this._cancelClicked);
        this.listenTo(this.context, 'button:confirm_button:click', this._confirmClicked);
    },

    /**
     * Initializes the list of fields to validate
     *
     * @private
     */
    _initValidationFields: function() {
        this.fieldsToValidate = {};
        _.each(this.meta.fields, function(field) {
            if (field.name !== 'name_field') {
                this.fieldsToValidate[field.name] = field;
            }
        }, this);
    },

    /**
     * Handles when the "Cancel" button has been clicked
     *
     * @private
     */
    _cancelClicked: function() {
        if (app.drawer.count()) {
            app.drawer.close();
        }
    },

    /**
     * Handles when the "Confirm" button has been clicked
     *
     * @private
     */
    _confirmClicked: function() {
        this.clearValidationErrors();
        this.model.doValidate(this.fieldsToValidate, (isValid) => {
            if (isValid) {
                let currentPassword = this.model.get('current_password');
                let newPassword = this.model.get('new_password');
                let newPasswordConfirm = this.model.get('new_password_confirm');

                if (this._validatePasswords(newPassword, newPasswordConfirm)) {
                    this._updatePassword(currentPassword, newPassword);
                }
            }
        });
    },

    /**
     * Validates the password fields to ensure they do not violate any of the
     * password policies
     *
     * @param {string} newPassword the new password value
     * @param {string} newPasswordConfirm the confirm new password value
     * @return {boolean} true if validation is successful; false otherwise
     * @private
     */
    _validatePasswords: function(newPassword, newPasswordConfirm) {
        if (newPassword !== newPasswordConfirm) {
            app.alert.show('passwords_mismatch', {
                level: 'error',
                messages: app.lang.get('ERR_REENTER_PASSWORDS'),
                autoClose: true,
                autoCloseDelay: 5000,
            });

            return false;
        }

        let newPasswordValidation = app.utils.validatePassword(newPassword);
        if (!newPasswordValidation.isValid) {
            let errMsg = app.lang.get('LBL_PASSWORD_ENFORCE_TITLE');
            if (newPasswordValidation.error) {
                errMsg += '<br><br>' + newPasswordValidation.error;
            }
            app.alert.show('passwords_invalid', {
                level: 'error',
                messages: errMsg,
            });

            return false;
        }

        return true;
    },

    /**
     * Calls the Sugar API to update the current User's password
     *
     * @param {string} currentPassword the User's current password
     * @param {string} newPassword the new password for the User
     * @private
     */
    _updatePassword: function(currentPassword, newPassword) {
        // Check to see if a robot has reached into the honey pot. If so, do
        // not update the password (name_field not real)
        if (app.config.honeypot_on && app.config.honeypot_on === true &&
            (this.$('input[name="name_field"]').val() || this.model.get('name_field'))) {
            return;
        }

        app.alert.show('password-reset', {
            level: 'process',
            title: app.lang.get('LBL_SAVING'),
            autoclose: false
        });
        app.api.updatePassword(currentPassword, newPassword, {
            success: function(data) {
                if (data && data.valid) {
                    //Password was valid and update successful
                    app.alert.show('password-reset-success', {
                        level: 'success',
                        messages: app.lang.get('LBL_PASSWORD_CHANGED'),
                        autoClose: true,
                        autoCloseDelay: 5000
                    });
                    if (app.drawer.count()) {
                        app.drawer.close();
                    }
                } else if (data.message) {
                    //Password was deemed invalid by server. Display provided message
                    app.alert.show('password-invalid', {
                        level: 'error',
                        title: null,
                        messages: data.message
                    });
                } else {
                    //Server should have provided data.message; use a generic message as fallback
                    app.alert.show('password-invalid', {
                        level: 'error',
                        messages: app.lang.get('ERR_GENERIC_TITLE') + ': ' +
                            app.lang.get('ERR_CONTACT_TECH_SUPPORT')
                    });
                }
            },
            error: (error) => {
                app.error.handleHttpError(error);
            },
            complete: () => {
                app.alert.dismiss('password-reset');
            }
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    }
})

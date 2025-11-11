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
 * @class View.Fields.Base.Users.EmailCredentialsField
 * @alias SUGAR.App.view.fields.BaseUsersEmailCredentialsField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    events: {
        'click button.change-password': '_changePasswordClicked',
        'click button.authorize': '_authorizeClicked',
        'click button.test-email': '_testEmailClicked',
        'click button.test-email-cancel': '_cancelTestEmailClicked',
        'click button.test-email-send': '_sendTestEmailClicked',
    },

    /**
     * Stores dynamic window message listener to clear it on dispose
     */
    messageListeners: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.oauth2Types = {
            google_oauth2: {
                application: 'GoogleEmail',
                dataSource: 'googleEmailRedirect'
            },
            exchange_online: {
                application: 'MicrosoftEmail',
                dataSource: 'microsoftEmailRedirect'
            }
        };

        // Load connector information
        this.connectorsLoaded = false;
        this._loadOauth2TypeInformation(() => {
            this.connectorsLoaded = true;
            this.render();
        });
    },

    /**
     * @inheritdoc
     */
    bindDomChange: function() {
        if (!(this.model instanceof Backbone.Model)) {
            return;
        }

        let el = this.$el.find(this.fieldTag);
        el.on('change', (event) => {
            let $target = $(event.currentTarget);
            let changedName = $target.data('name');
            let changedValue = $target.val();
            let modelValue = this.model.get(this.name) || {};
            modelValue[changedName] = changedValue;
            this.model.set(this.name, modelValue);
        });
    },

    /**
     * Sets the change_smtp_password flag when the "Change Password" button
     * is clicked
     *
     * @private
     */
    _changePasswordClicked: function() {
        let modelValue = this.model.get(this.name) || {};
        modelValue.mail_smtppass_change = true;
        this.model.set(this.name, modelValue);
        this.render();
    },

    /**
     * Initializes the authorization information for the OAuth2 tabs
     *
     * @param {Function} callback the callback to run after information is fetched
     * @private
     */
    _loadOauth2TypeInformation: function(callback) {
        _.each(this.oauth2Types, function(properties, smtpType) {
            if (!_.isUndefined(properties.auth_url)) {
                return;
            }
            var url = app.api.buildURL('EAPM', 'auth', {}, {application: properties.application});
            var callbacks = {
                success: (data) => {
                    if (data) {
                        this.oauth2Types[smtpType].auth_url = data.auth_url || false;
                    }
                },
                error: () => {
                    this.oauth2Types[smtpType].auth_url = false;
                },
                complete: () => {
                    callback.call(this);
                }
            };
            var options = {
                showAlerts: false,
                bulk: 'loadOauth2TypeInformation',
            };
            app.api.call('read', url, {}, callbacks, options);
        }, this);
        app.api.triggerBulkCall('loadOauth2TypeInformation');
    },

    /**
     * Handles auth when the Authorize button is clicked.
     */
    _authorizeClicked: function() {
        let smtpType = this.credentials.mail_smtptype;
        if (this.oauth2Types[smtpType] && this.oauth2Types[smtpType].auth_url) {
            let authorizationListener = (message) => {
                if (this) {
                    this.handleAuthorizeComplete(message, smtpType);
                }
                window.removeEventListener('message', authorizationListener);
            };
            window.addEventListener('message', authorizationListener);
            this.messageListeners.push(authorizationListener);
            let width = 600;
            let height = 600;
            let left = (screen.width - width) / 2;
            let top = (screen.height - height) / 4;
            window.open(
                this.oauth2Types[smtpType].auth_url,
                '_blank',
                `width=${width},height=${height},left=${left},top=${top},resizable=1`
            );
        }
    },

    /**
     * Handles the oauth completion event.
     * Note that the EAPM bean has already been saved at this point.
     *
     * @param {Object} message the message data received from the auth window
     * @param {string} smtpType the SMTP type authorized with
     * @return {boolean} True if success, otherwise false
     */
    handleAuthorizeComplete: function(message, smtpType) {
        let data = JSON.parse(message.data) || {};
        if (!data.dataSource ||
            !this.oauth2Types[smtpType] ||
            data.dataSource !== this.oauth2Types[smtpType].dataSource) {
            return false;
        }
        if (data.eapmId && data.emailAddress && data.userName) {
            let modelValue = this.model.get(this.name) || {};
            modelValue.eapm_id = data.eapmId;
            modelValue.authorized_account = data.emailAddress;
            modelValue.mail_smtpuser = data.userName;
        } else {
            app.alert.show('error', {
                level: 'error',
                messages: app.lang.get('LBL_EMAIL_AUTH_FAILURE', this.module)
            });
        }
        this.render();
        return true;
    },

    /**
     * Shows the test email modal when the Send Test Email button is clicked
     *
     * @private
     */
    _testEmailClicked: function() {
        this.$('.test-email-dialog').removeClass('hide');
    },

    /**
     * Hides the test email modal when the Cancel button in it is clicked
     *
     * @private
     */
    _cancelTestEmailClicked: function() {
        this.$('.test-email-dialog').addClass('hide');
    },

    /**
     * Handles when the Send button is clicked in the test email modal
     *
     * @private
     */
    _sendTestEmailClicked: function() {
        // Hide the modal
        this.$('.test-email-dialog').addClass('hide');

        // Build the arguments for the send test email API
        let settings = this.model.get(this.name);
        let args = {
            user_id: this.model.get('id'),
            name: this.model.get('full_name'),
            from_address: this._getFromAddress(),
            to_address: this.$('input.test-address').val(),
            mail_smtpuser: settings.mail_smtpuser,
            mail_smtppass: settings.mail_smtppass,
            eapm_id: settings.eapm_id,
        };

        // Call the send test email API
        app.alert.show('send_test_email_in_progress', {
            level: 'process',
            messages: 'LBL_LOADING',
            autoClose: false
        });

        let url = app.api.buildURL('OutboundEmail/testUserOverride');
        app.api.call('create', url, args, {
            success: (result) => {
                app.alert.show('send_test_email_results', {
                    level: result.status ? 'success' : 'error',
                    messages: result.status ? 'LBL_EMAIL_TEST_NOTIFICATION_SENT' : result.errorMessage,
                    autoClose: true,
                    autoCloseDelay: 5000
                });
            },
            complete: () => {
                app.alert.dismiss('send_test_email_in_progress');
            }
        });
    },

    /**
     * Gets the from address for sending a test email (based on the User's
     * primary email address) if it is available
     *
     * @return {string|null} the User's primary email address if available, or null
     * @private
     */
    _getFromAddress: function() {
        let emailAddrs = this.model.get('email');
        if (emailAddrs) {
            let fromAddr = _.findWhere(emailAddrs, {primary_address: true});
            if (fromAddr) {
                return fromAddr.email_address;
            }
        }

        return null;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.credentials = this.model.get(this.name);
        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        _.each(this.messageListeners, function(listener) {
            window.removeEventListener('message', listener);
        }, this);
        this.messageListeners = [];
        this._super('_dispose');
    }
});

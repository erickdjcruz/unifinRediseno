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
 * @class View.Fields.Base.OutboundEmail.EmailAuthorizeField
 * @alias SUGAR.App.view.fields.BaseOutboundEmailEmailAuthorizeField
 * @extends View.Fields.Base.ButtonField
 */
({
    extendsFrom: 'ButtonField',

    /**
     * email-provider field name
     */
    smtpField: 'mail_smtptype',

    events: {
        'click .btn': 'handleAuthorizeStart'
    },

    /**
     * SMTP providers requiring OAuth2.
     *
     * @property {Object}
     */
    oauth2Types: null,

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
    },

    /**
     * Handles storing/retrieving OAuth2 values when switching between OAuth2 tabs
     */
    bindDataChange: function() {
        this.listenTo(this.model, `change:${this.smtpField}`, function() {
            var previousValue = this.model.previous(this.smtpField);
            var newValue = this.model.get(this.smtpField);

            if (!this.oauth2Types[newValue]) {
                // This is not an OAuth2 tab
                this.model.set('mail_authtype', null);
            } else {
                // This is an OAuth2 tabcheck
                var oauth2Values = {
                    eapm_id: this.model.get('eapm_id'),
                    authorized_account: this.model.get('authorized_account'),
                    mail_smtpuser: this.model.get('mail_smtpuser')
                };
                if (_.isUndefined(previousValue)) {
                    // When the record first loads on an OAuth2 tab, store the values
                    // for that tab
                    this.storeOauth2Values(newValue, oauth2Values);
                } else {
                    // When switching from an OAuth2 tab, store the values for that
                    // tab
                    if (this.oauth2Types[previousValue]) {
                        this.storeOauth2Values(previousValue, oauth2Values);
                    }
                }
            }
            this.render();
        });
        this._super('bindDataChange');
    },

    /**
     * Stores the OAuth2 authorization values for an OAuth2 tab
     *
     * @param {string} smtpType the unique indicator of the OAuth2 tab
     * @param {Object} values to store for the OAuth2 tab
     */
    storeOauth2Values: function(smtpType, values) {
        _.extendOwn(this.oauth2Types[smtpType], values || {});
    },

    /**
     * Handles auth when the button is clicked.
     */
    handleAuthorizeStart: function() {
        var smtpType = this.model.get(this.smtpField);
        if (this.oauth2Types[smtpType] && this.oauth2Types[smtpType].auth_url) {
            let authorizationListener = _.bind(function(e) {
                if (this) {
                    this.handleAuthorizeComplete(e, smtpType);
                }
                window.removeEventListener('message', authorizationListener);
            }, this);
            window.addEventListener('message', authorizationListener);
            this.messageListeners.push(authorizationListener);
            var height = 600;
            var width = 600;
            var left = (screen.width - width) / 2;
            var top = (screen.height - height) / 4;
            var submitWindow = window.open(
                '/',
                '_blank',
                'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',resizable=1'
            );
            submitWindow.location.href = 'about:blank';
            submitWindow.location.href = this.oauth2Types[smtpType].auth_url;
        }
    },

    /**
     * Handles the oauth completion event.
     * Note that the EAPM bean has already been saved at this point.
     *
     * @param {Object} e
     * @param {string} smtpType
     * @return {boolean} True if success, otherwise false
     */
    handleAuthorizeComplete: function(e, smtpType) {
        var data = JSON.parse(e.data) || {};
        if (!data.dataSource ||
            !this.oauth2Types[smtpType] ||
            data.dataSource !== this.oauth2Types[smtpType].dataSource) {
            return false;
        }
        if (data.eapmId && data.emailAddress && data.userName) {
            // Store the authorization information for the OAuth smtpType
            this.storeOauth2Values(smtpType, {
                eapm_id: data.eapmId,
                authorized_account: data.emailAddress,
                mail_smtpuser: data.userName
            });
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
     * Extends the parent _render to hide the field until connector information
     * is loaded in order to guarantee that OAuth2 tabs show the correct
     * connector authorization elements
     * @private
     */
    _render: function() {
        // Load connector information before rendering
        if (!this.connectorsLoaded) {
            this.hide();
            if (!this.connectorsAreLoading) {
                this.connectorsAreLoading = true;
                this._loadOauth2TypeInformation(_.bind(function() {
                    if (!this.disposed) {
                        this.connectorsAreLoading = false;
                        this.connectorsLoaded = true;
                        this.render();
                    }
                }, this));
            }
        } else {
            var smtpType = this.model.get(this.smtpField);
            this._loadOauth2Values(smtpType);
            this._displayAuthorizationElements(smtpType);
            this.show();
            this._super('_render');
        }

    },

    /**
     * Initializes the authorization information for the OAuth2 tabs
     *
     * @param callback
     * @private
     */
    _loadOauth2TypeInformation: function(callback) {
        _.each(this.oauth2Types, function(properties, smtpType) {
            if (!_.isUndefined(properties.auth_url)) {
                return;
            }
            var url = app.api.buildURL('EAPM', 'auth', {}, {application: properties.application});
            var callbacks = {
                success: _.bind(function(data) {
                    if (data) {
                        this.oauth2Types[smtpType].auth_url = data.auth_url || false;
                        this.oauth2Types[smtpType].auth_warning = data.auth_warning || '';
                    }
                }, this),
                error: _.bind(function() {
                    this.oauth2Types[smtpType].auth_url = false;
                    this.oauth2Types[smtpType].auth_warning = app.lang.get('LBL_EMAIL_AUTH_API_ERROR');
                }, this),
                complete: _.bind(function() {
                    callback.call(this);
                }, this)
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
     * Loads any existing OAuth2 authorization values for a tab
     * @param {string} smtpType the unique indicator of the tab
     */
    _loadOauth2Values: function(smtpType) {
        if (!this.oauth2Types[smtpType]) {
            return;
        }
        this.model.set({
            mail_authtype: 'oauth2',
            eapm_id: this.oauth2Types[smtpType].eapm_id || null,
            authorized_account: this.oauth2Types[smtpType].authorized_account || '',
            mail_smtpuser: this.oauth2Types[smtpType].mail_smtpuser || ''
        });
    },

    /**
     * Determines what authorization elements to show based on the selected tab
     *
     * @param {string} smtpType
     */
    _displayAuthorizationElements: function(smtpType) {
        this.authWarning = '';
        this.authButton = false;

        // If this is an OAuth2 tab, display the correct authorization controls
        if (this.oauth2Types[smtpType] && this.action === 'edit') {
            this.$el.closest('.record-cell').show();
            if (this.oauth2Types[smtpType].auth_url) {
                // This tab's connector is enabled, so enable the authorization button
                this.authButton = 'enabled';
            } else if (this.oauth2Types[smtpType].auth_url === false) {
                // This tab's connector is not enabled, so disable the authorization button
                this.authWarning = this.oauth2Types[smtpType].auth_warning;
                this.authButton = 'disabled';
            }
        } else {
            this.$el.closest('.record-cell').hide();
        }
    },

    /**
     * Extends parent _dispose to remove any leftover window listeners
     * @private
     */
    _dispose: function() {
        _.each(this.messageListeners, function(listener) {
            window.removeEventListener('message', listener);
        }, this);
        this.messageListeners = [];
        this.stopListening();
        this._super('_dispose');
    }
})

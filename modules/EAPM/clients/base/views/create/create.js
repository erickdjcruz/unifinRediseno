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
 * @class View.Views.Base.EAPM.CreateView
 * @alias SUGAR.App.view.views.EAPMCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit();

        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Before init properties handling
     */
    _beforeInit: function() {
        this.fullAPIList = {};
        this.messageListenersToBeRemoved = [];

        this._getExternApiList();
    },

    /**
     * Fetch the external api list
     */
    _getExternApiList: function() {
        app.alert.show('eapm-loading-list', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
        });

        const url = app.api.buildURL(this.options.module, 'list');

        app.api.call('read', url, null, {
            success: _.bind(function(data) {
                if (this.disposed) {
                    return;
                }

                this.fullAPIList = data;

                this.setupFields();
            }, this),
            complete: _.bind(function() {
                app.alert.dismiss('eapm-loading-list');
            }, this),
        });
    },

    /**
     * Register related events
     */
    _registerEvents: function() {
        this.listenTo(this.model, 'change:application', this.setupFields, this);
    },

    /**
     * show or hide a field
     *
     * @param {string} fieldName
     * @param {bool} fieldName
     */
    _showHideField: function(fieldName, show) {
        const field = this.getField(fieldName);

        if (!field) {
            return;
        }

        const fieldEl = field.getFieldElement();

        if (!fieldEl) {
            return;
        }

        const fieldHolder = fieldEl.closest('.record-cell');

        show ? fieldHolder.show() : fieldHolder.hide();
    },

    /**
     * set if a field is required
     *
     * @param {string} fieldName
     * @param {bool} fieldName
     */
    _setFieldRequired: function(fieldName, required) {
        const field = this.getField(fieldName);

        if (!field) {
            return;
        }

        field.def.required = !!required;
        field.render();
    },

    /**
     * Setup the fields
     */
    setupFields: function() {
        if (_.isEmpty(this.fullAPIList)) {
            return;
        }

        const applicationType = this.model.get('application');
        const applicationMeta = this.fullAPIList[applicationType];

        this._setFieldRequired('application', true);

        if (!applicationMeta) {
            //here we don't have an application type selected in the UI
            const showFields = true;

            this._showHideField('url', showFields);
            this._showHideField('name', showFields);
            this._showHideField('password', showFields);

            this._setFieldRequired('url', showFields);
            this._setFieldRequired('name', showFields);
            this._setFieldRequired('password', showFields);

            return;
        }

        const needsUrl = applicationMeta.needsUrl ? true : false;
        const isAuthMethPassword = applicationMeta.authMethod === 'password';

        this._showHideField('url', needsUrl);
        this._showHideField('name', isAuthMethPassword);
        this._showHideField('password', isAuthMethPassword);

        this._setFieldRequired('url', needsUrl);
        this._setFieldRequired('name', isAuthMethPassword);
        this._setFieldRequired('password', isAuthMethPassword);
    },

    /**
     *  Start the authorization
     *
     * @inheritdoc
     */
    save: function() {
        this.disableButtons();

        async.waterfall(
            [
                _.bind(this.validateModelWaterfall, this),
                _.bind(this._startAuthProcess, this),
            ],
            _.bind(function(error) {
                this.enableButtons();

                if (error && error.status === 412 && !error.request.metadataRetry) {
                    this.handleMetadataSyncError(error);
                }
            }, this),
            this
        );
    },

    /**
     * Start the oauth process
     */
    _startAuthProcess: function() {
        const applicationType = this.model.get('application');
        const applicationMeta = this.fullAPIList[applicationType];
        const isPassword = applicationMeta.authMethod === 'password';

        if (isPassword) {
            //add logic for webex
        } else {
            this._startOauthProcess();
        }
    },

    /**
     * Start the oauth logging process
     *
     */
    _startOauthProcess: function() {
        const applicationType = this.model.get('application');

        app.alert.show('eapm-loading-client', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
        });

        const url = app.api.buildURL('EAPM', 'auth', null, {application: applicationType});

        app.api.call('read', url, null, {
            success: _.bind(function(data) {
                if (this.disposed) {
                    return;
                }

                if (!_.has(data, 'auth_url')) {
                    this._showFailedAlert('LBL_AUTH_UNSUPPORTED');
                    this.enableButtons();

                    return;
                }

                this._startAuth2(data);
            }, this),
            error: _.bind(function(data) {
                app.alert.dismiss('eapm-loading-client');
                this._showFailedAlert();

                this.enableButtons();
            }, this),
            complete: _.bind(function() {
                app.alert.dismiss('eapm-loading-client');
            }, this),
        });
    },

    /**
     * Start the oauth process
     *
     * @param {Object} data
     */
    _startAuth2: function(data) {
        const authorizationListener = _.bind(function(e) {
            if (this) {
                this.handleAuthorizeComplete(e);
            }
            window.removeEventListener('message', authorizationListener);
        }, this);

        window.addEventListener('message', authorizationListener);
        this.messageListenersToBeRemoved.push(authorizationListener);

        this._openAuthWindow(data);
    },

    /**
     * Open the auth window
     *
     * @param {Object} data
     */
    _openAuthWindow: function(data) {
        const urlData = {
            height: 600,
            width: 600,
            left: (screen.width - 600) / 2,
            top: (screen.height - 600) / 4,
            resizable: 1,
        };

        const urlParams = Object.entries(urlData).map(([key, value]) => `${key}=${value}`).join(',');
        const submitWindow = window.open('/', '_blank', urlParams);

        const dataKey = 'auth_url';

        submitWindow.location.href = 'about:blank';
        submitWindow.location.href = data[dataKey];

        //we have to re-enable the cancel/connect buttons.
        //since the opened window will be on a different origin
        //we can't add a listener like beforeunload
        //so we have to use an workaround here
        const checkChildWindow = setInterval(_.bind(function() {
            if (submitWindow.closed) {
                clearInterval(checkChildWindow);

                if (this.disposed) {
                    return;
                }

                this.enableButtons();
            }
        }, this), 500);
    },

    /**
     * Handles the oauth completion event.
     * Note that the EAPM bean has already been saved at this point.
     *
     * @param {Object} e
     * @param {string} smtpType
     * @return {boolean} True if success, otherwise false
     */
    handleAuthorizeComplete: function(e) {
        if (!e) {
            this._showFailedAlert();
            this.enableButtons();

            return;
        }

        if (!e.data) {
            this._showFailedAlert();
            this.enableButtons();

            return;
        }

        try {
            const response = JSON.parse(e.data);

            if (!response || !response.result || response.result !== true) {
                this._showFailedAlert();
                this.enableButtons();

                return;
            }

            this._showSuccessAlert();
            this._closeDrawer();
        } catch (e) {
            this._showFailedAlert();
            this.enableButtons();

            return;
        }
    },

    /**
     * Display a success alery
     *
     */
    _showSuccessAlert: function() {
        app.alert.show('success', {
            level: 'success',
            messages: app.lang.get('LBL_CONNECTED', this.module),
            autoClose: true
        });
    },

    /**
     * Display a failed message
     *
     * @param {string} label
     */
    _showFailedAlert: function(label = 'LBL_AUTH_ERROR') {
        app.alert.show('error', {
            level: 'error',
            messages: app.lang.get(label, this.module)
        });
    },

    /**
     * Close the drawer
     */
    _closeDrawer: function() {
        if (this.closestComponent('drawer')) {
            app.drawer.close(this.context, this.model);
        } else {
            app.navigate(this.context, this.model);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');

        _.each(this.messageListenersToBeRemoved, function(listener) {
            window.removeEventListener('message', listener);
        }, this);

        this.messageListenersToBeRemoved = [];

        this.stopListening();
    }
});

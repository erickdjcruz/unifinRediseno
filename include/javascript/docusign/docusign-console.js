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

(function DocuSignConsole(app) {

    const utils = {
        /**
         * Initiate send
         *
         * @param {Object} payload
         */
        initiateSend: function(payload, step) {
            if (_.isUndefined(payload.processInProgress) && app.DocuSign._activeSendingProcess === true) {
                return;
            }
            app.DocuSign._activeSendingProcess = true;
            payload.processInProgress = true;

            const showRecipients = this.shouldShowRecipients();

            if (_.isUndefined(step)) {
                if (showRecipients) {
                    let contextModule = this._getEnvelopeSourceModule();
                    let ctxModelId = this._getEnvelopeSourceModelId();

                    let recipientsListContext = {
                        module: 'DocuSignEnvelopes',
                        contextModule: contextModule,
                        ctxModelId: ctxModelId,
                        isMultiSelect: true,
                    };
                    if (payload.template) {
                        recipientsListContext.templateDetails = payload.template;
                    }
                    app.drawer.open(
                        {
                            layout: 'recipients-list',
                            type: 'recipients-list',
                            context: recipientsListContext
                        },
                        _.bind(function(selected) {
                            if (selected.closeEvent) {
                                app.DocuSign._activeSendingProcess = false;
                                return;
                            }
                            payload.recipients = [];
                            _.each(selected, function(recipient) {
                                payload.recipients.push(recipient);
                            });

                            const nextStep = 'envelopeSetup';
                            this.initiateSend(payload, nextStep);
                        }, this)
                    );

                    return;
                } else {
                    const nextStep = 'envelopeSetup';
                    this.initiateSend(payload, nextStep);

                    return;
                }
            }

            if (step === 'envelopeSetup') {
                app.drawer.open(
                    {
                        layout: 'envelope-setup',
                        type: 'envelope-setup',
                        context: {
                            module: 'DocuSignEnvelopes',
                            payload: payload,
                        }
                    },
                    _.bind(function(envelopeDetails) {
                        if (envelopeDetails.closeEvent) {
                            if (_.isUndefined(envelopeDetails.dsProcessWillContinue)) {
                                app.DocuSign._activeSendingProcess = false;
                            }
                            return;
                        }

                        const step = 'openEnvelope';

                        payload.envelopeName = envelopeDetails.envelopeName;

                        this.initiateSend(payload, step);
                    }, this)
                );

                return;
            }

            if (step === 'openEnvelope' || step === 'openDraft') {
                app.DocuSign._activeSendingProcess = false;

                //make the browser consider the action as user not script made
                const tab = window.open(app.DocuSign._loadPageUrl);

                this.openEnvelope(payload, tab);

                return;
            }

            if (step === 'selectTemplate') {
                app.drawer.open(
                    {
                        layout: 'templates-list',
                        type: 'templates-list',
                        context: {
                            module: 'DocuSignEnvelopes',
                        }
                    },
                    _.bind(function(selected) {
                        if (selected.closeEvent) {
                            app.DocuSign._activeSendingProcess = false;
                            return;
                        }
                        payload.templateSelected = selected;
                        const step = 'getTemplate';
                        this.initiateSend(payload, step);
                    }, this)
                );

                return;
            }

            if (step === 'getTemplate') {
                app.api.call('create', app.api.buildURL('DocuSign/getTemplateDetails'), {
                    template: payload.templateSelected
                }, {
                    success: _.bind(function(template) {
                        payload.template = template;
                        this.initiateSend(payload);
                    }, this),
                });

                return;
            }
        },


        /**
         * Initiate send with composite templates approach
         */
        initiateCompositeSend: function(payload, step) {
            if (_.isUndefined(payload.processInProgress) && app.DocuSign._activeSendingProcess === true) {
                return;
            }
            app.DocuSign._activeSendingProcess = true;
            payload.processInProgress = true;

            const showRecipients = this.shouldShowRecipients();

            if (_.isUndefined(step) && showRecipients) {
                let contextModule = this._getEnvelopeSourceModule();
                let ctxModelId = this._getEnvelopeSourceModelId();

                let recipientsListContext = {
                    module: 'Users',
                    contextModule: contextModule,
                    ctxModelId: ctxModelId,
                };
                if (payload.template) {
                    recipientsListContext.templateDetails = payload.template;
                }
                app.drawer.open(
                    {
                        layout: 'recipients-list-composite',
                        type: 'recipients-list-composite',
                        context: recipientsListContext,
                        isMultiSelect: true
                    },
                    _.bind(function(selected) {
                        if (_.isUndefined(selected) || selected.closeEvent) {
                            if (_.isUndefined(selected.dsProcessWillContinue)) {
                                app.DocuSign._activeSendingProcess = false;
                            }
                            return;
                        }
                        payload.recipients = [];
                        _.each(selected, function(recipient) {
                            payload.recipients.push(recipient);
                        });
                        const nextStep = 'envelopeSetup';
                        payload.composite = true;
                        this.initiateSend(payload, nextStep);
                    }, this)
                );

                return;
            }

            if ((_.isUndefined(step) && !showRecipients) || step === 'envelopeSetup') {
                const nextStep = 'envelopeSetup';
                this.initiateSend(payload, nextStep);

                return;
            }

            if (step === 'selectTemplate') {
                app.drawer.open(
                    {
                        layout: 'templates-list-composite',
                        type: 'templates-list-composite',
                        context: {
                            module: 'DocuSignEnvelopes',
                        }
                    },
                    _.bind(function(selected) {
                        if (_.isUndefined(selected) || selected.closeEvent) {
                            app.DocuSign._activeSendingProcess = false;
                            return;
                        }
                        payload.templateSelected = selected;
                        payload.composite = true;
                        const step = 'getTemplate';
                        this.initiateCompositeSend(payload, step);
                    }, this)
                );

                return;
            }

            if (step === 'getTemplate') {
                app.api.call('create', app.api.buildURL('DocuSign/getTemplateDetails'), {
                    template: payload.templateSelected
                }, {
                    success: _.bind(function(template) {
                        payload.template = template;
                        this.initiateCompositeSend(payload);
                    }, this),
                });

                return;
            }

            const nextStep = 'openEnvelope';
            this.initiateCompositeSend(payload, nextStep);
        },

        /**
         * Opens an envelope in DocuSign.
         *
         * If it's not created yet, creates it first, and then it opens
         *
         * @param {Object} payload
         * @param {Object} tab Tab opened for DocuSign
         */
        openEnvelope: function(payload, tab) {
            app.alert.show('load-tab-for-sending', {
                level: 'process',
                title: app.lang.get('LBL_LOADING')
            });

            app.api.call('create', app.api.buildURL('DocuSign/send'), payload, {
                success: _.bind(this.succesGettingEnvelope, this, tab),
                error: function(error) {
                    app.alert.show('error-loading-tab', {
                        level: 'error',
                        messages: error.message
                    });
                },
                complete: function() {
                    app.alert.dismiss('load-tab-for-sending');
                }
            });
        },

        /**
         * Get recipient selection value
         *
         * @return {boolean}
         */
        isRecipientSelectionEnabled: function() {
            let recipientSelectionIsEnabled = false;
            if (!_.isUndefined(app.config.docusign) && _.isString(app.config.docusign.recipientSelection)) {
                recipientSelectionIsEnabled = app.config.docusign.recipientSelection === 'show';
            }

            return recipientSelectionIsEnabled;
        },

        /**
         * Check if conditions are meet to show recipients
         *
         * @return {boolean}
         */
        shouldShowRecipients: function() {
            const ctxModel = app.controller.context;
            const recordViewContext = ctxModel.get('layout') === 'record';
            const homeModule = ctxModel.get('module') === 'Home';

            const recipientSelectionIsEnabled = this.isRecipientSelectionEnabled();

            const showRecipients = recipientSelectionIsEnabled && recordViewContext && !homeModule;

            return showRecipients;
        },

        /**
         * Success getting envelope to open
         *
         * @param {Object} tab
         * @param {Object} res
         */
        succesGettingEnvelope: function(tab, res) {
            if ((res.status && res.status === 'error') || res.envelopeStatus === 'deleted') {
                var minifiedErrorMessage = res.message.toLowerCase();
                if (minifiedErrorMessage.includes('envelope status in docusign is now')) {
                    if (res.envelopeStatus === 'deleted') {
                        this.confirmDelete(res);
                    } else {
                        this.confirmUpdate(res);
                    }
                } else if (minifiedErrorMessage !== 'cancel' && !_.isEmpty(res.message)) {
                    app.alert.show('ds_error', {
                        level: 'error',
                        messages: res.message,
                        autoClose: false
                    });
                }
                tab.close();
                return;
            }

            tab.location.href = res.url;

            this.listenForTabClosing();
        },

        /**
         * Creates a confirmation alert and ask for permission to delete the draft
         *
         * @param {Object} data
         */
        confirmDelete: function(data) {
            app.alert.show('draft-does-not-exist', {
                level: 'confirmation',
                messages: app.lang.get('LBL_DRAFT_DELETED_CONFIRMATION', 'DocuSignEnvelopes'),
                autoClose: false,
                onConfirm: function() {
                    let removeCallbacks = this.getConfirmationCallbacks({
                        successMessage: app.lang.get('LBL_DRAFT_DELETE_SUCCESS', 'DocuSignEnvelopes'),
                        errorMessage: app.lang.get('LBL_DRAFT_DELETE_ERROR', 'DocuSignEnvelopes')
                    });
                    app.api.call(
                        'create',
                        app.api.buildURL('DocuSign/removeEnvelope'), {
                            envelopeId: data.envelopeId
                        }, removeCallbacks
                    );
                    app.alert.show('envelope-loading', {
                        level: 'process',
                        title: app.lang.get('LBL_LOADING')
                    });
                },
            });
        },

        /**
         * Confirm envelope status update
         *
         * @param {Object} data
         */
        confirmUpdate: function(data) {
            app.alert.show('draft-does-not-exist', {
                level: 'confirmation',
                messages: app.lang.get('LBL_DRAFT_CHANGED_CONFIRM', 'DocuSignEnvelopes', {status: data.status}),
                autoClose: false,
                onConfirm: function() {
                    let updateCallbacks = this.getConfirmationCallbacks({
                        successMessage: app.lang.get('LBL_DRAFT_CHANGED_SUCCESS', 'DocuSignEnvelopes'),
                        errorMessage: app.lang.get('LBL_DRAFT_CHANGED_ERROR', 'DocuSignEnvelopes')
                    });
                    app.api.call(
                        'create',
                        app.api.buildURL('DocuSign/updateEnvelope'), {
                            envelopeId: data.envelopeId
                        }, updateCallbacks
                    );

                    app.alert.show('envelope-loading', {
                        level: 'process',
                        title: app.lang.get('LBL_LOADING')
                    });
                },
            });
        },

        /**
         * Get confirmation callbacks
         *
         * @param {Object} options
         * @return {Object}
         */
        getConfirmationCallbacks: function(options) {
            return {
                success: function(res) {
                    if (res) {
                        app.alert.show('succes-change-envelope', {
                            level: 'success',
                            messages: options.successMessage,
                            autoClose: true
                        });
                        app.events.trigger('docusign:reload');
                    } else {
                        app.alert.show('error-change-envelope', {
                            level: 'error',
                            messages: options.errorMessage,
                            autoClose: true,
                            autoCloseDelay: '10000'
                        });
                    }
                },
                error: function(error) {
                    app.alert.show('error-change-envelope', {
                        level: 'error',
                        messages: error.message
                    });
                },
                complete: function() {
                    app.alert.dismiss('envelope-loading');
                }
            };
        },

        /**
         * Listen for tab closing
         */
        listenForTabClosing: function() {
            $(window).on('storage.docusignAction', function(e) {
                if (e.originalEvent.key !== 'docusignAction') {
                    return;
                }
                const action = e.originalEvent.newValue;
                if (!action) {
                    return;
                }

                $(window).off('storage.docusignAction');

                app.events.trigger('docusign:send:finished');

                if (app.controller.context.get('module') === 'pmse_Inbox' &&
                    app.controller.layout.name === 'show-case') {
                    return;
                }

                app.events.trigger('docusign:reload');
            });
        },

        /**
         * Get envelope source module
         *
         * @return {string}
         */
        _getEnvelopeSourceModule: function() {
            var module = app.controller.context.get('module');

            if (module === 'pmse_Inbox' && app.controller.layout.name === 'show-case') {
                try {
                    var sourceModel = app.controller.layout._components[0]
                        .getComponent('sidebar')
                        .getComponent('main-pane')
                        .model;

                    return sourceModel.get('_module');
                } catch (showCaseError) {
                    app.log.error(`_getEnvelopeSourceModule. show-case layout error: ${showCaseError}`);
                }
            }

            return module;
        },

        /**
         * Get envelope source id
         *
         * @return {string}
         */
        _getEnvelopeSourceModelId: function() {
            var module = app.controller.context.get('module');
            var modelId = app.controller.context.get('modelId');

            if (module === 'pmse_Inbox' && app.controller.layout.name === 'show-case') {
                try {
                    var sourceModel = app.controller.layout._components[0]
                        .getComponent('sidebar')
                        .getComponent('main-pane')
                        .model;

                    return sourceModel.get('id');
                } catch (showCaseError) {
                    app.log.error(`_getEnvelopeSourceModelId. show-case layout error: ${showCaseError}`);
                }
            }

            return modelId;
        },

        /**
         * Displays error message that not all recipients selected have a role set
         */
        _showRolesNotSetAlert: function() {
            const msg = app.lang.get('LBL_RECIPIENT_ROLE_MISSING_ERROR', 'DocuSignEnvelopes');

            app.alert.show('recipient-role-not-set', {
                level: 'error',
                messages: msg,
                autoClose: true
            });
        },
    };

    app.DocuSign = app.DocuSign || {};
    app.DocuSign.utils = utils;

    app.events.on('app:init', function() {
        app.DocuSign._loadPageUrl = app.api.buildURL('DocuSign', 'loadPage');

        app.events.on('docusign:send:initiate', app.DocuSign.utils.initiateSend, app.DocuSign.utils);
        app.events.on('docusign:compositeSend:initiate', app.DocuSign.utils.initiateCompositeSend, app.DocuSign.utils);

        app.events.on('app:view:change', function(layoutType, contextOptions) {
            const docusignSendingLayouts = [
                'recipients-list',
                'recipients-list-composite',
                'templates-list',
                'templates-list-composite',
                'envelope-setup'
            ];

            //reset flag when user changes the page
            if (!_.contains(docusignSendingLayouts, layoutType) && _.isUndefined(contextOptions.drawer)) {
                app.DocuSign._activeSendingProcess = false;
                return;
            }
        });
    });
})(SUGAR.App);

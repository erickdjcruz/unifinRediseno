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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * Compose Email Action
         *
         * @class Core.Actions.ComposeEmailAction
         * @alias SUGAR.App.Actions.ComposeEmailAction
         *
         * @param {Object} def Action Definition
         */
        function ComposeEmail(def) {
            this.def = def;
        }

        /**
         * Merge selected email template to current record and feed it to either
         * the Compose Email drawer, or the External Email client.
         *
         * @param {Data.Bean} opts.recordModel Current record bean
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        ComposeEmail.prototype.run = function(opts, currentExecution) {
            const recordModel = opts.recordModel;
            const def = this.def;
            const properties = def.properties;

            const pmseTemplatePath = 'actionButton/evaluateBPMEmailTemplate';
            const emailTemplatePath = 'actionButton/evaluateEmailTemplate';

            const templateId = properties.id;
            const usePmseTemplate = app.utils.isTruthy(properties.pmse);

            const templatePath = usePmseTemplate ? pmseTemplatePath : emailTemplatePath;
            const requestType = 'create';

            const callbacks = {
                success: _.bind(function loadEmailDrawer(emailData) {
                    if (this.getEmailClientType() === 'sugar') {
                        this.composeEmailBySugarClient(recordModel, templateId, emailData, currentExecution);
                    } else {
                        this.composeEmailByExternalClient(recordModel, emailData, currentExecution);
                    }
                }, this)
            };

            const requestMeta = this.createRequestParamForSugarClient(recordModel, properties);
            const apiUrl = app.api.buildURL(templatePath, requestType, requestMeta, {});

            app.api.call(requestType, apiUrl, requestMeta, null, callbacks);
        };

        /**
         * Retrieves configured email client type.
         *
         * @return {string}
         */
        ComposeEmail.prototype.getEmailClientType = function() {
            const emailType = app.user.get('preferences').email_client_preference.type;

            return emailType;
        };

        /**
         * Open external email client with prefilled template data
         *
         * @param {Data.Bean} recordModel Current record bean
         * @param {Array} emailData.emailTo Array of recipient email addresses
         * @param {string} emailData.subject Email subject
         * @param {string} emailData.body Email body
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         */
        ComposeEmail.prototype.composeEmailByExternalClient = function(recordModel, emailData, currentExecution) {
            var emailBody = emailData.body.replace(/\n/g, '\r\n')
                .replace(/<p>/g, '\r\n')
                .replace(/(<([^>]+)>)/ig, '');

            var emailToAddresses = '';

            if (emailData.emailTo) {
                emailToAddresses = _.chain(emailData.emailTo).pluck('email_address').value().join(';');
            }

            if (emailToAddresses === '') {
                emailToAddresses = _.chain(recordModel.get('email'))
                    .pluck('email_address')
                    .value()
                    .join(';');
            }

            currentExecution.nextAction();

            this.mailto('mailto:' + emailToAddresses +
                '?subject=' + encodeURIComponent(emailData.subject) +
                '&body=' + encodeURIComponent(emailBody));
        };

        /**
         * Navigate current tab to given location, used to force the
         * mailto: handler to open a compose window
         *
         * @param {string} location mailto formatted string
         */
        ComposeEmail.prototype.mailto = function(location) {
            window.location.href = location;
        };

        /**
         * Open Compose Email drawer with prefilled template data
         *
         * @param {Data.Bean} recordModel Current record bean
         * @param {string} templateId Standard/PMSE Email Template Id
         * @param {Array} emailData.emailTo Array of recipient email addresses
         * @param {string} emailData.subject Email subject
         * @param {string} emailData.body Email body
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         */
        ComposeEmail.prototype.composeEmailBySugarClient = function(
            recordModel,
            templateId,
            emailData,
            currentExecution
            ) {
            let emailModel = this.createEmailForSugarClient(recordModel, templateId, emailData);

            app.drawer.open({
                layout: 'compose-email',
                context: {
                    create: true,
                    module: 'Emails',
                    model: emailModel
                }
            });

            currentExecution.nextAction();
        };

        /**
         * Create email bean model
         *
         * @param {Object} recordModel Current record bean
         * @param {string} templateId Standard/PMSE Email Template Id
         * @param {Array} emailData.emailTo Array of recipient email addresses
         * @param {string} emailData.subject Email subject
         * @param {string} emailData.body Email body
         *
         * @return {Model.Datas.Base.EmailsModel}
         */
        ComposeEmail.prototype.createEmailForSugarClient = function(recordModel, templateId, emailData) {
            let emailModel = app.data.createBean('Emails');

            emailModel.set({
                related: recordModel,
                parent: recordModel,
                parent_id: recordModel.get('id'),
                parent_type: recordModel.module,
                parent_name: recordModel.get('name'),
                mustLinkRecord: true,
                emailTemplateId: templateId,
                name: emailData.subject,
                description: emailData.description,
                description_html: emailData.body
            });

            if (emailData.emailTo !== false && !_.isEmpty(emailData.emailTo)) {
                const toCollection = this.createEmailToCollection(emailModel, emailData);

                emailModel.set('to_collection', toCollection);
            }

            return emailModel;
        };

        /**
         * Create recipients collection
         *
         * @param {Model.Datas.Base.EmailsModel} emailModel
         * @param {Array} emailData.emailTo Array of recipient email addresses
         * @param {string} emailData.subject Email subject
         * @param {string} emailData.body Email body
         *
         * @return {Data.MixedBeanCollection}
         */
        ComposeEmail.prototype.createEmailToCollection = function(emailModel, emailData) {
            const emailAddresses = emailData.emailTo;
            let emailToCollection = app.data.createMixedBeanCollection();
            let linkedCollection = app.data.createBeanCollection();
            linkedCollection._create = [];

            _.each(emailAddresses, function getEmail(emailMeta) {
                const emailParticipantMeta = {
                    _link: 'to',
                    email_address: emailMeta.email_address,
                    email_address_id: emailMeta.email_address_id,
                    invalid_email: false,
                    deleted: false
                };

                let emailParticipant = app.data.createBean('EmailParticipants', emailParticipantMeta);

                emailToCollection.add(emailParticipant);

                linkedCollection._create.push(emailParticipant);
            });

            linkedCollection.link = {
                name: 'to',
                bean: emailModel
            };

            emailToCollection._linkedCollections = {
                'to': linkedCollection
            };

            return emailToCollection;
        };

        /**
         * Craft parameters for template retrieval API request
         *
         * @param {Data.Bean} recordModel
         * @param {string} properties.emailToFormula Sugar formula to calculate email recipients
         * @param {string} properties.id PMSE/Standard Email Template Id
         *
         * @return {Object}
         */
        ComposeEmail.prototype.createRequestParamForSugarClient = function(recordModel, properties) {
            let requestParam = {
                emailToData: {
                    formulaElement: properties.emailToFormula,
                    validFormula: true,
                    validationMessage: ''
                },
                emailToField: true,
                targetRecordId: recordModel.get('id'),
                targetTemplateId: properties.id,
                targetRecordModule: recordModel.module
            };

            return requestParam;
        };

        app.actions = app.actions || {};
        app.actions = _.extend(app.actions, {
            ComposeEmail: ComposeEmail
        });
    });
})(SUGAR.App);

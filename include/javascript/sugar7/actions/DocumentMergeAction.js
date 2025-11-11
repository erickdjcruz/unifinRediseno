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
         * Document Merge Action
         *
         * @class Core.Actions.DocumentMergeAction
         * @alias SUGAR.App.Actions.DocumentMergeAction
         *
         * @param {Object} def Action Definition
         */
        function DocumentMerge(def) {
            this.def = def;
        }

        /**
         * Merge a document template
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        DocumentMerge.prototype.run = function(opts, currentExecution) {
            const actionDef = this.def;

            const templateId = actionDef.properties.id;
            const templateName = actionDef.properties.name;
            const isPdf = actionDef.properties.pdf;

            this.mergeDocument(opts, templateId, templateName, isPdf, currentExecution);
        };

        /**
         * Trigger the document merge event
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {string} templateId Template Id
         * @param {string} templateName Template Name
         * @param {bool} isPdf Covert the document to pdf
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         */
        DocumentMerge.prototype.mergeDocument = function(opts, templateId, templateName, isPdf, currentExecution) {
            const recordModel = opts.recordModel;
            const currentRecordId = recordModel.get('id');
            const currentRecordModule = recordModel.module;

            // make sure the merge widget layout is initialized
            app.events.trigger('document_merge:show_widget');

            app.events.trigger('document:merge', {
                currentRecordId,
                currentRecordModule,
                templateId,
                templateName,
                isPdf,
            });

            app.alert.show('wdocs_loading', {
                level: 'info',
                messages: app.lang.getModString('LBL_DOCUMENT_MERGIN_ACTION_PROCESS', 'DocumentMerges'),
                autoClose: true,
            });

            currentExecution.nextAction();
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            DocumentMerge: DocumentMerge,
        });
    });
})(SUGAR.App);

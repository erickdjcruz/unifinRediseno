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
 * @class View.Views.elseifBase.DRI_Workflow_Templates.TemplateImportView
 * @alias SUGAR.App.view.views.DRI_Workflow_TemplatesTemplateImportView
 * @extends View.Views.Base.BaseView
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(this.context, 'template:import:finish', this.importTemplate);
    },

    /**
     * @inheritdoc
     *
     * Sets up the file field to edit mode
     *
     * @param {View.Field} field
     * @private
     */
    _renderField: function(field) {
        this._super('_renderField', [field]);
        if (field.name === 'template_import') {
            field.setMode('edit');
        }
    },

    /**
     * Import the Smart Guide Template File (.json)
     */
    importTemplate: function() {
        let alertMessage = '';
        let projectFile = this.$('[name=template_import]');

        // Check if a file was chosen
        if (_.isEmpty(projectFile.val())) {
            alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING', this.module);
            this.showAlert('error_validation_process', 'error', alertMessage, false);
        } else {
            alertMessage = app.lang.get('LBL_CHECKING_IMPORT_UPLOAD', this.module);
            this.showAlert('upload', 'process', alertMessage, false);

            let callbacks = {
                success: _.bind(this.importTemplateSuccess, this),
                error: _.bind(this.uploadFileError, this),
            };

            this.model.uploadFile('check-template-import', projectFile, callbacks, {
                deleteIfFails: true,
                htmlJsonFormat: true,
            });
        }
    },

    /**
     * Handle error callback for uploadFile
     *
     * @param {Object} error
     */
    uploadFileError: function(error) {
        app.alert.dismiss('upload');
        this.showAlert('template-import-saved', 'error', error.error_message || error.message, false);
    },

    /**
     * Handle success callback for importTemplate
     *
     * @param {Object} data
     */
    importTemplateSuccess: function(data) {
        app.alert.dismiss('upload');

        if (data.duplicate) {
            let alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR',
                this.module, data.record);
            this.showAlert('template-import-duplicate', 'error', alertMessage, true, 20000);

        } else if (data.update && !_.isEmpty(data.record.id)) {
            app.alert.show('template-import-confirm-update', {
                level: 'confirmation',
                messages: app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM', this.module, data.record),
                onConfirm: _.bind(this.doImport, this),
            });
        } else {
            this.doImport();
        }
    },

    /**
     * Import the Smart Guide Template File (.json)
     */
    doImport: function() {
        let alertMessage = '';
        let projectFile = this.$('[name=template_import]');

        // Check if a file was chosen
        if (_.isEmpty(projectFile.val())) {
            alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING', this.module);
            this.showAlert('error_validation_process', 'error', alertMessage, false);
        } else {
            alertMessage = app.lang.get('LBL_IMPORTING_TEMPLATE', this.module);
            this.showAlert('upload', 'process', alertMessage, false);

            let callbacks = {
                success: _.bind(this.doImportSuccess, this),
                error: _.bind(this.uploadFileError, this),
            };
            this.model.uploadFile('template-import', projectFile, callbacks, {
                deleteIfFails: true,
                htmlJsonFormat: true,
            });
        }
    },

    /**
     * Handle success callback for doImport
     *
     * @param {Object} data
     */
    doImportSuccess: function(data) {
        let alertMessage = '';

        app.alert.dismiss('upload');
        if (data.record.deleted === 1) {
            alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED',
                this.module, data.record);
            this.showAlert('template-import-not-saved', 'warning', alertMessage, true, 20000);

            let route = app.router.buildRoute(this.module);
            app.router.navigate(route, {trigger: true});
        } else {
            let route = app.router.buildRoute(this.module, data.record.id);
            app.router.navigate(route, {trigger: true});

            if (data.record.new_with_id) {
                alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS',
                    this.module, data.record);
            } else {
                alertMessage = app.lang.get('LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS',
                    this.module, data.record);
            }

            this.showAlert('template-import-saved', 'success', alertMessage, true, 20000);
        }
    },

    /**
     * This fnction shows alert to the user
     * @param {string} name
     * @param {string} level
     * @param {string} message
     * @param {boolean} autoClose
     * @param {number} autoCloseDelay
     */
    showAlert: function(name, level, message, autoClose, autoCloseDelay) {
        let def = {
            level: level,
            autoClose: autoClose,
        };

        if (level === 'process') {
            def.title = message;
        } else {
            def.messages = message;
        }

        if (app.utils.isTruthy(autoClose)) {
            def.autoCloseDelay = autoCloseDelay;
        }
        app.alert.show(name, def);
    },
});

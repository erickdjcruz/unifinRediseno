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
 * @class View.Views.Base.VisualPipeline.ConfigHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseVisualPipelineConfigHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    plugins: ['ErrorDecoration'],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._viewAlerts = [];

        this.moduleLangObj = {
            // using "Tile View" for config title
            module: app.lang.get('LBL_PIPELINE_VIEW_NAME', this.module)
        };
    },

    /**
     * Displays alert message for invalid models
     */
    showInvalidModel: function() {
        var self = this;
        if (!this instanceof app.view.View) {
            app.logger.error('This method should be invoked by Function.prototype.call(), passing in as ' +
                'argument an instance of this view.');
            return;
        }

        var errorMsg = app.lang.get('LBL_PIPELINE_ERR_VALIDATION_FAILED', self.module);
        _.each(this.validatedModels, function(model) {
            if (!model.isValid) {
                errorMsg += '<li>' + model.moduleName + '</li>';
            }
        });

        var name = 'invalid-data';
        self._viewAlerts.push(name);
        app.alert.show(name, {
            level: 'error',
            messages: errorMsg
        });
    },

    /**
     * @inheritdoc
     */
    cancelConfig: function() {
        if (this.triggerBefore('cancel')) {
            if (app.drawer.count()) {
                app.drawer.close(this.context, this.context.get('model'));
            }
            app.router.navigate('#Administration', {trigger: true});
        }
    },

    /**
     * Process all the models of the collection and prepares the context
     * bean for save action
     */
    _setupSaveConfig: function() {
        var ctxModel = this.context.get('model');
        var enabledModules = ctxModel.get('enabled_modules');
        var tableHeader = {};
        var tileHeader = {};
        var tileBodyFields = {};
        var recordsPerColumn = {};
        var hiddenValues = {};
        var availableColumns = {};
        let showColumnCount = {};
        let showColumnTotal = {};
        let totalField = {};

        _.each(this.collection.models, function(model) {
            var moduleName = model.get('enabled_module');
            tableHeader[moduleName] = model.get('table_header');
            tileHeader[moduleName] = model.get('tile_header');
            tileBodyFields[moduleName] = model.get('tile_body_fields');
            recordsPerColumn[moduleName] = model.get('records_per_column');
            hiddenValues[moduleName] = model.get('hidden_values');
            showColumnCount[moduleName] = model.get('show_column_count');
            showColumnTotal[moduleName] = model.get('show_column_total');
            totalField[moduleName] = model.get('total_field');
            availableColumns[moduleName] = model.get('available_columns_edited') ||
                model.get('available_columns');
        }, this);

        ctxModel.set({
            is_setup: true,
            enabled_modules: enabledModules,
            table_header: tableHeader,
            tile_header: tileHeader,
            tile_body_fields: tileBodyFields,
            records_per_column: recordsPerColumn,
            hidden_values: hiddenValues,
            available_columns: availableColumns,
            show_column_count: showColumnCount,
            show_column_total: showColumnTotal,
            total_field: totalField
        }, {silent: true});
    },

    /**
     * Calls the context model save and saves the config model in case
     * the default model save needs to be overwritten
     *
     * @protected
     */
    _saveConfig: function() {
        this.validatedModels = [];
        this.getField('save_button').setDisabled(true);

        if (this.collection.models.length === 0) {
            this._setupSaveConfig();
            this._super('_saveConfig');
        } else {
            async.waterfall([
                _.bind(this.validateCollection, this)
            ], _.bind(function(result) {
                this.validatedModels.push(result);

                // doValidate() has finished on all models.
                if (this.collection.models.length === this.validatedModels.length) {

                    var found = _.find(this.validatedModels, function(details) {
                        return details.isValid === false;
                    });

                    if (found) {
                        this.showInvalidModel();
                        this.getField('save_button').setDisabled(false);
                    } else {
                        this._setupSaveConfig();
                        this._super('_saveConfig');
                    }
                }

            }, this));
        }
    },

    /**
     * Validates all the models in the collection using the validation tasks
     */
    validateCollection: function(callback) {
        var fieldsToValidate = {};
        var allFields = this.getFields(this.module, this.model);

        for (var fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }

        // Clear errors from any previous validation runs first
        this.clearValidationErrors(this.getFieldNames());

        _.each(this.collection.models, function(model) {
            model.doValidate(fieldsToValidate, function(isValid) {
                var moduleName = app.lang.getModuleName(model.get('enabled_module'), {plural: true});
                callback({modelId: model.id, isValid: isValid, moduleName: moduleName});
            });
        }, this);
    }
})

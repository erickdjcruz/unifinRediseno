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
 * @class View.Layouts.Base.VisualPipelineConfigDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseVisualPipelineConfigDrawerLayout
 * @extends View.Layouts.Base.ConfigDrawerLayout
 */
({
    extendsFrom: 'BaseConfigDrawerLayout',

    plugins: ['ErrorDecoration'],

    fieldsAllowedInTileBody: 5, // Use a number <= than 0 to disable the 'Number of fields allowed in a tile' check.

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setAllowedModules();
        this.moduleLangObj = {
            // using "Tile View" for error messages
            module: app.lang.get('LBL_PIPELINE_VIEW_NAME', this.module)
        };
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('pipeline:config:model:add', this.addModelToCollection, this);
        this.context.on('pipeline:config:model:remove', this.removeModelFromCollection, this);
    },

    /**
     * Returns the list of modules the user has access to
     * and are supported.
     *
     * @return {Array} The list of module names.
     */
    getAvailableModules: function() {
        var selectedModules = this.model.get('enabled_modules');

        return _.filter(selectedModules, function(module) {
            return !_.isEmpty(app.metadata.getModule(module));
        });
    },

    /**
     * Sets the list of modules the user has no access to on the model.
     */
    setNotAvailableModules: function() {
        let modules = app.metadata.getModuleNames({
            filter: 'display_tab',
            access: 'read'
        });
        let notAvailableModules = _.reject(modules, function(module) {
            let moduleDetail = app.metadata.getModule(module);
            return !_.isEmpty(moduleDetail) &&
                !moduleDetail.isPipelineExcluded &&
                this._checkDropdownField(moduleDetail.fields);
        }, this);
        this.model.set('notAvailableModules', notAvailableModules);

    },

    /**
     * Sets up the models for each of the enabled modules from the configs
     */
    loadData: function(options) {
        if (!this.checkAccess()) {
            this.blockModule();
            return;
        }

        var availableModules = this.getAvailableModules();
        var tableHeaders = this.model.get('table_header');
        var tileHeaders = this.model.get('tile_header');
        var tileBodyFields = this.model.get('tile_body_fields');
        var recordsPerColumn = this.model.get('records_per_column');
        var hiddenValues = this.model.get('hidden_values');
        var availableColumns = this.model.get('available_columns');
        let showColumnCount = this.model.get('show_column_count') || {};
        let showColumnTotal = this.model.get('show_column_total') || {};
        let totalField = this.model.get('total_field') || {};

        if (!(recordsPerColumn instanceof Object)) {
            recordsPerColumn = JSON.parse(recordsPerColumn);
        }

        _.each(availableModules, function(moduleName) {
            var data = {
                enabled: true,
                enabled_module: moduleName,
                table_header: tableHeaders[moduleName],
                tile_header: tileHeaders[moduleName],
                tile_body_fields: tileBodyFields[moduleName],
                records_per_column: recordsPerColumn[moduleName],
                hidden_values: hiddenValues[moduleName],
                available_columns: availableColumns[moduleName],
                show_column_count: showColumnCount[moduleName],
                show_column_total: showColumnTotal[moduleName],
                total_field: totalField[moduleName],
            };
            this.addModelToCollection(moduleName, data);
        }, this);
        this.setNotAvailableModules();
        this.setActiveTabIndex(0);
    },

    /**
     * Checks VisualPipeline ACLs to see if the User is a system admin
     * or if the user has a developer role for the VisualPipeline module
     *
     * @inheritdoc
     */
    _checkModuleAccess: function() {
        var acls = app.user.getAcls().VisualPipeline;
        var isSysAdmin = (app.user.get('type') == 'admin');
        var isDev = (!_.has(acls, 'developer'));

        return (isSysAdmin || isDev);
    },

    /**
     * Sets the allowed modules that the admin are allowed to configure
     */
    setAllowedModules: function() {
        var moduleDetails = {};
        let allowedModules = app.metadata.getModuleNames({
            filter: 'display_tab',
            access: 'read'
        });

        var modules = {};

        _.each(allowedModules, function(module) {
            moduleDetails = app.metadata.getModule(module);
            if (moduleDetails &&
                !moduleDetails.isBwcEnabled &&
                !_.isEmpty(moduleDetails.fields) &&
                !moduleDetails.isPipelineExcluded &&
                this._checkDropdownField(moduleDetails.fields)) {
                modules[module] = app.lang.getAppListStrings('moduleList')[module];
            }
        }, this);

        this.context.set('allowedModules', modules);
    },

    /**
     * Function to check if the module has any dropdown type fields
     *
     * @param {Object} fields
     * @return {boolean}
     */
    _checkDropdownField: function(fields) {
        for (const property in fields) {
            if (fields[property].type === 'enum') {
                return true;
            }
        }
        return false;
    },

    /**
     * Sets the active tab
     */
    setActiveTabIndex: function(index) {
        if (this.collection.length >= 1 || !_.isUndefined(index)) {
            var activeIndex = !_.isUndefined(index) ? index : this.collection.length - 1;
            this.context.set('activeTabIndex', activeIndex);
        }
    },

    /**
     * Removes a model from the collection and triggers events
     * to re-render the components
     * @param {string} module Module Name
     */
    removeModelFromCollection: function(module) {
        var modelToDelete = _.find(this.collection.models, function(model) {
            return model.get('enabled_module') === module;
        });

        if (!_.isEmpty(modelToDelete)) {
            this.collection.remove(modelToDelete);
            this.setActiveTabIndex();
        }
    },

    /**
     * Adds a model from the collection and triggers events
     * to re-render the components
     * @param {string} module Module Name
     * @param {Object} data Model data to add to the collection
     */
    addModelToCollection: function(module, data) {
        var data = data || {};
        var existingBean = _.find(this.collection.models, function(model) {
            if (_.contains(_.keys(this.context.get('allowedModules')), module)) {
                return model.get('enabled_module') === module;
            }
        }, this);

        if (_.isEmpty(existingBean)) {
            var bean = app.data.createBean(this.module, {
                enabled: data.enabled || true,
                enabled_module: data.module || module,
                table_header: data.table_header || '',
                tile_header: data.tile_header || '',
                tile_body_fields: data.tile_body_fields || '',
                records_per_column: data.records_per_column || '',
                hidden_values: data.hidden_values || '',
                available_columns: data.available_columns || '',
                show_column_count: data.show_column_count || '',
                show_column_total: data.show_column_total || '',
                total_field: data.show_column_total ? (data.total_field || '') : '',
            });

            this.getModuleFields(bean);
            this.addValidationTasks(bean);
            this.collection.add(bean);
        }

        this.setActiveTabIndex();

        this.context.trigger('pipeline:config:set-active-module', module);
    },

    /**
     * Set the fields for the module on the bean
     *
     * @param {Object} bean ta Model data to add to the collection
     */
    getModuleFields: function(bean) {
        var module = bean.get('enabled_module');
        var content = {};
        var dropdownFields = {};
        var allFields = {};
        let allTotalableFields = {};
        var studioFields = [];
        var metaFields = app.metadata.getModule(module) ? app.metadata.getModule(module).fields : {};
        const totalableFields = ['int', 'float', 'decimal', 'currency'];

        _.each(metaFields, function(metaField) {
            if (this.isValidStudioField(metaField)) {
                studioFields.push(metaField);
            }
        }, this);

        _.each(metaFields, function(field) {
            if (field.type === 'enum' && field.source !== 'non-db' &&
                app.acl.hasAccess('read', module, null, field.name)) {
                dropdownFields[field.name] = app.lang.get(field.label || field.vname, module);
            }
        }, this);

        _.each(studioFields, function(field) {
            if (_.isObject(field) && app.acl.hasAccess('read', module, null, field.name)) {
                var label = app.lang.get(field.label || field.vname, module);
                if (!_.isEmpty(label)) {
                    allFields[field.name] = label;
                    if (totalableFields.includes(field.type)) {
                        allTotalableFields[field.name] = label;
                    }
                }
            }
        }, this);

        content.dropdownFields = dropdownFields;
        content.fields = allFields;
        content.allTotalableFields = allTotalableFields;

        bean.set('tabContent', content);
    },

    /**
     * Checks if a metadata field is valid to be shown in Studio layout editors
     *
     * @param {Object} metaField metadata field to be checked
     * @return {boolean} true if the field can be shown in studio layout
     */
    isValidStudioField: function(metaField) {
        if (!_.isUndefined(metaField.studio)) {
            if (_.isObject(metaField.studio)) {
                if (!_.isUndefined(metaField.studio.recordview)) {
                    return (metaField.studio.recordview !== 'hidden' && metaField.studio.recordview !== false);
                }

                if (!_.isUndefined(metaField.studio.visible)) {
                    return metaField.studio.visible;
                }
            } else {
                return (metaField.studio !== 'false' && metaField.studio !== false && metaField.studio !== 'hidden');
            }
        }

        // JSON fields are not supposed to be modified in studio
        if (!_.isUndefined(metaField.type) && metaField.type === 'json') {
            return false;
        }

        // remove id type fields except those with names as *_name or email1
        return ((!_.isUndefined(metaField.name) &&
            (metaField.name === 'email1' || metaField.name.slice(-5) === '_name')) ||
            (
                (!_.isUndefined(metaField.type) && metaField.type !== 'id') &&  metaField.type !== 'parent_type' &&
                (_.isEmpty(metaField.dbType) || metaField.dbType !== 'id') &&
                (_.isEmpty(metaField.source) || metaField.source === 'db' || metaField.source === 'custom_fields') &&
                (!_.isUndefined(metaField.name) && metaField.name !== 'deleted')
            ));
    },

    /**
     * Adds validation tasks to the fields in the layout for the enabled modules
     */
    addValidationTasks: function(bean) {
        if (bean !== undefined) {
            bean.addValidationTask('check_table_header', _.bind(this._validateTableHeader, bean));
            bean.addValidationTask('check_tile_header', _.bind(this._validateTileOptionsHeader, bean));
            bean.addValidationTask('check_tile_body_fields', _.bind(this._validateTileOptionsBody, bean));
            bean.addValidationTask('check_records_displayed', _.bind(this._validateRecordsDisplayed, bean));
            bean.addValidationTask('check_total_field', _.bind(this._validateTileOptionsTotal, bean));

            if (this.fieldsAllowedInTileBody > 0) {
                bean.addValidationTask(
                    'check_nb_fields_in_tile_body_fields',
                    _.bind(this._validateNbFieldsInTileOptions, {
                        model: bean,
                        nbFieldsAllowed: this.fieldsAllowedInTileBody
                    })
                );
            }

        } else {
            _.each(this.collection.models, function(model) {
                model.addValidationTask('check_table_header', _.bind(this._validateTableHeader, model));
                model.addValidationTask('check_tile_header', _.bind(this._validateTileOptionsHeader, model));
                model.addValidationTask('check_tile_body_fields', _.bind(this._validateTileOptionsBody, model));
                model.addValidationTask('check_records_displayed', _.bind(this._validateRecordsDisplayed, model));
                model.addValidationTask('check_total_field', _.bind(this._validateTileOptionsTotal, model));

                if (this.fieldsAllowedInTileBody > 0) {
                    model.addValidationTask(
                        'check_nb_fields_in_tile_body_fields',
                        _.bind(this._validateNbFieldsInTileOptions, {
                            model: model,
                            nbFieldsAllowed: this.fieldsAllowedInTileBody
                        })
                    );
                }

            }, this);
        }
    },

    /**
     * Validates table header values for the enabled module
     *
     * @protected
     */
    _validateTableHeader: function(fields, errors, callback) {
        if (_.isEmpty(this.get('table_header'))) {
            errors.table_header = errors.table_header || {};
            errors.table_header.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates Tile Options header values for the enabled module
     *
     * @protected
     */
    _validateTileOptionsHeader: function(fields, errors, callback) {
        if (_.isEmpty(this.get('tile_header'))) {
            errors.tile_header = errors.tile_header || {};
            errors.tile_header.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates Tile Options body values for the enabled module
     *
     * @protected
     */
    _validateTileOptionsBody: function(fields, errors, callback) {
        if (_.isEmpty(this.get('tile_body_fields'))) {
            errors.tile_body_fields = errors.tile_body_fields || {};
            errors.tile_body_fields.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates number of fields in the tile options for the enabled module
     *
     * @protected
     */
    _validateNbFieldsInTileOptions: function(fields, errors, callback) {
        var nbFields = this.model.get('tile_body_fields').length;
        if (nbFields > this.nbFieldsAllowed) {
            errors.tile_body_fields = errors.tile_body_fields || {};
            errors.tile_body_fields.tooManyFields = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates records per column values for the enabled module
     *
     * @protected
     */
    _validateRecordsDisplayed: function(fields, errors, callback) {
        if (_.isEmpty(this.get('records_per_column'))) {
            errors.records_per_column = errors.records_per_column || {};
            errors.records_per_column.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * Validates Tile Options total value for the enabled module
     * @param {Object} fields
     * @param {Object} errors
     * @param {Function} callback
     *
     * @protected
     */
    _validateTileOptionsTotal: function(fields, errors, callback) {
        if (this.get('show_column_total') && !this.get('total_field')) {
            errors.total_field = errors.total_field || {};
            errors.total_field.required = true;
        }

        callback(null, fields, errors);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.context.off('pipeline:config:model:add', null, this);
        this.context.off('pipeline:config:model:remove', null, this);

        this._super('_dispose');
    }
})

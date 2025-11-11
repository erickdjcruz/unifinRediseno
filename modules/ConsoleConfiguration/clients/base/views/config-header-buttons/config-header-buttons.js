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
 * @class View.Views.Base.ConsoleConfiguration.ConfigHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseConsoleConfigurationConfigHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    /**
     * The labels to be created when saving console configuration
     */
    labelList: {},

    /**
     * The column definitions to be saved when saving console configuration
     */
    selectedFieldList: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._viewAlerts = [];

        this.moduleLangObj = {
            // using "Console Configuration" for config title
            module: app.lang.get('LBL_CONSOLE_CONFIG_TITLE', this.module)
        };
    },

    /**
     * Displays alert message for invalid models
     */
    showInvalidModel: function() {
        if (!this instanceof app.view.View) {
            app.logger.error('This method should be invoked by Function.prototype.call(), passing in as ' +
                'argument an instance of this view.');
            return;
        }
        var name = 'invalid-data';
        this._viewAlerts.push(name);
        app.alert.show(name, {
            level: 'error',
            messages: 'ERR_RESOLVE_ERRORS'
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
        }
    },

    /**
     * Process all the models of the collection and prepares the context
     * beans "order_by_primary" & "order_by_secondary" for save action
     */
    _setOrderByFields: function() {
        var consoleId = this.context.get('consoleId');
        var ctxModel = this.context.get('model');

        var orderByPrimary = ctxModel.get('order_by_primary') || {};
        orderByPrimary[consoleId] = !_.isEmpty(orderByPrimary[consoleId]) ? orderByPrimary[consoleId] : {};

        var orderBySecondary = ctxModel.get('order_by_secondary') || {};
        orderBySecondary[consoleId] = !_.isEmpty(orderBySecondary[consoleId]) ? orderBySecondary[consoleId] : {};

        _.each(this.collection.models, function(model) {
            var moduleName = model.get('enabled_module');
            orderByPrimary[consoleId][moduleName] = this._buildOrderByValue(model, 'order_by_primary');
            orderBySecondary[consoleId][moduleName] = this._buildOrderByValue(model, 'order_by_secondary');
        }, this);

        ctxModel.set({
            order_by_primary: orderByPrimary,
            order_by_secondary: orderBySecondary,
        }, {silent: true});
    },

    /**
     * Process all the models of the collection and prepares the context
     * bean for save action
     */
    _beforeSaveConfig: function() {
        var consoleId = this.context.get('consoleId');
        var ctxModel = this.context.get('model');

        // Get the current settings for the given console ID. If there are no
        // settings for the given console ID, create them
        var enabledModules = ctxModel.get('enabled_modules') || {};
        enabledModules[consoleId] = !_.isEmpty(enabledModules[consoleId]) ? enabledModules[consoleId] : [];

        var filterDef = ctxModel.get('filter_def') || {};
        filterDef[consoleId] = !_.isEmpty(filterDef[consoleId]) ? filterDef[consoleId] : {};

        let freezeFirstColumn = ctxModel.get('freeze_first_column') || {};
        freezeFirstColumn[consoleId] = !_.isEmpty(freezeFirstColumn[consoleId]) ? freezeFirstColumn[consoleId] : {};

        // Update the variables holding the field values for the given console ID
        _.each(this.collection.models, function(model) {
            var moduleName = model.get('enabled_module');
            let isFreezeColumn = model.get('freeze_first_column');
            // model.get returns a string value so we convert it to boolean first
            isFreezeColumn = _.isString(isFreezeColumn) ? JSON.parse(isFreezeColumn) : isFreezeColumn;
            filterDef[consoleId][moduleName] = model.get('filter_def');
            freezeFirstColumn[consoleId][moduleName] = isFreezeColumn;
        }, this);

        // to build the definitions of selected fields and labels
        this.buildSelectedList();

        ctxModel.set({
            is_setup: true,
            enabled_modules: enabledModules,
            labels: this.labelList,
            viewdefs: this.selectedFieldList,
            filter_def: filterDef,
            freeze_first_column: freezeFirstColumn
        }, {silent: true});
        return this._super('_beforeSaveConfig');
    },

    /**
     * This build a view meta object for a module
     *
     * @param module
     * @return An object of view metadata
     */
    buildViewMetaObject: function(module) {
        return {
            base: {
                view: {
                    'multi-line-list': {
                        panels: [
                            {
                                label: 'LBL_LABEL_1',
                                fields: []
                            }
                        ],
                        // use the original collectionOptions and filterDef
                        collectionOptions: app.metadata.getView(module, 'multi-line-list').collectionOptions || {},
                        filterDef: app.metadata.getView(module, 'multi-line-list').filterDef || {}
                    }
                }
            }
        };
    },

    /**
     * This builds both field list and label list.
     */
    buildSelectedList: function() {
        var self = this;
        var selectedList = {};
        var labelList = {};

        // the main ul elements of the selected list, one ul for each module
        $('.columns ul.field-list').each(function(idx, ul) {
            var module = $(ul).attr('module_name');

            // init selectedList for this module
            selectedList[module] = self.buildViewMetaObject(module);

            // init labelList for this module
            labelList[module] = [];

            $(ul).children('li').each(function(idx2, li) {
                if (_.isEmpty($(li).attr('fieldname'))) {
                    // multi field column
                    selectedList[module].base.view['multi-line-list'].panels[0].fields
                        .push(self.buildMultiFieldObject(li, module, labelList[module]));
                } else {
                    // single field column
                    selectedList[module].base.view['multi-line-list'].panels[0].fields
                        .push(self.buildSingleFieldObject(li, module));
                }
            });
        });
        this.selectedFieldList = selectedList;
        this.labelList = labelList;
    },

    /**
     *
     * @param li The <li> element that represents the multi field column
     * @param module Module name
     * @param labelList The label list
     * @return Object
     */
    buildMultiFieldObject: function(li, module, labelList) {
        var subfields = [];
        var header = $(li).find('li.list-header');
        var self = this;

        // We may need to add the label to the system if it's a multi field column
        this.addLabelToList(header, module, labelList);

        // construct the field level definitions in subfields
        $(li).find('li.pill').each(function(idx2, li) {
            var field = {default: true, enabled: true};
            var fieldname = $(li).attr('fieldname');
            if (self.isSpecialField(fieldname, module)) {
                self.buildSpecialField(fieldname, field, module);
            } else {
                self.buildRegularField(li, field, module);
            }
            subfields.push(field);
        });
        return {
            // column level definitions
            name: $(header).attr('fieldname'),
            label: $(header).attr('fieldlabel'),
            subfields: subfields
        };
    },

    /**
     *
     * @param header The header element
     * @param module Module name
     * @param labelList The list to be added to
     */
    addLabelToList: function(header, module, labelList) {
        var label = $(header).attr('fieldlabel');
        var labelValue = $(header).attr('data-original-title');
        if (label == app.lang.get(label, module) && !_.isEmpty(labelValue)) {
            // label not already in system, add it to the list to save to system
            labelList.push({label: label, labelValue: labelValue});
        }
    },

    /**
     *
     * @param li The <li> element
     * @param module
     * @return Object
     */
    buildSingleFieldObject: function(li, module) {
        var subfields = [];
        var field = {default: true, enabled: true};
        var fieldname = $(li).attr('fieldname');

        // construct the field level definitions in subfields
        if (this.isSpecialField(fieldname, module)) {
            this.buildSpecialField(fieldname, field, module);
        } else {
            this.buildRegularField(li, field, module);
        }
        subfields.push(field);
        return {
            // column level definitions
            name: $(li).attr('fieldname'),
            label: $(li).attr('fieldlabel'),
            subfields: subfields
        };
    },

    /**
     * To check if this is a special field.
     * @param fieldname
     * @param module
     * @return {boolean} true if it's a special field, false otherwise
     */
    isSpecialField: function(fieldname, module) {
        var type = app.metadata.getModule(module, 'fields')[fieldname].type;
        return type == 'widget';
    },

    /**
     * To build the special field definitions.
     * @param fieldname The field name
     * @param field The field object to be populated
     * @param module The module name
     */
    buildSpecialField: function(fieldname, field, module) {
        var console = app.metadata.getModule(module, 'fields')[fieldname].console;
        // copy everything from console
        for (property in console) {
            field[property] = console[property];
        }
        field.widget_name = fieldname;
    },

    /**
     * To build the regular field definitions
     * @param li The <li> element of a regular field.
     * @param field The field object to be populated
     * @param module The module name
     */
    buildRegularField: function(li, field, module) {
        field.name = $(li).attr('fieldname');
        field.label = $(li).attr('fieldlabel');

        var fieldDef = app.metadata.getModule(module, 'fields')[field.name];
        var type = fieldDef.type;

        field.type = type;
        if (!_.isEmpty(fieldDef.related_fields)) {
            field.related_fields = fieldDef.related_fields;
        }

        if (type === 'relate') {
            // relate field, get the actual field type
            var actualType = this.getRelateFieldType(field.name, module);
            if (!_.isEmpty(actualType) && actualType === 'enum') {
                // if the actual type is enum, need to add enum and enum_module
                field.type = actualType;
                field.enum_module = fieldDef.module;
            } else {
                // not enum type, add module and related_fields
                field.module = fieldDef.module;
                field.related_fields =
                    fieldDef.related_fields ||
                    [fieldDef.id_name];
            }
            field.link = false;
        } else if (type === 'name') {
            field.link = false;
        } else if (type === 'text') {
            if (_.isEmpty(fieldDef.dbType)) {
                // if type is text and there is no dbType (such as description field)
                // make it not sortable
                field.sortable = false;
            }
        }
    },

    /**
     * To get the actual field type of a relate field.
     * @param fieldname
     * @param module
     * @return {string|*}
     */
    getRelateFieldType: function(fieldname, module) {
        var fieldDef = app.metadata.getModule(module, 'fields')[fieldname];
        if (!_.isEmpty(fieldDef) && !_.isEmpty(fieldDef.rname) && !_.isEmpty(fieldDef.module)) {
            return app.metadata.getModule(fieldDef.module, 'fields')[fieldDef.rname].type;
        }
        return '';
    },

    /**
     * Parses the 'order by' components of the given model for the given field
     * and concatenates them into the proper ordering string. Example: if the
     * primary sort field is 'name', and primary sort direction is 'asc',
     * it will return 'name:asc'
     *
     * @param {Object} model the model being saved
     * @param {string} the base field name
     * @private
     */
    _buildOrderByValue: function(model, fieldName) {
        var value = model.get(fieldName) || '';
        if (!_.isEmpty(value)) {
            var direction = model.get(fieldName + '_direction') || 'asc';
            value += ':' + direction;
        }
        return value;
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
            this._setOrderByFields();
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
                        this._setOrderByFields();
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

        _.each(this.collection.models, function(model) {
            model.doValidate(fieldsToValidate, function(isValid) {
                callback({modelId: model.id, isValid: isValid});
            });
        }, this);
    }
})

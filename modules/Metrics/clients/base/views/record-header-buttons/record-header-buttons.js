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
 * @class View.Views.Base.Metrics.RecordHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseMetricsRecordHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    /**
     * The labels to be created when saving console configuration
     */
    labelList: [],

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
        this.events = _.extend(this.events, {'click a[name="edit_button"]:not(.disabled)': 'editConfig'});
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
            if (this.context.get('create')) {
                if (app.drawer.count()) {
                    app.drawer.close(this.context, this.model);
                }
            } else { // cancel edit
                this.context.trigger('edit:cancelled');
            }
        }
    },

    /**
     * Handles 'edit' button click
     */
    editConfig: function() {
        let activeTabIndex = 0;
        let ariaControls;

        if (this.layout && this.layout.$('#tabs')) {
            activeTabIndex = this.layout.$('#tabs').tabs('option', 'active');
            ariaControls = this.layout.$('#tabs').find('li.tab.active').attr('aria-controls');
        }

        this.context.set({
            'activeTabIndex': activeTabIndex,
            'ariaControls': ariaControls
        });

        this.context.trigger('edit:clicked');
    },

    /**
     * Prepares the context beans "order_by_primary" & "order_by_secondary" for save action
     */
    _setOrderByFields: function() {
    },

    /**
     * Prepares the context bean for save action
     */
    _beforeSaveConfig: function() {
        // to build the definitions of selected fields and labels
        this.buildSelectedList();

        this.model.set({
            labels: this.labelList,
            viewdefs: this.selectedFieldList
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
        var labelList = [];

        // the main ul elements of the selected list, one ul for each module
        $('.columns ul.field-list').each(function(idx, ul) {
            var module = $(ul).attr('module_name');

            // init selectedList for this module
            selectedList = self.buildViewMetaObject(module);

            // init labelList for this module
            labelList = [];

            $(ul).children('li').each(function(idx2, li) {
                if (_.isEmpty($(li).attr('fieldname'))) {
                    // multi field column
                    selectedList.base.view['multi-line-list'].panels[0].fields
                        .push(self.buildMultiFieldObject(li, module, labelList));
                } else {
                    // single field column
                    selectedList.base.view['multi-line-list'].panels[0].fields
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
     * Gets a list of the underlying fields contained in a multi-line list
     * @param module
     * @return {Array} a list of field definitions from the multi-line list metadata
     * @private
     */
    _getMetaFields: function(module) {
        let multiLineMeta = app.metadata.getView(module, 'multi-line-list');
        let subfields = [];
        _.each(multiLineMeta.panels, function(panel) {
            _.each(panel.fields, function(fieldDefs) {
                subfields = subfields.concat(fieldDefs.subfields);
            });
        }, this);
        return subfields;
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
        let metaFields = this._getMetaFields(module);
        let metaField = metaFields.find(metaField => metaField.name === field.name && !metaField.widget_name);
        if (metaField && metaField.type) {
            field.type = metaField.type;
            if (metaField.disable_field) {
                field.disable_field = metaField.disable_field;
            }
        }

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
        this.validateModel(_.bind(function(result) {
            if (!result.isValid) {
                this.showButton('save_button');
                this.showInvalidModel();
            } else {
                this._setOrderByFields();
                this.model.save({}, {
                    success: _.bind(function() {
                        this.showSavedConfirmation();
                        if (app.drawer.count()) {
                            // close the drawer and return to Opportunities
                            app.drawer.close(this.context, this.context.get('model'));
                            // Config changed... reload metadata
                            app.sync();
                        } else {
                            app.router.navigate(app.router.getPreviousFragment() || this.module, {trigger: true});
                        }
                    }, this),
                    error: _.bind(function() {
                        this.showButton('save_button');
                    }, this)
                });
            }
        }, this));
    },

    /**
     * @inheritdoc
     */
    _getSaveConfigURL: function() {
        return app.api.buildURL(this.module);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.action = this.context.get('action');
        this._super('_render');
        this.setButtonStates();
    },

    /**
     * Shows a button.
     * @param {string} name
     */
    showButton: function(name) {
        this.getField(name).setDisabled(false);
        this.$('a[name=' + name + ']').removeClass('hide');
    },

    /**
     * Shows buttons for current action
     */
    setButtonStates: function() {
        let acls = app.user.getAcls().Metrics;
        let isAdmin = (app.user.get('type') == 'admin');
        let isDev = (!_.has(acls, 'developer'));
        let $cancelButton = this.$('a[name=cancel_button]');
        let $saveButton = this.$('a[name=save_button]');
        let $editButton = this.$('a[name=edit_button]');

        if (this.action === 'detail') {
            $cancelButton.addClass('hide');
            $saveButton.addClass('hide');
            if (!isAdmin && !isDev) {
                $editButton.addClass('hide');
            } else {
                $editButton.removeClass('hide');
            }
        } else {
            $editButton.addClass('hide');
            $cancelButton.removeClass('hide');
            $saveButton.removeClass('hide');
        }
    },

    /**
     * Validates model using the validation tasks
     */
    validateModel: function(callback) {
        var fieldsToValidate = {};
        var allFields = this.getFields(this.module, this.model);
        for (var fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }

        this.model.doValidate(fieldsToValidate, function(isValid) {
            callback({isValid: isValid});
        });
    }
})

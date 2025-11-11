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
 * @class View.Fields.Base.CJPopulateFields
 * @alias SUGAR.App.view.fields.BaseCJPopulateFields
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    //Add or remove event handler
    events: {
        'click .btn[name=populate_add]': 'addItem',
        'click .btn[name=populate_remove]': 'removeItem',
    },

    // Excelude fields Types
    denyListFieldTypes: ['id', 'link', 'image', 'html', 'file'],
    denyListFieldNames: [],

    /**
     * Group fit class.
     *
     * @property {string}
     */
    fitGroupClass: 'two',

    /**
     * Initialize the properties and events
     *
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.optionsList = [];
        this.optionsListLength = 0;
        this._currentIndex = 0;
        this.addedOptionsArray = [];
        this.addedFieldsDefs = [];
        this.customDateFieldType = 'cj-fieldset-for-date-in-populate-fields';
        this.selectedModuleName = '';
        this.populateFieldsMetaData = {};

        this.namePrefix = 'multiple_options';
        if (this.name && this.type) {
            this.namePrefix = `${ this.name }_${ this.type }_`;
        }

        this.optionListFieldTag = `select.select2[name*=${ this.namePrefix }_options]`;

        // to remove the error class on main field only
        if (this.view) {
            this.view.on('field:error', this.handleFieldErrorDecoration, this);
        }

        // trim empty options and return the length so that in HBS we
        // will check the length and show the table
        Handlebars.registerHelper('processPopulateFieldDataForDV', function(value) {
            _.each(value, function(currentOption, index, list) {
                if (_.isEmpty(currentOption) || _.isEmpty(currentOption.id)) {
                    value.splice(index, 1);
                }
            });
            this.processPopulateFieldDataForDV = value.length > 0;
        }, this);
    },

    /**
     * @inheritdoc
     */
    handleFieldErrorDecoration: function(field, hasError) {
        if (!hasError) {
            return;
        }
        this._removeErrorClassOnMainFieldComponent();
    },

    /**
     * Remove the error class from the main field
     * component
     */
    _removeErrorClassOnMainFieldComponent: function() {
        if (
            this.$el &&
            _.isFunction(this.$el.closest) &&
            this.$el.closest('.record-cell').length >= 1 &&
            this.$el.closest('.record-cell').hasClass('error')
        ) {
            this.$el.closest('.record-cell').removeClass('error');
        }
    },

    /**
     * Get the Module Name from the
     * relationship field
     */
    getModuleName: function() {},

    /**
     * Prepare the options list and
     * fields meta data according to the
     * selected module
     */
    _prepareOptionsListAndFieldsMetaData: function() {
        let moduleName = this.getModuleName();
        if (_.isEmpty(moduleName)) {
            return;
        }

        if (!_.isEmpty(this.selectedModuleName) && moduleName !== this.selectedModuleName) {
            this.restVariablesAndData(true);
            this.selectedModuleName = moduleName;
        } else {
            this.selectedModuleName = moduleName;
        }

        if (_.isEmpty(this.selectedModuleName)) {
            return;
        }

        let fields = {};
        fields[''] = '';
        if (app.acl.hasAccess('access', this.selectedModuleName)) {
            let moduleDef = app.metadata.getModule(this.selectedModuleName);
            _.each(moduleDef.fields, function(field, fieldName) {
                if (
                    field.extensive_filters !== false &&
                    field.dbType !== 'id' &&
                    field.dbType !== 'currency' &&
                    (field.source !== 'non-db' || (field.source === 'non-db' && ((field.type === 'relate' &&
                        field.link_type !== 'relationship_info') || field.type === 'tag'))) &&
                    field.readonly !== true &&
                    _.isEmpty(field.formula) &&
                    _.isEmpty(field.function) &&
                    _.isEmpty(field.function_bean) &&
                    !_.contains(this.denyListFieldTypes, field.type) &&
                    !_.contains(this.denyListFieldNames, fieldName) &&
                    !this.includeSubString(fieldName)
                ) {
                    field.actualFieldName = fieldName;
                    if (!_.isEqual(this.selectedModuleName, 'Emails') ||
                        (_.isEqual(this.selectedModuleName, 'Emails') && _.contains(['name', 'subject'], fieldName))) {
                        let modifiedFieldName = fieldName;
                        let modifiedFieldDef = field;
                        modifiedFieldDef.originalType = field.type || '';

                        let displayLabel = app.lang.get(modifiedFieldDef.vname, this.selectedModuleName) || '';

                        if (!modifiedFieldName.includes(this.namePrefix)) {
                            modifiedFieldName = `${ this.namePrefix }${ modifiedFieldName }`;
                        }
                        modifiedFieldDef.name = modifiedFieldName;

                        if (!_.isEmpty(modifiedFieldDef.id_name) &&
                            !modifiedFieldDef.id_name.includes(this.namePrefix)) {
                            modifiedFieldDef.actual_id_name = modifiedFieldDef.id_name;
                            modifiedFieldDef.id_name = `${ this.namePrefix }${ modifiedFieldDef.id_name }`;
                        }
                        if (!_.isEmpty(modifiedFieldDef.type) && modifiedFieldDef.type === 'multienum') {
                            modifiedFieldDef.type = 'enum';
                        }

                        if (!_.isEmpty(modifiedFieldDef.type) && (modifiedFieldDef.type === 'date' ||
                            modifiedFieldDef.type === 'datetimecombo')) {
                            modifiedFieldName += this.customDateFieldType;

                            let mainDateField = field;
                            mainDateField.css_class = 'cj_main_date';
                            mainDateField.name = fieldName;
                            mainDateField.shortName = 'main_date';
                            if (!mainDateField.name.includes(this.namePrefix)) {
                                mainDateField.name = `${ this.namePrefix }${ fieldName }`;
                            }

                            modifiedFieldDef = {
                                name: modifiedFieldName,
                                label: '',
                                vname: '',
                                actualFieldName: fieldName,
                                type: this.customDateFieldType,
                                originalType: field.type,
                                selectiveDateFieldName: `${ mainDateField.name }_date_type`,
                                inline: true,
                                equal_spacing: true,
                                fields: [
                                    {
                                        label: '',
                                        labelValue: '',
                                        name: `${ mainDateField.name }_date_type`,
                                        options: 'cj_date_type_selection_list',
                                        type: 'enum',
                                        css_class: 'cj_selective_date_type',
                                        shortName: 'selective_date',
                                    },
                                    mainDateField,
                                    {
                                        label: '',
                                        labelValue: '',
                                        name: `${ mainDateField.name }_date_relative`,
                                        options: (modifiedFieldDef.type === 'date') ? 'cj_relative_date_type_list' :
                                            'cj_relative_datetime_type_list',
                                        type: 'enum',
                                        css_class: 'cj_relative_date_type',
                                        shortName: 'relative_date',
                                    },
                                    {
                                        label: '',
                                        labelValue: '',
                                        name: `${ mainDateField.name }_date_int`,
                                        type: 'int',
                                        css_class: 'cj_int_date_type',
                                        shortName: 'int_date',
                                    },
                                ]
                            };

                            displayLabel = app.lang.get(mainDateField.vname, this.selectedModuleName) || '';
                        }
                        fields[modifiedFieldName] = displayLabel;
                        this.populateFieldsMetaData[modifiedFieldName] = modifiedFieldDef;
                    }
                } else if (_.contains(['currency_id', 'base_rate', 'currency_name', 'currency_symbol'], fieldName)) {
                    this.model.fields[fieldName] = field;
                }
            }, this);
        }
        this.optionsList = fields;
        this.optionsListLength = Object.keys(this.optionsList).length;

        this.loadPopulateAddedOptionsArray();
        this.bindEventsForSubFields();
    },

    /**
     * check if string contains certain
     * sub-strings
     *
     * @param {string} fieldName
     * @return {boolean}
     */
    includeSubString: function(fieldName) {
        if (
            fieldName.startsWith('cj_') ||
            fieldName.startsWith('is_cj_') ||
            fieldName.startsWith('repeat_') ||
            fieldName.includes('customer_journey') ||
            (fieldName.startsWith('dri_') &&
                !_.isEqual(fieldName, 'dri_workflow_sort_order'))
        ) {
            return true;
        }
        return false;
    },

    /**
     * Changes default behavior when doing inline editing on a List view.  We want to
     * load 'list' template instead of 'edit' template
     * read-only during inline editing.
     *
     * @override
     */
    _loadTemplate: function() {
        this._super('_loadTemplate');

        let template = app.template.getField(
            this.type,
            (this.view && this.view.name) ? `${ this.view.name }-${ this.tplName }` : '',
            this.model.module);

        if (!template && this.view && this.view.meta && this.view.meta.template) {
            template = app.template.getField(
                this.type,
                `${ this.view.meta.template }-${ this.tplName }`,
                this.model.module);
        }

        // If we're loading edit template on List view switch to detail template instead
        if (!template && this.view && this.view.action === 'list' && _.contains(['edit', 'detail'], this.tplName)) {
            this.template = app.template.getField(
                this.type,
                'list',
                this.module, this.tplName
            ) || app.template.empty;
            this.tplName = 'list';
        }

        this.template = template || this.template;
    },

    /**
     * Render the options and dropdown fields etc.
     * @inheritdoc
     */
    _render: function() {
        this._prepareOptionsListAndFieldsMetaData();
        this._super('_render');

        // on initial render, if edit view and no value is set then
        // render the empty row
        let length = parseInt(this.value.length);
        if (this.tplName === 'edit' && (isNaN(length) || _.isNull(length) || _.isEqual(this.value.length, 0))) {
            this.addSelectTo();
        }

        this._renderMainDropdown();
        this._renderSubFields();
        this._removeErrorClassOnMainFieldComponent();
        return this;
    },

    /**
     * It will render the SubFields against
     * the Main dropdown field
     */
    _renderSubFields: function() {
        if (_.isEmpty(this.value)) {
            return;
        }

        this._setSubFieldsModelData();

        if (this.view) {
            /*
             * this.populateFieldsMetaData contains all the fields
             * while this.addedOptionsArray contains only the fields
             * added on the view so we have to render only the fields
             * which are selected by users not all on edit and detail
             * views
             */
            _.each(this.addedOptionsArray, function(subFieldName) {
                if (!_.isEmpty(subFieldName)) {
                    this._renderSubField(subFieldName);
                }
            }, this);
        }
    },

    /**
     * It will render the SubField according to
     * action
     *
     * @param {string} subFieldName
     */
    _renderSubField: function(subFieldName) {
        /*
         * FIX-ME
         * Somehow we are getting same field mutliple times
         * in view and it's not causing issue but we are removing
         * here all duplicat fields copies except one
         * to avoid any performance issue
         */
        if (!_.isEmpty(subFieldName)) {
            let allDuplicateFieldsIndexes = [];
            _.each(this.view.fields, function(field, index, fieldList) {
                if (field.name === subFieldName) {
                    allDuplicateFieldsIndexes.push(index);
                }
            });

            // getting the exisintg rendered elements
            let existingFieldElems = [];
            this.$('span[sfuuid]').each(function() {
                let $this = $(this);
                let sfId = $this.attr('sfuuid');
                existingFieldElems.push(sfId);
            });

            // removed the duplicate elements and keep only
            // that which has already span rendered
            if (allDuplicateFieldsIndexes.length > 1) {
                _.each(allDuplicateFieldsIndexes, function(dupFieldIndex, index) {
                    if (!_.contains(existingFieldElems, dupFieldIndex)) {
                        delete this.view.fields[dupFieldIndex];
                    }
                }, this);
            }
        }

        // set the field element
        if (this.view && !_.isEmpty(subFieldName)) {
            let field = this.view.getField(subFieldName);
            if (field && !_.isEmpty(field.$el)) {
                if (this.$el.find(`#${ subFieldName }`).length >= 1) {
                    field.setElement(this.$(`span[sfuuid='${ field.sfId }']`));

                    try {
                        // for date field and we have issue on create view now
                        if (field._hasTimePicker) {
                            field._hasTimePicker = false;
                        }
                        field.setMode(this.action);
                    } catch (e) {
                        SUGAR.App.logger.fatal(`Failed to render ${ field } on ${ this }\n${ e }`);
                        SUGAR.App.error.handleRenderError(this, '_renderField', field);
                    }
                }
            }
        }
    },

    /**
     * It will set the subFields data in model
     */
    _setSubFieldsModelData: function() {
        _.each(this.value, function(currentOption, index, list) {
            if (
                !_.isEmpty(currentOption) &&
                !_.isEmpty(currentOption.id) &&
                !_.isEmpty(this.populateFieldsMetaData[currentOption.id])
            ) {
                let subFieldDef = this.populateFieldsMetaData[currentOption.id];
                //date field set
                if (!_.isEmpty(subFieldDef.type) && subFieldDef.type === this.customDateFieldType) {
                    _.each(subFieldDef.fields, function(f) {
                        if (!_.isEmpty(currentOption.childFieldsData) &&
                            !_.isEmpty(currentOption.childFieldsData[f.shortName])) {
                            this.model.set(f.name, currentOption.childFieldsData[f.shortName].value,
                                {silent: true});
                        }
                    }, this);
                } else if (!_.isEmpty(subFieldDef.id_name)) {
                    // relate field
                    this.model.set(subFieldDef.id_name, currentOption.id_value, {silent: true});
                    this.model.set(subFieldDef.name, currentOption.value, {silent: true});
                } else {
                    this.model.set(subFieldDef.name, currentOption.value, {silent: true});
                }
            }
        }, this);
    },

    /**
     * Render the Main dropdown that have certian
     * options like Select Users etc.
     */
    _renderMainDropdown: function() {
        let allowedTpls = ['edit'];
        if (!_.isEmpty(this.tplName) && _.contains(allowedTpls, this.tplName)) {
            let inList = (this.view.name === 'recordlist') ? true : false;

            this.$(this.optionListFieldTag).select2({
                dropdownCssClass: inList ? 'select2-narrow' : '',
                containerCssClass: inList ? 'select2-narrow' : '',
                width: inList ? 'off' : '250px',
                minimumResultsForSearch: 5
            }).on('change', _.bind(this.handleChange, this))
                .on('select2-focus', _.bind(_.debounce(this.handleFocus, 0), this));

            if (app.acl.hasAccessToModel('edit', this.model, this.name) === false) {
                this.$(this.optionListFieldTag).select2('disable');
            } else {
                this.$(this.optionListFieldTag).select2('enable');
            }
        } else if (this.tplName === 'disabled') {
            this.$(this.optionListFieldTag).select2('disable');
        }
    },

    /**
     * Toggle hide or show cj_selective_date_type based on if value is 'relative'
     *
     * @param {Object} e jQuery Change Event Object
     */
    handleChange: function(e) {
        let currentValue = e.val;
        let oldValue = _.isEmpty(e.removed) ? '' : e.removed.id;
        let found = _.find(this.value, function(currentOption) {
            return currentValue === currentOption.id;
        }) || false;

        let currentEleIndex = e.currentTarget.getAttribute('data-index');

        if (found && currentEleIndex !== 0 && this.value.length > 1) {
            this.value[currentEleIndex].id = oldValue;
        } else {
            this.value[currentEleIndex].id = currentValue;
            if (this.value[currentEleIndex].value) {
                delete this.value[currentEleIndex].value;
            }

            if (!_.isEmpty(oldValue) && !_.isEmpty(this.populateFieldsMetaData[oldValue])) {
                if (this.populateFieldsMetaData[oldValue].name) {
                    this.model.set(this.populateFieldsMetaData[oldValue].name, '');
                }
                if (this.populateFieldsMetaData[oldValue].id_name) {
                    this.model.set(this.populateFieldsMetaData[oldValue].id_name, '');
                }
            }

            this.populateAddedOptionsArray(currentValue);
            this.populateAddedOptionsArray(oldValue, 'remove');
        }
        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * Called to update value when a
     * selection is made from options
     * and set the value in model
     *
     * @param {Object} model
     */
    setValue: function(model) {
        if (!model) {
            return;
        }

        this.value = this.value || [];
        let index = this._currentIndex;
        this.value[index || 0] = {
            id: model.id
        };
        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * Convert string to array of objects
     * @param {string} value
     * @return {Array} Array of objects
     */
    format: function(value) {
        value = value || '[]';
        value = JSON.parse(value);

        return value;
    },

    /**
     * It will prepare data according to the
     * format which will be saved in DB
     */
    prepareData: function() {
        let value = this.value || [];
        if (this.model.isNew() && (_.isEmpty(value) || this.model.get(this.name) !== value)) {
            if (_.isEmpty(value)) {
                value = [];
                this.model.setDefault(this.name, value);
            }
        }

        if (_.isEmpty(value)) {
            if (this.action === 'edit' || this.action === 'detail' && this._checkAccessToAction(this.action)) {
                value = [];
                this.restVariablesAndData();
            }
            return JSON.stringify(value);
        }

        if (!_.isArray(value)) {
            value = [{id: value}];
        }

        // Place the add button as needed
        if (_.isArray(value) && !_.isEmpty(value)) {
            _.each(value, function(currentOption, index, list) {
                if (!_.isEmpty(currentOption) && !_.isEmpty(currentOption.id)) {
                    if (currentOption.remove_button && index !== 0) {
                        delete currentOption.remove_button;
                    } else {
                        currentOption.remove_button = true;
                    }
                    // As empty value is added in options so -2
                    if (index === list.length - 1 && index !== this.optionsListLength - 2) {
                        currentOption.add_button = true;
                    } else {
                        delete currentOption.add_button;
                    }
                    this._traverseValueForSingleOption(currentOption);
                }
            }, this);

            // number of valid options
            let numOptions = _.filter(value, function(currentOption) {
                return !_.isUndefined(currentOption) && !_.isNull(currentOption) && !_.isUndefined(currentOption.id);
            }).length;

            // Show remove button for all unset combos and only set combos if there are more than one
            _.each(value, function(currentOption) {
                if (!_.isUndefined(currentOption) && !_.isNull(currentOption) && _.isUndefined(currentOption.id) ||
                    numOptions > 1) {
                    currentOption.remove_button = true;
                }
            });
        }
        return JSON.stringify(value);
    },

    /**
     * Get the field value from the model
     *
     * @param {Object} field
     * @return {string | Object | Array}
     */
    getRelateFieldModelData: function(field) {
        if (this.model && this.model.has(field)) {
            return this.model.get(field);
        }
        return '';
    },

    /**
     * It will return the option on add new
     * button click from the array of options
     * which hasn't been added so far.
     */
    getValueForNewOption: function() {
        let optionListKeys = Object.keys(this.optionsList);
        let remainingOptions = _.difference(optionListKeys, this.addedOptionsArray);

        let ddValue = remainingOptions[0] ? remainingOptions[0] : '';
        return ddValue;
    },

    /**
     * Load the addedOptionsArray everytime
     * for track purpose
     */
    loadPopulateAddedOptionsArray: function() {
        // For Detail View -- START
        // as this field on change event on model cause issues
        // on field rendering so here we getting the value from
        // model if not set in the field for rendering
        let length = (_.isUndefined(this.value) || _.isNull(this.value)) ? 0 : parseInt(this.value.length);
        if ((this.action === 'detail' || this.tplName === 'detail') && (isNaN(length) || _.isNull(length) ||
            _.isEqual(length, 0))) {
            this.value = this.format(this.getRelateFieldModelData(this.name));
        }
        // For Detail View -- END

        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption)) {
                this.populateAddedOptionsArray(currentOption.id);
            }
        }, this);
    },

    /**
     * It will populate the addedOptionsArray that
     * can be used to have idea which options are
     * on view.
     *
     * @param {string} value
     * @param {string} op
     */
    populateAddedOptionsArray: function(value, op = 'add') {
        if (_.isEqual(op, 'remove')) {
            this.addedOptionsArray = _.without(this.addedOptionsArray, value);
        } else {
            this.addedOptionsArray.push(value);
        }
        this.populateAddedFieldsDefs(value, op);
        this.addedOptionsArray = _.uniq(this.addedOptionsArray);
    },

    /**
     * Populate the addedFieldsDefs that can be
     * used to have idea which fields are added
     * on view and which fields needs to be
     * validated or further actions.
     *
     * @param {string} value
     * @param {string} op
     */
    populateAddedFieldsDefs: function(value, op) {
        if (this.isCustomDateFieldType(value) && !_.isEmpty(this.populateFieldsMetaData[value].fields)) {
            _.each(this.populateFieldsMetaData[value].fields, function(field) {
                this.populateAddedFieldsDefsHelper(op, field.name, field);
            }, this);
        } else {
            this.populateAddedFieldsDefsHelper(op, value, this.populateFieldsMetaData[value]);
        }
    },

    /**
     * Helper function for populateAddedFieldsDefs
     *
     * @param {string} op
     * @param {string} fieldName
     * @param {Object} field
     */
    populateAddedFieldsDefsHelper: function(op, fieldName, field) {
        if (op === 'add') {
            if (!_.isEmpty(field)) {
                this.addedFieldsDefs[fieldName] = field;
            }
        } else {
            delete this.addedFieldsDefs[fieldName];
        }
    },

    /**
     * Check if field is of custom date type.
     *
     * @param {string} value
     */
    isCustomDateFieldType: function(value) {
        return (!_.isEmpty(value) &&
            !_.isEmpty(this.populateFieldsMetaData[value]) &&
            !_.isEmpty(this.populateFieldsMetaData[value].type) &&
            this.populateFieldsMetaData[value].type === this.customDateFieldType);
    },

    /**
     * It will add the selectTo option in model
     * and on view and format the data accordingly
     */
    addSelectTo: function() {
        this.value = this.value || [];
        this.loadPopulateAddedOptionsArray();

        let modelObj = {
            id: ''
        };
        this._currentIndex = this.value.length;
        this.setValue(modelObj);
        this._currentIndex++;
    },

    /**
     * Dispose all the fields and remove the
     * elements
     *
     * @param {Object} fieldDef
     */
    _disposeSubFields: function(fieldDef) {
        if (this.view && !_.isEmpty(fieldDef) && !_.isEmpty(fieldDef.name)) {
            let field = this.view.getField(fieldDef.name);
            if (field && !_.isEmpty(field.$el)) {
                field.unbindDom();
                field.dispose();
            }

            this._resetSubFieldModel(fieldDef);

            _.each(this.view.fields, function(field, index, fieldList) {
                if (field.name === fieldDef.name) {
                    delete fieldList[index];
                }
            });
        }
    },

    /**
     * It will unset the model data of all
     * subfields
     */
    _resetSubFieldsModel: function() {
        _.each(this.populateFieldsMetaData, function(subField, optionName, list) {
            if (!_.isEmpty(subField) && !_.isEmpty(subField.name)) {
                this._resetSubFieldModel(subField);
            }
        }, this);
    },

    /**
     * It will unset the model data of
     * particular subfield
     *
     * @param {Object} SubField
     */
    _resetSubFieldModel: function(subField) {
        if (this.model) {
            if (!_.isEmpty(subField.name) && this.model.has(subField.name)) {
                this.model.set(subField.name, '');
            }
            if (!_.isEmpty(subField.id_name) && this.model.has(subField.id_name)) {
                this.model.set(subField.id_name, '');
            }
            if (!_.isEmpty(subField.type) && subField.type === this.customDateFieldType) {
                _.each(subField.fields, function(f) {
                    this.model.set(f.name, '');
                }, this);
            }
        }
    },

    /**
     * It will remove the selectTo option in model
     * and on view and format the data accordingly
     *
     * @param {int} index
     */
    removeSelectTo: function(index) {
        let toBeRemoveValue = this.value[index].id;
        this.populateAddedOptionsArray(toBeRemoveValue, 'remove');
        this._currentIndex--;
        this.value.splice(index, 1);
        if (!_.isEmpty(toBeRemoveValue)) {
            this._disposeSubFields(this.populateFieldsMetaData[toBeRemoveValue]);
        }
        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.model) {
            this.bindEventsForSubFields();
            this.listenTo(this.model, 'sync', this._render, this);
        }
    },

    /**
     * Bind event for all fields
     */
    bindEventsForSubFields: function() {
        _.each(this.addedOptionsArray, function(optionName) {
            let subField = this.populateFieldsMetaData[optionName];
            if (!_.isEmpty(subField)) {
                this._bindSubFieldsModelEvent(subField, optionName);
            }
        }, this);
    },

    /**
     * It will bind the model on change event
     * on subFields
     *
     * @param {Object} subField
     * @param {string} optionName
     */
    _bindSubFieldsModelEvent: function(subField, optionName) {
        let fieldName = (_.isEqual(subField.type, 'relate') && !_.isEmpty(subField.id_name)) ? subField.id_name
            : subField.name;
        if (subField.type === this.customDateFieldType) {
            _.each(subField.fields, function(f) {
                this._bindSubFieldsModelEventHelper(f.name, optionName, subField);
            }, this);
        } else {
            this._bindSubFieldsModelEventHelper(fieldName, optionName);
        }
    },

    /**
     * Bind onchange event for all fields
     *
     * @param {string} fieldName
     * @param {string} optionName
     * @param {Object} parentfield
     */
    _bindSubFieldsModelEventHelper: function(fieldName, optionName, parentfield) {
        this.model.off(`change:${ fieldName }`, null);
        this.listenTo(this.model, `change:${ fieldName }`, function() {
            this._traverseValueOnChange(optionName, parentfield);
            this._updateAndTriggerChange();
        }, this);
    },

    /**
     * It will traverse the value of field and
     * call the formatter
     *
     * @param {string} optionName
     * @param {Object} parentfield
     */
    _traverseValueOnChange: function(optionName, parentField = null) {
        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption) && _.isEqual(currentOption.id, optionName)) {
                this._traverseValueForSingleOption(currentOption);
            }
        }, this);
    },

    /**
     * _traverseValueChange helper function
     *
     * @param {Object} currentOption
     */
    _traverseValueForSingleOption: function(currentOption) {
        if (
            _.isEmpty(currentOption) ||
            _.isEmpty(currentOption.id) ||
            _.isEmpty(this.populateFieldsMetaData) ||
            _.isEmpty(this.populateFieldsMetaData[currentOption.id])
        ) {
            return;
        }

        currentOption.label = this.populateFieldsMetaData[currentOption.id].label ||
            this.populateFieldsMetaData[currentOption.id].vname;
        currentOption.value = this.getRelateFieldModelData(this.populateFieldsMetaData[currentOption.id].name);
        currentOption.module = this.selectedModuleName;
        currentOption.type = this.populateFieldsMetaData[currentOption.id].originalType || '';
        currentOption.actualFieldName = this.populateFieldsMetaData[currentOption.id].actualFieldName || '';

        // relate field
        if (this.populateFieldsMetaData[currentOption.id].type === 'relate' &&
            this.populateFieldsMetaData[currentOption.id].id_name) {
            currentOption.id_name = this.populateFieldsMetaData[currentOption.id].id_name || '';
            currentOption.actual_id_name = this.populateFieldsMetaData[currentOption.id].actual_id_name || '';
            currentOption.id_value = this.getRelateFieldModelData(currentOption.id_name);
        }

        // currency field
        if (this.populateFieldsMetaData[currentOption.id].type === 'currency') {
            currentOption.id_name = 'currency_id';
            currentOption.id_value = this.getRelateFieldModelData(currentOption.id_name);
        }

        // date field
        if (this.populateFieldsMetaData[currentOption.id].type === this.customDateFieldType) {
            currentOption.childFieldsData = {};
            _.each(this.populateFieldsMetaData[currentOption.id].fields, function(f) {
                currentOption.childFieldsData[f.shortName] = {
                    id: f.name,
                    value: this.getRelateFieldModelData(f.name),
                };
            }, this);
        }
    },

    /**
     * Forcing change event on value update since backbone isn't picking up on changes within an object within the
     * array.
     */
    _updateAndTriggerChange: function() {
        this.model.unset(this.name, {silent: true}).set(this.name, this.prepareData());
    },

    /**
     * Trigger on Add button click
     */
    addItem: _.debounce(function(evt) {
        let index = $(evt.currentTarget).data('index');
        if (!index || this.value[index].id) {
            this.addSelectTo();
        }
    }, 0),

    /**
     * Trigger on Remove button click
     */
    removeItem: function(evt) {
        let index = $(evt.currentTarget).data('index');
        if (_.isNumber(index)) {
            this.removeSelectTo(index);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening(this.model);
        let $el = this.$(this.optionListFieldTag);
        let plugin = $el.data('select2');
        if (plugin) {
            plugin.close();
        }
        this._super('_dispose');
        this.restVariablesAndData();
    },

    /**
     * Reset the variables
     */
    restVariablesAndData: function(callFromPrepareOptionFunc = false) {
        //reset previous data first
        this._resetSubFieldsModel();

        this.value = [];
        this.optionsList = [];
        this.optionsListLength = 0;
        this._currentIndex = 0;
        this.addedOptionsArray = [];
        this.addedFieldsDefs = [];
        this.selectedModuleName = '';
        this.populateFieldsMetaData = {};
        this.processPopulateFieldDataForDV = false;

        if (this.model && this.name) {
            this.model.set(this.name, '', {silent: true});
        }

        if (!callFromPrepareOptionFunc) {
            this._prepareOptionsListAndFieldsMetaData();
        }
    },
})

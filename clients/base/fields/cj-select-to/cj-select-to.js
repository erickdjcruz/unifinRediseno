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
 * Show progress as a bar filled with color depending on the value
 *
 * @class View.Fields.Base.CjSelectToField
 * @alias SUGAR.App.view.fields.BaseCjSelectToField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    //events for add/remove events
    events: {
        'click .btn[name=add]': 'addItem',
        'click .btn[name=remove]': 'removeItem',
    },

    /**
     * Group fit class.
     *
     * @property {string}
     */
    fitGroupClass: 'two',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.separator = '|';
        this.dropdownName = 'cj_select_to_options_list';
        this.optionsList = [];
        this.optionsListLength = 0;
        this.currentIndex = 0;
        this.addedOptionsArray = [];
        this.callRenderEnumFieldsOptions = true;
        this.templateAvailableModules = [];

        this.namePrefix = 'multiple_options';
        if (this.name && this.type) {
            this.namePrefix = `${ this.name }_${ this.type }_`;
        }

        this.subFieldsMapping = {
            'specific_users': {
                name: `${ this.namePrefix }specific_user_name`,
                type: 'relate',
                id_name: `${ this.namePrefix }specific_user_id`,
                module: 'Users',
                isMultiSelect: true
            },
            'specific_contacts': {
                name: `${ this.namePrefix }specific_contact_name`,
                type: 'relate',
                id_name: `${ this.namePrefix }specific_contact_id`,
                module: 'Contacts',
                isMultiSelect: true
            },
            'related_parent_users': {
                name: `${ this.namePrefix }related_parent_users`,
                type: 'enum',
                isMultiSelect: true,
                options: 'cj_blank_list',
                relatedModuleForOptions: 'Users',
            },
            'related_parent_contacts': {
                name: `${ this.namePrefix }related_parent_contacts`,
                type: 'enum',
                isMultiSelect: true,
                options: 'cj_blank_list',
                relatedModuleForOptions: 'Contacts',
            }
        };
        this.optionListFieldTag = `select.select2[name*=${ this.namePrefix }_options]`;

        this._prepareOptionsList();

        Handlebars.registerHelper('setFormattedRnameArray', function(formattedRname, currentOption, currentThis) {
            if (!_.isEmpty(formattedRname)) {
                this.formattedRnameArray = formattedRname.split(currentThis.separator);
            }
        }, this);

        // trim empty options and return the length so that in HBS we
        // will check the length and show the table
        Handlebars.registerHelper('processDataForDV', function(value) {
            _.each(value, function(currentOption, index, list) {
                if (_.isEmpty(currentOption) || _.isEmpty(currentOption.id)) {
                    value.splice(index, 1);
                }
            });
            this.processDataForDV = value.length > 0;
        }, this);

        Handlebars.registerHelper('getMainDropDownValue', function(optionID, currentThis) {
            return _.filter(currentThis.optionsList, function(option, index) {
                return (index === optionID);
            });
        }, this);
    },

    /**
     * Get the options list.
     */
    _prepareOptionsList: function() {
        if (_.isEmpty(this.dropdownName)) {
            return;
        }

        this.optionsList = app.lang.getAppListStrings(this.dropdownName);
        this.optionsListLength = Object.keys(this.optionsList).length;
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
     * @inheritdoc
     *
     * @param {boolean} subRender
     */
    _render: function(subRender = true) {
        this._super('_render');

        // on initial render, if edit view and no value is set then
        // render the empty row
        if (this.tplName === 'edit' && _.isEqual(this.value.length, 0)) {
            this.addSelectTo();
            subRender = true;
        }

        this._renderMainDropdown();
        this._renderSubFields();
        this._renderEnumFieldsOptions();

        // to avoid to many calls and limit size reach problem
        if (this.callRenderEnumFieldsOptions && subRender) {
            this.callRenderEnumFieldsOptions = false;
            this._renderEnumFields(subRender);
        }

        return this;
    },

    /**
     * Render the enum fields against the relate field
     * option
     *
     * @param {boolean} render
     */
    _renderEnumFields: function(render = true) {
        if (!render) {
            return;
        }

        let url = this._getAvailableModulesApiURL();
        if (_.isEmpty(url)) {
            return;
        }

        app.api.call('read', url, null, {
            success: _.bind(this.moduleReadSuccess, this),
            error: _.bind(this.moduleReadError, this)
        });
    },

    /**
     * On Success render fields options
     *
     * @param {Array} availableModules
     */
    moduleReadSuccess: function(availableModules) {
        this.templateAvailableModules = availableModules;
        this._renderEnumFieldsOptions();
        this.callRenderEnumFieldsOptions = true;
        this._render(false);
    },

    /**
     * On Error show alert
     *
     * @param {Object} error
     */
    moduleReadError: function(error) {
        app.alert.show(`error_${ this.name }`, {
            level: 'error',
            messages: error.message,
            autoClose: false
        });
    },

    /**
     * It will return the URL to get the available
     * modules against template and it must be override
     * in the child class.
     */
    _getAvailableModulesApiURL: function() {},

    /**
     * Render the enum fields options against the relate fields and
     * set the mode accordingly as well as the setting of data
     */
    _renderEnumFieldsOptions: function() {
        _.each(this.subFieldsMapping, function(subField, optionName) {
            if (!_.isEmpty(subField) && _.isEqual(subField.type, 'enum') && this.view) {
                this._renderEnumFieldOptions(subField, optionName);
            }
        }, this);
    },

    /**
     * Render the enum field options against the relate fields and
     * set the mode accordingly as well as the setting of data
     * @param {Object} subField
     * @param {string} optionName
     */
    _renderEnumFieldOptions: function(subField, optionName) {
        let field = this.view.getField(subField.name);
        if (field && !_.isEmpty(field.$el)) {
            field.items = this._getEnumOptions(this.templateAvailableModules, subField);
            field.setMode(this.action);
            field.render();

            // set the enum values again as the Template is changed so if
            // previous value doesn't exists in new options then remove it
            _.each(this.value, function(currentOption, index, list) {
                if (!_.isEmpty(currentOption) && _.isEqual(currentOption.id, optionName)) {
                    this._setFormattedIdsAndRnames(currentOption, subField);
                }
            }, this);
        }
    },

    /**
     * It will return the relate fields names from
     * the availablemodules metadata according to the
     * subField metadata.
     *
     * @param {Array} availableModules
     * @param {Object} subField
     */
    _getEnumOptions: function(availableModules, subField) {
        let toBeAddedFieldsList = {};
        _.each(availableModules, function(module) {
            let metaData = app.metadata.getModule(module);
            _.each(metaData.fields, function(field) {
                if (_.isEqual(field.type, 'relate') && _.isEqual(field.module, subField.relatedModuleForOptions)) {
                    toBeAddedFieldsList[field.name] = app.lang.get(field.vname || field.label, module);
                }
            }, this);
        }, this);

        return toBeAddedFieldsList;
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
            if (this.value[currentEleIndex].formattedRname) {
                delete this.value[currentEleIndex].formattedRname;
            }
            if (this.value[currentEleIndex].formattedIds) {
                delete this.value[currentEleIndex].formattedIds;
            }

            if (!_.isEmpty(oldValue) && !_.isEmpty(this.subFieldsMapping[oldValue])) {
                if (this.subFieldsMapping[oldValue].name) {
                    this.model.set(this.subFieldsMapping[oldValue].name, '');
                }
                if (this.subFieldsMapping[oldValue].id_name) {
                    this.model.set(this.subFieldsMapping[oldValue].id_name, '');
                }
            }

            this.populateAddedOptionsArray(currentValue);
            this.populateAddedOptionsArray(oldValue, 'remove');
        }
        this._updateAndTriggerChange();
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
            _.each(this.subFieldsMapping, function(subField, optionName, list) {
                if (!_.isEmpty(subField) && !_.isEmpty(subField.name)) {
                    this._renderSubField(subField);
                }
            }, this);
        }
    },

    /**
     * It will render the SubField according to
     * action
     *
     * @param {Object} subField
     */
    _renderSubField: function(subField) {
        if (this.view && !_.isEmpty(subField.name)) {
            let field = this.view.getField(subField.name);
            if (field && !_.isEmpty(field.$el)) {
                if (this.$el.find(`#${ subField.name }`).length >= 1) {
                    this.$el.find(`#${ subField.name }`).append(field.el);

                    try {
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
     * for edit view only
     */
    _setSubFieldsModelData: function() {
        if (this.tplName === 'edit' && ((this.view && this.view.action === 'edit' &&
            this.view.currentState !== 'create') || !_.isEmpty(this.context.get('copiedFromModelId'))) &&
            !_.isEmpty(this.model)) {
            _.each(this.value, function(currentOption, index, list) {
                if (
                    !_.isEmpty(currentOption) &&
                    !_.isEmpty(currentOption.id) &&
                    !_.isEmpty(this.subFieldsMapping[currentOption.id])
                ) {
                    let subFieldDef = this.subFieldsMapping[currentOption.id];
                    // relate field
                    if (!_.isEmpty(subFieldDef.id_name)) {
                        this.model.set(subFieldDef.id_name,
                            this._convertDefaultStringInToArray(currentOption.formattedIds || ''),
                            {silent: true});
                        this.model.set(subFieldDef.name,
                            this._convertDefaultStringInToArray(currentOption.formattedRname || ''),
                            {silent: true});
                    } else {
                        this.model.set(subFieldDef.name,
                            this._convertDefaultStringInToArray(currentOption.formattedIds || ''),
                            {silent: true});
                    }
                }
            }, this);
        }
    },

    /**
     * It will convert the string into array
     * according to separator
     *
     * @param {string} defaultString
     */
    _convertDefaultStringInToArray: function(defaultString) {
        if (_.isEmpty(defaultString)) {
            return [];
        }
        let result = defaultString;
        if (_.isString(defaultString)) {
            result = defaultString.split(this.separator);
        }
        return result;
    },

    /**
     * It will dispose the field
     *
     * @param {Object} fieldDef
     */
    _disposeSubFields: function(fieldDef) {
        if (!_.isEmpty(fieldDef) && !_.isEmpty(fieldDef.name)) {
            if (this.view) {
                let field = this.view.getField(fieldDef.name);
                if (field && !_.isEmpty(field.$el)) {
                    field.dispose();
                }

                this._resetSubFieldModel(fieldDef);

                _.each(this.view.fields, function(field, index, fieldList) {
                    if (field.name === fieldDef.name) {
                        delete fieldList[index];
                    }
                });

            }
        }
    },

    /**
     * It will unset the model data of all
     * subfields
     */
    _resetSubFieldsModel: function() {
        _.each(this.subFieldsMapping, function(subField, optionName, list) {
            if (!_.isEmpty(subField) && !_.isEmpty(subField.name)) {
                this._resetSubFieldModel(subField);
            }
        }, this);
    },

    /**
     * It will unset the model data of
     * particular subfield
     *
     * @param {Object} subField
     */
    _resetSubFieldModel: function(subField) {
        if (this.model) {
            if (!_.isEmpty(subField.name) && this.model.has(subField.name)) {
                this.model.set(subField.name, '');
            }
            if (!_.isEmpty(subField.id_name) && this.model.has(subField.id_name)) {
                this.model.set(subField.id_name, '');
            }
        }
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
        let index = this.currentIndex;
        this.value[index || 0] = {
            id: model.id
        };
        this._updateAndTriggerChange();
    },

    /**
     * It will return the relate field
     * model data
     *
     * @param {string} relateField
     */
    getRelateFieldModelData: function(relateField) {
        if (this.model && this.model.has(relateField)) {
            return this.model.get(relateField);
        }
    },

    /**
     * @inheritdoc
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
        if (this.model.isNew() && (_.isEmpty(value) || this.model.get(this.name) != value)) {
            if (_.isEmpty(value)) {
                value = [];
                this.model.setDefault(this.name, value);
            }
        }

        if (_.isEmpty(value)) {
            if (this.action === 'edit' || this.action === 'detail' && this._checkAccessToAction(this.action)) {
                value = [];
                this.resetVariablesAndData();
            }
            return JSON.stringify(value);
        }

        if (!_.isArray(value)) {
            value = [{id: value}];
        }

        // Place the add button as needed
        if (_.isArray(value) && !_.isEmpty(value)) {
            this.addButton(value);

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
     * Add button if needed
     *
     * @param {Array} value
     */
    addButton: function(value) {
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

                if (currentOption.id && this.subFieldsMapping[currentOption.id]) {
                    let formattedIds = [];
                    let formattedRname = [];

                    // relate field
                    if (!_.isEmpty(this.subFieldsMapping[currentOption.id].id_name)) {
                        formattedIds = currentOption.formattedIds ||
                            this.getRelateFieldModelData(this.subFieldsMapping[currentOption.id].id_name);
                        formattedRname = currentOption.formattedRname ||
                            this.getRelateFieldModelData(this.subFieldsMapping[currentOption.id].name);
                    } else {
                        formattedIds = currentOption.formattedIds ||
                            this.getRelateFieldModelData(this.subFieldsMapping[currentOption.id].name);
                        formattedRname = currentOption.formattedRname || currentOption.formattedIds;
                    }

                    if (_.isArray(formattedIds)) {
                        formattedIds = formattedIds.join(this.separator);
                    }
                    if (_.isArray(formattedRname)) {
                        formattedRname = formattedRname.join(this.separator);
                    }
                    currentOption.formattedRname = formattedRname;
                    currentOption.formattedIds = formattedIds;
                }
            }
        }, this);
    },

    /**
     * It will return the option on add new
     * button click from the array of options
     * which hasn't been added so far.
     */
    getValueForNewOption: function() {
        let optionListKeys = Object.keys(this.optionsList);
        let remainingOptions = _.difference(optionListKeys, this.addedOptionsArray);

        return remainingOptions[0] ? remainingOptions[0] : '';
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

        this.addedOptionsArray = _.uniq(this.addedOptionsArray);
    },

    /**
     * It will add the selectTo option in model
     * and on view and format the data accordingly
     */
    addSelectTo: function() {
        this.value = this.value || [];
        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption)) {
                this.populateAddedOptionsArray(currentOption.id);
            }
        }, this);

        let modelObj = {
            id: ''
        };
        this.currentIndex = this.value.length;
        this.setValue(modelObj);
        this.currentIndex++;
    },

    /**
     * It will remove the selectTo option in model
     * and on view and format the data accordingly
     *
     * @param {int} index
     */
    removeSelectTo: function(index) {
        if (_.isUndefined(this.value) || _.isUndefined(this.value[index])) {
            return;
        }

        let toBeRemoveValue = this.value[index].id;
        this.populateAddedOptionsArray(toBeRemoveValue, 'remove');
        this.currentIndex--;
        this.value.splice(index, 1);
        if (!_.isEmpty(toBeRemoveValue)) {
            this._disposeSubFields(this.subFieldsMapping[toBeRemoveValue]);
        }
        this._updateAndTriggerChange();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.model) {
            this.listenTo(this.model, `change:${ this.name }`, function(model, value) {
                this._render(false);
            }, this);

            _.each(this.subFieldsMapping, function(subField, optionName) {
                if (!_.isEmpty(subField) && _.contains(['relate', 'enum'], subField.type)) {
                    this._bindSubFieldsModelEvent(subField, optionName);
                }
            }, this);

            this.listenTo(this.model, 'sync', this._render, this);
        }
    },

    /**
     * It will bind the model on change event
     * on subFields
     *
     * @param {Object} subField
     * @param {string} optionName
     */
    _bindSubFieldsModelEvent: function(subField, optionName) {
        let fieldName = (_.isEqual(subField.type, 'relate') &&
            !_.isEmpty(subField.id_name)) ? subField.id_name : subField.name;
        this.listenTo(this.model, `change:${ fieldName }`, function() {
            this._traverseValueOnChange(subField, optionName);
            this._updateAndTriggerChange();
        }, this);
    },

    /**
     * It will traverse the value of field and
     * call the formatter
     *
     * @param {Object} subField
     * @param {string} optionName
     */
    _traverseValueOnChange: function(subField, optionName) {
        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption) && _.isEqual(currentOption.id, optionName)) {
                this._setFormattedIdsAndRnames(currentOption, subField);
            }
        }, this);
    },

    /**
     * It will format the Rnames and Ids
     *
     * @param {Object} currentOption
     * @param {Object} subField
     */
    _setFormattedIdsAndRnames: function(currentOption, subField) {
        // relate field
        if (!_.isEmpty(subField.id_name)) {
            currentOption.formattedRname = this.model.get(subField.name) || currentOption.formattedRname;
            currentOption.formattedIds = this.model.get(subField.id_name) || currentOption.formattedIds;
        } else {
            currentOption.formattedIds = this.model.get(subField.name) || currentOption.formattedIds;
            this._setFormattedRnamesForEnum(currentOption, subField);
        }
    },

    /**
     * It will format the Rnames for Enum fields
     *
     * @param {Object} currentOption
     * @param {Object} subField
     */
    _setFormattedRnamesForEnum: function(currentOption, subField) {
        if (!this.view) {
            return;
        }

        let field = this.view.getField(subField.name);
        if (_.isEmpty(field) || _.isEmpty(field.$el) || _.isEmpty(field.items)) {
            return;
        }

        let formattedRname = [];
        _.each(currentOption.formattedIds, function(rname, index) {
            if (field.items[rname]) {
                formattedRname.push(field.items[rname]);
            } else {
                formattedRname.splice(index, 1);
                currentOption.formattedIds.splice(index, 1);
            }
        }, this);

        currentOption.formattedRname = formattedRname;
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
     * Reset the variables
     */
    resetVariablesAndData: function() {
        this.addedOptionsArray = [];
        this.currentIndex = 0;
        this.value = [];
        this.callRenderEnumFieldsOptions = true;
        this.templateAvailableModules = [];
        this._prepareOptionsList();
        this._resetSubFieldsModel();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        let $el = this.$(this.optionListFieldTag);
        let plugin = $el.data('select2');
        if (plugin) {
            plugin.close();
        }
        this._super('_dispose');
        this.resetVariablesAndData();
    },
});

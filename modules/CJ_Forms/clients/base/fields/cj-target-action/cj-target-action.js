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
 * @class View.Fields.Base.CJTargetActionField
 * @alias SUGAR.App.view.fields.BaseCJTargetActionField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    // Add or remove event handler
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

    targetActivitiesMeta: {
        'name': 'cj_forms_target_cj-target_multi_enum_field',
        'required': true,
        'reportable': true,
        'audited': true,
        'importable': 'true',
        'massupdate': false,
        'options': 'cj_blank_list',
        'type': 'enum',
        'isMultiSelect': true,
        'originalType': 'multienum'
    },

    actionActivitiesMeta: {
        'name': 'cj_forms_action_cj-action_enum_field',
        'required': true,
        'reportable': true,
        'audited': true,
        'importable': 'true',
        'massupdate': false,
        'options': 'cj_blank_list',
        'type': 'enum',
        'originalType': 'enum',
    },

    targetStagesMeta: {
        'name': 'cj_forms_target_cj-target_multi_enum_field',
        'required': true,
        'reportable': true,
        'audited': true,
        'importable': 'true',
        'massupdate': false,
        'options': 'cj_blank_list',
        'type': 'enum',
        'originalType': 'enum',
    },

    /**
     * Initialize the properties and events
     *
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initProperties();

        // to remove the error class on main field only
        if (this.view) {
            this.view.on('field:error', this.handleFieldErrorDecoration, this);
        }

        // set index of value object
        Handlebars.registerHelper('setIndex', function(value) {
            this.index = value;
        });

        // check if key equals to fieldName and index
        Handlebars.registerHelper('checkFieldName', function(key, fieldName, index, options) {
            return (key === `${fieldName}_${index}`) ? options.fn(this) : options.inverse(this);
        });
    },

    /**
     * Initialize field properties
     */
    _initProperties: function() {
        this.optionsList = [];
        this.optionsListLength = 0;
        this._currentIndex = 0;
        this.addedOptionsArray = [];
        this.addedActionsArray = [];
        this.addedFieldsDefs = [];
        this.populateFieldsMetaData = {};
        this.actionFieldsMetaData = {};
        this.targetFieldName = 'cj_forms_target_cj-target_multi_enum_field';
        this.actionFieldName = 'cj_forms_action_cj-action_enum_field';

        this.actionsList = {
            'Smart Guide Stage': app.lang.getAppListStrings('dri_subworkflow_action_list'),
            'Smart Guide Activities': app.lang.getAppListStrings('dri_workflow_activities_action_list'),
            'Smart Guide': app.lang.getAppListStrings('dri_workflow_action_list'),
        };

        this.targetModuleMapping = {
            'DRI_Workflow_Templates': 'Smart Guide',
            'DRI_SubWorkflow_Templates': 'Smart Guide Stage',
            'DRI_Workflow_Task_Templates': 'Smart Guide Activities',
        };

        this.namePrefix = 'multiple_options';
        if (this.name && this.type) {
            this.namePrefix = `${ this.name }_${ this.type }_`;
        }

        this.optionListFieldTag = `select.select2[name*=${ this.namePrefix }_options]`;
        this.actionListFieldTag = `select.select2[name*=${ this.namePrefix }_actions]`;
    },

    /**
     * Handles field decoration in case of error
     *
     * @param {Object} field
     * @param {boolean} hasError
     * @return {undefined}
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
     * Validates that target_action field is not empty
     *
     * @return {boolean}
     */
    _validateField: function() {
        let isValid = true;
        let mainTriggerType = this.model.get('main_trigger_type');

        if (_.isEmpty(mainTriggerType) || _.isEqual(mainTriggerType, 'smart_guide_to_sugar_action')) {
            return isValid;
        }

        let modelValue = this.format(this.model.get(this.name));

        if (modelValue.length > 0) {
            let lastRow = _.last(modelValue);

            if (_.isEmpty(lastRow.id)) {
                isValid = false;
                this._toggleValidationErrorClass('.inline-block.cj-target-list', true);
            }

            if (_.isEmpty(lastRow.action_id)) {
                isValid = false;
                this._toggleValidationErrorClass('.inline-block.cj-action-list', true);
            }
        }

        return isValid;
    },

    /**
     * Add / Remove validation error class and required icon
     *
     * @param {string} className
     * @param {boolean} add
     * @return {undefined}
     */
    _toggleValidationErrorClass: function(className, add) {
        if (!this.$el || this.$el.find(className).length === 0) {
            return;
        }

        let $actionList = this.$el.find(className).last();
        let $requiredIcon = $actionList.find('.add-on');

        if (add) {
            $actionList.addClass('cj-validation-error');

            if ($requiredIcon.length === 0) {
                $requiredIcon = $(this.exclamationMarkTemplate([app.error.getErrorString('required', true)]));
                $requiredIcon.removeClass('error-tooltip');
                $actionList.append($requiredIcon);
            }
        } else {
            $actionList.removeClass('cj-validation-error');
            $requiredIcon.remove();
        }
    },

    /**
     * Prepare the options list and
     * fields meta data according to the
     * selected module
     */
    _prepareOptionsListAndFieldsMetaData: function() {
        this.optionsList = app.lang.getAppListStrings('cj_forms_taget_action_module_list');
        this.optionsListLength = Object.keys(this.optionsList).length;

        this.loadPopulateAddedOptionsArray();
        this.bindEventsForSubFields(this.addedOptionsArray, this.populateFieldsMetaData);
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

        // unset populate and action field meta object
        // to avoid any duplicate / dangling object
        this.populateFieldsMetaData = {};
        this.actionFieldsMetaData = {};

        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption)) {
                if (_.isEqual(currentOption.id, 'Smart Guide') && !_.isEqual(currentOption.action_id, 'completed')) {
                    return;
                }

                this.populateMetaAndOptions(currentOption.id, index);
            }
        }, this);
    },

    /**
     * Populate meta for target and action field
     *
     * @param {string} id
     * @param {number} index
     */
    populateMetaAndOptions: function(id, index) {
        // render target field for Smart Guide Stage and Smart Guide Activities
        if (_.includes(['Smart Guide Stage', 'Smart Guide Activities'], id)) {
            let targetFieldName = `${this.targetFieldName}_${index}`;
            this.populateFieldsMetaData[targetFieldName] = _.clone(this.targetActivitiesMeta);

            // for Smart Guide Stage set stage meta
            if (_.isEqual(id, 'Smart Guide Stage')) {
                this.populateFieldsMetaData[targetFieldName] = _.clone(this.targetStagesMeta);
            }

            this.populateFieldsMetaData[targetFieldName].name = targetFieldName;
            this.populateAddedOptionsArray(targetFieldName);
        }

        // render action field for Smart Guide and Smart Guide Stage
        if (_.includes(['Smart Guide', 'Smart Guide Stage'], id)) {
            // action field
            let actionFieldName = `${this.actionFieldName}_${index}`;
            this.actionFieldsMetaData[actionFieldName] = _.clone(this.actionActivitiesMeta);
            this.actionFieldsMetaData[actionFieldName].name = actionFieldName;

            this.populateAddedActionsArray(actionFieldName);
        }
    },

    /**
     * It will populate the addedActionsArray that
     * can be used to have idea which options are
     * on view.
     *
     * @param {string} value
     * @param {string} op
     */
    populateAddedActionsArray: function(value, op = 'add') {
        if (_.isEqual(op, 'remove')) {
            this.addedActionsArray = _.without(this.addedActionsArray, value);
        } else {
            this.addedActionsArray.push(value);
        }

        this.addedActionsArray = _.uniq(this.addedActionsArray);
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

        if (_.isEqual(this.action, 'detail') || _.isEqual(this.tplName, 'detail')) {
            let templateId = this.model && this.model.get('smart_guide_template_id');
            this.fetchTemplateData(templateId);
        }

        this._initialRender();
        this._renderMainDropdown();
        this._renderSubFields();
        this._renderActionField();
        this._removeErrorClassOnMainFieldComponent();
    },

    /**
     * Remove empty object from field value
     */
    _removeEmptyRow: function() {
        if (_.isEqual(this.action, 'detail') || _.isEqual(this.tplName, 'detail')) {
            _.each(this.value, function(currentOption, index, list) {
                if (_.isEmpty(currentOption) || _.isEmpty(currentOption.id)) {
                    this.value.splice(index, 1);
                }
            }, this);

            this.processPopulateFieldDataForDV = this.value.length > 0;

            // set value in model before rendering the field
            this.model.unset(this.name, {silent: true}).set(this.name, JSON.stringify(this.value));
        }
    },

    /**
     * if edit view and no value is set then render the empty row
     */
    _initialRender: function() {
        let length = parseInt(this.value.length);

        if (this.tplName === 'edit' && (isNaN(length) || _.isNull(length) || _.isEqual(this.value.length, 0))) {
            this.initialRender = true;

            this.addSelectTo();
            delete this.initialRender;
        }
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
                let fieldElement = this.$el.find(`#${ subFieldName }`);

                if (fieldElement.length >= 1) {
                    field.setElement(this.$(`span[sfuuid='${ field.sfId }']`));

                    try {
                        let dataIndex = _.first(fieldElement).getAttribute('data-index');
                        let fieldOptions = this.getFieldOptions(dataIndex, subFieldName);

                        if (this.shouldRenderField(dataIndex, subFieldName)) {
                            field.items = fieldOptions.items;
                            field.def.placeholder = fieldOptions.placeholder;
                            field.def.no_required_placeholder = true;

                            field.setMode(this.action);
                            this._removeErrorClassOnMainFieldComponent();
                        }
                    } catch (e) {
                        SUGAR.App.logger.fatal(`Failed to render ${ field } on ${ this }\n${ e }`);
                        SUGAR.App.error.handleRenderError(this, '_renderField', field);
                    }
                }
            }
        }
    },

    /**
     * For target field if value has id then render it and
     * for action field if value has action_id then render it
     *
     * @param {number} dataIndex
     * @param {string} fieldName
     * @return {boolean}
     */
    shouldRenderField: function(dataIndex, fieldName) {
        let modelData = this.value && this.value[dataIndex];
        let renderField = false;

        if (modelData) {
            if (fieldName.includes(this.targetFieldName)) {
                renderField = !_.isEmpty(modelData.id);
            } else if (fieldName.includes(this.actionFieldName)) {
                renderField = !_.isEmpty(modelData.action_id);
            }
        }

        return renderField;
    },

    /**
     * Provide enum options and placeholder label for target and action field
     *
     * @param {number} dataIndex
     * @param {string} fieldName
     * @return {Object}
     */
    getFieldOptions: function(dataIndex, fieldName) {
        let items = {};
        let placeHolder = '';
        let modelData = this.value && this.value[dataIndex];

        // set enum values for stage and activities
        if (modelData) {
            let completActionList = app.lang.getAppListStrings('dri_subworkflow_complete_action_list');

            switch (modelData.id) {
                case 'Smart Guide':
                    items = completActionList;
                    placeHolder = 'LBL_SELECT_SMART_GUIDE_ACTIVITIES_ACTION_PLACEHOLDER';
                    break;
                case 'Smart Guide Activities':
                    items = this.templateActivities;
                    placeHolder = 'LBL_SELECT_SMART_GUIDE_ACTIVITIES_PLACEHOLDER';
                    break;
                case 'Smart Guide Stage':
                    // for target field set template stages
                    items = fieldName.includes(this.targetFieldName) ? this.templateStages : {};
                    placeHolder = 'LBL_SELECT_SMART_GUIDE_STAGE_PLACEHOLDER';

                    if (fieldName.includes(this.actionFieldName)) {
                        // for action field set selected stage activities
                        items = this.stageActivities  && this.stageActivities[modelData.value];
                        placeHolder = 'LBL_SELECT_SMART_GUIDE_ACTIVITY_PLACEHOLDER';

                        if (_.isEqual(modelData.action_id, 'completed')) {
                            items = completActionList;
                            placeHolder = 'LBL_SELECT_STAGE_ACTIVITIES_ACTION_PLACEHOLDER';
                        }
                    }
                    break;
            }
        }

        return {
            items: items,
            placeholder: app.lang.get(placeHolder),
        };
    },

    /**
     * Fetch template data according to smart guide template id
     *
     * @param {string} newTemplateId
     * @return {undefined}
     */
    fetchTemplateData: function(newTemplateId) {
        if (_.isEqual(newTemplateId, this.templateId)) {
            return;
        }

        this.templateId = newTemplateId;

        // if template stages were previously fetched
        if (!_.isUndefined(this.templateStages)) {
            this._resetSubFieldsModel();
            this._updateAndTriggerChange();
        }

        if (!_.isEmpty(newTemplateId)) {
            let url = app.api.buildURL('DRI_Workflow_Templates', 'widget-data', {
                id: this.templateId
            });

            app.api.call('read', url, null, {
                success: _.bind(this.loadCompleted, this),
                error: _.bind(this.loadError, this),
            });
        } else {
            delete this.templateActivities;
            delete this.templateStages;
            delete this.stageActivities;
        }

        this._render();
    },

    /**
     * Populate stages and activities according to fetched template data
     *
     * @param {Object} response
     */
    loadCompleted: function(response) {
        // store all activities of template
        this.templateActivities = {};
        // store all stages of template
        this.templateStages = {};
        // store activities according to stage
        this.stageActivities = {};

        _.each(response.stages, function(stage) {
            let stageId = stage.id;
            this.templateStages[stageId] = stage.name;
            this.stageActivities[stageId] = {};

            _.each(stage.activities, function(activity) {
                this.stageActivities[stageId][activity.id] = activity.name;

                _.each(activity.children, function(childActivity) {
                    this.stageActivities[stageId][childActivity.id] = childActivity.name;
                }, this);
            }, this);

            this.templateActivities = _.extend(this.templateActivities, this.stageActivities[stageId]);
        }, this);

        if (_.isEmpty(this.value) || (this.value.length === 1 && _.isEmpty(this.value[0].value))) {
            this.setInitialValues(0);
        }

        this._render();
    },

    /**
     * Handles the error if returned from the widget data api
     *
     * @param {Object} error
     */
    loadError: function(error) {
        app.alert.show(error.error, {
            level: 'error',
            autoClose: true,
            messages: app.lang.get('LBL_ERROR')
        });
    },

    /**
     * It will set the subFields data in model
     */
    _setSubFieldsModelData: function() {
        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption.id) && !_.isEqual(currentOption.id, 'Smart Guide')) {
                // render target field if id is other than Smart Guide
                let subFieldName = `${this.targetFieldName}_${index}`;

                this.model.set(subFieldName, currentOption.value, {silent: true});
            }
        }, this);
    },

    /**
     * It will set the subFields data in model
     */
    _setActionFieldsModelData: function() {
        _.each(this.value, function(currentOption, index, list) {
            if (!_.isEmpty(currentOption.id) && !_.isEqual(currentOption.id, 'Smart Guide Activities')) {
                // render action field if id is Smart Guide Stage
                let subFieldName = `${this.actionFieldName}_${index}`;

                this.model.set(subFieldName, currentOption.action_value, {silent: true});
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
            let dropdownCssMeta = {
                dropdownCssClass: inList ? 'select2-narrow' : '',
                containerCssClass: inList ? 'select2-narrow' : '',
                minimumResultsForSearch: 5
            };

            this.$(this.optionListFieldTag).select2(dropdownCssMeta)
                .on('change', _.bind(this.handleChange, this))
                .on('select2-focus', _.bind(_.debounce(this.handleFocus, 0), this));

            this.$(this.actionListFieldTag).select2(dropdownCssMeta)
                .on('change', _.bind(this.handleActionChange, this))
                .on('select2-focus', _.bind(_.debounce(this.handleFocus, 0), this));

            if (app.acl.hasAccessToModel('edit', this.model, this.name) === false) {
                this.$(this.optionListFieldTag).select2('disable');
                this.$(this.actionListFieldTag).select2('disable');
            } else {
                this.$(this.optionListFieldTag).select2('enable');
                this.$(this.actionListFieldTag).select2('enable');
            }
        } else if (this.tplName === 'disabled') {
            this.$(this.optionListFieldTag).select2('disable');
            this.$(this.actionListFieldTag).select2('disable');
        }
    },

    /**
     * Action field change handler
     *
     * @param {Event}
     */
    handleActionChange: function(event) {
        let currentEleIndex = event.currentTarget.getAttribute('data-index');
        this.value[currentEleIndex].action_id = event.val;

        // reset action_value
        if (this.value[currentEleIndex].action_value) {
            delete this.value[currentEleIndex].action_value;
        }

        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * Set action field data in model and then render it
     */
    _renderActionField: function() {
        this._setActionFieldsModelData();

        if (this.view) {
            _.each(this.addedActionsArray, function(subFieldName) {
                if (!_.isEmpty(subFieldName)) {
                    this._renderSubField(subFieldName);
                }
            }, this);

            this.bindEventsForSubFields(this.addedActionsArray, this.actionFieldsMetaData);
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
        let currentEleIndex = Number(e.currentTarget.getAttribute('data-index'));
        let targetField = `${this.targetFieldName}_${currentEleIndex}`;
        let actionField = `${this.actionFieldName}_${currentEleIndex}`;
        let remainingItems = this.value.length - (currentEleIndex + 1);

        this.value[currentEleIndex].id = currentValue;
        this.value[currentEleIndex].action_id = '';

        if (this.value[currentEleIndex].value) {
            delete this.value[currentEleIndex].value;
        }

        if (this.value[currentEleIndex].action_value) {
            delete this.value[currentEleIndex].action_value;
        }

        if (this.model.has(targetField)) {
            this.model.unset(targetField);
        }

        if (this.model.has(actionField)) {
            this.model.unset(actionField);
        }

        // if Smart Guide is selected remove all the values after current index
        if (_.isEqual(currentValue, 'Smart Guide') && remainingItems > 0) {
            this.value.splice(currentEleIndex + 1, remainingItems);
        }

        this.populateAddedOptionsArray(currentValue);
        this.populateAddedOptionsArray(oldValue, 'remove');

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
        let index = this._currentIndex || 0;
        this.value[index] = {
            id: model.id
        };

        if (this.initialRender) {
            this.setInitialValues(index);
        }

        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * Initially show add button, hide remove button and set id according to parent type
     *
     * @param {number} index
     */
    setInitialValues: function(index) {
        // set initial values only on edit view
        if (_.isEqual(this.action, 'edit') || _.isEqual(this.tplName, 'edit')) {
            this.value = this.value || [{}];
            let targetModule = '';

            if (this.view && this.view.model) {
                let parentId = '';
                let parentType = '';
                let parent = this.view.model.get('parent');

                if (parent) {
                    parentId = parent.id;
                    parentType = parent._module;
                }

                targetModule = this.value[0].id || this.targetModuleMapping[parentType];

                // initially set parent activity / stage
                switch (targetModule) {
                    case 'Smart Guide Stage':
                        parentId = (this.templateStages && this.templateStages[parentId]) ? parentId : '';
                        break;
                    case 'Smart Guide Activities':
                        parentId = (this.templateActivities && this.templateActivities[parentId]) ? parentId : '';
                        break;
                    default:
                        parentId = '';
                }

                this.value[index] = {
                    id: targetModule || '',
                    value: parentId,
                };
            }

            this.value[index].remove_button = false;
            // set add_button to true if target module is not Smart Guide
            this.value[index].add_button = !_.isEqual(targetModule, 'Smart Guide');

            this.model.unset(this.name, {silent: true}).set(this.name, JSON.stringify(this.value));
        }
    },

    /**
     * Convert string to array of objects
     *
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
     *
     * @return {string|null}
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
                if (!_.isEmpty(currentOption)) {
                    // show remove button for multiple rows if not Smart Guide
                    if ((index === 0 && list.length - 1 === 0) || _.isEqual(currentOption.id, 'Smart Guide')) {
                        currentOption.remove_button = false;
                    } else {
                        currentOption.remove_button = true;
                    }

                    // show add button for last row if not Smart Guide
                    if (index === list.length - 1 && !_.isEqual(currentOption.id, 'Smart Guide')) {
                        currentOption.add_button = true;
                    } else {
                        delete currentOption.add_button;
                    }
                    this._traverseValueForSingleOption(currentOption, index);
                }
            }, this);
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
     *
     * @return {string}
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
        this.populateAddedFieldsDefs(op, value, this.populateFieldsMetaData[value]);
        this.addedOptionsArray = _.uniq(this.addedOptionsArray);
    },

    /**
     * Populate the addedFieldsDefs that can be
     * used to have idea which fields are added
     * on view and which fields needs to be
     * validated or further actions.
     *
     * @param {string} op
     * @param {string} fieldName
     * @param {Object} field
     */
    populateAddedFieldsDefs: function(op, fieldName, field) {
        if (_.isEqual(op, 'add')) {
            if (!_.isEmpty(field)) {
                this.addedFieldsDefs[fieldName] = field;
            }
        } else {
            delete this.addedFieldsDefs[fieldName];
        }
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

        _.each(this.actionFieldsMetaData, function(subField, optionName, list) {
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
        if (this.model && !_.isEmpty(subField.name) && this.model.has(subField.name)) {
            this.model.set(subField.name, '');
        }
    },

    /**
     * It will remove the selectTo option in model
     * and on view and format the data accordingly
     *
     * @param {int} index
     */
    removeSelectTo: function(index) {
        let toBeRemoveValue = `${this.targetFieldName}_${index}`;
        let toBeRemoveActionValue = `${this.actionFieldName}_${index}`;

        this.populateAddedOptionsArray(toBeRemoveValue, 'remove');
        this.populateAddedActionsArray(toBeRemoveActionValue, 'remove');
        this._currentIndex--;
        this.value.splice(index, 1);

        if (!_.isEmpty(toBeRemoveValue)) {
            this._disposeSubFields(this.populateFieldsMetaData[toBeRemoveValue]);
            this._disposeSubFields(this.actionFieldsMetaData[toBeRemoveActionValue]);
        }

        this._updateSubFieldModel(index);
        this._updateAndTriggerChange();
        this._render();
    },

    /**
     * On removing the item from value update the field model
     *
     * @param {number} currentIndex
     */
    _updateSubFieldModel: function(currentIndex) {
        for (let index = 0; index < this.value.length; index++) {
            // update model only from current index
            if (index >= currentIndex) {
                let newTargetValue = this.model.get(`${this.targetFieldName}_${index + 1}`);
                let newActionValue = this.model.get(`${this.actionFieldName}_${index + 1}`);

                this.model.set(`${this.targetFieldName}_${index}`, newTargetValue, {silent: true});
                this.model.set(`${this.actionFieldName}_${index}`, newActionValue, {silent: true});
            }
        }

        let targetField = `${this.targetFieldName}_${this.value.length}`;
        let actionField = `${this.actionFieldName}_${this.value.length}`;

        if (this.model.has(targetField)) {
            delete this.model.attributes[targetField];
        }

        if (this.model.has(actionField)) {
            delete this.model.attributes[actionField];
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.model) {
            this.bindEventsForSubFields(this.addedOptionsArray, this.populateFieldsMetaData);
            this.listenTo(this.model, 'sync', this._render, this);
            this.listenTo(this, 'view:smart_guide_id:changes', _.bind(this.fetchTemplateData, this));
        }
    },

    /**
     * Bind event for all target fields
     *
     * @param {Array} addedOptionsArray
     * @param {Object} subFieldMeta
     */
    bindEventsForSubFields: function(addedOptionsArray, subFieldMeta) {
        _.each(addedOptionsArray, function(optionName) {
            let subField = subFieldMeta[optionName];

            if (!_.isEmpty(subField)) {
                this._bindSubFieldsModelEventHelper(subField.name, optionName);
            }
        }, this);
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
            this._renderActionField();
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
                this._traverseValueForSingleOption(currentOption, index);
            }
        }, this);
    },

    /**
     * _traverseValueChange helper function
     *
     * @param {Object} currentOption
     * @param {number} index
     */
    _traverseValueForSingleOption: function(currentOption, index) {
        if (_.isEmpty(currentOption) || _.isEmpty(currentOption.id) || this.initialRender) {
            return;
        }

        currentOption.value = this.getRelateFieldModelData(`${this.targetFieldName}_${index}`);
        currentOption.action_value = this.getRelateFieldModelData(`${this.actionFieldName}_${index}`);
    },

    /**
     * Forcing change event on value update since backbone
     * isn't picking up on changes within an object within the array.
     */
    _updateAndTriggerChange: function() {
        this.model.unset(this.name, {silent: true}).set(this.name, this.prepareData());
    },

    /**
     * Trigger on Add button click
     */
    addItem: function(evt) {
        let index = Number($(evt.currentTarget).data('index'));

        if (index >= 0 && this.value[index].id && this.value[index].action_id) {
            this.addSelectTo();
        }
    },

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
        this.actionsList = [];
        this.optionsListLength = 0;
        this._currentIndex = 0;
        this.addedOptionsArray = [];
        this.addedFieldsDefs = [];
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

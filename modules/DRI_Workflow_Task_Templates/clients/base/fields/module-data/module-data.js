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
 * @class View.Fields.Base.DRIWorkflowTaskTemplates.ModuleDataField
 * @alias SUGAR.App.view.fields.BaseDRIWorkflowTaskTemplatesModuleDataField
 * @extends View.Fields.Base.EnumField
 */
({
    /**
     * ModuleData FieldTemplate (base)
     */
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';

        if (this.model) {
            this.listenTo(this.model, 'sync', this.setModuleList);
            if (this.def) {
                if (this.def.onChangeTriggerField) {
                    this.listenTo(this.model, `change:${this.def.onChangeTriggerField}`, this.setModuleList);
                }
                if (this.def.name) {
                    this.listenTo(this.model, `change:${this.def.name}`, this.setFieldList);
                }
                this.setModuleList();
            }
        }
    },

    /**
     * Generate a module list depending upon the available modules list
     *
     * @return {undefined}
     */
    setModuleList: function() {
        let parentId = this.model.get('dri_workflow_template_id');
        if (_.isEmpty(parentId) ||
            _.isEmpty(this.def.onChangeTriggerField) ||
            _.isEmpty(this.def.onChangeTriggerValueEqualTo)) {
            return;
        }
        if (_.isEqual(this.model.get(this.def.onChangeTriggerField), this.def.onChangeTriggerValueEqualTo)) {
            let template = app.data.createBean('DRI_Workflow_Templates', {id: parentId});
            template.fetch({
                success: _.bind(this._setModuleListSuccess, this),
            });
        }
    },

    /**
     * Populate field items depending upon the available modules list
     *
     * @param {Bean} template
     * @return {undefined}
     */
    _setModuleListSuccess: function(template) {
        let availableModuleList = template.get('available_modules');
        if (!_.isEmpty(availableModuleList) && _.includes(availableModuleList, _.first(this.value)) === false) {
            this.model.set(this.name, '');
        }

        let moduleList = {};
        _.each(availableModuleList, function(module) {
            let lable = app.lang.get('LBL_MODULE_NAME_SINGULAR', module);
            moduleList[module] = lable;
        });

        this.setFieldList();
        this.items = moduleList;
        this.render();
    },

    /**
     * Generate a field list depending upon the selected module
     *
     * @return {undefined}
     */
    setFieldList: function() {
        if (!_.isEmpty(this.model.get(this.def.name))) {
            let parent = app.data.createBean(this.model.get(this.def.name));
            let options = {};
            let fieldTypes = ['date', 'datetime', 'datetimecombo'];

            _.each(parent.fields, function(field) {
                if (_.includes(fieldTypes, field.type) && !_.isEqual(field.source, 'non-db')) {
                    options[field.name] = app.lang.get(field.vname, parent.module);
                }
            });

            let listField = this.view.getField(this.def.fieldListName);
            if (!_.isUndefined(listField)) {
                listField.items = options;
                listField.render();

                if (!options[this.model.get(this.def.fieldListName)]) {
                    this.model.set(this.def.fieldListName, '');
                }
            }
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening(this.model);
        this._super('_dispose');
    },
});

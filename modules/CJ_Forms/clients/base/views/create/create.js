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
 * @class View.Views.Base.CJForms.CreateView
 * @alias SUGAR.App.view.views.CJFormsCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.on('record:showHidePanel', app.CJBaseHelper.showHidePanel, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.$('div[data-panelname="hidden_panel"]').hide();
        this.$('div[data-panelname="LBL_RECORDVIEW_PANEL4"]').hide();
        this.hideOrShowFields();
        this.setSmartGuideTemplate();
    },

    /**
     * Set the Smart Guide template
     * on opening of create view from
     * subpanels
     */
    setSmartGuideTemplate: function() {
        let smartGuideID = '';
        let smartGuideName = '';
        if (
            this.context &&
            this.context.parent &&
            this.context.parent.get('model')
        ) {
            let parentModule = this.context.parent.get('module');
            if (_.contains(['DRI_SubWorkflow_Templates', 'DRI_Workflow_Task_Templates'], parentModule)) {
                smartGuideID = this.context.parent.get('model').get('dri_workflow_template_id');
                smartGuideName = this.context.parent.get('model').get('dri_workflow_template_name');
            } else if (_.isEqual(parentModule, 'DRI_Workflow_Templates')) {
                smartGuideID = this.context.parent.get('model').get('id');
                smartGuideName = this.context.parent.get('model').get('name');
            }

            if (this.model) {
                this.model.set('smart_guide_template_id', smartGuideID);
                this.model.set('smart_guide_template_name', smartGuideName);

                this.setParentField(smartGuideID);
            }
        }
    },

    /**
     * Update the parent flex relate
     * field with the selected template
     * as for sugar_action_to_smart_guide, Forms
     * record will always be linked against
     * Workflow Templates.
     */
    setParentField: function(smartGuideID = '') {
        if (_.isEqual(this.model.get('main_trigger_type'), 'sugar_action_to_smart_guide')) {
            if (_.isEmpty(smartGuideID)) {
                smartGuideID = this.model.get('smart_guide_template_id');
            }

            this.model.set('parent_type', 'DRI_Workflow_Templates');
            this.model.set('parent_id', smartGuideID);
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (this.model) {
            this.model.on('change:main_trigger_type', function() {
                this._renderSpecificField('parent_name');
                this._renderSpecificField('trigger_event');
                this._renderSpecificField('trigger_label');
                this._renderSpecificField('target_action_label');
                this.hideOrShowFields();
            }, this);

            this.model.on('change:smart_guide_template_id', function() {
                this._renderSpecificField('module_trigger');
                this._triggerFieldEvent('target_action');
                this.setParentField();
            }, this);
        }
    },

    /**
     * Render the field on view
     */
    _renderSpecificField: function(fieldName) {
        if (_.isEmpty(fieldName)) {
            return;
        }

        let field = this.getField(fieldName);

        if (field) {
            field.render();
        }
    },

    /**
     * Trigger event of specific field
     *
     * @param {string} fieldName
     */
    _triggerFieldEvent: function(fieldName) {
        if (_.isEmpty(fieldName)) {
            return;
        }

        let field = this.getField(fieldName);

        if (field) {
            field.trigger('view:smart_guide_id:changes', this.model.get('smart_guide_template_id'));
        }
    },

    /**
     * Show or hide fields on the base of main trigger type
     */
    hideOrShowFields: function() {
        let fields = ['date_entered_by', 'date_modified_by', 'field_trigger'];
        let triggerType = this.model.get('main_trigger_type');

        _.each(fields, function(fieldname) {
            let field = this.getField(fieldname);

            if (field) {
                let fieldEle = field.getFieldElement().closest('.record-cell');
                let showField = !_.isEmpty(triggerType);

                // show field_trigger if main_trigger_type is sugar_action_to_smart_guide
                if (showField && _.isEqual(fieldname, 'field_trigger')) {
                    showField = _.isEqual(triggerType, 'sugar_action_to_smart_guide');
                }

                showField ? fieldEle.show() : fieldEle.hide();
            }
        }, this);
    },

    /**
     * @inheritdoc
     */
    validateModelWaterfall: function(callback) {
        let fields = this.getFields(this.module);
        let populateFields = this.getField('populate_fields');
        let targetActionField = this.getField('target_action');
        let isValidField = true;

        if (populateFields) {
            fields = _.extend({}, fields, populateFields.addedFieldsDefs);
        }

        if (targetActionField) {
            fields = _.extend({}, fields, targetActionField.populateFieldsMetaData);
            fields = _.extend({}, fields, targetActionField.actionFieldsMetaData);
            isValidField = targetActionField._validateField();
        }

        this.model.doValidate(fields, function(isValid) {
            // if model is valid but target_action field is not valid
            if (isValid && !isValidField) {
                app.alert.show('field_validation_error', {
                    level: 'error',
                    messages: 'ERR_RESOLVE_ERRORS'
                });

                isValid = isValidField;
            }

            callback(!isValid);
        });
    },
});

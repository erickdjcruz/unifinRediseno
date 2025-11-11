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
 * @class View.Views.Base.CJForms.RecordView
 * @alias SUGAR.App.view.views.CJFormsRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.on('record:showHidePanel', app.CJBaseHelper.showHidePanel, this);
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
                this._hideOrShowFieldTrigger();
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
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.$('div[data-panelname="hidden_panel"]').hide();
        this._hideOrShowFieldTrigger();
    },

    /**
     * Show or hide field_trigger on the base of main trigger type
     */
    _hideOrShowFieldTrigger: function() {
        let field = this.getField('field_trigger');

        if (field) {
            let fieldEle = field.getFieldElement().closest('.record-cell');
            let triggerType = this.model.get('main_trigger_type');

            _.isEqual(triggerType, 'sugar_action_to_smart_guide') ? fieldEle.show() : fieldEle.hide();
        }
    },

    /**
     * @inheritdoc
     */
    hasUnsavedChanges: function() {
        let editableFieldNames = [];
        let setAsEditable = _.bind(function(fieldName) {
            if (fieldName && _.indexOf(this.noEditFields, fieldName) === -1) {
                editableFieldNames.push(fieldName);
            }
        }, this);

        let changedAttributes = this.model.changedAttributes(this.model.getSynced());

        if (this.resavingAfterMetadataSync || _.isEmpty(changedAttributes)) {
            return false;
        }

        // get names of all editable fields on the page including fields in a fieldset
        _.each(this.meta.panels, function(panel) {
            _.each(panel.fields, function(field) {
                if (!field.readonly) {
                    setAsEditable(field.name);
                    if (field.fields && _.isArray(field.fields)) {
                        _.each(field.fields, function(field) {
                            setAsEditable(field.name);
                        });
                    }
                }
            });
        });

        // check whether the changed attributes are among the editable fields
        let unsavedFields = _.intersection(_.keys(changedAttributes), editableFieldNames);

        _.each(unsavedFields, function(val, key) {
            if (val === 'populate_fields') {
                let field = this.getField('populate_fields');
                if (field && !field.hasChanged()) {
                    delete unsavedFields[key];
                    unsavedFields.length = unsavedFields.length - 1;
                }
            }
        },this);

        return !_.isEmpty(unsavedFields);
    },

    /**
     * @inheritdoc
     */
    saveClicked: function() {
        // Disable the action buttons
        this.toggleButtons(false);

        let allFields = this.getFields(this.module, this.model);
        let fieldsToValidate = {};
        let erasedFields = this.model.get('_erased_fields');
        let populateFields = this.getField('populate_fields');
        let targetActionField = this.getField('target_action');

        for (let fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey) &&
                (!_.contains(erasedFields, fieldKey) || this.model.get(fieldKey) || allFields[fieldKey].id_name)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }

        if (populateFields) {
            fieldsToValidate = _.extend({}, fieldsToValidate, populateFields.addedFieldsDefs);
        }

        if (targetActionField) {
            fieldsToValidate = _.extend({}, fieldsToValidate, targetActionField.populateFieldsMetaData);
            fieldsToValidate = _.extend({}, fieldsToValidate, targetActionField.actionFieldsMetaData);
        }

        this.model.doValidate(fieldsToValidate, _.bind(this.validationComplete, this));
    },

    /**
     * @inheritdoc
     */
    validationComplete: function(isValid) {
        this.toggleButtons(true);
        let isValidField = true;
        let targetActionField = this.getField('target_action');

        if (targetActionField) {
            isValidField = targetActionField._validateField();
        }

        if (isValid) {
            if (isValidField) {
                this.handleSave();
            } else {
                app.alert.show('field_validation_error', {
                    level: 'error',
                    messages: 'ERR_RESOLVE_ERRORS'
                });
            }
        }

        if (typeof this.validationCallback === 'function') {
            this.validationCallback(isValid);
        }
    },

    /**
     * Holds a reference to the alert this view triggers
     */
    cancelClicked: function() {
        /**
         * On updating custom fields i.e. populate_fields and target_action field
         * multiple times on edit view and then clicking cancel the model does not
         * revert changed attributes correctly
         */
        let changedAttributes = this.model.changedAttributes(this.model.getSynced());
        this.model.set(changedAttributes, {revert: true, hideDbvWarning: true});
        this._super('cancelClicked');
    },
});

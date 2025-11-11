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
 * @class View.Views.Base.DRIWorkflowTaskTemplates.RecordView
 * @alias SUGAR.App.view.views.DRIWorkflowTaskTemplatesRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

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

        if (this.resavingAfterMetadataSync) {
            return false;
        }

        let changedAttributes = this.model.changedAttributes(this.model.getSynced());

        if (_.isEmpty(changedAttributes)) {
            return false;
        }

        // Get names of all editable fields on the page including fields in a fieldset
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

        // Check whether the changed attributes are among the editable fields
        let unsavedFields = _.intersection(_.keys(changedAttributes), editableFieldNames);

        _.each(unsavedFields, function(val, key) {
            if (val === 'cj_populate_fields') {
                let field = this.getField('cj_populate_fields');
                if (field && !field.hasChanged()) {
                    delete unsavedFields[key];
                    unsavedFields.length = unsavedFields.length - 1;
                }
            }
        }, this);

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
        for (let fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey) &&
                (!_.contains(erasedFields, fieldKey) || this.model.get(fieldKey) || allFields[fieldKey].id_name)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }

        let populateFields = this.getField('populate_fields');
        if (populateFields) {
            fieldsToValidate = _.extend({}, fieldsToValidate, populateFields.addedFieldsDefs);
        }
        this.model.doValidate(fieldsToValidate, _.bind(this.validationComplete, this));
    }
});

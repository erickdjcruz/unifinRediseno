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
 * @class View.Views.Base.DRIWorkflowTaskTemplates.PreviewView
 * @alias SUGAR.App.view.views.DRIWorkflowTaskTemplatesPreviewView
 * @extends View.Views.Base.PreviewView
 */
({
    extendsFrom: 'PreviewView',

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
        this.model.doValidate(fieldsToValidate, _.bind(this.validationComplete, this, true));
    },

    /**
     * @inheritdoc
     */
    validationComplete: function(isValid) {
        this.toggleButtons(true);
        if (isValid) {
            this.handleSave();
        }
        if (typeof this.validationCallback === 'function') {
            this.validationCallback(isValid);
        }
    },
});

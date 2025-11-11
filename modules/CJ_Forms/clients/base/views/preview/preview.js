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
 * @class View.Views.Base.CJ_Forms.PreviewView
 * @alias SUGAR.App.view.views.BaseCJFormsPreviewView
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

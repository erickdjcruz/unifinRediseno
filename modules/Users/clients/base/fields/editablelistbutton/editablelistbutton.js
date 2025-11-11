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
 * @class View.Fields.Base.Users.EditablelistbuttonField
 * @alias SUGAR.App.view.fields.BaseUsersEditablelistbuttonField
 * @extends View.Fields.Base.EditablelistbuttonField
 */
({
    extendsFrom: 'EditablelistbuttonField',

    /**
     * Flag to check if we should navigate to Reassign User Records page
     * {boolean}
     */
    triggerReassignUserRecords: false,

    /**
     * @inheritdoc
     */
    saveClicked: function(evt) {
        let changedAttributes = this.model.changedAttributes();
        if (changedAttributes && changedAttributes.status && changedAttributes.status === 'Inactive') {
            this.triggerReassignUserRecords = true;
        }
        this._super('saveClicked', [evt]);
    },

    /**
     * @inheritdoc
     */
    onSaveComplete: function() {
        this._super('onSaveComplete');
        if (this.triggerReassignUserRecords) {
            this.triggerReassignUserRecords = false;
            app.alert.show('reassign_records', {
                level: 'confirmation',
                messages: app.lang.get('LBL_REASS_CONFIRM_REASSIGN', this.module),
                onConfirm: _.bind(function() {
                    let url = app.bwc.buildRoute('Users', this.model.get('id'), 'reassignUserRecords');
                    app.router.navigate(url, {trigger: true});
                }, this),
                onCancel: function() {
                    return;
                },
            }, this);
        }
    },

    /**
     * @inheritdoc
     */
    _getNoAccessErrorMessage: function(error) {
        if (error.code === 'license_seats_needed' && _.isString(error.message)) {
            return error.message;
        }
        return this._super('_getNoAccessErrorMessage', [error]);
    }
})

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
 * @class View.Views.Base.Users.RecordlistView
 * @alias SUGAR.App.view.views.BaseUsersRecordlistView
 * @extends View.Views.Base.RecordlistView
 */
({
    extendsFrom: 'RecordlistView',

    /**
     * Extend the parent function to add editability checking for IDM
     */
    parseFields: function() {
        _.each(this.meta.panels, function(panel) {
            app.utils.setUsersEditableFields(panel.fields, 'recordlist');
        }, this);

        return this._super('parseFields');
    },

    /**
     * @override
     */
    getDeleteMessages: function(model) {
        let messages = this._super('getDeleteMessages', [model]);
        messages.confirmation = app.lang.get('LBL_DELETE_USER_CONFIRM', this.module);
        return messages;
    },

    /**
     * @override
     */
    deleteModelSuccessCallback: function(model) {
        this._modelToDelete = null;
        this.collection.remove(model, {silent: true});
        app.events.trigger('preview:close');

        this.layout.trigger('list:record:deleted', model);
        let url = app.bwc.buildRoute('Users', model.get('id'), 'reassignUserRecords');
        app.router.navigate(url, {trigger: true});
    },

    /**
     * @inheritdoc
     *
     * Handles IDM alert messaging
     */
    editClicked: function(model, field) {
        this._super('editClicked', [model, field]);

        if (app.config.idmModeEnabled) {
            let message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_REGULAR_USER', this.module);

            // Admin users should see a link to the SugarIdentity user edit page
            if (app.user.get('type') === 'admin') {
                let link = decodeURI(this.meta.cloudConsoleEditUsersLink);

                message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_ADMIN_USER', this.module);
                message = message.replace('%s', link);
            }

            app.alert.show('edit-user-record', {
                level: 'info',
                autoClose: false,
                messages: app.lang.get(message)
            });
        }
    },
})

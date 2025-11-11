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
 * @class View.Views.Base.Users.DashablerecordView
 * @alias SUGAR.App.view.views.BaseUsersDashablerecordView
 * @extends View.Views.Base.DashablerecordView
 */
({
    extendsFrom: 'DashablerecordView',

    /**
     * Flag to check if we should navigate to Reassign User Records page
     * {boolean}
     */
    triggerReassignUserRecords: false,

    /**
     * @inheritdoc
     */
    _setReadonlyFields: function() {
        this._super('_setReadonlyFields');
        _.each(this.meta.panels, function(panel) {
            app.utils.setUsersEditableFields(panel.fields, 'dashablerecord');
        });
    },

    /**
     * @inheritdoc
     */
    completeCallback: function() {
        this._super('completeCallback');
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
    handleSave: function() {
        let changedAttributes = this.model.changedAttributes();
        if (changedAttributes && changedAttributes.status && changedAttributes.status === 'Inactive') {
            this.triggerReassignUserRecords = true;
        }
        this._super('handleSave');
    },

    /**
     * @inheritdoc
     *
     * Handles IDM alert messaging
     */
    editRecord: function() {
        this._super('editRecord');
        if (app.config.idmModeEnabled) {
            let message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_REGULAR_USER', this.module);

            // Admin users should see a link to the SugarIdentity user edit page
            if (app.user.get('type') === 'admin') {
                let link = decodeURI(this.meta.cloudConsoleEditUserLink);
                let linkTemplate = Handlebars.compile(link);
                let url = linkTemplate({
                    record: encodeURIComponent(app.utils.createUserSrn(this.model.get('id')))
                });

                message = app.lang.get('LBL_IDM_MODE_NON_EDITABLE_FIELDS_FOR_ADMIN_USER', this.module);
                message = message.replace('%s', url);
            }

            app.alert.show('edit-user-record', {
                level: 'info',
                autoClose: false,
                messages: app.lang.get(message)
            });
        }
    }
})

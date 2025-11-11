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
 * Custom RecordlistView used within Subpanel layouts.
 *
 * @class View.Views.Base.Contacts.SubpanelForCasesView
 * @alias SUGAR.App.view.views.SubpanelForCasesView
 * @extends View.Views.Base.SubpanelListView
 */
({
    extendsFrom: 'SubpanelListView',

    unlinkPrimary: false,

    /**
     * Formats the messages to display in the alerts when unlinking a record.
     *
     * @override
     * @param {Data.Bean} model The model concerned.
     * @return {Object} The list of messages.
     * @return {string} return.confirmation Confirmation message.
     * @return {string} return.success Success message.
     */
    getUnlinkMessages: function(model) {
        var message = this._super('getUnlinkMessages', [model]);
        var context = this.getMessageContext(model);

        if (this.unlinkPrimary) {
            // Show the message about clearing Primary Conact Field
            // if it's similar with the unlinked Contact
            message.confirmation = app.utils.formatString(
                app.lang.get('NTC_UNLINK_CASES_CONTACT_CONFIRMATION'),
                [context]
            );
        }

        return message;
    },

    /**
     * Show the alert in the time of unlinking a record.
     *
     * @override
     * @param {Data.Bean} model The model concerned.
     */
    showUnlinkMessage: function(model) {
        var recordModel = this.context.parent.children[0].parent.get('model');
        var contactField = recordModel.fields.primary_contact_name;

        this.unlinkPrimary =
            (recordModel.get('primary_contact_id') === model.get('id')) ||
            (recordModel.previous('primary_contact_id') === model.get('id'));

        if (this.unlinkPrimary && contactField.required === true) {
            var context = this.getMessageContext(model);

            // Show message about it's not possible to unlink the Primary Contact
            // if it's a required field
            app.alert.show('unlink_error', {
                level: 'error',
                messages: app.utils.formatString(
                    app.lang.get('NTC_UNLINK_CASES_CONTACT_ERROR'),
                    [context]
                ),
            });

            this._modelToUnlink = null;

            return;
        }

        this._super('showUnlinkMessage', [model]);
    },
})

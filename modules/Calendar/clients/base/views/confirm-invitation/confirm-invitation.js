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
({
    className: 'alert-wrapper calendar-alert',

    extendsFrom: 'AlertView',

    /**
     * @override
     */
    initialize: function(options) {
        this.events = _.extend({}, this.events, {
            'click [data-action=confirm]': 'saveClicked',
            'click [data-action=saveAndSendEmails]': 'saveAndSendEmailsClicked'
        });
        this.context = options.context;

        this._super('initialize', [options]);

        this.name = 'confirm-invitation';
    },

    /**
     * @override
     */
    _getAlertTemplate: function(options, templateOptions) {
        options = options || {};
        let template = app.template.getView('confirm-invitation.email', this.options.module);

        return template();
    },

    /**
     * @override
     */
    cancelClicked: function(event) {
        $('.calendar-alert').remove();
        this.context.trigger('button:cancel:click');
    },

    /**
     * @override
     */
    saveClicked: function(event) {
        $('.calendar-alert').remove();
        this.context.trigger('button:save:click', this.options.event);
    },

    /**
     * Save and send emails handler
     */
    saveAndSendEmailsClicked: function() {
        $('.calendar-alert').remove();
        this.context.trigger('button:saveAndSendInvites:click', this.options.event);
    }
});

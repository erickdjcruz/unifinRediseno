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
 * @class View.Views.Base.Messages.ActivityCardContentView
 * @alias SUGAR.App.view.views.MessagesActivityCardContentView
 * @extends View.Views.Base.ActivityCardContentView
 */
({
    extendsFrom: 'ActivityCardContentView',

    /**
     * Return formatted date of the end chat
     */
    getMessageDate: function() {
        return this.activity.get('date_end') ?
            app.date(this.activity.get('date_end')).formatUser() :
            '';
    },

    /**
     * Get the status message
     *
     * @return {String}
     */
    getStatusMessage: function() {
        var message = '';
        var statusString = '';

        switch(this.activity.get('status')) {
            case 'Completed':
                statusString = app.lang.get('LBL_ACTIVITY_FINISHED', this.module);
                break;
            default:
                statusString = app.lang.get('LBL_ACTIVITY_IN_PROGRESS', this.module);
        }

        message = '(' +
            app.lang.get('LBL_ACTIVITY_STATUS_TEXT', this.module) +
            ' ' +
            statusString +
            ') ' +
            this.getMessageDate();

        return message;
    },

    /**
     * Set the status message in the status panel
     *
     * This function manipulates the DOM, so must be invoked after any render call
     */
    setStatusMessage: function() {
        var $panel = this.$el.find('.panel-status');

        if ($panel && $panel.length) {
            $panel.append(this.getStatusMessage());
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.setStatusMessage();
    }
})

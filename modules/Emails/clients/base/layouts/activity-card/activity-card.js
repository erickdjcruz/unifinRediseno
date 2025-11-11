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
 * @class View.Layouts.Base.Emails.ActivityCardLayout
 * @alias SUGAR.App.view.layouts.BaseEmailsActivityCardLayout
 * @extends View.Layout.Base.ActivityCardLayout
 */
({
    extendsFrom: 'ActivityCardLayout',

    /**
     * Constant representing the state of an email when it is a draft.
     *
     * @property {string}
     */
    STATE_DRAFT: 'Draft',

    /**
     * Constant representing the state of an email when it is a draft.
     *
     * @property {string}
     */
    STATE_ARCHIVED: 'Archived',

    /**
     * @inheritdoc
     *
     * Hides the Forward, Reply, and Reply All icons if the email card is a draft.
     */
    setCardMenuVisibilities: function() {
        // if the email card is a draft
        if (this.model && this.model.get('state') === this.STATE_DRAFT) {
            this.$('.cabmenu .activity-card-emailaction').hide();
        }
    },

    /**
     * Returns the created date for the record based on the state of the email
     *
     * @param state
     * @return {string|null}
     */
    getCreatedDate: function(state) {
        if (state === this.STATE_ARCHIVED) {
            return this.model.get('date_sent');
        } else if (state === this.STATE_DRAFT) {
            return this.model.get('date_entered');
        }
        return '';
    }
})

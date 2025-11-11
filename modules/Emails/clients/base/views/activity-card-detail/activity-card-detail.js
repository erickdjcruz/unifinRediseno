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
 * @class View.Views.Base.Emails.ActivityCardDetailView
 * @alias SUGAR.App.view.views.BaseEmailsActivityCardDetailView
 * @extends View.Views.Base.ActivityCardDetailview
 */
({
    extendsFrom: 'ActivityCardDetailview',

    /**
     * The state of the email
     */
    state: null,

    /**
     * Initializes hbs date variables with date_entered
     */
    initDateDetails: function() {
        if (this.activity) {
            this.state = this.activity.get('state');
            const activityCard = this.getActivityCardLayout();
            this.setDateDetails(activityCard.getCreatedDate(this.state));
            this.detailDateTimeTooltip = (this.state === this.STATE_ARCHIVED) ?
                'LBL_DATE_SENT' :
                'LBL_DATE_CREATED';
        }
    },
})

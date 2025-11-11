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
 * @class View.Views.Base.Emails.ActivityCardContentView
 * @alias SUGAR.App.view.views.BaseEmailsActivityCardContentView
 * @extends View.Views.Base.ActivityCardContentView
 */
({
    extendsFrom: 'ActivityCardContentView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.initAttachmentDetails('attachments_collection');
        this.listenTo(this, 'tinymce:resize', this.toggleShowMore);
    },

    /**
     * Initializes hbs date variables with date_modified
     */
    initDateDetails: function() {
        if (!this.activity) {
            return;
        }
        const state = this.activity.get('state');
        const activityCard = this.getActivityCardLayout();
        let detailDate = app.date(activityCard.getCreatedDate(state));
        let dateModified = app.date(this.activity.get('date_modified'));

        if (detailDate.isValid() && dateModified.isValid()) {
            detailDate = detailDate.formatUser();
            dateModified = dateModified.formatUser();

            if (detailDate !== dateModified) {
                this.dateModified = dateModified;
            }
        }
    },
})

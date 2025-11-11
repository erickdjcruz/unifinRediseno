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
 * @class View.Views.Portal.Emails.ActivityCardHeaderView
 * @alias SUGAR.App.view.views.PortalEmailsActivityCardHeaderView
 * @extends View.Views.Base.Emails.ActivityCardHeaderView
 */
({
    extendsFrom: 'BaseEmailsActivityCardHeaderView',

    /**
     * This function sets this.from_email or this.to_email based on type.
     * It gets the value from either this.activity.from_collection.models or this.activity.to_collection.models.
     * @param type 'from' or 'to'
     */
    setEmailString: function(type) {
        var collectionField = type + '_collection';
        var emailField = type + '_email';
        _.each(this.activity.get(collectionField).models, function(model) {
            this[emailField] =
                this[emailField] ? this[emailField] + ',' + model.get('email_address') : model.get('email_address');
        }, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.setEmailString('from');
        this.setEmailString('to');

        if (this.from_email && this.to_email) {
            this.hasAvatarUser = true;
        }

        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    setUsersFields: function() {
        // Don't need to do anything for portal
        return;
    }
})

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
 * @class View.Views.Base.Emails.ActivityCardHeaderView
 * @alias SUGAR.App.view.views.BaseEmailsActivityCardHeaderView
 * @extends View.Views.Base.ActivityCardHeaderView
 */
({
    extendsFrom: 'ActivityCardHeaderView',

    /**
     * @inheritdoc
     */
    setUsersFields: function() {
        var panel = this.getUsersPanel();

        const fieldsToDisplay = ['from', 'to', 'cc', 'bcc'];

        fieldsToDisplay.map((name) => {
            this[`${name}Field`] = _.find(panel.fields, (field) =>
                field.name === `${name}_collection`
            );
        });

        this.hasAvatarUser = !!this.fromField && !!this.toField;
    }
})

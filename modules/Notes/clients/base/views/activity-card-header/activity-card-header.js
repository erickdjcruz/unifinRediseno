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
 * @class View.Views.Base.Notes.ActivityCardHeaderView
 * @alias SUGAR.App.view.views.BaseNotesActivityCardHeaderView
 * @extends View.Views.Base.ActivityCardHeaderView
 */
({
    extendsFrom: 'ActivityCardHeaderView',

    /**
     * @inheritdoc
     */
    setUsersFields: function() {
        var panel = this.getUsersPanel();
        this.userField = _.find(panel.fields, function(field) {
            return field.name === 'modified_by_name';
        });

        this.hasAvatarUser = !!this.userField;
    }
})

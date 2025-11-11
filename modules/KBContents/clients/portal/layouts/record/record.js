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
 * Portal KBContents Record layout.
 *
 * @class View.Layouts.Portal.KBContents.PortalRecordLayout
 * @alias SUGAR.App.view.layouts.PortalKBContentsRecordLayout
 * @extends View.Layouts.Portal.PortalRecordLayout
 */
({
    extendsFrom: 'RecordLayout',

    /**
     * @inheritdoc
     */
    render: function() {
        var activity = this.getComponent('sidebar').getComponent('main-pane').getComponent('activity');
        if (activity) {
            activity.hideActivity = this.shouldHideActivity();
        }
        this._super('render');
    },

    /**
     * Check if we should hide notes
     * @return {boolean}
     */
    shouldHideActivity: function() {
        return !_.isUndefined(app.config.showKBNotes) &&
            app.config.showKBNotes === 'disabled';
    }
})

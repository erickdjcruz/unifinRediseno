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
 * @class View.Views.Base.DRI_Workflows.RecdordListView
 * @alias SUGAR.App.view.views.DRI_WorkflowsRecdordListView
 * @extends View.Views.Base.BaseView
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (!app.user.hasAutomateLicense()) {
            app.CJBaseHelper.invalidLicenseError();
        }
    },
});

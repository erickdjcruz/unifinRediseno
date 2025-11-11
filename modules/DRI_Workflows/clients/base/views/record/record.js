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
 * @class View.Views.Base.DRI_Workflows.RecordView
 * @alias SUGAR.App.view.views.DRI_WorkflowsRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.saveCallback = this.saveCallbackCustom;
        this.model.fields.dri_workflow_template_name.readonly = true;
        this.model.fields.dri_workflow_template_id.readonly = true;

        if (!app.user.hasAutomateLicense()) {
            app.CJBaseHelper.invalidLicenseError();
        }

        let route;
        if (app.routeBackTo) {
            route = app.routeBackTo;
            delete app.routeBackTo;
        }

        if (route) {
            this.cancelCallback = function() {
                app.router.navigate(route, {trigger: true});
            };

            const timeoutInterval = 3000;
            this.saveCallback = function() {
                setTimeout(function() {
                    app.router.navigate(route, {trigger: true});
                }, timeoutInterval);
            };
        }
    },

    /**
    * @inheritdoc
    */
    saveCallbackCustom: function() {
        this._super('saveCallback');
        this.model.trigger('workflow-template:hide-show:click', null);
    },
});

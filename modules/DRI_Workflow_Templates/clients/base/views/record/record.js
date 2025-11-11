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
 * @class View.Views.elseifBase.DRI_Workflow_Templates.RecordView
 * @alias SUGAR.App.view.views.DRI_Workflow_TemplatesRecordView
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
        this.listenTo(this.context, 'button:export_button:click', this.exportClicked);

        if (!app.user.hasAutomateLicense()) {
            app.CJBaseHelper.invalidLicenseError();
        }
    },

    /**
     * Handles the scenario when export button is clicked for the Smart Guide template
     */
    exportClicked: function() {
        app.alert.show('massexport_loading', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
        });

        app.api.fileDownload(
            app.api.buildURL(this.module, 'export', {id: this.model.id}, {platform: 'base'}),
            {
                complete: function() {
                    app.alert.dismiss('massexport_loading');
                },
            },
            {iframe: this.$el}
        );
    },

    /**
     * Delete the model once the user confirms the action
     *
     * @return {undefined}
     */
    deleteModel: function() {
        this.model.destroy({
            // Show alerts for this request
            showAlerts: {
                process: true,
                success: {messages: this.getDeleteMessages().success},
            },
            success: _.bind(function() {
                let redirect = this._targetUrl !== this._currentUrl;
                this.context.trigger('record:deleted', this._modelToDelete);
                this._modelToDelete = false;

                if (redirect) {
                    this.unbindBeforeRouteDelete();
                    // Replace the url hash back to the current staying page
                    app.router.navigate(this._targetUrl, {trigger: true});
                    return;
                }
                app.router.navigate(this.module, {trigger: true});
            }, this),

            error: _.bind(function(model, error) {
                this._modelToDelete = false;
            }, this),
        });
    },

    /**
    * @inheritdoc
    */
    saveCallbackCustom: function() {
        this._super('saveCallback');
        this.model.trigger('workflow-template:hide-show:click', null);
    },
});

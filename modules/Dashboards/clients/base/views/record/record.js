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
 * @class View.Views.Base.Dashboards.RecordView
 * @alias SUGAR.App.view.views.BaseDashboardsRecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * @inheritdoc
     * Additionaly it makes the metadata (dashboard components description) available through the record call;
     * it adds the FilterSharing plugin to the list of plugins used in the view.
     */
    initialize: function(options) {
        this.options.context.addFields(['metadata']);
        this.plugins = _.union((this.plugins || []), ['FilterSharing']);
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     * Additionaly it calls the method responsible for sharing the filters used
     * on a list view dashlet with the teams the dashboard is shared with.
     */
    _saveModel: function() {
        var options;
        var successCallback = _.bind(function() {
            this.triggerListviewFilterUpdate();
            // Loop through the visible subpanels and have them sync. This is to update any related
            // fields to the record that may have been changed on the server on save.
            _.each(this.context.children, function(child) {
                if (child.get('isSubpanel') && !child.get('hidden')) {
                    if (child.get('collapsed')) {
                        child.resetLoadFlag({recursive: false});
                    } else {
                        child.reloadData({recursive: false});
                    }
                }
            });
            if (this.createMode) {
                app.navigate(this.context, this.model);
            } else if (!this.disposed && !app.acl.hasAccessToModel('edit', this.model)) {
                //re-render the view if the user does not have edit access after save.
                this.render();
            }
        }, this);
        var errorCallBack = _.bind(function(model, error) {
            if (error.status === 412 && !error.request.metadataRetry) {
                this.handleMetadataSyncError(error);
            } else if (error.status === 409) {
                app.utils.resolve409Conflict(error, this.model, _.bind(function(model, isDatabaseData) {
                    if (model) {
                        if (isDatabaseData) {
                            successCallback();
                        } else {
                            this._saveModel();
                        }
                    }
                }, this));
            } else if (error.status === 403 || error.status === 404) {
                this.alerts.showNoAccessError.call(this);
            } else {
                this.editClicked();
            }
        }, this);

        //Call editable to turn off key and mouse events before fields are disposed (SP-1873)
        this.turnOffEvents(this.fields);

        options = {
            showAlerts: true,
            success: successCallback,
            error: errorCallBack,
            lastModified: this.model.get('date_modified'),
            viewed: true
        };

        // ensure view and field are sent as params so collection-type fields come back in the response to PUT requests
        // (they're not sent unless specifically requested)
        options.params = options.params || {};
        if (this.context.has('dataView') && _.isString(this.context.get('dataView'))) {
            options.params.view = this.context.get('dataView');
        }

        if (this.context.has('fields')) {
            options.params.fields = this.context.get('fields').join(',');
        }

        options = _.extend({}, options, this.getCustomSaveOptions(options));

        this.model.save({}, options);
    },

    /**
     * @inheritdoc
     */
    getDeleteMessages: function() {
        var messages = {};
        var modelName = app.lang.get(this.model.get('name'), this.model.get('dashboard_module'));

        messages.confirmation = app.lang.get('LBL_DELETE_DASHBOARD_CONFIRM', this.module, {name: modelName});
        messages.success = app.lang.get('LBL_DELETE_DASHBOARD_SUCCESS', this.module, {
            name: modelName
        });

        return messages;
    }
})

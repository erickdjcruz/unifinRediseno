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
 * @class View.Views.Base.DRI_Workflows.CreateView
 * @alias SUGAR.App.view.views.DRI_WorkflowsCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.model.on('change:dri_workflow_template_id', this._changeTemplate, this);

        this.alerts = _.extend({}, this.alerts, {
            parentNotFoundException: function(error) {
                if (!this instanceof app.view.View) {
                    app.logger.error('This method should be invoked by Function.prototype.call(),' +
                        'passing in as argument an instance of this view.');
                    return;
                }
                var name = 'error';
                this._viewAlerts.push(name);
                app.alert.show(name, {
                    level: 'error',
                    messages: error.message ? error.message : 'Could not find Parent',
                    autoCloseDelay: 9000
                });
            }
        });
    },

    /**
     * Added our custom alert message in options.error
     * @param {type} options
     * @return {Array}
     */
    getCustomSaveOptions: function(options) {
        options.error = _.bind(function(model, error) {
            if (error.status == 404) {
                this.alerts.parentNotFoundException.call(this, error);
                this.enableButtons();
            }
        }, this);
        return {};
    },

    /**
     * @private
     */
    _changeTemplate: function(model) {
        this.getTemplate().xhr.done(function(arrs) {
            const template = app.data.createBean('DRI_Workflow_Templates', arrs);
            model.set({
                name: template.get('name'),
                type: template.get('type'),
                description: template.get('description'),
                team_name: template.get('team_name'),
                progress: template.get(0),
            });
        });
    },

    /**
     * @return {*} Returns a SUGAR.Api.HttpRequest object
     */
    getTemplate: function() {
        var url = app.api.buildURL('DRI_Workflow_Templates', 'read', {
            id: this.model.get('dri_workflow_template_id')
        });

        return app.api.call('view', url);
    },

})

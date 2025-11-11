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
 * @class View.Views.Base.DRISubWorkflowTemplates.CreateView
 * @alias SUGAR.App.view.views.DRISubWorkflowTemplatesCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(this.model, 'change:dri_workflow_template_id', this.setSortOrder);
        this.setSortOrder();
    },

    /**
     * Set the sort order from the last stage
     *
     * @return {undefined}
     */
    setSortOrder: function() {
        if (!this.model.get('dri_workflow_template_id') || this.model.get('sort_order')) {
            return;
        }

        let url = app.api.buildURL('DRI_Workflow_Templates', 'last-stage', {
            id: this.model.get('dri_workflow_template_id'),
        });

        app.api.call('read', url, null, {
            success: _.bind(function(data) {
                this.model.set('sort_order', data.sort_order + 1);
            }, this),
            error: _.bind(function() {
                this.model.set('sort_order', 1);
            }, this)
        });
    },
});

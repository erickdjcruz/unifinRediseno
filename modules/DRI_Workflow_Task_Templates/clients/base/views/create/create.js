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
 * @class View.Views.Base.DRIWorkflowTaskTemplates.CreateView
 * @alias SUGAR.App.view.views.DRIWorkflowTaskTemplatesCreateView
 * @extends View.Views.Base.CreateView
 */
({
    extendsFrom: 'CreateView',

    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(this.model, 'change:dri_subworkflow_template_id', this.setSortOrder);
        this.listenTo(this.model, 'change:dri_subworkflow_template_id', this.setRelatedJourneyTemplate);
        this.setSortOrder();
        this.setRelatedJourneyTemplate();
    },

    /**
     * Set the sort order from the last task
     *
     * @return {undefined}
     */
    setSortOrder: function() {
        if (!this.model.get('dri_subworkflow_template_id') || this.model.get('sort_order')) {
            return;
        }

        let url = app.api.buildURL('DRI_SubWorkflow_Templates', 'last-task', {
            id: this.model.get('dri_subworkflow_template_id'),
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

    /**
     * Set the Smart Guide template for the model, if stage template exists in the model
     *
     * @return {undefined}
     */
    setRelatedJourneyTemplate: function() {
        if (!this.model.get('dri_subworkflow_template_id')) {
            return;
        }

        let stage = app.data.createBean('DRI_SubWorkflow_Templates', {
            id: this.model.get('dri_subworkflow_template_id'),
        });

        stage.fetch({
            success: _.bind(function() {
                this.model.set('dri_workflow_template_id', stage.get('dri_workflow_template_id'));
            }, this)
        });
    },

    /**
     * @inheritdoc
     */
    validateModelWaterfall: function(callback) {
        let fields = this.getFields(this.module);
        let populateFields = this.getField('populate_fields');
        if (populateFields) {
            fields = _.extend({}, fields, populateFields.addedFieldsDefs);
        }
        this.model.doValidate(fields, function(isValid) {
            callback(!isValid);
        });
    },
});

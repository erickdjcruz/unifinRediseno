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
 * @class View.Fields.Base.CjDRIWorkflowTaskTemplatesSelectToGuestsField
 * @alias SUGAR.App.view.fields.BaseCjDRIWorkflowTaskTemplatesSelectToGuestsField
 * @extends View.Fields.Base.CjSelectToField
 */
 ({
    extendsFrom: 'CjSelectToField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'cj-select-to';
    },

    /**
     * @inheritdoc
     */
    _getAvailableModulesApiURL: function() {
        if (_.isEmpty(this.model.get('dri_workflow_template_id'))) {
            // should empty the values of all enum fields.
            return;
        }

        return app.api.buildURL(
            'DRI_Workflow_Task_Templates',
            'available-modules',
            null,
            {template_id: this.model.get('dri_workflow_template_id')}
        );
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        if (this.model) {
            this.listenTo(this.model, 'change:activity_type', this._renderEnum);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening(this.model);
        this._super('_dispose');
    },
})

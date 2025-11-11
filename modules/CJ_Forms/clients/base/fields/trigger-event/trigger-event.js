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
 * @class View.Fields.Base.CJForms.TriggerEventField
 * @alias SUGAR.App.view.fields.BaseCJFormsTriggerEventField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',
    itemsMap: {
        'DRI_Workflow_Task_Templates': {
            '': '',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'not_applicable': 'Not Applicable',
        },
        'DRI_SubWorkflow_Templates': {
            '': '',
            'in_progress': 'In Progress',
            'completed': 'Completed',
        },
        'DRI_Workflow_Templates': {
            '': '',
            'completed': 'Completed',
        },
    },

    /**
     * @override
     */
    _loadTemplate: function() {
        this.type = 'enum';
        this._super('_loadTemplate');
    },

    /**
     * @override
     * @protected
     * @chainable
     */
    _render: function() {
        this.prepareItems();
        this._super('_render');
    },

    /**
     * Prepare the items object and render
     * the field
     */
    prepareItems: function() {
        this.items = {};
        let parentType = this.model.get('parent_type');
        let items = this.itemsMap[parentType];

        if (!_.isEmpty(items) && !_.isUndefined(items)) {
            this.items = items;
        }
    }
});

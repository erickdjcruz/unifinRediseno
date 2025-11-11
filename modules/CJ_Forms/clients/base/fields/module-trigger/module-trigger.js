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
 * @class View.Fields.Base.CJForms.ModuleTriggerField
 * @alias SUGAR.App.view.fields.BaseCJFormsModuleTriggerField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',
    templatesCollection: {},

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
     * Return the Workflow template record
     */
    getTemplateID: function() {
        return (this.model) ? this.model.get('smart_guide_template_id') : '';
    },

    /**
     * @inheritdoc
     */
    loadEnumOptions: function(fetch, callback, error) {
        this.items = {};

        if (_.isEmpty(this.templatesCollection) || this.templatesCollection.length == 0) {
            let templatesCollection = app.data.createBeanCollection('DRI_Workflow_Templates');
            templatesCollection.fetch({
                fields: ['id', 'available_modules'],
                limit: -1,
                success: _.bind(function(data) {
                    this.templatesCollection = templatesCollection;
                    this.render();
                },this)
            });
        }
    },

    /**
     * Prepare the items object and render
     * the field
     */
    prepareItems: function() {
        this.items = {};
        if (_.isEmpty(this.templatesCollection)) {
            return;
        }

        let templateID = this.getTemplateID();
        if (_.isEmpty(templateID)) {
            this.model.unset('module_trigger');
            return;
        }

        let template = this.templatesCollection.get(templateID);
        if (_.isEmpty(template)) {
            return;
        }

        this.items = {'': ''};
        let availableModules = template.get('available_modules');

        _.each(availableModules, function(module) {
            this.items[module] = module;
        }, this);

        if (!_.includes(availableModules, this.model.get('module_trigger'))) {
            this.model.unset('module_trigger');
        }
    }
});

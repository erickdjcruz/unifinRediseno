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
 * The formula builder used in  tag builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderFormulasView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderFormulasView
 */
({
    /**
     * The preview string
     *
     * @var string
     */
    preview: '',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);
        this.initTag();
        this.listenTo(this.context, 'change:currentModule', this._render, this);
    },

    initTag: function() {
        let tagBuilderFactory = new App.utils.DocumentMerge.TagBuilderFactory();
        let tagBuilder = tagBuilderFactory.getTagBuilder('directive');
        let tag = tagBuilder.newTag().setName('formula').get();
        this.tag = tag;
    },

    /**
     * @inheritdoc
     */
    _render: function(view, selectedModule) {
        this._super('_render');

        this._buildFormulaBuilderField(selectedModule);
    },

    /**
     * creates a new formula builder field and appends it to the current element
     *
     * @param {string} module
     */
    _buildFormulaBuilderField: function(module) {
        this.formulaField = app.view.createField({
            view: this,
            viewName: 'edit',
            targetModule: module || this.module,
            callback: _.bind(this.formulaChanged, this),
            formula: '',
            def: {
                type: 'formula-builder',
                name: 'formula-builder',
            },
        });
        this.formulaField.render();

        this.$('.row-fluid.builder').append(this.formulaField.$el);
    },

    /**
     * Update the preview each time the formula changes
     *
     * @param {string} formula
     */
    formulaChanged: function(formula) {
        this.tag.setAttribute({'value': formula});
        let tagValue = this.tag.compile().getTagValue();
        this.$('.preview').html(tagValue);
        this.$('.preview').attr('value', tagValue);
    },
});

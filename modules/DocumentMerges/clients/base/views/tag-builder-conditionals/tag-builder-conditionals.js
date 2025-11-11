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
 * The conditonal tag builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderConditionalsView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderConditionalsView
 */
({
    /**
     * The condition part of the if statement.
     * @var string
     */
    condition: '',

    /**
     * The result part of the if statement.
     * @var string
     */
    conditionResult: '',

    /**
     * The array of multiple else if conditions.
     * @var array
     */
    elses: [
        {
            condition: '',
            result: '',
        },
    ],

    /**
     * The condition part of the else if statement.
     * @var string
     */
    elseCondition: '',

    /**
     * The result part of the else if statement.
     * @var string
     */
    elseConditionResult: '',

    /**
     * The object containing the full statement.
     * @var Object
     */
    conditionalObject: {
        if: {
            condition: '',
            result: '',
        },
        elseifs: [],
        else: {
            condition: '',
            result: ''
        }
    },

    /**
     * The tag preview.
     * @var string
     */
    preview: '',

    /**
     * @inheritdoc
     */
    events: {
        'change [name=condition]': 'setConditionalTagValues',
        'change [name=conditionResult]': 'setConditionalTagValues',
        'change [name=elseConditionResult]': 'setConditionalTagValues',
        'change [name=elseIfCondition]': 'setElseIfConditionalTagValues',
        'change [name=elseIfConditionResult]': 'setElseIfConditionalTagValues',
        'click .addElse': 'addElse',
        'click .removeElse': 'removeElse',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        const conditionBuilder = new app.utils.DocumentMerge.TagBuilderFactory().getTagBuilder('conditional');
        this.conditionalTag = conditionBuilder.newTag();
        this.conditionalTag.setName('conditional');
    },

    /**
     * Sets the elseif conditional values on the tag.
     * @param {Event} evt
     */
    setElseIfConditionalTagValues: function(evt) {
        const block = evt.target.getAttribute('block');
        const statement = evt.target.getAttribute('statement');
        const index = evt.target.getAttribute('data-index');
        this.elses[index][block] = evt.target.value;

        if (!this.conditionalObject.elseifs[index]) {
            this.conditionalObject.elseifs[index] = {};
        }

        this.conditionalObject[statement][index] = this.elses[index];

        this.updateTag();
    },

    /**
     * Sets the conditional values on the tag.
     * @param {Event} evt
     */
    setConditionalTagValues: function(evt) {
        const conditionName = evt.target.getAttribute('name');
        const block = evt.target.getAttribute('block');
        const statement = evt.target.getAttribute('statement');
        this[conditionName] = evt.target.value;
        this.conditionalObject[statement][block] = this[conditionName];

        this.updateTag();
    },

    /**
     * Updates the tag.
     */
    updateTag: function() {
        let tag = this.conditionalTag.setAttributes(this.conditionalObject).get();
        this.preview = tag.compile().getTagValue();

        this.render();
    },

    /**
     * Adds an additional else block.
     * @param {Event} evt
     */
    addElse: function(evt) {
        this.elses.push({
            condition: '',
            result: '',
        });

        this.render();
    },

    /**
     * Remove the selected ifelse statement.
     * @param {Event} evt
     */
    removeElse: function(evt) {
        let elseIndex = evt.currentTarget.getAttribute('elseIndex');
        this.elses.splice(elseIndex, 1);

        this.render();
    },

    /**
     * Set the value of the hidden input
     * so we can copy to clipboard
     *
     * @param {Event} evt
     */
    setFieldCopyTargetValue: function(evt) {
        let selectedOption = evt.target.options[evt.target.selectedIndex];
        this.$('#moduleFieldsList').attr('value', selectedOption.value);
    }
});

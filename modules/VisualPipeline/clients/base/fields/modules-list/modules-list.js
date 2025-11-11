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
 * @class View.Fields.Base.VisualPipeline.ModulesListField
 * @alias SUGAR.App.view.fields.BaseVisualPipelineModulesListField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    plugins: ['EllipsisInline'],

    /**
     * HTML tag of the append tag checkbox.
     *
     * @property {string}
     */
    appendTagInput: 'input[name=append_tag]',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        var items = {};
        if (options.def.name === 'enabled_modules') {
            items = this.context.get('allowedModules');
        }
        if (options.def.name === 'tile_body_fields') {
            var tabContent = this.model.get('tabContent');
            items = tabContent.fields;
        }
        this.items = items;
    },

    /**
     * @inheritdoc
     */
    getSelect2Options: function(optionsKeys) {
        optionsKeys = this._super('getSelect2Options', [optionsKeys]);

        if (this.name === 'enabled_modules') {
            optionsKeys.formatSelection = this.formatSelect2Selection;
            optionsKeys.formatResult = this.formatSelect2Result;
        }

        return optionsKeys;
    },

    /**
     * Formats a dropdown selection
     *
     * @param {Object} state The id and text object
     * @return {string} HTML to use for the item
     */
    formatSelect2Selection: function(state) {
        return '<span class="enabled-module-item" data-module="' + state.id + '">' + state.text + '</span>';
    },

    /**
     * Formats a dropdown result
     *
     * @param {Object} state The id and text object
     * @return {string} HTML to use for the item
     */
    formatSelect2Result: function(state) {
        return '<span class="enabled-module-result-item" data-module="' + state.id + '">' + state.text + '</span>';
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.items = this._sortModuleNamesAlphabetical(this.items);

        this._super('_render');
        if (this.name === 'enabled_modules') {
            this.attachEvents();
        }

        if (this.name === 'enabled_modules') {
            // add the data module to the li
            _.each(this.$('.select2-search-choice'), function(el) {
                var $el = $(el);
                $el.attr('data-module', $el.find('span').data('module'));
                $el.addClass('enabled-module-item');
            }, this);
        }
    },

    /**
     * Set up events for the field to add and remove items from the collection.
     */
    attachEvents: function() {
        this.handleRemoveItemHandler = _.bind(this._handleRemoveItemFromCollection, this);
        this.handleAddItemHandler = _.bind(this._handleAddItemToCollection, this);

        this.$el.on('select2-removed', this.handleRemoveItemHandler);
        this.$el.on('select2-selecting', this.handleAddItemHandler);
    },

    /**
     * Handles triggering the removal of the model from the collection
     */
    _handleRemoveItemFromCollection: function(e) {
        if (!_.isEmpty(e.val)) {
            this.context.trigger('pipeline:config:model:remove', e.val);
        }
    },

    /**
     * Handles triggering for adding a model from the collection
     */
    _handleAddItemToCollection: function(e) {
        if (!_.isEmpty(e.val)) {
            this.context.trigger('pipeline:config:model:add', e.val);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$el.off('select2-removed', this.handleRemoveItemHandler);
        this.$el.off('select2-selecting', this.handleAddItemHandler);
        this._super('_dispose');
    },

    /**
     * Sorts module names object by property value as name of module
     *
     * @param {Object} obj
     * @return {Object}
     * @private
     */
    _sortModuleNamesAlphabetical: function(obj) {
        let sortedKeys = Object.keys(obj).sort((a, b) => obj[a].localeCompare(obj[b]));
        let sortedObj = {};
        for (let i = 0; i < sortedKeys.length; i++) {
            sortedObj[sortedKeys[i]] = obj[sortedKeys[i]];
        }
        return sortedObj;
    }
});

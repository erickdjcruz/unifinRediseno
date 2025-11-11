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
 * @class View.Layouts.Base.ProductBundles.QuoteDataGroupLayout
 * @alias SUGAR.App.view.layouts.BaseProductBundlesQuoteDataGroupLayout
 * @extends View.Views.Base.Layout
 */
({
    /**
     * @inheritdoc
     */
    tagName: 'tbody',

    /**
     * @inheritdoc
     */
    className: 'quote-data-group',

    /**
     * The colspan value for the list
     */
    listColSpan: 0,

    /**
     * This is the ProductBundle ID from the model set here on the component
     * for easier access by parent layouts
     */
    groupId: undefined,

    /**
     * The Quote Data Group Header view added to this layout
     * @type View.Views.Base.ProductBundles.QuoteDataGroupHeaderView
     */
    quoteDataGroupHeader: undefined,

    /**
     * The Quote Data Group List view added to this layout
     * @type View.Views.Base.ProductBundles.QuoteDataGroupListView
     */
    quoteDataGroupList: undefined,

    /**
     * The Quote Data Group Footer view added to this layout
     * @type View.Views.Base.ProductBundles.QuoteDataGroupFooterView
     */
    quoteDataGroupFooter: undefined,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (options.model.get('default_group')) {
            // for the default group, we only want the quote-data-group-list component
            options.meta = _.clone(options.meta);
            options.meta.components = [{
                view: 'quote-data-group-list'
            }];
        }

        this._super('initialize', [options]);

        // set the groupID to the model ID
        this.groupId = this.model.cid;

        // Initialize the collection
        this._initCollection();

        var listMeta = app.metadata.getView('Products', 'quote-data-group-list');
        if (listMeta && listMeta.panels && listMeta.panels[0].fields) {
            this.listColSpan = listMeta.panels[0].fields.length;
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.model.on('change:product_bundle_items', this.render, this);
        // listen for the currency id to change on the parent record
        this.context.parent.get('model').on('change:currency_id', function(model, value, options) {
            this.model.set({
                currency_id: model.get('currency_id'),
                base_rate: model.get('base_rate')
            });
            this._render();
        }, this);
    },

    /**
     * Sets the collection of bundle line items and sorts its models based on
     * their position attribute, so that line items in the group will show up
     * in the correct order on the Quotes worksheet
     *
     * @private
     */
    _initCollection: function() {
        // Set the collection to the bundle items collection
        this.collection = this.model.get('product_bundle_items');

        // Sort the collection by model position
        this.collection.comparator = function(model) {
            return model.get('position');
        };
        this.collection.sort();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // add the group id to the bundle level tbody
        this.$el.attr('data-group-id', this.groupId);
        this.$el.data('group-id', this.groupId);
        this.$el.attr('data-record-id', this.model.id);
        this.$el.data('record-id', this.model.id);

        // set the product bundle ID on all the QLI/Notes rows
        this.$('tr.quote-data-group-list').attr('data-group-id', this.groupId);
        this.$('tr.quote-data-group-list').data('group-id', this.groupId);
    },

    /**
     * Adds a row model to this layout's collection and, if the row is in edit mode, it adds
     * the row model to the QuoteDataGroupListView's toggledModels object
     *
     * @param {Data.Bean} model The row model that needs to be added to the collection
     * @param {boolean} isRowInEdit Is the row currently in edit mode?
     */
    addRowModel: function(model, isRowInEdit) {
        if (isRowInEdit) {
            this.quoteDataGroupList.toggledModels[model.cid] = model;
        }

        this.collection.add(model, {
            at: model.get('position')
        });
    },

    /**
     * Removes a row model from this layout's collection and, if the row is in edit mode, it removes
     * the row model from the QuoteDataGroupListView's toggledModels object
     *
     * @param {Data.Bean} model The row model that needs to be removed from the collection
     * @param {boolean} isRowInEdit Is the row currently in edit mode?
     */
    removeRowModel: function(model, isRowInEdit) {
        var modelId;

        if (isRowInEdit) {
            modelId = model.get('id');
            if (this.quoteDataGroupList.toggledModels[modelId]) {
                delete this.quoteDataGroupList.toggledModels[modelId];
            }
            if (this.quoteDataGroupList.toggledModels[model.cid]) {
                delete this.quoteDataGroupList.toggledModels[model.cid];
            }
        }

        this.collection.remove(model);
    },

    /**
     * Switches the model of this group and its components
     *
     * @param {Bean} model the new Product Bundles model to use for this group
     */
    switchModel: function(model) {
        // Dispose the old model
        this.model.dispose();

        // Replace the old model and collection on the layout
        this.model = model;
        this.model.on('change:product_bundle_items', this.render, this);
        this._initCollection();

        // Set the group ID of this group layout to be the new model's cid
        this.groupId = this.model.cid;

        // Replace the model and collection on the inner views
        this.quoteDataGroupList.switchModel(model);
        if (!this.model.get('default_group')) {
            this.quoteDataGroupHeader.switchModel(model);
            this.quoteDataGroupFooter.switchModel(model);
        }
    },

    /**
     * Gets a reference to the QuoteDataGroupList being added to the layout
     *
     * @inheritdoc
     */
    addComponent: function(component, def) {
        this._super('addComponent', [component, def]);

        if (component.name === 'quote-data-group-list') {
            this.quoteDataGroupList = component;
        } else if (component.name === 'quote-data-group-footer') {
            this.quoteDataGroupFooter = component;
        } else if (component.name === 'quote-data-group-header') {
            this.quoteDataGroupHeader = component;
        }
    },

    /**
     * Unsets a reference to the QuoteDataGroupList being removed from the layout
     *
     * @inheritdoc
     */
    removeComponent: function(component) {
        this._super('removeComponent', [component]);

        if (component.name === 'quote-data-group-list') {
            this.quoteDataGroupList = null;
        } else if (component.name === 'quote-data-group-footer') {
            this.quoteDataGroupFooter = null;
        } else if (component.name === 'quote-data-group-header') {
            this.quoteDataGroupHeader = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        var model;
        this.quoteDataGroupHeader = null;
        this.quoteDataGroupList = null;
        this.quoteDataGroupFooter = null;

        if (this.context && this.context.parent && this.context.parent.has('model')) {
            model = this.context.parent.get('model');
            model.off('change:currency_id', null, this);
        }

        this._super('_dispose');
    }
})

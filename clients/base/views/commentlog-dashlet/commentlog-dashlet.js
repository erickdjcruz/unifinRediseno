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
 * Contextual dashlet to show comments for a record
 *
 * @class View.Views.Base.CommentlogDashletView
 * @alias SUGAR.App.view.views.BaseCommentlogDashletView
 * @extends View.View
 */
({
    plugins: ['Dashlet', 'Editable'],

    /**
     * Default settings for dashlet
     */
    _defaultSettings: {
        limit: 3
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.meta = _.extend(this.meta, app.metadata.getView(null, this.name));
    },

    /**
     * Set up the comment log collection when init dashlet
     */
    initDashlet: function(viewName) {
        this._mode = viewName;
        this.setUpCollection();
    },

    /**
     * Set up the comment log collection
     */
    setUpCollection: function() {
        this.collection = app.data.createRelatedCollection(this._getClonedModel(), 'commentlog_link');
    },

    /**
     * Get the contextual model for the dashlet
     *
     * @return {Data.Bean|undefined} The context, if it exists.
     * @private
     */
    _getContextModel: function() {
        if (this._contextModel) {
            return this._contextModel;
        }
        var model;
        var baseModule = this.context.get('module');
        var currContext = this.context;
        while (currContext) {
            var contextModel = currContext.get('rowModel') || currContext.get('model');

            if (contextModel && contextModel.get('_module') === baseModule) {
                model = contextModel;

                var parentHasRowModel = currContext.parent && currContext.parent.has('rowModel');
                if (!parentHasRowModel) {
                    break;
                }
            }

            currContext = currContext.parent;
        }
        return this._contextModel = model || app.controller.context.get('model');
    },

    /**
     * Create a new model with the id of the context model to stop changing the context model
     *
     * @return {Data.Bean}
     * @private
     */
    _getClonedModel: function() {
        var model = this._getContextModel();
        return app.data.createBean(model.module, {id: model.get('id')});
    },

    /**
     * Will reset the dataFetched flag of the commentlog field
     * and re-render the field in order to show appropriate content.
     */
    updateFieldsCollection: function() {
        _.each(this.fields, function(field) {
            field.collection.dataFetched = true;
            field.render();
        });
    },

    /**
     * Through options, the refresh button will be reset on complete.
     * Through the success callback we will be able to update the comment field.
     *
     * The property `loadAll` sent through options
     * if set to true will allow to load all comments.
     *
     * @param {Object} options Call options for reading comments.
     */
    getExtendedOptions: function(options) {
        var limit = options && options.loadAll ? -1 : this._defaultSettings.limit;
        return _.extend(options || {}, {
            limit: limit,
            success: _.bind(this.updateFieldsCollection, this),
            error: _.bind(this.updateFieldsCollection, this)
        });
    },

    /**
     * Load the comment log collection.
     *
     * @param {Object} options Call options for reading comments.
     */
    loadData: function(options) {
        var extendedOptions = this.getExtendedOptions(options);
        this.collection.fetch(extendedOptions);
    },

    /**
     * Determine if this dashlet has an unsaved comment in process.
     *
     * @return {boolean} `true` if there are unsaved comments. `false` otherwise.
     */
    hasUnsavedChanges: function() {
        return _.any(this.fields, function(field) {
            return _.isFunction(field.getCurrentCommentText) &&
                !_.isEmpty(field.getCurrentCommentText().trim()) && !field.isSaving;
        }, this);
    }
})

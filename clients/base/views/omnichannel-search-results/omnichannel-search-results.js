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
 * @class View.Views.Base.OmnichannelSearchResultsView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchResultsView
 * @extends View.Views.Base.QuicksearchResultsView
 */
({
    extendsFrom: 'QuicksearchResultsView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // The mixed bean collection for executing cross-module searches
        this.collection = app.data.createMixedBeanCollection();

        // Set the number of maximum results to display. Defaults to 5, but can
        // be changed by adding a 'limit' property in the metadata
        this.limit = 5;
        if (this.meta && this.meta.limit) {
            this.limit = this.meta.limit;
        }
    },

    /**
     * Parses models when collection resets and renders the view.
     *
     * @override
     */
    bindDataChange: function() {
        // When signaled, perform a fetch
        this.layout.on('omnichannelsearch:quicksearch:fire', this.fireQuickSearch, this);

        // If the layout has `omnichannel:results:close` called on it, we
        // need to hide search backdrop
        this.layout.on('quicksearch:close omnichannel:close omnichannel:results:close', this.abortQuicksearch, this);

        // On a collection sync, format the search results and display
        this.collection.on('sync', this.displayResults, this);
    },

    /**
     * Aborts any collection fetch in progress and closes the quicksearch
     * results view
     */
    abortQuicksearch: function() {
        this.collection.abortFetchRequest();
        this.close();
        this.removeBackdrop();
    },

    /**
     * Formats and displays the current collection models
     */
    displayResults: function() {
        if (this.disposed) {
            return;
        }

        // Format the models to display correctly
        var gsUtils = app.utils.GlobalSearch;
        gsUtils.formatRecords(this.collection,false);

        _.each(this.collection.models, function(model) {
            model.viewAccess = app.acl.hasAccessToModel('view', model);

            var moduleMeta = this._fieldsMeta[model.module] ||
                gsUtils.getFieldsMeta(model.module, {linkablePrimary: false});
            this._fieldsMeta[model.module] = moduleMeta;
            model.primaryFields = gsUtils.highlightFields(model, moduleMeta.primaryFields);
            model.secondaryFields = gsUtils.highlightFields(model, {}, true);

            model.primaryFields = _.values(model.primaryFields);
            model.secondaryFields = _.values(model.secondaryFields).slice(0, 3);
        }, this);

        // Dynamically binds the click events
        this.$el.on('click', '.view-all-results', _.bind(this.viewAllResultsClicked, this));
        this.$el.on('click', '[data-action=rowClicked]', _.bind(this.rowClicked, this));

        // If the user has already closed the quicksearch results at this point,
        // do not reopen them
        if (this.isOpen()) {
            this._showQuickSearchPanel();
        }
    },

    /**
     * Fires a quicksearch using the current collection
     *
     * @param {Object} options the options to use for the collection fetch
     */
    fireQuickSearch: function(options) {
        if (_.isEmpty(options.query)) {
            return;
        }

        // Set the collection fetch limit option
        options = _.extend(options, {
            limit: this.limit
        });

        // Update the UI to show the search in progress
        this.collection.dataFetched = false;
        this.close();
        this.layout.expand();
        this._showQuickSearchPanel();

        this.collection.fetch(options);
    },

    /**
     * Opens and displays the quicksearch panel
     *
     * @private
     */
    _showQuickSearchPanel: function() {
        this.render();
        this.open();
        this.addBackdrop();
    },

    /**
     * Handler for row click event
     * Switches to relevant module tab
     *
     * @param {Event} evt row click event
     */
    rowClicked: function(evt) {
        var loadModel = [];
        var rowId = evt.currentTarget && $(evt.currentTarget).closest('li') ?
            $(evt.currentTarget).closest('li')[0].dataset.id : '';
        var collection = this.collection;
        var dashboard = this.closestComponent('omnichannel-dashboard') || {};
        if (!_.isEmpty(rowId) && collection) {
            loadModel = _.filter(collection.models, function(model) {
                if (model.id === rowId) {
                    return model;
                }
            }, this);
        }

        // set current row as model for the dashboard tab and switch tab based on module
        if (dashboard && loadModel.length !== 0) {
            app.alert.show('data-load', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
            });
            loadModel[0].fetch({
                success: _.bind(function(model) {
                    app.alert.dismiss('data-load');
                    if (this.disposed) {
                        return;
                    }
                    dashboard.setModel(dashboard.moduleTabIndex[model.get('_module')], model);
                    dashboard.switchTab(dashboard.moduleTabIndex[model.get('_module')]);
                }, this)
            });
        }
    },

    /**
     * Show the quickresults dropdown
     */
    open: function() {
        this.$('.typeahead').show();
    },

    /**
     * Hide the quickresults dropdown
     */
    close: function() {
        this.collection.reset();
        this.$('.typeahead').hide();
        this.removeBackdrop();
        this.layout.trigger('omnichannel:modulelist:close');
    },

    /**
     * Adds the search result backdrop
     */
    addBackdrop: function() {
        $('.omnichannel-search-list').addClass('omnichannel-search-backdrop');
        $('.omnichannel-search-list').find('.omnibar-search').addClass('loading');
    },

    /**
     * Removes the search result backdrop
     */
    removeBackdrop: function() {
        $('.omnichannel-search-list').removeClass('omnichannel-search-backdrop');
        $('.omnichannel-search-list').find('.omnibar-search').removeClass('loading');
    },

    /**
     * Click event handler for the view all results link
     */
    viewAllResultsClicked: function() {
        // Close the quicksearch results panel and signal to the layout to
        // do a full search
        this.close();
        this.layout.trigger('omnichannelsearch:quicksearch:viewallresults');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$el.off('click');
        this.layout.off('omnichannelsearch:quicksearch:fire', this.fireQuickSearch, this);
        this.layout.off('omnichannel:close omnichannel:results:close', this.abortQuicksearch, this);
        this.collection.off('sync', this.displayResults, this);
        this._super('_dispose');
    }
})

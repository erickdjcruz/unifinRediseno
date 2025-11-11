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
 * @class View.Views.Base.OmnichannelSearchBarView
 * @alias SUGAR.App.view.views.BaseOmnichannelSearchBarView
 * @extends View.Views.Base.QuicksearchBarView
 */
({
    extendsFrom: 'QuicksearchBarView',

    className: 'omnichannel-search-bar-wrapper table-cell w-full',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        // Set the number of maximum results to display. Defaults to 20, but can
        // be changed by adding a 'limit' property in the metadata
        this.limit = 20;
        if (this.meta && this.meta.limit) {
            this.limit = this.meta.limit;
        }

        // Sets a persistent option on the collection to always use the xmod_aggs
        // flag, which returns facet/filter count information along with each
        // collection fetch
        this.collection.setOption({
            params: {
                xmod_aggs: true
            }
        });

        // Remove unneeded event from the parent quicksearch bar view
        this.layout.off('quicksearch:close');

        app.events.on('omnichannelsearch:bar:search:term', this.fireSearch, this);
        this.layout.on('omnichannelsearch:bar:clear:term', this.clearSearchTerm, this);
        this.layout.on('omnichannelsearch:quicksearch:viewallresults', this.fireSearch, this);

        var dashboard = this.closestComponent('omnichannel-dashboard');
        if (dashboard) {
            this.searchTerm = dashboard.savedSearchTerm;
        }
    },

    /**
     * Fires the search based on the search term.
     * Waits & debounces for 0.5 seconds before firing a search.
     */
    fireSearch: _.debounce(function() {
        // When a search is triggered, show "Loading..."
        this.collection.dataFetched = false;
        this.collection.showMore = false;

        // When a search from the search bar completes, let other views know
        var options = this._getDefaultFetchOptions();
        options = _.extend(options, {
            success: _.bind(function() {
                app.events.trigger('omnichannelsearch:bar:search:complete');
                var dashboard = this.closestComponent('omnichannel-dashboard');
                if (dashboard) {
                    dashboard.savedSearchTerm = options.query;
                }
            }, this)
        });

        app.events.trigger('omnichannelsearch:bar:search:started');
        this.collection.query = options.query;
        this.collection.fetch(options);
    }, 500),

    /**
     * @inheritdoc
     *
     * Handles different key stroke cases, so that when the user hits enter the
     * full search fires instead of quicksearch
     */
    keyupHandler: function(event) {
        if (event.keyCode === 13) {
            // Enter pressed, cancel any open quick search and fire a full
            // search instead
            this.layout.trigger('omnichannel:results:close');
            this.fireQuickSearch.cancel();
            this.fireSearch();
        } else {
            // Any other key pressed, fire a quick search
            this.layout.trigger('omnichannel:modulelist:close');
            this.fireQuickSearch();
        }
    },

    /**
     * Delegates firing a quick search to the quick search results view, so that
     * it can use a separate collection to avoid interfering with this layout's
     * collection. Waits & debounces for 0.5 seconds before firing
     */
    fireQuickSearch: _.debounce(function() {
        var options = this._getDefaultFetchOptions();
        this.layout.trigger('omnichannelsearch:quicksearch:fire', options);
    }, 500),

    /**
     * Returns the default options used for collection fetches
     *
     * @return {Object} the set of default collection fetch options
     * @private
     */
    _getDefaultFetchOptions: function() {
        return {
            query: this._getSearchTerm(),
            module_list: this._getSearchSelectedModules(),
            limit: this._getSearchLimit(),
            apiOptions: {
                fetchWithPost: true,
                useNewApi: true
            }
        }
    },

    /**
     * Returns the search term currently entered in the search bar
     *
     * @return {string} the contents of the search bar
     * @private
     */
    _getSearchTerm: function() {
        return this.$input ? this.$input.val() : '';
    },

    /**
     * Returns the search modules currently selected by the user
     *
     * @return {Array} the list of selected search modules
     * @private
     */
    _getSearchSelectedModules: function() {
        var metadata = this.model.get('metadata');
        var selectedModules = [];
        if (metadata.tabs && this.collection.selectedModules.length === 0) {
            _.each(metadata.tabs, function(tab) {
                if (tab.icon && tab.icon.module &&
                    app.acl.hasAccess.call(app.acl, 'view', tab.icon.module)) {
                    selectedModules.push(tab.icon.module);
                }
            });
        } else {
            selectedModules = this.collection.selectedModules;
        }
        return selectedModules;
    },

    /**
     * Returns the default search result limit to use for collection fetches
     *
     * @return {number} the search result limit
     * @private
     */
    _getSearchLimit: function() {
        var limit = this.layout.v2 ? this.limit : 20;
        limit = app.config && app.config.maxSearchQueryResult || limit;
        return limit;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (this.searchTerm) {
            this.fireSearch();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.events.off('omnichannelsearch:bar:search:term', this.fireSearch);
        this.layout.off('omnichannelsearch:bar:clear:term', this.clearSearchTerm, this);
        this.layout.off('omnichannelsearch:quicksearch:viewallresults', this.fireSearch, this);
        this._super('_dispose');
    }
})

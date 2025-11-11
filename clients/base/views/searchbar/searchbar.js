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
 * @class View.Views.Base.SearchbarView
 * @alias SUGAR.App.view.views.BaseSearchbarView
 * @extends View.View
 */
({
    /**
     * the library to search
     */
    library: [],

    /**
     * the matched records
     */
    matches: null,

    /**
     * search tool
     */
    fuse: null,

    /**
     * @inheritdoc
     */
    events: {
        'click .search-group .sicon-close': 'closeIconClickHandler',
        'keyup [data-action="search"]': 'doSearch'
    },

    /**
     * Max number of records per page
     */
    maxNum: 7,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.searchDropdown = null;
        this.searchButtonIcon = 'search';

        this.context.on('page:clicked', this._search, this);

        this._initProperties();
    },

    /**
     * Handler for clicks on the close.
     */
    closeIconClickHandler: function() {
        this.$('input[data-action=search]').val('');
        this.toggleSearchIcon('search');
    },

    /**
     * Toggles the search icon between the magnifying glass and x.
     *
     * @param {string} searchButtonIcon Indicates the state of the search button icon
     * - `search` means magnifying glass.
     * - `close` means X icon.
     */
    toggleSearchIcon: function(searchButtonIcon) {
        if (this.searchButtonIcon === searchButtonIcon) {
            return;
        }
        var iconEl = this.$('input[data-action=search] + i').first();
        this.searchButtonIcon = searchButtonIcon;
        switch (searchButtonIcon) {
            case 'search':
                iconEl.removeClass('sicon-close');
                iconEl.addClass('sicon-search');
                break;
            case 'close':
                iconEl.removeClass('sicon-search');
                iconEl.addClass('sicon-close');
                break;
        }
    },

    /**
     * Handler when the library data is ready
     */
    sourceDataReady: function() {
        this._populateLibrary();
        this.initFuse();
    },

    /**
     * initialize some properties
     * This should be overridden by sub classes to define their own greeting, event, etc.
     *
     * @private
     */
    _initProperties: function() {
    },

    /**
     * This function populates the search library.
     * This should be overridden by sub classes to build the library for search.
     *
     * @private
     */
    _populateLibrary: function() {
    },

    /**
     * This function returns fuse options.
     * This can be overridden by sub classes to define their own options as needed
     */
    getFuseOptions: function() {
        return {
            // we don't need score
            includeScore: false,
            // Search only in the name and description attributes
            keys: ['name', 'description']
        };
    },

    /**
     * initialize fuse object
     */
    initFuse: function() {
        if (!this.fuse && this.library) {
            this.fuse = new Fuse(this.library, this.getFuseOptions());
        }
    },

    /**
     * Starts a new search and show the search results dropdown.
     */
    doSearch: _.debounce(function() {
        var $input = this.$('input[data-action=search]');
        var term = $input.val().trim();

        if (term === '') {
            if (this.searchDropdown) {
                this.searchDropdown.hide();
            }
            this.toggleSearchIcon('search');
            return;
        }

        this.toggleSearchIcon('close');

        if (_.isNull(this.searchDropdown)) {
            this.searchDropdown = app.view.createLayout({
                context: this.context,
                name: 'searchbar-dropdown',
                module: this.module
            });
            this.searchDropdown.initComponents();
            this.layout._components.push(this.searchDropdown);
            this.searchDropdown.render();

            var $searchGroup = this.$('.search-group');
            $searchGroup.after(this.searchDropdown.$el);
        }

        this.searchDropdown.hide();
        this.context.trigger('data:fetching');
        this.searchDropdown.show();

        // do fuse search, which supports fuzzy search
        if (this.fuse) {
            this.matches = this.fuse.search(term);
            this.totalRecords = this.matches.length;

            this._search();
        }
    }, 400),

    /**
     * @param {Object} options The search options
     * @private
     */
    _search: function(options) {
        var pageNumber = options && options.pageNum || 1;
        var offset = (pageNumber - 1) * this.maxNum;

        // looks up this.matches and gets the records we need and add them to data
        var data = {records: [], total: this.totalRecords, next_offset: offset + this.maxNum};
        for (var i = offset; i < offset + this.maxNum && i < this.totalRecords; i++) {
            data.records.push(this.matches[i]);
        }
        if (this.context) {
            var parsedData = this._parseData(data);
            this.context.trigger('data:fetched', parsedData);
        }
    },

    /**
     * Parses search results.
     *
     * @param {Object} result The search result
     * @return {Object} parsed data
     * @private
     */
    _parseData: function(result) {
        var totalPages = result.total > 0 ?
            Math.ceil(result.total / this.maxNum) : 0;
        var currentPage = result.next_offset > 0 ?
            result.next_offset / this.maxNum : totalPages;
        var records = _.map(result.records, function(record) {
            return {
                name: record.name,
                description: record.description,
                url: app.utils.buildUrl(record.href.replace(/^\/+/g, ''))
            };
        });
        return {
            currentPage: currentPage,
            records: records,
            totalPages: totalPages
        };
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('page:clicked', null, this);
        }
        if (this.fuse) {
            delete this.fuse;
        }
    }
})

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
 * @class View.Views.Base.KBContents.DashletSearchableKbListView
 * @alias SUGAR.App.view.views.BaseKBContentsDashletSearchableKbListView
 * @extends View.DashletNestedsetListView
 */
({
    extendsFrom: 'KBContentsDashletNestedsetListView',

    events: {
        'keyup [data-action="search"]': 'searchKBs',
        'click .sicon-close': 'clearQuickSearch'
    },

    /**
     * Search options.
     * @property {Object}
     */
    searchOptions: {
        max_num: 4,
        module_list: 'KBContents'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.searchDropdown = null;
        this.context.on('page:clicked', this._search, this);
    },

    /**
     * Handler for clearing the quick search bar
     *
     * @param {Event} event A click event on the close button of search bar
     */
    clearQuickSearch: function(event) {
        // clear the input
        this.$('input[data-action=search]').val('');

        // clear the drop down
        if (this.searchDropdown) {
            this.searchDropdown.hide();
        }

        // remove the icon
        this.toggleClearQuickSearchIcon(false);
    },

    /**
     * Append or remove an icon to the quicksearch input so the user can clear the search easily
     * @param {boolean} addIt TRUE if you want to add it, FALSE to remove
     */
    toggleClearQuickSearchIcon: function(addIt) {
        if (addIt && !this.$('.sicon-close.add-on')[0]) {
            this.$('.search-container').append('<i class="sicon sicon-close add-on"></i>');
        } else if (!addIt) {
            this.$('.sicon-close.add-on').remove();
        }
    },

    /**
     * Initialize dashlet properties.
     */
    initDashlet: function() {
        this._super('initDashlet');

        this.searchOptions = {
            module_list: this.settings.get('module_list') || this.searchOptions.module_list,
            max_num: this.settings.get('max_num') || this.searchOptions.max_num
        };
        this.maxChars = this.settings.get('max_chars') || this.maxChars;
    },

    /**
     * Starts a new search and show the search results dropdown.
     */
    searchKBs: _.debounce(function() {
        var $input = this.$('input[data-action=search]');
        var term = $input.val().trim();

        if (term === '') {
            if (this.searchDropdown) {
                this.searchDropdown.hide();
            }
            this.toggleClearQuickSearchIcon(false);
            return;
        }

        this.searchOptions.q = term;

        if (_.isNull(this.searchDropdown)) {
            this.searchDropdown = app.view.createLayout({
                context: this.context,
                name: 'contentsearch-dropdown'
            });
            this.searchDropdown.initComponents();
            this.layout._components.push(this.searchDropdown);
            this.searchDropdown.render();
            $input.after(this.searchDropdown.$el);
        }

        this.searchDropdown.hide();
        this.context.trigger('data:fetching');
        this.searchDropdown.show();
        this._search();
        this.toggleClearQuickSearchIcon(true);
    }, 400),

    /**
     * Calls search api
     *
     * @param {Object} options The search options
     * @private
     */
    _search: function(options) {
        var pageNumber = options && options.pageNum || 1;
        var offset = (pageNumber - 1) * this.searchOptions.max_num;
        var params = _.extend({}, this.searchOptions, {offset: offset});
        var url = app.api.buildURL('genericsearch', null, null, params);
        app.api.call('read', url, null, {
            success: _.bind(function(result) {
                if (this.disposed) {
                    return;
                }
                if (this.context) {
                    var data = this._parseData(result);
                    this.context.trigger('data:fetched', data);
                }
            }, this)
        });
    },

    /**
     * Parses search results.
     *
     * @param {Object} result The search result
     * @return {Object} parsed data
     * @private
     */
    _parseData: function(result) {
        var self = this;
        var totalPages = result.total > 0 ?
            Math.ceil(result.total / this.searchOptions.max_num) : 0;
        var currentPage = result.next_offset > 0 ?
            result.next_offset / this.searchOptions.max_num : totalPages;
        var records = _.map(result.records, function(record) {
            return {
                name: record.name,
                description: self._truncate(record.description),
                url: app.utils.buildUrl(record.url.replace(/^\/+/g, ''))
            };
        });
        return {
            options: this.searchOptions,
            currentPage: currentPage,
            records: records,
            totalPages: totalPages
        };
    },

    /**
     * Truncates search result so it is shorter than the maxChars
     * Only truncate on full words to prevent ellipsis in the middle of words
     * @param {string} text The search result entry to truncate
     * @return {string} the shortened version of an entry
     * @private
     */
    _truncate: function(text) {
        text = text || '';

        if (text.length > this.maxChars) {
            var cut = text.substring(0, this.maxChars);
            // cut at a full word
            while (!(/\s/.test(cut[cut.length - 1])) && cut.length > 0) {
                cut = cut.substring(0, cut.length - 1);
            }
            text = cut + '...';
        }

        return text;
    },

    /**
     * Open a document in new tab.
     * @inheritdoc
     */
    _openDocument: function(module, id) {
        var route = app.router.buildRoute(module, id);
        window.open('#' + route, '_blank');
    },

    /**
     * The view doesn't need standard handlers for data change because it use own events and handlers.
     *
     * @override
     */
    bindDataChange: function() {}
})

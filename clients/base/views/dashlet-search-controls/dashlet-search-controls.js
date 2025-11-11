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
 * @class View.Views.Base.DashletSearchControlsView
 * @alias SUGAR.App.view.views.BaseDashletSearchControlsView
 * @extends View.View
 */
({
    events: {
        'click .add-on.sicon-close': 'clearQuickSearch',
        'keyup .search-name': 'throttledSearch',
        'paste .search-name': 'throttledSearch',
    },

    /**
     * Sort dropdown items
     *
     * @property {Array}
     */
    sortItems: null,

    /**
     * Current sort order
     *
     * @property {integer}
     */
    currentSortOrder: null,

    /**
     * Placeholder string for the search field
     *
     * @property {string}
     */
    searchFieldPlaceholder: null,

    /**
     * String that was last searched for
     *
     * @property {string}
     */
    currentSearch: '',

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.sortItems = options.sortItems;
        this.searchFieldPlaceholder = options.searchFieldPlaceholder;
        if (!_.isEmpty(this.sortItems)) {
            this.currentSortOrder = _.first(this.sortItems).id;
        }
    },

    /**
     * Fires the quick search.
     * @param {Event} event A keyup event.
     */
    throttledSearch: _.debounce(function() {
        this.applyQuickSearch();
    }, 400),

    /**
     * Handler for clearing the quick search bar
     *
     * @param {Event} event A click event on the close button of search bar
     */
    clearQuickSearch: function(event) {
        this.$('input.search-name').val('');
        this.applyQuickSearch();
    },

    /**
     * Show or hide an icon to the quicksearch input so the user can clear the search easily
     * @param {boolean} show TRUE if you want show the clear icon, FALSE to hide
     */
    toggleClearQuickSearchIcon: function(show) {
        this.$('.sicon-close.add-on').toggle(show);
    },

    /**
     * Applies an updated filterdef with the current value on the quicksearch field.
     */
    applyQuickSearch: function() {
        var searchElem = this.$('input.search-name');
        var newSearch = searchElem.val();

        if (this.currentSearch !== newSearch) {
            this.currentSearch = newSearch;

            this.layout.trigger('dashlet:controls:search', newSearch);
            this.toggleClearQuickSearchIcon(!_.isEmpty(this.currentSearch));
        }
    },

    /**
     * Render the sort dropdown
     * @private
     */
    _renderSortDropdown: function() {
        var sortDropdown = this.$('.dashlet-controls-sort-field');

        sortDropdown.select2({
            data: this.sortItems,
            multiple: false
        });
        sortDropdown.select2('val', this.currentSortOrder);

        sortDropdown.on('change', _.bind(function(event) {
            this.currentSortOrder = event.val;
            this.layout.trigger('dashlet:controls:sort', event.val);
        }, this));
    },

    /**
     * @inheritdoc
     * @param options
     * @private
     */
    _render: function(options) {
        this._super('_render', [options]);
        this._renderSortDropdown();
    }
})

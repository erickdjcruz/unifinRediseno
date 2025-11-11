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
 * @class View.Views.Base.ListPaginationView
 * @alias SUGAR.App.view.views.BaseListPaginationView
 * @extends View.Views.Base.BaseView
 */
({
    events: {
        'click .paginate-prev:not(.disabled)': 'handlePaginate',
        'click .paginate-next:not(.disabled)': 'handlePaginate',
        'click .page-count': 'getPageCount',
        'keydown input.current-page': 'handleInput',
        'change input.current-page': 'handlePaginate',
        'focus input.current-page:not([data-focus-expect=true])': 'handleFocusPageInput',
    },

    /**
     * Maintains the current page being displayed
     *
     * @property {number}
     */
    page: 1,

    /**
     * Current pagination limit
     *
     * @property {number}
     */
    limit: 0,

    /**
     * Maintains the total number of pages present in the collection
     *
     * @property {number}
     */
    pagesCount: 0,

    /**
     * Flag to display the loading text
     *
     * @property {boolean}
     */
    isLoadingCount: false,

    cachedCollection: {},

    /**
     * Flag if input field "current-page" should be focused
     *
     * @property {boolean}
     */
    pageInputFocusExpect: false,

    /**
     * Store valid key-value pagination actions
     */
    paginationActions: {
        '': '',
        'paginate': 'PAGINATE',
        'filter': 'FILTER',
    },

    /**
     * The current pagination action
     */
    paginationAction: '',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.collection = this.context.get('collection');

        this.bindCollectionEvents();
        this.bindLayoutEvents();
        this.bindContextEvents();
    },

    /**
     * Bind collection events
     */
    bindCollectionEvents: function() {
        if (!this.collection) {
            return;
        }

        this.collection.on('list:page-total:fetched', this.pageTotalFetched, this);
        this.collection.on('list:page-total:fetching', this.loadingPageTotal, this);
        this.collection.on('add', _.bind(function() {
            this.setCache();
            this.render();
        }, this));
        this.collection.on('remove', _.bind(function() {
            if (this.layout && this.layout.type && this.layout.type === 'list') {
                return;
            }

            this.handleCollectionRemove();
        }, this));
        this.collection.on('reset', this.handleCollectionReset, this);
    },

    /**
     * Bind layout events
     */
    bindLayoutEvents: function() {
        if (!this.layout) {
            return;
        }

        this.layout.on('list:sort:fire', this.handleListSort, this);
        this.layout.on('list:record:deleted', this.handleCollectionRemove, this);

        let parentLayout = this.layout.layout || {};
        if (!_.isEmpty(parentLayout)) {
            this.listenTo(parentLayout, 'metric:count:fetched', function(total) {
                let metricsComp = parentLayout.getComponent('forecast-metrics') || {};
                if (!_.isEmpty(metricsComp)) {
                    let hasActiveChanged = metricsComp.model.get('lastActive') !== metricsComp.model.get('active');
                    // if the active metric tab has changed set the paginationAction as paginate
                    if (hasActiveChanged) {
                        this.paginationAction = this.paginationActions.paginate;
                    }
                }

                this.pageTotalFetched(total);
            });
        }
    },

    /**
     * Bind context events
     */
    bindContextEvents: function() {
        if (!this.context) {
            return;
        }

        this.context.on('filter:fetch:start', function() {
            this.clearCache();
            this.setPaginationAction('filter');
        }, this);
        this.context.on('filter:fetch:success', function() {
            this.handleListFilter();
        }, this);
        this.context.on('reload', function() {
            this.clearCache();
        }, this);
    },

    /**
     * Handles the collection reset event
     *
     * The reset event is triggered on every paginate, as the
     * list shows a subset of records (different than list-bottom)
     */
    handleCollectionReset: function() {
        if (this.paginationAction === this.paginationActions.paginate) {
            if (this.collection.page !== this.page && this.collection.page === 1) {
                // should update page after linking records
                this.getFirstPage(false);
            }
            return;
        }

        if (this.paginationAction === this.paginationActions.filter && this.collection.page !== this.page &&
            this.collection.page === 1) {
            return;
        }

        const limit = parseInt(this.collection.getOption('limit'));
        if (this.limit !== limit) {
            // Clear cache if records limit was changed
            this.limit = limit;
            this.clearCache();
        }
        let parentLayout = this.layout.layout || {};
        let metricsComp = {};

        if (!_.isEmpty(parentLayout)) {
            metricsComp = parentLayout.getComponent('forecast-metrics') || {};
        }

        if (_.isEmpty(metricsComp)) {
            this.pagesCount = 0;
        }
        this.setCache();
        this.render();
    },

    /**
     * Handles the collection remove event
     *
     * The remove event is triggered when record(s) are deleted/unlinked/etc
     * from the collection
     */
    handleCollectionRemove: function() {
        this.clearPaginationAction();
        this.clearCache();
        this.getFirstPage(true);
    },

    /**
     * Set data of current page to cache variable
     */
    setCache: function() {
        if (!this.collection) {
            return;
        }

        this.cachedCollection[this.page] = {
            models: _.clone(this.collection.models),
            next_offset: this.collection.next_offset,
            page: this.page
        };
    },

    /**
     * Clear cache variable
     */
    clearCache: function() {
        this.cachedCollection = {};
    },

    /**
     * Set collection data from cache variable
     * @return {boolean} true if collection was restored
     */
    restoreFromCache: function() {
        if (!this.cachedCollection[this.page]) {
            return false;
        }

        const cache = this.cachedCollection[this.page];
        this.collection.reset(cache.models);
        this.collection.next_offset = cache.next_offset;
        this.collection.page = cache.page;
        this.page = cache.page;
        return true;
    },

    /**
     * Set the current pagination action
     *
     * @param action
     */
    setPaginationAction: function(action) {
        this.paginationAction = this.paginationActions[action];
        this.context.set('paginationAction', this.paginationAction);
    },

    /**
     * Clear the pagination action
     */
    clearPaginationAction: function() {
        this.setPaginationAction('');
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.setVisibility();
        this._super('_renderHtml');

        this.$('[data-focus-expect=true]')
            .trigger('focus')
            .attr('data-focus-expect', false);
    },

    /**
     * Handles page number entry into the pagination input field
     * and updates the current page number value accordingly.
     *
     * @param {Event} event
     */
    handlePageInput: function(event) {
        let target = this.$(event.currentTarget);
        if (target.length > 0) {
            let inputVal = target.val() || this.page;
            if (_.isString(inputVal) && !isNaN(parseInt(inputVal))) {
                inputVal = parseInt(inputVal);
            }

            inputVal = this.validatePageNumber(inputVal);

            if (inputVal !== this.page) {
                this.getPage(inputVal);
            }

            if (event.target.value !== inputVal) {
                event.target.value = inputVal;
            }
        }
    },

    /**
     * Checks the page number to so that it falls under the valid page range
     * @param {number} page number to be validated
     * @return {number} a valid page number to be paginated to
     */
    validatePageNumber: function(pageNum) {
        if (pageNum < 1) {
            pageNum = 1;
        }

        if (!_.isUndefined(this.pagesCount) && this.pagesCount > 0 && pageNum > this.pagesCount) {
            pageNum = this.pagesCount;
        }

        return pageNum;
    },

    /**
     * Paginates the collection to a given page number.
     *
     * @param {number} page value of page number to paginate to
     */
    getPage: function(page) {
        if (_.isString(page)) {
            page = parseInt(page);
        }

        var options = {
            reset: true,
            page: page,
            limit: this.collection.getOption('limit'),
            strictOffset: true
        };

        this.page = page;
        options.success = _.bind(function(shouldCache = true) {
            this.layout.trigger('list:paginate:success');

            // Tell the side drawer that there are new records to look at
            if (app.sideDrawer) {
                app.sideDrawer.trigger('sidedrawer:collection:change', this.collection);
            }

            // update count label
            this.context.trigger('list:paginate');

            this.render();

            if (shouldCache) {
                this.setCache();
            }
        }, this);

        if (this.restoreFromCache()) {
            options.success(false);
        } else {
            this.context.trigger('list:paginate:start');
            this.collection.paginate(options);
        }
    },

    /**
     * Switch on the event's data action and call the appropriate paginate
     *
     * @param event
     */
    handlePaginate: function(event) {
        this.setPaginationAction('paginate');

        let action = event.currentTarget.getAttribute('data-action');

        let callback;
        let layoutEvent;

        switch (action) {
            case 'paginate-prev':
                layoutEvent = 'list:paginate:previous';
                callback = _.bind(this.getPreviousPage, this);
                break;
            case 'paginate-next':
                layoutEvent = 'list:paginate:next';
                callback = _.bind(this.getNextPage, this);
                break;
            case 'paginate-input':
                layoutEvent = 'list:paginate:input';
                callback = _.bind(this.handlePageInput, this, event);
                break;
        }

        if (action === 'paginate-prev' || action === 'paginate-next') {
            if (this.pagesCount === 0) {
                this.getPageCount();
            }
        }

        if (this.layout && this.layout._events && !!this.layout._events[layoutEvent]) {
            this.layout.trigger(layoutEvent, callback);
        } else {
            callback();
        }
    },

    /**
     * Check valid value for page input
     *
     * @param event
     */
    handleInput: function(event) {
        if (!this.isNumberInput(event.key)) {
            event.preventDefault();
        }
    },

    /**
     * Validate if the input is integer or arrow keys or delete or backspace or enter
     *
     * @param {string} key
     * @return {boolean}
     */
    isNumberInput: function(key) {
        if ((key >= '0' && key <= '9') ||
            [
                'ArrowLeft',
                'ArrowUp',
                'ArrowRight',
                'ArrowDown',
                'Backspace',
                'Delete',
                'Enter',
            ].includes(key)) {
            return true;
        }

        return false;
    },

    /**
     * Paginate to the previous page
     */
    getPreviousPage: function() {
        let pageNum = this.validatePageNumber(this.page - 1);
        this.getPage(pageNum);
    },

    /**
     * Paginate to the next page
     */
    getNextPage: function() {
        let pageNum = this.validatePageNumber(this.page + 1);
        this.getPage(pageNum);
    },

    /**
     * Trigger the paginate event on the context. This in turn fetches the total count from
     * 'collection-count' field.
     */
    getPageCount: function() {
        // Check if the collection-count does not exist, in case of Forecasts module's Opp Review Tab,
        // we do not have that field hence we cannot trigger 'paginate' and we'll have to call fetchCount from this
        this.context.get('noCollectionField') ? this.fetchCount() : this.context.trigger('paginate');
    },

    /**
     * Fetches the total amount of filtered records from the collection
     */
    fetchCount: function() {
        if (_.isNull(this.collection.total)) {
            app.alert.show('fetch_count_alert', {
                level: 'process',
                title: app.lang.get('LBL_LOADING'),
                autoClose: false
            });
        }

        this.collection.trigger('list:page-total:fetching');

        this.isLoadingCount = true;

        let successCallback = _.bind(function(total) {
            this.isLoadingCount = false;
            this.collection.trigger('list:page-total:fetched', total);
        }, this);
        this.collection.fetchTotal({
            success: successCallback,
            complete: function() {
                app.alert.dismiss('fetch_count_alert');
            }
        });
    },

    /**
     * Sets the page count correctly on the pagination component ones the total is fetched successfully.
     * @param {number} total total number of records present in the collection
     */
    pageTotalFetched: function(total) {
        let limit = this.collection.getOption('limit') || app.config.maxQueryResult || 0;
        if (_.isNumber(total) && limit > 0) {
            this.pagesCount = Math.ceil(total / limit);
        }

        this.isLoadingCount = false;
        this.render();
    },

    /**
     * Handles the fetching event to get total pages. Sets the 'isLoadingCount' to true to display
     * loading labels.
     */
    loadingPageTotal: function() {
        this.isLoadingCount = true;
        this.render();
    },

    /**
     * Set pagesCount variable to -1 to hide pagination
     */
    setVisibility: function() {
        if (this.pagesCount > 1) {
            return;
        }

        let collectionDataFetched = _.has(this.collection, 'dataFetched') && this.collection.dataFetched;
        let isLastOffset = _.has(this.collection, 'next_offset') && this.collection.next_offset === -1;
        let currentPage = _.has(this.collection, 'page') ? this.collection.page : -1;

        let isPortalThemeConfig = false;
        if (this.layout.context.parent && this.layout.context.parent.get('layout') === 'portaltheme-config') {
            isPortalThemeConfig = true;
        }

        if ((collectionDataFetched && isLastOffset) || isPortalThemeConfig) {
            this.pagesCount = currentPage;
            if (this.getHiddenState() && currentPage === 1) {
                this.pagesCount = -1;
            }
        }
    },

    /**
     * Return true if pagination should be hidden for Pages Count === 1
     *
     * @return {boolean}
     */
    getHiddenState: function() {
        return this.layout.type !== 'list';
    },

    /**
     * Get the first page
     *
     * @param fetch true to fetch the collection, false to render to 1
     */
    getFirstPage: function(fetch) {
        if (fetch) {
            this.getPage(1);
            return;
        }

        this.page = 1;

        if (!_.has(this.cachedCollection, 1)) {
            this.setCache();
        }

        this.render();
    },

    /**
    * Set the page to 1 and render
    *
    * On list column sort, BaseListView paginates to the first page using
    * the new sort criteria. There's no need to fetch the collection again
    */
    handleListSort: function() {
        this.clearCache();
        this.getFirstPage(false);
        if (!_.isEmpty(this.context)) {
            this.context.trigger('refresh:count');
        }
    },

    /**
     * Handle list filter
     */
    handleListFilter: function() {
        this.getFirstPage(false);
    },

    /**
     * Unset keys set in context
     */
    unsetContextKeys: function() {
        this.context.unset('paginationAction');
    },

    /**
     * Handles the on focus event for page input
     */
    handleFocusPageInput: function() {
        this.pageInputFocusExpect = true;

        if (this.pagesCount === 0) {
            this.getPageCount();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose() {
        if (this.collection) {
            this.collection.off('list:page-total:fetched', this.pageTotalFetched, this);
            this.collection.off('list:page-total:fetching', this.loadingPageTotal, this);
            this.collection.off('add remove reset', this.render, this);
        }

        this.unsetContextKeys();

        this._super('_dispose');
    }
})

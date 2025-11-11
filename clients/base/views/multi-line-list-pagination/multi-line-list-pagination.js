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
 * @class View.Views.Base.MultiLineListPaginationView
 * @alias SUGAR.App.view.views.BaseMultiLineListPaginationView
 * @extends View.Views.Base.ListPaginationView
 */
({
    extendsFrom: 'ListPaginationView',

    /**
     * List of previous filters
     */
    previousFilters: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.template = app.template.getView('list-pagination');
        this.autoRefresh(true);
    },

    /**
     * @inheritdoc
     */
    bindCollectionEvents: function() {
        this._super('bindCollectionEvents');

        if (!this.collection) {
            return;
        }

        this.listenTo(this.collection, 'list:paginate:loading', this.showLoading, this);
        this.listenTo(this.context, 'filter:fetch:start', this.showLoading, this);
    },

    /**
     * @inheritdoc
     */
    handleCollectionReset: function() {
        if (this.paginationAction === this.paginationActions.paginate && !this.isFilterChanged()) {
            if (this.collection.page !== this.page && this.collection.page === 1) {
                // should update page after linking records
                this.getFirstPage(false);
            }
            return;
        }

        this.handleFiltersChange();

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
     * @inheritdoc
     */
    getHiddenState: function() {
        return false;
    },

    /**
     * Update pagination data if filter data was changed
     */
    handleFiltersChange: function() {
        if (this.isFilterChanged()) {
            this.previousFilters = this.collection.filterDef;
            this.pagesCount = 0;
            this.page = 1;
            this.clearCache();
        }
    },

    /**
     * Check if filter data was changed
     *
     * @return {boolean}
     */
    isFilterChanged: function() {
        return !_.isEqual(this.previousFilters, this.collection.filterDef);
    },

    /**
     * Loading pagination handler
     */
    showLoading: function() {
        this.collection.dataFetched = false;
        this.render();
    },

    /**
     * Auto refresh the list view every 5 minutes
     *
     * @param {boolean} start `true` to start the timer, `false` to stop it
     */
    autoRefresh: function(start) {
        if (start) {
            clearInterval(this._timerId);
            this._timerId = setInterval(() => {
                this.clearCache();
                this.getPage(this.page);
            }, 5 * 1000 * 60); // 5 min default
        } else {
            clearInterval(this._timerId);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose() {
        this.stopListening();
        this.autoRefresh(false);

        this._super('_dispose');
    },
})

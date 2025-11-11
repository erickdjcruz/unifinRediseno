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
 * @class View.Views.Reports.ListPaginationView
 * @alias SUGAR.App.view.views.ReportsListPaginationView
 * @extends View.Views.Base.BaseListPaginationView
 */
({
    extendsFrom: 'ListPaginationView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initialize helper data
     */
    _initProperties: function() {
        if (this.layout && this.layout.options) {
            this._panelWrapper = this.layout.options.panelWrapper;
        }

        this.context.set('isUsingListPagination', true);
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'report:data:table:build:count', this.buildCollectionCount);
        this.listenTo(this._panelWrapper, 'panel:collapse', this.togglePaginationWidget, this);
        this.listenTo(this._panelWrapper, 'panel:minimize', this.togglePaginationWidget, this);
    },

    /**
     * @inheritdoc
     */
    getPageCount: function() {
        if (this.context.get('previewMode')) {
            app.alert.show('report-preview-limitation', {
                level: 'warning',
                messages: app.lang.get('LBL_REPORTS_PREVIEW_LIMITATION'),
                autoClose: true
            });

            return;
        }

        this._super('getPageCount');
    },

    /**
     * @inheritdoc
     */
    getPage: function(page) {
        if (this.context.get('previewMode')) {
            app.alert.show('report-preview-limitation', {
                level: 'warning',
                messages: app.lang.get('LBL_REPORTS_PREVIEW_LIMITATION'),
                autoClose: true
            });

            return;
        }

        if (_.isString(page)) {
            page = parseInt(page);
        }

        let options = {
            reset: true,
            page: page,
            limit: this.collection.getOption('limit'),
            strictOffset: true
        };

        this.page = page;
        options.success = _.bind(this.successPagination, this);

        options.error = _.bind(this.errorPagination, this);

        if (this.restoreFromCache()) {
            options.success(false, false);
        } else {
            options.functionContext = this.layout.getComponent('rows-columns');
            this.context.trigger('rows-columns:load:collection', options);
        }
    },

    /**
     * Success pagination
     *
     * @param {Object} data
     * @param {boolean} shouldCache
     */
    successPagination: function(data, shouldCache = true) {
        this.layout.trigger('list:paginate:success');
        this.context.trigger('report:data:table:loaded', false, 'table');
        // Tell the side drawer that there are new records to look at
        if (app.sideDrawer) {
            app.sideDrawer.trigger('sidedrawer:collection:change', this.collection);
        }

        if (!_.isEmpty(data)) {
            const rowsAndColumnsComp = this.layout.getComponent('rows-columns');

            this.context.set('rebuildData', true);
            rowsAndColumnsComp.setData(data);
            rowsAndColumnsComp.render();
        }

        this.collection.page = this.page;
        this.collection.length = this.collection.models.length;

        // update count label
        this.context.trigger('list:paginate');

        this.render();

        if (shouldCache) {
            this.setCache();
        }

        this.context.trigger('report:data:table:build:count', this.collection);
    },

    /**
     * Error pagination handling
     *
     * @param {Object} error
     */
    errorPagination: function(error) {
        app.alert.show('list-pagination-error', {
            level: 'error',
            title: error.responseText,
        });
    },

    /**
     * @inheritdoc
     */
    restoreFromCache: function() {
        if (!this.cachedCollection[this.page]) {
            return false;
        }

        const cache = this.cachedCollection[this.page];
        this.collection.next_offset = cache.next_offset;
        this.collection.page = cache.page;
        this.collection.models = cache.models;

        this.page = cache.page;
        this.context.set('rebuildData', false);

        const rowsAndColumnsComp = this.layout.getComponent('rows-columns');

        rowsAndColumnsComp.collection = this.collection;
        rowsAndColumnsComp.render();

        return true;
    },

    /**
     * Hide/show pagination
     *
     * @param {boolean} hide
     */
    togglePaginationWidget: function(hide) {
        if (hide) {
            this.$el.hide();
        } else {
            this.$el.show();
        }
    },

    /**
     * Build collection count field
     *
     * @param {Object} collection
     */
    buildCollectionCount: function(collection) {
        if (this.disposed || _.isUndefined(collection)) {
            return;
        }

        this._disposeCollectionCount();

        this.collectionCount = app.view.createField({
            def: {
                type: 'collection-count',
                name: 'CollectionCount',
            },
            view: this,
            viewName: 'detail',
            model: this.model,
            collection
        });

        this.collectionCount.cachedCount = collection.total;
        this.collectionCount.updateCount();

        let countText = this.collectionCount.$el.find('.count').html();
        const showingLabel = app.lang.get('LBL_SHOWING', 'Reports');

        countText = `${showingLabel} ${countText}`;
        this.collectionCount.$el.find('.count').html(countText);
        this.collectionCount.$el.find('.count').removeClass('count');
        this.$('[data-container="collection-count-widget-container"]').append(this.collectionCount.$el);
    },

    /**
     * Dispose subcomponent
     */
    _disposeCollectionCount: function() {
        if (this.collectionCount) {
            this.collectionCount.dispose();
            this.collectionCount = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeCollectionCount();

        this._super('_dispose');
    },
})

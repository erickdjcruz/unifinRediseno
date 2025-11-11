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
 * @class View.Views.Reports.ReportDashletBdoy
 * @alias SUGAR.App.view.views.BaseReportDashletBody
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        this._cachedViews = {
            chartView: null,
            listView: null,
            filterView: null,
        };

        this._listViewType = 'list';
        this._filterViewType = 'filters';
        this._chartViewType = 'chart';

        this._activeViewType = this.layout.model.get('defaultSelectView');

        const lastState = this.layout.model.get('userLastState');

        if (lastState && _.has(lastState, 'defaultView')) {
            this._activeViewType = lastState.defaultView;
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'report-dashlet:change:view-type', this.changeDashletContent);
        this.listenTo(this.context, 'report-dashlet:refresh', this.refreshReportDashlet);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        const forceRender = false;

        this.renderDashletContent(forceRender);
    },

    /**
     * Hide the current view then show the selected one
     *
     * @param {Object} data
     */
    changeDashletContent: function(data) {
        const newDataType = data.type;

        this.hideCurrentElement(this._activeViewType);

        this._activeViewType = newDataType;

        this.renderDashletContent();
    },

    /**
     * Refresh current selected component
     */
    refreshReportDashlet: function() {
        const activeViewType = this._activeViewType;
        const activeView = this._cachedViews[activeViewType];

        if (!activeView) {
            return;
        }

        if (activeViewType === this._listViewType) {
            //we will trigger the list update
            this.context.trigger('runtime:filters:updated');
        } else if (activeViewType === this._chartViewType) {
            //we will trigget the chart update
            this.context.trigger('report:refresh');
        }
    },

    /**
     * Display the view
     *
     * @param {boolean} forceRender force to call the render
     */
    renderDashletContent: function(forceRender) {
        const viewType = this._activeViewType;

        switch (viewType) {
            case this._listViewType:
                this._renderListView(forceRender);
                break;
            case this._chartViewType:
                this._renderChartView(forceRender);
                break;
            case this._filterViewType:
                this._renderFilterView(forceRender);
                break;
            default:
                break;
        }
    },

    /**
     * Hide the current element before the selected one will be displayed
     *
     * @param {string} oldViewType
     */
    hideCurrentElement: function(oldViewType) {
        let oldView = this._cachedViews[oldViewType];

        if (oldView) {
            oldView.hide();
        }
    },

    /**
     * Create/Get from cache the Report Table then display it
     *
     * @param {string} forceRender force to call the render
     */
    _renderListView: function(forceRender) {
        const viewType = this._listViewType;
        const viewName = 'report-table';

        this._createViewController(viewType, viewName, forceRender);
    },

    /**
     * Create/Get from cache the Report Chart then display it
     *
     * @param {string} forceRender force to call the render
     */
    _renderChartView: function(forceRender) {
        const viewType = this._chartViewType;
        const viewName = 'report-chart';

        this._createViewController(viewType, viewName, forceRender);

        // chart needs to resize, as well as the legend
        this.context.trigger('container-resizing');
    },

    /**
     * Create/Get from cache the Report Filters then display it
     *
     * @param {string} forceRender force to call the render
     */
    _renderFilterView: function(forceRender) {
        const viewType = this._filterViewType;
        const viewName = 'report-filters';

        this._createViewController(viewType, viewName, forceRender);
    },

    /**
     * Create view controller
     *
     * @param {string} viewType
     * @param {string} viewName
     * @param {boolean} forceRender
     */
    _createViewController: function(viewType, viewName, forceRender) {
        if (this._cachedViews[viewType]) {
            this._cachedViews[viewType].show();
        } else {
            const capitalizedViewType = app.utils.capitalize(viewType);
            const disposeFctName = `_dispose${capitalizedViewType}`;

            this[disposeFctName]();

            const customCssClass = viewName + '-dashlet-view-container';

            this._cachedViews[viewType] = app.view.createView({
                name: viewName,
                module: 'Reports',
                context: this.context,
                model: this.layout.model,
                stayCollapsed: true,
                useCustomReportDef: true,
                bypassFiltersSync: true,
                layout: this.layout,
                className: 'inherit-width-height inline ' + customCssClass,
            });

            this._cachedViews[viewType].render();

            this.$('.bodyPlaceholder').append(this._cachedViews[viewType].$el);

            if (forceRender) {
                this.render();
            }
        }

    },

    /**
     * Dispose the list view
     */
    _disposeList: function() {
        const listKey = this._listViewType;

        this._disposeCachedChild(listKey);
    },

    /**
     * Dispose the chart view
     */
    _disposeChart: function() {
        const chartKey = this._chartViewType;

        this._disposeCachedChild(chartKey);
    },

    /**
     * Dispose the filter view
     */
    _disposeFilters: function() {
        const filtertKey = this._filterViewType;

        this._disposeCachedChild(filtertKey);
    },

    /**
     * Dispose component
     */
    _disposeCachedChild: function(key) {
        if (this._cachedViews[key]) {
            this._cachedViews[key].dispose();
            this._cachedViews[key] = null;
        }
    },

    /**
     * Dispose the list/chart/filter view
     */
    _disposeCachedChildren: function() {
        this._disposeList();
        this._disposeChart();
        this._disposeFilters();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeCachedChildren();

        this._super('_dispose');
    },
});

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
 * @class View.Views.Base.ReportDashletFilterView
 * @alias SUGAR.App.view.views.BaseReportDashletFilterView
 * @extends View.View
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
     * Property initialization
     *
     */
    _initProperties: function() {
        this._runtimeFilters = {};
        this._filtersDef = false;
        this._usePreviewClasses = this.options.usePreviewClasses;
        this.RECORD_NOT_FOUND_ERROR_CODE = 404;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'runtime:filter:changed', this.runtimeFilterChanged, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._getReportFiltersOperators();
    },

    /**
     * Update filters def
     *
     * @param {Object} data
     */
    runtimeFilterChanged: function(data) {
        if (!this._filtersDef || this.disposed) {
            return;
        }

        this._updateFilterDefinition(this._filtersDef, data.filterData);

        this.model.set('filtersDef', this._filtersDef);
        this.model.trigger('change:filtersDef', this.model, this._filtersDef);
        this.context.trigger('refresh:preview:controller');
    },

    /**
     * Update filterDefition
     *
     * @param {Object} filters
     * @param {Object} runtimeFilters
     */
    _updateFilterDefinition: function(filters, filterDef) {
        _.each(filters, function goThroughFilters(filter) {
            if (filter.operator) {
                this._updateFilterDefinition(filter, filterDef);
            } else if (filterDef.name === filter.name && filterDef.table_key === filter.table_key) {
                _.each(filterDef, function updateValue(prop, key) {
                    filter[key] = prop;
                }, this);
            }
        }, this);
    },

    /**
     * Show/Hide the hidden filters widget
     *
     * @param {boolean} show
     */
    _showHiddenFilters: function(show) {
        const emptyFiltersEl = this.$('[data-widget="report-hidden-filters"]');

        if (show) {
            this._showFilters(false);
            emptyFiltersEl.removeClass('hidden');
        } else {
            emptyFiltersEl.addClass('hidden');
        }
    },

    /**
     * Show/Hide the empty filters widget
     *
     * @param {boolean} show
     */
    _showEmptyFilters: function(show) {
        const emptyFiltersEl = this.$('[data-widget="report-no-filters"]');

        if (show) {
            this._showFilters(false);
            emptyFiltersEl.removeClass('hidden');
        } else {
            emptyFiltersEl.addClass('hidden');
        }
    },

    /**
     * Show/Hide the filters widget
     *
     * @param {boolean} show
     */
    _showFilters: function(show) {
        const emptyFiltersEl = this.$('[data-container="filters-container"]');

        if (show) {
            emptyFiltersEl.show();
        } else {
            emptyFiltersEl.hide();
        }
    },

    /**
     * Makes a call to Reports/:id/filter to fetch specific saved report data
     */
    _getReportFiltersOperators: function() {
        const reportId = this.model.get('reportId');
        const url = app.api.buildURL('Reports/' + reportId + '/filter?use_saved_filters=false');

        app.api.call('read', url, null, {
            success: _.bind(this._tryBuildFilters, this),
            error: _.bind(this._failedLoadReportData, this),
        });
    },

    /**
     * Handle the report failed
     *
     * @param {Error} error
     */
    _failedLoadReportData: function(error) {
        if (this.disposed) {
            return;
        }

        let showErrorAlert = error && _.isString(error.message);

        // don't show no access alert for dashlet
        if (error && _.has(error, 'status') && error.status === this.RECORD_NOT_FOUND_ERROR_CODE) {
            showErrorAlert = false;
        }

        if (showErrorAlert) {
            app.alert.show('failed_to_load_report', {
                level: 'error',
                messages: error.message,
                autoClose: true,
            });
        }

        this._showEmptyFilters(true);
    },

    /**
     * Build filters widgets
     *
     * @return {boolean}
     */
    _tryBuildFilters: function(data) {
        if (!this.model || this.disposed) {
            return;
        }

        const filters = this.model.get('filtersDef');

        if (!filters) {
            this._showEmptyFilters(true);

            return;
        }

        const allFilters = this._getValidFilters(filters.Filter_1);
        const runtimeFilters = this._getRuntimeFilters(this._getValidFilters(filters), []);

        if (_.isEmpty(allFilters)) {
            this._showEmptyFilters(true);
        } else if (_.isEmpty(runtimeFilters)) {
            this._showHiddenFilters(true);
        }

        this._disposeFilters();

        _.each(runtimeFilters, function buildFilter(runtimeFilter) {
            this._buildFilter(app.utils.deepCopy(runtimeFilter), data);
        }, this);

        this._filtersDef = filters;

        return !_.isEmpty(runtimeFilters);
    },

    /**
     * Build filter widget element
     *
     * @param {Object} filterData
     * @param {Object} operators
     */
    _buildFilter: function(filterData, reportData) {
        const runtimeFilterId = app.utils.generateUUID();

        const runtimeFilterWidget = app.view.createView({
            type: 'report-runtime-filter-widget',
            context: this.context,
            module: 'Reports',
            stayCollapsed: this.options.stayCollapsed,
            hideToolbar: this.options.hideToolbar,
            reportData: this.model,
            filterData,
            runtimeFilterId,
            operators: reportData.runtimeOperators,
            users: reportData.users,
        });

        runtimeFilterWidget.render();

        this.$('[data-container="filters-container"]').append(runtimeFilterWidget.$el);

        this._runtimeFilters[runtimeFilterId] = runtimeFilterWidget;
    },

    /**
     * Returns all the runtime filters
     *
     * @param {Object} filters
     * @param {Array} runtimeFilters
     * @return {Array}
     */
    _getRuntimeFilters: function(filters, runtimeFilters) {
        _.each(filters, function goThroughFilters(filter) {
            if (_.isEmpty(filter.operator)) {
                if (filter.runtime === 1) {
                    runtimeFilters.push(filter);
                }
            } else {
                const validFilters = this._getValidFilters(filter);

                runtimeFilters = this._getRuntimeFilters(validFilters, runtimeFilters);
            }
        }, this);

        return runtimeFilters;
    },

    /**
     * Returns only filters, without operators
     * @param {Object} filter
     * @return {Array}
     */
    _getValidFilters: function(filter) {
        return _.chain(filter)
                .values()
                .filter(function ignoreOperator(filterData) {
                    return _.isObject(filterData);
                })
                .value();
    },

    /**
     * Dispose runtime filters elements
     */
    _disposeFilters: function() {
        _.each(this._runtimeFilters, function disposeWidget(widget) {
            widget.dispose();
        }, this);

        this._runtimeFilters = {};
        this.$('[data-container="filters-container"]').empty();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeFilters();

        this._super('_dispose');
    },
})

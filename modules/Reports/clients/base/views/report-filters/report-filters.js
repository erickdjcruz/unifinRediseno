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
 * @class View.Views.Base.Reports.ReportFiltersView
 * @alias SUGAR.App.view.views.BaseReportsReportFiltersView
 * @extends View.Views.Base.View
 */
({
    className: 'report-filters-panel contents h-full',
    plugins: ['ReportsPanel'],
    events: {
        'click [data-action="apply-runtime-filters"]': 'applyRuntimeFilters',
    },

    /**
     * Before init properties
     */
    _beforeInit: function() {
        this._reportData = app.data.createBean();
        this._runtimeFilters = {};
        this._runtimeFiltersDef = {};

        this.dataType = 'filters';
        this.RECORD_NOT_FOUND_ERROR_CODE = 404;
        this.SERVER_ERROR_CODES = [500, 502, 503, 504];
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'runtime:filter:changed', this.runtimeFilterChanged, this);
        this.listenTo(this.context, 'runtime:filter:broken', this.disableApplyRuntimeFiltersButtons, this);
        this.listenTo(this.context, 'reset:to:default:filters', this.resetToDefaultFilters, this);
        this.listenTo(this.context, 'copy:filters:to:clipboard', this.copyFiltersToClipboard, this);
        this.listenTo(this.context, 'dashboard-filters-meta-ready', this.integrateDashboardFilters);
    },

    /**
     * Reset filters to default ones
     */
    resetToDefaultFilters: function() {
        this._loadReportData(true, _.bind(this._updateReportFilters, this));
    },

    /**
     * Update filters
     */
    _updateFilters: function() {
        const bypassFiltersSync = this.options.bypassFiltersSync;

        if (bypassFiltersSync) {
            this._notifyFiltersChanged();
        } else {
            this._updateReportFilters();
        }
    },

    /**
     * Notify filters changed
     */
    _notifyFiltersChanged: function() {
        this.$('button[data-action="apply-runtime-filters"]').prop('disabled', true);

        this.context.trigger('reports:filters:changed', this._runtimeFiltersDef);
    },

    /**
     * Update Report's Cache filters
     */
    _updateReportFilters: function() {
        const reportId = this.model.get('id');
        const url = app.api.buildURL('Reports/' + reportId + '/updateReportFilters');

        app.api.call('create', url, {
            runtimeFilters: this._runtimeFiltersDef,
        }, {
            success: _.bind(this.notifyRuntimeFiltersUpdated, this),
        });
    },

    /**
     * Inform everyone that the filters have been updated
     */
    notifyRuntimeFiltersUpdated: function() {
        this.context.trigger('runtime:filters:updated', this._runtimeFiltersDef);

        this.$('button[data-action="apply-runtime-filters"]').prop('disabled', true);
    },

    /**
     * Deactivate filters that are already a part of a dashboard filter
     *
     * @param {Array} filtersAffected
     */
    integrateDashboardFilters: function(filtersAffected) {
        _.each(this._runtimeFilters, (runtimeController) => {
            runtimeController.enableRuntimeFilter();

            _.each(filtersAffected, (affectedFilter) => {
                if (runtimeController._filterData.name === affectedFilter.fieldName &&
                    runtimeController._filterData.table_key === affectedFilter.tableKey) {
                    runtimeController.disableRuntimeFilter();
                }
            });
        });
    },

    /**
     * Copy filters text to clipboard
     */
    copyFiltersToClipboard: function() {
        const textToCopy = JSON.stringify(this._runtimeFiltersDef);
        const successCopyAlertData = {
            level: 'success',
            messages: app.lang.get('LBL_RUNTIME_FILTERS_COPIED'),
            autoClose: true,
        };

        // navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy);

            app.alert.show('runtime-filter-copied', successCopyAlertData);
        } else {
            let textarea = document.createElement('textarea');

            textarea.textContent = textToCopy;
            textarea.style.position = 'fixed';

            document.body.appendChild(textarea);
            textarea.select();

            try {
                document.execCommand('copy');

                app.alert.show('runtime-filter-copied', successCopyAlertData);
            }
            catch (ex) {
                console.warn('Copy to clipboard failed.', ex);
            }
            finally {
                document.body.removeChild(textarea);
            }
        }
    },

    /**
     * Return the report data
     */
    getReportData: function() {
        return this._reportData;
    },

    /**
     * Return the runtime filters
     */
    getRuntimeFilters: function() {
        return this._runtimeFiltersDef;
    },

    /**
     * Return the raw runtime filters
     */
    getRawRuntimeFilters: function() {
        return this._runtimeFilters;
    },

    /**
     * Notify everyone that we have new filters
     */
    applyRuntimeFilters: function() {
        if (this._canApplyFilters()) {
            this.$('.apply-filters-btn').addClass('disabled').attr('disabled', true);
            this._reportData.set('filtersDef', app.utils.deepCopy(this._runtimeFiltersDef));
            this._updateFilters();
        } else {
            app.alert.show('runtime-filter-invalid', {
                level: 'warning',
                messages: app.lang.get('LBL_RUNTIME_FILTERS_INVALID'),
                autoClose: true,
            });

            return;
        }
    },

    /**
     * Update filters def
     *
     * @param {Object} data
     */
    runtimeFilterChanged: function(data) {
        this.$('.apply-filters-btn').removeClass('disabled').removeAttr('disabled');
        this._updateFilterDefinition(this._runtimeFiltersDef, data);
    },

    /**
     * Disable button
     */
    disableApplyRuntimeFiltersButtons: function() {
        this.$('.apply-filters-btn').addClass('disabled').attr('disabled', true);
    },

    /**
     * Check if all the filter values are valid
     *
     * @return {boolean}
     */
    _canApplyFilters: function() {
        let canApply = true;

        _.each(this._runtimeFilters, function isValid(runtimeWidget) {
            if (!runtimeWidget.isValid()) {
                canApply = false;
            }
        }, this);

        return canApply;
    },

    /**
     * Get the report and filters data
     *
     * @param {boolean} ignoreSavedFilters
     * @param {Function} callback
     */
    _loadReportData: function(ignoreSavedFilters, callback) {
        const useSavedFilters = ignoreSavedFilters ? 'false' : 'true';
        const reportId = this.model.get('id');
        const url = app.api.buildURL('Reports/' + reportId + '/filter?use_saved_filters=' + useSavedFilters);

        app.api.call('read', url, null, {
            success: _.bind(this._storeReportData, this, callback),
            error: _.bind(this._failedLoadReportData, this),
        });
    },

    /**
     * Setup preview widget view
     */
    _setupPreviewReportPanel: function() {
        this._storeReportData(false, this.context.get('previewData').filtersData);
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

        this._showEmptyFilters(true, true);

        this.context.trigger('report:data:filters:loaded', false, this.dataType);

        let reportModel = this.context.get('model');

        if (!reportModel.get('report_type') && this.layout) {
            reportModel = this.layout.model;
        }

        let showErrorAlert = error && _.isString(error.message);

        // don't show no access alert for dashlet
        if (error && reportModel.get('filter') && _.has(error, 'status') &&
            error.status === this.RECORD_NOT_FOUND_ERROR_CODE) {
            showErrorAlert = false;
        }

        if (showErrorAlert) {
            app.alert.show('failed_to_load_report', {
                level: 'error',
                messages: error.message,
                autoClose: true,
            });
        }

        // don't show alert for dashlets
        if (!reportModel.get('filter')) {
            const message = app.utils.tryParseJSONObject(error.responseText);
            let errorMessage = message ? message.error_message : error.responseText;
            const targetReportId = reportModel.get('id') || reportModel.get('report_id');

            if (_.isEmpty(errorMessage) || error.status === this.RECORD_NOT_FOUND_ERROR_CODE) {
                errorMessage = app.lang.get('LBL_NO_ACCESS', 'Reports');
            }

            if (this.SERVER_ERROR_CODES.includes(error.status)) {
                errorMessage = app.lang.get('LBL_SERVER_ERROR', 'Reports');
            }

            app.alert.show('report-data-error', {
                level: 'error',
                title: errorMessage,
                messages: app.lang.getModuleName('Reports') + ': ' + targetReportId,
            });
        }

        this.context.set(
            'permissionsRestrictedReport',
            error.status === this.RECORD_NOT_FOUND_ERROR_CODE
        );
    },

    /**
     * Setup and store report data
     *
     * @param {Object} data
     * @param {Function} callback
     */
    _storeReportData: function(callback, filtersData) {
        if (this.disposed) {
            return;
        }

        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this._failedLoadReportData({});

            return;
        }

        const filterMeta = this.model.get('filter') || {};

        let filtersMeta = filtersData.reportDef.filters_def;

        const lastStateKey = this.model.get('lastStateKey');
        const customFilterMeta = this._getCustomFiltersMeta(filterMeta, lastStateKey);

        if (!_.isEmpty(customFilterMeta)) {
            filtersMeta = customFilterMeta;
        }

        const filtersDef = filtersMeta ? this._getValidFilters(filtersMeta.Filter_1) : {};

        if (_.isEmpty(filtersDef)) {
            this._showEmptyFilters(true);
            this.context.trigger('report:data:filters:loaded', false, this.dataType);

            return;
        }

        this._reportData.set('filtersDef', filtersMeta);
        this._reportData.set('fullTableList', filtersData.reportDef.full_table_list);
        this._reportData.set('operators', filtersData.runtimeOperators);
        this._reportData.set('users', filtersData.users);

        this._runtimeFiltersDef = app.utils.deepCopy(this._reportData.get('filtersDef'));

        const validFilters = this._tryBuildFilters();

        if (!validFilters) {
            this.context.trigger('report:data:filters:loaded', false, this.dataType);

            return;
        }

        this.context.trigger('report:data:filters:loaded', false, this.dataType);
        this._setTitle('LBL_REPORTS_FILTERS');

        this.layout.trigger('panel:widget:finished:loading', false, false);

        if (this.context.get('previewMode')) {
            this.$('.report-filters-container :input').attr('disabled', true);
        }

        this.context.trigger('filters-loaded-successfully');

        if (callback) {
            callback();
        }
    },

    /**
     * Show/Hide the filters widget
     *
     * @param {boolean} show
     */
    _showFilters: function(show) {
        const emptyFiltersEl = this.$('[data-container="filters-container"]');
        const applyButtonEl = this.$('[data-container="apply-button-container"]');

        if (show) {
            emptyFiltersEl.show();
            applyButtonEl.show();
        } else {
            emptyFiltersEl.hide();
            applyButtonEl.hide();

            if (!this.context.get('previewMode')) {
                this._hideResetFilterButton();
            }
        }
    },

    /**
     * Hide Reset filter button when there are no active filters
     */
    _hideResetFilterButton: function() {
        const toolbar = this.layout.getComponent('report-filters-toolbar');

        if (!toolbar) {
            return;
        }

        const dropdownButtons = toolbar.getField('action_menu').dropdownFields;
        let resetBtnIdx = -1;

        _.each(dropdownButtons, function getResetButton(button, idx) {
            if (button.name === 'reset') {
                resetBtnIdx = idx;
            }
        }, this);

        dropdownButtons.splice(resetBtnIdx, 1);
    },

    /**
     * Show/Hide the hidden filters widget
     *
     * @param {boolean} show
     */
    _showHiddenFilters: function(show) {
        const emptyFiltersEl = this.$('[data-widget="report-hidden-filters"]');

        if (show) {
            this.context.trigger('report:data:filters:loaded', false, this.dataType);
            this._showFilters(false);
            this._setTitle('LBL_REPORTS_FILTERS', false);

            emptyFiltersEl.removeClass('hidden');
        } else {
            emptyFiltersEl.addClass('hidden');
        }
    },

    /**
     * Show/Hide the empty filters widget
     *
     * @param {boolean} show
     * @param {boolean} noAccess
     */
    _showEmptyFilters: function(show, noAccess) {
        const elId = noAccess ? 'report-no-data' : 'report-no-filters';
        const emptyFiltersEl = this.$(`[data-widget="${elId}"]`);

        this.context.trigger('report:data:filters:loaded', !show, this.dataType);
        this._showFilters(!show);
        this._setTitle('LBL_REPORTS_FILTERS', false);

        emptyFiltersEl.toggleClass('hidden', !show);
    },

    /**
     * Show the apply filters button
     */
    _hideAdditionalComponents: function() {
        this.$('[data-container="apply-button-container"]').removeClass('hidden');
    },

    /**
     * Set the report title
     *
     * @param {Mixed} title
     * @param {boolean} showNumberOfFilters
     */
    _setTitle: function(title, showNumberOfFilters) {
        const panelToolbar = this.layout.getComponent('report-filters-toolbar');

        if (_.isEmpty(panelToolbar)) {
            return;
        }

        const filtersLabel = title ? title : 'Filters';
        let filtersTitle = app.lang.get(filtersLabel, 'Reports');

        if (showNumberOfFilters) {
            filtersTitle = filtersTitle + ' (' + _.keys(this._runtimeFilters).length + ')';
        }

        panelToolbar.$('.dashlet-title').text(filtersTitle);
    },

    /**
     * Build filters widgets
     *
     * @return {boolean}
     */
    _tryBuildFilters: function() {
        const filters = this._runtimeFiltersDef;

        const runtimeFilters = this._getRuntimeFilters(this._getValidFilters(filters), []);

        if (_.isEmpty(runtimeFilters)) {
            this._showHiddenFilters(true);
        }

        this._disposeFilters();

        _.each(runtimeFilters, function buildFilter(runtimeFilter) {
            this._buildFilter(app.utils.deepCopy(runtimeFilter));
        }, this);

        return !_.isEmpty(runtimeFilters);
    },

    /**
     * Build filter widget element
     *
     * @param {Object} filterData
     */
    _buildFilter: function(filterData) {
        const runtimeFilterId = filterData.runtimeFilterId;

        const runtimeFilterWidget = app.view.createView({
            module: 'Reports',
            type: 'report-runtime-filter-widget',
            context: this.context,
            reportData: this._reportData,
            stayCollapsed: !!this.options.stayCollapsed,
            hideToolbar: !!this.options.hideToolbar,
            filterData,
            runtimeFilterId,
        });

        runtimeFilterWidget.render();

        this.$('[data-container="filters-container"]').append(runtimeFilterWidget.$el);

        this._runtimeFilters[runtimeFilterId] = runtimeFilterWidget;
    },

    /**
     * Update filterDefition
     *
     * @param {Object} filters
     * @param {Object} runtimeFilterData
     */
    _updateFilterDefinition: function(filters, runtimeFilterData) {
        let filterDef = runtimeFilterData.filterData;

        _.each(filters, function goThroughFilters(filter) {
            if (filter.operator) {
                this._updateFilterDefinition(filter, runtimeFilterData);
            } else if (filterDef.name === filter.name &&
                filterDef.table_key === filter.table_key &&
                runtimeFilterData.runtimeFilterId === filter.runtimeFilterId
            ) {
                _.each(filterDef, function updateValue(prop, key) {
                    filter[key] = prop;
                }, this);
            }
        }, this);
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
                    filter.runtimeFilterId = app.utils.generateUUID();
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

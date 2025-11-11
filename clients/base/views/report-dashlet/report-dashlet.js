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
 * @class View.Views.Base.ReportDashletView
 * @alias SUGAR.App.view.views.BaseReportDashletView
 * @extends View.View
 */
 ({
    plugins: ['Dashlet', 'GridBuilder', 'ReportIntelligenceDashlet'],

    events: {
        'click [data-fieldname="intelligent"] input': '_setIntelligence',
        'click [data-bs-toggle="tab"]': '_changeTab',
    },

    /**
     * @inheritdoc
     */
    initDashlet: function(viewName) {
        this._mode = viewName;

        // transform meta for rendering
        if (this.meta.config) {
            this._initConfigProperties();

            if (this.dashletConfig) {
                this.meta.panels = this.dashletConfig.dashlet_config_panels;

                this._buildGridsFromPanelsMetadata();
            }
        } else {
            this._initDashletProperties();

            if (!this.meta.preview) {
                this.setupAutoRefresh();
            }
        }

        this._registerEvents();

        this._syncReport();
    },

    /**
     * Init Dashlet properties
     */
    _initDashletProperties: function() {
        this.reportId = this.settings.get('reportId');
        this._oldReportId = this.reportId;
        this._dashletId = this.layout.dashletId;
        this.lastStateKey = this._buildUserLastStateKey(this.reportId);
        this.userLastState = this._getUserLastState(this.lastStateKey);
        this.defaultSelectView = this.userLastState.defaultView;
        this.autoRefresh = this.settings.get('autoRefresh');
        this.RECORD_NOT_FOUND_ERROR_CODE = 404;

        this.isDashletLoading = false;
        this.saveDashboardOnSync = true;
    },

    /**
     * Initialize the properites configuration
     */
    _initConfigProperties: function() {
        this._categoryViews = [];
        this._previewController = false;
        this.reportId = this.settings.get('reportId');
        this._oldReportId = this.reportId;
        this._listenToFields = ['sortColumnList', 'sortOrderList', 'showTotalRecordCount', 'chartType', 'showXLabel',
                            'showYLabel', 'showLegend', 'showTitle', 'primaryChartColumn', 'secondaryChartColumn',
                            'defaultView', 'filtersDef', 'listOrderBy', 'primaryChartOrder', 'secondaryChartOrder'];

        if (!this.settings.get('limit')) {
            const listLimitOptions = app.lang.getAppListStrings('dashlet_limit_options');
            this.settings.set('limit', _.chain(listLimitOptions).values().first().value());
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        if (this.meta.config) {
            this.layout.before('dashletconfig:save', this._validateConfig, this);

            this.listenTo(this.context, 'refresh:preview:controller', this.refreshPreviewController, this);
        } else {
            this.listenTo(this.context, 'reports:filters:changed', this.dashletFiltersChanged, this);
        }
    },

    /**
     * @inheritdoc
     */
    loadData: function() {
        this.refreshResults();
    },

    /**
      * Sync with report data
      */
    _syncReport: function() {
        this.isDashletLoading = true;

        const reportId = this.settings.get('reportId');

        if (!reportId) {
            return;
        }

        this._getReportDataById(
            reportId,
            _.bind(this.syncReportData, this),
            _.bind(this._failedLoadReportData, this)
        );
    },

    /**
     * Setup auto refresh
     */
    setupAutoRefresh: function() {
        const autoRefresh = this.autoRefresh;

        if (!autoRefresh) {
            return;
        }

        //convert to milli seconds
        const timer = parseInt(autoRefresh, 10) * 60 * 1000;

        if (Number.isNaN(timer) || timer < 0) {
            return;
        }

        if (timer > 0) {
            this._disableAutoRefresh();
            this._enableAutoRefresh(timer);
        }
    },

    /**
     * Disable activated refresh interval
     *
     * @protected
     */
    _disableAutoRefresh: function() {
        if (this.timerId) {
            clearInterval(this.timerId);
            this.timerId = null;
        }

        return this;
    },

    /**
     * Activate auto refresh data fetch.
     *
     * @param {Integer} msec Interval time in milli seconds(msec > 0).
     * @protected
     */
    _enableAutoRefresh: function(msec) {
        this.timerId = setInterval(
            _.bind(function() {
                this.refreshResults();
            }, this),
            msec
        );

        return this;
    },

    /**
     * Handle report changed
     *
     * @param {Object} data
     */
    syncReportData: function(data) {
        const newReportDateModified = data.dateModified;
        const reportId = data.reportId;
        const currentUserReportDateModified = this._getUserLastReportDateFromState(reportId);

        let userDataChanged = false;

        if (_.isEmpty(reportId)) {
            this._toggleIntelligence(false);
            this._toggleTabs(false);
            this.isDashletLoading = false;
            this.saveDashboardOnSync = true;

            return;
        }

        // check if report changed since the last user visit
        if (currentUserReportDateModified) {
            userDataChanged = !moment(newReportDateModified).isSame(currentUserReportDateModified);
        }

        if (userDataChanged) {
            // reset user state for the old key
            this._resetUserState(this.lastStateKey);
        }

        this.reportId = reportId;
        this.lastStateKey = this._buildUserLastStateKey(reportId);

        // if same report but metadata changed
        if (userDataChanged) {
            const alertId = app.utils.generateUUID();

            app.alert.show(alertId, {
                level: 'info',
                messages: app.lang.get(
                    'LBL_REPORT_DASHLET_REST_TO_REPORT_DEFAULTS',
                    null,
                    {
                        label: this.settings.get('label'),
                    }
                ),
            });

            // reset user state for the new key
            this._resetUserState(this.lastStateKey);
        }

        this.setupReportProperties(data);

        this.saveDashboardOnSync = true;
    },

    /**
      * Handle report changed
      *
      * @param {Object} model
      */
    reportChanged: function(model) {
        const reportId = model.get('reportId');

        if (!reportId) {
            return;
        }

        this._syncReport();
    },

    /**
     * Retrieves report dashlet related properties
     *
     * @return {Object}
     */
    getDashletSpecificData: function() {
        let currentUserRestrictedDashlet = false;

        const dashModelMeta = this.dashModel.get('metadata');
        const dashletMetaId = this.layout.options.dashletMetaId;

        if (_.isArray(dashModelMeta.currentUserRestrictedDashlets)) {
            if (_.contains(dashModelMeta.currentUserRestrictedDashlets, dashletMetaId)) {
                currentUserRestrictedDashlet = true;
            }
        }

        return {
            reportId: this.settings.get('reportId'),
            currentUserRestrictedDashlet,
        };
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.meta.config) {
            this.listenTo(this.settings, 'change:reportId', _.bind(this.reportChanged, this, this.settings));

            this.listenTo(this.model, 'change:label', this.refreshLabel, this);

            _.each(this._listenToFields, function refresh(fieldName) {
                this.listenTo(this.settings, 'change:' + fieldName, function refreshPreview() {
                    this.refreshPreviewController();

                    if (_.isFunction(this[fieldName + 'Changed'])) {
                        this[fieldName + 'Changed']();
                    }
                }, this);
            }, this);
        }
    },

    /**
     * Refresh label
     *
     * @param {Object} model
     */
    refreshLabel: function(model) {
        this.settings.set('label', model.get('label'));

        this.refreshPreviewController();
    },

    /**
     * Download chart image
     */
    downloadChart: function() {
        let langKey = 'LBL_REPORTS_DASHLET_UNABLE_TO_DOWNLOAD_CHART_TAB';
        const encodeCanvas = true;


        let dashletBody = this._reportDashletWrapper ?
                        this._reportDashletWrapper.getComponent('report-dashlet-body') :
                        false;
        let reportChart = dashletBody ? dashletBody._cachedViews.chart : false;
        let chartField = reportChart ? reportChart._chartField : false;

        if (!chartField) {
            app.alert.show('failed_to_get_chart_canvas', {
                level: 'warning',
                messages: app.lang.get(langKey),
            });

            return false;
        }

        let chartDownloaded = false;

        chartField.chart.chart.options.animation.onComplete = () => {
            if (this.disposed) {
                return;
            }

            if (chartDownloaded) {
                delete chartField.chart.chart.options.animation.onComplete;
                reportChart.context.trigger('report:data:chart:loaded', false, 'chart');
                reportChart._showChart(true);

                return;
            }

            const chart = this._getChartElement(langKey, encodeCanvas);

            if (chart === false) {
                return;
            }

            const reportName = this.settings.get('reportName');
            const userName = app.user.get('full_name');
            const date = app.date.format(new Date(), 'Y-m-d H:m:s');
            const fileName = `${reportName}_chart_${userName}_${date}.png`;

            let link = document.createElement('a');
            link.download = fileName;
            link.href = chart;
            link.click();

            _.debounce(() => {
                chartField.chart.chart.legend.options.display = false;

                if (chartField.chart.chart.options.title) {
                    chartField.chart.chart.options.title.display = false;
                }

                if (chartField.chart.chart.options.plugins && chartField.chart.chart.options.plugins.title) {
                    chartField.chart.chart.options.plugins.title.display = false;
                }

                chartField.chart.chart.update();
            })();

            chartDownloaded = true;
        };

        reportChart.context.trigger('report:data:chart:loaded', true, 'chart');
        reportChart._showChart(false);

        chartField.chart.chart.legend.options.display = !!this.settings.get('showLegend') &&
                                                        this.settings.get('chartType') !== 'treemapF';

        if (chartField.chart.chart.options.title) {
            chartField.chart.chart.options.title.display = this.settings.get('showTitle');
        }

        if (chartField.chart.chart.options.plugins && chartField.chart.chart.options.plugins.title) {
            chartField.chart.chart.options.plugins.title.display = this.settings.get('showTitle');
        }

        chartField.chart.chart.update();
    },

    /**
     * Get chart canvas as dataURL
     *
     * @param {string} langKey
     * @param {boolean} defaultEncode
     *
     * @return {string|boolean}
     */
    _getChartElement: function(langKey, defaultEncode) {
        const chartParent = this.$('[data-content="chart"]');
        let chartCanvas = _.first(chartParent.find('canvas'));

        if (!chartCanvas) {
            app.alert.show('failed_to_get_chart_canvas', {
                level: 'warning',
                messages: app.lang.get(langKey),
            });

            return false;
        }

        const isDarkMode = app.utils.isDarkMode();
        const xPosition = 0;
        const yPosition = 0;

        let newCanvas = document.createElement('canvas');
        newCanvas.width = chartCanvas.width;
        newCanvas.height = chartCanvas.height;

        destCtx = newCanvas.getContext('2d');

        if (!isDarkMode) {
            destCtx.fillStyle = 'white';
        }

        destCtx.fillRect(xPosition, yPosition, chartCanvas.width, chartCanvas.height);
        destCtx.drawImage(chartCanvas, xPosition, yPosition);

        chartCanvas = newCanvas;

        if (defaultEncode) {
            const encodedChart = chartCanvas.toDataURL();

            return encodedChart;
        }

        let img = document.createElement('img');
        img.src = chartCanvas.toDataURL();

        return img;
    },

    /**
     * Update user last state
     *
     * @param {string} key
     * @param {string|undefined} mappingKey
     */
    _updateLastState: function(key, mappingKey) {
        const reportId = this.settings.get('reportId');

        if (!reportId) {
            return;
        }

        const lastStateKey = this._buildUserLastStateKey(reportId);
        const lastState = this._getUserLastState(lastStateKey);

        if (mappingKey) {
            lastState[mappingKey] = this.settings.get(key);
        } else {
            lastState[key] = this.settings.get(key);
        }

        app.user.lastState.set(lastStateKey, lastState);
    },

    filtersDefChanged: function() {
        this._updateLastState('filtersDef');
    },

    /**
     * Dashlet runtime filters changed
     *
     * @param {Object} runtimeFilters - runtime filters def
     */
    dashletFiltersChanged: function(runtimeFilters) {
        this.userLastState = this._getUserLastState(this.lastStateKey);

        this.userLastState.filtersDef = runtimeFilters;

        app.user.lastState.set(this.lastStateKey, this.userLastState);

        this._createAndShowDashlet();
    },

    /**
     * Refresh preview controller
     */
    refreshPreviewController: function() {
        if (this._previewController) {
            this._previewController.refreshPreview();
        }
    },

    /**
     * Update the help popup body
     */
    primaryChartColumnChanged: function() {
        this._setModalInfo('primaryChartColumn', 'primaryChartColumnInfo');
        this.orderByColumnChanged('primaryChartColumn', 'primaryChartOrder', 'chartPrimaryOrderBy', 'Chart');
    },

    /**
     * Update the help popup body
     */
    secondaryChartColumnChanged: function() {
        this._setModalInfo('secondaryChartColumn', 'secondaryChartColumnInfo');
        this.orderByColumnChanged('secondaryChartColumn', 'secondaryChartOrder', 'chartSecondaryOrderBy', 'Chart');
    },

    /**
     * Given the column name, sets the modal body info
     *
     * @param {string} columnName
     * @param {string} columnInfoName
     * @return {string|undefined}
     */
    _setModalInfo: function(columnName, columnInfoName) {
        const column = _.find(this.settings.get('groupDefs'), function(groupDef) {
            return groupDef.name === this.settings.get(columnName);
        }, this);

        if (!column) {
            return;
        }

        let table = column.table_key.replaceAll(':', ' > ');

        table = table.replace('self', this.settings.get('module'));

        // capitalize each word in sentence
        table = table.replace(/(^\w{1})|(\s+\w{1})/g, function capitalize(letter) {
            return letter.toUpperCase();
        });

        const columnLabel = column.label;
        const popupInfo = `${table} > ${columnLabel}`;

        this.getField(columnInfoName).setPopupInfo(popupInfo);
    },

    /**
     * Chart type has been changed
     */
    chartTypeChanged: function() {
        this._toggleChartFields();
        this._setChartSortFields(true);
    },

    /**
     * update orderBy object
     *
     * @param {string} columnKey
     * @param {string} orderKey
     * @param {string} orderTypeKey
     * @param {string} columnType
     */
    orderByColumnChanged: function(columnKey, orderKey, orderTypeKey, columnType) {
        const columns = this._getColumns(columnType);

        if (!columns) {
            return;
        }

        const sortColumn = this.settings.get(columnKey);

        if (!sortColumn) {
            this.settings.set(orderTypeKey, undefined);
            return;
        }

        let targetColumn = _.find(columns, function getColumn(column) {
            return column.name === sortColumn;
        });

        if (!targetColumn) {
            const summaryColumns = this.settings.get('summaryColumns');
            const summaryTarget = this._getDataSeries(sortColumn);

            targetColumn = _.find(summaryColumns, function getColumn(item) {
                return item.group_function === summaryTarget.groupFunction &&
                    item.table_key === summaryTarget.tableKey;
            });
        }

        if (!targetColumn) {
            return;
        }

        const sortOrderKey = 'sort_dir';

        targetColumn[sortOrderKey] = this.settings.get(orderKey);

        this.settings.set(orderTypeKey, [targetColumn]);
    },

    /**
     * Order changed
     */
    sortOrderChanged: function(orderTypeKey, orderKey) {
        const orderBy = _.first(this.settings.get(orderTypeKey));

        if (!orderBy) {
            this.settings.set(orderTypeKey, undefined);
            return;
        }

        const sortOrderKey = 'sort_dir';

        orderBy[sortOrderKey] = this.settings.get(orderKey);

        this.settings.set(orderTypeKey, [orderBy]);
    },

    /**
     * Column has been changed
     */
    primaryChartOrderChanged: function() {
        this.sortOrderChanged('chartPrimaryOrderBy', 'primaryChartOrder');
    },

    /**
     * Column has been changed
     */
    secondaryChartOrderChanged: function() {
        this.sortOrderChanged('chartSecondaryOrderBy', 'secondaryChartOrder');
    },

    /**
     * Column has been changed
     */
    sortColumnListChanged: function() {
        this.orderByColumnChanged('sortColumnList', 'sortOrderList', 'listOrderBy', 'Table');
    },

    /**
     * Order has been changed
     */
    sortOrderListChanged: function() {
        this.sortOrderChanged('listOrderBy', 'sortOrderList');
    },

    /**
     * Returns the x-axis label based on report data
     *
     * @return {string}
     */
    _getXaxisLabel: function() {
        const groupDefs = this.settings.get('groupDefs');
        const chartType = this.settings.get('chartType');
        let label = '';

        if (!groupDefs || !chartType) {
            return label;
        }

        if (_.isEmpty(groupDefs)) {
            return label;
        }

        return chartType === 'lineF' ? _.last([].concat(groupDefs)).label : _.first([].concat(groupDefs)).label;
    },

    /**
     * Returns the y-axis label based on report data
     *
     * @return {string}
     */
    _getYaxisLabel: function() {
        const summaryColumns = this.settings.get('summaryColumns');
        let label = '';

        if (this.settings && summaryColumns) {
            _.each(summaryColumns, function(column) {
                if (!_.isUndefined(column.group_function)) {
                    label = column.label;
                }
            });
        }

        return label;
    },

    /**
     * Get the valid set of columns
     *
     * @param {string} columnType
     */
    _getColumns: function(columnType) {
        const displayColumns = this.settings.get('displayColumns');
        const summaryColumns = this.settings.get('summaryColumns');
        const groupDefs = this.settings.get('groupDefs');

        if (!displayColumns && !summaryColumns) {
            return false;
        }

        if (columnType === 'Chart') {
            return (groupDefs && _.isArray(groupDefs)) ? groupDefs : [];
        }

        return (displayColumns && displayColumns.length > 0) ? displayColumns : summaryColumns;
    },

    /**
     * Checks if the settings are valid
     *
     * @return {boolean}
     */
    _validateConfig: function() {
        const intelligent = this.settings.get('intelligent');
        const link = this.settings.get('linkedFields');
        const reportId = this.settings.get('reportId');
        const lineChartMinGroups = 2;

        let valid = !intelligent || (intelligent && !!link);
        let messages = 'LBL_REPORTS_DASHLET_INVALID_LINK';

        if (this.settings.get('chartType') === 'lineF' &&
            this.settings.get('groupDefs').length < lineChartMinGroups) {
            valid = false;
            messages = 'LBL_REPORTS_DASHLET_LINE_CHART_INVALID';
        }

        if (!reportId) {
            valid = false;
            messages = 'LBL_REPORTS_DASHLET_INVALID_REPORT';
        }

        if (!valid) {
            app.alert.show('dashlet_report_invalid_config', {
                level: 'warning',
                messages,
            });

            return valid;
        }

        if (this._oldReportId && this.reportId !== this._oldReportId) {
            const lastStateKey = this._buildUserLastStateKey(this._oldReportId);
            const defaultState = this._getUserDefaultState();

            app.user.lastState.set(lastStateKey, defaultState);
        } else {
            this._updateLastState('listOrderBy', 'orderBy');
        }

        this._updateLastState('defaultView');

        return valid;
    },

    /**
     * Setup settings model
     *
     * @param {Object} data
     */
    _setupSettings: function(data) {
        const reportDef = data.reportDef;

        this.settings.set({
            'module': reportDef.module,
            'filtersDef': reportDef.filters_def,
            'fullTableList': reportDef.full_table_list,
            'reportType': data.reportType,
            'chartType': reportDef.chart_type,
            'groupDefs': reportDef.group_defs,
            'displayColumns': reportDef.display_columns,
            'summaryColumns': reportDef.summary_columns,
            'reportName': reportDef.report_name,
            'saved_report': reportDef.report_name,
            'chartFunction': reportDef.numerical_chart_column,
            'reportDateModified': data.dateModified,
            'hasReportAccess': data.hasReportAccess,
            'isBarChart': false,
        });

        const savedReportField = this.getField('saved_report');

        if (savedReportField && _.isFunction(savedReportField.render)) {
            savedReportField.render();
        }

        if (this.reportId !== this._oldReportId) {
            this.settings.set('label', reportDef.report_name);
        }

        let orderBy = {};

        if (_.has(reportDef, 'order_by') && reportDef.order_by.length > 0) {
            orderBy = _.first(reportDef.order_by);
        } else if (_.has(reportDef, 'summary_order_by') && reportDef.summary_order_by.length > 0) {
            orderBy = _.first(reportDef.summary_order_by);
        }

        let sortColumn;
        let sortOrder;

        if (!_.isEmpty(orderBy)) {
            sortColumn = orderBy.name;
            sortOrder = orderBy.sort_dir === 'd' ? 'desc' : 'asc';
        }

        this.settings.set({
            'sortColumnList': sortColumn,
            'sortOrderList': sortOrder,
            'listOrderBy': orderBy,
        });

        this.sortColumnListChanged();
        this.sortOrderListChanged();
    },

    /**
     * Setup view controllers
     */
    _setupViews: function() {
        if (!this.meta.config) {
            return;
        }

        this._disposeViews();

        _.each(this.meta.panels, function createView(panel) {
            if (panel.custom_view) {
                const customView = app.view.createView({
                    type: panel.custom_view,
                    manager: this,
                    context: this.context,
                    model: this.settings,
                });

                customView.render();
                this.$('[data-container="' + panel.custom_view + '"').append(customView.$el);

                this._categoryViews.push(customView);
            }
        }, this);
    },

    /**
     * Setup fields controllers
     */
    _setupFields: function() {
        this._updateUIElements();
        this._setTableSortField();
        this._setChartSortFields();
    },

    /**
     * Update UI elements
     */
    _updateUIElements: function() {
        if (!this.meta.config) {
            return;
        }

        const reportType = this.settings.get('reportType');
        const hasCustomizableRows = _.includes(['tabular'], reportType);
        const title = app.lang.get('LBL_REPORTS_DASHLET_NO_AVAILABLE_FOR_REPORT_TYPE');

        this._toggleInput(
            reportType !== 'Matrix',
            'showTotalRecordCount',
            title
        );

        const limitField = this.getField('limit');

        limitField.setDisabled(!hasCustomizableRows);

        let checkboxContainerEl = $(`[data-fieldname="limit"]`);

        checkboxContainerEl.attr('rel', 'tooltip');
        checkboxContainerEl.tooltip({title});
        checkboxContainerEl.tooltip(!hasCustomizableRows ? 'enable' : 'disable');
    },

    /**
     * Set chart sortBy items
     *
     * @param {boolean} forceChange
     */
    _setChartSortFields: function(forceChange) {
        if (!this.meta.config) {
            return;
        }

        if (this.settings.get('chartType') === 'none' || this.settings.get('reportType') === 'tabular') {
            return;
        }

        const columnsType = 'Chart';
        const groupDefs = this._getColumns(columnsType);

        if (!groupDefs) {
            return;
        }

        const primarySortItems = {};

        const secondarySortItems = {};

        const primaryChartColumn = this.getField('primaryChartColumn');
        const primaryChartOrder = this.getField('primaryChartOrder');

        const secondaryChartOrderSet = this.getField('secondaryChartOrderSet');
        const secondaryChartColumn = this.getField('secondaryChartColumn');
        const secondaryChartOrder = this.getField('secondaryChartOrder');

        const isBarChart = this.settings.get('isBarChart');

        const chartDataSeries = this.settings.get('chartFunction');
        const summaryColumns = this.settings.get('summaryColumns');

        if (groupDefs && _.isArray(groupDefs) && groupDefs.length > 0) {
            const firstItem = _.first(groupDefs);

            primarySortItems[firstItem.name] = firstItem.label;
        }

        if (groupDefs && _.isArray(groupDefs) && groupDefs.length > 1) {
            const secondItemIndex = 1;
            const secondItem = groupDefs[secondItemIndex];

            secondarySortItems[secondItem.name] = secondItem.label;
        }

        let dataSeries;

        if (summaryColumns && chartDataSeries) {
            dataSeries = this._getDataSeries(chartDataSeries);

            let dataSeriesLabel = _.chain(summaryColumns)
                .filter(function filter(item) {
                    return item.group_function === dataSeries.groupFunction && item.table_key === dataSeries.tableKey;
                })
                .map('label')
                .first()
                .value();

            primarySortItems[chartDataSeries] = dataSeriesLabel;
        }

        primaryChartColumn.items = primarySortItems;
        secondaryChartColumn.items = secondarySortItems;

        const hasSecondSortBy = groupDefs.length > 1;

        if (this.settings.get('primaryChartColumn')) {
            primaryChartColumn.render();
        } else {
            let defaultItem = _.chain(primarySortItems).keys().first().value();

            this.settings.set({
                primaryChartColumn: defaultItem,
                primaryChartOrder: 'asc',
            });

            this.primaryChartColumnChanged();
            this.primaryChartOrderChanged();

            primaryChartOrder.render();
        }

        if (_.keys(primaryChartColumn.items).length === 1) {
            primaryChartColumn.$('.select2-container.select2').select2('disable');
        }

        if (hasSecondSortBy && isBarChart) {
            secondaryChartOrderSet.$el.parent().parent().show();

            if (this.settings.get('secondaryChartColumn')) {
                secondaryChartColumn.render();
            } else {
                let defaultItem = _.chain(secondarySortItems).keys().first().value();

                this.settings.set({
                    secondaryChartColumn: defaultItem,
                    secondaryChartOrder: 'asc',
                });

                this.secondaryChartColumnChanged();
                this.secondaryChartOrderChanged();

                secondaryChartOrder.render();
            }
        } else {
            secondaryChartOrderSet.$el.parent().parent().hide();
        }

        if (_.keys(secondaryChartColumn.items).length === 1) {
            secondaryChartColumn.$('.select2-container.select2').select2('disable');
        }

        const xAxisLabel = this.settings.get('xAxisLabel');
        const yAxisLabel = this.settings.get('yAxisLabel');

        if (forceChange || !xAxisLabel) {
            this.settings.set('xAxisLabel', this._getXaxisLabel());
        }

        if (forceChange || !yAxisLabel) {
            this.settings.set('yAxisLabel', this._getYaxisLabel());
        }
    },

    /**
      * Get data series meta
      *
      * @param {string} dataSeries
      * @return {Object}
      */
    _getDataSeries: function(dataSeries) {
        const dataSeriesParts = dataSeries.split(':');

        if (!dataSeriesParts || dataSeriesParts.length <= 1) {
            return false;
        }

        const groupFunction = dataSeriesParts.pop();
        let fieldName;
        let tableKey;

        if (groupFunction === 'count') {
            // function format for count table_key:count
            fieldName = 'id';
        } else {
            // function format for others table_key:field_name:function_name(ex: avg)
            fieldName = dataSeriesParts.pop();
        }

        tableKey = dataSeriesParts.join(':');

        return {
            fieldName,
            tableKey,
            groupFunction,
        };
    },

    /**
     * Set sortBy items
     */
    _setTableSortField: function() {
        if (!this.meta.config) {
            return;
        }

        const fieldController = this.getField('sortColumnList');

        if (!fieldController) {
            return;
        }

        const columns = this._getColumns('Table');

        if (!columns) {
            return;
        }

        let items = {};

        _.each(columns, function buildItems(column) {
            items[column.name] = column.label;
        });

        fieldController.items = items;

        if (this.settings.get('sortColumnList')) {
            fieldController.render();
        } else {
            const orderBy = this.settings.get('listOrderBy');

            let sortColumn = _.chain(items).keys().first().value();
            let sortOrder = 'asc';

            if (!_.isEmpty(orderBy)) {
                const orderByDef = _.chain(orderBy).first().value();

                sortColumn = orderByDef.name;
                sortOrder = orderByDef.sort_dir === 'd' ? 'desc' : 'asc';
            }

            this.settings.set({
                sortColumnList: sortColumn,
                sortOrderList: sortOrder,
            });

            this.sortColumnListChanged();
            this.sortOrderListChanged();

            this.getField('sortOrderList').render();
        }

        if (_.keys(items).length === 1) {
            fieldController.$('.select2-container.select2').select2('disable');
        }
    },

    /**
      * Reset user state
      *
      * @param {string} lastStateKey
      *
      */
    _resetUserState: function(lastStateKey) {
        const defaultState = this._getUserDefaultState();

        this.userLastState = defaultState;

        app.user.lastState.set(lastStateKey, defaultState);
    },

    /**
     * Build the unique key for the dashlet
     *
     * @param {string} reportId
     *
     * @return {string}
     */
    _buildUserLastStateKey: function(reportId) {
        let uniqueStateId = this.settings.get('uniqueStateId');

        if (_.isUndefined(uniqueStateId)) {
            uniqueStateId = app.utils.generateUUID();
            this.settings.set('uniqueStateId', uniqueStateId);
        }

        uniqueStateId = `:${uniqueStateId}`;

        const module = this.settings.get('module');
        const currentUserId = app.user.id;
        const stateKey = `${module}:${reportId}:${currentUserId}${uniqueStateId}`;
        const lastStateKey = app.user.lastState.buildKey(
            'report-dashlet-selected-view',
            app.controller.context.get('layout'),
            stateKey
        );

        return lastStateKey;
    },

    /**
     * Navigate to the report of the dashlet
     */
    viewReport: function() {
        const reportLink = '#' + app.router.buildRoute('Reports', this.reportId);

        window.open(reportLink, '_blank');
    },

    /**
     * Reset to default the ording sort
     */
    resetDashletDefaultSettings: function() {
        const defaultState = this._getUserDefaultState();

        app.user.lastState.set(this.lastStateKey, defaultState);

        this._syncReport();
    },

    /**
     * Refresh the result
     */
    refreshResults: function() {
        this._syncReport();
    },

    /**
     * Sync the report without resaving the Dashboard
     */
    silentRefreshResults: function() {
        this.saveDashboardOnSync = false;
        this._syncReport();
    },

    /**
     * Get the default state for user
     *
     * @return {Object}
     */
    _getUserDefaultState: function() {
        let defaultView = this.settings.get('defaultView');

        if (!defaultView || defaultView === 'chart') {
            const hasChart = this.settings.get('chartType') !== 'none';

            defaultView = hasChart ? 'chart' : 'list';
        }

        return {
            defaultView,
        };
    },

    /**
     * Get last user state for dashlet
     *
     * @param {string} lastStateKey
     *
     * @return {string}
     */
    _getUserLastState: function(lastStateKey) {
        let lastState = app.user.lastState.get(lastStateKey);

        if (!_.isUndefined(lastState)) {
            lastState = this._massageLastState(lastState);

            return lastState;
        }

        return this._getUserDefaultState();
    },

    /**
     * Get last user state for dashlet
     *
     * @param {string} reportId
     *
     * @return {string}
     */
    _getUserLastReportDateFromState: function(reportId) {
        const lastStateKey = this._buildUserLastStateKey(reportId);
        const lastState = this._getUserLastState(lastStateKey);

        return lastState.userLastReportDate;
    },

    /**
     * Ensuare the last state is valid
     *
     * @param {Object} lastState
     *
     * @return {Object}
     */
    _massageLastState: function(lastState) {
        // We need to take extra care when accessing the last state as the report might have changed
        // by an admin in the reports record-view

        if (_.has(lastState, 'filtersDef')) {
            const filtersDef = this.settings.get('filtersDef');
            const lastStateFilterDefs = this._getFieldsCompleteName(lastState.filtersDef.Filter_1);

            let dashletFilterDefs = [];

            if (!_.isUndefined(filtersDef)) {
                dashletFilterDefs = this._getFieldsCompleteName(filtersDef.Filter_1);
            }

            if (dashletFilterDefs.join() !== lastStateFilterDefs.join()) {
                delete lastState.filtersDef;
            }
        }

        if (_.has(lastState, 'listOrderBy')) {
            const summaryColumns = this.settings.get('summaryColumns');
            const displayColumns = this.settings.get('displayColumns');

            let summaryFields = [];
            let displayFields = [];

            if (!_.isUndefined(summaryColumns)) {
                summaryFields = this._getFieldsCompleteName(summaryColumns);
            }

            if (!_.isUndefined(displayColumns)) {
                displayFields = this._getFieldsCompleteName(displayColumns);
            }

            const orderByField = _.first(this._getFieldsCompleteName(lastState.listOrderBy));

            if (!_.contains(displayFields, orderByField) && !_.contains(summaryFields, orderByField)) {
                delete lastState.listOrderBy;
            }
        }

        return lastState;
    },

    /**
     * Get the fields from filters in format table:name
     *
     * @param {Object} filtersDef
     *
     * @return {Array}
     */
    _getFieldsCompleteName: function(def) {
        return _.chain(def)
                .filter('name')
                .map(function(item) {
                    return `${item.table_key}:${item.name}`;
                })
                .value()
                .sort();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.meta.config) {
            this._wrapInputEl('intelligent');
            this._wrapInputEl('showTotalRecordCount');

            const intelligence = this.settings.get('intelligent') ? this.settings.get('intelligent') : false;

            this._toggleIntelligence(intelligence);
            this._toggleTabs(false);

            this._setIntelligence();
        } else if (!app.acl.hasAccess('view', 'Reports')) {
            this._manageAclActions();
            this.$('.report-dashlet-placeholder').toggleClass('inherit-width-height', false);
            this.$('[data-widget="report-no-data"]').toggleClass('hidden', false);
        }
    },

    /**
     * Makes a call to Reports/:id/ to fetch specific saved report data
     *
     * @param {string} reportId the ID for the report we're looking for
     * @param {Function} successCallback
     * @param {Function} failedCallback
     */
    _getReportDataById: function(reportId, successCallback, failedCallback) {
        const url = app.api.buildURL('Reports/' + reportId + '/filter?use_saved_filters=false');

        app.api.call('read', url, null, {
            success: _.bind(successCallback, this),
            error: _.bind(failedCallback, this),
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
        this.saveDashboardOnSync = true;

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

        if (this.meta.config) {
            this.settings.set({
                reportId: '',
                saved_report: '',
                reportName: '',
            });

            this.reportChanged(this.settings);
            this.getField('saved_report').render();
        } else {
            this._createAndShowDashlet();
        }

        if (!this.layout || !(this.layout instanceof app.view.Layout)) {
            return;
        }

        const kebabElement = this.layout.$('.sicon-kebab');

        if (kebabElement) {
            kebabElement.parent().hide();
        }
    },

    /**
     * Setup Report Properties
     *
     * @param {Object} data
     */
    setupReportProperties: function(data) {
        if (this.disposed || !data) {
            return;
        }

        const newReportDateModified = data.dateModified;
        const currentReportDateModified = this.settings.get('reportDateModified');
        // if currentReportDateModified is empty, it means that the report is being loaded for the first time
        // and we should not reset the properties
        const reportDataChanged =
            !moment(newReportDateModified).isSame(currentReportDateModified) && !_.isEmpty(currentReportDateModified);
        const currentUserReportDateModified = this._getUserLastReportDateFromState(data.reportId);

        let userDataChanged = true;

        if (reportDataChanged || this._oldReportId !== data.reportId) {
            this._resetReportProperties();
            this._setupSettings(data);
        } else {
            this.saveDashboardOnSync = false;
        }

        if (currentUserReportDateModified) {
            userDataChanged = !moment(newReportDateModified).isSame(currentUserReportDateModified);
        }

        if (this.reportId !== this._oldReportId || userDataChanged) {
            this._updateLastState('reportDateModified', 'userLastReportDate');
        }

        if (this.meta.config) {
            this._setLinkedFields(data.reportDef.module);
            this._setupViews();
            this._setupFields();
            this._wrapInputEl('intelligent');
            this._wrapInputEl('showTotalRecordCount');

            if (this.settings.get('reportId')) {
                this._toggleIntelligence(true);
                this._toggleTabs(true);
            } else {
                this._toggleIntelligence(false);
                this._toggleTabs(false);
            }

            this._setIntelligence();
            this._toggleChartFields();
            this._setChartSortFields();

            this._setModalInfo('primaryChartColumn', 'primaryChartColumnInfo');
            this._setModalInfo('secondaryChartColumn', 'secondaryChartColumnInfo');
        } else {
            if (this.saveDashboardOnSync) {
                this.saveDashletSettings();
            }
            this.reloadDashlet();
        }
    },

    /**
     * Reset custom report properties
     */
    _resetReportProperties: function() {
        this.settings.set({
            sortColumnList: undefined,
            sortOrderList: 'asc',
            primaryChartColumn: undefined,
            primaryChartOrder: 'asc',
            secondaryChartColumn: undefined,
            secondaryChartOrder: 'asc',
            linkedFields: '',
            intelligent: false,
            chartFunction: undefined,
            chartPrimaryOrderBy: [],
            chartSecondaryOrderBy: [],
        });
    },

    /**
     * Change preview
     *
     * @param {jQuery} e
     */
    _changeTab: function(e) {
        const tabName = e.currentTarget.getAttribute('data-tabname');

        this._generatePreview(tabName);
    },

    /**
     * Create the preview controller
     *
     * @param {string} tabName
     */
    _generatePreview: function(tabName) {
        this._disposePreviewController();

        if (tabName === 'general') {
            return;
        }

        this._previewController = app.view.createView({
            type: 'report-dashlet-' + tabName + '-preview',
            context: this.context,
            model: this.settings,
        });

        this._previewController.render();

        const sidebarEl = app.drawer.$('[data-component=sidebar]');

        sidebarEl.empty();
        sidebarEl.append(this._previewController.$el);
    },

    /**
     * Enable or disable tabs
     *
     * @param {boolean} enable
     */
    _toggleTabs: function(enable) {
        if (!this.meta.config) {
            return;
        }

        const hasChart = this.settings.get('chartType') !== 'none' && this.settings.get('reportType') !== 'tabular';
        const reportId = this.settings.get('reportId');

        // if we have no chart we have to disable the option of choosing chart as default view
        if (hasChart) {
            this.getField('defaultView').items.chart = app.lang.get('LBL_CHART');

            if (!this.settings.get('defaultView')) {
                this.settings.set('defaultView', 'chart');
            }
        } else {
            const defaultView = this.settings.get('defaultView');

            if (!defaultView || defaultView === 'chart') {
                this.settings.set('defaultView', 'list');
            }

            delete this.getField('defaultView').items.chart;
        }

        this._toggleTab('list', enable, false);
        this._toggleTab('filter', enable, false);
        this._toggleTab('chart', enable && hasChart, reportId && !hasChart);
    },

    /**
     * Enable or disable a specific tab
     *
     * @param {string} tabName
     * @param {boolean} enable
     * @param {boolean} showInfo
     */
    _toggleTab: function(tabName, enable, showInfo) {
        const tabTextEl = this.$('.tab.tab-badgeable.' + tabName + ' a');

        tabTextEl.css('pointer-events', enable ? '' : 'none');

        if (showInfo) {
            tabTextEl.css('color', enable ? '' : '#6f777b');
        } else {
            tabTextEl.css('color', enable ? '' : '#9ba1a6');
        }

        const tab = this.$('.tab.tab-badgeable.' + tabName);

        const hideTooltip = enable || !showInfo;

        if (enable) {
            tabTextEl.removeClass('opacity-40');
        } else {
            tabTextEl.addClass('opacity-40');
        }

        tab.attr('rel', 'tooltip');
        tab.tooltip({
            title: app.lang.get('LBL_REPORTS_DASHLET_INVALID_FEATURE'),
            placement: 'bottom',
        });
        tab.tooltip((hideTooltip) ? 'disable' : 'enable');

        tab.css('pointer-events', (enable || showInfo) ? '' : 'none');

        const infoIcon = this.$('[data-fieldname="info-' + tabName + '"]');

        if (hideTooltip) {
            infoIcon.hide();
        } else {
            infoIcon.show();
        }
    },

    /**
     * Handle the conditional display of settings input field based on checkbox toggle state
     *
     * @param {Object} toggle
     * @param {Object} dependent
     */
    _toggleDepedent: function(toggle, dependent) {
        const isDependentDisposed = dependent.disposed;

        let inputField = '';

        if (!isDependentDisposed) {
            inputField = dependent.$(dependent.fieldTag);
        }

        const enabled = this.settings.get(toggle.name);
        const value = enabled ? this.settings.get(dependent.name) : '';

        if (!isDependentDisposed) {
            inputField
                .prop('disabled', !enabled)
                .val(value);
        }
    },

    /**
     * Create the map and display it
     */
    _createAndShowDashlet: function() {
        this._manageAclActions();

        if (!app.acl.hasAccess('view', 'Reports')) {
            return;
        }

        this._disposeWrapper();

        this._reportDashletWrapper = app.view.createLayout({
            name: 'report-dashlet-wrapper',
            layout: this.layout,
            context: this.context,
            model: this._createBeanReport(),
            module: 'Reports',
        });

        this._reportDashletWrapper.initComponents();
        this._reportDashletWrapper.render();

        this.$('.report-dashlet-placeholder').append(this._reportDashletWrapper.$el);
    },

    /**
     * Create bean report
     *
     * @return {Data.Bean} model
     */
    _createBeanReport: function() {
        let report = app.data.createBean('Reports', {
            id: this.reportId,
            report_id: this.reportId,
            report_name: this.settings.get('reportName'),
            report_type: this.settings.get('reportType'),
            dashlet_id: this._dashletId,
            list: {
                showCount: !!this.settings.get('showTotalRecordCount'),
                rowsPerPage: this.settings.get('limit'),
                orderBy: this.settings.get('listOrderBy'),
                filtersDef: this.settings.get('filtersDef'),
                summaryColumns: this.settings.get('summaryColumns'),
                displayColumns: this.settings.get('displayColumns'),
                groupDefs: this.settings.get('groupDefs'),
                fullTableList: this.settings.get('fullTableList'),
            },
            chart: this._getChartSettings(),
            filter: {
                filtersDef: this.settings.get('filtersDef'),
            },
            intelligent: this._getIntelligenceSettings(),
            hasChart: this.settings.get('chartType') === 'none' || this.settings.get('reportType') === 'tabular',
            lastStateKey: this.lastStateKey,
            userLastState: this.userLastState,
            defaultSelectView: this.defaultSelectView,
            customCssClasses: 'report-dashlet-container-type',
            content: this._buildReportContent(),
        });

        return report;
    },

    /**
     * Build the report def json
     *
     * @return {Object}
     */
    _buildReportContent: function() {
        return JSON.stringify({
            'full_table_list': this.settings.get('fullTableList'),
            'summary_columns': this.settings.get('summaryColumns'),
            'display_columns': this.settings.get('displayColumns'),
        });
    },

    /**
     * Hide buttons the user does not have access to
     */
    _manageAclActions: function() {
        const dashletToolbar = this.layout.getComponent('dashlet-toolbar');

        if (!dashletToolbar) {
            return;
        }

        const userActionsBtns = _.find(dashletToolbar.buttons, {name: 'userActions'});

        if (!userActionsBtns) {
            return;
        }

        const closestDashboard = this.closestComponent('dashboard');
        const dashModel = closestDashboard ? closestDashboard.model : this.model;
        const aclModuleKey = 'acl_module';
        const aclActionKey = 'acl_action';
        const dropdownButtonsKey = 'dropdown_buttons';

        if ((!_.isUndefined(userActionsBtns[aclActionKey]) &&
            !_.isUndefined(userActionsBtns[aclModuleKey]) &&
            !app.acl.hasAccess(userActionsBtns[aclActionKey], userActionsBtns[aclModuleKey])) ||
            !app.utils.reports.hasAccessToAllReport({content: this._buildReportContent()})) {
            dashletToolbar.buttons = _.without(dashletToolbar.buttons, userActionsBtns);

            dashletToolbar.render();
            return;
        }

        for (let i = userActionsBtns[dropdownButtonsKey].length - 1; i > -1; i--) {
            const item = userActionsBtns[dropdownButtonsKey][i];

            const hasAclModule = _.has(item, aclModuleKey);
            const hasAclAccessLvl = _.has(item, aclActionKey);

            let hasAccess = true;

            if (!hasAclAccessLvl || !hasAclModule) {
                continue;
            }

            if (hasAclAccessLvl) {
                hasAccess = app.acl.hasAccessToModel(item[aclActionKey], dashModel);
            }

            if (hasAccess && hasAclAccessLvl && hasAclModule) {
                hasAccess = app.acl.hasAccess(item[aclActionKey], item[aclModuleKey]);
            }

            if (!hasAccess) {
                userActionsBtns[dropdownButtonsKey].splice(i, 1);
            }
        }
    },

    /**
     * Build intelligence settings
     *
     * @return {Object}
     */
    _getIntelligenceSettings: function() {
        const appContext = app.controller.context;
        const sideCtx = app.sideDrawer.currentContextDef;

        const module = sideCtx ? sideCtx.context.module : appContext.get('module');
        const contextModel = sideCtx ? sideCtx.context.model : appContext.get('model');

        return {
            intelligent: this.settings.get('intelligent'),
            link: this.settings.get('linkedFields'),
            targetModule: module,
            contextId: sideCtx ? sideCtx.context.modelId : appContext.get('modelId'),
            contextName: contextModel.get('name') || contextModel.get('full_name'),
        };
    },

    /**
     * Get chart settings from config.
     *
     * @return {Object}
     */
    _getChartSettings: function() {
        return {
            chartType: this.settings.get('chartType'),
            showLegend: !!this.settings.get('showLegend'),
            isBarChart: !!this.settings.get('isBarChart'),
            primaryOrderBy: this.settings.get('chartPrimaryOrderBy'),
            secondaryOrderBy: this.settings.get('chartSecondaryOrderBy'),
            filtersDef: this.settings.get('filtersDef'),
            summaryColumns: this.settings.get('summaryColumns'),
            displayColumns: this.settings.get('displayColumns'),
            groupDefs: this.settings.get('groupDefs'),
            fullTableList: this.settings.get('fullTableList'),
            autoRefresh: !!this.settings.get('autoRefresh'),
            config: !!this.settings.get('config'),
            showTitle: !!this.settings.get('showTitle'),
            showXLabel: !!this.settings.get('showXLabel'),
            showYLabel: !!this.settings.get('showYLabel'),
            showValues: this.settings.get('showValues'),
            xAxisLabel: this.settings.get('xAxisLabel'),
            yAxisLabel: this.settings.get('yAxisLabel'),
            colorData: 'class',
            reduceXTicks: true,
            showControls: false,
        };
    },

    /**
     * Trigger an event to refresh the dashlet components
     */
    _refreshActiveComponent: function() {
        this.context.trigger('report-dashlet:refresh');
    },

    /**
     * @inheritdoc
     * When rendering fields, handle the state of the axis labels
     */
    _renderField: function(field) {
        this._super('_renderField', [field]);

        // manage display state of fieldsets with toggle
        if (this.meta.config) {
            if (!_.isUndefined(field.def.toggle)) {
                const toggle = this.getField(field.def.toggle);
                const dependent = this.getField(field.def.dependent);

                this._toggleDepedent(toggle, dependent);

                this.listenTo(this.settings, 'change:' + toggle.name, function() {
                    this._toggleDepedent(toggle, dependent);
                }, this);

                this.listenTo(this.settings, 'change:' + dependent.name, function() {
                    this._toggleDepedent(toggle, dependent);
                }, this);
            }
        }
    },

    /**
     * Reload Dashlet
     */
    reloadDashlet: function() {
        this._createAndShowDashlet();
    },

    /**
     * Save dashlet meta
     */
    saveDashletSettings: function() {
        this.settings.set('config', false);

        const dashletLayout = this.layout;
        const dashboardGrid = dashletLayout.layout;
        const dashletId = dashletLayout.el.getAttribute('data-gs-id');

        const dashletIndex = _.findIndex(dashboardGrid.dashlets, function getDashletDef(dashletDef) {
            return dashletDef.id === dashletId;
        });

        const dashletMeta = dashboardGrid.dashlets[dashletIndex];

        dashboardGrid.editDashlet(dashletLayout, {
            link: dashletMeta.context,
            view: this.settings.attributes,
        });

        _.first(this.layout.meta.components).view = this.settings.attributes;
    },

    /**
     * Dispose the preview controller
     */
    _disposePreviewController: function() {
        if (this._previewController) {
            this._previewController.dispose();
            this._previewController = false;
        }
    },

    /**
     * Dispose custom views
     */
    _disposeViews: function() {
        _.each(this._categoryViews, function disposeView(view) {
            view.dispose();
        }, this);

        this._categoryViews = [];
    },

    /**
     * Dispose the wrapper
     */
    _disposeWrapper: function() {
        if (this._reportDashletWrapper) {
            this._reportDashletWrapper.dispose();
            this._reportDashletWrapper = null;
            this.$('.report-dashlet-placeholder').empty();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeWrapper();
        this._disposeViews();
        this._disposePreviewController();
        this._disableAutoRefresh();

        this._super('_dispose');
    },
});

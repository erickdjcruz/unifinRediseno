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
 * @class View.Views.Base.Reports.ReportChartView
 * @alias SUGAR.App.view.views.BaseReportsReportChartView
 * @extends View.Views.Base.View
 */
({
    plugins: ['ReportsPanel'],

    /**
     * Before init properties
     */
    _beforeInit: function() {
        this._reportData = app.data.createBean();
        this._settings = app.data.createBean();

        this._chartField = null;
        this._chartDef = {
            type: 'chart',
            customLegend: true,
        };

        this.RECORD_NOT_FOUND_ERROR_CODE = 404;
        this.SERVER_ERROR_CODES = [500, 502, 503, 504];
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this, 'chart:clicked', this._drilldown, this);
        this.listenTo(this.context, 'runtime:filters:updated', this.runtimeFiltersUpdated, this);
        this.listenTo(this.context, 'split-screens-resized', this.updateChartUI, this);
        this.listenTo(this.context, 'dashlet:mode:loaded', this.updateChartUI, this);

        if (_.has(this, 'layout') && this.layout && _.has(this.layout, 'layout') && this.layout.layout) {
            this.listenTo(this.layout.layout, 'panel:collapse', this._collapseChart, this);
            this.listenTo(this.layout.layout, 'grid-panel:size:changed', this._updateChartSize, this);
        }
    },

    /**
     * Whenever orientation/size/visibility of the chart container changes, we have to rerender the chart
     */
    updateChartUI: function() {
        if (!this._chartField) {
            return;
        }

        const isFunnelOrDonut = (type) => ['funnelF', 'donutF'].includes(type);

        const hasDefaultChart = isFunnelOrDonut(this.model.get('chart_type'));
        const hasCustomChart =  this.model.get('chart') && isFunnelOrDonut(this.model.get('chart').chartType);

        // we only need to recreate the chart element if it's donut or funnel
        if (hasDefaultChart || hasCustomChart) {
            this._chartField.generateD3Chart();
        }
    },

    /**
     * Refetch chart data with the new runtime filters
     */
    runtimeFiltersUpdated: function() {
        this._loadReportData();
    },

    /**
     * @inheritdoc
     */
    _renderField: function(field) {
        this._super('_renderField', [field]);

        if (!this._chartField && field.def.type === 'chart') {
            this._chartField = field;
        }
    },

    /**
     * Get the report and chart data
     */
    _loadReportData: function() {
        const useCustomReportDef = this.options.useCustomReportDef;
        this.context.set('reportHasChart', false);

        if (useCustomReportDef) {
            this._loadCustomReportData();

            return;
        }

        this._loadDefaultReportData();
    },

    /**
     * Get the default report and chart data
     */
    _loadDefaultReportData: function() {
        const reportId = this.model.get('id');
        const url = app.api.buildURL('Reports/' + reportId + '/chart?use_saved_filters=true');

        app.api.call('read', url, null, {
            success: _.bind(this._storeReportData, this),
            error: _.bind(this._handleError, this),
        });
    },

    /**
     * Get the custom report and chart data
     */
    _loadCustomReportData: function() {
        const reportId = this.model.get('id');
        const url = app.api.buildURL('Reports/' + reportId + '/chart');

        app.api.call('create', url, this._getChartReportMeta(), {
            success: _.bind(this._storeReportData, this),
            error: _.bind(this._handleError, this),
        });
    },

    /**
     * Build and apply the complete meta for a report chart
     *
     * @param {Object} reportDef
     *
     * @return {Object}
     */
    _applyCustomChartReportMeta: function(reportDef) {
        const mappingTable = {
            filtersDef: 'filters_def',
            summaryColumns: 'summary_columns',
            displayColumns: 'display_columns',
            groupDefs: 'group_defs',
            fullTableList: 'full_table_list',
            multipleOrderBy: 'multipleOrderBy',
            orderBy: 'order_by',
            summaryOrderBy: 'summary_order_by',
            intelligent: 'intelligent',
        };

        const chartReportMeta = this._getChartReportMeta();

        _.each(chartReportMeta, function mapProperties(propValue, propKey) {
            const reportDefKey = mappingTable[propKey];

            if (reportDefKey) {
                reportDef[reportDefKey] = propValue;
            }
        }, this);

        reportDef.useSavedFilters = false;

        return reportDef;
    },

    /**
     * Build and return the complete meta for a report chart
     *
     * @return {Object}
     */
    _getChartReportMeta: function() {
        const reportId = this.model.get('id');
        const chartMeta = app.utils.deepCopy(this.model.get('chart')) || {};
        const intelligenceMeta = this.model.get('intelligent') || {};

        const meta = {
            record: reportId,
            reportType:  this.model.get('report_type'),
        };

        const lastStateKey = this.model.get('lastStateKey');

        let hasSecondGroupBy = false;

        meta.filtersDef = this._getCustomFiltersMeta(chartMeta, lastStateKey);

        if (_.has(chartMeta, 'summaryColumns') && !_.isEmpty(chartMeta.summaryColumns)) {
            meta.summaryColumns = chartMeta.summaryColumns;
        }

        if (_.has(chartMeta, 'displayColumns') && !_.isEmpty(chartMeta.displayColumns)) {
            meta.displayColumns = chartMeta.displayColumns;
        }

        if (_.has(chartMeta, 'groupDefs') && !_.isEmpty(chartMeta.groupDefs)) {
            meta.groupDefs = chartMeta.groupDefs;

            hasSecondGroupBy = meta.groupDefs.length > 1;
        }

        if (_.has(chartMeta, 'fullTableList') && !_.isEmpty(chartMeta.fullTableList)) {
            meta.fullTableList = chartMeta.fullTableList;
        }

        const orderByKey = 'orderBy';
        const summaryOrderByKey = 'summaryOrderBy';
        const sortDirKey = 'sort_dir';

        if (_.has(chartMeta, 'primaryOrderBy') && !_.isEmpty(chartMeta.primaryOrderBy)) {
            let order = _.first(chartMeta.primaryOrderBy);

            order[sortDirKey] = order[sortDirKey] === 'asc' ? 'a' : 'd';

            meta[orderByKey] = [order];
        }

        if (_.has(chartMeta, 'secondaryOrderBy') &&
            !_.isEmpty(chartMeta.secondaryOrderBy) &&
            chartMeta.isBarChart &&
            hasSecondGroupBy
        ) {
            let order = _.first(chartMeta.secondaryOrderBy);

            order[sortDirKey] = order[sortDirKey] === 'asc' ? 'a' : 'd';

            meta[orderByKey] = _.union(meta[orderByKey], [order]);
            meta.multipleOrderBy = true;
        }

        if (_.has(meta, orderByKey)) {
            meta[summaryOrderByKey] = meta[orderByKey];
        }

        if (!_.isEmpty(intelligenceMeta)) {
            meta.intelligent = intelligenceMeta;
        }

        if (!_.isUndefined(chartMeta.chartType)) {
            meta.chartType = chartMeta.chartType;
        }

        return meta;
    },

    /**
     * Setup preview widget view
     */
    _setupPreviewReportPanel: function() {
        _.defer(
            _.bind(this._storeReportData, this, this.context.get('previewData').chartData)
        );
    },

    /**
     * Setup chart properties and store report data
     *
     * @param {Object} data
     */
    _storeReportData: function(data) {
        if (this.disposed) {
            return;
        }

        if (data.error) {
            this._handleError(data);

            return;
        }

        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this._handleError({});
        }

        data = this._sanitizeData(data);

        if (_.isEmpty(data)) {
            this._showEmptyChart(true);
            this.context.set('reportHasChart', false);

            if (_.has(this, 'layout') && this.layout && typeof(this.layout.trigger) === 'function') {
                this.layout.trigger('panel:widget:finished:loading', true, false);
            }

            return;
        }

        const validChart = this._setChartParams(data);

        if (!validChart) {
            this._showEmptyChart(true);
            this.context.set('reportHasChart', false);

            if (_.has(this, 'layout') && this.layout && typeof(this.layout.trigger) === 'function') {
                this.layout.trigger('panel:widget:finished:loading', true, false);
            }

            return;
        }

        this._reportData.set('rawReportData', data.reportData);
        this._reportData.set('rawChartData', data.chartData);

        this.context.set('reportHasChart', true);
        this.context.trigger('report:data:chart:loaded', false, 'chart');
        this._setFooter();

        if (_.has(this, 'layout') && this.layout && typeof(this.layout.trigger) === 'function') {
            this.layout.trigger('panel:widget:finished:loading', false, false);
        }
    },

    /**
     * Remove broken data
     *
     * @param {Object} data
     * @return {Object}
     */
    _sanitizeData: function(data) {
        if (_.isEmpty(data) || _.isEmpty(data.chartData.values)) {
            return data;
        }

        const props = data.chartData.properties;
        const title = _.first(props).title.split(' ');

        if (data.chartData.values.length > 0 && _.last(title) === _.last(data.chartData.values).label) {
            data.chartData.values.pop();

            const labelIdx = data.chartData.label.indexOf('');
            data.chartData.label.splice(labelIdx, 1);

            _.each(data.chartData.values, function cleanValues(barData) {
                barData.links.splice(labelIdx, 1);
                barData.valuelabels.splice(labelIdx, 1);
                barData.values.splice(labelIdx, 1);
            });
        }

        return data;
    },

    /**
     * Collapse/Maximize the chart widget
     *
     * @param {boolean} collapse
     */
    _collapseChart: function(collapse) {
        if (collapse) {
            this.$el.hide();
        } else {
            this.$el.show();
        }
    },

    /**
     * Update chart size
     */
    _updateChartSize: function() {
        this.trigger('chart-container:size:changed');
    },

    /**
     * Show/Hide the chart widget
     *
     * @param {boolean} show
     */
    _showChart: function(show) {
        const emptyChartEl = this.$('[data-container="chart-container"]');

        if (show) {
            emptyChartEl.show();
        } else {
            emptyChartEl.hide();
        }
    },

    /**
     * Show/Hide the empty chart widget
     *
     * @param {boolean} show
     * @param {boolean} noAccess
     */
    _showEmptyChart: function(show, noAccess) {
        if (this.disposed) {
            return;
        }

        const elId = noAccess ? 'report-no-data' : 'report-no-chart';
        const emptyChartEl = this.$(`[data-widget="${elId}"]`);

        this.context.trigger('report:data:chart:loaded', !show, 'chart');
        this._showChart(!show);
        this._showFooter(!show);

        emptyChartEl.toggleClass('hidden', !show);

        if (this._chartField) {
            this._chartField.disposeLegend();
        }
    },

    /**
     * Show/Hide the footer bar
     *
     * @param {boolean} show
     */
    _showFooter: function(show) {
        const footerEl = this.$('[data-widget="report-footer"]');

        if (show) {
            footerEl.show();
        } else {
            footerEl.hide();
        }
    },

    /**
     * Set the report footer
     */
    _setFooter: function() {
        if (!_.has(this, 'layout') ||
            !this.layout ||
            typeof(this.layout.getComponent) !== 'function' ||
            !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            return;
        }

        const footerBar = this.layout.getComponent('report-panel-footer');
        const chartData = this._reportData.get('rawChartData').values;

        if (_.isEmpty(footerBar)) {
            return;
        }

        if (_.isEmpty(chartData)) {
            footerBar.$('.dashlet-title').text('');
            this.context.trigger('report:set-footer-visibility', true);
            return;
        }

        const title = this._reportData.get('rawChartParams').report_title;

        footerBar.$('.dashlet-title').text(title);
    },

    /**
     * Setup chart properties
     *
     * @param {Object} data
     *
     * @return {boolean}
     */
    _setChartParams: function(data) {
        const reportData = data.reportData;
        const chartData = data.chartData;

        const chartProperties = _.first(chartData.properties);
        const chartConfig = this._getChartConfig(chartProperties.type);

        const chartType = chartProperties.type;

        const groupDefsKey = 'group_defs';
        const groupDefs = reportData[groupDefsKey];

        if (chartType === 'none' || _.isEmpty(groupDefs)) {
            return false;
        }

        const properties = {
            report_title: chartProperties.title,
            show_legend: chartProperties.legend === 'on' ? true : false,
            print_chart_legend: chartProperties.legend === 'on' ? true : false,
            print_chart_title: chartProperties.title ? true : false,
            module: chartProperties.base_module,
            allow_drillthru: chartProperties.allow_drillthru,
            saveChartAsImage: true,
            imageExportType: reportData.pdfChartImageExt,
            saved_report_id: this.model.get('id'),
        };

        const config = {
            label: reportData.label,
            chart_type: chartType,
            stacked: chartConfig.barType === 'stacked' || chartConfig.barType === 'basic' ? true : false,
            vertical: chartConfig.orientation === 'vertical' ? true : false,
            x_axis_label: this._getXaxisLabel(reportData.group_defs, chartProperties, chartType),
            y_axis_label: this._getYaxisLabel(reportData),
        };

        const defaultSettings = this._getChartDefaultSettings();
        const customSettings = this._getCustomChartParams();

        const settings = _.extend(properties, config, defaultSettings, customSettings);

        this._reportData.set('rawChartParams', settings);
        this._settings.set(settings);

        return true;
    },

    /**
     * Get custom chart settings
     *
     * @return {Object}
     */
    _getCustomChartParams: function() {
        const useCustomReportDef = this.options.useCustomReportDef;
        const chartConfig = this.model.get('chart');
        let customSettings = {};

        if (!useCustomReportDef || !chartConfig) {
            return customSettings;
        }

        const showLegendIndex = 'show_legend';
        const showControlsIndex = 'show_controls';
        const showTitleIndex = 'show_title';
        const showXlabelIndex = 'show_x_label';
        const showYlabelIndex = 'show_y_label';
        const xAxisLabel = 'x_axis_label';
        const yAxisLabel = 'y_axis_label';

        if (!_.isUndefined(chartConfig.config)) {
            customSettings.config = chartConfig.config;
        }

        if (!_.isUndefined(chartConfig.showTitle)) {
            customSettings[showTitleIndex] = chartConfig.showTitle;
        }

        if (!_.isUndefined(chartConfig.showXLabel)) {
            customSettings[showXlabelIndex] = chartConfig.showXLabel;
        }

        if (!_.isUndefined(chartConfig.showYLabel)) {
            customSettings[showYlabelIndex] = chartConfig.showYLabel;
        }

        if (!_.isUndefined(chartConfig.showValues)) {
            customSettings.showValues = chartConfig.showValues;
        }

        if (!_.isUndefined(chartConfig.xAxisLabel)) {
            customSettings[xAxisLabel] = chartConfig.xAxisLabel;
        }

        if (!_.isUndefined(chartConfig.yAxisLabel)) {
            customSettings[yAxisLabel] = chartConfig.yAxisLabel;
        }

        if (!_.isUndefined(chartConfig.colorData)) {
            customSettings.colorData = chartConfig.colorData;
        }

        if (!_.isUndefined(chartConfig.reduceXTicks)) {
            customSettings.config = chartConfig.reduceXTicks;
        }

        if (!_.isUndefined(chartConfig.showControls)) {
            customSettings[showControlsIndex] = chartConfig.showControls;
        }

        if (!_.isUndefined(chartConfig.showLegend)) {
            customSettings[showLegendIndex] = chartConfig.showLegend;
        }

        return customSettings;
    },

    /**
     * Open drilldown drawer
     *
     * @param {Object} drawerContext
     */
    _openDrawer: function(drawerContext) {
        const currentModule = app.drawer.context.get('module');

        app.drawer.context.set('module', drawerContext.chartModule);

        app.drawer.open({
            layout: 'drillthrough-drawer',
            context: drawerContext
        }, _.bind(function resetDrawerModule() {
            if (currentModule) {
                // reset the drawer module
                app.drawer.context.set('module', currentModule);
            }
        }, this, currentModule));
    },

    /**
     * Open a focus drawer
     *
     * @param {Object} drawerContext
     * @param {string} tabTitle
     */
    _openSideDrwawer: function(context, tabTitle) {
        if (app.sideDrawer) {
            const drawerIsOpen = app.sideDrawer.isOpen();
            const drawerContext = app.sideDrawer.currentContextDef;

            if (drawerIsOpen && _.isEqual(context, drawerContext)) {
                return;
            }

            const sideDrawerClick = !!this.$el && (this.$el.closest('#side-drawer').length > 0);

            if (!_.has(context, 'dataTitle')) {
                const baseModuleKey = 'base_module';

                const hasReportData = _.has(context, 'reportData') && _.has(context.reportData, 'base_module');
                const hasLabel = _.has(context, 'reportData') && _.has(context.reportData, 'label');

                const reportModule = hasReportData ? context.reportData[baseModuleKey] : '';
                const reportLabel = hasLabel ? context.reportData.label : '';

                context.dataTitle = app.sideDrawer.getDataTitle(
                    reportModule,
                    'LBL_FOCUS_DRAWER_DASHBOARD',
                    reportLabel
                );
            }

            const sideDrawerMeta = {
                dashboardName: tabTitle,
                layout: 'report-side-drawer',
                css_class: 'flex flex-column',
                context
            };

            app.sideDrawer.open(sideDrawerMeta, null, sideDrawerClick);
        }
    },

    /**
     * Setup drilldown list view
     *
     * @param event
     * @param activeElements
     * @param {Function} chart
     * @param {BaseChart} wrapper
     * @param {Object} reportDef
     */
    _drilldown: function(event, activeElements, chart, wrapper, reportDef) {
        const useCustomReportDef = this.options.useCustomReportDef;
        if (useCustomReportDef) {
            reportDef = this._applyCustomChartReportMeta(reportDef);
        }

        const chartConfig = this.model.get('chart');
        const chartExtraParams = {};
        const showLegendKey = 'show_legend';

        if (chartConfig) {
            chartExtraParams[showLegendKey] = chartConfig.showLegend;
        } else {
            chartExtraParams[showLegendKey]  = this._reportData.get('rawChartParams')[showLegendKey];
        }

        if (this.context.get('previewMode')) {
            app.alert.show('report-preview-limitation', {
                level: 'warning',
                messages: app.lang.get('LBL_REPORTS_PREVIEW_LIMITATION'),
                autoClose: true
            });

            return;
        }

        let params = Object.assign({}, wrapper.params, chartExtraParams);

        // funnel chart uses chartjs v2 which has a different signature
        if (wrapper.chartType === 'funnel') {
            if (_.isEmpty(activeElements)) {
                return;
            }

            const internalChart = _.first(activeElements)._chart;
            const elementClicked = _.first(internalChart.getElementAtEvent(event));

            params.seriesIndex = elementClicked._datasetIndex;
            params.seriesLabel = internalChart.data.datasets[params.seriesIndex].label;
            params.groupIndex = elementClicked._index;
            params.groupLabel = internalChart.data.labels[params.groupIndex];
        } else {
            const element = chart.getElementsAtEventForMode(event, 'nearest', {intersect: true}, false)[0];

            if (_.isEmpty(element)) {
                return;
            }

            if (params.chart_type === 'line chart') {
                params.groupIndex = element.datasetIndex;
                params.groupLabel = chart.data.datasets[params.groupIndex].label;
                params.seriesIndex = element.index;
                params.seriesLabel = chart.data.labels[params.seriesIndex];
            } else {
                params.seriesIndex = element.datasetIndex;
                params.seriesLabel = chart.data.datasets[params.seriesIndex].label;
                params.groupIndex = element.index;
                params.groupLabel = chart.data.labels[params.groupIndex];
            }
        }

        params.saved_report_id = this.model.id ? this.model.id : this.model.get('id');

        this._handleFilter(params, null, reportDef, wrapper.rawData);
    },

    /**
     * Handle either navigating to target module or update list view filter.
     *
     * @param {Object} params chart display parameters
     * @param {Object} state chart display and data state
     * @param {Object} reportData report data as returned from API
     * @param {Object} chartData chart data with properties and data array
     */
    _handleFilter: function(params, state, reportData, chartData) {
        app.alert.show('listfromreport_loading', {level: 'process', title: app.lang.get('LBL_LOADING')});

        const module = params.baseModule;
        const reportId = this.model.get('id');
        const enums = SUGAR.charts.getEnums(reportData);
        const groupDefs = SUGAR.charts.getGrouping(reportData);

        const drawerContext = {
            chartData: chartData,
            chartModule: module,
            chartState: state,
            dashModel: null,
            dashConfig: params,
            enumsToFetch: enums,
            filterOptions: {
                auto_apply: false
            },
            groupDefs: groupDefs,
            layout: 'report-side-drawer',
            module: 'Reports',
            reportData: reportData,
            reportId: reportId,
            skipFetch: true,
            useCustomReportDef: this.options.useCustomReportDef,
            useSavedFilters: (_.isUndefined(reportData.useSavedFilters) || reportData.useSavedFilters) ?
                            true :
                            reportData.useSavedFilters,
        };

        this._openSideDrwawer(drawerContext, reportData.label);
    },

    /**
     * Update the record list in drill through drawer.
     *
     * @param {Object} params chart display parameters
     * @param {Object} state chart display and data state
     */
    _updateList: function(params, state) {
        const drawer = this.closestComponent('drawer').getComponent('drillthrough-drawer');

        drawer.context.set('dashConfig', params);
        drawer.context.set('chartState', state);

        drawer.updateList();

        if (this._chartField && this._chartField.chart) {
            this._chartField.chart.updateParams(params);
        }
    },

    /**
     * Returns the x-axis label based on report data
     *
     * @param {Object} groups
     * @param {Object} properties
     * @param {string} chartType
     *
     * @return {string}
     */
    _getXaxisLabel: function(groups, properties, chartType) {
        if (_.isEmpty(groups)) {
            return '';
        }

        return chartType === 'line chart' ?
            properties.seriesName || _.last([].concat(groups)).label :
            properties.groupName || _.first([].concat(groups)).label;
    },

    /**
     * Returns the y-axis label based on report data
     *
     * @param {Object} data
     *
     * @return {string}
     */
    _getYaxisLabel: function(data) {
        let label = '';
        let chartFunction = '';

        if (_.has(data, 'numericalChartColumn')) {
            const dataSeries = app.utils.reports.getDataSeries(data.numericalChartColumn);

            if (dataSeries && _.has(dataSeries, 'groupFunction')) {
                chartFunction = dataSeries.groupFunction;
            }
        }

        if (data && data.summary_columns && _.isArray(data.summary_columns)) {
            for (let i = 0; i < data.summary_columns.length; i++) {
                let column = data.summary_columns[i];

                if (_.has(column, 'group_function') && !_.isUndefined(column.group_function) &&
                    chartFunction === column.group_function) {
                    label = column.label;

                    break;
                }
            }
        }

        return label;
    },

    /**
     * Builds the chart config based on the type of chart
     *
     * @param {string} chartType
     *
     * @return {Mixed}
     */
    _getChartConfig: function(chartType) {
        if (_.contains(['pie chart', 'donut chart', 'treemap chart', 'gauge chart'], chartType)) {
            return {
                chartType: chartType,
            };
        }

        if (_.contains(['line chart'], chartType)) {
            return {
                lineType: 'grouped',
                chartType: 'line chart',
            };
        }

        if (_.contains(['funnel chart 3D'], chartType)) {
            return {
                chartType: 'funnel chart',
            };
        }

        if (_.contains(['stacked group by chart'], chartType)) {
            return {
                orientation: 'vertical',
                barType: 'stacked',
                chartType: 'group by chart',
            };
        }

        if (_.contains(['group by chart'], chartType)) {
            return {
                orientation: 'vertical',
                barType: 'grouped',
                chartType: 'group by chart',
            };
        }

        if (_.contains(['bar chart'], chartType)) {
            return {
                orientation: 'vertical',
                barType: 'basic',
                chartType: 'group by chart',
            };
        }

        if (_.contains(['horizontal group by chart'], chartType)) {
            return {
                orientation: 'horizontal',
                barType: 'stacked',
                chartType: 'horizontal group by chart',
            };
        }

        if (_.contains(['horizontal bar chart', 'horizontal'], chartType)) {
            return {
                orientation: 'horizontal',
                barType: 'grouped',
                chartType: 'horizontal group by chart',
            };
        }

        if (_.contains(['horizontal grouped bar chart'], chartType)) {
            return {
                orientation: 'horizontal',
                barType: 'grouped',
                chartType: 'horizontal group by chart'
            };
        }

        if (_.contains(['vertical grouped bar chart'], chartType)) {
            return {
                orientation: 'vertical',
                barType: 'grouped',
                chartType: 'group by chart',
            };
        }

        return {
            orientation: 'vertical',
            barType: 'stacked',
            chartType: 'bar chart',
        };
    },

    /**
     * Handle the report failed
     *
     * @param {Error} error
     */
    _handleError: function(error) {
        // don't show alert for dashlets
        if (!this.options.useCustomReportDef) {
            const message = app.utils.tryParseJSONObject(error.responseText);
            let errorMessage = message ? message.error_message : error.responseText;

            let reportModel = this.context.get('model');

            if (!reportModel.get('report_type') && this.layout) {
                reportModel = this.layout.model;
            }

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

        this._showEmptyChart(true, true);
        this.context.set('reportHasChart', false);

        if (_.has(this, 'layout') && this.layout && typeof(this.layout.trigger) === 'function') {
            this.layout.trigger('panel:widget:finished:loading', true, false);
        }

        this.context.set(
            'permissionsRestrictedReport',
            error.status === this.RECORD_NOT_FOUND_ERROR_CODE
        );
    },

    /**
     * Provides the base chart settings
     *
     * @return {Object}
     */
    _getChartDefaultSettings: function() {
        return {
            direction: app.lang.direction,
            colorData: 'class',
            allowScroll: true,
            config: true,
            hideEmptyGroups: true,
            reduceXTicks: true,
            rotateTicks: true,
            show_controls: false,
            show_title: false,
            show_x_label: true,
            show_y_label: true,
            staggerTicks: false,
            wrapTicks: false,
            showValues: 'middle',
            auto_refresh: 0,
        };
    },

    /**
     * Dispose chart element
     */
    _disposeChart: function() {
        if (this._chartField) {
            this._chartField.dispose();
            this._chartField = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeChart();
        this._super('_dispose');
    },
})

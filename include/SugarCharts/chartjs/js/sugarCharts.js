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
 * Main function to create a chartjs chart
 * @param {string} chartId unique id to identify DOM element
 * @param {Object} data Data for the chart
 * @param {string} css Custom css to load
 * @param chartConfig
 * @param chartParams
 * @param callback
 */
function loadSugarChart(chartId, data, css, chartConfig, chartParams, callback) {
    let elementId = 'chart_' + chartId;

    // make sure the chart container exists
    if (!document.getElementById(elementId)) {
        return;
    }

    var chartGroupType = chartConfig.barType ||
        chartConfig.lineType ||
        chartConfig.pieType ||
        chartConfig.funnelType ||
        'basic';

    // fix report view
    if (_.isUndefined(chartParams.chart_type) && !_.isUndefined(chartParams.type)) {
        chartParams.chart_type = chartParams.type;
        chartParams.type = 'saved-report-view';
    }

    let options = _.extend(
        {
            allowScroll: true,
            baseModule: 'Reports',
            colorData: 'default',
            dataType: chartGroupType === 'stacked' ? 'grouped' : chartGroupType,
            direction: 'ltr',
            hideEmptyGroups: true,
            label: SUGAR.charts.translateString('LBL_DASHLET_SAVED_REPORTS_CHART'),
            margin: {top: 10, right: 10, bottom: 10, left: 10},
            module: 'Reports',
            overflowHandler: false,
            reduceXTicks: false,
            rotateTicks: true,
            saved_report_id: chartConfig.chartId || chartId, //eslint-disable-line camelcase
            show_controls: false, //eslint-disable-line camelcase
            show_legend: true, //eslint-disable-line camelcase
            show_title: true, //eslint-disable-line camelcase
            show_tooltips: true, //eslint-disable-line camelcase
            show_x_label: false, //eslint-disable-line camelcase
            show_y_label: false, //eslint-disable-line camelcase
            showValues: false,
            stacked: true,
            staggerTicks: true,
            vertical: true,
            wrapTicks: true,
            x_axis_label: '', //eslint-disable-line camelcase
            y_axis_label: '', //eslint-disable-line camelcase
            allow_drillthru: true, //eslint-disable-line camelcase,
            barThickness: 20,
            chartElementId: elementId,
        }, chartConfig, chartParams);

    let noDataAvailable = SUGAR.charts.translateString('LBL_NO_DATA_AVAILABLE');

    // get and save the fiscal start date
    SUGAR.charts.defineFiscalYearStart();

    SUGAR.charts.get(data, options, (data, params) => {
        if (SUGAR.charts.dataIsEmpty(data)) {
            SUGAR.charts.renderError(chartId, noDataAvailable);
            if (callback) {
                callback(null);
            }
            return;
        }

        let chart = SUGAR.charts.getChart(data, params);
        chart.createChart();
        SUGAR.charts.renderChart(chartId, chart, data);

        if (callback) {
            callback(chart);
        }
    });
}

/**
 * Global sugar chart class
 */
(function($) {
    if (typeof SUGAR === 'undefined' || !SUGAR) {
        SUGAR = {};
    }

    /**
     * Base chart class - handles some common tasks
     * @param data Data for chart like report results
     * @param params Chart configuration
     * @constructor
     */
    function BaseChart(data, params) {
        this.rawData = data;
        this.params = params;
        this.chartType = '';
        this.app = SUGAR.charts.sugarApp;
        this.labels = SUGAR.charts.getChartStrings();
        this.chartElement = document.getElementById(this.params.chartElementId);
        this.chart = null;
        this.locale = SUGAR.charts.getUserLocale();

        this.chartJsMajorVersion = 3;

        this.hasSavedChart = false;

        this.colorList = [];
        this.defaultColorList = [
            '#517bf8', // @ocean
            '#36b0ff', // @pacific
            '#00e0e0', // @teal
            '#00ba83', // @green
            '#6cdf46', // @army
            '#ffd132', // @yellow
            '#ff9445', // @orange
            '#fa374f', // @red
            '#f476b1', // @coral
            '#cd74f2', // @pink
            '#8f5ff5', // @purple
            '#29388c', // @darkOcean
            '#145c95', // @darkPacific
            '#00636e', // @darkTeal
            '#056f37', // @darkGreen
            '#537600', // @darkArmy
            '#866500', // @darkYellow
            '#9b4617', // @darkOrange
            '#bb0e1b', // @darkRed
            '#a23354', // @darkCoral
            '#832a83', // @darkPink
            '#4c2d85', // @darkPurple
            '#c6ddff', // @lightOcean
            '#c0edff', // @lightPacific
            '#c5fffb', // @lightTeal
            '#baffcc', // @lightGreen
            '#e4fbb4', // @lightArmy
            '#fff7ad', // @lightYellow
            '#ffdebc', // @lightOrange
            '#ffd4d0', // @lightRed
            '#fde2eb', // @lightCoral
            '#f7d0fd', // @lightPink
            '#e2d4fd', // @lightPurple
        ];
        this.baseColorList = Array.isArray(this.params.colorOverrideList) ?
            this.params.colorOverrideList :
            this.defaultColorList;
        this.wrapperProperties = {};
    }

    /**
     * Sets the new chart params, updates chart colors, and re-renders the chart
     * @param newParams
     */
    BaseChart.prototype.updateParams = function(newParams) {
        this.params = newParams;

        this.updateChartColors();
        this.chart.update();
    };

    /**
     * Gets the chart options. This function should be extended by each chart type to provide
     * the relevant details.
     * @return {Object}
     */
    BaseChart.prototype.getChartOptions = function() {
        return {};
    };

    /**
     * Creates a new chart
     * @return {Object}
     */
    BaseChart.prototype.createChart = function() {
        this.destroyChart();
        this.data = this.transformData();

        this.chart = new window.Chart(this.chartElement, this.getChartOptions());
        return this.chart;
    };

    /**
     * Creates a new savable chart
     * @return {Object}
     */
    BaseChart.prototype.createSavableChart = function() {
        this.data = this.transformData();

        let savableChartOptions = this.makeChartSavable(this.getChartOptions());
        this.chart = new window.Chart(this.getSavableChartElement(), savableChartOptions);
        return this.chart;
    };

    /**
     * Checks if an image file of the chart should be saved
     * @return {boolean}
     */
    BaseChart.prototype.shouldSaveChart = function() {
        const isClassicSave = this.params.reportView && this.params.imageExportType && !this.params.isOnDrillthrough;
        const isNewSave = _.has(this.params, 'saveChartAsImage') && this.params.saveChartAsImage === true;

        return !!(isClassicSave) || !!(isNewSave);
    };

    /**
     * Gets the element to use for savable charts
     * @return {null|HTMLElement}
     */
    BaseChart.prototype.getSavableChartElement = function() {
        return document.getElementById(`${this.params.chartElementId}_print`);
    };

    /**
     * Check if chart is savable from Sidecar
     *
     * @return {boolean}
     */
    BaseChart.prototype.isSavableChartFromSidecar = function() {
        return (_.has(this.params, 'saveChartAsImage') && this.params.saveChartAsImage === true);
    };

    /**
     * Alters the chart options to add the callback that saves the chart
     * @param chartOptions
     * @return {Object}
     */
    BaseChart.prototype.makeChartSavable = function(chartOptions) {
        chartOptions.plugins = chartOptions.plugins || [];
        chartOptions.plugins.push({
            id: 'savechart',
            afterRender: (chart, options) => this.handleChartSave(),
        });

        chartOptions.options.animation = chartOptions.options.animation || {};
        chartOptions.options.animation.duration = 0;

        const showLegendInPrint = (this.params.show_legend || this.params.print_chart_legend) &&
                                    this.params.chartType !== 'treemapChart';
        const showTitleInPrint = this.params.show_title || this.params.print_chart_title;

        if (chartOptions.options && chartOptions.options.plugins && chartOptions.options.plugins.legend) {
            chartOptions.options.plugins.legend.display =  showLegendInPrint;
        }

        if (chartOptions.options && chartOptions.options.legend) {
            chartOptions.options.legend.display = showLegendInPrint;
        }

        if (chartOptions.options && chartOptions.options.plugins && chartOptions.options.plugins.title) {
            chartOptions.options.plugins.title.display =  showTitleInPrint;
        }

        if (chartOptions.options && chartOptions.options.title) {
            chartOptions.options.title.display = showTitleInPrint;
        }

        return chartOptions;
    };

    /**
     * Saves the chart to an image
     */
    BaseChart.prototype.handleChartSave = function() {
        // Only save the chart the first time it is done rendering, and only on report view
        if (!this.shouldSaveChart() || this.hasSavedChart) {
            return;
        }

        let savableChartElement = this.getSavableChartElement();
        const saveFromSidecar = this.isSavableChartFromSidecar();

        SUGAR.charts.saveChartToImage(
            Chart.getChart(savableChartElement),
            this.params.saved_report_id,
            this.params.imageExportType,
            null,
            saveFromSidecar
        );
        this.hasSavedChart = true;
        this.destroyChart(savableChartElement);
    };

    /**
     * If this chart already exists, find and destroy it
     */
    BaseChart.prototype.destroyChart = function(chartElement = null) {
        // Use the saved chart object if possible
        if (this.chart && !chartElement) {
            this.chart.destroy();
            return;
        }

        // Otherwise, we need to find the chart and then destroy it. This is more straightforward
        // on chart.js 3 than on chart.js 2
        chartElement = chartElement || this.chartElement;
        if (this.chartJsMajorVersion === 2) {
            Chart2.helpers.each(Chart2.instances, (function(instance) {
                if (instance.canvas.id === chartElement.id) {
                    instance.destroy();
                }
            }).bind(this));
        } else {
            let chart = Chart.getChart(chartElement);
            if (chart) {
                chart.destroy();
            }
        }
    };

    /**
     * Checks if there are values provided in the chart data
     * @return {boolean}
     */
    BaseChart.prototype.hasValues = function() {
        return !!this.rawData.values.filter(d => Array.isArray(d.values) && d.values.length).length;
    };

    /**
     * Checks if chart data is discrete
     * @return {boolean}
     */
    BaseChart.prototype.isDiscreteData = function() {
        return this.hasValues() &&
            Array.isArray(this.rawData.label) &&
            this.rawData.label.length === this.rawData.values.length &&
            this.rawData.values.reduce((a, c, i) => {
                return a && Array.isArray(c.values) && c.values.length === 1 &&
                    this.pickLabel(c.label) === this.pickLabel(this.rawData.label[i]);
            }, true);
    };

    /**
     * Gets the correct label, with a default value if there isn't one
     * @param label
     * @return {string}
     */
    BaseChart.prototype.pickLabel = function(label) {
        label = [].concat(label)[0];
        return !_.isEmpty(label) ? label : this.labels.noLabel;
    };

    /**
     * Gets the chart properties
     * @return {Object}
     */
    BaseChart.prototype.getProperties = function() {
        let props = _.isArray(this.rawData.properties) ? _.first(this.rawData.properties) : this.rawData.properties;
        return props || {};
    };

    /**
     * Gets the graph title, if one is provided
     * @return {string}
     */
    BaseChart.prototype.getTitle = function() {
        let props = this.getProperties();
        return props ? props.title : '';
    };

    /**
     * Sums the provided values
     * @param values
     * @return {number}
     */
    BaseChart.prototype.sumValues = function(values) {
        // 0 is default value if reducing an empty list
        return values.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
    };

    /**
     * Truncate tick labels past a certain length
     * @param label
     * @param characterLimit optional, defaults to 20
     * @return {string}
     */
    BaseChart.prototype.setTickLength = function(label, characterLimit = 20) {
        if (label.length >= characterLimit) {
            return label.substring(0, characterLimit - 1).trim() + '…';
        }
        return label;
    };

    /**
     * Helper function for formatting numeric tick values. Handles both numbers and currencies
     * @param value
     * @param isCurrency
     * @param roundNum
     * @return {string}
     */
    BaseChart.prototype.formatNumericTicks = function(value, isCurrency, roundNum = 0) {
        return SUGAR.charts.sugarApp.utils.charts.numberFormatSI(value, roundNum, isCurrency, this.locale);
    };

    /**
     * Customize tooltip title
     *
     * @param {Object} tooltip
     * @param {Object} data
     * @return {boolean|string|Object}
     */
    BaseChart.prototype.getCustomTooltipTitle = function(tooltip, data) {
        if (!_.isUndefined(this.params.tooltip) && !_.isUndefined(this.params.tooltip.title)) {
            if (!_.isFunction(this.params.tooltip.title)) {
                return this.params.tooltip.title;
            }
            return this.params.tooltip.title(this, tooltip, data);
        }
        return false;
    };

    /**
     * Customize tooltip label
     *
     * @param {Object} tooltip
     * @param {Object} data
     * @return {boolean|string|Object}
     */
    BaseChart.prototype.getCustomTooltipLabel = function(tooltip, data) {
        if (!_.isUndefined(this.params.tooltip) && !_.isUndefined(this.params.tooltip.label)) {
            if (!_.isFunction(this.params.tooltip.label)) {
                return this.params.tooltip.label;
            }
            return this.params.tooltip.label(this, tooltip, data);
        }
        return false;
    };

    /**
     * Generic options and callbacks for building tooltips
     * @return {Object}
     */
    BaseChart.prototype.getTooltipOptions = function() {
        return {
            display: true,
            displayColors: false,
            callbacks: {
                title: (function(tooltip) {
                    let title = this.getCustomTooltipTitle(tooltip);
                    if (!title) {
                        title = _.first(tooltip).label;
                    }
                    return title;
                }).bind(this),
                label: (function(tooltip) {
                    let label = this.getCustomTooltipLabel(tooltip);
                    if (!label) {
                        let value = tooltip.raw;
                        let props = this.getProperties();
                        let yIsCurrency = props.yDataType === 'currency';
                        let isNonBarMultipleDatasets = !this.params.barType && this.data.datasets.length > 1;
                        let isGroupedBarChart = !this.isSingleDataset() &&
                            ['grouped', 'stacked'].includes(this.params.barType);
                        let isPieLike = ['pieChart', 'donutChart'].includes(this.params.chartType);

                        let total;
                        if (isPieLike) {
                            total = this.sumValues(this.data.datasets[0].data);
                        } else if (isGroupedBarChart) {
                            total = this.sumValues(this.data.datasets.map(dataset => dataset.data[tooltip.dataIndex]));
                        } else if (this.mutatedBarChart) {
                            const allValues = _.chain(this.data.datasets).pluck('data').flatten().value();
                            total = this.sumValues(allValues);
                        } else {
                            total = this.sumValues(tooltip.dataset.data);
                        }

                        let labels = [];

                        if ((isGroupedBarChart || isNonBarMultipleDatasets) && !isPieLike) {
                            let text = isNonBarMultipleDatasets ? props.groupName : props.seriesName;
                            if (text) {
                                labels.push({
                                    text: text,
                                    value: tooltip.dataset.label,
                                });
                            }
                        }

                        labels.push({
                            text: yIsCurrency ? this.labels.tooltip.amount : this.labels.tooltip.count,
                            value: yIsCurrency ?
                                this.app.currency.formatAmountLocale(value, this.locale.currency_id) :
                                this.app.utils.charts.numberFormat(value, this.locale.precision, false, this.locale),
                        });

                        if (this.app.utils.charts.isNumeric(value) && !isNonBarMultipleDatasets) {
                            labels.push({
                                text: this.labels.tooltip.percent,
                                value: this.app.utils.charts.numberFormatPercent(value, total, this.locale),
                            });
                        }

                        label =  labels.map(label => `${label.text}: ${label.value}`);
                    }
                    return label;
                }).bind(this),
            }
        };
    };

    /**
     * Checks if the chart has a single dataset
     * @return {boolean}
     */
    BaseChart.prototype.isSingleDataset = function() {
        return this.rawData.values && this.rawData.values[0] && this.rawData.values[0].values.length === 1;
    };

    /**
     * Gets the tick options for a chart, depending on how the chart is configured
     * @param axis
     * @return {Object}
     */
    BaseChart.prototype.getTickOptions = function(axis) {
        const getLabel = index => {
            let label = this.getProperties().type === 'line chart' ?
                this.rawData.label[index] :
                this.rawData.values[index].label;
            return this.setTickLength(this.pickLabel(label));
        };

        // Default: just display the labels
        let tickOptions = {
            autoSkip: false,
            color: this.getTextColor(),
            callback: index => getLabel(index),
        };
        let numericTickOptions = {
            autoskip: false,
            minRotation: 0,
            maxRotation: 0,
            color: this.getTextColor(),
            callback: (value, index, values) => {
                let props = this.getProperties();
                const round = (value < 10 || props.yDataType === 'currency') ? 2 : null;
                return this.formatNumericTicks(value, props.yDataType === 'currency', round);
            },
        };

        if (axis === 'y' && this.params.orientation !== 'horizontal') {
            return numericTickOptions;
        }

        if (axis === 'x') {
            if (this.params.orientation === 'horizontal') {
                return numericTickOptions;
            }

            if (this.params.rotateTicks) {
                tickOptions = {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 45,
                    color: this.getTextColor(),
                    callback: index => getLabel(index),
                };
            }

            if (this.params.staggerTicks) {
                tickOptions = {
                    autoSkip: false,
                    maxRotation: 0,
                    minRotation: 0,
                    color: this.getTextColor(),
                    callback: index => {
                        let label = getLabel(index);
                        return index % 2 === 0 ? [' ', label] : label;
                    },
                };
            }

            if (this.params.wrapTicks) {
                tickOptions = {
                    autoSkip: false,
                    maxRotation: 0,
                    minRotation: 0,
                    color: this.getTextColor(),
                    callback: index => {
                        let label = getLabel(index);
                        return /\s/.test(label) ? label.split(' ') : label;
                    },
                };
            }
        }

        return tickOptions;
    };

    /**
     * Gets the color array for the chart. If an index is provided, returns the specific color at that index
     * @param index
     * @param usePattern
     * @return {Array|string}
     */
    BaseChart.prototype.getColors = function(index = -1, usePattern = false) {
        if (!this.colorList.length) {
            let colorList = this.baseColorList;
            let dataLength = -1;

            switch (this.params.chartType) {
                case 'pieChart':
                case 'donutChart':
                case 'lineChart':
                case 'funnelChart':
                case 'treemapChart':
                    dataLength = this.rawData.values.length;
                    break;
                case 'barChart':
                    if (['grouped', 'stacked'].includes(this.params.barType)) {
                        if (this.rawData.values &&
                            this.rawData.values[0] &&
                            this.rawData.values[0].values.length === 1
                        ) {
                            dataLength = this.rawData.values.length;
                        } else {
                            dataLength = this.rawData.label.length;
                        }
                    } else {
                        dataLength = this.rawData.values.length;
                    }
                    break;
            }

            if (dataLength > this.baseColorList.length) {
                for (let i = 1; i < dataLength / this.baseColorList.length; i++) {
                    colorList = colorList.concat(this.baseColorList);
                }
            }

            this.colorList = colorList;
        }

        if (index !== -1) {
            return usePattern ? this.createPattern(this.colorList[index]) : this.colorList[index];
        } else {
            return this.colorList;
        }
    };

    /**
     * Creates a hatch pattern using the given color as a background
     * @param color
     * @return {CanvasPattern}
     */
    BaseChart.prototype.createPattern = function(color) {
        // Based on https://stackoverflow.com/a/66227018

        // create a 10x10 px canvas for the pattern's base shape
        let shape = document.createElement('canvas');
        shape.width = 10;
        shape.height = 10;
        // get the context for drawing
        let c = shape.getContext('2d');
        // fill in the background color
        c.fillStyle = color;
        c.fillRect(0, 0, shape.width, shape.height);
        // draw 1st line of the shape
        c.strokeStyle = '#ffffff'; // @white
        c.beginPath();
        c.moveTo(2, 0);
        c.lineTo(10, 8);
        c.stroke();
        // draw 2nd line of the shape
        c.beginPath();
        c.moveTo(0, 8);
        c.lineTo(2, 10);
        c.stroke();
        // create the pattern from the shape
        return c.createPattern(shape, 'repeat');
    };

    /**
     * Gets the color to use for text elements of the charts
     * @return {string}
     */
    BaseChart.prototype.getTextColor = function() {
        return this.app.utils.isDarkMode() && !this.params.ignoreDarkMode ?
            '#e5eaed' : // @gray30
            '#2b2d2e'; // @gray90
    };

    /**
     * Gets the colors to use for data labels
     *
     * @return {Array}
     */
    BaseChart.prototype.getDataLabelsColor = function() {
        const grayBlack = '#2b2d2e'; // @gray90
        const grayWhite = '#e5eaed'; // @gray30

        // as long as the label is placed outside of the segment and on the chart container
        // we need to set a color opposite to the theme and ignore the segment color
        if (this.params.showValues === 'top' && !this.params.stacked) {
            return _.map(this.getColors(), function() {
                return this.app.utils.isDarkMode() ? grayWhite : grayBlack;
            }, this);
        }

        return _.map(this.getColors(), function(color) {
            return this.app.utils.isWhiteColor(color) ? grayBlack : grayWhite;
        }, this);
    };

    /**
     * Gets the color to use for border elements of the charts
     * @return {string}
     */
    BaseChart.prototype.getBorderColor = function() {
        if (this.app.utils.isDarkMode() && !this.params.ignoreDarkMode) {
            if (this.params.isOnDrillthrough) {
                return '#2b2d2e'; // @gray90
            }
            return this.params.reportView ?
                '#16191d' : // @gray95
                '#000000'; // @black
        } else {
            if (this.params.isOnDrillthrough) {
                return '#f1f3f4'; // @gray20
            }
            return this.params.reportView ?
                '#f8fafc' : // @gray10
                '#ffffff'; // @white
        }
    };

    /**
     * Gets the color to use for axis elements of the charts
     * @return {string}
     */
    BaseChart.prototype.getAxisColors = function() {
        return this.app.utils.isDarkMode() && !this.params.ignoreDarkMode ?
            '#4d5154' : // @gray80
            '#e5eaed'; // @gray30
    };

    /**
     * Gets the on click callback function to run when clicking on a chart
     * @return {Function}
     */
    BaseChart.prototype.getClickHandler = function() {
        if (this.params.onClick && _.isFunction(this.params.onClick)) {
            return this.params.onClick.bind(this);
        }

        return function(event, activeElements, chart) {
            let element;
            let groupIndex;
            let groupLabel;
            let seriesIndex;
            let seriesLabel;

            let params = Object.assign({}, this.params);

            // report_def is defined as a global in _reportCriteriaWithResult
            // but only in Reports module
            // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
            //TODO: fix usage of global report_def
            let reportDef = report_def;
            // jscs:enable requireCamelCaseOrUpperCaseIdentifiers

            if (this.chartType === 'funnel') {
                if (_.isEmpty(activeElements)) {
                    return;
                }
                let internalChart = activeElements[0]._chart;
                let elementClicked = internalChart.getElementAtEvent(event);
                groupIndex = elementClicked[0]._index;
                groupLabel = internalChart.data.labels[groupIndex];
                seriesIndex = elementClicked[0]._datasetIndex;
                seriesLabel = internalChart.data.datasets[seriesIndex].label;
                params.seriesLabel = seriesLabel;
                params.seriesIndex = seriesIndex;
                params.groupLabel = groupLabel;
                params.groupIndex = groupIndex;
            } else {
                element = chart.getElementsAtEventForMode(event, 'nearest', {intersect: true}, false);
                if (_.isEmpty(element)) {
                    return;
                }
                groupIndex = element[0].index;
                groupLabel = chart.data.labels[groupIndex];
                seriesIndex = element[0].datasetIndex;
                seriesLabel = chart.data.datasets[seriesIndex].label;

                if (params.chart_type === 'line chart') {
                    params.groupLabel = seriesLabel;
                    params.groupIndex = seriesIndex;
                    params.seriesLabel = groupLabel;
                    params.seriesIndex = groupIndex;
                } else {
                    params.seriesLabel = seriesLabel;
                    params.seriesIndex = seriesIndex;
                    params.groupLabel = groupLabel;
                    params.groupIndex = groupIndex;
                }
            }

            let enums = SUGAR.charts.getEnums(reportDef);
            let groupDefs = SUGAR.charts.getGrouping(reportDef);

            let drawerContext = {
                chartData: this.app.utils.deepCopy(this.rawData),
                chartModule: reportDef.module,
                dashConfig: params,
                dashModel: null,
                enumsToFetch: enums,
                filterOptions: {
                    auto_apply: false
                },
                groupDefs: groupDefs,
                layout: 'drillthrough-drawer',
                module: 'Reports',
                reportData: reportDef,
                reportId: this.params.saved_report_id,
                skipFetch: true,
                useSavedFilters: true
            };

            SUGAR.charts.openDrawer(drawerContext);
        }.bind(this);
    };

    /**
     * Constructor for bar charts
     * @param data
     * @param params
     * @constructor
     */
    function BarChart(data, params) {
        BaseChart.call(this, data, params);
        this.chartType = 'bar';
        this.hiddenData = {};

        if ((this.params.reportView && this.isDiscreteData()) || this.params.barType === 'stacked') {
            this.params.dataType = 'grouped';
        }

        // Minimum space given to each datapoint on the axis. In vertical
        // bar charts, we want some extra space to make labels more readable
        let orientation = this.params.orientation ? this.params.orientation : 'horizontal';

        //We need minimum data point thickness in order to set the correct minimum height or width for the chart
        //Based on min height/width, we have horizontal/vertical scroll if needed
        const horizontalMinDatapointThickness = 45;
        const verticalMinDatapointThickness = 40;

        this.minDatapointThickness = orientation === 'horizontal' ? horizontalMinDatapointThickness :
            verticalMinDatapointThickness;

        // Percentage of how much space a data point takes up in its axis container
        this.categoryPercentage = 0.8;

        // Percentage of how much space a bar takes up in its category container
        this.barPercentage = 0.9;

        this.wrapperProperties = this.getChartWrapperProperties();
    }
    BarChart.prototype = Object.create(BaseChart.prototype);

    /**
     * BarChart options and callbacks for building tooltips
     * @return {Object}
     */
    BarChart.prototype.getTooltipOptions = function() {
        let tooltipOptions = BaseChart.prototype.getTooltipOptions.call(this);

        if (this.params.orientation !== 'horizontal') {

            /**
             * Positioner function that keeps tooltip on screen (x Axis)
             *
             * @param {Array} elements
             * @param {Object} eventPosition
             *
             * @return {Object}
             */
            window.Chart.Tooltip.positioners.alwaysOnScreenPositioner = function(elements, eventPosition) {
                let xPos = eventPosition.x;
                let yPos = eventPosition.y;

                // only do this if we target an element of the chart
                if (elements.length > 0) {
                    const element = _.first(elements).element;
                    const chartPos = $(element.$context.chart.canvas).position();
                    const tooltipPos = element.tooltipPosition();
                    const tooltipWidth = this.width;

                    // check if the tooltip goes of screen on the left side
                    // only do this for a vertical chart
                    const left = chartPos.left + tooltipPos.x - tooltipWidth;

                    if (left < 0 && !element.horizontal) {
                        // place it onto the right of the element and change the caret to the left part
                        xPos = tooltipPos.x + tooltipWidth + element.width;
                        this.xAlign = 'left';
                    }
                }

                return {
                    x: xPos,
                    y: yPos,
                };
            };

            tooltipOptions.position = 'alwaysOnScreenPositioner';
            tooltipOptions.xAlign = 'right';
        }

        return tooltipOptions;
    };

    /**
     * Gets the default CSS properties that should be applied to any
     * wrapper element around the chart
     * @return {Object} the key/value CSS properties to apply
     */
    BarChart.prototype.getChartWrapperProperties = function() {
        let wrapperProperties = {};

        // Get the total count of data points that will be displayed
        let values = this.rawData && this.rawData.values || [];
        let valuesCount = values.length;

        // For grouped bar charts that are not stacked, each datapoint
        // has sub-values, so we need to count those instead
        if (this.params.dataType === 'grouped' && !this.params.stacked) {
            valuesCount = 0;
            _.each(values, function(value) {
                let subValues = value.values || [];
                valuesCount += subValues.length;
            }, this);
        }

        // To set the correct minimum height or width for the chart, we need
        // to figure out how large it needs to be so that each datapoint follows
        // the minDatapointThickness setting
        let orientation = this.params.orientation ? this.params.orientation : 'horizontal';
        if (orientation === 'horizontal') {
            let topMargin = _.get(this.params, ['margin', 'top']) || 0;
            let bottomMargin = _.get(this.params, ['margin', 'bottom']) || 0;
            let minChartHeight = (valuesCount * this.minDatapointThickness) + topMargin + bottomMargin;
            wrapperProperties['min-height'] = `${minChartHeight}px`;
        } else {
            let leftMargin = _.get(this.params, ['margin', 'left']) || 0;
            let rightMargin = _.get(this.params, ['margin', 'right']) || 0;
            let minChartWidth = (valuesCount * this.minDatapointThickness) + leftMargin + rightMargin;
            wrapperProperties['min-width'] = `${minChartWidth}px`;
            wrapperProperties['min-height'] = '200px';
        }

        return wrapperProperties;
    };

    /**
     * This is a helper method for formatting the chartJS bar charts
     * so that they look more similar to 11.0.0 when the labels are at the top
     * See https://sugarcrm.atlassian.net/browse/SS-2445 for more info
     * @param {Object} chartDetails
     * @param {Object} params
     * @param {Object} props
     * @param {Object} rawData
     * @param {Object} locale
     * @return {Object}
     */
    const formatBarChartTopLabels = function(chartDetails, params, props, rawData, locale) {
        if (params.orientation !== 'vertical') {
            chartDetails.options.plugins.datalabels.anchor = 'end';
        }

        if (params.barType === 'grouped') {
            let maxValue = 0;
            rawData.values.forEach(group => {
                group.values.forEach(value => {
                    maxValue = Math.max(maxValue, value);
                });
            });

            if (maxValue != 0) {
                // Increase the max for the y axis depending on how long the text for the maximum value will be,
                // so that the data label's text does not stretch into the chart's legend.
                const multiplier = SUGAR.charts.sugarApp.utils.charts.numberFormatSI(
                    maxValue, 2,
                    props.yDataType,
                    locale
                ).length * .068;
                chartDetails.options.scales.y.max = maxValue + (maxValue * multiplier);
            }
        }
        return chartDetails;
    };

    /**
     * Gets the chart options for bar charts
     * @return {Object}
     */
    BarChart.prototype.getChartOptions = function() {
        let isHorizontal = this.params.orientation === 'horizontal';

        // For basic bar charts or bar charts with only one dataset we need to transform the data
        // so that we can display the legend
        const isSimpleChart = !['grouped', 'stacked'].includes(this.params.barType) || this.isSingleDataset();

        if (isSimpleChart) {
            this.mutateToStacked();
        }

        if (!isHorizontal) {
            this.params.rotateTicks = true;
            this.params.staggerTicks = false;
            this.params.wrapTicks = false;
        }

        let chartDetails = {
            type: this.chartType,
            data: this.data,
            plugins: [ChartDataLabels],
            options: {
                animation: {
                    onComplete: function(animation) {
                        if (animation.initial) {
                            animation.chart.update();
                        }
                    }
                },
                indexAxis: isHorizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: isHorizontal ? this.params.show_y_label : this.params.show_x_label,
                            text: isHorizontal ? this.params.y_axis_label : this.params.x_axis_label,
                            color: this.getTextColor(),
                            font: {
                                weight: 'bold',
                            },
                        },
                        stacked: this.params.stacked,
                        ticks: this.getTickOptions('x'),
                        border: {
                            display: true,
                        },
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: this.getAxisColors(),
                        },
                    },
                    y: {
                        title: {
                            display: isHorizontal ? this.params.show_x_label : this.params.show_y_label,
                            text: isHorizontal ? this.params.x_axis_label : this.params.y_axis_label,
                            color: this.getTextColor(),
                            font: {
                                weight: 'bold',
                            },
                        },
                        stacked: this.params.stacked,
                        ticks: this.getTickOptions('y'),
                        border: {
                            display: true,
                        },
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: this.getAxisColors(),
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: this.params.show_legend,
                        labels: {
                            color: this.getTextColor(),
                            usePointStyle: true,
                            pointStyle: 'circle',
                        },
                    },
                    title: {
                        display: this.params.show_title,
                        text: this.getTitle(),
                        color: this.getTextColor(),
                    },
                    tooltip: this.getTooltipOptions(),
                    datalabels: {
                        color: this.getDataLabelsColor(),
                        anchor: (context) => {
                            return this.getLabelAnchorValue(context);
                        },
                        align: (context) => {
                            return this.getLabelAlignValue(context);
                        },
                        padding: 4,
                        formatter: (function(value, context) {
                            let props = this.getProperties();
                            return this.formatNumericTicks(value, props.yDataType === 'currency', 2);
                        }).bind(this),
                        display: (function(context) {
                            if ([0, '0', 'total'].includes(this.params.showValues)) {
                                return false;
                            }

                            if (this.params.showValues === 'top' && this.params.stacked !== true) {
                                return true;
                            }

                            // Avoid unreadable labels on small bars.
                            const pixelsPerDigit = 10;
                            const value = context.dataset.data[context.dataIndex];
                            const minBarSize = 10;
                            const targetBarSize = Math.round(value).toString().length * pixelsPerDigit;
                            const datasetMeta = context.chart.getDatasetMeta(context.datasetIndex);
                            const barWidth = datasetMeta.data[context.dataIndex].width;
                            const barHeight = datasetMeta.data[context.dataIndex].height;

                            return barWidth >= targetBarSize && value > 0 && barHeight > minBarSize;
                        }).bind(this),
                    },
                },
                onClick: this.getClickHandler(),
            }
        };

        if (this.params.showValues === 'total') {
            let totalAxis = isHorizontal ? 'y1' : 'x1';
            chartDetails.options.scales[totalAxis] = {
                display: true,
                stacked: this.params.stacked,
                position: isHorizontal ? 'right' : 'top',
                title: {
                    display: false,
                },
                border: {
                    display: false,
                },
                grid: {
                    drawOnGridArea: false,
                    drawTicks: false,
                },
                ticks: {
                    minRotation: 0,
                    maxRotation: 0,
                    autoSkip: false,
                    color: this.getTextColor(),
                    callback: index => {
                        let props = this.getProperties();
                        let value = this.sumValues(this.rawData.values[index].values);
                        return this.formatNumericTicks(value, props.yDataType === 'currency');
                    },
                },
            };
        }

        if (this.params.showValues === 'top') {
            chartDetails = formatBarChartTopLabels(
                chartDetails,
                this.params,
                this.getProperties(),
                this.rawData,
                this.locale
            );
        }
        return chartDetails;
    };

    /**
     * Transform a basic bar chart into a stacked one so that we can use legend
     */
    BarChart.prototype.mutateToStacked = function() {
        if (_.isUndefined(this.rawData) ||
            (_.isArray(this.rawData.label) && this.rawData.label.length === 1)) {
            return;
        }
        this.params.stacked = true;
        this.mutatedBarChart = true;

        let formattedDataset = [];

        if (_.isUndefined(this.data)) {
            return;
        }

        const dataset = _.first(this.data.datasets);

        if (!_.isUndefined(dataset)) {
            _.each(dataset.data, function(value, idx) {
                const columns = this.data.labels.length;
                let values = [];

                for (let columnIdx = 0; columnIdx < columns; columnIdx++) {
                    if (columnIdx === idx) {
                        values.push(value);
                    } else {
                        values.push(0);
                    }
                }

                const undefinedLabel = this.app.lang.get('LBL_CHART_NO_LABEL');
                const isLabelDefined = dataset.label && _.isString(dataset.label);
                let label = this.data.labels[idx];

                if (isLabelDefined && dataset.label.toLowerCase() !== undefinedLabel.toLowerCase()) {
                    label = dataset.label;
                }

                formattedDataset.push({
                    backgroundColor: dataset.backgroundColor[idx],
                    barPercentage: dataset.barPercentage,
                    categoryPercentage: dataset.categoryPercentage,
                    data: values,
                    label: label,
                });
            }, this);

            this.data.datasets = formattedDataset;
        }
    };

    /**
     * Clamp the position of a label inside a vertical bar chart
     *
     * @param {Object} context
     *
     * @return {string|boolean}
     */
    BarChart.prototype.clampDatalabelVerticalPosition = function(context) {
        // make sure we don't display the label outside of the segment if we have a groupped chart
        if (this.params.orientation === 'vertical' && !(this.params.showValues === 'top' && !this.params.stacked)) {
            let minBarHeight = 25;
            const datasetMeta = context.chart.getDatasetMeta(context.datasetIndex);
            const barHeight = datasetMeta.data[context.dataIndex].height;

            if (barHeight < minBarHeight) {
                return 'center';
            }
        }

        return false;
    };

    /**
     * Gets the data label align value
     *
     * @param {Object} context
     *
     * @return {string}
     */
    BarChart.prototype.getLabelAlignValue = function(context) {
        let alignMap = {
            '1': 'start',
            start: 'end',
            middle: 'center',
            end: 'start',
            top: this.params.stacked ? 'start' : 'end',
        };

        const clampedPosition = this.clampDatalabelVerticalPosition(context);

        return clampedPosition || alignMap[this.params.showValues] || 'center';
    };

    /**
     * Gets the data label anchor value
     *
     * @param {Object} context
     *
     * @return {string}
     */
    BarChart.prototype.getLabelAnchorValue = function(context) {
        let anchorMap = {
            '1': 'end',
            start: 'start',
            middle: 'center',
            end: 'end',
            top: 'end',
        };

        const clampedPosition = this.clampDatalabelVerticalPosition(context);

        return clampedPosition || anchorMap[this.params.showValues] || 'center';
    };

    /**
     * Gets the total value of the largest column of the dataset
     * @return {number}
     */
    BarChart.prototype.getLargestColumnTotal = function() {
        return Math.max(...this.rawData.values.map(value => this.sumValues(value.values)));
    };

    /**
     * Updates the chart colors, if a chart segment has been selected
     */
    BarChart.prototype.updateChartColors = function() {
        let isSingleDataset = this.rawData.values &&
            this.rawData.values[0] &&
            this.rawData.values[0].values.length === 1;
        let isGroupedOrStacked = ['grouped', 'stacked'].includes(this.params.barType);

        if (!_.isUndefined(this.params.groupIndex) && !_.isUndefined(this.params.seriesIndex)) {
            this.chart.data.datasets.map((dataset, index) => {
                if ((isSingleDataset || !isGroupedOrStacked) && !this.mutatedBarChart) {
                    this.chart.data.datasets[index].backgroundColor = [...this.getColors()];
                } else {
                    this.chart.data
                        .datasets[index]
                        .backgroundColor = new Array(this.rawData.values.length).fill(this.getColors(index));
                }
            });

            if (isGroupedOrStacked || this.mutatedBarChart) {
                this.chart.data
                    .datasets[this.params.seriesIndex]
                    .backgroundColor[this.params.groupIndex] = this.getColors(this.params.seriesIndex, true);
            } else {
                this.chart.data
                    .datasets[0]
                    .backgroundColor[this.params.groupIndex] = this.getColors(this.params.groupIndex, true);
            }
        }
    };

    /**
     * Transforms the data into a format chartjs can use
     * @return {Object}
     */
    BarChart.prototype.transformData = function() {
        if (['grouped', 'stacked'].includes(this.params.barType) && !this.isSingleDataset()) {
            return {
                labels: this.rawData.values.map(value => this.pickLabel(value.label)),
                datasets: this.rawData.label.map((label, index) => ({
                    categoryPercentage: this.categoryPercentage,
                    barPercentage: this.barPercentage,
                    label: this.pickLabel(label),
                    backgroundColor: new Array(this.rawData.values[0].values.length).fill(this.getColors(index)),
                    data: this.rawData.values.map(value => value.values[index]),
                })),
            };
        } else {
            return {
                labels: this.rawData.values.map(value => this.pickLabel(value.label)),
                datasets: [{
                    categoryPercentage: this.categoryPercentage,
                    barPercentage: this.barPercentage,
                    label: this.pickLabel(this.rawData.label.length === 1 ? this.rawData.label[0] : ''),
                    backgroundColor: [...this.getColors()],
                    data: this.rawData.values.map(value => this.sumValues(value.values)),
                }],
            };
        }
    };

    /**
     * Constructor for pie charts
     * @param data
     * @param params
     * @constructor
     */
    function PieChart(data, params) {
        BaseChart.call(this, data, params);
        this.chartType = 'pie';
    }
    PieChart.prototype = Object.create(BaseChart.prototype);

    /**
     * Gets the chart options for pie charts
     * @return {Object}
     */
    PieChart.prototype.getChartOptions = function() {
        let chartOptions = {
            type: this.chartType,
            data: this.data,
            plugins: [],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: this.params.cutout,
                plugins: {
                    legend: {
                        display: this.params.show_legend,
                        labels: {
                            color: this.getTextColor(),
                            usePointStyle: true,
                            pointStyle: 'circle',
                        },
                    },
                    title: {
                        display: this.params.show_title,
                        text: this.getTitle(),
                        color: this.getTextColor(),
                    },
                    tooltip: this.getTooltipOptions(),
                    datalabels: {
                        color: this.getDataLabelsColor(),
                        anchor: 'center',
                        padding: 4,
                        formatter: (function(value, context) {
                            return this.pickLabel(this.rawData.values[context.dataIndex].label);
                        }).bind(this),
                        display: context => context.dataset.data[context.dataIndex] !== 0,
                    },
                },
                onClick: this.getClickHandler(),
            },
        };

        if (this.params.donutLabelsOutside) {
            chartOptions.plugins.push(ChartDataLabels);
        }

        if (!_.isEmpty(this.params.hole)) {
            chartOptions.plugins.push({
                id: 'donuthole',
                beforeDraw: chart => {
                    // Based on https://stackoverflow.com/a/67486170
                    let width = chart.width;
                    let height = chart.height;

                    chart.ctx.restore();
                    let fontSize = (height / 114).toFixed(2);
                    chart.ctx.font = `${fontSize}em Inter`;
                    chart.ctx.textBaseline = 'middle';
                    chart.ctx.fillStyle = this.getTextColor();

                    let textX = Math.round((width - chart.ctx.measureText(this.params.hole).width) / 2);
                    let textY = 0;

                    if (!_.isUndefined(chart.legend) && chart.legend.height > 0) {
                        textY = chart.legend.height;
                    }

                    textY = (textY + height) / 2;

                    chart.ctx.fillText(this.params.hole, textX, textY);
                    chart.ctx.save();
                },
            });
        }

        return chartOptions;
    };

    /**
     * Updates the chart colors, if a chart segment has been selected
     */
    PieChart.prototype.updateChartColors = function() {
        if (!_.isUndefined(this.params.groupIndex)) {
            this.chart.data.datasets[0].backgroundColor = [...this.getColors()];
            this.chart.data
                .datasets[0]
                .backgroundColor[this.params.groupIndex] = this.getColors(this.params.groupIndex, true);
        }
    };

    /**
     * Transforms the data into a format chartjs can use
     * @return {Object}
     */
    PieChart.prototype.transformData = function() {
        return {
            labels: this.rawData.values.map(value => this.pickLabel(value.label)),
            datasets: [{
                backgroundColor: [...this.getColors()],
                borderColor: this.getBorderColor(),
                borderWidth: 2,
                data: this.rawData.values.map(value => this.sumValues(value.values)),
            }],
        };
    };

    /**
     * Constructor for line charts
     * @param data
     * @param params
     * @constructor
     */
    function LineChart(data, params) {
        BaseChart.call(this, data, params);
        this.chartType = 'line';
    }
    LineChart.prototype = Object.create(BaseChart.prototype);

    /**
     * Gets the chart options for line charts
     * @return {Object}
     */
    LineChart.prototype.getChartOptions = function() {
        return {
            type: this.chartType,
            data: this.data,
            options: {
                pointHitRadius: 5,
                pointHoverRadius: 5,
                responsive: true,
                maintainAspectRatio: this.params.maintainAspectRatio ? this.params.maintainAspectRatio : false,
                aspectRatio: this.params.aspectRatio ? this.params.aspectRatio : 2,
                scales: {
                    x: {
                        title: {
                            display: this.params.show_x_label,
                            text: this.params.x_axis_label,
                            color: this.getTextColor(),
                        },
                        ticks: this.getTickOptions('x'),
                        border: {
                            display: true,
                        },
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: this.getAxisColors(),
                        },
                    },
                    y: {
                        title: {
                            display: this.params.show_y_label,
                            text: this.params.y_axis_label,
                            color: this.getTextColor(),
                        },
                        ticks: this.getTickOptions('y'),
                        border: {
                            display: true,
                        },
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: this.getAxisColors(),
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: this.params.show_legend,
                        labels: {
                            color: this.getTextColor(),
                            usePointStyle: true,
                            pointStyle: 'circle',
                        },
                    },
                    title: {
                        display: this.params.show_title,
                        text: this.getTitle(),
                        color: this.getTextColor(),
                    },
                    tooltip: this.getTooltipOptions(),
                },
                onClick: this.getClickHandler(),
            },
        };
    };

    /**
     * @inheritdoc
     */
    LineChart.prototype.getTooltipOptions = function() {
        return {
            display: true,
            displayColors: false,
            callbacks: {
                title: (function(tooltip) {
                    let title = this.getCustomTooltipTitle(tooltip);
                    if (!title) {
                        title = _.first(tooltip).label;
                    }
                    return title;
                }).bind(this),
                label: (function(tooltip) {
                    let label = this.getCustomTooltipLabel(tooltip);
                    if (!label) {
                        let value = tooltip.raw;
                        let props = this.getProperties();
                        let yIsCurrency = props.yDataType === 'currency';

                        let labels = [
                            {
                                text: props.groupName,
                                value: tooltip.dataset.label,
                            },
                            {
                                text: yIsCurrency ? this.labels.tooltip.amount : this.labels.tooltip.count,
                                value: yIsCurrency ?
                                    this.app.currency.formatAmountLocale(value, this.locale.currency_id) :
                                    this.app.utils.charts.numberFormat(value, this.locale.precision, false,
                                        this.locale),
                            }
                        ];

                        label = labels.map(label => `${label.text}: ${label.value}`);
                    }
                    return label;
                }).bind(this),
            }
        };
    };

    /**
     * Updates the chart colors, if a chart segment has been selected
     */
    LineChart.prototype.updateChartColors = function() {
        if (!_.isUndefined(this.params.groupIndex) && !_.isUndefined(this.params.seriesIndex)) {
            this.chart.data.datasets.map((dataset, index) => {
                this.chart.data.datasets[index].pointRadius = new Array(dataset.data.length).fill(3);
            });

            this.chart.data.datasets[this.params.groupIndex].pointRadius[this.params.seriesIndex] = 6;
        }
    };

    /**
     * Transforms the data into a format chartjs can use
     * @return {Object}
     */
    LineChart.prototype.transformData = function() {
        return {
            labels: this.rawData.label.map(label => this.pickLabel(label)),
            datasets: this.rawData.values.map((value, index) => ({
                label: this.pickLabel(value.label),
                fill: false,
                borderColor: this.getColors(index),
                pointRadius: 3,
                backgroundColor: this.getColors(index),
                data: value.values,
            })),
        };
    };

    /**
     * Constructor for funnel charts
     * @param data
     * @param params
     * @constructor
     */
    function FunnelChart(data, params) {
        BaseChart.call(this, data, params);
        this.chartType = 'funnel';
        this.chartJsMajorVersion = 2;
    }

    FunnelChart.prototype = Object.create(BaseChart.prototype);

    /**
     * @inheritdoc
     */
    FunnelChart.prototype.createChart = function() {
        this.destroyChart();
        this.data = this.transformData();

        // funnel plugin only works on chartjs v2. We load that version under Chart2
        this.chart = new window.Chart2(this.chartElement, this.getChartOptions());
        return this.chart;
    };

    /**
     * @inheritdoc
     */
    FunnelChart.prototype.createSavableChart = function() {
        this.data = this.transformData();

        let savableChartOptions = this.makeChartSavable(this.getChartOptions({alwaysDisplayLabel: true}));
        this.chart = new window.Chart2(this.getSavableChartElement(), savableChartOptions);
        return this.chart;
    };

    /**
     * @inheritdoc
     */
    FunnelChart.prototype.handleChartSave = function() {
        // Only save the chart the first time it is done rendering, and only on report view
        if (!this.shouldSaveChart() || this.hasSavedChart) {
            return;
        }

        let savableChartElement = this.getSavableChartElement();
        const saveFromSidecar = this.isSavableChartFromSidecar();

        Chart2.helpers.each(Chart2.instances, (function(instance) {
            if (instance.canvas.id === savableChartElement.id) {
                SUGAR.charts.saveChartToImage(
                    instance,
                    this.params.saved_report_id,
                    this.params.imageExportType,
                    null,
                    saveFromSidecar
                );
            }
        }).bind(this));
        this.hasSavedChart = true;
        this.destroyChart(savableChartElement);
    };

    /**
     * Gets the chart options for funnel charts
     * @param rules
     *
     * @return {Object}
     */
    FunnelChart.prototype.getChartOptions = function(rules) {
        return {
            plugins: [ChartDataLabelsV1],
            type: this.chartType,
            data: this.data,
            options: {
                responsive: true,
                maintainAspectRatio: this.params.maintainAspectRatio ? this.params.maintainAspectRatio : false,
                aspectRatio: this.params.aspectRatio ? this.params.aspectRatio : 2,
                sort: 'desc',
                legend: {
                    display: this.params.show_legend,
                    labels: {
                        fontColor: this.getTextColor(),
                        usePointStyle: true,
                        pointStyle: 'circle',
                    },
                },
                title: {
                    display: this.params.show_title,
                    text: this.getTitle(),
                    fontColor: this.getTextColor(),
                },
                tooltips: this.getTooltipOptions(),
                onClick: this.getClickHandler(),
                plugins: {
                    datalabels: {
                        color: this.getDataLabelsColor(),
                        align: 'center',
                        formatter: (function(value, context) {
                            let props = this.getProperties();
                            return this.formatNumericTicks(value, props.yDataType === 'currency', 2);
                        }).bind(this),
                        display: (function(context) {
                            if ([0, '0', 'total'].includes(this.params.showValues)) {
                                return false;
                            }

                            if (rules && rules.alwaysDisplayLabel) {
                                return true;
                            }

                            const targetSet = context.chart.getDatasetMeta(context.datasetIndex || 0);
                            const targetBar = targetSet && targetSet.data ? targetSet.data[context.dataIndex] : false;
                            const targetCorners = targetBar ? targetBar._cornersCache : false;

                            const topLeftCornerIndex = 0;
                            const bottomLeftCornerIndex = 1;
                            const yPosIndex = 1;
                            const minHeightAllowed = 10;

                            let barHeight = 0;

                            if (targetCorners &&
                                targetCorners[topLeftCornerIndex] &&
                                targetCorners[bottomLeftCornerIndex]) {
                                const topY = targetCorners[topLeftCornerIndex][yPosIndex] || 0;
                                const bottomY = targetCorners[bottomLeftCornerIndex][yPosIndex] || 0;

                                barHeight = topY - bottomY;
                            }

                            return barHeight >= minHeightAllowed;
                        }
                        ).bind(this),
                    },
                }
            },
        };
    };

    /**
     * Updates the chart colors, if a chart segment has been selected
     */
    FunnelChart.prototype.updateChartColors = function() {
        if (!_.isUndefined(this.params.groupIndex)) {
            this.chart.data.datasets[0].backgroundColor = [...this.getColors()];
            this.chart.data
                .datasets[0]
                .backgroundColor[this.params.groupIndex] = this.getColors(this.params.groupIndex, true);
        }
    };

    /**
     * Transforms the data into a format chartjs can use
     * @return {Object}
     */
    FunnelChart.prototype.transformData = function() {
        return {
            labels: this.rawData.values.map(value => this.pickLabel(value.label)).reverse(),
            datasets: [{
                backgroundColor: [...this.getColors()],
                data: this.rawData.values.map(value => this.sumValues(value.values)).reverse(),
            }],
        };
    };

    /**
     * @inheritdoc
     */
    FunnelChart.prototype.getTooltipOptions = function() {
        return {
            position: 'custom',
            enabled: true,
            mode: 'point',
            display: true,
            displayColors: false,
            labelColor: function(tooltipItem, chart) {
                return {
                    borderColor: chart.data.datasets[0].backgroundColor[tooltipItem.index],
                    backgroundColor: chart.data.datasets[0].backgroundColor[tooltipItem.index]
                };
            },
            callbacks: {
                title: (function(tooltipItem, data) {
                    let title = this.getCustomTooltipTitle(tooltipItem, data);
                    if (!title) {
                        title = this.pickLabel(data.labels[tooltipItem[0].index]);
                    }
                    return title;
                }).bind(this),
                label: (function(tooltipItem, data) {
                    let label = this.getCustomTooltipLabel(tooltipItem, data);
                    if (!label) {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        let props = this.getProperties();
                        let yIsCurrency = props.yDataType === 'currency';
                        let isNonBarMultipleDatasets = this.data.datasets.length > 1;
                        let total = this.sumValues(data.datasets[tooltipItem.datasetIndex].data);
                        let labels = [];

                        if (isNonBarMultipleDatasets) {
                            labels.push({
                                text: isNonBarMultipleDatasets ? props.groupName : props.seriesName,
                                value: data.datasets[tooltipItem.datasetIndex].label,
                            });
                        }

                        labels.push({
                            text: yIsCurrency ? this.labels.tooltip.amount : this.labels.tooltip.count,
                            value: yIsCurrency ?
                                this.app.currency.formatAmountLocale(value, this.locale.currency_id) :
                                this.app.utils.charts.numberFormat(value, this.locale.precision, false, this.locale),
                        });

                        if (this.app.utils.charts.isNumeric(value) && !isNonBarMultipleDatasets) {
                            labels.push({
                                text: this.labels.tooltip.percent,
                                value: this.app.utils.charts.numberFormatPercent(value, total, this.locale),
                            });
                        }

                        label = labels.map(label => `${label.text}: ${label.value}`);
                    }
                    return label;
                }).bind(this),
            }
        };
    };

    /**
     * Constructor for treemap charts
     * @param data
     * @param params
     * @constructor
     */
    function TreemapChart(data, params) {
        BaseChart.call(this, data, params);
        this.chartType = 'treemap';
    }
    TreemapChart.prototype = Object.create(BaseChart.prototype);

    /**
     * Gets the chart options for treemap charts
     * @return {Object}
     */
    TreemapChart.prototype.getChartOptions = function() {
        return {
            type: this.chartType,
            data: this.data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false, // Legend is no use on this chart
                    },
                    title: {
                        display: this.params.show_title,
                        text: this.getTitle(),
                        color: this.getTextColor(),
                    },
                    tooltip: this.getTooltipOptions(),
                },
                onClick: this.getClickHandler(),
            }
        };
    };

    /**
     * Updates the chart colors, if a chart segment has been selected
     */
    TreemapChart.prototype.updateChartColors = function() {
        if (!_.isUndefined(this.params.groupIndex)) {
            this.chart.data.datasets[0].backgroundColor = (function(ctx) {
                if (ctx.type !== 'data') {
                    return;
                }
                return this.getColors(ctx.dataIndex, ctx.dataIndex === this.params.groupIndex);
            }).bind(this);
        }
    };

    /**
     * Transforms the data into a format chartjs can use
     * @return {Object}
     */
    TreemapChart.prototype.transformData = function() {
        // Pre-sort the data. The way the treemap library provides tooltip data does not reference the dataset in
        // any useful way, so if we don't feed it a pre-sorted list then we can't get the label and value properly.
        let newData = this.rawData.values.map(value => ({
            label: this.pickLabel(value.label),
            value: this.sumValues(value.values),
        }));
        newData.sort((a, b) => b.value - a.value);
        this.sortedData = newData;

        let props = this.getProperties();
        let yIsCurrency = props.yDataType === 'currency';

        return {
            labels: this.sortedData.map(value => this.pickLabel(value.label)),
            datasets: [{
                backgroundColor: (function(ctx) {
                    if (ctx.type !== 'data') {
                        return;
                    }
                    return this.getColors(ctx.dataIndex);
                }).bind(this),
                tree: this.sortedData,
                key: 'value',
                groups: ['label'],
                spacing: 1,
                labels: {
                    display: true,
                    align: 'center',
                    position: 'center',
                    color: '#ffffff', // @white
                    formatter: (function(ctx) {
                        if (ctx.type !== 'data') {
                            return;
                        }

                        let value = ctx.raw.v;
                        return [
                            this.sortedData[ctx.dataIndex].label,
                            yIsCurrency ?
                                this.app.currency.formatAmountLocale(value, this.locale.currency_id) :
                                this.app.utils.charts.numberFormat(value, this.locale.precision, false, this.locale),
                        ];
                    }).bind(this),
                },
            }],
        };
    };

    /**
     * Generic options and callbacks for building tooltips
     * @return {Object}
     */
    TreemapChart.prototype.getTooltipOptions = function() {
        return {
            display: true,
            displayColors: false,
            callbacks: {
                title: (function(tooltip) {
                    let title = this.getCustomTooltipTitle(tooltip);
                    if (!title) {
                        let dataIndex = _.first(tooltip).dataIndex;
                        title = this.pickLabel(this.sortedData[dataIndex].label);
                    }
                    return title;
                }).bind(this),
                label: (function(tooltip) {
                    let label = this.getCustomTooltipLabel(tooltip);
                    if (!label) {
                        let dataIndex = tooltip.dataIndex;
                        let value = this.sortedData[dataIndex].value;
                        let props = this.getProperties();
                        let yIsCurrency = props.yDataType === 'currency';
                        let isNonBarMultipleDatasets = !this.params.barType && this.data.datasets.length > 1;
                        let total = this.sumValues(this.sortedData.map(value => value.value));

                        let labels = [];

                        if (isNonBarMultipleDatasets) {
                            labels.push({
                                text: isNonBarMultipleDatasets ? props.groupName : props.seriesName,
                                value: tooltip.dataset.label,
                            });
                        }

                        labels.push({
                            text: yIsCurrency ? this.labels.tooltip.amount : this.labels.tooltip.count,
                            value: yIsCurrency ?
                                this.app.currency.formatAmountLocale(value, this.locale.currency_id) :
                                this.app.utils.charts.numberFormat(value, this.locale.precision, false, this.locale),
                        });

                        if (this.app.utils.charts.isNumeric(value) && !isNonBarMultipleDatasets) {
                            labels.push({
                                text: this.labels.tooltip.percent,
                                value: this.app.utils.charts.numberFormatPercent(value, total, this.locale),
                            });
                        }

                        label = labels.map(label => `${label.text}: ${label.value}`);
                    }
                    return label;
                }).bind(this),
            }
        };
    };

    SUGAR.charts = {
        sugarApp: (SUGAR.App || SUGAR.app || app),

        /**
         * Gets the chart data. If we're using this chart in a chart field or something similar, then
         * we already have the data and can immediately invoke the callback. On Reports results view,
         * however, all we have is a string representing the URL of the chart data. In this case, fetch
         * that data then invoke the callback.
         * @param urlordata
         * @param params
         * @param success
         */
        get: function(urlordata, params, success) {
            let data;

            if (_.isString(urlordata)) {
                data = {
                    r: new Date().getTime()
                };
                $.extend(data, params);
                $.ajax({
                    url: urlordata,
                    data: data,
                    dataType: 'json',
                    async: false,
                    success: data => success(data, params),
                });
            } else {
                success(urlordata, params);
            }
        },

        /**
         * From the provided data and chart parameters, generate the appropriate type of chart
         * @param data
         * @param params
         * @return {Object}
         */
        getChart: function(data, params) {
            let chart = SUGAR.charts.getChartInstance(data, params);

            if (chart.shouldSaveChart()) {
                let paramsForSaveChart = this.sugarApp.utils.deepCopy(params);

                //we will show the title only for the export file
                paramsForSaveChart.show_title = true;

                let savableChart = SUGAR.charts.getChartInstance(data, paramsForSaveChart, true);
                savableChart.createSavableChart();
            }

            return chart;
        },

        /**
         * Gets an instance of a chart class
         * @param data
         * @param params
         * @param isSavableChart
         * @return {BarChart|LineChart|PieChart|FunnelChart|TreemapChart}
         */
        getChartInstance: function(data, params, isSavableChart = false) {
            let chart;
            let chartParams = Object.assign({}, params);
            switch (params.chartType) {
                case 'barChart':
                    chart = new BarChart(data, chartParams);
                    break;
                case 'lineChart':
                    chart = new LineChart(data, chartParams);
                    break;
                case 'donutChart':
                case 'pieChart':
                    chartParams.cutout = chartParams.chartType === 'donutChart' ? '50%' : '0';
                    chart = new PieChart(data, chartParams);
                    break;
                case 'funnelChart':
                    chart = new FunnelChart(data, chartParams);
                    break;
                case 'treemapChart':
                    chart = new TreemapChart(data, chartParams);
                    break;
            }

            if (isSavableChart) {
                chart.params.ignoreDarkMode = true;
            }

            return chart;
        },

        /**
         * Execute callback function if specified
         *
         * @param callback function to invoke after chart rendering
         * @param chart Sucrose chart instance to render
         * @param chartId chart id used to select the chart container
         * @param params chart display control parameters
         */
        callback: function(callback, chart, chartId, params, chartData) {
            if (!_.isFunction(chart.update)) {
                return;
            }

            if (callback) {
                // if the call back is provided, include the chart as the only param
                callback(chart);
                return;
            }

            // only assign the event handler if chart supports it
            if (!_.isFunction(chart.seriesClick) || !params.allow_drillthru) {
                return;
            }

            // This default seriesClick callback is normally used
            // by the Report module charts. Saved Reports Chart
            // dashlets override with their own handler
            chart.seriesClick(_.bind(function(data, eo, chart, labels) {
                var chartState;
                var drawerContext;
                // report_def is defined as a global in _reportCriteriaWithResult
                // but only in Reports module
                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                //TODO: fix usage of global report_def
                let reportDef = report_def;
                // jscs:enable requireCamelCaseOrUpperCaseIdentifiers

                chartState = this.buildChartState(eo, labels);
                if (!_.isFinite(chartState.seriesIndex)) {
                    return;
                }

                if (params.chart_type === 'line chart') {
                    params.groupLabel = this.extractSeriesLabel(chartState, data);
                    params.seriesLabel = this.extractGroupLabel(chartState, labels);
                } else {
                    params.seriesLabel = this.extractSeriesLabel(chartState, data);
                    params.groupLabel = this.extractGroupLabel(chartState, labels);
                }

                var enums = this.getEnums(reportDef);
                var groupDefs = this.getGrouping(reportDef);

                drawerContext = {
                    chartData: chartData,
                    chartModule: reportDef.module,
                    chartState: chartState,
                    dashConfig: params,
                    dashModel: null,
                    enumsToFetch: enums,
                    filterOptions: {
                        auto_apply: false
                    },
                    groupDefs: groupDefs,
                    layout: 'drillthrough-drawer',
                    module: 'Reports',
                    reportData: reportDef,
                    reportId: chartId,
                    skipFetch: true,
                    useSavedFilters: true
                };

                chart.clearActive();
                if (chart.cellActivate) {
                    chart.cellActivate(chartState);
                } else if (chart.seriesActivate) {
                    chart.seriesActivate(chartState);
                } else {
                    chart.dataSeriesActivate(eo);
                }
                chart.dispatch.call('tooltipHide', this);

                this.sugarApp.alert.show('listfromreport_loading', {
                    level: 'process',
                    title: this.translateString('LBL_LOADING')
                });
                chart.clearActive();
                chart.render();
                this.openDrawer(drawerContext);

            }, this));
        },

        /**
         * Create an active state object based on chart element clicked
         *
         * @param eo an event object with extended properties
         * constructed from a clicked chart element
         * @param labels an array of grouping labels
         */
        buildChartState: function(eo, labels) {
            var seriesIndex;
            var state = {};

            if (!_.isEmpty(eo.series) && _.isFinite(eo.series.seriesIndex)) {
                seriesIndex = eo.series.seriesIndex;
            } else if (_.isFinite(eo.seriesIndex)) {
                seriesIndex = eo.seriesIndex;
            }
            if (_.isEmpty(labels)) {
                if (!_.isFinite(seriesIndex) && _.isFinite(eo.pointIndex)) {
                    seriesIndex = eo.pointIndex;
                }
            } else {
                if (_.isFinite(eo.groupIndex)) {
                    state.groupIndex = eo.groupIndex;
                }
                if (_.isFinite(eo.pointIndex)) {
                    state.pointIndex = eo.pointIndex;
                }
            }
            state.seriesIndex = seriesIndex;

            return state;
        },

        /**
         * Get the series label from chart data based on chart element clicked
         *
         * @param eo an event object with extended properties
         * constructed from a clicked chart element
         * @param data report data
         */
        extractSeriesLabel: function(state, data) {
            return data[state.seriesIndex].key;
        },

        /**
         * Get the group label from chart labels based on chart element clicked
         *
         * @param eo an event object with extended properties
         * constructed from a clicked chart element
         * @param labels an array of grouping labels
         */
        extractGroupLabel: function(state, labels) {
            var index = _.isUndefined(state.pointIndex) ? state.groupIndex : state.pointIndex;
            return _.isEmpty(labels) ? null : labels[index];
        },

        /**
         * Get the first or second grouping from report definition
         * or the first grouping if there is only one
         *
         * @param reportDef report definition object
         * @param i group definition index
         * @return {Object}
         */
        getGrouping: function(reportDef, i) {
            var groupDefs = reportDef.group_defs;
            if (isNaN(i)) {
                return groupDefs;
            }
            return i > 0 && groupDefs.length > 1 ? groupDefs[1] : groupDefs[0];
        },

        /**
         * Get and save the fiscal year start date as an application cached variable
         */
        defineFiscalYearStart: function() {
            let fiscalYear = this.getFiscalStartDate();

            if (!_.isEmpty(fiscalYear)) {
                return;
            }

            const config = this.sugarApp.metadata.getModule('Forecasts', 'config');
            let fiscalStartDate = !_.isEmpty(config) ? config.timeperiod_start_date : null;

            if (_.isEmpty(fiscalStartDate)) {
                fiscalYear = new Date().getUTCFullYear();
                fiscalStartDate = fiscalYear + '-01-01';
            }

            this.sugarApp.api.call('GET', this.sugarApp.api.buildURL('TimePeriods/' + fiscalStartDate), null, {
                success: _.bind(this.setFiscalStartDate, this),
                error: _.bind(function() {
                    // Needed to catch the 404 in case there isnt a current timeperiod
                }, this)
            });
        },

        /**
         * Process and set the defined fiscal time period in the application cache
         *
         * @param firstQuarter the currently configured fiscal time period
         */
        setFiscalStartDate: function(firstQuarter) {
            // it will be false if timeperiods are not set up
            if (!firstQuarter) {
                return;
            }

            const timePeriodType = 'Quarter';
            const fiscalYear = firstQuarter.start_date.split('-')[0];
            const quarterDateStart = new Date(firstQuarter.start_date);      // 2017-01-01
            let fiscalMonth = quarterDateStart.getUTCMonth();
            const day = quarterDateStart.getUTCDate();

            if (firstQuarter.type === timePeriodType) {
                const quarterNumber = firstQuarter.name.match(/.*Q(\d{1})/)[1];  // [1-4]
                fiscalMonth = fiscalMonth - (quarterNumber - 1) * 3; // 1
            }

            const fiscalYearStart = new Date(Date.UTC(fiscalYear, fiscalMonth, day, 0, 0, 0)).toUTCString();

            this.sugarApp.cache.set('fiscaltimeperiods', {'annualDate': fiscalYearStart});
        },

        /**
         * Get the currently defined fiscal time period from the application cache
         *
         * @return {string} a string representation of a UTC datetime
         */
        getFiscalStartDate: function() {
            var timeperiods = this.sugarApp.cache.get('fiscaltimeperiods');
            var datetime = !_.isEmpty(timeperiods) && !_.isUndefined(timeperiods.annualDate) ?
                timeperiods.annualDate :
                null;
            return datetime;
        },

        /**
         * Calculates the target year in case fiscal year is next year
         *
         * @param label chart group or series label
         * @return {string} the actual year
         */
        getTargetYear: function(label) {
            const config = this.sugarApp.metadata.getModule('Forecasts', 'config');

            if (!_.isUndefined(config) &&
                config.timeperiod_fiscal_year === 'next_year') {
                const actualYear = new Date(label).getUTCFullYear() - 1;

                return actualYear.toString();
            }

            return label;
        },

        /**
         * Process the user selected chart date label based on the report def
         * column function
         *
         * @param label chart group or series label
         * @param type group or series column function
         * @return {Array} a date range from a date parsed label
         */
        getDateValues: function(label, type) {
            var dateParser = this.sugarApp.date;
            var userLangPref = this.sugarApp.user.getLanguage() || 'en_us';

            let pattern = this.sugarApp.date.getUserDateFormat();
            const partialDatePatterns = {
                year: 'YYYY', // 2017
                quarter: 'Q YYYY', // Q3 2017
                month: 'MMMM YYYY', // March 2017
                week: 'W YYYY', // W56 2017
            };

            var startDate;
            var endDate;
            var y1;
            var y2;
            var m1;
            var m2;
            var d1;
            var d2;
            var values = [];

            if (_.isEmpty(label)) {
                // empty label, don't bother to process
                startDate = '';
                endDate = '';
            } else {
                switch (type) {

                    case 'fiscalYear':
                        // 2017
                        label = this.getTargetYear(label);

                        var fy = new Date(this.getFiscalStartDate() || new Date().getFullYear() + '-01-01');
                        fy.setUTCFullYear(label);
                        y1 = fy.getUTCFullYear();
                        m1 = fy.getUTCMonth() + 1;
                        d1 = fy.getUTCDate();
                        fy.setUTCMonth(fy.getUTCMonth() + 12);
                        fy.setUTCDate(fy.getUTCDate() - 1);
                        y2 = fy.getUTCFullYear();
                        m2 = fy.getUTCMonth() + 1;
                        d2 = fy.getUTCDate(); //1-31
                        startDate = y1 + '-' + m1 + '-' + d1;
                        endDate = y2 + '-' + m2 + '-' + d2;
                        break;

                    case 'fiscalQuarter':
                        // Q1 2017
                        var fy = new Date(this.getFiscalStartDate() || new Date().getFullYear() + '-01-01');
                        var re = /Q([1-4]{1})\s(\d{4})/;
                        var rm = label.match(re);
                        rm[2] = this.getTargetYear(rm[2]);

                        fy.setUTCFullYear(rm[2]);
                        fy.setUTCMonth((rm[1] - 1) * 3 + fy.getUTCMonth());
                        y1 = fy.getUTCFullYear();
                        m1 = fy.getUTCMonth() + 1;
                        d1 = fy.getUTCDate();
                        fy.setUTCMonth(m1 + 2);
                        fy.setUTCDate(fy.getUTCDate() - 1);
                        y2 = fy.getUTCFullYear();
                        m2 = fy.getUTCMonth() + 1;
                        d2 = fy.getUTCDate();
                        startDate = y1 + '-' + m1 + '-' + d1;
                        endDate = y2 + '-' + m2 + '-' + d2;
                        break;

                    case 'day':
                        var parsedDate = dateParser(label, pattern, userLangPref);
                        startDate = parsedDate.formatServer(true); //2017-12-31
                        break;

                    default:
                        pattern = partialDatePatterns[type] || partialDatePatterns.year;
                        var parsedDate = dateParser(label, pattern, userLangPref);
                        var momentType = type === 'week' ? 'isoweek' : type;
                        startDate = parsedDate.startOf(momentType).formatServer(true); //2017-01-01
                        endDate = parsedDate.endOf(momentType).formatServer(true); //2017-12-31
                        break;
                }
            }

            values.push(startDate);
            if (type !== 'day') {
                values.push(endDate);
                values.push(type);
            }

            return values;
        },

        /**
         * Process the user selected chart label and return an array with a
         * single filter input value, or three if a date range
         *
         * @param label chart group or series label
         * @param def report definition object
         * @param type the data type for the field
         * @param enums list of enums with their key value data translations
         *
         * @return {Array} a single element if not a date else three
         */
        getValues: function(label, def, type, enums) {
            var dateFunctions = ['year', 'quarter', 'month', 'week', 'day', 'fiscalYear', 'fiscalQuarter'];
            var columnFn = def.column_function;
            var isDateFn = !_.isEmpty(columnFn) && dateFunctions.indexOf(columnFn) !== -1;
            var values = [];

            // Send empty string if value is undefined
            if (this.translateString('LBL_CHART_UNDEFINED') === label) {
                label = '';
            }

            if (isDateFn) {
                // returns [dateStart, dateEnd, columnFn]
                values = this.getDateValues(label, columnFn);
            } else {
                switch (type) {
                    case 'bool':
                        if (this.translateListStrings('dom_switch_bool').on === label) {
                            values.push('1');
                        } else if (this.translateListStrings('dom_switch_bool').off === label) {
                            values.push('0');
                        }
                        break;
                    case 'enum':
                    case 'radioenum':
                        const data = enums[def.table_key + ':' + def.name][label];
                        if (_.isArray(data)) {
                            _.each(data, function(item) {
                                values.push(item);
                            });
                        } else {
                            values.push(data);
                        }

                        break;
                    case 'multienum':
                        const multiEnumValue = enums[def.table_key + ':' + def.name][label];
                        if (multiEnumValue) {
                            values.push(multiEnumValue);
                        } else {
                            const labels = label.split(', ');
                            _.each(labels, (label) => {
                                const multiEnumValue = enums[def.table_key + ':' + def.name][label];
                                values.push(multiEnumValue);
                            });
                        }
                        break;
                    case 'date':
                    case 'datetime':
                    case 'datetimecombo':
                        // convert to server format before sending
                        var date = new this.sugarApp.date(label, this.sugarApp.date.getUserDateFormat());
                        let dateValue = date.formatServer(true);
                        if (dateValue === 'Invalid date') {
                            dateValue = '';
                        }
                        values.push(dateValue);
                        break;
                    case 'int':
                        const numberSeparator = this.sugarApp.user.getPreference('number_grouping_separator');
                        label = label.replaceAll(numberSeparator, '');
                        values.push(label);
                        break;
                    default:
                        // returns [label]
                        values.push(label);
                        break;
                }
            }

            return values;
        },

        /**
         * Construct a new report definition filter
         *
         * @param reportDef report definition object
         * @param params chart display control parameters
         * @param enums list of enums with their key value data translations
         * @return {Array}
         */
        buildFilter: function(reportDef, params, enums) {
            var filter = [];

            var groups = this.getGrouping(reportDef, 0);
            var series = this.getGrouping(reportDef, 1);
            var groupType = this.getFieldDef(groups, reportDef).type;
            var seriesType = this.getFieldDef(series, reportDef).type;
            var isGroupType = params.dataType === 'grouped';
            var groupLabel = params.groupLabel;
            var seriesLabel = params.seriesLabel;

            var hasSameLabel = !_.isEmpty(seriesLabel) &&
                !_.isEmpty(groupLabel) &&
                seriesLabel === groupLabel;
            var hasSameGroup = groups.name === series.name &&
                groups.label === series.label &&
                groups.table_key === series.table_key;

            var addGroupRow = _.bind(function() {
                var groupsName = groups.table_key + ':' + groups.name;
                var groupsValues = this.getValues(groupLabel, groups, groupType, enums);
                addFilterRow(groupsName, groupsValues);
            }, this);

            var addSeriesRow = _.bind(function() {
                var seriesName = series.table_key + ':' + series.name;
                var seriesValues = this.getValues(seriesLabel, series, seriesType, enums);
                addFilterRow(seriesName, seriesValues);
            }, this);

            function addFilterRow(name, values) {
                var row = {};
                row[name] = values;
                filter.push(row);
            }

            // PIE | FUNNEL CHART & DISCRETE DATA
            if (!isGroupType && hasSameGroup && !_.isEmpty(seriesLabel) && _.isEmpty(groupLabel)) {
                // then use series
                groupLabel = groupLabel || seriesLabel;
                params.groupLabel = groupLabel;
                addSeriesRow();
            }
            // BASIC TYPE & DISCRETE DATA
            /*
                Accounts by Type ::
                Bar Chart :: industry == industry && Apparel != Accounts
            */
            else if (hasSameGroup && !hasSameLabel) {
                // then use group
                addGroupRow();
            }
            // PIE | FUNNEL CHART & GROUPED DATA
            // this happens when data with multiple groupings is displayed as pie or funnel
            /*
                Accounts by Type by Industry ::
                Bar Chart :: type != industry
            */
            else if (!isGroupType && !hasSameGroup) {
                // then use group
                if (!hasSameLabel) {
                    groupLabel = groupLabel || seriesLabel;
                    params.groupLabel = groupLabel;
                }
                addGroupRow();
            }
            // GROUPED OR BASIC TYPE & DISCRETE DATA (isGroupType ignored)
            /*
                Accounts by Type
                Bar Grouped Chart :: type == type && Apparel == Apparel
            */
            else if (hasSameGroup && hasSameLabel) {
                // then use either, but only one
                addSeriesRow();
            }
            // GROUPED TYPE & GROUPED DATA
            /*
                Accounts by Type by Industry ::
                Bar Grouped Chart :: type != industry
            */
            else if (isGroupType && !hasSameGroup) {
                // then use both
                addGroupRow();
                addSeriesRow();
            }

            return filter;
        },

        /**
         * If the type for the group by field is an enum type, return it
         *
         * @param reportDef
         * @return {Array} array of enums group defs
         */
        getEnums: function(reportDef) {
            var enumTypes = ['enum', 'radioenum', 'multienum'];
            var groups = this.getGrouping(reportDef);
            var enums = [];
            _.each(groups, function(group) {
                var groupType = this.getFieldDef(group, reportDef).type;
                if (groupType && _.contains(enumTypes, groupType)) {
                    enums.push(group);
                }
            }, this);
            return enums;
        },

        /**
         * Gets the field def from the group def
         *
         * @param groupDef
         * @param reportDef
         * @return {*} array
         */
        getFieldDef: function(groupDef, reportDef) {
            var module = reportDef.module || reportDef.base_module;

            if (groupDef.table_key === 'self') {
                return this.sugarApp.metadata.getField({name: groupDef.name, module: module});
            }

            // Need to parse something like 'Accounts:contacts:assigned_user_link:user_name'
            var relationships = groupDef.table_key.split(':');
            var fieldsMeta = this.sugarApp.metadata.getModule(module, 'fields');
            var fieldDef;
            for (var i = 1; i < relationships.length; i++) {
                var relationship = relationships[i];
                fieldDef = fieldsMeta[relationship];
                module = fieldDef.module || this._getModuleFromRelationship(fieldDef.relationship, module);
                fieldsMeta = this.sugarApp.metadata.getModule(module, 'fields');
            }
            fieldDef = fieldsMeta[groupDef.name];
            fieldDef.module = fieldDef.module || module;
            return fieldDef;
        },

        /**
         * Get the other side's module name
         *
         * @param relationshipName
         * @param module
         * @return {string} module name
         * @private
         */
        _getModuleFromRelationship: function(relationshipName, module) {
            var relationship = this.sugarApp.metadata.getRelationship(relationshipName);
            return module === relationship.lhs_module ? relationship.rhs_module : relationship.lhs_module;
        },

        /**
         * Open a drill through drawer
         */
        openDrawer: function(drawerContext) {
            var currentModule = this.sugarApp.drawer.context.get('module');

            // This needs to set to target module for Merge to show the target module fields
            this.sugarApp.drawer.context.set('module', drawerContext.chartModule);

            this.sugarApp.drawer.open({
                layout: 'drillthrough-drawer',
                context: drawerContext
            }, _.bind(function() {
                // reset the drawer module
                if (currentModule) {
                    this.sugarApp.drawer.context.set('module', currentModule);
                }
            }, this, currentModule));
        },

        /**
         * Main render chart method
         *
         * @param id chart id used to select the chart container
         * @param chart Chart.js chart instance to render
         * @param json report data to render
         */
        renderChart: function(id, chart, json) {
            chart.updateChartColors();
            chart.chart.update();
        },

        /**
         * Display an error message in chart container
         *
         * @param id chart id used to select the chart container
         * @param str error message string
         */
        renderError: function(id, message) {
            // TODO renderError
        },

        /**
         * Translate a chart string using current application language
         *
         * @param {string} appString string to translate
         * @param {string} module module where the string is defined
         * @return {string}
         */
        translateString: function(appString, module) {
            if (SUGAR.App) {
                // Sidecar
                if (module) {
                    return SUGAR.App.lang.get(appString, module);
                } else {
                    return SUGAR.App.lang.get(appString);
                }
            } else if (typeof app !== 'undefined' && app && app.lang) {
                // BWC works
                if (module) {
                    return app.lang.get(appString, module);
                } else {
                    return app.lang.get(appString);
                }
            } else if (SUGAR.language) {
                // BWC not works?
                if (module) {
                    return SUGAR.language.get(module, appString);
                } else {
                    return SUGAR.language.get('app_strings', appString);
                }
            } else {
                return appString;
            }
        },

        /**
         * Translate a chart string using current application language
         *
         * @param {string} appString string to translate
         * @return {string}
         */
        translateListStrings: function(appList) {
            if (SUGAR.App) {
                // Sidecar
                return SUGAR.App.lang.getAppListStrings(appList);
            } else if (app) {
                // BWC works
                return app.lang.getAppListStrings(appList);
            } else if (SUGAR.language) {
                // BWC not works?
                return SUGAR.language.get('app_list_strings', appList);
            } else {
                return appList;
            }
        },

        /**
         * Is data returned from the server empty?
         *
         * @param {Object} data
         * @return {boolean}
         * @deprecated Use dataIsEmpty(data) method instead.
         */
        isDataEmpty: function(data) {
            return data !== undefined && data !== 'No Data' && data !== '';
        },

        /**
         * Is data returned from the server empty?
         *
         * @param {Object} data
         * @return {boolean}
         */
        dataIsEmpty: function(data) {
            return _.isUndefined(data) ||
                _.isUndefined(data.values) ||
                !_.isArray(data.values) ||
                _.isEmpty(data.values);
        },

        /**
         * Saves the provided chart to an image
         * @param chart
         * @param chartId
         * @param imageExt
         * @param saveTo
         * @param saveFromSidecar
         */
        saveChartToImage: function(chart, chartId, imageExt, saveTo, saveFromSidecar) {
            //we don't have a chart to print
            //so there is nothing to be encoded and saved
            if (!chart || chart && (chart.width === 0 && chart.height === 0)) {
                return;
            }

            let uri = chart.toBase64Image(imageExt === 'jpg' ? 'image/jpeg' : 'image/png');
            let saveToUrl = saveTo || 'index.php?action=DynamicAction&DynamicAction=saveImage&module=Charts&to_pdf=1';

            if (saveFromSidecar === true) {
                // we have to get the bwc session id
                this.sugarApp.bwc.login(false, function saveImage() {
                    $.post(saveToUrl, {
                        imageStr: uri,
                        chart_id: chartId,
                    });
                });

                return;
            }

            $.post(saveToUrl, {
                imageStr: uri,
                chart_id: chartId,
            });
        },

        /**
         * Save the current chart to an image
         *
         * @param id chart id used to construct the chart container id
         * @param chart Sucrose chart instance to call
         * @param json report data to render
         * @param jsonfilename name of the data file to save image as
         * @param imageExt type of image to save
         * @param saveTo url of service to post the image to
         * @param complete the callback to reset chart instance after saving image
         *
         * @deprecated Since 11.3.0. Use SUGAR.charts.saveChartToImage() instead
         */
        saveImageFile: function(id, chart, json, jsonfilename, imageExt, saveTo, complete) {
            app.logger.warn(
                '`SUGAR.charts.saveImageFile()` is deprecated. Use `SUGAR.charts.saveChartToImage()` instead.'
            );
        },

        /**
         * Build a set of translated strings for intra-chart rendering
         * @param type the chart type
         */
        getChartStrings: function(type) {
            var noLabelStr = this.translateString('LBL_CHART_UNDEFINED');
            return {
                legend: {
                    close: this.translateString('LBL_CHART_LEGEND_CLOSE'),
                    open: this.translateString('LBL_CHART_LEGEND_OPEN'),
                    noLabel: noLabelStr
                },
                tooltip: {
                    amount: this.translateString('LBL_CHART_AMOUNT'),
                    count: this.translateString('LBL_CHART_COUNT'),
                    date: this.translateString('LBL_CHART_DATE'),
                    group: this.translateString('LBL_CHART_GROUP'),
                    key: this.translateString('LBL_CHART_KEY'),
                    percent: this.translateString('LBL_CHART_PERCENT')
                },
                noData: this.translateString('LBL_CHART_NO_DATA'),
                noLabel: noLabelStr,
                noDrillthru: this.translateString('LBL_CHART_NO_DRILLTHRU', 'Reports')
            };
        },

        /**
         * Construct a locale settings object in a format consumable by D3's locale() method
         *
         * @param {Object} pref (optional)  The associative array of preferences from which to build locale
         * @return {Object}  An associate array of locale settings
         */
        getLocale: function(pref) {
            var preferences = pref || this.getUserPreferences();

            return {
                'decimal': preferences.decimal_separator,
                'thousands': preferences.number_grouping_separator,
                'grouping': [3],
                'currency': [preferences.currency_symbol, ''],
                'currency_id': preferences.currency_id,
                'dateTime': '%a %b %e %X %Y',
                'date': this._dateFormat(preferences.datepref),
                'time': this._timeFormat(preferences.timepref),
                'periods': this._timePeriods(preferences.timepref),
                'days': this._dateStringArray('dom_cal_day_long'),
                'shortDays': this._dateStringArray('dom_cal_day_short'),
                'months': this._dateStringArray('dom_cal_month_long'),
                'shortMonths': this._dateStringArray('dom_cal_month_short'),
                'precision': preferences.decimal_precision
            };
        },

        /**
         * Construct a locale settings object for the current user
         *
         * @return {Object}  An associate array of locale settings
         */
        getUserLocale: function() {
            return this.getLocale(this.getUserPreferences());
        },

        /**
         * Retrieve the user preferences from which to build a locale
         *
         * @return {Object}  An associative array object of preferences
         * @private
         */
        getUserPreferences: function() {
            return this.sugarApp.user.get('preferences') || {};
        },

        /**
         * Get a preference setting from the currently loaded user object
         *
         * @param {string} pref  The name of the preference to retrieve
         * @return {string|Array|Object}
         */
        userPreference: function(pref) {
            return this.sugarApp.user.getPreference(pref) || pref;
        },

        /**
         * Construct a system locale settings object for the system
         *
         * @return {Object}  An associate array of locale settings
         */
        getSystemLocale: function() {
            return this.getLocale(this._getSystemPreferences());
        },

        /**
         * Retrieve the system preferences from which to build a locale
         *
         * @return {Object}  An associative array object of preferences
         * @private
         */
        _getSystemPreferences: function() {
            var config = this.sugarApp.config;
            var currency = this.sugarApp.currency;

            return {
                decimal_separator: config.defaultDecimalSeparator,
                number_grouping_separator: config.defaultNumberGroupingSeparator,
                currency_symbol: currency.getCurrencySymbol(currency.getBaseCurrencyId()),
                // TODO: datef and timef in config.php don't seem to be available in js
                datepref: 'm/d/Y',
                timepref: 'H:i',
                decimal_precision: config.defaultCurrencySignificantDigits
            };
        },

        /**
         * Given a user date format preference in a form like 'mm/dd/yyyy'
         * returns a D3 formatting specifier like '%b/%d/%Y'
         *
         * @param {string} pref  A string encoding in the form 'm/d/y'
         * which can contain one or more upper or lower case characters
         * in any order with optional separators or spaces
         * @return {string}  A date format pattern string in the form of '%b %-d, %Y'
         * @private
         */
        _dateFormat: function(pref) {
            if (!pref) {
                return '%b %-d, %Y';
            }
            return pref
                .replace(/([mMyYdD]+)/ig, '%$1');
        },

        /**
         * Given a Sugar user time format preference
         * returns a D3 time formatting specifier
         *
         * @param {string} pref  A string encoding in the form 'h:ia'
         * where 'h' indicates 12 hour clock and 'H' indicates 24 hour clock
         * and 'i' with a colon as separator
         * @return {string}  A time format pattern string in the form of '%-I:%M'
         * @private
         */
        _timeFormat: function(pref) {
            if (!pref) {
                return '%-I:%M:%S';
            }
            return pref
                .replace('h', 'I')
                .replace('i', 'M')
                .replace(/[aA\s]+/, '')
                .replace(/([HIM]+)/ig, '%$1');
        },

        /**
         * Given a Sugar user time format preference
         * returns a D3 time period formatting specifier
         *
         * @param {string} pref  A string encoding in the form 'h:ia'
         * where the final character is expected to be 'a', 'A' or empty
         * with an optional leading space,
         * @return {Array}  A nominal array of time period options in the form ['am', 'pm']
         * @private
         */
        _timePeriods: function(pref) {
            if (!pref) {
                return ['AM', 'PM'];
            }
            var period = pref.indexOf(' A') !== -1 ?
                [' AM', ' PM'] :
                pref.indexOf('A') !== -1 ?
                    ['AM', 'PM'] :
                    pref.indexOf(' a') !== -1 ?
                        [' am', ' pm'] :
                        pref.indexOf('a') !== -1 ?
                            ['am', 'pm'] :
                            ['', ''];
            return period;
        },

        /**
         * Given the name of a Sugar language pack set of date strings
         * returns an array of date name strings for D3
         *
         * @param {string} listLabel  The name of a list that references an
         * object as structural array in the form {0: '', 1: 'Monday', ...}
         * with integer keys for each date string and a zero padding element
         * @return {Array}  A nominal array of date name strings in the form ['Monday', ...]
         * with the zero padding element removed
         * @private
         */
        _dateStringArray: function(listLabel) {
            return _.filter(_.values(this.translateListStrings(listLabel)));
        },
    };
})(jQuery);

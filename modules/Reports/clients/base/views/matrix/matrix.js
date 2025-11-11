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
 * @class View.Views.Reports.MatrixView
 * @alias SUGAR.App.view.views.MatrixView
 */
 ({
    plugins: ['ReportsPanel'],

    /**
     * Initialize helper data
     */
    _initProperties: function() {
        this._matrixTypeMapping = {
            '2x2': {
                template: 'two-by-two',
                builder: '_buildTwoByTwoMatrix'
            },
            '2x1': {
                template: 'two-by-one',
                builder: '_buildTwoByOneMatrix'
            },
            '1x2': {
                template: 'one-by-two',
                builder: '_buildOneByTwoMatrix'
            },
        };

        this._matrixType = 'two-by-two';
        this._matrixTable = [];
        this._hasData = false;

        this.RECORD_NOT_FOUND_ERROR_CODE = 404;
        this.SERVER_ERROR_CODES = [500, 502, 503, 504];
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'runtime:filters:updated', _.bind(this._loadReportData, this, undefined));
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.context.get('previewMode')) {
            this.context.trigger('report:data:table:loaded', false, 'table');
        }
    },

    /**
     * Setup preview widget view
     */
    _setupPreviewReportPanel: function() {
        this._buildMatrix(this.context.get('previewData').tableData);

        this.context.trigger('report:data:table:loaded', false, 'table');
    },

    /**
     * Fetch the data to be rendered in list
     *
     */
    _loadReportData: function() {
        const url = app.api.buildURL('Reports', 'retrieveSavedReportsRecords');

        let reportModel = this.context.get('model') || this.get('model');

        if (!reportModel.get('report_type') && this.layout) {
            reportModel = this.layout.model;
        }

        const reportId = reportModel.get('id') || reportModel.get('report_id');
        const reportType = reportModel.get('report_type');
        const intelligent = reportModel.get('intelligent');

        let requestMeta = {
            record: reportId,
            use_saved_filters: true,
            intelligent,
            reportType,
        };

        const listOptions = reportModel.get('list');
        const lastStateKey = reportModel.get('lastStateKey');

        let customMeta = this._getCustomReportMeta(listOptions, lastStateKey);

        requestMeta = _.extend(requestMeta, customMeta);

        app.api.call('create', url, requestMeta, {
            success: _.bind(this._buildMatrix, this),
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

        this._matrixTable = [];
        this._hasData = false;

        this.render();

        this.context.trigger('report:data:table:loaded', false, 'table');

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
        if (!reportModel.get('list')) {
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
     * Build Matrix Table
     *
     * @param {Object} reportData
     */
    _buildMatrix: function(reportData) {
        if (_.isEmpty(reportData.layoutType)) {
            this.context.trigger('report:build:data:table', 'summary');

            return;
        }

        if (this.disposed ||
            _.isEmpty(reportData) ||
            (!_.isEmpty(reportData) && _.isEmpty(reportData.data))) {
            this._matrixTable = [];
            this._hasData = false;

            this.render();

            if (this.context) {
                this.context.trigger('report:data:table:loaded', false, 'table');
            }

            return;
        }

        this._hasData = true;

        const matrixTypeData = this._matrixTypeMapping[reportData.layoutType];

        this._matrixType = matrixTypeData.template;

        if (_.isFunction(this[matrixTypeData.builder])) {
            this[matrixTypeData.builder](reportData);
        }

        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this._failedLoadReportData({});

            return;
        }

        this.render();

        this.context.trigger('report:data:table:loaded', false, 'table');
    },

    /**
     * Build the 1 by 2 matrix report table
     *
     * @param {Object} reportData
     */
    _buildOneByTwoMatrix: function(reportData) {
        const headers = reportData.header;
        const columnsIdx = 1;

        let groupColumns = _.union(
            [_.first(_.first(headers))],
            headers[columnsIdx],
            ['Total']
        );

        const lastGroupColumns = _.union(_.last(reportData.header), ['Total']);
        const lastGroupColumnsNb = lastGroupColumns.length;
        const legendNb = reportData.legend.length;

        this._buildOneByTwoHeader(reportData, lastGroupColumns, lastGroupColumnsNb);

        _.each(reportData.data, function(group) {
            this._buildOneByTwoBody(groupColumns, legendNb, group, lastGroupColumnsNb, lastGroupColumns, true);
        }, this);

        this._buildOneByTwoBody(
            groupColumns,
            legendNb,
            reportData.grandTotalBottomFormatted,
            lastGroupColumnsNb,
            lastGroupColumns,
            false,
            true,
            true
        );
    },

    /**
     * Build Table header
     *
     * @param {Object} reportData
     * @param {Object} lastGroupColumns
     * @param {number} lastGroupColumnsNb
     */
    _buildOneByTwoHeader: function(reportData, lastGroupColumns, lastGroupColumnsNb) {
        const secondGroupIdx = 1;
        const thirdGroupIdx = 2;

        const firstHeader = _.first(reportData.header);
        const secondHeader = reportData.header[secondGroupIdx];
        const thirdHeader = reportData.header[thirdGroupIdx];

        // add the first header
        this._matrixTable = [
            [
                {
                    value: _.first(firstHeader),
                    rowspan: 4,
                    colspan: 1,
                    bold: true,
                },
                {
                    value: firstHeader[secondGroupIdx],
                    rowspan: 1,
                    colspan: secondHeader.length * lastGroupColumnsNb,
                    bold: true,
                },
                {
                    value: _.last(firstHeader),
                    rowspan: 4,
                    colspan: 1,
                    bold: true,
                    grandTotal: true,
                },
            ],
        ];

        this._matrixTable.push([]);

        // add the second header
        _.each(secondHeader, function(columnName) {
            this._matrixTable[this._matrixTable.length - 1].push({
                value: columnName,
                colspan: lastGroupColumnsNb,
                rowspan: 1,
                bold: true,
            });
        }, this);

        this._matrixTable.push([]);

        // add the third header
        for (let headerIdx = 0; headerIdx < secondHeader.length; headerIdx++) {
            const columnName = _.first(thirdHeader);

            this._matrixTable[this._matrixTable.length - 1].push({
                value: columnName,
                colspan: lastGroupColumnsNb,
                rowspan: 1,
                bold: true,
            });
        }

        this._matrixTable.push([]);

        // add the final header
        for (let headerIdx = 0; headerIdx < secondHeader.length; headerIdx++) {
            _.each(lastGroupColumns, function(columnName) {
                this._matrixTable[this._matrixTable.length - 1].push({
                    value: columnName,
                    colspan: 1,
                    rowspan: 1,
                    bold: true,
                    grandTotal: columnName === 'Total',
                });
            }, this);
        }
    },

    /**
     * Build Table body
     *
     * @param {Object} groupColumns
     * @param {number} legendCount
     * @param {Object} group
     * @param {number} lastGroupColumnsNb
     * @param {Object} lastGroupColumns
     * @param {boolean} skipTotal
     * @param {boolean} bold
     * @param {boolean} grandTotal
     */
    _buildOneByTwoBody: function(
        groupColumns,
        legendCount,
        group,
        lastGroupColumnsNb,
        lastGroupColumns,
        skipTotal,
        bold,
        grandTotal) {
        const totalKey = 'Total';

        let groupKeys = groupColumns;

        // go through all the data as many times as the legend's size
        for (let displayColumnIdx = 0; displayColumnIdx < legendCount; displayColumnIdx++) {
            _.each(groupKeys, function(groupKey) {
                const secondGroup = group[groupKey];

                // check if this is the header cell
                if (_.isString(secondGroup)) {
                    const cellValue = displayColumnIdx ? [] : [{
                        value: secondGroup,
                        colspan: 1,
                        rowspan: legendCount,
                        bold: true,
                        grandTotal,
                    }];

                    this._matrixTable.push(cellValue);
                } else {
                    // if not we have to go through all of the columns
                    if (groupKey === totalKey && !skipTotal) {
                        return;
                    }

                    this._buildOneByTwoGrid(
                        secondGroup,
                        lastGroupColumns,
                        lastGroupColumnsNb,
                        groupKey,
                        skipTotal,
                        displayColumnIdx,
                        bold,
                        grandTotal
                    );
                }
            }, this);

            if (!skipTotal) {
                groupByDataKeys = _.keys(group.Total);
                groupByDataValue = group.Total[groupByDataKeys[displayColumnIdx]];

                this._matrixTable[this._matrixTable.length - 1].push({
                    value: groupByDataValue,
                    colspan: 1,
                    rowspan: 1,
                    bold: true,
                    grandTotal,
                });
            }
        }
    },

    /**
     * Build Table Grid
     *
     * @param {Object} secondGroup
     * @param {Object} lastGroupColumns
     * @param {number} lastGroupColumnsNb
     * @param {string} groupKey
     * @param {boolean} skipTotal
     * @param {boolean} bold
     * @param {boolean} grandTotal
     */
    _buildOneByTwoGrid: function(
        secondGroup,
        lastGroupColumns,
        lastGroupColumnsNb,
        groupKey,
        skipTotal,
        displayColumnIdx,
        bold,
        grandTotal
    ) {
        const totalKey = 'Total';

        if (_.isUndefined(secondGroup)) {
            secondGroup = {};
        }

        for (let lastGroupIdx = 0; lastGroupIdx < lastGroupColumnsNb; lastGroupIdx++) {
            const lastGroupColumnName = lastGroupColumns[lastGroupIdx];
            const lastGroup = secondGroup[lastGroupColumnName];

            if (groupKey === totalKey && lastGroupColumnName !== totalKey && skipTotal) {
                continue;
            }

            let groupByDataKeys = [];
            let groupByDataValue = '';

            if (!_.isUndefined(lastGroup)) {
                groupByDataKeys = _.keys(lastGroup);
                groupByDataValue = lastGroup[groupByDataKeys[displayColumnIdx]];
            }

            this._matrixTable[this._matrixTable.length - 1].push({
                value: groupByDataValue,
                colspan: 1,
                rowspan: 1,
                bold: bold ? bold : lastGroupColumnName === totalKey,
                grandTotal,
            });
        }
    },

    /**
     * Build the 2 by 1 matrix report table
     *
     * @param {Object} reportData
     */
    _buildTwoByOneMatrix: function(reportData) {
        const headers = reportData.header;
        const columnsIdx = 1;

        let groupColumns = _.union(
            [_.first(_.first(headers))],
            headers[columnsIdx],
            ['Total']
        );

        const groupByColumnsNb = groupColumns.length;
        const lastGroupColumns = _.union(_.last(reportData.header), ['Total']);
        const lastGroupByColumnsNb = lastGroupColumns.length;
        const legendCount = reportData.legend.length;

        this._buildTwoByOneHeader(reportData);

        _.each(reportData.data, function(group, groupName) {
            this._buildTwoByOneBody(
                group,
                groupName,
                lastGroupColumns,
                lastGroupByColumnsNb,
                groupColumns,
                groupByColumnsNb,
                legendCount
            );
        }, this);

        // build grand total
        let grandTotalKeys = _.keys(reportData.grandTotalBottomFormatted);
        const lastEntry = _.last(grandTotalKeys);

        grandTotalKeys[grandTotalKeys.length - 1] = _.first(grandTotalKeys);
        grandTotalKeys[0] = lastEntry;

        for (let displayColumnIdx = 0; displayColumnIdx < legendCount; displayColumnIdx++) {
            this._buildTwoByOneGrandTotal(reportData, grandTotalKeys, displayColumnIdx, legendCount);
        }
    },

    /**
     * Build Matrix Table header
     *
     * @param {Object} reportData
     */
    _buildTwoByOneHeader: function(reportData) {
        const firstHeader = _.first(reportData.header);
        const lastHeader = _.last(reportData.header);
        const secondGroupIdx = 1;
        const thirdGroupIdx = 2;

        this._matrixTable = [
            [
                {
                    value: _.first(firstHeader),
                    rowspan: 2,
                    colspan: 1,
                    bold: true,
                },
                {
                    value: firstHeader[secondGroupIdx],
                    rowspan: 2,
                    colspan: 1,
                    bold: true,
                },
                {
                    value: firstHeader[thirdGroupIdx],
                    rowspan: 1,
                    colspan: lastHeader.length,
                    bold: true,
                },
                {
                    value: _.last(firstHeader),
                    rowspan: 2,
                    colspan: 1,
                    bold: true,
                    grandTotal: true,
                },
            ],
        ];

        this._matrixTable.push([]);

        _.each(lastHeader, function(columnName) {
            this._matrixTable[this._matrixTable.length - 1].push({
                value: columnName,
                colspan: 1,
                rowspan: 1,
                bold: true,
            });
        }, this);
    },

    /**
     * Build Table Body
     *
     * @param {Object} data
     * @param {number} groupByColumnsNb
     * @param {Object} lastGroupColumns
     * @param {number} lastGroupByColumnsNb
     * @param {number} groupByColumnsNb
     * @param {number} legendCount
     */
    _buildTwoByOneBody: function(
        data,
        groupByColumnsNb,
        lastGroupColumns,
        lastGroupByColumnsNb,
        groupColumns,
        groupByColumnsNb,
        legendCount
    ) {
        let isSameRow = true;

        _.each(groupColumns, function(groupName) {
            const grandTotal = groupName === 'Total';
            const group = data[groupName];

            if (_.isString(group)) {
                const topRowSpan = (groupByColumnsNb - 1) * legendCount;

                this._matrixTable.push([{
                    value: group,
                    colspan: 1,
                    rowspan: topRowSpan,
                    bold: true,
                }]);

                isSameRow = true;
            } else {
                if (isSameRow) {
                    isSameRow = false;

                    this._matrixTable[this._matrixTable.length - 1].push({
                        value: groupName,
                        colspan: 1,
                        rowspan: legendCount,
                        bold: true,
                        grandTotal,
                    });
                } else {
                    this._matrixTable.push([{
                        value: groupName,
                        colspan: 1,
                        rowspan: legendCount,
                        bold: true,
                        grandTotal,
                    }]);
                }

                this._buildTwoByOneGrid(group, lastGroupColumns, legendCount, lastGroupByColumnsNb, grandTotal);
            }
        }, this);
    },

    /**
     * Build Table Grid
     *
     * @param {Object} group
     * @param {Object} lastGroupColumns
     * @param {number} legendCount
     * @param {number} lastGroupByColumnsNb
     * @param {boolean} bold
     */
    _buildTwoByOneGrid: function(group, lastGroupColumns, legendCount, lastGroupByColumnsNb, bold) {
        if (_.isUndefined(group)) {
            group = {};
        }

        for (let displayColumnIdx = 0; displayColumnIdx < legendCount; displayColumnIdx++) {
            if (displayColumnIdx) {
                this._matrixTable.push([]);
            }

            for (let lastGroupIdx = 0; lastGroupIdx < lastGroupByColumnsNb; lastGroupIdx++) {
                const lastGroupColumnName = lastGroupColumns[lastGroupIdx];
                const lastGroupData = group[lastGroupColumnName];

                let groupByDataKeys = [];
                let groupByDataValue = '';

                if (!_.isUndefined(lastGroupData)) {
                    groupByDataKeys = _.keys(lastGroupData);
                    groupByDataValue = lastGroupData[groupByDataKeys[displayColumnIdx]];
                }

                this._matrixTable[this._matrixTable.length - 1].push({
                    value: groupByDataValue,
                    colspan: 1,
                    rowspan: 1,
                    bold: bold ? bold : lastGroupColumnName === 'Total',
                });
            }
        }
    },

    /**
     * Build Table Grand Total
     *
     * @param {Object} reportData
     * @param {Object} grandTotalKeys
     * @param {number} displayColumnIdx
     * @param {number} legendCount
     */
    _buildTwoByOneGrandTotal: function(reportData, grandTotalKeys, displayColumnIdx, legendCount) {
        _.each(grandTotalKeys, function(columnName) {
            const group = reportData.grandTotalBottomFormatted[columnName];

            if (_.isString(group)) {
                const cellValue = displayColumnIdx ? [] : [{
                    value: group,
                    colspan: 2,
                    rowspan: legendCount,
                    bold: true,
                    grandTotal: true,
                }];

                this._matrixTable.push(cellValue);
            } else {
                let groupByDataKeys = [];
                let groupByDataValue = 0;

                if (!_.isUndefined(group)) {
                    groupByDataKeys = _.keys(group);
                    groupByDataValue = group[groupByDataKeys[displayColumnIdx]];
                }

                if (_.isObject(groupByDataValue)) {
                    const groupKeyValue = _.chain(groupByDataValue).keys().first().value();
                    groupByDataValue = groupByDataValue[groupKeyValue];
                }

                this._matrixTable[this._matrixTable.length - 1].push({
                    value: groupByDataValue,
                    colspan: 1,
                    rowspan: 1,
                    bold: true,
                    grandTotal: true,
                });
            }
        }, this);
    },

    /**
     * Build a two group defs type of matrix report
     *
     * @param {Object} reportData
     */
    _buildTwoByTwoMatrix: function(reportData) {
        const secondGroupIdx = 1;
        const thirdGroupIdx = 2;
        const firstGroupByRowsNb = 2;

        let completeHeader = app.utils.deepCopy(_.first(reportData.header));
        completeHeader.push('Total');

        const secondGroupColumns = reportData.header[secondGroupIdx];
        const secondGroupByColumnsNb = secondGroupColumns.length;
        const grandTotalRowsNb = 2;

        const legendCount = reportData.legend.length;

        // build header
        this._matrixTable = [
            [
                {
                    value: _.first(_.first(reportData.header)),
                    rowspan: firstGroupByRowsNb,
                    colspan: 1,
                    bold: true,
                },
                {
                    value: _.first(reportData.header)[secondGroupIdx],
                    rowspan: 1,
                    colspan: secondGroupByColumnsNb,
                    bold: true,
                },
                {
                    value: _.first(reportData.header)[thirdGroupIdx],
                    rowspan: grandTotalRowsNb,
                    colspan: 1,
                    bold: true,
                    grandTotal: true,
                },
            ],
            _.map(secondGroupColumns, function build(value) {
                return {
                    value: value,
                    rowspan: 1,
                    colspan: 1,
                    bold: true,
                };
            })
        ];

        // build body
        const headers = reportData.header;
        const columnsIdx = 1;

        let groupColumns = _.union(
            [_.first(_.first(headers))],
            headers[columnsIdx],
            ['Total']
        );

        if (_.has(reportData, 'groupColumns') && _.isArray(reportData.groupColumns)) {
            groupColumns = reportData.groupColumns;
        }

        _.each(reportData.data, _.bind(this._processMatrixDataGroup, this, groupColumns, legendCount));
    },

    /**
     * Process and add cells to matrix
     * @param {Object} columnsNames
     * @param {number} legendCount
     * @param {Object} groupByData
     */
    _processMatrixDataGroup: function(columnsNames, legendCount, groupByData) {
        for (let displayColumnIdx = 0; displayColumnIdx < legendCount; displayColumnIdx++) {
            const grandTotal = groupByData[_.first(columnsNames)] === 'Grand Total';

            _.each(columnsNames, function(columnName) {
                const bold = grandTotal ? grandTotal : columnName === 'Total';

                this._addCellToMatrix(groupByData[columnName], legendCount, displayColumnIdx, bold, grandTotal);
            }, this);
        }
    },

    /**
     * Adding a cell to the row data
     *
     * @param {Object} groupByData
     * @param {number} legendCount
     * @param {number} displayColumnIdx
     * @param {boolean} bold
     * @param {boolean} grandTotal
     */
    _addCellToMatrix: function(groupByData, legendCount, displayColumnIdx, bold, grandTotal) {
        if (_.isString(groupByData)) {
            const cellValue = displayColumnIdx ? [] : [{
                value: groupByData,
                colspan: 1,
                rowspan: legendCount,
                bold: true,
                grandTotal: grandTotal,
            }];

            this._matrixTable.push(cellValue);
        } else {
            if (_.isUndefined(groupByData)) {
                this._matrixTable[this._matrixTable.length - 1].push({
                    value: '',
                    colspan: 1,
                    rowspan: 1,
                    bold,
                    grandTotal,
                });
            } else {
                const groupByDataKeys = _.keys(groupByData);
                const groupByDataValue = groupByData[groupByDataKeys[displayColumnIdx]];

                this._matrixTable[this._matrixTable.length - 1].push({
                    value: groupByDataValue,
                    colspan: 1,
                    rowspan: 1,
                    bold,
                    grandTotal,
                });
            }
        }
    },
})

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
 * @class View.Views.Reports.RowsColumnsView
 * @alias SUGAR.App.view.views.ReportsRowsColumnsView
 * @extends View.Views.Base.RecordlistView
 */
({
    extendsFrom: 'RecordlistView',

    /**
     * Used in report complexity calculations
     */
    complexities: {
        'low': 0,
        'medium': 1,
        'high': 2,
    },

    pagination: true,
    loading: true,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.filter(this.plugins, function(pluginName) {
            if (!_.isUndefined(options.layout) && options.layout.options.useCustomReportDef) {
                return (pluginName !== 'ReorderableColumns' && pluginName !== 'ResizableColumns');
            }

            return (pluginName !== 'ReorderableColumns');
        });

        if (!_.contains(this.plugins, 'ReportsPanel')) {
            this.plugins.push('ReportsPanel');
        }

        this._super('initialize', [options]);

        this.context.set('reportComplexities', this.complexities);

        /**
         * Init data is being used on the parent controller
         */
        this._setUserLastState();
    },

    /**
     * Initialize helper data
     */
    _initProperties: function() {
        this.model = app.data.createBean(); // reset current model
        this.collection = app.data.createBeanCollection(); // reset collection
        this.context.set('mass_collection', app.data.createBeanCollection()); //reset mass collection
        this.useCustomReportDef = false;
        this.orderByKeys = ['orderBy'];

        if (this.layout && this.layout.options) {
            this.useCustomReportDef = this.layout.options.useCustomReportDef;
        }

        if (this.useCustomReportDef) {
            this._initCustomProperties();
        }

        if (!this.limit) {
            this.limit = 50;
        }

        this.RECORD_NOT_FOUND_ERROR_CODE = 404;
        this.SERVER_ERROR_CODES = [500, 502, 503, 504];

        this.context.set('rebuildData', true);

        this._isDetail = !this.context.get('previewMode');
        this.leftColumns = [];
        this.rightColumns = [{isColumnDropdown: true}];
        if (_.isUndefined(this._fields)) {
            this._fields = {};
        }
        this._fields.all = [];

        this.isReportComplex = false;
    },

    /**
     * Init custom properties
     */
    _initCustomProperties: function() {
        const pageListOptions = this.layout.model.get('list');

        this.reportType = this.layout.model.get('report_type');

        this.lastStateKey = this.layout.model.get('lastStateKey');

        if (_.has(pageListOptions, 'rowsPerPage')) {
            this.limit = pageListOptions.rowsPerPage;
        }

        if (pageListOptions) {
            this.showFooterDetails = pageListOptions.showCount;
        }

        if (_.has(pageListOptions, 'orderBy') && pageListOptions.orderBy) {
            //this is coming from the user state config
            this.customConfiguredOrderBy = pageListOptions.orderBy;
        }

        const dataLoaded = false;

        this._initializeCustomOrderBy(dataLoaded);

        const userLastState = this.layout.model.get('userLastState');

        if (userLastState && _.has(userLastState, 'defaultView')) {
            this.lastSelectedView = userLastState.defaultView;
        } else {
            this.lastSelectedView = this.layout.model.get('defaultSelectView');
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        const noOptions = undefined;

        this.listenTo(this.context, 'rows-columns:load:collection', this._loadReportData, this);
        this.listenTo(this.context, 'runtime:filters:updated', _.bind(this._loadReportData, this, noOptions));
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this._fields.visible = [];

            this._failedLoadReportData({});

            this._super('_render');

            return;
        }

        this._processHeader();

        if (_.isUndefined(this.data)) {
            this._fields.visible = [];
        } else {
            this._fields.visible = this.data.header;
        }

        const ctxRebuildData = this.context.get('rebuildData');
        if (ctxRebuildData === true && !_.isUndefined(this.data)) {
            this.buildCollection(this.data);
        }

        this._super('_render');

        if (!_.isUndefined(this.data) && this.pagination) {
            const pagination = this.layout.getComponent('report-table-pagination');
            pagination.collection = this.collection;
            pagination.totalCount = pagination.collection.total;
            pagination.pagesCount = this.data.totalPages;
        }

        this._setFooter();
    },

    /**
     * Initialize the orderBy object
     *
     * TODO: - try and get order_by from this.data.order_by
     */
    _initializeOrderBy: function() {
        //we get the last state
        if (this.useCustomReportDef) {
            this._initializeCustomOrderBy(true);

            return;
        }

        //we get the last state for report
        this.orderBy = app.user.lastState.get(this.orderByLastStateKey);
        const dataOrderBy = _.first(this.data.orderBy);

        if (_.isUndefined(this.orderBy) && dataOrderBy) {
            this.orderBy = {
                field: dataOrderBy.name,
                direction: dataOrderBy.sort_dir === 'a' ? 'asc' : 'desc',
                table_key: '',
                column_function: '',
                group_function: '',
            };
        }
    },

    /**
     * Initialize the orderBy object
     *
     * @param {boolean} dataLoaded check if the data is loaded from server
     */
    _initializeCustomOrderBy: function(dataLoaded) {
        const lastState = app.user.lastState.get(this.lastStateKey);
        const hasLastStateForOrderBy = _.has(lastState, 'orderBy') && lastState.orderBy;
        const lastOrderBy = hasLastStateForOrderBy ? lastState.orderBy : false;

        let orderBy;

        if (lastOrderBy) {
            orderBy = this._createOrderByMeta(lastOrderBy, dataLoaded);
        } else if (this.customConfiguredOrderBy && this.customConfiguredOrderBy.length === 1) {
            orderBy = this._createOrderByMeta(this.customConfiguredOrderBy, dataLoaded);
        }

        if (orderBy) {
            this.customOrderBy = orderBy;
            this.orderBy = orderBy;
        }
    },

    /**
     * Create orderBy object
     *
     * @param {Array} ordersMeta
     * @param {boolean} dataLoaded check if the data is loaded from server
     *
     * @return {Object}
     */
    _createOrderByMeta: function(ordersMeta, dataLoaded) {
        const tableKey = 'table_key';
        const sortDirKey = 'sort_dir';
        const columnFunctionKey = 'column_function';
        const groupFunctionKey = 'group_function';

        let orderByMeta = _.first(ordersMeta);

        let orderBy = {
            column_function: '',
            group_function: '',
            table_key: '',
        };

        const hasName = (_.has(orderByMeta, 'name') && orderByMeta.name) ||
            (_.has(orderByMeta, 'rname') && orderByMeta.rname);
        const hasField = _.has(orderByMeta, 'field') && orderByMeta.field;

        if (dataLoaded && (!hasName) && (!hasField)) {
            orderBy.field = _.first(this.data.header).name;
        } else if (dataLoaded && hasName) {
            orderBy.field = orderByMeta.field || orderByMeta.name;
        } else {
            orderBy.field = !hasName ? orderByMeta.field : orderByMeta.name || orderByMeta.rname;
        }

        if (_.has(orderByMeta, sortDirKey)) {
            const curentSortDir = orderByMeta.sort_dir;

            if (curentSortDir !== 'asc' && curentSortDir !== 'desc') {
                orderBy.direction = curentSortDir === 'a' ? 'asc' : 'desc';
            } else {
                orderBy.direction = curentSortDir;
            }

        } else if (_.has(orderByMeta, 'direction')) {
            orderBy.direction = orderByMeta.direction;
        } else {
            orderBy.direction = 'asc';
        }

        if (_.has(orderByMeta, tableKey)) {
            orderBy[tableKey] = orderByMeta[tableKey];
        }

        if (_.has(orderByMeta, columnFunctionKey)) {
            orderBy[columnFunctionKey] = orderByMeta[columnFunctionKey];
        }

        if (_.has(orderByMeta, groupFunctionKey)) {
            orderBy[groupFunctionKey] = orderByMeta[groupFunctionKey];
        }

        if (_.has(orderByMeta, 'rname')) {
            orderBy.rname = orderByMeta.rname;
        }

        return orderBy;
    },

    /**
     * Process table header
     */
    _processHeader: function() {
        if (_.isUndefined(this.data)) {
            return;
        }

        for (let index in this.data.header) {
            let headerItem = this.data.header[index];
            const headerName = headerItem.name;

            if (['relate', 'name', 'fullname', 'username'].includes(headerItem.type)) {
                headerItem.link = !this.context.get('previewMode');
            }

            if (['datetime'].includes(headerItem.type)) {
                headerItem.type = 'datetimecombo';
            }

            headerItem.module = headerItem.ext2 || headerItem.module;
            headerItem.rname = !_.isUndefined(headerItem.rname) ? headerItem.rname : _.clone(headerItem.name);

            const headerCol = _.filter(this.data.header, function(item) {
                return item.name === headerName;
            });

            if (headerCol.length > 1) {
                const orderBy = _.first(this.data.orderBy);
                let columnKey = headerItem.column_key;

                if (!_.isUndefined(orderBy) &&
                    !_.isUndefined(columnKey) &&
                    orderBy.name === headerName &&
                    orderBy.table_key === columnKey.substr(0, columnKey.lastIndexOf(':'))) {
                    orderBy.field += index;
                }
                headerItem.name += index;
                this.data.header[index] = headerItem;
            }
        }
    },

    /**
     * Build collection
     *
     * @param {Array} data
     */
    buildCollection: function(data) {
        const records = data.records;
        const header = data.header || this._fields.visible;

        this._initCollection();

        /**
         * Iterate records and try to create models for each value
         *
         * later these models will be evaluated separately for each table cell
         */
        for (let index in records) {
            const record = records[index];

            let model = app.data.createBean();
            for (let recordIndex in record) {
                const field = record[recordIndex];
                const fieldMeta = header[recordIndex];

                if (_.has(fieldMeta, 'group_function') && _.has(fieldMeta, 'type') &&
                    _.has(field, 'type') && (fieldMeta.type !== field.type)) {
                    fieldMeta.type = field.type;
                    fieldMeta.field_type = field.type;
                }

                if (['date', 'datetime', 'datetimecombo'].includes(field.type) &&
                    !_.isEmpty(field.value) && _.has(fieldMeta ,'qualifier')) {
                    fieldMeta.type = 'text';
                }

                let modelField = this._buildModel(field, fieldMeta);
                model.set(fieldMeta.name, modelField);
            }
            this.collection.models.push(model);
        }
        this.collection.length = this.collection.models.length;
    },

    /**
     * Based on the number of records we have to figured out how we will display the report
     *
     * @param {number} recordsNo
     * @param {number} fieldsNo
     * @return {boolean}
     */
    _getReportComplexity: function(recordsNo, fieldsNo) {
        const reportsComplexityKey = 'reports_complexity_display';
        const reportsComplexity = app.config[reportsComplexityKey];

        if (_.isUndefined(reportsComplexity) || _.isEmpty(reportsComplexity)) {
            return this.complexities.low;
        }

        let complexity = 0;

        if (_.isNumber(recordsNo) && _.isNumber(fieldsNo)) {
            complexity = recordsNo * fieldsNo;
        }

        if (complexity < reportsComplexity.simplified) {
            this.context.set('reportComplexity', this.complexities.low);
            return this.complexities.low;
        }

        if (complexity > reportsComplexity.export) {
            this.context.set('reportComplexity', this.complexities.high);
            return this.complexities.high;
        }

        this.context.set('reportComplexity', this.complexities.medium);
        return this.complexities.medium;
    },

    /**
     * Set some init data on the collection
     */
    _initCollection: function() {
        this.collection.setOption('limit', this.limit);
        this.collection.models = [];
        this.collection.dataFetched = true;
        this.collection.offset = 0;
        this.collection.next_offset = this.data.nextOffset || 0;
        this.collection.total = this.data.totalCount;
    },

    /**
     * Create a model
     *
     * @param {object} field
     * @param {object} fieldMeta
     * @returns BeanModel
     */
    _buildModel: function(field, fieldMeta) {
        let modelField = app.data.createBean();
        modelField.fields = [fieldMeta];

        /**
         * set the values of each cell model
         */
        modelField.set(fieldMeta.name, field.value);
        modelField.set(field.name, field.value);
        modelField.set(field.rname, field.value);
        if (fieldMeta.type !== 'id') {
            modelField.set('id', field.id);
        }
        modelField.set('_module', field.module); //make the focus drawer work
        modelField.set(field.id_name, field.id);
        modelField.module = fieldMeta.module || field.module;

        if (fieldMeta.type === 'image') {
            modelField.value = field.value;
            modelField.id = field.parentRecordId;
        }

        return modelField;
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function() {
        this.tplName = 'recordlist';
        this.template = app.template.getView(this.tplName);
    },

    /**
     * Set data pagination
     *
     * @param {Array} data
     */
    setData: function(data) {
        this.data = data;
    },

    /**
     * Set user last state
     */
    _setUserLastState: function() {
        const moduleReportId = this.module + ':' + this.context.get('modelId');
        this._allListViewsFieldListKey = app.user.lastState.buildKey('field-list', 'list-views', moduleReportId);
        this._thisListViewFieldSizesKey = app.user.lastState.buildKey('width-fields', 'record-list', moduleReportId);
        this.orderByLastStateKey = app.user.lastState.buildKey('order-by', 'record-list', moduleReportId);
    },
    /**
     * Alter meta in order to be renderable by this controller
     */
    _setHeaderFields: function() {
        this.data = this.context.get('data');
        this._fields.visible = this.data.header;

        // disable focus drawer buttons
        _.each(this._fields.visible, function disableFocusDrawer(item) {
            item.disableFocusDrawerRecordSwitching = true;
        });

        const panelHeaderIndex = this._getPanelIndexByName('panel_header');
        this.meta.panels[panelHeaderIndex].fields  = this._fields.visible;
    },

    /**
     * Get the panel by name
     *
     * @param {string} name
     *
     * @return {number}
     */
    _getPanelIndexByName: function(name) {
        return _.findIndex(this.meta.panels, function goThroughPanels(panelDef) {
            return panelDef.name === name;
        });
    },

    /**
     * @inheritdoc
     */
    setOrderBy: function(event) {
        if (this.context.get('previewMode')) {
            app.alert.show('report-preview-limitation', {
                level: 'warning',
                messages: app.lang.get('LBL_REPORTS_PREVIEW_LIMITATION'),
                autoClose: true
            });

            return;
        }

        const currentEvt = event.currentTarget.dataset;

        if (this.useCustomReportDef && !_.isUndefined(this.customOrderBy)) {
            this.customOrderBy.table_key = currentEvt.tablekey;
            this.customOrderBy.rname = currentEvt.realname;
            this.customOrderBy.column_function = currentEvt.columnfct;
            this.customOrderBy.group_function = currentEvt.groupfct;

        } else if (!_.isUndefined(this.orderBy)) {
            this.orderBy.table_key = currentEvt.tablekey;
            this.orderBy.rname = currentEvt.realname;
            this.orderBy.column_function = currentEvt.columnfct;
            this.orderBy.group_function = currentEvt.groupfct;
        }

        this.context.set('rebuildData', true);
        if (_.isUndefined(this.orderBy)) {
            this.orderBy = {
                table_key: currentEvt && _.has(currentEvt, 'tablekey') ? currentEvt.tablekey : '',
                column_function: currentEvt && _.has(currentEvt, 'columnfct') ? currentEvt.columnfct : '',
                group_function: currentEvt && _.has(currentEvt, 'groupfct') ? currentEvt.groupfct : '',
            };

            if (currentEvt && _.has(currentEvt, 'realname') && currentEvt.realname) {
                this.orderBy.rname = currentEvt.realname;
            } else {
                this.orderBy.field = '';
            }
        }

        this._super('setOrderBy', [event]);
    },

    /**
     * @inheritdoc
     */
    _setOrderBy: function(options) {
        if (this.useCustomReportDef) {
            this._setCustomOrderBy();
        } else if (this.orderByLastStateKey) {
            app.user.lastState.set(this.orderByLastStateKey, this.orderBy);
        }
        // refetch the collection
        this.context.resetLoadFlag({recursive: false});
        this.context.set('skipFetch', false);

        this._loadReportData();
    },

    /**
     * Set custom orderBy
     */
    _setCustomOrderBy: function() {
        let lastState = app.user.lastState.get(this.lastStateKey);

        if (lastState) {
            lastState.orderBy = [this.customOrderBy];
        } else {
            lastState = {
                defaultView: this.lastSelectedView,
                orderBy: [this.customOrderBy],
            };
        }

        app.user.lastState.set(this.lastStateKey, lastState);
    },

    /**
     * Setup preview widget view
     */
    _setupPreviewReportPanel: function() {
        this._rebuildData(this.context.get('previewData').tableData);
        this.context.trigger('report:data:table:build:count', this.collection);
        this.context.trigger('report:data:table:loaded', false, 'table');
    },

    /**
     * Fetch the data to be rendered in list
     *
     * @param {Object} options
     */
    _loadReportData: function(options) {
        this.loading = true;
        this.viewingSimplified = false;
        this.context.trigger('report:data:table:loaded', this.loading, 'table');

        var self = this;
        if (_.isUndefined(options)) {
            /**
             * If this is being called from an event,
             * then we will have no options.
             *
             * This means we need to make sure those are being set
             */
            var reportModel = self.context.get('model');

            if (_.isFunction(self.getSortOptions)) {
                var options = self.getSortOptions(this.collection);
            }

            if (_.isUndefined(options)) {
                return;
            }

            this.collection.resetPagination();

            // set the success callback
            options.success = _.bind(this._rebuildData, self);
            options.error = _.bind(this._failedLoadReportData, self);
        } else if (options) {
            var self = options.functionContext;
            var reportModel = this.context.get('model') || this.get('model');
        }

        if (!reportModel.get('report_type') && self.layout) {
            reportModel = self.layout.model;
        }

        const reportId = reportModel.get('id') || reportModel.get('report_id');
        const reportType = reportModel.get('report_type');
        const offset = options.limit * options.page - options.limit;

        const lastStateSort = app.user.lastState.get(self.orderByLastStateKey);
        const sort = lastStateSort ? lastStateSort : null;

        const intelligent = reportModel.get('intelligent');

        const url = app.api.buildURL('Reports', 'retrieveSavedReportsRecords');
        let orderBy = sort ? [{
            name: sort.rname || sort.field,
            table_key: sort.table_key,
            sort_dir: sort.direction === 'asc' ? 'a' : 'd',
            column_function: sort.column_function ? sort.column_function : '',
            group_function: sort.group_function ? sort.group_function : '',
        }] : null;

        if (this.useCustomReportDef) {
            let customOrderBy = this.customOrderBy;

            orderBy = customOrderBy ? [{
                name: customOrderBy.rname || customOrderBy.field,
                table_key: customOrderBy.table_key,
                sort_dir: customOrderBy.direction === 'asc' ? 'a' : 'd',
                column_function: customOrderBy.column_function ? customOrderBy.column_function : '',
                group_function: customOrderBy.group_function ? customOrderBy.group_function : '',
            }] : null;
        }

        let requestMeta = {
            maxNum: this.limit,
            record: reportId,
            use_saved_filters: true,
            intelligent,
            reportType,
            offset,
        };

        _.each(this.orderByKeys, function each(item) {
            requestMeta[item] = orderBy;
        });

        const listOptions = reportModel.get('list');
        const lastStateKey = reportModel.get('lastStateKey');

        const customReportMeta = this._getCustomReportMeta(listOptions, lastStateKey);

        requestMeta = _.extend(requestMeta, customReportMeta);

        app.api.call('create', url, requestMeta, {
            success: _.bind(options.success, self),
            error: _.bind(options.error, self),
        });
    },

    /**
     * Rebuild data
     *
     * @param {array} data
     * @returns
     */
    _rebuildData: function(data) {
        if (this.disposed) {
            return;
        }

        this.context.set('data', data);

        const ctxModel = this.context.get('model');
        ctxModel.dataFetched = true;

        this.context.trigger('report:data:table:loaded', false, 'table');

        this._setHeaderFields();
        this._initializeOrderBy();
        this.layout.render();
        this.layout.trigger('list:sort:fire');

        const visibleEmptyPanel = this._isEmptyPanel(data) ||
                                    !this.layout ||
                                    !app.utils.reports.hasAccessToAllReport(this.layout.model);
        this._toggleEmptyPanel(visibleEmptyPanel);

        this.context.trigger('report:data:table:build:count', this.collection);

        this.layout.$el.removeClass('notLoaded');
    },

    /**
     * Is empty panel
     *
     * @param {Object} data
     * @return {boolean}
     */
    _isEmptyPanel: function(data) {
        return data.records.length === 0;
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

        this._toggleEmptyPanel(true);

        this.showFooterDetails = false;

        this._prepareFooterForCustomView();

        this.context.trigger('report:data:table:build:count', this.collection);
        this.context.trigger('report:data:table:loaded', false, 'table');

        this.layout.$el.removeClass('notLoaded');

        const siblings = this.$el.siblings();

        siblings.find('.report-panel-footer').addClass('hidden');

        if (siblings.hasClass('flex-table-pagination')) {
            siblings.hide();
        }

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
     * Set the data table footer
     */
    _setFooter: function() {
        this._prepareFooterForCustomView();

        const footerBar = this.layout.getComponent('report-panel-footer');
        const grandTotalData = this.data ? this.data.grandTotal : [];

        if (_.isEmpty(footerBar)) {
            return;
        }

        footerBar.$('.title-container > ul').empty();

        _.each(grandTotalData, function goThroughTotals(totalData) {
            let title = totalData.vname;
            let value = totalData.value;
            let moduleName = totalData.module;
            let isTranslatedTitle = totalData.isvNameTranslated;

            if (_.isEmpty(value) || value === '0') {
                return;
            }

            if (!isTranslatedTitle) {
                title = app.lang.get(title, moduleName);
            }

            _.defer(function addGrandTotal() {
                footerBar.$('.title-container > ul').append('<li>' + title + ': ' + value + '</li>');
            });
        }, this);
    },

    /**
     * We have to resize the footer
     */
    _prepareFooterForCustomView: function() {
        if (!this.reportType) {
            return;
        }

        const tablePlaceholder = this.$el.closest('.dataTablePlaceholder');

        if (tablePlaceholder.length < 1) {
            return;
        }

        //we have to replace _ with - to match the css class name
        const reportType = this.reportType.replaceAll(/_/g, '-');

        if (this.showFooterDetails === true) {
            tablePlaceholder.removeClass(`${reportType}-dashle-no-count`);
            tablePlaceholder.addClass(`${reportType}-dashlet-count`);
        } else if (this.showFooterDetails === false) {
            tablePlaceholder.removeClass(`${reportType}-dashlet-count`);
            tablePlaceholder.addClass(`${reportType}-dashlet-no-count`);
        }
    },

    /**
     * Toggle empty panel
     *
     * @param {boolean} show
     */
    _toggleEmptyPanel: function(show) {
        const emptyPanelEl = this.$('.no-data-available');

        if (show) {
            emptyPanelEl.removeClass('hidden');
            this.$('.dataTable').addClass('hidden');
            this.$('.simplified-table').addClass('hidden');
            this.$('.simplified-message').addClass('hidden');
            this.$el.addClass('noBorderBottom');
        } else {
            emptyPanelEl.addClass('hidden');
            this.$('.dataTable').removeClass('hidden');
            this.$('.simplified-message').removeClass('hidden');
            this.$('.simplified-table').removeClass('hidden');
            this.$el.removeClass('noBorderBottom');
        }

        this.context.trigger('report:set-header-visibility', show);
        this.context.trigger('report:set-footer-visibility', show);
    },

    /**
     * Show the simplified version of the table
     *
     * @param {Event} e
     */
    showSimplified: function(e) {
        const dataset = e.target.dataset;
        const shouldRerender = dataset.rerender;
        this.viewingSimplified = true;
        this.startBuildCollection(this.data, shouldRerender);

        this.$('.simplified-message').addClass('hidden');
    },

    /**
     * Handling errors
     *
     * @param {Error} error
     */
    _handleError: function(error) {
        app.alert.show('rows-columns-error', {
            level: 'error',
            title: error.responseText,
        });
    },
})

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
 * @class View.Views.Base.PipelineRecordlistContentView
 * @alias SUGAR.App.view.views.BasePipelineRecordlistContentView
 * @extends View.View
 */
({
    className: 'my-pipeline-content',
    monthsToDisplay: 6,

    events: {
        'click a[name=arrow-left]': 'navigateLeft',
        'click a[name=arrow-right]': 'navigateRight'
    },

    resultsPerPageColumn: 7,

    tileVisualIndicator: {
        'outOfDate': '#bb0e1b', // We can use any CSS accepted value for color, e.g: #CC1E13
        'nearFuture': '#ff9445',
        'inFuture': '#056f37',
        'default': '#145c95'
    },

    //used to force api to return these fields also for a proper coloring.
    tileVisualIndicatorFields: {
        'Opportunities': 'date_closed',
        'Tasks': 'date_due',
        'Leads': 'status',
        'Cases': 'status'
    },

    hasAccessToView: true,

    dataFetched: false,

    totalRecords: 0,

    sideDrawerGap: 0,

    /**
     * Sorting state of content data
     * If field property is empty sorting will be by default
     */
    orderBy: {
        field: '',
        direction: 'desc'
    },

    /**
     * Cached fieldnames to retrieve for tile view
     * This does not include fields from record view
     */
    _fieldsToFetch: [],

    /**
     * Coefficient for determinate the scroll position to start to load the next batch of Records
     */
    nextRequestReady: 0.8,

    /**
     * If this property is true, any other request for getting additional column data will be blocked
     */
    _isFetchingColumn: false,

    /**
     * Scroll position for columns after data fetch
     */
    _columnScrollTop: [],

    /**
     * if enabled on the admin page, displays Count in column header next to Tile Header Field
     */
    isShowCount: false,
    /**
     * if enabled on the admin page, displays Total sum in column header next to Tile Header Field
     */
    isShowTotal: false,

    /**
     * Fetching column data
     */
    _columnDataFetching: [],

    /**
     * Initialize various pipelineConfig variables and set action listeners
     *
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.startDate = app.date().format('YYYY-MM-DD');
        this.pipelineConfig = app.metadata.getModule('VisualPipeline', 'config');
        this.meta = _.extend(
            this.meta || {},
            app.metadata.getView(null, 'pipeline-recordlist-content'),
            app.metadata.getView(this.module, 'pipeline-recordlist-content')
        );
        this.pipelineFilters = [];
        this.hiddenHeaderValues = [];
        this.action = 'list';

        this.orderByLastStateKey = app.user.lastState.buildKey('order-by', 'record-list', this.module);
        this.orderBy = this._initOrderBy();
    },

    /**
     * @private
     * @return {Object}
     */
    _initOrderBy: function() {
        let lastStateOrderBy = app.user.lastState.get(this.orderByLastStateKey) || this.orderBy;

        if (!_.isEmpty(lastStateOrderBy.field) &&
            !app.acl.hasAccess('read', this.module, app.user.get('id'), lastStateOrderBy.field)
        ) {
            lastStateOrderBy = this.orderBy;
        }

        return lastStateOrderBy;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.listenTo(this.context, 'pipeline:recordlist:model:created', this.handleTileViewCreate);
        this.listenTo(this.context, 'pipeline:recordlist:filter:changed', this.buildFilters);
        this.listenTo(this.context, 'button:delete_button:click', this.deleteRecord);
        this.listenTo(this.context, 'side-drawer:before:open', this.handleBeforeSideDrawerOpens);
        this.listenTo(this.context, 'side-drawer:start:close', this.handleSideDrawerCloses);

        this.resizeContainerHandler = _.bind(this.resizeContainer, this);
        window.addEventListener('resize', this.resizeContainerHandler);
    },

    /**
     * Triggers a re-fetch for the model added by the create drawer.
     * @param {Data.Bean} model The model created through the drawer.
     */
    handleTileViewCreate: function(model) {
        this._callWithTileModel(model, 'addModelToCollection');
    },

    /**
     * Handle before open side drawer
     * @param {Object} $el Focus Drawer icon been clicked
     */
    handleBeforeSideDrawerOpens: function($el) {
        let sideDrawer = app.sideDrawer;
        let pipelineContent = $el.closest('.my-pipeline-content');

        if (pipelineContent.length) {
            let $table = $el.closest('table');
            let tableResponsive = $table.closest('.table-responsive');
            if (tableResponsive.length) {
                tableResponsive
                    .removeClass('no-scroll')
                    .css('width', tableResponsive.width());
            }

            this._toggleSideDrawerColumnFocusStyling(true);

            let $td = $el.closest('td');
            let currentColumnIndex = $td.index();
            let currentRow = $el.closest('.tile');
            let currentRowIndex = currentRow.closest('li').index();
            this.setCurrentColumn(currentColumnIndex, currentRowIndex, sideDrawer.openingDuration);
        }
    },

    /**
     * Close side drawer with animation
     */
    handleSideDrawerCloses: function() {
        let sideDrawer = app.sideDrawer;
        let $pipelineContent = this.$('#my-pipeline-content');
        let $tableResponsiveDiv = $pipelineContent.find('.table-responsive');
        let $tr = $pipelineContent.find('table tr');
        let $arrowHolder = $tr.find('.arrowholder');

        // Unset the focused column and tile
        $tr.find('.blue-border').removeClass('blue-border');
        $tr.find('td.current-column').removeClass('current-column');

        // Animate the columns by translating them back to their original positions
        let originalTransition = $tr.css('transition');
        $tr.css('transition', `transform ${sideDrawer.closingDuration}ms ease-in-out`);
        $tr.css('transform', ``);
        $tableResponsiveDiv.css('width', 'auto');

        // After the animation is complete, resize and restore horizontal scrolling
        setTimeout(() => {
            $pipelineContent.css('overflow-x', 'auto');
            $arrowHolder.css('visibility', 'visible');
            $tr.css('transition', originalTransition);

            this.resizeContainer();
            this._toggleSideDrawerColumnFocusStyling(false);
        }, sideDrawer.closingDuration);
    },

    /**
     * Builds metadata for each tile in the recordlist view
     */
    buildTileMeta: function() {
        var tileDef = this.meta.tileDef || [];
        var tileBodyArr = [];
        var fieldMetadata = app.metadata.getModule(this.module, 'fields');

        _.each(tileDef.panels, function(panel) {
            if (panel.is_header) {
                let headerField =  this.pipelineConfig.tile_header[this.module];
                if (fieldMetadata[headerField]) {
                    fieldMetadata[headerField].link = true;
                }
                panel.fields = [fieldMetadata[headerField]];
            } else {
                var tileBodyField = this.pipelineConfig.tile_body_fields[this.module];
                _.each(tileBodyField, function(tileBody) {
                    var tileFieldMeta = app.utils.deepCopy(fieldMetadata[tileBody]);
                    if (_.isObject(tileFieldMeta.displayParams)) {
                        _.extend(tileFieldMeta, tileFieldMeta.displayParams);
                        delete tileFieldMeta.displayParams;
                    }
                    tileBodyArr.push(tileFieldMeta);
                }, this);
                panel.fields = tileBodyArr;
            }
        }, this);

        this.meta.tileDef = tileDef;
    },

    /**
     * Sets number of results to be displayed for a column in the page
     * @param {integer} resultsNum
     */
    setResultsPerPageColumn: function(resultsNum) {
        var recordsPerColumn = this.pipelineConfig.records_per_column[this.module];
        resultsNum = resultsNum || recordsPerColumn;
        var results = parseInt(resultsNum);
        if (!isNaN(results)) {
            this.resultsPerPageColumn = results;
        }
    },

    /**
     * Sets values to be hidden in the tile
     * @param {Array} hiddenValues an array of values to be hidden
     */
    setHiddenHeaderValues: function(hiddenValues) {
        hiddenValues =
            hiddenValues || this.pipelineConfig.hidden_values[this.module] || [];
        if (_.isEmpty(hiddenValues)) {
            return;
        }

        this.hiddenHeaderValues = hiddenValues;
    },

    /**
     * Builds filter definition for the tiles to be recordlist to be displayed and reloads the data
     * @param {Array} filterDef
     */
    buildFilters: function(filterDef) {
        this.pipelineType = this.context.get('model').get('pipeline_type');
        this.pipelineFilters = filterDef || [];
        this.offset = 0;
        this.loadData();
    },

    /**
     * Checks if the user has access to view and loads data to be displayed on the recordlist
     */
    loadData: function() {
        this.recordsToDisplay = [];
        this.buildTileMeta();
        this.setResultsPerPageColumn();
        this.setHiddenHeaderValues();

        this.getTableHeader();
        if (this.hasAccessToView) {
            this.buildRecordsList();
        }
    },

    /**
     * Sets records to display.
     *
     * @param {string} headerField The header field
     * @param {Array} options List of options
     */
    _setRecordsToDisplay: function(headerField, options) {
        // Get all the whitelisted column names for current module
        if (!_.isUndefined(this.pipelineConfig.available_columns) &&
            this.pipelineConfig.available_columns[this.module]) {
            let items = this.pipelineConfig.available_columns[this.module][headerField] || [];
            var index = 0;
            _.each(items, function(item, key) {
                index = index <= 11 ? index : index % 12;
                if ((!_.isEmpty(options[key]) || _.includes(_.values(options), key)) &&
                    (_.indexOf(this.hiddenHeaderValues, item) === -1)) {
                    this.recordsToDisplay.push({
                        'headerName': !_.isEmpty(options[key]) ? options[key] : item,
                        'headerKey': key,
                        'records': [],
                        'colorIndex': index,
                        'offset': 0,
                        'headerCount': 0,
                        'headerTotal': 0,
                        'headerTotalPreferred': 0,
                        'oneDigitBadge': true,
                        'headerID': this.formatStringID(key),
                    });
                    index++;
                }
            }, this);
        } else {
            var items = _.difference(options, this.hiddenHeaderValues);
            _.each(options, function(option, key) {
                var index = _.indexOf(items, option);
                index = index <= 11 ? index : index % 12;
                if (!_.isEmpty(key) && (_.indexOf(this.hiddenHeaderValues, key) === -1)) {
                    this.recordsToDisplay.push({
                        'headerName': option,
                        'headerKey': key,
                        'records': [],
                        'colorIndex': index,
                        'offset': 0,
                        'headerCount': 0,
                        'headerTotal': 0,
                        'headerTotalPreferred': 0,
                        'oneDigitBadge': true,
                        'headerID': this.formatStringID(key),
                    });
                }
            }, this);
        }
    },

    /**
     * Gets the table headers for all the columns being displayed on the page
     */
    getTableHeader: function() {
        if (this.pipelineType !== 'date_closed') {
            var headerField = this.pipelineConfig.table_header[this.module] || '';

            if (!app.acl.hasAccessToModel('read', this.model, headerField)) {
                this.context.trigger('open:config:fired');
                return;
            }

            if (headerField) {
                var moduleFields = app.metadata.getModule(this.module, 'fields');
                var optionsList = moduleFields[headerField].options;

                if (optionsList) {
                    var options = app.lang.getAppListStrings(optionsList) || [];
                }

                if (!_.isEmpty(options)) {
                    this._setRecordsToDisplay(headerField, options);
                } else {
                    // call enum api
                    app.api.enumOptions(this.module, headerField, {
                        success: _.bind(function(data) {
                            if (!this.disposed) {
                                this._setRecordsToDisplay(headerField, data);
                                this._super('_render');
                                if (this.hasAccessToView) {
                                    this.buildRecordsList();
                                }
                            }
                        }, this)
                    });
                }
            }

            this.headerField = headerField;
        } else {
            var self = this;
            var currDate = app.date(this.startDate);

            this.recordsToDisplay.push({
                'headerName': currDate.format('MMMM YYYY'),
                'headerKey': currDate.format('MMMM YYYY'),
                'records': [],
                'colorIndex': 0,
                'headerTotal': 0,
                'headerTotalPreferred': 0,
                'headerCount': 0,
                'oneDigitBadge': true,
                'headerID': this.formatStringID(currDate.format('MMMM YYYY')),
            });

            for (var i = 1; i < this.monthsToDisplay; i++) {
                currDate.add(1, 'months');
                self.recordsToDisplay.push({
                    'headerName': currDate.format('MMMM YYYY'),
                    'headerKey': currDate.format('MMMM YYYY'),
                    'records': [],
                    'colorIndex': i,
                    'headerTotal': 0,
                    'headerTotalPreferred': 0,
                    'headerCount': 0,
                    'oneDigitBadge': true,
                    'headerID': this.formatStringID(currDate.format('MMMM YYYY')),
                });
            }
            this.headerField = 'date_closed';
        }

        this.hasAccessToView = app.acl.hasAccessToModel('read', this.model, this.headerField) ? true : false;
        this._super('render');
    },

    /**
     * Gets the colors for each of the column headers
     * @return {string[]|null|Array} an array of hexcode for the colors
     * @deprecated Since 10.3.0
     */
    getColumnColors: function() {
        app.logger.warn(
            'getColumnColors() is deprecated in 10.3.0. ' +
            'Please use the utility CSS class: .pipeline-bg-color-n where n is 0-11.'
        );
        var columnColor = this.pipelineConfig.header_colors;
        if (_.isEmpty(columnColor) || columnColor == 'null') {
            columnColor = {};
        }

        return columnColor;
    },

    /**
     * Sets offset to 0 before render
     */
    preRender: function() {
        this.offset = 0;
    },

    /**
     * Call the render method from the super class to render the view between the calls to preRender and postRender
     * @inheritdoc
     */
    render: function() {
        this.preRender();
        this._super('render');
        this.postRender();
    },

    /**
     * Calls methods to add draggable action to the tile and bind scroll to the view
     */
    postRender: function() {
        this.resizeContainer();
        this.buildDraggable();
        this.bindColumnScroll();
        this.blockCurrentColumnSort();
        //checks and displays if down arrow icons should be in columns
        this.displayDownArrows();
    },

    /**
     * Adds a newly created model to the view.
     * @param {Object} model Model that should be added to a column.
     */
    addModelToCollection: function(model) {
        var collection = this.getColumnCollection(model);

        if (collection && collection.records) {
            var literal = this.addTileVisualIndicator([model.toJSON()]);
            model.set('tileVisualIndicator', literal[0].tileVisualIndicator);

            collection.records.add(model, {at: 0});
            this.dataFetched = true;
            this.totalRecords = this.totalRecords + 1;
        }

        this._super('render');
        this.postRender();
    },

    /**
     * Returns the collection of the column to which a new opportunity is being added
     * @param {Object} model for the newly created opportunity
     * @return {*} a collection object
     */
    getColumnCollection: function(model) {
        if (this.pipelineType === 'date_closed') {
            return _.findWhere(this.recordsToDisplay, {
                headerName: app.date(model.get(this.headerField)).format('MMMM YYYY')
            });
        }

        return _.findWhere(this.recordsToDisplay, {headerKey: model.get(this.headerField)});
    },

    /**
     * Shows the loading cell and calls method to fetch all the records to be displayed on the page
     */
    buildRecordsList: function() {
        app.alert.show('pipeline-records-loading', {
            level: 'process'
        });
        this.getRecords();
    },

    /**
     * Returns an array of all the filters to be applied on the records
     * @param {Object} column contains details like headerName, headerKey etc. about a column of records
     * @return {Array}
     */
    getFilters: function(column) {
        var filter = [];
        var filterObj = {};

        if (this.pipelineType !== 'date_closed') {
            filterObj[this.headerField] = {'$equals': column.headerKey};
            filter.push(filterObj);
            _.each(this.pipelineFilters, function(filterDef) {
                filter.push(filterDef);
            }, this);
        } else {
            var startMonth = app.date(column.headerName, 'MMMM YYYY').startOf('month').format('YYYY-MM-DD');
            var endMonth = app.date(column.headerName, 'MMMM YYYY').endOf('month').format('YYYY-MM-DD');
            filterObj[this.headerField] = {'$dateBetween': [startMonth, endMonth]};
            filter.push(filterObj);

            _.each(this.pipelineFilters, function(filterDef) {
                filter.push(filterDef);
            }, this);
        }

        return filter;
    },

    /**
     * Return an array of fields to be fetched and displayed on each tile
     * @return {Array} an array of fields
     */
    getFieldsForFetch: function() {
        if (!_.isEmpty(this._fieldsToFetch)) {
            return this._fieldsToFetch;
        }
        var fields =
            _.flatten(
                _.map(_.flatten(_.pluck(this.meta.tileDef.panels, 'fields')), function(field) {
                    if (field === undefined) {
                        return;
                    }

                    return _.union(
                        // The name of this field itself
                        [field.name],
                        // The name of any relate ID field
                        [field.id_name],
                        // The names of any sub-fields
                        _.pluck(field.fields, 'name'),
                        // The names of any related fields
                        _.flatten(field.related_fields)
                    );
                })
            );

        fields.push(
            this.headerField,
            this.tileVisualIndicatorFields[this.module]
        );

        var fieldMetadata = app.metadata.getModule(this.module, 'fields');
        if (fieldMetadata) {
            // Filter out all fields that are not actual bean fields
            fields = _.reject(fields, function(name) {
                return _.isUndefined(fieldMetadata[name]);
            });
        }

        return this._fieldsToFetch = _.uniq(fields);
    },

    /**
     * Uses fields to get the requests for the data to be fetched
     */
    getRecords: function() {
        var fields = this.getFieldsForFetch();
        var requests = this.buildRequests(fields);
        this.fetchData(requests);
    },

    /**
     * Uses fields, filters and other properties to build requests for the data to be fetched
     * @param {Array} fields to be displayed on each tile
     * @return {Array} an array of request objects with dataType, method and url
     */
    buildRequests: function(fields) {
        var requests = {};
        requests.requests = [];

        _.each(this.recordsToDisplay, function(column) {
            var filter = this.getFilters(column);

            var getArgs = {
                filter: filter,
                fields: fields,
                'max_num': this.resultsPerPageColumn,
                'offset': this.offset
            };

            if (!_.isEmpty(this.orderBy.field)) {
                getArgs.order_by = `${this.orderBy.field}:${this.orderBy.direction}`;
            }

            var req = {
                'url': app.api.buildURL(this.module, null, null, getArgs).replace('rest/', ''),
                'method': 'GET',
                'dataType': 'json'
            };

            requests.requests.push(req);
        }, this);

        return requests;
    },

    /**
     * Makes the api call to get the data for the tiles
     * @param {Array} requests an array of request objects
     */
    fetchData: function(requests) {
        var self = this;
        this.moreData = false;
        app.api.call('create', app.api.buildURL(null, 'bulk'), requests, {
            success: function(dataColumns) {
                app.alert.dismiss('pipeline-records-loading');
                if (dataColumns.length !== self.recordsToDisplay.length) {
                    // the data being returned is not for this view
                    // user must've clicked several tabs before data finished loading
                    return;
                }
                self.dataFetched = true;
                self.totalRecords = 0;
                _.each(self.recordsToDisplay, function(column, index) {
                    var records = app.data.createBeanCollection(self.module);
                    if (!_.isEmpty(column.records.models)) {
                        records = column.records;
                    }
                    var contents = dataColumns[index].contents;
                    var augmentedContents = self.addTileVisualIndicator(contents.records);
                    records.add(augmentedContents);
                    column.records = records;
                    self.totalRecords = self.totalRecords + records.length;
                    column.offset = contents.next_offset;
                    self.isShowCount = this.pipelineConfig.show_column_count[this.module];
                    if (self.isShowCount) {
                        self.displayColumnCount(self, column);
                    }
                    self.isShowTotal = this.pipelineConfig.show_column_total[this.module];
                    if (self.isShowTotal) {
                        self.displayColumnTotal(self, column);
                    }

                    if (contents.next_offset > -1 && !self.moreData) {
                        self.moreData = true;
                    }
                }, self);

                self._super('render');
                self.postRender();

                if (self.moreData) {
                    self.offset += self.resultsPerPageColumn;
                }
            }
        });
    },

    /**
     * Stabilizes the height and width of critical page elements
     */
    resizeContainer: function() {
        if (this.disposed) {
            return;
        }

        let $kanbanCol = $('.kanban-col');
        if ($kanbanCol.length) {
            let $tResponsive = $kanbanCol.closest('.table-responsive');
            let $pipelineContent = $('.my-pipeline-content');
            let arrow = $('.arrowholder');
            let kanbanColMargin =
                parseInt($kanbanCol.css('marginLeft')) + parseInt($kanbanCol.css('marginRight'));
            let kanbanColPadding = $kanbanCol.innerWidth() - $kanbanCol.width();
            let tableResponsivePadding = $tResponsive.innerWidth() - $tResponsive.width();
            let isHorizontalScrollLimit =
                $pipelineContent.width() + $pipelineContent.scrollLeft() >= _.first($pipelineContent).scrollWidth;
            $tResponsive.toggleClass('no-scroll', $tResponsive.get(0).scrollWidth == $tResponsive.get(0).offsetWidth);

            let arrowWidth = 0;

            if (arrow.length) {
                arrowWidth = arrow.width() * arrow.length;
            }

            //track the horizontal scroll position
            let currentHorizScroll = $pipelineContent.scrollLeft();
            $pipelineContent.children().hide();
            let pipelineContentWidth = $pipelineContent.width();
            $pipelineContent.children().show();
            //restore the saved scroll position
            $pipelineContent.scrollLeft(currentHorizScroll);

            let $originalTitle = $kanbanCol.find('.original-title');
            $originalTitle.hide();

            let contentWidthForColumns = pipelineContentWidth - arrowWidth - tableResponsivePadding;
            let kanbanColCalculatedWidth =
                (contentWidthForColumns / $kanbanCol.length) - kanbanColMargin - kanbanColPadding;
            $kanbanCol.css('width', kanbanColCalculatedWidth);

            if (isHorizontalScrollLimit && this._isFetchingColumn) {
                $pipelineContent.scrollLeft($pipelineContent.width());
            }
            $originalTitle.show();

            let $ul = $kanbanCol.find('ul');
            $ul.hide();

            let $mainPane = this.$el.closest('.main-pane');
            let $pipelineBlock = this.$el.closest('.pipeline-refresh-btn');
            let height = $mainPane.height() - $pipelineBlock.height();
            let tbl = $kanbanCol.closest('.table');
            tbl.height(tbl.height() + height);
            $kanbanCol.height(tbl.height() - 30);

            let $kanbanColHeader = $kanbanCol.find('.kanban-col-header');
            $ul.height($kanbanCol.height() - $kanbanColHeader.height() - 50).show();
            $('.column-fade-top, .column-fade-bottom').width($ul.width());
        }
        this.displayDownArrows();

        // When the container is resized, re-scroll to the focused column if needed
        let currentColumn = $kanbanCol.closest('.current-column');
        if (currentColumn.length) {
            let columnIndex = currentColumn.index();
            let columnRow = currentColumn.find('div.blue-border').closest('li').index();
            this.setCurrentColumn(columnIndex, columnRow);
        }
    },

    /**
     * Gives the ability for a tile to be dragged and moved to other columns on the page
     */
    buildDraggable: function() {
        if (!app.acl.hasAccessToModel('edit', this.model) ||
            !app.acl.hasAccessToModel('edit', this.model, this.headerField)) {
            return;
        }

        this.$('.column').sortable({
            connectWith: '.column',
            classes: {
                'ui-sortable-helper': 'pipeline rounded-lg'
            },
            handle: '.pipeline-tile',
            cancel: '.portlet-toggle',
            placeholder: 'portlet-placeholder ui-corner-all mb-4 rounded-lg',
            appendTo: 'body',
            helper: 'clone',
            start: _.bind(function(event, ui) {
                ui.item.closest('.kanban-col').addClass('start-column');
                ui.item.show();
                ui.placeholder.height(ui.item.height());

                // Create a placeholder clone. This is so that we can still
                // show a placeholder in the original column while dragging
                // over other columns
                ui.placeholder.clone()
                    .addClass('placeholder-clone')
                    .height(ui.item.height())
                    .hide()
                    .insertAfter(ui.item);
                ui.item.hide();
            }, this),
            update: _.bind(function() {
                $('.kanban-col').removeClass('start-column');
            }, this),
            over: _.bind(function(event, ui) {
                var eventTarget = $(event.target);
                let kanbanCol = eventTarget.closest('.kanban-col');
                if (!kanbanCol.hasClass('start-column')) {
                    kanbanCol.addClass('target-column');
                }
            }, this),
            out: _.bind(function(event, ui) {
                var eventTarget = $(event.target);
                eventTarget.closest('.kanban-col').removeClass('target-column');
            }, this),
            change: (event, ui) => {
                // If hovering over the original column, move the actual
                // placeholder there so that on drop the ordering remains
                if (ui.item.parent().is(ui.placeholder.parent())) {
                    ui.item.parent().find('.placeholder-clone').hide();
                    ui.placeholder.insertAfter(ui.item);
                } else {
                    ui.item.parent().find('.placeholder-clone').show();
                }
            },
            stop: (event, ui) => {
                // After dropping, remove the cloned placeholder item
                ui.item.parent().find('.placeholder-clone').remove();
            },
            receive: _.bind(function(event, ui) {
                var modelId = this.$(ui.item).data('modelid');
                var oldCollection = _.findWhere(this.recordsToDisplay, {
                    headerKey: this.$(ui.sender).attr('data-column-key')
                });
                var newCollection = _.findWhere(this.recordsToDisplay, {
                    headerKey: this.$(ui.item).parent('ul').attr('data-column-key')
                });
                var model = oldCollection.records.get(modelId);
                if (!app.acl.hasAccessToModel('edit', model)) {
                    app.alert.show('not_authorized', {
                        level: 'error',
                        messages: 'Not allowed to perform action "save" on this record',
                        autoClose: true,
                    });

                    this.$(ui.sender).sortable('cancel');
                    return;
                }
                var success = _.bind(function() {
                    this.switchCollection(oldCollection, model, newCollection);
                    this.saveModel(model, {
                        ui: ui,
                        oldCollection: oldCollection,
                        newCollection: newCollection
                    });
                }, this);
                var error = _.bind(function() {
                    this.$(ui.sender).sortable('cancel');
                    this.$('.column').sortable('enable');
                }, this);
                var complete = function() {
                    app.alert.dismiss('model_loading');
                };

                // Run any functionality necessary before the change is processed
                this._preChange();

                model.fetch({
                    view: 'record',
                    fields: this.getFieldsForFetch(),
                    success: success,
                    error: error,
                    complete: complete
                });
            }, this)
        });

        this.$('.portlet')
            .addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
            .find('.span12')
            .addClass('ui-widget-header ui-corner-all');
    },

    /**
     * Gets called when a tile is dragged to another column
     * Removes the tile from the former column collection and adds it to the later one
     * @param {Object} oldCollection Collection object for the column to which the tile previously belonged
     * @param {Object} model model of the tile being moved
     * @param {Object} newCollection Collection object for the column to which the tile is moved
     */
    switchCollection: function(oldCollection, model, newCollection) {
        oldCollection.records.remove(model);
        newCollection.records.add(model, {at: 0});
    },

    /**
     * Gets called to save the model once it switches columns
     * @param {Object} model for the tile to be saved
     * @param {Object} pipelineData contains info about the pipeline ui and collections involved in the change
     */
    saveModel: function(model, pipelineData) {
        var self = this;

        // Set the changes on the model before validating and saving. If validation
        // fails, the updated model will be opened in a record view drawer which causes
        // the synced attributes to change, so we need to store a backup of the
        // previous values for changed fields on the model in case we need to revert
        this._setNewModelValues(model, pipelineData.ui);
        model.oldValues = _.pick(model.previousAttributes(), function(value, key) {
            return key in model.changed;
        });

        // Validate the model according to the record view validation rules. For
        // accurate validation which takes SugarLogic dependencies into account,
        // we need to actually open the record view. Here we load the view into
        // the side drawer (without opening it), then validate it. If validation
        // is successful, the model/collection change is saved. Otherwise, the
        // record view is opened in a regular drawer for the user to fix the
        // invalid fields
        var sideDrawer = this._getSideDrawer();
        var beanCollection = app.data.createBeanCollection(this.module, [model]);
        if (sideDrawer) {
            sideDrawer.showComponent({
                layout: 'tile-validation-drawer',
                context: {
                    skipRouting: true,
                    model: model,
                    collection: beanCollection,
                    module: self.module,
                    saveImmediately: true,
                    validationCallback: function(isValid) {
                        self._handleValidationResults(isValid, model, pipelineData);
                    },
                    saveCallback: function(saved) {
                        self._callWithTileModel(model, '_postChange', [!saved, pipelineData]);
                    }
                }
            });
        }
    },

    /**
     * Sets the changed values on the model before validation and saving. This is
     * useful to override in case custom action must be taken to handle field changes
     * (for example, converting "January 2020" to "01/31/2020" before setting the
     * value on the model)
     * @param {Object} model the model to set the values on
     * @param (Object} ui an object with the ui details of the tiles like originalPosition, offset, etc.
     * @private
     */
    _setNewModelValues: function(model, ui) {
        model.set(this.headerField, this.$(ui.item).parent('ul').attr('data-column-key'));
    },

    /**
     * Gets the side drawer component associated with the layout
     * @return {Object} The side drawer, or undefined if it does not exist
     * @private
     */
    _getSideDrawer: function() {
        if (!this.sideDrawer) {
            this.sideDrawer = this.layout.getComponent('side-drawer');
        }
        return this.sideDrawer;
    },

    /**
     * Opens a drawer to the record view to fix any invalid fields on the model
     * after switching the model to a new column
     * @param isValid boolean indicating whether the model passed validation
     * @param {Object} model the model that was validated
     * @param {Object} pipelineData contains info about the pipeline ui and collections involved in the change
     * @private
     */
    _handleValidationResults: function(isValid, model, pipelineData) {
        if (!isValid) {
            var self = this;
            var beanCollection = app.data.createBeanCollection(this.module, [model]);
            app.drawer.open({
                layout: 'tile-validation-drawer',
                context: {
                    skipRouting: true,
                    module: self.module,
                    model: model,
                    collection: beanCollection,
                    noEditFields: [self.headerField],
                    saveImmediately: true,
                    saveCallback: function(saved) {
                        app.drawer.close(saved);
                    },
                    cancelCallback: function() {
                        app.drawer.close(false);
                    },
                    editOnly: true
                }
            }, function(saved) {
                self._callWithTileModel(model, '_postChange', [!saved, pipelineData]);
            });
        }
    },

    /**
     * Utility function that runs before a column change is processed
     * @private
     */
    _preChange: function() {
        // Disable dragging while the change is being processed to prevent any
        // potential issues due to multiple simultaneous drag/drops
        this.$('.column').sortable('disable');

        // Display a loading message while the model data is being fetched
        app.alert.show('model_loading', {
            level: 'process',
        });
    },

    /**
     * Utility function. It fetches a model with only the fields required by the view.
     * @param {Object} model A model that is passed to the view from elsewhere.
     * @param {string} methodName The name of the method that should be called with the tile view compatible model.
     * This method should has to accept at least 1 parameter, the first being a model.
     * @param {Array} params Any other params that should be passed to the method called.
     * @private
     */
    _callWithTileModel: function(model, methodName, params) {
        this._preChange();
        var tileModel = app.data.createBean(this.module, {
            id: model.get('id')
        });
        tileModel.fetch({
            view: 'record',
            fields: this.getFieldsForFetch(),
            success: _.bind(function() {
                var newParams = _.union([tileModel], params || []);
                this[methodName].apply(this, newParams);
            }, this),
            error: _.bind(function() {
                this.$('.column').sortable('enable');
            }, this),
            complete: function() {
                app.alert.dismiss('model_loading');
            }
        });
    },

    /**
     * Utility function that runs after a column change is processed
     * @param {Object} model the model involved in the column change
     * @param {boolean} shouldRevert indicates whether the change needs to be reverted
     * @param {Object} pipelineData contains info about the pipeline ui and collections involved in the change
     * @private
     */
    _postChange: function(model, shouldRevert, pipelineData) {
        var validCollection = this.getColumnCollection(model);
        if (shouldRevert) {
            this._revertChanges(model, pipelineData);
        } else if (validCollection.headerKey !== pipelineData.newCollection.headerKey) {
            this.switchCollection(pipelineData.newCollection, model, validCollection);
        }

        // Since both this view and the record view make changes to the model,
        // sync its final attributes here to avoid "unsaved changes" warnings
        model.setSyncedAttributes(model.attributes);

        this._super('render');
        this.postRender();
        this.$('.column').sortable('enable');
    },

    /**
     * Reverts the changes to the model and collections made by a column move
     * @param {Object} model the model involved in the change
     * @param {Object} pipelineData contains info about the pipeline ui and collections involved in the change
     * @private
     */
    _revertChanges: function(model, pipelineData) {
        model.set(model.oldValues);
        this.switchCollection(pipelineData.newCollection, model, pipelineData.oldCollection);
        this.$(pipelineData.ui.sender).sortable('cancel');
    },

    /**
     * Binds scroll to the recordlist pane
     */
    bindScroll: function() {
        this.$el.on('scroll', _.bind(this.listScrolled, this));
    },

    /**
     * Binds scroll to the record list panes for the all columns
     */
    bindColumnScroll: function() {
        this.$('ul').on('scroll', _.bind(this.listColumnScrolled, this));
    },

    /**
     * Block sorting inside the current column
     */
    blockCurrentColumnSort: function() {
        $('ul.column').mouseenter(function() {
            $(this).sortable('option', 'items', '');
        });
        $('ul.column').mouseleave(function() {
            $(this).sortable('option', 'items', '> *');
        });
    },

    /**
     * Listens to the scroll event on the list
     * Checks and displays if more data is present on the page
     * @param event
     */
    listScrolled: function(event) {
        var elem = $(event.currentTarget);
        var isAtBottom = (elem[0].scrollHeight - elem.scrollTop()) <= elem.outerHeight();

        if (isAtBottom && this.moreData) {
            this.buildRecordsList();
        }
    },

    /**
     * Listens to the scroll event on the list
     * Checks and displays if down or/and up arrow icons should be in column
     * Checks and displays if more data is present in the column
     * @param event
     */
    listColumnScrolled: function(event) {
        let $elem = $(event.currentTarget);
        let $tdParent = $elem.closest('td');

        let hideCaretUp = $elem.scrollTop() <= 0;
        $tdParent.find('.sicon-caret-up').toggleClass('invisible', hideCaretUp);

        let columnFadeTop = $tdParent.find('.column-fade-top');
        if (columnFadeTop) {
            columnFadeTop.toggleClass('invisible', hideCaretUp);
        }

        let hideCaretDown = (_.first($elem).scrollHeight - $elem.scrollTop()) <= Math.ceil($elem.outerHeight());
        $tdParent.find('.sicon-caret-down').toggleClass('invisible', hideCaretDown);

        let columnFadeBottom = $tdParent.find('.column-fade-bottom');
        if (columnFadeBottom) {
            columnFadeBottom.toggleClass('invisible', hideCaretDown);
        }

        let isNextRecords = ((_.first($elem).scrollHeight - $elem.scrollTop()) * this.nextRequestReady) <=
            $elem.outerHeight();

        if (isNextRecords && !this._isFetchingColumn) {
            //blocks any other request for getting additional column data
            this._isFetchingColumn = true;
            this.getColumnRecords($elem.attr('data-column-key'));
        }

        this.saveColumnScrollTop();
    },

    /**
     * Uses fields to get the requests for the column data to be fetched
     * Displays the loading message as we’re waiting for the next batch of Records to load
     * @param {string} headerKey
     */
    getColumnRecords: function(headerKey) {
        this._columnDataFetching.push(headerKey);
        let fields = this.getFieldsForFetch();
        let requests = this.buildColumnRequests(fields, headerKey);

        if (requests.length) {
            app.alert.show('pipeline-records-loading', {
                level: 'process'
            });
            this.saveColumnScrollTop();
            this.fetchColumnData(requests, headerKey);
        } else {
            //allows other requests for getting additional column data
            this._isFetchingColumn = false;
        }
    },

    /**
     * Saves current positions for every column scrollbar
     */
    saveColumnScrollTop: function() {
        let columns = this.$('ul');
        this._columnScrollTop = [];

        _.each(columns, function(column) {
            let headerKey = $(column).attr('data-column-key');
            this._columnScrollTop[headerKey] = {
                scrollTop: $(column).scrollTop(),
            };
        }, this);
    },

    /**
     * Uses fields, filters and other properties to build requests for the data to be fetched
     * @param {Array} fields to be displayed on each tile
     * @param {string} headerKey
     * @return {Array} an array of request objects with dataType, method and url
     */
    buildColumnRequests: function(fields, headerKey) {
        let requests = [];

        _.each(this.recordsToDisplay, function(column) {
            if (column.offset > -1 && column.headerKey === headerKey) {
                let filter = this.getFilters(column);

                let getArgs = {
                    filter: filter,
                    fields: fields,
                    max_num: column.offset >= this.resultsPerPageColumn ? this.resultsPerPageColumn :
                        (this.resultsPerPageColumn * 2),
                    offset: column.offset
                };

                if (!_.isEmpty(this.orderBy.field)) {
                    getArgs.order_by = `${this.orderBy.field}:${this.orderBy.direction}`;
                }

                let req = {
                    'url': app.api.buildURL(this.module, null, null, getArgs).replace('rest/', ''),
                    'method': 'GET',
                    'dataType': 'json'
                };

                requests.push(req);
                return requests;
            }
        }, this);

        return requests;
    },

    /**
     * Makes the api call to get the data for the tiles
     * @param {Array} requests an array of request objects
     * @param {string} headerKey
     */
    fetchColumnData: function(requests, headerKey) {
        let self = this;
        app.api.call('create', app.api.buildURL(null, 'bulk'), {requests}, {
            success: function(dataColumns) {
                _.each(self.recordsToDisplay, function(column) {
                    if (column.headerKey === headerKey) {
                        let records = app.data.createBeanCollection(self.module);

                        if (!_.isEmpty(column.records.models)) {
                            records = column.records;
                        }

                        let contents = _.first(dataColumns).contents;
                        let augmentedContents = self.addTileVisualIndicator(contents.records);
                        records.add(augmentedContents);
                        column.records = records;
                        column.offset = contents.next_offset;
                        let index = self._columnDataFetching.indexOf(headerKey);
                        self._columnDataFetching.splice(index, 1);
                    }
                }, self);

                let currentColumn = $('.table .current-column');
                let currentColumnIndex = currentColumn.index();
                let currentRowIndex = currentColumn.find('.blue-border').closest('li').index();

                self._super('render');
                self.postRender();

                if (currentColumn.length !== 0) {
                    self.setCurrentColumn(currentColumnIndex, currentRowIndex);
                }

                if (app.sideDrawer && app.sideDrawer.isOpen()) {
                    app.sideDrawer.showPreviousNextBtnGroup();
                }
            },
            complete: function() {
                app.alert.dismiss('pipeline-records-loading');
                //allows other requests for getting additional column data
                self._isFetchingColumn = false;
            }
        });
    },

    /**
     * Sets current column and tile for side drawer
     * @param {integer} column
     * @param {integer} row
     * @param {integer} animationDuration
     */
    setCurrentColumn: function(column, row, animationDuration = 0) {
        let $pipelineContent = this.$('#my-pipeline-content');
        let $tr = $pipelineContent.find('table tr');
        let $arrowHolder = $pipelineContent.find('.arrowholder');
        let $td = $tr.find('td').eq(column);
        let $tile = $td.find('li').eq(row).find('.tile');

        // Prevent the user from horizontally scrolling while a column is focused
        $pipelineContent.css('overflow-x', 'hidden');
        $arrowHolder.css('visibility', 'hidden');

        // Set the new focused column
        $tr.find('td.current-column').removeClass('current-column');
        $td.addClass('current-column');

        // Set the new focused tile and scroll it into view in the column if necessary
        $tr.find('.blue-border').removeClass('blue-border');
        $tile.addClass('blue-border');
        let bounding = _.first($tile).getBoundingClientRect();
        if (bounding.top < $td.find('ul').offset().top) {
            _.first($tile).scrollIntoView(true);
        }
        if (bounding.bottom > (window.innerHeight || document.documentElement.clientHeight)) {
            _.first($tile).scrollIntoView(false);
        }

        // Animate the columns by translating them to the left based on column and scroll positions
        let scrollOffset = $pipelineContent.scrollLeft();
        let scrollPx = (($td.position().left + 6) - scrollOffset);
        let originalTransition = $tr.css('transition');
        $tr.css('transition', `transform ${animationDuration}ms ease-in-out`);
        $tr.css('transform', `translate(${(-scrollPx * 0.0625)}rem)`);
        setTimeout(() => {
            $tr.css('transition', originalTransition);
        }, animationDuration);
    },

    /**
     * Checks and displays if down arrow icons should be in columns and
     * positioning the scrollbar for every column after fetching the data
     */
    displayDownArrows: function() {
        let columns = this.$('ul');

        _.each(columns, function(column) {
            let headerKey = $(column).attr('data-column-key');

            let record = _.first(_.filter(this.recordsToDisplay, function(record) {
                return record.headerKey === headerKey;
            }));

            if (!_.isUndefined(this._columnScrollTop[headerKey])) {
                if (!_.isUndefined(this._columnScrollTop[headerKey].offset)) {
                    _.first($(column)).children[this._columnScrollTop[headerKey].offset - 3].scrollIntoView(true);
                } else {
                    $(column).scrollTop(this._columnScrollTop[headerKey].scrollTop);
                }
            }

            if ((_.first($(column)).scrollHeight - $(column).scrollTop()) > Math.ceil($(column).outerHeight())) {
                let $tdParent = $(column).closest('td');
                $tdParent.find('.sicon-caret-down').removeClass('invisible');

                let columnFadeBottom = $tdParent.find('.column-fade-bottom');
                if (columnFadeBottom) {
                    columnFadeBottom.removeClass('invisible');
                }
            } else if (record.offset && !_.includes(this._columnDataFetching, headerKey)) {
                this._isFetchingColumn = true;
                this.getColumnRecords(headerKey);
            }
        }, this);
    },

    /**
     * Adds the visual indicator to all the tiles based on the status or date depending on the modules
     * @param {Array} modelsList a list of all the tile models
     * @return {Array} updated model list with all the indicator values
     */
    addTileVisualIndicator: function(modelsList) {
        var self = this;
        var updatedModel = {};
        var dueDate = app.date();
        var expectedCloseDate = app.date();

        return _.map(modelsList, function(model) {
            switch (model._module) {
                case 'Cases':
                    updatedModel = self.addIndicatorBasedOnStatus(model);
                    break;
                case 'Leads':
                    updatedModel = self.addIndicatorBasedOnStatus(model);
                    break;
                case 'Opportunities':
                    expectedCloseDate = app.date(model.date_closed, 'YYYY-MM-DD');
                    updatedModel = self.addIndicatorBasedOnDate(model, expectedCloseDate);
                    break;
                case 'Tasks':
                    dueDate = app.date.parseZone(model.date_due);
                    updatedModel = self.addIndicatorBasedOnDate(model, dueDate);
                    break;
                default:
                    model.tileVisualIndicator = self.tileVisualIndicator.default;
                    updatedModel = model;
            }

            return updatedModel;
        });
    },

    /**
     * Adds indicator based on the date_closed or date_due
     * @param {Object} model model object for the tile to which the indicator is being added
     * @param {string} date date string related to the model
     * @return {Object} updated model with visual indicator
     */
    addIndicatorBasedOnDate: function(model, date) {
        var now = app.date();
        var aMonthFromNow = app.date().add(1, 'month');

        if (date.isBefore(now)) {
            model.tileVisualIndicator = this.tileVisualIndicator.outOfDate;
        }
        if (date.isAfter(aMonthFromNow)) {
            model.tileVisualIndicator = this.tileVisualIndicator.inFuture;
        }
        if (date.isBetween(now, aMonthFromNow)) {
            model.tileVisualIndicator = this.tileVisualIndicator.nearFuture;
        }

        return model;
    },

    /**
     * Adds indicator based on the Opportunity status
     * @param {Object} model model object for the tile to which the indicator is being added
     * @return {Object} updated model with visual indicator
     */
    addIndicatorBasedOnStatus: function(model) {
        // Group statuses in 3 categories:
        var inFuture = ['New', 'Converted'];
        var outOfDate = ['Dead', 'Closed', 'Rejected', 'Duplicate', 'Recycled'];
        var nearFuture = ['Assigned', 'In Process', , 'Pending Input', ''];

        if (_.indexOf(outOfDate, model.status) !== -1) {
            model.tileVisualIndicator = this.tileVisualIndicator.outOfDate;
        }
        if (_.indexOf(inFuture, model.status) !== -1) {
            model.tileVisualIndicator = this.tileVisualIndicator.inFuture;
        }
        if (_.indexOf(nearFuture, model.status) !== -1) {
            model.tileVisualIndicator = this.tileVisualIndicator.nearFuture;
        }

        return model;
    },

    /**
     * Listens to the arrow-left button click
     * Updates the start date to 5 months prior
     * Sets offset to 0
     * Reloads the data in the recordlist view
     */
    navigateLeft: function() {
        this.startDate = app.date(this.startDate).subtract(5, 'month').format('YYYY-MM-DD');
        this.offset = 0;
        this.loadData();
    },

    /**
     * Listens to the arrow-right button click
     * Updates the start date to next 5 months
     * Sets offset to 0
     * Reloads the data in the recordlist view
     */
    navigateRight: function() {
        this.startDate = app.date(this.startDate).add(5, 'month').format('YYYY-MM-DD');
        this.offset = 0;
        this.loadData();
    },

    /**
     * @inheritdoc
     */
    sortData: function() {
        app.user.lastState.set(this.orderByLastStateKey, this.orderBy);

        this.offset = 0;
        this.loadData();
    },

    /**
     * @param {string} module
     * @return {Array}
     */
    getSortableFields: function() {
        let sortableFields = [];
        let listViewDefs = app.metadata.getView(this.module, 'list');

        _.each(listViewDefs.panels, function(panel) {
            _.each(panel.fields, function(field) {
                if (app.utils.isSortable(this.module, field.name)) {
                    sortableFields.push(field);
                }
            }, this);
        }, this);

        return sortableFields;
    },

    /**
     * Adds the count in the column header for spesific column only
     * @param {Object} self js context the function that calls
     * @param {Object} column object with column data
     */
    displayColumnCount: function(self, column) {
        let filter = self.getFilters(column);
        let url = app.api.buildURL(self.module, 'count', {}, {filter});
        app.api.call('read', url, null, {
            success: function(data) {
                column.oneDigitBadge = data.record_count < 10;
                column.headerCount = self.formatBadgeData(data.record_count);
                let tpl = app.template.getView('pipeline-recordlist-content.count');
                self.$el.find('#original-badge-' + column.headerID).remove();
                self.$el.find('#original-title-' + column.headerID).append(
                    tpl({
                        isShowCount: self.isShowCount,
                        colorIndex: column.colorIndex,
                        headerID: column.headerID,
                        headerCount: column.headerCount,
                        oneDigitBadge: column.oneDigitBadge,
                    })
                );
            }
        });
    },

    /**
     * Display column sum
     * @param {Object} self is the 'this' object for the model.
     * @param {Object} column object with column data
     */
    displayColumnTotal: function(self, column) {
        const params = {
            filter: self.getFilters(column),
            sumField: self.pipelineConfig.total_field[self.module],
        };
        const url = app.api.buildURL(self.module, 'total', {}, params);
        const userPreferredCurrencyId = app.user.getPreference('currency_id');
        const BaseCurrencyId = app.currency.getBaseCurrencyId();
        app.api.call('read', url, null, {
            success: function(data) {
                if (_.isEmpty(data)) {
                    return;
                }
                if (userPreferredCurrencyId !== BaseCurrencyId &&
                    app.user.getPreference('currency_show_preferred') &&
                    data.field_type === 'currency'
                ) {
                    column.userPreferredCurrencyId = userPreferredCurrencyId;
                }
                column.isCurrency = (data.field_type === 'currency');

                if (column.isCurrency) {
                    let dataSum = data.sum_by_field || 0;
                    let decimal = (dataSum) ? 2 : 0;
                    column.headerTotal = app.currency.formatAmountLocale(dataSum, BaseCurrencyId, decimal);

                    if (column.userPreferredCurrencyId) {
                        let headerTotalPreferred = app.currency.convertAmount(
                            dataSum, BaseCurrencyId, userPreferredCurrencyId
                        );
                        column.headerTotalPreferred = app.currency.formatAmountLocale(
                            headerTotalPreferred, userPreferredCurrencyId, decimal
                        );
                    }
                } else {
                    column.headerTotal = data.sum_by_field;
                }
                let tpl = app.template.getView('pipeline-recordlist-content.total');
                self.$el.find('#total-kanban-' + column.headerID).remove();
                self.$el.find('#original-sum-title-' + column.headerID).append(
                    tpl({
                        colorIndex: column.colorIndex,
                        headerTotal: column.headerTotal,
                        isCurrency: column.isCurrency,
                        isSum: column.isSum,
                        headerID: column.headerID,
                        isShowTotal: self.isShowTotal,
                        userPreferredCurrencyId: column.userPreferredCurrencyId,
                        headerTotalPreferred: column.headerTotalPreferred,
                    })
                );

            }
        });
    },

    /**
     * Rounding and cutting badges data for output
     *
     * @param {number} value
     * @return {mixed}
     */
    formatBadgeData: function(value) {
        if (!_.isNumber(value)) {
            value = 0;
        }

        if (value <= 999) {
            return value;
        }

        return '999+';
    },

    /**
     * Replace special characters in a string with underscore
     *
     * @param {string} value
     * @return {string}
     */
    formatStringID: function(value) {
        return value.replace(/[&\/\\#, +()$~%.'":*?<>{}]/g, '_');
    },

    /**
     * Alters the styling of the side drawer to work on Tile View correctly
     *
     * @param {bool} toggle true to enable TV side drawer styling; false to return it to default
     * @private
     */
    _toggleSideDrawerColumnFocusStyling: function(toggle) {
        if (app.sideDrawer) {
            app.sideDrawer.$el.toggleClass('pipeline', toggle);
            if (toggle) {
                app.sideDrawer.drawerConfigs.left = '21rem';
            } else {
                app.sideDrawer.drawerConfigs = app.utils.deepCopy(app.sideDrawer.defaultDrawerConfigs);
            }
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._toggleSideDrawerColumnFocusStyling(false);
        this.stopListening();
        window.removeEventListener('resize', this.resizeContainerHandler);
        this.$el.off('scroll');
        this.$('ul').off('scroll');

        this._super('_dispose');
    },
})

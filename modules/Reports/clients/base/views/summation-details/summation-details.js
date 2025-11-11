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
 * @class View.Views.Reports.SummationDetailsView
 * @alias SUGAR.App.view.views.ReportsSummationDetailsView
 * @extends View.Views.Base.View
 */
({
    extendsFrom: 'ReportsRowsColumnsView',

    pagination: false,

    LIST_ACTION: {
        'APPEND': 'APPEND',
        'PREPEND': 'PREPEND',
    },

    events: {
        'click .sicon-arrow-left-double': 'toggleGroup',
        'click .sortable': 'sortGroup',
        'click [data-action=export-csv]': 'exportToCsv',
        'click [data-action=show-simplified]': 'showSimplified',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.filter(this.plugins, function(pluginName) {
            return pluginName !== 'ResizableColumns';
        });

        if (!_.contains(this.plugins, 'ReportExport')) {
            this.plugins.push('ReportExport');
        }

        this._super('initialize', [options]);

        this.template = app.template.getView('summation-details', this.module);

        this.rightColumns = [];
        this.isFirstColumnFreezed = false;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.loading) {
            this._adjustLoadingWidgetSize('table');
        }

        if (this.reportComplexity === this.complexities.high ||
            (this.reportComplexity === this.complexities.medium && this.loading)) {
            this.setHeaderVisibility(false);
            this.setFooterVisibility(false);

            return;
        }

        if (this.reportComplexity === this.complexities.medium) {
            this.context.trigger('toggle-orientation-buttons', false);
        } else {
            this.context.trigger('toggle-orientation-buttons', true);
        }

        if (!this.loading) {
            this._placeGroups();
        }
    },

    /**
     * Toggle group
     *
     * @param {Event} evt
     */
    toggleGroup: function(evt) {
        const isUp = _.contains(evt.target.classList, 'up');

        if (this.reportComplexity === this.complexities.medium) {
            this.toggleSimplifiedGroup(evt);
        } else {
            let closestEl = 'table';
            let elementsToModify = [
                '.subgroup',
                'tbody',
            ];

            const setElementsVisibility = function setVisibility(show) {
                _.each(elementsToModify, function each(element) {
                    const targetEl = this.$(evt.target).closest(closestEl).find(element);

                    show ? targetEl.show() : targetEl.hide();
                });
            };

            setElementsVisibility(!isUp);
        }

        this.$(evt.target).toggleClass('up', !isUp).toggleClass('down', isUp);
    },

    /**
     * Toggle group
     *
     * @param {Event} evt
     */
    toggleSimplifiedGroup: function(evt) {
        const isUp = _.contains(evt.target.classList, 'up');

        const bodyEl = this.$(evt.target).closest('tbody');
        const groupBody = bodyEl.find('[data-table="group-body"]');

        if (groupBody.length === 0) {
            return;
        }

        isUp ? groupBody.hide() : groupBody.show();
    },

    /**
     * Rerender the header
     *
     * @param {Array} data
     */
    renderTableMeta: function(data) {
        if (data) {
            this.context.set('data', data);
        }
        this._setHeaderFields();
        this._initializeOrderBy(data);
        this.render();
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

        this.orderBy = app.user.lastState.get(this.orderByLastStateKey);
        const dataOrderBy = _.first(this.data.orderBy);

        if (dataOrderBy) {
            this.orderBy = dataOrderBy;
            this.orderBy.field = this.orderBy.rname = dataOrderBy.name;
            this.orderBy.direction = dataOrderBy.sort_dir === 'a' ? 'asc' : 'desc';

            app.user.lastState.set(this.orderByLastStateKey, this.orderBy);
        }
    },

    /**
     * Render table meta
     *
     * @param {Array} data
     */
    _rebuildData: function(data) {
        if (this.disposed) {
            return;
        }

        this.context.set('rebuildData', true);

        if (_.has(data, 'reportType') && data.reportType === 'summary') {
            this.context.trigger('report:build:data:table', 'summary');

            return;
        }

        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this._toggleEmptyPanel(true);

            return;
        }

        this.data = data;
        this._initializeOrderBy(data);
        this.renderTableMeta(data);

        const emptyPanel = this._isEmptyPanel(data);
        this._toggleEmptyPanel(emptyPanel);

        if (!this.loading) {
            this.context.trigger('report:data:table:loaded', false, 'table');
        }
    },

    /**
     * Build collection
     *
     * @param {Array} data
     */
    buildCollection: function(data) {
        this.data = data;
        this.reportComplexity = this._getReportComplexity(data.recordsNo, _.size(data.header));

        if (_.has(this, 'layout') && this.layout) {
            this.exportAccess = app.acl.hasAccess('export', this.layout.module) &&
                            app.utils.reports.hasAccessToAllReport(this.layout.model, 'export');
        }

        if (this.reportComplexity === this.complexities.medium) {
            this.context.trigger('report:data:table:loaded', false, 'table');
            this.render();

            return;
        }

        if (this.reportComplexity === this.complexities.high) {
            this.context.trigger('report:data:table:loaded', false, 'table');
        }

        this.startBuildCollection(data);
    },

    /**
     * Start to build the data collection
     *
     * @param {Array} data
     */
    startBuildCollection: function(data) {
        let groups = [];
        for (const index in data.groups) {
            let group = data.groups[index];
            let groupBy = _.flatten(this._buildGroupHeader(group));
            let subgroups = this._buildSubgroups(group);
            let colspan;
            for (let subgroup of subgroups) {
                colspan = _.first(subgroup.records).length;
            }
            groups.push({
                subgroups, groupBy, colspan
            });
        }

        data.groups = groups;
        this.data = data;

        if (this.reportComplexity === this.complexities.medium) {
            this.setHeaderVisibility(true);
            this.setFooterVisibility(true);

            this.loading = false;
            this.render();
        } else if (this.reportComplexity === this.complexities.low) {
            this.loading = false;
            this.render();
        }

        const visibleEmptyPanel = this._isEmptyPanel(data) ||
                                    !this.layout ||
                                    !app.utils.reports.hasAccessToAllReport(this.layout.model);
        this._toggleEmptyPanel(visibleEmptyPanel);
    },

    /**
     *
     * @param {Array} group
     */
    _buildGroupHeader: function(group) {
        let keys = Object.keys(group);

        if (keys.length === 1) {
            group = group[keys[0]];
        }

        let groupBy = [];

        if (!_.isUndefined(group.key) && !_.isUndefined(group.id)) {
            let groupValue = this._getGroupHeaderValue(group);
            const countLbl = app.lang.get('LBL_COUNT', 'Reports');

            if (_.isEmpty(groupValue)) {
                groupValue = app.lang.get('LBL_NONE_STRING', 'Reports');
            }

            groupBy.push(`${group.key} = ${groupValue}, ${countLbl} = ${group.count}`);
        }

        if (group.dataStructure) {
            groupBy.push(this._buildGroupHeader(group.dataStructure));
        }

        return groupBy;
    },


    /**
     * Get group header value
     *
     * @param {Object} group
     * @return {string}
     */
    _getGroupHeaderValue: function(group) {
        let groupValue = group.id;

        if (_.isEmpty(group.fieldMeta)) {
            return groupValue;
        }

        if (!_.isEmpty(group.fieldMeta.id) && !_.isEmpty(group.fieldMeta.module)) {
            groupValue = `<a href='#${group.fieldMeta.module}/${group.fieldMeta.id}'>${group.fieldMeta.value}</a>`;
            return groupValue;
        }

        if (group.fieldMeta.type) {
            let format;

            switch (group.fieldMeta.type) {
                case 'date':
                    if (!group.fieldMeta.showPlainText) {
                        format = app.user.getPreference('datepref');
                        format = app.date.convertFormat(format);

                        const value = group.fieldMeta.value;

                        if (value === '') {
                            groupValue = app.lang.get('LBL_NONE_STRING', 'Reports');
                        } else {
                            const dateValue = app.date(group.fieldMeta.value);
                            groupValue = dateValue.format(format);
                        }
                    }
                    break;
                case 'datetime':
                case 'datetimecombo':
                    if (!group.fieldMeta.showPlainText) {
                        format = app.user.getPreference('datepref') + ' ' + app.user.getPreference('timepref');
                        format = app.date.convertFormat(format);

                        const value = group.fieldMeta.value;

                        if (value === '') {
                            groupValue = app.lang.get('LBL_NONE_STRING', 'Reports');
                        } else {
                            const dateTimeValue = app.date(group.fieldMeta.value);
                            groupValue = dateTimeValue.format(format);
                        }
                    }
                    break;
                case 'enum':
                    if (_.isString(group.fieldMeta.module)) {
                        const moduleMeta = app.metadata.getModule(group.fieldMeta.module);
                        const fieldDef = moduleMeta.fields[group.fieldMeta.name];

                        if (_.isString(fieldDef.options)) {
                            const options = app.lang.getAppListStrings(fieldDef.options);
                            groupValue = options[groupValue];
                        } else if (_.isString(fieldDef.function) && !_.isUndefined(this.data.functionOptions) &&
                            !_.isUndefined(this.data.functionOptions[fieldDef.function]) &&
                            _.isString(group.fieldMeta.value)) {
                            groupValue = this.data.functionOptions[fieldDef.function][group.fieldMeta.value];
                        }
                    }
                    break;
            }
        }

        return groupValue;
    },

    /**
     * Build subgroups
     *
     * @param {Array} group
     */
    _buildSubgroups: function(group) {
        let keys = Object.keys(group);
        if (keys.length === 1) {
            group = group[keys[0]];
        }

        if (_.has(group, 'dataStructure')) {
            group = this._buildSubgroups(group.dataStructure);
        }

        let subgroups = [];

        if (_.has(group, 'records')) {
            subgroups.push(group);
            return subgroups;
        }

        for (let index in group) {
            let subgroup = group[index];

            let header = _.flatten(this._buildGroupHeader(subgroup));
            if (_.has(subgroup, 'header')) {
                header = [subgroup.header]
            }

            if(_.has(subgroup, 'dataStructure')) {
                subgroup = this._buildSubgroups(subgroup);
                if (_.isArray(subgroup)) {
                    for (let item of subgroup) {
                        if (_.has(item, 'header')) {
                            const _header = this._buildSubgroupHeader(item);
                            header.push(_header);
                        }
                        header = this._flattenHeader(header);

                        const records = item.records;
                        subgroups.push({header, records});
                        header = [];
                    }
                }
            }
            header = this._flattenHeader(header);

            if (_.has(subgroup, 'records')) {
                const records = subgroup.records;
                subgroups.push({header, records});
            }
        }

        return subgroups;
    },

    /**
     * Flattend an array
     *
     * @param {Array} header
     */
    _flattenHeader: function(header) {
        return _.chain(header)
        .flatten()
        .unique()
        .value();
    },

    /**
     * Return a header for a subgroup
     *
     * @param {Array|Object} subgroup
     */
    _buildSubgroupHeader: function(subgroup) {
        if (_.isArray(subgroup.header) && subgroup.header.length === 1 &&
            _.isEmpty(_.first(subgroup.header)) && _.has(subgroup, 'records')) {
            const countLbl = app.lang.get('LBL_COUNT', 'Reports');
            const groupSize = subgroup.records.length;

            const newHeader = [`${countLbl}: ${groupSize}`];

            return newHeader;
        } else {
            return subgroup.header;
        }
    },

    /**
     * Add groups in dom
     */
    _placeGroups: function() {
        if (this.context.get('rebuildData') === false) {
            return;
        }

        if (!this.data) {
            return;
        }

        let groupPartial = 'group';

        if (this.reportComplexity === this.complexities.medium) {
            groupPartial = 'group-simplified';
        }


        let placeholder = document.createDocumentFragment();

        for (let group of this.data.groups) {
            let data = Handlebars.helpers.partial(groupPartial, this, group, {hash: {}});
            placeholder.appendChild(this.createElementFromHTML(data.string));
        }

        _.defer(_.bind(function _append() {
            let list = this.$('.flex-list-view-content')[0];

            list.appendChild(placeholder);
            placeholder = null;

            this.context.trigger('report:data:table:loaded', false, 'table');
            this.context.trigger('report:panel-toolbar-visibility', true);
        }, this));

        if (this.reportComplexity === this.complexities.medium) {
            this.data = null;
        }
        this.context.set('rebuildData', false);
    },

    /**
     * Create Html element
     *
     * @param {string} htmlString
     * @return HTMLElement
     */
    createElementFromHTML: function(htmlString) {
        var div = document.createElement('div');
        div.innerHTML = htmlString.trim();

        // Change this to div.childNodes to support multiple top-level nodes.
        return div.firstChild;
    },

    /**
     * Sort a group
     *
     * @param {Event} evt
     */
    sortGroup: function(evt) {
        this.loading = true;
        this.context.trigger('report:data:table:loaded', this.loading, 'table');
        this.context.trigger('report:panel-toolbar-visibility', false);

        const eventTarget = this.$(evt.currentTarget);
        let orderBy = eventTarget.data('orderby');
        const fieldName = eventTarget.data('fieldname');
        const fieldMeta = _.filter(this._fields.visible, function(item) {
            return item.name === fieldName;
        })[0];

        this.context.set('rebuildData', true);

        // if no alternate orderby, use the field name
        if (!orderBy) {
            orderBy = fieldName;
        }

        if (_.isUndefined(this.orderBy)) {
            this.orderBy = {
                field: '',
                direction: 'desc'
            }
        }

        let tableKey = evt.currentTarget.dataset.tablekey;

        this.orderBy.table_key = tableKey.substr(0, tableKey.lastIndexOf(':'));
        this.orderBy.type = fieldMeta.type;
        this.orderBy.sort_on = fieldMeta.sort_on;
        this.orderBy.sort_on2 = fieldMeta.sort_on2;
        this.orderBy.rname = fieldMeta.rname;

        // if same field just flip
        if (orderBy === this.orderBy.field) {
            this.orderBy.direction = this.orderBy.direction === 'desc' ? 'asc' : 'desc';
        } else {
            this.orderBy.field = orderBy;
            this.orderBy.direction = 'desc';
        }

        //we get the last state
        if (this.useCustomReportDef && !_.isUndefined(this.customOrderBy)) {
            this.customOrderBy.table_key = tableKey.substr(0, tableKey.lastIndexOf(':'));
            this.customOrderBy.type = fieldMeta.type;
            this.customOrderBy.sort_on = fieldMeta.sort_on;
            this.customOrderBy.sort_on2 = fieldMeta.sort_on2;
            this.customOrderBy.rname = fieldMeta.rname;

            this._setCustomOrderBy();
        } else if (this.orderByLastStateKey) {
            app.user.lastState.set(this.orderByLastStateKey, this.orderBy);
        }

        this._loadReportData();
    },

    /**
     * Set header visibility
     *
     * @param {boolean} show
     */
    setHeaderVisibility: function(show) {
        if (_.has(this, 'layout') && _.has(this.layout, 'layout')) {
            const header = this.layout.layout.getComponent('report-panel-toolbar');

            if (!header) {
                return;
            }

            show ? header.show() : header.hide();
        }
    },

    /**
     * Set footer visibility
     *
     * @param {boolean} show
     */
    setFooterVisibility: function(show) {
        const footer = this.layout.getComponent('report-panel-footer');

        if (!footer) {
            return;
        }

        show ? footer.show() : footer.hide();
    },

    /**
     * Is empty panel
     *
     * @param {Object} data
     * @return {boolean}
     */
    _isEmptyPanel: function(data) {
        return data.groups.length === 0;
    },
})

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
 * @class View.Views.Reports.SummationView
 * @alias SUGAR.App.view.views.ReportsSummationView
 * @extends View.Views.Base.ReportsRowsColumnsView
 */
 ({
    extendsFrom: 'ReportsRowsColumnsView',

    pagination: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (!_.contains(this.plugins, 'ReportExport')) {
            this.plugins.push('ReportExport');
        }

        this.events = _.extend({}, this.events, {
            'click [data-action=export-csv]': 'exportToCsv',
            'click [data-action=show-simplified]': 'showSimplified',
        });

        this._super('initialize', [options]);
        this.template = app.template.getView('summation', this.module);
    },

    /**
     * @inheritdoc
     */
    _initProperties: function() {
        this._super('_initProperties');

        this.orderByKeys.push('summaryOrderBy');
    },

    /**
     * Build collection
     *
     * @param {Array} data
     */
    buildCollection: function(data) {
        this.data = data;
        const records = data.records;
        const header = data.header || this._fields.visible;
        const shouldRerender = true;

        this.reportComplexity = this._getReportComplexity(_.size(records), _.size(header));

        if (_.has(this, 'layout') && this.layout) {
            this.exportAccess = app.acl.hasAccess('export', this.layout.module) &&
                            app.utils.reports.hasAccessToAllReport(this.layout.model, 'export');
        }

        if (this.reportComplexity === this.complexities.medium && !this.viewingSimplified) {
            this.context.trigger('report:data:table:loaded', false, 'table');
            return;
        }

        this.startBuildCollection(data, !shouldRerender);
    },

    /**
     * Start to build the data collection
     *
     * @param {Array} data
     * @param {boolean} shouldRerender
     */
    startBuildCollection: function(data, shouldRerender) {
        this._initCollection();

        this.collection.models = data.records;
        this.collection.length = data.records.length;

        this.loading = false;
        this.context.trigger('report:data:table:loaded', this.loading, 'table');

        if (shouldRerender) {
            this.render();
        }

        const visibleEmptyPanel = this._isEmptyPanel(data) ||
                                    !this.layout ||
                                    !app.utils.reports.hasAccessToAllReport(this.layout.model);
        this._toggleEmptyPanel(visibleEmptyPanel);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (this.loading) {
            this._adjustLoadingWidgetSize('table');
        }

        if (this.reportComplexity === this.complexities.medium) {
            this.context.trigger('toggle-orientation-buttons', false);
        } else {
            this.context.trigger('toggle-orientation-buttons', true);
        }
    },
})

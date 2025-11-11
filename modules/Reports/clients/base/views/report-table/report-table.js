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
 * @class View.Views.Reports.ReportTableView
 * @alias SUGAR.App.view.views.ReportsReportTableView
 * @extends View.Views.Base.View
 */
({
    /**
     * Map used in rendering data tables
     */
    _dataTableMap: {
        'tabular': 'rows-columns',
        'detailed_summary': 'summation-details',
        'summary': 'summation',
        'Matrix': 'matrix',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();

        this._registerEvents();
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        this._dataTable = null;
        this._viewTypeMappingTable = {
            'tabular': 'rows-columns',
            'summation': 'summation',
            'detailed_summary': 'summation-details',
        };

        this._customCssClasses = this.model.get('customCssClasses');

        this._initModelProperties();
    },

    /**
     * Init data from model
     */
    _initModelProperties: function(forceRender) {
        this.reportType = 'tabular';

        if (this.context.get('previewMode')) {
            this.reportType = this.context.get('previewData').reportType;
        } else if (this.model && this.model.get('report_type')) {
            this.reportType = this.model.get('report_type');
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        if (_.has(this, 'layout') && _.has(this.layout, 'layout')) {
            this.listenTo(this.layout.layout, 'panel:collapse', this._collapseTable, this);
        }

        this.listenTo(this.context, 'report:build:data:table', this.rebuildDataTableList, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');
        this.renderList();
    },

    /**
     * Instantiate the right table to render
     */
    renderList: function() {
        const dataTableType = this._dataTableMap[this.reportType];
        this._disposeList();

        let layoutMeta = {
            type: dataTableType,
            context: this.context,
            module: 'Reports',
            useCustomReportDef: this.options.useCustomReportDef,
        };

        if (_.has(this, 'layout') && this.layout) {
            layoutMeta.layout = this.layout;
        }

        if (_.has(this, 'layout') && _.has(this.layout, 'layout')) {
            layoutMeta.panelWrapper = this.layout.layout;
        }

        if (!this.context.get('model').get('report_type')) {
            layoutMeta.model = this.model;
        }

        this._dataTable = app.view.createLayout(layoutMeta);

        this._dataTable.initComponents();
        this.$('.dataTablePlaceholder').append(this._dataTable.$el);
        this._dataTable.render();

        this.$('.dataTablePlaceholder').toggleClass('!overflow-y-auto', this.reportType === 'Matrix');
    },

    rebuildDataTableList: function(reportType) {
        this.model.set('report_type', reportType);

        this.reportType = this.model.get('report_type');

        this.renderList();
    },

    /*
     * Collapse/Maximize the table widget
     *
     * @param {boolean} collapse
     */
    _collapseTable: function(collapse) {
        if (collapse) {
            this.$el.hide();
        } else {
            this.$el.show();
        }
    },

    /**
     * Dispose subcomponent
     */
    _disposeList: function() {
        if (this._dataTable) {
            this._dataTable.dispose();
            this._dataTable = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeList();

        this._super('_dispose');
    },
});

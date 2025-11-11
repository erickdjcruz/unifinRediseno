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
 * @class View.Views.Reports.ReportPreviewHeaderView
 * @alias SUGAR.App.view.views.ReportsReportPreviewHeaderView
 */
({
    events: {
        'click [data-action="close-report-preview"]': 'closeDrawer',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        const previewData = this.layout ? this.layout.options.def.previewData : {};

        this._reportName = previewData.reportName ? previewData.reportName : app.lang.get('LBL_REPORT_DEFAULT_NAME');
        this._showQuery = previewData.showQuery;
        this._queries = previewData.tableData ? previewData.tableData.queries : [];
    },

    /**
     * Close Drawer
     */
    closeDrawer: function() {
        app.drawer.close();
    },
})

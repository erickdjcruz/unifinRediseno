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
 * @class View.Views.Base.ReportsReportExportView
 * @alias SUGAR.App.view.views.BaseReportsReportExportModalView
 * @extends View.View
 */
 ({
    events: {
        'click .close': 'closeModal',
        'click .export-pdf': 'exportToPdf',
        'click .export-csv': 'exportToCsv',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (!this.plugins) {
            this.plugins = [];
        }
        if (!_.contains(this.plugins, 'ReportExport')) {
            this.plugins.push('ReportExport');
        }
        this._super('initialize', [options]);
    },

    /**
     * Open Export Modal
     */
    openModal: function() {
        this.render();

        let modalEl = this.$('[data-content=report-export-modal]');

        modalEl.modal({
            backdrop: 'static'
        });
        modalEl.modal('show');

        modalEl.on('hidden.bs.modal', _.bind(function handleModalClose() {
            this.$('[data-content=report-export-modal]').remove();
        }, this));
    },

    /**
     * Close the modal and destroy it
     */
    closeModal: function() {
        this.dispose();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$('[data-content=report-export-modal]').remove();
        $('.modal-backdrop').remove();

        this._super('_dispose');
    },
});

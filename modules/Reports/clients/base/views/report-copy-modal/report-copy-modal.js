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
 * @class View.Views.Base.ReportsReportCopyModalView
 * @alias SUGAR.App.view.views.BaseReportsReportCopylModalView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click [data-type]': 'copyAs',
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
     * Init properties
     */
    _initProperties: function() {
        const forceRender = false;

        this._isReady = false;
        this._currentRecordType = null;
        this._initModelProperties(forceRender);
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        const forceRender = true;

        this.listenTo(
            this.model,
            'sync',
            this._initModelProperties,
            this,
            forceRender
        );
    },

    /**
     * Init data from model
     *
     * @param {boolean} forceRender
     */
    _initModelProperties: function(forceRender) {
        if (this.model && this.model.get('report_type')) {
            this._isReady = true;
            this._currentRecordType = this.model.get('report_type');
        }

        if (forceRender === true) {
            this.render();
        }
    },

    /**
     * Copy report as
     *
     * @param {jQuery} element
     */
    copyAs: function(element) {
        const reportType = element.currentTarget.dataset.type;

        const route = app.bwc.buildRoute('Reports', null, 'ReportCriteriaResults', {
            id: this.model.get('id'),
            page: 'report',
            mode: 'copyAs',
            newReportType: reportType,
        });

        this.closeModal();

        app.router.navigate(route, {trigger: true});
    },

    /**
     * Open Copy Modal
     */
    openModal: function() {
        this.render();

        let modalEl = this.$('[data-content=report-copy-modal]');

        modalEl.modal({
            backdrop: 'static'
        });
        modalEl.modal('show');

        modalEl.on('hidden.bs.modal', _.bind(function handleModalClose() {
            this.$('[data-content=report-copy-modal]').remove();
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
        this.$('[data-content=report-copy-modal]').remove();
        $('.modal-backdrop').remove();

        this._super('_dispose');
    },
});

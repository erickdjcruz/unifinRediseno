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
 * Layout for maps configuration
 *
 * @class View.Layouts.Base.AdministrationMapsLoggerDisplayView
 * @alias SUGAR.App.view.layouts.BaseAdministrationMapsLoggerDisplayView
 */
({
    extends: 'BasePaginationView',

    /**
     * Event listeners
     */
    events: {
        'click [data-action=paginate-prev]': 'clickPrevPage',
        'click [data-action=paginate-next]': 'clickNextPage',
        'click [data-action=log-details]': 'clickLogDetails',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initProperties();

        this.listenTo(this.context, 'retrieved:maps:logs', this.loadLogs, this);
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.paginationLimit = 25;
        this.defaultCurrentPage = 1;

        if (!this.model.get('totalPages')) {
            this.model.set('totalPages', 0);
        }

        if (!this.model.get('records')) {
            this.model.set('records', 0);
        }

        if (!this.model.get('offset')) {
            this.model.set('offset', 0);
        }

        if (!this.model.get('limit')) {
            this.model.set('limit', this.paginationLimit);
        }

        if (!this.model.get('currentPage')) {
            this.model.set('currentPage', this.defaultCurrentPage);
        }
    },

    /**
     * Retrieve the maps geocode service then display it
     */
    loadLogs: function() {
        if (this.disposed) {
            return;
        }

        const enabledModules = this.model.get('enabledLoggingModules');

        if (!enabledModules || !(_.isArray(enabledModules) && enabledModules.length > 0)) {
            this.model.set({
                'totalPages': 0,
                'records': 0,
                'limit': this.paginationLimit,
                'currentPage': this.defaultCurrentPage,
            });

            this.render();

            return;
        }

        const logsConfig = {
            startDate: this.model.get('maps_loggerStartdate'),
            logLevel: this.model.get('maps_loggerLevel'),
            modules: enabledModules,
            offset: this.model.get('offset'),
            limit: this.model.get('limit') || 0,
        };

        const url = App.api.buildURL('Administration/maps/logs', null, {}, logsConfig);

        app.alert.show('loading-logs', {
            level: 'process'
        });

        app.api.call('read', url, null, {
            success: _.bind(this.displayLogs, this),
            error: _.bind(this.errorLoadLogs, this),
        });
    },

    /**
     * Handling error for getting logs
     *
     * @param {Object} error
     */
    errorLoadLogs: function(error) {
        app.alert.dismiss('loading-logs');

        app.alert.show('error-load-logs', {
            level: 'error',
            messages: error.message,
        });
    },

    /**
     * Display the logs
     *
     * @param {Object} data
     */
    displayLogs: function(data) {
        app.alert.dismiss('loading-logs');

        this.model.set({
            totalPages: data.totalPages,
            records: data.records
        });

        this.render();
    },

    /**
     * Pagination back
     */
    clickPrevPage: function() {
        const currentPage = this.model.get('currentPage');
        const nextPageNr = currentPage - 1;
        const limit = this.model.get('limit');

        if (currentPage === 1) {
            return;
        }

        let nextOffset = ((nextPageNr) - 1) * limit;

        this.model.set({
            offset: nextOffset,
            currentPage: nextPageNr
        });

        this.loadLogs();
    },

    /**
     * Pagination forward
     */
    clickNextPage: function() {
        const currentPage = this.model.get('currentPage');
        const totalPages = this.model.get('totalPages');
        const limit = this.model.get('limit');

        if (currentPage === totalPages) {
            return;
        }

        const nextPageNr = currentPage + 1;

        const nextOffset = ((nextPageNr) - 1) * limit;

        this.model.set({
            currentPage: nextPageNr,
            offset: nextOffset
        });

        this.loadLogs();
    },

    /**
     * Get more details about a specific log
     *
     * @param {UIEvent} e
     */
    clickLogDetails: function(e) {
        this.disposeModal();

        const placeholderEl = e.currentTarget.closest('tr');

        if (!placeholderEl) {
            return;
        }

        const recordId = placeholderEl.getAttribute('data-id');
        const recordModule = placeholderEl.getAttribute('data-module');
        const records = this.model.get('records');

        if (!recordId || !recordModule) {
            return;
        }

        let detailedLogs = app.lang.getModString('LBL_MAPS_LOGGER_NO_LOGS_AVAILABLE', this.module);

        const targetRecord = _.chain(records)
                                .filter(record => record.parent_id === recordId)
                                .first()
                                .value();

        const errorMessageKey = 'error_message';

        if (targetRecord && _.has(targetRecord, errorMessageKey) && targetRecord[errorMessageKey]) {
            //set the error message also replace some characters that are coming from provider
            detailedLogs = targetRecord[errorMessageKey].replace(/[-[/\]{}()*+?".,\\^$|#\s]/g, ' ');
        }

        let mapsLoggerDetails = {
            name: 'maps-logger-details-modal',
            type: 'maps-logger-details-modal',
            recordId,
            recordModule,
            detailedLogs,
        };

        this.modal = app.view.createView(mapsLoggerDetails);

        $('body').append(this.modal.$el);

        this.modal.openModal();
    },

    /**
     * Dispose the modal view
     */
    disposeModal: function() {
        if (this.modal && this.modal.dispose) {
            this.modal.dispose();
            this.modal = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.disposeModal();
        this._super('_dispose');
    },
});

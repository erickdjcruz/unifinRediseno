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
 * @class View.Views.Base.CJFormBatchView
 * @alias SUGAR.App.view.views.BaseCJFormBatchView
 * @extends View.Views.Base.View
 */
({
    /**
     * @property {number} processedCount Number of processed elements.
     */
    processedCount: 0,

    /**
     * @property {number} totalCount Total Number of elements.
     */
    totalCount: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.batchChunk = app.CJBaseHelper.getBatchChunk();
        this.errorsArray = [];
    },

    /**
     * Provide a chunk of record to be updated
     *
     * @return {Array}
     */
    getRecordsToUpdate: function() {
        return this.recordsToUpdate.slice(this.processedCount, this.processedCount + this.batchChunk);
    },

    /**
     * Show batching modal with record saving message
     */
    startRecordSaving: function() {
        const message = app.lang.get('LBL_CJ_FORM_BATCH_ACTIVE_SMART_GUIDES_DETECTED');
        this.batchProgressModel = new Backbone.Model({
            isStopped: false
        });
        this.progressView = this._getProgressView();

        this.progressView.updateModal(message);
    },

    /**
     * First get activities then start batching process according to response
     *
     * @param {Object} params
     */
    startBatchingProcess: function(params) {
        if (_.isUndefined(params)) {
            return;
        }

        const url = app.api.buildURL(params.module, 'activitiesRSA', {
            id: params.record,
        });
        const message = app.lang.get('LBL_CJ_FORM_BATCH_PROCESSING_ACTIVE_SUGAR_ACTIONS');

        this.progressView.updateModal(message);

        app.api.call('read', url, null, {
            success: _.bind(this.getActivitiesRSASuccess, this),
            error: _.bind(this.apiErrorHandler, this)
        });
    },

    /**
     * Get activities target action success handler, set total record,
     * update modal and start batching process
     *
     * @param {Object} responseData
     */
    getActivitiesRSASuccess: function(responseData) {
        if (!_.isObject(responseData) || Object.keys(responseData).length === 0) {
            this.endBatchingProcess(true, false);
            return;
        }

        this.recordsToUpdate = _.toArray(responseData);
        this.totalCount = this.recordsToUpdate.length;
        const message = app.lang.get('LBL_CJ_FORM_BATCH_UPDATING_ACTIVE_SMART_GUIDES');

        this.progressView.reset();
        this.progressView.setTotalRecords(this.totalCount);
        this.progressView.updateModal(message);
        this.batchProgressModel.trigger('massupdate:start');

        this.updateRecords(this.getRecordsToUpdate());
    },

    /**
     * Error handler, populate error array and end batching process
     */
    apiErrorHandler: function() {
        this.errorsArray.push(true);
        this.endBatchingProcess();
    },

    /**
     * Update the provided chunk of record by calling the api
     *
     * @param {Array} records
     */
    updateRecords: function(records) {
        const url = app.api.buildURL('CJ_Forms', 'performTargetActions');
        const data = {
            records_to_update: records
        };

        app.api.call('create', url, data, {
            success: _.bind(this.performTargetActionsSuccess, this),
            error: _.bind(this.apiErrorHandler, this)
        });
    },

    /**
     * Perform target action success handler, process item,
     * add error if any, when completed reload journeys
     *
     * @param {Array} response
     */
    performTargetActionsSuccess: function(response) {
        this.batchProgressModel.trigger('massupdate:item:processed');

        this.processedCount += this.batchChunk;

        if (response && response.error) {
            this.errorsArray.push(response.error);
        }

        if (this.processedCount >= this.totalCount) {
            this.endBatchingProcess();
            return;
        }

        this.updateRecords(this.getRecordsToUpdate());
    },

    /**
     * End the form batching process, show success / failure alert
     * and reload related journeys
     *
     * @param {boolean} recordSaved
     * @param {boolean} batchingPerformed
     */
    endBatchingProcess: function(recordSaved = true, batchingPerformed = true) {
        this.batchProgressModel.trigger('massupdate:end');

        if (recordSaved) {
            this.handleAlerts(_.isEmpty(this.errorsArray), batchingPerformed);

            if (this.layout && _.isFunction(this.layout.convertComplete)) {
                this.layout.convertComplete('success', 'LBL_CONVERTLEAD_SUCCESS', true);
            }

            const journeyLayout = _.find(this.context.children, function(child) {
                return child && _.isEqual(child.get('link'), 'dri_workflows');
            });

            if (journeyLayout) {
                journeyLayout.trigger('reload_workflows');
            }
        }
    },

    /**
     * Handle alerts for success / failure and batching performed or not
     *
     * @param {boolean} success
     * @param {boolean} batchingPerformed
     */
    handleAlerts: function(success, batchingPerformed) {
        if (success) {
            const label = batchingPerformed ? 'LBL_CJ_FORM_BATCH_SUCCESS' : 'LBL_RECORD_SAVED';

            this.showAlert('batching-success', 'success', label, false);
        } else {
            let failureLabel = 'LBL_CJ_FORM_BATCH_FAILURE';

            if (_.isEqual(app.user.get('type').toLowerCase(), 'admin')) {
                failureLabel = 'LBL_CJ_FORM_BATCH_FAILURE_FOR_ADMIN';
            }

            this.showAlert('batching-failure', 'error', failureLabel, false);
            this.showAlert('record-success', 'success', 'LBL_RECORD_SAVED', true, 5000);
        }
    },

    /**
     * Show alert according to provides attributes
     *
     * @param {string} alertName
     * @param {string} level
     * @param {string} label
     * @param {boolean} autoClose
     * @param {number} autoCloseDelay
     */
    showAlert: function(alertName, level, label, autoClose, autoCloseDelay) {
        let alertDef = {
            level: level,
            messages: app.lang.get(label),
            autoClose: autoClose,
        };

        if (autoCloseDelay) {
            alertDef.autoCloseDelay = autoCloseDelay;
        }

        app.alert.show(alertName, alertDef);
    },

    /**
     * Create the Progress view and return it in the same layout.
     *
     * @return {Backbone.View}
     * @protected
     */
    _getProgressView: function() {
        const progressView = app.view.createView({
            context: this.context,
            type: 'cj-forms-batch-progress',
            layout: this.layout,
            model: this.batchProgressModel
        });

        this.layout._components.push(progressView);
        this.layout.$el.append(progressView.$el);

        progressView.render();
        return progressView;
    },
})

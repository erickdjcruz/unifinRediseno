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
 * @class View.Views.Base.CJFormsBatchProgressView
 * @alias SUGAR.App.view.views.BaseCJFormsBatchProgressView
 * @extends View.Views.Base.MassupdateProgressView
 */
({
    extendsFrom: 'MassupdateProgressView',

    plugins: ['editable'],

    /**
     * @inheritdoc
     */
    _labelSet: {
        TITLE: 'LBL_CJ_FORM_BATCH_TITLE',
    },

    /**
     * @property {number} processedCount Number of processed elements.
     */
    processedCount: 0,

    /**
     * @property {Done} failsCount Number of fails.
     */
    failsCount: 0,

    /**
     * @property {number} currentProgress Current progress percentage.
     */
    currentProgress: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.chunkCount = app.CJBaseHelper.getBatchChunk();
    },

    /**
     * @inheritdoc
     *
     * Use model to listen events insted of collection.
     */
    bindDataChange: function() {
        if (!this.model) {
            return;
        }

        this.listenTo(this, 'render', this.initHolders);

        this.listenTo(this.model, 'massupdate:start', this.showProgress);
        this.listenTo(this.model, 'massupdate:end', this.hideProgress);
        this.listenTo(this.model, 'massupdate:item:processed', this.onItemProcessed);
    },

    /**
     * @inheritdoc
     */
    initLabels: function() {
        this.LABELSET = this._labelSet;
    },

    /**
     * Reset view parameters.
     */
    reset: function() {
        this.processedCount = 0;
        this.failsCount = 0;
        this.totalRecord = 0;
    },

    /**
     * Set number of total elements for progress.
     *
     * @param {number} total Number of total records.
     */
    setTotalRecords: function(total) {
        this.totalRecord = total;
    },

    /**
     * Returns number of total records.
     *
     * @return {number} Number of total records.
     */
    getTotalRecords: function() {
        return this.totalRecord;
    },

    /**
     * Increments count of processed records.
     */
    incrementProgressSize: function() {
        this.processedCount = this.processedCount + this.chunkCount;
        // if processed count exceeds total count then set total count
        this.processedCount = this.processedCount > this.totalRecord ? this.totalRecord : this.processedCount;
    },

    /**
     * Returns number representing processed records count.
     *
     * @return {number} Progress size.
     */
    getProgressSize: function() {
        return this.processedCount;
    },

    /**
     * Handler for drawer `reset` event.
     *
     * @return {boolean}
     */
    _onDrawerReset: function() {
        this.showProgress();
        return false;
    },

    /**
     * @inheritdoc
     *
     * Setup handler for drawer to prevent closing it.
     * We need it b/ the operation an be too long and in this time
     * token can be expired.
     */
    showProgress: function() {
        app.drawer.before('reset', this._onDrawerReset, this);
        this._super('showProgress');
    },

    /**
     * @inheritdoc
     *
     * Dismiss alerts:
     * 1. `stop_confirmation` - confirmation on pause
     * 2. `check_error_message` - check errors status alert
     * Triggers `massupdate:end:completed` event on model.
     * Removes handler for drawer.
     */
    hideProgress: function() {
        app.drawer.offBefore('reset', this._onDrawerReset, this);
        this.hide();
        app.alert.dismiss('stop_confirmation');
        app.alert.dismiss('check_error_message');
        this.model.trigger('massupdate:end:completed');
    },

    /**
     * Called with new item is processed.
     *
     * Increments number of processed elements and
     * calls {@link View.MergeDuplicatesProgressView#updateProgress}.
     * Triggers `massupdate:item:processed:completed` event on model.
     */
    onItemProcessed: function() {
        this.incrementProgressSize();
        this.updateProgress();
        this.model.trigger('massupdate:item:processed:completed');
    },

    /**
     * Update current progress status.
     */
    updateProgress: function() {
        let size = this.getProgressSize();
        let percent = (size * 70 / this.totalRecord) + this.currentProgress;

        this.$holders.progressbar.css({'width': percent + '%'});
    },

    /**
     * Update modal message and progress.
     *
     * @param {string} message
     */
    updateModal: function(message) {
        this.currentProgress += 10;

        this.$holders.message.text(message);
        this.$holders.progressbar.css({'width': this.currentProgress + '%'});
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    },
})

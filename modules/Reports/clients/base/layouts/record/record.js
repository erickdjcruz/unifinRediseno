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
 * @class View.Layouts.Base.Reports.RecordLayout
 * @alias SUGAR.App.view.layouts.BaseReportsRecordLayout
 * @extends View.Views.Base.Layout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initSavedReportsMeta();
    },

    /**
     * Initialize Saved Reports Meta
     */
    _initSavedReportsMeta: function() {
        if (!this.model.get('id')) {
            return;
        }

        const reportId = this.model.get('id');
        const params = {
            track: true,
            trackAction: 'detailview',
        };
        const url = app.api.buildURL('Reports/activeSavedReport', reportId, {}, params);

        app.api.call('read', url, null, {
            success: _.bind(this._storeSavedReportsMeta, this),
        });
    },

    /**
     * Store Saved Reports
     *
     * @param {Array} savedReports
     */
    _storeSavedReportsMeta: function(savedReports) {
        if (this.disposed) {
            return;
        }

        this.context.set('savedReportsMeta', savedReports);
        this.context.trigger('report:savedReportsMeta:sync:complete');

        this._manageReportDefChanged(savedReports);
    },

    /**
    * Take care of report def changes
    *
    *  @param {Object} reportData
    */
    _manageReportDefChanged: function(reportData) {
        const lastChangeInfo = reportData.lastChangeInfo;
        const seenDate = lastChangeInfo.lastReportSeenDate;
        const modifiedDate = lastChangeInfo.lastReportModifiedDate;

        // check if the report has been modified since the last time I saw the report
        if (!moment(seenDate).isBefore(moment(modifiedDate))) {
            return;
        }

        // reset state
        this._resetUserState();

        // show notification if you're not the one that changed the report
        const currentUserId = lastChangeInfo.currentUserId;
        const modifiedUserId = lastChangeInfo.modifiedUserId;

        if (currentUserId !== modifiedUserId) {
            this._showNotification();
        }
    },

    /**
     * Reset last state
     */
    _resetUserState: function() {
        const moduleReportId = this.module + ':' + this.context.get('modelId');
        const orderByLastStateKey = app.user.lastState.buildKey('order-by', 'record-list', moduleReportId);

        app.user.lastState.remove(orderByLastStateKey);
    },

    /**
     * Show report def changed notification
     */
    _showNotification: function() {
        app.alert.show('modify_since_last_refresh', {
            level: 'info',
            messages: app.lang.get('LBL_UPDATES_SINCE_LAST_REFRESH', 'Reports'),
            autoClose: true,
        });
    },
})

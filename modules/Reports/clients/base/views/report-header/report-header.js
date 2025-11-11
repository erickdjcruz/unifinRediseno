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
 * @class View.Views.Base.Reports.ReportHeaderView
 * @alias SUGAR.App.view.views.BaseReportsReportHeaderView
 * @extends View.View
 */
({
    events: {
        'click .report-header-padding [data-bs-toggle="dropdown"]': 'toggleActionButton',
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
        this._currentUrl = Backbone.history.getFragment();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'button:refresh:click', this.refreshClicked, this);
        this.listenTo(this.context, 'button:edit:click', this.editClicked, this);
        this.listenTo(this.context, 'button:copy:click', this.copyClicked, this);
        this.listenTo(this.context, 'button:schedule:click', this.scheduleListClicked, this);
        this.listenTo(this.context, 'button:export:click', this.exportClicked, this);
        this.listenTo(this.context, 'button:delete:click', this.deleteClicked, this);
        this.listenTo(this.context, 'button:details:click', this.detailsClicked, this);
        this.listenTo(this.model, 'sync', this.toggleRigtsideButton, this);

        //event register for preventing actions
        // when user escapes the page without confirming deleting
        app.routing.before('route', this.beforeRouteDelete, this);
        $(window).on('beforeunload.delete' + this.cid, _.bind(this.warnDeleteOnRefresh, this));
    },

    /**
     * Hide/Show action buttons of the dropdown menu
     */
    toggleActionButton: function() {
        if (this.disposed) {
            return;
        }

        const restrictionActions = ['export'];
        const dropdownMenu = this.$('.report-header-padding [data-menu="dropdown"]');

        _.each(restrictionActions, function(typeOfAction) {
            if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model, typeOfAction)) {
                typeOfAction = typeOfAction.replace(/^\w/, c => c.toUpperCase());
                const actionButton = dropdownMenu.find(`span:contains("${typeOfAction}")`);

                if (actionButton.length > 0) {
                    actionButton.parent().hide();
                }
            }
        }, this);
    },

    /**
     * Hide/Show rightside buttons
     */
    toggleRigtsideButton: function() {
        if (this.disposed) {
            return;
        }

        if (!this.layout || !app.utils.reports.hasAccessToAllReport(this.layout.model)) {
            this.$('.report-header-padding').hide();
        }
    },

    /**
     * Trigger the refresh of the report.
     */
    refreshClicked: function() {
        this.context.trigger('report:panel-toolbar-visibility', false);
        this.context.trigger('report:refresh');
    },

    /**
     * Go to the Reports Wizard Edit page
     *
     * @param {Data.Bean} model Selected row's model.
     * @param {RowActionField} field
     */
    editClicked: function(model, field) {
        if (!model || !_.has(model, 'id')) {
            return;
        }

        if (this.context.get('permissionsRestrictedReport')) {
            app.alert.show('data-table-error', {
                level: 'error',
                title: app.lang.get('LBL_NO_ACCESS', 'Reports'),
                messages: app.lang.getModuleName('Reports') + ': ' + model.id,
            });

            return;
        }

        const route = app.bwc.buildRoute('Reports', null, 'ReportCriteriaResults', {
            id: model.id,
            page: 'report',
            mode: 'edit',
            sidecarEdit: 'edit'
        });

        app.router.navigate(route, {trigger: true});
    },

    /**
     * Go to the Reports Wizard Edit page
     *
     * @param {Data.Bean} model Selected row's model.
     * @param {RowActionField} field
     */
    scheduleListClicked: function(model, field) {
        if (!model || !_.has(model, 'id')) {
            return;
        }

        const filterOptions = new app.utils.FilterOptions().config({
            initial_filter_label: model.get('name'),
            initial_filter: 'by_report',
            filter_populate: {
                'report_id': [model.get('id')]
            }
        });

        app.controller.loadView({
            module: 'ReportSchedules',
            layout: 'records',
            filterOptions: filterOptions.format()
        });
    },

    /**
     * Event handler for open copy modal.
     */
    copyClicked: function() {
        const modal = app.view.createView({
            name: 'report-copy-modal',
            type: 'report-copy-modal'
        });

        $('body').append(modal.$el);

        modal.openModal();
    },

    /**
     * Event handler for export click event.
     */
    exportClicked: function() {
        const reportExport = app.view.createView({
            name: 'report-export-modal',
            type: 'report-export-modal',
            context: this.context
        });

        $('body').append(reportExport.$el);

        reportExport.openModal();

    },

    /**
     * Delete current record
     *
     * @param {Data.Bean} model Selected row's model.
     */
    deleteClicked: function(model) {
        if (!model || !_.has(model, 'id')) {
            return;
        }

        this.warnDelete(model);
    },

    /**
     * Event handler for openening details module.
     */
    detailsClicked: function() {
        const modal = app.view.createView({
            name: 'report-detail-modal',
            type: 'report-detail-modal'
        });

        $('body').append(modal.$el);

        modal.openModal();
    },

    /**
     * Render
     *
     * Update button label
     */
    _render: function() {
        this._super('_render');
    },

    /**
     * Pre-event handler before current router is changed.
     *
     * @return {boolean} `true` to continue routing, `false` otherwise.
     */
    beforeRouteDelete: function() {
        if (this._modelToDelete) {
            this.warnDelete(this._modelToDelete);
            return false;
        }

        return true;
    },

    /**
     * Popup dialog message to confirm delete action
     *
     * @param {Data.Bean} model Selected row's model.
     */
    warnDelete: function(model) {
        let self = this;
        this._modelToDelete = model;

        self._targetUrl = Backbone.history.getFragment();
        //Replace the url hash back to the current staying page
        if (self._targetUrl !== self._currentUrl) {
            app.router.navigate(self._currentUrl, {trigger: false, replace: true});
        }

        app.alert.show('delete_confirmation', {
            level: 'confirmation',
            messages: self.getDeleteMessages().confirmation,
            onConfirm: _.bind(self.deleteModel, self),
            onCancel: function() {
                self._modelToDelete = false;
            }
        });
    },

    /**
     * Delete the model once the user confirms the action
     */
    deleteModel: function() {
        let self = this;

        self.model.destroy({
            //Show alerts for this request
            showAlerts: {
                'process': true,
                'success': {
                    messages: self.getDeleteMessages().success
                }
            },
            success: function() {
                const redirect = self._targetUrl !== self._currentUrl;

                self.context.trigger('record:deleted', self._modelToDelete);

                self._modelToDelete = false;

                if (redirect) {
                    self.unbindBeforeRouteDelete();
                    //Replace the url hash back to the current staying page
                    app.router.navigate(self._targetUrl, {trigger: true});
                    return;
                }

                app.router.navigate(self.module, {trigger: true});
            }
        });
    },

    /**
     * Formats the messages to display in the alerts when deleting a record.
     *
     * @return {Object} The list of messages.
     * @return {string} return.confirmation Confirmation message.
     * @return {string} return.success Success message.
     *
     * @return {string}
     */
    getDeleteMessages: function() {
        let messages = {};
        const model = this.model;
        const name = Handlebars.Utils.escapeExpression(this._getNameForMessage(model)).trim();
        const context = app.lang.getModuleName(model.module).toLowerCase() + ' "' + name + '"';

        messages.confirmation = app.utils.formatString(
            app.lang.get('NTC_DELETE_CONFIRMATION_FORMATTED', this.module),
            [context]
        );

        messages.success = app.utils.formatString(app.lang.get('NTC_DELETE_SUCCESS'), [context]);
        return messages;
    },

    /**
     * Retrieves the name of a record
     *
     * @param {Data.Bean} model The model concerned.
     *
     * @return {string} name of the record.
     */
    _getNameForMessage: function(model) {
        return app.utils.getRecordName(model);
    },

    /**
     * Popup browser dialog message to confirm delete action
     *
     * @return {string} The message to be displayed in the browser dialog.
     */
    warnDeleteOnRefresh: function() {
        if (this._modelToDelete) {
            return this.getDeleteMessages().confirmation;
        }
    },

    /**
     * Detach the event handlers for warning delete
     */
    unbindBeforeRouteDelete: function() {
        app.routing.offBefore('route', this.beforeRouteDelete, this);
        $(window).off('beforeunload.delete' + this.cid);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');

        this.unbindBeforeRouteDelete();
    },
})

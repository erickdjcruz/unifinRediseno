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
 * @class View.Views.Base.ReportsReportDetailModalView
 * @alias SUGAR.App.view.views.BaseReportsReportDetailModalView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .close': 'closeModal',
        'click [class="modal-backdrop in"]': 'closeModal',
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
        this._createReportDetails(false);
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        const forceRender = true;

        this.listenTo(
            this.context,
            'report:savedReportsMeta:sync:complete',
            this._createReportDetails,
            this,
            forceRender
        );

        this.listenTo(
            this.model,
            'sync',
            this._createReportDetails,
            this,
            forceRender
        );
    },

    /**
     * Open Detail Modal
     */
    openModal: function() {
        this.render();

        let modalEl = this.$('[data-content=report-details-modal]');

        modalEl.modal({
            backdrop: 'static'
        });
        modalEl.modal('show');

        modalEl.on('hidden.bs.modal', _.bind(function handleModalClose() {
            this.$('[data-content=report-details-modal]').remove();
        }, this));
    },

    /**
     * Prepare Report Details data to be displayed
     *
     * @param {boolean} forceRender
     */
    _createReportDetails: function(forceRender) {
        if (!this.model || !this.context || !this.model.dataFetched) {
            return;
        }

        const encodedReportContent = this.model.get('content');

        if (!encodedReportContent) {
            return;
        }

        const reportContent = JSON.parse(encodedReportContent);

        const reportNameKey = 'report_name';
        const reportTypeKey = 'report_type';

        const reportType = this.model.get(reportTypeKey);

        const type = app.lang.getAppListStrings('dom_report_types')[reportType];
        const assignedUser = this.model.get('assigned_user_name');
        const reportSchedules = this._createReportSchedules();
        const reportName = reportContent[reportNameKey];
        const modules = this._getReportModules(reportContent);
        const displayColumns = this._getReportDisplayColumns(reportContent);
        const groupBy = this._getReportGroupBy(reportContent);
        const teams = this._getReportTeams();
        const summaryColumns = this._getReportSummaryColumns(reportContent);

        this.reportDetails = {
            reportName,
            modules,
            displayColumns,
            groupBy,
            reportSchedules,
            teams,
            assignedUser,
            summaryColumns,
            type,
        };

        if (forceRender === true) {
            this.render();
        }
    },

    /**
     * Create a report schedules list
     *
     * @return {Array}
     */
    _createReportSchedules: function() {
        let reportSchedules = [];
        const savedReportsMeta = this.context.get('savedReportsMeta');
        const nextRunKey = 'next_run';

        if (_.isEmpty(savedReportsMeta) || !_.has(savedReportsMeta, 'scheduler') ||
            _.size(savedReportsMeta.scheduler) < 1) {
            return reportSchedules;
        }

        reportSchedules = app.utils.deepCopy(savedReportsMeta.scheduler);

        _.each(reportSchedules, function map(item) {
            item[nextRunKey] = app.date(item[nextRunKey]).formatUser(false);
        }, this);

        return reportSchedules;
    },

    /**
     * Create a full modules list
     *
     * @param {Object} reportContent
     *
     * @return {Array}
     */
    _getReportModules: function(reportContent) {
        let fullTableList = [];
        const appModuleListString = app.lang.getAppListStrings('moduleList');
        const fullTableListKey = 'full_table_list';

        if (_.size(reportContent[fullTableListKey]) < 1) {
            return fullTableList;
        }

        fullTableList = _.map(reportContent[fullTableListKey], function each(item) {
            if (!_.has(item, 'name') || !item.name) {
                if (_.has(item, 'module') && item.module && !_.has(fullTableList, item.module)) {
                    const moduleKey = item.module;
                    let moduleName = moduleKey;

                    if (_.has(appModuleListString, moduleName)) {
                        moduleName = appModuleListString[moduleName];
                    }

                    return moduleName;
                }
            } else {
                if (!_.has(fullTableList, item.name)) {
                    const moduleName = item.name;

                    return moduleName;
                }
            }
        }, this);

        return fullTableList;
    },

    /**
     * Create the display columns data to be displayed
     *
     * @param {Object} reportContent
     *
     * @return {string}
     */
    _getReportDisplayColumns: function(reportContent) {
        const displayColumnsKey = 'display_columns';

        const displayColumns = _.chain(reportContent[displayColumnsKey])
            .map(function each(item) {
                return item.label;
            })
            .join(', ')
            .value();

        return displayColumns;
    },

    /**
     * Create the group by data to be displayed
     *
     * @param {Object} reportContent
     *
     * @return {Array}
     */
    _getReportGroupBy: function(reportContent) {
        let fullTableList = [];
        const orderByKey = 'group_defs';

        if (_.size(reportContent[orderByKey]) < 1) {
            return fullTableList;
        }

        fullTableList = _.map(reportContent[orderByKey], function each(item) {
            const groupName = item.name;

            if (_.has(item, 'label')) {
                return item.label;
            }

            return groupName;
        }, this);

        return fullTableList;
    },

    /**
     * Create the teams data to be displayed
     *
     * @return {Array}
     */
    _getReportTeams: function() {
        const teamNames = this.model.get('team_name');

        const teamsList = _.chain(teamNames)
                            .sortBy(function sort(team) {
                                return team.primary !== true;
                            })
                            .map(this._createTeamName, this)
                            .value();

        return teamsList;
    },

    /**
     * Create team name to be displayed
     *
     * @param {Object} team
     *
     * @return {string}
     */
    _createTeamName: function(team) {
        let teamName = '';

        const firstNameKey = 'name';
        const secondNameKey = 'name_2';

        const firstName = team[firstNameKey];
        const secondName = team[secondNameKey];
        const primaryLabel = app.lang.get('LBL_COLLECTION_PRIMARY');

        if (team[secondNameKey]) {
            teamName = `${firstName} ${secondName}`;
        } else {
            teamName = `${firstName}`;
        }

        if (team.primary) {
            teamName += ` (${primaryLabel})`;
        }

        return teamName;
    },

    /**
     * Create the group by data to be displayed
     *
     * @param {Object} reportContent
     *
     * @return {Array}
     */
    _getReportSummaryColumns: function(reportContent) {
        let fullTableList = [];
        const orderByKey = 'summary_columns';

        if (_.size(reportContent[orderByKey]) < 1) {
            return fullTableList;
        }

        fullTableList = _.map(reportContent[orderByKey], function each(item) {
            const groupName = item.name;

            if (_.has(item, 'label')) {
                return item.label;
            }

            return groupName;
        }, this);

        return fullTableList;
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
        this.$('[data-content=report-details-modal]').remove();
        $('.modal-backdrop').remove();

        this._super('_dispose');
    },
});

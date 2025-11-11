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
 * @class View.Layouts.Base.Reports.ReportPanelLayout
 * @alias SUGAR.App.view.layouts.BaseReportsReportPanelLayout
 * @extends View.Views.Base.Layout
 */
({
    className: 'flex w-full multi-line-list-view report-panel',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._initContextFields();

        this._registerEvents();
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        this._endpoint = 'panel';
        this._splitScreen = null;
        this._filterPanel = null;
    },

    /**
     * Initialize model persistent options
     */
    _initContextFields: function() {
        let fields = this.context.get('fields');

        const requiredFields = [
            'chart_type',
            'content',
            'report_type',
            'teams',
            'team_name',
            'assigned_user_name',
            'is_template',
        ];

        fields = _.union(fields, requiredFields);

        this.context.set('fields', fields);
    },

    /**
     * Set this._panels when component is initialized.
     */
    _initPanels: function() {
        if (!this.model.get('id')) {
            const config = {};

            this._buildLayout(config);

            return;
        }

        let url = app.api.buildURL('Reports/' + this._endpoint, this.model.get('id'));

        app.api.call('read', url, {}, {
            success:  _.bind(function(data) {
                if (this.disposed) {
                    return;
                }

                if (_.isEmpty(data)) {
                    data = {};
                } else {
                    data = JSON.parse(data);
                }

                this._buildLayout(data);
            }, this),
            error: function(error) {
                app.alert.show('error_while_retrieve', {
                    level: 'error',
                    messages: ['ERR_HTTP_500_TEXT_LINE2']
                });
                app.logger.error(error);
            }
        });
    },

    /**
     * Set the visibility of the chart
     *
     * @param {string} reportType
     * @param {string} chartType
     * @param {Object} config
     *
     * @return {Object}
     */
    _manageChartVisibility: function(reportType, chartType, config) {
        if (reportType === 'tabular' || chartType === 'none') {
            config.hidden = 'firstScreen';
        }

        return config;
    },

    /**
     * Manage the visibility of the components on preview mode
     *
     * @param {Object} config
     * @return {Object}
     */
    _managePreviewComponentsVisibility: function(config) {
        const previewData = this.context.get('previewData');
        const reportType = previewData.reportType;
        const chartType = previewData.chartType;

        const reportConfig = this._manageChartVisibility(reportType, chartType, config);

        return reportConfig;
    },

    /**
     * Manage the visibility of the components
     *
     * @param {Object} config
     * @return {Object}
     */
    _manageComponentsVisibility: function(config) {
        const reportType = this.model.get('report_type');
        const chartType = this.model.get('chart_type');

        const reportConfig = this._manageChartVisibility(reportType, chartType, config);

        return reportConfig;
    },

    /**
     * Setup layout components depending on preview mode
     *
     * @param {Object} config
     */
    _buildLayout: function(config) {
        let reportConfig = {};

        if (this.context.get('previewMode')) {
            reportConfig = this._managePreviewComponentsVisibility(config);
        } else {
            reportConfig = this._manageComponentsVisibility(config);
        }

        this._setupComponents(reportConfig);
    },

    /**
     * Register panel related events
     */
    _registerEvents: function() {
        this.listenToOnce(this.model, 'sync', this._initPanels);
        this.listenTo(this.context, 'split-screens-resized', this.handleSave);
        this.listenTo(this.context, 'change:reportComplexity', this.reportComplexityChanged);
    },

    /**
     * Disable the resizer when the report is in simplified view
     *
     * @param {Context} context
     * @param {Integer} reportComplexity
     */
    reportComplexityChanged: function(context, reportComplexity) {
        if (!_.isUndefined(this.context.get('reportComplexities')) &&
            reportComplexity === this.context.get('reportComplexities').medium) {
            this._splitScreen.toggleResizer(false);
        } else {
            this._splitScreen.toggleResizer(true);
        }
    },

    /**
     * Setup panels splitLayoutConfig
     *
     * @param {Object} splitLayoutConfig
     */
    _setupComponents: function(splitLayoutConfig) {
        this._createSplitLayout(splitLayoutConfig);
        this._createFiltersLayout();

        this.context.trigger('report-layout-config-retrieved', splitLayoutConfig);
    },

    /**
     * Create split layout
     *
     * @param {Object} splitLayoutConfig
     */
    _createSplitLayout: function(splitLayoutConfig) {
        if (this._splitScreen) {
            this._splitScreen.dispose();

            this._splitScreen = null;
        }

        this._splitScreen = app.view.createLayout({
            name: 'resizable-split-screens',
            layout: this,
            context: this.context,
            meta: {
                name: 'resizable-split-screens',
                isLoading: false,
                components: [
                    {
                        layout: 'report-chart',
                    },
                    {
                        layout: 'report-table',
                    },
                ],
                secondScreenStyle: {
                    overflow: 'hidden',
                },
                handleDisabled: this.context.get('previewMode'),
            },
        });

        this._splitScreen.initComponents();
        this._splitScreen.render();

        this.$el.html(this._splitScreen.$el);

        this.context.trigger('split-screens-config-change', splitLayoutConfig, true);
    },

    /**
     * Create filters layout
     */
    _createFiltersLayout: function() {
        if (this._filterPanel) {
            this._filterPanel.dispose();

            this._filterPanel = null;
        }

        this._filterPanel = app.view.createLayout({
            name: 'report-filters',
            layout: this,
            context: this.context,
        });

        this._filterPanel.initComponents();
        this._filterPanel.render();

        this.$el.append(this._filterPanel.$el);
    },

    /**
     * Saves current model metadata
     *
     * @param {Object} resizeConfig
     */
    handleSave: function(resizeConfig) {
        if (!app.acl.hasAccessToModel('read', this.model)) {
            this.model.unset('updated');
            return;
        }

        let url = app.api.buildURL('Reports/panel', this.model.get('id'));

        if (_.isUndefined(resizeConfig)) {
            return;
        }

        const data = {
            layoutConfig: resizeConfig,
        };

        app.api.call('update', url, data, {
            success:  _.bind(function(data) {
                if (!this.disposed) {
                    this.model.unset('updated');
                }
            }, this),
            error: function(error) {
                app.alert.show('error_while_save', {
                    level: 'error',
                    title: app.lang.get('ERR_INTERNAL_ERR_MSG'),
                    messages: ['ERR_HTTP_500_TEXT_LINE1', 'ERR_HTTP_500_TEXT_LINE2']
                });
                app.logger.error(error);
            }
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this._splitScreen instanceof app.view.Layout) {
            this._splitScreen.dispose();
            this._splitScreen = null;
        }
        if (this._filterPanel instanceof app.view.Layout) {
            this._filterPanel.dispose();
            this._filterPanel = null;
        }

        this._super('_dispose');
    },
})

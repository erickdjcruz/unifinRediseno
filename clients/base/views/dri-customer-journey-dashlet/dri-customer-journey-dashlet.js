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
 * @class View.views.BaseDriCustomerJourneyDashletView
 * @alias SUGAR.App.view.views.BaseDriCustomerJourneyDashletView
 * @extends View.View
 */
({
    plugins: ['Dashlet'],

    colors: {
        completed: '#00d427',
        not_completed: '#ffc231',
        in_progress: '#0075c7',
        not_started: '#ccc',
        cancelled: '#ffc231',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._initProperties();
        this._super('initialize', [options]);
        this._noAccessTemplate = app.template.get(`${ this.name }.noaccess`);
    },

    /**
     * Initialize properties
     */
    _initProperties: function() {
        this.chartData = new Backbone.Model();
        this.isFetching = false;
        this.selected = null;
    },

    /**
     * {@inheritdoc}
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(this.model, 'customer_journey_widget_reloading', this.loadData);
        this.listenTo(this.model, 'customer_journey:active-cycle:click', this.setActiveCycle);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!app.user.hasAutomateLicense()) {
            this.$el.html(this._noAccessTemplate());
            return;
        }

        this._super('_render');
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        if (this.isFetching) {
            return;
        }
        this.isFetching = true;

        let url = app.api.buildURL(
            this.model.module,
            'customer-journey/chart-data',
            {
                id: this.model.get('id'),
            },
            {
                selected: this.selected,
            }
        );

        app.api.call('read', url, null, {
            success: _.bind(this.loadChartDataSuccess, this),
            complete: _.bind(this.loadChartDataComplete, this, options),
        });
    },

    /**
     * Set selected variable to incoming id after validations
     *
     * @param {string} id
     */
    setActiveCycle: function(id) {
        if (!id || this.selected !== id) {
            this.selected = id;
            this.loadData();
        }
    },

    /**
     * Render chart on successfully loading data
     *
     * @param {Object} data
     */
    loadChartDataSuccess: function(data) {
        if (!data) {
            return {};
        }

        this.selected = data.id;
        let dashletToolbar = this.layout.getComponent('dashlet-toolbar');

        if (dashletToolbar) {
            // manually set the icon class to spiny
            this.$('[data-action=loading]')
                .removeClass(dashletToolbar.cssIconDefault)
                .addClass(dashletToolbar.cssIconRefresh);
        }

        let chartData = {
            properties: [{
                labels: 'value',
                type: 'donut chart'
            }],
            values: data.stages,
        };
        let colorList = [];

        _.each(data.stages, function(stage) {
            colorList.push(this.colors[stage.state]);
        }, this);

        let chartParams = {
            hole: `${ Math.floor(data.progress * 100) }%`,
            chartType: 'donutChart',
            show_legend: false,
            show_title: false,
            colorOverrideList: colorList,
            tooltip: {
                label: this.getCustomTooltipLabel,
            }
        };

        _.defer(_.bind(function() {
            this.chartData.set({rawChartData: chartData, rawChartParams: chartParams});
        }, this));
    },

    /**
     * Customize tooltip label
     *
     * @param {Object} chart
     * @param {Object} tooltip
     * @return {Array} Array of strings
     */
    getCustomTooltipLabel: function(chart, tooltip) {
        return [`${chart.labels.tooltip.count}: ${chart.rawData.values[tooltip.dataIndex].count}`,
        `${chart.labels.tooltip.percent}: ${Math.round(chart.rawData.values[tooltip.dataIndex].percentage)}%`];
    },

    /**
     * Complete on loading data from api
     */
    loadChartDataComplete: function(opts) {
        this.isFetching = false;
        if (opts && _.isFunction(opts.complete)) {
            opts.complete();
        }
    },

    /**
     * {@inheritdoc}
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    },
})

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
 * @class View.views.BaseDriCustomerJourneyMomentumDashletView
 * @alias SUGAR.App.view.views.BaseDriCustomerJourneyMomentumDashletView
 * @extends View.View
 */
({
    plugins: ['Dashlet', 'Chart', 'CssLoader'],

    className: 'customer-journey-momentum-chart-wrapper p-0',
    loaded: false,
    data: null,
    selected: null,

    tplErrorMap: {
        ERROR_INVALID_LICENSE: 'invalid-license',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.listenTo(this.model, 'customer_journey_widget_reloading', this.loadData);
        this.listenTo(this.model, 'customer_journey:active-cycle:click', this.setActiveCycle);

        this.listenTo(this.layout, 'dashlet:collapse', this.renderChart);

        // initialize the chart
        this.chart = sucrose.charts
            .gaugeChart()
            .showLabels(false)
            .showTitle(false)
            .tooltips(false)
            .showPointer(true)
            .showLegend(false)
            .colorData('data')
            .ringWidth(50)
            .direction('ltr')
            .maxValue(100);

        this._noAccessTemplate = app.template.get(`${ this.name }.noaccess`);
    },

    /**
     * @param {string} id
     */
    setActiveCycle: function(id) {
        if (!id || this.selected != id) {
            this.selected = id;
            this.loadData();
        }
    },

    /**
     * @inheritdoc
     */
    unbind: function() {
        this._super('unbind');
        this.stopListening();
    },

    /**
     * Renders the chart
     */
    renderChart: function() {
        if (!this.isChartReady()) {
            return;
        }

        d3sugar
            .select(`svg#${this.cid}`)
            .datum(this.chartCollection)
            .transition()
            .duration(500)
            .call(this.chart);

        this.chart_loaded = _.isFunction(this.chart.update);

        this.displayNoData(!this.chart_loaded);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        if (this.meta.config) {
            return;
        }

        this.loaded = false;
        this.data = null;

        if (this.$el) {
            this.$el.children().fadeTo('slow', 0.7);
        }

        let url = app.api.buildURL(
            this.model.module,
            'customer-journey/momentum-chart',
            {
                id: this.model.get('id'),
            },
            {
                selected: this.selected,
            }
        );

        app.api.call('read', url, null, {
            success: _.bind(this.loadCompleted, this),
            error: _.bind(this.loadError, this),
            complete: options ? options.complete : null,
        });
    },

    /**
     * @return {boolean}
     */
    hasChartData: function() {
        return !!this.data;
    },

    /**
     * @param {Object} data
     */
    loadCompleted: function(data) {
        this.loaded = true;
        this.error = '';
        this.template = app.template.get(this.name);

        this.evaluateResult(data);
        if (!this.disposed) {
            this.render();
        }
    },

    /**
     * @param {Object} error
     */
    loadError: function(error) {
        this.loaded = true;

        if (this.disposed) {
            return;
        }

        this.$el.children().fadeTo('slow', 1);

        let tpl = this.tplErrorMap[error.message] || 'error';
        this.error = error;
        this.template = app.template.get(`${this.name}.${tpl}`);

        this.render();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!app.user.hasAutomateLicense()) {
            this.$el.html(this._noAccessTemplate());
            return;
        }

        if (this.$el) {
            this.$el.children().fadeTo('slow', 1);
        }

        this._super('_render');

        if (!this.meta.config) {
            if (this.loaded) {
                this.displayNoData(!this.data);
            } else {
                this.displayNoData(true);
            }
        }
    },

    /**
     * Processes the chart data
     *
     * @param {Object} data
     */
    evaluateResult: function(data) {
        if (!data) {
            return;
        }

        this.total = data.ratio;
        this.data = data;
        this.selected = data.id;

        this.chartCollection = {
            data: data.data,
            properties: {
                title: data.name,
                value: data.ratio,
                values: data.values,
                colorLength: data.data.length,
            }
        };
    },
});

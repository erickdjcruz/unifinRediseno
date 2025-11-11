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
({
    plugins: ['Dashlet'],

    /**
     * Holds report data from the server's endpoint once we fetch it
     */
    chartData: undefined,

    /**
     * We'll use this property to bind loadData function for event
     */
    refresh: null,

    /**
     * Flag to store if we're already making a request. Use this to avoid obvious and unnecessary
     * chart re-renders from both `useful` and `notuseful` getting updated at once.
     * @property {boolean}
     */
    isFetching: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.chartData = new Backbone.Model();
        this._super('initialize', [options]);
        this.refresh = _.bind(this.loadData, this);
        this.listenTo(app.controller.context.get('model'), 'change:useful', this.refresh);
        this.listenTo(app.controller.context.get('model'), 'change:notuseful', this.refresh);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        var currModel = app.controller.context.get('model'),
            model = currModel.clone(),
            opts = options || {},
            self = this;

        if (this.isFetching) {
            return;
        }
        this.isFetching = true;

        model.fetch({
            success: function(model) {
                var dt = self.layout.getComponent('dashlet-toolbar'),
                    useful = model.get('useful') || '0',
                    notuseful = model.get('notuseful') || '0';
                if (dt) {
                    // manually set the icon class to spiny
                    self.$('[data-action=loading]')
                        .removeClass(dt.cssIconDefault)
                        .addClass(dt.cssIconRefresh);
                }

                useful = parseInt(useful, 10);
                notuseful = parseInt(notuseful, 10);

                // correcting values for pie chart,
                // because pie chart not support all zero values.
                if (0 === useful && 0 === notuseful) {
                    self.chartData.set({rawChartData: {values: []}});
                    return;
                }
                let chartData = {
                    properties: [{
                        labels: 'value',
                        type: 'donut chart'
                    }],
                    values: [
                        {
                            label: [app.lang.get('LBL_USEFUL', 'KBContents')],
                            values: [useful],
                        },
                        {
                            label: [app.lang.get('LBL_NOT_USEFUL', 'KBContents')],
                            values: [notuseful],
                        },
                    ],
                };

                let chartParams = {
                    hole: `${parseInt(useful * 100 / (notuseful + useful))}%`,
                    donutLabelsOutside: true,
                    chartType: 'donutChart',
                    show_legend: false,
                    show_title: false,
                    colorOverrideList: [
                        '#00ba83', // @green
                        '#fa374f', // @red
                    ],
                };
                _.defer(_.bind(function() {
                    self.chartData.set({rawChartData: chartData, rawChartParams: chartParams});
                }, this));
            },
            complete: () => {
                this.isFetching = false;
                if (opts && _.isFunction(opts.complete)) {
                    opts.complete();
                }
            }
        });
    },

    /**
     * @inheritdoc
     *
     * Dispose listeners for 'change:useful' and 'change:notuseful' events.
     */
    dispose: function() {
        this.stopListening(app.controller.context.get('model'), 'change:useful', this.refresh);
        this.stopListening(app.controller.context.get('model'), 'change:notuseful', this.refresh);
        this._super('dispose');
    }
})

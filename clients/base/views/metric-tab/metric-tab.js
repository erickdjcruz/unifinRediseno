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
 * @class View.Views.Base.MetricTab
 * @alias SUGAR.App.view.views.BaseMetricTab
 * @extends View.View
 */
({
    /**
     * Attach toggle active function to clicking a metric
     */
    events: {
        'click .kpi-metric-tab': 'toggleActive',
        'click .delete-metric-btn': 'deleteClicked',
        'click .hide-btn': 'hideBtnClicked'
    },

    /**
     * Determines if field is active
     */
    active: false,

    /**
     * List of metric tab badges
     * @type Array
     */
    badges: [],

    activeMetricKey: null,

    /**
     * Determines  if logged in user has admin access
     *
     * @property {boolean}
     */
    hasAdminAccess: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setActiveMetricKey();
        this.active = ((this.model.get('active') || this.getActiveMetricId()) === this.meta.id);
        if (this.active) {
            this.setActiveMetric(this.meta, true);
        }
        this.hasAdminAccess = this.checkAdminAccess();
    },

    /**
     * Checks Metrics ACLs to see if the User is a system admin
     * or if the user has admin role for the Metrics module
     */
    checkAdminAccess: function() {
        let acls = app.user.getAcls().Metrics;
        let isAdmin = !_.has(acls, 'admin');
        let isSysAdmin = (app.user.get('type') === 'admin');

        return (isSysAdmin || isAdmin);
    },

    /**
     * Sets active metric key
     */
    setActiveMetricKey: function() {
        this.activeMetricKey = 'sticky-metric-item-' +
            this.layout.layout.layout.meta.metric_context +
            '-' +
            this.layout.layout.layout.meta.metric_module;
    },

    /**
     * Gets the lastState key
     * @returns {string}
     */
    getActiveMetricKey: function() {
        return this.activeMetricKey;
    },

    /**
     * Gets the active metric id from the last state
     * @param {string} last state key [optional]
     * @return {string} active metric id
     */
    getActiveMetricId: function(key) {
        return app.user.lastState.get(key || this.getActiveMetricKey()).id;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (app.metadata.getModule(this.module).consoleTabBadges && this.active && this.$('.kpi-metric-badges')[0]) {
            this.setBadges();
        }
    },

    /**
     * Request data, format and output metric tab badges
     */
    setBadges: function() {
        let self = this;
        let qty = [];
        let metric = _.findWhere(this.context.metrics, {id: this.meta.id});
        let metricFilter = (metric) ? metric.filter_def : {};

        this.badges = this.layout.layout.layout.meta.badges;

        $.each(this.badges, function(i, badge) {
            let params = {filter: _.union(metricFilter, badge.filter)};
            let url = app.api.buildURL(this.module, 'count', {}, params);

            app.api.call('read', url, null, {
                success: _.bind(function(data) {
                    if (self.disposed) {
                        return;
                    }
                    qty[i] = self.formatBadgeData(data.record_count);

                    if (Object.keys(qty).length === self.badges.length) {
                        for (let key in qty) {
                            if (qty[key] <= 0) {
                                // don't show the badge
                                continue;
                            }
                            $('<span/>',{
                                text: qty[key],
                                class: 'badge kpi-metric-badge-color-' + key,
                                title: app.lang.get(self.badges[key].tooltip),
                                rel: 'tooltip',
                            }).appendTo(self.$('.kpi-metric-badges'));
                        }

                        let badgeWidth = Math.ceil(self.$('.kpi-metric-badges')[0].offsetWidth);
                        if (badgeWidth) {
                            self.$('.kpi-metric-label').width(148 - badgeWidth);
                        }
                    }
                }, this),
            });
        });
    },

    /**
     * Sets a metric as an active metric
     * @param metric {Object} the metric that needs to be set as active
     */
    setActiveMetric: function(metric, silent) {
        silent = _.isUndefined(silent) ? false : silent;
        if (metric) {
            let activeMetricIndex = _.findIndex(this.context.metrics, (ctxMetric) => ctxMetric.id === metric.id);
            app.user.lastState.set(this.getActiveMetricKey(), {
                id: metric.id,
                index: activeMetricIndex
            });
            this.model.set('active', metric.id, {silent: silent});
        }
    },

    /**
     * Switches the active metric when a metric is clicked.
     */
    toggleActive: function() {
        if (!this.active) {
            const callback = () => {
                this.setActiveMetric(this.meta);
                this.context.trigger('active:metric:changed', this.meta.id);
            };

            app.events.trigger('active:metric:change', callback);
        }
    },

    /**
     * Rounding and cutting badges data for output
     *
     * @param {number} value
     * @return {mixed}
     */
    formatBadgeData: function(value) {
        if (!_.isNumber(value)) {
            value = 0;
        }

        if (value < 1000) {
            return value;
        }

        value /= 1000;
        return value.toFixed(1) + 'K';
    },

    /**
     * Handles delete clicked event on the metric tab
     */
    deleteClicked: function() {
        if (this.context.metrics && this.meta.id) {
            let metricAttr = this.getMetricAttributes(this.meta.id);
            let metricBean = app.data.createBean(this.module, {
                id: this.meta.id
            });

            if (!_.isEmpty(metricAttr)) {
                metricBean.set(metricAttr);
                metricBean.setSyncedAttributes(metricAttr);

                // Set the module name to correctly format the Confirmation message string
                metricBean.module = 'Metrics';
                // this property will be used in success callback to navigate back to console
                this.context.set('navigateBack', true);

                if (metricBean) {
                    let recordView = app.view.createView({
                        type: 'record',
                        model: metricBean,
                        module: 'Metrics',
                        context: this.context
                    });
                    recordView.warnDelete(metricBean);
                }
            }
        }
    },

    /**
     * Handles hide clicked event on the metric tab
     */
    hideBtnClicked: function() {
        let hiddenTab = this.layout.getComponent(this.name);
        this.context.trigger('click:metric:hide', hiddenTab);
    },

    /**
     * Gets metric attributes from the metadata for a given id
     * @param id {string} id of the metric whose attributes are needed
     * @return {Object} attributes of the found metric
     */
    getMetricAttributes: function(id) {
        let metricAttr = _.filter(this.context.metrics, function(metric) {
            return metric.id === id;
        });

        return metricAttr ? metricAttr[0] : {};
    },
})

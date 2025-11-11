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
 * @class View.Layouts.Base.MetricTabsLayout
 * @alias SUGAR.App.view.layouts.BaseMetricTabsLayout
 * @extends View.Layout
 */
({
    className: 'metric-tabs w-full',
    plugins: ['Dropdown'],

    /**
     * Attach toggle active function to clicking a metric in overflow dropdown
     */
    events: {
        'click .overflow-dropdown-metric-item': 'selectDropdownMetricItem'
    },

    _catalog: {},

    _$moreMetricsDD: undefined,

    activeMetricKey: null,

    isRefreshProcess: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.getHiddenMetrics();
        this.setActiveMetricKey();

        this.showLoader = true;
        this.hasVisibleMetrics = true;
        this.hasHiddenMetrics = false;

        this.before('render', function() {
            if (!this.hasVisibleMetrics) {
                let emptyTemplate = app.template.getLayout('metric-tabs.empty-ribbon');
                this.$el.html(emptyTemplate({hasHiddenMetrics: this.hasHiddenMetrics}));
                this.context.trigger('metric:empty');
                return false;
            }
        });

        this.setLoaderCount();
        // This stuff of code from class View.Layouts.Base.HeaderLayout because
        // this layout was removed from 12.3 version
        var resize = _.bind(this.resize, this);
        $(window)
            .off('resize.header')
            .on('resize.header', resize);

        app.events.on('focusdrawer:close', this.handleFocusDrawerClose, this);
    },

    /**
     * Gets the metrics that are not visible to the user on the metric ribbon
     */
    getHiddenMetrics: function() {
        let kpiMetricsLayout = this.layout && this.layout.layout ? this.layout.layout : {};

        if (!_.isEmpty(kpiMetricsLayout)) {
            let metricContext = kpiMetricsLayout.meta ? kpiMetricsLayout.meta.metric_context : '';
            let metricModule = kpiMetricsLayout.meta ? kpiMetricsLayout.meta.metric_module : '';

            if (_.isEmpty(metricContext) || _.isEmpty(metricModule)) {
                return;
            }

            let url = app.api.buildURL('Metrics', 'hidden', null, {
                metric_context: metricContext,
                metric_module: metricModule
            });
            app.api.call('GET', url, null, {
                success: _.bind(function(results) {
                    this.context.hiddenMetrics = results;
                    this.hasHiddenMetrics = !_.isEmpty(results);
                    if (this.isHideAction) {
                        this.isHideAction = false;
                        this.layout.loadData();
                        this.loadListData();
                    }
                }, this),
                error: _.bind(function() {
                    this.isHideAction = false;
                }, this)
            });
        }
    },

    /**
     * Sets the number of loaders to show on the metric ribbon
     */
    setLoaderCount: function() {
        let ribbonWidth = $(window).width() - 24;

        this.loaderCount = (Math.floor(ribbonWidth / 164) > 1) ? Math.floor(ribbonWidth / 164) : 1;
    },

    /**
     * Sets active metric key
     */
    setActiveMetricKey: function() {
        this.activeMetricKey = 'sticky-metric-item-' +
            this.layout.layout.meta.metric_context +
            '-' +
            this.layout.layout.meta.metric_module;
    },

    /**
     * Gets the lastState key
     * @returns {string}
     */
    getActiveMetricKey: function() {
        return this.activeMetricKey;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        //Tells the parent layout that the selected active metric has changed
        this.stopListening(this.model);
        this.listenTo(this.model, 'change:active', this._handleActiveMetricsChange);

        this.stopListening(this.context, 'record:deleted')
            .stopListening(this.context, 'active:metric:changed')
            .stopListening(this.context, 'click:metric:hide');
        this.listenTo(this.context, 'record:deleted', this.handleMetricDelete);
        this.listenTo(this.context, 'active:metric:changed', this.handleMetricChange);
        this.listenTo(this.context, 'click:metric:hide', this.handleMetricHide);

        this.stopListening(this.collection);
        this.listenTo(this.collection, 'data:sync:complete', this.dataSyncComplete);
        this.listenTo(this.context, 'list:paginate:start', this.showLoading);
        this.listenTo(this.context, 'filter:fetch:start', this.showLoading);
    },

    /**
     * Sets a metric as an active metric
     * @param metric {Object} the metric that needs to be set as active
     */
    setActiveMetric: function(metric) {
        if (metric) {
            let activeMetricIndex = _.findIndex(this.context.metrics, (ctxMetric) => ctxMetric.id === metric.id);
            app.user.lastState.set(this.getActiveMetricKey(), {
                id: metric.id,
                index: activeMetricIndex
            });
            this.model.set('active', metric.id);
        }
    },

    /**
     * Handles setting new active metric or reloading of data once a metric has been deleted
     * @param model {Object} deleted model
     */
    handleMetricDelete: function(model) {
        const deletedModel = this.getComponent(model.get('name'));

        let nextActiveMetric = {};

        // if an active metric is deleted, set the new active metric
        if (deletedModel.active) {
            nextActiveMetric = this.getNextActiveMetric(model);
        }

        this.getHiddenMetrics();
        this.layout.loadData();

        // if a new active metric is chosen, then update last state and set new active metric on the model
        if (!_.isEmpty(nextActiveMetric)) {
            this.setActiveMetric(nextActiveMetric);
        }
    },

    /**
     * Handle event of hiding metric tab
     * @param {Object} metricToHide The metric that is being hidden
     */
    handleMetricHide: function(metricToHide) {
        metricToHide.$el.remove();
        let nextActiveMetric = {};
        if (metricToHide.active) {
            nextActiveMetric = this.getNextActiveMetric(metricToHide.meta);
        }

        let visibleList = _.filter(_.map(this.context.metrics, function(metric) { return metric.id; }), function(id) {
            return id !== metricToHide.meta.id;
        }, this);

        let hiddenList = _.map(this.context.hiddenMetrics, function(metric) { return metric.id; });

        if (!_.includes(hiddenList, metricToHide.meta.id)) {
            hiddenList.push(metricToHide.meta.id);
        }

        let kpiMetricLayout = this.layout && this.layout.layout ? this.layout.layout : {};

        if (kpiMetricLayout) {
            this.isHideAction = true;
            let configAttrs = {
                visible_list: visibleList || this.context.metrics,
                hidden_list: hiddenList,
                metric_module: kpiMetricLayout.meta.metric_module,
                metric_context: kpiMetricLayout.meta.metric_context
            };

            // if a new active metric is chosen, then update last state and set new active metric on the model
            if (!_.isEmpty(nextActiveMetric)) {
                this.setActiveMetric(nextActiveMetric);
            }
            this._saveConfig(configAttrs);
        }
    },

    /**
     * Calls the context model save and saves the config model in case
     * the default model save needs to be overwritten
     *
     * @protected
     */
    _saveConfig: function(configAttrs) {
        if (configAttrs) {
            let url = app.api.buildURL('Metrics', 'config');

            app.api.call('create', url,
                {
                    metric_context: configAttrs.metric_context,
                    metric_module: configAttrs.metric_module,
                    visible_list: configAttrs.visible_list || this.context.metrics,
                    hidden_list: configAttrs.hidden_list || this.context.hiddenMetrics
                },
                {
                    success: _.bind(function() {
                        this.getHiddenMetrics();
                    }, this)
                });
        }
    },

    /**
     * Handle event of switching metric tab
     */
    handleMetricChange: function() {
        this.loadListData();
    },

    /**
     * Load list data based on metric filter
     */
    loadListData: function() {
        this.showLoading();
        this.collection.trigger('list:paginate:loading');

        let metricId = this.getActiveMetricId();
        let bean = app.data.createBean('Metrics', {
            id: metricId
        });
        bean.fetch({
            success: _.bind(function(metric) {
                if (metric.attributes.filter_def === null) {
                    metric.attributes.filter_def = [];
                }
                app.events.trigger('metric:initialize', metric.attributes);
                if (this.context) {
                    this.context.trigger('filter:apply');
                }
            }, this)
        });
    },

    /**
     * Show loading block
     */
    showLoading: function() {
        $('.multi-line-list-view', this.$el.closest('.dashlets')).addClass('data-loader');
    },

    /**
     * Update metric counters
     */
    updateMetricsCounts: function() {
        let kpiMetricLayout = this.layout && this.layout.layout ? this.layout.layout : {};
        let metricModule = kpiMetricLayout.meta.metric_module;
        // Combine all requests into one API call to improve performance
        let url = app.api.buildURL(null, 'bulk');
        let params = {
            requests: []
        };
        _.each(this.context.metrics, function(metric) {
            // temporarily show cached count if available
            if (!_.isUndefined(metric.recordCount)) {
                this.$('#ribbon-' + metric.id).find('.kpi-metric-number-row').text(metric.recordCount);
            }
            if (metric.module || metricModule) {
                let url = app.api.buildURL(metric.metric_module || metricModule, 'count', {},
                    {filter: metric.filter_def}).substr(4);
                params.requests.push({
                    url: url,
                    method: 'GET'
                });
            }
        }, this);
        let callbacks = {
            success: _.bind(function(results) {
                if (this.disposed) {
                    return;
                }
                _.each(results, function(data, index) {
                    let recordCount = data.contents.record_count;
                    let metricId = this.context.metrics[index].id;
                    this.context.metrics[index].recordCount = recordCount;
                    this.$('#ribbon-' + metricId).find('.kpi-metric-number-row').text(recordCount);
                }, this);
            }, this)
        };

        // Initiate the bulk API call
        app.api.call('create', url, params, callbacks);
    },

    /**
     * Gets a new active model
     * @param model {Object} removed active metric model
     * @return {Object} new active metric model
     */
    getNextActiveMetric: function(model) {
        let metricsList = this.context.metrics;
        let metricsLen = metricsList.length;
        let nextModel = [];
        let currentIndex = '';

        // if model is defined, this happens when hiding or deleting a metric tab
        if (model) {
            // if current metric is the last metric in the list
            // and the current metric is the one being hidden or deleted then return
            if (metricsLen === 1 && metricsList[0].id === model.id) {
                return nextModel;
            }

            currentIndex = _.findIndex(this.context.metrics, (metric) => metric.id === model.id);

            // if current metrics is last in the list then get previous metric
            // else get the next metric from the list
            if (currentIndex === metricsLen - 1) {
                nextModel = metricsList[currentIndex - 1];
            } else {
                nextModel = metricsList[currentIndex + 1];
            }
        } else { // else the model is not defined, this happens when a metric tab is made inactive
            currentIndex = app.user.lastState.get(this.getActiveMetricKey()).index || 0;

            // if current index is out of bounds then get the last metric in the list
            if (currentIndex > metricsLen - 1) {
                nextModel = _.last(metricsList);
            } else {
                nextModel = metricsList[currentIndex];
            }
        }

        return nextModel;
    },

    /**
     * Rendering a view after a metric has become active
     *
     * @private
     */
    _handleActiveMetricsChange: function() {
        if (!this.isHideAction) {
            this.render();
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.setLoaderCount();
        this._super('_render');

        if (this.showLoader) {
            let loaderTemplate = app.template.getLayout('metric-tabs.metric-loader');
            this.$el.html(loaderTemplate({loaderCount: this.loaderCount}));
        }

        let contextMetrics = this.context && this.context.metrics ? this.context.metrics : [];

        if (contextMetrics && contextMetrics.length) {
            this.hasVisibleMetrics = true;
            let activeMetricId = this.getActiveMetricId();
            let activeMetric = _.find(this.context.metrics, function(metric) {
                return metric.id === activeMetricId;
            });

            // if active metric is no longer present in the list,
            // we get the next valid metric and set it as new active metric
            if (_.isUndefined(activeMetric)) {
                this.setActiveMetric(this.getNextActiveMetric());
            }

            this._resetRibbon();
        }

        let refreshButton = this.$el.closest('.dashlets').find('.filter-refresh-button');
        refreshButton.off('click');
        refreshButton.on('click', _.bind(function(e) {
            e.stopPropagation();
            this.isRefreshProcess = true;
            this.loadListData();
        }, this));
    },

    /**
     * Refresh metric tabs if any records have been updated
     * after the focus drawer is closed.
     * @param {Array} updatedModels
     */
    handleFocusDrawerClose: function(updatedModels) {
        if (!_.isEmpty(updatedModels)) {
            this.isRefreshProcess = true;
            this.loadListData();
        }
    },

    /**
     * @inheritdoc
     *
     * @param {Object} component.
     * @protected
     */
    _placeComponent: function(component) {
        if (!this._$moreMetricsDD) {
            return;
        }

        let isOverflow = component.meta && component.meta.short;
        let tpl = this._getListTemplate(component);
        let $content = $(tpl({metric: component.meta.id, overflow: isOverflow})).append(component.el);

        this._catalog[component.meta.id] = this._catalog[component.meta.id] || {};

        if (isOverflow) {
            $content.addClass('hidden');
            this._catalog[component.meta.id].short = $content;
            this._$moreMetricsDD.find('[data-container="ribbon-overflow"]').append($content);
        } else {
            this._catalog[component.meta.id].long = $content;
            this.$('[data-action="more-metrics"]').before($content);
        }
    },

    /**
     * Ribbon reassembly
     *
     * @protected
     */
    _resetRibbon: function() {
        this._components = [];
        this._catalog = {};
        this.$el.html(this.template(this, this.options));

        // cache the more-dropdown now
        this._$moreMetricsDD = this.$('[data-action="more-metrics"]');

        if (this.context && this.context.metrics && this.context.metrics.length) {
            let activeKey = this.getActiveMetricKey();
            if (!app.user.lastState.get(activeKey)) {
                app.user.lastState.set(activeKey, {
                    id: this.context.metrics[0].id,
                    index: 0
                });
            }

            // remove the loaders before adding the metric tabs
            this.showLoader = false;

            this._addTabs(this.context.metrics);
            this.resize();

            let activeMetricId = this.getActiveMetricId(activeKey);

            if (activeMetricId && this.$('#ribbon-' + activeMetricId).is(':hidden')) {
                this.replaceMetric(activeMetricId);
            }
        }
    },

    /**
     * Filling the ribbon with tabs
     *
     * @param {Array} metrics List of metrics to add to the ribbon
     * @private
     */
    _addTabs: function(metrics) {
        _.each(metrics, function(metric) {
            let newTabElement = this._addTab(metric);
            newTabElement.long.render();
            newTabElement.short.render();
        }, this);

        if (this.isRefreshProcess) {
            this.refreshMetrics();
            this.isRefreshProcess = false;
        } else {
            this.updateMetricsCounts();
        }

        let activeMetric = this.getActiveMetricId();
        let hiddenMetricsArray = [];
        if (!_.isEmpty(this.context.hiddenMetrics)) {
            hiddenMetricsArray = this.context.hiddenMetrics.map(el => el.id);
        }
        if (hiddenMetricsArray.includes(activeMetric)) {
            if (!_.isEmpty(metrics[0])) {
                this.setActiveMetric(metrics[0]);
            }
        }
    },

    /**
     * Filling the metric tab with metric data
     *
     * @param {Object} metric metric data
     * @return {Object} metric tab
     * @private
     */
    _addTab: function(metric) {
        let tab = {};

        let def = {
            view: {
                type: 'metric-tab',
                short: false,
                id: metric.id,
                name: metric.name,
                number: this.context.number,
            }
        };
        tab.long = this.createComponentFromDef(def);
        this.addComponent(tab.long, def);

        def = {
            view: {
                type: 'metric-tab',
                short: true,
                id: metric.id,
                name: metric.name,
                number: this.context.number,
            }
        };
        tab.short = this.createComponentFromDef(def);
        this.addComponent(tab.short, def);

        // trigger an event if this metric tab is active, multi line list view depends on this to get metric data
        if (metric.id == this.getActiveMetricId()) {
            app.events.trigger('metric:ready', metric);
        }

        return tab;
    },

    /**
     * Getting template for list element
     *
     * @param {Object} component
     * @private
     */
    _getListTemplate: function(component) {
        return app.template.getLayout('metric-tabs.list', component.meta.id) ||
            app.template.getLayout('metric-tabs.list');
    },

    dataSyncComplete: function() {
        $('.multi-line-list-view', this.$el.closest('.dashlets')).removeClass('data-loader');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.events.off('focusdrawer:close', this.handleFocusDrawerClose, this);
        $(window).off('resize.header');
        this.stopListening();
        this._super('_dispose');
    },

    /**
     * Resize the metric-tabs to the specified width and move the extra metrics
     * to the `more-metrics` drop down.
     */
    resize: function() {
        let width = $(window).width() - 95; // minus kebab width

        if (!this._$moreMetricsDD) {
            return;
        }

        if (!width || width <= 0) {
            return;
        }

        let $metricList = this.$('[data-container="metric-tabs"]');
        let $dropdown = this._$moreMetricsDD.find('[data-container="ribbon-overflow"]');

        if ($metricList.outerWidth(true) >= width) {
            this.removeMetricsFromList($metricList, width);
        } else {
            this.addMetricsToList($metricList, width);
        }

        this.moreMetricsCount = $dropdown.children('li').not('.hidden').length;
        this.$('#more-count').text(this.moreMetricsCount + ' More');
        this._$moreMetricsDD.toggleClass('hidden', $dropdown.children('li').not('.hidden').length === 0);
        // adjust dropdown menu position
        let $dropdownMenu = this._$moreMetricsDD.find('.dropdown-menu');
        $dropdownMenu.css('left', $metricList.width() - 108);
    },

    /**
     * Move metrics from the dropdown to the list to fit the specified width.
     *
     * @param {jQuery} $metrics The jQuery element that contains all the
     *   metrics.
     * @param {number} width The current width we have available.
     */
    addMetricsToList: function($metrics, width) {
        let $dropdown = this._$moreMetricsDD.find('[data-container="ribbon-overflow"]');
        let $toHide = $dropdown.children('li').not('.hidden').first();
        let currentWidth = $metrics.outerWidth(true);

        while (currentWidth < width && $toHide.length > 0) {
            this.toggleMetric($toHide.data('metric'), true);

            $toHide = $dropdown.children('li').not('.hidden').first();

            currentWidth = $metrics.outerWidth(true);
        }

        if (currentWidth >= width) {
            this.removeMetricsFromList($metrics, width);
        }
    },

    /**
     * Move metrics from the list to the dropdown to fit the specified width
     *
     * @param {jQuery} $metrics The jQuery element that contains all the
     *   metrics.
     * @param {number} width The current width we have available.
     */
    removeMetricsFromList: function($metrics, width) {
        let $toHide = this._$moreMetricsDD.prev();

        while ($metrics.outerWidth(true) > width && $toHide.length > 0) {
            this.toggleMetric($toHide.data('metric'), false);
            $toHide = $toHide.prev();
            // "More" dropdown should be displayed here to calculate its width value on the next step
            this._$moreMetricsDD.toggleClass('hidden', false);
        }
    },

    /**
     * @param {string} metricId The metricId of metric you want to turn on/off.
     * @param {boolean} state `true` to show it on mega menu, `false`
     *   otherwise. If no state given, will toggle.
     * @return {Object}
     */
    toggleMetric: function(metricId, state) {
        if (!this._catalog[metricId].short) {
            state = !_.isUndefined(state) ? !state : undefined;
            this._catalog[metricId].long.toggleClass('hidden', state);
            return this;
        }

        // keep it in sync
        let newState = this._catalog[metricId].short.toggleClass('hidden', state).hasClass('hidden');
        this._catalog[metricId].long.toggleClass('hidden', !newState);

        return this;
    },

    /**
     * Changing metric visibility
     *
     * @param {string} metricId The metricId of metric you want to turn on/off.
     */
    replaceMetric: function(metricId) {
        let $ribbon = this.$('[data-container="metric-tabs"]');
        let $toHide = $ribbon.children('li:not(.hidden)');
        let metricToHide = ($toHide[$toHide.length - 2].dataset.metric);
        this.$('#ribbon-' + metricToHide).toggleClass('hidden', true);
        this.$('#overflow-' + metricToHide).toggleClass('hidden', false);
        this.$('#ribbon-' + metricId).toggleClass('hidden', false);
        this.$('#overflow-' + metricId).toggleClass('hidden', true);
    },

    /**
     * Switches the active metric when a metric is clicked.
     *
     * @param {Oblect} event The metric you want to turn on/off.
     */
    selectDropdownMetricItem: function(event) {
        let metricId = event.target.closest('li').dataset.metric;
        this.setActiveMetric({id: metricId});
        this.loadListData();
    },

    refreshMetrics: function() {
        let selectedMetric = this.context.metrics.filter(function(metric) {
            return metric.id === this.getActiveMetricId();
        }, this);

        let unSelectedMetrics = this.context.metrics.filter(function(metric) {
            return metric.id !== this.getActiveMetricId();
        }, this);

        let metrics = _.union(selectedMetric, unSelectedMetrics);
        $.each(metrics, function(i, metric) {
            let url = app.api.buildURL(metric.metric_module, 'count', {}, {
                filter: metric.filter_def,
                id: metric.id
            });

            let $metric = $('#ribbon-' + metric.id + ' .kpi-metric');
            $metric.addClass('metric-loader');
            $metric.attr('data-url', url);

            app.api.call('read', url, null, {
                success: _.bind(function(data) {
                    this.count = data.record_count;
                }, this),
                complete: _.bind(function(data) {
                    $('.kpi-metric[data-url="' + data.params.url + '"] .kpi-metric-number-row').text(this.count);
                    $('.kpi-metric[data-url="' + data.params.url + '"]').removeClass('metric-loader');
                }, this),
            });
        }, this);
    },

    /**
     * Gets the active metric id from the last state
     * @param {string} last state key [optional]
     * @return {string} active metric id
     */
    getActiveMetricId: function(key) {
        let metricId;
        let activeMetric = app.user.lastState.get(key || this.getActiveMetricKey());

        // if no active metric is retrieved default to the first metric as active
        if (_.isUndefined(activeMetric)) {
            metricId = this.context.metrics[0].id || '';
        } else {
            metricId = activeMetric.id;
        }
        return metricId;
    }
})

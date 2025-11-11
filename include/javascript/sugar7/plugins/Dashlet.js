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
(function (app) {
    app.events.on("app:init", function () {
        var sync = function (method, model, options) {
                options = app.data.parseOptionsForSync(method, model, options);
                var callbacks = app.data.getSyncCallbacks(method, model, options),
                    path = (this.dashboardModule === 'Home' || model.id) ? this.apiModule : this.apiModule + '/' + this.dashboardModule;
                app.api.records(method, path, model.attributes, options.params, callbacks);
            },
            Dashlet = app.Bean.extend({
                sync: sync,
                apiModule: 'Dashboards',
                module: 'Home'
            });

        app.plugins.register('Dashlet', 'view', {

            /**
             * Is used to generate max-height for dashlets.
             */
            rowHeight: 42,

            /**
             * Override this property if there's a need to provide custom
             * target element for max height appliance.
             *
             * @property {HTMLElement} Target element.
             */
            maxHeightTarget: '',

            onAttach: function () {
                this.on("init", function () {
                    this.dashletConfig = app.metadata.getView(this.module, this.name);

                    /**
                     * Dashboard record bean representing the dashboard to
                     * which this dashlet is attached.
                     *
                     * @property {Object}
                     */
                    this.dashModel = this.layout.context.get('model');

                    var settings = _.extend({}, this.meta),
                        viewName = 'main',
                        buildGrid = false;
                    delete settings.panels;
                    delete settings.action;
                    delete settings.dependencies;
                    delete settings.buttons;
                    this.settings = new Dashlet(settings);
                    // On the multi-line list and focus view, side drawer/focus drawer
                    // the dashlets need the correct module context, which
                    // is set here. The settings will sent to the dashlet, there the module will be used as needed.
                    if (_.contains(['multi-line', 'focus'], this.context.get('layout')) && settings.module === null) {
                        settings.module = this.module;
                    }
                    if (settings.module && this.context.parent) {
                        this.model = this.context.parent.get('model');
                    }
                    if (this.meta && this.meta.config) {
                        viewName = 'config';
                        this.action = 'edit';
                        this.model = this.context.parent ? this.context.parent.get('model') : this.model;
                        //needed to allow the record hbs to render our settings rather than the context model
                        this.dashModel.set(settings);
                        this.dashModel.set("componentType", (this instanceof app.view.Layout) ? "layout" : "view");

                        this.settings.on("change", function(model) {
                            this.dashModel.set(model.changed);
                        }, this);

                        this.meta.panels = this.dashletConfig.panels;
                        var templateName = this.name + '.dashlet-config';
                        this.template = app.template.getView(templateName, this.module) ||
                            app.template.getView(templateName);
                        if (!this.template) {
                            this.template = app.template.getView('dashletconfiguration-edit') || app.template.empty;
                            var originalPlugins = this.plugins;
                            this.plugins = ['GridBuilder'];
                            app.plugins.attach(this, 'view');
                            this.plugins = _.union(this.plugins, originalPlugins);
                            buildGrid = true;
                        }
                    } else if (this.meta && this.meta.preview) {
                        viewName = 'preview';
                        this.settings.module = this.module;
                        var templateName = this.name + '.dashlet-preview';
                        this.template = app.template.getView(templateName, this.module) ||
                            app.template.getView(templateName) ||
                            this.template;
                    } else {
                        this.settings.module = this.module;
                    }
                    if (this.initDashlet && _.isFunction(this.initDashlet)) {
                        this.initDashlet(viewName);
                        var height = this.calculateMaxHeight();
                        if (_.isNumber(height)) {
                            var $target = this.$(this.maxHeightTarget || this.$el);
                            $target.css('max-height', height + 'px');
                        }
                    }
                    if (buildGrid) {
                        this._buildGridsFromPanelsMetadata();
                    }

                    if (!_.isUndefined(this.layout)) {
                        this.listenTo(
                            this.layout.context,
                            'dashboard-filter-mode-changed',
                            this.toggleFilterMode,
                            this);
                        this.listenTo(
                            this.layout.context,
                            'refresh-dashlet-results',
                            this.refreshDashletResults,
                            this);
                        this.listenTo(
                            this.layout.context,
                            'silent-refresh-dashlet-results',
                            this.silentRefreshDashletResults,
                            this);
                        this.listenTo(
                            this.context,
                            'refresh-results-complete',
                            this.refreshDashletComplete,
                            this);
                        this.listenTo(
                            this.context,
                            'filters-loaded-successfully',
                            this.sendDashboardFilters,
                            this);
                        this.listenTo(
                            this.layout.context,
                            'dashboard-filter-group-highlight',
                            this.highlightFilterGroup,
                            this);
                        this.listenTo(
                            this.context,
                            'dashlet-finished-loading',
                            this.dashletFinishedLoading,
                            this);
                    }
                });

                this.once('render', function() {
                    app.analytics.trackPageView('/dashlet/' + this.name);
                });
            },

            /**
             * Send a message containing the dashboard filters for anyone interested
             */
            sendDashboardFilters: function() {
                const dashboardFilters = this.layout.model.get('metadata').filters;
                const dashletId = this.layout.el.getAttribute('data-gs-id');
                const filtersAffected = [];

                _.each(dashboardFilters, (filterGroup) => {
                    _.each(filterGroup.fields, (field) => {
                        if (field.dashletId === dashletId) {
                            filtersAffected.push(field);
                        }
                    });
                });

                this.context.trigger('dashboard-filters-meta-ready', filtersAffected);
            },

            /**
             * Switch ON/OFF the filter mode
             *
             * @param {string} state
             */
            toggleFilterMode: function(state, refetch) {
                if (state === 'edit') {
                    this._createFilterMode();
                } else {
                    this.refreshDashletResults(refetch);
                }
            },

            /**
             * Mark dashlet loaded
             */
            dashletFinishedLoading: function() {
                this.isDashletLoading = false;
            },

            /**
             * Create and display the filter mode
             */
            _createFilterMode: function() {
                this.$el.children().hide();

                this._filterModeController = app.view.createView({
                    context: this.layout.context,
                    type: 'dashlet-filter-mode',
                    module: this.module,
                    layout: this.layout,
                    model: this.settings,
                    dashletSpecificData: this.getDashletSpecificData ? this.getDashletSpecificData() : {},
                    manager: this,
                });

                this._filterModeController.show();

                this.$el.append(this._filterModeController.$el);
            },

            /**
             * Hide and destroy the filter mode
             */
            _destroyFilterMode: function() {
                if (this._filterModeController) {
                    this._filterModeController.hide();
                    this._filterModeController.dispose();
                    this._filterModeController = false;

                    this.$el.children().show();

                    this.context.trigger('dashlet:mode:loaded');
                }
            },

            refreshDashletComplete: function() {
                this._destroyFilterMode();
            },

            /**
             * Listen to show/hide the group highlight
             *
             * @param {Array} dashletIds
             * @param {boolean} highlight
             */
            highlightFilterGroup: function(dashletIds, highlight) {
                const dashletId = this.layout.el.getAttribute('data-gs-id');
                const dashletEl = this.layout.$el.find('[data-type="dashlet"]');

                if (_.includes(dashletIds, dashletId) && dashletEl.length === 1) {
                    dashletEl.toggleClass('dashlet-widget-highlight', highlight);
                    dashletEl.toggleClass('border-none', !highlight);
                }
            },

            /**
             * Refresh dashlet results
             *
             * @param {boolean} refetch
             */
            refreshDashletResults: function(refetch) {
                if (_.isFunction(this.refreshResults) && refetch) {
                    this.refreshResults();
                } else {
                    this._destroyFilterMode();
                }
            },

            /**
             * Silent refresh dashlet results
             *
             * @param {boolean} refetch
             */
            silentRefreshDashletResults: function(refetch) {
                if (_.isFunction(this.silentRefreshResults) && refetch) {
                    this.silentRefreshResults();
                } else {
                    this._destroyFilterMode();
                }
            },

            /**
             * Build grid panel metadata based on panel span size
             */
            _buildGridsFromPanelsMetadata: function() {
                _.each(this.meta.panels, function (panel) {
                    if (!_.isUndefined(this.getLabelPlacement)) {
                        panel.labelsOnTop = this.getLabelPlacement();
                    }
                    // it is assumed that a field is an object but it can also be a string
                    // while working with the fields, might as well take the opportunity to check the user's ACLs for the field
                    _.each(panel.fields, function (field, index) {
                        if (_.isString(field)) {
                            panel.fields[index] = field = {name: field};
                        }
                        // bind labelsonTop preference to field for use in
                        // templates
                        field.labelsOnTop = panel.labelsOnTop;
                    }, this);

                    // labels: visibility for the label
                    if (_.isUndefined(panel.labels)) {
                        panel.labels = true;
                    }

                    // Set flag so that show more link can be displayed to show hidden panel.
                    if (panel.hide) {
                        this.hiddenPanelExists = true;
                    }

                    if (_.isFunction(this.getGridBuilder)) {
                        var options = {
                            fields:      panel.fields,
                            columns:     panel.columns,
                            labels:      panel.labels,
                            labelsOnTop: panel.labelsOnTop,
                            tabIndex:    0
                        },
                            gridResults = this.getGridBuilder(options).build();

                        panel.grid   = gridResults.grid;
                    }
                }, this);
            },

            /**
             * Default max-height is 466 and placed in css.
             *
             * @return {number|boolean|undefined}
             */
            calculateMaxHeight: function() {
                if (!this.triggerBefore('calculateMaxHeight')) {
                    return false;
                }

                if (!this.meta.config && this.settings.has('limit')) {
                    return this.settings.get('limit') * this.rowHeight;
                }
            },

            /**
             * Run when the dashlet plugin is detached.
             */
            onDetach: function() {
                this.settings.off();
                delete this.dashletConfig;
                delete this.dashModel;
            }
        });
    });
})(SUGAR.App);

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
(function(app) {
    app.events.on('app:init', function() {
        /**
         * CJAsPanelOrTab plug-in will help the view switching on record views
         * according to the setting in admin section in 'Configure Display Setting'.
         *
         * This plugin will load the CJ component as Tab/Panel/Dashlet
         * according to the params.
         */
        app.plugins.register('CJAsPanelOrTab', ['view'], {
            /**
             * Contains CJ layout Object
             *
             * @property {Object} _CJ
             * @protected
             */
            _CJ: null,

            /**
             * Contains the tab name that will render on record views
             *
             * @property {string} _CJTabName
             * @protected
             */
            _CJTabName: 'customer_journey_tab',

            /**
             * For CJ config handling
             *
             * @property {string} _loadedConfig
             * @protected
             */
            _loadedConfig: false,

            /**
             * Bind handlers for view
             *
             * @return {undefined}
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    if (app.user.hasAutomateLicense()) {
                        this._loadCJLayout();
                    }
                });
            },

            /**
             * Returns true if the first non-header panel has useTabs set to true
             *
             * @return {boolean}
             * @private
             */
            _isRecordViewHasTabEnabled: function() {
                if (!(this.meta && this.meta.panels) ||
                    !(this.options && this.options.meta && this.options.meta.panels)
                ) {
                    return false;
                }

                let secondPanelIndex = '1';
                let panels = this.options.meta.panels;

                if (
                    (_.first(panels) && _.first(panels).newTab && !_.first(panels).header) ||
                    (panels[secondPanelIndex] && panels[secondPanelIndex].newTab)
                ) {
                    return true;
                }
                return false;
            },

            /**
             * Check if panels meta is defined and not empty
             *
             * @return {bolean}
             * @private
             */
            _isPanelMetaExists: function() {
                if (!_.isEmpty(this) &&
                    !_.isEmpty(this.options) &&
                    !_.isEmpty(this.options.meta) &&
                    !_.isEmpty(this.options.meta.panels)
                ) {
                    return true;
                }
            },

            /**
             * Check the current module name against which CJLayout will render
             *
             * @return {string}
             * @private
             */
            _getCurrentModule: function() {
                return this.model.module || this.model._module ||
                        (_.isFunction(this.model.get) && (this.model.get('module') || this.model.get('_module'))) ||
                        this.module;
            },

            /**
             * Check prerequisite before loading the layout
             *
             * @param {string} module
             * @return {boolean}
             * @private
             */
            _preRequisite: function(module) {
                //license
                let enabledModules = app.CJBaseHelper.getCJEnabledModules();

                if (!_.contains(enabledModules, module)) {
                    app.logger.debug(
                        app.utils.formatString(
                            app.lang.get('LBL_CUSTOMER_JOURNEY_MODULE_NOT_ENABLED'), {'module': module}
                        )
                    );
                    return false;
                }
                return true;
            },

            /**
             * Load the CJ layout according to the display settings
             *
             * @return {undefined}
             * @private
             */
            _loadCJLayout: function() {
                // If dashlet view then return as we will explicitly call functions from dashlet
                if (!_.isEmpty(this.type) && _.isEqual(this.type, 'cj-as-a-dashlet')) {
                    return;
                }

                let module = this._getCurrentModule();

                if (!this._preRequisite(module)) {
                    return;
                }

                this.displaySetting = app.CJBaseHelper.getCJRecordViewSettings(module);
                let tabEnabled = this._isRecordViewHasTabEnabled();

                if (
                    (!tabEnabled && this.displaySetting.includes('tab')) ||
                    (tabEnabled && _.isEqual(this.displaySetting, 'panel_top'))
                ) {
                    this.displaySetting = 'panel_bottom';
                }

                if (this.displaySetting.includes('tab')) {
                    let isModelSynced = this._isModelSynced();
                    if (isModelSynced) {
                        this.render();
                        this._loadCjAsTab();
                    } else {
                        this.listenTo(this.model, 'sync', this._loadCjAsTab);
                    }
                } else {
                    if (this._isModelSynced()) {
                        this._loadCjAsPanel();
                    } else {
                        this.listenTo(this.model, 'sync', this._loadCjAsPanel);
                    }
                }
            },

            /**
             * Load the CJ layout as dashlets without header
             *
             * @return {undefined}
             * @private
             */
            _loadCjAsDashlet: function() {
                if (this.disposed) {
                    return;
                }

                let module = this._getCurrentModule();

                if (!app.user.hasAutomateLicense() || !this._preRequisite(module)) {
                    this.dashletError = true;
                    return;
                }

                let context = this._prepareContextForCJTab();
                this._CJ = app.view.createLayout({
                    module: module,
                    context: context,
                    name: 'dri-workflows',
                    type: 'dri-workflows',
                });
                this._CJ.showHeaderRow = false;
                this._CJ.initComponents(undefined, context, module);
                this._CJ.loadData();

                if (!_.isEmpty(this._CJ)) {
                    let element = this.$('div[data-id*="customer-journey-as-a-dashlet"]');

                    if (element || element.length > 0) {
                        element.append(this._CJ.el);
                        // explicitly exapnding the panel as it is requirement
                        // that in tab view, always has the opened view
                        this._CJ.context.set('moreLess', 'more');
                        this._CJ.render();
                    }
                }
            },

            /**
             * Check if model has already been synced or not
             *
             * @return {boolean}
             * @private
             */
            _isModelSynced: function() {
                return !!(this.model && this.model.dataFetched && !this.model.inSync);
            },

            /**
             * Check if in View already CJP panel/tab meta exists or not
             *
             * @return {boolean}
             * @private
             */
            _isCJPanelMetaExists: function() {
                let isExists = false;

                if (this._isPanelMetaExists()) {
                    _.each(this.options.meta.panels, function panelEach(panel) {
                        if (!_.isEmpty(panel.name) && _.isEqual(panel.name, this._CJTabName)) {
                            isExists = true;
                            return;
                        }
                    }, this);
                }
                return isExists;
            },

            /**
             * Check if in View already CJP panel/tab meta exists or not
             * in Extra Info Layout (case for panel bottom)
             *
             * @param {Object} components
             * @return {boolean}
             * @private
             */
            _isCJPanelMetaExistsInExtraInfoLayout: function(components) {
                let isExists = false;

                if (_.isEmpty(components)) {
                    return isExists;
                }
                _.each(components, function(comp) {
                    if (!_.isEmpty(comp) && !_.isEmpty(comp.layout) && _.isEqual(comp.layout, 'dri-workflows')) {
                        isExists = true;
                        return;
                    }
                }, this);
                return isExists;
            },

            /**
             * Load the CJ layout as panel
             *
             * @return {undefined}
             * @private
             */
            _loadCjAsPanel: function() {
                if (this.disposed || (this.context && this.context.get('reloadSingleJourney'))) {
                    return;
                }

                if (_.isEqual(this.displaySetting, 'panel_bottom')) {
                    this._loadCjAsPanelBottom();
                } else if (_.isEqual(this.displaySetting, 'panel_top')) {
                    this._loadCjAsPanelTop();
                }
            },

            /**
             * Load the CJ layout as panel Bottom using the Extra-Info layout
             *
             * @return {undefined}
             * @private
             */
            _loadCjAsPanelBottom: function() {
                let layout = this._getLayout();
                let extraInfo = false;

                if (layout && layout.meta && layout.meta.components) {
                    extraInfo = layout.getComponent('extra-info');

                    _.each(layout.meta.components, function(component, index) {
                        let name = !_.isEmpty(component.layout) ? component.layout : '';

                        if (!_.isEmpty(component.layout) && !_.isEmpty(component.layout.type)) {
                            name = component.layout.type;
                        }
                        if (_.isEqual(name, 'extra-info')) {
                            if (_.isEmpty(component.layout.components)) {
                                component.layout = {
                                    type: 'extra-info',
                                    components: [],
                                };
                            }
                            if (!this._isCJPanelMetaExistsInExtraInfoLayout(component.layout.components)) {
                                component.layout.components.push(this._getCJPanelLayoutMeta());
                                this._addPanelMetaInExtraInfoLayout(extraInfo, layout);
                            }
                        }
                    }, this);
                }
                if (extraInfo) {
                    extraInfo.render();
                }
            },

            /**
             * Load the CJ layout as panel top
             *
             * @return {undefined}
             * @private
             */
            _loadCjAsPanelTop: function() {
                this.render();
                this._loadCJP();
                this._appendCJEleInView();
                this._removePanelHeaderDiv();
            },

            /**
             * Add the Smart Guide panel meta in Extra-info layout
             *
             * @param {Object} extraInfo
             * @param {Object} layout
             * @return {undefined}
             * @private
             */
            _addPanelMetaInExtraInfoLayout: function(extraInfo, layout) {
                if (extraInfo) {
                    extraInfo.meta.components = extraInfo.meta.components || [];
                    extraInfo.meta.components.push(this._getCJPanelLayoutMeta());

                    _.each(extraInfo.meta.components, function addCJPanelInExtraInfo(comp) {
                        if (!_.isEmpty(comp.layout) && _.isEqual(comp.layout, 'dri-workflows')) {
                            extraInfo.initComponents([comp], layout.context, this._getCurrentModule());
                        }
                    }, this);
                }
            },

            /**
             * Return the layout meta of Smart Guide panel
             *
             * @return {undefined}
             * @private
             */
            _getCJPanelLayoutMeta: function() {
                return {
                    layout: 'dri-workflows',
                    label: 'LBL_DRI_WORKFLOWS',
                    context: {
                        link: 'dri_workflows',
                        activeArchivedTrigger: true,
                    },
                };
            },

            /**
             * Return the layout object
             *
             * @return {Object}
             * @private
             */
            _getLayout: function() {
                let layout = {};

                if (!_.isEmpty(this.layout)) {
                    layout = this.layout;
                } else if (!_.isEmpty(this.options) && !_.isEmpty(this.options.layout)) {
                    layout = this.options.layout;
                }
                return layout;
            },

            /**
             * Load the CJ layout in _CJ variable for some panel and tab views
             *
             * @return {undefined}
             * @private
             */
            _loadCJP: function() {
                if (
                    this.disposed ||
                    _.isEmpty(this) ||
                    _.isEmpty(this.model) ||
                    _.isEmpty(this.context) ||
                    !_.isEmpty(this._CJ)
                ) {
                    return;
                }

                let context = this._prepareContextForCJTab();
                let module = this._getCurrentModule();
                this._CJ = app.view.createLayout({
                    module: module,
                    context: context,
                    name: 'dri-workflows',
                    type: 'dri-workflows',
                });

                this._CJ.initComponents(undefined, context, module);
                this._CJ.loadData();
                this.listenTo(this._CJ.collection, 'add remove sync', this._CJ.renderHeaderPanelData);
            },

            /**
             * Append the CJ in the respective div and that name is defined in _CJTabName
             *
             * @return {undefined}
             * @private
             */
            _appendCJEleInView: function() {
                if (!_.isEmpty(this._CJ) && !_.isEmpty(this.$el)) {
                    let element = this._getCJEleInView();

                    if (element || element.length > 0) {
                        element.append(this._CJ.el);
                        // explicitly exapnding the panel as it is requirement
                        // that in tab view, always has the opened view
                        this._CJ.context.set('moreLess', 'more');
                        this._CJ.render();
                    }
                }
            },

            /**
             * Return the element of Smart Guide panel and tab
             * that is added in the view via _addPanelInMeta
             *
             * Panel Top
             * Tab First
             * Tab last
             *
             * @return {undefined}
             * @private
             */
            _getCJEleInView: function() {
                if (!this.displaySetting) {
                    return;
                }

                let element = null;

                if (this.displaySetting.includes('tab')) {
                    element = this.$(`div[id*='${this._CJTabName}']`);
                } else if (this.displaySetting.includes('panel')) {
                    element = this.$(`div[data-panelname*='${this._CJTabName}']`).find('.record-panel-content');
                }
                return element;
            },

            /**
             * Remove the header and body content div when CJ render as
             * Panel Top as it is not required
             *
             * @return {undefined}
             * @private
             */
            _removePanelHeaderDiv: function() {
                let element = this._getCJEleInView();

                if (element || element.length > 0) {
                    let panelHeader = element.parent().find('.row-fluid.record-panel-header');

                    if (element || element.length > 0) {
                        panelHeader.remove();
                    }
                }
            },

            /**
             * Load the CJ layout as tab
             *
             * @return {undefined}
             * @private
             */
            _loadCjAsTab: function() {
                if (this.disposed || (this.context && this.context.get('reloadSingleJourney'))) {
                    return;
                }

                this._loadCJP();
                this._appendCJEleInView();
                this._setTabContentOverflow();
            },

            /**
             * Set overflow property of tab-content to visible
             * to make the last dropdown values visible
             */
            _setTabContentOverflow: function() {
                this.$('.tab-content').addClass('overflow-visible');
            },

            /**
             * Prepare the context for the CJ as tab view
             *
             * @return {Object|undefined}
             * @private
             */
            _prepareContextForCJTab: function() {
                if (_.isEmpty(this.model)) {
                    return;
                }

                let context = this.context.getChildContext({
                    module: 'DRI_Workflows',
                    model: app.data.createRelatedBean(
                            this.model, app.data.createBean('DRI_Workflows'), 'dri_workflows'),
                    forceNew: true,
                    link: 'dri_workflows',
                });

                context.set('collection', app.data.createRelatedCollection(this.model, 'dri_workflows'));
                context.set('moreLess', 'more');
                context.set('activeArchivedTrigger', true);
                // attribute for indication that CJ loaded as Tab
                context.set('cjAsTab', true);
                return context;
            },

            /**
             * Dispose all variables and CJ layout object.
             */
            disposePlugin: function() {
                if (!_.isEmpty(this._CJ)) {
                    this._CJ.dispose();
                }

                this._CJ = null;
                this._CJTabName = null;
            },

            /**
             * @inheritdoc
             * Unbind all variables and CJ layout object.
             */
            onDetach: function() {
                this.disposePlugin();
            },
        });
    });
})(SUGAR.App);

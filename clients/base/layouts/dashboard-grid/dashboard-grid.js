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
 * @class View.Layouts.Base.DashboardGridLayout
 * @alias SUGAR.App.view.layouts.BaseDashboardGridLayout
 * @extends View.Layout
 */
({
    /**
     * Class applied to HTML element that holds the dashlet grid
     */
    className: 'dashboard-grid relative mx-2',

    /**
     * Event listeners
     */
    events: {
        'click .empty-create-button': 'addDashletClicked'
    },

    /**
     * Reference to the grid managed by this controller
     */
    grid: null,

    /**
     * Options passed to the grid during initialization.
     *
     * see https://github.com/gridstack/gridstack.js/tree/develop/doc#grid-options
     */
    defaultGridOptions: {
        handleClass: 'dashlet-header',
        animate: 'true',
        maxRow: 0, // 0 means no maximum
        minRow: 1,
        verticalMargin: 15,
        cellHeight: 34,
        disableOneColumnMode: true,
        draggable: {
            handle: '.dashlet-header',
            scroll: false,
            appendTo: 'body',
            containment: null,
            cancel: 'input,textarea,button,select,option,[role="button"]'
        },
        resizable: {
            handles: 'sw,se'
        }
    },

    /**
     * Flag to know if dashlets have been loaded into the grid
     */
    dashletsLoaded: false,

    /**
     * Flag to know if grid events have been bound
     *
     * This prevents duplicate grid event bindings if dashletsLoaded
     * was changed to force dashlet reload
     */
    gridEventsBound: false,

    /**
     * Default options passed to each dashlet container when initilizing the grid
     * item that holds it.
     *
     * see https://github.com/gridstack/gridstack.js/tree/develop/doc#item-options
     */
    defaultElementOptions: {
        autoPosition: false,
        x: 0,
        y: 0,
        width: 12,
        minWidth: 2,
        minHeight: 5,
        height: 6,
    },

    /**
     * Index to track which tab of a tabbed dashboard we're looking at.
     */
    tabIndex: -1,

    /**
     * Collection of dashlet metadata. For tabbed dashboards, this is initialized
     * to the dashlets for our current tab index. For non-tabbed dashboards, this
     * is the dashlets saved on the Dashboard's metadata
     */
    dashlets: [],

    /**
     * Get the current tab index, set this.dashlets to the initial dashlets,
     * and initialize our grid.
     *
     * @inheritdoc
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._setDefaultGridOptions();
        this._setDefaultElementOptions();
        this.tabIndex = this._getTabIndex(options);
        this._setInitialDashlets();

        if (this.isSearchContext()) {
            this._setupSearchDashboard();
        }

        try {
            this.grid = GridStack.init(this._getGridOptions(), this.el);
        } catch (e) {
            console.warn('failed to load gridstack');
        }

        // This property is used by existing dashboards to apply legacy drag/drop
        // functionality that we no longer want.
        this.model.set('drag_and_drop', false);

        // To prevent a race condition, only save after the last save-able event has arrived
        this.debouncedSave = _.debounce(_.bind(this.handleSave, this), 100);
    },

    /**
     * Get grid options.
     * @return {Object}
     * @private
     */
    _getGridOptions: function() {
        var gridOptions = _.extend({}, this.defaultGridOptions);
        if (this.context.parent && this.context.parent.get('readonly')) {
            gridOptions = _.extend(gridOptions, {disableDrag: true, disableResize: true});
        }
        return gridOptions;
    },

    /**
     * @inheritdoc
     * @private
     */
    _render: function() {
        this._super('_render');
        // We load dashlets on render, because the DOM element holding the grid
        // needs to be in place before we add elements to it to properly size
        // and position the dashlets
        if (!this.dashletsLoaded) {
            this.loadDashlets();
            this.dashletsLoaded = true;

            // only bind grid events after the initial load
            if (!this.gridEventsBound) {
                this.bindGridEvents();
                this.gridEventsBound = true;
            }
        }
        this.grid.$el.toggleClass('grid-stack-empty', this.grid.isAreaEmpty());
    },

    /**
     * Indicates if we are in the search page or not.
     *
     * @return {boolean} `true` means we are in the search page.
     */
    isSearchContext: function() {
        return this.context.parent && this.context.parent.get('search');
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.isSearchContext()) {
            return;
        }

        this._super('bindDataChange');
        this.context.on('button:add_dashlet_button:click', this.addDashletClicked, this);
    },

    /**
     * Bind GridStack events
     */
    bindGridEvents: function() {
        /* The search's filter dashboard is a special dashboard:
           1. It's not saved in database by design.
           2. User can't add/remove dashlets.
           3. When dashlet is resized/moved, the dashboard shouldn't be saved since it doesn't exist in the database.
         */
        if (this.isSearchContext()) {
            return;
        }

        // Event fired when a dashlet is removed from a dashboard
        this.grid.on('removed', _.bind(this.saveDashboard, this));

        // This event is fired on drag, drop, resize, or adding elements
        // from the grid
        this.grid.on('change', _.bind(function(event, items) {
            this._handleGridChange(event, items);
            this.saveDashboard();
        }, this));

        this.grid.on('resizestop', _.bind(function(event, el) {
            const dashletId = _.first(el.element).getAttribute('data-gs-id');
            const targetDashlet = _.find(this.dashlets, function(dashlet) {
                return dashlet.id === dashletId;
            }, this);

            const dashletView = targetDashlet.view;

            if (_.isUndefined(dashletView)) {
                return;
            }

            const viewType = dashletView.type;
            const panelWrapper = _.find(this._components, function getPanel(panel) {
                if (_.isArray(panel.meta.components) && panel.meta.components.length > 0) {
                    const componentWithId = _.find(panel.meta.components, function(metaComponent) {
                        return _.has(metaComponent, 'id');
                    });

                    if (!_.isUndefined(componentWithId)) {
                        return componentWithId.id === dashletId;
                    }
                }

                return panel.meta.type === viewType;
            }, this);

            if (_.isUndefined(panelWrapper)) {
                return;
            }

            panelWrapper.manageSizeUpdated();
        }, this));
    },

    /**
     * Saves the current dashboard metadata to the DB
     */
    saveDashboard: function() {
        if (!this.preventSave) {
            this.debouncedSave();
        }
    },

    /**
     * Load dashlets from the metadata into the grid. If our metadata changed
     * during load due to an element being auto-positioned, save.
     */
    loadDashlets: function() {
        let changed = false;

        // Add each of the dashlets from metadata to the grid. We need to
        // do this as a batch update to the grid, so that the grid doesn't
        // try to auto-position any dashlets until all of them have been added
        this.grid.batchUpdate();
        _.each(this.dashlets, function(dashletMeta, index) {
            let oldmeta = this.dashlets[index];
            _.extend(this.dashlets[index], this.addDashlet(dashletMeta));
            if (!_.isEqual(oldmeta, this.dashlets[index])) {
                changed = true;
            }
        }, this);
        this.grid.commit();

        if (changed) {
            this.saveDashboard();
        }
    },

    /**
     * Create a dashboard-grid container from the dashlet metadata passed in, and
     * add it to the grid
     *
     * @param {Object} dashletDef dashlet metadata
     * @return {Object} dashlet metadata updated after being added to the grid
     */
    addDashlet: function(dashletDef) {
        // Obtain a copy of the default options so we can extend them without
        // changing object-level defaults
        var defaultOptions = app.utils.deepCopy(this.defaultElementOptions);
        var options = _.extendOwn(defaultOptions, dashletDef);

        // Generate a UUID for components to be stored with metadata so each
        // dashlet can be uniquely ID'd both on the frontend and in the DB
        options.id = options.id || app.utils.generateUUID();

        // Components are indexed in the order they're added for reference later
        var dashletGridWrapper = this._initializeDashlet(dashletDef, options.id);

        // Depending on the options set above for positioning, the dashlet
        // metadata may need to be updated after being added to the grid
        var widget = this.grid.addWidget(dashletGridWrapper.el, options);
        dashletDef = _.extend(dashletDef, this._getWidgetAttributes(widget));
        return dashletDef;
    },

    /**
     * Opens dashlet select drawer to allow users to add dashlets to the layout
     * @param {Event} evt
     */
    addDashletClicked: function(evt) {
        var self = this;
        let targetLayout = (this.context.parent) ? this.context.parent.get('layout') : null;
        app.controller.context.set('targetLayout', targetLayout);

        app.drawer.open({
            layout: 'dashletselect',
            context: this.context
        }, function(model) {
            if (!model) {
                return;
            }
            var conf = model.toJSON();
            var ctx = {module: model.get('module')};
            if (model.get('link')) {
                ctx.link = model.get('link');
            }
            var dashletDef = {
                context: ctx
            };
            var type = conf.componentType;
            delete conf.config;
            delete conf.componentType;
            if (_.isEmpty(dashletDef.context.module) && _.isEmpty(dashletDef.context.link)) {
                delete dashletDef.context;
            }
            // use deepCopy to get rid of any undefined attributes that may
            // cause the Unsaved data warning.
            dashletDef[type] = app.utils.deepCopy(conf);
            self.addNewDashlet(dashletDef);
        });
    },

    /**
     * Called when adding a new dashlet to the layout. This sets new dashlets
     * to autoposition themselves, then unsets the autoposition property so
     * future page loads will respect the user's positioning.
     *
     * @param {Object} dashletDef dashlet metadata
     * @param {bool} autoPosition
     */
    addNewDashlet: function(dashletDef, autoPosition = true) {
        dashletDef.autoPosition = autoPosition;
        dashletDef = this.addDashlet(dashletDef);
        dashletDef.autoPosition = false;
        this.dashlets.push(dashletDef);
        this.saveDashboard();
    },

    /**
     * Removes a dashlet from the grid
     *
     * @param {View.Layout} dashlet Backbone layout holding our dashlet
     */
    removeDashlet: function(dashlet) {
        // We use the unique ID to get its index in our components list and
        // dashlet metadata collection so we can remove the correct dashlet
        // from each
        var id = dashlet.el.getAttribute('data-gs-id');
        var index = _.findIndex(this.dashlets, function(dashletDef) {
            return dashletDef.id === id;
        });
        this.dashlets.splice(index, 1);
        this._components.splice(index, 1);

        // this action should be performed after splicing the old dashlet
        // to prevent re-saving it on the grid
        this.grid.removeWidget(dashlet.el);

        dashlet.model.unset('updated');
    },

    /**
     * Collapses/Expands the given dashlet
     *
     * @param {Object} dashlet the dashlet view object
     * @param {bool} collapse true to collapse the dashlet; false to expand it
     */
    toggleCollapseDashlet: function(dashlet, collapse) {
        // When we save a grid's metadata to the DB, we need to expand all
        // dashlets temporarily. In order to prevent recursive save loops from
        // occuring due to the event listeners from that, we need to set a flag
        this.preventSave = true;

        let grid = this.grid;
        let el = dashlet.$el;
        let node = el.data('_gridstack_node');

        if (collapse) {
            el
                .data('expand-min-height', node.minHeight)
                .data('expand-height', node.height);
            grid
                .resizable(el, false)
                .minHeight(el, null)
                .resize(el, null, 0);
        } else {
            grid
                .minHeight(el, el.data('expand-min-height'))
                .resize(el, null, el.data('expand-height'));
            if (!this.isSearchContext()) {
                grid.resizable(el, true);
            }
        }

        dashlet.trigger('dashlet:expand', !collapse);

        this.preventSave = false;
    },

    /**
     * Collapses the given dashlet
     *
     * @param {Object} dashlet the dashlet view object
     * @deprecated since 12.1.0, use {@link #toggleCollapse} instead
     */
    collapseDashlet: function(dashlet) {
        this.toggleCollapseDashlet(dashlet, true);
    },

    /**
     * Called when clicking "Edit" in the gear menu on a dashlet. Updates the
     * dashlet metadata and saves most recent user preferences
     *
     * @param {View.Layout} dashlet Backbone layout holding our dashlet
     * @param {Object} newDashletDef Updated metadata
     */
    editDashlet: function(dashlet, newDashletDef) {
        var id = dashlet.el.getAttribute('data-gs-id');
        var index = _.findIndex(this.dashlets, function(dashletDef) {
            return dashletDef.id === id;
        });
        _.extend(this.dashlets[index], newDashletDef);
        this.saveDashboard();
    },

    /**
     * Saves current model metadata
     */
    handleSave: function() {
        // If the user doesn't have permission to edit the dashboard, don't save
        if (!app.acl.hasAccessToModel('edit', this.model)) {
            this.model.unset('updated');
            return;
        }

        _.each(this.dashlets, function(dashletDef, i) {
            if (dashletDef.view && dashletDef.view.type === 'dashablerecord') {
                var newDef = app.utils.deepCopy(dashletDef);
                _.each(newDef.view.tabs, function(tab, i) {
                    delete newDef.view.tabs[i].meta;
                    delete newDef.view.tabs[i].model;
                    if (tab.type === 'list') {
                        delete newDef.view.tabs[i].collection;
                    }
                });
                this.dashlets[i] = newDef;
            }
        }, this);

        // Temporarily expand the dashlets so that their correct sizes and
        // positions are saved to the DB
        let collapsedDashlets = [];
        _.each(this._components, function(dashlet) {
            if (dashlet.isDashletCollapsed()) {
                collapsedDashlets.push(dashlet);
                this.toggleCollapseDashlet(dashlet, false);
            }
        }, this);

        // Save the dashboard metadata to the DB
        this.model.set('metadata', this._updateModelMeta(), {silent: true});
        this.model.save({}, {
            silent: true,
            showAlerts: false,
            success: _.bind(function() {
                if (!this.disposed) {
                    this.model.unset('updated');
                }
                this.grid.$el.toggleClass('grid-stack-empty', this.grid.isAreaEmpty());
            }, this),
            error: function() {
                app.alert.show('error_while_save', {
                    level: 'error',
                    title: app.lang.get('ERR_INTERNAL_ERR_MSG'),
                    messages: ['ERR_HTTP_500_TEXT_LINE1', 'ERR_HTTP_500_TEXT_LINE2']
                });
            }
        });

        // Re-collapse any dashlets that were temporarily expanded
        _.each(collapsedDashlets, function(dashlet) {
            this.toggleCollapseDashlet(dashlet, true);
        }, this);
    },

    /**
     * Create a dashboard-grid container layout from the dashlet metadata passed
     * in
     *
     * @param {Object} dashletDef Dashlet metadata
     * @param {string} dashletMetaId Dashlet metadata id
     *
     * @return {View.Layout} Dashlet Grid Item layout
     * @private
     */
    _initializeDashlet: function(dashletDef, dashletMetaId) {
        let dashletGridWrapper = app.view.createLayout({
            name: 'dashlet-grid-wrapper',
            layout: this,
            meta: {name: _.size(this._components)},
            context: this.context,
            dashletMetaId: dashletMetaId
        });
        this._components.push(dashletGridWrapper);
        dashletGridWrapper.addDashlet(dashletDef);
        // Legacy components need to have this class removed if they're being
        // loaded into a grid.
        dashletGridWrapper.$el.find('.dashlet').removeClass('ui-draggable');
        return dashletGridWrapper;
    },

    /**
     * Ensure our dashlet metadata has the current height/width/position for
     * each element
     * @param {Event} event ignored
     * @param {Array} items Dashlet grid containers
     * @private
     */
    _handleGridChange: function(event, items) {
        _.each(items, function(item) {
            var index = _.findIndex(this.dashlets, function(dashlet) {
                return dashlet.id === item.id;
            });

            if (index !== -1) {
                this.dashlets[index] = _.extend(this.dashlets[index], this._getItemAttributes(item));
            }
        }, this);
    },

    /**
     * Convert "widget" attributes to an object that matches our metadata
     * properties
     *
     * @param {jQuery} widget Gridstack Grid item
     * @return {Object} updated metadata
     * @private
     */
    _getWidgetAttributes: function(widget) {
        return {
            x: widget.getAttribute('data-gs-x'),
            y: widget.getAttribute('data-gs-y'),
            width: widget.getAttribute('data-gs-width'),
            height: widget.getAttribute('data-gs-height'),
            id: widget.getAttribute('data-gs-id') || ''
        };
    },

    /**
     * Convert "item" attributes to an object that matches our metadata
     *
     * @param {Object} item
     * @return {Object} updated widget metadata
     * @private
     */
    _getItemAttributes: function(item) {
        return {
            x: item.x,
            y: item.y,
            width: item.width,
            height: item.height,
            id: item.id,
        };
    },

    /**
     * Convert legacy component metadata to grid-style metadata
     * @param {Array} legacyComponents old-style dashboard metadata
     * @return {Array} Collection of dashlets
     * @private
     */
    _convertLegacyComponents: function(legacyComponents) {
        var dashlets = [];
        var x = 0;
        var y;
        var height = this.defaultElementOptions.height;
        var width;
        // "component" in legacy metadata is one column of dashlet-row elements
        _.each(legacyComponents, function(component) {
            // reset y index for each "component" to restart at the top of the grid
            y = 0;
            _.each(component.rows, function(row) {
                _.each(row, function(dashlet, dashletIndex) {
                    // do not convert component without view metadata
                    if (!_.has(dashlet, 'view')) {
                        return;
                    }

                    // Convert legacy dashboard width to grid-style width.
                    // Legacy dashboard "components" were sized in a 12-column
                    // grid, and each dashlet-row also contained 12 columns
                    var dashletWidth = dashlet.width || 12;
                    width = dashletWidth / 12 * component.width;
                    height = dashlet.height || height;

                    var dashletDef = _.extend(dashlet, {
                        x: x + width * dashletIndex,
                        y: y,
                        width: width,
                        height: height,
                    });
                    dashlets.push(dashletDef);
                });
                // increment y-value after every dashlet-row
                y += height;
            });
            // increment x-value after every "component"
            x += component.width;
        });
        return dashlets;
    },

    /**
     * Get current tab index
     * @param {Object} options
     * @return {number}
     * @private
     */
    _getTabIndex: function(options) {
        return this.context.get('activeTab') || 0;
    },

    /**
     * Set this.dashlets when component is initialized.
     * @private
     */
    _setInitialDashlets: function() {
        var metadata = app.utils.deepCopy(this.model.get('metadata'));
        // If our model isn't populated, return here
        if (!metadata) {
            return;
        }
        // The dashlet-main component sets the legacyComponents metadata property
        // on already existing dashboards. If it's set, we need to convert legacy
        // metadata to current metadata
        if (!_.isEmpty(metadata.legacyComponents)) {
            this.dashlets = this._convertLegacyComponents(metadata.legacyComponents);
        } else if (metadata.tabs) {
            this.dashlets = metadata.tabs[this.tabIndex].dashlets || [];
        } else {
            this.dashlets = metadata.dashlets || [];
        }
    },

    /**
     * Prior to saving, we need to set this.dashlets on the model to the proper
     * attribute depending on whether this is a tabbed dashboard or not.
     * @return {Object} updated metadata
     * @private
     */
    _updateModelMeta: function() {
        var metadata = app.utils.deepCopy(this.model.get('metadata'));
        // If our model isn't populated, return here
        if (!metadata) {
            return;
        }
        if (metadata.tabs) {
            metadata.tabs[this.tabIndex].dashlets = this.dashlets;
            delete metadata.tabs[this.tabIndex].components;
        } else {
            metadata.dashlets = this.dashlets;
            delete metadata.components;
        }
        delete metadata.legacyComponents;
        return metadata;
    },

    /**
     * Disable dragging and resizing for dashlets if user lacks edit access to
     * the dashboard.
     *
     * @private
     */
    _setDefaultGridOptions: function() {
        var editable = !app.acl.hasAccessToModel('edit', this.model);
        var rtl = app.lang.direction === 'rtl';
        this.defaultGridOptions.disableDrag = editable;
        this.defaultGridOptions.disableResize = editable;
        this.defaultGridOptions.rtl = rtl;
    },

    /**
     * Setups dashboard settings for search dasahboard.
     *
     * @private
     */
    _setupSearchDashboard() {
        this.defaultGridOptions.cellHeight = 42;
        this.defaultGridOptions.disableResize = true;
        this.defaultGridOptions.disableDrag = true;
        this.defaultGridOptions.marginTop = 50;

        _.each(this.dashlets, function(dashletMeta, index) {
            this.dashlets[index].minHeight = 1;
            this.dashlets[index].height = 1;

            if (this.dashlets[index].view.ui_type === 'multi') {
                // Get value for dashlet height
                let q = this._getMultiDashletHeight();
                if (isNaN(q)) {
                    q = 4; // default value before SS-3346
                }
                this.dashlets[index].minHeight = q;
                this.dashlets[index].height = q;
            }
        }, this);

        //Handle the height recalculation on window resize
        $(window).on('resize', _.debounce(_.bind(this._resizeMultiDashlet, this), 100));
    },

    /**
     * Change grid column mode if window was resized
     */
    _resizeMultiDashlet: function() {
        let q = this._getMultiDashletHeight();

        _.each(this.dashlets, function(dashletMeta, index) {
            if (this.dashlets[index].view.ui_type === 'multi') {
                let $multiDashlet = this.grid.$el.find(`[data-gs-id='${this.dashlets[index].id}']`)[0];
                if ($multiDashlet) {
                    $multiDashlet.setAttribute('data-gs-min-height', q);
                    $multiDashlet.setAttribute('data-gs-height', q);
                }
            }
        }, this);
    },

    /**
     * @inheritdoc
     * @private
     */
    _dispose: function() {
        if (this.context) {
            this.context.off('button:add_dashlet_button:click');
        }
        this.grid.off('change');
        this.grid.off('removed');
        $(window).off('resize', this._resizeMultiDashlet, this);
        this._super('_dispose');
    },

    /**
     * Set default values based on whether or not the dashboard is in a side drawer
     * @private
     */
    _setDefaultElementOptions: function() {
        if (!_.isUndefined(this.closestComponent('dashboard-pane')) && app.config.platform !== 'portal') {
            this.defaultElementOptions.minWidth = 6;
        } else {
            // When navigating from list/record views to home, the previous view's
            // value is still set when `initialize` is called
            this.defaultElementOptions.minWidth = 2;
        }
    },

    /**
     * Calculate dashlet height for multi type dashlet. This formula was obtained empirically.
     * @return {number}
     * @private
     */
    _getMultiDashletHeight: function() {
        let h = $('.dashboard.bg-secondary-content-background.w-full.absolute').outerHeight();
        let d = 97 - (h * 0.02);
        //divide-by-zero protection
        d = d > 0 ? d : 1;
        let q = Math.floor(h / d);
        return q;
    },
})

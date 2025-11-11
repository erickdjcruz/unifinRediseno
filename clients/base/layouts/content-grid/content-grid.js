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
 * This component is an abstracted type of dashboard-grid. De-couples
 * dashboard-grid and dashlets
 *
 * @class View.Views.Base.ContentGridLayout
 * @alias SUGAR.App.view.views.BaseContentGridLayout
 * @extends View.Layout
 */
({
    className: 'content-grid grid-stack',

    /**
     * Class name for grid item
     */
    gridItemClassName: 'content-grid-item',

    /**
     * Should this component use Gridstack?
     */
    useGridstack: true,

    /**
     * The grid
     */
    grid: null,

    /**
     * How many pixels per row on a Gridstack grid?
     *
     * Although there is no documentation on how this is calculated,
     * it's consistently opts.cellHeight + opts.verticalMargin
     */
    pixelsPerGridstackRow: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (this.useGridstack) {
            this.initGridstack();
        }
    },

    /**
     * Gridstack options on initialization
     */
    getGridstackOptions: function() {
    },

    /**
     * Gridstack widget options
     */
    getGridstackWidgetOptions: function() {
    },

    /**
     * Initialize the Gridstack library
     */
    initGridstack: function() {
        try {
            this.grid = GridStack.init(this.getGridstackOptions(), this.el);

            this.pixelsPerGridstackRow = this.grid.opts.cellHeight + this.grid.opts.verticalMargin;
        } catch (e) {
            console.warn('failed to load gridstack');
        }
    },

    /**
     * Add a component to the Gridstack grid
     *
     * @param component
     * @param options
     */
    addGridstackWidget(component, options = {}) {
        if (this.grid) {
            this.grid.addWidget(component.el, options);
        }
    },

    /**
     * Add and render components onto the grid
     */
    initGrid: function() {
        let defs = this.getDefsForComponents();

        _.each(defs, def => {
            let wrappedDefs = this.getWrappedDefsForGridItem(def);
            let comp = this.createComponentFromDef(wrappedDefs, null, this.module);
            this.addComponent(comp);

            comp.initComponents();
            comp.render();

            this.addGridstackWidget(comp, this.getGridstackWidgetOptions(comp));
        }, this);
    },

    /**
     * Get definitions for components to be added to the grid
     *
     * To be overridden by child components as definitions may
     * be retrieved from different places
     */
    getDefsForComponents: function() {
    },

    /**
     * Wrap the def in the grid item class name
     *
     * @param def
     * @return {Object}
     */
    getWrappedDefsForGridItem: function(def) {
        return {
            layout: {
                css_class: this.gridItemClassName,
                components: [
                    def
                ]
            }
        };
    }
})

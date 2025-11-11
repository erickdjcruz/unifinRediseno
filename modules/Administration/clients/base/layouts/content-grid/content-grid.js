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
 * @class View.Layouts.Base.AdministrationContentGridLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationContentGridLayout
 * @extends View.Views.Base.ContentGridLayout
 */

({
    extendsFrom: 'ContentGridLayout',

    /**
     * The admin layout
     */
    adminLayout: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.adminLayout = this.closestComponent('administration');

        if (this.adminLayout) {
            this.adminLayout.on('admin:panel-defs:fetched', function() {
                this.initGrid();
                this.resizeGrid();
            }, this);
        }

        this.debouncedResizeGrid = _.debounce(_.bind(this.resizeGrid, this), 100);
        $(window).on('resize', this.debouncedResizeGrid);
    },

    /**
     * Change grid column mode if window was resized
     */
    resizeGrid: function() {
        let columns = ($(window).width() <= 960) ? 1 : 12;

        if (this.grid && this.grid.opts.column !== columns) {
            this.grid.column(columns);
            this.grid.compact();
        }
    },

    /**
     * @inheritdoc
     */
    getGridstackOptions: function() {
        return {
            staticGrid: true, // removes drag|drop|resize
            disableOneColumnMode: true, // will manually do 1 column,
            rtl: app.lang.direction === 'rtl'
        };
    },

    /**
     * @inheritdoc
     */
    getGridstackWidgetOptions: function(component) {
        return {
            minHeight: 2, // widget occupies a min of 2 rows
            width: 6, // widget occupies half of the grid columns
            minWidth: 6,
            height: this.getGridstackWidgetHeight(component),
        };
    },

    /**
     *  Get the height of a Gridstack widget based on the outer
     *  height of the wrapper element
     *
     * @param component
     * @return int
     */
    getGridstackWidgetHeight: function(component) {
        let $wrapper = component.$el.find('.content-container-items');
        return Math.ceil($wrapper.outerHeight(true) / this.pixelsPerGridstackRow);
    },

    /**
     * @inheritdoc
     * @override
     */
    getDefsForComponents: function() {
        let defs = [];
        let adminPanelDefs = this.adminLayout && _.isFunction(this.adminLayout.getAdminPanelDefs) ?
            this.adminLayout.getAdminPanelDefs() :
            [];

        _.each(adminPanelDefs, def => {
            defs.push(this.getContentContainerComponentDef(def));
        }, this);

        return defs;
    },

    /**
     * Get the content-container layout definition
     *
     * @param def
     * @return {Object}
     */
    getContentContainerComponentDef: function(def) {
        let items = [];

        if (def.options) {
            _.each(def.options, option => {
                items.push({
                    label: option.label,
                    tooltip: option.description,
                    icon: option.icon,
                    customIcon: option.customIcon,
                    href: option.link,
                    onclick: option.onclick,
                    target: option.target
                });
            });
        }

        return {
            layout: {
                name: 'content-container',
                css_class: 'grid-stack-item-content',
                label: def.label || '',
                description: def.description || '',
                components: [
                    {
                        view: {
                            name: 'action-items',
                            items: items
                        }
                    }
                ]
            }
        };
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off('resize', this.resizeGrid, this);
        this._super('_dispose');
    }
})

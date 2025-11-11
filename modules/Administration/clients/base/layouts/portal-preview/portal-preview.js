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
 * @class View.Layouts.Base.AdministrationPortalPreviewLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationPortalPreviewLayout
 * @extends View.Layout
 */
({
    /**
     * Cache the preview components as they may be expensive to retrieve
     *
     * {
     *     layout-name: {
     *         view-name-1: component,
     *         view-name-2: component,
     *     }
     * }
     */
    componentsCache: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.componentsCache = {};
    },

    /**
     * @inheritdoc
     */
    initComponents: function(components, options, module) {
        // set config-layout on context so dashboard-fabs and record dashlets
        // can adjust their behavior accordingly
        this.context.set('config-layout', true);
        this._super('initComponents', [components, options, module]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.context.on('portal:config:preview', this.handleConfigPreview, this);
        this.on('dashboard:restore_dashlets_button:click', this.restorePortalHomeDashlets, this);
    },

    /**
     * Restore dashboard metadata (set dashlets to initial state)
     *
     * @param {Object} context context of the dashboard component which has portal-preview layout
     */
    restorePortalHomeDashlets: function(context) {
        var model = context ? context.get('model') : {};

        if (!_.isEmpty(model)) {
            var attributes = {
                id: model.get('id')
            };
            var params = {
                dashboard_module: model.get('dashboard_module'),
                dashboard: 'portal-home'
            };

            var url = app.api.buildURL('Dashboards', 'restore-metadata', attributes, params);

            app.api.call('update', url, null, {
                success: _.bind(function(response) {
                    var dashboard = this.getComponent('dashboard');
                    dashboard.model.set(response);
                    dashboard.model.setSyncedAttributes(response);
                }, this)
            });
        }
    },

    /**
     * Handles the event 'portal:config:preview'
     *
     * Triggers 'data:preview' on the preview component to let the component
     * handle it's own preview behavior
     *
     * Expects the data event argument to look like:
     * {
     *     preview_components: [
     *         ...
     *         {
     *             layout: 'layout-name',
     *             view: 'view-name',
     *             fields: [...],
     *             properties: [...],
     *             preview_data: '...'
     *         },
     *         ...
     *     ]
     * }
     *
     * @param data
     */
    handleConfigPreview: function(data) {
        _.each(data.preview_components, function(def) {
            var component = this.getPreviewComponent(def);

            if (!component) {
                return;
            }

            component.trigger('data:preview', {
                fields: def.fields && !_.isEmpty(def.fields) ? def.fields : [],
                properties: def.properties && !_.isEmpty(def.properties) ? def.properties : [],
                preview_data: data.preview_data
            });
        }, this);
    },

    /**
     * Get the specified preview component and cache it (if found)
     *
     * Requires def.layout and def.view to prevent expensive recursive
     * searching for a specific component
     * {
     *     layout: 'layout-name',
     *     view: 'view-name'
     * }
     *
     * @param def
     * @return {View.View} the view component
     */
    getPreviewComponent: function(def) {
        if (!def.layout || !def.view) {
            return null;
        }

        if (this.componentsCache[def.layout] && this.componentsCache[def.layout][def.view]) {
            return this.componentsCache[def.layout][def.view];
        }

        var layout = this.getLayoutChain(def.layout);
        var component = layout ? layout.getComponent(def.view) : null;

        if (component) {
            if (!this.componentsCache[def.layout]) {
                this.componentsCache[def.layout] = {};
            }

            this.componentsCache[def.layout][def.view] = component;
        }

        return component;
    },

    /**
     * Get nested layout if it's needed
     * @param layouts
     * @return {View.Layout}
     */
    getLayoutChain: function(layouts) {
        var components = layouts.split('.');
        var self = this;

        _.each(components, function(item) {
            if (self) {
                self = self.getComponent(item);
            }
        });

        return self;
    },

    /**
     * Unset config-layout on context when this is disposed
     * @private
     */
    _dispose: function() {
        this.componentsCache = null;

        this.context.unset('config-layout');
        this.context.off('portal:config:preview', this.handleConfigPreview, this);
        this._super('_dispose');
    }
})

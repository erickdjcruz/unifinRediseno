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
 * @class View.Views.Base.AdministrationMapsModuleWidgetView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsModuleWidgetView
 */
({
    /**
     * Event listeners
     */
    events: {
        'click [data-action=open-settings]': 'displaySetting',
        'click [data-action=open-subpanel-config]': 'displaySubpanelConfig',
        'click [data-action=open-mappings]': 'displayMappings',
        'click [data-action=remove-module]': 'removeModule',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(options);
    },

    /**
     * Property initialization
     *
     * @param {Object} options
     */
    _initProperties: function(options) {
        if (options.widgetModule) {
            this.widgetModule = options.widgetModule;
        }
    },

    /**
     * Message parent to add a new settings view for selected module
     */
    displaySetting: function() {
        const viewMeta = {
            viewName: 'maps-module-settings',
            widgetModule: this.widgetModule,
        };

        this.triggerDisplayView(viewMeta);
    },

    /**
     * Message parent to add a new subpanel-config view for selected module
     */
    displaySubpanelConfig: function() {
        const viewMeta = {
            viewName: 'maps-module-subpanel-config',
            widgetModule: this.widgetModule,
        };

        this.triggerDisplayView(viewMeta);
    },

    /**
     * Message parent to add a new mappings view for selected module
     */
    displayMappings: function() {
        const viewMeta = {
            viewName: 'maps-module-mappings',
            widgetModule: this.widgetModule,
        };

        this.triggerDisplayView(viewMeta);
    },

    /**
     * Remove selected module from geocoding
     */
    removeModule: function() {
        let availableModules = this.model.get('maps_enabled_modules');

        availableModules = _.reject(availableModules, function removeModule(module) {
            return module === this.widgetModule;
        }, this);

        this.model.set('maps_enabled_modules', availableModules);
    },

    /**
     * Trigger an action to display the view
     *
     * @param {Array} viewMeta
     */
    triggerDisplayView: function(viewMeta) {
        this.context.trigger('display:map:module:config', viewMeta);
    },
});

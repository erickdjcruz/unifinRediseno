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
 * Layout for maps module setup
 *
 * @class View.Layouts.Base.AdministrationMapsModuleSetupLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationMapsModuleSetupLayout
 */
({

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Property initialization
     */
    _initProperties: function() {
        this._configView = null;
    },

    /**
     * Register context event handlers
     *
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'display:map:module:config', this.updateUI, this);
    },

    /**
     * Update the UI elements from user action
     *
     * @param {Object} data
     */
    updateUI: function(data) {
        const viewName = data.viewName;
        const module = data.widgetModule;
        const $container = this.$('[data-container=config-container]');
        const moduleData = data.moduleData;

        $container.empty();

        this._createConfigView(module, moduleData, viewName, $container);
    },

    /**
     * Initialize and render inner config
     *
     * @param {string} module
     * @param {Object} moduleData
     * @param {string} viewName
     * @param {jQuery} $container
     */
    _createConfigView: function(module, moduleData, viewName, $container) {
        this._disposeConfigView();

        var configView = app.view.createView({
            name: viewName,
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            moduleData: moduleData,
            widgetModule: module
        });

        this._configView = configView;

        $container.append(configView.$el);
        configView.render();
    },

    /**
     * Dispose loaded component
     *
     */
    _disposeConfigView: function() {
        if (this._configView) {
            this._configView.dispose();
        }

        this._configView = null;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeConfigView();

        this._super('_dispose');
    },
});

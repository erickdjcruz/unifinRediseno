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
 * @class View.Views.Base.ActivityTimelineView
 * @alias SUGAR.App.view.views.BaseActivityTimelineView
 * @extends View.Views.Base.ActivityTimelineBaseView
 */
({
    className: 'activity-timeline',

    extendsFrom: 'ActivityTimelineBaseView',

    plugins: ['EmailClientLaunch', 'LinkedModel', 'Dashlet'],

    /**
     * Rendered layout activities
     */
    additionalComponents: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.template = app.template.getView('activity-timeline-base');
        this.$el.addClass('dashlet-unordered-list');
        this.filter = {
            module: null
        };
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.context.on('filter:change:module', this.handleFilter, this);
    },

    /**
     * @inheritdoc
     *
     * Inject the singular module name.
     */
    _render: function() {
        this.disposeAdditionalComponents();

        this._super('_render');

        if (_.isFunction(this.layout.setTitle)) {
            var moduleSingular = app.lang.get(this.meta.label,
                this.module,
                {
                    moduleSingular: app.lang.getModuleName(this.module)
                }
            );
            this.layout.setTitle(moduleSingular);
        }

        this.initializeFilter();
    },

    /**
     * Create new filter layout and append it to the current view
     */
    initializeFilter: function() {
        var filter = app.view.createLayout({
            type: 'activity-filter',
            layout: this.layout,
            context: this.context,
        });

        filter.initComponents();
        filter.render();

        this.additionalComponents.push(filter);

        _.first(filter.$el).classList += ' bg-transparent';
        this.$('.activity-timeline-filter').append(filter.$el);
    },

    /**
     * Handle of filter change event
     *
     * @param {string} filterModule name of filtered module
     * @param {boolean} silent
     */
    handleFilter: function(filterModule, silent) {
        if (!silent || !this.filter.module) {
            const isModuleAvailable = filterModule === 'Audit' ? true :
                app.metadata.getModule(filterModule);
            this.filter.module = isModuleAvailable ? filterModule : 'all_modules';
            this._setActivityModulesAndFields(this.baseModule);
            this._initCollection();
            this.reloadData();
        }
    },

    /**
     * Must implement this method as a part of the contract with the Dashlet
     * plugin. Kicks off the various paths associated with a dashlet:
     * Configuration, preview, and display.
     *
     * @param {string} viewName The name of the view as defined by the `oninit`
     *   callback in {@link DashletView#onAttach}.
     */
    initDashlet: function(viewName) {
        this._mode = viewName;

        if (this._mode === 'config') {
            this.layout.before('dashletconfig:save', function() {
                // save the toolbar
                if (this.meta.custom_toolbar) {
                    this.settings.set('custom_toolbar', this.meta.custom_toolbar);
                }
            }, this);
        }
    },

    /**
     * @inheritdoc
     *
     * Get the activity-timeline dashlet metadata for the baseModule
     * @param {string} baseModule module name
     */
    getModulesMeta: function(baseModule) {
        return app.metadata.getView(baseModule, 'activity-timeline');
    },

    /**
     * @inheritdoc
     *
     * Get the activity-timeline dashlet metadata for the baseModule
     * @param {string} baseModule module name
     */
    getModulesCardMeta: function(baseModule) {
        const customName = 'activity-card-definition-for-' + baseModule.toLowerCase();
        return app.metadata.getView(this.baseModule, customName) ||
            app.metadata.getView(baseModule, 'activity-card-definition');
    },

    /**
     * Disposes additional components
     */
    disposeAdditionalComponents: function() {
        _.each(this.additionalComponents, function(component) {
            component.dispose();
        }, this);

        this.additionalComponents = [];
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.disposeAdditionalComponents();
        this._super('_dispose');
    }
})

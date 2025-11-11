
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
 * @class View.Views.Base.Reports.ReportFiltersLayout
 * @alias SUGAR.App.view.views.BaseReportsReportFiltersLayout
 * @extends View.Views.Base.Layout
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
     * Initialize controller's
     */
    _initProperties: function() {
        this._layoutConfigRetrieved = false;
        this._filtersRetrieved = false;
        this._hadUserInteraction = false;
        this._isVisible = false;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'toggle:advanced:filters', this.toggleAdvancedFilters);
        this.listenTo(this.context, 'split-screens-visibility-change', this.setVisibilityState);
        this.listenTo(this.context, 'report-layout-config-retrieved', this.layoutConfigRetrieved);
        this.listenTo(this.context, 'report:data:filters:loaded', this.runtimeFiltersLoaded);
    },

    /**
     * Handle Visibility State
     */
    runtimeFiltersLoaded: function() {
        const runtimeFilters = this.getComponent('report-filters').getRawRuntimeFilters();
        const numberOfFilters = _.keys(runtimeFilters).length;

        this.context.trigger('filters-container-content-loaded', {
            filtersActive: this._isVisible,
            numberOfFilters,
        });

        if (this._hadUserInteraction) {
            return;
        }

        const initialConfigurationOfTheFilterPanel = _.isUndefined(this._isVisible);

        if (initialConfigurationOfTheFilterPanel) {
            const hasFilters = numberOfFilters > 0;
            this._isVisible = hasFilters;

            this.$el.toggleClass('!hidden', !this._isVisible);
        }
    },

    /**
     * Handle Visibility State
     *
     * @param {Object} config
     */
    layoutConfigRetrieved: function(config) {
        if (this._hadUserInteraction) {
            return;
        }

        this._isVisible = config.filtersActive;
        this.$el.toggleClass('!hidden', !this._isVisible);

        this.context.trigger('filters-container-content-loaded', {
            filtersActive: this._isVisible,
        });
    },

    /**
     * Handle Visibility State
     *
     * @param {Object} config
     */
    setVisibilityState: function(config) {
        this._hadUserInteraction = true;

        this._isVisible = config.filtersActive;
        this.$el.toggleClass('!hidden', !this._isVisible);
    },

    /**
     * Show Advanced Filters View
     */
    toggleAdvancedFilters: function() {
        this.context.disableRecordSwitching = true;
        this.context.hideRecordSwitching = true;

        this.context.set({
            runtimeFilters: this.getComponent('report-filters').getRuntimeFilters(),
            reportData: this.getComponent('report-filters').getReportData(),

        });

        app.sideDrawer.open({
            layout: 'report-advanced-filters',
            context: this.context,
        });

        this.updateCloseButtonLabel();
    },

    /**
     * Update close button label
     */
    updateCloseButtonLabel: function() {
        const closeTitle = app.lang.get('LBL_ADVANCED_CLOSE', 'Reports');
        app.sideDrawer.$('button[data-action="drawerClose"]').attr({
            'data-original-title': closeTitle,
            'title': closeTitle
        });
    }
})

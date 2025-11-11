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
 * @class View.Views.Base.HomeDashboardHeaderpaneView
 * @alias SUGAR.App.view.views.BaseHomeDashboardHeaderpaneView
 * @extends View.Views.Base.HeaderpaneMainView
 */
({
    extendsFrom: 'DashboardHeaderpaneMainView',

    className: 'preview-headerbar home-headerpane',
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['DashboardFiltersVisibility']);

        this._super('initialize', [options]);

        this._initProperties();

        this._registerEvents();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'button:dashboard_filters:click', this.toggleDashboardFilter);
        this.listenTo(this.context, 'button:cancel_dashboard_filters:click', this.cancelDashboardFilters);
        this.listenTo(this.context, 'button:save_dashboard_filters:click', this.saveDashboardFilters);
        this.listenTo(this.context, 'dashboard-saved-success', this.saveDashboardSuccess);
        this.listenTo(this.context, 'dashboard-filter-mode-updated', this.filterModeState);
        this.listenTo(this.context, 'filter-groups-updated', this.enableSaveButton);
        this.listenTo(this.context, 'filter-field-updated', this.enableSaveButton);
        this.listenTo(this.context, 'toggle:change-visibility:button', this._toggleDashboardFiltersButton);
        this.listenTo(this.context, 'dashboard-filters-metadata-loaded', this._handleFiltersButtonVisibility);
        this.listenTo(this.context, 'dashboard:filter:broken', this.disableSaveButton);
        this.listenTo(this.model, 'sync', this.modelSync);

        // we only extend events so that save works
        _.extend(this.events, this._getHeaderpaneEvents());
    },

    /**
     * Initialize properties
     */
    _initProperties: function() {
        this.hasAccessToReports = app.acl.hasAccess('view', 'Reports');

        this.initFiltersVisibilityProperties();
    },

    /**
     * @inheritdoc
     */
    _renderHeader: function() {
        this._super('_renderHeader');

        const showFiltersButton = this.$('[data-action="change-visibility"]');

        showFiltersButton.off('click', _.bind(this.toggleDashboardFilter, this));
        showFiltersButton.on('click', _.bind(this.toggleDashboardFilter, this));
    },

    /**
     * Model synced
     */
    modelSync: function() {
        if (!this.model || this.disposed) {
            return;
        }

        const metadata = this.model ? this.model.get('metadata') : {};

        if (!metadata || metadata.tabs) {
            return;
        }

        const showFiltersButton = this.$('[data-action="change-visibility"]');
        const originalTitle = app.lang.get('LBL_DASHBOARD_FILTERS', 'Home');

        showFiltersButton.removeClass('disabled');
        showFiltersButton.attr('data-original-title', originalTitle);
        showFiltersButton.attr('title', originalTitle);

        const compatibleDashlets = _.filter(metadata.dashlets, (dashlet) => {
            return dashlet.view && dashlet.view.filtersDef;
        });

        if (_.isEmpty(compatibleDashlets)) {
            this._filtersOnScreen = true;

            this.toggleDashboardFilter();

            const incompatibleTitle = app.lang.get('LBL_DASHBOARD_DASHLETS_INCOMPATIBLE', 'Home');

            showFiltersButton.addClass('disabled');
            showFiltersButton.attr('title', incompatibleTitle);
            showFiltersButton.attr('data-original-title', incompatibleTitle);
        }
    },

    /**
     * Show/hide the button given a set of rules
     *
     * @param {Object} filterGroups
     */
    _handleFiltersButtonVisibility: function(filterGroups) {
        // we execute this code on the next frame given the fact that _renderHeader has to be executed first
        // _renderHeader is being called when the model has been synced(same as this method) so we
        // have to make sure this one always executes after _renderHeader
        // this limitation came from the implementation of the headerpane controller
        _.debounce(() => {
            const metadata = this.model ? this.model.get('metadata') : {};

            if (metadata.tabs) {
                return;
            }

            this.modelSync();

            const showFiltersButton = this.$('[data-action="change-visibility"]');
            const dashbordMain = this.layout.getComponent('dashboard-main');

            showFiltersButton.toggleClass('!hidden', false);

            if (!dashbordMain) {
                showFiltersButton.toggleClass('!hidden', true);
                return;
            }

            const dashletMain = dashbordMain.getComponent('dashlet-main');

            if (!dashletMain) {
                showFiltersButton.toggleClass('!hidden', true);
                return;
            }

            if (this._filtersOnScreen) {
                const showFiltersButton = this.$('.report-visibility-button');

                showFiltersButton.toggleClass('active', true);
            }

            this._updateNumberOfFilters(filterGroups);
        })();
    },

    /**
     * Get headerpane events
     *
     * @return {Object}
     */
    _getHeaderpaneEvents: function() {
        const parentDashboardHeaderpaneController = app.view._getController({
            type: 'view',
            name: 'dashboard-headerpane',
            module: 'Dashboards'
        });

        if (typeof parentDashboardHeaderpaneController !== 'function' ||
            typeof parentDashboardHeaderpaneController.prototype !== 'object') {
            return {};
        }

        return parentDashboardHeaderpaneController.prototype.events;
    },

    /**
     * Cancel action
     */
    cancelDashboardFilters: function() {
        this.context.trigger('dashboard-filters-canceled');
        this.context.trigger('dashboard-filter-mode-changed', 'detail');
        this.context.trigger('dashboard-filter-mode-updated', 'detail');

        this.manageCancelSaveButtons(false);
    },

    /**
     * Save action
     */
    saveDashboardFilters: function() {
        this.disableSaveButton();
        this.context.trigger('dashboard-filters-save');
    },

    /**
     * Save dashboard success
     */
    saveDashboardSuccess: function() {
        this.manageCancelSaveButtons(false);
    },

    /**
     * Filter mode ON
     *
     * The Cancel/Save button should be visible in the headerpane
     */
    filterModeState: function(state) {
        const buttonsVisibility = state === 'edit';

        this.manageCancelSaveButtons(buttonsVisibility);
    },

    /**
     * Show/Hide Cancel/Save buttons
     *
     * @param {boolean} visibility
     */
    manageCancelSaveButtons: function(visibility) {
        this.$('[name="cancel_dashboard_filters"]').toggleClass('!hidden', !visibility);
        this.$('[name="save_dashboard_filters"]').toggleClass('!hidden', !visibility);
        this.$('.report-visibility-button').toggleClass('!hidden', visibility);
    },

    /**
     * Filter group save button disabling
     */
    disableSaveButton: function() {
        this.$('[name="save_dashboard_filters"]').toggleClass('disabled', true);
    },

    /**
     * Filter group save button enabling
     *
     * When a change is made, the save button should be enabled
     */
    enableSaveButton: function() {
        this.$('[name="save_dashboard_filters"]').toggleClass('disabled', false);
    },

    /**
     * Show/Hide the filter container
     */
    toggleDashboardFilter: function() {
        const buttonDisabled = this.$('[data-action="change-visibility"]').hasClass('disabled');

        if (buttonDisabled) {
            return;
        }

        this._filtersOnScreen = !this._filtersOnScreen;

        const showFiltersButton = this.$('.report-visibility-button');
        showFiltersButton.toggleClass('active', this._filtersOnScreen);

        this.storeFilterPanelState(this._filtersOnScreen);

        this.context.trigger('dashboard-filter-toggled', this._filtersOnScreen);
    },

    /**
     * Update the displayed number of filters
     *
     * @param {Object} filterGroups
     */
    _updateNumberOfFilters: function(filterGroups) {
        const formattedFiltersNumber = this._getNumberOfFiltersToDisplay(filterGroups);
        const filtersBadgeEl = this.$('.report-filters-badge');

        filtersBadgeEl.toggleClass('!hidden', true);

        if (formattedFiltersNumber > 0 || _.isString(formattedFiltersNumber)) {
            filtersBadgeEl.toggleClass('!hidden', false);
            filtersBadgeEl.text(formattedFiltersNumber);
        }
    },

    /**
     * Get a formatted display number
     *
     * @param {Object} filterGroups
     *
     * @return {string}
     */
    _getNumberOfFiltersToDisplay: function(filterGroups) {
        const maxNumberOfFiltersDisplayed = 9;
        const maxNumberOfFiltersLabel = '9+';

        let formattedFiltersNumber = _.size(filterGroups);

        if (formattedFiltersNumber > maxNumberOfFiltersDisplayed) {
            formattedFiltersNumber = maxNumberOfFiltersLabel;
        }

        return formattedFiltersNumber;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        const showFiltersButton = this.$('[data-action="change-visibility"]');
        showFiltersButton.off('click', _.bind(this.toggleDashboardFilter, this));

        this._super('_dispose');
    },
});

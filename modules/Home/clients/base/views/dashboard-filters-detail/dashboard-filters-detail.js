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
 * @class View.Views.Base.HomeDashboardFiltersDetail
 * @alias SUGAR.App.view.views.BaseHomeDashboardFiltersDetail
 * @extends View.View
 */
({
    className: 'dashboard-filter-container bg-[--dashlet-background] h-full overflow-hidden w-[230px]',
    plugins: ['DashboardFilters'],
    events: {
        'click [data-action="switch-to-edit"]': 'switchToEditMode',
        'click [data-action="add-first-filter"]': 'addFirstFilter',
        'click [data-action="apply-runtime-filters"]': 'applyRuntimeFilters',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * @inheritdoc
     */
    _initProperties: function() {
        this._editMode = false;

        this.initFiltersProperties();
    },

    /**
     * @inheritdoc
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'dashboard-filter-group-invalid-save', this.invalidGroupSave, this);
        this.listenTo(this.context, 'filter-operator-data-changed', this.enableApplyButton, this);
    },

    /**
     * Dashboard meta data has been loaded, we need to create the views
     */
    manageDashboardFilters: function(filterGroups) {
        let showEditButton = false;
        let showApplyFiltersContainer = false;
        let showNoFilterContainer = false;
        let showAddContainer = false;

        this.handleDashboardFilters(filterGroups);

        this._hasGroupFilters = !_.isEmpty(this._filterGroups);
        this._hasEditAccess = app.acl.hasAccessToModel('edit', this.model);

        let isTemplateDashboard = false;
        if (!_.isUndefined(this.model) && !_.isUndefined(this.model.get('is_template'))) {
            isTemplateDashboard = this.model.get('is_template');
        }
        showNoFilterContainer = !this._hasGroupFilters;
        showApplyFiltersContainer = this._hasGroupFilters;

        if (this._hasEditAccess && !isTemplateDashboard) {
            showEditButton = this._hasGroupFilters;
            showAddContainer = !this._hasGroupFilters;
        }

        this.$('[data-action="show-edit-button"]').toggleClass('hidden', !showEditButton);
        this.$('[data-container="apply-filters-container"]').toggleClass('hidden', !showApplyFiltersContainer);
        this.$('[data-container="no-filter-container"]').toggleClass('hidden', !showNoFilterContainer);
        this.$('[data-container="add-container"]').toggleClass('hidden', !showAddContainer);

        this.toggleLoading(false);
    },

    /**
     * Enable the apply filters button
     */
    enableApplyButton: function() {
        this.$('.apply-filters-btn').removeClass('disabled').removeAttr('disabled');
    },

    /**
     * Disable the apply filters button
     */
    disableApplyButton: function() {
        this.$('.apply-filters-btn').addClass('disabled').attr('disabled', true);
    },

    /**
     * Toggle loading screen
     *
     * @param {boolean} toggle
     */
    toggleLoading: function(toggle) {
        this.$('.filters-skeleton-loader').toggleClass('hidden', !toggle);
        this.$('.dashboard-filters-detail-container').toggleClass('hidden', toggle);
    },

    /**
     * Add first filter and go to edit mode
     */
    addFirstFilter: function() {
        this.context.trigger('dashboard-filters-interaction');
        this.switchToEditMode();
    },

    /**
     * Go to edit mode
     */
    switchToEditMode: function() {
        let canEdit = this._canAlterFilters();

        if (canEdit) {
            this.context.trigger('dashboard-filter-mode-changed', 'edit');
            this.context.trigger('dashboard-filter-mode-updated', 'edit');
        } else {
            this._showCantAlterWarning();
        }
    },

    /**
     * Apply runtime filters
     */
    applyRuntimeFilters: function() {
        let canEdit = this._canAlterFilters();

        if (canEdit) {
            this.context.trigger('dashboard-filters-apply');
            this.context.trigger('refresh-dashlet-results');
            this.disableApplyButton();
        } else {
            this._showCantAlterWarning();
        }
    },

    /**
     * Show cannot alter filters warning
     */
    _showCantAlterWarning: function() {
        app.alert.show('show_alter_alert', {
            level: 'warning',
            messages: app.lang.get('LBL_NO_ALTER_FILTERS', this.module),
            autoClose: true,
        });
    },

    /**
     * Can or cannot edit filters
     *
     * @return {boolean}
     */
    _canAlterFilters: function() {
        const dashboardMain = this.closestComponent('dashboard-main');
        const dashletMain = dashboardMain ? dashboardMain.getComponent('dashlet-main') : false;
        const dashboardGrid = dashletMain ? dashletMain.getComponent('dashboard-grid') : false;

        let canAlter = true;

        if (dashboardGrid) {
            _.each(dashboardGrid._components, (dashletWrapper) => {
                _.each(dashletWrapper._components, (dashlet) => {
                    if (dashlet.isDashletLoading) {
                        canAlter = false;
                    }
                });
            });
        }

        return canAlter;
    },

    /**
     * Build the unique key for the dashboard
     *
     * @param {string} dashboardId
     *
     * @return {string}
     */
    _buildDashboardStateKey: function(dashboardId) {
        const module = this.module;

        const currentUserId = app.user.id;
        const stateKey = `${module}:${dashboardId}:${currentUserId}`;
        const lastStateKey = app.user.lastState.buildKey(
            'dashboard-filters',
            stateKey
        );

        return lastStateKey;
    },

    /**
     * Get last state for dashboard
     *
     * @param {string} lastStateKey
     *
     * @return {string}
     */
    _getDashboardLastState: function(lastStateKey) {
        const lastState = app.user.lastState.get(lastStateKey);

        if (lastState) {
            return JSON.parse(lastState);
        }

        return this._getDashboardDefaultState();
    },

    /**
     * Get the default state for user
     *
     * @return {Object}
     */
    _getDashboardDefaultState: function() {
        const defaultState = {};

        return defaultState;
    },
});

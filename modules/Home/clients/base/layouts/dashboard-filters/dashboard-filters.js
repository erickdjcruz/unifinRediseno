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
 * @class View.Layouts.Base.Home.DashboardFiltersLayout
 * @alias SUGAR.App.view.layouts.BaseHomeDashboardFiltersLayout
 * @extends View.Layouts.Base.Layout
 */
({
    className: 'h-full hidden',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['DashboardFiltersVisibility']);

        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();

        this._manageDashboardFiltersVisibility();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this._activeFilterGroupId = false;
        this._dashboardFilterView = false;
        this._filterGroups = false;

        this.dashboardId = this.model.id;

        this.componentLastStateKey = 'dashboard-filters';

        this.hasAccessToReports = app.acl.hasAccess('view', 'Reports');

        this.initFiltersVisibilityProperties();
    },

    /**
     * @inheritdoc
     */
    _registerEvents: function() {
        if (!this.hasAccessToReports) {
            return;
        }

        this.listenTo(this.context, 'dashboard-filter-toggled', this.toggleFilterComponent);
        this.listenTo(this.context, 'dashboard-filter-mode-updated', this.manageFilterState);
        this.listenTo(this.context, 'dashboard-filters-canceled', this.cancelDashboardFilters);
        this.listenTo(this.context, 'dashboard-filters-save', this.saveDashboardFilters);
        this.listenTo(this.context, 'dashboard-filters-apply', this.applyDashboardFilters);
        this.listenTo(this.model, 'sync', this.modelSync);
        this.listenTo(this.context, 'filter-operator-data-changed', this.updateGroupFilters);
        this.listenTo(this.context, 'dashboard-filters-interaction', this.updateGroupFilters);
    },

    /**
     * Model synced
     */
    modelSync: function() {
        const metadata = this.model.get('metadata');
        let filterGroups = {};

        if (_.has(metadata, 'filters')) {
            filterGroups = metadata.filters;
        }

        const currentUserRestrictedGroups = this.model.get('metadata').currentUserRestrictedGroups;

        if (!_.isUndefined(currentUserRestrictedGroups)) {
            this.currentUserRestrictedGroups = currentUserRestrictedGroups;
        }

        if (JSON.stringify(filterGroups) !== JSON.stringify(this._filterGroups)) {
            this._syncGroups();
        }
    },

    /**
     * Get the groups from meta, or create a new one
     *
     * @param {Object} options
     */
    _syncGroups: function(options = {}) {
        let filterGroups = {};
        const metadata = this.model.get('metadata');

        if (_.has(metadata, 'filters')) {
            filterGroups = _.isEmpty(metadata.filters) ? {} : metadata.filters;
        }

        this._filterGroups = app.utils.deepCopy(filterGroups);

        this._applyUserFilters(metadata, options);

        this.context.trigger('dashboard-filters-metadata-loaded', this._filterGroups);

        this.manageFilterState('detail');
    },

    /**
     * Apply User filters
     *
     * @param {Object} metadata
     */
    _applyUserFilters: function(metadata, options) {
        const lastStateKey = this._getUserLastStateKey();
        const userDashboardMetadata = app.user.lastState.get(lastStateKey);

        if (this.model.get('is_template') && !_.isEmpty(userDashboardMetadata)) {
            this._filterGroups = userDashboardMetadata.filters;
            return;
        }

        if (_.isEmpty(userDashboardMetadata) ||
            userDashboardMetadata.runtimeFiltersDateModified === metadata.runtimeFiltersDateModified) {
            return;
        }

        if (moment(userDashboardMetadata.runtimeFiltersDateModified).isBefore(metadata.runtimeFiltersDateModified)) {
            let showNotifyLastRefresh = true;

            if (!_.isEmpty(options) && _.has(options, 'notifyLastRefresh')) {
                showNotifyLastRefresh = options.notifyLastRefresh;
            }

            if (showNotifyLastRefresh) {
                app.alert.show('modify_since_last_refresh', {
                    level: 'info',
                    messages: app.lang.get('LBL_FILTER_UPDATES_SINCE_LAST_REFRESH', 'Dashboards'),
                    autoClose: true,
                });
            }

            this._resetLastState();
            return;
        }

        this._filterGroups = userDashboardMetadata.filters;
    },

    /**
     * Manage dashboard filters visibility
     */
    _manageDashboardFiltersVisibility: function() {
        const filtersVisible = this.isDashboardFiltersPanelActive();

        if (filtersVisible === true) {
            this.toggleFilterComponent(true);
        }
    },

    /**
     * Manage state
     *
     * @param {string} state
     */
    manageFilterState: function(state) {
        const isEdit = state === 'edit';
        const viewNameToBeCreated = isEdit ? 'dashboard-filters-edit' : 'dashboard-filters-detail';

        if (!this._dashboardFilterView || this._dashboardFilterView._editMode !== isEdit) {
            this._destroyDashboardFilterView();
            this._createDashboardFilterView(viewNameToBeCreated);
            this.toggleDashboardFabButton(state);

            return;
        }

        this._dashboardFilterView.manageDashboardFilters(this._filterGroups);
    },

    /**
     * Show/Hide fab button
     *
     * @param {string} state
     */
    toggleDashboardFabButton: function(state) {
        if (!_.has(this, 'layout') || !_.has(this.layout, 'layout')) {
            return;
        }

        const fabComponent = this.layout.layout.getComponent('dashboard-fab');

        if (!fabComponent) {
            return;
        }

        state === 'edit' ? fabComponent.hide() : fabComponent.show();
    },

    /**
     * Destroy dashboard filter view
     */
    _destroyDashboardFilterView: function() {
        if (this._dashboardFilterView) {
            this._dashboardFilterView.dispose();
            this._dashboardFilterView = false;
        }
    },

    /**
     * Create dashboard filter view
     *
     * @param {string} viewNameToBeCreated
     */
    _createDashboardFilterView: function(viewNameToBeCreated) {
        this._dashboardFilterView = app.view.createView({
            type: viewNameToBeCreated,
            context: this.context,
            model: this.model,
            layout: this,
        });

        this._dashboardFilterView.render();
        this.$('[data-container="dashboard-filters-container"]').append(this._dashboardFilterView.$el);

        this._dashboardFilterView.manageDashboardFilters(this._filterGroups);

        if (viewNameToBeCreated === 'dashboard-filters-detail') {
            this._dashboardFilterView.toggleLoading(true);
        }
    },

    /**
     * Set the new filters on the model
     */
    updateGroupFilters: function() {
        const metadata = app.utils.deepCopy(this.model.get('metadata'));
        metadata.filters = this._filterGroups;

        this.model.set('metadata', metadata, {silent: true});
        this.context.trigger('filter-field-updated');
    },

    /**
     * Cancel the dashboard filters state
     */
    cancelDashboardFilters: function() {
        if (this.model.revertAttributes) {
            this.model.revertAttributes({silent: true});
        }

        this._syncGroups();
    },

    /**
     * Return invalid groups
     *
     * @return {Array}
     */
    _invalidGroupsForSave: function() {
        const filterGroups = app.utils.deepCopy(this._filterGroups);

        const invalidGroups = _.chain(filterGroups)
            .map((item, key) => {
                return Object.assign({}, item, {key});
            })
            .filter((item) => {
                return item.fields.length < 1;
            })
            .pluck('key')
            .value();

        return invalidGroups;
    },

    /**
     * Check if we are able to save
     *
     * @return {boolean}
     */
    _isValidSave: function() {
        const invalidGroups = this._invalidGroupsForSave();

        if (invalidGroups.length > 0) {
            this.context.trigger('dashboard-filter-group-invalid-save', invalidGroups);

            app.alert.show('invalid_groups', {
                level: 'error',
                messages: app.lang.get('LBL_ONE_GROUP_REQUIRED', this.module),
                autoClose: true,
            });

            return false;
        }

        // validate each filter of each group
        if (this._dashboardFilterView && !this._dashboardFilterView.isValid()) {
            app.alert.show('runtime-filter-invalid', {
                level: 'error',
                messages: app.lang.get('LBL_RUNTIME_FILTERS_INVALID'),
                autoClose: true,
            });

            return false;
        }

        return true;
    },

    /**
     * Apply Dashboard Filters
     */
    applyDashboardFilters: function() {
        if (!this._isValidSave()) {
            return;
        }

        this.context.trigger('show-dashlet-loading');
        this._dashboardFilterView.toggleLoading(true);

        const metadata = app.utils.deepCopy(this.model.get('metadata'));
        const runtimeFiltersDateModified = this._getCurrentDatetime();

        metadata.runtimeFiltersDateModified = runtimeFiltersDateModified;
        metadata.filters = this._filterGroups;

        this._updateUserLastState(metadata);

        if (this.model.revertAttributes) {
            this.model.revertAttributes({silent: true});
        }
    },

    /**
     *
     * Save the dashboard filters state
     *
     */
    saveDashboardFilters: function() {
        if (!this._isValidSave()) {
            return;
        }

        this.context.trigger('show-dashlet-loading');
        this._dashboardFilterView.toggleLoading(true);

        const metadata = app.utils.deepCopy(this.model.get('metadata'));

        metadata.filters = this._filterGroups;

        const runtimeFiltersDateModified = this._getCurrentDatetime();
        metadata.runtimeFiltersDateModified = runtimeFiltersDateModified;

        this.model.set('metadata', metadata, {silent: true});
        this.model.save({}, {
            silent: true,
            showAlerts: false,
            success: () => {
                this._syncGroups({
                    notifyLastRefresh: false,
                });
                this._dashboardFilterView.toggleLoading(false);
                this.context.trigger('dashboard-saved-success');
                this.context.trigger('dashboard-filter-mode-changed', 'detail', true);
            },
            error: () => {
                app.alert.show('error_while_save', {
                    level: 'error',
                    title: app.lang.get('ERR_INTERNAL_ERR_MSG'),
                    messages: ['ERR_HTTP_500_TEXT_LINE1', 'ERR_HTTP_500_TEXT_LINE2']
                });
            }
        });
    },

    /**
     * Update user last state
     *
     * Only update the values for the user and not for the dashboard
     *
     * @param {Object} metadata
     */
    _updateUserLastState: function(metadata) {
        this._setLastState({
            filters: metadata.filters,
            runtimeFiltersDateModified: metadata.runtimeFiltersDateModified,
        });

        this._syncGroups();
        this._dashboardFilterView.toggleLoading(false);
        this.context.trigger('silent-refresh-dashlet-results', true);
    },

    /**
     * Get current UTC time
     *
     * @return {string}
     */
    _getCurrentDatetime: function() {
        return app.date().format('YYYY-MM-DD HH:mm:ss');
    },

    /**
     * Set last state
     *
     * @param {Object} filtersMetadata
     */
    _setLastState: function(filtersMetadata) {
        const lastStateKey = this._getUserLastStateKey();

        app.user.lastState.set(
            lastStateKey,
            filtersMetadata
        );
    },

    /**
     * Reset last state
     */
    _resetLastState: function() {
        const lastStateKey = this._getUserLastStateKey();

        app.user.lastState.set(lastStateKey, {});
    },

    /**
     * Get the unique key for the Dashboard and User combination
     *
     * @return {string}
     */
    _getUserLastStateKey: function() {
        const dashboardKey = `Dashboards:${this.dashboardId}`;
        const lastStateKey = app.user.lastState.buildKey(
            this.componentLastStateKey,
            app.user.id,
            dashboardKey
        );

        return lastStateKey;
    },

    /**
     * Function that adds the filter component to the dashboard
     *
     * @param {boolean} toggle
     */
    toggleFilterComponent: function(toggle) {
        this.$el.toggleClass('hidden', !toggle);

        this.context.trigger('dashboard-filter-mode-changed', 'detail');
        this.context.trigger('dashboard-filter-mode-updated', 'detail');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._destroyDashboardFilterView();

        this._super('_dispose');
    },
});

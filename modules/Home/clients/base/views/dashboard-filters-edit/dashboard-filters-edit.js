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
 * @class View.Views.Base.HomeDashboardFiltersEdit
 * @alias SUGAR.App.view.views.BaseHomeDashboardFiltersEdit
 * @extends View.View
 */
({
    className: 'dashboard-filter-container bg-[--dashlet-background] h-full overflow-hidden w-[230px]',
    plugins: ['DashboardFilters'],
    events: {
        'click [data-panelaction="createNewGroup"]': 'addNewFilterGroup',
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
     * Init properties
     */
    _initProperties: function() {
        this._editMode = true;

        this.initFiltersProperties();
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'dashboard-filter-widget-clicked', this.manageFieldInFilterGroup);
        this.listenTo(this.context, 'dashboard-filter-group-selected', this.filtersGroupSelected);
        this.listenTo(this.context, 'dashboard-filter-group-name-changed', this.filterGroupNameChanged);
        this.listenTo(this.context, 'dashboard-filter-group-removed', this.removeFilterGroup);
        this.listenTo(this.context, 'dashboard-filter-group-invalid-save', this.invalidGroupSave);
    },

    /**
     * Dashboard meta data has been loaded, we need to create the views
     */
    manageDashboardFilters: function(filterGroups) {
        this.handleDashboardFilters(filterGroups);

        this._toggleAddFilterButton();
    },

    /**
     * Toggle loading screen
     *
     * @param {boolean} toggle
     */
    toggleLoading: function(toggle) {
        this.$('.filters-skeleton-loader').toggleClass('hidden', !toggle);
        this.$('.dashboard-filters-edit-container').toggleClass('hidden', toggle);
    },

    /**
     * Either add or remove a filter in the group
     *
     * @param {Object} data
     */
    manageFieldInFilterGroup: function(data) {
        if (!this._filterGroups || _.isEmpty(_.keys(this._filterGroups))) {
            return;
        }

        this.context.trigger('dashboard-filters-interaction');

        if (data.highlighted) {
            this._addFieldToFilterGroup(data);
        } else {
            this._removeFieldFromFilterGroup(data);
        }
    },

    /**
     * Remove a group
     *
     * @param {string} groupId
     */
    removeFilterGroup: function(groupId) {
        if (!_.has(this._filterGroups, groupId) || !_.has(this._filterGroupsView, groupId)) {
            return;
        }

        this.context.trigger('dashboard-filters-interaction');

        this._filterGroupsView[groupId].dispose();

        delete this._filterGroups[groupId];
        delete this._filterGroupsView[groupId];

        const newSelectedGroup = _.chain(this._filterGroups).keys().first().value() || false;

        this._activeFilterGroupId = newSelectedGroup;

        this.notifyGroupsUpdated();
        this._toggleAddFilterButton();
    },

    /**
     * Create new empty group from UI
     */
    addNewFilterGroup: function() {
        this.context.trigger('dashboard-filters-interaction');

        this._createNewGroup();
        this.notifyGroupsUpdated();
        this._toggleAddFilterButton();
    },

    /**
     * Triggered when a group name is updated
     *
     * @param {string} groupLabel
     * @param {string} groupId
     */
    filterGroupNameChanged: function(groupLabel, groupId) {
        if (!_.has(this._filterGroups, groupId)) {
            return;
        }

        this._filterGroups[groupId].label = groupLabel;

        this.notifyGroupsUpdated();
    },

    /**
     * Change active filters group
     *
     * @param {string} filtersGroupId
     */
    filtersGroupSelected: function(filtersGroupId) {
        if (!_.has(this._filterGroups, filtersGroupId) || this._activeFilterGroupId === filtersGroupId) {
            return;
        }

        this._activeFilterGroupId = filtersGroupId;

        this.notifyGroupsSelected();
    },

    /**
     * Show/Hide Add Filter Button
     */
    _toggleAddFilterButton: function() {
        const showNoFilterContainer = _.isEmpty(this._filterGroups);

        this.$('[data-container="no-filter-container"]').toggleClass('hidden', !showNoFilterContainer);
        this.$('[data-container="add-container"]').toggleClass('hidden', !showNoFilterContainer);
        this.$('.edit-add-group').toggleClass('hidden', showNoFilterContainer);
    },

    /**
     * Create a new filter group
     */
    _createNewGroup: function() {
        const groupId = app.utils.generateUUID();
        const groupMeta = {
            label: app.lang.get('LBL_DASHBOARD_FILTER_GROUP'),
            fieldType: false,
            fields: [],
            filterDef: {
                qualifier_name: 'not_empty',
            },
            fieldsDashlet: [],
        };

        const isSelected = true;

        this.createGroup(groupId, groupMeta, isSelected);
    },

    /**
     * Add a filter to the selected group
     *
     * @param {Object} data
     */
    _addFieldToFilterGroup: function(data) {
        let filterGroup = this._filterGroups[this._activeFilterGroupId];

        filterGroup.fields = _.without(filterGroup.fields, _.findWhere(filterGroup.fields, {
            dashletId: data.dashletId,
        }));

        filterGroup.fieldType = data.field.type;

        filterGroup.fields.push({
            dashletId: data.dashletId,
            fieldName: data.field.name,
            fieldDef: data.field,
            targetModule: data.targetModule,
            tableKey: data.filter.table_key,
            dashletLabel: data.dashletLabel,
            isRelated: data.isRelated,
            targetFieldModule: this.targetFieldModule,
            dashletSpecificData: data.dashletSpecificData,
        });

        this._filterGroups[this._activeFilterGroupId] = filterGroup;
        const groupView = this._filterGroupsView[this._activeFilterGroupId];

        groupView.toggleGroupInvalid(false);

        this.notifyGroupsUpdated();
    },

    /**
     * Remove a filter from the selected group
     *
     * @param {Object} data
     */
    _removeFieldFromFilterGroup: function(data) {
        let filterGroup = this._filterGroups[this._activeFilterGroupId];

        filterGroup.fields = _.without(filterGroup.fields, _.findWhere(filterGroup.fields, {
            fieldName: data.field.name,
            dashletId: data.dashletId,
        }));

        if (_.isEmpty(filterGroup.fields)) {
            filterGroup.fieldType = false;
            filterGroup.filterDef = {
                qualifier_name: 'not_empty',
            };
        }

        this._filterGroups[this._activeFilterGroupId] = filterGroup;

        this.notifyGroupsUpdated();
    },
});

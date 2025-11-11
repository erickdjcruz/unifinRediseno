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
(function register(app) {
    app.events.on('app:init', function init() {
        app.plugins.register('DashboardFilters', ['view'], {
            /**
             * @inheritdoc
             */
            onAttach: function() {
                this.on('init', function registerDashboardFilters() {
                });
            },

            /**
             * @inheritdoc
             */
            initFiltersProperties: function() {
                this._activeFilterGroupId = false;
                this._hasGroupFilters = false;
                this._isGroupClicked = false;

                this._filterGroups = {};
                this._filterGroupsView = {};
            },

            /**
             * Here we know that some groups are invalid
             *
             * @param {Array} invalidGroups
             */
            invalidGroupSave: function(invalidGroups) {
                this.markInvalidGroups(invalidGroups);
            },

            /**
             * Checks if the groups are valid
             *
             * @return {boolean}
             */
            isValid: function() {
                let isValid = true;

                _.each(this._filterGroupsView, (group) => {
                    const groupValid = group.isValid();

                    group.toggleGroupInvalid(!groupValid);

                    if (isValid) {
                        isValid = groupValid;
                    }
                });

                return isValid;
            },

            /**
             * Mark the groups as invalid in the UI
             *
             * @param {Array} groups
             */
            markInvalidGroups: function(groups) {
                _.each(groups, (key) => {
                    const groupView = this._filterGroupsView[key];

                    groupView.toggleGroupInvalid(true);
                });
            },
            /**
             * Destroy current groups view
             */
            destroyCurrentViews: function() {
                _.each(this._filterGroupsView, (group) => {
                    if (group && _.isFunction(group.dispose)) {
                        group.dispose();
                    }
                });

                this._filterGroupsView = {};
            },

            /**
             * Create group
             *
             * @param {string} groupId
             * @param {Object} groupMeta
             * @param {boolean} isSelected
             */
            createGroup: function(groupId, groupMeta, isSelected) {
                this._filterGroups[groupId] = groupMeta;
                if (this.layout instanceof app.view.Layout && _.isArray(this.layout.currentUserRestrictedGroups)) {
                    groupMeta.currentUserRestrictedGroup = _.contains(this.layout.currentUserRestrictedGroups, groupId);
                }

                if (this._editMode && isSelected) {
                    this.context.trigger('dashboard-filter-group-selected', groupId);

                    this._activeFilterGroupId = groupId;
                }

                this.createGroupFilter(groupId, groupMeta);
            },

            /**
             * Create filter widget element
             *
             * @param {string} groupId
             * @param {Object} groupMeta
             */
            createGroupFilter: function(groupId, groupMeta) {
                const widgetNo = Object.keys(this._filterGroups).length;

                const filterWidget = app.view.createView({
                    type: 'dashboard-filter-group-widget',
                    module: 'Home',
                    editMode: this._editMode,
                    groupId,
                    groupMeta,
                    widgetNo,
                });

                filterWidget.render();

                this.$('[data-container="group-filters-container"]').append(filterWidget.$el);

                this._filterGroupsView[groupId] = filterWidget;
            },

            /**
             * Dashboard meta data has been loaded, we need to create the views
             */
            handleDashboardFilters: function(filterGroups) {
                this._activeFilterGroupId = false;
                this.destroyCurrentViews();

                this._filterGroups = filterGroups;

                if (!_.isEmpty(this._filterGroups)) {
                    this._activeFilterGroupId = _.first(_.keys(this._filterGroups));
                }

                _.each(this._filterGroups, function createGroup(groupMeta, groupId) {
                    this.createGroup(groupId, groupMeta, groupId === this._activeFilterGroupId);
                }, this);

                if (this._editMode) {
                    if (_.isEmpty(this._filterGroups)) {
                        this._createNewGroup();
                    }
                }

                this.notifyGroupsUpdated();
            },

            /**
             * Notify listeners that groups' data has changed
             */
            notifyGroupsUpdated: function() {
                this.context.trigger('filter-groups-updated', this._filterGroups, this._activeFilterGroupId);
            },

            /**
             * Notify listener that group has been selected
             */
            notifyGroupsSelected: function() {
                this._isGroupClicked = true;

                this.context.trigger('filter-groups-updated', this._filterGroups, this._activeFilterGroupId,
                    this._isGroupClicked);
            },

            /**
             * Unbind events on dispose.
             */
            onDetach: function() {
                this.destroyCurrentViews();
            },
        });
    });
})(SUGAR.App);

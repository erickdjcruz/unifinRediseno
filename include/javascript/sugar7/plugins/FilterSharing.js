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
(function(app) {
    app.events.on('app:init', function() {
        /**
         * Plugin intended to use with dashboard components. It shares filters found on
         * list view dashlets with the teams the dashboard is shared with.
         */
        app.plugins.register('FilterSharing', ['layout', 'view'], {
            /**
             * A list of filters that are already defined  - through metadata - and are not meant to be modified.
             */
            predefinedFilters: null,

            /**
             * Checks if the current layout/view is related to Dashboards.
             *
             * @return {boolean} True if on a dashboard layout/record.
             */
            isDashboard: function() {
                return this.name === 'dashboard' || (this.type === 'record' && this.module === 'Dashboards');
            },

            /**
             * Checks if the given dashlet is a list view dashlet and if it has a filter applied.
             *
             * @param {Object} dashlet The metadata of a dashlet component
             * @return {boolean} True if the dashlet is a list view dashlet with a filter id.
             */
            isFilteredListViewDashlet: function(dashlet) {
                return dashlet.view && dashlet.view.type === 'dashablelist' &&
                    dashlet.view.filter_id && this.isCustomFilter(dashlet.view.filter_id);
            },

            /**
             * Checks for predefined filters in the metadata and returns their ids.
             *
             * @return {Array} The list of filter ids.
             */
            getMetadataFilterIds: function() {
                var allFilters = _.flatten(_.compact(_.map(app.metadata.getModules(), function(module) {
                    return module.filters && module.filters.basic &&
                        module.filters.basic.meta && module.filters.basic.meta.filters;
                })));
                this.predefinedFilters = _.unique(_.pluck(allFilters, 'id'));
                return this.predefinedFilters;
            },

            /**
             * Checks if the given filter id is a unique filter id or a predefined filter
             * identifier which has a current user based filter definition.
             *
             * @param {string} filterId A filter id or a predefined filter identifier.
             * @return {boolean} True if the filter is a predefined filter.
             */
            isCustomFilter: function(filterId) {
                var filters = this.predefinedFilters ? this.predefinedFilters : this.getMetadataFilterIds();
                return !_.contains(filters, filterId);
            },

            /**
             * Will lookup any filters used on any list view dashlets
             * on the given dashboard.
             *
             * @return {Array} The list of filters to be shared.
             */
            getListViewFilterIds: function() {
                var ids = [];
                var metadata = this.model.get('metadata');

                if (metadata && metadata.components) {
                    _.each(metadata.components, function(section) {
                        _.each(section.rows, function(row) {
                            _.each(row, function(dashlet) {
                                if (this.isFilteredListViewDashlet(dashlet)) {
                                    ids.push(dashlet.view.filter_id);
                                }
                            }, this);
                        }, this);
                    }, this);
                }

                return ids;
            },

            /**
             * To have the filter shared only with those intended to have access
             * we need to remove the global team from the list of teams.
             *
             * @param {Object} filterData Detailed information about a filter that can be converted to a bean.
             * @return {Bean} The filter model without the global team.
             */
            getPrivateFilter: function(filterData) {
                var filterModel = app.data.createBean('Filters', filterData);
                var filterTeams = filterModel.get('team_name');
                var globalTeam = _.findWhere(filterTeams, {id: '1'});

                filterModel.set('team_name', _.without(filterTeams, globalTeam));

                return filterModel;
            },

            /**
             * It will create a new filter bean based on the response of a filter request.
             * Update filter model If there are any new teams the dashboard is shared with or new user assigned to it.
             *
             * @param {Object} filterUsersData Information about assigned filter id and array of teams.
             * @param {Object} filter Detailed information about a filter that can be converted to a bean.
             */
            updateListViewFilters: function(filterUsersData, filter) {
                var filterModel = this.getPrivateFilter(filter);
                var isFilterTeamsUpdated = this.updateFilterTeams(filterModel, filterUsersData.dashboardTeams);
                if (isFilterTeamsUpdated) {
                    filterModel.save();
                }
            },

            /**
             * Checks if the filter dashboard teams are different compared to the filter's teams.
             * In case there is a change we apply the dashboard's teams on filters', thus sharing
             * the filter with the same teams.
             * TODO: When filter management will be enabled this logic will need to be replaced,
             * to take into account the exact change made.
             *
             * @param {Object} filterModel Information about Fliter teams.
             * @param {Array} dashboardTeams List of updated teams sharing the Dashboard
             * @return {boolean} True if changes acquire in filter teams.
             */
            updateFilterTeams: function(filterModel, dashboardTeams) {
                var filterTeams = filterModel.get('team_name');
                var hasNewFilterTeam = _.some(filterTeams, function(team) {
                    return !_.findWhere(dashboardTeams, {id: team.id});
                });
                var hasNewDashboardTeam = _.some(dashboardTeams, function(team) {
                    return !_.findWhere(filterTeams, {id: team.id});
                });
                var isFilterTeamsChanged = hasNewDashboardTeam || hasNewFilterTeam;

                if (isFilterTeamsChanged) {
                    filterModel.set('team_name', dashboardTeams);
                }
                return isFilterTeamsChanged;
            },

            /**
             * Initializes an update on any filters used on a list view dashlet.
             * For being able to update the filters, first we need to get them.
             */
            triggerListviewFilterUpdate: function() {
                if (this.isDashboard()) {
                    var filterIds = this.getListViewFilterIds();
                    var dashboardTeams = this.model.get('team_name');
                    var assignedUserId = this.model.get('assigned_user_id');

                    _.each(filterIds, function(filterId) {
                        var url = app.api.buildURL('Filters/' + filterId, null, null);
                        var filterUsersData = {
                            assignedUserId: assignedUserId,
                            dashboardTeams: dashboardTeams
                        };
                        app.api.call('GET', url, null, {
                            success: _.bind(this.updateListViewFilters, this, filterUsersData),
                            error: function() {
                                app.logger.error('Filter can not be read, thus is not shared. Filter id: ' + filterId);
                            }
                        });
                    }, this);
                }
            }
        });
    });
})(SUGAR.App);

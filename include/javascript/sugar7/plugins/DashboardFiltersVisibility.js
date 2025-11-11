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
        app.plugins.register('DashboardFiltersVisibility', ['view', 'layout'], {
            /**
             * Get dashboard filter active key
             *
             * @return {string}
             */
            getDashboardFilterActiveKey: function() {
                const dashboardFilterActiveKey = this.model.get('id') +
                    ':' + app.user.id +
                    ':' + this._dashboardFilterActiveKey;

                return dashboardFilterActiveKey;
            },

            /**
             * Init filters visibility properties
             */
            initFiltersVisibilityProperties: function() {
                this._dashboardFilterActiveKey = 'dashboard-filter-active';

                this._filtersOnScreen = this.isDashboardFiltersPanelActive();
            },

            /**
             * Returns whether dashboard filters panel is active or not
             *
             * @return {boolean}
             */
            isDashboardFiltersPanelActive: function() {
                const dashboardFilterActiveKey = this.getDashboardFilterActiveKey();
                const dashboardFiltersActive = !!app.user.lastState.get(dashboardFilterActiveKey);

                return dashboardFiltersActive;
            },

            /**
             * Store filter panel state
             *
             * @param {boolean} active
             */
            storeFilterPanelState: function(active) {
                const dashboardFilterActiveKey = this.getDashboardFilterActiveKey();

                app.user.lastState.set(dashboardFilterActiveKey, active);
            },
        });
    });
})(SUGAR.App);

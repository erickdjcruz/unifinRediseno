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
 * @class View.Layouts.Base.Calendar.MainSchedulerLayout
 * @alias SUGAR.App.view.layouts.BaseCalendarMainSchedulerLayout
 * @extends View.Layouts.Base.BaseLayout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.initVars();

        options.context.set('skipFetch', true);

        this._super('initialize', [options]);

        this.listenTo(this.context, 'calendars:cache:force-refresh', _.bind(this.forceCalendarRefresh, this));
    },

    /**
     * Initialize variables
     */
    initVars: function() {
        /**
         * Identifier of the type of calendar we are showing in this component
         */
        this.componentStorageKey = 'main-panel';

        /**
         * Scheduler key used to store calendar configurations
         * Each location where user can modify the calendar on the fly, has it's own key
         * ie: main / lists / subpanels
         */
        this.keyToStoreCalendarConfigurations = app.Calendar.utils.buildUserKeyForStorage(this.componentStorageKey);
    },

    /**
     * Force Calendar Refresh
     *
     * Setup context parameters like calendars, location... then update context
     */
    forceCalendarRefresh: function() {
        let schedulerContext = this.getContextSettingsForScheduler();
        this.context.set(schedulerContext);
    },

    /**
     * @override
     */
    _render: function() {
        let schedulerContext = this.getContextSettingsForScheduler();
        this.context.set(schedulerContext);

        this._super('_render');
    },

    /**
     * Get context settings for scheduler component
     *
     * @return {Object}
     */
    getContextSettingsForScheduler: function() {
        let calendarConfigurations = app.Calendar.utils.getConfigurationsByKey(this.keyToStoreCalendarConfigurations);

        calendarConfigurations.otherCalendars = _.filter(calendarConfigurations.otherCalendars,
            function filterCalendars(calendar) {
                return calendar.selected;
            }
        );
        calendarConfigurations = calendarConfigurations.myCalendars.concat(calendarConfigurations.otherCalendars);

        const keyToStoreCalendarView = app.Calendar.utils.buildUserKeyForStorage('main');
        const defaultView = app.cache.get(keyToStoreCalendarView) || 'expandedMonth';

        return {
            module: this.module,
            calendars: calendarConfigurations,
            defaultView: defaultView,
            visibleRelationsInContextList: [],
            location: 'main',
            keyToStoreCalendarConfigurations: this.keyToStoreCalendarConfigurations,
            customKendoOptions: {
                listExportButtons: true
            }
        };
    },

    /**
     * @override
     */
    _dispose: function() {
        this.context.off('calendars:cache:force-refresh');
        this._super('_dispose');
    }
});

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
 * @class View.Views.Base.Users.CopyContentItemsView
 * @alias SUGAR.App.view.layouts.BaseUsersCopyContentItemsView
 * @extends View.Views.Base.View
 */
 ({
    /**
     * Modules which can't be used to copy/clone dashboards/filters from
     */
    denyModules: [
        'Login', 'Sync', 'Connectors', 'CustomQueries', 'EAPM', 'FAQ', 'OAuthKeys', 'OAuthTokens',
        'SNIP', 'Styleguide', 'SugarLive', 'Trackers', 'TrackerQueries', 'TrackerSessions', 'TrackerPerfs',
        'UpgradeWizard', 'WebLogicHooks', 'iFrames', 'TimePeriods', 'TeamNotices', 'DataSets', 'SugarFavorites',
        'SavedSearch', 'PdfManager', 'Teams', 'ACLRoles', 'Releases',
    ],

    /**
     * Default selected section
     */
    section: 'user_prefs',

    /**
     * default subsection
     */
    selectingSection: 'from_modules',

    /**
     * @inheritdoc
     */
    events: {
        'change .ut-pref-choice': 'changeSection',
        'change .fromUser': 'changeFromUser',
        'change [name=selection]': 'changeSelection',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        let modulesMeta = app.metadata.getModules({
            filter: 'display_tab',
            access: true,
        });

        this.modules = Object.keys(modulesMeta)
            .filter(key => !this.denyModules.includes(key))
            .reduce((obj, key) => {
                obj[key] = modulesMeta[key];
                return obj;
            }, {});

        this.currentUserId = app.user.id;
        this.sourceUser = app.user.id;
        this.retrieveUsers();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this._renderDropdowns();
        this.updateSelectionFilters();
    },

    /**
     * retrieve the users in order to display them in the user select field
     */
    retrieveUsers: function() {
        const usersUrl = app.api.buildURL('Users', null, null, {
            filter: [{
                status: {
                    $equals: 'Active'
                }
            }],
            max_num: -1,
            order_by: 'first_name:asc',
        });

        app.api.call('read', usersUrl, null, {
            success: _.bind(function(data) {
                this.users = data.records;
                this.render();
            }, this),
            error: function(error) {
                app.alert.show('user-utils-error', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_USER_UTILS_DATA_ERROR', this.module),
                });
            },
        });
    },

    /**
     * When the section dropdown changes
     *
     * @param {Event} evt
     */
    changeSection: function(evt) {
        this.section = evt.target.value;

        switch (this.section) {
            case 'user_prefs':
                this.$('.user-prefs-section').removeClass('hide');
                this.$('.dashboards-section').addClass('hide');
                this.$('.filters-section').addClass('hide');
                break;
            case 'dashboards':
                this.$('.user-prefs-section').addClass('hide');
                this.$('.dashboards-section').removeClass('hide');
                this.$('.filters-section').addClass('hide');
                this.selectingSection = 'from_modules';
                this._setDefaultSelection(this.section);
                break;
            case 'filters':
                this.$('.user-prefs-section').addClass('hide');
                this.$('.dashboards-section').addClass('hide');
                this.$('.filters-section').removeClass('hide');
                this.selectingSection = 'from_modules';
                this._setDefaultSelection(this.section);
                break;
        }
    },

    /**
     * Sets the default selection for modules
     *
     * @param {string} section
     */
    _setDefaultSelection: function(section) {
        this.$(`.${section}-section [name="selection"][value="from_modules"]`).prop('checked', true);
        this.$(`.${section}-section [name="selection"][value="from_modules"]`).trigger('change');
    },

    /**
     * show selects as select2
     */
    _renderDropdowns: function() {
        this.$('.select-modules').select2({
            allowClear: true,
            containerCssClass: 'select2-choices-pills-close',
        });
    },

    /**
     * event for changing the "from" user
     *
     * @param {Event} evt
     */
    changeFromUser: function(evt) {
        const target = evt.target;
        this.sourceUser = target.options[target.selectedIndex].dataset.id;

        this.updateSelectionFilters();
    },

    /**
     * Update the dashboards and filters hybrid select initial filter
     */
    updateSelectionFilters: function() {
        this.getField('dashboards_select').context.set('assigned_user', this.sourceUser);
        this.getField('filters_select').context.set('assigned_user', this.sourceUser);
    },

    /**
     * When changing sections make sure the other sections are hidden
     *
     * @param {Event} evt
     */
    changeSelection: function(evt) {
        this.selectingSection = evt.target.value;

        switch (this.selectingSection) {
            case 'existing_dashboards':
                this.$('.existing-dashboards').removeClass('hide');
                this.$('.from-modules').addClass('hide');
                this.$('.existing-filters').addClass('hide');
                break;
            case 'existing_filters':
                this.$('.existing-dashboards').addClass('hide');
                this.$('.from-modules').addClass('hide');
                this.$('.existing-filters').removeClass('hide');
                break;
            case 'from_modules':
                this.$('.existing-dashboards').addClass('hide');
                this.$('.from-modules').removeClass('hide');
                this.$('.existing-filters').addClass('hide');
                break;
        }
    },

    /**
     * Return the selected preferences
     */
    getSelectedPrefTypes: function() {
        const selectedTypes = [];

        const favoriteReports = this.$('[name=favorite_reports]').prop('checked') &&
            selectedTypes.push('CloneFavoriteReports');
        const sugarEmailClient = this.$('[name=sugar_email_client]').prop('checked') &&
            selectedTypes.push('CloneSugarEmailClient');
        const scheduledReporting = this.$('[name=scheduled_reporting]').prop('checked') &&
            selectedTypes.push('CloneScheduledReporting');
        const notifyOnAssignement = this.$('[name=notify_on_assignment]').prop('checked') &&
            selectedTypes.push('CloneNotifyOnAssignment');
        const reminderOptions = this.$('[name=reminder_options]').prop('checked') &&
            selectedTypes.push('CloneRemindersOptions');
        const defaultTeams = this.$('[name=default_teams]').prop('checked') &&
            selectedTypes.push('CloneDefaultTeams');
        const navigationBar = this.$('[name=navigation_bar]').prop('checked') &&
            selectedTypes.push('CloneNavigationBar');

        return selectedTypes;
    },

    /**
     * Get modules for filter cloning
     */
    getModulesForFilters: function() {
        return this.$('.filters-section select.select-modules').val();
    },

    /**
     * Get modules for dashboard cloning
     */
    getModulesForDashboards: function() {
        return this.$('.dashboards-section select.select-modules').val();
    },

    /**
     * Reset the current section to user_prefs
     */
    resetSection: function() {
        this.section = 'user_prefs';
        this.selectingSection = 'from_modules';
    }
});

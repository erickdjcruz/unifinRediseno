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
 * @class View.Views.Base.Users.CopyContentButtonsView
 * @alias SUGAR.App.view.layouts.BaseUsersCopyContentButtonsView
 * @extends View.Layouts.Base.View
 */
 ({
    /**
     * @inheritdoc
     */
    events: {
        'click [name=copy_button]': 'copy',
        'click [name=clear_button]': 'clear',
        'click [name=cancel_button]': 'cancel',
    },

    /**
     * Cloning name->label map
     * Used in alerts
     */
    cloningTypes: {
        'CloneFavoriteReports': 'LBL_FAVORITE_REPORTS',
        'CloneSugarEmailClient': 'LBL_SUGAR_EMAIL_CLIENT',
        'CloneScheduledReporting': 'LBL_SCHEDULED_REPORTING',
        'CloneNotifyOnAssignment': 'LBL_NOTIFY_ON_ASSIGNMENT',
        'CloneRemindersOptions': 'LBL_REMINDER_OPTIONS',
        'CloneDefaultTeams': 'LBL_DEFAULT_TEAMS',
        'CloneNavigationBar': 'LBL_NAVBAR_SELECTION',
        'CloneDashboards': 'LBL_DASHBOARDS_UTILS',
        'CopyDashboards': 'LBL_EXISTING_DASHBOARD',
        'CloneFilters': 'LBL_FILTERS',
        'CopyFilters': 'LBL_EXISTING_FILTERS',
        'CloneUserSettings': 'LBL_USER_LOCALE',
    },

    /**
     * Go back to the users list
     */
    cancel: function() {
        app.router.navigate('#Users', {trigger: true});
    },

    /**
     * Re-renders the layout so all inputs are cleared
     */
    clear: function() {
        this.layout.render();
        if (this.layout.getComponent('copy-content-items') instanceof app.view.View) {
            this.layout.getComponent('copy-content-items').resetSection();
        }
    },

    /**
     * Copy settings to users
     */
    copy: function() {
        const itemsView = this.layout.getComponent('copy-content-items');
        const usersView = this.layout.getComponent('copy-content-users');

        const destinationUsers = usersView.getField('users_select').getSelected();
        const destinationTeams = usersView.getField('teams_select').getSelected();
        const destinationRoles = usersView.getField('roles_select').getSelected();

        const dashboards = itemsView.getField('dashboards_select').getSelected();
        const filters = itemsView.getField('filters_select').getSelected();

        // used later in the success message
        let modules = _.isEmpty(itemsView.getModulesForDashboards()) ?
             itemsView.getModulesForFilters() : itemsView.getModulesForDashboards();
        this.context.set('cloneModules', modules);
        this.context.set('copyDashboards', dashboards);
        this.context.set('copyFilters', filters);

        const selectingSection = itemsView.selectingSection;
        const section = itemsView.section;
        const sourceUser = itemsView.sourceUser;

        switch (section) {
            case 'user_prefs':
                const types = itemsView.getSelectedPrefTypes();
                let payload = [];
                for (const type of types) {
                    payload.push({
                        type: type,
                        sourceUser: sourceUser,
                        destinationUsers: destinationUsers,
                        destinationTeams: destinationTeams,
                        destinationRoles: destinationRoles,
                    });
                }
                this.callCommand(payload);
                break;
            case 'dashboards':
                let dashboardsPayload = [{
                    sourceUser: sourceUser,
                    destinationUsers: destinationUsers,
                    destinationTeams: destinationTeams,
                    destinationRoles: destinationRoles,
                    dashboards: dashboards,
                    modules: itemsView.getModulesForDashboards(),
                }];

                _.first(dashboardsPayload).type = selectingSection === 'from_modules' ?
                    'CloneDashboards' : 'CopyDashboards';

                this.callCommand(dashboardsPayload);
                break;
            case 'filters':
                let filtersPayload = [{
                    sourceUser: sourceUser,
                    destinationUsers: destinationUsers,
                    destinationTeams: destinationTeams,
                    destinationRoles: destinationRoles,
                    filters: filters,
                    modules: itemsView.getModulesForFilters(),
                }];

                _.first(filtersPayload).type = selectingSection === 'from_modules' ?
                    'CloneFilters' : 'CopyFilters';

                this.callCommand(filtersPayload);
                break;
        }
    },

    /**
     * Makes api call to UserUtilities.
     *
     * @param {Array} payload
     */
    callCommand: function(payload) {
        const validatedPayload = this.validatePayload(payload);

        if (!validatedPayload) {
            return;
        }

        app.alert.show('utils-processing', {
            level: 'process',
            title: app.lang.getModString('LBL_IN_PROGRESS', this.module),
        });

        const url = app.api.buildURL('userUtilities');
        const commands = this.getCommandNames(payload);
        const messageLabel = this.getMessageLabel(payload);

        app.api.call('create', url, {'actions': payload}, {
            success: _.bind(function(commands, messageLabel) {
                let destinationList = this.context.get('destinationList');
                if (destinationList.length >= 20) {
                    const messageLabel = app.lang.getModString('LBL_UTILS_USER_TEAMS_ROLES', this.module);
                    destinationList =
                        destinationList.length + ' ' + messageLabel;
                }

                if (_.isArray(destinationList)) {
                    destinationList = destinationList.join(', ');
                }

                let alertAttributes = {
                    commands: commands.join(', '),
                    destinationList: destinationList,
                };

                if (messageLabel === 'LBL_COPY_CONTENT_COUNT_SUCCESS') {
                    alertAttributes.count =
                        this.context.get('copyDashboards').length || this.context.get('copyFilters').length;
                }

                if (messageLabel === 'LBL_COPY_CONTENT_CLONE_MODULES_SUCCESS') {
                    alertAttributes.moduleList = this.context.get('cloneModules').join(', ');
                }

                app.alert.show('utils-success', {
                    level: 'success',
                    messages: app.lang.getModString(messageLabel, this.module, alertAttributes),
                    autoClose: false
                });
                this.clear();
            }, this, commands, messageLabel),
            error: function(error) {
                app.alert.show('utils-error', {
                    level: 'error',
                    messages: error.message,
                });
            },
            complete: function() {
                app.alert.dismiss('utils-processing');
            }
        });
    },

    /**
     * Finds the right message to display in case of success
     *
     * @param {Array} payload
     */
    getMessageLabel: function(payload) {
        for (const command of payload) {
            if (command.type === 'CloneDashboards' || command.type === 'CloneFilters') {
                return 'LBL_COPY_CONTENT_CLONE_MODULES_SUCCESS';
            }

            if (command.type === 'CopyDashboards' || command.type === 'CopyFilters') {
                return 'LBL_COPY_CONTENT_COUNT_SUCCESS';
            }
        }

        return 'LBL_COPY_CONTENT_SUCCESS';
    },

    /**
     * Returns an array of the commands given
     *
     * @param {Array} payload
     */
    getCommandNames: function(payload) {
        let commandNames = [];

        for (const command of payload) {
            const commandName = this.parseCommandType(command.type);
            commandNames.push(commandName);
        }

        return commandNames;
    },

    /**
     * Returns the name of the command action
     *
     * @param {string} commandType
     */
    parseCommandType: function(commandType) {
        return app.lang.getModString(this.cloningTypes[commandType], this.module);
    },

    /**
     * Validates the command payload
     *
     * @param {Array} payload
     */
    validatePayload: function(payload) {
        const destinationList = this.context.get('destinationList');
        const contentView = this.layout.name === 'copy-content' ?
                          this.layout.getComponent('copy-content-items') :
                          this.layout.getComponent('copy-content-locale');
        const selectedActions = contentView.$('input:checked');

        if (destinationList.length == 0) {
            app.alert.show('utils-error-no-users', {
                level: 'error',
                messages: app.lang.getModString('LBL_NO_DESTINATION', this.module),
            });

            return false;
        }

        if (this.layout.name === 'copy-content' && selectedActions.length === 0) {
            app.alert.show('utils-error-no-prefs', {
                level: 'error',
                messages: app.lang.getModString('LBL_NO_USER_PREFERENCES', this.module),
            });

            return false;
        }

        for (const command of payload) {
            if (command.type === 'CopyDashboards' && command.dashboards.length === 0) {
                app.alert.show('utils-error-no-dashboards', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_NO_DASHBOARD', this.module),
                });

                return false;
            }

            if ((command.type === 'CloneDashboards' || command.type === 'CloneFilters') &&
                command.modules.length === 0) {
                app.alert.show('utils-error-no-modules', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_NO_MODULES', this.module),
                });

                return false;
            }

            if (command.type === 'CopyFilters' && command.filters.length === 0) {
                app.alert.show('utils-error-no-filters', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_NO_FILTERS', this.module),
                });

                return false;
            }

            if (this.layout.name === 'copy-user-settings' && selectedActions.length === 0) {
                app.alert.show('utils-error-no-settings', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_NO_USER_SETTINGS', this.module),
                });

                return false;
            }
        }

        return true;
    }
});

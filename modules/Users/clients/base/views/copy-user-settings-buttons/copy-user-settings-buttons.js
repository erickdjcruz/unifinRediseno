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
 * @class View.Views.Base.Users.CopyUserSettingsButtonsView
 * @alias SUGAR.App.view.layouts.BaseUsersCopyUserSettingsButtonsView
 * @extends View.Layouts.Base.View
 */
({
    extendsFrom: 'UsersCopyContentButtonsView',

    /**
     * @inheritdoc
     */
    copy: function() {
        let userSettings = {};
        const destinationUsers = this.context.get('destinationUsers');
        const destinationTeams = this.context.get('destinationTeams');
        const destinationRoles = this.context.get('destinationRoles');

        if (_.isEmpty(destinationRoles) && _.isEmpty(destinationTeams) && _.isEmpty(destinationUsers)) {
            app.alert.show('user-utils-error', {
                level: 'error',
                messages: app.lang.getModString('LBL_USER_UTILITIES_DESTINATION_USER_ERROR', this.module),
            });
            return;
        }

        const settingsView = this.layout.getComponent('copy-content-locale');
        const selectedSettings = settingsView.$('input:checked');

        for (const setting of selectedSettings) {
            settingName = setting.name;
            userSettings[settingName] = this._getUserSetting(settingName, settingsView);
        }

        const payload = [{
            type: 'CloneUserSettings',
            userSettings: userSettings,
            destinationUsers: destinationUsers,
            destinationTeams: destinationTeams,
            destinationRoles: destinationRoles,
        }];

        this.callCommand(payload);
    },

    /**
     * Gets the value for the setting
     *
     * @param {string} settingName
     */
    _getUserSetting: function(settingName, settingsView) {
        let setting = settingsView.$(`select[name=${settingName}]`).val();

        if (!setting) {
            setting = settingsView.$(`input[name=${settingName}]:not([type=checkbox])`).val();
        }

        return setting;
    }
});

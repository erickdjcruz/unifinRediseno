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
 * @class View.Views.Base.DRI_Workflows.ConfigureModulesHeaderpaneView
 * @alias SUGAR.App.view.views.BaseDRI_WorkflowsConfigureModulesHeaderpaneView
 * @extends View.Views.Base.HeaderpaneView
 */
 ({
    extendsFrom: 'HeaderpaneView',

    events: {
        'click a[name=save_button]': 'saveConfig',
        'click a[name="cancel_button"]': 'cancelConfig',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.context.set('skipFetch', true);
        this.title = app.lang.get('LBL_CONFIGURE_MODULES_CONTENT_TITLE', this.module);

        if (!app.user.hasAutomateLicense()) {
            app.CJBaseHelper.invalidLicenseError();
        }
    },

    /**
     * Save changes to config parameters
     */
    saveConfig: function() {
        let configModule = this.layout.getComponent('configure-modules-content');
        let data = {};

        _.each(configModule.meta.fields, function(def) {
            let enabledModules = configModule.configModel.get(def.name);
            if (typeof enabledModules === 'string' || enabledModules instanceof String) {
                enabledModules = enabledModules.split(',');
            }
            data[def.name] = enabledModules;
        }, this);
        data.recordview_display_settings = {};
        _.each(configModule.displaySettings, function(value, module) {
            let fieldName = configModule.getFieldName(module);
            if (configModule.model.has(fieldName)) {
                configModule.displaySettings[module] = data.recordview_display_settings[module] =
                    configModule.model.get(fieldName);
            }
        }, this);

        app.alert.show('settings:save', {
            level: 'process',
            title: app.lang.getAppString('LBL_SAVING'),
        });

        let url = app.api.buildURL(configModule.module, 'config');

        this.disableEnableSaveButton(true);

        app.api.call('update', url, data, {
            success: _.bind(this.updateAPISuccess, this, configModule),
            error: _.bind(this.updateAPIError, this),
            complete: _.bind(this.updateAPIComplete, this),
        });
    },

    /**
     * When Update API is succesful then update meta
     *
     * @param {Object} configModule
     * @param {Object} result
     */
    updateAPISuccess: function(configModule, result) {
        app.alert.dismiss('settings:save');

        app.alert.show('settings:updatingMeta', {
            level: 'info',
            messages: app.lang.get('LBL_CONFIGURE_MODULES_UPDATE_META', this.module),
            autoClose: false
        });

        app.alert.show('settings:success', {
            level: 'success',
            title: app.lang.getAppString('LBL_SUCCESS'),
            messages: app.lang.getAppString('LBL_EMAIL_SETTINGS_SAVED'),
            autoClose: true
        });

        app.metadata.sync(_.bind(function() {
            app.alert.dismiss('settings:updatingMeta');
            window.location.reload(true);

            if (err && result) {
                app.alert.show('error', {
                    level: 'error',
                    messages: result.message,
                    autoClose: true
                });
            }
        }, this));

        configModule.render();
    },

    /**
     * When Update API fails
     *
     * @param {Object} error
     */
    updateAPIError: function(error) {
        this.disableEnableSaveButton();

        let errorMessage;

        if (error.code !== 'invalid_license') {
            errorMessage = `${app.lang.getAppString('ERR_AJAX_LOAD')} ${app.lang.getAppString(error.message)}`;
        } else {
            errorMessage = error.message;
        }

        app.CJBaseHelper.invalidLicenseError('settings:error', errorMessage);
    },

    /**
     * When Update API completes then dismiss alert
     *
     * @param {Object} data
     */
    updateAPIComplete: function(data) {
        app.alert.dismiss('settings:save');
    },

    /*
     * Disable/Enable the save button
     *
     * @param {boolean} disable
     * @return {void}
     */
    disableEnableSaveButton: function(disable = false) {
        let saveButton = this.getField('save_button');
        if (saveButton) {
            saveButton.setDisabled(disable);
        }
    },

    /*
     * Go back to the previous page when cancel button is clicked
     * @return {void}
     */
    cancelConfig: function() {
        app.router.goBack();
    },
});

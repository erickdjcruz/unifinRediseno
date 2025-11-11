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
 * @class View.Views.Base.AdministrationMapsConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsConfigView
 * @extends View.Views.Base.AdministrationConfigView
 */
({
    extendsFrom: 'AdministrationConfigView',
    prefix: 'maps_',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.meta.label = app.lang.get('LBL_MAP_CONFIG_TITLE');
        this.saveMessage = 'LBL_SAVE_SUCCESS';

        this.context.safeRetrieveModulesData = _.bind(this.safeRetrieveModulesData, this);
    },

    /**
     * @inheritdoc
     */
    copySettingsToModel: function(settings) {
        this._super('copySettingsToModel', [settings]);

        let data = {};

        _.each(settings, function resolveData(value, key) {
            if (key.indexOf(this.prefix) === 0) {
                data[key.replace(this.prefix, '')] = value;
            }
        }, this);

        this.context.trigger('retrived:maps:config', data);
    },

    /**
     * Force load the header-view from layout
     *
     * It will change the Save button enabled/disabled state.
     *
     * @param {boolean} state The state to be set.
     */
    toggleHeaderButton: function(state) {
        var header = this.layout.getComponent('config-header');

        if (header) {
            header.enableButton(state);
        }
    },

    /**
     * Retreive a deep clone of settings data
     *
     * @return {Object}
     */
    safeRetrieveModulesData: function(module) {
        const _modulesData = _.isEmpty(this.model.get('maps_modulesData')) ? {} : this.model.get('maps_modulesData');
        let modulesData = app.utils.deepCopy(_modulesData);

        if (_.isEmpty(modulesData)) {
            modulesData[module] = {};
        }

        if (!_.has(modulesData, module)) {
            modulesData[module] = {};
        }

        if (!_.has(modulesData[module], 'mappings')) {
            modulesData[module].mappings = {
                'mappings': true
            };

            modulesData[module].mappingType = 'moduleFields';
            modulesData[module].mappingRecord = {};
        }

        if (!_.has(modulesData[module], 'settings')) {
            modulesData[module].settings = {
                'autopopulate': false
            };
        }

        if (!_.has(modulesData[module], 'subpanelConfig')) {
            modulesData[module].subpanelConfig = [];
        }

        return modulesData;
    },

    /**
     * Ensure that all modules are configured;
     *
     * @return {boolean}
     */
    canSave: function() {
        const enabledModules = this.model.get('maps_enabled_modules');
        const modulesData = this.model.get('maps_modulesData');

        let invalidModules = _.filter(enabledModules, function isModuleValid(module) {
            const moduleData = modulesData[module];

            return ((moduleData && !moduleData.mappings) || !moduleData);
        });

        if (_.isEmpty(invalidModules)) {
            return true;
        }

        app.alert.show('maps-invalid-module-config', {
            level: 'error',
            title: app.lang.get('LBL_MAPS_CONFIG_INVALID_MODULE_TITLE'),
            messages: app.lang.get('LBL_MAPS_CONFIG_INVALID_MODULE', null, {
                module: invalidModules.toString(),
            }),
            autoClose: true
        });

        return false;
    },

    /**
     * @inheritdoc
     */
    save: function() {
        if (!this.canSave()) {
            return;
        }

        const options = {
            error: _.bind(this.saveErrorHandler, this),
            success: _.bind(this.saveSuccessHandler, this)
        };

        const apiUrl = app.api.buildURL(`${this.module}/maps`, this.settingPrefix);

        app.api.call('create', apiUrl, this.model.toJSON(), options);
    },

    /**
     * @inheritdoc
     */
    saveErrorHandler: function(e) {
        app.alert.show(this.settingPrefix + '-warning', {
            level: 'error',
            title: app.lang.get('LBL_ERROR'),
            messages: e.message ? e.message : app.lang.get('LBL_AWS_SAVING_ERROR', this.module),
        });
    },
});

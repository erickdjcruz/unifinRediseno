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
 * @class View.Views.Base.DRI_Workflows.ConfigureModulesContentView
 * @alias SUGAR.App.view.views.BaseDRI_WorkflowsConfigureModulesContentView
 * @extends View.View
 */
({
    /**
     * Contains grace period message
     *
     * @property {string} gracePeriodMessage
     */
    gracePeriodMessage: '',

    /**
     * Contains Header Label
     *
     * @property {string} headerLaber
     */
    headerLaber: '',

    /**
     * Contains dynamic fields meta of modules
     *
     * @property {Array} fieldsMeta
     */
    fieldsMeta: [],

    /**
     * Contains the display settings of modules
     *
     * @property {Object} displaySettings
     */
    displaySettings: {},

    /**
     * @inheritdoc
     *
     * set the label from meta and add the button events
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        let headerLaber = this.meta.header_label || this.options.meta.header_label;
        if (!_.isEmpty(headerLaber)) {
            this.headerLaber = headerLaber;
        }

        this.configDisplaySettingDesc = new Handlebars.SafeString(App.utils.formatString(App.lang.get(
            'LBL_CONFIGURE_DISPLAY_SETTING_DESC', 'DRI_Workflows')));

        this.configModel = new app.data.createBean('DRI_Workflows');
        let url = app.api.buildURL('DRI_Workflows', 'graceperiod-remaining-days');

        app.api.call('read', url, null, {
            success: _.bind(this.checkUserLimitSuccess, this),
        });
    },

    /**
     * Set grace period message on successful check-user-limit api call
     *
     * @param {Object} response
     */
    checkUserLimitSuccess: function(response) {
        if (!_.isUndefined(response.remaining_days)) {
            this.gracePeriodMessage = app.lang.get(
                'LBL_CJ_REMAINING_DAYS_WHEN_USER_LIMIT_REACHED',
                'DRI_Workflows',
                {gracePeriodDays: response.remaining_days}
            );
            this._render();
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.configModel) {
            this.configModel.on('change:enabled_modules', this.updateModuleFields, this);
        }
    },

    /**
     * Add or Remove fields in display settings when module is added or remove from list of enable modules
     */
    updateModuleFields: function() {
        let prevEnabledModules = this.configModel.previousAttributes().enabled_modules || '';
        prevEnabledModules = typeof prevEnabledModules !== 'object' ?
            prevEnabledModules.split(',') : prevEnabledModules;
        let enabledModules = this.configModel.get('enabled_modules');
        enabledModules = typeof enabledModules === 'string' ? enabledModules.split(',') : enabledModules;
        let addModules = _.difference(enabledModules, prevEnabledModules);
        let removeModule = _.first(_.difference(prevEnabledModules, enabledModules));

        // show module field on adding enabled module and set value
        if (!_.isUndefined(addModules)) {
            _.each(addModules, function(addModule) {
                let addModuleFieldName = this.getFieldName(addModule);
                if ($(`[data-name='${ addModuleFieldName }']`).length) {
                    this.displaySettings[addModule] = $(`[name='${ addModuleFieldName }']`).val();
                    $(`[data-name='${ addModuleFieldName }']`).show();
                } else {
                    this.displaySettings[addModule] = 'panel_bottom';
                }
                this.fieldsMeta = [];
                this.prepareFieldsMeta(this.displaySettings);
                this.render();
            }, this);
        }

        // hide module field on removing enabled module
        if (!_.isUndefined(removeModule) && !_.isEmpty(removeModule)) {
            delete this.displaySettings[removeModule];
            this.fieldsMeta = [];
            this.prepareFieldsMeta(this.displaySettings);
            let removeModuleFieldName = this.getFieldName(removeModule);
            $(`[data-name='${ removeModuleFieldName }']`).hide();
        }
    },

    /**
     * Loads available settings.
     */
    loadData: function() {
        let data = app.config.customer_journey;
        if (!_.isEmpty(data)) {
            _.each(data, function(value, key) {
                this.configModel.set(key, value, {silent: true,});
            }, this);
        }
        if (!_.isEmpty(data) && !_.isEmpty(data.recordview_display_settings)) {
            _.each(data.recordview_display_settings, function(position, module) {
                if (!app.config.customer_journey.enabled_modules.split(',').includes(module)) {
                    delete data.recordview_display_settings[module];
                }
            });
            this.fieldsMeta = [];
            this.displaySettings = data.recordview_display_settings;
            this.prepareFieldsMeta(data.recordview_display_settings);
        }
    },

    /*
     * Prepare the fields metadata
     *
     * @param {Object} recordview_display_settings
     * @returns {undefined}
     */
    prepareFieldsMeta: function(recordviewDisplaySettings) {
        _.each(recordviewDisplaySettings, function(value, module) {
            let moduleRecordView = app.metadata.getView(module, 'record');
            let metaMismatch = false;

            if (value.includes('tab')) {
                metaMismatch = _.isEmpty(moduleRecordView.templateMeta) || !moduleRecordView.templateMeta.useTabs;
            }
            if (metaMismatch) {
                this.displaySettings[module] = 'panel_bottom';
            }

            let fieldMeta = {
                name: `${module.toLowerCase()}_display_field`,
                type: 'configure-record-view-display-enum',
                options: 'configure_record_view_display_dom',
                baseModule: module,
            };

            this.fieldsMeta.push(fieldMeta);
        }, this);
    },

    /**
     * Set model fields value according to displaySettings
     */
    _render: function() {
        this._super('_render');

        let saveField = this.$el.find('a[name="save_button"]');
        let isLicenseValid = app.user.hasAutomateLicense();

        if (saveField) {
            isLicenseValid ? saveField.css('pointer-events', 'all') : saveField.css('pointer-events', 'none');
        }
        app.CJFieldHelper._enableOrDisableField(this.getField('enabled_modules'), !isLicenseValid);

        if (!_.isEmpty(this.displaySettings)) {
            _.each(this.displaySettings, function(value, module) {
                let fieldName = this.getFieldName(module);
                this.model.set(fieldName, value);
            }, this);
        }
    },

    /**
     * get the field name for display settings
     */
    getFieldName: function(module = '') {
        if (!_.isEmpty(module)) {
            return `${module.toLowerCase()}_display_field`;
        }
    },

    /**
         * @inheritdoc
         *
         * Dispose the events and local variables
         */
    _dispose: function() {
        this.fieldsMeta = [];
        this.displaySettings = {};
        this.configModel.off('change:enabled_modules');
        this._super('_dispose');
    }
});

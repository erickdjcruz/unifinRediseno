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
 * @class View.Views.Base.AdministrationMapsModuleSettingsView
 * @alias SUGAR.App.view.views.BaseAdministrationMapsModuleSettingsView
 */
({
    /**
     * Event listeners
     */
    events: {
        'change [data-fieldname=autopopulate]': 'autopopulateChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._settings = {
            autopopulate: {
                'false': app.lang.get('LBL_NO'),
                'true': app.lang.get('LBL_YES'),
            }
        };

        if (options.widgetModule) {
            this.widgetModule = options.widgetModule;
        }
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        if (this.context.safeRetrieveModulesData) {
            const currentSettings = this.context.safeRetrieveModulesData(this.widgetModule);

            if (_.isEmpty(currentSettings[this.widgetModule].settings) ||
                (!_.has(currentSettings[this.widgetModule].settings, 'autopopulate'))
            ) {
                this._notifyAutopopulateChanged(false);
            }
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        let select2Options = this._getSelect2Options();

        this._disposeSelect2('autopopulate');

        this.$('[data-fieldname=autopopulate]').select2(select2Options);

        this._updateUI();
    },

    /**
     * Update UI elements with saved data
     */
    _updateUI: function() {
        const _settings = this._getSettings();
        const autopopulate = app.utils.isTruthy(_settings.autopopulate) ? 'true' : 'false';

        this.$('[data-fieldname=autopopulate]').select2('data', {
            id: autopopulate,
            text: this._settings.autopopulate[autopopulate]
        });
    },

    _getSettings: function() {
        const _modulesData = this.context.safeRetrieveModulesData(this.widgetModule);

        return _modulesData[this.widgetModule].settings;
    },

    /**
     * Create generic Select2 options object
     *
     * @return {Object}
     */
    _getSelect2Options: function() {
        var select2Options = {
            minimumResultsForSearch: -1
        };

        return select2Options;
    },

    /**
     * Event handler for unit type selection change
     *
     * @param {UIEvent} e
     *
     */
    autopopulateChanged: function(e) {
        const unitType = e.currentTarget.value;

        this._notifyAutopopulateChanged(unitType);
    },

    /**
     * Dispose a select2 element
     */
    _disposeSelect2: function(name) {
        this.$('[data-fieldname=' + name + ']').select2('destroy');
    },

    /**
     * Dispose all select2 elements
     */
    _disposeSelect2Elements: function() {
        this._disposeSelect2('autopopulate');
    },

    /**
     * Notify parent about the change of the settings
     *
     * @param {string} unitType
     */
    _notifyAutopopulateChanged: function(unitType) {
        const value = app.utils.isTruthy(unitType);
        const widgetModule = this.widgetModule;

        let modulesData = this.context.safeRetrieveModulesData(widgetModule);

        modulesData[widgetModule].settings.autopopulate = value;

        this.model.set('maps_modulesData', modulesData);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSelect2Elements();

        this._super('_dispose');
    },
});

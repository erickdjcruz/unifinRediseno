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
 * Layout for maps configuration
 *
 * @class View.Layouts.Base.AdministrationMapsLoggerControlsView
 * @alias SUGAR.App.view.layouts.BaseAdministrationMapsLoggerControlsView
 */
({
    /**
     * Event listeners
     */
    events: {
        'change [data-fieldname=logger-level]': 'loggerLevelChanged',
        'click [data-fieldname=enableModule]': 'clickEnableModuleLog',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Property initialization
     */
    _initProperties: function() {
        this.availableModulesLoaded = false;
        this.modulesWidgets = [];
        this._availableModulesForCurrentLicense = [];
        this._select2Data = this._getSelect2Data();
    },

    /**
     * Check if default data is setup
     */
    _initDefaultData: function() {
        if (!this.model.get('maps_loggerLevel')) {
            this.model.set('maps_loggerLevel', 'error');
        }

        if (!this.model.get('maps_loggerStartdate')) {
            const defaultDate = app.date().subtract(1, 'days').format('YYYY-MM-DD');

            this.model.set('maps_loggerStartdate', defaultDate);
        }

        const enabledModules = this.model.get('maps_enabled_modules');

        if (!enabledModules) {
            this.model.set('maps_enabled_modules', []);
        } else if (enabledModules.length > 0 && !this.model.get('enabledLoggingModules')) {
            //always we show directly the logs only for the first module.
            //then the user can select whatever module
            this.model.set('enabledLoggingModules', [enabledModules[0]]);
        }

        if (!this.model.get('enabledLoggingModules')) {
            this.model.set('enabledLoggingModules', []);
        }

        this.setAvailableSugarModules();
    },

    /**
     * Register context event handlers
     *
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'retrived:maps:config', this.configRetrieved, this);
    },

    /**
     * Called when config is being retrieved from DB
     *
     * @param {Object} data
     */
    configRetrieved: function(data) {
        if (this.disposed) {
            return;
        }

        this._initDefaultData();
        this._updateUI(data);
    },

    /**
     * Update the UI elements from config
     *
     * @param {Object} data
     */
    _updateUI: function(data) {
        this._updateGeneralSettingsUI(data);
        this._updateModulesWidgets(data);
    },

    /**
     * Update the module widget
     *
     * @param {Object} data
     */
    _updateModulesWidgets: function(data) {
        const availableModules = this.model.get('maps_enabled_modules');

        this.availableModulesLoaded = true;

        this.render();

        this.$('[data-widget=report-loading]').hide();

        if (_.isEmpty(availableModules)) {
            this.$('.maps-missing-modules').show();
        }

        this.notifyLoggerDisplay();
    },

    /**
     * Update Log Level and Measuremenet Unit from config
     *
     * @param {Object} data
     */
    _updateGeneralSettingsUI: function(data) {
        this._updateSelect2El('logger-level', data);

        const loggerStartDate = this.model.get('maps_loggerStartdate');

        this.$('[data-fieldname=logger-startdate]').datepicker('setValue', loggerStartDate);
    },

    /**
     * Update select2 value
     *
     * @param {string} elId
     * @param {Object} data
     */
    _updateSelect2El: function(elId, data) {
        const dataKey = app.utils.kebabToCamelCase(elId);

        if (_.has(data, dataKey)) {
            let id = data[dataKey];
            let text = app.lang.getModString(this._getSelect2Label(dataKey, data[dataKey]), this.module);

            this.$('[data-fieldname=' + elId + ']').select2('data', {
                id: id,
                text: text
            });
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        let select2Options = this._getSelect2Options({
            'minimumResultsForSearch': -1,

            sortResults: function(results, container, query) {
                results = _.sortBy(results, 'text');
                return results;
            }
        });

        this.$('[data-fieldname=logger-level]').select2(select2Options);
        this._createDatePicker();
    },

    /**
     * Create date picker widget
     */
    _createDatePicker: function() {
        const userDateFormat = 'Y-m-d';

        const options = {
            format: app.date.toDatepickerFormat(userDateFormat),
            weekStart: parseInt(app.user.getPreference('first_day_of_week'), 10),
        };

        const datePicker = this.$('[data-fieldname=logger-startdate]').datepicker(options);

        datePicker.on('keydown', function keyDown(e) {
            e.preventDefault();
        });

        datePicker.on('changeDate', _.bind(this.startDateChanged, this));
    },

    /**
     * Event handler for start date selection change
     *
     * @param {UIEvent} e
     */
    startDateChanged: function(e) {
        const startDate = e.currentTarget.value;
        const key = 'maps_loggerStartdate';

        this.model.set(key, startDate);

        this.notifyLoggerDisplay();
    },

    /**
     * Event handler for log level selection change
     *
     * @param {UIEvent} e
     */
    loggerLevelChanged: function(e) {
        const logLevel = e.currentTarget.value;
        const key = 'maps_loggerLevel';

        this.model.set(key, logLevel);

        this.notifyLoggerDisplay();
    },

    /**
     * Notify the logger display component to update the logs
     */
    notifyLoggerDisplay: function() {
        this.model.set({
            offset: 0,
            currentPage: 1
        });

        this.context.trigger('retrieved:maps:logs');
    },

    /**
     * Event handler for changing the modules to be used for logging
     *
     * @param {UIEvent} e
     */
    clickEnableModuleLog: function(e) {
        const moduleName = e.currentTarget.getAttribute('data-modulename');
        const isChecked = e.currentTarget.checked;

        let enabledLoggingModules = this.model.get('enabledLoggingModules');

        if (!enabledLoggingModules) {
            return;
        }

        const indexOfFocusedModule = enabledLoggingModules.indexOf(moduleName);

        if (isChecked && indexOfFocusedModule < 0) {
            enabledLoggingModules.push(moduleName);
        } else if (!isChecked && indexOfFocusedModule > -1) {
            enabledLoggingModules.splice(indexOfFocusedModule, 1);
        }

        this.model.set('enabledLoggingModules', enabledLoggingModules);

        this.notifyLoggerDisplay();
    },

    /**
     * Create generic Select2 options object
     *
     * @return {Object}
     */
    _getSelect2Options: function(additionalOptions) {
        var select2Options = {};

        select2Options.placeholder = app.lang.get('LBL_MAPS_SELECT_NEW_MODULE_TO_GEOCODE', 'Administration');
        select2Options.dropdownAutoWidth = true;

        select2Options = _.extend({}, additionalOptions);

        return select2Options;
    },

    /**
     * Data for select2
     *
     * @return {Object}
     */
    _getSelect2Data: function() {
        const data = {
            'logLevel': {
                'error': 'LBL_MAPS_LOGGER_LOG_ERROR',
                'success': 'LBL_MAPS_LOGGER_LOG_SUCCESS',
                'all': 'LBL_MAPS_LOGGER_LOG_ALL_MESSAGES',
            },
            'availableModules': this._availableModules,
        };

        return data;
    },

    /**
     * Get dropdown label
     *
     * @param {string} select2Id
     * @param {string} key
     * @return {string}
     */
    _getSelect2Label: function(select2Id, key) {
        return this._select2Data[select2Id][key];
    },

    /**
     * Create generic Select2 component or return a cached select2 element
     *
     * @param {string} fieldname
     * @param {string} queryFunc
     * @param {boolean} reset
     * @param {Function} callback
     */
    select2: function(fieldname, queryFunc, reset, callback) {
        if (this._select2 && this._select2[fieldname]) {
            return this._select2[fieldname];
        };

        this._disposeSelect2(fieldname);

        let additionalOptions = {};

        if (queryFunc && this[queryFunc]) {
            additionalOptions.query = _.bind(this[queryFunc], this);
        }

        var el = this.$('[data-fieldname=' + fieldname + ']')
            .select2(this._getSelect2Options(additionalOptions))
            .data('select2');

        this._select2 = this._select2 || {};
        this._select2[fieldname] = el;

        if (reset) {
            el.onSelect = (function select(fn) {
                return function returnCallback(data, options) {
                    if (callback) {
                        callback(data);
                    }

                    if (arguments) {
                        arguments[0] = {
                            id: 'select',
                            text: app.lang.get('LBL_MAPS_SELECT_NEW_MODULE_TO_GEOCODE', 'Administration')
                        };
                    }

                    return fn.apply(this, arguments);
                };
            })(el.onSelect);
        }

        return el;
    },

    /**
     * Get a list of available modules
     */
    setAvailableSugarModules() {
        this._availableModules = {};

        _.each(app.metadata.getModules(), function getAvailableModules(moduleData, moduleName) {
            if (!_.contains(this._deniedModules, moduleName)) {
                let moduleLabel = app.lang.getModString('LBL_MODULE_NAME', moduleName);

                if (!moduleLabel) {
                    moduleLabel = app.lang.getModuleName(moduleName, {
                        plural: true
                    });
                }

                this._availableModulesForCurrentLicense[moduleName] = moduleLabel;

                if (!_.contains(this.model.get('maps_enabled_modules'), moduleName)) {
                    this._availableModules[moduleName] = moduleLabel;
                }
            }
        }, this);
    },

    /**
     * Dispose a select2 element
     */
    _disposeSelect2: function(name) {
        this.$('[data-fieldname=' + name + ']').select2('destroy');
    },

    /**
     * Dispose datepicker element
     */
    _disposeDatePicker: function(name) {
        const loggerStartDateEl = this.$('[data-fieldname=' + name + ']');
        const dataPicker = loggerStartDateEl.datepicker();
        const datePickerData = loggerStartDateEl.data('datepicker');

        if (datePickerData && !datePickerData.hidden) {
            //when SC-2395 gets implemented change this to 'remove' not 'hide'
            loggerStartDateEl.datepicker('hide');
        }

        if (dataPicker && _.isFunction(dataPicker.off)) {
            dataPicker.off('changeDate');
            dataPicker.off('keydown');
        }
    },

    /**
     * Dispose all select2 elements
     */
    _disposeSelect2Elements: function() {
        this._disposeSelect2('logger-level');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSelect2Elements();
        this._disposeDatePicker('logger-startdate');

        this._super('_dispose');
    },
});

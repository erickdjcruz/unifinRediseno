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
 * @class View.Layouts.Base.AdministrationMapsControlsLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationMapsControlsLayout
 */
({
    /**
     * Event listeners
     */
    events: {
        'change [data-fieldname=log-level]': 'logLevelChanged',
        'change [data-fieldname=unit-type]': 'unitTypeChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        this._select2Data = this._getSelect2Data();
    },

    /**
     * Property initialization
     */
    _initProperties: function() {
        this._modulesWidgets = [];
        this._availableModulesForCurrentLicense = [];
        this._deniedModules = this._getDeniedModules();
    },

    /**
     * Check if default data is setup
     */
    _initDefaultData: function() {
        if (!this.model.get('maps_logLevel')) {
            this.model.set('maps_logLevel', 'fatal');
        }

        if (!this.model.get('maps_unitType')) {
            this.model.set('maps_unitType', 'miles');
        }

        if (!this.model.get('maps_enabled_modules')) {
            this.model.set('maps_enabled_modules', ['Accounts']);
        }

        this.setAvailableSugarModules();
    },

    /**
     * Register context event handlers
     *
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'retrived:maps:config', this.configRetrieved, this);
        this.listenTo(this.model, 'change', this.refreshAvailableModules, this);
    },

    /**
     * Called when config is being retrieved from DB
     *
     * @param {Object} data
     */
    configRetrieved: function(data) {
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
     * Refresh available modules
     */
    refreshAvailableModules: function() {
        this.setAvailableSugarModules();
        this._updateModulesWidgets(this.model.toJSON());
    },

    /**
     * Update the module widget
     *
     * @param {Object} data
     */
    _updateModulesWidgets: function(data) {
        const availableModules = this.model.get('maps_enabled_modules');
        const $container = this.$('[data-container=modules-widgets-container]');
        const availableModulesForCurrentLicense = _.keys(this._availableModulesForCurrentLicense);

        $container.empty();

        this._disposeModulesWidgets();

        if (_.isEmpty(availableModules)) {
            this.$('.maps-missing-modules').show();
        } else {
            this.$('.maps-missing-modules').hide();
        }

        _.chain(availableModules)
            .filter(function filter(currentModule) {
                return _.contains(availableModulesForCurrentLicense, currentModule);
            }, this)
            .each(function createModuleWidget(module) {
                let moduleData = {};

                if (_.has(data, 'modulesData') && _.has(data.modulesData, module)) {
                    moduleData = data.modulesData[module];
                }

                this._createModuleWidgetView(module, moduleData, $container);
            }, this);
    },

    /**
     * Update Log Level and Measuremenet Unit from config
     *
     * @param {Object} data
     */
    _updateGeneralSettingsUI: function(data) {
        this._updateSelect2El('log-level', data);
        this._updateSelect2El('unit-type', data);
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

        this.$('[data-fieldname=log-level]').select2(select2Options);
        this.$('[data-fieldname=unit-type]').select2(select2Options);
        this.select2('add-new-module', '_queryAvailableModules', true, _.bind(this.addNewModuleChanged, this));
    },

    /**
     * Populate select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryAvailableModules: function(query) {
        return this._query(query, '_availableModules');
    },

    /**
     * Generic select2 selection list builder
     *
     * @param {Object} query
     * @param {string} list
     *
     */
    _query: function(query, list) {
        var listElements = this[list];
        var data = {
            results: [],
            more: false
        };

        if (_.isObject(listElements)) {
            _.each(listElements, function pushValidResults(element, index) {
                if (query.matcher(query.term, element)) {
                    data.results.push({id: index, text: element});
                }
            });

            data.results = _.sortBy(data.results, 'text');

        } else {
            listElements = null;
        }

        query.callback(data);
    },

    /**
     * Event handler for log level selection change
     *
     * @param {UIEvent} e
     *
     */
    logLevelChanged: function(e) {
        const logLevel = e.currentTarget.value;
        const key = 'maps_logLevel';

        this.model.set(key, logLevel);
    },

    /**
     * Event handler for unit type selection change
     *
     * @param {UIEvent} e
     *
     */
    unitTypeChanged: function(e) {
        const unitType = e.currentTarget.value;
        const key = 'maps_unitType';

        this.model.set(key, unitType);
    },

    /**
     * Add a new module to geocoded module list
     *
     * @param {Object} data
     */
    addNewModuleChanged: function(data) {
        let availableModules = this.model.get('maps_enabled_modules');

        availableModules.push(data.id);

        this.model.set('maps_enabled_modules', availableModules);
        this.model.trigger('change', this.model);

        this.setAvailableSugarModules();
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
                'fatal': 'LBL_MAPS_LOG_LVL_FATAL',
                'debug': 'LBL_MAPS_LOG_LVL_DEBUG',
                'error': 'LBL_MAPS_LOG_LVL_ERROR'
            },
            'unitType': {
                'miles': 'LBL_MAPS_UNIT_TYPE_MILES',
                'km': 'LBL_MAPS_UNIT_TYPE_KM'
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
     * Initialize and render inner module item
     *
     * @param {string} module
     * @param {Object} moduleData
     * @param {jQuery} $container
     */
    _createModuleWidgetView: function(module, moduleData, $container) {
        var widgetView = app.view.createView({
            name: 'maps-module-widget',
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            moduleData: moduleData,
            widgetModule: module
        });

        this._modulesWidgets.push(widgetView);

        $container.append(widgetView.$el);
        widgetView.render();
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
     * Get the list of denied modules
     *
     * @return {Array}
     */
    _getDeniedModules: function() {
        return [
            'ACLActions', 'ACLFields', 'ACLRoles', 'Activities', 'Administration', 'ArchiveRuns', 'Audit',
            'CampaignLog', 'CampaignTrackers', 'Charts', 'Configurator', 'Connectors', 'ConsoleConfiguration',
            'ContractTypes', 'Currencies', 'CustomFields', 'CustomQueries', 'Dashboards', 'DataArchiver', 'DataPrivacy',
            'DataSet_Attribute', 'DataSets', 'DocumentRevisions', 'DynamicFields', 'Emails', 'EAPM', 'EditCustomFields',
            'EmailAddresses', 'EmailMan', 'EmailMarketing', 'EmailParticipants', 'EmailTemplates', 'EmbeddedFiles',
            'Employees', 'Exports', 'Expressions', 'FAQ', 'ForecastManagerWorksheets', 'ForecastWorksheets', 'Groups',
            'HealthCheck', 'History', 'Holidays', 'Home', 'Import', 'InboundEmail', 'KBArticles', 'KBDocuments',
            'KBOLDContents', 'KBOLDDocumentKBOLDTags', 'KBOLDDocumentRevisions', 'KBOLDDocuments', 'KBOLDTags',
            'Library', 'Login', 'Manufacturers', 'MergeRecords', 'MobileDevices', 'ModuleBuilder', 'MySettings',
            'OAuthKeys', 'OAuthTokens', 'OptimisticLock', 'OutboundEmailConfiguration', 'PdfManager', 'ProductBundles',
            'ProductBundleNotes', 'ProductTypes', 'Project', 'ProjectTask', 'PushNotifications', 'QueryBuilder',
            'Quotas', 'Relationships', 'Releases', 'ReportMaker', 'Reports', 'SNIP', 'Shifts', 'SavedSearch',
            'Schedulers', 'SchedulersJobs', 'Shippers', 'Studio', 'Styleguide', 'Subscriptions', 'SugarFavorites',
            'SugarLive', 'Sugar_Favorites', 'Sync', 'TaxRates', 'TeamMemberships', 'TeamNotices', 'TeamSetModules',
            'TeamSets', 'Teams', 'TimePeriods', 'TrackerPerfs', 'TrackerQueries', 'TrackerSessions', 'Trackers',
            'UpgradeWizard', 'UserPreferences', 'UserSignatures', 'Versions', 'VisualPipeline', 'WebLogicHooks',
            'Words', 'Worksheet', 'WorkFlow', 'WorkFlowActionShells', 'WorkFlowActions', 'WorkFlowAlertShells',
            'WorkFlowAlerts', 'WorkFlowTriggerShells', 'ShiftExceptions', 'iCals', 'iFrames', 'pmse_Business_Rules',
            'pmse_Emails_Templates', 'pmse_Inbox', 'pmse_Project', 'vCals', 'vCards', 'ReportSchedules',
            'ProductCategories', 'OutboundEmail', 'Notifications', 'Newsletters', 'KBContents', 'KBContentTemplates',
            'Geocode', 'Filters', 'Feeds', 'Feedbacks', 'DocumentTemplates', 'DocumentMerges', 'CommentLog',
            'Comments', 'Categories', 'Queues', 'Error', 'ChangeTimers', 'HintAccountsets', 'HintEnrichFieldConfigs',
            'HintNewsNotifications', 'HintNotificationTargets', 'CloudDrivePaths'
        ];
    },

    /**
     * Dispose any loaded components
     *
     */
    _disposeModulesWidgets: function() {
        _.each(this._modulesWidgets, function(component) {
            component.dispose();
        }, this);

        this._modulesWidgets = [];
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
        this._disposeSelect2('log-level');
        this._disposeSelect2('unit-type');
        this._disposeSelect2('add-new-module');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSelect2Elements();
        this._disposeModulesWidgets();

        this._super('_dispose');
    },
});

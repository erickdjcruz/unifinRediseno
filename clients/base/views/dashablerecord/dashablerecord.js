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
 * Dashablerecord is a dashlet representation of a module record view. Users
 * can build dashlets of this type for any accessible and approved module.
 *
 * The specific record is not configured in advance. Rather, only the
 * module to which the record belongs is. Records are loaded into the
 * dashlet via the `change:model` event.
 *
 * @class View.Views.Base.DashablerecordView
 * @alias SUGAR.App.view.views.BaseDashablerecordView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * The plugins used by this view.
     *
     * This list is the same as that of the normal record view, with the
     * following exceptions:
     *
     * * Dashlet plugin added (because this is a dashlet)
     * * Pii removed (no PII drawer)
     * * Audit removed (no Audit Log drawer)
     * * FindDuplicates removed (no find-duplicate button)
     * * Pagination added (used by the list view tabs)
     */
    plugins: [
        'SugarLogic',
        'ErrorDecoration',
        'GridBuilder',
        'Editable',
        'ToggleMoreLess',
        'Dashlet',
        'Pagination',
        'ConfigDrivenList',
        'ActionButton',
        'RowEditable',
    ],

    /**
     * We want to load field `record` templates
     */
    fallbackFieldTemplate: 'record',

    /**
     * Modules that are permanently blacklisted so users cannot configure a
     * dashlet for these modules.
     *
     * @property {string[]}
     */
    moduleBlacklist: [
        'Campaigns',
        'Home',
        'Forecasts',
        'Project',
        'ProjectTask',
        'UserSignatures',
        'OutboundEmail',
    ],

    /**
     * Extra modules that we need to check for user access
     *
     * @property {string[]}
     */
    extraModules: [
        'ProductCategories',
        'ProductTypes',
        'Manufacturers',
        'ContractTypes',
        'Shippers',
        'ShiftExceptions',
    ],

    /**
     * List of modules that should not be available as tabs
     *
     * @property {string[]}
     */
    tabBlacklist: [
        'Tags',
    ],

    /**
     * Flag indicates if a module is available for display.
     *
     * @property {boolean}
     */
    moduleIsAvailable: true,

    /**
     * Cache of the modules a user is allowed to see.
     *
     * The keys are the module names and the values are the module names after
     * resolving them against module and/or app strings. The cache logic can be
     * seen in {@link BaseDashablerecordView#_getAvailableModules}.
     *
     * @property {Object}
     */
    _availableModules: {},

    /**
     * The default settings for a record view dashlet.
     *
     * @property {Object}
     */
    _defaultSettings: {
        freeze_first_column: true,
        limit: 5, // for tabs with list view
    },

    /**
     * Denotes the mode of operation for the dashlet:
     * 'main' during normal use, 'config' during configuration.
     *
     * @property {string}
     */
    _mode: 'main',

    /**
     * List of fields we wish to banish from the header.
     *
     * @property {string[]}
     */
    _noshowFields: ['favorite', 'follow', 'badge', 'status'],

    /**
     * Size of avatars within the dashlet toolbar.
     *
     * @property {number}
     */
    _avatarSize: 28,

    /**
     * Ensures we only bind event listeners once.
     *
     * @property {boolean}
     */
    _hasDelegated: false,

    /**
     * Cap on the maximum number of tabs allowed.
     *
     * @property {Object}
     */
    _tabLimit: {
        number: 6,
        label: 'LBL_SIX'
    },

    /**
     * The tabs in the view
     *
     * @property {Array}
     */
    tabs: [],

    /**
     * The pseudo dashlet component that is the live preview on the config view
     *
     * @property {View}
     */
    _pseudoDashlet: null,

    dataView: 'recorddashlet',

    /**
     * Are we in a config drawer layout?
     */
    configLayout: false,

    /**
     * Turn off headerpane
     */
    enableHeaderPane: true,

    /**
     * Defines the scroll container jQuery element
     */
    scrollContainer: null,

    /**
     * Stores references to fields by model ID
     */
    rowFields: {},

    /**
     * Stores any list edit toggled models by model ID
     */
    toggledListModels: {},

    /**
     * Disable extra metadata panels
     */
    showExtraMeta: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (options.meta.pseudo) {
            this.plugins = _.without(this.plugins, 'Pagination');
        }
        // bolt record view metadata for the given module onto the dashlet
        this._defaultBaseMeta = options.meta;
        this.options.meta = this._extendMeta({module: options.meta.module, type: 'record'});

        this._super('initialize', [options]);

        this._noAccessTemplate = app.template.get(this.name + '.noaccess');
        this._noTabsAvailableTemplate = app.template.get(`${this.name}.no-tabs-available`);
        this._recordsTemplate = app.template.get(this.name + '.records');
        this._recordTemplate = app.template.get(this.name + '.record');
        this._tabsTemplate = app.template.get(this.name + '.tabs');
        this._configListTemplate = app.template.get(this.name + '.list-config');

        // listen to tab events
        this.events = _.extend(this.events || {}, this.rowEditEvents, {
            'click [class*="orderBy"]': 'setOrderBy',
            'click [data-action=tab-switcher]': 'tabSwitcher',
        });

        /**
         * Events to listen to on the dashlet toolbar's context.
         *
         * @property {Object}
         */
        this._toolbarContextEvents = {
            'button:edit_button:click': this.editRecord,
            'button:save_button:click': this.saveClicked,
            'button:cancel_button:click': this.cancelClicked,
        };

        this.configLayout = this._inConfigLayout();
        this.context.on('focusRow', this.focusRow, this);
        this.context.on('unfocusRow', this.unhighlightRows, this);
    },

    /**
     * @inheritdoc
     */
    saveClicked: function() {
        // Disable the action buttons.
        this.toggleButtons(false);
        var allFields = this.getFields(this.module, this.model);
        var fieldsToValidate = {};
        var erasedFields = this.model.get('_erased_fields');
        for (var fieldKey in allFields) {
            if (app.acl.hasAccessToModel('edit', this.model, fieldKey) &&
                (!_.contains(erasedFields, fieldKey) || this.model.get(fieldKey) || allFields[fieldKey].id_name)) {
                _.extend(fieldsToValidate, _.pick(allFields, fieldKey));
            }
        }

        // name field is not defined in metadata, inject it here to be validated
        var $recordCells = this._getRecordCells();
        var nameField = this._getNameFieldFromRecordCells($recordCells);
        var nameFieldName = $(nameField).find('.index').attr('data-fieldname');
        var nameFieldDef = app.metadata.getField({module: this.module, name: nameFieldName});
        if (!_.isEmpty(nameFieldDef)) {
            if (!_.contains(fieldsToValidate, nameFieldName)) {
                fieldsToValidate[nameFieldName] = nameFieldDef;
            }
            // field group such as fullname
            if (fieldsToValidate[nameFieldName].fields) {
                _.each(fieldsToValidate[nameFieldName].fields, function(field) {
                    var fieldDef = app.metadata.getField({module: this.module, name: field});
                    if (!_.isEmpty(fieldDef) && fieldDef.required && !_.contains(fieldsToValidate, field)) {
                        fieldsToValidate[field] = fieldDef;
                    }
                }, this);
            }
        }
        this.model.doValidate(fieldsToValidate, _.bind(this.validationComplete, this));
    },

    /**
     * Handles when the save button is clicked on an editable list row
     *
     * @param event
     * @private
     */
    _handleRowSaveClicked: function(event) {
        let row = this._getClickedRowElement(event);
        let model = this.collection.get(row.data('id'));
        if (model) {
            let fieldsToValidate = this.getFields(model.module, model);
            model.doValidate(fieldsToValidate, (isValid) => {
                if (isValid) {
                    let cjFormBatch;
                    const options = {
                        success: () => {
                            this._toggleRow(row, false);

                            const saveRecordCallback = (view) => {
                                cjFormBatch = view;

                                if (!_.isUndefined(cjFormBatch)) {
                                    const params = {
                                        record: model.get('id'),
                                        module: this.module,
                                    };

                                    cjFormBatch.startBatchingProcess(params);
                                }
                            };

                            app.CJBaseHelper.fetchActiveSmartGuideCount(this.context, this.layout, this.module,
                                model.get('id'),
                                saveRecordCallback
                            );
                        },
                        error: () => {
                            if (!_.isUndefined(cjFormBatch)) {
                                cjFormBatch.endBatchingProcess(false, false);
                            }
                        },
                        showAlerts: {
                            'process': true,
                            'success': {
                                messages: app.lang.get('LBL_RECORD_SAVED', self.module)
                            }
                        },
                        params: {
                            allowBatching: true
                        },
                    };

                    model.save({}, options);
                }
            });
        }
    },

    /**
     * Extend this dashlet's metadata with that of the record view
     * (or, if available, its recorddashlet view) metadata.
     * Also mutate the panel metadata to handle its headerpane.
     *
     * @param {Object} tab Target tab.
     * @return {Object} The extended metadata.
     * @private
     */
    _extendMeta: function(tab) {
        var desiredMeta;
        if (tab.type === 'record') {
            desiredMeta = app.metadata.getView(tab.module, 'recorddashlet') || this._getRecordMeta(tab.module);
        } else {
            desiredMeta = app.metadata.getView(tab.module, 'list');
        }
        var newMeta = _.extend(
            {},
            this._defaultBaseMeta,
            desiredMeta,
            tab.meta
        );

        // Make sure we start with all the buttons. These get filtered out next,
        // but we need the full list and not the already filtered ones.
        newMeta.buttons = desiredMeta.buttons;

        // split out the headerpane if necessary, and remove unwanted fields
        // we need to inject these into the toolbar
        if (newMeta.panels) {
            this._prepareHeader(newMeta.panels, newMeta.buttons, tab);
        }

        return newMeta;
    },

    /**
     * Must implement this method as a part of the contract with the Dashlet
     * plugin. Kicks off the various paths associated with a dashlet:
     * Configuration, preview, and display.
     *
     * @param {string} viewName The name of the view as defined by the `oninit`
     *   callback in {@link DashletView#onAttach}.
     */
    initDashlet: function(viewName) {
        this._mode = viewName;
        this._initializeSettings();

        // always save the host dashboard's module
        // (this might be different from the "base record type")
        this._baseModule = this.settings.get('base_module') ||
            this.settings.get('module') ||
            this.model.module ||
            this.model.get('module');

        if (this._mode === 'config') {
            this._configureDashlet();
        } else if (this.meta.pseudo) {
            // re-render the pseudo-dashlet in the configuration whenever the set of selected tabs or its order changes
            this.layout.context.on('dashablerecord:config:tablist:change', function(newTabs = [], resetActiveTab) {
                var oldTabsLength = this.tabs.length;

                this._initTabs(newTabs);

                // Show no tabs available template, since there are no tabs in the config screen
                if (_.size(newTabs) === 0) {
                    return this._noTabsAvailable();
                }

                // if we removed a tab, lets reset back to the first tab
                if (resetActiveTab || newTabs.length !== oldTabsLength) {
                    this.setActiveTab(0);
                }

                this._updateViewToCurrentTab();
                this.render();
                this.settings.set('tabs', this.tabs);
            }, this);

            this.layout.context.trigger('dashablerecord:config:tablist:change', this.meta.tabs, true);
        } else {
            this._initTabs();
            this.setActiveTab(0);
            this._updateViewToCurrentTab();
        }

        this.before('render', function() {
            // ACL check
            if (!this.moduleIsAvailable || !this.model) {
                this._showHideListBottom(null, true);
                return this._noAccess();
            }

            var activeTab = this._getActiveTab();
            if (!activeTab) {
                return;
            }

            if (
                this._mode === 'main' &&
                this.model &&
                this.model.module === this.module
            ) {
                // ensure the header is populated with the relevant data if we already have it
                this._prepareHeader(this.meta.panels, this.meta.buttons, activeTab);
            }
        });
    },

    /**
     * Initialize the SugarLogic plugin for the current model. As the dashlet is initialized,
     * the current model is a config model which is swapped later to the actual record model.
     * In case there are multiple tabs containing different records, we have to initialize
     * the plugin for each model (there are 3 critical plugin inputs - the model, the collection
     * and the fields displayed).
     */
    initTabSugarLogic: function() {
        if (!this.disposed) {
            // we may lack a collection, so we create one to be able to trigger onLoad logics
            if (!this.collection) {
                if (this.model.collection) {
                    this.collection = this.model.collection;
                } else {
                    this.collection = app.data.createBeanCollection(this.model.module, [this.model]);
                }
            }

            this.stopSugarLogic();
            this.startSugarLogic();
            this.collection.trigger('sync', this.collection, this.collection.models);
        }
    },

    /**
     * Prevent shortcuts from dashablerecord overriding the shortcuts set by the main view
     * @override
     */
    registerShortcuts: _.noop,

    /**
     * Gets the currently active tab object.
     *
     * @return {Object|undefined} The currently active tab object, or the
     *   first configured tab if it exists, or else `undefined`.
     * @private
     */
    _getActiveTab: function() {
        if (this.settings.get('activeTab')) {
            return this.settings.get('activeTab');
        }

        if (this.tabs && this.tabs.length) {
            var activeTabIndex = this.settings.get('activeTabIndex') || 0;
            return this.tabs[activeTabIndex];
        }

        if (this.settings.has('tabs')) {
            var tab = this.settings.get('tabs')[0];
            if (_.isString(tab)) {
                return {
                    link: tab,
                    module: this._getRelatedModule(tab),
                    type: this._getTabType(tab)
                };
            }
            return tab;
        }
    },

    /**
     * Set a tab as the active tab and save both the tab and the index
     *
     * @param {number} newActiveTabIndex The index of this.tabs to make the active tab
     */
    setActiveTab: function(newActiveTabIndex) {
        if (this.tabs.length < newActiveTabIndex) {
            this.settings.set('activeTabIndex', null);
            this.settings.set('activeTab', null);
        } else {
            this.settings.set('activeTabIndex', newActiveTabIndex);
            this.settings.set('activeTab', this.tabs[newActiveTabIndex]);
        }
    },

    /**
     * Checks if the passed in tab is the active tab by comparing the link name
     *
     * @param {Object} tab The tab to check
     * @return {boolean}
     * @private
     */
    _isActiveTab: function(tab) {
        return tab.link === this._getActiveTab().link;
    },

    /**
     * Fetches relate fields depending on tabs for the contextual model
     *
     * @private
     */
    _loadContextModel: function() {
        var model = this._getContextModel();
        var fields = this._getRelateFieldsForContextModel();
        model.setOption('fields', fields);
        model.fetch({
            showAlerts: true,
            success: _.bind(function(model) {
                if (this.disposed) {
                    return;
                }
                this._syncIdsToModels(model);
            }, this)
        });
    },

    /**
     * Set ids for related models to the correct models in side the tabs
     *
     * @param {Data.Bean} model The fetched model that contains relate field data
     * @private
     */
    _syncIdsToModels: function(model) {
        _.each(this.tabs, function(tab) {
            if (tab.type !== 'record') {
                return;
            }

            var link = tab.link;
            var relateField = model.get(link);
            if (relateField && relateField.records) {
                relateField = relateField.records[0];
            }
            if (relateField && relateField.id) {
                if (tab.model) {
                    tab.model.dispose();
                }
                tab.model = app.data.createRelatedBean(this._getContextModel(), relateField.id, link);
                this._setDataView(tab.model);
                if (this._isActiveTab(tab)) {
                    this._updateViewToCurrentTab();
                    this._loadDataForTabs([tab]);
                }
            }
        }, this);
    },

    /**
     * Set the dataView for the model to tell the server what fields to load
     * Depending on if recorddashlet metadata is defined, dataView will be recorddashlet
     * or record
     *
     * @param {Data.Bean} model The model to set the dataView on
     * @private
     */
    _setDataView: function(model) {
        var dataView = app.metadata.getView(model.module, 'recorddashlet') ? this.dataView : 'record';
        model.setOption('view', dataView);
    },

    /**
     * Gets relate field names for the context model for each record tab
     * Used to force relate fields onto the context model's fetch so that we get
     * an id for the related record
     *
     * @return {Array} List of relate field names
     * @private
     */
    _getRelateFieldsForContextModel: function() {
        var contextModel = this._getContextModel();
        var tabs = this.settings.get('tabs');
        var fields = [];
        var moduleFields = _.values(app.metadata.getModule(contextModel.module, 'fields'));
        _.each(tabs, function(tab) {
            if (tab.type !== 'record') {
                return;
            }
            var link = tab.link;
            var field = _.find(moduleFields, function(mField) {
                return mField.link === link ||
                    (mField.name === 'parent_type' && contextModel.get('parent_type') &&
                        contextModel.get('parent_type').toLowerCase() === link);
            });
            if (field) {
                if (field.name === 'parent_type') {
                    fields.push(contextModel.get(field.name).toLowerCase());
                } else {
                    fields.push(field.name);
                }
            }
        }, this);

        return fields;
    },

    /**
     * Returns true if the name of the given link should be interpreted as a
     * "link" to the base record.
     *
     * @param {string} link Name of the link to check.
     * @return {boolean} `true` if this link refers to the base record type.
     * @private
     */
    _isLinkToBaseRecord: function(link) {
        return link === '' || link === this._baseModule;
    },

    /**
     * Inject the pseudo dashlet (a configuration pane) into the
     * dashletconfiguration layout.
     *
     * @private
     */
    _addPseudoDashlet: function() {
        var pseudoDashlet = this.layout.getComponent('dashlet');
        if (pseudoDashlet && pseudoDashlet.meta && pseudoDashlet.meta.pseudo) {
            return;
        }

        var metadata = {
            component: this.type,
            name: this.type,
            type: this.type,
            module: this._baseModule,
            config: [],
            preview: []
        };

        var newContext = this.context.getChildContext();
        newContext.prepare(); // try to avoid the pagination plugin complaining about collection not being defined
        var component = {
            name: metadata.component,
            type: metadata.type,
            preview: true,
            context: newContext,
            module: metadata.module,
            custom_toolbar: 'no'
        };
        component.view = _.extend({module: metadata.module}, metadata.preview, component);
        component.view.tabs = [];
        component.view.pseudo = true;

        var settingsTabs = this.settings.get('tabs');
        component.view.tabs = settingsTabs;
        var layout = {
            type: 'dashlet',
            css_class: 'dashlets',
            config: false,
            preview: false,
            module: metadata.module,
            context: this.context,
            components: [
                component
            ],
            pseudo: true
        };
        var pseudoLayout = this.layout.initComponents([{layout: layout}], this.context);
        this._cachePseudoComponent(pseudoLayout);
    },

    /**
     * Save the pseudo dashlet so we can reference it anytime
     *
     * @param {Array} layouts An array of created layouts
     * @private
     */
    _cachePseudoComponent: function(layouts) {
        var layout = layouts[0];
        this._pseudoDashlet = layout.getComponent('dashablerecord');
    },

    /**
     * Event handler for tab switcher.
     *
     * @param {Event} event Click event.
     */
    tabSwitcher: function(event) {
        // don't switch tabs if you are editing
        if (this.action == 'edit' || this.inlineEditMode || !_.isEmpty(this.toggledListModels)) {
            return; // maybe we should show a message?
        }
        var index = this.$(event.currentTarget).data('index');

        if (index === this.settings.get('activeTabIndex')) {
            return;
        }

        this.setActiveTab(index);

        if (this._mode === 'config') {
            this.render();
        } else if (this.meta.pseudo) {
            this._updateViewToCurrentTab();
            this.render();

            if (_.has(this.settings.get('activeTab'), 'type') &&
              this.settings.get('activeTab').type === 'list' &&
              !this.model.get('fields')
            ) {
                this._updateDisplayColumns();
            }
        } else {
            var tab = this.tabs[index];
            this.collection = this.tabs[index].collection || null;
            this._updateViewToCurrentTab();
            this.context.set('collection', this.collection);
            tab.skipFetch = false;

            if (this.collection && !this.collection.dataFetched && !tab.skipFetch && !this.meta.pseudo) {
                this._loadDataForTabs([tab]);
            } else if (tab.type === 'record' && !tab.model.dataFetched) {
                this._loadDataForTabs([tab]);
            }
            this.render();

            this.initTabSugarLogic();
        }
    },

    /**
     * Update the view with tab data at the view level so that the
     * templates can reference the correct meta, model and module
     *
     * @private
     */
    _updateViewToCurrentTab: function() {
        var tab = this.settings.get('activeTab');

        if (!tab) {
            return;
        }

        this.model = tab.model;
        this.module = tab.module;
        this.context.set('module', this.module);
        this.meta = this._extendMeta(tab);
        this.options.meta = this._extendMeta({module: tab.module, type: tab.type});
        this._initDropdownBasedViews();
        this.modulePlural = app.lang.getAppListStrings('moduleList')[this.module] || this.module;
        this.moduleSingular = app.lang.getAppListStrings('moduleListSingular')[this.module] || this.modulePlural;
        this.action = tab.type === 'list' ? 'list' : 'detail';
        // From ConfigDrivenList Plugin
        this.filterConfigFieldsForDashlet();
        this._buildGridsFromPanelsMetadata();
        this.collection = tab.collection || null;
        this.context.set('model', this.model, {silent: true});
        this._prepareHeader(this.meta.panels, this.meta.buttons, tab);
    },

    /**
     * @override
     * Override getLabelPlacement from record.js to always return true since
     * we always want dashlet record labels on top
     *
     * @return {boolean} true
     */
    getLabelPlacement: function() {
        return true;
    },

    /**
     * Set order by on collection.
     * The event is canceled if an element being dragged is found.
     *
     * @param {Event} event jQuery event object.
     */
    setOrderBy: function(event) {
        var $target = $(event.currentTarget);

        if ($target.find('ui-draggable-dragging').length) {
            return;
        }

        var tab = this.settings.get('activeTab');
        var collection = tab.collection;
        // first check if alternate orderby is set for column
        var orderBy = $target.data('orderby');
        // if no alternate orderby, use the field name
        if (!orderBy) {
            orderBy = $target.data('fieldname');
        }
        if (!_.isEmpty(orderBy) && !app.acl.hasAccess('read', tab.module, app.user.get('id'), orderBy)) {
            // no read access to the orderBy field, don't bother to reload data
            return;
        }
        // if same field just flip
        if (orderBy === tab.order_by.field) {
            tab.order_by.direction = tab.order_by.direction === 'desc' ? 'asc' : 'desc';
        } else {
            tab.order_by.field = orderBy;
            tab.order_by.direction = 'desc';
        }

        collection.orderBy = tab.order_by;
        collection.resetPagination();
        this._loadDataForTabs([tab]);
    },

    /**
     * Create collection based on tab properties and current context.
     *
     * @param {Object} tab Tab properties.
     * @return {Data.BeanCollection|null} A new instance of bean collection or `null`
     *   if we cannot access module metadata.
     * @private
     */
    _createCollection: function(tab) {
        // on the multi-line list view, the first time the collections are created this.model is from Home
        // (i.e. it's the dashboard model). This causes fetching to complain.
        var modelToUse = this._getContextModel() || this.model;
        return app.data.createRelatedCollection(modelToUse, tab.link);
    },

    /**
     * Get the row model from row-model-data.
     *
     * @return {Data.Bean|undefined} The rowModel, if it exists.
     * @private
     */
    _getRowModel: function() {
        return this.context.parent.parent.get('rowModel');
    },

    /**
     * Get the contextual model for the dashlet
     *
     * @return {Data.Bean}
     * @private
     */
    _getContextModel: function() {
        let contextModel = null;

        if (this._contextModel) {
            contextModel = this._contextModel;
        } else if (this._hasRowModel()) {
            contextModel = this._cloneModel(this._getRowModel());
        } else {
            var context = this.context;

            // search upward for the first context that has the correct record model
            while (context) {
                var model = context.get('model');

                if (model && model.has('id')) {
                    var module = context.get('module');

                    if (module === this._baseModule) {
                        contextModel = model;
                        break;
                    }
                }

                context = context.parent;
            }

            contextModel = this._cloneModel(contextModel || app.controller.context.get('model'));
        }

        return this._contextModel = contextModel;
    },

    /**
     * Create a new model with the same attributes as the passed in model.
     * Also copies the id
     *
     * @param {Data.Bean} model The model to copy
     * @return {Data.Bean}
     * @private
     */
    _cloneModel: function(model) {
        var clonedModel = app.data.createBean(model.module);
        clonedModel.copy(model);
        clonedModel.set('id', model.get('id'));
        return clonedModel;
    },

    /**
     * Determine if we have a rowModel or not.
     *
     * @return {boolean} `true` if we have a rowModel. `false` otherwise.
     * @private
     */
    _hasRowModel: function() {
        return this.context &&
            this.context.parent &&
            this.context.parent.parent &&
            this.context.parent.parent.has('rowModel');
    },

    /**
     * Retrieve collection options for a specific tab.
     *
     * @param {Object} tab The tab.
     * @return {Object} Collection options.
     * @return {number} return.limit The number of records to retrieve.
     * @return {Object} return.params Additional parameters to the API call.
     * @return {Object|null} return.fields Specifies the fields on each
     * requested model.
     * @private
     */
    _getCollectionOptions: function(tab) {
        return {
            limit: tab.limit || this.settings.get('limit'),
            relate: true,
            params: {
                order_by: !_.isEmpty(tab.order_by) ? tab.order_by.field + ':' + tab.order_by.direction : null,
                include_child_items: tab.include_child_items || null
            },
            fields: this._addRelateFields(tab.module, tab.fields)
        };
    },

    /**
     * Add relates and actual id fields to the field list.
     *
     * @param {string} module The module name
     * @param {Array} fields The field list
     * @return {Array} The expanded field list
     */
    _addRelateFields: function(module, fields) {
        fields = fields || [];
        var vardefFieldMetadata = app.metadata.getModule(module, 'fields') || {};

        // The columns of a dashablerecord can handle fields defined in a module's
        // list view defs, including 'fieldset' type fields that aren't defined
        // in the module's vardefs. This means we need to include the related
        // fields necessary for those in the fetch as well.
        var listViewFieldMetadata = {};
        _.each(this._getFieldMetaForView(app.metadata.getView(module, 'list')) || [], function(field) {
            listViewFieldMetadata[field.name] = field;
        });

        if (vardefFieldMetadata !== {} || listViewFieldMetadata !== {}) {
            // we need to find the relates and add the actual id fields
            var relates = [];
            _.each(fields, function(name) {
                // If the field definition is not found in the vardefs, look in
                // the list view defs
                var meta = vardefFieldMetadata[name] || listViewFieldMetadata[name];
                if (!meta) {
                    return;
                }
                if (meta.type == 'relate') {
                    relates.push(meta.id_name);
                } else if (meta.type == 'parent') {
                    relates.push(meta.id_name);
                    relates.push(meta.type_name);
                }
                if (_.isArray(meta.related_fields)) {
                    relates = relates.concat(meta.related_fields);
                }

                if (_.isArray(meta.fields)) {
                    _.each(meta.fields, function(field) {
                        if (_.isObject(field) && field.name) {
                            relates.push(field.name);
                        } else if (_.isString(field)) {
                            relates.push(field);
                        }
                    }, this);
                }
            });

            fields = _.union(fields, relates);
        }

        return fields;
    },

    /**
     * Retrieve pagination options for current tab. Called by 'Pagination' plugin.
     *
     * @return {Object} Pagination options.
     */
    getPaginationOptions: function() {
        return this._getCollectionOptions(this.settings.get('activeTab'));
    },

    /**
     * Fetch data for tabs.
     *
     * @param {Object} [options={}] Options that are passed to collection/model's
     *   fetch method.
     */
    loadData: function(options) {
        if (this.disposed || this._mode === 'config' || this._mode === 'preview' || this.meta.pseudo) {
            return;
        }
        if (this._contextModel) {
            this._contextModel = undefined;
        }
        this.toggledListModels = {};
        this._loadContextModel();
        this._super('loadData', [options]);
        this._loadDataForTabs(this.tabs, options);
    },

    /**
     * @inheritdoc
     */
    _renderField: function(field, $fieldEl) {
        // Make sure that we render the subfields of the non-editable fieldsets in 'detail' mode
        if (!_.contains(this.editableFields, field) && field.fields && _.isArray(field.fields)) {
            field.setElement($fieldEl || this.$(`span[sfuuid=${field.sfId}]`));
            field.setMode('detail');
        } else {
            this._super('_renderField', [field, $fieldEl]);
        }
    },

    /**
     * @inheritdoc
     */
    _getDropdownBasedViewName: function() {
        return this._mode === 'main' ? 'recorddashlet' : null;
    },

    /**
     * Load data for passed set of tabs.
     *
     * @param {Object[]} tabs Set of tabs to update.
     * @param {Object} [options={}] load options.
     * @private
     */
    _loadDataForTabs: function(tabs, options) {
        // don't load data on the pseudo config  or preview dashlet
        if (this.meta.pseudo || this._mode === 'preview' || this.configLayout) {
            return;
        }

        options = options || {};
        var self = this;
        var loadDataRequests = [];
        var shouldNotMakeRequest = function(tab) {
            if (!tab) {
                return true;
            }
            if (tab.type === 'list') {
                return !tab.collection ||
                    tab.skipFetch ||
                    (
                        tab.collection.link &&
                        tab.collection.link.bean &&
                        tab.collection.link.bean.has('view_name')
                    );
            } else if (tab.type === 'record') {
                return !tab.model ||
                    tab.skipFetch ||
                    tab.model.dataFetched ||
                    _.isEmpty(tab.model.get('id'));
            }
        };
        _.each(tabs, function(tab, index) {
            if (shouldNotMakeRequest(tab)) {
                return;
            }
            loadDataRequests.push(function(callback) {
                if (tab.type === 'list') {
                    tab.collection.setOption(self._getCollectionOptions(tab));
                    tab.collection.fetch({
                        complete: function() {
                            tab.collection.dataFetched = true;
                            callback(null);

                            if (self._isActiveTab(tab)) {
                                self.initTabSugarLogic();
                            }
                        }
                    });
                } else if (tab.type === 'record') {
                    tab.model.fetch({
                        showAlerts: true,
                        success: _.bind(function(model) {
                            callback(null);
                            if (self._isActiveTab(tab)) {

                                // Check the metadata again. On the first switch to the tab,
                                // some of the fields we need for the header buttons might
                                // not be available
                                self.meta = self._extendMeta(tab);
                                self._initDropdownBasedViews();

                                self.render();
                                // init SugarLogic only after fields have been rendered
                                self.initTabSugarLogic();
                            }
                        }, this)
                    });
                }
            });
        }, this);
        if (!_.isEmpty(loadDataRequests)) {
            async.parallel(loadDataRequests, function() {
                if (self.disposed) {
                    return;
                }
                self.collection = self.settings.get('activeTab').collection;
                self.context.set('collection', self.collection);

                self.render();
                if (_.isFunction(options.complete)) {
                    options.complete.call(self);
                }
            });
        }
    },

    /**
     * Util method to determine if we are in a config layout. Used to allow
     * dashlet to render an empty record view for config displays
     *
     * @return {boolean} Whether we are in a config layout
     * @private
     */
    _inConfigLayout: function() {
        var context = this.context;
        while (context) {
            if (context.get('config-layout')) {
                return true;
            }
            context = context.parent;
        }
        return false;
    },

    /**
     * Get the fields metadata for a tab.
     *
     * @param {Object} tab The tab.
     * @return {Object[]} The fields metadata or an empty array.
     * @private
     */
    _getFieldMetaForTab: function(tab) {
        // FIXME: this function needs to be renamed as it only applies to list view tabs
        var meta = app.metadata.getView(tab.module, 'list') || {};
        return this._getFieldMetaForView(meta);
    },

    /**
     * Get the columns to display for a tab.
     *
     * @param tab {Object} Tab to display.
     * @return {Object[]} Array of objects defining the field metadata for
     *   each column.
     * @private
     */
    _getColumnsForTab: function(tab) {
        var columns = [];
        var fields = this._getFieldMetaForTab(tab);
        var moduleMeta = app.metadata.getModule(tab.module);

        _.each(tab.fields, function(name) {
            var field = _.find(fields, function(field) {
                return field.name === name;
            }, this);

            // field may not be in module's list view metadata
            field = field || app.metadata._patchFields(tab.module, moduleMeta, [name]);

            // handle setting of the sortable flag on the list
            // this will not always be true
            var sortableFlag;
            var fieldDef = moduleMeta.fields[field.name];

            // if the module's field def says nothing about the sortability, then
            // assume it's ok to sort
            if (_.isUndefined(fieldDef) || _.isUndefined(fieldDef.sortable)) {
                sortableFlag = true;
            } else {
                // Get what the field def says it is supposed to do
                sortableFlag = !!fieldDef.sortable;
            }

            var column = _.extend({sortable: sortableFlag}, field);
            columns.push(column);
        }, this);

        return columns;
    },

    /**
     * @inheritdoc
     *
     * New model related properties are injected into each model.
     */
    _renderHtml: function() {
        if (this.meta.config) {
            this._super('_renderHtml');
            return;
        }

        // Flag to show the tabs in the tabs.hbs template
        this.showTabs = this.tabs && this.tabs.length > 1;

        this.tabsHtml = this._tabsTemplate(this);

        var tab = this.settings.get('activeTab');

        var tabType = tab && tab.type;

        if (tabType === 'record') {
            this._setRecordState();
            this._setReadonlyFields();
        }

        this.tabContentHtml = this._getTabContentTemplate(tabType)(this);

        // Link to studio if showing a single record
        if (this._shouldShowStudioText(tab)) {
            this.showStudioText = true;
            this.linkToStudio = '#bwc/index.php?module=ModuleBuilder&action=index&type=studio';
        } else {
            this.showStudioText = false;
        }
        this._showHideListBottom(tab);

        this._super('_renderHtml');

        // Only show the SugarLive record link button if we're on an record tab that has an actual record in it
        if (tabType === 'record' && this.model.get('id')) {
            this.createSugarLiveLinkButton();
        } else if (this.sugarLiveLinkButton) {
            this._destroySugarLiveLinkButton();
        }
    },

    /**
     * Ensure at most one button exists at a time for the dashlet, for the current
     * active record view tab only
     * @inheritdoc
     */
    createSugarLiveLinkButton: function() {
        if (this.sugarLiveLinkButton) {
            this._destroySugarLiveLinkButton();
        }

        this._getCurrentSugarLiveContact();
        this._super('createSugarLiveLinkButton');
    },

    /**
     * @inheritdoc
     */
    _toggleSugarLiveButtonVisibility: function(isEdit) {
        let toolbar = this._getToolbar();
        if (toolbar && toolbar.$) {
            if (this.showSugarLiveLinkButton && !isEdit && this.model.get('id')) {
                toolbar.$('.omni-record-link').removeClass('hide');
            } else {
                toolbar.$('.omni-record-link').addClass('hide');
            }

            toolbar.adjustHeaderPaneTitle();
        }
    },

    /**
     * @inheritdoc
     */
    _insertSugarLiveButton: function(linkButton) {
        let toolbar = this._getToolbar();
        if (toolbar && toolbar.$) {
            let actionButtons = toolbar.$('.fieldset.actions.dashlet-toolbar').first();
            actionButtons.before(linkButton.$el);
        }
    },

    /**
     * Helper method to determine if we should show the edit in studio message
     *
     * @param {Object} tab The tab
     * @return {boolean} `true` to show the message
     * @private
     */
    _shouldShowStudioText: function(tab) {
        if (!tab || !tab.type || !tab.module) {
            return false;
        }
        return this.meta.pseudo && tab.type === 'record' &&
            app.acl.hasAccess('developer', tab.module) &&
            !_.isNull(app.metadata.getView(tab.module, 'recorddashlet'));
    },

    /**
     * Set the state of the current record view tab
     *
     * @private
     */
    _setRecordState: function() {
        var tab = this._getActiveTab();
        var contextModel = this._getContextModel();
        if (tab.model.dataFetched || this.meta.pseudo || this.configLayout) {
            this.recordState = 'READY';
        } else if ((contextModel.dataFetched && _.isEmpty(tab.model.get('id')) ||
            (tab.link && _.isEmpty(tab.model.get('id')))
        )) {
            this.recordState = 'NODATA';
        } else {
            this.recordState = 'LOADING';
        }
    },

    /**
     * Sets all model fields in `extraNoEditFields` array if we are in a config
     * layout, since the dashlet is for display purposes only and does not
     * represent a record.
     *
     * @private
     */
    _setReadonlyFields: function() {
        if (this.configLayout) {
            var tab = this._getActiveTab();
            var noEditFields = this.extraNoEditFields || [];
            noEditFields.push(_.keys(tab.model.fields));
            noEditFields = _.uniq(noEditFields);
            this.extraNoEditFields = noEditFields;
        }
    },

    /**
     * Get the correct content template based off of tab type and view type
     *
     * @param {string} tabType Either `record` or `list`
     * @return {*}
     * @private
     */
    _getTabContentTemplate: function(tabType) {
        if (this.meta.pseudo) {
            return tabType === 'list' ? this._configListTemplate : this._recordTemplate;
        }

        if (this._mode === 'main') {
            return tabType === 'list' ? this._recordsTemplate : this._recordTemplate;
        }
    },

    /**
     * @override
     *
     * Listen to change:model event to populate this dashlet with a new bean.
     */
    bindDataChange: function() {
        this.context.on('change:model', function(ctx, model) {
            this.switchModel(model);
            this._injectRecordHeader(model);
            this.render();
        }, this);
    },

    /**
     * @override
     *
     * The buttons are actually on the dashlet-toolbar component, so we have to
     * listen there rather than on the record view dashlet context.
     */
    delegateButtonEvents: function() {
        if (this._hasDelegated) {
            return;
        }

        var toolbar = this._getToolbar();
        if (!toolbar) {
            return;
        }

        var context = toolbar.context;
        this._hasDelegated = true;
        _.each(this._toolbarContextEvents, function(value, key) {
            context.on(key, value, this);
        }, this);
    },

    /**
     * @override
     */
    editClicked: function() {
        // the dashlet toolbar is triggering record view's editClicked, so override it here
        this._getToolbar().editClicked();
    },

    /**
     * Edit the underlying record (rather than the dashlet itself).
     */
    editRecord: function() {
        this._super('editClicked', arguments);
    },

    /**
     * @override
     *
     * Propagate button state requests to the dashlet toolbar.
     */
    setButtonStates: function(state) {
        var toolbar = this._getToolbar();
        toolbar && toolbar.setButtonStates(state);
    },

    /**
     * @override
     *
     * Do nothing. Dashlets should not affect the route.
     */
    setRoute: _.noop,

    /**
     * Get the dashlet toolbar component.
     *
     * @return {View.Layout}
     * @private
     */
    _getToolbar: function() {
        return this.layout && this.layout.getComponent('dashlet-toolbar');
    },

    /**
     * Use the given model to render this dashlet.
     * This transfers any events from the existing model to the new one.
     *
     * @param {Data.Bean} model Model to render.
     */
    switchModel: function(model) {
        this.model && this.model.abortFetchRequest();
        this.stopListening(this.model);
        this.model = model;
        if (this.module !== this.model.module) {
            this.module = this.model.module;
            this.meta = this._extendMeta({type: 'record', module: this.module});
            this._initDropdownBasedViews();
        }
        this._initTabs();
    },

    /**
     * Certain dashlet settings can be defaulted.
     *
     * Builds the available module cache by way of the
     * {@link BaseDashablerecordView#_setDefaultModule} call.
     *
     * @private
     */
    _initializeSettings: function() {
        var settings = _.extend(
            {},
            this._defaultSettings,
            this.settings.attributes
        );
        this.settings.set(settings);
        this._setDefaultModule();
        if (!this.settings.get('label')) {
            this.settings.set('label', 'LBL_MODULE_NAME');
        }
        if (!this.settings.has('tabs') && this.meta.tabs) {
            this.settings.set('tabs', this.meta.tabs);
        }
    },

    /**
     * Filters the list of extra modules to return only those the user has access to
     * @return {string[]}
     * @private
     */
    _getVisibleExtraModules: function() {
        return this.extraModules.filter(moduleName => app.acl.hasAccess('view', moduleName));
    },

    /**
     * Sets the default module when a module isn't defined in this dashlet's
     * view definition.
     *
     * If the module was defined but it is not in the list of available modules
     * in config mode, then the view's module will be used.
     *
     * @private
     */
    _setDefaultModule: function() {
        var availableModules = this._getAvailableModules();
        var metadata = app.metadata.getModule(this.model.module);
        var fields = metadata && metadata.fields;
        var module = this.settings.get('module') || this.context.get('module');

        // note: the module in the settings might actually be the name of a link field
        if (fields && this._isALink(module, fields)) {
            // FIXME: I might actually have to call _getModuleFromLink or whatever here
            module = fields[module].module;
        }

        if (module in availableModules) {
            this.settings.set('module', module);
        } else if (this._mode === 'config') {
            module = this.context.parent.get('module');
            if (_.contains(this.moduleBlacklist, module)) {
                module = _.first(_.keys(availableModules));
                // On 'initialize' model is set to context's model - that model can have no access at all
                // and we'll result in 'no-access' template after render. So we change it to default model.
                this.model = app.data.createBean(module);
            }
            this.settings.set('module', module);
        } else {
            this.moduleIsAvailable = false;
        }
    },

    /**
     * Perform any necessary setup before the user can configure the dashlet.
     *
     * Modifies the dashlet configuration panel metadata to allow it to be
     * dynamically primed prior to rendering.
     *
     * @private
     */
    _configureDashlet: function() {
        var availableModules = this._getAvailableModules();
        var validTabs = this._getValidTabs(_.keys(availableModules));

        _.each(this._getFieldMetaForView(this.meta), function(field) {
            if (field.name === 'module' || field.name === 'tab_list') {
                // load the list of available modules into the metadata
                field.options = validTabs;
                field.default = this.module;
            }
        }, this);

        this.listenTo(this.layout, 'init', this._addPseudoDashlet);

        // load the previously selected tabs by default
        var initialTabs = this.settings.get('tab_list');
        // in case this is the initial setup, load in the current module as the tab
        if (_.isUndefined(initialTabs)) {
            initialTabs = [this.settings.get('module')];
            var configTabs = this._generateConfigTabs(initialTabs);
            this.settings.set('tabs', configTabs);
        }
        this.settings.set('templateEdit', 'detail');
        this.settings.set('tab_list', initialTabs);
        this.settings.set('label', 'LBL_DASHLET_RECORDVIEW_NAME');

        this._bindSettingsEvents();
        this._bindSaveEvents();
    },

    /**
     * Gets all of the modules the current user can see.
     *
     * This is used for populating the module select field.
     * Filters out any modules that are blacklisted.
     *
     * @return {Object} {@link BaseDashablerecordView#_availableModules}
     * @private
     */
    _getAvailableModules: function() {
        if (_.isEmpty(this._availableModules) || !_.isObject(this._availableModules)) {
            this._availableModules = {};
            var visibleModules = app.metadata.getModuleNames({filter: 'visible', access: 'read'});
            visibleModules = visibleModules.concat(this._getVisibleExtraModules());
            var allowedModules = _.difference(visibleModules, this.moduleBlacklist);

            _.each(allowedModules, function(module) {
                var recordMeta = this._getRecordMeta(module);
                var hasRecordView = !_.isEmpty(this._getFieldMetaForView(recordMeta));
                if (hasRecordView) {
                    this._availableModules[module] = app.lang.getModuleName(module, {plural: true});
                }
            }, this);
        }
        return this._availableModules;
    },

    /**
     * Gets the fields metadata from a particular view's metadata.
     *
     * @param {Object} meta The view's metadata.
     * @return {Object[]} The fields metadata or an empty array.
     * @private
     */
    _getFieldMetaForView: function(meta) {
        meta = _.isObject(meta) ? meta : {};
        return _.compact(!_.isUndefined(meta.panels) ? _.flatten(_.pluck(meta.panels, 'fields')) : []);
    },

    /**
     * Gets the correct record view metadata.
     *
     * @param {string} module
     * @return {Object} The correct module record metadata.
     * @private
     */
    _getRecordMeta: function(module) {
        let recordMeta = app.metadata.getView(module, 'record');

        // Disable extra meta on the dashlet for the Users module
        if (module !== 'Users') {
            let extraMeta = app.metadata.getView(module, 'record-extra-meta');
            if (recordMeta && recordMeta.panels && extraMeta && extraMeta.panels) {
                recordMeta.panels = recordMeta.panels.concat(extraMeta.panels);
            }
        }

        return recordMeta;
    },

    /**
     * Renders the no-access template, then aborts further rendering.
     *
     * @return {boolean} Always returns `false`.
     * @private
     */
    _noAccess: function() {
        this.$el.html(this._noAccessTemplate());
        return false;
    },

    /**
     * Renders the no-tabs-available template, then aborts further rendering.
     *
     * @return {boolean} Always returns `false`.
     * @private
     */
    _noTabsAvailable: function() {
        this.$el.html(this._noTabsAvailableTemplate());
        return false;
    },

    /**
     * Prepare the header fields from the given panels and buttons.
     *
     * @param {Object[]} panels Record view panel metadata.
     * @param {Object[]} buttons Record view button metadata.
     * @param {Object[]} activeTab Active/current tab.
     * @private
     */
    _prepareHeader: function(panels, buttons, activeTab) {
        this._headerFields = this._headerFields || [];
        this._headerButtons = this._headerButtons || [];
        var model = activeTab ? activeTab.model : null;
        var tabType = activeTab ? activeTab.type : null;

        // find which (if any) of the panels is for the header
        var headerIndex = _.findIndex(panels, function(panel) {
            return panel.header === true;
        });

        if (headerIndex !== -1) {
            // get all the fields we want to show in the header and shrink them down if necessary
            var header = panels[headerIndex];
            var fields = _.filter(header.fields, _.bind(function(field) {
                return !field.type || !_.includes(this._noshowFields, field.type);
            }, this));

            // shrink certain header fields down for the toolbar
            _.each(fields, function(field) {
                if (field.size) {
                    field.size = 'button';
                }
                if (field.type === 'avatar') {
                    field.height = field.height ? Math.min(field.height, this._avatarSize) : this._avatarSize;
                    field.width = field.width ? Math.min(field.width, this._avatarSize) : this._avatarSize;
                }
            }, this);

            // tweak the buttons as necessary
            var desiredButtons = this._getHeaderButtons(buttons, activeTab);
        } else if (tabType === 'list') {
            this._headerFields = this._getHeaderFieldsForListTab();
        }
        this._headerFields = fields || this._headerFields;
        this._headerButtons = desiredButtons || this._headerButtons;

        this._initButtons();
        this._injectRecordHeader(model);

        if (_.isFunction(this.insertActionButtonsRows)) {
            this.insertActionButtonsRows();
        }
    },

    /**
     * Adjust the given button definitions for appropriate use in the dashlet
     * toolbar.
     *
     * @param {Object[]} buttons List of button fielddefs from metadata.
     * @param {Object[]} tab Current tab
     * @return {Object[]} The list of button definitions tweaked for the
     *   dashlet toolbar.
     * @private
     */
    _getHeaderButtons: function(buttons, tab) {
        var desiredButtons = [];
        // If we're rendering this dashlet in a console config layout, we do NOT
        // want edit, save, or cancel buttons on our empty dashlet
        // Also don't display the buttons if there's no related record
        if (this.configLayout || !this.model || !this.model.get('id')) {
            return desiredButtons;
        }

        var self = this;
        _.each(buttons, function(button) {
            if (button.buttons) { // dropdown
                // remove all dividers
                button.buttons = _.filter(button.buttons, function(subButton) {
                    return subButton.type !== 'divider';
                });

                // Mark dropdowns as needing to be filtered
                _.each(button.buttons, function(button) {
                    button.filterForRecordDashlet = true;
                });
            }
            var desiredButtonNames = [
                'save_button',
                'cancel_button',
                'edit_button',
                'dashlet_save_button',
                'dashlet_cancel_button',
                'dashlet_edit_button',
            ];
            if (_.includes(desiredButtonNames, button.name) || button.type === 'actiondropdown') {
                if (!button.name.includes('dashlet_')) {
                    button.name = 'dashlet_' + button.name;
                }
                desiredButtons.push(button); // note, save the original button, not the subbutton
            }
        });

        return desiredButtons;
    },

    /**
     * Re-fetch the model and update the metadata after an action has finished
     * @private
     */
    _updateAllowedButtons: function() {
        var tab = this.settings.get('activeTab');

        if (!tab) {
            return;
        }

        var self = this;
        tab.model.fetch({
            showAlerts: true,
            success: function(model) {
                if (self._isActiveTab(tab)) {
                    self.meta = self._extendMeta(tab);
                    self.render();
                }
            }
        });
    },

    /**
     * Send header fielddefs and model data to the dashlet toolbar.
     *
     * @param {Data.Bean} [model] Model to send to the toolbar. If undefined,
     *   the header fielddefs will be sent to the toolbar but the model data
     *   will not.
     * @private
     */
    _injectRecordHeader: function(model) {
        if (this.meta && this.meta.pseudo) {
            return;
        }
        // inject header content into dashlet toolbar
        var dashletToolbar = this._getToolbar();
        if (dashletToolbar) {
            var buttonsToSend = [];
            var activeTab = this._getActiveTab() || {};
            if (app.acl.hasAccessToModel('edit', model) && activeTab.type === 'record') {
                buttonsToSend = this._headerButtons;
            }

            var toolbarCtx = dashletToolbar.context;
            toolbarCtx.trigger('dashlet:toolbar:change', this._headerFields, buttonsToSend, model, this);
            this.delegateButtonEvents();
        }
    },

    /**
     * Build the header fields array object for list type tab
     *
     * @return {Array}
     * @private
     */
    _getHeaderFieldsForListTab: function() {
        var labelValue = app.lang.get('LBL_RELATED_RECORDS', null, {
            module: app.lang.getModuleName(this.module, {plural: true})
        });
        return [
            {
                dismiss_label: true,
                height: this._avatarSize,
                label: 'LBL_PICTURE_FILE',
                name: 'picture',
                size: 'button',
                type: 'avatar',
                width: this._avatarSize,
                readonly: true,
            },
            {
                type: 'label',
                formatted_value: labelValue,
                readonly: true,
            },
        ];
    },

    /**
     * Set the proper widths of the dashlet-toolbar fields
     */
    adjustHeaderpaneFields: function() {
        this._super('adjustHeaderpaneFields');
        var toolbar = this._getToolbar();
        if (!toolbar) {
            return;
        }
        var $recordCells = this._getRecordCells();
        var nameField = this._getNameFieldFromRecordCells($recordCells);
        var $nameField = $(nameField);
        if ($nameField.hasClass('edit')) {
            // We need to calculate how much available space there is for the name field
            var fieldsWidth = 0;
            var toolbarWidth = toolbar.$el.outerWidth();
            var btnsWidth = toolbar.$('.btn-toolbar').outerWidth();
            _.each(toolbar.$('.table-cell-wrapper'), function(cell) {
                var $cell = $(cell);
                var parentType = $cell.parent().data('type');
                // ignore the name field since we are going to change its width anyways
                if (parentType === 'name' || parentType === 'fullname') {
                    return;
                }
                fieldsWidth += $cell.outerWidth();
            });

            // subtracting additional 20px to avoid the name field overlapping button
            var nameFieldWidth = toolbarWidth - btnsWidth - fieldsWidth - 20;
            $nameField.find('.table-cell-wrapper').css({width: nameFieldWidth + 'px'});
        } else {
            $nameField.find('.table-cell-wrapper').css({width: ''});
        }

        // Make sure the header fields use the correct templates
        if (this.action === 'detail') {
            let nameFields = _.filter(this.editableFields, function(field) {
                return _.contains(['name', 'fullname'], field.type);
            });
            _.each(nameFields, function(field) {
                if (field.$el) {
                    field.enableTitleLink = !!this.closestComponent('side-drawer');
                    if (field.enableTitleLink) {
                        field.module = this.model.module || this.context.get('module');
                        field.modelId = this.model.get('id');
                        field.linkTarget = 'focus';
                    }
                    field.setMode('dashlet-header');
                }
            }, this);
        }

        toolbar.adjustHeaderPaneTitle();
    },

    /**
     * Get the name field a list of record-cells
     * @param {Array} $cells Array of jQuery elements
     * @return {jQuery}
     * @private
     */
    _getNameFieldFromRecordCells: function($cells) {
        var nameFields = ['name', 'fullname'];
        return _.find($cells, function(cell) {
            return _.contains(nameFields, $(cell).data('type'));
        });
    },

    /**
     * dashablerecord has fields on this view and on the dashlet-toolbar view. We need both
     * @inheritdoc
     */
    _getNonButtonFields: function() {
        var viewFields = this._filterButtonsFromFields(this.fields);
        var toolbarFields = [];
        var toolbar = this._getToolbar();
        if (toolbar) {
            toolbarFields = this._filterButtonsFromFields(toolbar.fields);
        }

        return _.union(toolbarFields, viewFields);
    },

    /**
     * Filter out dashletaction fields
     * @inheritdoc
     */
    _filterButtonsFromFields: function(fields) {
        fields = this._super('_filterButtonsFromFields', [fields]);
        return _.filter(fields, function(field) {
            return field.type !== 'dashletaction';
        });
    },

    /**
     * Set up buttons from the header instead of from record meta. Assume this._headerButtons is set up already
     * @override
     */
    _initButtons: function() {
        this.buttons = [];
        _.each(this._headerButtons, function(button) {
            this.registerFieldAsButton(button.name);
        }, this);
    },

    /**
     * Get button field instances from the toolbar instead of from this view.
     * @override
     */
    registerFieldAsButton: function(buttonName) {
        var toolbar = this._getToolbar();
        if (toolbar) {
            var button = toolbar.getField(buttonName);
            if (button) {
                this.buttons[buttonName] = button;
            }
        }
    },

    /**
     * Gets the dashletconfiguration layout.
     *
     * @return {View.Layout} The dashletconfiguration layout.
     * @private
     */
    _getDashletConfiguration: function() {
        return this.closestComponent('dashletconfiguration');
    },

    /**
     * Turn the dashlet configuration save button on or off.
     *
     * @param {bool} enabled true to enable and false to disable.
     * @private
     */
    _toggleDashletSaveButton: function(enabled) {
        this._getDashletConfiguration().trigger('dashletconfig:save:toggle', enabled);
    },

    /**
     * Initialize tabs.
     *
     * @param {Object[]} [newTabs] List of new tabs.
     * @private
     */
    _initTabs: function(newTabs) {
        this.tabs = [];

        var dashletTabs;
        if (this._mode === 'config') {
            dashletTabs = this.meta.tabs || [];
        } else if (this.meta.pseudo) {
            dashletTabs = this._patchTabsFromSettings(newTabs);
        } else {
            dashletTabs = newTabs || this._getTabsFromSettings() || this.meta.tabs || [];
        }

        _.each(dashletTabs, function(tab, index) {
            if (!app.acl.hasAccess('view', tab.module)) {
                return;
            }
            if (tab.active) {
                this.settings.set('activeTabIndex', index);
                this.settings.set('activeTab', tab);
            }
            tab.type = tab.type || this._getTabType(tab.link);
            var module;
            if (tab.type === 'list') {
                var collection = this._createCollection(tab);
                if (_.isNull(collection)) {
                    return;
                }
                module = tab.module;
                tab.model = app.data.createBean(module);
                if (tab.fields) {
                    tab.model.set('fields', tab.fields, {silent: true});
                }
                if (tab.limit) {
                    tab.model.set('limit', tab.limit, {silent: true});
                }
                if (tab.auto_refresh) {
                    tab.model.set('auto_refresh', tab.auto_refresh, {silent: true});
                }
                if (!_.isUndefined(tab.freeze_first_column)) {
                    tab.model.set('freeze_first_column', tab.freeze_first_column, {silent: true});
                }
                tab.collection = collection;
                tab.relate = _.isObject(collection.link);
                tab.include_child_items = tab.include_child_items || false;
                tab.collection.display_columns = [{
                    fields: this._getColumnsForTab(tab),
                    module: module
                }];
                tab.collection.orderBy = tab.order_by || {};
                if (this.meta.pseudo) {
                    tab.meta = app.metadata.getView(null, this.name).listsettings;
                    _.each(this._getFieldMetaForView(tab.meta), function(field) {
                        if (field.name === 'fields') {
                            // load the list of available modules into the metadata
                            field.options = this._getAvailableColumns(tab);
                            field.default = tab.module;
                        }
                    }, this);
                } else {
                    tab.meta = this._getFieldMetaForTab(tab);
                }
            } else if (tab.type === 'record') {
                // Single record (record view tab)
                module = tab.module;
                tab.meta = app.metadata.getView(module, 'recorddashlet') || this._getRecordMeta(module);
                var contextModel = this._getContextModel() || app.data.createBean(module);
                if (this.meta.pseudo) {
                    tab.model = app.data.createBean(module);
                } else if (tab.link === tab.module || tab.link === '') {
                    // Record view for the context model
                    tab.model = this._cloneModel(this._getContextModel());
                } else {
                    // related 1 side record view
                    var id = null;
                    var relationship = contextModel.get(tab.link);
                    if (relationship) {
                        id = relationship.id || null;
                    }
                    tab.model = app.data.createRelatedBean(contextModel, id, tab.link);
                }

                if (tab.module === 'Users') {
                    this._initUserModuleRecordTab(tab);
                }

                if (this.configLayout) {
                    _.each(tab.meta.panels, function(panel) {
                        _.each(panel.fields, function(field) {
                            field.readonly = true;
                        });
                    });
                }
                this._setDataView(tab.model);
            }
            this.tabs[index] = tab;
        }, this);

        // Set this to false if we pruned out all the tabs
        this.moduleIsAvailable = !((this.tabs.length === 0) && (dashletTabs.length > 0));
    },

    /**
     * Updates Users module record tabs to take into account whether they are a
     * Group or Portal API type User, as these User types have special views
     *
     * @param {Object} tab the tab's settings data
     * @private
     */
    _initUserModuleRecordTab: function(tab) {
        let modelFetchFields = tab.model.getOption('fields') || [];
        modelFetchFields.push('is_group', 'portal_only');
        tab.model.setOption('fields', _.uniq(modelFetchFields));

        this.listenTo(tab.model, 'change:is_group change:portal_only', () => {
            let viewType = tab.model.get('is_group') ? 'group' :
                tab.model.get('portal_only') ? 'portalapi' :
                    false;
            if (['group', 'portalapi'].includes(viewType)) {
                tab.meta = _.extend({}, tab.meta, app.metadata.getView('Users', `record-${viewType}`));
            }

            if (this._isActiveTab(tab)) {
                this.meta = this._extendMeta(tab);
                this.render();
            }
        });
    },

    /**
     * Sets up tabs by pulling any saved tab info from setting and merging
     * with any new tabs to be created
     *
     * @param {Array} newTabs Tabs to shown
     * @return {Array} Tabs that have picked up any saved settings
     */
    _patchTabsFromSettings: function(newTabs) {
        var settings = this.settings.get('tabs');
        var tabs = [];

        _.each(newTabs, function(t) {
            var foundTab = _.find(settings, function(s) {
                return s.link == t.link;
            });
            if (foundTab) {
                tabs.push(_.extend(t, foundTab));
            } else {
                tabs.push(t);
            }

        });
        return tabs;
    },

    /**
     * Given a list of tab names (either base module or a link field name),
     * return a list of tabs suitable for rendering.
     *
     * @param {string[]} tablist List of tabs (base module or link name).
     * @param {Object} [options] Additional tab options (to be applied to every
     *   tab.)
     * @return {Object[]} The list of tab objects.
     * @private
     */
    _generateConfigTabs: function(tablist, options) {
        if (_.isEmpty(tablist)) {
            return [];
        }

        options = options || {};

        return _.map(tablist, function(tab) {
            var link = tab;
            if (_.isObject(tab)) {
                link = tab.link || tab.module;
            }

            var tabType = this._getTabType(link);
            var relatedModule = this._getRelatedModule(link);

            var baseOptions = {
                type: tabType,
                label: this._getLinkLabel(link),
                module: relatedModule,
                link: link
            };

            // FIXME CS-63: we'll probably want to do this differently later
            if (tabType === 'list') {
                baseOptions.fields = _.pluck(app.metadata.getView(relatedModule, 'list').panels[0].fields, 'name');
                baseOptions.limit = this._defaultSettings.limit;
                baseOptions.freeze_first_column = this._defaultSettings.freeze_first_column;
                baseOptions.skipFetch = true;
            }

            return _.extend(baseOptions, options);
        }, this);
    },

    /**
     * Determine whether a tab for the given link name is a list type or a
     * record type.
     *
     * @param {string} linkName The link name.
     * @return {string} "list" if this tab should be a list view and "record"
     *   otherwise.
     * @private
     */
    _getTabType: function(linkName) {
        return app.data.canHaveMany(this._baseModule, linkName) ? 'list' : 'record';
    },

    /**
     * Get the link label for the given link field name.
     *
     * @param {string} linkName Name of the link field.
     * @return {string}
     * @private
     */
    _getLinkLabel: function(linkName) {
        if (this._isLinkToBaseRecord(linkName)) {
            return this._getBaseRecordLabel();
        }

        var fields = this._getBaseModuleFields();
        var linkField = fields[linkName];
        var module = this._getModuleFromLinkField(linkField);

        return app.lang.get(
            linkField.vname,
            [this._baseModule, module]
        );
    },

    /**
     * Get the related module name given a link name,
     * relative to the base module.
     *
     * If given the base module, just return that.
     *
     * @param {string} linkName Link field name.
     * @return {string} Name of the related module.
     * @private
     */
    _getRelatedModule: function(linkName) {
        if (this._isLinkToBaseRecord(linkName)) {
            return this._baseModule;
        }

        return this._getModuleFromLinkField(this._getBaseModuleFields()[linkName]);
    },

    /**
     * Get the module from a link field.
     *
     * @param {Object} linkField The link field vardef.
     * @return {string} The name of the module.
     * @private
     */
    _getModuleFromLinkField: function(linkField) {
        if (!linkField || !linkField.name) {
            return '';
        }

        if (linkField.module) {
            return linkField.module;
        }

        // a lot of link fields don't actually have a module on them (look at the Accounts vardef for proof)
        // in that case, determine the module from the relationship rather than the link
        return app.data.getRelatedModule(this._baseModule, linkField.name);
    },

    /**
     * For the dashlet, get a list of tabs to show from the config
     * settings.
     *
     * @return {Object[]} List of dashlet tabs as retrieved from settings.
     * @private
     */
    _getTabsFromSettings: function() {
        return this.settings.get('tabs');
    },

    /**
     * Check if the given string corresponds to a link field.
     *
     * @param {string} str The name to check.
     * @param {Object} fields The fielddefs for the module to check.
     * @return {boolean} true if the given name corresponds to a link field,
     *   false otherwise.
     * @private
     */
    _isALink: function(str, fields) {
        return !!(fields[str] && fields[str].type === 'link');
    },

    /**
     * Get a translated label of the form "This <module>"
     * for the base module.
     *
     * @return {string}
     * @private
     */
    _getBaseRecordLabel: function() {
        return app.lang.get(
            'TPL_DASHLET_RECORDVIEW_THIS_RECORD_TYPE',
            null,
            {moduleSingular: app.lang.getModuleName(this._baseModule)}
        );
    },

    /**
     * Get the list of acceptable tabs filtered across available modules,
     * which are related to the base module.
     *
     * @param {string[]} availableModules The list of available modules.
     * @return {Object} A mapping of link field names to display link labels.
     * @private
     */
    _getValidTabs: function(availableModules) {
        var baseRecordTypes = {};

        // get the "This Account" label
        baseRecordTypes[this._baseModule] = this._getBaseRecordLabel();

        // find the related module for each link field and make sure we can use it
        var linkFields = this._getBaseModuleLinks();
        _.each(linkFields, function(linkField) {
            var relatedModule = app.data.getRelatedModule(this._baseModule, linkField.name);

            if (!_.contains(availableModules, relatedModule) || _.contains(this.tabBlacklist, relatedModule)) {
                return;
            }

            baseRecordTypes[linkField.name] = this._getLinkLabel(linkField.name);
        }, this);

        return baseRecordTypes;
    },

    /**
     * In config mode, bind events that occur when the configuration options
     * are (about to be) saved.
     *
     * @private
     */
    _bindSaveEvents: function() {
        this.layout.before('dashletconfig:save', function() {
            // save the dashlet tabs settings in full, not the strings-only version
            var tabs = this._pseudoDashlet.settings.get('tabs');
            var settings = {
                base_module: this._baseModule,
                label: this.settings.get('label'),
                tabs: this._getTabsToSave(tabs),
                //tab_list: this.settings.get('tab_list'), // not sure if we need this, uncomment if we find a bug
            };

            // don't save unwanted record view metadata into the dashlet. Just whitelist what we need.
            this.settings.clear({silent: true});

            this.settings.set(settings, {silent: true});
            this.dashModel.set(settings, {silent: true});
        }, this);
    },

    /**
     * Sanitize and only save properties of tabs that matter in DB
     * @param {Array} tabs List of tabs to save
     * @return {Array} Tabs that have been sanitized
     */
    _getTabsToSave: function(tabs) {
        var returnTabs = [];
        _.each(tabs, function(tab) {
            var tabToSave = {
                type: tab.type,
                module: tab.module,
                label: tab.label,
                link: tab.link
            };
            if (tab.type === 'list') {
                var listOptions = {
                    fields: tab.model.get('fields') || [],
                    limit: tab.model.get('limit'),
                    auto_refresh: tab.model.get('auto_refresh'),
                    freeze_first_column: tab.model.get('freeze_first_column'),
                };
                tabToSave = _.extend(tabToSave, listOptions);
            }
            returnTabs.push(tabToSave);
        });
        return returnTabs;
    },

    /**
     * In config mode, bind events that occur when the configuration options
     * change.
     *
     * @private
     */
    _bindSettingsEvents: function() {
        // Commenting this out until we support 2 level relationships
        // this.settings.on('change:module', function(model, moduleName) {
        //     this.dashModel.set('module', moduleName);
        //
        //     // clear out any previously selected tabs, except for the new base record type
        //     this._resetConfigTabs();
        //     this.settings.set('tabs', [moduleName]);
        // }, this);

        this.settings.on('change:tab_list', function(model, tabs) {
            // show warning message if too many tabs
            if (tabs && tabs.length > this._tabLimit.number) {
                this._showTooManyTabsWarning();
            }

            // disable save button on both 0 and too many tabs
            var enableSaveButton = tabs && tabs.length && tabs.length <= this._tabLimit.number;
            this._toggleDashletSaveButton(enableSaveButton);

            // for rendering the tabs in the config
            var configTabs = this._generateConfigTabs(tabs || [], {skipFetch: true});
            model.set('tabs', configTabs, {silent: true});
            var configDashletLayout = this.layout.getComponent('dashlet');
            configDashletLayout.context.trigger('dashablerecord:config:tablist:change', configTabs);
        }, this);
    },

    /**
     * Show a warning that there are too many tabs selected.
     *
     * @private
     */
    _showTooManyTabsWarning: function() {
        app.alert.show('too_many_tabs', {
            level: 'warning',
            messages: app.lang.get(
                'TPL_DASHLET_RECORDVIEW_TOO_MANY_TABS',
                null,
                {num: this._tabLimit.number, numWord: app.lang.get(this._tabLimit.label)}
            )
        });
    },

    /**
     * Return the fielddefs from the base module.
     *
     * @return {Object} Fielddefs from the base module.
     * @private
     */
    _getBaseModuleFields: function() {
        if (this._baseModuleFields) {
            return this._baseModuleFields;
        }

        this._baseModuleFields = app.metadata.getModule(this._baseModule).fields;
        return this._baseModuleFields;
    },

    /**
     * Get all fields of type link from the base module.
     *
     * @return {Object[]} List of base module fields of type link.
     * @private
     */
    _getBaseModuleLinks: function() {
        return _.filter(this._getBaseModuleFields(), function(field) {
            return field.type && field.type === 'link';
        });
    },

    /**
     * Show or hide the list-bottom component depending on the tab type.
     *
     * @param {Object} tab Tab to be shown.
     * @param {boolean} forceHide Force the list-bottom component to hide
     * @private
     */
    _showHideListBottom: function(tab, forceHide) {
        var listBottom = this.layout.getComponent('list-bottom');
        if (!listBottom) {
            return;
        }
        if (forceHide) {
            listBottom.hide();
        } else {
            tab && tab.type === 'list' ? listBottom.show() : listBottom.hide();
        }
    },

    /**
     * Get the collection of record-cell elements in the header.
     *
     * @return {jQuery|undefined} The collection of record-cell and btn-toolbar
     *   elements from the toolbar, or `undefined` if the toolbar is not
     *   available.
     * @private
     */
    _getRecordCells: function() {
        var toolbar = this._getToolbar();
        if (!toolbar) {
            return;
        }
        return toolbar.$('h4.record-toolbar').children('.record-cell, .btn-toolbar');
    },

    /**
     * @override
     *
     * Get the available width for fields in the header.
     * From the full width of the container we need to subtract
     * the paddings and reserve some space for custom buttons.
     * The super method relies on functionality that is not present
     * in the context this component is used in. (`layout.getPaneWidth`)
     *
     * @return {number} Returns the numeric width value to be assgined to the title field.
     */
    getContainerWidth: function() {
        var containerWidth = 0;
        var titleRightIndent = 10;
        var defaultRecommendedWidth = 230;
        var btnBar = this.layout.$el.find('.btn-toolbar');
        var titleBar = this.layout.$el.find('.dashlet-title');
        // we need to use header bar as we are subtracting other widths from this to get title width
        var headerBar = this.layout.$el.find('.dashlet-header');

        if (titleBar.length && headerBar) {
            var titleBarChildMargins = 0;
            _.each(titleBar.children(), function(child) {
                titleBarChildMargins += parseInt($(child).css('marginLeft'), 10) || 0;
                titleBarChildMargins += parseInt($(child).css('marginRight'), 10) || 0;
            });
            containerWidth = headerBar.width() - titleBarChildMargins - titleRightIndent;

            if (btnBar.length) {
                var buttonsWidth = 0;
                _.each(btnBar.children(), function(button) {
                    var $button = $(button);
                    if ($button.css('display') !== 'none') {
                        buttonsWidth += $button.outerWidth(true);
                    }
                });

                var btnBarLeftMargin = parseInt(btnBar.css('marginLeft'), 10) || 0;
                if (buttonsWidth > btnBarLeftMargin) {
                    containerWidth -= (buttonsWidth - btnBarLeftMargin);
                }
            }
        }

        return (containerWidth > 0) ? containerWidth : defaultRecommendedWidth;
    },

    /**
     * Gets a list of columns for a list view tab regardless of any saved settings.
     * Updates the fields enum field
     * @private
     */
    _updateDisplayColumns: function() {
        var tab = this.settings.get('activeTab');
        if (!tab) {
            return;
        }
        // need to check if we already have columns saved in tabs and pull that instead if it is set
        var availableColumns = this._getAvailableColumns(tab);
        var columnsFieldName = 'fields';
        var columnsField = this.getField(columnsFieldName);
        if (columnsField) {
            columnsField.items = availableColumns;
        }
        this.model.set(columnsFieldName, _.keys(availableColumns));
        columnsField.render();
    },

    /**
     * Gets all columns for a list view tab
     *
     * @param {Object} tab
     * @return {Array} A list of columns
     * @private
     */
    _getAvailableColumns: function(tab) {
        var columns = {};
        var module = tab.module;
        if (!module) {
            return columns;
        }

        _.each(this._getFieldMetaForTab(tab), function(field) {
            columns[field.name] = app.lang.get(field.label || field.name, module);
        });

        return columns;
    },

    /**
     * Focus a row in the list
     * @param {string} id The id of the record to focus on
     */
    focusRow: function(id) {
        var $row = this.getRowDomForModelId(id);
        this.highlightRow($row);
        this.makeRowVisible($row);
    },

    /**
     * Highlights a row by making the row blue. Also removes the highlight from
     * any other row.
     * @param {jQuery} $el The element for the row to highlight
     */
    highlightRow: function($el) {
        this.unhighlightRows();
        if ($el.length) {
            $el.addClass('current highlighted');
        }
    },

    /**
     * Un-highlight all currently selected rows.
     */
    unhighlightRows: function() {
        let highlightedRows = this.$('tr.current.highlighted');
        if (highlightedRows.length) {
            highlightedRows.removeClass('current highlighted');
        }
    },

    /**
     * Get the DOM for the row that represents a model.
     * @param {string} id The model id
     * @return {jQuery}
     */
    getRowDomForModelId: function(id) {
        return this.$(`tr[data-id="${id}"]`);
    },

    /**
     * Scroll the list so the selected row is visible
     * @param $selected
     */
    makeRowVisible: function($selected) {
        if (!$selected) {
            this.$el.scrollTop();
            return;
        }

        let rowTop = $selected.position().top;
        let rowHeight = $selected.height();
        let rowBottom = rowTop + rowHeight;
        let dashletTop = this.$el.scrollTop();
        let dashletHeight = this.$el.height();
        let dashletBottom = dashletTop + dashletHeight;

        if (rowBottom >= dashletBottom || rowTop <= dashletTop) {
            this.$el.scrollTop(rowTop);
        }
    },

    /**
     * @inheritdoc
     * Adds logic for handling inline editing on list tab rows
     *
     * @private
     */
    _render: function() {
        this._checkRowEditAccess();
        this._super('_render');
        this.scrollContainer = this.$el.find('.dashablerecord .tab-content');
        this._setRowFields();
        this._toggleRowsByModelId(_.keys(this.toggledListModels), true);
    },
})

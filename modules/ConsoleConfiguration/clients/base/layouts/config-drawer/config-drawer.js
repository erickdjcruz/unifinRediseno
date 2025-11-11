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
 * @class View.Layouts.Base.ConsoleConfigurationConfigDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseConsoleConfigurationConfigDrawerLayout
 * @extends View.Layouts.Base.ConfigDrawerLayout
 */
({
    extendsFrom: 'BaseConfigDrawerLayout',

    plugins: ['ErrorDecoration'],

    /**
     * Holds a list of all modules with multi-line list views that can be
     * configured using the Console Configurator
     */
    supportedModules: ['Accounts', 'Cases', 'Opportunities'],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.setAllowedModules();

        // Unless a console ID was passed in with the context, parse the console
        // information from the parent context it was opened from
        if (!this.context.get('consoleId')) {
            this._parseConsoleContext();
        }
    },

    /**
     * Extracts console information from the parent console context
     */
    _parseConsoleContext: function() {
        var consoleContext = this.context.parent;
        if (consoleContext) {
            this.context.set({
                consoleId: consoleContext.get('modelId'),
                consoleTabs: this._parseConsoleTabs(consoleContext.get('tabs'))
            });
        }
    },

    /**
     * Parses a list of console tabs from the console context to extract the
     * names of the modules used in the console
     * @param tabsArray
     * @return {Array}
     */
    _parseConsoleTabs: function(tabsArray) {
        var modules = [];
        _.each(tabsArray, function(tab) {
            var tabComponents = tab.components || [];
            _.each(tabComponents, function(component) {
                if (component.view === 'multi-line-list' && component.context && component.context.module) {
                    modules.push(component.context.module);
                }
            });
        });
        return modules;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('consoleconfiguration:config:model:add', this.addModelToCollection, this);
        this.context.on('consoleconfiguration:config:model:remove', this.removeModelFromCollection, this);
    },

    /**
     * Returns the list of modules the user has access to
     * and are supported.
     *
     * @return {Array} The list of module names.
     */
    getAvailableModules: function() {
        var moduleNames = app.metadata.getModuleNames();

        // Get the configured list of currently enabled modules for the tab.
        // If there is no setting saved yet for this, use the list of modules
        // parsed from the parent console context
        var selectedModules = this.model.get('enabled_modules')[this.context.get('consoleId')] ||
            this.context.get('consoleTabs');

        return _.filter(selectedModules, function(module) {
            return _.contains(moduleNames, module);
        });
    },

    /**
     * Sets up the models for each of the enabled modules from the configs
     */
    loadData: function(options) {
        if (!this.checkAccess()) {
            this.blockModule();
            return;
        }
        // Get the modules that are currently enabled for the console
        var availableModules = this.getAvailableModules();

        // Get the ID of the console in order to load the settings for the
        // correct console
        var consoleId = this.context.get('consoleId');

        // Load the settings saved for this particular console ID. If no settings
        // are saved yet for this console, create them
        var orderByPrimary = this.model.get('order_by_primary')[consoleId] || {};
        var orderBySecondary = this.model.get('order_by_secondary')[consoleId] || {};
        var filterDef = this.model.get('filter_def')[consoleId] || {};

        _.each(availableModules, function(moduleName) {
            // Parse the sort fields and directions from the 'order by' setting
            var orderByPrimaryComponents = this._parseOrderByComponents(orderByPrimary[moduleName] || '');
            var orderBySecondaryComponents = this._parseOrderByComponents(orderBySecondary[moduleName] || '');

            var data = {
                defaults: this._getModelDefaults(consoleId, moduleName),
                enabled: true,
                enabled_module: moduleName,
                order_by_primary: orderByPrimaryComponents[0] || '',
                order_by_primary_direction: orderByPrimaryComponents[1] || 'asc',
                order_by_secondary: orderBySecondaryComponents[0] || '',
                order_by_secondary_direction: orderBySecondaryComponents[1] || 'asc',
                filter_def: filterDef[moduleName] || [],
            };
            this.addModelToCollection(moduleName, data);
        }, this);
        this.setActiveTabIndex();
    },

    /**
     * Takes a stored order_by value and splits it into field name and direction
     *
     * @param value
     * @return {Array} an array containing the order_by field and direction data
     * @private
     */
    _parseOrderByComponents: function(value) {
        if (_.isString(value)) {
            return value.split(':');
        }
        return [];
    },

    /**
     * Utility function to get an object containing a mapping of
     * {field name} => {default value} for the given console and module tab
     *
     * @param {string} consoleId the ID of the console to grab default settings for
     * @param {string} moduleName the module tab name to grab default settings for
     * @return {Object} containing the mapping, which can be used directly by the
     *                  tab's model.set() function
     * @private
     */
    _getModelDefaults: function(consoleId, moduleName) {
        var config = app.metadata.getModule('ConsoleConfiguration', 'config') || {};
        var defaults = config.defaults || {};
        var defaultAttributes = {};

        _.each(defaults, function(value, key) {
            if (_.isObject(value) && _.isObject(value[consoleId]) && !_.isUndefined(value[consoleId][moduleName])) {
                if (key === 'order_by_primary' || key === 'order_by_secondary') {
                    var orderByComponents = this._parseOrderByComponents(value[consoleId][moduleName]);
                    defaultAttributes[key] = orderByComponents[0] || '';
                    defaultAttributes[key + '_direction'] = orderByComponents[1] || 'desc';
                } else {
                    defaultAttributes[key] = value[consoleId][moduleName];
                }
            }
        }, this);
        return defaultAttributes;
    },

    /**
     * Checks ConsoleConfiguration ACLs to see if the User is a system admin
     * or if the user has a developer role for the ConsoleConfiguration module
     *
     * @inheritdoc
     */
    _checkModuleAccess: function() {
        var acls = app.user.getAcls().ConsoleConfiguration;
        var isSysAdmin = (app.user.get('type') == 'admin');
        var isDev = (!_.has(acls, 'developer'));

        return (isSysAdmin || isDev);
    },

    /**
     * Sets the allowed modules that the admin are allowed to configure
     */
    setAllowedModules: function() {
        var moduleDetails = {};
        var allowedModules = this.supportedModules;
        var modules = {};
        _.each(allowedModules, function(module) {
            moduleDetails = app.metadata.getModule(module);
            if (moduleDetails &&
                !moduleDetails.isBwcEnabled &&
                !_.isEmpty(moduleDetails.fields)) {
                modules[module] = app.lang.getAppListStrings('moduleList')[module];
            }
        });

        this.context.set('allowedModules', modules);
    },

    /**
     * Sets the active tab
     */
    setActiveTabIndex: function() {
        if (this.collection.length >= 1) {
            var activeParentTabIndex = this.context.parent.get('activeTab');

            // get active index based on current tab name selected on the console
            var activeIndex = activeParentTabIndex > 0 ? activeParentTabIndex - 1 : 0;

            this.context.set('activeTabIndex', activeIndex);
        }
    },

    /**
     * Removes a model from the collection and triggers events
     * to re-render the components
     * @param {string} module Module Name
     */
    removeModelFromCollection: function(module) {
        var modelToDelete = _.find(this.collection.models, function(model) {
            return model.get('enabled_module') === module;
        });

        if (!_.isEmpty(modelToDelete)) {
            this.collection.remove(modelToDelete);
            this.setActiveTabIndex();
        }
    },

    /**
     * Adds a model from the collection and triggers events
     * to re-render the components
     * @param {string} module Module Name
     * @param {Object} data Model data to add to the collection
     */
    addModelToCollection: function(module, data) {
        var data = data || {};
        var existingBean = _.find(this.collection.models, function(model) {
            if (_.contains(_.keys(this.context.get('allowedModules')), module)) {
                return model.get('enabled_module') === module;
            }
        }, this);

        if (_.isEmpty(existingBean)) {
            var bean = app.data.createBean(this.module, {
                defaults: data.defaults || {},
                enabled: data.enabled || true,
                enabled_module: data.module || module,
                order_by_primary: data.order_by_primary || '',
                order_by_primary_direction: data.order_by_primary_direction || 'asc',
                order_by_secondary: data.order_by_secondary || '',
                order_by_secondary_direction: data.order_by_secondary_direction || 'asc',
                filter_def: data.filter_def || '',
            });

            this.setTabContent(bean);
            this.setFilterableFields(bean);
            this.addValidationTasks(bean);

            bean.on('change:columns', function() {
                this.setTabContent(bean, true);
                this.setSortValues(bean);
            }, this);

            this.collection.add(bean);
        }

        this.setActiveTabIndex();
    },

    /**
     * Sets the filterable fields
     * @param bean
     */
    setFilterableFields: function(bean) {
        var module = bean.get('enabled_module');
        var filterableFields = app.data.getBeanClass('Filters').prototype.getFilterableFields(module);
        bean.set('filterableFields', filterableFields);
    },

    /**
     * Sets the tab content for the module on the bean
     *
     * @param {Object} bean to edit/add to the collection
     * @param {boolean} update Flag to show if it's the updating of bean
     */
    setTabContent: function(bean, update) {
        update = update || false;

        var tabContent = {};
        var module = bean.get('enabled_module');
        var multiLineFields = update ?
            this.getColumns(bean) :
            this._getMultiLineFields(module);

        // Set the information about the tab's fields, including which fields
        // can be used for sorting
        var fields = {};
        var sortFields = {};
        var nonSortableTypes = ['id', 'relate', 'widget', 'assigned_user_name'];
        _.each(multiLineFields, function(field) {
            if (_.isObject(field) && app.acl.hasAccess('read', module, null, field.name)) {
                // Set the field information
                fields[field.name] = field;

                // Set the sort field information if the field is sortable
                var label = app.lang.get(field.label || field.vname, module);
                var isSortable = !_.isEmpty(label) && field.sortable !== false &&
                    field.sortable !== 'false' && nonSortableTypes.indexOf(field.type) === -1;
                if (isSortable) {
                    sortFields[field.name] = label;
                }
            }
        });
        tabContent.fields = fields;
        tabContent.sortFields = sortFields;

        bean.set('tabContent', tabContent);
        bean.trigger('change:tabContent');
    },

    /**
     * Sets values of the sortable fields
     *
     * @param {Object} bean
     */
    setSortValues: function(bean) {
        const sortValue1 = bean.get('order_by_primary');
        const sortValue2 = bean.get('order_by_secondary');
        const columns = this.getColumns(bean);

        if (sortValue2 && !columns[sortValue2]) {
            bean.set('order_by_secondary', '');
        }

        if (sortValue1 && !columns[sortValue1]) {
            if (sortValue2) {
                bean.set('order_by_primary', sortValue2);
                bean.set('order_by_secondary', '');
            } else {
                bean.set('order_by_primary', '');
            }
        }
    },

    /**
     * Return values of the sortable fields using selected columns and metadata
     *
     * @param {Object} bean
     * @return {Object} a list fields by selected columns
     */
    getColumns: function(bean) {
        const module = bean.get('enabled_module');
        var columns = bean.get('columns');
        var moduleFields = app.metadata.getModule(module, 'fields');

        _.each(columns, function(field, key) {
            // add related_fields from widgets, they should be sortable
            if (!_.isEmpty(field.console) && !_.isEmpty(field.console.related_fields)) {
                var relatedFields = field.console.related_fields;
                _.each(relatedFields, function(field) {
                    if (_.isEmpty(columns[field]) && !_.isEmpty(moduleFields[field])) {
                        columns[field] = moduleFields[field];
                    }
                });
            }
        });

        return columns;
    },

    /**
     * Gets a unique list of the underlying fields contained in a multi-line list
     * @param module
     * @return {Array} a list of field definitions from the multi-line list metadata
     * @private
     */
    _getMultiLineFields: function(module) {
        // Get the unique lists of subfields and related_fields from the multi-line
        // list metadata of the module
        var multiLineMeta = app.metadata.getView(module, 'multi-line-list');
        var moduleFields = app.metadata.getModule(module, 'fields');
        var subfields = [];
        var relatedFields = [];
        _.each(multiLineMeta.panels, function(panel) {
            var panelFields = panel.fields;
            _.each(panelFields, function(fieldDefs) {
                subfields = subfields.concat(fieldDefs.subfields);
                _.each(fieldDefs.subfields, function(subfield) {
                    if (subfield.related_fields) {
                        var related = _.map(subfield.related_fields, function(relatedField) {
                            return moduleFields[relatedField];
                        });
                        relatedFields = relatedFields.concat(related);
                    }
                });
            }, this);
        }, this);

        // To filter out special fields as they should not be available for sorting or filtering.
        subfields = _.filter(subfields, function(field) {
            return _.isEmpty(field.widget_name);
        });

        // Return the combined list of subfields and related fields. Ensure that
        // the correct field type is associated with the field (important for
        // filtering)
        var fields = _.compact(_.uniq(subfields.concat(relatedFields), false, function(field) {
            return field.name;
        }));
        return _.map(fields, function(field) {
            if (moduleFields[field.name]) {
                field.type = moduleFields[field.name].type;
            }
            return field;
        });
    },

    /**
     * Adds validation tasks to the fields in the layout for the enabled modules
     */
    addValidationTasks: function(bean) {
        if (bean !== undefined) {
            bean.addValidationTask('check_order_by_primary', _.bind(this._validatePrimaryOrderBy, bean));
        } else {
            _.each(this.collection.models, function(model) {
                model.addValidationTask('check_order_by_primary', _.bind(this._validatePrimaryOrderBy, model));
            }, this);
        }
    },

    /**
     * Validates table header values for the enabled module
     *
     * @protected
     */
    _validatePrimaryOrderBy: function(fields, errors, callback) {
        if (_.isEmpty(this.get('order_by_primary'))) {
            errors.order_by_primary = errors.order_by_primary || {};
            errors.order_by_primary.required = true;
        }

        callback(null, fields, errors);
    }
})

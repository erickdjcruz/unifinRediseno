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
 * @class View.Fields.Base.FilterField
 * @alias SUGAR.App.view.fields.BaseFilterField
 * @extends View.Fields.Base.BaseField
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.initProperties();
        this.initListeners();
        this.setFilterId(this.getModelFilterId());

        this.model.addValidationTask(this.name + '_name_exists', _.bind(this._validateFilterName, this));
    },

    /**
     * @override
     */
    _render: function() {
        this._super('_render');

        if (this.action == 'edit') {
            this.disposeComponents();
            this.renderComponents();
        }
    },

    /**
     * @inheritdoc
     */
    format: function(value) {
        try {
            value = JSON.parse(value).filterName;
        } catch (error) {
            value = null;
        }
        return value;
    },

    /**
     * Validates filter name
     *
     * @protected
     */
    _validateFilterName: function(fields, errors, callback) {
        if (_.isNull(this.filterpanel)) {
            callback(null, fields, errors);
        }

        var context = this.filterpanel.context;
        var model = context.editingFilter;
        var filterName = '';

        if (model instanceof app.Bean) {
            // If filter name is missing, we manually built it
            if (_.isEmptyValue(model.get('name'))) {
                filterName = this.buildFilterName();
                model.set('name', filterName);
            }

            if (model.get('name') == this.filterName) {
                callback(null, fields, errors);
            } else {
                // Continue with the model validation only once the filter model has been saved
                context.listenTo(context, 'filter:add', function filterAdd() {
                    callback(null, fields, errors);
                });

                // Trigger filter save event so to make sure the new filter name is properly stored
                context.trigger('filter:create:save', filterName);
            }
        } else {
            callback(null, fields, errors);
        }
    },

    /**
     * Builds a filter name in case the record is saved without a filter name
     *
     * @return {string} Filter name
     */
    buildFilterName: function() {
        var calendarName = this.model.get('name');

        return 'Calendar: ' + calendarName;
    },

    /**
     * Initialize properties
     */
    initProperties: function() {
        /**
         * Indicates the default filter id used to initialize filter component
         *
         * @param {string}
         */
        this.filterDefaultId = 'assigned_to_me';

        /**
         * Indicates the current filter id used by the filter component. Default is `All <module_name>`
         *
         * @param {string}
         */
        this.filterId = 'all_records';

        /**
         * Indicates the default filter module used by the filter component
         *
         * @param {string}
         */
        this.filterDefaultModule = this.def.module || this.module;

        /**
         * @param {app.data.beanModel}
         */
        this.filterModel = null;

        /**
         * @param {app.data.beanCollection}
         */
        this.filterCollection = null;

        /**
         * @param {app.Context}
         */
        this.filterContext = null;

        /**
         * Indicates the 'filterpanel' layout component
         *
         * @param {app.view.Layout}
         */
        this.filterpanel = null;

        /**
         * Indicates the 'filter' child layout of the 'filterpanel' layout
         *
         * @param {app.view.Layout}
         */
        this.filter = null;

        /**
         * Indicates the name of the field to retrieve the module
         *
         * This field allows creating the filter layout for a module stored on another field of this model.
         * The parameter will keep the name of the source field
         *
         * @param {string}
         */
        this.filterModuleField = this.def.moduleField;

        /**
         * Indicates the loading state of the related module filters.
         * True if filters are still in loading from database,
         * false if they have already been loaded.
         *
         * @param {boolean}
         */
        this.filterLoadInProgress = false;

        /**
         * Indicates the filter name built based on the filter id
         *
         * @param {string}
         */
        this.filterName = null;
    },

    /**
     * Initialize listeners
     */
    initListeners: function() {
        if (_.isString(this.filterModuleField) && !_.isEmptyValue(this.filterModuleField)) {
            this.listenTo(this.model, 'change:' + this.filterModuleField, _.bind(this.onModuleChange, this));
        }

        this.listenTo(this.model, 'change:' + this.name, _.bind(this.onFieldChange, this));
    },

    /**
     * Initialize filter state
     *
     * @param {string} module
     */
    initFilterState: function(module) {
        var linkName = null;
        var filterId = this.filterDefaultId;

        if (this.filter instanceof app.view.Layout) {
            this.filter.initializeFilterState(module, linkName, filterId);
        }
    },

    /**
     * Handles changing module
     *
     * Reinitializes the filter component when module changes
     * Populates the filter component with the new filters
     * Renders the updated filter component
     *
     * @param {app.data.beanModel} model
     * @param {string} module
     */
    onModuleChange: function(model, module) {
        // Do nothing as long as the dynamic module is the same with the one the filter component is built on
        if (this.filterCollection instanceof app.BeanCollection && module == this.filterCollection.moduleName
        ) {
            return;
        }

        // In case the model is reverting its attributes, make sure we use the previous filter id
        if (model.hasChanged(this.name)) {
            this.setFilterId(this.filterId);
        } else {
            this.setFilterId();
        }

        this.initFilterState(module);
        this.initFilterCollection();
        this.loadComponents();
    },

    /**
     * Handles changing field value
     *
     * Updates the current 'filterId' with what is found on model
     *
     * @param {app.data.beanModel} model
     * @param {string} filter
     */
    onFieldChange: function(model, filter) {
        var filterId;

        try {
            filterId = JSON.parse(filter).filterId;
        } catch (e) {
            filterId = '';
        }

        this.setFilterId(filterId);
    },

    /**
     * Handles changing dropdown filter
     *
     * Sets the current 'filterId' and the filter name
     * Makes sure the model value is also updated
     *
     * @param {string} filterId
     */
    onFilterChange: function(filterId) {
        if (this.view.inlineEditMode || this.view.action == 'edit' || this.view.createMode) {
            this.setFilterId(filterId);
            this.setFilterName();
            this.setModelValue();
        }
    },

    /**
     * Sets model value
     *
     * It can only be a JSON, composed of filter id, filter name and filter definition,
     * or null, if there is no filter selected at this moment.
     */
    setModelValue: function() {
        var value = this.prepareModelValue();
        var options = {
            'silent': true
        };

        this.model.set(this.name, value, options);
    },

    /**
     * Prepares value to be set on model.
     *
     * @return {string|null} JSON if filters have been loaded and can retrieve the current filter id model
     *                       Null if filters collection is still empty (has not been loaded)
     */
    prepareModelValue: function() {
        var model = this.getFilterCollectionModelById();

        if (model instanceof app.data.beanModel) {
            return JSON.stringify({
                'filterId': this.filterId,
                'filterName': this.filterCollection._getTranslatedFilterName(model),
                'filterDef': model.get('filter_definition'),
                'filterTpl': model.get('filter_template')
            });
        }

        return null;
    },

    /**
     * Prepares metadata used to build filterpanel component.
     *
     * @return {Object}
     */
    getFilterMeta: function() {
        return {
            'model': this.initFilterModel(),
            'collection': this.initFilterCollection(),
            'context': this.initFilterContext(),
            'module': this.getFilterModule() || this.filterDefaultModule,
            'layout': this,
            'action': this.action || this.view.action,
            'name': 'filterpanel',
            'meta': {
                'components': [
                    {
                        'layout': {
                            'name': 'filter',
                            'layout': 'filter',
                            'components': [
                                {
                                    'view': 'filter-module-dropdown'
                                },
                                {
                                    'view': 'filter-filter-dropdown'
                                }
                            ]
                        }
                    },
                    {
                        'view': 'filter-rows'
                    },
                    {
                        'view': 'filter-actions'
                    }
                ],
                'filterOptions': {
                    'show_actions': true,
                    'currentFilterId': this.filterId
                }
            }
        };
    },

    /**
     * Initialize the filter model
     *
     * @return {app.data.beanModel}
     */
    initFilterModel: function() {
        var module = this.getFilterModule();

        if (!(this.filterModel instanceof app.data.beanModel) || this.filterModel.module != module) {
            this.filterModel = app.data.createBean(module);

            this.filterModel.set({
                'currentFilterId': this.filterId,
                'filter_id': this.filterId
            });
        }

        return this.filterModel;
    },

    /**
     * Initialize the filter collection
     *
     * @return {app.data.beanCollection}
     */
    initFilterCollection: function() {
        if (!(this.filterCollection instanceof app.data.beanCollection)) {
            this.filterCollection = app.data.createBeanCollection('Filters');
        }

        this.filterCollection.setModuleName(this.getFilterModule() || this.filterDefaultModule);
        this.filterCollection.setOption('limit', -1);

        return this.filterCollection;
    },

    /**
     * Initialize the filter context
     *
     * @return {app.Context}
     */
    initFilterContext: function() {
        var module = this.getFilterModule();

        if (!(this.filterContext instanceof app.Context) || this.filterContext.get('module') != module) {
            var ctxParams = {
                'module': module || this.filterDefaultModule,
                'model': this.initFilterModel(),
                'collection': this.initFilterCollection(),
                'filter_id': this.filterId,
                'currentFilterId': this.filterId,
                'layout': this.view,
                'filterOptions': {
                    // Prevent other similar components from using the same filter id (it is cached in LocalStorage)
                    'stickiness': false,
                    'show_actions': true,
                    'currentFilterId': this.filterId
                }
            };

            this.filterContext = new app.Context(ctxParams);
        }

        return this.filterContext;
    },

    /**
     * Retrieves module corresponding to the filter component.
     *
     * @return {string}
     */
    getFilterModule: function() {
        if (!app.acl.hasAccess('view', this.model.get(this.filterModuleField))) {
            return;
        }

        if (_.isString(this.filterModuleField) && !_.isEmptyValue(this.filterModuleField)) {
            return this.model.get(this.filterModuleField);
        }

        return this.filterDefaultModule;
    },

    /**
     * Retrieves filter id from the model value.
     *
     * @return {string}
     */
    getModelFilterId: function() {
        var filterId;

        if (this.model.has(this.name)) {
            var value = this.model.get(this.name);

            if (!_.isEmpty(value)) {
                try {
                    value = JSON.parse(value);
                } catch (error) {
                    value = {};
                }

                filterId = value.filterId;
            }
        }

        return filterId;
    },

    /**
     * Retrieves child filter component from main filterpanel component.
     *
     * @return {app.view.Layout}
     */
    getFilterpanelFilter: function() {
        var component = this.filterpanel.getComponent('filter');

        if (!(component instanceof app.view.Layout)) {
            component = this.filterpanel.getComponent('filterpanel');

            component = component.getComponent('filter') || component.getComponent('filter-dropdown');
        }

        return component;
    },

    /**
     * Set the filter id
     *
     * @param {string} filterId
     */
    setFilterId: function(filterId) {
        this.filterId = filterId || this.filterDefaultId;
    },

    /**
     * Set the name of the filter
     */
    setFilterName: function() {
        var model = this.getFilterCollectionModelById();

        if (model instanceof app.data.beanModel) {
            this.filterName = this.filterCollection._getTranslatedFilterName(model);
        }
    },

    /**
     * Render components
     */
    renderComponents: function() {
        if (this.filterpanel instanceof app.view.Layout) {
            this.showComponents();
        } else {
            this.loadComponents();
        }
    },

    /**
     * Build components
     */
    buildComponents: function() {
        if (this.filterpanel instanceof app.view.Layout) {
            return;
        }

        this.filterpanel = app.view.createLayout(this.getFilterMeta());

        if (_.isFunction(this.filterpanel.initComponents)) {
            this.filterpanel.initComponents();
        }

        this.filterpanel.trigger('init');
        this.filter = this.getFilterpanelFilter();

        if (this.filter instanceof app.view.Layout) {
            this.listenTo(this.filter, 'filter:change:filter', _.bind(this.onFilterChange, this));
        }
    },

    /**
     * Show components
     */
    showComponents: function() {
        var filterModule = this.getFilterModule();
        this.filterpanel.module = filterModule;
        this.filter.module = filterModule;

        this.filterModel.set('filter_id', this.filterId);
        this.filterpanel.model.set('filter_id', this.filterId);

        this.filterContext.set('filter_id', this.filterId);
        this.filterpanel.context.set('filter_id', this.filterId);

        this.filterContext.set('currentFilterId', this.filterId);
        this.filterpanel.context.set('currentFilterId', this.filterId);

        this.filter.context.set('filter_id', this.filterId);
        this.filter.context.set('currentFilterId', this.filterId);

        this.filterpanel.render();

        this.$('[data-content=wrapper]').append(this.filterpanel.el);
    },

    /**
     * Check if the components can be loaded
     *
     * @return {boolean}
     */
    canLoadComponents: function() {
        var isValidModule = true;

        if (this.def.hasOwnProperty('moduleField') &&
            (
                _.isEmptyValue(this.filterModuleField) ||
                !this.model.has(this.filterModuleField) ||
                _.isEmptyValue(this.model.get(this.filterModuleField))
            )
        ) {
            isValidModule = false;
        }

        return isValidModule && !this.filterLoadInProgress;
    },

    /**
     * Load components
     *
     * Initialize and load filter collection then build and render components
     */
    loadComponents: function() {
        if (!this.canLoadComponents()) {
            return;
        }

        this.filterLoadInProgress = true;
        this.initFilterCollection();

        var options = {
            'success': _.bind(function loadSuccess() {
                this.filterLoadInProgress = false;

                if (this.disposed) {
                    return;
                }

                var filterModel = this.getFilterCollectionModelById();

                if (filterModel instanceof app.data.beanModel) {
                    this.filterCollection.collection.defaultFilterFromMeta = this.filterId;
                }

                this.buildComponents();
                this.renderComponents();

                if (this.action == 'detail') {
                    this.render();
                }

            }, this),
            'error': _.bind(function loadError() {
                this.filterLoadInProgress = false;

                if (this.disposed) {
                    return;
                }

                this.buildComponents();
                this.renderComponents();
            }, this)
        };

        this.filterCollection.load(options);
    },

    /**
     * Retrieves the filter model from filter component's collection
     *
     * @return {app.view.beanModel|null}
     */
    getFilterCollectionModelById: function() {
        var filterModel;

        try {
            filterModel = this.filter.filters.collection.get(this.filterId);
        } catch (e) {
            filterModel = null;
        }

        return filterModel;
    },

    /**
     * Dispose filter component
     */
    disposeComponents: function() {
        if (this.filter instanceof app.view.Layout) {
            this.filter.dispose();
        }

        if (this.filterpanel instanceof app.view.Layout) {
            this.filterpanel.dispose();
        }

        this.filter = null;
        this.filterpanel = null;
        this.filterCollection = null;
        this.filterContext = null;
        this.filterModel = null;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.model.off('change:' + this.name);
        this.disposeComponents();
        return this._super('_dispose');
    },

    /**
     * Fix for current implementation of FocusDrawer where 'checkFocusAvailability'
     * triggers a call to our field.getComponent
     *
     * If it doens't find the method, it throws an error and stops rendering the field
     */
    getComponent: function() {
        return;
    }
});

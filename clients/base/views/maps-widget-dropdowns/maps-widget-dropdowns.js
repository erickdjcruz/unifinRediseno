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
 * Maps widget view
 *
 * @class View.Views.Base.MapsWidgeDropdownsView
 * @alias SUGAR.App.view.views.BaseMapsWidgetDropdownsView
 * @extends View.View
 */
 ({
    /**
     * Event listeners
     */
    events: {
        'change [data-fieldname=modules]': 'moduleChanged',
        'change [data-fieldname=filterBy]': 'filterByChanged',
        'change [data-fieldname=unitType]': 'unitTypeChanged',
        'change [data-fieldname=radius]': 'radiusChanged',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     */
    _initProperties: function() {
        let parentModule = this.context.get('module');

        if (_.has(this.context, 'parent') && !_.isNull(this.context.parent)) {
            parentModule = this.context.parent.get('module');
        }

        this.LOCAL_STORAGE_WIDGET_KEY = `maps_widget_data_${parentModule}`;
        this._filters = {};

        this._updateWidgetData();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._select2 = {};

        _.each(this._dropdowns, function createSelect2(data, key) {
            const queryFunction = `_query${app.utils.capitalize(key)}`;

            let additionalOptions = {
                placeholder: data.placeholder
            };

            if (key === 'radius') {
                additionalOptions.formatSelection = _.bind(this._getSelect2Radius, this);
                additionalOptions.formatResult = _.bind(this._getSelect2Radius, this);
            }

            this.select2(key, queryFunction, additionalOptions);

            if (data.title) {
                let text = key === 'modules' ? data.options[data.title] : data.title;

                this._select2[key].data({
                    id: _.invert(data.options)[data.title],
                    text,
                });
            }
        }, this);
    },

    /**
     * Handle module changes
     *
     * @param {jQuery} e
     */
    moduleChanged: function(e) {
        const selectedModule = e.currentTarget.value;

        this.model.set('modules', selectedModule);
        this.model.set('filterBy', '');

        this._select2.filterBy.val(null);

        this._saveDropdownsState();
        this._updateWidgetData();
    },

    /**
     * Handle filterBy selected
     *
     * @param {jQuery} e
     */
    filterByChanged: function(e) {
        const filterByChanged = e.currentTarget.value;

        this.model.set('filterBy', this._dropdowns.filterBy.options[filterByChanged]);

        this._saveDropdownsState();
        this._updateWidgetData();
    },

    /**
     * Handle unitType selected
     *
     * @param {jQuery} e
     */
    unitTypeChanged: function(e) {
        const unitTypeChanged = e.currentTarget.value;

        this.model.set('unitType', this._dropdowns.unitType.options[unitTypeChanged]);
        this.model.set('radius', '');

        this._select2.radius.val(null);

        this._saveDropdownsState();
        this._updateWidgetData();
    },

    /**
     * Handle radius selected
     *
     * @param {jQuery} e
     */
    radiusChanged: function(e) {
        const radiusChanged = e.currentTarget.value;

        this.model.set('radius', this._dropdowns.radius.options[radiusChanged]);

        this._saveDropdownsState();
        this._updateWidgetData();
    },

    /**
     * Save dropdowns state to user last state
     */
    _saveDropdownsState: function() {
        app.user.lastState.set(this.LOCAL_STORAGE_WIDGET_KEY, {
            modules: this.model.get('modules'),
            filterBy: this.model.get('filterBy'),
            unitType: this.model.get('unitType'),
            radius: this.model.get('radius'),
        });
    },

    /**
     * Update widget data
     */
    _updateWidgetData: function() {
        this._setMapsWidgetData();
        this._dropdowns = this._getDropdownsMeta();

        this._setModuleFilters(this.model.get('modules'));
    },

    /**
     * Build dropdown meta
     *
     * @return {Object}
     */
    _getDropdownsMeta: function() {
        const _availableModules = this._getAvailableModules();
        const _defaultModule = _.chain(_availableModules)
                                .keys()
                                .first()
                                .value();

        if (!this.model.get('modules')) {
            this.model.set('modules', _defaultModule);
        }

        if (!this.model.get('unitType')) {
            this.model.set('unitType', this._getUnitTypesList().miles);
        }

        if (!this.model.get('radius')) {
            this.model.set('radius', '5');
        }

        const modules = this._buildDropdownMeta(
            'modules',
            this.model.get('modules'),
            app.lang.get('LBL_MAP_MODULES'),
            true,
            _availableModules
        );

        const filterBy = this._buildDropdownMeta(
            'filterBy',
            this.model.get('filterBy'),
            app.lang.get('LBL_MAP_FILTER_BY'),
            true,
            {}
        );

        const unitType = this._buildDropdownMeta(
            'unitType',
            this.model.get('unitType'),
            app.lang.get('LBL_MAP_UNIT_TYPE'),
            true,
            this._getUnitTypesList()
        );

        const radius = this._buildDropdownMeta(
            'radius',
            this.model.get('radius'),
            app.lang.get('LBL_MAPS_RADIUS_INPUT'),
            true,
            this._getRadiusList()
        );

        return {
            modules,
            filterBy,
            unitType,
            radius,
        };
    },

    /**
     * Populate the modules list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryModules: function(query) {
        return this._query(query, 'modules');
    },

    /**
     * Populate the filters list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryFilterBy: function(query) {
        return this._query(query, 'filterBy');
    },

    /**
     * Populate the unit type list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryUnitType: function(query) {
        return this._query(query, 'unitType');
    },

    /**
     * Populate the radius list select2 component
     *
     * @param {Object} query
     *
     * @return {Function}
     */
    _queryRadius: function(query) {
        return this._query(query, 'radius');
    },

    /**
     * Generic select2 selection list builder
     *
     * @param {Object} query
     * @param {string} list
     *
     */
    _query: function(query, list) {
        var listElements = this._dropdowns[list].options;
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
        } else {
            listElements = null;
        }

        query.callback(data);
    },

    /**
     * Create generic Select2 options object
     *
     * @return {Object}
     */
    _getSelect2Options: function(additionalOptions) {
        var select2Options = _.extend(
            {
                minimumResultsForSearch: -1,
                dropdownAutoWidth: true,
                width: '150px',
                allowClear: true,
            },
            additionalOptions
        );

        return select2Options;
    },

    /**
     * Create generic Select2 component or return a cached select2 element
     *
     * @param {string} fieldname
     * @param {string} queryFunc
     */
    select2: function(fieldname, queryFunc, additionalOptions) {
        if (this._select2 && this._select2[fieldname]) {
            return this._select2[fieldname];
        };

        this._disposeSelect2(fieldname);

        if (queryFunc && this[queryFunc]) {
            additionalOptions.query = _.bind(this[queryFunc], this);
        }

        var el = this.$('[data-fieldname=' + fieldname + ']')
            .select2(this._getSelect2Options(additionalOptions))
            .data('select2');

        this._select2 = this._select2 || {};
        this._select2[fieldname] = el;

        return el;
    },

    /**
     * Format the select2 element
     *
     * @param {Object} item
     * @return {string}
     */
    _getSelect2Radius: function(item) {
        const unitType = this.model.get('unitType') || app.lang.get('LBL_MAP_UNIT_TYPE_MILES');

        return `${item.text} ${unitType}`;
    },

    /**
     * Set saved data from local storage
     */
    _setMapsWidgetData: function() {
        let widgetData = {
            modules: '',
            filterBy: '',
        };

        const savedData = app.user.lastState.get(this.LOCAL_STORAGE_WIDGET_KEY);

        if (savedData) {
            widgetData = savedData;
        }

        this.model.set(widgetData);
    },

    /**
     * Build dropdown metadata
     *
     * @param {string} id
     * @param {string} title
     * @param {string} placeholder
     * @param {boolean} dynamicTitle
     * @param {Object} options
     *
     * @return {Object}
     */
    _buildDropdownMeta: function(id, title, placeholder, dynamicTitle, options) {
        let meta = {
            id,
            title,
            placeholder,
            dynamicTitle,
            options
        };

        return meta;
    },

    /**
     * Get enabled maps modules
     *
     * @return {Object}
     */
    _getAvailableModules: function() {
        const enabledModulesKey = 'enabled_modules';

        if (!_.has(app.config, 'maps')) {
            return {};
        }

        let availableModules = _.map(app.config.maps[enabledModulesKey], function map(module) {
            let option = {};

            option[module] = app.lang.getModuleName(module, {
                plural: true,
            });

            return option;
        });

        availableModules = app.utils.maps.arrayToObject(availableModules);

        return availableModules;
    },

    /**
     * Get unit types
     *
     * @return {Object}
     */
    _getUnitTypesList: function() {
        return {
            'miles': app.lang.get('LBL_MAP_UNIT_TYPE_MILES'),
            'km': app.lang.get('LBL_MAP_UNIT_TYPE_KM'),
        };
    },

    /**
     * Get unit types
     *
     * @return {Object}
     */
    _getRadiusList: function() {
        return {
            '5': '5',
            '10': '10',
            '25': '25',
            '50': '50',
            '75': '75',
            '100': '100',
            '125': '125',
            '150': '150',
            '200': '200',
            '250': '250',
        };
    },

    /**
     * Set available filters for specified module
     *
     * @param {string} module
     */
    _setModuleFilters: function(module) {
        if (this._filters[module]) {
            this._updateSelect2Filters(this._filters[module]);

            return;
        }

        let filtersCollection = app.data.createBeanCollection('Filters');

        filtersCollection.setModuleName(module);
        filtersCollection.load({
            success: _.bind(function success(filters) {
                this._updateSelect2Filters(filters);

                if (this._select2 && this._dropdowns.filterBy.title) {
                    this._select2.filterBy.data({
                        id: _.invert(this._dropdowns.filterBy.options)[this._dropdowns.filterBy.title],
                        text: this._dropdowns.filterBy.title,
                    });
                }

                this._filters[module] = filters;

                this.layout.trigger('maps:subpanel:filter:collection:fetched');
            }, this),
        });
    },

    /**
     * Update select2 filter options
     *
     * @param {Object} filters
     */
    _updateSelect2Filters: function(filters) {
        let select2Filters = _.map(filters.models, function map(filter) {
            let filterData = {};

            filterData[filter.id] = app.lang.get(filter.get('name'), null, {module: filters.moduleName});

            return filterData;
        });

        select2Filters = app.utils.maps.arrayToObject(select2Filters);

        this._dropdowns.filterBy.options = select2Filters;
    },

    /**
     * Get target module filters
     *
     * @return {Object}
     */
    getAvailableFilters: function() {
        const module = this.model.get('modules');

        return this._filters[module];
    },

    /**
     * Dispose a select2 element
     */
    _disposeSelect2: function(name) {
        if (this._select2 && _.isObject(this._select2)) {
            delete this._select2[name];
        }

        this.$('[data-fieldname=' + name + ']').select2('destroy');
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSelect2('modules');
        this._disposeSelect2('filterBy');
        this._disposeSelect2('unitType');
        this._disposeSelect2('radius');

        this._select2 = {};

        this._super('_dispose');
    },
});

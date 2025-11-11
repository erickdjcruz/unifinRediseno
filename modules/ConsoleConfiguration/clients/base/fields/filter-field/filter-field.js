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
 * @class View.Fields.Base.ConsoleConfiguration.FilterFieldField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationFilterFieldField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click [data-action=add]': 'addRow',
        'click [data-action=remove]': 'removeRow',
        'change [data-filter=field] input[type=hidden]': 'handleFieldSelected',
        'change [data-filter=operator] input[type=hidden]': 'handleOperatorSelected',
    },

    /**
     * Stores the list of filter field options. Defaults for all filter lists
     * can be specified here
     */
    fieldList: {
        $owner: {
            'predefined_filter': true,
            'label': 'LBL_CURRENT_USER_FILTER'
        },
        $favorite: {
            'predefined_filter': true,
            'label': 'LBL_FAVORITES_FILTER'
        }
    },
    filterFields: {},

    /**
     * Stores the mapping of filter operator options
     */
    filterOperators: {},
    _operatorsWithNoValues: [],

    /**
     * Stores the filter definition
     */
    filterDef: [],

    /**
     * Stores the template to render a row of the filter list
     */
    rowTemplate: null,

    /**
     * Map of fields types.
     *
     * Specifies correspondence between field types and field operator types.
     */
    fieldTypeMap: {
        'datetime': 'date',
        'datetimecombo': 'date'
    },

    /**
     * Stores the name of the module this filter refers to
     */
    moduleName: null,

    /**
     * @override
     * @param {Object} opts
     */
    initialize: function(opts) {
        this._super('initialize', [opts]);

        this.moduleName = this.model.get('enabled_module');

        // Store partial template
        this.rowTemplate = app.template.getField('filter-field', 'edit-filter-row', 'ConsoleConfiguration');

        // Store the filter field and operator information for the module
        this.loadFilterOperators(this.model.get('enabled_module'));
        this.loadFilterFields(this.model.get('enabled_module'));

        this.filterDef = this.model.get('filter_def');
    },

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this field is re-rendered
     * when the config is reset
     */
    bindDataChange: function() {
        if (this.model) {
            this.model.on('consoleconfig:reset:default', function() {
                this.render();
            }, this);
        }
    },

    /**
     * Loads the list of filter fields for supplied module.
     *
     * @param {string} module The module to load the filter fields for.
     */
    loadFilterFields: function(module) {
        // Get the set of filterableFields for the tab, and extend it with
        // the default fieldList
        var filterableFields = this.model.get('filterableFields') || {};
        this.fieldList = _.extend({}, this.fieldList, filterableFields);

        // For each field, if it is filterable (or a pre-defined filter), add it
        // to the filterFields list
        this.filterFields = {};
        _.each(this.fieldList, function(fieldDef, fieldName) {
            var label = app.lang.get(fieldDef.label || fieldDef.vname, module);
            var isPredefined = fieldDef.predefined_filter;
            var isFilterable = !_.isEmpty(label) && this.filterOperatorMap[fieldDef.type];
            if (isPredefined || isFilterable) {
                this.filterFields[fieldName] = label;
            }
        }, this);
    },

    /**
     * Loads the list of filter operators for supplied module.
     *
     * @param {string} [module] The module to load the filters for.
     */
    loadFilterOperators: function(module) {
        this.filterOperatorMap = app.metadata.getFilterOperators(module);
        this._operatorsWithNoValues = ['$empty', '$not_empty'];
    },

    /**
     * In edit mode, render filter input fields using the edit-filter-row template.
     * @inheritdoc
     * @private
     */
    _render: function() {
        this._super('_render');
        this.populateFilter(this.model.get('filter_def'));

        // If the filter definition is empty, add a fresh row
        if (this.$('[data-filter=row]').length === 0) {
            this.addRow();
        }
    },

    /**
     * Builds the initial elements of the filter for the given filter definition
     * @param array filterDef the filter definition
     */
    populateFilter: function(filterDef) {
        filterDef = app.data.getBeanClass('Filters').prototype.populateFilterDefinition(filterDef, true);
        _.each(filterDef, function(row) {
            this.populateRow(row);
        }, this);
    },

    /**
     * Populates row fields with the row filter definition.
     *
     * In case it is a template filter that gets populated by values passed in
     * the context/metadata, empty values will be replaced by populated
     * value(s).
     *
     * @param {Object} rowObj The filter definition of a row.
     */
    populateRow: function(rowObj) {
        var moduleMeta = app.metadata.getModule(this.moduleName);
        var fieldMeta = moduleMeta.fields;

        _.each(rowObj, function(value, key) {
            var isPredefinedFilter = (this.fieldList[key] && this.fieldList[key].predefined_filter === true);

            if (key === '$or') {
                var keys = _.reduce(value, function(memo, obj) {
                    return memo.concat(_.keys(obj));
                }, []);

                key = _.find(_.keys(this.fieldList), function(key) {
                    if (_.has(this.fieldList[key], 'dbFields')) {
                        return _.isEqual(this.fieldList[key].dbFields.sort(), keys.sort());
                    }
                }, this);

                // Predicates are identical, so we just use the first.
                value = _.values(value[0])[0];
            } else if (key === '$and') {
                var values = _.reduce(value, function(memo, obj) {
                    return _.extend(memo, obj);
                }, {});
                var def = _.find(this.fieldList, function(fieldDef) {
                    return _.has(values, fieldDef.id_name || fieldDef.name);
                }, this);

                var operator = '$equals';
                key = def ? def.name : key;

                //  We want to get the operator from our values object only for currency fields
                if (def && !_.isString(values[def.name]) && def.type === 'currency') {
                    operator = _.keys(values[def.name])[0];
                    values[key] = values[key][operator];
                }
                value = {};
                value[operator] = values;
            } else if (!fieldMeta[key] && !isPredefinedFilter) {
                return;
            }

            if (!this.fieldList[key]) {
                //Make sure we use name for relate fields
                var relate = _.find(this.fieldList, function(field) { return field.id_name === key; });
                // field not found so don't create row for it.
                if (!relate) {
                    return;
                }
                key = relate.name;
                // for relate fields in version < 7.7 we used `$equals` and `$not_equals` operator so for version
                // compatibility & as per TY-159 needed to fix this since 7.7 & onwards we will be using `$in` &
                // `$not_in` operators for relate fields
                if (_.isString(value) || _.isNumber(value)) {
                    value = {$in: [value]};
                } else if (_.keys(value)[0] === '$not_equals') {
                    var val = _.values([value])[0];
                    value = {$not_in: val};
                }
            }

            if (_.isString(value) || _.isNumber(value)) {
                value = {$equals: value};
            }
            _.each(value, function(value, operator) {
                this.initRow(null, {name: key, operator: operator, value: value});
            }, this);
        }, this);
    },

    /**
     * Add a row
     * @param {Event} e
     * @return {jQuery} $row The added row element.
     */
    addRow: function(e) {
        var $row;

        if (e) {
            // Triggered by clicking the plus sign. Add the row to that point.
            $row = this.$(e.currentTarget).closest('[data-filter=row]');
            $row.after(this.rowTemplate());
            $row = $row.next();
        }

        return this.initRow($row);
    },

    /**
     * Remove a row
     * @param {Event} e
     */
    removeRow: function(e) {
        var $row = this.$(e.currentTarget).closest('[data-filter=row]');
        $row.remove();
        if (this.$('[data-filter=row]').length === 0) {
            this.addRow();
        }

        this.model.set('filter_def', this.buildFilterDef(true), {silent: true});
    },

    /**
     * Initializes a row either with the retrieved field values or the
     * default field values.
     *
     * @param {jQuery} [$row] The related filter row.
     * @param {Object} [data] The values to set in the fields.
     * @return {jQuery} $row The initialized row element.
     */
    initRow: function($row, data) {
        $row = $row || $(this.rowTemplate()).appendTo(this.$el);
        data = data || {};
        var model;
        var field;

        // Init the row with the data available.
        $row.attr('data-name', data.name);
        $row.attr('data-operator', data.operator);
        $row.attr('data-value', data.value);
        $row.data('name', data.name);
        $row.data('operator', data.operator);
        $row.data('value', data.value);

        // Create a blank model for the field selector enum, and set the
        // field value if we know it.
        model = app.data.createBean(this.model.get('enabled_module'));
        if (data.name) {
            model.set('filter_row_name', data.name);
        }

        // Create the field selector enum and add it to the dom
        field = this.createField(model, {
            name: 'filter_row_name',
            type: 'enum',
            options: this.filterFields
        });
        field.render();
        $row.find('[data-filter=field]').append(field.$el);

        // Store the field in the data attributes.
        $row.data('nameField', field);

        // If this selector has a value, init the operator field as well
        if (data.name) {
            this.initOperatorField($row);
        }
        return $row;
    },

    /**
     * Initializes the operator field.
     *
     * @param {jQuery} $row The related filter row.
     */
    initOperatorField: function($row) {
        var $fieldWrapper = $row.find('[data-filter=operator]');
        var data = $row.data();
        var fieldName = data.nameField.model.get('filter_row_name');
        var previousOperator = data.operator;

        // Make sure the data attributes contain the right selected field.
        data.name = fieldName;

        if (!fieldName) {
            return;
        }

        // For relate fields
        data.id_name = this.fieldList[fieldName].id_name;
        // For flex-relate fields
        data.type_name = this.fieldList[fieldName].type_name;

        //Predefined filters don't need operators and value field
        if (this.fieldList[fieldName].predefined_filter === true) {
            data.isPredefinedFilter = true;
            return;
        }

        // Get operators for this filter type
        var fieldType = this.fieldTypeMap[this.fieldList[fieldName].type] || this.fieldList[fieldName].type;
        var payload = {};
        var types = _.keys(this.filterOperatorMap[fieldType]);

        // For parent field with the operator '$equals', the operator field is
        // hidden and we need to display the value field directly. So here we
        // need to assign 'previousOperator' and 'data.operator variables' to let
        // the value field initialize.
        //FIXME: We shouldn't have a condition on the parent field. TY-352 will
        // fix it.
        if (fieldType === 'parent' && _.isEqual(types, ['$equals'])) {
            previousOperator = data.operator = types[0];
        }

        fieldType === 'parent' ?
            $fieldWrapper.addClass('hide').empty() :
            $fieldWrapper.removeClass('hide').empty();
        $row.find('[data-filter=value]').addClass('hide').empty();

        _.each(types, function(operand) {
            payload[operand] = app.lang.get(
                this.filterOperatorMap[fieldType][operand],
                [this.moduleName, 'Filters']
            );
        }, this);

        // Render the operator field
        var model = app.data.createBean(this.moduleName);

        if (previousOperator) {
            model.set('filter_row_operator', data.operator === '$dateRange' ? data.value : data.operator);
        }

        var field = this.createField(model, {
            name: 'filter_row_operator',
            type: 'enum',
            // minimumResultsForSearch set to 9999 to hide the search field,
            // See: https://github.com/ivaynberg/select2/issues/414
            searchBarThreshold: 9999,
            options: payload
        });
        field.render();
        $fieldWrapper.append(field.$el);

        data.operatorField = field;

        var hide = fieldType === 'parent';
        this._hideOperator(hide, $row);

        // We want to go into 'initValueField' only if the field value is known.
        // We need to check 'previousOperator' instead of 'data.operator'
        // because even if the default operator has been set, the field would
        // have set 'data.operator' when it rendered anyway.
        if (previousOperator) {
            this.initValueField($row);
        }
    },

    /**
     * Initializes the value field.
     *
     * @param {jQuery} $row The related filter row.
     */
    initValueField: function($row) {
        var self = this;
        var data = $row.data();
        var operation = data.operatorField.model.get('filter_row_operator');

        // Make sure the data attributes contain the right operator selected.
        data.operator = operation;
        if (!operation) {
            return;
        }

        if (_.contains(this._operatorsWithNoValues, operation)) {
            return;
        }

        // Patching fields metadata
        var moduleName = this.moduleName;
        var module = app.metadata.getModule(moduleName);
        var fields = app.metadata._patchFields(moduleName, module, app.utils.deepCopy(this.fieldList));

        // More patch for some field types
        var fieldName = $row.find('[data-filter=field] input[type=hidden]').select2('val');
        var fieldType = this.fieldTypeMap[this.fieldList[fieldName].type] || this.fieldList[fieldName].type;
        var fieldDef = fields[fieldName];

        switch (fieldType) {
            case 'enum':
                fieldDef.isMultiSelect = this.isCollectiveValue($row);
                // Set minimumResultsForSearch to a negative value to hide the search field,
                // See: https://github.com/ivaynberg/select2/issues/489#issuecomment-13535459
                fieldDef.searchBarThreshold = -1;
                break;
            case 'bool':
                fieldDef.type = 'enum';
                fieldDef.options = fieldDef.options || 'filter_checkbox_dom';
                break;
            case 'int':
                fieldDef.auto_increment = false;
                //For $in operator, we need to convert `['1','20','35']` to `1,20,35` to make it work in a varchar field
                if (operation === '$in') {
                    fieldDef.type = 'varchar';
                    fieldDef.len = 200;
                    if (_.isArray($row.data('value'))) {
                        $row.attr('data-value', $row.data('value').join(','));
                    }
                }
                break;
            case 'teamset':
                fieldDef.type = 'relate';
                fieldDef.isMultiSelect = this.isCollectiveValue($row);
                break;
            case 'datetimecombo':
            case 'date':
                fieldDef.type = 'date';
                //Flag to indicate the value needs to be formatted correctly
                data.isDate = true;
                if (operation.charAt(0) !== '$') {
                    //Flag to indicate we need to build the date filter definition based on the date operator
                    data.isDateRange = true;
                    return;
                }
                break;
            case 'relate':
                fieldDef.auto_populate = true;
                fieldDef.isMultiSelect = this.isCollectiveValue($row);
                break;
            case 'parent':
                data.isFlexRelate = true;
                break;
        }
        fieldDef.required = false;
        fieldDef.readonly = false;

        // Create new model with the value set
        var model = app.data.createBean(moduleName);

        var $fieldValue = $row.find('[data-filter=value]');
        $fieldValue.removeClass('hide').empty();

        // Add the field type as an attribute on the HTML element so that it
        // can be used as a CSS selector.
        $fieldValue.attr('data-type', fieldType);

        //fire the change event as soon as the user start typing
        var _keyUpCallback = function(e) {
            if ($(e.currentTarget).is('.select2-input')) {
                return; //Skip select2. Select2 triggers other events.
            }
            this.value = $(e.currentTarget).val();
            // We use "silent" update because we don't need re-render the field.
            model.set(this.name, this.unformat($(e.currentTarget).val()), {silent: true});
            model.trigger('change');
        };

        //If the operation is $between we need to set two inputs.
        if (operation === '$between' || operation === '$dateBetween') {
            var minmax = [];
            var value = $row.data('value') || [];
            if (fieldType === 'currency' && $row.data('value')) {
                value = $row.data('value') || {};
                model.set(value);
                value = value[fieldName] || [];
                // FIXME: Change currency.js to retrieve correct unit for currency filters (see TY-156).
                model.set('id', 'not_new');
            }

            model.set(fieldName + '_min', value[0] || '');
            model.set(fieldName + '_max', value[1] || '');
            minmax.push(this.createField(model, _.extend({}, fieldDef, {name: fieldName + '_min'})));
            minmax.push(this.createField(model, _.extend({}, fieldDef, {name: fieldName + '_max'})));

            if (operation === '$dateBetween') {
                minmax[0].label = app.lang.get('LBL_FILTER_DATEBETWEEN_FROM');
                minmax[1].label = app.lang.get('LBL_FILTER_DATEBETWEEN_TO');
            } else {
                minmax[0].label = app.lang.get('LBL_FILTER_BETWEEN_FROM');
                minmax[1].label = app.lang.get('LBL_FILTER_BETWEEN_TO');
            }

            data.valueField = minmax;

            _.each(minmax, function(field) {
                $fieldValue.append(field.$el);
                this.listenTo(field, 'render', function() {
                    field.$('input, select, textarea').addClass('inherit-width');
                    field.$('.input-append').prepend('<span class="add-on">' + field.label + '</span>')
                        .addClass('input-prepend')
                        .removeClass('date'); // .date makes .inherit-width on input have no effect
                    field.$('input, textarea').on('keyup', _.debounce(_.bind(_keyUpCallback, field), 400));
                });
                field.render();
            }, this);
        } else if (data.isFlexRelate) {
            var values = {};
            _.each($row.data('value'), function(value, key) {
                values[key] = value;
            }, this);
            model.set(values);

            var field = this.createField(model, _.extend({}, fieldDef, {name: fieldName}));
            findRelatedName = app.data.createBeanCollection(model.get('parent_type'));
            data.valueField = field;
            $fieldValue.append(field.$el);

            if (model.get('parent_id')) {
                findRelatedName.fetch({
                    params: {filter: [{'id': model.get('parent_id')}]},
                    complete: _.bind(function() {
                        if (!this.disposed) {
                            if (findRelatedName.first()) {
                                model.set(fieldName,
                                    findRelatedName.first().get(field.getRelatedModuleField()),
                                    {silent: true});
                            }
                            if (!field.disposed) {
                                field.render();
                            }
                        }
                    }, this)
                });
            } else {
                field.render();
            }
        } else {
            // value is either an empty object OR an object containing `currency_id` and currency amount
            if (fieldType === 'currency' && $row.data('value')) {
                // for stickiness & to retrieve correct saved values, we need to set the model with data.value object
                model.set($row.data('value'));
                // FIXME: Change currency.js to retrieve correct unit for currency filters (see TY-156).
                // Mark this one as not_new so that model isn't treated as new
                model.set('id', 'not_new');
            } else {
                model.set(fieldDef.id_name || fieldName, $row.data('value'));
            }
            // Render the value field
            var field = this.createField(model, _.extend({}, fieldDef, {name: fieldName}));
            $fieldValue.append(field.$el);

            data.valueField = field;

            this.listenTo(field, 'render', function() {
                field.$('input, select, textarea').addClass('inherit-width');
                // .date makes .inherit-width on input have no effect so we need to remove it.
                field.$('.input-append').removeClass('date');
                field.$('input, textarea').on('keyup',_.debounce(_.bind(_keyUpCallback, field), 400));
            });
            if ((fieldDef.type === 'relate' || fieldDef.type === 'nestedset') &&
                !_.isEmpty($row.data('value'))
            ) {
                var findRelatedName = app.data.createBeanCollection(fieldDef.module);
                var relateOperator = this.isCollectiveValue($row) ? '$in' : '$equals';
                var relateFilter = [{id: {}}];
                relateFilter[0].id[relateOperator] = $row.data('value');
                findRelatedName.fetch({fields: [fieldDef.rname], params: {filter: relateFilter},
                    complete: function() {
                        if (!self.disposed) {
                            if (findRelatedName.length > 0) {
                                model.set(fieldDef.id_name, findRelatedName.pluck('id'), {silent: true});
                                model.set(fieldName, findRelatedName.pluck(fieldDef.rname), {silent: true});
                            }
                            if (!field.disposed) {
                                field.render();
                            }
                        }
                    }
                });
            } else {
                field.render();
            }
        }

        // When the value changes, update the filter value
        var updateFilter = function() {
            self._updateFilterData($row);
            self.model.set('filter_def', self.buildFilterDef(true), {silent: true});
        };
        this.listenTo(model, 'change', updateFilter);
        this.listenTo(model, 'change:' + fieldName, updateFilter);

        // Manually trigger the filter request if a value has been selected lately
        // This is the case for checkbox fields or enum fields that don't have empty values.
        var modelValue = model.get(fieldDef.id_name || fieldName);

        // To handle case: value is an object with 'currency_id' = 'xyz' and 'likely_case' = ''
        // For currency fields, when value becomes an object, trigger change
        if (!_.isEmpty(modelValue) && modelValue !== $row.data('value')) {
            model.trigger('change');
        }
    },

    /**
     * Check if the selected filter operator is a collective type.
     *
     * @param {jQuery} $row The related filter row.
     */
    isCollectiveValue: function($row) {
        return $row.data('operator') === '$in' || $row.data('operator') === '$not_in';
    },

    /**
     * Update filter data for this row
     * @param $row Row to update
     * @private
     */
    _updateFilterData: function($row) {
        var data = $row.data();
        var field = data.valueField;
        var name = data.name;
        var valueForFilter;

        //Make sure we use ID for relate fields
        if (this.fieldList[name] && this.fieldList[name].id_name) {
            name = this.fieldList[name].id_name;
        }

        //If we have multiple fields we have to build an array of values
        if (_.isArray(field)) {
            valueForFilter = [];
            _.each(field, function(field) {
                var value = !field.disposed && field.model.has(field.name) ? field.model.get(field.name) : '';
                value = $row.data('isDate') ? (app.date.stripIsoTimeDelimterAndTZ(value) || '') : value;
                valueForFilter.push(value);
            });
        } else {
            var value = !field.disposed && field.model.has(name) ? field.model.get(name) : '';
            valueForFilter = $row.data('isDate') ? (app.date.stripIsoTimeDelimterAndTZ(value) || '') : value;
        }

        // Update filter value once we've calculated final value
        $row.data('value', valueForFilter);
        $row.attr('data-value', valueForFilter);
    },

    /**
     * Shows or hides the operator field of the filter row specified.
     *
     * Automatically populates the operator field to have value `$equals` if it
     * is not in midst of populating the row.
     *
     * @param {boolean} hide Set to `true` to hide the operator field.
     * @param {jQuery} $row The filter row of interest.
     * @private
     */
    _hideOperator: function(hide, $row) {
        $row.find('[data-filter=value]')
            .toggleClass('span4', !hide)
            .toggleClass('span8', hide);
    },

    /**
     * Utility function that instantiates a field for this form.
     *
     * The field action is manually set to `detail` because we want to render
     * the `edit` template but the action remains `detail` (filtering).
     *
     * @param {Data.Bean} model A bean necessary to the field for storing the
     *   value(s).
     * @param {Object} def The field definition.
     * @return {View.Field} The field component.
     */
    createField: function(model, def) {
        var obj = {
            def: def,
            view: this.view,
            nested: true,
            viewName: 'edit',
            model: model
        };

        var field = app.view.createField(obj);
        return field;
    },

    /**
     * Fired when a user selects a field to filter by
     * @param {Event} e
     */
    handleFieldSelected: function(e) {
        var $el = this.$(e.currentTarget);
        var $row = $el.parents('[data-filter=row]');
        var fieldOpts = [
            {field: 'operatorField', value: 'operator'},
            {field: 'valueField', value: 'value'}
        ];
        this._disposeRowFields($row, fieldOpts);
        this.initOperatorField($row);

        // Update the attributes of the row
        $row.attr('data-name', $el.val());
        $row.attr('data-operator', '');
        $row.attr('data-value', '');

        this.model.set('filter_def', this.buildFilterDef(true), {silent: true});
    },

    /**
     * Fired when a user selects an operator to filter by
     * @param {Event} e
     */
    handleOperatorSelected: function(e) {
        var $el = this.$(e.currentTarget);
        var $row = $el.parents('[data-filter=row]');
        var fieldOpts = [
            {'field': 'valueField', 'value': 'value'}
        ];
        this._disposeRowFields($row, fieldOpts);
        this.initValueField($row);

        // Update the attributes of the row
        $row.attr('data-operator', $el.val());
        $row.attr('data-value', '');

        this.model.set('filter_def', this.buildFilterDef(true), {silent: true});
    },

    /**
     * Disposes fields stored in the data attributes of the row element.
     *
     *     @example of an `opts` object param:
     *      [
     *       {field: 'nameField', value: 'name'},
     *       {field: 'operatorField', value: 'operator'},
     *       {field: 'valueField', value: 'value'}
     *      ]
     *
     * @param  {jQuery} $row The row which fields are to be disposed.
     * @param  {Array} opts An array of objects containing the field object and
     *  value to the data attributes of the row.
     */
    _disposeRowFields: function($row, opts) {
        var data = $row.data();
        var model;

        if (_.isObject(data) && _.isArray(opts)) {
            _.each(opts, function(val) {
                if (data[val.field]) {
                    //For in between filter we have an array of fields so we need to cover all cases
                    var fields = _.isArray(data[val.field]) ? data[val.field] : [data[val.field]];
                    data[val.value] = '';
                    _.each(fields, function(field) {
                        model = field.model;
                        if (val.field === 'valueField' && model) {
                            model.clear({silent: true});
                            this.stopListening(model);
                        }
                        field.dispose();
                        field = null;
                    }, this);
                    return;
                }
                if (data.isDateRange && val.value === 'value') {
                    data.value = '';
                }
            }, this);
        }
        //Reset flags
        data.isDate = false;
        data.isDateRange = false;
        data.isPredefinedFilter = false;
        data.isFlexRelate = false;
        $row.data(data);
    },

    /**
     * Build filter definition for all rows.
     *
     * @param {boolean} onlyValidRows Set `true` to retrieve only filter
     *   definition of valid rows, `false` to retrieve the entire field
     *   template.
     * @return {Array} Filter definition.
     */
    buildFilterDef: function(onlyValidRows) {
        var $rows = this.$('[data-filter=row]');
        var filter = [];

        _.each($rows, function(row) {
            var rowFilter = this.buildRowFilterDef($(row), onlyValidRows);

            if (rowFilter) {
                filter.push(rowFilter);
            }
        }, this);

        return filter;
    },

    /**
     * Build filter definition for this row.
     *
     * @param {jQuery} $row The related row.
     * @param {boolean} onlyIfValid Set `true` to validate the row and return
     *   `undefined` if not valid, or `false` to build the definition anyway.
     * @return {Object} Filter definition for this row.
     */
    buildRowFilterDef: function($row, onlyIfValid) {
        var data = $row.data();
        if (onlyIfValid && !this.validateRow($row)) {
            return;
        }
        var operator = data.operator;
        var value = data.value || '';
        var name = data.id_name || data.name;
        var filter = {};

        if (_.isEmpty(name)) {
            return;
        }

        if (data.isPredefinedFilter || !this.fieldList) {
            filter[name] = '';
            return filter;
        } else {
            if (!_.isEmpty(data.valueField) && _.isFunction(data.valueField.delegateBuildFilterDefinition)) {
                filter[name] = {};
                filter[name][operator] = data.valueField.delegateBuildFilterDefinition();
            } else if (this.fieldList[name] && _.has(this.fieldList[name], 'dbFields')) {
                var subfilters = [];
                _.each(this.fieldList[name].dbFields, function(dbField) {
                    var filter = {};
                    filter[dbField] = {};
                    filter[dbField][operator] = value;
                    subfilters.push(filter);
                });
                filter.$or = subfilters;
            } else {
                if (data.isFlexRelate) {
                    var valueField = data.valueField;
                    var idFilter = {};
                    var typeFilter = {};

                    idFilter[data.id_name] = valueField.model.get(data.id_name);
                    typeFilter[data.type_name] = valueField.model.get(data.type_name);
                    filter.$and = [idFilter, typeFilter];
                    // Creating currency filter. For all but `$between` operators we use
                    // type property from data.valueField. For `$between`, data.valueField
                    // is an array and therefore we check for type==='currency' from
                    // either of the elements.
                } else if (data.valueField && (data.valueField.type === 'currency' ||
                    (_.isArray(data.valueField) && data.valueField[0].type === 'currency'))
                ) {
                    // initially value is an array which we later convert into an object for saving and retrieving
                    // purposes (stickiness structure constraints)
                    var amountValue;
                    if (_.isObject(value) && !_.isUndefined(value[name])) {
                        amountValue = value[name];
                    } else {
                        amountValue = value;
                    }

                    var amountFilter = {};
                    amountFilter[name] = {};
                    amountFilter[name][operator] = amountValue;

                    // for `$between`, we use first element to get dataField ('currency_id') since it is same
                    // for both elements and also because data.valueField is an array
                    var dataField;
                    if (_.isArray(data.valueField)) {
                        dataField = data.valueField[0];
                    } else {
                        dataField = data.valueField;
                    }

                    var currencyId;
                    currencyId = dataField.getCurrencyField().name;

                    var currencyFilter = {};
                    currencyFilter[currencyId] = dataField.model.get(currencyId);

                    filter.$and = [amountFilter, currencyFilter];
                } else if (data.isDateRange) {
                    //Once here the value is actually a key of date_range_selector_dom and we need to build a real
                    //filter definition on it.
                    filter[name] = {};
                    filter[name].$dateRange = operator;
                } else if (operator === '$in' || operator === '$not_in') {
                    // IN/NOT IN require an array
                    filter[name] = {};
                    //If value is not an array, we split the string by commas to make it an array of values
                    if (_.isArray(value)) {
                        filter[name][operator] = value;
                    } else if (!_.isEmpty(value)) {
                        filter[name][operator] = (value + '').split(',');
                    } else {
                        filter[name][operator] = [];
                    }
                } else {
                    filter[name] = {};
                    filter[name][operator] = value;
                }
            }

            return filter;
        }
    },

    /**
     * Verify the value of the row is not empty.
     *
     * @param {Element} $row The row to validate.
     * @return {boolean} `true` if valid, `false` otherwise.
     */
    validateRow: function(row) {
        var $row = $(row);
        var data = $row.data();

        if (_.contains(this._operatorsWithNoValues, data.operator)) {
            return true;
        }

        // for empty value in currency we dont want to validate
        if (!_.isUndefined(data.valueField) && !_.isArray(data.valueField) && data.valueField.type === 'currency' &&
            (_.isEmpty(data.value) || (_.isObject(data.value) &&
                _.isEmpty(data.valueField.model.get(data.name))))) {
            return false;
        }

        //For date range and predefined filters there is no value
        if (data.isDateRange || data.isPredefinedFilter) {
            return true;
        } else if (data.isFlexRelate) {
            return data.value ?
                _.reduce(data.value, function(memo, val) {
                    return memo && !_.isEmpty(val);
                }, true) :
                false;
        }

        //Special case for between operators where 2 values are needed
        if (_.contains(['$between', '$dateBetween'], data.operator)) {

            if (!_.isArray(data.value) || data.value.length !== 2) {
                return false;
            }

            switch (data.operator) {
                case '$between':
                    // FIXME: the fields should set a true number (see SC-3138).
                    return !(_.isNaN(parseFloat(data.value[0])) || _.isNaN(parseFloat(data.value[1])));
                case '$dateBetween':
                    return !_.isEmpty(data.value[0]) && !_.isEmpty(data.value[1]);
                default:
                    return false;
            }
        }

        return _.isNumber(data.value) || !_.isEmpty(data.value);
    },
})

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
 * @class View.Views.Base.CJFieldTriggerFilterRowsView
 * @alias SUGAR.App.view.views.BaseCJFieldTriggerFilterRowsView
 * @extends View.View.Base.FilterRows
 */
({
    extendsFrom: 'FilterRowsView',
    viewCurrentAction: '',

    /**
     * @inheritdoc
     *
     */
    initialize: function(opts) {
        this._super('initialize', [opts]);
        this.formRowTemplate = app.template.get(this.name + '.filter-row-partial');
    },

    /**
     * @inheritdoc
     *
     */
    render: function() {
        if (_.isEqual(this.getViewCurrentAction(), 'detail')) {
            this.formRowTemplate = app.template.get(this.name + '.filter-row-partial-detail');
        }
        this._super('render');
    },

    /**
     * Provide the placeholder label for filter field, operator, and value
     *
     * @param {string} fieldName
     * @param {string} type
     * @return {string}
     */
    getPlaceHolderLabel: function(fieldName, type) {
        let label = _.isEqual(type, 'enum') ? 'LBL_CJ_SELECT_VALUE' : 'LBL_CJ_ENTER_VALUE';

        switch (fieldName) {
            case 'filter_row_name':
                label = 'LBL_CJ_SELECT_FIELD';
                break;
            case 'filter_row_operator':
                label = 'LBL_CJ_SELECT_OPERATOR';
                break;
        }

        return label;
    },

    /**
     * @inheritdoc
     *
     */
    createField: function(model, def) {
        if (def && def.name) {
            def.placeholder = this.getPlaceHolderLabel(def.name, def.type);
        }

        let viewName = _.isEqual(this.getViewCurrentAction(), 'detail') ? 'detail' : 'edit';
        let obj = {
            meta: {
                view: viewName
            },
            def: def,
            model: model,
            context: app.controller.context,
            viewName: viewName,
            view: this
        };
        let field = app.view.createField(obj);
        field.action = 'detail';
        return field;
    },

    /**
     * Return the main view action
     *
     * @return {string}
     */
    getViewCurrentAction: function() {
        if (this.layout && this.layout.options) {
            this.viewCurrentAction = this.layout.options.action;
        }

        // for preview view set action to detail
        this.viewCurrentAction = _.isEqual(this.viewCurrentAction, 'preview') ? 'detail' : this.viewCurrentAction;

        return this.viewCurrentAction;
    },

    /**
     * @inheritdoc
     *
     */
    initValueField: function($row) {
        let self = this;
        let data = $row.data();
        let operation = data.operatorField.model.get('filter_row_operator');

        // We have always listened to model changes. More recently, we are
        // listening to attribute changes because collection fields only
        // trigger attribute change events. We don't want to fire a search
        // when both the model and attribute change events occur, hence the
        // debounce.
        let search = null;
        if (!_.isEqual(this.getViewCurrentAction(), 'detail')) {
            search = _.debounce(function() {
                self._updateFilterData($row);
                self.fireSearch();
            }, 200);
        }

        // Make sure the data attributes contain the right operator selected.
        data.operator = operation;
        if (!operation) {
            return;
        }

        if (_.contains(this._operatorsWithNoValues, operation)) {
            this.fireSearch();
            return;
        }

        // Patching fields metadata
        let moduleName = this.moduleName;
        let module = app.metadata.getModule(moduleName);
        let fields = app.metadata._patchFields(moduleName, module, app.utils.deepCopy(this.fieldList));

        // More patch for some field types
        let fieldName =  data.name || $row.find('[data-filter=field] input[type=hidden]').select2('val');
        let fieldType = this.fieldTypeMap[this.fieldList[fieldName].type] || this.fieldList[fieldName].type;
        let fieldDef = fields[fieldName];

        this.modifyFieldDefs(
            $row,
            fieldDef,
            fieldType,
            operation,
            data
        );

        /**
         * modifyFieldDefs will set the flag isDateRange to true if we don't
         * need to build date filter definition based on the date operator
         */
        if (data.isDateRange) {
            return;
        }

        // Create new model with the value set
        let model = app.data.createBean(moduleName);

        let $fieldValue = $row.find('[data-filter=value]');
        $fieldValue.removeClass('hide').empty();

        // Add the field type as an attribute on the HTML element so that it
        // can be used as a CSS selector.
        $fieldValue.attr('data-type', fieldType);

        //fire the change event as soon as the user start typing
        let _keyUpCallback = function(e) {
            if ($(e.currentTarget).is('.select2-input')) {
                return; //Skip select2. Select2 triggers other events.
            }
            this.value = $(e.currentTarget).val();
            // We use "silent" update because we don't need re-render the field.
            model.set(this.name, this.unformat($(e.currentTarget).val()), {silent: true});
            model.trigger('change');
        };

        this.processValueField(
            $row,
            fieldDef,
            fieldType,
            operation,
            data,
            search,
            model,
            $fieldValue,
            fieldName,
            _keyUpCallback
        );
    },

    /**
    * Process the Value field against
    * operators and field types
    */
    processValueField: function(
        $row,
        fieldDef,
        fieldType,
        operation,
        data,
        search,
        model,
        $fieldValue,
        fieldName,
        _keyUpCallback
    ) {
        //If the operation is $between we need to set two inputs.
        if (operation === '$between' || operation === '$dateBetween') {
            this.processBetweenOperators(
                $row,
                fieldDef,
                fieldType,
                operation,
                data,
                model,
                $fieldValue,
                fieldName,
                _keyUpCallback
            );
        } else if (data.isFlexRelate) {
            this.processFlexRelateData(
                $row,
                fieldDef,
                model,
                $fieldValue,
                fieldName
            );
        } else {
            this.processOtherData(
                $row,
                fieldDef,
                fieldType,
                data,
                model,
                $fieldValue,
                fieldName,
                _keyUpCallback
            );
        }

        // When the value change a quicksearch should be fired to update the results

        if (_.isFunction(search)) {
            this.listenTo(model, 'change', search);
            this.listenTo(model, 'change:' + fieldName, search);
        }

        // Manually trigger the filter request if a value has been selected lately
        // This is the case for checkbox fields or enum fields that don't have empty values.
        let modelValue = model.get(fieldDef.id_name || fieldName);

        // To handle case: value is an object with 'currency_id' = 'xyz' and 'likely_case' = ''
        // For currency fields, when value becomes an object, trigger change
        if (!_.isEmpty(modelValue) && modelValue !== $row.data('value')) {
            model.trigger('change');
        }
    },

    /**
    * Process remaining data and operators
    * i.e relate, currency etc fields
    */
    processOtherData: function(
        $row,
        fieldDef,
        fieldType,
        data,
        model,
        $fieldValue,
        fieldName,
        _keyUpCallback
    ) {
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
        let field = this.createField(model, _.extend({}, fieldDef, {name: fieldName}));
        let fieldContainer = $(field.getPlaceholder().string);
        $fieldValue.append(fieldContainer);
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
            let findRelatedName = app.data.createBeanCollection(fieldDef.module);
            let relateOperator = this.isCollectiveValue($row) ? '$in' : '$equals';
            let relateFilter = [{id: {}}];
            relateFilter[0].id[relateOperator] = $row.data('value');
            findRelatedName.fetch({fields: [fieldDef.rname], params: {filter: relateFilter},
                complete: _.bind(function() {
                    if (!this.disposed) {
                        if (findRelatedName.length > 0) {
                            model.set(fieldDef.id_name, findRelatedName.pluck('id'), {silent: true});
                            model.set(fieldName, findRelatedName.pluck(fieldDef.rname), {silent: true});
                        }
                        if (!field.disposed) {
                            this._renderField(field, fieldContainer);
                        }
                    }
                }, this)
            });
        } else {
            this._renderField(field, fieldContainer);
        }
    },

    /**
    * Process the Flex relate fields data
    */
    processFlexRelateData: function(
        $row,
        fieldDef,
        model,
        $fieldValue,
        fieldName
    ) {
        _.each($row.data('value'), function(value, key) {
            model.set(key, value);
        }, this);

        let field = this.createField(model, _.extend({}, fieldDef, {name: fieldName}));
        let fieldContainer = $(field.getPlaceholder().string);
        let findRelatedName = app.data.createBeanCollection(model.get('parent_type'));
        data.valueField = field;
        $fieldValue.append(fieldContainer);

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
                            this._renderField(field, fieldContainer);
                        }
                    }
                }, this)
            });
        } else {
            this._renderField(field, fieldContainer);
        }
    },

    /**
     * Process the between filters logic
     */
    processBetweenOperators: function(
        $row,
        fieldDef,
        fieldType,
        operation,
        data,
        model,
        $fieldValue, fieldName, _keyUpCallback
    ) {
        let minmax = [];
        let value = $row.data('value') || [];
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
            let fieldContainer = $(field.getPlaceholder().string);
            $fieldValue.append(fieldContainer);
            this.listenTo(field, 'render', function() {
                field.$('input, select, textarea').addClass('inherit-width');
                field.$('.input-append').prepend('<span class="add-on">' + field.label + '</span>');
                field.$('.input-append').addClass('input-prepend');
                // .date makes .inherit-width on input have no effect so we need to remove it.
                field.$('.input-append').removeClass('date');
                field.$('input, textarea').on('keyup', _.debounce(_.bind(_keyUpCallback, field), 400));
            });
            this._renderField(field, fieldContainer);
        }, this);
    },

    /**
     * Modify fields further on the
     * base of orignal field type
     *
     */
    modifyFieldDefs: function(
        $row,
        fieldDef,
        fieldType,
        operation,
        data
    ) {
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
                        $row.data('value', $row.data('value').join(','));
                    }
                }
                break;
            case 'teamset':
                fieldDef.type = 'relate';
                fieldDef.isMultiSelect = this.isCollectiveValue($row);
                break;
            case 'datetimecombo':
            case 'date':
                fieldDef.type = (_.includes(this.amountDaysOperators, operation)) ? 'text' : 'date';
                //Flag to indicate the value needs to be formatted correctly
                data.isDate = true;
                if (operation.charAt(0) !== '$') {
                    //Flag to indicate we need to build the date filter definition based on the date operator
                    data.isDateRange = true;
                    this.fireSearch();
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
    },

    /**
     * @inheritdoc
     *
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
})

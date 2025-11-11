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
 * @class View.Fields.Base.ConsoleConfiguration.DirectionsField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationDirectionsField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click .restore-defaults-btn': 'restoreClicked'
    },

    /**
     * Stores the default attributes for the model
     */
    defaults: {},

    /**
     * Stores a mapping of {value} => {label} used for sort direction fields
     */
    sortDirectionLabels: {
        desc: 'LBL_CONSOLE_DIRECTIONS_DESCENDING',
        asc: 'LBL_CONSOLE_DIRECTIONS_ASCENDING'
    },

    /**
     * These store the template strings representing the default field values
     */
    primarySortName: '',
    primarySortDirection: '',
    secondarySortName: '',
    secondarySortDirection: '',
    filterString: '',

    /**
     * Link to detailed instructions
     */
    detailedInstructionsLink: '',

    /**
     * @inheritdoc
     *
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._initDefaults();
    },

    /**
     * Initializes the template strings that represent the tab model's default
     * values for the fields on the console configuration view
     *
     * @private
     */
    _initDefaults: function() {
        this.defaults = this.model.get('defaults') || {};

        // Build detailedInstructionsLink
        var serverInfo = app.metadata.getServerInfo();
        this.detailedInstructionsLink = 'https://www.sugarcrm.com/crm/product_doc.php?edition=' +
            serverInfo.flavor + '&version=' + serverInfo.version + '&lang=' + app.lang.getLanguage() +
            '&module=ConsoleManagement';

        let products = app.user.getProductCodes();
        this.detailedInstructionsLink += products ?
            '&products=' + encodeURIComponent(products.join(',')) :
            '';

        // Get the tabContent attribute, which includes a mapping of
        // {sort field value} => {sort field label}
        var tabContent = this.model.get('tabContent');
        var sortFields = tabContent.sortFields || {};

        // Initialize the primary sort default template strings
        this.primarySortName = sortFields[this.defaults.order_by_primary] || '';
        var sortDirection = this.defaults.order_by_primary_direction || 'asc';
        this.primarySortDirection = app.lang.get(this.sortDirectionLabels[sortDirection], this.module);

        // Initialize the secondary sort default template strings
        this.secondarySortName = sortFields[this.defaults.order_by_secondary];
        sortDirection = this.defaults.order_by_secondary_direction || 'asc';
        this.secondarySortDirection = app.lang.get(this.sortDirectionLabels[sortDirection], this.module);

        // Initialize the filter definition default template string
        this._buildFilterString(this.defaults.filter_def);
    },

    /**
     * Builds a readable string representing a filter definition
     *
     * @param {Array} filterDef the filter definition to convert into a string
     * @private
     */
    _buildFilterString: function(filterDef) {
        filterDef = filterDef || [];

        // Make use of the existing filter-field code to help with getting
        // field and operator labels
        var tempField = app.view.createField({
            def: {
                name: 'temp_field',
                type: 'filter-field'
            },
            view: this.view,
            nested: true,
            viewName: 'edit',
            model: this.model
        });

        // Add the rows/rules of the filter definition to the filter string one
        // at a time
        this.filterString = '';
        _.each(filterDef, function(filter) {
            _.each(filter, function(conditions, field) {
                // If this is not the first filter rule, add an "and" to separate them
                if (!_.isEmpty(this.filterString)) {
                    this.filterString += app.lang.get('LBL_CONSOLE_DIRECTIONS_FILTER_AND', this.module);
                }
                this.filterString += this._buildFilterRowString(field, conditions, tempField);
            }, this);
        }, this);

        tempField.dispose();
    },

    /**
     * Helper function to build a string representing a single row/rule of a
     * filter definition
     *
     * @param {string} field the field name of the filter rule (ex: 'name' or '$owner')
     * @param {Object} conditions the conditions of the filter rule, typically a map
     *                 of operator => value(s)
     * @param {Object} filterField an instance of filter-field useful for getting
     *                  label information for filter rules
     * @return {string} a string representing the filter row
     * @private
     */
    _buildFilterRowString: function(field, conditions, filterField) {
        var rowString = '';
        rowString += this._getFilterFieldString(field, filterField);

        // If this is not a predefined filter, also add the operator
        if (filterField.fieldList[field] && !filterField.fieldList[field].predefined_filter) {
            rowString += this._getFilterOperatorAndValueString(field, conditions, filterField);
        }

        return rowString;
    },

    /**
     * Helper function to get the field of a filter row as a readable string
     *
     * @param {string} field the field name of the filter rule (ex: 'name' or '$owner')
     * @param {Object} filterField an instance of filter-field useful for getting
     *                  label information for filter rules
     * @return {string} a string representing the field label
     * @private
     */
    _getFilterFieldString: function(field, filterField) {
        if (filterField.filterFields && filterField.filterFields[field]) {
            return filterField.filterFields[field] + ' ';
        }
        return '';
    },

    /**
     * Helper function to get the operator(s) and value(s) of a filter row as a
     * readable string
     *
     * @param {string} field the field name of the filter rule (ex: 'name' or '$owner')
     * @param {Object} conditions the conditions of the filter rule, typically a map
     *                 of operator => value(s)
     * @param {Object} filterField an instance of filter-field useful for getting
     *                  label information for filter rules
     * @return {string} a string representing the operator and value(s) labels
     * @private
     */
    _getFilterOperatorAndValueString: function(field, conditions, filterField) {
        var operatorAndValueString = '';
        _.each(conditions, function(value, operator) {
            // If there are multiple conditions on the field, separate them with commas
            if (!_.isEmpty(operatorAndValueString)) {
                operatorAndValueString += ', ';
            }

            // Add the operator label based on the field type
            var fieldData = app.metadata.getField({
                module: this.model.get('enabled_module'),
                name: field
            });
            var hasOperatorLabel = filterField.filterOperatorMap && filterField.filterOperatorMap[fieldData.type];
            if (hasOperatorLabel) {
                var operatorMap = filterField.filterOperatorMap[fieldData.type];
                if (operatorMap[operator]) {
                    operatorAndValueString += app.lang.get(operatorMap[operator], 'Filters') + ': ';
                }
            }

            // If the operator requires a value, add the value to the string
            if (!_.contains(filterField._operatorsWithNoValues, operator)) {
                operatorAndValueString += this._getFilterValueString(value) + ' ';
            }
        }, this);
        return operatorAndValueString;
    },

    /**
     * Helper function to get the value(s) of a filter row value as a readable
     * string
     *
     * @param value the value(s) of the filter row
     * @return {Array|string} the string representing the filter row's value(s)
     * @private
     */
    _getFilterValueString: function(value) {
        if (_.isArray(value)) {
            var valueString = '(';
            for (var i = 0; i < value.length; i++) {
                if (i > 0) {
                    if (i === value.length - 1) {
                        valueString += ' ' + app.lang.get('LBL_CONSOLE_DIRECTIONS_FILTER_OR', this.module);
                    } else {
                        valueString += ', ';
                    }
                }
                valueString += value[i];
            }
            valueString += ')';
            return valueString;
        }
        return value;
    },

    /**
     * Set defaultViewMeta to context and trigger defaultmetaready.
     *
     * @param data
     */
    setViewMetaData: function(data) {
        this.context.set('defaultViewMeta', data);
        this.context.trigger('consoleconfig:reset:defaultmetaready');
    },

    /**
     * Sets the default values for fields on the model when the reset button is
     * clicked. Triggers an event to signal to the filter field to re-render properly
     */
    restoreClicked: function() {
        this.model.set(this.defaults);
        this.model.trigger('consoleconfig:reset:default');

        var params = {modules: this.model.get('enabled_module'), type: 'view', name: 'multi-line-list'};
        var url = app.api.buildURL('ConsoleConfiguration', 'default-metadata', {}, params);
        app.api.call('GET', url, null, {
            success: _.bind(this.setViewMetaData, this)
        });
    }
})

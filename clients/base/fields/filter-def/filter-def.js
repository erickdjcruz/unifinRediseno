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
 * @class View.Fields.Base.FilterdefField
 * @alias SUGAR.App.view.fields.BaseFilterdefField
 * @extends View.Fields.Base.BaseField
 */
({
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
     * Stores the list of filter field options.
     */
    fieldList: {},

    /**
     * Modules that are marked as needing the field list API call in order to get a more full field list for filtering
     * Normally we only use the call for non-visible modules since we dont store their filterable fields in metadata.
     */
    needForcedAPICall: [
        'pmse_Inbox',
    ],

    /**
     * Array of filter definitions in readble format
     */
    readableFilterDef: [],
    /**
     * Source module for the filter definition
     */
    sourceModule: '',
    /**
     * The filter defs
     */
    filterDefs: {},
    /**
     * Operator map associated with filterable fields
     */
    filterOperatorMap: {},

    /**
     * @override
     * @protected
     * @chainable
     */
    _render: function() {
        var self = this;
        if (this.action === 'list' && !this.fieldList || _.isEmpty(this.fieldList)) {
            this.loadFieldList(false, function() {
                self.isFetchingOptions = false;

                //Re-render widget since we have fresh options list
                if (!this.disposed) {
                    this.render();
                }
            });
            if (this.isFetchingOptions) {
                // Set loading message in place of empty DIV while options are loaded via API
                this.$el.html('<div class="select2-loading">' + app.lang.get('LBL_LOADING') + '</div>');
                return this;
            }
        }
        app.view.Field.prototype._render.call(this);
    },

    /**
     * Load the options for this field and pass them to callback function.  May be asynchronous.
     * @param {boolean} fetch (optional) Force use of DataArchiver API to load FieldDefs.
     * @param {Function} callback (optional) Called when fieldList is available.
     */
    loadFieldList: function(fetch, callback, error) {
        var self = this;
        var _module = this.module;
        var _fieldsKey = 'cache:' + _module + ':fieldDefs';

        if (!_.isUndefined(app.metadata.getModule(_module)) && !this.needForcedAPICall.includes(_module)) {
            this.fieldList = app.data.getBeanClass('Filters').prototype.getFilterableFields(_module);
            if (_.isUndefined(this.fieldList.deleted)) {
                this.fieldList.deleted = app.metadata.getField({module: _module, name: 'deleted'});
            }
        } else {
            this.fieldList = undefined;
        }

        fetch = fetch || false;

        if (fetch || !this.fieldList) {
            this.isFetchingOptions = true;
            var _key = 'request:' + _module + ':fieldDefs';
            var request;
            //if previous request is existed, ignore the duplicate request
            if (this.context.get(_key)) {
                request = this.context.get(_key);
                request.xhr.done(_.bind(function(o) {
                    if (this.fieldList !== o) {
                        this.fieldList = o;
                        callback.call(this);
                    }
                }, this));
            } else {
                var url = app.api.buildURL('metadata/' + _module + '/fields', null, null, {});
                request = app.api.call('read', url, null, {
                    success: function(results) {
                        if (self.disposed) {
                            return;
                        }
                        self.fieldList = results;
                        self.context.set(_fieldsKey, self.fieldList);
                    },
                    error: function(e) {
                        if (self.disposed) {
                            return;
                        }

                        if (error) {
                            error(e);
                        }

                        // Continue to use Sugar7's default error handler.
                        if (_.isFunction(app.api.defaultErrorHandler)) {
                            app.api.defaultErrorHandler(e);
                        }
                    },
                    complete: function() {
                        if (!self.disposed) {
                            self.context.unset(_key);
                            callback.call(self);
                        }
                    }
                });
                this.context.set(_key, request);
            }
        }
    },

    /**
     * @inheritdoc
     *
     * Format the value to a string.
     * Return an empty string for undefined, null and object types.
     * Convert boolean to True or False.
     * Convert array, int and other types to a string.
     *
     * @param {string} the filter def in string format
     * @return {string} the formatted value
     */
    format: function(value) {
        this.readableFilterDef = [];
        this.sourceModule = this.module;
        this.filterDefs = JSON.parse(value);
        this.filterOperatorMap = app.metadata.getFilterOperators(this.sourceModule);

        _.each(this.filterDefs, function(def) {
            this.readableFilterDef.push(this.parseDef(def));
        }, this);

        return this.readableFilterDef.join(',\n');
    },

    /**
     * Responsible for parsing through the filterDef and creating a human readable string
     * @param def the filter definition line to be parsed
     * @return {string} the filter definition line in human readable format
     */
    parseDef: function(def) {
        // Create an object that will be referenced through the chain of private methods that need to be called
        this.readableString = {
            keyStringReadable: '',
            operatorStringReadable: '',
            valueStringReadable: '',
            toString: function() {
                return this.keyStringReadable + ' ' + this.operatorStringReadable + ' ' + this.valueStringReadable;
            }
        };

        // Call the first method in the chain
        _.each(def, function(value, key) {
            var keyValuePair = new app.utils.FilterOptions().keyValueFilterDef(key, value, this.fieldList);
            key = keyValuePair[0];
            value = keyValuePair[1];

            // Set readableString object
            this.readableString.keyStringReadable = this._getKeyString(key, value);
            this.readableString.operatorStringReadable = this._getOperatorString(key, value);
            this.readableString.valueStringReadable = this._getValueString(key, value);
        }, this);

        // Return the readable string to be displayed
        return this.readableString.toString();
    },

    /**
     * Add the key string to the readableString object
     * @param key
     * @param value
     * @private
     */
    _getKeyString: function(key, value) {
        // Get the key string in readable format
        return app.lang.get(this.fieldList[key].vname, this.sourceModule);
    },

    /**
     * Add the operator string to the readableString object
     * @param key
     * @param value
     * @private
     */
    _getOperatorString: function(key, value) {
        // Get the operator string in a readable format
        var fieldName = key;
        var fieldOperator = Object.keys(value)[0];
        var fieldType = this.fieldTypeMap[this.fieldList[fieldName].type] || this.fieldList[fieldName].type;

        var operatorStringReadable = app.lang.get(this.filterOperatorMap[fieldType][fieldOperator], 'Filters');
        return _.isUndefined(operatorStringReadable) ? '' : operatorStringReadable;
    },

    /**
     * Add the value string to the readableString object
     * @param value
     * @param fieldOperator
     * @param fieldType
     * @private
     */
    _getValueString: function(key, value) {
        // Get the value string in a readable format
        var operatorsWithNoValues = ['$empty', '$not_empty'];

        var fieldName = key;
        var fieldOperator = Object.keys(value)[0];
        var fieldType = this.fieldTypeMap[this.fieldList[fieldName].type] || this.fieldList[fieldName].type;

        var value = value[fieldOperator];

        var valueStringReadable = '';

        // Depending on the type of value, it is rendered differently
        if (_.contains(operatorsWithNoValues, fieldOperator)) {
            valueStringReadable = '';
        } else if (_.isArray(value)) {
            valueStringReadable = value.join(' or ');
        } else if (fieldType === 'bool') {
            valueStringReadable = Boolean(value).toString();
        } else if (fieldType === 'currency') {
            valueStringReadable = app.currency.formatAmount(value.amount, value.currency_id, 2);
        } else {
            valueStringReadable = value.toString();
        }

        return valueStringReadable;
    },
})

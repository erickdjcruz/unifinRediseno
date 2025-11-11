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
 * @class View.Fields.Base.FormulaBuilderField
 * @alias SUGAR.App.view.fields.BaseFormulaBuilderField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click a[data-action=add-related]': 'addRelatedField',
        'click a[data-action=add-rollup]': 'addRollupField',
        'change textarea.formula-editor': 'formulaChanged',
        'click a[data-action=related]': 'showRelatedControllers',
        'click a[data-action=rollup]': 'showRollupControllers',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);
    },

    /**
     * Quick initialization of field properties
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        this._module = options.targetModule;
        this._formula = options.formula ? options.formula : '';
        this._callback = options.callback;
        this._returnType = options.returnType;
        this._matchField = options.matchField;

        // metadata of all the select2s needed
        this._dropdowns = [{
            selector: 'a[data-select=functions]',
            func: _.bind(this._addFunction, this),
            query: '_queryFunctions',
            label: 'LBL_ACTIONBUTTON_FUNCTIONS',
            skipValue: true,
            format: '_formatFunctionOptions'
        }, {
            selector: 'a[data-select=fields]',
            func: _.bind(this._addField, this),
            query: '_queryFields',
            label: 'LBL_ACTIONBUTTON_FIELDS',
            skipValue: true,
        }, {
            selector: 'a[data-select=related-module]',
            func: _.bind(this._changeRelatedModule, this),
            query: '_queryRelatedModule',
            label: 'LBL_ACTIONBUTTON_R_MODULE',
        }, {
            selector: 'a[data-select=related-fields]',
            query: '_queryRelatedFields',
            label: 'LBL_ACTIONBUTTON_R_FIELDS',
        }, {
            selector: 'a[data-select=rollup-module]',
            func: _.bind(this._changeRollupModule, this),
            query: '_queryRollupModule',
            label: 'LBL_ACTIONBUTTON_ROLL_MODULE',
        }, {
            selector: 'a[data-select=rollup-fields]',
            query: '_queryRollupFields',
            label: 'LBL_ACTIONBUTTON_ROLL_FIELDS',
        }, {
            selector: 'a[data-select=rollup-function]',
            query: '_queryRollupType',
            label: 'LBL_ACTIONBUTTON_ROLL_TYPE',
        }];

        this._functions = this._getFunctions();

        this._rollupFunctions = {
            rollupMin: app.lang.get('LBL_ACTIONBUTTON_MINIMUM'),
            rollupMax: app.lang.get('LBL_ACTIONBUTTON_MAXIMUM'),
            rollupAve: app.lang.get('LBL_ACTIONBUTTON_AVERAGE'),
            rollupSum: app.lang.get('LBL_ACTIONBUTTON_SUM'),
        };

        this._currentRelatedFields = {};
        this._relatedFields = {};
        this._rollupModules = {};
        this._relatedModules = {};
        this._currentRollupFields = {};
        this._rollupFields = {};
        this._functionsHelp = {};
        this._fields = {};
        this._fieldsType = {};

        this._getMeta();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._cachedElements = new Map();

        this._super('_render');

        _.each(this._dropdowns, function __createSelect2(dropdownData) {
            this._createSelect2(
                dropdownData.selector,
                dropdownData.func,
                dropdownData.query,
                dropdownData.label,
                dropdownData.skipValue,
                dropdownData.format
            );
        }, this);
    },

    /**
     * Finds DOM elements and caches them for later usage
     *
     * @param {string} selector
     *
     * @return {jQuery}
     */
    find: function(selector) {
        var $el = selector && this._cachedElements.has(selector) && this._cachedElements.get(selector);

        if ($el) {
            return $el;
        }

        $el = this.$(selector);

        if (selector && $el.length > 0) {
            this._cachedElements.set(selector, $el);
        }

        return $el;
    },

    /**
     * Return current formula
     *
     * @return {string}
     */
    getFormula: function() {
        return this._formula;
    },

    /**
     * Validates current formula
     *
     * @return {bool}
     */
    isValid: function() {
        if (Object.keys(this._fieldsType).length < 1) {
            return true;
        }

        var isValid = SUGAR.expressions.validateExpressionSidecar(
            this._formula,
            this._fieldsType,
            this._module,
            this._returnType
        );

        var children = this.$el.children();

        children.toggleClass('formula-invalid', !isValid);

        return isValid;
    },

    /**
     * Event handler for whenever the formula changes.
     *
     * @param {UIEvent} e
     *
     */
    formulaChanged: function(e) {
        this._formula = e.currentTarget.value;

        let $el = $(e.currentTarget);
        let $parent = $el.parent();

        if ($parent.hasClass('formula-invalid')) {
            $parent.toggleClass('formula-invalid', false);
        }

        if (this._callback) {
            this._callback(this._formula);
        }
    },

    /**
     * Show UI controls for related field selection
     *
     * @param {UIEvent} e
     *
     */
    showRelatedControllers: function(e) {
        var $button = $(e.currentTarget);
        var $bar = this.find('div.related-action-bar');

        var isActive = $button.hasClass('active');

        $bar.toggleClass('hidden', isActive);
        $button.toggleClass('active', !isActive);
    },

    /**
     * Show UI controls for rollup function selection
     *
     * @param {UIEvent} e
     *
     */
    showRollupControllers: function(e) {
        var $button = $(e.currentTarget);
        var $bar = this.find('div.rollup-action-bar');

        var isActive = $button.hasClass('active');

        $bar.toggleClass('hidden', isActive);
        $button.toggleClass('active', !isActive);
    },

    /**
     * Handle related field addition
     *
     * @param {UIEvent} e
     *
     */
    addRelatedField: function(e) {
        var link = this.find('a[data-select=related-module]').val();
        var field = this.find('a[data-select=related-fields]').val();

        // build the related field and add it to the existing formula
        this._formula = this._formula + 'related($' + link + ', "' + field + '")';
        this.find('textarea.formula-editor').val(this._formula);

        if (this._callback) {
            this._callback(this._formula);
        }
    },

    /**
     * Handle rollup function addition
     *
     * @param {UIEvent} e
     *
     */
    addRollupField: function(e) {
        var link = this.find('a[data-select=rollup-module]').val();
        var field = this.find('a[data-select=rollup-fields]').val();
        var func = this.find('a[data-select=rollup-function]').val();

        // build the rollup field and add it to the existing formula
        this._formula = this._formula + func + '($' + link + ',"' + field + '")';
        this.find('textarea.formula-editor').val(this._formula);

        if (this._callback) {
            this._callback(this._formula);
        }
    },

    /**
     * Handle adding a generic formula function
     *
     * @param {Object} data
     *
     */
    _addFunction: function(data) {
        // adds the function to the already existing formula
        this._formula = this._formula + data.text + '(';
        this.find('textarea.formula-editor').val(this._formula);

        if (this._callback) {
            this._callback(this._formula);
        }
    },

    /**
     * Handle adding a field
     *
     * @param {Object} data
     *
     */
    _addField: function(data) {
        // adds the field to the already existing formula
        this._formula = this._formula + '$' + data.text;
        this.find('textarea.formula-editor').val(this._formula);

        if (this._callback) {
            this._callback(this._formula);
        }
    },

    /**
     * Handle changing the relate module in the relate field UI bar
     *
     * @param {Object} data
     *
     */
    _changeRelatedModule: function(data) {
        // whenever we change the related module we have to change the related fields as well
        this._currentRelatedFields = this._relatedFields[data.text];
    },

    /**
     * Handle changing the rollup module in the rollup function UI bar
     *
     * @param {Object} data
     *
     */
    _changeRollupModule: function(data) {
        // whenever we change the rollup module we have to change the rollup fields as well
        this._currentRollupFields = this._rollupFields[data.text];
    },

    /**
     * Create a generic Select2 control
     *
     * @param {string} id
     * @param {Function} callback
     * @param {string} queryFunction
     * @param {string} label
     * @param {bool} skipValue
     * @param {string} formatOptions
     *
     */
    _createSelect2: function(selector, callback, queryFunction, label, skipValue, formatOptions) {
        var el = this.find(selector)
            .select2(this._getSelect2Options(queryFunction, label, formatOptions))
            .data('select2');

        el.onSelect = (function select(fn) {
            return function returnCallback(data, options) {
                if (callback) {
                    callback(data);
                }

                // we have to reset the select2 display after each select
                if (arguments && skipValue) {
                    arguments[0] = {
                        id: 'select',
                        text: app.lang.get(label)
                    };
                }

                return fn.apply(this, arguments);
            };
        })(el.onSelect);
    },

    /**
     * Create a generic wrapper around a querying function used by the Select2 component to build up the options list
     *
     * @param {string} queryFunction
     * @param {string} label
     * @param {string} formatOptions
     *
     * @return {Object}
     */
    _getSelect2Options: function(queryFunction, label, formatOptions) {
        var select2Options = {};

        select2Options.placeholder = app.lang.get(label);
        select2Options.query = _.bind(this[queryFunction], this);
        select2Options.dropdownAutoWidth = true;

        if (formatOptions) {
            select2Options.formatResult = _.bind(this[formatOptions], this);
        }

        return select2Options;
    },

    /**
     * Return custom Select2 item formatted option, in order to add the item tooltip
     *
     * @param {Object} option
     *
     * @return {string}
     */
    _formatFunctionOptions: function(option) {
        // adds the help for each function as a tooltip
        var text = option.text;
        var help = this._functionsHelp[text] ?
            this._functionsHelp[text] :
            app.lang.get('LBL_ACTIONBUTTON_HELP_NOT_AVAILABLE');
        return '<div data-toggle=\'tooltip\' data-html=\'true\' rel=\'tooltip\' ' +
            ' data-bs-placement=\'right\' title=\'' + _.escape(help) + '\'>' + _.escape(text) + '</div>';
    },

    /**
     * Wrapper for querying functions for select2 components
     *
     * @param {Function} query
     *
     */
    _queryFunctions: function(query) {
        this._query(query, '_functions');
    },

    /**
     * Wrapper for querying fields for select2 components
     *
     * @param {Function} query
     *
     */
    _queryFields: function(query) {
        this._query(query, '_fields');
    },

    /**
     * Wrapper for querying related modules for select2 components
     *
     * @param {Function} query
     *
     */
    _queryRelatedModule: function(query) {
        this._query(query, '_relatedModules');
    },

    /**
     * Wrapper for querying rollup modules for select2 components
     *
     * @param {Function} query
     *
     */
    _queryRollupModule: function(query) {
        this._query(query, '_rollupModules');
    },

    /**
     * Wrapper for querying related fields for select2 components
     *
     * @param {Function} query
     *
     */
    _queryRelatedFields: function(query) {
        this._query(query, '_currentRelatedFields');
    },

    /**
     * Wrapper for querying rollup fields for select2 components
     *
     * @param {Function} query
     *
     */
    _queryRollupFields: function(query) {
        this._query(query, '_currentRollupFields');
    },

    /**
     * Wrapper for querying rollup types for select2 components
     *
     * @param {Function} query
     *
     */
    _queryRollupType: function(query) {
        this._query(query, '_rollupFunctions');
    },

    /**
     * Generic implementation of select2 query method
     *
     * @param {Function} query
     * @param {Object} options
     *
     */
    _query: function(query, options) {
        var listElements = this[options];
        var data = {
            results: [],
            more: false
        };

        // return only the options that match the input query
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
     * Return function list
     *
     * @return {Object}
     */
    _getFunctions: function() {
        var typeMap = SUGAR.expressions.Expression.TYPE_MAP;
        var funcMap = SUGAR.FunctionMap;
        var funcList = {};

        for (var functionName in funcMap) {
            if (_.isFunction(funcMap[functionName]) && funcMap[functionName].prototype) {
                for (var functionReturnType in typeMap) {
                    if (funcMap[functionName].prototype instanceof typeMap[functionReturnType]) {
                        funcList[functionName] = functionName;
                        break;
                    }
                }
            }
        }
        return funcList;
    },

    /**
     * Retrieve FormulaBuilder metadata from server
     *
     */
    _getMeta: function() {
        app.api.call(
            'create',
            app.api.buildURL('formulaBuilder/meta'),
            {
                module: this._module
            },
            null,
            {
                success: _.bind(function callback(data) {
                    this._fields = data.fields;
                    this._relatedFields = data.relateFields;
                    this._relatedModules = data.relateModules;
                    this._functionsHelp = data.help;
                    this._rollupFields = data.rollupFields;
                    this._fieldsType = this._getFieldsType(data);

                    if (this._matchField && !this._returnType) {
                        this._returnType = this._getMatchFieldType();
                    }

                    this._calculateRollupModules();
                }, this)
            }
        );
    },

    /**
     * Because of Defect 67228, formula builder doesn't recognize email1 as a valid field,
     * so we're not allowed to use it in our calculations, however for actions such as Compose Email in
     * ActionButtons, it is preferable to also have email1 in order to build the recipient list.
     *
     * This adds the field for any module that has email address support.
     *
     * @param {Object} data
     *
     * @return {Object}
     */
    _getFieldsType: function(data) {
        let fieldsTypes = data.fieldsTypes;

        if (_.has(fieldsTypes, 'email_addresses')) {
            fieldsTypes.email1 = ['email1', 'string'];
        }

        return fieldsTypes;
    },

    /**
     * Get matched field type
     *
     * @return {string}
     */
    _getMatchFieldType: function() {
        var _fieldType = 'string';

        _.each(this._fieldsType, function getType(data) {
            if (data[0] === this._matchField) {
                _fieldType = data[1];
            }
        }, this);

        return _fieldType;
    },

    /**
     * Filter down related modules with rollupable fields and save them
     *
     */
    _calculateRollupModules: function() {
        this._rollupModules = {};
        // build a list of rollup modules from all the valid rollup fields we have
        _.each(this._rollupFields, function getModules(fields, moduleName) {
            if (fields.length > 0 || Object.keys(fields).length > 0) {
                var linkName = '';

                _.each(this._relatedModules, function getLink(relModuleName, relLink) {
                    if (relModuleName === moduleName) {
                        linkName = relLink;
                    }
                });

                this._rollupModules[linkName] = moduleName;
            }
        }, this);
    },
});

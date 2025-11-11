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
(function initExpressions(app) {
    app.events.on('app:init', function appInit() {
        /**
         * Adding some functionality from the old BWC expressions javascript framework.
         * Adapted to work with Sidecar
         */
        /**
         * Validate a given formula
         *
         * @param {string} expression
         * @param {Array} fieldsArray
         * @param {string} moduleName
         * @param {string} matchType
         *
         * @return {bool}
         */
        SUGAR.expressions.validateExpressionSidecar = function(expression, fieldsArray, moduleName, matchType) {
            try {
                var varTypeMap = {};

                fieldsArray = fieldsArray ? fieldsArray : [];
                expression = expression ? expression : '';

                _.each(fieldsArray, function getVarTypeMap(fieldData) {
                    varTypeMap[fieldData[0]] = fieldData[1];
                });

                var tokens = new SUGAR.expressions.ExpressionParser().tokenize(expression);

                SUGAR.expressions.setReturnTypesSidecar(tokens, varTypeMap);
                SUGAR.expressions.validateReturnTypesSidecar(tokens);

                var def = SUGAR.expressions.validateRelateFunctionsSidecar(tokens, moduleName);
                var returnType = tokens.returnType;

                if (tokens.name === 'related' && def.type) {
                    switch (def.type) {
                        case 'date':
                        case 'datetime':
                        case 'datetimecombo':
                            returnType = 'date';
                            break;
                        case 'bool':
                            returnType = 'boolean';
                            break;
                        default:
                            matchType = '';
                    }
                }

                if (matchType && matchType !== returnType) {
                    app.alert.show('alert_return_type_missmatch', {
                        level: 'error',
                        title: app.lang.get('LBL_INVALID_FORMULA'),
                        messages: app.lang.get('LBL_RETURN_TYPE_MISSMATCH') + ' ' + matchType,
                        autoClose: true,
                        autoCloseDelay: 5000
                    });

                    return false;
                }

                return true;
            } catch (e) {
                app.alert.show('alert_expression_error', {
                    level: 'error',
                    title: app.lang.get('LBL_INVALID_FORMULA'),
                    messages: e.message ? e.message : e,
                    autoClose: true,
                    autoCloseDelay: 5000
                });

                return false;
            }
        };

        /**
         * Add return type to the given expression
         *
         * @param {Object} t
         * @param {Object} vMap
         *
         */
        SUGAR.expressions.setReturnTypesSidecar = function(t, vMap) {
            var see = SUGAR.expressions.Expression;

            if (t.type === 'variable') {
                if (_.isUndefined(vMap[t.name])) {
                    throw 'Unknown field: ' + t.name;
                } else if (vMap[t.name] === 'relate') {
                    t.returnType = SUGAR.expressions.Expression.GENERIC_TYPE;
                } else {
                    t.returnType = vMap[t.name];
                }
            }

            if (t.type === 'function') {
                for (var i in t.args) {
                    SUGAR.expressions.setReturnTypesSidecar(t.args[i], vMap);
                }

                var fMap = SUGAR.FunctionMap;

                if (_.isUndefined(fMap[t.name])) {
                    throw t.name + ': No such function defined';
                }

                for (var j in see.TYPE_MAP) {
                    if (fMap[t.name].prototype instanceof see.TYPE_MAP[j]) {
                        t.returnType = j;
                        break;
                    }
                }

                // For the conditional function, if both argument return types are same, set the conditional type
                if (t.name === 'ifElse') {
                    var args = t.args;
                    var returnTypeIndex = 2;

                    if (args[1].returnType === args[returnTypeIndex].returnType) {
                        t.returnType = args[1].returnType;
                    }
                }

                if (!t.returnType) {
                    throw t.name + ': No known return type!';
                }
            }
        };

        /**
         * Validate expression return type
         *
         * @param {Object} t
         *
         */
        SUGAR.expressions.validateReturnTypesSidecar = function(t) {
            if (t.type === 'function') {
                //Depth first recursion
                for (var i in t.args) {
                    SUGAR.expressions.validateReturnTypesSidecar(t.args[i]);
                }

                var fMap = SUGAR.FunctionMap;
                var see = SUGAR.expressions.Expression;

                if (_.isUndefined(fMap[t.name])) {
                    throw t.name + ': No such function defined';
                }

                var types = fMap[t.name].prototype.getParameterTypes();
                var count = fMap[t.name].prototype.getParamCount();

                // check parameter count
                if (count === see.INFINITY && t.args.length === 0) {
                    throw t.name + ': Requires at least one parameter';
                }

                if (count !== see.INFINITY && t.args instanceof Array && t.args.length !== count) {
                    throw t.name + ': Requires exactly ' + count + ' parameter(s)';
                }

                if (typeof types === 'string') {
                    for (var j = 0; j < t.args.length; j++) {
                        if (!t.args[j].returnType) {
                            throw t.name + ': No known return type!';
                        }

                        if (!fMap[t.name].prototype.isProperType(new see.TYPE_MAP[t.args[j].returnType](), types)) {
                            throw t.name + ': All parameters must be of type \'' + types + '\'';
                        }
                    }
                } else {
                    for (var k = 0; k < types.length; k++) {
                        if (!fMap[t.name].prototype.isProperType(new see.TYPE_MAP[t.args[k].returnType](), types[k])) {
                            throw t.name + ': The parameter at index ' + k + ' must be of type ' + types[k];
                        }
                    }
                }
            }
        };

        /**
         * Valite expressions that work off relate fields
         *
         * @param {Object} t
         * @param {string} moduleName
         *
         * @return {bool}
         */
        SUGAR.expressions.validateRelateFunctionsSidecar = function(t, moduleName) {
            if (t.type === 'function') {
                for (var i in t.args) {
                    SUGAR.expressions.validateRelateFunctionsSidecar(t.args[i], moduleName);
                }

                // These functions all take a link and a string for a related field
                let relFuncs = ['related', 'rollupAve', 'rollupMax', 'rollupMin', 'rollupSum'];

                // These functions take a single string for a Users field
                let userFuncs = ['currentUserField'];

                let url;
                if (relFuncs.includes(t.name)) {
                    url = 'index.php?' + SUGAR.expressions.paramsToUrl({
                        module: 'ExpressionEngine',
                        action: 'validateRelatedField',
                        tmodule: moduleName,
                        package: '',
                        link: t.args[0].name,
                        related: t.args[1].value
                    });
                } else if (userFuncs.includes(t.name)) {
                    url = 'index.php?' + SUGAR.expressions.paramsToUrl({
                        module: 'ExpressionEngine',
                        action: 'validateUserField',
                        package: '',
                        related: t.args[0].value
                    });
                } else {
                    return true;
                }

                var resp = SUGAR.expressions.httpFetchSyncSidecar(url);
                var def = JSON.parse(resp.responseText);

                //Check if a field was found
                if (typeof def === 'string') {
                    throw t.name + ': ' + def;
                }

                let genericTypeFunctions = ['related', 'currentUserField'];
                let numberTypes = ['decimal', 'int', 'float', 'currency'];

                if (!genericTypeFunctions.includes(t.name) && def.type && !numberTypes.includes(def.type)) {
                    throw (t.name + ': related field  ' + t.args[1].value + ' must be a number');
                }

                return def;
            }
        };

        /**
         * Validate/evaluate formula through backend APIs
         *
         * @param {string} url
         * @param {Object|null|undefined} postData
         * @param {Object} headers
         *
         * @return {Object}
         */
        SUGAR.expressions.httpFetchSyncSidecar = function(url, postData, headers) {
            headers = headers || {};
            var globalXmlhttp = new XMLHttpRequest();
            var method = 'GET';

            if (typeof postData !== 'undefined') {
                method = 'POST';
            }

            try {
                globalXmlhttp.open(method, url, false);
            } catch (e) {
                var message = 'message:' + e.message + ':url:' + url;

                app.alert.show('alert_expression_error', {
                    level: 'error',
                    title: app.lang.get('LBL_INVALID_FORMULA'),
                    messages: message,
                    autoClose: true,
                    autoCloseDelay: 5000
                });
            }

            if (method === 'POST' && typeof headers['Content-Type'] === 'undefined') {
                headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }

            for (var header in headers) {
                globalXmlhttp.setRequestHeader(header, headers[header]);
            }

            globalXmlhttp.send(postData);

            var args = {
                responseText: globalXmlhttp.responseText,
                responseXML: globalXmlhttp.responseXML,
                request_id: 0
            };

            return args;
        };

        /**
         * Convert given parameters to a URL encoded string
         *
         * @param {Object} params
         *
         * @return {string}
         */
        SUGAR.expressions.paramsToUrl = function(params) {
            var parts = [];
            for (var i in params) {
                if (_.has(params, i)) {
                    parts.push(encodeURIComponent(i) + '=' + encodeURIComponent(params[i]));
                }
            }
            return parts.join('&');
        };

        /**
         * Returns map index for a given expression
         *
         * @param {Array} arr
         * @param {string} val
         * @param {number} start
         *
         * @return {number}
         */
        SUGAR.expressions.arrayIndexOf = function(arr, val, start) {
            if (typeof arr.indexOf === 'function') {
                return arr.indexOf(val, start);
            }

            for (var i = start || 0, j = arr.length; i < j; i++) {
                if (arr[i] === val) {
                    return i;
                }
            }

            return -1;
        };

    });
})(SUGAR.App);

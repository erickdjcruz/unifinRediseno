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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * EnumOperator Management
         *
         * @class Core.FilterOperators.EnumOperator
         * @alias SUGAR.App.FilterOperators.EnumOperator
         *
         * @param {Object} def Operator Definition
         *
         */
        function EnumOperator(def) {
            this.def = def;
        }

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        EnumOperator.prototype.getUpdatedInput = function(callback) {
            this.updateEnumFilterData(callback);
        };

        /**
         * Get enum options list
         *
         * @param {Function} callback Callback function
         */
        EnumOperator.prototype.updateEnumFilterData = function(callback) {
            if (this.def._optionsList) {
                this.updateEnumOptionsList(callback, false);

                return;
            }

            app.api.call(
                'read',
                app.api.buildURL(`${this.def._seedModule}/enum/${this.def._seedFieldDef.name}`),
                null,
                {
                    success: (optionsList) => {
                        this.def._optionsList = optionsList;

                        this.updateEnumOptionsList(callback, true);
                    },
                    error: () => {
                        callback({
                            properties: {
                                _loading: false,
                            },
                        });
                    },
                }
            );
        },

        /**
         * Update enum options list
         *
         * @param {Function} callback Callback function
         * @param {boolean} needsUpdating check if we have the list enum already
         */
        EnumOperator.prototype.updateEnumOptionsList = function(callback, needsUpdating) {
            let _inputData = this.def._optionsList;
            let _inputType = '';
            let _inputValue = {};
            let _inputValue1 = {};
            let _needsRendering = true;

            if (!_.isEmpty(this.def._seedFieldDef.enumOptions)) {
                _inputData = this.def._seedFieldDef.enumOptions;
            }

            if (!_.isEmpty(_inputData)) {
                delete _inputData[''];
            }

            if (_.contains(['one_of', 'not_one_of'], this.def._qualifierName)) {
                _inputType = 'enum-multiple';

                _inputValue = {};
                _inputValue1 = {};

                if (!needsUpdating) {
                    _needsRendering = false;
                }

                _.each(this.def._filterData.input_name0, function getOptions(option) {
                    _inputValue[option] = _inputData[option];
                }, this);

                _.each(_inputData, function getOptions(option, key) {
                    if (!_.has(_inputValue, key) && option) {
                        _inputValue1[key] = option;
                    }
                }, this);
            } else {
                _inputType = 'enum-single';

                const choices = this.def._filterData.input_name0;
                _inputValue = _.first(choices);

                if (!_inputValue) {
                    const firstEnumChoice = _.chain(_inputData).keys().first().value();

                    _inputValue = firstEnumChoice;
                    this.def._filterData.input_name0 = [_inputValue];
                }
            }

            callback({
                properties: {
                    _optionsList: this.def._optionsList,
                    _filterData: this.def._filterData,
                    _loading: false,
                    _inputData,
                    _inputType,
                    _inputValue,
                    _inputValue1,
                },
                needsUpdating,
                needsRendering: _needsRendering,
            });
        },

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            EnumOperator: EnumOperator
        });
    });
})(SUGAR.App);

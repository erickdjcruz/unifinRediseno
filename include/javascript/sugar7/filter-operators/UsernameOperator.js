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
         * UsernameOperator Management
         *
         * @class Core.FilterOperators.UsernameOperator
         * @alias SUGAR.App.FilterOperators.UsernameOperator
         *
         * @param {Object} def Operator Definition
         */
        function UsernameOperator(def) {
            this.def = def;
        };

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        UsernameOperator.prototype.getUpdatedInput = function(callback) {
            if (_.contains(['one_of', 'not_one_of'], this.def._qualifierName)) {
                let _inputType = 'select-multiple';

                let _inputValue = {};
                let _inputValue1 = {};

                _.each(this.def._filterData.input_name0, function getOptions(option) {
                    _inputValue[option] = this.def._users[option];
                }, this);

                _.each(this.def._users, function getOptions(option, key) {
                    if (!_.has(_inputValue, key)) {
                        _inputValue1[key] = option;
                    }
                }, this);

                callback({
                    properties: {
                        _inputType,
                        _inputValue,
                        _inputValue1,
                    },
                    needsRendering: true,
                });
            } else {
                const choices = this.def._filterData.input_name0;
                _inputValue = _.first(choices);

                if (!_inputValue) {
                    this.def._filterData.input_name0 = [_.chain(this.def._users).keys().first().value()];
                }

                callback({
                    properties: {
                        _inputType: 'select-single',
                        _inputValue: _.first(this.def._filterData.input_name0),
                    },
                    needsRendering: true,
                });
            }
        };

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            UsernameOperator: UsernameOperator
        });
    });
})(SUGAR.App);

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
         * BoolOperator Management
         *
         * @class Core.FilterOperators.BoolOperator
         * @alias SUGAR.App.FilterOperators.BoolOperator
         *
         * @param {Object} def Operator Definition
         */
        function BoolOperator(def) {
            this.def = def;
        };

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        BoolOperator.prototype.getUpdatedInput = function(callback) {
            let _inputValue = _.first(this.def._filterData.input_name0);

            if (!_inputValue) {
                _inputValue = 'yes';

                this.def._filterData.input_name0 = [_inputValue];
            }

            callback({
                properties: {
                    _inputType: 'enum-single',
                    _inputData: {
                        yes: 'yes',
                        no: 'no'
                    },
                    _inputValue,
                },
            });
        };

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            BoolOperator: BoolOperator
        });
    });
})(SUGAR.App);

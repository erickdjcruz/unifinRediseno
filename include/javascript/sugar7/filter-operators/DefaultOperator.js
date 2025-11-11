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
         * DefaultOperator Management
         *
         * @class Core.FilterOperators.DefaultOperator
         * @alias SUGAR.App.FilterOperators.DefaultOperator
         *
         * @param {Object} def Operator Definition
         */
        function DefaultOperator(def) {
            this.def = def;
        };

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        DefaultOperator.prototype.getUpdatedInput = function(callback) {
            callback({
                properties: {
                    _inputType: 'text',
                    _inputValue: this.def._filterData ? this.def._filterData.input_name0 : '',
                },
            });
        };

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            DefaultOperator: DefaultOperator
        });
    });
})(SUGAR.App);

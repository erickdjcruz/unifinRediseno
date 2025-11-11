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
         * RelateOperator Management
         *
         * @class Core.FilterOperators.RelateOperator
         * @alias SUGAR.App.FilterOperators.RelateOperator
         *
         * @param {Object} def Operator Definition
         */
        function RelateOperator(def) {
            this.def = def;
        };

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        RelateOperator.prototype.getUpdatedInput = function(callback) {
            if (_.contains(['is', 'is_not'], this.def._qualifierName)) {
                const _seedFieldDef = this.def._seedFieldDef;
                const filterData = this.def._filterData;

                _seedFieldDef.type = 'relate';
                _seedFieldDef.id_name = 'id_relate_filter_operator';
                _seedFieldDef.module = _seedFieldDef.ext2 ? _seedFieldDef.ext2 : this.def._seedModule;

                const _inputValue = app.data.createBean(_seedFieldDef.module);

                _inputValue.set(_seedFieldDef.id_name, filterData.input_name0);
                _inputValue.set(_seedFieldDef.name, filterData.input_name1);

                callback({
                    properties: {
                        _inputType: 'relate',
                        _seedFieldDef,
                        _inputValue,
                    },
                    events: {
                        relateChangeEvent: {
                            entity: _inputValue,
                            eventName: 'change:' + _seedFieldDef.name,
                            callback: _.bind(this.relateChanged, this),
                        },
                    },
                });
            } else {
                callback({
                    properties: {
                        _inputType: 'text',
                        _inputValue: this.def._filterData.input_name0,
                    },
                });
            }
        };

        /**
         * Relate operator changes
         *
         * @param {Object} model
         * @return {Object}
         */
        RelateOperator.prototype.relateChanged = function(model) {
            const _filterData = this.def._filterData;
            const _seedFieldDef = this.def._seedFieldDef;

            _filterData.input_name0 = model.get(_seedFieldDef.id_name);
            _filterData.input_name1 = model.get(_seedFieldDef.name);

            return {
                _filterData,
            };
        };

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            RelateOperator: RelateOperator
        });
    });
})(SUGAR.App);

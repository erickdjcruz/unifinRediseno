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
         * Date Operator Management
         *
         * @class Core.FilterOperators.DateOperator
         * @alias SUGAR.App.FilterOperators.DateOperator
         *
         * @param {Object} def Operator Definition
         *
         */
        function DateOperator(def) {
            this.def = def;
        }

        /**
         * Get updated input
         *
         * @param {Function} callback Callback function
         */
        DateOperator.prototype.getUpdatedInput = function(callback) {
            if (this.def._qualifierName === 'between_dates') {
                callback(this.updateBetweenDatesFilterData());
            } else if (this.def._qualifierName === 'between_datetimes') {
                callback(this.updateBetweenDatetimesFilterData());
            } else if (this.def._qualifierName.indexOf('_n_days') != -1) {
                callback(this.updateNthDays());
            } else if (_.contains(['date', 'datetime'], this.def._fieldType)) {
                callback(this.updateDatetimeFilterData());
            } else if (this.def._fieldType === 'datetimecombo') {
                callback(this.updateDatetimecomboFilterData());
            }

            return {};
        };

        /**
         * Between Dates qualifier updated
         *
         * @return {Object}
         */
        DateOperator.prototype.updateBetweenDatesFilterData = function() {
            return {
                properties: {
                    _inputType: 'date-between',
                    _inputValue: this.toDisplayDate(this.def._filterData.input_name0),
                    _inputValue1: this.toDisplayDate(this.def._filterData.input_name1),
                },
            };
        };

        /**
         * Between Datetime qualifier updated
         *
         * @return {Object}
         */
        DateOperator.prototype.updateBetweenDatetimesFilterData = function() {
            return {
                properties: {
                    _inputType: 'datetime-between',
                    _inputValue: this.toDisplayDate(this.def._filterData.input_name0),
                    _inputValue1: this.def._filterData.input_name1,
                    _inputValue2: this.toDisplayDate(this.def._filterData.input_name2),
                    _inputValue3: this.def._filterData.input_name3,
                },
            };
        },

        /**
         * Nth Days qualifier updated
         *
         * @return {Object}
         */
        DateOperator.prototype.updateNthDays = function() {
            return {
                properties: {
                    _inputType: 'text',
                    _inputValue: this.def._filterData.input_name0,
                },
            };
        },

        /**
         * Datetime qualifier updated
         *
         * @return {Object}
         */
        DateOperator.prototype.updateDatetimeFilterData = function() {
            return this.getDatetimeFilterData(this.def._qualifierName, 'date', false);
        },

        /**
         * Datetimecombo qualifier updated
         *
         * @return {Object}
         */
        DateOperator.prototype.updateDatetimecomboFilterData = function() {
            return this.getDatetimeFilterData(this.def._qualifierName, 'datetimecombo', true);
        },

        /**
         * Get datetime filter data
         *
         * @param {string} qualifierName
         * @param {string} dateType
         * @param {boolean} useTime
         *
         * @return {Object}
         */
        DateOperator.prototype.getDatetimeFilterData = function(qualifierName, dateType, useTime) {
            if (qualifierName.indexOf('tp_') === 0) {
                return {
                    properties: {
                        _inputType: 'empty',
                    },
                };
            } else {
                const updatedInputs = {
                    _inputType: dateType,
                    _inputValue: this.toDisplayDate(this.def._filterData.input_name0),
                };

                if (useTime) {
                    updatedInputs._inputValue1 = this.def._filterData.input_name1;
                }

                return {
                    properties: updatedInputs,
                };
            }
        },

        /**
         * Format display date
         *
         * @param {string} filterDate
         *
         * @return {string}
         */
        DateOperator.prototype.toDisplayDate = function(filterDate) {
            let dateFragments = filterDate.match(/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/);

            const yearIdx = 1;
            const monthIdx = 2;
            const dayIdx = 3;

            if (!dateFragments) {
                return filterDate;
            }

            let displayDate = app.user.getPreference('datepref');

            displayDate = displayDate.replace('Y', dateFragments[yearIdx]);
            displayDate = displayDate.replace('m', dateFragments[monthIdx]);
            displayDate = displayDate.replace('d', dateFragments[dayIdx]);

            return displayDate;
        },

        app.filterOperators = app.filterOperators || {};

        app.filterOperators = _.extend(app.filterOperators, {
            DateOperator: DateOperator
        });
    });
})(SUGAR.App);

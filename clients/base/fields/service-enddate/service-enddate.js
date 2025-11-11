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
* @class View.Fields.Base.ServiceEnddateField
* @alias SUGAR.App.view.fields.BaseServiceEnddateField
* @extends View.Fields.Base.DateField
*/
({
    extendsFrom: 'DateField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.viewName = 'detail';
        options.def.readonly = true;
        this._super('initialize', [options]);
        this.setFieldDependencyNames(options);
    },

    /**
     * Will set on the field the field names this field is dependent on.
     * Those field names can come from metadata or if they don't, they
     * will default to some names that have a chance to appear.
     *
     * @param {Object} options A set of parameters that help creating the field
     */
    setFieldDependencyNames: function(options) {
        this.startDateFieldName = options.def.startDateFieldName ||
            'service_start_date';
        this.durationUnitFieldName = options.def.durationUnitFieldName ||
            'service_duration_unit';
        this.durationValueFieldName = options.def.durationValueFieldName ||
            'service_duration_value';
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:' + this.startDateFieldName, this.calculateEndDate, this);
        this.model.on('change:' + this.durationUnitFieldName, this.calculateEndDate, this);
        this.model.on('change:' + this.durationValueFieldName, this.calculateEndDate, this);
    },

    /**
     * Based on what duration unit is set, there are different methods for
     * the sake of manipulating dates. In here one set of methods will be
     * returned (a getter and a setter).
     *
     * @return {Object} A set of two methods for reading and setting the date.
     */
    getMethodNames: function() {
        var unit = this.model.get(this.durationUnitFieldName);
        var nameParts = {
            year: 'FullYear',
            month: 'Month',
            day: 'Date'
        };
        return {
            get: 'get' + nameParts[unit],
            set: 'set' + nameParts[unit]
        };
    },

    /**
     * This method is repsonsible for checking if an enddate can be calculated.
     * In order to calculate an end date all fields the date is dependent on
     * have to have a value set.
     *
     * @return {boolean} True if the end date can be calculated.
     */
    canCalculateEndDate: function() {
        return !!(this.model.get(this.startDateFieldName) &&
            this.model.get(this.durationValueFieldName) &&
            this.model.get(this.durationUnitFieldName));
    },

    /**
     * If possible it will calculate the end date based on a start date and
     * duration. The date is being converted to a regular date format for
     * easier manipulation and after the calculation takes part, the new
     * value will converted to the correct format. A render is being triggered
     * in order to display the new value as soon as possible.
     */
    calculateEndDate: function() {
        // Don't attempt to recalculate the end date for coterm line items. No
        // matter what the user enters for start date for those, we don't want
        // to change this.
        if (!_.isEmpty(this.model.get('add_on_to_id'))) {
            return;
        }
        if (this.canCalculateEndDate()) {
            // Begin with the end date equal to zero hour on the specified start
            // date, correcting any offset added by the javascript Date object
            var endDate = new Date(this.model.get(this.startDateFieldName));
            var offsetInMs = endDate.getTimezoneOffset() * 60000;
            endDate.setTime(endDate.getTime() + offsetInMs);

            // Add the specified duration to the date, then subtract one day
            // as service runs through one day prior
            var methods = this.getMethodNames();
            var newDate = endDate[methods.get]() + this.model.get(this.durationValueFieldName);
            endDate[methods.set](newDate);
            endDate.setDate(endDate.getDate() - 1);

            var formattedEndDate = this.unformat(app.date(endDate));
            this.model.set(this.name, formattedEndDate);
        } else {
            this.model.set(this.name, '');
        }
    }
});

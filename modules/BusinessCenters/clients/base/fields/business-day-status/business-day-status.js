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
 * business-day-status is a field that stores whether a Business Center is open
 * or closed any particular day of the week. It distinguishes between three
 * possible statuses: "Open", "Closed", and "Open 24 Hours". The server
 * only recognizes Open (1) and Closed (0) - "Open 24 Hours" is handled
 * entirely client-side. When receiving a 1, it uses the values of
 * the opening and closing times received to determine
 *
 * @class View.Fields.Base.BusinessCenters.BusinessDayStatusField
 * @alias SUGAR.App.view.fields.BaseBusinessCentersBusinessDayStatusField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * Converts (client-side) model values to dropdown keys.
     *
     * @property {Object}
     */
    _valueToStatus: {
        0: 'Closed',
        1: 'Open',
        2: 'Open 24 Hours',
    },

    /**
     * Defines the start and end times of the day.
     *
     * @property {Object}
     */
    _dayStartEnd: {
        startHour: 0,
        startMinute: 0,
        endHour: 23,
        endMinute: 59
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'enum';
        this.def.options = 'business_day_status_dom';

        // name should be of the format "is_open_<day>"
        this.day = this.name.slice(this.name.lastIndexOf('_') + 1);
        this._openClosedFields = this._getOpenClosedFields();

        if (this.model && this.model.isNew()) {
            this.view.once('render', function() {
                this._handleModelChange(this.model, this.getValue());
            }, this);
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange', arguments);

        var changesToTrack = _.map(this._openClosedFields, function(field) {
            return 'change:' + field;
        });
        this.model.on('sync ' + changesToTrack.join(' '), function() {
            if (this.isOpenAllDay()) {
                this.model.set(this.name, 2);
                this.render();
            }
        }, this);

        var clearTimeDisplays = _.bind(function() {
            this._handleModelChange();
        }, this);
        this.model.on('change:' + this.name, clearTimeDisplays, this);
        this.model.once('sync', clearTimeDisplays, this);
        this.model.on('attributes:revert', clearTimeDisplays, this);
    },

    /**
     * Update the open/close hours as appropriate.
     *
     * @private
     */
    _handleModelChange: function() {
        if (this.disposed) {
            return;
        }

        var options = {};
        var value = this.getValue();

        if (this.isOpenAllDayValue(value)) {
            // set the open/close hours to all day as necessary
            options[this._openClosedFields[0]] = this._dayStartEnd.startHour;
            options[this._openClosedFields[1]] = this._dayStartEnd.startMinute;
            options[this._openClosedFields[2]] = this._dayStartEnd.endHour;
            options[this._openClosedFields[3]] = this._dayStartEnd.endMinute;
            this.model.set(options);
            this._hideTimeselectFields();
        } else if (this.isClosedValue(value)) {
            // note that since you can't update to null, we have to make do with all zeroes
            options[this._openClosedFields[0]] = 0;
            options[this._openClosedFields[1]] = 0;
            options[this._openClosedFields[2]] = 0;
            options[this._openClosedFields[3]] = 0;
            this.model.set(options);
            this._hideTimeselectFields();
        } else {
            this._showTimeselectFields();
        }
    },

    /**
     * Format the given value as a dropdown key for business day statuses.
     *
     * @param {*} value Value to format.
     * @return {string} The dom key for business_day_status_dom.
     */
    format: function(value) {
        // the server will send true or false initially, but client-side it's always stored as 0, 1, or 2.
        // false always means "Closed", but true can mean either "Open" or "Open 24 Hours" - the values
        // of the opening and closing times sent down are used to disambiguate
        if (value === true) {
            value = 1;
        } else if (_.isNull(value) || _.isUndefined(value) || value === false) {
            value = 0;
        }
        return this._valueToStatus[value];
    },

    /**
     * Convert the dropdown key to a value we can store in the model.
     * Even though the is_open_<day> fields are booleans on the server,
     * here we store them as numbers to allow the client side to
     * distinguish between "open part of the day" and "open all day".
     *
     * @param {string} value Dropdown key.
     * @return {number} 0, 1, or 2, for use as lookup keys in _valueToStatus.
     */
    unformat: function(value) {
        switch (value) {
            case 'Open':
                return 1;
            case 'Open 24 Hours':
                // note: the values are booleans on the server, so they will convert 2 to 1.
                return 2;
            case 'Closed':
            default:
                return 0;
        }
    },

    /**
     * Get the current numeric status of this business day.
     *
     * @return {number} The numeric status (0, 1, or 2).
     */
    getValue: function() {
        return this.unformat(this.format(this.model.get(this.name)));
    },

    /**
     * Is this value the special "closed" value?
     *
     * @param {*} value The value to check.
     * @return {boolean} `true` if the given value denotes "closed" and
     *   `false` otherwise.
     */
    isClosedValue: function(value) {
        return value === 0;
    },

    /**
     * Is this value the special "open" value?
     *
     * @param {*} value The value to check.
     * @return {boolean} `true` if the given value denotes "open" and
     *   `false` otherwise.
     */
    isOpenValue: function(value) {
        return value === 1;
    },

    /**
     * Is this value the special "open all day" value?
     *
     * Note that "Open 24 Hours" will result in `false`.
     *
     * @param {*} value The value to check.
     * @return {boolean} `true` if the given value denotes "open all day" and
     *   `false` otherwise.
     */
    isOpenAllDayValue: function(value) {
        return value === 2;
    },

    /**
     * Check if this field should be marked as "Open 24 Hours" based on the
     * open/close time fields of the model.
     *
     * @return {boolean} `true` if this field should be marked as open all day
     *   based on the values of the other open/close time fields on the model.
     */
    isOpenAllDay: function() {
        var openClosedValues = _.map(
            this._openClosedFields,
            function(field) {
                return parseInt(this.model.get(field), 10) || 0;
            },
            this
        );

        return openClosedValues[0] === this._dayStartEnd.startHour &&
            openClosedValues[1] === this._dayStartEnd.startMinute &&
            openClosedValues[2] === this._dayStartEnd.endHour &&
            openClosedValues[3] === this._dayStartEnd.endMinute;
    },

    /**
     * Get the opening and closing hour fields for this business day.
     *
     * @return {string[]} The list of names of open and close fields.
     * @private
     */
    _getOpenClosedFields: function() {
        if (this._openClosedFields) {
            return this._openClosedFields;
        }

        var openHourField = this.day + '_open_hour';
        var openMinuteField = this.day + '_open_minutes';
        var closeHourField = this.day + '_close_hour';
        var closeMinuteField = this.day + '_close_minutes';
        return [openHourField, openMinuteField, closeHourField, closeMinuteField];
    },

    /**
     * This field always has a value
     * @override
     */
    isFieldEmpty: function() {
        return false;
    },

    /**
     * Hide the timeselect fields.
     *
     * @private
     */
    _hideTimeselectFields: function() {
        this._showHideTimeselectFields(false);
    },

    /**
     * Show the timeselect fields.
     *
     * @private
     */
    _showTimeselectFields: function() {
        this._showHideTimeselectFields(true);
    },

    /**
     * Show or hide the timeselect fields.
     *
     * @param {boolean} show If `true`, show. Hide otherwise.
     * @private
     */
    _showHideTimeselectFields: function(show) {
        var openTimeselectField = this.day + '_open';
        var closeTimeselectField = this.day + '_close';
        _.each([openTimeselectField, closeTimeselectField], function(fieldName) {
            var field = this.view.getField(fieldName);
            show ? field.show() : field.hide();
        }, this);
    }
})

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
({
    extendsFrom: 'RecordView',

    _days: [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.model && this.model.addValidationTask(
            'all_open_hours_before_close_hours_' + this.cid,
            _.bind(this.validateBusinessHours, this)
        );
    },

    /**
     * Ensure that when business day status is set to Open for any day,
     * it has valid hours.
     *
     * NOTE: This function is also used by the BusinessCenters create view.
     * Any properties of `this` used in this function must be available
     * to the create view as well.
     *
     * @param {Object} fields List of fields that can be validated.
     * @param {Object} errors Existing validation errors.
     * @param {Function} callback To be called after completion.
     */
    validateBusinessHours: function(fields, errors, callback) {
        var newErrors = {};
        _.each(this._days, function(day) {
            var openName = day + '_open';
            var closeName = day + '_close';
            var open = this.model.get(openName);
            var close = this.model.get(closeName);

            // special case: when closed or open all day, it's acceptable for all four values to be set to zero
            var businessDayStatusName = 'is_open_' + day;
            var businessDayStatus = this.getField(businessDayStatusName);
            var businessDayStatusValue = businessDayStatus.getValue();
            if (businessDayStatus.isOpenAllDayValue(businessDayStatusValue) ||
                businessDayStatus.isClosedValue(businessDayStatusValue)
            ) {
                if (
                    open.hour === close.hour &&
                    open.minute === close.minute &&
                    !open.hour &&
                    !open.minute
                ) {
                    return;
                }
            }

            open = app.date(open);
            close = app.date(close);
            if (!open.isBefore(close)) {
                newErrors[openName] = {
                    ERROR_TIME_IS_BEFORE: this.getField(closeName).label
                };
                newErrors[closeName] = {
                    ERROR_TIME_IS_AFTER: this.getField(openName).label
                };
            }
        }, this);
        callback(null, fields, _.extend(errors, newErrors));
    }
})

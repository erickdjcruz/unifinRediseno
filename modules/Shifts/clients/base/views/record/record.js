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
 * @class View.Views.Base.Shifts.RecordView
 * @alias SUGAR.App.view.views.ShiftsRecordView
 * @extends View.Views.Base.RecordView
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
        'saturday',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.model && this.model.addValidationTask(
            'all_open_hours_before_close_hours_' + this.cid,
            _.bind(this.validateHoursList, this)
        );
    },

    /*
    * Validation of the time fields
    *
    * @param {Object} fields List of fields that can be validated.
    * @param {Object} errors Existing validation errors.
    * @param {Function} callback To be called after completion.
    */
    validateHoursList: function(fields, errors, callback) {
        let newErrors = {};

        _.each(this._days, function(day) {
            _.extend(newErrors, this.validateHours(day));
        }, this);

        callback(null, fields, _.extend(errors, newErrors));
    },

    /*
    * Validation a one day
    *
    * @param {String} day
    * @return {Object} Existing validation errors
    */
    validateHours: function(day) {
        let newErrors = {};
        const openName = day + '_open';
        const closeName = day + '_close';

        const open = app.date(this.model.get(openName));
        const close = app.date(this.model.get(closeName));

        const isOpen = this.model.get('is_open_' + day);

        if (isOpen && !open.isBefore(close)) {
            newErrors[openName] = {
                ERROR_TIME_IS_BEFORE: this.getField(closeName).label,
            };
            newErrors[closeName] = {
                ERROR_TIME_IS_AFTER: this.getField(openName).label,
            };
        }

        return newErrors;
    },
});

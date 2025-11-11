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
 * @class View.Fields.Base.CjTimeField
 * @alias SUGAR.App.view.fields.BaseCjTimeField
 * @extends View.Fields.Base.BaseField
 */
({
    serverTimeFormat: 'HH:mm',

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
        this._super('initialize', [options]);
    },

    /**
     * {@inheritDoc}
     */
    _render: function() {
        this._super('_render');

        if (this.action !== 'edit' && this.action !== 'massupdate') {
            return;
        }

        this._setupTimePicker();
    },

    /**
     * Return user time format.
     * @return {string} User time format.
     */
    getUserTimeFormat: function() {
        return app.user.getPreference('timepref');
    },

    /**
     * Unformats time value for storing in model.
     * @return {string} Unformatted value or `undefined` if value is
     *  an invalid time.
     * @override
     */
    unformat: function(value) {
        if (!value) {
            return value;
        }

        value = app.date(value, app.date.convertFormat(this.getUserTimeFormat()), true);

        if (!value.isValid()) {
            return;
        }

        return value.format(this.serverTimeFormat);
    },

    /**
     * Formats time value according to user preferences.
     * @param {string} value time value to format.
     * @return {Object} On edit mode the returned value is an
     *   object with `time`. On detail mode the returned
     *   value is time, formatted according to user preferences if supplied
     *   value is a valid time, otherwise returned value is `undefined`.
     * @override
     */
    format: function(value) {
        if (!value) {
            return value;
        }

        value = app.date(value, this.serverTimeFormat);

        if (!value.isValid()) {
            return;
        }

        value = value.format(app.date.convertFormat(this.getUserTimeFormat()));

        return value;
    },

    /**
     * Set up the time picker.
     * @protected
     */
    _setupTimePicker: function() {
        var options = {
            timeFormat: this.getUserTimeFormat(),
            step: 15,
            disableTextInput: true,
            className: 'prevent-mousedown',
            appendTo: this.view.$el
        };
        this.$(this.fieldTag).timepicker(options);
    },

    /**
     * {@inheritDoc}
     */
    _dispose: function() {
        if (this.$(this.fieldTag).timepicker) {
            this.$(this.fieldTag).timepicker('remove');
        }

        this._super('_dispose');
    },
});

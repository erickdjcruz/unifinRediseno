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
 * @class View.Fields.Base.TimeselectField
 * @alias SUGAR.App.view.fields.BaseTimeselectField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    fieldTag: 'input[data-type=time]',

    /**
     * @inheritdoc
     *
     * The direction for this field should always be `ltr`.
     */
    direction: 'ltr',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        _.extend(this.events, {
            'click [data-action="show-timepicker"]': 'showTimepicker'
        });

        this._super('initialize', [options]);

        this._hoursField = this.def.fields && this.def.fields[0].name;
        this._minutesField = this.def.fields && this.def.fields[1].name;

        // special handling for default values in create mode
        if (this.model && this.model.isNew() && this.def.default_times) {
            var defaultTimes = this.def.default_times;
            this.model.set(this.name, {hour: defaultTimes.hour, minute: defaultTimes.minute});
            this.model.set(this._hoursField, defaultTimes.hour);
            this.model.set(this._minutesField, defaultTimes.minute);
        }
    },

    /**
     * Binds model changes on this field, taking into account both field tags.
     *
     * @override
     */
    bindDataChange: function() {
        if (!this.model) {
            return;
        }

        // propagate changes from this field to its component hour and minute fields
        this.model.on('change:' + this.name, function(model, value, options) {
            if (this.disposed) {
                return;
            }

            value = app.date(value);
            if (!options || options.updateSubFields !== false) {
                this.model.set(this._hoursField, value.hour());
                this.model.set(this._minutesField, value.minute());
            }

            if (this._inDetailMode()) {
                this.render();
                return;
            }

            this.$(this.fieldTag).val(this.format(value) || '');
        }, this);

        // update this field as the underlying hours and minutes change
        this.model.on('sync change:' + this._hoursField + ' change:' + this._minutesField, function(model, value) {
            var hour = parseInt(this.model.get(this._hoursField), 10) || 0;
            var minute = parseInt(this.model.get(this._minutesField), 10) || 0;
            this.model.set(this.name, {hour: hour, minute: minute}, {updateSubFields: false});
        }, this);

        this.view.on('editable:toggle_fields', function() {
            this.$(this.fieldTag).val(this.format(this.model.get(this.name)));
        }, this);
    },

    /**
     * @inheritdoc
     */
    bindDomChange: function() {
        this._super('bindDomChange');

        if (this._inDetailMode()) {
            return;
        }

        var $timeField = this.$(this.fieldTag);
        var selfView = this.view;

        $timeField.timepicker().on({
            showTimepicker: function() {
                selfView.trigger('list:scrollLock', true);
            },
            hideTimepicker: function() {
                selfView.trigger('list:scrollLock', false);
            },
            focus: _.bind(function() {
                this.handleFocus();
                var date = new Date();
                var value = this.model.get(this.name);
                var hours = value.hour;
                var minutes = value.minute;
                date.setHours(hours, minutes);
                $timeField.timepicker('setTime', date);
            }, this)
        });
    },

    /**
     * Format a time object in the user's time format.
     *
     * @param {Object} value Hour-minute object.
     * @param {number} value.hour The hour.
     * @param {number} value.minute The minute.
     * @return {string|undefined} The given time object in the user's time
     *   format.
     */
    format: function(value) {
        if (_.isNull(value) || _.isUndefined(value) || _.isNaN(value)) {
            return '';
        }

        value = app.date(value);

        if (!value.isValid()) {
            return;
        }

        return value.format(this._getMomentUserTimeFormat());
    },

    /**
     * Convert the string time in the user's time format to an object.
     *
     * Note, the value of this field itself is not used by the server.
     * It's simply designed to propagate to and from the relevant
     * hour and minute fields.
     *
     * @param {string} value Value to store as a time string in the user's
     *   preferred time format.
     * @return {Object|undefined} The date as an object.
     * @return {number} return.hour The hour.
     * @return {number} return.minute The minute.
     */
    unformat: function(value) {
        if (!value) {
            return value;
        }

        value = app.date(value, this._getMomentUserTimeFormat());

        if (!value.isValid()) {
            return;
        }

        return {hour: value.hour(), minute: value.minute()};
    },

    /**
     * Return user time format (for configuring the timepicker).
     *
     * @return {string} User time format.
     * @private
     */
    _getTimepickerUserTimeFormat: function() {
        return app.user.getPreference('timepref');
    },

    /**
     * Return user time format (for use with app.date).
     *
     * @return {string} User time format.
     * @private
     */
    _getMomentUserTimeFormat: function() {
        return app.date.getUserTimeFormat();
    },

    /**
     * Set up the time picker.
     *
     * @protected
     */
    _setupTimePicker: function() {
        var options;
        var localeData = app.date.localeData();
        var lang = {
            am: localeData.meridiem(1, 0, true),
            pm: localeData.meridiem(13, 0, true),
            AM: localeData.meridiem(1, 0, false),
            PM: localeData.meridiem(13, 0, false)
        };

        options = {
            timeFormat: this._getTimepickerUserTimeFormat(),
            scrollDefaultNow: _.isUndefined(this.def.scroll_default_now) ?
                true :
                !!this.def.scroll_default_now,
            step: this.def.step || 15,
            disableTextInput: _.isUndefined(this.def.disable_text_input) ?
                false :
                !!this.def.disable_text_input,
            className: this.def.css_class || 'prevent-mousedown',
            appendTo: this.$el,
            lang: lang
        };

        this.$(this.fieldTag).timepicker(options);
        this._hasTimePicker = true;
    },

    /**
     * Handler to show time picker on icon click.
     *
     * We trigger the focus on element instead of the jqueryfied element, to
     * trigger the focus on the input and avoid the `preventDefault()` imposed
     * in the library.
     */
    showTimepicker: function() {
        this.$(this.fieldTag)[0].focus();
    },

    /**
     * Hide the timepicker.
     */
    hideTimepicker: function() {
        this.$(this.fieldTag).timepicker('hide');
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this._hasTimePicker) {
            this.hideTimepicker();
        }

        this._super('_render');

        if (this._inDetailMode()) {
            return;
        }

        this._setupTimePicker();
    },

    /**
     * Determine if the field is currently in a read-only (detail) mode.
     *
     * @return {boolean}
     * @protected
     */
    _inDetailMode: function() {
        return this.action !== 'edit' && this.action !== 'massupdate';
    },

    /**
     * @inheritdoc
     *
     * Add extra logic to unbind the field tag.
     */
    unbindDom: function() {
        this._super('unbindDom');

        if (this._inDetailMode()) {
            return;
        }

        this.$(this.fieldTag).off();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this._hasTimePicker) {
            this.$(this.fieldTag).timepicker('remove');
        }

        this._super('_dispose');
    }
})

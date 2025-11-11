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
         *
         */
        app.plugins.register('RecurringEvents', ['view', 'field'], {
            /**
            * If this component is a View
            */
            isView: false,

            /**
            * If this component is a Field
            */
            isField: false,

            /**
             * Recurring Event's fields
             */
            recurringEventFields: [
                'repeat_type',
                'repeat_interval',
                'repeat_dow',
                'repeat_selector',
                'repeat_days',
                'repeat_ordinal',
                'repeat_unit',
                'repeat_end_type',
                'repeat_until',
                'repeat_count',
                'recurrence',
            ],

            /**
             * @inheritdoc
             */
            onAttach: function(component, plugin) {
                if (component instanceof app.view.Field) {
                    this.isField = true;
                    this._fieldOnAttach(component);
                } else if (component instanceof app.view.View) {
                    this.isView = true;
                    this._viewOnAttach(component);
                }
            },

            /**
             * onAttach for a Field component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _fieldOnAttach: function(component) {
                this.before('render', function() {
                    if (this.name === 'repeat_interval') {
                        this.prepareRepeatIntervalValues();
                    }

                    if (this.name === 'repeat_ordinal') {
                        this.prepareRepeatOrdinalValues();
                    }
                });
            },

            /**
             * onAttach for a View component
             *
             * @param {App.view.Component} component
             * @protected
             */
            _viewOnAttach: function(component) {
                this.once('init', function() {
                    this._setNoEditFields();
                }, this);
            },

            /**
             * Create the repeat_interval field's values as strings
             */
            prepareRepeatIntervalValues: function() {
                const repeatType = this.getRepeatIntervalKeyword();

                if (!repeatType || _.isEmpty(this.items)) {
                    return;
                }

                _.each(this.items, function(value, key) {
                    let repeatIntervalValue = this.getRepeatIntervalString(key, repeatType);

                    if (!repeatIntervalValue) {
                        return;
                    }

                    this.items[key] = repeatIntervalValue;
                }, this);
            },
            /**
             * Get the repeat type keyword
             */
            getRepeatIntervalKeyword: function() {
                const repeatType = this.model.get('repeat_type');

                if (!repeatType) {
                    return;
                }

                switch (repeatType) {
                    case 'Daily':
                        return app.lang.get('LBL_CALENDAR_DAY');
                    case 'Weekly':
                        return app.lang.get('LBL_CALENDAR_WEEK');
                    case 'Monthly':
                        return app.lang.get('LBL_CALENDAR_MONTH');
                    case 'Yearly':
                        return app.lang.get('LBL_CALENDAR_YEAR');
                    default:
                        return '';
                }
            },

            /**
             * Get the string for the `repeat_interval` field value
             * @param {string} key
             * @param {string} keyword
             */
            getRepeatIntervalString: function(key, keyword) {
                if (!keyword) {
                    return;
                }

                const firstValueKey = '1';
                const interval = key === firstValueKey ? '' : app.lang.get(`LBL_CALENDAR_REPEAT_INTERVAL_VALUE_${key}`);

                return app.lang.get('TPL_REPEAT_INTERVAL',
                    this.module,
                    {
                        repeatIntervalValue: interval,
                        repeatTypeValue: keyword
                    }
                );
            },

            /**
             * Create the repeat_ordinal field's values as strings with the first letter capitalized
             */
            prepareRepeatOrdinalValues: function() {
                if (_.isEmpty(this.items)) {
                    return;
                }

                if (this.action === 'detail') {
                    const repeatOrdinal = this.model.get('repeat_ordinal');

                    if (repeatOrdinal) {
                        this.items[repeatOrdinal] = repeatOrdinal.charAt(0).toUpperCase() + repeatOrdinal.slice(1);
                    }
                } else {
                    const options = app.lang.getAppListStrings(this.def.options);
                    if (options) {
                        this.items = this._filterOptions(options);
                    }
                }
            },

            /**
             * Sets the recurrence fields as no edit fields
             */
            _setNoEditFields: function() {
                if (_.isUndefined(this.noEditFields)) {
                    this.noEditFields = [];
                }

                _.each(this.recurringEventFields, function(field) {
                    this.noEditFields.push(field);
                }, this);
            },

            /**
             * After editing a rrule, if the events can't be generated, reverts model attributes to the previous values
             */
            _handleNoEventsGenerated: function() {
                this.model.revertAttributes();
            }
        });
    });
})(SUGAR.App);

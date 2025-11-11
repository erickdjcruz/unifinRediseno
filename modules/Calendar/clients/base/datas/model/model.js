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
 * @class Model.Datas.Base.CalendarModel
 * @alias SUGAR.App.model.datas.BaseCalendarModel
 * @extends Data.Bean
 */
 ({
    initialize: function(options) {
        app.Bean.prototype.initialize.call(this, options);

        if (this.isNew()) {
            this.listenTo(this, 'change:calendar_module', _.debounce(_.bind(this.prefillDefaultFields, this), 0));
        } else {
            this.once('sync', function() {
                this.listenTo(this, 'change:calendar_module', _.debounce(_.bind(this.prefillDefaultFields, this), 0));
            }, this);
        }

        this.addValidationTask('dates_exists', _.bind(this._validateDateFields, this));
        this.addValidationTask('fields_required', _.bind(this.validateRequiredFields, this));

        this.denyFields = ['date_entered', 'date_modified'];
    },

    /**
     * Returns data of a calendar configuration
     *
     * @return {Object}
     */
    compile: function() {
        return {
            id: this.get('id') || this.get('calendarId'),
            calendarId: this.get('calendarId'),
            userId: this.get('userId'),
            teamId: this.get('teamId'),
        };
    },

    /**
     * Prefill default fields
     *
     * @param {Object} model
     * @param {string} module
     * @param {Object} options
     */
    prefillDefaultFields: function(model, module, options) {
        if (options.revert) {
            return;
        }

        const calendarModule = this.get('calendar_module');
        const moduleMeta = app.metadata.getModule(calendarModule);

        if (moduleMeta) {
            const fieldsMetadata = moduleMeta.fields;

            const fieldsToCalculate = {
                'subject': {
                    fieldTypes: ['varchar', 'name', 'fullname'],
                    keys: ['subject', 'name']
                },
                'event_start': {
                    fieldTypes: ['datetime', 'datetimecombo'],
                    keys: ['startdate', 'datestart', 'start', 'dateentered']
                },
                'event_end': {
                    fieldTypes: ['datetime', 'datetimecombo'],
                    keys: ['enddate', 'dateend', 'end', 'dateentered']
                },
            };

            _.each(fieldsToCalculate, function(fieldToCalculate, fieldName) {
                let bestScore = 0;
                let bestScoreField = '';
                _.each(fieldToCalculate.keys, function(keyToSearch) {
                    _.each(fieldsMetadata, function(fieldDef, fieldDefName) {
                        if (this.denyFields.indexOf(fieldDef.name) == -1 &&
                        fieldToCalculate.fieldTypes.indexOf(fieldDef.type) >= 0) {
                            const fieldScore = this.calculateMatchScore(keyToSearch, fieldDef);
                            if (fieldScore > bestScore) {
                                bestScore = fieldScore;
                                bestScoreField = fieldDefName;
                            }
                        }
                    }, this);
                }, this);
                if (bestScore > 0) {
                    this.set(fieldName, bestScoreField);
                } else {
                    this.unset(fieldName);
                }
            }, this);

            const defaultColor = this.getDefaultBackgroundColor(this.get('calendar_module'));

            let defaultTemplate = '';
            if (_.isArray(fieldsMetadata.name.db_concat_fields)) {
                _.each(fieldsMetadata.name.db_concat_fields, function(field) {
                    defaultTemplate += ' {::' + field + '::}';
                });
            } else {
                defaultTemplate += '{::name::}';
            }
            defaultTemplate += ' {::description::}';

            let defaultParams = {
                duration_minutes: '',
                duration_hours: '',
                duration_days: '',
                color: defaultColor,
                dblclick_event: 'detail:self:id',
                allow_create: true,
                allow_update: true,
                allow_delete: true,
                event_tooltip_template: defaultTemplate,
                day_event_template: defaultTemplate,
                week_event_template: defaultTemplate,
                month_event_template: defaultTemplate,
                agenda_event_template: defaultTemplate,
                timeline_event_template: defaultTemplate,
                schedulermonth_event_template: defaultTemplate,
                ical_event_template: defaultTemplate,
            };
            //Calls and Meetings needs durations instead of event_end
            if (calendarModule == 'Calls' || calendarModule == 'Meetings') {
                defaultParams = _.extend(defaultParams, {
                    event_end: '',
                    duration_minutes: 'duration_minutes',
                    duration_hours: 'duration_hours',
                });
            }

            this.set(defaultParams);
        }
    },

    /**
     * Validate date fields
     *
     * Validate there is start date and an end or some duration
     *
     * @param {Object} fields The list of field names and their definitions.
     * @param {Object} errors The list of field names and their errors.
     * @param {Function} callback Async.js waterfall callback.
     * @private
     */
    _validateDateFields: function(fields, errors, callback) {
        const eventStartGiven = !_.isEmpty(this.get('event_start'));
        const eventEndGiven = !_.isEmpty(this.get('event_end'));
        const durationMinutesGiven = !_.isEmpty(this.get('duration_minutes'));
        const durationHoursGiven = !_.isEmpty(this.get('duration_hours'));
        const durationDaysGiven = !_.isEmpty(this.get('duration_days'));

        if (!eventStartGiven) {
            errors.event_start = app.lang.get('LBL_EVENT_START_ERROR', 'Calendar');
        }
        if (!eventEndGiven && !durationMinutesGiven && !durationHoursGiven && !durationDaysGiven) {
            errors.event_end = app.lang.get('LBL_EVENT_END_ERROR', 'Calendar');
        }

        callback(null, fields, errors);
    },

    /**
     * It will validate required fields.
     *
     * @param {Array} fields The list of fields to be validated.
     * @param {Object} errors A list of error messages.
     * @param {Function} callback Callback to be called at the end of the validation.
     */
    validateRequiredFields: function(fields, errors, callback) {
        if (!app.acl.hasAccess('view', this.get('calendar_module'))) {
            this.set({
                calendar_module: '',
                event_start: '',
                event_end: '',
                duration_minutes: '',
                duration_hours: '',
                duration_days: ''
            });
        }

        _.each(fields, function(field) {
            if (_.has(field, 'required') && field.required) {
                var key = field.name;

                if (!this.get(key)) {
                    errors[key] = errors[key] || {};
                    errors[key].required = true;
                }
            }
        }, this);

        callback(null, fields, errors);
    },

    /**
     * Calculates a value representing how close a field is to a given concept (start date / end date...)
     *
     * @param {string} text         Concept key to search for
     * @param {Object} fieldDef     A field definition
     * @return {number}
     */
    calculateMatchScore: function(text, fieldDef) {
        if (typeof text != 'string' || typeof fieldDef != 'object') {
            return 0;
        }

        const fieldModule = this.get('calendar_module');
        let fieldName = fieldDef.name;

        if (fieldName.substr(fieldName.length - 2) === '_c') {
            fieldName = fieldName.substr(0, fieldName.length - 2);
        }

        fieldName = fieldName.replace(/_/g, '');

        let fieldLabel = fieldDef.vname || '';
        if (!_.isEmpty(fieldLabel)) {
            fieldLabel = app.lang.get(fieldLabel, fieldModule);
            fieldLabel = fieldLabel.toLowerCase().replace(/\s/g, '');
        }

        let score = 0;

        if (fieldName.indexOf(text) >= 0) {
            score += 1;

            //prioritize 'startdate' above 'bigstartfieldname' for given 'startdate'
            if (fieldName.length > text.length) {
                score -= fieldName.length / text.length;
            } else if (fieldName.length < text.length) {
                score -= text.length / fieldName.length;
            }
        }
        if (fieldLabel.indexOf(text) >= 0) {
            score += 1;

            //prioritize 'startdate' above 'bigstartfieldname' for given 'startdate'
            if (fieldName.length > text.length) {
                score -= fieldLabel.length / text.length;
            } else if (fieldName.length < text.length) {
                score -= text.length / fieldLabel.length;
            }
        }
        return score;
    },

    /**
     * Get default color of a module
     *
     * @param {string} module
     * @return {string} Hex format
     */
    getDefaultBackgroundColor: function(module) {
        const defaultStyleSheet = _.find(document.styleSheets, function(styleSheet) {
            return styleSheet.href.indexOf('cache/themes/clients/base/default') > 0;
        });
        const labelRule = _.find(defaultStyleSheet.rules, function(rule) {
            return rule.selectorText == '.label-' + module;
        });
        if (typeof labelRule == 'object') {
            let rgbColor = labelRule.style['background-color'];
            rgbColor = rgbColor.match(/\d+/g);
            rgbColor = _.map(rgbColor, function(color) {
                return parseInt(color);
            });
            return this.rgbToHex(rgbColor);
        }
        return '';
    },

    /**
     * Convert rgb color to hex
     *
     * @param {int} comp
     * @return {string}
     */
    componentToHex: function(comp) {
        let hex = comp.toString(16);
        return hex.length == 1 ? '0' + hex : hex;
    },

    /**
     * Return hex from rgb
     *
     * @param {Array} rgb
     * @return {string}
     */
    rgbToHex: function(rgb) {
        return '#' + this.componentToHex(rgb[0]) + this.componentToHex(rgb[1]) + this.componentToHex(rgb[2]);
    },
});

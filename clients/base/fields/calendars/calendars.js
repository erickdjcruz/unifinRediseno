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
 * @class View.Fields.Base.CalendarsField
 * @alias SUGAR.App.view.fields.BaseCalendarsField
 * @extends View.Fields.Base.BaseField
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.fieldTag = 'div';

        this.events = {
            'click span[name=removeOtherCalendar]': this._removeOtherCalendar,
            'click [name=selectAll]': this.selectCalendars
        };

        this.bkItems = [];
        this.items = [];

        this.selectAllDelayTime = 100;

        this._registerEvents();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        this.listenToOnce(this.context, 'updatefields', _.bind(this.render, this));

        this.listenTo(this.model, 'change:' + this.name, _.bind(this.updateCount, this));
        this.listenTo(this.model, 'change:' + this.name, _.bind(this.checkSelectAllInput, this));
    },

    /**
     * Listening to external events
     */
    _registerEvents: function() {
        this.listenTo(this, 'calendars:selectAll', _.bind(this.selectAllCalendars, this));
    },

    /**
     * Select all calendars
     */
    selectAllCalendars: function() {
        //for low bandwidth, wait for api to finish
        if (typeof this.selectAllCalendarsTimeout !== 'undefined') {
            clearTimeout(this.selectAllCalendarsTimeout);
            this.selectAllCalendarsTimeout = null;
        }
        if (typeof this.request.status == 'undefined') {
            this.selectAllCalendarsTimeout =
            _.delay(_.bind(this.selectAllCalendars, this), this.selectAllDelayTime);
            return;
        }

        let configurations = app.utils.deepCopy(this.items) || [];

        configurations = _.map(configurations, function(config) {
            return {
                calendarId: config.calendarId,
                userId: config.userId,
                teamId: config.teamId,
                selected: true
            };
        });

        let data = {
            myCalendars: this.model.get('myCalendars') || [],
            otherCalendars: this.model.get('otherCalendars') || []
        };
        data[this.name] = configurations;

        if (this.view.name !== 'calendar-scheduler-dashlet') {
            app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, data);
        }

        this.model.set(this.name, configurations);
        this.updateCount();

        this.context.trigger('calendars:cache:force-refresh');
    },

    /**
     * Remove Other Calendar
     *
     * @param {Event} e
     */
    _removeOtherCalendar: function(e) {
        const calendarName = $(e.currentTarget).parent()[0].innerText.bold();

        app.alert.show('message', {
            level: 'confirmation',
            messages:
                app.lang.getModString('LBL_CALENDAR_REMOVE_OTHER_CALENDAR', 'Calendar', {calendarName: calendarName}),
            autoClose: false,
            onConfirm: _.bind(function() {
                const $input = $(e.currentTarget).parent().find('input');
                const calendarId = $input.attr('calendarId');
                const userId = $input.attr('userId');
                const teamId = $input.attr('teamId');
                const newCalendars = _.filter(this.model.get(this.name), function(calendar) {
                    if (
                        calendar.calendarId === calendarId &&
                        calendar.userId === userId &&
                        calendar.teamId === teamId
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                });

                const dataToSaveInStorage = {
                    myCalendars: this.model.get('myCalendars') || [],
                    otherCalendars: newCalendars
                };

                if (this.view.name !== 'calendar-scheduler-dashlet') {
                    app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                }
                this.model.set(this.name, newCalendars);

                this.render();
            }, this)
        });
    },

    /**
     * Update Count in UI
     */
    updateCount: function() {
        const $field = this.$el.parent().parent();
        const data = this.model.get(this.name);

        if (Array.isArray(data) && data.length > 0) {
            let numberOfCalendarsSelected;

            if (this.name == 'otherCalendars') {
                const calendarsSelected = _.filter(data, function(calendar) {
                    return calendar.selected === true;
                });
                numberOfCalendarsSelected = calendarsSelected.length;
            } else {
                numberOfCalendarsSelected = data.length;
            }

            if (numberOfCalendarsSelected == 0) {
                $field.find('span[name=count]').html(' ');
            } else {
                $field.find('span[name=count]').html('(' + numberOfCalendarsSelected + ') ');
            }
        } else {
            $field.find('span[name=count]').html(' ');
        }
    },

    /**
     * Check the current status of Select All field
     */
    checkSelectAllInput: function() {
        const data = this.model.get(this.name);
        const items = app.utils.deepCopy(this.bkItems);

        if (_.isArray(data) && data.length > 0) {
            let numberOfCalendarsSelected;
            if (this.name === 'otherCalendars') {
                const calendarsSelected = _.filter(data, function(calendar) {
                    return calendar.selected === true;
                });
                numberOfCalendarsSelected = calendarsSelected.length;

            } else {
                numberOfCalendarsSelected = data.length;
            }

            if (numberOfCalendarsSelected === 0 || numberOfCalendarsSelected !== items.length) {
                this.checked = false;
                this.$('#' + this.name).prop('checked', false);
            } else {
                this.checked = true;
                this.$('#' + this.name).prop('checked', true);
            }
        } else {
            this.checked = false;
            this.$('#' + this.name).prop('checked', false);
        }
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        this.loadEnumOptions();
        this.updateCount();
        this.resizeCalendarsList();
    },

    /**
     * Resize calendars list
     */
    resizeCalendarsList: function() {
        const windowHeight = $(window).height();
        const headerHeight = $('#header .navbar').outerHeight();
        const footerHeight = $('footer').outerHeight();
        const miniCalendarHeight = 300;
        const remainingHeight = windowHeight - headerHeight - miniCalendarHeight - footerHeight;

        $('.calendarsWrapper').css('max-height', remainingHeight + 'px');
        this.checkSelectAllInput();
    },

    /**
     * Select all calendars based on input field
     *
     * @param {Event} e
     */
    selectCalendars: function(e) {
        const currentTarget = e.currentTarget.id;

        if (currentTarget === 'myCalendars') {
            const checkedMyCalendars = this.$el.find('#myCalendars').prop('checked');

            if (checkedMyCalendars) {
                this.$('input[type=checkbox]').prop('checked', true);
                this.selectAllCalendars();
            } else {
                this.$('input[type=checkbox]').prop('checked', false);
                this.uncheckAllCalendars(currentTarget);
            }
        } else {
            const checkedOtherCalendars = this.$el.find('#otherCalendars').prop('checked');

            if (checkedOtherCalendars) {
                this.$('input[type=checkbox]').prop('checked', true);
                this.selectAllCalendars();
            } else {
                this.$('input[type=checkbox]').prop('checked', false);
                this.uncheckAllCalendars(currentTarget);
            }
        }
    },

    /**
     * Clear calendar list
     *
     * @param {string} currentTarget
     */
    uncheckAllCalendars: function(currentTarget) {
        let fieldToClear = currentTarget;

        const currentCalendars = app.utils.deepCopy(this.model.get(fieldToClear));

        //useful for clear otherCalendars
        if (fieldToClear === 'myCalendars') {
            this.model.set(fieldToClear, []);
        } else {
            const updatedCalendars = _.map(currentCalendars, function(calendar) {
                calendar.selected = false;
                return calendar;
            });
            this.model.set(fieldToClear, updatedCalendars);
        }


        if (this.view.name !== 'calendar-scheduler-dashlet') {
            let cache = app.user.lastState.get(this.view.keyToStoreCalendarConfigurations);
            if (fieldToClear === 'myCalendars') {
                cache[fieldToClear] = [];
            } else {
                cache[fieldToClear] = this.model.get(fieldToClear);
            }

            app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, cache);
        }
    },

    /**
     * @inheritdoc
     */
    loadEnumOptions: function() {
        let calendarPayload = {
            calendarType: this.def.calendar_type,
            calendarFilter: this.def.calendar_filter,
            viewSource: this.def.view_source,
            recordModule: this.view.module,
            layout: app.controller.context.get('layout')
        };

        calendarPayload.calendars = app.Calendar.utils.getConfigurationsByKey(
            this.view.keyToStoreCalendarConfigurations,
            this.name
        );

        if (this.view.name == 'calendar-scheduler-dashlet' && this.name == 'otherCalendars') {
            calendarPayload.calendars = this.model.get('otherCalendars') || [];
        }

        this.request = app.api.call('create', app.api.buildURL('Calendar/calendars'), calendarPayload, {
            success: _.bind(function(data) {
                let calendars;

                if (this.disposed) {
                    return;
                }

                //let the scheduler know all available modules
                if (this.name == 'myCalendars') {
                    let myCalendars = _.values(data.calendars);
                    let myCalendarsModulesAdded = [];
                    let myCalendarsFiltered = [];
                    _.each(myCalendars, function(myCalendar) {
                        if (myCalendarsModulesAdded.indexOf(myCalendar.module) == -1) {
                            myCalendarsFiltered.push(myCalendar);
                            myCalendarsModulesAdded.push(myCalendar.module);
                        }
                    });
                    this.context.set('myAvailableCalendars', myCalendarsFiltered);
                }

                //remove deleted calendars from local storage
                let configurationsToShow = app.Calendar.utils.getConfigurationsByKey(
                    this.view.keyToStoreCalendarConfigurations
                );

                if (this.name == 'myCalendars') {
                    let myConfigurationsToShow = configurationsToShow.myCalendars;
                    let myConfigurationsToShowVerified =
                    _.filter(myConfigurationsToShow, function(storedCalendar) {
                        let calendarStillExists = false;
                        _.each(data.calendars, function(calendarFromDb) {
                            if (calendarFromDb.calendarId === storedCalendar.calendarId &&
                                (_.isEmpty(calendarFromDb.userId) ||
                                    storedCalendar.userId === calendarFromDb.userId)
                            ) {
                                calendarStillExists = true;
                            }
                        });
                        return calendarStillExists;
                    });

                    if (configurationsToShow.myCalendars.length !== myConfigurationsToShowVerified.length) {
                        const dataToSaveInStorage = {
                            myCalendars: myConfigurationsToShowVerified,
                            otherCalendars: configurationsToShow.otherCalendars || []
                        };

                        if (this.view.name !== 'calendar-scheduler-dashlet') {
                            app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                        }

                        //trigger count update
                        this.model.set(this.name, myConfigurationsToShowVerified);
                    }

                    calendars = data.calendars;
                } else if (this.name == 'otherCalendars') {
                    let otherConfigurationsToShow;
                    let otherConfigurationsToShowVerified;

                    if (this.view.name == 'calendar-scheduler-dashlet') {
                        otherConfigurationsToShow = this.model.get('otherCalendars') || [];
                        otherConfigurationsToShowVerified =  this.verifyConfigurations(otherConfigurationsToShow, data);
                    } else {
                        otherConfigurationsToShow = configurationsToShow.otherCalendars;
                        otherConfigurationsToShowVerified =  this.verifyConfigurations(otherConfigurationsToShow, data);

                        if (configurationsToShow.otherCalendars.length !== otherConfigurationsToShowVerified.length) {
                            const dataToSaveInStorage = {
                                myCalendars: configurationsToShow.myCalendars || [],
                                otherCalendars: otherConfigurationsToShowVerified
                            };
                            app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);

                            //trigger count update
                            this.model.set(this.name, otherConfigurationsToShowVerified);
                        }
                    }

                    calendars = data.calendars;
                }

                let calendarItems = [];
                _.each(calendars, function(calendar) {
                    calendar.userName = DOMPurify.sanitize(calendar.userName);
                    let cal = {
                        id: calendar.calendarId,
                        name: calendar.name,
                        calendarId: calendar.calendarId,
                        selected: this.calendarIsSelected(calendar),
                        color: calendar.color,
                        user: calendar.user,
                        userId: calendar.userId,
                        userName: calendar.userName,
                        team: calendar.team,
                        teamId: calendar.teamId,
                        teamName: calendar.teamName
                    };

                    if (!_.isEmpty(calendar.userId) && _.isEmpty(calendar.teamId)) {
                        if (this.name === 'myCalendars' && this.view.name === 'calendar-scheduler-dashlet') {
                            cal.name = calendar.name;
                            cal.id = calendar.calendarId + ':user:current_user';
                        } else {
                            cal.name = calendar.userName + '\'s ' + calendar.name;
                            cal.id = calendar.calendarId + ':user:' + calendar.userId;
                        }
                    } else if (_.isEmpty(calendar.userId) && !_.isEmpty(calendar.teamId)) {
                        cal.name = calendar.teamName + '\'s ' + calendar.name;
                        cal.id = calendar.calendarId + ':team:' + calendar.teamId;
                    }

                    if (!_.isEmpty(calendar.userId)) {
                        if (typeof calendar.userColor == 'string' && !_.isEmpty(calendar.userColor)) {
                            cal.dotColor = calendar.userColor;
                        } else {
                            cal.dotColor = app.Calendar.utils.pastelColor(calendar.userId);
                        }
                    }
                    calendarItems.push(cal);
                }, this);

                this.items = calendarItems;
                this.bkItems = app.utils.deepCopy(calendarItems);

                this._render();
            }, this),
            error: function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }
        });
    },

    /**
     * Show calendar definition
     *
     * @param {Object} calendar
     * @return {boolean}
     */
    calendarIsSelected: function(calendar) {
        let calendarToShow;

        if (this.name == 'myCalendars') {
            calendarToShow = _.find(this.model.get(this.name), function(calendarConfig) {
                return calendarConfig.calendarId === calendar.calendarId;
            });
        } else {
            if (this.view.name == 'main-panel') {
                calendarToShow = _.find(this.model.get(this.name), _.bind(function(calendarConfig) {
                    let configurationsInStorage =
                        app.Calendar.utils.getConfigurationsByKey(this.view.keyToStoreCalendarConfigurations);
                    configurationsInStorage = configurationsInStorage.myCalendars
                        .concat(configurationsInStorage.otherCalendars);

                    let configurationInStorage = _.find(configurationsInStorage, function(configStorage) {
                        if (calendarConfig.calendarId === configStorage.calendarId &&
                            (_.isEmpty(calendarConfig.userId) || configStorage.userId === calendarConfig.userId) &&
                            (_.isEmpty(calendarConfig.teamId) || configStorage.teamId === calendarConfig.teamId)
                        ) {
                            return true;
                        }
                    });

                    if (typeof configurationInStorage != 'undefined') {
                        if (!_.isEmpty(calendarConfig.teamId)) {
                            return calendarConfig.calendarId === calendar.calendarId &&
                                calendarConfig.teamId === calendar.teamId &&
                                configurationInStorage.selected === true;

                        } else if (!_.isEmpty(calendarConfig.userId)) {
                            return calendarConfig.calendarId === calendar.calendarId &&
                                calendarConfig.userId === calendar.userId &&
                                configurationInStorage.selected === true;
                        }
                    }

                    return false;
                }, this));
            } else if (this.view.name == 'calendar-scheduler-dashlet') {
                calendarToShow = _.find(this.model.get(this.name), _.bind(function(calendarConfig) {

                    if (!_.isEmpty(calendarConfig.teamId)) {
                        return calendarConfig.calendarId === calendar.calendarId &&
                            calendarConfig.teamId === calendar.teamId &&
                            calendarConfig.selected === true;

                    } else if (!_.isEmpty(calendarConfig.userId)) {
                        return calendarConfig.calendarId === calendar.calendarId &&
                        calendarConfig.userId === calendar.userId &&
                        calendarConfig.selected === true;
                    }

                    return false;
                }, this));
            }
        }

        if (typeof calendarToShow == 'undefined') {
            return false;
        }
        return true;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        const items = app.utils.deepCopy(this.bkItems);
        this.markSelectedOptions(items);
        this.checkSelectAllInput();

        this._super('_render');
    },

    /**
     * Mark selected calendars
     *
     * @param {Array} items
     * @return {Array}
     */
    markSelectedOptions: function(items) {
        _.each(items, function(item) {
            item.selected = this.calendarIsSelected(item);
        }, this);

        return items;
    },

    /**
     * Handle input change
     *
     * @param {Event} e
     */
    handleInputChange: function(e) {
        const searchFor = e.currentTarget.value.toLowerCase();
        const items = app.utils.deepCopy(this.bkItems);

        let newItems = [];
        _.each(items, function(item) {
            if (item.name.toLowerCase().indexOf(searchFor) >= 0) {
                newItems.push(item);
            }
        }, this);

        this.items = this.markSelectedOptions(newItems);

        const $items = app.template.getField('calendars', 'items.calendars')(this);

        this.$('ul').html($items);

        this.$('input[type=checkbox]').on('change', _.bind(this.handleCheckboxChange, this));
    },

    /**
     * Handle checkbox change
     *
     * @param {Event} e
     */
    handleCheckboxChange: function(e) {
        if (e.currentTarget.id === 'myCalendars' || e.currentTarget.id === 'otherCalendars') {
            return;
        }
        const $checkbox = $(e.currentTarget).closest('li').find('input[type=checkbox]');

        const calendarId = $checkbox.attr('calendarId');
        const userId = $checkbox.attr('userId');
        const teamId = $checkbox.attr('teamId');
        const calendar = {
            calendarId: calendarId,
            userId: userId,
            teamId: teamId
        };

        this.updateCalendars(calendar, $checkbox[0].checked);
    },

    /**
     * Update model AND storage with calendars selected.
     * Specific actions are going to be executed in each view where the field is added
     *
     * @param {Object} calendar
     * @param {boolean} checked
     */
    updateCalendars: function(calendar, checked) {
        let dataToSaveInStorage;
        let configurations;

        const calendarId = calendar.calendarId;
        const userId = calendar.userId;
        const teamId = calendar.teamId;

        const myConfigurations = this.model.get('myCalendars') || [];
        const otherConfigurations = this.model.get('otherCalendars') || [];

        let calendarsOnModel = this.model.get(this.name);

        if (Array.isArray(calendarsOnModel)) {
            calendarsOnModel = app.utils.deepCopy(calendarsOnModel);
        } else {
            calendarsOnModel = [];
        }

        if (this.name == 'myCalendars') {
            if (checked) {
                const tempConfig = {
                    calendarId: calendarId,
                    userId: userId,
                    teamId: ''
                };

                const newCalendarsList = calendarsOnModel.concat([tempConfig]);
                dataToSaveInStorage = {
                    myCalendars: newCalendarsList,
                    otherCalendars: otherConfigurations
                };

                if (this.view.name != 'calendar-scheduler-dashlet') {
                    app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                }

                this.model.set(this.name, newCalendarsList);
            } else {
                configurations = _.filter(calendarsOnModel, _.bind(function(calendar) {
                    if (calendar.calendarId === calendarId) {
                        return false;
                    }
                    return true;
                }, this));

                dataToSaveInStorage = {
                    myCalendars: configurations,
                    otherCalendars: otherConfigurations
                };

                if (this.view.name != 'calendar-scheduler-dashlet') {
                    app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                }

                this.model.set(this.name, configurations);
            }
        } else {
            if (checked) {
                configurations = _.map(calendarsOnModel, _.bind(function(calendar) {
                    if (calendar.calendarId === calendarId) {
                        if (
                            (!_.isEmpty(calendar.userId) && calendar.userId === userId) ||
                            (!_.isEmpty(calendar.teamId) && calendar.teamId === teamId)
                        ) {
                            calendar.selected = true;
                        }
                    }
                    return calendar;
                }, this));

                dataToSaveInStorage = {
                    myCalendars: myConfigurations,
                    otherCalendars: configurations
                };

                if (this.view.name != 'calendar-scheduler-dashlet') {
                    app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                }

                this.model.set(this.name, configurations);
            } else {
                configurations = _.map(calendarsOnModel, _.bind(function(calendar) {
                    if (calendar.calendarId === calendarId) {
                        if (
                            (!_.isEmpty(calendar.userId) && calendar.userId === userId) ||
                            (!_.isEmpty(calendar.teamId) && calendar.teamId === teamId)
                        ) {
                            calendar.selected = false;
                        }
                    }
                    return calendar;
                }, this));

                dataToSaveInStorage = {
                    myCalendars: myConfigurations,
                    otherCalendars: configurations
                };

                if (this.view.name != 'calendar-scheduler-dashlet') {
                    app.user.lastState.set(this.view.keyToStoreCalendarConfigurations, dataToSaveInStorage);
                }

                this.model.set(this.name, configurations);
            }
        }
    },

    /**
     * Verify configuration
     *
     * @param {Array} configurationToShow
     * @param {Array} data
     * @return {Array}
     */
    verifyConfigurations: function(configurationToShow, data) {
        _.filter(configurationToShow, function(storedCalendar) {
            let calendarStillExists = false;
            _.each(data.calendars, function(calendarFromDb) {
                if (calendarFromDb.calendarId === storedCalendar.calendarId &&
                    (_.isEmpty(calendarFromDb.userId) ||
                        storedCalendar.userId === calendarFromDb.userId) &&
                    (_.isEmpty(calendarFromDb.teamId) ||
                        storedCalendar.teamId === calendarFromDb.teamId)
                ) {
                    calendarStillExists = true;
                }
            });
            return calendarStillExists;
        });

        return configurationToShow;
    },

    /**
     * @inheritdoc
     */
    bindDomChange: function() {
        this.$('input').on('keyup', _.debounce(_.bind(this.handleInputChange, this), 100));

        this.$('input[type=checkbox]').on('change', _.bind(this.handleCheckboxChange, this));
    }
});

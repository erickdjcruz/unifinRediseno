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
 * @class View.Views.Base.Calendar.MainPanelView
 * @alias SUGAR.App.view.views.BaseCalendarMainPanelView
 * @extends View.Views.Base.View
 */
 ({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.events = this.events || {};
        this.events['click span[name=addCalendar]'] = 'addCalendar';

        this.componentStorageKey = 'main-panel';
        this.keyToStoreCalendarConfigurations = app.Calendar.utils.buildUserKeyForStorage(this.componentStorageKey);

        this.miniCalendar = null;

        this.delaySelectMyCalendarsTime = 100;

        this._super('initialize', [options]);

        this.listenTo(this.context, 'scheduler:view:changed', _.bind(this.updateMiniCalendar, this));
    },

    /**
     * Update the mini calendar
     */
    updateMiniCalendar: function() {
        let schedulerView = this.layout.getComponent('scheduler');
        let startDate = schedulerView.scheduler.date();

        this.miniCalendar.value(startDate);
    },

    /**
     * @override
     */
    bindDataChange: function() {
        this.model.on('change:myCalendars', this.reloadCalendar, this);
        this.model.on('change:otherCalendars', this.reloadCalendar, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        let calendarsSaved = app.user.lastState.get(this.keyToStoreCalendarConfigurations);
        if (typeof calendarsSaved == 'undefined') {
            this.selectMyCalendars();
        }

        calendarsSaved = calendarsSaved || {};

        const myConfigurations = calendarsSaved.myCalendars || [];
        const otherConfigurations = calendarsSaved.otherCalendars || [];

        this.model.set({
            myCalendars: myConfigurations,
            otherCalendars: otherConfigurations
        });

        this._super('_render');

        if (typeof kendo == 'object') {
            this.culturePreferences();
            this.loadMiniCalendar();
            this.calculateCalendarPosition();
        } else {
            $.getScript('cache/include/javascript/sugar_grp_calendar.js', _.bind(function() {
                this.culturePreferences();
                this.loadMiniCalendar();
                this.calculateCalendarPosition();
            }, this));
        }
    },

    /**
     * Select my calendars
     */
    selectMyCalendars: function() {
        if (typeof this.delaySelectingMyCalendars !== 'undefined') {
            clearTimeout(this.delaySelectingMyCalendars);
            this.delaySelectingMyCalendars = null;
        }

        let myCalendars = this.getField('myCalendars');
        if (myCalendars instanceof app.view.Field) {
            if (myCalendars.$el.find('input[type=checkbox]').length == 0) {
                this.delaySelectingMyCalendars =
                    _.delay(_.bind(this.selectMyCalendars, this), this.delaySelectMyCalendarsTime);
            } else {
                myCalendars.$el.find('input[type=checkbox]').attr('checked', true);
                myCalendars.trigger('calendars:selectAll');
            }
        } else {
            this.delaySelectingMyCalendars =
                    _.delay(_.bind(this.selectMyCalendars, this), this.delaySelectMyCalendarsTime);
        }
    },

    /**
     * Load mini calendar
     */
    loadMiniCalendar: function() {
        const userDatePref = app.Calendar.utils.getKendoDateMapping(
            app.user.getPreference('datepref'), 'fullVerboseMonth'
        );

        this.$('[data-content=mini-calendar]').kendoCalendar({
            change: _.bind(function changeMiniCalendar() {
                let schedulerView = this.layout.getComponent('scheduler');

                if (schedulerView.scheduler._selectedView.name === 'month') {
                    schedulerView.scheduler.view('day');
                    schedulerView.$el.find('.k-dropdown').trigger('change');
                }

                let dateSelected = new Date(moment(this.miniCalendar.current()).format('YYYY/MM/DD'));
                schedulerView.scheduler.date(dateSelected);
                schedulerView.scheduler.select({
                    events: [],
                    start: dateSelected,
                    end: dateSelected,
                    groupIndex: 0
                });

                let loadedEventsStart = new Date(schedulerView._eventsLoaded.startDate);
                let loadedEventsEnd = new Date(schedulerView._eventsLoaded.endDate);

                if (loadedEventsStart > dateSelected || loadedEventsEnd < dateSelected) {
                    schedulerView.trigger('calendar:reload');
                }
            }, this),
            footer: kendo.format(`{0: dddd, ${userDatePref}}`,  new Date())
        });

        this.$('.k-icon.k-i-arrow-60-right')
            .removeClass('k-icon')
            .removeClass('k-i-arrow-60-right')
            .addClass('sicon')
            .addClass('sicon-chevron-right');
        this.$('.k-icon.k-i-arrow-60-left')
            .removeClass('k-icon')
            .removeClass('k-i-arrow-60-left')
            .addClass('sicon')
            .addClass('sicon-chevron-left');

        this.miniCalendar = this.$('[data-content=mini-calendar]').data('kendoCalendar');
    },

    /**
     * Calculate calendar position
     *
     * When refresh on this page, we might get into here before kendo css loaded,
     * so no positioninig possible.
     * solution is to generate a resonable amount of tries before quit
     */
    calculateCalendarPosition: function() {
        if (this.$el.css('background-color') == 'rgba(0, 0, 0, 0)') {
            /**
             * A count down for number of tries. Wait for slow networks
             */
            let resonableLimit = 50;
            /**
             * Try interval
             */
            const timeToWait = 100;

            const waitForCssToLoad = setInterval(function() {
                if (this.$el.css('background-color') != 'rgba(0, 0, 0, 0)') {
                    this.positionMiniCalendar();
                    clearInterval(waitForCssToLoad);
                }

                if (resonableLimit == 0) {
                    clearInterval(waitForCssToLoad);
                }
                resonableLimit--;
            }.bind(this), timeToWait);
        } else {
            this.positionMiniCalendar();
        }
    },

    /**
     * Position mini calendar
     */
    positionMiniCalendar: function() {
        this.$('[data-content=mini-calendar]').css('width', '100%');
        this.$('[data-content=mini-calendar] .k-calendar-view').css('width', '100%');
    },

    /**
     * Add other calendar drawer
     */
    addCalendar: function() {
        app.drawer.open(
            {
                layout: 'add-calendar',
                context: {
                    module: 'Calendar',
                    mixed: true,
                    mainCalendarSource: true
                }
            },
            _.bind(function closeCalendarAdd(calendar) {
                if (typeof calendar == 'undefined') {
                    return;
                }

                const calendarsInLS = app.user.lastState.get(this.keyToStoreCalendarConfigurations);
                if (typeof (calendarsInLS) !== 'undefined' && typeof calendarsInLS.otherCalendars !== 'undefined') {
                    let calendarAlreadyAdded = false;
                    _.each(calendarsInLS.otherCalendars, function searchCalendar(calendarInLS) {
                        if (calendarInLS.calendarId === calendar.calendarId &&
                            ((_.isEmpty(calendarInLS.teamId) && calendarInLS.userId === calendar.userId) ||
                            (_.isEmpty(calendarInLS.userId) && calendarInLS.teamId === calendar.teamId))
                        ) {
                            calendarAlreadyAdded = true;
                        }
                    });
                    if (calendarAlreadyAdded) {
                        app.alert.show('calendar-already-added', {
                            level: 'info',
                            messages: app.lang.getModString('LBL_CALENDAR_CALENDAR_ALREADY_ADDED', 'Calendar'),
                            autoClose: true
                        });
                        return;
                    }
                }

                calendar.selected = true;
                let otherCalendarsList = this.model.get('otherCalendars');
                otherCalendarsList.push(calendar);

                app.user.lastState.set(this.keyToStoreCalendarConfigurations, {
                    myCalendars: this.model.get('myCalendars'),
                    otherCalendars: this.model.get('otherCalendars')
                });

                this.model.trigger('change:otherCalendars');

                const field = this.getField('otherCalendars');
                field.render();
            }, this)
        );
    },

    /**
     * Reload calendar
     */
    reloadCalendar: function() {
        let scheduler = this.layout.getComponent('scheduler');
        if (scheduler.$el.html() == '') {
            return;
        }
        const myConfigurations = this.model.get('myCalendars') || [];
        const otherConfigurations = this.model.get('otherCalendars') || [];

        const otherConfigurationsSelected = _.filter(otherConfigurations, function(configuration) {
            return configuration.selected;
        });

        const calendarsList = myConfigurations.concat(otherConfigurationsSelected);
        const calendarsModels = _.map(calendarsList, function(calendarData) {
            return app.data.createBean('Calendar', calendarData);
        });
        scheduler.calendars = app.data.createBeanCollection('Calendar', calendarsModels);

        scheduler.trigger('calendar:reconfigure');
    },

    /**
     * Setup the culture preference
     */
    culturePreferences: function() {
        const weekStart = parseInt(app.user.getPreference('first_day_of_week'), 10);
        kendo.culture('en-US');

        if (weekStart) {
            kendo.culture().calendar.firstDay = weekStart;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.miniCalendar) {
            this.miniCalendar.destroy();
            this.miniCalendar = null;
        }

        this.context.off('scheduler:view:changed');

        this._super('_dispose');
    }
});

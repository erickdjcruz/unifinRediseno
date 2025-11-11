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
 * @class View.Views.Base.Calendar.SchedulerView
 * @alias SUGAR.App.view.views.BaseCalendarSchedulerView
 * @extends View.Views.Base.View
 */
 ({
    className: 'calendar-scheduler',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._initVars();
        this._super('initialize', [options]);

        this.positionLegend = _.bind(this.positionLegend, this);

        this.setSchedulerEvents();
    },

    /**
     * Initialize parameters
     */
    _initVars: function() {
        /**
         * A reference to the Kendo Scheduler component
         */
        this.scheduler = null;

        /**
         * Reference to toolbar SubView
         */
        this.toolbarView = null;

        /**
         * Reference to popul used to confirm email sending
         */
        this.confirmationPopupView = null;

        /**
         * The event focused at a given moment
         */
        this._selectedState = {};

        /**
         * Calendar definitions fetched for calendars in this component.
         * Contains all fields from db
         */
        this.calendarDefs = [];
        /**
         * Raw events got from the last fetch
         * The start and end dates show the interval of the fetch
         */
        this._eventsLoaded = {
            startDate: '',
            endDate: '',
            events: []
        };

        /**
         * Calendar wrapper id. Unique identifier of the scheduler
         */
        this._schedulerCssId = 'scheduler_' + this.cid;

        /**
         * Event deleted. Store it at this level to have access to it anywhere needed until it's gone
         */
        this._deletedEvent = {};

        /**
         * Flage to indicate the action in progress. Store it at this level to have access to it everywhere
         */
        this._isDeleteAction = false;

        /**
         * Raw views which can be seen in the calendar.
         */
        this._allPossibleViews = ['day', 'workWeek', 'week', 'expandedMonth', 'agenda', 'timeline', 'monthSchedule'];

        this.DAY_VIEW = 'day';
        this.WEEK_VIEW = 'week';
        this.WORK_WEEK_VIEW = 'workWeek';
        this.EXPANDED_MONTH_VIEW = 'expandedMonth';
        this.AGENDA_VIEW = 'agenda';
        this.TIMELINE_VIEW = 'timeline';
        this.MONTH_SCHEDULE_VIEW = 'monthSchedule';

        /**
         * Calendar's views
         */
        this.views = this.setupCalendarViews();

        /**
         * URI to get events
         */
        this.eventsURL = 'Calendar/getEvents';

        /**
         * Number of next months to load events
         */
        this.nrOfNextMonthsToLoadEvents = 1;

        /**
         * Calendars to show on this view
         */
        this.calendars = app.data.createBeanCollection('Calendar', this.options.context.get('calendars'));

        /**
         * Location of the scheduler: main, record or records
         */
        this.location = this.options.context.get('location') || '';

        /**
         * Options for kendo scheduler
         */
        this.customKendoOptions = this.options.context.get('customKendoOptions') || {};

        /**
         * Flag whether to load calendars or not
         */
        this.options.skipFetch = this.options.context.get('skipFetch') || false;

        this.keyToStoreCalendarConfigurations = this.options.context.get('keyToStoreCalendarConfigurations');
        this.keyToStoreCalendarView = app.Calendar.utils.buildUserKeyForStorage(this.location);
        this.keyToStoreCalendarBusinessHours = 'calendarBusinessHours';

        this._setBusinessHours();
    },

    /**
     * Adds events this component will listen for
     */
    setSchedulerEvents: function() {
        this.listenTo(app.events, 'calendar:reload', _.bind(this.loadData, this));
        this.listenTo(this, 'calendar:reload', _.bind(this.loadData, this));
        this.listenTo(this, 'calendar:reconfigure', _.bind(this._reconfigureCalendar, this));
        this.listenTo(this.context, 'change:calendars', _.bind(this.updateCalendars, this));
        this.listenTo(this.context, 'button:cancel:click', _.bind(this.cancelSave, this));
        this.listenTo(this.context, 'button:save:click', _.bind(this.saveRecord, this));
        this.listenTo(this.context, 'button:saveAndSendInvites:click', _.bind(this.saveRecordAndSendInvites, this));

        this.events = {
            'click .previewEvent': '_previewEvent'
        };
    },

    /**
     * Setup calendar views
     *
     * @return {Array}
     */
    setupCalendarViews: function() {
        let views = this.options.context.get('availableViews');
        if (typeof views == 'undefined' || views.length == 0) {
            views = app.utils.deepCopy(this._allPossibleViews); //make sure to use a copy
        }

        let defaultView = this.options.context.get('defaultView');
        if (typeof defaultView == 'undefined' || defaultView == '') {
            defaultView = views[0];
        }

        views = _.map(views, _.bind(function(view) {
            let newView = {
                type: this.getViewType(view),
                title: this.getViewTitle(view),
                selected: false,
                schedulerViewName: view,
                showWorkHours: true,
            };

            if (view === this.MONTH_SCHEDULE_VIEW) {
                newView.showWorkHours = false;
            }
            if (defaultView === view) {
                newView.selected = true;
            }
            return newView;
        }, this));

        return views;
    },

    /**
     * Update Calendars
     *
     * Useful when loading the main scheduler. Calendar configurations are set
     * on the main-scheduler layout so we need to update default vars and reload the scheduler
     */
    updateCalendars: function() {
        this.location = this.options.context.get('location') || '';
        this.customKendoOptions = this.options.context.get('customKendoOptions') || {};
        this.options.skipFetch = this.options.context.get('skipFetch') || false;
        this.views = this.setupCalendarViews();
        this.calendars = app.data.createBeanCollection('Calendar', this.options.context.get('calendars'));
        this.keyToStoreCalendarConfigurations = this.options.context.get('keyToStoreCalendarConfigurations');
        this.keyToStoreCalendarView = app.Calendar.utils.buildUserKeyForStorage(this.location);

        if (_.isNull(this.scheduler)) {
            this.once('calendar:initialized', _.bind(this._reconfigureCalendar, this));
        } else {
            this._reconfigureCalendar();
        }
    },

    /**
     * Cancel save record
     */
    cancelSave: function() {
        this.offlineRefreshDataSource();
    },

    /**
     * Save record
     *
     * @param {Object} clonedEvent Event
     */
    saveRecord: function(clonedEvent) {
        if (_.isNull(this.eventInChange)) {
            app.logger.error('Failed to get event in change');
            return;
        }

        if (clonedEvent) {
            this.updateEvent(clonedEvent);
        }
    },

    /**
     * Save record and send invites
     *
     * @param {Object} clonedEvent
     */
    saveRecordAndSendInvites: function(clonedEvent) {
        if (clonedEvent) {
            clonedEvent.sendInvites = true;
            this.saveRecord(clonedEvent);
        }
    },

    /**
     * Populate calendar
     *
     * @param  {Object} res Full response from api
     */
    populateCalendarWithData: function(res) {
        if (this.disposed) {
            return;
        }

        let kendoEvents = [];
        this._eventsLoaded.events = [];
        this._eventsLoaded.startDate = res.startDate;
        this._eventsLoaded.endDate = res.endDate;
        this._usersInEventsLoaded = res.users;

        if (!_.isEmpty(res)) {
            _.each(
                res.data,
                function(row) {
                    let rowData = {
                        recordId: row.id,
                        calendarId: row.calendarId,
                        id: row.calendarId + '_' + row.id,
                        eventUsers: row.eventUsers,
                        name: row.title,
                        start: moment(row.start).toDate(),
                        end: moment(row.end).toDate(),
                        title: row.title,
                        description: row.description,
                        isAllDay: row.isAllDay,
                        module: row.module,
                        event_tooltip: row.event_tooltip,
                        day_event_template: row.day_event_template,
                        week_event_template: row.week_event_template,
                        month_event_template: row.month_event_template,
                        agenda_event_template: row.agenda_event_template,
                        timeline_event_template: row.timeline_event_template,
                        schedulermonth_event_template: row.schedulermonth_event_template,
                        dbclickRecordId: row.dbclickRecordId,
                        color: row.color,
                        assignedUserName: row.assignedUserName,
                        assignedUserId: row.assignedUserId,
                        invitees: row.invitees
                    };

                    const start = moment(row.start);
                    const end = moment(row.end);
                    const duration = moment.duration(end.diff(start));
                    if (duration.asDays() >= 1) {
                        rowData.isAllDay = true;
                    }

                    if (start.format() === end.format()) {
                        rowData.isAllDay = true;
                    }

                    //fix kendo not showing events made on date types spanning
                    //on expected cells
                    let schedulerViewType = this.scheduler._selectedView.name;
                    schedulerViewType = this.getViewType(schedulerViewType);
                    if (schedulerViewType === this.EXPANDED_MONTH_VIEW) {
                        let calendarDef = _.find(this.calendarDefs, function(calendarDef) {
                            return calendarDef.calendarId === row.calendarId;
                        });
                        let calendarModule = calendarDef.module;
                        let moduleMetadata = app.metadata.getModule(calendarModule);
                        let fieldsMetadata = moduleMetadata.fields;

                        let endDef = fieldsMetadata[calendarDef.end_field];
                        if (typeof endDef != 'undefined') {
                            let endDefFieldType = endDef.dbType || endDef.dbtype || endDef.type;
                            if (endDefFieldType == 'date') {
                                rowData.end = moment(rowData.end).set('minute', 1);
                                rowData.end = new Date(rowData.end);
                            }
                        }
                    }
                    const kendoSchedulerEvent = new kendo.data.SchedulerEvent(rowData);

                    this._eventsLoaded.events.push(app.utils.deepCopy(rowData));
                    kendoEvents.push(kendoSchedulerEvent);
                }, this
            );
        }
        if (this.scheduler) {
            this._syncedEvents = app.utils.deepCopy(kendoEvents);
            this.scheduler._selectedView.options.dataSource.data(kendoEvents);

            this.updateUsersLegend(res);
            this.positionLegend();
        }
    },

    /**
     * Generates and adds on DOM the list of users
     *
     * @param {Object} data Response from the database
     */
    updateUsersLegend: function(data) {
        let list = '';
        _.each(data.users, function(user) {
            const userColor = app.Calendar.utils.pastelColor(user.id);

            list += '<li><div><span class="userDot" style="background-color:' + userColor +
             '"></span> ' + DOMPurify.sanitize(user.name) + '</div></li>';
        });

        this.$('.usersLegend ul').html(list);
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        if (!this.scheduler) {
            return;
        }

        const url = app.api.buildURL(this.eventsURL);
        let data = {};

        if (this.calendars instanceof app.data.beanCollection) {
            data.calendarConfigurations = this.calendars.compile();
        } else {
            data.calendarConfigurations = this.calendars;
        }

        if (typeof this.listFilter !== 'undefined' && !_.isEmpty(this.listFilter)) {
            data.listFilter = this.listFilter;
        }
        if (typeof this.filterModule !== 'undefined' && !_.isEmpty(this.filterModule)) {
            data.filterModule = this.filterModule;
        }

        //show loading alert
        const visibleAlerts = app.alert.getAll();
        if (!visibleAlerts['loading-calendar-events'] && !visibleAlerts['data:sync:process']) {
            app.alert.show('loading-calendar-events', {
                level: 'process',
                messages: app.lang.get('LBL_LOADING')
            });
        }

        //set start/end dates based on the current view
        const schedulerView = this.scheduler.view();
        if (schedulerView) {
            data.startDate = moment(schedulerView.startDate()).format();
            data.endDate = moment(schedulerView.endDate()).set({
                hour: 23,
                minute: 59,
                second: 59
            }).format();

            let retrieveEventsOptions = _.extend({}, options, {
                success: _.bind(this.populateCalendarWithData, this),
                error: _.bind(function(data) {
                    // refresh token if it has expired
                    app.error.handleHttpError(data, {});
                }, this),
                complete: _.bind(function() {
                    app.alert.dismiss('loading-calendar-events');
                    if (this.context) {
                        this.context.trigger('calendar:loaded');
                    }
                }, this)
            });

            app.api.call('create', url, data, retrieveEventsOptions);
        }
    },

    /**
     * Reconfigure the calendar
     */
    _reconfigureCalendar: function() {
        const params = {
            calendars: this.calendars.compile()
        };
        if (params.calendars.length == 0) {
            this.calendarDefs = [];
            this.loadData();
            return;
        }
        app.api.call('create', app.api.buildURL('Calendar/getCalendarDefs'), params, {
            success: _.bind(function(calendarDefs) {
                if (this.disposed) {
                    return;
                }

                this.calendarDefs = calendarDefs;

                _.each(calendarDefs, function(calendarDef) {
                    if (this.scheduler) {
                        this.scheduler.resources[0].dataSource.add({
                            text: DOMPurify.sanitize(calendarDef.name),
                            value: DOMPurify.sanitize(calendarDef.id),
                            color: DOMPurify.sanitize(calendarDef.color)
                        });
                    }
                }, this);

                this.loadData();
            }, this),
            error: _.bind(function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }, this),
        });
    },

    /**
     * Update templates
     *
     * Add circles on each event then put them on DOM
     */
    _updateTemplates: function() {
        if (this.disposed) {
            return;
        }

        let cells;
        let viewName = this.scheduler.view().name;
        const viewType = this.scheduler.view().type;
        if (viewType === this.EXPANDED_MONTH_VIEW) {
            viewName = 'Month';
        } else if (viewName === this.MONTH_SCHEDULE_VIEW) {
            viewName = 'Scheduler';
        }

        if (viewName === this.AGENDA_VIEW) {
            cells = this.$('div.k-task .event-template');
        } else {
            cells = this.$('div[role=gridcell] > .event-template');
        }
        let prependCircle = _.bind(function(htmlContent, event) {
            //add text color based on backround color
            if (event.color) {
                const isWhiteColor = app.Calendar.utils.whiteColor(event.color);
                let color;

                if (isWhiteColor) {
                    color = '#000000';
                } else {
                    color = '#FFFFFF';
                }
                htmlContent = '<div class="templateHtmlWrapper" style="color:' + color + ';">' +
                    '<div class="calendarEventBody">' + DOMPurify.sanitize(htmlContent) + '</div></div>';

                if (viewName === this.AGENDA_VIEW) {
                    this.$('div.k-task[data-uid=' + event.uid + ']').each(function() {
                        $(this).css('background-color', event.color);
                    });
                }

                const assignedUserColor = app.Calendar.utils.pastelColor(event.assignedUserId);
                htmlContent = $(htmlContent).prepend('<div class="previewEvent" data-module=' +
                    _.escape(event.module) +
                    ' data-record=' +  _.escape(event.dbclickRecordId) + ' rel="tooltip" data-bs-placement="bottom"' +
                    ' aria-haspopup="true" aria-expanded="false" data-original-title="' +
                    DOMPurify.sanitize(event.assignedUserName) +
                    '"><span class="userBar" style="background-color:' +
                    _.escape(assignedUserColor) + '"></span></div>');

                _.each(event.invitees, function(invitee, idx) {
                    if (invitee.id !== event.assignedUserId && idx < 3) {
                        const inviteeColor = app.Calendar.utils.pastelColor(invitee.id);
                        const inviteeName = invitee.name;

                        htmlContent = $(htmlContent).prepend('<div class="previewEvent" data-module=' +
                            _.escape(event.module) +
                        ' data-record=' +  _.escape(event.dbclickRecordId) +
                            ' rel="tooltip" data-bs-placement="bottom"' +
                        ' aria-haspopup="true" aria-expanded="false" data-original-title="' +
                            DOMPurify.sanitize(inviteeName) +
                        '"><span class="userBar" style="background-color:' +
                            _.escape(inviteeColor) + '"></span></div>');
                    }
                }, this);
            }
            return htmlContent;
        }, this);

        _.each(cells, function(cell) {
            let uid;
            if (viewName === this.AGENDA_VIEW) {
                uid = $(cell)
                    .parent()
                    .parent()
                    .data('uid');
            } else {
                uid = $(cell)
                    .parent()
                    .data('uid');
            }

            let event = this.scheduler.occurrenceByUid(uid);
            switch (viewName) {
                case this.DAY_VIEW:
                    $(cell).html(prependCircle(event.day_event_template, event));
                    break;
                case this.WEEK_VIEW:
                case this.WORK_WEEK_VIEW:
                    $(cell).html(prependCircle(event.week_event_template, event));
                    break;
                case this.EXPANDED_MONTH_VIEW:
                case 'Month':
                    $(cell).html(prependCircle(event.month_event_template, event));
                    break;
                case this.AGENDA_VIEW:
                    $(cell).html(prependCircle(event.agenda_event_template, event));
                    break;
                case this.TIMELINE_VIEW:
                    $(cell).html(prependCircle(event.timeline_event_template, event));
                    break;
                case this.MONTH_SCHEDULE_VIEW:
                case 'Scheduler':
                    $(cell).html(prependCircle(event.schedulermonth_event_template, event));
                    break;
            }
        }, this);

        this.addTooltips();
    },

    /**
     * Add a preview of the record when the circle is clicked
     *
     * @param {Object} e
     */
    _previewEvent: function(e) {
        const module = e.currentTarget.dataset.module;
        const recordId = e.currentTarget.dataset.record;
        const model = app.data.createBean(module, {id: recordId});

        const windowWidth = window.innerWidth;
        const mainPanelWidth = app.controller.layout.$('.calendar-main-panel').width();
        const previewPanelWidth = app.controller.layout.$('.preview-pane').width();
        const calendarWidth = windowWidth - mainPanelWidth - previewPanelWidth;
        const intialCalendarWidth = windowWidth - mainPanelWidth;

        app.events.trigger('preview:render', model, null);

        app.controller.layout.$('.preview-pane').removeClass('hide');
        app.controller.layout.$('.scheduler-component').css('width', calendarWidth);
        this.scheduler.resize();

        app.controller.layout.$('.closeSubdetail').on('click', _.bind(function() {
            app.controller.layout.$('.preview-pane').addClass('hide');
            app.controller.layout.$('.scheduler-component').css('width', intialCalendarWidth);
            this.scheduler.resize();
        }, this));
    },

    /**
     * Add tooltips to the events
     */
    addTooltips: function() {
        let kendoTooltip = this.$('.k-scheduler-content').data('kendoTooltip');
        if (kendoTooltip) {
            kendoTooltip.hide();
            kendoTooltip.destroy();
        }

        const tooltip = app.template.getView('scheduler.tooltip', 'Calendar')();

        if (this.scheduler.view().name === this.AGENDA_VIEW) {
            this.$('.k-scheduler-content').kendoTooltip({
                    filter: '.k-task',
                    content: tooltip,
                    position: 'left',
                    autoHide: true,
                    showAfter: 500,
                    callout: false,
                    show: _.bind(this._setTooltipContent, this),
                    animation: {
                        close: {
                            duration: 0
                        }
                    }
                }
            );
        } else {
            this.$('.k-scheduler-header, .k-scheduler-content').kendoTooltip({
                    filter: 'div[role=gridcell]',
                    content: tooltip,
                    position: 'right',
                    autoHide: true,
                    showAfter: 500,
                    callout: false,
                    show: _.bind(this._setTooltipContent, this),
                    width: 200,
                    animation: {
                        close: {
                            duration: 0
                        }
                    }
                }
            );
        }
    },

    /**
     * Set the tooltip content
     *
     * @param {Object} tooltip
     */
    _setTooltipContent: function(tooltip) {
        if (tooltip.sender.target().hasClass('k-event-drag-hint') || this._isDeleteAction) {
            tooltip.sender.hide();
        } else {
            const target = tooltip.sender.target();
            const uid = target.data('uid');
            let event = this.scheduler.occurrenceByUid(uid);

            const assignedUserColor = app.Calendar.utils.pastelColor(event.assignedUserId);

            let time = '<span>' + moment(event.start).format('ddd, MMMM D');
            if (moment(event.start).format('ddd MMMM') != moment(event.end).format('ddd MMMM')) {
                time += ' - ' + moment(event.end).format('ddd, MMMM D');
            }
            time += '</span><span>' + moment(event.start).format('h:mma') + ' - ' +
                moment(event.end).format('h:mma') + '</span>';

            if (event.module == 'Calls' || event.module == 'Meetings') {
                tooltip.sender.content.find('.event-tooltip .tooltip-attendees').removeClass('hidden');
                tooltip.sender.content.find('.event-tooltip .tooltip-description').removeClass('hideBottomBorder');

                const acceptedInvitees = _.filter(event.invitees, function(invitee) {
                    return invitee.acceptStatus == 'accept';
                });

                const inviteesAcceptedTemplate = app.lang.getModString('LBL_INVITEES_ACCEPTED', 'Calendar', {
                    count: acceptedInvitees.length
                });

                let attendees = inviteesAcceptedTemplate;
                let moreAttendees = '';

                _.each(acceptedInvitees, function(attendee, idx) {
                    const attendeeColor = app.Calendar.utils.pastelColor(attendee.id);

                    const newAttendee = '<div class="attendee"><span class="userDot" style="background-color:' +
                        attendeeColor + '"></span><span>' + DOMPurify.sanitize(attendee.name) + '</span></div>';

                    if (idx < 2) {
                        attendees += newAttendee;
                    } else {
                        moreAttendees += newAttendee;
                    }
                });

                if (acceptedInvitees.length > 2) {
                    const moreToLoad = acceptedInvitees.length - 2;
                    const moreToLoadLabel = app.lang.getModString('LBL_MORE_INVITEES_TO_LOAD', 'Calendar', {
                        count: moreToLoad
                    });
                    moreAttendees = '<a data-toggle="collapse" role="button" href="#attendeeCollapse-' +
                        event.id + '">' + moreToLoadLabel + '</a> <div class="collapse" id="attendeeCollapse-' +
                        event.id + '">' + moreAttendees + '</div>';

                    attendees += moreAttendees;
                }

                tooltip.sender.content.find('.event-tooltip .tooltip-attendees .category-container').html(attendees);
                tooltip.sender.content.find('#attendeeCollapse-' + event.id).on('show.bs.collapse', _.bind(function() {
                    tooltip.sender.content.find('a[data-toggle=collapse]').addClass('hide');
                }, this));
            } else {
                tooltip.sender.content.find('.event-tooltip .tooltip-attendees').addClass('hidden');
                tooltip.sender.content.find('.event-tooltip .tooltip-description').addClass('hideBottomBorder');
            }

            const sanitizedEventName = DOMPurify.sanitize(event.name);
            const sanitizedEventTooltip = DOMPurify.sanitize(event.event_tooltip);
            tooltip.sender.content.find('.event-tooltip .tooltip-header .category-container').html(sanitizedEventName);
            tooltip.sender.content.find('.event-tooltip .tooltip-time .category-container').html(time);
            tooltip.sender.content.find('.event-tooltip .tooltip-description .category-container')
                .html(sanitizedEventTooltip);
            tooltip.sender.content.find('.event-tooltip .tooltip-header .userDot')
                .css('background-color', assignedUserColor);
            tooltip.sender.content.parent().css('box-shadow', 'none');
            tooltip.sender.content.parent().parent().css('margin-left', '0px');
        }
    },

    /**
     * Render
     *
     * Render this view in DOM
     * and eventually start initializing Kendo Scheduler
     *
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        const params = {
            calendars: this.calendars.compile()
        };

        if (params.calendars.length == 0 || (_.isNull(this.scheduler) && this.options.skipFetch)) {
            this.calendarDefs = [];
            this._createCalendar();

            return;
        }

        app.api.call('create', app.api.buildURL('Calendar/getCalendarDefs'), params, {
            success: _.bind(function(calendarDefs) {
                this.calendarDefs = calendarDefs;

                this._createCalendar();
            }, this),
            error: _.bind(function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }, this),
        });
    },

    /**
     * Initializes Kendo Scheduler component
     */
    _initializeScheduler: function() {
        let rawResources = [];
        _.each(this.calendarDefs, function(calendar) {
            rawResources.push({
                text: calendar.id,
                value: calendar.id,
                color: calendar.color
            });
        });

        let resourcesDS = new kendo.data.DataSource({
            data: rawResources
        });

        //considering the autoBind is set to false, we have to manually fetch resources ds
        resourcesDS.fetch();

        let kendoOptions = _.extend({}, {
            date: new Date(),
            timezone: app.user.attributes.preferences.timezone,
            currentTimeMarker: {
                useLocalTimezone: false
            },
            startTime: new Date('2000/1/1 00:00 AM'),
            toolbar: ['pdf'],
            messages: {
                pdf: app.lang.getModString('LBL_CALENDAR_PDF_EXPORT', 'Calendar'),
                showWorkDay: app.lang.getModString('LBL_CALENDAR_SHOW_FULL_DAY', 'Calendar')
            },
            pdf: {
                fileName: app.lang.getModString('LBL_CALENDAR_CALENDAR_EXPORT', 'Calendar') + '.pdf',
                landscape: false,
                calculatePaperSize: true,
                calendarIdRef: this._schedulerCssId
            },
            eventTemplate: '<div class="event-template"></div>',
            workDays: [1,2,3,4,5],
            views: this.views,
            dataSource: new kendo.data.SchedulerDataSource({
                data: []
            }),
            autoBind: false,
            selectable: true,
            editable: {
                confirmation: false, //default delete confirmation,
                resize: true,
                move: true
            },
            edit: _.bind(this._eventDoubleClickHandler, this),
            moveStart: _.bind(this._moveResizeStartHandler, this),
            moveEnd: _.debounce(_.bind(this._moveResizeHandler, this), 0),
            resizeStart: _.bind(this._moveResizeStartHandler, this),
            resizeEnd: _.debounce(_.bind(this._moveResizeHandler, this), 0),
            remove: _.bind(this._deleteHandler, this),
            dataBound: _.bind(this._updateTemplates, this),
            navigate: _.bind(this._navigateHandler, this),
            workDayStart: this._getBusinessHours('start'),
            workDayEnd: this._getBusinessHours('end'),
            resources: [
                {
                    field: 'calendarId',
                    title: 'calendarId',
                    dataSource: resourcesDS
                },
            ],
            majorTimeHeaderTemplate: this._getMajorTimeHeaderTemplate(),
        }, this.customKendoOptions);

        const selectedView = _.find(this.views, (view) => view.selected).type;
        if (selectedView !== this.MONTH_SCHEDULE_VIEW) {
            kendoOptions.dateHeaderTemplate = this._getDateHeaderTemplate(
                _.find(this.views, (view) => view.selected).type
            );
        }

        if (selectedView !== this.EXPANDED_MONTH_VIEW) {
            kendoOptions.selectedDateFormat = this._getSelectedDateFormat(selectedView);
        }

        //finally, kendo initialization
        this.$('#' + this._schedulerCssId).kendoScheduler(kendoOptions);
        this.scheduler = this.$('#' + this._schedulerCssId)
            .data('kendoScheduler');

        this.toolbarView = app.view.createView({
            name: 'scheduler-toolbar',
            type: 'scheduler-toolbar',
            module: 'Calendar',
        });
        this.toolbarView.views = this.views;
        this.toolbarView.formattedDate = this.scheduler._model.formattedDate;
        this.toolbarView.formattedShortDate = this.scheduler._model.formattedShortDate;

        this.toolbarView.render();

        this.$('.k-scheduler-toolbar.k-toolbar').html(this.toolbarView.$el.html());

        this.scheduler._model.bind('change', _.bind(function(e) {
            if (e.field == 'formattedDate') {
                this.$('.k-lg-date-format').html(e.sender.source.formattedDate);
                this.$('.k-sm-date-format').html(e.sender.source.formattedShortDate);
            }
        }, this));

        this.scheduler.wrapper.on('mousedown.kendoScheduler', _.debounce(_.bind(function(e) {
            if (e.target.hasAttribute('role') && e.target.getAttribute('role') === 'gridcell') {
                this.context.trigger('scheduler:view:changed');
            }
        }, this), 0));

        //we need to wait until the dashlet is loaded in order to have a context menu
        _.defer(_.bind(function() {
            this.$('#' + this._schedulerCssId).find('.k-dropdown').select2({
                minimumResultsForSearch: -1,
            });

            $('#context_menu_' + this._schedulerCssId).kendoContextMenu({
                filter: '.k-scheduler-table',
                showOn: 'dblclick',
                open: _.bind(this._contextMenuOpen, this),
                select: _.bind(this._contextMenuSelect, this),
                target: '#' + this._schedulerCssId
            });
        }, this));

        this.trigger('calendar:initialized');
    },

    /**
     * Returns if the calendar is created
     *
     * @return {boolean}
     */
    _calendarIsCreated: function() {
        if (typeof kendo == 'undefined' || !(this.scheduler instanceof kendo.ui.Scheduler)) {
            return false;
        }
        return true;
    },

    /**
     * Bind legend position event
     */
    _bindLegendEvent: function() {
        $(window).on('resize', this.positionLegend);
    },

    /**
     * Position the legend dropdown
     */
    positionLegend: function() {
        if (_.isEmpty(this.$el)) {
            return;
        }
        _.each(this.$('.dropdown-menu'), function(dropdownMenu) {
            const menuWidth = $(dropdownMenu).width();
            const parentWidth = $(dropdownMenu).parent().width();
            const parentOffset = $(dropdownMenu).parent().offset();
            const leftOffSet = 27;
            const topOffset = 30;
            $(dropdownMenu).css({
                left: parentOffset.left - menuWidth + parentWidth - leftOffSet,
                top: parentOffset.top + topOffset
            });
        },this);
    },

    /**
     * Create the Kendo component
     */
    _createCalendar: function() {
        if (this._calendarIsCreated()) {
            return;
        }

        this.views = this.setupCalendarViews();
        if (typeof kendo === 'object') {
            this.culturePreferences();
            this._initializeScheduler();
            this._bindExportEvent();
            this._bindLegendEvent();
        } else {
            $.getScript('cache/include/javascript/sugar_grp_calendar.js', _.bind(function() {
                this.culturePreferences();
                this._initializeScheduler();
                this._bindExportEvent();
                this._bindLegendEvent();
            }, this));
        }
    },

    /**
     * Open drawer to edit the record
     *
     * @param {Object} data
     */
    _editHandlerSuccessCallback: function(data) {
        const module = data.module;
        app.drawer.open(
            {
                layout: 'create',
                context: {
                    layoutName: 'create',
                    create: true,
                    module: data.module,
                    model: data
                }
            },
            _.bind(function() {
                this.loadData();

                if (this.isPageWithSubpanels()) {
                    this.refreshSubpanels(module);
                }
            },this)
        );
    },

    /**
     * Trigger a route change to the specified url
     *
     * @param {string} url
     */
    _navigate: function(url) {
        _.defer(function() {
            app.router.navigate(url, {
                trigger: true
            });
        });
    },

    /**
     * Handler for double click event
     *
     * @param {Object} e
     */
    _eventDoubleClickHandler: function(e) {
        e.preventDefault();

        this._selectedState = e.event;
        let moduleMeta = app.metadata.getModule(e.event.module);
        let url;
        let model;

        if (e.event.isNew()) {
            return;
        }

        let actionOnDbClick = this.getActionOnDbClick(e.event);

        if (_.isEmpty(e.event.dbclickRecordId)) {
            const moduleName = app.lang.getModuleName(e.event.module);
            const relatedModule = app.lang.getModuleName(actionOnDbClick.module);
            const message = app.lang.getModString('LBL_NAVIGATE_TO_RECORD_WARNING', 'Calendar', {
                relatedModule: relatedModule.toLowerCase(),
                moduleName: moduleName.toLowerCase()
            });

            app.alert.show('navigation-record', {
                level: 'confirmation',
                messages: message,
                autoClose: false,
                onConfirm: _.bind(function() {
                    this.navigateToRecord(e.event);
                }, this),
            });
            return;
        }

        if (actionOnDbClick.action == 'detail') {
            if (moduleMeta.isBwcEnabled) {
                url = '#bwc/index.php?module=' + actionOnDbClick.module + '&action=DetailView&record=' +
                    e.event.dbclickRecordId;
                this._navigate(url);
            } else {
                url = '#' + actionOnDbClick.module + '/' + e.event.dbclickRecordId;
                this._navigate(url);
            }
        } else if (actionOnDbClick.action == 'detail-newtab') {
            if (moduleMeta.isBwcEnabled) {
                url = app.utils.getSiteUrl() + '#bwc/index.php?module=' + actionOnDbClick.module +
                    '&action=DetailView&record=' + e.event.dbclickRecordId;
                window.open(url);
            } else {
                url = app.utils.getSiteUrl() + '#' + actionOnDbClick.module + '/' + e.event.dbclickRecordId;
                window.open(url);
            }
        } else if (actionOnDbClick.action == 'edit') {
            moduleMeta = app.metadata.getModule(actionOnDbClick.module);

            if (this.isAllowed('allow_update', e.event)) {
                if (moduleMeta.isBwcEnabled) {
                    url = '#bwc/index.php?module=' + actionOnDbClick.module + '&action=EditView&record=' +
                         e.event.dbclickRecordId;
                    this._navigate(url);
                } else {
                    model = app.data.createBean(actionOnDbClick.module, {
                        id: e.event.dbclickRecordId,
                        module: actionOnDbClick.module
                    });
                    model.fetch({
                        params: {
                            view: 'record'
                        },
                        success: _.bind(this._editHandlerSuccessCallback, this),
                        error: function() {
                            app.alert.show('error-fetch-model', {
                                level: 'error',
                                messages: app.lang.getModString('LBL_CALENDAR_ERROR_FETCH_MODEL', 'Calendar'),
                                autoClose: false
                            });
                        }
                    });
                }
            } else {
                app.alert.show('not-allowed', {
                    level: 'warning',
                    messages: app.lang.getModString('LBL_CALENDAR_NOT_ALLOWED_TO_EDIT', 'Calendar'),
                    autoClose: true
                });
            }
        }
    },

    /**
     * Get informations about double click action of this event
     *
     * @param {Object} event
     * @return {Object}
     */
    getActionOnDbClick: function(event) {
        let dblClick;
        let calendar;

        const currentUserType = app.user.get('type');
        const calendarId = event.calendarId;

        calendar = _.find(this.calendarDefs, function(calendar) {
            return calendar.calendarId === calendarId;
        });
        dblClick = calendar.dblclick_event.split(':');
        if (dblClick.length == 3) {
            dblClick = {
                action: dblClick[0],
                module: dblClick[1],
                id: dblClick[2] == 'id' ? event.recordId : event.dbclickRecordId
            };
        }

        if (dblClick.module == 'self') {
            dblClick.module = calendar.module;
        }

        if (currentUserType == 'user' && dblClick.module == 'Users') {
            dblClick.module = 'Employees';
        }

        return dblClick;
    },

    /**
     * Build model with proper default fields
     *
     * @param {Object} calendarDef
     * @param {Object} view
     * @param {Object} state
     * @return {Data.Bean} A new instance of a bean.
     */
    buildModelWithProperDefaultFields: function(calendarDef, view, state) {
        let model;
        const startField = calendarDef.start_field;
        let dataPrefill = {
            module: calendarDef.module
        };

        dataPrefill[startField] = moment(state.start).format();

        let endField = calendarDef.end_field;

        //in Calls & Meetings modules, end date is calculated based on durations
        if (calendarDef.module == 'Calls' || calendarDef.module == 'Meetings') {
            dataPrefill.duration_minutes = 30;
            endField = 'date_end';
        }

        if (!_.isEmpty(endField)) {
            dataPrefill[endField] = moment(state.start).add(30, 'minutes').format();
        }

        let moduleMetadata = app.metadata.getModule(calendarDef.module);
        let fieldsMetadata = moduleMetadata.fields;

        let startDef = fieldsMetadata[calendarDef.start_field];
        let endDef = fieldsMetadata[calendarDef.end_field];
        if (typeof startDef != 'undefined') {
            let startDefFieldType = startDef.dbType || startDef.dbtype || startDef.type;
            if (startDefFieldType == 'date') {
                dataPrefill[calendarDef.start_field] = moment(state.start).format('YYYY-MM-DD');
            }
        }
        if (typeof endDef != 'undefined') {
            let endDefFieldType = endDef.dbType || endDef.dbtype || endDef.type;
            if (endDefFieldType == 'date') {
                dataPrefill[calendarDef.end_field] = moment(state.start).format('YYYY-MM-DD');
            }
        }

        model = app.data.createBean(calendarDef.module, dataPrefill);

        return model;
    },

    /**
     * Open list with available calendars
     *
     * @param {Object} e
     */
    _contextMenuOpen: function(e) {
        //on Agenda View edit event is not triggered so we have to trigger it here
        const schedulerView = this.scheduler.view();
        if (schedulerView.name === this.AGENDA_VIEW) {
            const uid = $(e.event.target)
                .closest('.k-task')
                .data('uid');

            const event = this.scheduler.occurrenceByUid(uid);
            this.scheduler.editEvent(event);
            e.preventDefault();
            return;
        }

        const state = this._selectedState;
        if (state && typeof state.isNew == 'function' && state.isNew()) {
            let menu = e.sender;
            menu.remove('.contextCalendarItem');

            //get all calendar defs of My Calendars in this scheduler. Only one per module
            let calendars;
            if (this.location == 'dashboard') {
                calendars = this.calendarDefs;
            } else {
                calendars = this.context.get('myAvailableCalendars');
            }
            const availableCalendars = _.filter(calendars, function(calendar) {
                return app.acl.hasAccess('view', calendar.module) && calendar.allow_create;
            });

            _.each(
                availableCalendars,
                function(calendar) {
                    let text = '';

                    if (calendar.module === 'KBContents') {
                        text = app.lang.getModString('LNK_NEW_KBCONTENT_TEMPLATE', calendar.module);
                    } else {
                        let createLabel = 'LNK_NEW_' + calendar.objName.toUpperCase();
                        text = app.lang.get(createLabel, calendar.module);

                        if (text === createLabel) {
                            createLabel = 'LNK_NEW_RECORD';
                            text = app.lang.getModString(createLabel, calendar.module);
                        }
                    }
                    menu.append([{
                        text: text,
                        cssClass: 'contextCalendarItem', //used to remove old items
                        uid: calendar.module
                    }]);
                }, this
            );
        } else {
            e.preventDefault();
        }
    },

    /**
     * Context menu select
     *
     * @param {Object} e
     */
    _contextMenuSelect: function(e) {
        const state = this._selectedState;
        const selectedIdx = $(e.item).index();

        //get all calendar defs of My Calendars in this scheduler. Only one per module
        let calendars;
        if (this.location == 'dashboard') {
            calendars = this.calendarDefs;
        } else {
            calendars = this.context.get('myAvailableCalendars');
        }

        const availableCalendars = _.filter(calendars, function(calendar) {
            return app.acl.hasAccess('view', calendar.module);
        });

        const calendar = availableCalendars[selectedIdx];
        const selectedModule = calendar.module;

        if (selectedModule === 'Quotes') {
            app.router.navigate('Quotes/create', {trigger: true});
        } else {
            //open drawer with default values based on module
            const model = this.buildModelWithProperDefaultFields(calendar, this, state);
            app.drawer.open({
                layout: 'create',
                context: {
                    layoutName: 'create',
                    create: true,
                    module: selectedModule,
                    model: model
                }},
                _.bind(function(context, model) {
                    if (!(model instanceof app.data.beanModel)) {
                        return; //no record created
                    }

                    this.loadData();

                    if (this.isPageWithSubpanels()) {
                        this.refreshSubpanels(model.module);
                    }
                }, this)
            );
        }
    },

    /**
     * Move event is starting
     *
     * @param {Object} e
     */
    _moveResizeStartHandler: function(e) {
        if (this.isAllowed('allow_update', e.event)) {
            this.setCssMarkerToHideTooltip(e);
        } else {
            let errorMessage = app.lang.getModString('LBL_CALENDAR_NOT_ALLOWED_TO_MOVE', 'Calendar');
            if (arguments.callee.name == 'resizeHandler') {
                errorMessage = app.lang.getModString('LBL_CALENDAR_NOT_ALLOWED_TO_CHANGE', 'Calendar');
            }
            app.alert.show('not-allowed', {
                level: 'warning',
                messages: errorMessage,
                autoClose: true
            });
            e.preventDefault();
        }
    },

    /**
     * Hide tooltip when the event is resized
     *
     * @param {Object} e
     */
    setCssMarkerToHideTooltip: function(e) {
        let element = this.$('[data-uid=' + e.event.uid + ']');
        element.addClass('k-event-drag-hint');
    },

    /**
     * Move event was done
     *
     * @param {Object} e
     */
    _moveResizeHandler: function(e) {
        this.eventInChange = e;
        let clonedEvent = app.utils.deepCopy(e.event);

        let schedulerViewType = this.scheduler._selectedView.name;
        schedulerViewType = this.getViewType(schedulerViewType);

        if (schedulerViewType === this.EXPANDED_MONTH_VIEW || schedulerViewType === this.MONTH_SCHEDULE_VIEW) {
            let originalEvent = this._eventsLoaded.events.find((evt) => evt.recordId === e.event.recordId);
            if (!originalEvent) {
                e.preventDefault();
            }

            // When resizing elements in the month/scheduler view,
            // the end date of the event will come as 00:00 the next day
            // We will need to turn that back in order to use the correct day of the month
            let startDate = moment(e.start);
            let endDate = moment(e.end);

            let origStartDate = moment(originalEvent.start);
            let origEndDate = moment(originalEvent.end);

            let calendarDef = _.find(this.calendarDefs, function(calendarDef) {
                return calendarDef.calendarId === originalEvent.calendarId;
            });
            let moduleMetadata = app.metadata.getModule(calendarDef.module);
            let fieldsMetadata = moduleMetadata.fields;

            let endDef = fieldsMetadata[calendarDef.end_field];
            let endDefFieldType = '';

            if (typeof endDef != 'undefined') {
                endDefFieldType = endDef.dbType || endDef.dbtype || endDef.type;
            }

            const dateFormat = 'YYYYMMDD';

            if (origStartDate.format(dateFormat) === startDate.format(dateFormat)) {
                if (origStartDate.format(dateFormat) === origEndDate.format(dateFormat) &&
                    startDate.format(dateFormat) !== endDate.format(dateFormat) &&
                    endDefFieldType != 'date') {
                    endDate.subtract(1, 'days');
                }
            } else if (schedulerViewType === this.MONTH_SCHEDULE_VIEW && originalEvent.isAllDay) {
                endDate.subtract(1, 'days');
            }

            let newStart = origStartDate.year(startDate.year()).month(startDate.month()).date(startDate.date());
            let newEnd = origEndDate.year(endDate.year()).month(endDate.month()).date(endDate.date());

            e.event.start = newStart.toDate();
            e.event.end = newEnd.toDate();

            e.event.dirtyFields.start = true;
            e.event.dirtyFields.end = true;

            clonedEvent.start = newStart.toDate();
            clonedEvent.end = newEnd.toDate();
        }

        if (clonedEvent.module == 'Calls' || clonedEvent.module == 'Meetings') {
            this.confirmationPopupView = app.view.createView({
                context: this.context,
                type: 'confirm-invitation',
                event: clonedEvent,
            });

            $('#alerts').append(this.confirmationPopupView.$el);

            this.confirmationPopupView.render();
        } else {
            app.alert.show('moveResizeHandlerAlert', {
                level: 'confirmation',
                messages: app.lang.getModString('LBL_CALENDAR_CONFIRM_CHANGE_RECORD', 'Calendar'),
                autoClose: false,
                onConfirm: _.bind(function() {
                    this.saveRecord(clonedEvent);
                }, this),
                onCancel: _.bind(this.cancelSave, this)
            });
        }
    },

    /**
     * Update event
     *
     * @param {Object} eventData
     */
    updateEvent: function(eventData) {
        let data = {
            recordId: eventData.recordId,
            module: eventData.module,
            calendarId: eventData.calendarId,
            start: moment(eventData.start).format(),
            end: moment(eventData.end).format()
        };

        if (typeof eventData.sendInvites == 'boolean') {
            data.sendInvites = true;
        }

        app.alert.show('move_resize_event', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
            autoClose: false
        });

        //solve the case when multiple move/resize actions are made quicker then requests
        this.scheduler.dataSource.trigger('progress');

        let url = app.api.buildURL('Calendar/updateRecord') + '/' + this.eventInChange.event.recordId;
        app.api.call('create', url, data, {
            success: _.bind(function(updated) {
                if (this.disposed) {
                    return;
                }

                if (updated) {
                    app.alert.show('successUpdateRecordAlert', {
                        level: 'success',
                        messages: app.lang.getModString('LBL_CALENDAR_RECORD_SAVED', 'Calendar'),
                        autoClose: true
                    });

                    if (this.isPageWithSubpanels()) {
                        this.refreshSubpanels(this.eventInChange.event.module);
                        app.events.trigger('multidateFieldChanged');
                    }
                } else {
                    app.alert.show('update-record-restricted', {
                        level: 'error',
                        messages: app.lang.getModString('LBL_CALENDAR_RESTRICT_UPDATE', 'Calendar'),
                        autoClose: true
                    });
                }
            }, this),
            error: function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            },
            complete: _.bind(function() {
                app.alert.dismiss('move_resize_event');
                this.eventInChange = null;
                app.events.trigger('calendar:reload');
            }, this)
        });
    },

    /**
     * Delete handler
     *
     * @param {Object} e
     */
    _deleteHandler: function(e) {
        e.preventDefault();

        if (this.isAllowed('allow_delete', e.event)) {
            this._deletedEvent = e;
            this._isDeleteAction = true;
            app.alert.show('confirmRemoveRecordAlert', {
                level: 'confirmation',
                messages: app.lang.getModString('LBL_CALENDAR_CONFIRM_DELETE_RECORD', 'Calendar'),
                autoClose: false,
                onConfirm: _.bind(this.deleteEvent, this),
                onCancel: _.bind(this.cancelDelete, this)
            });
        } else {
            app.alert.show('not-allowed', {
                level: 'warning',
                messages: app.lang.getModString('LBL_CALENDAR_NOT_ALLOWED_TO_DELETE', 'Calendar'),
                autoClose: true
            });
        }
    },

    /**
     * Delete event
     */
    deleteEvent: function() {
        app.alert.show('event-deleted', {
            level: 'process',
            messages: app.lang.getModString('LBL_DELETING', 'Calendar')
        });
        app.api.call(
            'delete',
            app.api.buildURL(this._deletedEvent.event.module) + '/' + this._deletedEvent.event.recordId, null, {
                success: _.bind(this._deleteEventSuccessCallback, this),
                error: function(data) {
                    // refresh token if it has expired
                    app.error.handleHttpError(data, {});
                }
            }
        );
    },

    /**
    * Event delete success callback
    */
    _deleteEventSuccessCallback: function() {
        app.alert.dismiss('event-deleted');
        app.alert.show('deleteAlert', {
            level: 'success',
            messages: app.lang.getModString('LBL_CALENDAR_RECORD_DELETED', 'Calendar'),
            autoClose: true
        });

        app.events.trigger('calendar:reload');

        if (this.isPageWithSubpanels()) {
            this.refreshSubpanels(this._deletedEvent.event.module);
        }

        this._isDeleteAction = false;
    },

    /**
     * Mark delete action
     */
    cancelDelete: function() {
        this._isDeleteAction = false;
    },

    /**
     * Offline refresh data source
     *
     * Loads events from last fetch (cached on this view), on the calendar component
     */
    offlineRefreshDataSource: function() {
        // Need to patch end/start dates
        this._syncedEvents.forEach((evt) => {
            evt.start = moment(evt.start).toDate();
            evt.end = moment(evt.end).toDate();
        });

        this.scheduler._selectedView.options.dataSource.data(this._syncedEvents);
    },

    /**
     * Refresh collections on all subpanels of the given module
     *
     * @param {string} module
     */
    refreshSubpanels: function(module) {
        const filterpanel = app.controller.layout
            .getComponent('sidebar')
            .getComponent('main-pane')
            .getComponent('filterpanel');
        const subpanels = filterpanel.componentsList.subpanels;
        const targetSubpanels = _.filter(subpanels._components, function(subpanel) {
            return subpanel.module === module;
        });

        if (targetSubpanels.length > 0) {
            _.each(targetSubpanels, function(subpanel) {
                subpanel.collection.fetch();
            });
        }
    },

    /**
     * Returns whether the current page contains subpanels
     *
     * @return {boolean}
     */
    isPageWithSubpanels: function() {
        let filterPanel;
        const sidebar = app.controller.layout.getComponent('sidebar');
        if (sidebar) {
            const mainPane = sidebar.getComponent('main-pane');
            if (mainPane) {
                filterPanel = mainPane.getComponent('filterpanel');
            }
        }

        if (filterPanel instanceof app.view.Layout) {
            if (filterPanel.componentsList.subpanels) {
                return true;
            }
        }

        return false;
    },

    /**
     * Navigate handler
     *
     * @param {Object} options
     */
    _navigateHandler: function(options) {
        if (options.action === 'changeView') {
            if (options.view === this.MONTH_SCHEDULE_VIEW) {
                delete this.scheduler.options.dateHeaderTemplate;
            } else {
                this.scheduler.options.dateHeaderTemplate = this._getDateHeaderTemplate(options.view);
            }

            if (options.view === this.EXPANDED_MONTH_VIEW) {
                delete this.scheduler.options.selectedDateFormat;
            } else {
                this.scheduler.options.selectedDateFormat = this._getSelectedDateFormat(options.view);
            }
        }

        this._navigationLoadData(options);
    },

    /**
     * Load data after navigation
     *
     * Wait for UI navigation to complete, then load data if not already loaded
     *
     * @param {Object} options
     */
    _navigationLoadData: _.debounce(function(options) {
        let schedulerView = this.scheduler.view();
        const intervalDates = {
            start: moment(schedulerView.startDate()).format('YYYY-MM-DD'),
            end: moment(schedulerView.endDate()).format('YYYY-MM-DD')
        };

        let selectedDate = new Date(options.date.getTime());;
        schedulerView = this.scheduler.view();

        schedulerView.select({
            events: [],
            start: selectedDate,
            end: selectedDate,
            groupIndex: 0
        });

        this.context.trigger('scheduler:view:changed');

        let intervalStart = moment(intervalDates.start).format('YYYY-MM-DD');
        let intervalEnd = moment(intervalDates.end).format('YYYY-MM-DD');
        if (
            intervalStart != moment(this._eventsLoaded.startDate).format('YYYY-MM-DD') ||
            intervalEnd != moment(this._eventsLoaded.endDate).format('YYYY-MM-DD')
        ) {
            this.loadData();
        }

        const viewName = schedulerView.options.schedulerViewName;
        app.cache.set(this.keyToStoreCalendarView, viewName);
    }, 0),

    /**
     * Returns whether an action like create/update/delete is allowed for a calendar event
     *
     * @param {string} action
     * @param {Object} event
     * @return {boolean}
     */
    isAllowed: function(action, event) {
        let calendar = _.find(this.calendarDefs, function(calendar) {
            return calendar.id === event.calendarId;
        });
        if (typeof calendar == 'undefined') {
            return false;
        } else {
            return calendar[action] || false;
        }
    },

    /**
     * Transforms a View Name into an accepted View Name format
     *
     * @param {string} viewName
     * @return {string}
     */
    getViewType: function(viewName) {
        let type;
        switch (viewName) {
            case this.DAY_VIEW:
            case 'DayView':
                type = this.DAY_VIEW;
                break;
            case this.WEEK_VIEW:
            case 'WeekView':
                type = this.WEEK_VIEW;
                break;
            case this.WORK_WEEK_VIEW:
            case 'WorkWeekView':
                type = this.WORK_WEEK_VIEW;
                break;
            case 'month':
            case this.EXPANDED_MONTH_VIEW:
            case 'Month':
                type = this.EXPANDED_MONTH_VIEW;
                break;
            case this.AGENDA_VIEW:
                type = this.AGENDA_VIEW;
                break;
            case this.TIMELINE_VIEW:
            case 'TimelineView':
                type = this.TIMELINE_VIEW;
                break;
            case this.MONTH_SCHEDULE_VIEW:
            case 'Scheduler':
                type = this.MONTH_SCHEDULE_VIEW;
                break;
        }
        return type;
    },

    /**
     * Transforms a view title into an accepted View Title format
     *
     * @param {string} viewName
     * @return {string}
     */
    getViewTitle: function(viewName) {
        let title;
        switch (viewName) {
            case this.DAY_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_DAY', 'Calendar');
                break;
            case this.WEEK_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_WEEK', 'Calendar');
                break;
            case this.WORK_WEEK_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_WORKWEEK', 'Calendar');
                break;
            case 'month':
            case 'Month':
            case this.EXPANDED_MONTH_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_MONTH', 'Calendar');
                break;
            case this.AGENDA_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_AGENDA', 'Calendar');
                break;
            case this.TIMELINE_VIEW:
                title = app.lang.getModString('LBL_CALENDAR_VIEW_TIMELINE', 'Calendar');
                break;
            case this.MONTH_SCHEDULE_VIEW:
            case 'Scheduler':
                title = app.lang.getModString('LBL_CALENDAR_VIEW_SCHEDULERMONTH', 'Calendar');
                break;
        }
        return title;
    },

    /**
     * Bind export events
     *
     * After creating the scheduler, and it's on DOM, we need to attach the handlers for Export, Publish and Settings
     */
    _bindExportEvent: function() {
        this.$('.export_icalendar')
            .on(
                'click',
                _.bind(this._getICalIdentifier, this)
            );

        this.$('.publish_icalendar')
            .on(
                'click',
                _.bind(this._publishCalendar, this)
            );
        this.$('.userSettings')
        .on(
            'click',
            _.bind(this._userSettings, this)
        );
    },

    /**
     * Get iCal id
     *
     * Get the ICal id where we store calendar definitions
     */
    _getICalIdentifier: function() {
        const postData = {
            calendarConfigurations: this.calendars.compile()
        };
        app.api.call('create', app.api.buildURL('Calendar/getICalConfigurationsUID'), postData, {
            success: _.bind(function successSaveCalendarConfigurations(data) {
                if (this.disposed) {
                    return;
                }

                if (_.isEmpty(data.key)) {
                    app.alert.show('set-publish-key', {
                        level: 'info',
                        messages: app.lang.getModString('LBL_CALENDAR_CONFIGURE_PUBLISH_KEY', 'Calendar'),
                        autoClose: false
                    });
                    return;
                }
                this._exportCalendar(data);
            }, this),
            error: function errorSaveCalendarConfigurations(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }
        });
    },

    /**
     * Export ICal calendar
     *
     * @param {Object} options
     */
    _exportCalendar: function(options) {
        let sugarLocation = app.utils.getSiteUrl();
        sugarLocation = sugarLocation
            .replace(/#$/, '')
            .replace(/(index\.php)$/, '')
            .replace(/\/$/, '');
        const url = sugarLocation + '/' + app.config.serverUrl +
        '/Calendar/getICalData?type=ics&user_id=' + app.user.id + '&key=' + options.key +
        '&calendarsUID=' + options.calendarConfigurationUID + '&export=1';

        window.open(url);
    },

    /**
     * Publish ICal calendar
     */
    _publishCalendar: function() {
        this.addPublishModalElementOnDom();

        const postData = {
            calendarConfigurations: this.calendars.compile()
        };

        //obtain a publish key
        app.api.call('create', app.api.buildURL('Calendar/getICalPublishUrl'), postData, {
            success: function(data) {
                if (this.disposed) {
                    return;
                }

                if (data === 'empty_publish_key') {
                    app.alert.show('set-publish-key', {
                        level: 'info',
                        messages: app.lang.getModString('LBL_CALENDAR_CONFIGURE_PUBLISH_KEY', 'Calendar'),
                        autoClose: false
                    });
                    return;
                }
                //update modal
                $('[data-content=publish-icalendar-modal]')
                    .find('input')
                    .attr('value', data);

                //open modal
                $('[data-content=publish-icalendar-modal]').modal('show');
                //on close, remove it from body in order to not mess with other modals
                $('[data-content=publish-icalendar-modal]')
                    .on('hidden.bs.modal', function modalCloseHandler() {
                        $('[data-content=publish-icalendar-modal]').remove();
                    });
            },
            error: function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }
        });
    },

    /**
     * User settings
     *
     * Added possibility to set business hours
     */
    _userSettings: function() {
        this.addBusinessHoursElementOnDom();

        //update modal
        const calendarBusinessHours = app.user.lastState.get(this.keyToStoreCalendarBusinessHours);

        $('[name=startTime]').timepicker({
            timeFormat: app.user.getPreference('timepref'),
            disableTextInput: true
        });

        $('[name=endTime]').timepicker({
            timeFormat: app.user.getPreference('timepref'),
            disableTextInput: true
        });

        if (calendarBusinessHours) {
            $('[data-content=business-hours-modal]')
                .find('[name=startTime]')
                .timepicker('setTime', calendarBusinessHours.start);

            $('[data-content=business-hours-modal]')
                .find('[name=endTime]')
                .timepicker('setTime', calendarBusinessHours.end);
        }
        //open modal
        $('[data-content=business-hours-modal]').modal('show');
        //on close, remove it from body in order to not mess with other modals
        $('[data-content=business-hours-modal]')
            .on('hidden.bs.modal', function modalCloseHandler() {
                $('[data-content=business-hours-modal]').remove();
            });

        $('[name=saveHours]').on('click', _.bind(function() {
            this._saveBusinessHours();
        }, this));
    },

    /**
     * Add publish modal element on DOM
     */
    addPublishModalElementOnDom: function() {
        const modal = app.template.getView('scheduler.publish-modal', 'Calendar');
        $('body').append(modal());
    },

    /**
     * Add business hours modal element on DOM
     */
    addBusinessHoursElementOnDom: function() {
        const modal = app.template.getView('scheduler.business-hours', 'Calendar');
        $('body').append(modal());
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
     * Save business hours to local storage
     */
    _saveBusinessHours: function() {
        let startTime = $('[name=startTime]').val();
        let endTime = $('[name=endTime]').val();

        if (startTime && endTime) {
            startTime = app.date(startTime, app.date.convertFormat(app.user.getPreference('timepref')));
            endTime = app.date(endTime, app.date.convertFormat(app.user.getPreference('timepref')));

            const businessHours = {
                start: startTime.format('HH:mm'),
                end: endTime.format('HH:mm')
            };

            app.user.lastState.set(this.keyToStoreCalendarBusinessHours, businessHours);

            $('[data-content=business-hours-modal]').remove();
            $('.modal-backdrop').remove();

            //need to remove the old calendar and recreate it with the new options
            if (this.toolbarView) {
                this.toolbarView.dispose();
                this.$('.k-scheduler-toolbar.k-toolbar').remove();
            }

            if (this.confirmationPopupView) {
                this.confirmationPopupView.dispose();
            }

            this.stopListening(this.context, 'change:calendars');

            this.$('#' + this._schedulerCssId).find('.k-scheduler-layout').remove();
            this.$('#' + this._schedulerCssId).find('.k-scheduler-footer').remove();

            this.cleanupScheduler();
            this._createCalendar();

            app.events.trigger('calendar:reload');
        }
    },

    /**
     * Set business hours
     */
    _setBusinessHours: function() {
        let businessHours = app.user.lastState.get(this.keyToStoreCalendarBusinessHours);

        if (!businessHours) {
            businessHours = {
                start: '09:00',
                end: '17:00'
            };
            app.user.lastState.set(this.keyToStoreCalendarBusinessHours, businessHours);
        }
    },

    /**
     * Get business hours
     *
     * @param {string} momentIdentifier
     * @return {Object}
     */
    _getBusinessHours: function(momentIdentifier) {
        const businessHours = app.user.lastState.get(this.keyToStoreCalendarBusinessHours);
        return new Date(`2000-01-01 ${businessHours[momentIdentifier]}`);
    },

    /**
     * Get date header template
     *
     * @param {string} view
     * @return {string}
     */
    _getDateHeaderTemplate: function(view) {
        let dataHeaderTemplate = '';
        let kendoDateFormat = '';

        if (view === this.TIMELINE_VIEW) {
            kendoDateFormat = app.Calendar.utils.getKendoDateMapping(
                app.user.getPreference('datepref'), 'dayAndVerboseMonth'
            );
            dataHeaderTemplate = kendo.template(
                `<span class='k-link k-nav-day'>#=kendo.format('{0:${kendoDateFormat}}', date)#</span>`
            );
        } else if (view === this.MONTH_SCHEDULE_VIEW) {
            kendoDateFormat = app.Calendar.utils.getKendoDateMapping(app.user.getPreference('datepref'), 'dayAndMonth');
            dataHeaderTemplate = kendo.template(
                `<span class='k-link k-nav-day'>#=kendo.format('{0:m}', date)#</span>`
            );
        } else {
            kendoDateFormat = app.Calendar.utils.getKendoDateMapping(app.user.getPreference('datepref'), 'dayAndMonth');
            dataHeaderTemplate = kendo.template(`#var dateString = isMobile ? kendo.toString(date,'ddd')[0] :
                kendo.toString(date,'ddd ${kendoDateFormat}'); #<span class='k-link k-nav-day'>#=dateString#</span>`);
        }

        return dataHeaderTemplate;
    },

    /**
     * Get major time header template
     *
     * @return {string}
     */
    _getMajorTimeHeaderTemplate: function() {
        let kendoTimeFormat = app.Calendar.utils.getKendoTimeMapping(app.user.getPreference('timepref'));
        return `#=kendo.toString(date, '${kendoTimeFormat}')#`;
    },

    /**
     * Get selected date format
     *
     * @param {string} view
     * @return {string}
     */
    _getSelectedDateFormat: function(view) {
        const userDatePref = app.Calendar.utils.getKendoDateMapping(
            app.user.getPreference('datepref'), 'fullVerboseMonth'
        );
        let selectedDateFormat = '';
        switch (view) {
            case this.DAY_VIEW:
            case this.TIMELINE_VIEW:
                selectedDateFormat = `{0:dddd, ${userDatePref}}`;
                break;
            case this.WORK_WEEK_VIEW:
            case this.WEEK_VIEW:
            case this.AGENDA_VIEW:
            case this.MONTH_SCHEDULE_VIEW:
                selectedDateFormat = `{0:dddd, ${userDatePref}} - {1:dddd, ${userDatePref}}`;
                break;
        }

        return selectedDateFormat;
    },

    /**
     * Navigate to record view
     *
     * @param {Object} event
     */
    navigateToRecord: function(event) {
        let url;
        const moduleMeta = app.metadata.getModule(event.module);

        if (moduleMeta.isBwcEnabled) {
            url = '#bwc/index.php?module=' + event.module + '&action=DetailView&record=' +
                event.recordId;
            this._navigate(url);
        } else {
            url = '#' + event.module + '/' + event.recordId;
            this._navigate(url);
        }
    },

    /**
     * Destroy Kendo Scheduler and remove legend positioning event
     */
    cleanupScheduler: function() {
        if (typeof kendo != 'undefined' && this.scheduler instanceof kendo.ui.Scheduler) {
            this.scheduler.destroy();
            this.scheduler = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.toolbarView) {
            this.toolbarView.dispose();
        }
        if (this.confirmationPopupView) {
            this.confirmationPopupView.dispose();
        }

        this.stopListening(this.context);

        this.cleanupScheduler();

        this._super('_dispose');
    }
});

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
 * @class View.Views.Base.CalendarSchedulerDashletView
 * @alias SUGAR.App.view.views.BaseCalendarSchedulerDashletView
 * @extends View.Views.Base.View
 */
 ({
    plugins: ['Dashlet'],

    /**
     * @inheritdoc
     */
    events: {
        'click span[name=addCalendar]': '_addCalendar'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.listenTo(this.model, 'change:availableViews', _.bind(this.changeAvailableViewsEvent, this));
    },

    /**
     * Initialize the dashlet
     */
    initDashlet: function() {
        if (this.meta.config) {

            this.layout.before('dashletconfig:save', _.bind(function() {
                if (this.validateFields() === false) {
                    return false;
                }
            },this));

            let fields = this.dashletConfig.panels[0].fields;
            let availableViewsField = _.find(fields, function findAvailableViewsField(field) {
                return field.name === 'availableViews';
            });
            let defaultViewField = _.find(fields, function findDefaultViewField(field) {
                return field.name === 'defaultView';
            });

            if (availableViewsField && defaultViewField) {
                let currentlySelectedAvailableViewsKeys = this.settings.get('availableViews');
                defaultViewField.options = {};

                let availableList = app.lang.getAppListStrings(availableViewsField.options);
                _.each(currentlySelectedAvailableViewsKeys, function selectedViewsIterator(keyAvailable) {
                    defaultViewField.options[keyAvailable] = availableList[keyAvailable];
                });
            }
        } else {
            let myConfigurations = this.settings.get('myCalendars') || [];
            myConfigurations = _.map(myConfigurations, function(configuration) {
                configuration.userId = 'current_user';
                return configuration;
            });
            let otherConfigurations = this.settings.get('otherCalendars') || [];

            let otherCalendarsSelected = _.filter(otherConfigurations, function(calendar) {
                return calendar.selected === true;
            });

            let calendars = myConfigurations.concat(otherCalendarsSelected);
            let availableViews = _.clone(this.settings.get('availableViews'));
            if (!availableViews) {
                availableViews = [];
            }

            let defaultView = this.settings.get('defaultView');
            if (!defaultView) {
                defaultView = '';
            }

            let calendarContext = this.context.getChildContext();
            calendarContext.set({
                skipFetch: true,
                module: 'Calendar',
                calendars: calendars,
                availableViews: availableViews,
                defaultView: defaultView,
                visibleRelationsInContextList: this.settings.get('contextMenu'),
                location: 'dashboard',
                customKendoOptions: {
                    listExportButtons: false,
                }
            });

            this.listenTo(calendarContext, 'calendar:loaded', _.bind(function childCalendarWasLoaded() {
                if (this.disposed) {
                    return;
                }
                let dashletToolbar = this.layout.getComponent('dashlet-toolbar');
                let $el = dashletToolbar.$('[data-action=loading]');
                $el.removeClass(dashletToolbar.cssIconRefresh).addClass(dashletToolbar.cssIconDefault);
            }, this));

            this.schedulerView = app.view.createView({
                name: 'scheduler',
                type: 'scheduler',
                context: calendarContext,
                parentView: this
            });

            this.layout.$el.off('resizestop.updateEvents');

            this.layout.$el.on('resizestop.updateEvents', _.debounce(_.bind(function() {
                this.schedulerView.scheduler.resize();
            }, this), 200));
        }
    },

    /**
     * Only used for refresh dashlet button.
     * In rest, the calendar is loaded internally (schedulerView)
     *
     * @method
     * @param  {Object} options At the moment only interesting parameter is complete callback
     */
    loadData: function(options) {
        if (this.meta.config) {
            return;
        }

        if (this.schedulerView instanceof app.view.View === false || this.schedulerView.disposed === true) {
            app.alert.show('error-no-scheduler', {
                level: 'error',
                messages: app.lang.getModString('LBL_CALENDAR_NO_SCHEDULER_VIEW', 'Calendar'),
                autoClose: false
            });
            return;
        }

        if (typeof options !== 'undefined' && typeof options.complete !== 'undefined') {
            this.schedulerView.trigger('calendar:reload');
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        //load calendar data
        this.listenTo(this.schedulerView, 'calendar:initialized', _.bind(this.loadCalendarData, this));
        //this class helps in resizing calendar dashlet to fit calendar component
        this.$el
            .parent()
            .parent()
            .addClass('calendar');

        //we need to add scheduler html element to dom before initializing the scheduler (Scheduler view->render)
        this._super('_render');

        if (this.meta.config) {
            //add addCalendar button
            const addCalendarButton =
                '<span name=\'addCalendar\'><i class=\'sicon sicon-plus\' title=\'Add calendars\'></i></span>';

            this.$('[data-type=calendars][data-name=otherCalendars] > .record-label').append(addCalendarButton);

            this.$el.addClass('mediumDots');
        } else {
            if (this.schedulerView instanceof app.view.View === true &&
                typeof this.schedulerView.disposed === 'undefined') {

                this.$('[data-content=scheduler]').html(this.schedulerView.$el);

                this.schedulerView.render();
            } else {
                app.alert.show('error-no-scheduler', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_CALENDAR_NO_SCHEDULER_VIEW', 'Calendar'),
                    autoClose: false
                });
                return;
            }
        }
    },

    /**
     *
     * Change available views event
     */
    changeAvailableViewsEvent: function() {
        let fields = this.fields;
        let availableViewsField = _.find(fields, function findAvailableViewsField(field) {
            return field.name === 'availableViews';
        });
        let defaultViewField = _.find(fields, function findDefaultViewField(field) {
            return field.name === 'defaultView';
        });

        if (availableViewsField && defaultViewField) {
            let currentlySelectedAvailableViewsKeys = this.settings.get('availableViews');
            defaultViewField.def.options = {};

            let availableList = app.lang.getAppListStrings(availableViewsField.def.options);

            _.each(currentlySelectedAvailableViewsKeys, function availableViewsIterator(keyAvailable) {
                defaultViewField.def.options[keyAvailable] = availableList[keyAvailable];
            });

            defaultViewField.items = defaultViewField.def.options;

            if (currentlySelectedAvailableViewsKeys.indexOf(this.settings.get('defaultView')) === -1) {
                if (currentlySelectedAvailableViewsKeys.length >= 1) {
                    defaultViewField.value = [currentlySelectedAvailableViewsKeys[0]];
                    this.settings.set('defaultView', currentlySelectedAvailableViewsKeys[0]);
                } else {
                    defaultViewField.value = [''];
                    this.settings.set('defaultView', '');
                }
            }

            defaultViewField.render();
        }
    },

    /**
     * Reconfigure calendar
     */
    loadCalendarData: function() {
        this.schedulerView._reconfigureCalendar();
    },

    /**
     *
     * @inheritdoc
     */
    _dispose: function() {
        if (this.schedulerView instanceof app.view.View) {
            this.schedulerView.dispose();
        }

        delete this.schedulerView;

        this._super('_dispose');
    },

    /**
     * Validates dashlet fields marked as required
     *
     * @return {boolean}
     */
    validateFields: function() {
        let _validModel = true;

        _.each(this.fields, _.bind(function checkRequired(field) {
            if (field.def.required === true && _.isEmptyValue(this.dashModel.get(field.name))) {

                field.model.trigger('error:validation:' + field.name, {
                    'required': true
                });

                _validModel = false;
            }
        },this));

        return _validModel;
    },

    /**
     * Add calendar option
     */
    _addCalendar: function() {
        app.drawer.open(
            {
                layout: 'add-calendar',
                context: {
                    module: 'Calendar',
                    mixed: true,
                    dashletSource: true
                }
            },
            _.bind(function close(calendar) {
                if (typeof calendar === 'undefined') {
                    return;
                }

                let otherCalendars = this.settings.get('otherCalendars') || [];
                otherCalendars = app.utils.deepCopy(otherCalendars);

                let calendarAlreadyAdded = false;
                _.each(otherCalendars, function searchCalendar(otherCalendar) {
                    if (otherCalendar.calendarId === calendar.calendarId &&
                        (_.isEmpty(otherCalendar.userId) || otherCalendar.userId === calendar.userId) &&
                        (_.isEmpty(otherCalendar.teamId) || otherCalendar.teamId === calendar.teamId)
                    ) {
                        calendarAlreadyAdded = true;
                    }
                });

                if (calendarAlreadyAdded === true) {
                    app.alert.show('calendar-already-added', {
                        level: 'info',
                        messages: app.lang.get('LBL_CALENDAR_CALENDAR_ALREADY_ADDED', 'Calendar'),
                        autoClose: true
                    });
                    return;
                } else {
                    otherCalendars.push(_.extend(calendar, {selected: true}));
                }

                this.settings.set('otherCalendars', otherCalendars);

                this.getField('otherCalendars').render();

            }, this)
        );
    }
});

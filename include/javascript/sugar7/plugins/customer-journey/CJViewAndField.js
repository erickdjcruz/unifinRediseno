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

(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('CJViewAndField', ['view', 'field'], {

            /**
             * Toggles the display of all buttons
             */
            toggleButtons: function() {
                _.each(
                    this.fields,
                    function(field) {
                        this.toggleField(field);

                        if (field.fields) {
                            _.each(field.fields, _.bind(this.toggleField, this));
                        }
                    },
                    this
                );
            },

            /**
             * Toggles the display of the field
             *
             * @param {Object} field
             */
            toggleField: function(field) {
                if (field.def.type === 'rowaction') {
                    switch (field.name) {
                        case 'activity_not_applicable_button':
                            if (!this.isClosableForNotApplicable(field.model)) {
                                this.hideField(field);
                            }
                            break;
                        case 'activity_complete_button':
                            if (this.isParent(field.model)) {
                                field.def.tooltip = 'LBL_COMPLETE_PARENT_BUTTON_TITLE';
                                field.tooltip = 'LBL_COMPLETE_PARENT_BUTTON_TITLE';
                                field.render();
                            }
                            if (!this.isClosable(field.model)) {
                                this.hideField(field);
                            }
                            break;
                        case 'activity_start_button':
                            if (!this.isStartable(field.model)) {
                                this.hideField(field);
                            }
                            break;
                        case 'activity_assign_me_button':
                            if (!this.isAssignable(field.model)) {
                                this.hideField(field);
                            }
                            break;
                        case 'journey_cancel_button':
                            if (['completed', 'cancelled'].includes(field.model.get('state'))) {
                                this.hideField(field);
                            }
                            break;
                        case 'journey_add_stage_button':
                            if (field.model.get('state') === 'cancelled') {
                                this.hideField(field);
                            }
                            break;
                        case 'journey_archive_button':
                            if ((field.model.get('state') !== 'completed' &&
                                field.model.get('state') !== 'cancelled') ||
                                field.model.get('archived')) {
                                this.hideField(field);
                            }
                            break;
                        case 'journey_unarchive_button':
                            if (!field.model.get('archived')) {
                                this.hideField(field);
                            }
                            break;
                        case 'activity_template_preview_button':
                            // When the user does not have access to edit the activity we should remove the label since
                            // The preview button will be displayed first instead of the complete button.
                            if (!app.acl.hasAccessToModel('edit', field.model, field)) {
                                field.$('i').parent().html(field.$('i'));
                                field.def.label = ' ';
                                field.label = ' ';
                                field.def.tooltip = 'LBL_PREVIEW';
                                field.render();
                            }
                            break;
                    }

                    if (_.contains(this.model.get('disabled_stage_actions'), field.name) ||
                        _.contains(this.model.get('disabled_activity_actions'), field.name)) {
                        // don't disable actions on workflow template
                        if (this.module !== 'DRI_Workflow_Templates') {
                            this.hideField(field);
                        }
                    }
                }
            },

            /**
             * Hide Field on render
             *
             * @param {Object} field
             */
            hideField: function(field) {
                field.hide();
                const id = field.model.get('id');
                const option = 'dri_workflow_templates_disabled_activity_actions_list';
                const options = 'dri_subworkflows_state_list';
                const optionsCalls = 'cj_meetings_completed_status_list';
                const optionsMeetings = 'cj_meetings_completed_status_list';
                const callStatus = 'call_status_dom';
                const statusNap = app.lang.getAppListStrings(option).activity_not_applicable_button;
                const statusComplete = app.lang.getAppListStrings(options).completed;
                const statusHeld = app.lang.getAppListStrings(optionsCalls).Held;
                const statusNotHeld = app.lang.getAppListStrings(optionsMeetings)['Not Held'];
                const statusInprogress = app.lang.getAppListStrings(callStatus)['In Progress'];
                const activityID = $(`[data-id="${id}"]`);
                const timeZone = '.dri-subworkflow-activity-actions > .activity-due-date';
                const statuses = [statusNap, statusComplete, statusHeld, statusNotHeld, statusInprogress];
                if (!_.isEmpty(field.model.get('forms')) &&
                (statuses.includes(field.model.get('status')))) {
                    const rsaIcon = '.btn.activity-form';
                    activityID.find(rsaIcon).hide();
                    activityID.find(timeZone).hide();
                } else if ((statuses.includes(field.model.get('status')))) {
                    activityID.find(timeZone).hide();
                }

                // This field may not have been rendered the first time here, make sure to hide it once it gets rendered
                this.listenTo(field, 'render', function() {
                    field.hide();
                });
            },

            /**
             * Checks if an activity is startable
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isStartable: function(activity) {
                switch (activity.module) {
                    case 'Tasks':
                    case 'Calls':
                        return (
                            activity.get('status') !== 'In Progress' &&
                            activity.get('status') !== 'Held' &&
                            activity.get('status') !== 'Not Held' &&
                            activity.get('status') !== 'Completed' &&
                            activity.get('status') !== 'Not Applicable' &&
                            !activity.get('is_cj_parent_activity') &&
                            !this.isBlocked(activity) &&
                            !this.isBlockedByStages(activity)
                        );
                    default:
                        return false;
                }
            },

            /**
             * Checks if an activity is closable
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isClosable: function(activity) {
                switch (activity.module) {
                    case 'Tasks':
                        return (
                            activity.get('status') !== 'Completed' &&
                            activity.get('status') !== 'Not Applicable' &&
                            activity.get('status') !== 'Deferred' &&
                            !this.isBlocked(activity) &&
                            !this.isBlockedByStages(activity)
                        );
                    case 'Meetings':
                    case 'Calls':
                        return (
                            activity.get('status') !== 'Held' &&
                            activity.get('status') !== 'Not Held' &&
                            activity.get('status') !== 'Deferred' &&
                            !this.isBlocked(activity) &&
                            !this.isBlockedByStages(activity)
                        );
                    default:
                        return false;
                }
            },

            /**
             * Checks if an activity is closable for Not Applicable
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isClosableForNotApplicable: function(activity) {
                switch (activity.module) {
                    case 'Tasks':
                        return (
                            activity.get('status') !== 'Completed' && activity.get('status') !== 'Not Applicable' &&
                            !this.isBlocked(activity) && !this.isBlockedByStages(activity)
                        );
                    case 'Meetings':
                    case 'Calls':
                        return activity.get('status') !== 'Held' && activity.get('status') !== 'Not Held' &&
                            !this.isBlocked(activity) && !this.isBlockedByStages(activity);
                    default:
                        return false;
                }
            },

            /**
             * Checks if an activity is closed
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isClosed: function(activity) {
                let optionList;
                switch (activity.module) {
                    case 'Tasks':
                        optionList = 'cj_tasks_completed_status_list';
                    case 'Meetings':
                        optionList = 'cj_meetings_completed_status_list';
                    case 'Calls':
                        optionList = 'cj_calls_completed_status_list';
                    default:
                        optionList = false;
                }
                if (!optionList) {
                    return optionList;
                }
                return !!app.lang.getAppListStrings(optionList)[activity.get('status')];
            },

            /**
             * Checks if an activity is assignable
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isAssignable: function(activity) {
                return activity.get('assigned_user_id') !== app.user.id;
            },

            /**
             * Formats the stage
             * Arranges the order of activities
             *
             * @param {Object} data
             * @return {Object}
             */
            formatStage: function(data) {
                var order = 1;
                var stage = app.data.createBean(this.stageModule, data);
                var row = this.createStageData(stage);

                _.each(
                    data.activities,
                    function(data) {
                        var activity = this.formatActivity(data, order);
                        row.activities[activity.model.id] = activity;
                        order++;
                    },
                    this
                );

                return row;
            },

            /**
             * Create the stage data
             *
             * @param {Object} stage
             * @return {Object}
             * @private
             */
            createStageData: function(stage) {
                return {
                    data: stage.attributes,
                    model: stage,
                    stateClass: '',
                    activities: {}
                };
            },

            /**
             * Formats the activity
             * Arranges the order of child activities
             *
             * @param {Object} data
             * @param {int} order
             * @return {Object}
             */
            formatActivity: function(data, order) {
                var activity = app.data.createBean(data._module, data);

                this.activities[activity.id] = activity;

                var row = this.createActivityData(activity, order);

                if (data.children && data.children.length) {
                    var childOrder = 1;

                    _.each(
                        data.children,
                        function(data) {
                            var childRow = this.formatActivityChild(data, `${order}.${childOrder}`);
                            row.children[childRow.model.id] = childRow;
                            this.activities[childRow.model.id] = childRow.model;
                            childOrder++;
                        },
                        this
                    );
                }

                return row;
            },

            /**
             * Formats the child activity
             *
             * @param {Object} data
             * @param {int} order
             * @return {Object}
             */
            formatActivityChild: function(data, order) {
                var child = app.data.createBean(data._module, data);
                return this.createActivityData(child, order);
            },

            /**
             * Builds the activity data for presentation
             *
             * @param {Object} activity
             * @param {string|int} order
             * @return {Object}
             */
            createActivityData: function(activity, order) {
                var picture = activity.get('assigned_user') && activity.get('assigned_user').picture;
                var forms = activity.get('forms') || [];

                activity.set(
                    'pictureUrl',
                    picture ? app.api.buildFileURL(
                        {
                            module: 'Users',
                            id: activity.get('assigned_user').id,
                            field: 'picture',
                        },
                        {cleanCache: true}
                    )
                        : ''
                );

                return {
                    data: activity.attributes,
                    module: activity.module,
                    url: activity.get(this.activityUrlField),
                    forms: this.isClosed(activity) || this.isBlocked(activity) || this.isBlockedByStages(activity) ||
                        this.isParent(activity) ? [] : forms,
                    blockedBy: this.getBlockedByInfo(activity),
                    blockedByStages: this.getBlockedByInfoForStages(activity),
                    model: activity,
                    order: order,
                    icon: this.getIconByActivityType(activity),
                    iconTooltip: this.getIconTooltip(activity),
                    statusLabel: this.getStatusLabel(activity),
                    statusClass: this.getStatusClass(activity),
                    startDate: this.getStartDateInfo(activity),
                    dueDate: this.getDueDateInfo(activity),
                    typeClass: this.getTypeClass(activity),
                    isParent: this.isParent(activity),
                    showChildren: this.getActivityDisplayChildren(activity.id) === this.MORE_LESS_STATUS.MORE,
                    children: {}
                };
            },

            /**
             * Check if activity is blocked
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isBlocked: function(activity) {
                return activity.get('blocked_by') && activity.get('blocked_by').length;
            },

            /**
             * Check if activity is blocked by stage
             *
             * @param {Object} activity
             * @return {boolean}
             */
            isBlockedByStages: function(activity) {
                return activity.get('blocked_by_stages') && activity.get('blocked_by_stages').length;
            },
            /**
             * Get the blocked by info for the activity
             * Blocked by activity(ies)
             *
             * @param {Object} activity
             * @return {Object|boolean}
             */
            getBlockedByInfo: function(activity) {
                if (!this.isBlocked(activity)) {
                    return false;
                }

                var info = {
                    text: `${app.lang.get('LBL_BLOCKED_BY', 'DRI_Workflows')}:`,
                };

                var count = 0;
                _.each(
                    activity.get('blocked_by'),
                    function(id) {
                        if (activity.id !== id && this.journeyActivities[id]) {
                            info.text += `\n${this.journeyActivities[id]}`;
                            count++;
                        }
                    },
                    this
                );

                if (0 === count) {
                    return false;
                }

                return info;
            },

            /**
             * Get the blocked by info for the activity
             * Blocked by stage(s)
             *
             * @param {Object} activity
             * @return {Object|boolean}
             */
            getBlockedByInfoForStages: function(activity) {
                if (!this.isBlockedByStages(activity)) {
                    return false;
                }

                var info = {
                    text: `${app.lang.get('LBL_BLOCKED_BY_STAGES', 'DRI_Workflows')}:`,
                };

                var count = 0;
                _.each(
                    activity.get('blocked_by_stages'),
                    function(id) {
                        if (activity.dri_subworkflow_template_id !== id && this.journeyStages[id]) {
                            info.text += `\n${this.journeyStages[id]}`;
                            count++;
                        }
                    },
                    this
                );

                if (0 === count) {
                    return false;
                }

                return info;
            },

            /**
             * Get the due date info
             *
             * @param {Object} activity
             * @return {Object|boolean}
             */
            getDueDateInfo: function(activity) {
                var dueDateFields = {
                    Tasks: 'date_due',
                    Calls: 'date_start',
                    Meetings: 'date_start'
                };

                var fieldName = dueDateFields[activity.module];

                if (_.isEmpty(activity.get(fieldName)) || this.isClosed(activity)) {
                    return false;
                }

                var emptyDate = app.date('2100-01-01T12:00:00');
                var date = app.date(activity.get(fieldName));
                var now = app.date();
                var tomorrow = app.date().add(1, 'day');

                if (date.format('YYYY') === emptyDate.format('YYYY')) {
                    return false;
                }

                var info = {
                    fromNow: date.fromNow(),
                    formatUser: date.formatUser(),
                    fieldName: app.lang.get(activity.fields[fieldName].vname, activity.module)
                };

                if (date.isBefore(now)) {
                    info.className = 'overdue';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_OVERDUE', activity.module);
                } else if (date.format('YYYY-MM-DD') === now.format('YYYY-MM-DD')) {
                    info.className = 'today';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TODAY', activity.module);
                } else if (date.format('YYYY-MM-DD') === tomorrow.format('YYYY-MM-DD')) {
                    info.className = 'tomorrow';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TOMORROW', activity.module);
                } else {
                    info.className = 'future';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_FUTURE', activity.module);
                }

                info.title = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TEXT', activity.module, info);

                if (date.format('YYYY') === now.format('YYYY')) {
                    info.text = date.format('MMM D');
                } else {
                    info.text = date.format('MMM D, YYYY');
                }

                return info;
            },

            /**
             * Get the start date info
             *
             * @param {Object} activity
             * @return {Object|boolean}
             */
            getStartDateInfo: function(activity) {
                let startDateFields = {
                    Calls: 'date_end',
                };

                let fieldName = startDateFields[activity.module];

                if (_.isUndefined(fieldName) || _.isEmpty(activity.get(fieldName)) || this.isClosed(activity)) {
                    return false;
                }

                let emptyDate = app.date('2100-01-01T12:00:00');
                let date = app.date(activity.get(fieldName));
                let now = app.date();
                let tomorrow = app.date().add(1, 'day');

                if (date.format('YYYY') === emptyDate.format('YYYY')) {
                    return false;
                }

                let info = {
                    fromNow: date.fromNow(),
                    formatUser: date.formatUser(),
                    fieldName: app.lang.get(activity.fields[fieldName].vname, activity.module)
                };

                if (date.isBefore(now)) {
                    info.className = 'future';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_FUTURE', activity.module);
                } else if (date.format('YYYY-MM-DD') === now.format('YYYY-MM-DD')) {
                    info.className = 'today';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TODAY', activity.module);
                } else if (date.format('YYYY-MM-DD') === tomorrow.format('YYYY-MM-DD')) {
                    info.className = 'tomorrow';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TOMORROW', activity.module);
                } else {
                    info.className = 'overdue';
                    info.status = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_OVERDUE', activity.module);
                }

                info.title = app.lang.get('LBL_CUSTOMER_JOURNEY_ACTIVITY_DUE_DATE_TEXT', activity.module, info);

                if (date.format('YYYY') === now.format('YYYY')) {
                    info.text = date.format('MMM D');
                } else {
                    info.text = date.format('MMM D, YYYY');
                }

                return info;
            },
        });
    });
})(SUGAR.App);

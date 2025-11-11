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
        app.plugins.register('CJEvents', ['view', 'field'], {

            /**
             * Toggles the panel when the workflowInfo div gets clicked
             */
            workflowInfoClicked: function() {
                if (this.isCJRenderedAsTab()) {
                    return;
                }

                if (this.actionBtnClicked) {
                    this.actionBtnClicked = false;
                    return;
                }

                let moreLess = this.hidePanel ? this.MORE_LESS_STATUS.MORE : this.MORE_LESS_STATUS.LESS;

                this.addRemoveClasses(moreLess);
            },

            /**
             * Add and remove CSS classes when journey is toggled more / less
             *
             * @param {boolean} moreLess
             */
            addRemoveClasses: function(moreLess) {
                let element = this.$el.find(`[data-id='${this.model.get('id')}']`);
                let workflowInfo = element.find('.dri-workflow-info');
                let workflowCard = element.find('.dri-workflow-card');

                if (moreLess === this.MORE_LESS_STATUS.MORE) {
                    workflowInfo.removeClass('dri-workflow-wrapper-closed');
                    workflowCard.removeClass('dri-workflow-wrapper-closed');
                    this.$el.addClass('open-border');
                } else {
                    workflowInfo.addClass('dri-workflow-wrapper-closed');
                    workflowCard.addClass('dri-workflow-wrapper-closed');
                    this.$el.removeClass('open-border');
                }

                this.toggleMoreLess(moreLess);
            },

            /**
             * Set action button clicked flag true to stop worflowInfoClicked action
             */
            actionButtonsClicked: function() {
                this.actionBtnClicked = true;
            },

            /**
             * Set action button clicked flag true to stop worflowInfoClicked action
             */
            actionButtonsClicked: function() {
                this.actionBtnClicked = true;
            },

            /**
             * Highlights the activity that another activity is blocked by
             *
             * @param {event} ev
             */
            blockedActivityClicked: function(ev) {
                var $el = $(ev.currentTarget);
                if ($el.hasClass('cj_blocked')) {
                    var id = $el.data('id');
                    var model = this.activities[id];

                    _.each(
                        model.get('blocked_by'),
                        function(blockedById) {
                            if (id !== blockedById && this.activities[blockedById]) {
                                var blockedBy = this.activities[blockedById];
                                var $blockedBy = this.$(`.dri-subworkflow-activity[data-id='${ blockedById }']`);
                                $blockedBy.effect('highlight', {color: '#e61718'});

                                if (!_.isEmpty(blockedBy.get(this.parentActivityId))) {
                                    this.showActivityChildren(blockedBy.get(this.parentActivityId));
                                }
                            }
                        },
                        this
                    );
                }
            },

            /**
             * Highlights the stage that another activity is blocked by
             *
             * @param {event} ev
             */
            blockedByStageActivityClicked: function(ev) {
                var $el = $(ev.currentTarget);
                if ($el.hasClass('cj_blocked_by_stage')) {
                    var id = $el.data('id');
                    var model = this.activities[id];

                    _.each(
                        model.get('blocked_by_stages'),
                        function(blockedByStageId) {
                            if (model.dri_subworkflow_template_id !== blockedByStageId &&
                                this.stages[blockedByStageId]) {
                                var blockedByStage = this.stages[blockedByStageId];
                                var $blockedByStage = this.$(`.dri-subworkflow[data-id='${ blockedByStageId }']`);
                                $blockedByStage.effect('highlight', {color: '#e61718'});
                            }
                        },
                        this
                    );
                }
            },

            /**
             * Sets the current journey as the Active
             * The active journeys are the one that are displayed in the chart dashlet
             */
            activeCycleClicked: function() {
                var parentModel = this.context.parent && this.context.parent.get('parentModel');
                parentModel && parentModel.trigger('customer_journey:active-cycle:click', this.model.id);
            },

            /**
             * Event handler when clicking on the preview eye icon on an activity
             *
             * @param {event} ev
             */
            previewActivityClicked: function(ev) {
                var id = $(ev.currentTarget).data('id');
                var activity = this.activities[id];
                this.previewModel(activity);
            },

            /**
             * remove the components from the given layout.
             *
             * @param {Object} layout
             * @param {Object} disposedComponents
             */
            disposeComponents: function(layout = null, disposedComponents = []) {
                if (_.isEmpty(layout) || _.isEmpty(disposedComponents)) {
                    return;
                }
                _.each(disposedComponents, function(compName, key) {
                    var comp = layout.getComponent(compName);
                    if (comp) {
                        comp.dispose();
                    }
                });
            },

            /**
             * Deletes a model.
             *
             * @param {Object} model
             */
            deleteModelClick: function(model) {
                let labelName = `${model.get('name')} activity`;
                labelName += _.isEqual(model.module, 'DRI_Workflow_Task_Templates') ? ' template' : '';

                app.alert.show('delete_activity', {
                    level: 'confirmation',
                    messages: app.lang.get('NTC_DELETE_CONFIRMATION', model.module),
                    onConfirm: _.bind(function() {
                        this.disablingJourneyAndStartLoading();
                        model.destroy({
                            success: _.bind(function() {
                                this.reloadData();
                                app.alert.show('success', {
                                    level: 'success',
                                    autoClose: true,
                                    messages: app.lang.get(
                                        'LBL_DELETE_SUCCESS_MESSAGE',
                                        'DRI_Workflows',
                                        {labelName: labelName}
                                    )
                                });
                            }, this),
                            error: _.bind(function(result) {
                                this.reloadData();
                                app.alert.show('error', {
                                    level: 'error',
                                    messages: result.message,
                                    autoClose: true
                                });
                            }, this),
                        });
                    }, this)
                });
            },

            /**
             * Opens up the drawer in edit mode to edit a given stage
             *
             * @param {Object} stage
             */
            editStageClick: function(stage) {
                var context = this.getStageContextById(stage.get('id'));

                stage.fetch({
                    success: _.bind(function() {
                        context.set('create', true);
                        context.get('model').link = null;

                        app.drawer.open({
                            module: stage.module,
                            layout: 'create',
                            context: context
                        }, _.bind(function(context, model) {
                            // Only reload if the model was saved, if not - revert all attributes from last sync
                            if (model) {
                                this.reloadData();
                            } else {
                                stage.revertAttributes();
                            }
                        }, this));
                    }, this),
                });
            },

            /**
             * Opens up the drawer in edit mode to edit a given activity
             *
             * @param {Object} activity
             */
            editActivityClick: function(activity) {
                var stageContext = this.getStageContextById(activity.get(this.activityStageId));

                activity.fetch({
                    success: _.bind(function() {
                        var context = stageContext.getChildContext({
                            module: activity.module,
                            model: activity,
                            forceNew: true,
                            create: true
                        });

                        app.drawer.open({
                            module: activity.module,
                            layout: 'create',
                            context: context
                        }, _.bind(function(context, model) {
                            //Only reload if the model was saved, if not - revert all attributes from last sync
                            if (model) {
                                this.reloadData();
                            } else {
                                activity.revertAttributes();
                            }
                        }, this));
                    }, this),
                });
            },

            /**
             * Checks if Activity status attribute is read-only
             *
             * @param {Object} activity
             */
            isStatusReadOnly: function(activity) {
                if (!_.isUndefined(activity) && !_.isUndefined(activity.get('is_status_readonly'))) {
                    if (app.utils.isTruthy(activity.get('is_status_readonly'))) {
                        return true;
                    }
                }
                return false;
            },

            /**
              * Handles starting a given activity
              *
              * @param {Object} activity
              */
            startActivityClick: function(activity) {
                if (!this.isStatusReadOnly(activity)) {
                    app.alert.show('processing_start_activity_click', {
                        level: 'process',
                        title: app.lang.get('LBL_PROCESSING_REQUEST'),
                    });
                    this.handleForms(
                        activity,
                        'in_progress',
                        _.bind(function() {
                            this.startActivity(activity);
                            app.alert.dismiss('processing_start_activity_click');
                        }, this)
                    );
                    this.enablingJourneyAndDoneLoading();
                } else {
                    this.showReadOnlyErrorMessage('error', 'LBL_ACTIVITY_STATUS_READ_ONLY');
                }
            },

            /**
             * Starts a given activity
             *
             * @param {Object} activity
             */
            startActivity: function(activity) {
                var url = '';
                if (activity.module === 'Tasks' || activity.module === 'Calls') {
                    this.disablingJourneyAndStartLoading();
                    [url, data] = this.buildUrlActivityStatusUpdateApi(activity, 'In Progress');
                    this.callActivityStatusUpdateApi(url, activity, data);
                }
            },

            /**
            * Show processing alert
            *
            * @param {string} name
            * @param {string} message
            */
            showProcessingAlert: function(name, message) {
                app.alert.show(name || 'process_complete_activity', {
                    level: 'process',
                    title: message || app.lang.get('LBL_CJ_PROCESSING_REQUEST'),
                    autoClose: false,
                });
            },

            /**
             * Provide the completed and not applicable statuses of activities
             *
             * @return {Object}
             */
            getCompleteStatusList: function() {
                return _.extend(
                    app.lang.getAppListStrings('cj_calls_completed_status_list'),
                    app.lang.getAppListStrings('cj_tasks_completed_status_list')
                );
            },

            /**
            * Get count of child activities that are neither completed nor not-applicable
            *
            * @param {Object} activity
            * @return {number}
            */
            getNotCompletedChildrenCount: function(activity) {
                const children =  activity.get('children');

                if (_.isUndefined(children)) {
                    return 0;
                }

                let count = 0;
                const statusList = this.getCompleteStatusList();

                _.each(children, function(childActivity) {
                    if (!statusList[childActivity.status]) {
                        count++;
                    }
                });

                return count;
            },

            /**
            * On confirm of complete activty click
            *
            * @param {string} activityID
            */
            completeActivityClickConfirm: function(activityID) {
                let activity = this.activities[activityID];

                if (!_.isUndefined(activity)) {
                    if (!this.isStatusReadOnly(activity)) {
                        const count = this.validateChildren(activity);

                        if (count < 1) {
                            this.readOnlyChildActivity = false;
                            this.isActivityChangeNotAllowed = false;
                            this.showProcessingAlert();
                            this.disablingJourneyAndStartLoading();

                            const statusList = this.getCompleteStatusList();
                            if (activity.get('is_cj_parent_activity')) {
                                this.completingParentActivity = activity;
                            }
                            _.each(activity.get('children'), (childActivity) => {
                                const childModel = this.activities[childActivity.id];
                                const status = childModel.get('status');
                                this.childActivitiesCount = 0;

                                if (!statusList[status]) {
                                    this.handleFormsForActivities(childModel, activity);
                                }
                            }, this);
                        } else {
                            this.blockByWarnings(this.activities[activity.id], count);
                        }
                    } else {
                        this.showReadOnlyErrorMessage('error', 'LBL_CHILD_ACTIVITY_STATUS_READ_ONLY');
                    }
                }
            },

            /**
            * Handles completing a given activity
            *
            * @param {Object} activity
            * @param {boolean} isClicked
            */
            completeActivityClick: function(activity, isClicked = false) {
                if (this.isParent(activity)) {
                    app.alert.show('success', {
                        level: 'confirmation',
                        messages: app.lang.get(
                            'LBL_CJ_COMPLETE_ALL_TASKS_CONFIRMATION',
                            this.model.module
                        ),
                        onConfirm: _.bind(this.completeActivityClickConfirm, this, activity.id)
                    });
                } else {
                    if (!this.isStatusReadOnly(activity)) {
                        if (isClicked) {
                            this.showProcessingAlert();
                            this.disablingJourneyAndStartLoading();
                        }

                        this.handleFormsForActivities(activity);
                    } else {
                        this.showReadOnlyErrorMessage('error', 'LBL_ACTIVITY_STATUS_READ_ONLY');
                    }
                }
            },

            /**
            * Validating activity children
            *
            * @param {Object} activity
            * @param {Object} object
            */
            validateChildren: function(activity) {
                let count = 0;

                _.each(activity.get('children'), (childActivity) => {
                    if (!_.isEmpty(childActivity.blocked_by) ||
                        !_.isEmpty(childActivity.blocked_by_stages)) {
                        count++;
                    }
                });

                return count;
            },

            /**
            * Show alerts for bloacked_by activities
            *
            * @param {Object} activity
            * @param {Int} count
            */
            blockByWarnings: function(activity, count) {
                if (count === 1) {
                    app.alert.show('bloack_by_children', {
                        level: 'warning',
                        messages: app.lang.get('LBL_CJ_BLOCK_BY',
                            this.model.module,
                            {Name: this.getBlockedByActivityName(activity)}
                        )
                    });
                } else {
                    app.alert.show('bloack_by_multiple_children', {
                        level: 'warning',
                        messages: app.lang.get('LBL_CJ_MULTIPLE_BLOCK_BY'),
                    });
                }
            },

            /**
            * Provide blocked_by activity name
            *
            * @param {Object} activity
            */
            getBlockedByActivityName: function(activity) {
                let childActivityName = '';

                _.each(activity.get('children'), (childActivity) => {
                    if (!_.isEmpty(childActivity.blocked_by) ||
                        !_.isEmpty(childActivity.blocked_by_stages)) {
                        childActivityName = childActivity.name;
                    }
                });

                return childActivityName;
            },

            /**
            * Handles forms for a given activity
            *
            * @param {Object} activity
            * @param {Object} parent
            */
            handleFormsForActivities: function(activity, parent) {
                this.handleForms(
                    activity,
                    'completed',
                    _.bind(this.completeActivity, this, activity, parent)
                );
            },

            /**
            * Completes a given activity
            *
            * @param {Object} activity
            * @param {Object} parent
            */
            completeActivity: function(activity, parent) {
                if (this.completingActivity) {
                    if (this.completingActivity !== activity && _.indexOf(this.completeQueue, activity) === -1) {
                        this.completeQueue.push(activity);
                    }
                    return;
                }

                this.completingActivity = activity;
                let preStatus = activity.get('status');
                let updatedStatus = '';

                switch (activity.module) {
                    case 'Tasks':
                        updatedStatus = 'Completed';
                        break;
                    case 'Meetings':
                    case 'Calls':
                        updatedStatus = 'Held';
                        break;
                    default:
                        return;
                }

                let fields = {
                    status: activity.fields.status,
                };

                [url, data] = this.buildUrlActivityStatusUpdateApi(activity, updatedStatus, parent);
                activity.doValidate(
                    fields,
                    _.bind(function(isValid) {
                        if (isValid) {
                            app.api.call('create', url, data, null, {
                                success: _.bind(function(response) {
                                    if (!response.isValid) {
                                        app.alert.dismiss('process_complete_activity');
                                        this.alertForRequiredFieldDependency(response);
                                        this.enablingJourneyAndDoneLoading();
                                    } else if (!_.isUndefined(response.isValidParent) &&
                                        response.isValidParent === false && !_.isUndefined(this.completeQueue)) {
                                        this.completeQueue = [];
                                        app.alert.dismiss('process_complete_activity');
                                        this.showNotAllowedErrorMessage();
                                        this.enablingJourneyAndDoneLoading();
                                    } else if (!_.isUndefined(response.isActivityChangeNotAllowed) &&
                                        response.isActivityChangeNotAllowed === true &&
                                        !_.isUndefined(this.completeQueue)) {
                                        this.isActivityChangeNotAllowed = true;
                                        this.completeActivitySuccess(activity, response.data);
                                    } else {
                                        if (!_.isUndefined(response.isChildReadOnly) &&
                                            response.isChildReadOnly === true) {
                                            this.readOnlyChildActivity = true;
                                        }
                                        this.completeActivitySuccess(activity, response.data);
                                    }
                                }, this),
                                error: _.bind(this.completeActivityError, this),
                                complete: _.bind(function() {
                                    this.completingActivity = false;
                                    if (!this.disposed) {
                                        this.render();
                                    }
                                    app.alert.dismiss('process_complete_activity');
                                }, this),
                            });
                        } else {
                            this.completingActivity = false;
                            activity.set('status', preStatus);
                            app.alert.dismiss('process_complete_activity');
                            app.alert.show('error', {
                                level: 'error',
                                messages: 'Validation failed',
                                autoClose: true,
                            });
                        }
                    }, this)
                );
            },

            /**
             * Successfull Update Activity Status
             *
             * @param {Object} activity
             * @param {Object} data
             */
            completeActivitySuccess: function(activity, data) {
                this.completingActivity = false;
                --this.childActivitiesCount;

                if (!_.isEmpty(this.completeQueue)) {
                    this.completeActivityClick(this.completeQueue.shift());
                } else {
                    app.alert.dismiss('process_complete_activity');
                    if (!_.isUndefined(this.completingParentActivity) &&
                        this.completingParentActivity != null &&
                        activity.get('cj_parent_activity_id') === this.completingParentActivity.get('id')) {
                        if (!_.isUndefined(this.readOnlyChildActivity) &&
                            this.readOnlyChildActivity === true) {
                            this.showReadOnlyErrorMessage('error', 'LBL_CHILD_ACTIVITY_STATUS_READ_ONLY');
                            this.readOnlyChildActivity = false;
                        } if (!_.isUndefined(this.isActivityChangeNotAllowed) &&
                            this.isActivityChangeNotAllowed === true) {
                            this.showNotAllowedErrorMessage();
                            this.isActivityChangeNotAllowed = false;
                        } else {
                            app.alert.show('success_complete_activity', {
                                level: 'success',
                                messages: app.lang.get('LBL_CJ_SUCCESS'),
                                autoClose: true,
                            });
                        }
                    } else if (!_.isUndefined(this.isActivityChangeNotAllowed) &&
                        this.isActivityChangeNotAllowed === true) {
                        this.showNotAllowedErrorMessage();
                        this.isActivityChangeNotAllowed = false;
                    }
                    this.completingParentActivity = null;
                    this.reloadData(data);
                }

                this.handleFormsForStage(activity, activity.get('dri_subworkflow_id'));
            },

            /**
             * Error while updating Activity Status
             *
             * @param {Object} result
             */
            completeActivityError: function(result) {
                this.completingActivity = false;
                this.reloadData();
                app.alert.dismiss('process_complete_activity');
                app.alert.show('error', {
                    level: 'error',
                    messages: result.message,
                    autoClose: true,
                });
            },

            /**
             * Event handler when clicking on the Assign Me on an activity
             *
             * @param {Object} activity
             */
            assignMeActivityClick: function(activity) {
                this.disablingJourneyAndStartLoading();
                activity.set('assigned_user_id', app.user.id);
                activity.save(null, {
                    success: _.bind(this.reloadData, this),
                    error: _.bind(function(result) {
                        this.reloadData();
                        app.alert.show('error', {
                            level: 'error',
                            messages: result.message,
                            autoClose: true
                        });
                    }, this),
                });
            },

            /**
             * Gets called when the Not Applicable button gets clicked
             * Updates the activity and reloads the data in the view
             *
             * @param {Object} activity
             */
            notApplicableActivityClick: function(activity) {
                if (!this.isStatusReadOnly(activity)) {
                    this.handleForms(
                        activity,
                        'not_applicable',
                        _.bind(this.notApplicableActivity, this, activity)
                    );
                } else {
                    this.showReadOnlyErrorMessage('error', 'LBL_CHILD_ACTIVITY_STATUS_READ_ONLY');
                }
            },

            /**
             * Handles Not Applicable status for the activity
             * Updates the activity and reloads the data in the view
             *
             * @param {Object} activity
             */
            notApplicableActivity: function(activity) {
                if (!_.isEmpty(activity.get('cj_parent_activity_id'))) {
                    // If it is a sub activity
                    if (!this.isStatusReadOnly(activity)) {
                        this.disablingJourneyAndStartLoading();
                        let updatedStatus = '';

                        switch (activity.module) {
                            case 'Tasks':
                                updatedStatus = 'Not Applicable';
                                break;
                            case 'Calls':
                                updatedStatus = 'Not Held';
                                break;
                            case 'Meetings':
                                updatedStatus = 'Not Held';
                                break;
                        }
                        const [url, data] = this.buildUrlActivityStatusUpdateApi(activity, updatedStatus);
                        this.callActivityStatusUpdateApi(url, activity, data);

                    } else {
                        this.showReadOnlyErrorMessage('error', 'LBL_ACTIVITY_STATUS_READ_ONLY');
                    }
                } else {
                    this.disablingJourneyAndStartLoading();
                    // If it is a parent activity
                    this.childrenActivitiesNotApplicable(activity);
                }
            },

            /**
             * Gets called when the Not Applicable button on Parent Activity is clicked
             * Updates the Sub Activity and reloads the data in the view
             *
             * @param {Object} activity
             */
            childrenActivitiesNotApplicable: function(activity) {
                let url = app.api.buildURL(
                    'DRI_Workflows',
                    'not-applicable',
                    null
                );
                let apiData = {
                    module: this.model.module,
                    record: this.model.get('id'),
                    activity_id: activity.id,
                    activity_module: activity.module,
                    activities: this.getActivitiesInfo(activity.get('children'), activity),
                    fieldsToValidate: this.getFieldsToValidate()
                };

                app.api.call('create', url, apiData, null, {
                    success: _.bind(function(response) {
                        if (!response.isValid) {
                            this.alertForRequiredFieldDependency(response);
                            return;
                        }

                        if ((!_.isUndefined(response.isValidParent) && response.isValidParent === false) ||
                            (!_.isUndefined(response.isActivityChangeNotAllowed) &&
                                response.isActivityChangeNotAllowed === true)) {
                            this.showNotAllowedErrorMessage();
                        }

                        if (!_.isUndefined(response.isChildReadOnly) && response.isChildReadOnly === true) {
                            this.showReadOnlyErrorMessage('error', 'LBL_CHILD_ACTIVITY_STATUS_READ_ONLY');
                        }

                        if (!_.isUndefined(response.isSelfReadOnly) && response.isSelfReadOnly === true) {
                            this.showReadOnlyErrorMessage('error', 'LBL_ACTIVITY_STATUS_READ_ONLY');
                        }

                        this.reloadData(response.data);
                        this.handleFormsForStage(activity, activity.get('dri_subworkflow_id'));
                    }, this),
                    error: _.bind(function(result) {
                        this.reloadData();
                        app.alert.show('error', {
                            level: 'error',
                            messages: result.message,
                            autoClose: true
                        });
                    }, this)
                });
            },

            /**
             * Opens the preview view in the intelligence pane
             *
             * @param {Object} activity
             */
            previewModel: function(activity) {
                app.events.trigger('preview:render', activity,
                    app.data.createBeanCollection(activity.module, [activity]), true);
            },

            /**
             * Adds a task
             *
             * @param {Object} stage
             */
            addTask: function(stage) {
                this.addActivity(stage, 'Tasks');
            },

            /**
             * Adds a meeting
             *
             * @param {Object} stage
             */
            addMeeting: function(stage) {
                this.addActivity(stage, 'Meetings');
            },

            /**
             * Adds a call
             *
             * @param {Object} stage
             */
            addCall: function(stage) {
                this.addActivity(stage, 'Calls');
            },

            /**
             * Adds a subtask
             *
             * @param {Object} activity
             */
            addSubTask: function(activity) {
                this.addSubActivity(activity, 'Tasks');
            },

            /**
             * Adds a submeeting
             *
             * @param {Object} activity
             */
            addSubMeeting: function(activity) {
                this.addSubActivity(activity, 'Meetings');
            },

            /**
             * Adds a subcall
             *
             * @param {Object} activity
             */
            addSubCall: function(activity) {
                this.addSubActivity(activity, 'Calls');
            },

            /**
             * Links an existing task
             *
             * @param {Object} stage
             */
            linkExistingTask: function(stage) {
                this.linkExistingActivity(stage, 'Tasks');
            },

            /**
             * Links an existing meeting
             *
             * @param {Object} stage
             */
            linkExistingMeeting: function(stage) {
                this.linkExistingActivity(stage, 'Meetings');
            },

            /**
             * Links an existing call
             *
             * @param {Object} stage
             */
            linkExistingCall: function(stage) {
                this.linkExistingActivity(stage, 'Calls');
            },

            /**
             * Gets the fields for the duplicate button
             *
             * @param {Array} fieldsInfo
             * @return {Array}
             */
            _getFieldsForDuplicateButton: function(fieldsInfo) {
                var fields = [];
                _.each(fieldsInfo, function(field) {
                    if (field.type !== 'link') {
                        fields = fields.concat(field.name);
                    }
                }, this);

                return _.uniq(fields);
            },

            /**
             * Handles when clicking on the duplicate button for an activity
             *
             * @param {Object} activity
             * @param {Object} buttonDef
             */
            duplicateButton: function(activity, buttonDef = {}) {
                let currentActivityModule = activity.get('module') || activity.get('_module') || activity.module ||
                    activity._module;
                if (!_.isEmpty(activity.get('id')) && !_.isEmpty(currentActivityModule)) {
                    app.alert.show('loading_duplicate', {
                        level: 'process',
                    });

                    let reFetchActivity = app.data.createBean(currentActivityModule, {id: activity.get('id')});
                    let fieldsForReFecth = this._getFieldsForDuplicateButton(reFetchActivity.fields);
                    reFetchActivity.setOption('fields', fieldsForReFecth);

                    reFetchActivity.fetch({
                        success: _.bind(this.reFetchActivitySuccess, this, activity, buttonDef, reFetchActivity),
                    });
                }
            },

            /**
             * ReFetching Activity is Successful when clicking on the duplicate button for an activity
             *
             * @param {Object} activity
             * @param {Object} buttonDef
             * @param {Object} reFetchActivity
             */
            reFetchActivitySuccess: function(activity, buttonDef, reFetchActivity) {
                let currentModel = activity;
                let prefill = app.data.createBean(activity.module);
                prefill.copy(reFetchActivity);
                currentModel.trigger('duplicate:before', prefill);
                prefill.unset('id');
                prefill.unset('is_escalated');

                let order = '';
                if (!_.isEmpty(buttonDef) && buttonDef.def.childCopy) {
                    order = `${ reFetchActivity.get(this.activitySortOrder) } .`;
                    let stage = this.stages[reFetchActivity.get(this.activityStageId)];
                    let activity = stage.activities[reFetchActivity.id];
                    let children = stage && activity ? activity.children : {};

                    let last = _.last(_.values(children));

                    if (last) {
                        order = `${ reFetchActivity.get(this.activitySortOrder) }.`;
                        order += (parseInt(last.model.get(this.activitySortOrder).split('.')[1]) + 1);
                    } else {
                        order = `${ reFetchActivity.get(this.activitySortOrder) }.1`;
                    }
                } else {
                    let stageID = reFetchActivity.get(this.activityStageId);
                    let lastActivity = this.stages[stageID] && _.last(_.toArray(this.stages[stageID].activities));
                    if (lastActivity) {
                        order = parseInt(lastActivity.data[this.activitySortOrder]) + 1;
                    }
                }

                prefill.set(this.activitySortOrder, order);

                app.alert.dismiss('loading_duplicate');
                app.drawer.open({
                    layout: 'create',
                    context: {
                        create: true,
                        forceNew: true,
                        model: prefill,
                        copiedFromModelId: reFetchActivity.get('id'),
                        module: reFetchActivity.get('_module') || reFetchActivity.get('module')
                    }
                }, _.bind(function(context, newModel) {
                    if (newModel && newModel.id) {
                        this.reloadData();
                    }
                },this)
                );
                prefill.trigger('duplicate:field', this);
            },

            /**
             * Event handler when clicking on the hide children icon on an activity
             *
             * @param {event} ev
             */
            hideActivityChildrenClicked: function(ev) {
                var id = $(ev.currentTarget).data('id');
                this.hideActivityChildren(id);
            },

            /**
             * Handles hiding the child activities
             *
             * @param {string} id
             */
            hideActivityChildren: function(id) {
                this.$(`.dri-activity-children[data-id='${ id }']`).addClass('hide');
                this.$(`.dri-subworkflow-activity[data-id='${ id }'] .dri-activity-show-children`).removeClass('hide');
                this.$(`.dri-subworkflow-activity[data-id='${ id }'] .dri-activity-hide-children`).addClass('hide');
                this.setActivityDisplayChildren(id, this.MORE_LESS_STATUS.LESS);
            },

            /**
             * Event handler when clicking on the show children icon on an activity
             *
             * @param {event} ev
             */
            showActivityChildrenClicked: function(ev) {
                var id = $(ev.currentTarget).data('id');
                this.showActivityChildren(id);
            },

            /**
             * Handles showing the child activities
             *
             * @param {string} id
             */
            showActivityChildren: function(id) {
                this.$(`.dri-activity-children[data-id='${ id }']`).removeClass('hide');
                this.$(`.dri-subworkflow-activity[data-id='${id}'] .dri-activity-show-children`).addClass('hide');
                this.$(`.dri-subworkflow-activity[data-id='${ id }'] .dri-activity-hide-children`).removeClass('hide');
                this.setActivityDisplayChildren(id, this.MORE_LESS_STATUS.MORE);
            },

            /**
             * Retrieves a stage context by id
             *
             * @param {string} stageId
             * @return {Object}
             */
            getStageContextById: function(stageId) {
                var stages = this.model.getRelatedCollection(this.stageLink);
                var stage = stages.get(stageId);
                return this.getStageContext(stage);
            },

            /**
             * Get the cache key for the activity display children
             *
             * @param {string} id
             * @return {string}
             */
            getActivityDisplayChildrenCacheKey: function(id) {
                return app.user.lastState.key('activity_display_children[' + id + ']', this);
            },

            /**
             * Set the cache key for the activity display children
             *
             * @param {string} id
             * @param {string} value
             */
            setActivityDisplayChildren: function(id, value) {
                var key = this.getActivityDisplayChildrenCacheKey(id);
                app.user.lastState.set(key, value);
            },

            /**
             * Retrieves a stage context by stage model
             *
             * @param {Object} stage
             * @return {Object}
             */
            getStageContext: function(stage) {
                var stageContext;

                stageContext = this.context.getChildContext({
                    module: this.stageModule,
                    model: stage,
                    forceNew: true
                });

                return stageContext;
            },

            /**
             * Shows alert for the empty required field
             *
             * @param {Object} data
             */
            alertForRequiredFieldDependency: function(data) {
                app.alert.show('error', {
                    level: 'error',
                    messages: app.lang.get('LBL_REQUIRED_FIELD_EMPTY_IN_ACTIVITY', this.model.module, {
                        fieldName: data.field,
                        recordUrl: app.router.buildRoute(data.module, data.id),
                        recordName: data.name
                    }),
                    autoClose: true,
                    autoCloseDelay: 10000,
                });

                this.completingActivity = false;
            },

            /**
             * It will return all the view level and field level dependencies
             *
             * @param {type} model
             * @return {Object}
             */
            _getSugarLogicDependenciesForModel: function(model) {
                var module = model.module;
                if (_.isEmpty(this.moduleDependencies[module])) {
                    var moduleMetadata = app.metadata.getModule(module) || {};
                    var dependencies = moduleMetadata.dependencies || [];

                    if (moduleMetadata.views && moduleMetadata.views.record) {
                        var recordMetadata = moduleMetadata.views.record.meta;
                        if (!_.isUndefined(recordMetadata.dependencies)) {
                            dependencies = dependencies.concat(recordMetadata.dependencies);
                        }
                    }

                    // Cache the results so we don't have to do this expensive lookup any more
                    this.moduleDependencies[module] = dependencies;
                }

                return this.moduleDependencies[module];
            },

            /**
             * @param {string} id
             * @return {string}
             */
            getActivityDisplayChildren: function(id) {
                var key = this.getActivityDisplayChildrenCacheKey(id);
                return app.user.lastState.get(key);
            },

            /**
             * Event handler when clicking on the Configure Template button
             */
            configureTemplateClick: function() {
                this.reRoute('DRI_Workflow_Templates', this.model.get('dri_workflow_template_id'));
            },

            /**
             * Event handler when clicking on the Edit Smart Guide button
             */
            editJourneyClick: function() {
                const route = app.router.buildRoute('DRI_Workflows', this.model.get('id'), 'edit');
                app.routeBackTo = document.location.hash;
                app.router.navigate(route, {trigger: true});
            },

            /**
             * Event handler when clicking on the assigned user
             */
            viewAssignedUserClick: function() {
                const route = app.router.buildRoute('Users', this.model.get('assigned_user_id'));
                app.router.navigate(route, {trigger: true});
            },

            /**
             * Update the assigned user section based on the width
             */
            resizeWorkflowInfo: function() {
                $el = this.$el;
                if (!$el) {
                    return;
                }

                const descWrapper = $el.find('.dri-workflow-desc');
                const actionButtonsWrapper = $el.find('.dri-workflow-action-buttons');
                const assignedUserWrapper = $el.find('.dri-workflow-assigned-user');
                const assignedUserLabel = $el.find('.dri-workflow-assigned-user-label');
                const assignedUserName = $el.find('.dri-workflow-assigned-user-name');

                const extraMargin = 50;
                let spaceAvailable = $el.width() - extraMargin;
                spaceAvailable -= descWrapper.width();
                spaceAvailable -= actionButtonsWrapper.width();
                spaceAvailable -= extraMargin;

                assignedUserLabel.removeClass('hidden').addClass('block');
                assignedUserName.removeClass('hidden').addClass('inline');

                if (spaceAvailable < assignedUserWrapper.width()) {
                    assignedUserName.removeClass('inline').addClass('hidden');
                    if (spaceAvailable < assignedUserWrapper.width()) {
                        assignedUserLabel.removeClass('block').addClass('hidden');
                    }
                }
            },

            /**
             * Provide an array containing activities id and module information
             *
             * @param {Object|Array} activities
             * @param {Object} currentActivity
             * @return {Array}
             */
            getActivitiesInfo: function(activities, currentActivity) {
                let activitiesInfo = [];

                if (!_.isEmpty(currentActivity)) {
                    activitiesInfo.push({
                        id: currentActivity.id,
                        module: currentActivity.module
                    });
                }

                for (let key in activities) {
                    let activity = activities[key];

                    activitiesInfo.push({
                        id: activity.id,
                        module: activity.module || activity._module
                    });
                }

                return activitiesInfo;
            },

            /**
             * Event handler when clicking on the Cancel Journey button
             */
            cancelJourneyClick: function() {
                app.alert.show('cancel_model', {
                    level: 'confirmation',
                    messages: app.lang.get('LBL_CJ_CANCEL_CONFIRMATION', this.model.module),
                    onConfirm: _.bind(function() {
                        let url = app.api.buildURL(this.model.module, 'cancel');
                        let apiData = {
                            record: this.model.get('id'),
                            activities: this.getActivitiesInfo(this.activities),
                            fieldsToValidate: this.getFieldsToValidate()
                        };

                        this.disablingJourneyAndStartLoading();
                        app.api.call('create', url, apiData, null, {
                            success: _.bind(function(response) {
                                if (!response.isValid) {
                                    this.alertForRequiredFieldDependency(response);
                                } else {
                                    this.reloadData(response.data);
                                    this.reloadAllJourneys();
                                    if (!_.isUndefined(response.isChildReadOnly) && response.isChildReadOnly === true) {
                                        this.showReadOnlyErrorMessage('warning',
                                            'LBL_GUIDE_CANCELLATION_ACTION_ACTIVITY_STATUS_READ_ONLY');
                                    } else if (!_.isUndefined(response.isActivityChangeNotAllowed) &&
                                        response.isActivityChangeNotAllowed === true) {
                                        this.showNotAllowedErrorMessage();
                                    } else {
                                        app.alert.show('success', {
                                            level: 'success',
                                            autoClose: true,
                                            messages: app.lang.get(
                                                'LBL_CANCEL_SUCCESS_MESSAGE',
                                                'DRI_Workflows',
                                                {labelName: this.model.get('name')}
                                            )
                                        });
                                    }
                                }
                            }, this),
                            error: _.bind(function(result) {
                                this.reloadData();

                                app.alert.show('error', {
                                    level: 'error',
                                    messages: result.message,
                                    autoClose: true
                                });
                            }, this),
                            complete: _.bind(function() {
                                this.enablingJourneyAndDoneLoading();
                            }, this)
                        });
                    }, this)
                });
            },

            /**
             * Event handler when clicking on the Archive Journey button
             */
            archiveJourneyClick: function() {
                app.alert.show('archive_model', {
                    level: 'confirmation',
                    messages: app.lang.get('LBL_CJ_ARCHIVE_CONFIRMATION', this.model.module),
                    onConfirm: _.bind(function() {
                        var url = app.api.buildURL(this.model.module, 'archive', {
                            id: this.model.get('id')
                        });

                        this.disablingJourneyAndStartLoading();
                        var parentContext = this.context.parent;
                        app.api.call('create', url, null, {
                            success: _.bind(function() {
                                this.updateArchiveUnarchiveLastState('archived', this.model.get('id'));
                                this.activateCycleAndReloadWorkflow(parentContext, true);
                            }, this),
                            error: _.bind(function(result) {
                                this.enablingJourneyAndDoneLoading();
                                this.activateCycleAndReloadWorkflow(parentContext);
                                app.alert.show('error', {
                                    level: 'error',
                                    messages: result.message,
                                    autoClose: true
                                });
                            }, this)
                        });
                    }, this)
                });
            },

            /**
             * Event handler when clicking on the Unarchive Journey button
             */
            unarchiveJourneyClick: function() {
                app.alert.show('archive_model', {
                    level: 'confirmation',
                    messages: app.lang.get('LBL_CJ_UNARCHIVE_CONFIRMATION', this.model.module),
                    onConfirm: _.bind(function() {
                        var url = app.api.buildURL(this.model.module, 'unarchive', {
                            id: this.model.get('id')
                        });

                        this.disablingJourneyAndStartLoading();
                        var parentContext = this.context.parent;
                        app.api.call('create', url, null, {
                            success: _.bind(function() {
                                this.updateArchiveUnarchiveLastState('active', this.model.get('id'));
                                this.activateCycleAndReloadWorkflow(parentContext, true);
                            }, this),
                            error: _.bind(function(result) {
                                this.enablingJourneyAndDoneLoading();
                                this.activateCycleAndReloadWorkflow(parentContext);
                                app.alert.show('error', {
                                    level: 'error',
                                    messages: result.message,
                                    autoClose: true
                                });
                            }, this)
                        });
                    }, this)
                });
            },

            /**
             * Event handler when clicking on the Delete Journey button
             *
             * @param {Object} model
             */
            deleteCycleClicked: function(model) {
                const modelId = model.get('id');
                const module = model.module;
                const isSmartGuide = _.isEqual(module, 'DRI_Workflows');
                const moduleMapping = {
                    'DRI_SubWorkflows': 'stage',
                    'DRI_Workflows': 'smart guide',
                    'DRI_SubWorkflow_Templates': 'stage template',
                };
                let labelName = `${model.get('name')} ${moduleMapping[module]}`;

                app.alert.show('delete_model', {
                    level: 'confirmation',
                    messages: app.lang.get('NTC_DELETE_CONFIRMATION', module),
                    onConfirm: _.bind(function() {
                        // We must retrieve the context here since the view
                        // Will be disposed when the request is finished.
                        const parentContext = this.context.parent;
                        const parentModel = parentContext.parent;
                        const alertName = 'process_deleting_journey';
                        if (!_.isUndefined(parentModel) && !_.isUndefined(parentModel.get('model'))) {
                            let parentJourneyModel = parentModel.get('model');
                            const relatedCollection = parentJourneyModel.getRelatedCollection('dri_workflows');
                            if (!_.isUndefined(relatedCollection)) {
                                const journeyIDs = _.map(relatedCollection.models, model => model.id);
                                const url = app.api.buildURL('DRI_Workflows', 'delete-stage-journey', null, {
                                    id: modelId,
                                    record: this.model.get('id'),
                                    moduleName: module,
                                    parentModule: parentModel.get('module'),
                                    parentModelId: parentModel.get('modelId'),
                                    currentJourneys: journeyIDs,
                                    state: parentContext.get('model').get('cj_active_or_archive_filter')
                                });

                                this.showProcessingAlert(alertName, app.lang.get('LBL_DELETING'));
                                this.disablingJourneyAndStartLoading();

                                app.api.call('create', url, null, {
                                    success: _.bind(function(nextJourney) {
                                        if (this.disposed || this.layout.disposed) {
                                            return;
                                        }
                                        app.alert.dismiss(alertName);
                                        if (!_.isUndefined(isSmartGuide)) {
                                            this.removeJourneyIdFromLastState(parentContext.parent, modelId);
                                            if (!_.isNull(nextJourney)) {
                                                this.layout.collection.add(nextJourney);
                                            }
                                            this.layout.collection.dataFetched = false;
                                            this.layout.collection.remove(model);
                                            this.render();
                                        } else {
                                            this.reloadData();
                                        }

                                        app.alert.show('success', {
                                            level: 'success',
                                            autoClose: true,
                                            messages: app.lang.get(
                                                'LBL_DELETE_SUCCESS_MESSAGE',
                                                'DRI_Workflows',
                                                {labelName: labelName}
                                            )
                                        });
                                    }, this),
                                    error: _.bind(function(result) {
                                        app.alert.dismiss(alertName);

                                        if (!_.isUndefined(isSmartGuide)) {
                                            this.activateCycleAndReloadWorkflow(parentContext);
                                        } else {
                                            this.reloadData();
                                        }

                                        app.alert.show('error', {
                                            level: 'error',
                                            messages: result.message,
                                            autoClose: true
                                        });
                                    }, this)
                                });
                            }
                        }
                    }, this)
                });
            },

            /**
             * Shows activity status read only error message
             *
             * @param {string} labelKey
             */
            showReadOnlyErrorMessage: function(errorLevel, labelKey) {
                app.alert.show('error_activity_status_readonly', {
                    level: errorLevel,
                    messages: app.lang.get(labelKey),
                    autoClose: false,
                });
            },

            /**
             * Shows activity status read only error message
             *
             * @param {string} labelKey
             */
            showNotAllowedErrorMessage: function() {
                app.alert.show('error_activity_not_allowed', {
                    level: 'error',
                    messages: app.lang.get('LBL_CURRENT_USER_UNABLE_TO_COMPLETE_STATUS',
                        'DRI_Workflow_Task_Templates'),
                    autoClose: false,
                });
            },
        });
    });
})(SUGAR.App);

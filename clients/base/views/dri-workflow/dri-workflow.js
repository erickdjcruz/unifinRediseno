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
 * @class View.Views.Base.DRIWorkflow
 * @alias SUGAR.App.view.views.DRIWorkflow
 * @extends View.View
 */
({
    plugins: ['Tooltip', 'ToggleMoreLess', 'CssLoader', 'SugarLogic', 'CJEvents', 'CJForms', 'CJViewAndField'],

    /**
     * Presentation mode class name.
     *
     * @property
     */
    presentationModeClass: 'dri-workflow-details-vertical',

    /**
     * Archived fields
     *
     * @property
     */
    recordIsArchived: false,

    /**
     * Status values.
     *
     * @property
     */
    MORE_LESS_STATUS: {
        MORE: 'more',
        LESS: 'less'
    },

    /**
     * Track the module dependencies for activities, so we dont have to fetch them every time
     *
     * @type {Object}
     */
    moduleDependencies: {},

    events: {
        'click .dri-workflow-info': 'workflowInfoClicked',
        'click .dri-workflow-action-buttons': 'actionButtonsClicked',
        'click .cj_blocked .sicon-ban': 'blockedActivityClicked',
        'click .cj_blocked_by_stage .sicon-ban': 'blockedByStageActivityClicked',
        'click .sortable-journey': 'activeCycleClicked',
        'click .dri-subworkflow-activity .activity-preview-icon-name': 'previewActivityClicked',
        'click .dri-subworkflow-activity .activity-preview-icon': 'previewActivityClicked',
        'click .activity-form': 'activityFormClicked',
        'click .dri-activity-hide-children': 'hideActivityChildrenClicked',
        'click .dri-activity-show-children': 'showActivityChildrenClicked',
        'click .dri-workflow-assigned-user-link': 'viewAssignedUserClick',
    },

    tplErrorMap: {
        ERROR_INVALID_LICENSE: 'invalid-license',
    },

    className: 'dri-workflow-wrapper mx-2 my-3.5 rounded-md shadow hover:shadow-lg ' +
        'transition-shadow hover:bg-[--dashlet-background-hover] ui-sortable-handle',

    stageModule: 'DRI_SubWorkflows',
    stageLink: 'dri_subworkflows',
    activityStageId: 'dri_subworkflow_id',
    parentActivityId: 'cj_parent_activity_id',
    activitySortOrder: 'dri_workflow_sort_order',
    activityUrlField: 'cj_url',

    /**
     * {@inheritdoc}
     */
    initialize: function(options) {
        this._initProperties();
        this._super('initialize', [options]);
        this.listenTo(this.model, 'change:state', this._setStateClass);
        this.listenTo(this.model, 'change:archived', this.hasArchivedField);
        this.listenTo(this.model, 'workflow-template:hide-show:click', this.reloadData);

        if (this.model) {
            this.listenTo(this.context, 'sync', this.reloadViewData);
        }

        this._setStateClass();
    },

    /**
     * Initialize properties
     */
    _initProperties: function() {
        this.stagesSortable = false;
        this.activitiesSortable = false;
        this.completingActivity = false;
        this.stageLinks = true;
        this.activityLinks = true;
        this.completeQueue = [];
    },

    /**
     * @override
     *
     * Binds events on the collection, and updates the checkboxes
     * consequently.
     */
    bindDataChange: function() {
        // Binding the events with the respectcive methods
        this._bindJourneryEvents();
        this._bindStageEvents();
        this._bindActivityEvents();

        var parentContext = this.getParentContext();

        if (parentContext) {
            this.listenTo(parentContext, 'change:moreLess', this.toggleParentMoreLess);
            this.listenTo(parentContext, 'change:moreLess', this.hideWhenParentMoreLessChanged);

            this.listenTo(this, 'render', this.toggleParentMoreLess);

            var parentModel = this.getParentModel();
            if (parentModel) {
                this.listenTo(
                    parentModel,
                    'sync',
                    function() {
                        parentContext.set('customer_journey_fetching_parent_model', false);
                    }
                );
            }
        }
    },

    /**
     * Bind events related to Journey
     *
     * @private
     */
    _bindJourneryEvents: function() {
        this.listenTo(this.context, 'journey:add_stage_button:click', this.addStageClick);
        this.listenTo(this.context, 'journey:configure_template_button:click', this.configureTemplateClick);
        this.listenTo(this.context, 'journey:edit_button:click', this.editJourneyClick);
        this.listenTo(this.context, 'journey:cancel_button:click', this.cancelJourneyClick);
        this.listenTo(this.context, 'journey:archive_button:click', this.archiveJourneyClick);
        this.listenTo(this.context, 'journey:unarchive_button:click', this.unarchiveJourneyClick);
        this.listenTo(this.context, 'journey:delete_button:click', this.deleteCycleClicked);
    },

    /**
     * Bind events related to Stage
     *
     * @private
     */
    _bindStageEvents: function() {
        this.listenTo(this.context, 'stage:edit_button:click', this.editStageClick);
        this.listenTo(this.context, 'stage:delete_button:click', this.deleteCycleClicked);

        this.listenTo(this.context, 'stage:add_task_button:click', this.addTask);
        this.listenTo(this.context, 'stage:add_meeting_button:click', this.addMeeting);
        this.listenTo(this.context, 'stage:add_call_button:click', this.addCall);

        this.listenTo(this.context, 'stage:link_task_button:click', this.linkExistingTask);
        this.listenTo(this.context, 'stage:link_meeting_button:click', this.linkExistingMeeting);
        this.listenTo(this.context, 'stage:link_call_button:click', this.linkExistingCall);

        this.listenTo(this.context, 'stage:add_sub_task_button:click', this.addSubTask);
        this.listenTo(this.context, 'stage:add_sub_meeting_button:click', this.addSubMeeting);
        this.listenTo(this.context, 'stage:add_sub_call_button:click', this.addSubCall);
    },

    /**
     * Bind events related to Activity
     *
     * @private
     */
    _bindActivityEvents: function() {
        this.listenTo(this.context, 'activity:complete_button:click', function(activity) {
            this.childActivitiesCount = this.getNotCompletedChildrenCount(activity);

            this.completeActivityClick(activity, true);
        });
        this.listenTo(this.context, 'activity:edit_button:click', this.editActivityClick);
        this.listenTo(this.context, 'activity:start_button:click', this.startActivityClick);
        this.listenTo(this.context, 'activity:assign_me_button:click', this.assignMeActivityClick);
        this.listenTo(this.context, 'activity:delete_button:click', this.deleteModelClick);
        this.listenTo(this.context, 'activity:not_applicable_button:click', function(activity) {
            this.childActivitiesCount = this.getNotCompletedChildrenCount(activity);

            this.notApplicableActivityClick(activity);
            this.enablingJourneyAndDoneLoading();
        });
        this.listenTo(this.context, 'activity:preview_button:click', this.previewModel);
        this.listenTo(this.context, 'activity:duplicate_button:click', this.duplicateButton);
    },

    /**
     * Build url for the activity status update call
     *
     * @param {Object} activity
     * @param {string} updatedStatus
     * @param {Object} parent
     * @return {string|undefined}
     */
    buildUrlActivityStatusUpdateApi: function(activity, updatedStatus, parent) {
        if (_.isEmpty(activity) || _.isEmpty(updatedStatus)) {
            return;
        }

        let bean = parent ? parent : activity;

        //send item clicked when it's parent to be able to make validations
        let targetItem = null;
        if (parent) {
            targetItem = {
                id: parent.get('id'),
                module: parent.get('_module'),
            };
        }
        let url = app.api.buildURL(
            'DRI_Workflows',
            'update-activity-state',
            null
        );
        let data = {
            module: this.model.module,
            record: this.model.get('id'),
            status: updatedStatus,
            activity_id: activity.id,
            activity_module: activity.module,
            fieldsToValidate: this.getFieldsToValidate(),
            parentActivity: this.getParentActivity(activity, updatedStatus),
            activities: this.getActivitiesInfo(bean.get('children'), bean),
            childActivitiesCount: this.childActivitiesCount,
            targetItem: targetItem,
        };

        return [url, data];
    },

    /**
     * Return parent activity if all sibling activities are completed
     *
     * @param {Object} activity
     * @param {string} updatedStatus
     * @return {Array}
     */
    getParentActivity: function(activity, updatedStatus) {
        let parent = [];

        if (!_.isEmpty(activity.get('cj_parent_activity_id'))) {
            // If it is a sub activity
            let completeStatuses = [].concat(
                _.keys(app.lang.getAppListStrings('cj_tasks_completed_status_list')),
                _.keys(app.lang.getAppListStrings('cj_calls_completed_status_list'))
            );

            let parentActivity = this.activities[activity.get('cj_parent_activity_id')];
            let activityStatuses = _.pluck(parentActivity.get('children'), 'status');
            let completedActivities = _.include(completeStatuses, updatedStatus) ? 1 : 0;

            for (let key in activityStatuses) {
                if (_.include(completeStatuses, activityStatuses[key])) {
                    completedActivities++;
                }
            }

            // if all sibling activities are completed / not applicable
            // then return parent activity to validate its formula
            if (completedActivities === activityStatuses.length) {
                parent = [{
                    id: activity.get('cj_parent_activity_id'),
                    module: activity.get('cj_parent_activity_type'),
                }];
            }
        }

        return parent;
    },

    /**
     * Calling the custom activity status update Api
     *
     * @param {string} url
     * @param {Object} activity
     */
    callActivityStatusUpdateApi: function(url, activity, data) {
        if (_.isEmpty(url)) {
            return;
        }
        app.api.call('create', url, data, null, {
            success: _.bind(function(response) {
                if (!response.isValid) {
                    this.alertForRequiredFieldDependency(response);
                    return;
                }
                if (!_.isUndefined(response.isActivityChangeNotAllowed) &&
                    response.isActivityChangeNotAllowed === true && !_.isUndefined(this.completeQueue)) {
                    app.alert.dismiss('process_complete_activity');
                    app.alert.show('error_activity_not_allowed', {
                        level: 'error',
                        messages: app.lang.get('LBL_CURRENT_USER_UNABLE_TO_COMPLETE_STATUS',
                            'DRI_Workflow_Task_Templates'),
                        autoClose: false,
                    });
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
     * Build and open Route
     *
     * @param {string} module
     * @param {string} id
     */
    reRoute: function(module = 'Home', id = '') {
        let route = app.router.buildRoute(module, id);
        window.open('#' + route, '_blank');
    },

    /**
     * Get parentContext trigger events for activate cycle and reload workflow
     *
     * @param {Object} parentContext
     * @param {boolean} remove
     */
    activateCycleAndReloadWorkflow: function(parentContext, remove = false) {
        if (parentContext) {
            parentContext.get('parentModel').trigger('customer_journey:active-cycle:click', null);
            this.layout.reloadJourneys(false);
        }
    },

    /**
     * Adjusting the stages span
     *
     * @private
     */
    _setSubworkflowSpan: function() {
        let minSpan = 3;
        let maxSpan = 12;
        let maxColumn = 4;
        let defaultSize = 1;
        let length = _.size(this.stages) || defaultSize;
        let span = length > maxColumn ? minSpan : maxSpan / length;
        this.subworkflowSpan = `span${ span }`;
    },

    /**
     * Toggles the panel when the parent layout changes the state
     */
    toggleParentMoreLess: function() {
        if (this.isCJRenderedAsTab()) {
            return;
        }

        var parentContext = this.getParentContext();

        if (!this.disposed && parentContext) {
            if (parentContext.get('moreLess') === this.MORE_LESS_STATUS.LESS) {
                this.hide();
            } else {
                this.show();
            }
        }
    },

    /**
     * Toggle the display in case of panel
     */
    hideWhenParentMoreLessChanged: function() {
        if (this.isCJRenderedAsTab()) {
            return;
        }

        if (!this.disposed) {
            this.addRemoveClasses(this.MORE_LESS_STATUS.LESS);
        }
    },

    /**
     * Check from context if CJ is rendered as Tab or not
     */
    isCJRenderedAsTab: function() {
        return this.context && this.context.get('cjAsTab');
    },

    /**
     * For populating the field data using LinkedModel plugin
     *
     * @param {Object} child context.
     * @param {Object} CJ Forms API response.
     * @return {Object} A new instance of the related or regular bean.
     */
    populateModelFromLinkedData: function(context, form, response) {
        if (_.isString(response.linkName) && !_.isEqual(response.linkName, 'emails') &&
            form.action_trigger_type === 'manual_create' && context.parent.get('model')) {
            let linkedModelPlugin = app.plugins.plugins.view.LinkedModel;
            return linkedModelPlugin.createLinkModel(context.parent.get('model'), response.linkName);
        }
    },

    /**
     * Get forms for the Activitiy(s), stage(s) and journey(s)
     *
     * @param {Array} formList
     * @param {event} triggerEvent
     * @return {Array}
     */
    getFormsOrStageAndJourneyForms: function(forms, triggerEvent) {
        return _.filter(forms || [], function(form) {
            return !triggerEvent || (form.main_trigger_type !== 'sugar_action_to_smart_guide' &&
                form.trigger_event === triggerEvent &&
                form.action_trigger_type !== 'automatic_create' && form.action_trigger_type !== 'automatic_update');
        });
    },

    /**
     * Prepare the recipients info for model's to_collection
     * Field of compose view to auto populate
     *
     * @param {Object} emailData
     * @return {Object} emailData
     */
    getRecipientsForComposeEmail: function(emailData) {
        var recipientRecords = [];
        if (!_.isEmpty(emailData.recipientsInfo)) {
            _.each(emailData.recipientsInfo, function(recipients, module) {
                _.each(recipients, function(recipientID) {
                    if (!_.isEmpty(recipientID)) {
                        var recipientName = this.getRecipientsName(emailData, recipientID);
                        recipientRecords.push(
                            app.data.createBean('EmailParticipants', {
                                _link: 'to',
                                parent: {
                                    _acl: {},
                                    _erased_fields: [],
                                    type: module,
                                    id: recipientID,
                                    name: recipientName
                                },
                                parent_type: module,
                                parent_id: recipientID,
                                parent_name: recipientName
                            })
                        );
                    }
                }, this);
            }, this);
        }
        return recipientRecords;
    },

    /**
     * Prepare the recipients info(name) for to_collection
     * field of compose view to auto populate
     *
     * @param {Object} emailData
     * @param {string} recipientID
     * @return {string} recipientName
     */
    getRecipientsName: function(emailData, recipientID) {
        var recipientName = recipientID;
        if (
            !_.isUndefined(emailData.recipientsWithTheirNames) &&
            !_.isUndefined(emailData.recipientsWithTheirNames[recipientID])
        ) {
            recipientName = emailData.recipientsWithTheirNames[recipientID];
        }
        return recipientName;
    },

    /**
     * Update the picture url's property for model's assigned user.
     *
     * @param {string} userId
     */
    updateUserAvatar: function(userId) {
        const pictureUrl = this.model.get('assigned_user_picture') ? app.api.buildFileURL({
            module: 'Users',
            id: userId,
            field: 'picture'
        }, {
            cleanCache: true
        }) : '';
        this.model.set('assigned_user_picture_url', pictureUrl);
    },

    /**
     * {@inheritdoc}
     */
    _render: function() {
        if (this.model.fields.progress) {
            // Make sure to use the right type of progress bar
            this.model.fields.progress.type = 'cj-progress-bar';
        }

        if (this.model.fields.momentum_ratio) {
            // Make sure to use the right type of momentum bar
            this.model.fields.momentum_ratio.type = 'cj-momentum-bar';
        }

        if (this.model.get('assigned_user_id')) {
            this.updateUserAvatar(this.model.get('assigned_user_id'));
        }

        this._super('_render');

        this.toggleButtons();

        if (this.stagesSortable) {
            this.initStageSortable();
        }

        if (this.activitiesSortable) {
            this.initActivitySortable();
        }

        if (this.model.attributes.state === 'cancelled') {
            this.$('.progress').addClass('progress-cancelled');
            this.$('.label ').addClass('label-cancelled');
            this.$('.dri-subworkflow-activity-buttons').hide();
            this.$('.add-stage-button-wrapper').hide();
        }

        this.setHorizontalScrollBarPosition();

        // expand the journey panel
        this.toggleMoreLess(this.MORE_LESS_STATUS.MORE);

        // add border for open journeys in dark mode
        this.$el.addClass('open-border');

        // set data-id attribute of journey
        this.$el.attr('data-id', this.model.get('id'));

        if (this.model.get('assigned_user_id')) {
            const resize = _.bind(this.resizeWorkflowInfo, this);
            let timeout = false;

            this.resizeWorkflowInfo();

            this.handleResize = function() {
                clearTimeout(timeout);
                timeout = setTimeout(resize, 50);
            };
            window.addEventListener('resize', this.handleResize, true);
        }
    },

    /**
     * Remove the deleted journey id from user last state
     *
     * @param {Object} parent
     * @param {string} journeyId
     */
    removeJourneyIdFromLastState: function(parent, journeyId) {
        const mode = app.CJBaseHelper.getValueFromCache('toggleActiveArchived',
            'cj_active_or_archive_filter',
            parent.get('module'),
            'dri-workflows-widget-configuration'
        );

        const key = app.user.lastState.buildKey(`${mode}-journeys-order`, parent.get('modelId'), parent.get('module'));
        let sortedOrder = app.user.lastState.get(key);

        if (!_.isUndefined(sortedOrder)) {
            const index = sortedOrder.indexOf(journeyId);

            if (index > -1) {
                sortedOrder.splice(index, 1);
                app.user.lastState.set(key, sortedOrder);
            }
        }
    },

    /**
     * Update archive and unarchive last state journey order
     *
     * @param {string} mode
     * @param {string} journeyId
     */
    updateArchiveUnarchiveLastState: function(mode, journeyId) {
        const parentModel = this.getParentModel();
        const key = app.user.lastState.buildKey(`${mode}-journeys-order`, parentModel.id, parentModel.module);
        let journeys = app.user.lastState.get(key);

        if (_.isArray(journeys) && !_.contains(journeys, journeyId)) {
            journeys.unshift(journeyId);
            app.user.lastState.set(key, journeys);
        }
    },

    /**
     * initializes sortable stages
     */
    initStageSortable: function() {
        var $rows = this.$('.dri-workflow-details > .row-fluid');
        $rows.sortable({
            connectWith: $rows,
            update: _.bind(this.updateStageOrder, this)
        });
    },

    /**
     * initializes sortable activities
     */
    initActivitySortable: function() {
        var $activities = this.$('.dri-stage-activities');

        $activities.sortable({
            connectWith: $activities,
            update: _.bind(this.updateActivityOrder, this)
        });

        this.$('.dri-activity-children').sortable({
            update: _.bind(this.updateSubActivityOrder, this)
        });
    },

    /**
     * Updates the reordered stages
     */
    updateStageOrder: function() {
        let stages = {};
        let order = 1;
        let save = [];

        this.setPresentationModeClass();

        _.each(this.$('.dri-workflow-details .dri-subworkflow'), function(el) {
            var id = $(el).data('id');
            if (this.stages[id]) {
                stages[id] = this.stages[id];
                var model = stages[id].model;
                if (model.get('sort_order') != order) {
                    model.set('sort_order', order, {silent: true});
                    save.push(function(callback) {
                        model.save(null, {
                            success: function() {
                                callback(null);
                            }
                        });
                    });
                }
            }
            order++;
        }, this);

        if (save.length > 0) {
            this.stages = stages;
            this.rows = this.chunk(this.stages, 4);
            this.render();
            save.push(
                _.bind(function(callback) {
                    this.model.save(null, {
                        success: function() {
                            callback(null);
                        }
                    });
                }, this)
            );

            this.disablingJourneyAndStartLoading();
            async.waterfall(save, _.bind(this.reloadData, this));
        }
    },

    /**
     * Updates the reordered activities
     *
     * @param {event} event
     * @param {Object} ui
     */
    updateActivityOrder: function(event, ui) {
        var $activities = _.isNull(ui.sender) ? ui.item.parent() : ui.sender;
        var stageId = $activities.parent().data('id');

        //Array to store the Id's and sort orders of related activities
        var activitiesIds = [];

        _.each($activities.children(), function(el) {
            var $row = $(el);
            var id = $row.data('id');
            var activity = this.activities[id];
            var activityId = activity.get('id');
            activitiesIds.push(activityId);
        }, this);

        var url = app.api.buildURL(
            'DRI_Workflow_Templates',
            'update-activity-order',
            {
                id: this.model.get('id')
            },
            {
                stage_id: stageId,
                activities_ids: activitiesIds
            }
        );

        this.disablingJourneyAndStartLoading();

        app.api.call('create', url, null, {
            success: _.bind(function(data) {
                this.reloadData();
            }, this),
            error: _.bind(function(result) {
                app.alert.show('error', {
                    level: 'error',
                    messages: result.message,
                    autoClose: true
                });
            })
        });
    },

    /**
     * Updates the reordered sub activities
     *
     * @param {event} event
     * @param {Object} ui
     */
    updateSubActivityOrder: function(event, ui) {
        var order = 1;
        var save = [];
        var $parent = ui.item.parent();
        var parentId = $parent.data('id');
        var parent = this.activities[parentId];
        var parentSortOrder = parent.get(this.activitySortOrder);

        _.each($parent.children(), function(el) {
            var $row = $(el);
            var id = $row.data('id');
            var activity = this.activities[id];
            var newOrder = parentSortOrder + '.' + order;
            if (activity && activity.get(this.activitySortOrder) !== newOrder) {
                activity.set(this.activitySortOrder, newOrder);
                save.push(function(callback) {
                    activity.save(null, {
                        success: function() {
                            callback(null);
                        }
                    });
                });
            }
            order++;
        }, this);

        if (save.length > 0) {
            this.disablingJourneyAndStartLoading();
            async.waterfall(save, _.bind(this.reloadData, this));
        }
    },

    /**
     * Sets the stateClass based on the state
     *
     * @private
     */
    _setStateClass: function() {
        switch (this.model.get('state')) {
            case 'not_started':
            case 'not_completed':
                this.stateClass = 'label-warning';
                break;
            case 'in_progress':
                this.stateClass = 'label-info';
                break;
            case 'ready_for_next_step':
                this.stateClass = 'label-pending';
                break;
            case 'completed':
                this.stateClass = 'label-success';
                break;
            default:
                this.stateClass = '';
                break;
        }
    },

    /**
     * Reloads the parent model
     */
    reloadParentModel: function() {
        let parentContext = this.getParentContext();
        let parentModel = this.getParentModel();

        if (parentContext) {
            parentContext.set('customer_journey_fetching_parent_model', true);
        }

        if (parentModel) {
            let _modelBackup = app.utils.deepCopy(parentModel.attributes);
            let changedAttributes = parentModel.changedAttributes(parentModel.getSynced());

            parentModel.fetch({
                success: function() {
                    let diff = parentModel.getChangeDiff(_modelBackup);
                    _.each(changedAttributes, function(item, key) {
                        if (!_.isUndefined(diff[key])) {
                            parentModel.set(key, diff[key]);
                        }
                    });
                },
                complete: _.bind(function() {
                    this.unsetReloadSingleJourney();
                }, this)
            });
        }
    },

    /**
     * Reloads the view data
     *
     * @param {Object} data
     */
    reloadViewData: function(data) {
        var parentModel = this.getParentModel();

        if (parentModel) {
            parentModel.trigger('customer_journey_widget_reloading');
        }

        this.loadData({'data': data});
    },

    /**
     * Sets if model has archived field
     *
     */
    hasArchivedField: function() {
        this.recordIsArchived = this.model.get('archived') === true;
    },

    /**
     * Get the parent context
     *
     * @return {Object}
     */
    getParentContext: function() {
        return (this.context && this.context.parent) || this.context;
    },

    /**
     * Get the parent model
     *
     * @return {Object}
     */
    getParentModel: function() {
        var parentContext = this.getParentContext();

        if (parentContext) {
            return parentContext.get('parentModel') || parentContext.get('model');
        }

        var models = [];

        _.each(
            this.getParentDefinitions(),
            function(def) {
                if (!_.isEmpty(this.model.get(def.id_name))) {
                    models.push(
                        app.data.createBean(def.module, {
                            id: this.model.get(def.id_name),
                            name: this.model.get(def.name)
                        })
                    );
                }
            },
            this
        );

        return models.shift();
    },

    /**
     * Get the parent definitions
     *
     * @return {Array}
     */
    getParentDefinitions: function() {
        if (!this.model) {
            return [];
        }

        var defs = {};

        _.each(this.model.fields, function(def) {
            if (def.customer_journey_parent && def.customer_journey_parent.enabled) {
                if (!defs[def.customer_journey_parent.rank]) {
                    defs[def.customer_journey_parent.rank] = [];
                }

                defs[def.customer_journey_parent.rank].push(def);
            }
        });

        var keys = _.keys(defs).sort();
        var sorted = [];

        _.each(keys, function(k) {
            _.each(defs[k], function(def) {
                sorted.push(def);
            });
        });

        return sorted;
    },

    /**
     * Reloads the view & parent model data
     *
     * @param {Object} data
     */
    reloadData: function(data) {
        this.setReloadSingleJourney();
        this.reloadViewData(data);
        this.reloadParentModel();
    },

    /**
     * Set reloadSingleJourney flag so we do not load all journeys when this is set
     */
    setReloadSingleJourney: function() {
        if (!this.context || !this.context.parent || !this.context.parent.parent) {
            return;
        }

        this.context.parent.parent.set('reloadSingleJourney', true);
    },

    /**
     * Unset reloadSingleJourney flag so if refreshing all journey is required we can do that
     */
    unsetReloadSingleJourney: function() {
        if (!this.context || !this.context.parent || !this.context.parent.parent) {
            return;
        }

        this.context.parent.parent.unset('reloadSingleJourney');
    },

    /**
     * It will check if Presentation mode is Horizontal
     * Then will scroll automatically to the first
     * In-progress stage
     *
     * @return {bool|undefined}
     */
    setHorizontalScrollBarPosition: function() {
        if (_.isEqual(this.getPresentationMode(), 'H')) {
            var leftPos = 0;
            _.each(this.$('.dri-workflow-details .dri-subworkflow'), function(el) {
                var id = $(el).data('id');
                if (this.stages[id]) {
                    if (_.isEqual(this.stages[id].model.get('state'), 'in_progress')) {
                        if (this.parentElement) {
                            this.parentElement.scrollLeft = leftPos;
                        }
                        return false;
                    } else {
                        leftPos = leftPos + this.clientWidth;
                    }
                }
            }, this);
        }
        if (!_.isNull(this.context) &&
            !_.isEmpty(this.context.parent) &&
            !_.isEmpty(this.context.parent.parent) &&
            _.isFunction(this.context.parent.parent.get)) {
            const moduleName = (this.context && this.context.parent.parent.get('module')) ?
                this.context.parent.parent.get('module') : '';
            this.fieldValue = app.CJBaseHelper.getValueFromCache('toggleActiveArchived', 'cj_active_or_archive_filter',
                moduleName, 'dri-workflows-widget-configuration');
            if (!_.isUndefined(this.layout.getComponent('dri-workflows-header'))) {
                let renderfield = this.layout.getComponent('dri-workflows-header').getField('filter');
                if (!_.isUndefined(renderfield)) {
                    renderfield.model.set('cj_active_or_archive_filter', this.fieldValue);
                    _.each(renderfield.dropdownFields, function(field) {
                        renderfield.handleFieldCss(field);
                        field.render();
                    }, renderfield);
                }
            }
        }
    },

    /**
     * {@inheritdoc}
     */
    loadData: function(options) {
        if (this.disposed || this.loading) {
            return;
        }

        this.disablingJourneyAndStartLoading();

        if (options && options.data) {
            this.loadCompleted(options.data);
            return;
        }

        this.loaded = false;
        this.loading = true;

        let url = app.api.buildURL(this.model.module, 'widget-data', {
            id: this.model.get('id')
        });

        app.api.call('read', url, null, {
            success: _.bind(this.loadCompleted, this),
            error: _.bind(this.loadError, this),
            complete: options ? options.complete : null
        });
    },

    /**
     * Handles the error if returned from the api
     *
     * @param {Object} error
     */
    loadError: function(error) {
        this.loaded = true;
        this.loading = false;

        if (this.disposed) {
            return;
        }

        this.enablingJourneyAndDoneLoading();

        var tpl = this.tplErrorMap[error.message] || 'error';
        this.error = error;
        this.template = app.template.get('dri-workflow.' + tpl);
        this.render();
    },

    /**
     * Start disabling and loading the Smart Guide
     */
    disablingJourneyAndStartLoading: function() {
        var width = this.$el.width();
        var height = this.$el.height();
        var elm = $('<div class=\'customer-journey-loading-div\'></div>');
        elm.height(height);
        elm.width(width);
        elm.css('position', 'absolute');
        elm.css('z-index', '100');
        this.$el.prepend(elm);
        this.$el.children().fadeTo('slow', 0.7);
    },

    /**
     * Enable back the Smart Guide
     */
    enablingJourneyAndDoneLoading: function() {
        this.$('.customer-journey-loading-div').remove();
        this.$el.children().fadeTo('slow', 1);
    },

    /**
     * Processes the data returned from the api
     *
     * @param {Object} response
     */
    loadCompleted: function(response) {
        this.getPresentationMode();
        this.loaded = true;
        this.loading = false;

        this.rows = [];
        this.journey = {};
        this.stages = {};
        this.activities = {};

        this.setPresentationModeClass();

        // Make sure the component is not disposed before updating the model
        if (this.disposed) {
            return;
        }

        this.error = '';
        this.template = app.template.get('dri-workflow');

        this.model.set(response);
        this.model.setSyncedAttributes(this.model.attributes);

        // After we update the model with new status etc this widget may have been disposed
        if (this.disposed) {
            return;
        }

        var stages = this.model.getRelatedCollection(this.stageLink);
        stages.comparator = 'sort_order';
        stages.reset();

        // store all activities of journey
        this.journeyActivities = [];
        // store all stages of journey
        this.journeyStages = [];

        _.each(response.stages, function(stage) {
            this.journeyStages[stage.id] = stage.name;

            _.each(stage.activities, function(activity) {
                this.journeyActivities[activity.id] = activity.name;

                _.each(activity.children, function(childActivity) {
                    this.journeyActivities[childActivity.id] = childActivity.name;
                }, this);
            }, this);
        }, this);

        _.each(
            response.stages,
            function(data) {
                var row = this.formatStage(data);
                this.stages[row.model.id] = row;
                stages.add(row.model);
            },
            this
        );

        this.journey = response.journey;
        this._setSubworkflowSpan();

        this.rows = this.chunk(this.stages, 4);
        this.progress = parseFloat(this.model.get('progress')) * 100;

        this.enablingJourneyAndDoneLoading();
        this.render();

        if (this.model.get('state') === 'completed' && !this.isCJRenderedAsTab()) {
            this.addRemoveClasses(this.MORE_LESS_STATUS.LESS);
        }
    },

    /**
     * https://gist.github.com/timruffles/3377784
     *
     * @param array
     * @param chunkSize
     * @return {Object}
     */
    chunk: function(array, chunkSize) {
        if (_.isObject(array)) {
            array = _.toArray(array);
        }

        // If Horizontal Presentation mode, then return all data instead of chunks
        if (_.isEqual(this.getPresentationMode(), 'H')) {
            return [array];
        }

        return _.reduce(
            array,
            function(reducer, item, index) {
                reducer.current.push(item);

                if (reducer.current.length === chunkSize || index + 1 === array.length) {
                    reducer.chunks.push(reducer.current);
                    reducer.current = [];
                }
                return reducer;
            },
            {current: [], chunks: []}
        ).chunks;
    },

    /**
     * Set the Presentation Mode class
     * According to the Presentation mode set
     * In the presentation mode field
     *
     * @return {undefined}
     */
    setPresentationModeClass: function() {
        if (_.isEqual(this.getPresentationMode(), 'H')) {
            this.presentationModeClass = 'dri-workflow-details-horizontal';
        } else {
            this.presentationModeClass = 'dri-workflow-details-vertical';
        }
    },

    /**
     * Returns the Presentation mode set in context
     * By the presentation mode field
     *
     * @return {string}
     */
    getPresentationMode: function() {
        const moduleName = (this.layout && this.layout.context &&
            this.layout.context.get('parentModule')) ? this.layout.context.get('parentModule') : '';
        const mode = app.CJBaseHelper.getValueFromCache('togglestate', 'cj_presentation_mode', moduleName,
            'dri-workflows');
        this.formatButton(mode);
        return mode;
    },

    /**
     * Apply the css class on the button
     * Based upon the presentation mode
     *
     * @param {string} presentationMode
     */
    formatButton: function(presentationMode) {
        if (_.isUndefined(this.$el) || _.isNull(this.$el)) {
            return;
        }
        if (_.isEqual(presentationMode, 'V')) {
            this.$(`[name = "vertical_scroll_view"]`).addClass('toggleButtonBg');
            this.$(`[name = "horizontal_scroll_view"]`).removeClass('toggleButtonBg');
        } else {
            this.$(`[name = "horizontal_scroll_view"]`).addClass('toggleButtonBg');
            this.$(`[name = "vertical_scroll_view"]`).removeClass('toggleButtonBg');
        }
    },

    /**
     * Adds an activity of given type to a stage
     *
     * @param {Object} stage
     * @param {string} module
     */
    addActivity: function(stage, module) {
        var stageContext = this.getStageContextById(stage.get('id'));

        var parent = this.getParentModel();

        var activity = app.data.createBean(module, {
            dri_subworkflow_id: stage.get('id'),
            dri_subworkflow_name: stage.get('name'),
            dri_workflow_template_id: this.model.get(
                'dri_workflow_template_id'
            ),
            dri_workflow_template_name: this.model.get(
                'dri_workflow_template_name'
            ),
            dri_subworkflow_template_id: stage.get(
                'dri_subworkflow_template_id'
            ),
            dri_subworkflow_template_name: stage.get(
                'dri_subworkflow_template_name'
            ),
            dri_workflow_id: stage.get('dri_workflow_id'),
            dri_workflow_name: stage.get('dri_workflow_name'),
            parent_type: parent ? parent.module : '',
            parent_name: parent ?
                parent.get('name') || parent.get('full_name') :
                '',
            parent_id: parent ? parent.id : '',
        });

        var lastActivity =
            this.stages[stage.id] &&
            _.last(_.toArray(this.stages[stage.id].activities));

        if (lastActivity) {
            activity.set(
                this.activitySortOrder,
                parseInt(lastActivity.data[this.activitySortOrder]) + 1
            );
        }

        var context = stageContext.getChildContext({
            module: module,
            model: activity,
            forceNew: true,
            create: true,
        });

        app.drawer.open({
            module: module,
            layout: 'create',
            context: context,
        },
            _.bind(function(context, model) {
                // Only reload if the model was saved
                if (model) {
                    this.reloadData();
                }
            }, this)
        );
    },

    /**
     * Adds an activity of given type to an activity
     * Creating a subactivity
     *
     * @param {Object} activity
     * @param {string} module
     */
    addSubActivity: function(activity, module) {
        var order = `${activity.get(this.activitySortOrder)}.`;
        var stageContext = this.getStageContextById(
            activity.get(this.activityStageId)
        );

        var parent = this.getParentModel();

        let subActivity =
            this.stages[activity.get(this.activityStageId)].activities[
            activity.id
            ];
        var children =
            this.stages[activity.get(this.activityStageId)] && subActivity ?
                subActivity.children :
                {};

        var last = _.last(_.values(children));

        if (last) {
            order = `${activity.get(this.activitySortOrder)}.`;
            order +=
                parseInt(
                    last.model.get(this.activitySortOrder).split('.')[1]
                ) + 1;
        } else {
            order = `${activity.get(this.activitySortOrder)}.1`;
        }

        // Creating a subactivity
        var child = app.data.createBean(module, {
            dri_subworkflow_id: activity.get('dri_subworkflow_id'),
            dri_subworkflow_name: activity.get('dri_subworkflow_name'),
            dri_workflow_id: activity.get('dri_workflow_id'),
            dri_workflow_name: activity.get('dri_workflow_name'),
            parent_type: parent ? parent.module : '',
            parent_name: parent ?
                parent.get('name') || parent.get('full_name') :
                '',
            parent_id: parent ? parent.id : '',
            cj_parent_activity_type: activity.module,
            cj_parent_activity_id: activity.id,
        });

        child.set(this.activitySortOrder, order);

        var context = stageContext.getChildContext({
            module: module,
            model: child,
            forceNew: true,
            create: true,
        });

        app.drawer.open({
            module: module,
            layout: 'create',
            context: context,
        },
            _.bind(function(context, model) {
                // Only reload if the model was saved
                if (model) {
                    this.reloadData();
                }
            }, this)
        );
    },

    /**
     * Creates a new stage and opens up the drawer
     */
    addStageClick: function() {
        var stage = app.data.createBean(this.stageModule, {
            dri_workflow_id: this.model.get('id'),
            dri_workflow_name: this.model.get('name')
        });

        var lastStage = this.model.getRelatedCollection(this.stageLink).last();

        if (lastStage) {
            stage.set('sort_order', parseInt(lastStage.get('sort_order')) + 1);
        }

        var context = this.context.getChildContext({
            module: this.stageModule,
            model: stage,
            forceNew: true,
            create: true
        });

        app.drawer.open({
            module: this.stageModule,
            layout: 'create',
            context: context
        }, _.bind(function(context, model) {
            // Only reload if the model was saved
            if (model) {
                this.reloadData();
            }
        }, this));
    },

    /**
     * Links an existing activity of given type to a stage
     *
     * @param {Object} stage
     * @param {string} module
     */
    linkExistingActivity: function(stage, module) {
        const parent = this.getParentModel();
        const stageLinkBean = stage.link ? stage.link.bean : undefined;

        let activityData = {
            dri_subworkflow_id: stage.get('id'),
            dri_subworkflow_name: stage.get('name'),
            dri_workflow_template_id: this.model.get(
                'dri_workflow_template_id'
            ),
            dri_workflow_template_name: this.model.get(
                'dri_workflow_template_name'
            ),
            dri_subworkflow_template_id: stage.get(
                'dri_subworkflow_template_id'
            ),
            dri_subworkflow_template_name: stage.get(
                'dri_subworkflow_template_name'
            ),
            dri_workflow_id: stageLinkBean ? stageLinkBean.get('id') : '',
            dri_workflow_name: stageLinkBean ? stageLinkBean.get('name') : '',
            parent_type: parent ? parent.module : '',
            parent_name: parent ?
                parent.get('name') || parent.get('full_name') :
                '',
            parent_id: parent ? parent.id : '',
        };

        const lastActivity =
            this.stages[stage.id] &&
            _.last(_.toArray(this.stages[stage.id].activities));

        if (lastActivity) {
            activityData[this.activitySortOrder] = parseInt(lastActivity.data[this.activitySortOrder]);
        }

        let filterLabel = '';
        switch (module) {
            case 'Tasks':
                filterLabel = 'LBL_AVAILABLE_TASKS';
                break;
            case 'Calls':
                filterLabel = 'LBL_AVAILABLE_CALLS';
                break;
            case 'Meetings':
                filterLabel = 'LBL_AVAILABLE_MEETINGS';
                break;
            default:
                break;
        }

        const filterOptions = new app.utils.FilterOptions()
            .config({
                initial_filter: 'available_items',
                initial_filter_label: filterLabel,
                filter_populate: {
                    'is_customer_journey_activity': {
                        $equals: 0,
                    },
                    'status': {
                        $not_in: module === 'Tasks' ? ['Not Applicable'] : ['Not Held'],
                    }
                },
            }).format();

        app.drawer.open({
            layout: 'dri-link-existing-activity',
            context: {
                module: module,
                isMultiSelect: true,
                filterOptions: filterOptions,
                stageParent: stage,
            },
        },
            _.bind(function(newActivities) {
                if (newActivities && newActivities.length) {
                    app.alert.show('loading', {
                        title: app.lang.get('LBL_LOADING'),
                        level: 'process',
                    });
                    const moduleSingularLower = app.lang.getModuleName(module).toLowerCase();
                    const modulePluralLower = app.lang.getModuleName(module, {plural: true}).toLowerCase();

                    let bulkCalls = newActivities.map((activity) => {
                        activityData[this.activitySortOrder] = activityData[this.activitySortOrder] + 1;
                        let url = app.api.buildURL(module + '/' + activity.get('id'));
                        return {
                            url: url.substr(4),
                            method: 'PUT',
                            data: _.extend(activity.attributes, activityData),
                        };
                    });

                    if (bulkCalls.length) {
                        let genericErrorAlert = {
                            level: 'error',
                            messages: app.lang.get('LBL_CJ_ACTIVITY_LINK_ERROR_GENERIC', null, {moduleSingularLower}),
                            autoClose: true,
                            autoCloseDelay: 7000,
                        };

                        app.api.call('create', app.api.buildURL(null, 'bulk'), {
                            requests: bulkCalls
                        }, null, {
                            success: _.bind(function(bulkResponses) {
                                let message = '';
                                let messageData = {};
                                let successRequests = [];
                                let errorRequests = [];

                                _.each(bulkResponses, function(response) {
                                    let activity = response.contents;
                                    if (response.status === 200) {
                                        successRequests.push(activity);
                                    } else {
                                        errorRequests.push(activity);
                                    }
                                }, this);

                                if (successRequests.length) {
                                    if (successRequests.length === 1) {
                                        let activity = successRequests[0];
                                        message = 'LBL_CJ_ACTIVITY_LINK_SUCCESS';
                                        messageData = {
                                            id: activity.id,
                                            module,
                                            moduleSingularLower,
                                            name: activity.name,
                                        };
                                    } else {
                                        message = 'LBL_CJ_ACTIVITY_LINK_SUCCESS_PLURAL';
                                        messageData = {
                                            modulePluralLower,
                                        };
                                    }

                                    app.alert.show('success', {
                                        level: 'success',
                                        messages: app.lang.get(message, null, messageData),
                                        autoClose: true,
                                        autoCloseDelay: 7000,
                                    });
                                }

                                if (errorRequests.length) {
                                    if (errorRequests.length === 1) {
                                        let activity = errorRequests[0];
                                        message = 'LBL_CJ_ACTIVITY_LINK_ERROR';
                                        messageData = {
                                            id: activity.id,
                                            module,
                                            moduleSingularLower,
                                            name: activity.name,
                                        };
                                    } else {
                                        message = 'LBL_CJ_ACTIVITY_LINK_ERROR_PLURAL';
                                        messageData = {
                                            modulePluralLower,
                                        };
                                    }

                                    app.alert.show('error', {
                                        level: 'error',
                                        messages: app.lang.get(message, null, messageData),
                                        autoClose: true,
                                        autoCloseDelay: 7000,
                                    });
                                }

                                stage.save({}, {
                                    success: _.bind(function() {
                                        stageLinkBean.save({}, {
                                            success: _.bind(function() {
                                                let layout = this.layout;
                                                layout.loadDataClicked = true;
                                                layout.context.set('customer_journey_fetching_parent_model', false);
                                                this.reloadData();
                                                app.alert.dismiss('loading');
                                            }, this),
                                            error: function() {
                                                app.alert.dismiss('loading');
                                                app.alert.show('error', genericErrorAlert);
                                            },
                                        });
                                    }, this),
                                    error: function() {
                                        app.alert.dismiss('loading');
                                        app.alert.show('error', genericErrorAlert);
                                    },
                                });
                            }, this),
                            error: function() {
                                app.alert.dismiss('loading');
                                app.alert.show('error', genericErrorAlert);
                            },
                        });
                    }
                }
            }, this)
        );
    },

    /**
     * Get the icon tool-tip
     *
     * @param {Object} activity
     * @return {string}
     */
    getIconTooltip: function(activity) {
        var activityTypeList = app.lang.getAppListStrings('dri_workflow_task_templates_activity_type_list');
        switch (activity.module) {
            case 'Tasks':
                var typeList = app.lang.getAppListStrings('dri_workflow_task_templates_type_list');
                return typeList[activity.get('customer_journey_type')] || activityTypeList[activity.module];
            default:
                return activityTypeList[activity.module];
        }
    },

    /**
     * Returns the status label from an activity model
     *
     * @param {Object} activity
     * @return {string}
     */
    getStatusLabel: function(activity) {
        let statusList = app.lang.getAppListStrings(activity.fields.status.options);
        let status = statusList[activity.get('status')];
        let points = activity.get('customer_journey_points') || 0;
        let score = activity.get('customer_journey_score') || 0;
        let progress = activity.get('customer_journey_progress') || 0;
        let label = points === 1 ? 'LBL_WIDGET_POINT' : 'LBL_WIDGET_POINTS';
        let labelValue = app.lang.get(label, 'DRI_Workflows');
        status = `${ status } - ${ progress }% (${ score }/${ points } ${ labelValue })`;

        if (!_.isEmpty(activity.get('cj_momentum_start_date'))) {
            let momentumLabel = app.lang.get('LBL_MOMENTUM_RATIO', 'DRI_Workflows');
            let momentum = activity.get('cj_momentum_ratio');
            let score = activity.get('cj_momentum_score');
            let points = activity.get('cj_momentum_points');
            let label = app.lang.get('LBL_WIDGET_POINTS', 'DRI_Workflows');
            status += `\n${ momentumLabel } - ${ momentum }% (${ score }/${ points } ${ label })`;
        }

        return status;
    },

    /**
     * Returns the status class from an activity model
     *
     * @param {Object} activity
     * @return {string}
     */
    getStatusClass: function(activity) {
        return `dri-subworkflow-activity-${ activity.get('status').replace(/\s+/, '-').toLowerCase() }`;
    },

    /**
     * Returns the type class from an activity model
     *
     * @param {Object} activity
     * @return {string}
     */
    getTypeClass: function(activity) {
        return activity.get('type');
    },

    /**
     * Get the icon as required
     * Based on different types of activities
     *
     * @param {Object} activity
     * @return {string}
     */
    getIconByActivityType: function(activity) {
        switch (activity.get('_module')) {
            case 'Tasks':
                switch (activity.get('customer_journey_type')) {
                    case 'customer_task':
                        return 'sicon sicon-star-fill';
                    case 'milestone':
                        return 'sicon sicon-trophy icon-trophy';
                    case 'internal_task':
                        return 'sicon sicon-user';
                    case 'agency_task':
                        return 'sicon sicon-account';
                    case 'automatic_task':
                        return 'sicon sicon-refresh';
                    default:
                        return '';
                }
                break;
            case 'Meetings':
                return 'sicon sicon-calendar';
            case 'Calls':
                return 'sicon sicon-phone';
            default:
                return '';
        }
    },

    /**
     * Check if activity is parent
     *
     * @param {Object} activity
     * @return {boolean}
     */
    isParent: function(activity) {
        return activity.get('is_cj_parent_activity');
    },

    /**
     * Get the icon as required
     * Based on different types of activities
     *
     * @param {Object} activity
     * @return {string}
     */
    getIconByActivityType: function(activity) {
        switch (activity.get('_module')) {
            case 'Tasks':
                switch (activity.get('customer_journey_type')) {
                    case 'customer_task':
                        return 'sicon sicon-star-fill';
                    case 'milestone':
                        return 'sicon sicon-trophy icon-trophy';
                    case 'internal_task':
                        return 'sicon sicon-user';
                    case 'agency_task':
                        return 'sicon sicon-account';
                    case 'automatic_task':
                        return 'sicon sicon-refresh';
                    default:
                        return '';
                }
                break;
            case 'Meetings':
                return 'sicon sicon-calendar';
            case 'Calls':
                return 'sicon sicon-phone';
            default:
                return '';
        }
    },

    /**
     * Event handler when clicking the activity form
     *
     * @param {event} ev
     */
    activityFormClicked: function(ev) {
        var $el = $(ev.currentTarget);
        var id = $el.data('id');
        var triggerEvent = $el.data('trigger_event');
        var activity = this.activities[id];

        this.handleForms(
            activity,
            triggerEvent,
            _.bind(function() {
                switch (triggerEvent) {
                    case 'completed':
                        this.completeActivity(activity);
                        break;
                    case 'in_progress':
                        this.startActivity(activity);
                        break;
                    case 'not_applicable':
                        this.notApplicableActivity(activity);
                        break;
                    default:
                        app.alert.show('warning_no_action', {
                            level: 'warning',
                            messages: translate('LBL_ACTION_NOT_FOUND'),
                            autoClose: true,
                            autoCloseDelay: 9000
                        });
                        break;
                }
            }, this)
        );
    },

    /**
     * Provide the fields to validate of specific module
     *
     * @param {string} module
     * @return {Object}
     */
    getFieldsToValidate: function(module) {
        let fields = {};

        if (this.layout && this.layout.fieldsToValidate) {
            fields = module ? this.layout.fieldsToValidate[module] : this.layout.fieldsToValidate;
        }

        return fields || {};
    },

    /**
    * Set the flag value
    */
    setGuidesFlag: function() {
        if (!_.isUndefined(this.layout.getComponent('dri-workflows-header'))) {
            let headerView = this.layout.getComponent('dri-workflows-header');
            if (!_.isUndefined(headerView)) {
                headerView.smartGuidesFlag = false;
            }
        }
    },

    /**
     * {@inheritdoc}
     */
    _dispose: function() {
        this.stopListening();
        if (this.model.get('assigned_user_id')) {
            window.removeEventListener('resize', this.handleResize, true);
        }
        this._super('_dispose');
    },
});

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
 * @class View.Views.Base.DRI_Workflow_Templates.DesignerView
 * @alias SUGAR.App.view.views.BaseDRI_Workflow_TemplatesDesignerView
 * @extends View.Views.Base.DriWorkflowView
 */
({
    plugins: ['Tooltip', 'ToggleMoreLess', 'SugarLogic', 'CJEvents', 'CJForms', 'CJViewAndField'],

    extendsFrom: 'DriWorkflowView',

    stageModule: 'DRI_SubWorkflow_Templates',
    stageLink: 'dri_subworkflow_templates',
    activityStageId: 'dri_subworkflow_template_id',
    parentActivityId: 'parent_id',
    activitySortOrder: 'sort_order',
    activityUrlField: 'url',

    /**
     * Initialize properties
     */
    _initProperties: function() {
        this.stagesSortable = true;
        this.activitiesSortable = true;
        this.modelLinks = true;
        this.stageLinks = true;
        this.activityLinks = true;
    },

    /**
     * Get the status class
     *
     * @param {Object} activity
     * @return {string}
     */
    getStatusClass: function(activity) {
        return '';
    },

    /**
     * Get the activity type
     *
     * @return {string}
     */
    getTypeClass: function(activity) {
        return activity.get('activity_type') === 'Tasks' ? activity.get('type') : '';
    },
    /**
     * Overriding that function to avoid running the parent function's code.
     */
    activityFormClicked: function() {},

    /**
     * Adds an activity of given type to a stage
     *
     * @param {Object} stage
     * @param {string} module
     */
    addActivity: function(stage, module) {
        let stageContext = this.getStageContextById(stage.get('id'));
        let model = stageContext.get('model');

        let activity = app.data.createBean('DRI_Workflow_Task_Templates', {
            dri_subworkflow_template_id: model.get('id'),
            dri_subworkflow_template_name: model.get('name'),
            dri_workflow_template_id: model.get('dri_workflow_template_id'),
            dri_workflow_template_name: model.get('dri_workflow_template_name'),
            activity_type: module,
        });

        let lastActivity = this.stages[stage.id] && _.last(_.toArray(this.stages[stage.id].activities));
        if (lastActivity) {
            activity.set('sort_order', parseInt(lastActivity.data.sort_order) + 1);
        }

        let context = stageContext.getChildContext({
            module: activity.module,
            model: activity,
            forceNew: true,
            create: true,
        });

        app.drawer.open(
            {
                module: activity.module,
                layout: 'create',
                context: context,
            }, _.bind(this.reloadView, this));
    },

    /**
     * This function reloads the data to show updated version
     * @param {Object} context
     * @param {Bean} model
     */
    reloadView: function(context, model) {
        // only reload if the model was saved
        if (model) {
            this.reloadData();
        }
    },

    /**
     * Adds an activity of given type to a activity
     *
     * @param {Object} activity
     * @param {string} module
     */
    addSubActivity: function(activity, module) {
        let order = `${ activity.get(this.activitySortOrder) }.`;
        let stageContext = this.getStageContextById(activity.get('dri_subworkflow_template_id'));

        let stage = this.stages[activity.get(this.activityStageId)];
        let children = stage && stage.activities[activity.id] ? stage.activities[activity.id].children : {};

        let last = _.last(_.values(children));
        if (last) {
            order += (parseInt(last.model.get(this.activitySortOrder).split('.')[1]) + 1);
        } else {
            order += '1';
        }

        let child = app.data.createBean('DRI_Workflow_Task_Templates', {
            dri_subworkflow_template_id: activity.get('dri_subworkflow_template_id'),
            dri_subworkflow_template_name: activity.get('dri_subworkflow_template_name'),
            dri_workflow_template_id: activity.get('dri_workflow_template_id'),
            dri_workflow_template_name: activity.get('dri_workflow_template_name'),
            sort_order: order,
            parent_id: activity.get('id'),
            parent_name: activity.get('name'),
            activity_type: module,
        });

        let context = stageContext.getChildContext({
            module: child.module,
            model: child,
            forceNew: true,
            create: true,
        });

        app.drawer.open(
            {
                module: child.module,
                layout: 'create',
                context: context
            }, _.bind(this.reloadView, this));
    },

    /**
     * @param {Object} response
     */
    loadCompleted: function(response) {
        this._super('loadCompleted', [response]);
        if (!this.disposed) {
            this.toggleMoreLess(this.MORE_LESS_STATUS.MORE);
        }
    },

    /**
     * Get the icon as required based on different types of activities
     *
     * @param {Object} activity
     * @return {string|undefined}
     */
    getIconByActivityType: function(activity) {
        switch (activity.get('activity_type')) {
            case 'Tasks':
                return this.getIconByType(activity.get('type'));
            case 'Meetings':
                return 'sicon sicon-calendar';
            case 'Calls':
                return 'sicon sicon-phone';
        }
    },

    /**
     * Get the icon as required based on different types of activities
     *
     * @param {Object} type
     * @return {string|undefined}
     */
    getIconByType: function(type) {
        switch (type) {
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
        }
    },

    /**
     * Returns the status label from an activity model
     *
     * @param {Object} activity
     * @return {string}
     */
    getStatusLabel: function(activity) {
        let points = activity.get('points') || 0;
        let label = points === 1 ? 'LBL_WIDGET_POINT' : 'LBL_WIDGET_POINTS';
        return `${points} ${app.lang.get(label, 'DRI_Workflows')}`;
    },

    /**
     * Get the icon tool-tip
     *
     * @param {Object} activity
     * @return {string}
     */
    getIconTooltip: function(activity) {
        let activityTypeList = app.lang.getAppListStrings('dri_workflow_task_templates_activity_type_list');
        let activityType = activity.get('activity_type');

        if (activityType === 'Tasks') {
            let typeList = app.lang.getAppListStrings('dri_workflow_task_templates_type_list');
            return typeList[activity.get('type')] || activityTypeList[activityType];
        } else {
            return activityTypeList[activityType];
        }
    },

    /**
     * Check if activity is parent
     *
     * @param {Object} activity
     * @return {boolean}
     */
    isParent: function(activity) {
        return activity.get('is_parent');
    },

    /**
    * Creates a new stage and opens up the drawer
    */
    addStageClick: function() {
        let stage = app.data.createBean(this.stageModule, {
            dri_workflow_template_id: this.model.get('id'),
            dri_workflow_template_name: this.model.get('name'),
        });

        let lastStage = this.model.getRelatedCollection(this.stageLink).last();
        if (lastStage) {
            stage.set('sort_order', parseInt(lastStage.get('sort_order')) + 1);
        }

        stage.set('start_next_journey_id', '');
        stage.set('start_next_journey_name', '');

        let context = this.context.getChildContext({
            module: this.stageModule,
            model: stage,
            forceNew: true,
            create: true,
        });

        app.drawer.open(
            {
                module: this.stageModule,
                layout: 'create',
                context: context,
            }, _.bind(this.reloadView, this));
    },
});

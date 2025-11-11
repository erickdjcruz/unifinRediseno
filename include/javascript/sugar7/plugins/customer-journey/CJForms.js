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
        app.plugins.register('CJForms', ['view', 'field'], {

            /**
             * Handles the forms logic
             *
             * @param {Object} target
             * @param {event} triggerEvent
             * @param {Function} callback
             * @return
             */
            handleForms: function(target, triggerEvent, callback) {
                if (this._loadingFormTarget) {
                    return;
                }

                let stage = null;
                let form = null;
                let url = '';
                let message = 'Processing';
                if (_.isEqual(callback, null)) {
                    // For Stage
                    stage = this.stages[target.id];
                    form = _.first(this.getFormsOrStageAndJourneyForms(stage.data.forms, triggerEvent));

                    if (form) {
                        url = app.api.buildURL('CJ_Forms', 'stage-target', {id: form.id}, {stage_id: target.id});
                        message = 'Processing Stage';
                    }
                } else if (_.isEqual(callback, 'journeyCall')) {
                    // For Journey
                    form = _.first(this.getFormsOrStageAndJourneyForms(this.journey.forms, triggerEvent));

                    if (form) {
                        url = app.api.buildURL('CJ_Forms', 'journey-target', {id: form.id}, {
                            journey_id: this.model.get('id'),
                        });
                        message = 'Processing Smart Guide';
                    }
                } else {
                    callback = callback || $.noop;
                    this.prevState = this.stages;
                    form = _.first(this.getFormsOrStageAndJourneyForms(target.get('forms'), triggerEvent));
                    if (form) {
                        url = app.api.buildURL('CJ_Forms', 'target', {id: form.id}, {activity_id: target.id});
                        message = 'Processing activity';
                    }
                }

                if (!form) {
                    if (_.isFunction(callback)) {
                        callback();
                    }
                    return;
                }

                if (form.action_trigger_type === 'automatic_create' ||
                    form.action_trigger_type === 'automatic_update') {
                    if (_.isFunction(callback)) {
                        callback();
                    }
                    return;
                }

                if (!url) {
                    return;
                }

                app.alert.show('loading_form_target', {
                    level: 'info',
                    messages: message
                });

                this._loadingFormTarget = true;
                app.api.call('read', url, null, {
                    success: _.bind(this.handleFormsSuccess, this, callback, form),
                    error: _.bind(function(result) {
                        app.alert.dismiss('loading_form_target');
                        this._loadingFormTarget = false;
                        app.alert.show('error', {
                            level: 'error',
                            messages: result.message,
                            autoClose: true
                        });
                    }, this)
                });
            },

            /**
             * Handle Forms Read API Success
             *
             * @param {Function} callback
             * @param {Object} form
             * @param {Object} response
             */
            handleFormsSuccess: function(callback, form, response) {
                app.alert.dismiss('loading_form_target');
                this._loadingFormTarget = false;

                if (!response.parent.id || (_.includes(['update_record', 'view_record'], form.action_type) &&
                    !response.target.id)) {
                    app.alert.show('related_record_not_found', {
                        level: 'info',
                        messages: app.lang.get('LBL_COULD_NOT_FIND_RELATED_RECORD', 'CJ_Forms'),
                        autoClose: true,
                    });

                    if (form.ignore_errors) {
                        callback();
                    }
                }

                let target = app.data.createBean(response.module,
                    form.action_type !== 'create_record' ? response.target : {});

                if (_.isEqual(callback, null)) {
                    // Set Smart Guide Template
                    target.set('dri_workflow_id', this.model.get('id'));
                    target.set('dri_workflow_name', this.model.get('name'));
                }

                if (form.action_type === 'view_record' && response.target.id) {
                    this.reRoute(target.module, target.id);
                } else if ((_.isEmpty(form.action_trigger_type) && form.action_type !== 'view_record') ||
                    form.action_trigger_type === 'manual_create' ||
                    (form.action_trigger_type === 'manual_update' && response.target.id)) {
                    this.createUpdate(callback, form, response, target);
                }

                if (_.isFunction(callback)) {
                    callback();
                }
            },

            /**
             * Create or Update the Activity
             *
             * @param {Function} callback
             * @param {Object} form
             * @param {Object} response
             * @param {Object} target
             */
            createUpdate: function(callback, form, response, target) {
                let emailTemplate;
                let parent = app.data.createBean(response.parent._module, response.parent);
                let collection = app.data.createBeanCollection(response.module, []);
                let layout = 'create';

                if (_.isEqual(target.module, 'Emails')) {
                    layout = 'create';
                    if (form.action_type === 'create_record') {
                        layout = 'compose';
                    }
                }

                let parentContext = this.context.getChildContext({
                    model: parent,
                    module: parent.module,
                    create: _.contains(['create', 'compose'], layout) ? true : false,
                    forceNew: true,
                    cjPanelNotRender: true,
                });

                if (target.module === 'Emails' && form.action_trigger_type === 'manual_create' &&
                    !_.isEmpty(form.email_templates_id) && !_.isUndefined(response.emailData.subject) &&
                    !_.isUndefined(response.emailData.body_html)) {
                    emailTemplate = app.data.createBean('EmailTemplates', {id: form.email_templates_id});
                    target.set('name', response.emailData.subject);
                    target.set('description_html', response.emailData.body_html);
                }

                let context = parentContext.getChildContext({
                    model: target,
                    collection: collection,
                    module: target.module,
                    link: response.linkName,
                    create: _.contains(['create', 'compose'], layout) ? true : false,
                    forceNew: true,
                    cjPanelNotRender: true,
                });

                let populatedTarget = this.populateModelFromLinkedData(context, form, response);
                let relationship;
                if (!_.isEmpty(populatedTarget)) {
                    context.set('model', populatedTarget);
                    if (!_.isEmpty(populatedTarget.link)) {
                        relationship = app.metadata.getRelationship(populatedTarget.link.name);
                    }
                }

                if (this.validateRealtionship(relationship)) {
                    app.alert.dismiss('loading_form_target');
                    this.addSubActivity(this.activities[context.parent.get('model').id], target.module);
                } else {
                    app.drawer.open(
                        {
                            module: target.module,
                            layout: layout,
                            context: context,
                        },
                        _.bind(function(context, model) {
                            // In case RSA has updated parent model or subpanel
                            if (model) {
                                let parentModule = this.context.parent.get('parentModule');
                                refreshView = _.find(form.relationship, (rel) => {
                                    return rel.module == parentModule;
                                });
                                if (!_.isEmpty(refreshView)) {
                                    window.location.reload();
                                }
                            }
                            if ((form.parent_type == 'DRI_SubWorkflow_Templates' ||
                                form.parent_type == 'DRI_Workflow_Templates') &&
                                (model && (!_.isEmpty(model.get('dri_subworkflow_id')) ||
                                    !_.isEmpty(model.get('dri_workflow_id')) ||
                                    !_.isEmpty(model.get('parent_id'))))) {
                                this.reloadAllJourneys();
                            }
                            if (model && _.isFunction(callback)) {
                                callback();
                            }
                        }, this)
                    );
                }

                let currentLayout = this.toggleDuplicateCheck(target);
                if (
                    target.module === 'Emails' && form.action_trigger_type === 'manual_create' &&
                    !_.isEmpty(form.email_templates_id) && !_.isUndefined(emailTemplate)
                ) {
                    let sidebarLayout = currentLayout.getComponent('sidebar');
                    if (sidebarLayout) {
                        this.disposeComponents(sidebarLayout, ['dashboard-pane', 'side-drawer']);
                        let mainPaneLayout = sidebarLayout.getComponent('main-pane');
                        if (mainPaneLayout) {
                            let composeView = mainPaneLayout.getComponent('compose-email');
                            this.view = composeView;
                            let recipientRecords = this.getRecipientsForComposeEmail(response.emailData);
                            if (!_.isEmpty(recipientRecords)) {
                                this.view.model.get('to_collection').add(recipientRecords);
                            }
                            app.view.fields.BaseEmailsHtmleditable_tinymceField.prototype._applyTemplate.call(this,
                                emailTemplate);
                        }
                    }
                }

                // Populate the model data from populate fields
                if (!_.isEmpty(form.populate_fields) && !_.isEmpty(JSON.parse(form.populate_fields))) {
                    _.each(JSON.parse(form.populate_fields), function(pf, key) {
                        if (!(_.isEqual(target.module, 'Emails') && _.contains(['name', 'subject', 'body_html'],
                            key))) {
                            if (!_.isUndefined(pf.id) && !_.isUndefined(pf.actualFieldName)) {
                                if ((pf.type === 'date' || pf.type === 'datetimecombo') &&
                                    !_.isUndefined(pf.childFieldsData)) {
                                    this.handleDateTimeField(pf, currentLayout);
                                } else if (pf.type === 'relate') {
                                    currentLayout.model.set(pf.actual_id_name, pf.id_value);
                                    currentLayout.model.set(pf.actualFieldName, pf.value);
                                } else if (pf.type === 'currency' && !_.isUndefined(pf.id_name) &&
                                    !_.isUndefined(pf.id_value)) {
                                    currentLayout.model.set(pf.id_name, pf.id_value);
                                    currentLayout.model.set(pf.actualFieldName, pf.value);
                                } else {
                                    currentLayout.model.set(pf.actualFieldName, pf.value);
                                }
                            } else {
                                currentLayout.model.set(pf.actualFieldName, pf.value);
                            }
                        }
                    }, this);
                }

                app.alert.dismiss('process_complete_activity');
                app.alert.dismiss('processing_complete_activity_click');
            },

            /**
             * Enable or Disable the duplicate Check
             *
             * @param {Object} target
             * @return {Object}
             */
            toggleDuplicateCheck: function(target) {
                // This is a hack to disable enable duplicate check
                let currentLayout = _.last(app.drawer._components);
                let sidebarLayout = currentLayout.getComponent('sidebar');

                if (sidebarLayout) {
                    this.disposeComponents(sidebarLayout, ['dashboard-pane', 'side-drawer']);
                    let mainPaneLayout = sidebarLayout.getComponent('main-pane');

                    if (mainPaneLayout) {
                        let disposeComp = ['filterpanel'];
                        if (!_.isEqual(target.module, 'Quotes')) {
                            disposeComp.push('extra-info');
                        }
                        this.disposeComponents(mainPaneLayout, disposeComp);
                        let record = mainPaneLayout.getComponent('record');
                        if (record) {
                            record.handleCancel = _.bind(function() {
                                this.recordCreateCancel(record);
                            }, this);
                            record.handleSave =  _.bind(function() {
                                this.handleSaveSuccess(record);
                            }, this);
                            record.setButtonStates(record.STATE.EDIT);
                            record.toggleEdit(true);
                        }
                        let create = mainPaneLayout.getComponent('create');
                        if (create) {
                            create.enableDuplicateCheck = false;
                            create.hasUnsavedChanges = function() {
                                return false;
                            };
                            create.cancel = function() {
                                create.model.revertAttributes();
                                this.recordCreateCancel(create);
                            };
                        }
                    }
                }
                return currentLayout;
            },

            /**
            * modules relationship is valid
            *
            * @param {Object} relationship
            */
            validateRealtionship: function(relationship) {
                const modules = ['Tasks', 'Meetings', 'Calls'];
                let flag = false;
                if (_.isEmpty(relationship) ||
                    _.isEmpty(relationship.lhs_module) ||
                    _.isEmpty(relationship.rhs_module)) {
                    return flag;
                }
                if (modules.includes(relationship.lhs_module) && modules.includes(relationship.rhs_module)) {
                    flag = true;
                }
                return flag;
            },

            /**
             * Handle Cancel of Activity
             *
             * @param {Object} recordCreate
             */
            recordCreateCancel: function(recordCreate) {
                app.events.trigger('create:model:changed', false);
                recordCreate.$el.off();
                if (app.drawer.count()) {
                    app.drawer.close(recordCreate.context);
                    recordCreate._dismissAllAlerts();
                } else {
                    app.router.navigate(recordCreate.module, {trigger: true});
                }
            },

            /**
             * On succesfully saving record of Activity
             *
             * @param {Object} record
             */
            handleSaveSuccess: function(record) {
                if (record.disposed) {
                    return;
                }
                record._saveModel();
                record.$('.record-save-prompt').hide();

                if (!record.disposed) {
                    if (record.editOnly) {
                        // If we are in edit-only mode, prevent multiple saves at a time.
                        // Buttons will be re-enabled after save call is complete
                        record.toggleButtons(false);
                    } else {
                        record.setButtonStates(record.STATE.VIEW);
                        record.action = 'detail';
                        record.unsetContextAction();
                        record.toggleEdit(false);
                        record.inlineEditMode = false;
                    }
                }
                if (record.closestComponent('drawer')) {
                    app.drawer.close(record.context, record.model);
                }
            },

            /**
             * Set Date according to selective type
             *
             * @param {Object} pf
             * @param {Object} currentLayout
             */
            handleDateTimeField: function(pf, currentLayout) {
                let selectiveType = '';
                if (!_.isUndefined(pf.childFieldsData) && !_.isUndefined(pf.childFieldsData.selective_date)) {
                    selectiveType = pf.childFieldsData.selective_date.value;
                }
                if (_.isEqual(selectiveType, 'relative') && !_.isUndefined(pf.childFieldsData.int_date.value)) {
                    let relativeType = '';
                    let dateValue = parseInt(pf.childFieldsData.int_date.value);
                    if (!_.isUndefined(pf.childFieldsData) && !_.isUndefined(pf.childFieldsData.relative_date)) {
                        relativeType = pf.childFieldsData.relative_date.value;
                    }
                    if (_.contains(['days', 'months', 'hours', 'minutes'], relativeType)) {
                        let incrementedDate = this.getIncrementedDate(relativeType, dateValue);
                        currentLayout.model.set(pf.actualFieldName, incrementedDate);
                    }
                } else if (_.isEqual(selectiveType, 'fixed') && !_.isUndefined(pf.childFieldsData.main_date.value)) {
                    currentLayout.model.set(pf.actualFieldName, pf.childFieldsData.main_date.value);
                }
            },

            /**
             * Increments date by relativeType by dateValue
             *
             * @param {string} relativeType
             * @param {int} dateValue
             * @return {string}
             */
            getIncrementedDate: function(relativeType, dateValue) {
                let date = new Date();
                switch (relativeType) {
                    case 'days':
                        date.setDate(date.getDate() + dateValue);
                        break;
                    case 'months':
                        date = new Date(date.setMonth(date.getMonth() + dateValue));
                        break;
                    case 'hours':
                        date = new Date(date.setHours(date.getHours() + dateValue));
                        break;
                    case 'minutes':
                        date = new Date(date.setMinutes(date.getMinutes() + dateValue));
                        break;
                }
                return date.toISOString();
            },

            /**
             * Fetch stage for a given id
             * Uses handleForms() for RSA related to stage
             *
             * @param {Object} activity
             * @param {string} stageId
             */
            handleFormsForStage: function(activity, stageId) {
                if (!_.isEmpty(activity) && !_.isEmpty(stageId)) {
                    let startNewJourney = !_.isEmpty(activity.get('start_next_journey_id'));
                    let stage = app.data.createBean(this.stageModule, {
                        id: stageId,
                    });
                    let object = _.clone(this);
                    stage.fetch({
                        success: _.bind(this.handleFormsForStageSuccess, object, stage,
                            startNewJourney, activity),
                    });
                }
            },

            /**
             * Success of Fetch stage for a given id
             *
             * @param {Object} stage
             * @param {boolean} startNewJourney
             */
            handleFormsForStageSuccess: function(stage, startNewJourney, activity) {
                if (_.isEqual(stage.get('state'), 'completed') || _.isEqual(stage.get('state'), 'in_progress')) {
                    let stageTarget = this.stages[stage.id];

                    if (!_.isEmpty(stageTarget) && !_.isUndefined(this.prevState) &&
                        this.prevState[stage.id].model.get('state') !== stage.get('state')) {
                        let stageForm = _.first(this.getFormsOrStageAndJourneyForms(stageTarget.data.forms,
                            stage.get('state')));
                        if (!_.isEmpty(stageForm) && _.isEqual(stage.get('state'), stageForm.trigger_event)) {
                            if (stageForm.trigger_event === 'in_progress') {
                                this.handleForms(stage, stageForm.trigger_event, null);
                            } else if (stageForm.trigger_event === 'completed') {
                                this.handleForms(stage, stageForm.trigger_event, null);
                            }
                        }
                    }
                    if ((stage.get('start_next_journey_id') && _.isEqual(stage.get('state'), 'completed')) ||
                        _.isEqual(startNewJourney, true)) {
                        this.reloadAllJourneys();
                    }
                    if (_.isEqual(stage.get('state'), 'completed')) {
                        this.checkNextStageRSA(activity, activity.get('dri_subworkflow_id'));
                        this.handleFormsForJourney(stage, this.model.get('id'));
                    }
                }
            },

            /**
             * Fetch journey for a given id
             * Uses handleForms() for RSA related to journey
             *
             * @param {Object} stage
             * @param {string} journeyId
             */
            handleFormsForJourney: function(stage, journeyId) {
                if (!_.isEmpty(stage) && !_.isEmpty(journeyId)) {
                    let journey = app.data.createBean('DRI_Workflows', {
                        id: journeyId,
                    });
                    let object = _.clone(this);
                    journey.fetch({
                        success: _.bind(this.handleFormsForJourneySuccess, object, journey),
                    });
                }
            },

            /**
             * Success of Fetch journey for a given id
             *
             * @param {Object} journey
             */
            handleFormsForJourneySuccess: function(journey) {
                if (_.isEqual(journey.get('state'), 'completed')) {
                    let journeyForm = _.first(this.getFormsOrStageAndJourneyForms(this.journey.forms,
                        journey.get('state')));
                    if (!_.isEmpty(journeyForm) && _.isEqual(journey.get('state'), journeyForm.trigger_event)) {
                        if (journeyForm.trigger_event === 'completed') {
                            this.handleForms(journey, journeyForm.trigger_event, 'journeyCall');
                        }
                    }
                }
            },

            /**
             * Completes RSA for next stage, if stage exists
             *
             * @param {Object} activity
             * @param {string} stageId
             */
            checkNextStageRSA: function(activity, stageId) {
                let stageIds = Object.keys(this.stages);
                let currentStageIdIndex = _.indexOf(stageIds, stageId);
                //checking if next stage exists
                if (!_.isUndefined(stageIds[currentStageIdIndex + 1])) {
                    let stage = this.stages[stageIds[currentStageIdIndex + 1]];
                    if (stage.model.get('state') !== 'completed') {
                        this.handleFormsForStage(activity, stageIds[currentStageIdIndex + 1]);
                    }
                }
            },

            /**
             * Reloads all Journeys after creation of new Journey
             */
            reloadAllJourneys: function() {
                this.layout.loadDataClicked = true;
                this.layout.context.set('customer_journey_fetching_parent_model', false);
                this.layout.context.trigger('reload_workflows', true);
            },
        });
    });
})(SUGAR.App);

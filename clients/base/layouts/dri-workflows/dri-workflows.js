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
 * Main layout of CJ
 *
 * @class View.Layouts.Base.DRI_WorkflowsLayout
 * @alias SUGAR.App.view.layouts.BaseDRI_WorkflowsLayout
 * @extends View.Layout
 */
({
    _rendered: false,
    loadDataClicked: false,
    enabled: true,
    startingJourney: false,
    showHeaderRow: true,
    fieldsToValidate: {},
    moduleDependencies: {},

    /**
     * Status values
     *
     * @property
     */
    MORE_LESS_STATUS: {
        MORE: 'more',
        LESS: 'less',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._addedIds = {};
        this._super('initialize', [options]);

        this._setSugarLogicDependenciesForModel(['Calls', 'Meetings', 'Tasks']);

        this.context._fetchCalled = true;
        this.context.set('skipFetch', false);

        let parent = this.context.get('parentModel');
        this.listenTo(parent, 'sync', function() {
            this.reloadJourneys(false);
        });

        this.listenTo(this.collection, 'add', this.addJourney);
        this.listenTo(this.collection, 'remove', this.deleteJourney);
        this.listenTo(this.collection, 'sync', this._populateJourneys);
        this.listenTo(this.collection, 'sync', this.cleanJourneys);

        this.listenTo(this.context, 'reload_workflows', this.reloadJourneys);
        this.listenTo(this.context, 'change:moreLess', this.toggleMoreLess);
        this.listenTo(this.context, 'parent:start_cycle:click', this.startJourneyClicked);
        this.listenTo(this.context, 'parent:vertical_scroll_view:click', this.applyScrollView);
        this.listenTo(this.context, 'parent:horizontal_scroll_view:click', this.applyScrollView);
        this.listenTo(this.context, 'parent:active_smart_guides:click', this.toggleGuides);
        this.listenTo(this.context, 'parent:archive_smart_guides:click', this.toggleGuides);
        this.listenTo(this.context, 'parent:all_smart_guides:click', this.toggleGuides);

        this.toggleMoreLess();

        this.context.set('fields', ['id', 'name']);
        this.collection.orderBy = {
            field: 'date_entered',
            direction: 'asc',
        };
        this.addFiltersInCollection();

        let driWorkflow = app.data.createBean('DRI_Workflows');
        let enabledModules = app.config.customer_journey && app.config.customer_journey.enabled_modules;

        if (enabledModules) {
            enabledModules = enabledModules.split(',');
            if (enabledModules.indexOf(parent.module) === -1) {
                this.enabled = false;
            }
        }

        _.each(driWorkflow.fields, function(def) {
            if (def.module === parent.module && def.customer_journey_parent) {
                if (!def.customer_journey_parent.enabled) {
                    this.enabled = false;
                }
            }
        }, this
        );
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (!this.enabled) {
            this.hide();
        }
        if (this.isPanelTop()) {
            this.renderHeaderPanelData();
        }
    },

    /**
     * Toggle between more and less views
     */
    toggleMoreLess: function() {
        if (this.context.get('moreLess') === 'more' && !this.loadDataClicked) {
            this.loadDataClicked = true;
            this.loadData();
        }
    },

    /**
     * Reloads all journey view data
     *
     * @param {boolean} forceReload
     * @return {undefined}
     */
    reloadJourneys: function(forceReload) {
        if (forceReload) {
            this.syncNewJourneys();
            return;
        }

        if (this.context && this.context.parent && this.context.parent.get('reloadSingleJourney')) {
            return;
        }

        this.addFiltersInCollection();
        let component = this.getComponent('dri-workflows-header');
        component.loadRemoval = !_.isUndefined(component) && !_.isEqual(component.collection.length, 0) ? true : false;
        this.removeJourneyViews();
        this.context.children = [];
        this.context.resetLoadFlag();
        this.loadData();
    },

    /**
     * Add newly created journeys in the DOM and
     * close the completed journeys panel
     */
    syncNewJourneys: function() {
        this.loadData();

        _.each(this._components, function(component) {
            if (_.isEqual(component.name, 'dri-workflow') &&
                _.isFunction(component.addRemoveClasses) && component.MORE_LESS_STATUS &&
                component.model && _.isEqual(component.model.get('state'), 'completed')
            ) {
                component.addRemoveClasses(component.MORE_LESS_STATUS.LESS);
            }
        });
    },

    /**
     * Removes all journey views
     */
    cleanJourneys: function() {
        _.each(this._components, function(component) {
            if (component.name === 'dri-workflow' && !this.collection.get(component.model.id)) {
                this.removeJourneyView(component);
            }
        }, this
        );
    },

    /**
     * Populate the collection of Journey by adding the journeys
     */
    _populateJourneys: function() {
        if (!this.context || !this.context.parent) {
            return;
        }

        let key = app.user.lastState.buildKey(`${this.getActiveOrArchiveMode()}-journeys-order`,
            this.context.parent.get('modelId'),
            this.context.parent.get('module')
        );
        this.setSmartGuideCount();
        let sortedOrder = app.user.lastState.get(key);

        // if user preference is empty then render journeys
        // according to date_entered
        if (!sortedOrder) {
            this.collection.each(this.addJourney, this);
        } else {
            let sortedJourneys = this.getSortedJourneys(sortedOrder);

            _.each(sortedJourneys, this.addJourney, this);
        }
    },

    /**
     * Set the SmartGuide Count
    */
    setSmartGuideCount: function() {
        let component = this.getComponent('dri-workflows-header');
        if (!_.isUndefined(component) && !_.isNull(this.context.parent) && !_.isUndefined(this.context.parent)) {
            const url = app.api.buildURL(this.context.parent.get('module'), 'get-smartguides-count', {
                id: this.collection.link.bean.get('id'),
            });
            component.loadRemoval = false;
            app.api.call('read', url, null, {
                success: _.bind(function(data) {
                    if (this.disposed) {
                        return;
                    }
                    component.smartGuidesFlag = true;
                    component.smartGuidesCount = data;
                    this.collection.dataFetched = true;
                    if (this.isPanelTop()) {
                        this.renderHeaderPanelData();
                    }
                    component._render();
                }, this),
                error: _.bind(function(result) {
                    app.alert.show('error', {
                        level: 'error',
                        messages: result.message,
                        autoClose: true
                    });
                })
            });
        }
    },

    /**
     * Sort Journeys according to Last State
     *
     * @param {Array} sortedOrder
     * @return {Array}
     */
    getSortedJourneys: function(sortedOrder) {
        let syncedJourneys = {};
        let notSyncedJourneys = [];

        // populate `syncedJourneys` object according to sortedOrder
        _.each(sortedOrder, function(id) {
            let journey = this.collection.get(id);

            if (journey) {
                syncedJourneys[id] = journey;
            }
        }, this);

        // capture those journeys which are in collection but not in `syncedJourneys` object
        this.collection.each(function(journey) {
            if (!syncedJourneys[journey.get('id')]) {
                notSyncedJourneys.push(journey);
            }
        }, this);

        const sortedJourneys = _.union(notSyncedJourneys, _.values(syncedJourneys));

        return sortedJourneys.reverse();
    },

    /**
     * Toggles the display of all journey views according to the state
     */
    checkHide: function() {
        let completedJourney = this.collection.filter(function(journey) {
            return journey.get('state') === 'completed';
        }, this);
        let completed = completedJourney.length;

        if (completed === this.collection.length &&
            this.loadDataClicked && this.context.get('showActiveJourneys') === false
        ) {
            this.context.set('moreLess', this.MORE_LESS_STATUS.LESS);
        }
    },

    /**
     * @inheritdoc
     */
    loadData: function() {
        if (this.loadDataClicked) {
            this.context._fetchCalled = false;
            this.collection.dataFetched = false;
            this.render();
            // set skipFetch attribute to false, so that context fetch the collection
            this.context.set('skipFetch', false);
            this.context.loadData();
        }
    },

    /**
     * Initializes a new journey panel, adds it to the layout and loads the data
     *
     * @param {Object} journey
     */
    addJourney: function(journey) {
        if (this._addedIds[journey.id]) {
            return;
        }

        let context = this.context.getChildContext({
            module: 'DRI_Workflows',
            model: journey,
            forceNew: true,
        });
        let view = this.createComponentFromDef({
            view: 'dri-workflow',
            context: context,
        });

        this.addComponentAfterHeader(view);

        view.loadData();

        this._addedIds[journey.id] = true;

        view.render();

        journey.on('change:state', function() {
            if (journey.get('state') === 'completed') {
                this.checkHide();
            }
        }, this
        );
    },

    /**
     * Adds a component to this layout after header.
     *
     * @param {Object} component Component (view or layout) to add.
     */
    addComponentAfterHeader: function(component) {
        let workflowsHeader = this.$el.find('.dri-workflows-header-wrapper');

        if (!component.layout) {
            component.layout = this;
        }

        // add component at 1st index after header
        this._components.splice(1, 0, component);
        // insert component element after header element
        workflowsHeader.after(component.el);
    },

    /**
     * Deletes a journey
     *
     * @param {Object} model
     */
    deleteJourney: function(model) {
        this.removeJourney(model);
        this.setSmartGuideCount();
    },

    /**
     * Removes a journey view
     *
     * @param {Object} view
     */
    removeJourneyView: function(view) {
        delete this._addedIds[view.model.id];
        this.removeComponent(view);
        view.dispose();
    },

    /**
     * Removes a journey and its related view
     *
     * @param {Object} model
     */
    removeJourney: function(model) {
        _.each(this._components, function(component) {
            if (component && _.isEqual(component.name, 'dri-workflow') &&
                component.model && _.isEqual(component.model.get('id'), model.get('id'))) {
                this.removeJourneyView(component);
            }
        }, this);
    },

    /**
     * Removes all journey views
     */
    removeJourneyViews: function() {
        let remove = [];

        _.each(this._components, function(component) {
            if (component.name === 'dri-workflow') {
                remove.push(component);
            }
        }, this
        );

        _.each(remove, this.removeJourneyView, this);
    },

    /**
     * Starts a new journey related to the parent
     *
     * @param {Object} model
     */
    startJourneyClicked: function(model) {
        if (_.isEmpty(model.get('dri_workflow_template_id')) || this.startingJourney) {
            return;
        }

        this.startingJourney = true;

        let url = app.api.buildURL(model.module, 'customer-journey/start-cycle',
            {
                id: this.context.get('parentModel').get('id'),
            },
            {
                template_id: model.get('dri_workflow_template_id'),
            }
        );

        this.$('.dri-workflows-actions-spinner').removeClass('hide');
        model.set({
            'dri_workflow_template_id': '',
            'dri_workflow_template_name': '',
        });

        this.disablingJourneyAndStartLoading();

        app.api.call('create', url, null, {
            success: _.bind(this.createJourneySuccess, this),

            error: function(result) {
                app.alert.show('error', {
                    level: 'error',
                    messages: result.message,
                    autoClose: true,
                });
            },

            complete: _.bind(function() {
                this.startingJourney = false;
                this.$('.dri-workflows-actions-spinner').addClass('hide');
                this.$('.customer-journey-loading-div').remove();
                this.$el.children().fadeTo('slow', 1);
            }, this),
        });
    },

    /**
     * Create Customer Journey success handler
     *
     * @param {Array} data
     */
    createJourneySuccess: function(data) {
        if (this.disposed) {
            return;
        }

        let parentData = data.parentData;

        if (this.context.get('moreLess') === this.MORE_LESS_STATUS.LESS) {
            this.context.set('moreLess', this.MORE_LESS_STATUS.MORE);
        }

        this._updateJourneysOrder(data.journeyId);

        let parentModel = this.context.get('parentModel');
        let _modelBackup = app.utils.deepCopy(parentModel.attributes);

        parentModel.set(parentData);

        let diff = parentModel.getChangeDiff(_modelBackup);
        let changedAttributes = parentModel.changedAttributes();

        if (_.isUndefined(changedAttributes)) {
            changedAttributes = parentModel.changedAttributes(parentModel.getSynced());
        }

        _.each(changedAttributes, function(item, key) {
            parentModel.set(key, diff[key]);
        });

        this.setGuidesCount(this._components);
        if (this.isPanelTop()) {
            this.renderHeaderPanelData();
        }
        if (_.isEqual(this.getActiveOrArchiveMode(), 'active') ||
            _.isEqual(this.getActiveOrArchiveMode(), 'all')) {
            let newJourney = app.data.createBean('DRI_Workflows', {id: data.journeyId});

            if (this.collection.length < app.config.maxQueryResult) {
                this.collection.add(newJourney);
                parentModel.trigger('customer_journey:active-cycle:click', null);
            } else {
                this._render();
            }
        } else {
            this._render();
        }
    },

    /**
     * Is Display Setting equals to Panel Top
     * @return {bool}
     */
    isPanelTop: function() {
        return _.first(this._components) && _.isEqual(_.first(this._components).displaySetting, 'panel_top');
    },

    /* render the data in header panel
    * @return {undefined}
    * @private
    */
    renderHeaderPanelData: function() {
        let cj = !_.isUndefined(this._CJ) ? this._CJ : this;

        if (!_.isEqual(cj.context.get('module'), 'DRI_Workflows')) {
            return;
        }

        if (_.isUndefined(_.first(cj.meta.components)) || _.size(cj.meta.components) === 0) {
            return;
        }
        let headerView = cj.getComponent(_.first(cj.meta.components).view);
        if (!_.isUndefined(headerView) && headerView.smartGuidesFlag && app.config.maxQueryResult) {
            let configCount = app.config.maxQueryResult;
            headerView.smartGuidesCount = headerView.smartGuidesCount < configCount ?
            headerView.smartGuidesCount : configCount;

            let headerText = app.template.get('dri-workflows-header.smart-guide-count')(headerView);

            const journeyTabSelector = '[data-panelname=customer_journey_tab] .record-panel-header span';
            const journeyTab = _.first(this.$el.parent().parent().parent().find(journeyTabSelector));
            if (journeyTab) {
                $(journeyTab).html(headerText);
            }
        }
    },

    /**
     * On creation of new Journey update the order
     *
     * @param {string} journeyId
     */
    _updateJourneysOrder: function(journeyId) {
        const key = app.user.lastState.buildKey('journeys-order', this.context.parent.get('modelId'),
            this.context.parent.get('module')
        );
        let sortedOrder = app.user.lastState.get(key);

        if (_.isArray(sortedOrder)) {
            // add new journey at first place
            sortedOrder.unshift(journeyId);
            // set user preference
            app.user.lastState.set(key, sortedOrder);
        }
    },

    /**
     * Disable the Smart Guide and show loader
     */
    disablingJourneyAndStartLoading: function() {
        let width = this.$el.width();
        let height = this.$el.height();
        let elm = $('<div class="customer-journey-loading-div"></div>');
        elm.height(height);
        elm.width(width);
        elm.css('position', 'absolute');
        elm.css('z-index', '100');
        this.$el.prepend(elm);
        this.$el.children().fadeTo('slow', 0.7);
    },

    /**
     * Applying horizontal or vertical scroll view
     *
     * @param {Object} attribute
     * @param {Object} button
     */
    applyScrollView: function(attribute, button) {
        if (_.isEqual(button.name, 'vertical_scroll_view')) {
            this.model.set('cj_presentation_mode', 'V');
        } else {
            this.model.set('cj_presentation_mode', 'H');
        }
        this.setFieldStateInCache();
        let component = this.getComponent('dri-workflow');
        if (!_.isUndefined(component)) {
            component.setHorizontalScrollBarPosition();
        }

        this.reloadJourneys(false);
    },

    /**
     * set the field state in cache
     * @return {string}
     */
    setFieldStateInCache: function() {
        const moduleName = (this.context && this.context.get('parentModule')) ? this.context.get('parentModule') : '';
        this.statekey = app.user.lastState.buildKey('togglestate', 'cj_presentation_mode', moduleName);
        return app.user.lastState.set(this.statekey, this.model.get('cj_presentation_mode'));
    },

    /**
     * Applying active or archieve
     * @param {Object} attribute
     * @param {Object} button
     */
    toggleGuides: function(attribute, button) {
        if (_.isEqual(button.name, 'archive_smart_guides')) {
            this.model.set('cj_active_or_archive_filter', 'archived');
        } else if (_.isEqual(button.name, 'active_smart_guides')) {
            this.model.set('cj_active_or_archive_filter', 'active');
        } else {
            this.model.set('cj_active_or_archive_filter', 'all');
        }

        this.setActiveArchiveInCache();
        this.reloadJourneys();

    },

    /**
     * set the field state in cache
     *
     * @return {string}
    */
    setActiveArchiveInCache: function() {
        const moduleName = (this.context && this.context.get('parentModule')) ? this.context.get('parentModule') : '';
        this.statekey = app.user.lastState.buildKey('toggleActiveArchived', 'cj_active_or_archive_filter', moduleName);
        return app.user.lastState.set(this.statekey, this.model.get('cj_active_or_archive_filter'));
    },

    /**
     * Unset reloadSingleJourney variable from parent context
     */
    unsetReloadSingleJourney: function() {
        if (!this.context || !this.context.parent) {
            return;
        }

        this.context.parent.unset('reloadSingleJourney');
    },

    /**
     * Add the filter in collection of archived on the base of selected value in widget
     * configuration layout
     */
    addFiltersInCollection: function() {
        let activeOrArchive = this.getActiveOrArchiveMode();

        if (_.isEqual(activeOrArchive, 'active')) {
            this.collection.filterDef = {
                archived: 0,
            };
        } else if (_.isEqual(activeOrArchive, 'archived')) {
            this.collection.filterDef = {
                archived: 1,
            };
        } else {
            this.collection.filterDef = {};
        }
    },

    /**
     * Returns the active or archive mode saved by user in widget setting
     *
     * @return {string}
     */
    getActiveOrArchiveMode: function() {
        let moduleName = (this.context && this.context.get('parentModule')) ? this.context.get('parentModule') : '';

        let mode = app.CJBaseHelper.getValueFromCache('toggleActiveArchived', 'cj_active_or_archive_filter',
            moduleName, 'dri-workflows-widget-configuration');

        return !_.isEmpty(mode) ? mode : 'active';
    },

    /**
     * It will cache the view level and field level dependencies of given modules
     *
     * @param {Array} modules
     */
    _setSugarLogicDependenciesForModel: function(modules) {
        _.each(modules, function(module) {
            // cache all dependencies of specific module
            if (_.isEmpty(this.moduleDependencies[module])) {
                let moduleMetadata = app.metadata.getModule(module) || {};
                let dependencies = moduleMetadata.dependencies || [];

                if (moduleMetadata.views && moduleMetadata.views.record) {
                    let recordMetadata = moduleMetadata.views.record.meta;

                    if (!_.isUndefined(recordMetadata.dependencies)) {
                        dependencies = dependencies.concat(recordMetadata.dependencies);
                    }
                }

                // Cache the results so we don't have to do this expensive lookup any more
                this.moduleDependencies[module] = dependencies;
            }

            if (_.isEmpty(this.fieldsToValidate[module])) {
                let fieldsToValidate = {};
                const allFields = App.metadata.getField({module: module});
                let model = app.data.createBean(module);

                // add fields having required dependency to fieldsToValidate
                _.each(this.moduleDependencies[module], function(dependency) {
                    _.each(dependency.actions, function(action) {
                        if (action.action == 'SetRequired' && action.params.target && action.params.value) {
                            fieldsToValidate[action.params.target] = action.params.value;
                        }
                    }, this);
                }, this);

                // add required fields to fieldsToValidate
                for (let fieldKey in allFields) {
                    let field = allFields[fieldKey];

                    if (app.acl.hasAccessToModel('edit', model, fieldKey) && field.required) {
                        if (field.source && field.id_name) {
                            fieldsToValidate[field.id_name] = field.required_formula;
                        } else {
                            fieldsToValidate[fieldKey] = field.required_formula;
                        }
                    }
                }

                this.fieldsToValidate[module] = fieldsToValidate;
            }
        }, this);
    },

    /**
     * Give the count of number of guides
     *
     * @param {Object} views
     */
    setGuidesCount: function(views) {
        _.each(views, function(view) {
            if (view && _.isEqual(view.name, 'dri-workflows-header') &&
                !_.isUndefined(this.collection) && this.collection.length < app.config.maxQueryResult) {
                return ++view.smartGuidesCount;
            }
        }, this);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    },
});

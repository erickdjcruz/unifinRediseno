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
 * @class View.Views.Base.ActivityTimelineBaseView
 * @alias SUGAR.App.view.views.BaseActivityTimelineBaseView
 * @extends View.View
 */
({

    className: 'activity-timeline-base',

    plugins: ['EmailClientLaunch', 'LinkedModel'],

    /**
     * Array default modules
     */
    defaultModules: [
        'Calls',
        'Emails',
        'Meetings',
        'Messages',
        'Notes',
        'Tasks',
    ],

    /**
     * String id of the expanded model
     */
    expandedModelId: '',

    /**
     * Rendered layout activities
     */
    renderedActivities: [],

    /**
     * Boolean status of whether all models been fetched
     */
    fetchCompleted: false,

    /**
     * Array models of related modules
     */
    models: [],

    /**
     * Fields to show as record date for different modules
     */
    recordDateFields: {},

    /**
     * String param for store selected module in filter
     */
    filter: {
        module: null,
    },

    /**
     * Search term
     */
    searchTerm: '',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (options.context) {
            this.baseModule = options.context.get('module');
            this.module = this.baseModule;
            this.baseRecord = this._getBaseModel(options);
        }

        options.meta = _.extend({}, options.meta,
            {preview: this._getModuleFieldMeta()}
        );

        this._super('initialize', [options]);

        if (this.baseModule) {
            this._setActivityModulesAndFields(this.baseModule);
        }

        this.searchTerm = '';
        this.renderedActivities = [];
        this.fetchCompleted = false;
        this.models = [];
        this.filter = {module: null};
        this.expandedModelId = '';

        this.events = _.extend({}, this.events, {
            'click .static-contents': 'handleExpandedClick',
            'click .btn.more': 'fetchModels',
            'click .add-on.sicon-close': 'clearSearch',
            'keyup [data-action="search"]': 'doSearch',
            'paste [data-action="search"]': 'doSearch'
        });

        app.events.on('focusdrawer:close', this.handleFocusDrawerClose, this);
        app.events.on('link:added link:removed', this.handleLinkChanges, this);
        app.events.on('timeline:link:added', this.handleTimelineLinkChanges, this);
    },

    /**
     * Reload data if any records have been updated in focus drawer
     * after the focus drawer is closed.
     * @param {Array} updatedModels
     */
    handleFocusDrawerClose: function(updatedModels) {
        if (!_.isEmpty(updatedModels)) {
            this.reloadData();
        }
    },

    /**
     * Reload data if any link changes in timeline
     * @param {string} parentModule
     * @param {string} parentId
     */
    handleTimelineLinkChanges: function(parentModule, parentId) {
        if (this.baseRecord.get('id') === parentId &&
            this.baseRecord.get('_module') === parentModule) {
            this.reloadData();
        }
    },

    /**
     * Reload data if any link changes
     * @param {Object} parentModel
     */
    handleLinkChanges: function(parentModel) {
        if (parentModel &&
            this.baseRecord.get('id') === parentModel.get('id') &&
            this.baseRecord.get('_module') === parentModel.get('_module')) {
            this.reloadData();
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        // listen for activity-card-emailactions
        this.listenTo(this.context, 'emailclient:close', function() {
            this.reloadData();
        }, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this._renderCards();
    },

    /**
     * Add new cards to timeline or re-render existing cards.
     */
    _renderCards: function() {
        if (this.fetchCompleted) {
            this.$('.dashlet-footer').hide();
        } else if (!_.isEmpty(this.models)) {
            this.$('.dashlet-footer').show();
        }
        this._hideSkeleton();
        if (!_.isEmpty(this.models)) {
            this.disposeActivities();
            this.$('.activity-timeline-cards').html('');
            this.appendCardsToView(this.models);
        } else if (!this.fetchCompleted) {
            this._showSkeleton();
        } else {
            const emptyTemplate = app.template.get('activity-timeline-base.empty-list');
            this.$('.activity-timeline-cards').html(emptyTemplate(this));
        }
    },

    /**
     * Check if a module is audited.
     * @param {string} module
     * @return {boolean}
     * @private
     */
    _isModuleAudited: function(module) {
        const moduleMeta = app.metadata.getModule(module);
        return moduleMeta && moduleMeta.isAudited;
    },

    /**
     * Return list of enabled modules.
     *
     * @param {string} module
     * @return {Array}
     */
    getEnabledModules: function(module) {
        let configedModules = null;
        if (app.config.timeline && app.config.timeline[module]) {
            configedModules = app.config.timeline[module].enabledModules;
        }
        let enabledModules = [];

        if (!_.isNull(configedModules)) {
            configedModules.map((link) => {
                const relatedModule = app.data.getRelatedModule(module, link);
                if (relatedModule && !_.contains(enabledModules, relatedModule)) {
                    enabledModules.push(relatedModule);
                }
            });
        } else {
            enabledModules = this.getDefaultModules(module);
        }
        if (this._isModuleAudited(module)) {
            enabledModules.push('Audit');
        }

        return enabledModules;
    },

    /**
     * Return available modules from subpanel metadata.
     * @param {string} module
     * @return {Array}
     */
    getDefaultModules: function(module) {
        let enabledModules = [];

        const meta = app.metadata.getModule(module);
        const subpanels = meta && meta.layouts && meta.layouts.subpanels &&
            meta.layouts.subpanels.meta && meta.layouts.subpanels.meta.components || [];
        const hiddenSubpanels = app.metadata.getHiddenSubpanels();

        subpanels.map((subpanel) => {
            const link = subpanel.context && subpanel.context.link || '';
            if (link) {
                const relatedModule = app.data.getRelatedModule(module, link);

                if (!_.contains(hiddenSubpanels, relatedModule.toLowerCase()) &&
                    !_.contains(enabledModules, relatedModule) &&
                    _.contains(this.defaultModules, relatedModule)) {
                    enabledModules.push(relatedModule);
                }
            }
        });

        return enabledModules;
    },

    /**
     * Get base model from parent context
     *
     * @param {Object} options
     * @return {Data.Bean} model the base model of the dashlet
     */
    _getBaseModel: function(options) {
        var model;
        var baseModule = options.context.get('module');
        var currContext = options.context;
        while (currContext) {
            var contextModel = currContext.get('rowModel') || currContext.get('model');

            if (contextModel && contextModel.get('_module') === baseModule) {
                model = contextModel;

                var parentHasRowModel = currContext.parent && currContext.parent.has('rowModel');
                if (!parentHasRowModel) {
                    break;
                }
            }

            currContext = currContext.parent;
        }
        return model;
    },

    /**
     * Get the activity-timeline metadata for the baseModule
     *
     * @param {string} baseModule module name
     */
    getModulesMeta: function(baseModule) {
        return app.metadata.getView(baseModule, 'activity-timeline-base');
    },

    /**
     * Get the activity-timeline metadata for the baseModule
     *
     * @param {string} baseModule module name
     */
    getModulesCardMeta: function(baseModule) {
        return app.metadata.getView(baseModule, 'activity-card-definition');
    },

    /**
     * Set activity modules and module field names
     *
     * @param {string} baseModule module name
     */
    _setActivityModulesAndFields: function(baseModule) {
        const enabledModules = this.getEnabledModules(baseModule);
        if (this.context) {
            this.context.set('enabledModules', enabledModules);
        }

        if (this.filter.module && this.filter.module !== 'all_modules' && enabledModules.includes(this.filter.module)) {
            this.activityModules = [this.filter.module];
        } else {
            this.activityModules = enabledModules;
        }

        if (this.activityModules.length === 0) {
            this.activityModules = this.defaultModules;
        }

        this.moduleFieldNames = {};
        this.recordDateFields = {};
        _.each(this.activityModules, function(module) {
            const meta = this.getModulesCardMeta(module);
            if (!_.isEmpty(meta)) {
                this.moduleFieldNames[module] = meta.fields;
                this.recordDateFields[module] = meta.record_date || 'date_entered';
            }
        }, this);
    },

    /**
     * Get preview field metadata or record field metadata to render content
     *
     * @return {Object} fieldMeta view metadata of available fields for related modules
     */
    _getModuleFieldMeta: function() {
        var fieldMeta = {};
        _.each(this.activityModules, function(module) {
            var fieldMap = {};
            _.each(this.moduleFieldNames[module], function(field) {
                fieldMap[field] = true;
            });

            var meta = app.metadata.getView(module, 'preview') ||
                app.metadata.getView(module, 'record') || {};
            var fields = [];
            _.each(meta.panels, function(panel) {
                _.each(panel.fields, function(field) {
                    if (fieldMap[field.name]) {
                        fields.push(field);
                    }
                });
            });

            meta.panels = [{fields: fields}];
            fieldMeta[module] = meta;
        }, this);
        return fieldMeta;
    },

    /**
     * Initialize collection of related records to base record
     * @private
     */
    _initCollection: function() {
        if (!(this.baseModule && this.baseRecord && this.activityModules)) {
            return;
        }
        var self = this;
        var RelatedActivityCollection = app.MixedBeanCollection.extend({
            activityModules: this.activityModules,
            buildURL: _.bind(function() {
                return app.api.serverUrl + '/' + this.baseModule + '/' +
                    this.baseRecord.get('id') + '/related_activities';
            }, this),
            sync: function(method, model, options) {
                options = self._getRequestOptions(method, model, options);

                var url = this.buildURL(options.params);
                var callbacks = app.data.getSyncCallbacks(method, model, options);
                app.api.call('create', url, options.attributes, callbacks);
            }
        });
        this.relatedCollection = new RelatedActivityCollection();
    },

    /**
     * Format params for Activities Timeline request
     *
     * @param method
     * @param model
     * @param options
     * @return {Object}
     * @private
     */
    _getRequestOptions: function(method, model, options) {
        options = app.data.parseOptionsForSync(method, model, options);
        options.attributes = _.extend(
            options.params,
            this.getRequestData()
        );

        if (options.params.fields) {
            delete options.params.fields;
        }

        return options;
    },

    /**
     * Return list of options for collection fetching
     *
     * @return {Object}
     */
    getRequestData: function() {
        let options = {};

        if (this.activityModules.indexOf('Audit') !== -1 && !_.isEmpty(this.moduleFieldNames.Audit)) {
            options.field_list = {
                'Audit': this.moduleFieldNames.Audit.join(','),
            };
        }

        this.activityModules.map((module) => {
            options.field_list = options.field_list || {};

            if (_.isArray(this.moduleFieldNames[module])) {
                options.field_list[module] = this.moduleFieldNames[module].join(',');
            }
        });

        options.module_list = this.activityModules.join(',');

        if (this.filter.module === 'all_modules') {
            options.add_create_record = 1;
        }

        if (options.module_list === 'Audit') {
            options.ignore_field_presence = ['assigned_user_id'];
        }

        options.alias_fields = {
            'record_date': this.recordDateFields
        };

        if (this.searchTerm) {
            let filtersBeanPrototype = app.data.getBeanClass('Filters').prototype;
            let moduleFilters = {};
            _.each(this.activityModules, (module) => {
                moduleFilters[module] = filtersBeanPrototype.buildSearchTermFilter(
                    module, this.searchTerm);
            });
            options.module_filters = moduleFilters;
        }

        options.order_by = 'record_date:desc';

        return options;
    },

    /**
     * @inheritdoc
     */
    loadData: function() {
        if (this._mode === 'config' || !this.filter.module) {
            return;
        }

        if (!this.relatedCollection) {
            this._initCollection();
        }

        this.fetchModels();
    },

    /**
     * Fetch models from related collection if not all models had been fetched
     */
    fetchModels: function() {
        if (!this.fetchCompleted && !_.isUndefined(this.relatedCollection)) {
            this.relatedCollection.fetch({
                offset: this.relatedCollection.next_offset,
                success: _.bind(function(coll) {
                    if (this.disposed) {
                        return;
                    }
                    _.each(coll.models, function(model) {
                        model.set('record_date', model.get(this.recordDateFields[model.get('_module')]));
                    }, this);
                    this.models = this.models.concat(coll.models);
                    this.fetchCompleted = coll.next_offset === -1;
                    this._renderCards();
                }, this)
            });
        }
    },

    /**
     * Gets the search term from the search input.
     * @return {string}
     * @private
     */
    _getSearchTerm: function() {
        const $input = this.$('input[data-action=search]');

        if ($input.val()) {
            return $input.val().trim();
        }

        return '';
    },

    /**
     * Sets search term to empty string and resets models.
     */
    clearSearch: function() {
        this.$('input[data-action=search]').val('');
        this.$('.sicon-close.add-on').addClass('hidden');
        this.searchTerm = '';
        this.doSearch();
    },

    /**
     * Starts a new search.
     */
    doSearch: _.debounce(function() {
        this.searchTerm = this._getSearchTerm();
        this._initCollection();
        this.reloadData();

        const el = this.$('.sicon-close.add-on');
        el.removeClass('hidden');
        if (!this.searchTerm) {
            el.addClass('hidden');
        }
    }, 400),

    /**
     * Set icon class attributess on related collection models base on module type
     */
    _setIconClass: function() {
        if (this.models) {
            _.each(this.models, function(model) {
                // it's a change card if the model's module is Audit, use this.module
                var mod = model.get('_module') == 'Audit' ? this.module : model.get('_module');
                const moduleMeta = app.metadata.getModule(mod);
                model.set('icon_module', mod);
                model.set('icon_class', moduleMeta.icon || 'sicon-default-module-lg');
            }, this);
        }
    },

    /**
     * Set field metadata on related collection models
     *
     * @private
     * @param {Data.Bean} model the model to be patched with fields meta
     */
    _patchFieldsToModel: function(model) {
        var fieldsMeta = this.meta.preview;
        model.set('fieldsMeta', fieldsMeta[model.get('_module')]);
    },

    /**
     * Handle expand/collapse action when expand/collapse icon is clicked
     *
     * @param {Event} event Click event that triggers the action
     */
    handleExpandedClick: function(event) {
        var $element = this.$(event.currentTarget);
        var $parent = $element.closest('.timeline-entry');
        var modelId = $parent.data('id');
        // to toggle chevron up and down
        var $el = $element.find('.expand-collapse');

        // if model is already expanded, reset id and collapse panel
        if (modelId === this.expandedModelId) {
            this.expandedModelId = '';
            $el.removeClass('sicon-chevron-up').addClass('sicon-chevron-down');
            $parent.children('.expanded-contents').addClass('hide');
            $parent.children('.static-contents').removeClass('expanded');
            return;
        }

        // collapse existing expanded model
        if (this.expandedModelId) {
            var $expanded = this.$('.timeline-entry[data-id="' + this.expandedModelId + '"]');
            $expanded.children('.expanded-contents').addClass('hide');
            $expanded.children('.static-contents').removeClass('expanded');
            $expanded.find('.expand-collapse').removeClass('sicon-chevron-up').addClass('sicon-chevron-down');
        }

        var model = _.find(this.models, function(model) {
            return model.get('id') === modelId;
        });
        this.expandedModelId = modelId;

        // if model data fetched, expand; if not fetch then expand
        if (model.get('fullBeanFetched')) {
            $parent.children('.expanded-contents').removeClass('hide');
            $parent.children('.static-contents').addClass('expanded');
            $el.removeClass('sicon-chevron-down').addClass('sicon-chevron-up');
        } else {
            model.fetch({
                view: app.metadata.getView(model.get('_module'), 'preview') ? 'preview' : 'record',
                success: _.bind(function(m) {
                    model.set('fullBeanFetched', true);
                    this._patchFieldsToModel(model);
                    this._render();

                    var $modelEl = this.$('.timeline-entry[data-id="' + modelId + '"]');
                    $modelEl.children('.expanded-contents').removeClass('hide');
                    $modelEl.children('.static-contents').addClass('expanded');
                    $modelEl.find('.expand-collapse').removeClass('sicon-chevron-down').addClass('sicon-chevron-up');
                }, this)
            });
        }
    },

    /**
     * Reload data.
     */
    reloadData: function(event) {
        if (this.isRefreshClicked(event) && !this.fetchCompleted) {
            return;
        }

        if (this.relatedCollection) {
            this.relatedCollection.reset([], {silent: true});
            this.relatedCollection.resetPagination();
        }
        this.fetchCompleted = false;
        this.models = [];
        this.disposeActivities();
        this.$('.activity-timeline-cards').html('');
        this.$('.dashlet-footer').hide();
        this._showSkeleton();
        this.loadData();
    },

    /**
     * Check if the event related to click on refresh button
     *
     * @param event
     * @return {boolean}
     */
    isRefreshClicked: function(event) {
        if (!event) {
            return false;
        }

        const parentEl = $(event.target).parent();
        const attr = parentEl.attr('data-dashletaction');
        return (attr === 'reloadData');
    },

    /**
     * Shows Skeleton Loader
     * @private
     */
    _showSkeleton: function() {
        $('.activity-timeline').addClass('hidden-overflow');
        this.$('.activity-timeline-cards').addClass('timeline-skeleton');
    },

    /**
     * Hides Skeleton Loader
     * @private
     */
    _hideSkeleton: function() {
        $('.activity-timeline').removeClass('hidden-overflow');
        this.$('.activity-timeline-cards').removeClass('timeline-skeleton');
    },

    /**
     * Create new record.
     *
     * @param {Event} event Click event.
     * @param {Object} params
     * @param {string} params.module Module name.
     * @param {string} params.link Relationship link.
     */
    createRecord: function(event, params) {
        var self = this;
        var model = this.createLinkModel(this.baseRecord, params.link);

        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                module: params.module,
                model: model
            }
        }, function(context, model) {
            if (!model) {
                return;
            }
            let baseModule = self.baseRecord.get('_module');
            let baseId = self.baseRecord.get('id');
            app.events.trigger('timeline:link:added', baseModule, baseId, model);
        });
    },

    /**
     * Compose an email related to the relevant record.
     *
     * @param {Event} event Event.
     * @param {Object} params Parameters.
     * @param {string} params.module Module name.
     * @param {string} params.link Relationship link.
     */
    composeEmail: function(event, params) {
        if (this.useSugarEmailClient()) {
            this.once('emailclient:close', function() {
                this.reloadData();
            }, this);

            this.launchEmailClient(event);
        } else {
            var options = this._retrieveEmailOptions($(event.currentTarget));
            window.open(this._buildMailToURL(options), '_blank');
        }
    },

    /**
     * Used by EmailClientLaunch as a hook point to retrieve email options that
     * are specific to a view/field.
     *
     * @return {Object} Email options.
     * @private
     */
    _retrieveEmailOptionsFromLink: function() {
        var parentModel = this.baseRecord;
        var emailOptions = {};

        if (parentModel && parentModel.id) {
            // set parent model as option to be passed to compose for To address & relate
            // if parentModel does not have email, it will be ignored as a To recipient
            // if parentModel's module is not an available module to relate, it will also be ignored
            emailOptions = {
                to: [{bean: parentModel}],
                related: parentModel
            };
        }

        return emailOptions;
    },

    /**
     * Create and render card layout
     *
     * @param model
     * @return {Object}
     */
    createCard: function(model) {
        var module = model.get('_module') || model.module || '';
        model.link = {};
        if (module) {
            let linkName = this.getModuleLink(module);
            if (linkName) {
                model.link = {
                    name: linkName,
                    bean: this.baseRecord,
                    type: 'card-link',
                };
            }
        }

        if (module === 'Audit') {
            model.set({
                parent_model: this.model
            });
        }

        var layout = app.view.createLayout({
            type: model.get('event_action') === 'create' ?
                'activity-card-create' :
                'activity-card',
            context: this.context,
            module: module,
            model: model,
            layout: this.layout,
            timelineType: this.name || 'activity-timeline-base'
        });

        layout.initComponents();

        this.renderedActivities.push(layout);

        // cz Seedbed doesn't like it any other way
        layout.render();
        return layout;
    },

    /**
     * Appends cards
     * @param models array of models to be added to the view
     */
    appendCardsToView: function(models) {
        this._setIconClass();

        _.each(models, _.bind(function(model) {
            this._patchFieldsToModel(model);
            var layout = this.createCard(model);

            if (layout) {
                this.$('.activity-timeline-cards').append(layout.el);
            }

            // check menu icon visibilities
            layout.setCardMenuVisibilities();
        }, this));
    },

    /**
     * Get card module link.
     *
     * @param {string} moduleName The name of card module.
     * @return {string} The card module link.
     */
    getModuleLink: function(moduleName) {
        if (!moduleName) {
            return '';
        }

        const cardMeta = this.getModulesCardMeta(moduleName);
        if (cardMeta && cardMeta.link) {
            return cardMeta.link;
        }

        let link = '';
        if (this.baseModule && app.config.timeline && app.config.timeline[this.baseModule] &&
            app.config.timeline[this.baseModule].enabledModules) {

            link = _.find(app.config.timeline[this.baseModule].enabledModules, function(link) {
                const relatedModule = app.data.getRelatedModule(this.baseModule, link);
                if (relatedModule === moduleName) {
                    return link;
                }
            }, this);
        }

        return link;
    },

    /**
     * Disposes activities
     */
    disposeActivities: function() {
        _.each(this.renderedActivities, function(component) {
            component.dispose();
        });
        this.renderedActivities = [];
    },
    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.disposeActivities();
        this._super('_dispose');
        app.events.off('focusdrawer:close', this.handleFocusDrawerClose, this);
        app.events.off('link:added link:removed', this.handleLinkChanges, this);
        app.events.off('timeline:link:added timeline:link:removed', this.handleTimelineLinkChanges, this);
    }
})

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
 * @class View.Views.Base.DocusignView
 * @alias SUGAR.App.view.views.BaseDocusignView
 * @extends View.View
 */
({
    plugins: ['Dashlet'],
    showNewEnvelopeDocs: false,

    envelopeListLayout: null,
    draftListLayout: null,
    docsListLayout: null,

    className: 'docusign',

    events: {
        'click .nav-tabs li[data-tabname]': 'switchContent',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.listenTo(app.events, 'docusign:reload', this.initDashlet, this);

        this.listenTo(this, 'chart:clicked', this.chartClickHandler, this);
        this.listenTo(this.context, 'sendDocumentsToDocuSign', this.sendToDocuSign, this);
        this.listenTo(this.context, 'selectTemplate', this.sendToDocuSignUsingTemplates, this);
        this.listenTo(this.context, 'sendWithTemplate', this.sendToDocuSignUsingTemplates, this);
    },

    /**
     * @inheritdoc
     */
    initDashlet: function(options) {
        if (this.meta.config) {
            return;
        }

        if (_.isUndefined(options) || _.isUndefined(options.params)) {
            options = {
                params: {}
            };
        }

        app.api.call('read', app.api.buildURL('DocuSign/checkEAPM'), {}, {
            success: _.bind(function(connected) {
                this.userIsConfigured = connected;

                if (this.userIsConfigured) {
                    if (app.controller.context.get('module') !== 'Home' &&
                        app.controller.context.get('layout') === 'record') {
                        this.showNewEnvelopeDocs = true;
                    }
                    this.showDrafts = true;
                } else {
                    this.showNewEnvelopeDocs = false;
                    this.showDrafts = false;
                }

                this.setupComponents();
            }, this),
            error: function(error) {
                app.alert.show('error-checking-eapm', {
                    level: 'error',
                    messages: error
                });
            },
        });
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        var userHasBeenChecked = _.isBoolean(this.userIsConfigured);
        if (userHasBeenChecked) {
            this.disposeDashletTabContents();

            this.setupComponents();
        }
        this._super('loadData', [options]);
    },

    /**
     * Setup chart and lists
     *
     * @param {Object} options
     */
    setupComponents: function(options) {
        if (this.disposed || this.meta.config) {
            return;
        }

        if (app.acl.hasAccess('read', this.module)) {
            this.loadOverview();
            this.createEnvelopesListLayout();
            if (this.showDrafts) {
                this.createDraftsListLayout();
            }
            if (this.showNewEnvelopeDocs) {
                this.createNewEnvelopeListView();
            }

            this.setupEnvelopesListFilters();
            this.envelopeCollection.fetch({
                success: _.bind(this.renderEnvelopeList, this)
            });

            if (this.showDrafts) {
                this.setupDraftsListFilters();
                this.draftCollection.fetch({
                    success: _.bind(this.renderDraftList, this)
                });
            }
            this.loaded = true;

            this.render();
        }
    },

    /**
     * Load envelopes
     *
     * @param {Object} options Collection options
     */
    setupEnvelopesListFilters: function(options) {
        if (app.controller.context) {
            var contextModule = app.controller.context.get('module');
            this.envelopeCollection.options = options || {
                params: {}
            };

            var layout = app.controller.context.get('layout');

            if (contextModule === 'Home') {
                this.envelopeCollection.options.params.layout = 'records';
            } else {
                if (layout === 'record') {
                    var contextBean = app.controller.context.get('model');
                    this.envelopeCollection.options.params.layout = 'record';
                    this.envelopeCollection.options.params.record = contextBean.get('id');
                } else {
                    this.envelopeCollection.options.params.layout = 'records';
                }
            }
            this.envelopeCollection.options.params.recordModule = contextModule;
            this.envelopeCollection.options = this.envelopeCollection.options || {};
            this.envelopeCollection.options.params.offset = 0;

            this.envelopeCollection._initOptions = _.extend({}, this.envelopeCollection.options);

            if (this.envelopeCollection.setOption) {
                options = _.extend(this.envelopeCollection.options);
                this.envelopeCollection.setOption('params', options.params);
            }
        }
    },

    /**
     * Create envelopes list layout
     */
    createEnvelopesListLayout: function() {
        var model = app.data.createBean('DocuSignEnvelopes');
        this.envelopeCollection = app.data.createBeanCollection('DocuSignEnvelopes', [], {});

        var enabledFieldNames = [
            'date_entered',
            'created_by_name',
            'name',
            'status',
            'envelope_id'
        ];
        this.envelopeCollection.setOption('fields', enabledFieldNames);

        var context = new app.Context({
            module: 'DocuSignEnvelopes',
            model: model,
            collection: this.envelopeCollection,
            fields: enabledFieldNames
        });

        this.envelopeListLayout = app.view.createLayout({
            context: context,
            type: 'docusign-envelopes',
            name: 'docusign-envelopes',
            module: 'DocuSignEnvelopes',
            collection: this.envelopeCollection
        });

        this.envelopeListLayout.initComponents();
    },

    /**
     * Setup drafts
     *
     * @param {Object} options
     */
    setupDraftsListFilters: function(options) {
        if (app.controller.context) {
            var contextModule = app.controller.context.get('module');
            this.draftCollection.options = options || {
                params: {
                    status: 'created'
                }
            };

            var layout = app.controller.context.get('layout');

            if (contextModule === 'Home') {
                this.draftCollection.options.params.layout = 'records';
            } else {
                if (layout === 'record') {
                    var contextBean = app.controller.context.get('model');
                    this.draftCollection.options.params.layout = 'record';
                    this.draftCollection.options.params.record = contextBean.get('id');
                } else {
                    this.draftCollection.options.params.layout = 'records';
                }
            }
            this.draftCollection.options.params.recordModule = contextModule;
            this.draftCollection.options = this.draftCollection.options || {};
            this.draftCollection.options.params.offset = 0;

            this.draftCollection._initOptions = _.extend({}, this.draftCollection.options);

            options = _.extend(this.draftCollection.options);
            this.draftCollection.setOption('params', options.params);
        }
    },

    /**
     * Create drafts list
     */
    createDraftsListLayout: function() {
        var model = app.data.createBean('DocuSignEnvelopes');
        this.draftCollection = app.data.createBeanCollection('DocuSignEnvelopes', [], {});

        var enabledFieldNames = [
            'date_entered',
            'created_by_name',
            'name',
            'status',
            'envelope_id'
        ];
        this.draftCollection.setOption('fields', enabledFieldNames);

        var context = new app.Context({
            module: 'DocuSignEnvelopes',
            model: model,
            collection: this.draftCollection,
            fields: enabledFieldNames
        });

        this.draftListLayout = app.view.createLayout({
            context: context,
            type: 'docusign-drafts',
            name: 'docusign-drafts',
            module: 'DocuSignEnvelopes',
            collection: this.draftCollection
        });

        this.draftListLayout.initComponents();

        //add handler to open drawer for draft seding
        this.listenTo(context, 'list:draft:open', _.bind(this.showDraft, this));
    },

    /**
     * Load overview tab
     */
    loadOverview: function() {
        var data = {
            recordModule: app.controller.context.get('module')
        };

        if (app.controller.context.get('module') !== 'Home') {
            if (app.controller.context.get('layout') === 'record') {
                var contextBean = app.controller.context.get('model');
                data.recordId = contextBean.get('id');
            }
        }

        app.api.call('create', app.api.buildURL('DocuSign/stats'), data, {
            success: _.bind(this.renderOverview, this),
            error: function(error) {
                app.alert.show('error-getting-counts', {
                    level: 'error',
                    messages: error.message
                });
            }
        });
    },

    /**
     * Render overview chart
     *
     * @param {Object} statuses
     */
    renderOverview: function(statuses) {
        if (_.isUndefined(statuses)) {
            return;
        }

        if (statuses.all === 0) {
            if (!this.meta.config && this.chartField) {
                this.chartField.$el.find('.chart-main').removeClass('h-full');
                this.chartField.displayNoData(true);
            }
            return;
        }

        let colorPalette = [
            '#517bf8', // @ocean
            '#36b0ff', // @pacific
            '#00e0e0', // @teal
            '#00ba83', // @green
            '#6cdf46', // @army
            '#ffd132', // @yellow
            '#ff9445' // @orange
        ];
        let colorPaletteNotNull = [];

        let rawChartData = {
            values: []
        };

        let availableStatuses = ['created', 'sent', 'delivered', 'completed', 'declined', 'voided', 'signed'];

        _.each(availableStatuses, function(status, idx) {
            if (statuses[status] !== 0) {
                rawChartData.values.push(
                    {
                        label: [
                            app.lang.get('LBL_ENVELOPE_STATUS_' + status.toUpperCase(), 'DocuSignEnvelopes')
                        ],
                        values: [
                            statuses[status]
                        ],
                        valuelabels: [
                            (statuses[status]).toString()
                        ],
                        key: status
                    }
                );
                colorPaletteNotNull.push(colorPalette[idx]);
            }
        });

        var params = this.chartModel.get('rawChartParams');
        params.colorOverrideList = colorPaletteNotNull;

        this.chartModel.set('rawChartParams', params);

        this.chartModel.set('rawChartData', rawChartData);
    },

    /**
     * @inheritdoc
     * When rendering fields, get a reference to the chart field if we don't have one yet
     */
    _renderField: function(field) {
        this._super('_renderField', [field]);

        // hang on to a reference to the chart field
        if ((_.isUndefined(this.chartField) || this.chartField.disposed) && field.name === 'chart') {
            this.chartField = field;
        }
    },

    /**
     * Filter envelope list
     *
     * @param {string} status
     */
    filterEnvelopeList: function(status) {
        if (status === 'created' && this.showDrafts) {
            this.$('[data-tabname=\'drafts\'] a').click();
        } else {
            var options = {
                params: {
                    status: status
                }
            };

            this.setupEnvelopesListFilters(options);
            this.envelopeCollection.fetch({
                success: _.bind(this.renderEnvelopeList, this)
            });

            this.$('[data-tabname=\'envelopes\'] a').click();
        }
    },

    /**
     * Create new envelope list view
     */
    createNewEnvelopeListView: function() {
        var contextCollection = app.controller.context.get('documentCollection');
        if (contextCollection instanceof app.data.beanCollection) {
            this.documentCollection = contextCollection;
        } else {
            this.documentCollection = app.data.createBeanCollection('Documents');
            app.controller.context.set('documentCollection', this.documentCollection);
        }

        var model = app.data.createBean('Documents');
        var context = this.context.getChildContext({
            forceNew: true
        });
        context.set({
            module: 'Documents',
            model: model,
            collection: this.documentCollection,
            layout: 'records'
        });

        this.docsListLayout = app.view.createLayout({
            context: context,
            type: 'docusign-documents',
            name: 'docusign-documents',
            module: 'Documents',
            collection: this.documentCollection
        });

        this.listenTo(this.documentCollection, 'change add remove', _.bind(function changeAddRemoveCollectionHandler() {
            if (this.disposed) {
                return;
            }
            this.docsListLayout.render();
            if (this.$el) {
                this.$('[data-tabname="documents"] a').click();
            }
        }, this));

        this.listenTo(this.documentCollection, 'reset', _.bind(function resetCollectionHandler() {
            if (this.disposed) {
                return;
            }

            this.docsListLayout.render();
        }, this));

        this.docsListLayout.initComponents();
    },

    /**
     * Render new envelope docs list
     */
    renderNewEnvelopeDocsList: function() {
        if (this.$el === null) {
            return;
        }

        this.$('.documents_docusign_content').html(this.docsListLayout.$el);
        this.docsListLayout.render();
    },

    /**
     * Render envelope list
     *
     * @param {Object} res
     */
    renderEnvelopeList: function(res) {
        if (this.$el === null || !(this.envelopeListLayout instanceof app.view.Layout)) {
            return;
        }

        this.$('.envelopes_docusign_content').html(this.envelopeListLayout.$el);
        this.envelopeListLayout.render();

        // we need to manually do this here because just now the component is on DOM
        this.envelopeListLayout.getComponent('docusign-envelopes-list').delegateEvents();
        this.envelopeListLayout.getComponent('list-bottom').delegateEvents();
    },

    /**
     * Render drafts list
     *
     * @param {Object} res
     */
    renderDraftList: function(res) {
        if (this.$el === null) {
            return;
        }

        this.$('.drafts_docusign_content').html(this.draftListLayout.$el);
        this.draftListLayout.render();

        // we need to manually do this here because just now the component is on DOM
        this.draftListLayout.getComponent('docusign-drafts-list').delegateEvents();
        this.draftListLayout.getComponent('list-bottom').delegateEvents();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (this.disposed) {
            return;
        }

        if (this.meta.config || this.loaded !== true) {
            this._super('_render');
            return;
        }

        // chartField is recreated on each render call so this model must always be defined before render
        this.chartModel = new Backbone.Model({
            rawChartParams: {
                show_title: false,
                chart_type: 'pie chart',
                allowScroll: false
            }
        });

        this._super('_render');


        this.renderEnvelopeList();

        if (this.showDrafts) {
            this.renderDraftList();
        }

        if (this.showNewEnvelopeDocs) {
            this.renderNewEnvelopeDocsList();
        }
    },

    /**
     * Initiate the send process, by opening the tab
     */
    sendToDocuSign: function() {
        if (!this.userIsConfigured) {
            app.alert.show('warn-docusign-user-not-logged-in', {
                level: 'warning',
                messages: app.lang.get('LBL_PLEASE_LOG_IN', 'DocuSignEnvelopes'),
                autoClose: true,
                autoCloseDelay: '10000'
            });

            return;
        }

        var controllerCtx = app.controller.context;
        var controllerModel = controllerCtx.get('model');
        var module = controllerModel.get('_module');
        var modelId = controllerModel.get('id');
        var documents = _.pluck(this.documentCollection.models, 'id');

        var data = {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                token: app.api.getOAuthToken()
            },
            documents: documents
        };

        app.events.trigger('docusign:send:initiate', data);
    },

    /**
     * Send to DocuSign using templates
     *
     * @param {string} event
     */
    sendToDocuSignUsingTemplates: function(event) {
        if (!this.userIsConfigured) {
            app.alert.show('warn-docusign-user-not-logged-in', {
                level: 'warning',
                messages: app.lang.get('LBL_PLEASE_LOG_IN', 'DocuSignEnvelopes'),
                autoClose: true,
                autoCloseDelay: '10000'
            });

            return;
        }

        const controllerCtx = app.controller.context;
        const controllerModel = controllerCtx.get('model');
        const module = controllerModel.get('_module');
        const modelId = controllerModel.get('id');
        const documents = _.pluck(this.documentCollection.models, 'id');

        const data = {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                token: app.api.getOAuthToken()
            },
            documents: documents
        };

        const step = 'selectTemplate';

        if (event === 'selectTemplate') {
            app.events.trigger('docusign:send:initiate', data, step);
        } else if (event === 'sendWithTemplate') {
            app.events.trigger('docusign:compositeSend:initiate', data, step);
        }
    },

    /**
     * Click handler to filter envelopes
     *
     * @param {Object} event
     * @param {Array} activeElements
     * @param {Object} chart
     */
    chartClickHandler: function(event, activeElements, chart) {
        let element = chart.getElementsAtEventForMode(event, 'nearest', {intersect: true}, false);
        if (_.isEmpty(element)) {
            return;
        }
        let groupIndex = element[0].index;
        let status = this.chartModel.attributes.rawChartData.values[groupIndex].key;

        this.filterEnvelopeList(status);
    },

    /**
     * Switch tab content
     *
     * @param {Event} e
     */
    switchContent: function(e) {
        var liElement = $(e.target).parent();
        var oldLiElement = liElement.parent().find('.active');
        liElement
            .parent()
            .find('li')
            .removeClass('active');
        liElement.addClass('active');

        var oldTab = oldLiElement.data('tabname');
        var newTab = liElement.data('tabname');
        if (oldTab !== newTab) {
            var oldContetSelector = '.' + oldTab + '_docusign_content';
            this.$(oldContetSelector).hide();

            var newContetSelector = '.' + newTab + '_docusign_content';
            this.$(newContetSelector).show();
        }
        if (newTab === 'overview') {
            this.$('.main').show();
        } else {
            // .main is the parent of overview and it must be hidden too for resize to work correctly
            this.$('.main').hide();
        }
    },

    /**
     * Show draft
     *
     * A draft is an envelope already created and saved.
     * Now we just open the tab for that envelope in order to send it.
     *
     * @param  {Object} model
     */
    showDraft: function(model) {
        if (this.userIsConfigured !== true) {
            app.alert.show('warn-docusign-user-not-logged', {
                level: 'warning',
                messages: app.lang.get('LBL_PLEASE_LOG_IN', 'DocuSignEnvelopes'),
                autoClose: true,
                autoCloseDelay: '10000'
            });
            return;
        }

        if (
            model.get('created_by_link').id !== app.user.id
        ) {
            app.alert.show('warn-docusign-create-user', {
                level: 'warning',
                messages: app.lang.get('LBL_SEND_NOT_ALLOWED', 'DocuSignEnvelopes'),
                autoClose: true,
                autoCloseDelay: '10000'
            });
            return;
        }

        var module = this._getEnvelopeSourceModule();
        var modelId = this._getEnvelopeSourceModelId();

        var documents = [];

        var draftId = model.get('envelope_id');

        var data = {
            returnUrlParams: {
                parentRecord: module,
                parentId: modelId,
                draftEnvelopeId: draftId,
                token: app.api.getOAuthToken()
            },
            draftEnvelopeId: draftId,
            documents: documents
        };

        const step = 'openDraft';

        app.events.trigger('docusign:send:initiate', data, step);
    },

    /**
     * Get envelope source module
     *
     * @return {string}
     */
    _getEnvelopeSourceModule: function() {
        var module = app.controller.context.get('module');

        if (module === 'pmse_Inbox' && app.controller.layout.name === 'show-case') {
            try {
                var sourceModel = app.controller.layout._components[0]
                    .getComponent('sidebar')
                    .getComponent('main-pane')
                    .model;

                return sourceModel.get('_module');
            } catch (showCaseError) {
                app.logger.debug('_getEnvelopeSourceModule. show-case layout error:' + showCaseError);
            }
        }

        return module;
    },

    /**
     * Get envelope source model id
     *
     * @return {string}
     */
    _getEnvelopeSourceModelId: function() {
        var module = app.controller.context.get('module');
        var modelId = app.controller.context.get('modelId');

        if (module === 'pmse_Inbox' && app.controller.layout.name === 'show-case') {
            try {
                var sourceModel = app.controller.layout._components[0]
                    .getComponent('sidebar')
                    .getComponent('main-pane')
                    .model;

                return sourceModel.get('id');
            } catch (showCaseError) {
                app.logger.debug('_getEnvelopeSourceModelId. show-case layout error:' + showCaseError);
            }
        }

        return modelId;
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        $(window).off('resize.' + this.cid);
        $(window).off('storage.docusignAction');

        app.alert.dismiss('load-tab-for-sending');
        app.alert.dismiss('envelope-loading');

        this.disposeDashletTabContents();

        this._super('dispose');
    },

    disposeDashletTabContents: function() {
        if (this.envelopeListLayout instanceof app.view.Layout) {
            this.envelopeListLayout.dispose();
        }
        if (this.draftListLayout instanceof app.view.Layout) {
            this.draftListLayout.dispose();
        }
        if (this.docsListLayout instanceof app.view.Layout) {
            this.docsListLayout.dispose();
        }
        if (this.chartField instanceof app.view.Field) {
            this.chartField.dispose();
        }
    }
});

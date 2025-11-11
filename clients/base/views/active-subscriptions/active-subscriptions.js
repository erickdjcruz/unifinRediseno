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
 * @class View.Views.Base.ActiveSubscriptionsView
 * @alias SUGAR.App.view.views.BaseActiveSubscriptionsView
 * @extends View.View
 */
({

    plugins: ['Dashlet'],

    /**
     * The module name to show active subscriptions for.
     *
     * @property {string}
     */
    baseModule: null,

    overallSubscriptionStartDate: 0,

    overallSubscriptionEndDate: 0,

    overallDaysDifference: 0,

    endDate: '',

    expiryComingSoon: false,

    /**
     * Object representing the initial state of our dropdown.
     *
     * @property {Object}
     */
    _defaultSettings: {
        linked_subscriptions_account_field: null,
    },

    /**
     * Flag indicating Purchases module is enabled.
     *
     * @property {bool}
     */
    purchasesModule: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins, [
            'FocusDrawer'
        ]);

        this._super('initialize', [options]);

        this.currentModule = this._currentModule();
        this.module = 'Purchases';
        this.moduleName = {'module_name': app.lang.getModuleName(this.module, {'plural': true})};
        this.baseModule = 'Accounts';

        if (!_.isUndefined(app.metadata.getModule('Purchases'))) {
            this.purchasesModule = true;
        }

        this.linkToDocumentation = this._buildDocumentationUrl();
    },

    /**
     * Set up collection when init dashlet.
     *
     * @param {string} viewName Current view
     */
    initDashlet: function(viewName) {
        this._initSettings();

        this._mode = viewName;

        // Builds our dynamic dropdown list, but also populates the Account field in case it is not already set,
        // for example on upgrade.
        this._buildFieldsList();

        this._initCollection();
    },

    _buildDocumentationUrl: function() {
        var serverInfo = app.metadata.getServerInfo();
        var language = app.lang.getLanguage();
        var module = 'activesubscriptionsdashlet';
        var route = app.controller.context.get('layout');

        let products = app.user.getProductCodes();
        products = products ? products.join(',') : '';

        var params = {
            edition: serverInfo.flavor,
            version: serverInfo.version,
            lang: language,
            module: module,
            route: route
        };
        if (!_.isEmpty(products)) {
            params.products = products;
        }

        return 'https://www.sugarcrm.com/crm/product_doc.php?' + $.param(params);
    },

    /**
     * Get base model from parent context.
     *
     * @deprecated Since 10.2.0. Does not support non-Account module support.
     * @private
     */
    _getBaseModel: function() {
        var baseModule = this.context.get('module');
        var currContext = this.context;

        if (baseModule !== this.baseModule) {
            return;
        }

        while (currContext) {
            var contextModel = currContext.get('rowModel') || currContext.get('model');

            if (contextModel && contextModel.get('_module') === baseModule) {
                this.baseModel = contextModel;

                var parentHasRowModel = currContext.parent && currContext.parent.has('rowModel');
                if (!parentHasRowModel) {
                    break;
                }
            }

            currContext = currContext.parent;
        }
    },

    /**
     * Build our settings object, based on defaults and the metadata, to be used throughout the controller.
     *
     * @private
     */
    _initSettings: function() {
        var settings = _.extend({}, this._defaultSettings, this.settings.attributes);

        this.settings.set(settings);

        return this;
    },

    /**
     * Fetches the metadata object that needs to be updated with the dynamically generated dropdown options.
     *
     * @private
     */
    _getDashletConfigField: function(fieldName) {
        var configPanelMetadata = this.dashletConfig.panels;
        var fieldMetadata = null;

        _.each(configPanelMetadata, function(p) {
            if (_.has(p, 'fields')) {
                _.each(p.fields, function(f) {
                    if (f.name === fieldName) {
                        fieldMetadata = f;
                        return;
                    }
                });
            }
        });

        return fieldMetadata;
    },

    /**
     * When the SugarLive is open we want to consider the context of the activeTab of the dashboard
     * rather than what is open behind the SugarLive
     *
     * @return {string}
     * @private
     */
    _currentModule: function() {
        if (app.omniConsoleConfig && app.omniConsoleConfig.isConfigPaneExpanded) {
            var configDashboard = app.omniConsoleConfig.getComponent('omnichannel-dashboard-config');
            var activeTab = configDashboard.tabModels[configDashboard.activeTab];
            return activeTab.module;
        } else if (app.omniConsole && app.omniConsole.isExpandedAndMaximized() && !app.sideDrawer.isOpen()) {
            var activeContact = app.omniConsole.getComponent('omnichannel-ccp').getActiveContact();
            if (activeContact) {
                var dashboard = app.omniConsole.getComponent('omnichannel-dashboard-switch')
                    .getDashboard(activeContact.contactId).getComponent('dashboard');
                var tabs = dashboard.context.get('tabs');
                var activeTab = tabs[dashboard.context.get('activeTab')];
                return activeTab.module;
            }
        }
        if (app.sideDrawer && app.sideDrawer.isOpen()) {
            let currentTab = app.sideDrawer.getActiveTab();
            if (currentTab) {
                return currentTab.context.module;
            }
        }
        return this.context.get('module');
    },

    /**
     * Create the dynamic dropdown options for the dashlet config page.
     *
     * @private
     */
    _buildFieldsList: function() {
        var configPanel = this._getDashletConfigField('linked_subscriptions_account_field');
        var configPanelOptions = {};

        if (this.currentModule === this.baseModule) {
            // If this dashlet is being added to the Accounts module record view, use the default ID field in the
            // Account module
            configPanelOptions.id = 'ID';
        } else {
            configPanelOptions = this._getRelationshipFields();
        }

        if (_.keys(configPanelOptions).length > 0) {
            // Populate dropdown with relationship field options
            configPanel.options = configPanelOptions;

            // If we don't have any existing field selected, or the previously selected field is no longer present
            if (
                !this.settings.get('linked_subscriptions_account_field') ||
                !configPanelOptions[this.settings.get('linked_subscriptions_account_field')]
            ) {
                this.settings.set({linked_subscriptions_account_field: _.first(Object.keys(configPanelOptions))});
            }
        }
    },

    /**
     * Grabs the 1:* Account-related fields on our current module and returns them in the enum dropdown format.
     *
     * @private
     */
    _getRelationshipFields: function() {
        var relFieldsForDropdown = {};

        // Grab the view metadata that has all of the available fields
        var currentModuleFields = app.metadata.getModule(this.currentModule, 'fields');

        // Grab all the 1:* Account relationship (link) and Account relate fields on the current module
        _.each(currentModuleFields, _.bind(function(f) {
            // Relationship logic
            var isRelationship = f.type === 'link';
            var rel = app.metadata.getRelationship(f.relationship);
            var isLinkedToAccounts = rel && (rel.lhs_module === this.baseModule || rel.rhs_module === this.baseModule);

            // Relate field logic
            var isRelateField = f.type === 'relate';
            var isRelatedToAccounts = f.module === this.baseModule;

            // Stores the current field that metadata is fetched for
            var relField = null;

            if (isRelationship && isLinkedToAccounts) {
                var fieldKey = null;

                var hasLeftJoinKey = _.has(rel, 'join_key_lhs') && rel.join_key_lhs !== null;
                var hasRightJoinKey = _.has(rel, 'join_key_rhs') && rel.join_key_rhs !== null;
                // Determine where to grab the account_id field based on the relationship
                if (hasLeftJoinKey && hasRightJoinKey) {
                    fieldKey = rel.lhs_module === this.baseModule ? 'join_key_lhs' : 'join_key_rhs';
                } else {
                    fieldKey = rel.lhs_module === this.baseModule ? 'rhs_key' : 'lhs_key';
                }

                relField = app.metadata.getField({name: rel[fieldKey], module: this.currentModule});

                if (_.has(relField, 'name')) {
                    relFieldsForDropdown[relField.name] = app.lang.get(relField.vname, this.currentModule);
                }
            }

            if (isRelateField && isRelatedToAccounts) {
                // Relate fields stemming from the above relationship fields can be in our list, we want to filter
                // those out by checking there isn't an existing entry in our dropdown options.
                if (!relFieldsForDropdown[f.id_name] && _.has(f, 'id_name')) {
                    relFieldsForDropdown[f.id_name] = app.lang.get(f.vname, this.currentModule);
                }
            }
        }, this));

        return relFieldsForDropdown;
    },

    /**
     * Gets the account ID from the model
     *
     * @param settingsAccountField
     * @return {string}
     * @private
     */
    _getAccountId: function(settingsAccountField) {
        var linkedAccountField = this.settings.get(settingsAccountField);
        if (!linkedAccountField) {
            return null;
        }

        // Normally, we get the field from the context's model. On focus drawer or
        // console dashboards or from SugarLive, we need to get the field from the parent context's rowModel/model
        var rowModelLayouts = ['focus', 'multi-line', 'omnichannel'];
        let linkField = '';
        let layout = this.context.get('layout') || this.context.parent.get('layout');
        if (_.contains(rowModelLayouts, layout)) {
            let model = this.context.parent.get('rowModel') || this.context.parent.get('model');
            linkField = model.get(linkedAccountField);
        } else {
            linkField = this.context.get('model').get(linkedAccountField);
        }
        return linkField || '';
    },

    /**
     * Initialize collection.
     *
     * @private
     */
    _initCollection: function() {
        if (this._mode === 'config' || !this.purchasesModule) {
            return;
        }

        var accountId = this._getAccountId('linked_subscriptions_account_field');

        var today = app.date().formatServer(true);
        var filter = [
            {
                'account_id': {
                    '$equals': accountId,
                }
            },
            {
                'service': {
                    '$equals': 1,
                },
            },
            {
                'start_date': {
                    '$lte': today
                }
            },
            {
                'end_date': {
                    '$gte': today
                }
            }
        ];
        var options = {
            'fields': this.dashletConfig.fields || [],
            'filter': filter,
            'limit': app.config.maxRecordFetchSize || 1000,
            'success': _.bind(function() {
                if (this.disposed) {
                    return;
                }
                var self = this;
                // Render here only when the model empty, else render after the bulk call is resolved.
                if (!this.collection.models.length) {
                    this.render();
                }
                _.each(this.collection.models, function(model) {
                    // add 1 day to display remaining time correctly
                    var nextDate = app.date(model.get('end_date')).add('1', 'day');
                    model.set('service_remaining_time', nextDate.fromNow());
                    var collections = model.get('pli_collection');
                    // create the payload
                    var bulkSaveRequests = self._createBulkCollectionsPayload(collections);
                    // send the payload
                    self._sendBulkCollectionsUpdate(bulkSaveRequests);
                });
            }, this),
        };
        this.collection = app.data.createBeanCollection(this.module, null, options);
        this.collection.fieldsMeta = {
            'total_amount': {
                'name': 'total_amount',
                'type': 'currency',
                'convertToBase': true,
                'currency_field': 'currency_id',
                'base_rate_field': 'base_rate'
            },
            'purchase': {
                'name': 'name',
                'link': true
            }
        };
    },

    /**
     * Load active subscriptions.
     *
     * @param {Object} options Call options
     */
    loadData: function(options) {
        if (this._mode === 'config' || !this.purchasesModule) {
            return;
        }
        this.collection.fetch(options);
    },

    _render: function(options) {
        this._super('_render', [options]);

        if (!this.settings.get('linked_subscriptions_account_field')) {
            // If we don't have any available fields, replace the dropdown with a label.
            this.template = app.template.get(this.name + '.unavailable');
            this._super('_render', [options]);
        }

        this.enableFocusDrawer();
    },

    /**
     * Utility method to create the payload that will be sent to the server via the bulk api call
     * to fetch the related PLIs for a purchase
     *
     * @param {Array} collections
     * @private
     */
    _createBulkCollectionsPayload: function(collections) {
        // loop over all the collections and create the requests
        var bulkSaveRequests = [];
        var url;
        collections.each(function(element) {
            // if the element is new, don't try and save it
            if (!element.isNew()) {
                // create the update url
                url = app.api.buildURL(element.module, 'read', {
                    id: element.get('id')
                });

                // get request on each PLI
                bulkSaveRequests.push({
                    // app.api.buildURL() in app.api.call() calls the rest endpoint with the following request
                    // remove rest from the url here
                    url: url.substr(4),
                    method: 'GET',
                });
            }
        });

        return bulkSaveRequests;
    },

    /**
     * Send the payload via the bulk api
     *
     * @param {Array} bulkSaveRequests
     * @private
     */
    _sendBulkCollectionsUpdate: function(bulkSaveRequests) {
        if (!_.isEmpty(bulkSaveRequests)) {
            app.api.call(
                'create',
                app.api.buildURL(null, 'bulk'),
                {
                    requests: bulkSaveRequests
                },
                {
                    success: _.bind(this._onBulkCollectionsUpdateSuccess, this)
                }
            );
        }
    },

    /**
     * Update the bundles when the results from the bulk api call
     *
     * @param {Array} bulkResponses
     * @private
     */
    _onBulkCollectionsUpdateSuccess: function(bulkResponses) {
        var purchaseId = _.first(bulkResponses).contents.purchase_id;
        var model = _.first(this.collection.models.filter(function(ele) {
            return ele.id === purchaseId;
        }));
        var collectionIndex = this.collection.models.indexOf(model);
        var currentSubscription = 0;
        var collections = model.get('pli_collection');
        var element;
        var quantity = 0;
        var totalAmount = 0;

        _.each(bulkResponses, function(record) {
            element = collections.get(record.contents.id);
            if (element) {
                var startDate = app.date(record.contents.service_start_date);
                var endDate = app.date(record.contents.service_end_date);
                var today = app.date().startOf('day');
                if (startDate <= today && endDate >= today) {
                    currentSubscription++;
                    quantity += record.contents.quantity;
                    totalAmount += parseFloat(app.currency.convertWithRate(parseFloat(record.contents.total_amount),
                        record.contents.base_rate));
                }
            }
        }, this);
        model.set('quantity', quantity);
        model.set('total_amount', totalAmount);
        if (currentSubscription === 0) {
            this.collection.models.splice(collectionIndex, 1);
        }
        this._caseComparator();
        this._daysDifferenceCalculator();
        this.render();
    },

    /**
     * Calculates the upper and lower bounds for the timeline Graph calculating the earliest
     * Start Date and End Date for all the records.
     */
    _caseComparator: function() {
        if (this.collection) {
            var daysPast = moment('1970-01-01');
            var min = Number.MAX_VALUE;
            var max = 0;
            var start;
            var end;
            var modelArray = this.collection.models;
            modelArray.forEach(function(model) {
                start = model.get('start_date');
                start = this.moment(start);
                start = start.diff(daysPast, 'days');
                end = model.get('end_date');
                end = this.moment(end);
                end = end.diff(daysPast, 'days');
                if (max < end) {
                    max = end;
                }
                if (min > start) {
                    min = start;
                }
            });
            this.overallSubscriptionEndDate = max;
            this.overallSubscriptionStartDate = min;
        }
    },

    /**
     * Calculates the width for the graph by adjusting in to the 60% width
     * and sets width for the subscription time past and subscription time left
     * to fit into 60% width.
     */
    _daysDifferenceCalculator: function() {
        var daysPast = moment('1970-01-01');
        var today = moment();
        if (this.collection) {
            var overallSubscriptionStartDate = this.overallSubscriptionStartDate;
            var overallDaysDifference = this.overallSubscriptionEndDate - overallSubscriptionStartDate;
            var start = null;
            var end = null;
            var startDate = null;
            var endDate = null;
            var activeTimelineWidth = null;
            var activePastTimelineWidth = null;
            var timelineOffset = 40;
            today = today.diff(daysPast, 'days');

            _.each(this.collection.models, function(model) {
                start = model.get('start_date');
                start = this.moment(start);
                start = start.diff(daysPast, 'days');
                startDate = ((start - overallSubscriptionStartDate) / overallDaysDifference).toFixed(2) * 100;

                end = model.get('end_date');
                end = this.moment(end);
                this.endDate = end;
                end = end.diff(daysPast, 'days');
                endDate = ((end - overallSubscriptionStartDate) / overallDaysDifference).toFixed(2) * 100;

                activeTimelineWidth = ((end - start) / overallDaysDifference) * 60;
                timelineOffset = timelineOffset + startDate * 0.6;
                activeTimelineWidth = (activeTimelineWidth + timelineOffset) > 100 ? (100 - timelineOffset)
                    : activeTimelineWidth;
                activePastTimelineWidth = ((today - start) / (end - start)) * 100;
                activePastTimelineWidth = activePastTimelineWidth >= 100 ? activePastTimelineWidth - 1
                    : activePastTimelineWidth;
                this.expiryComingSoon = (activePastTimelineWidth) >= 90 ? true : false;
                timelineOffset = isNaN(timelineOffset) ? 40 : timelineOffset;
                activeTimelineWidth = isNaN(activeTimelineWidth) ? 60 : activeTimelineWidth;
                activePastTimelineWidth = isNaN(activePastTimelineWidth) ? 99 : activePastTimelineWidth;
                activeTimelineWidth = (activeTimelineWidth === 0) ? 100 - activePastTimelineWidth : activeTimelineWidth;
                model.set({
                    startDate: _.first(app.date(model.get('start_date')).formatUser().split(' ')),
                    endDate: _.first(app.date(model.get('end_date')).formatUser().split(' ')),
                    expiration: this.endDate.fromNow(),
                    timelineOffset: timelineOffset,
                    subscriptionValidityActive: activeTimelineWidth.toFixed(2),
                    subscriptionActiveWidth: activePastTimelineWidth.toFixed(2),
                    expiryComingSoon: this.expiryComingSoon
                });
                timelineOffset = 40;
            });
        }
    },
})

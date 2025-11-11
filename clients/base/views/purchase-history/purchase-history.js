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
 * @class View.Views.Base.PurchaseHistoryView
 * @alias SUGAR.App.view.views.BasePurchaseHistoryView
 * @extends View.Views.Base.ActiveSubscriptionsView
 */
({
    extendsFrom: 'ActiveSubscriptionsView',

    plugins: ['Dashlet', 'Pagination', 'DashletSearchControls'],

    events: {
        'click .toggle-pli-list': 'togglePliList',
        'click [data-action=show-more-plis]': 'getNextPliPagination'
    },

    /**
     * Object representing the initial state of our dropdown.
     *
     * @property {Object}
     */
    _defaultSettings: {
        linked_account_field: null,
        limit: 10
    },

    /**
     * Purchases fields metadata
     *
     * @property {Object}
     */
    purchasesFields: null,

    /**
     *  Purchased Line Items fields metadata
     *
     * @property {Object}
     */
    pliFields: null,

    /**
     * Flag for if pagination needs to be hidden (used for when user lacks module access)
     *
     * @property {boolean}
     */
    hidePagination: true,

    /**
     * Label displayed for the PLI pagination link
     *
     * @property {string}
     */
    showMorePlisLabel: null,

    /**
     * List of fields to be included when fetching PLIs
     *
     * @property {Array}
     */
    pliFieldList: [
        'id', 'name', 'service_start_date', 'service_end_date',
        'quantity', 'total_amount', 'currency_id', 'base_rate',
    ],

    /**
     * Sort dropdown items
     *
     * @property {Array}
     */
    sortItems: null,

    /**
     * Placeholder text for the search field
     *
     * @property {string}
     */
    searchFieldPlaceholder: null,

    /**
     * Options and parameters for fetching the Purchase collection
     *
     * @property {Object}
     */
    collectionOptions: null,

    /**
     * Base filter hold the filter object without any other user entered filters
     *
     * @property {Object}
     */
    baseFilter: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.currentModule = this._currentModule();
        this.module = 'Purchases';
        this.moduleName = {'module_name': app.lang.getModuleName(this.module, {'plural': true})};
        this.baseModule = 'Accounts';

        var canAccessPurchases = !_.isUndefined(app.metadata.getModule('Purchases'));
        var canAccessPlis = !_.isUndefined(app.metadata.getModule('PurchasedLineItems'));
        this.purchasesModule = false;
        if (canAccessPurchases && canAccessPlis) {
            this.purchasesModule = true;
            this.hidePagination = false;

            this.purchasesFields = app.utils.deepCopy(app.metadata.getModule('Purchases', 'fields'));
            this.purchasesFields.name.link = true;
            this.purchasesFields.name.disableFocusDrawerRecordSwitching = true;

            this.pliFields = app.utils.deepCopy(app.metadata.getModule('PurchasedLineItems', 'fields'));
            this.pliFields.name.type = 'dates-name';
            this.pliFields.name.link = true;
            this.pliFields.name.disableFocusDrawerRecordSwitching = true;
            this.pliFields.total_amount.showTransactionalAmount = true;
            this.pliFields.total_amount.convertToBase = true;

            var moduleName = new Handlebars.SafeString(
                app.lang.getModuleName('PurchasedLineItems', {plural: true}).toLowerCase()
            );
            this.showMorePlisLabel = app.lang.get('TPL_SHOW_MORE_MODULE', 'PurchasedLineItems', {module: moduleName});

            this.hideFirstPaginationLoadingMessage = true;
            this.usePaginationComponent = true;

            this.sortItems = this._buildSortList('purchase_history_sort_dom');
            this.searchFieldPlaceholder = app.lang.get('LBL_SEARCH_PURCHASE_NAME');
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

        this.tbodyTag = 'ul[data-action="pagination-body"]';

        this._mode = viewName;

        // Builds our dynamic dropdown list, but also populates the Account field in case it is not already set,
        // for example on upgrade.
        this._buildFieldsList();

        if (!_.isEmpty(this.sortItems)) {
            this.currentSortOrder = _.first(this.sortItems).id;
        }

        this._initCollection();

        this.context.set('fields', this.dashletConfig.fields);
        this.context.set('limit', this.settings.get('limit'));
        this.context.set('skipFetch', false);

        // This is used in the parent view to determine whether or not it's
        // okay to render the dashlet.
        this.settings.set('linked_subscriptions_account_field', true);

        this.layout.reloadableComponent = this;
    },

    /**
     * Build the URL for the dashlet documentation.
     *
     * @return {string}
     * @private
     */
    _buildDocumentationUrl: function() {
        var serverInfo = app.metadata.getServerInfo();
        var language = app.lang.getLanguage();
        var module = 'purchasehistorydashlet';
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
     * Build our settings object, based on defaults and the metadata, to be used throughout the controller.
     *
     * @private
     */
    _initSettings: function() {
        var settings = _.extend({}, this._defaultSettings, this.settings.attributes);
        if (!settings.limit) {
            settings.limit = this._defaultSettings.limit;
        }

        this.settings.set(settings);
        return this;
    },

    /**
     * Create the dynamic dropdown options for the dashlet config page.
     *
     * @private
     */
    _buildFieldsList: function() {
        var configPanel = this._getDashletConfigField('linked_account_field');
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
                !this.settings.get('linked_account_field') ||
                !configPanelOptions[this.settings.get('linked_account_field')]
            ) {
                this.settings.set({linked_account_field: _.first(Object.keys(configPanelOptions))});
            }
        }
    },

    /**
     * Builds the list of ordered k-v pairs for the sort list.
     * @param sortStringsKey
     * @return {Array}
     * @private
     */
    _buildSortList: function(sortStringsKey) {
        var sortList = app.lang.getAppListStrings(sortStringsKey);
        var orderedKeys = _.map(app.lang.getAppListKeys(sortStringsKey), function(appListKey) {
            return appListKey.toString();
        });

        var moduleName = new Handlebars.SafeString(app.lang.getModuleName('Purchases', {plural: false})).toString();

        return _.map(orderedKeys, function(key) {
            var value = sortList[key].replace('{{module}}', moduleName);

            return {
                id: key,
                text: value
            };
        });
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

        var limit = this.settings.get('limit');
        var accountId = this._getAccountId('linked_account_field');

        this.baseFilter = {
            'account_id': {
                '$equals': accountId
            }
        };

        this.collectionOptions = {
            'fields': this.dashletConfig.fields || [],
            'filter': [this.baseFilter],
            'limit': limit || app.config.maxRecordFetchSize || 10,
            'params': {
                'order_by': this.currentSortOrder
            },
            'success': _.bind(function() {
                if (this.disposed) {
                    return;
                }
                this.render();
            }, this),
        };
        this.collection = app.data.createBeanCollection(this.module, null, this.collectionOptions);
        this.collection.fieldsMeta = this.purchasesFields;
        this.collection.component = this;
    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {
        if (this._mode === 'config' || !this.purchasesModule) {
            return;
        }

        // Show the loading text on dashlet refresh
        this.collection.dataFetched = false;
        this.render();

        this.collection.fetch(options);
    },

    /**
     * Show or hide the PLI list for a Purchase
     * @param event
     */
    togglePliList: function(event) {
        var $element = this.$(event.currentTarget).find('i.toggle-list');
        var purchaseId = $element.attr('data-id');
        var purchaseModel = this.collection.get(purchaseId);

        this._togglePliVisibility(purchaseModel);

        this._initPliCollection(purchaseModel);

        if (!purchaseModel.hasPlisLoaded) {
            purchaseModel.pliCollection.fetch();
        }

        this.render();
    },

    /**
     * Initializes the PLI collection for a purchase
     * @param purchase
     */
    _initPliCollection: function(purchase) {
        if (purchase.hasPlisLoaded) {
            return;
        }

        purchase.pliLoading = true;

        var limit = this.settings.get('limit');
        var filter = [
            {
                'purchase_id': {
                    '$equals': purchase.get('id'),
                }
            }
        ];
        var options = {
            'fields': this.pliFieldList,
            'params': {
                'order_by': 'service_end_date:desc'
            },
            'filter': filter,
            'limit': limit || app.config.maxRecordFetchSize || 10,
            'success': _.bind(function() {
                if (this.disposed) {
                    return;
                }

                purchase.pliLoading = false;
                purchase.hasPlisLoaded = true;

                this.render();
            }, this),
            'showAlerts': false,
            'add': true,
        };
        purchase.pliCollection = app.data.createBeanCollection('PurchasedLineItems', null, options);
        purchase.pliCollection.fieldsMeta = this.pliFields;
        purchase.pliCollection.component = this;
    },

    /**
     * Get the next pagination for a PLI list
     * @param event
     */
    getNextPliPagination: function(event) {
        var $element = this.$(event.currentTarget);
        var purchaseId = $element.attr('data-id');
        var purchaseModel = this.collection.get(purchaseId);

        purchaseModel.pliLoading = true;
        this.render();

        purchaseModel.pliCollection.paginate({
            add: true
        });
    },

    /**
     * Handle PLI list visibility state and show/hide icon
     * @param purchase
     * @private
     */
    _togglePliVisibility: function(purchase) {
        if (!purchase.chevronIcon || purchase.chevronIcon === 'sicon-chevron-down') {
            purchase.chevronIcon = 'sicon-chevron-up';
            purchase.showPliList = true;
        } else {
            purchase.chevronIcon = 'sicon-chevron-down';
            purchase.showPliList = false;
        }
    },

    /**
     * Updates the sort order with the newly selected criteria
     */
    applySort: function(sortOrder) {
        this.collectionOptions = _.extend({}, this.collectionOptions, {
            'params': {
                'order_by': sortOrder
            }
        });

        this.refetchCollection();
    },

    /**
     * Applies an updated filterdef with the current value on the quicksearch field.
     *
     * @param {string} searchInput the user-entered search string
     */
    applySearch: function(searchInput) {
        this.collectionOptions = _.extend({}, this.collectionOptions, {
            'filter': this.buildFilterDefinition(this.baseFilter || [], searchInput)
        });

        this.refetchCollection();
    },

    /**
     * Updates the persistent options and re-fetch the collection. Used to update the
     * collection on search/sort.
     * @private
     */
    refetchCollection: function() {
        // Pagination uses the persistent options, so we need to set those too.
        // Otherwise when new rows are added they will follow the default filter.
        this.collection._persistentOptions = this.collectionOptions;

        this.collection.dataFetched = false;
        this.render();

        this.collection.fetch(this.collectionOptions);
    },

    /**
     * Builds the filter definition to pass to the request when doing a quick
     * search.
     *
     * It will combine the filter definition for the search term with the
     * initial filter definition. Both are optional, so this method may return
     * an empty filter definition (empty `array`).
     *
     * @param {Object} oSelectedFilter original Selected filter
     * @param {string} searchTerm The term typed in the quick search field.
     * @return {Array} filterDef The filter definition.
     */
    buildFilterDefinition: function(oSelectedFilter, searchTerm) {
        if (!app.metadata.getModule('Filters') || !app.data.createBeanCollection('Filters')) {
            return [];
        }
        var filterBeanClass = app.data.getBeanClass('Filters').prototype;
        var selectedFilter = app.utils.deepCopy(oSelectedFilter);
        var searchTermFilter;
        var searchModule = this.module;

        selectedFilter = _.isArray(selectedFilter) ? selectedFilter : [selectedFilter];

        searchTermFilter = filterBeanClass.buildSearchTermFilter(searchModule, searchTerm, '$contains');

        var isSelectedFilter = _.size(selectedFilter) > 0;
        var isSearchFilter = _.size(searchTermFilter) > 0;

        selectedFilter = this.filterSelectedFilter(selectedFilter);

        var filterDef = [];

        if (isSelectedFilter && isSearchFilter) {
            selectedFilter.push(_.first(searchTermFilter));
            filterDef = [{'$and': selectedFilter}];
        } else if (isSelectedFilter) {
            filterDef = selectedFilter;
        } else if (isSearchFilter) {
            filterDef = searchTermFilter;
        }

        return filterDef;
    },

    /**
     * Filter fields that don't exist either on vardefs or search definition.
     *
     * Special fields (fields that start with `$`) like `$favorite` aren't
     * cleared.
     * @param {Array} selectedFilter def for currently selected filter
     * @return {Array} filtered def
     */
    filterSelectedFilter: function(selectedFilter) {
        var specialField = /^\$/;
        var meta = app.metadata.getModule(this.module);
        selectedFilter = _.filter(selectedFilter, function(def) {
            var fieldName = _.keys(def).pop();
            return specialField.test(fieldName) || meta.fields[fieldName];
        }, this);

        return selectedFilter;
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.collection) {
            this.collection.on('reset', this.render, this);
        }
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
     * @inheritdoc
     * @param options
     * @private
     */
    _render: function(options) {
        this._super('_render', [options]);

        if (!this.settings.get('linked_account_field')) {
            // If we don't have any available fields, replace the dropdown with a label.
            this.template = app.template.get(this.name + '.unavailable');
            this._super('_render', [options]);
        }
    },
})

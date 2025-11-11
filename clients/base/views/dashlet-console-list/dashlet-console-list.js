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
 * @class View.Views.Base.DashletConsoleListView
 * @alias SUGAR.App.view.views.BaseDashletConsoleListView
 * @extends View.Views.Base.ListView
 */
({
    extendsFrom: 'ListView',

    className: 'dashlet-console-list',

    /**
     * ListView sort will close previews, but this is not needed for dashlet console lists
     * In fact, closing preview causes problem when previewing this list dashlet
     * from dashlet-select
     */
    sort: $.noop,

    /**
     * These labels are used in the filter dropdown
     *  - labelDropdownTitle        label used as the dropdown title. ie `Filter`
     *  - labelAllRecordsFormatted  label used when all records are selected. ie `All <Module>s`
     */
    labelDropdownTitle: 'LBL_FILTER',
    labelAllRecords: 'LBL_FILTER_ALL_RECORDS',

    /**
     * @inheritdoc
     */
    dataView: '',

    /**
     * @inheritdoc
     */
    plugins: [
        'Dashlet',
        'Pagination',
        'ConfigDrivenList'
    ],

    /**
     * @inheritdoc
     */
    fallbackFieldTemplate: 'list',

    /**
     * The default settings for a list view dashlet.
     *
     * @property {Object}
     */
    _defaultSettings: {
        module: 'Cases',
        freeze_first_column: true,
        limit: 5,
        filter_id: 'all_records'
    },

    /**
     * The filter definition for selected filter
     *
     * @property {Array}
     */
    currentFilterDef: [],

    /**
     * The current search string in quick search bar
     *
     * @property {string}
     */
    currentSearch: '',

    /**
     * Cache of the modules a user is allowed to see.
     *
     * The keys are the module names and the values are the module names after
     * resolving them against module and/or app strings. The cache logic can be
     * seen in {@link BaseDashablelistView#_getAvailableModules}.
     *
     * @property {Object}
     */
    _availableModules: {
        'Cases': app.lang.getModuleName('Cases'),
        'Contacts': app.lang.getModuleName('Contacts')
    },

    /**
     * Flag indicates if a module is available for display.
     */
    moduleIsAvailable: true,

    /**
     * Flag to keep track the ACL access of the module
     */
    moduleACLAccess: true,

    /**
     * Truthy when filter dropdown is enabled.  Updated whenever the filter module changes.
     */
    filterDropdownEnabled: true,

    /**
     * Flag indicates if dashlet filter is accessible.
     */
    filterIsAccessible: true,

    /**
     * A list of all the filter models available
     *
     * @property {Array}
     */
    filterModels: [],

    /**
     * An object for different modules and their fields to filter results against
     *
     * @property {Object}
     */
    moduleFilterField: {
        Contacts: 'primary_contact_id',
    },

    /**
     * A list of all the modules available for custom filters
     *
     * @property {Array}
     */
    customFilterModuleList: ['Cases'],

    /**
     * Defines the scroll container jQuery element
     */
    scrollContainer: null,

    /**
     * @inheritdoc
     */
    'events': {
        'click .single': 'rowClicked',
        'click [class*="orderBy"]': 'setOrderBy',

        // filter quick search events
        'click .add-on.sicon-close': 'clearQuickSearch',
        'keyup .search-name': 'search',
        'paste .search-name': 'search',

        // filter dropdown events
        'click .choice-filter-close': 'handleClearFilter',
        'keydown .choice-filter-close': 'handleClearFilter',

        //dashlet action
        'click .sicon-refresh': 'refreshClicked',
    },

    /**
     * @inheritdoc
     *
     * Append lastStateID on metadata in order to active user cache. Load partials for dropdown selection and result.
     * Fix dropdown and other dashlet css on completion of reload
     */
    initialize: function(options) {
        options.meta = _.extend({}, options.meta, {
            last_state: {
                id: 'dashlet-console-list'
            }
        });
        this.currentFilterId = {};
        this._super('initialize', [options]);

        //Load partials
        this._select2formatSelectionTemplate = app.template.get('dashlet-console-list.selection-partial');
        this._select2formatResultTemplate = app.template.get('dashlet-console-list.result-partial');

        if (!_.isUndefined(this.collection)) {
            this.collection.on('data:sync:complete', function() {
                this._renderDropdown(this.filterModels[this.module]);
                if (this.currentFilterId[this.module] === this.layout.filters.collection.defaultFilterFromMeta) {
                    this.$('.choice-filter-close').hide();
                }

                var searchElem = this.$('input.search-name');
                this.updatePlaceholder(searchElem);

                // if search string is present but not displayed on the search bar
                if (!_.isEmpty(this.currentSearch) && searchElem.val() === '') {
                    // put back the search string after dashlet load
                    this.$('input.search-name').val(this.currentSearch);
                    this.toggleClearQuickSearchIcon(true);
                }

                this.checkFooterVisibility();
            }, this);
        }

        this.context.set('isUsingListPagination', true);
    },

    /**
     * Build the cache key for last filter.
     * @return {string} hash key.
     */
    getLastFilterKey: function() {
        return this.options.meta.last_state + '.' + this.module + '.last_filter_id';
    },

    /**
     * Get last filter id if cached.
     * @return {string|null} last filter id or null
     */
    getLastFilterId: function() {
        if (this.context.parent) {
            return this.context.parent.get(this.getLastFilterKey());
        }
        return null;
    },

    /**
     * Cache filter id.
     * @param {string} filterId
     */
    setLastFilterId: function(filterId) {
        if (this.context.parent) {
            this.context.parent.set(this.getLastFilterKey(), filterId);
        }
    },

    /**
     * Update quick search placeholder to Search by Field1, Field2, Field3 when the module changes
     * @param {Object} el
     */
    updatePlaceholder: function(el) {
        var filtersBeanPrototype = app.data.getBeanClass('Filters').prototype;
        var fields = filtersBeanPrototype.getModuleQuickSearchMeta(this.module).fieldNames;
        var fieldLabels = app.utils.getFieldLabels(this.module, fields);
        var label = app.lang.get('LBL_SEARCH_BY') + ' ' + fieldLabels.join(', ').toLowerCase() + '...';
        return el.attr('placeholder', label);
    },

    /**
     * select2 change handler to update the list based on selected filter
     *
     * @param {Event} evt
     */
    handleSelect: function(evt) {
        if (evt && evt.type === 'keydown' &&
            !(evt.keyCode === $.ui.keyCode.ENTER || evt.keyCode === $.ui.keyCode.SPACE)) {
            return;
        }
        evt.stopPropagation();
        this.setSelection(evt.val);
    },

    /**
     * Handle selection of a new filter dropdown value.
     *
     * @param {string} id The id of the filter to select in the dropdown.
     */
    setSelection: function(id) {
        this.currentFilterId[this.module] = id;

        var filter = this.layout.filters && this.layout.filters.collection ?
            this.layout.filters.collection.get(id) : {};
        var filterDef = !_.isEmpty(filter) ? filter.get('filter_definition') : [];

        filterDef = this.buildFilterDefinition(filterDef, this.currentSearch);
        this.currentFilterDef = filterDef;
        this.setLastFilterId(id);
        this._displayDashlet(filterDef);
    },

    /**
     * When a click happens on the close icon, clear the last filter and trigger selection of default filter
     *
     * @param {Event} evt
     * */
    handleClearFilter: function(evt) {
        if (evt && evt.type === 'keydown' &&
            !(evt.keyCode === $.ui.keyCode.ENTER || evt.keyCode === $.ui.keyCode.SPACE)) {
            return;
        }

        //This event is fired within .choice-filter and another event is attached to .choice-filter
        //We want to stop propagation so it doesn't bubble up.
        evt.stopPropagation();
        var filterId;
        if (this.currentFilterId[this.module] === this.layout.filters.collection.defaultFilterFromMeta) {
            filterId = 'all_records';
        } else {
            filterId = this.layout.filters.collection.defaultFilterFromMeta;
        }

        this.setSelection(filterId);
    },

    /**
     * Handler for refresh button or dashlet refresh click
     */
    refreshClicked: function() {
        this._displayDashlet(this.currentFilterDef);
    },

    /**
     * Render select2 filter dropdown
     * @param {Array} data array of all the available filter defs
     * @private
     */
    _renderDropdown: function(data) {
        this.filterNode = this.$('input.console-list-dropdown');

        this.filterNode.select2({
            data: data,
            multiple: false,
            minimumResultsForSearch: 7,
            formatSelection: _.bind(this.formatSelection, this),
            formatResult: _.bind(this.formatResult, this),
            formatResultCssClass: _.bind(this.formatResultCssClass, this),
            dropdownCss: {width: 'auto'},
            dropdownCssClass: 'search-filter-dropdown',
            escapeMarkup: function(m) {
                return m;
            },
            shouldFocusInput: function() {
                // We return false here so that we do not refocus on the field once
                // it has been blurred. If we return true, blur needs to happen
                // twice before it is really blurred.
                return false;
            },
            width: 'off'
        })
            .off('change')
            .on('change', _.bind(this.handleSelect, this));

        this.fixChoiceFilter(data);
        this.fixDropdownCss();
    },

    /**
     * Fix the choice-filter-label on first render
     *
     * @param {Array} data array of all the available filter defs
     */
    fixChoiceFilter: function(data) {
        var choiceElem = this.$('.choice-filter-label') || [];

        if (!_.isEmpty(choiceElem)) {
            _.each(data, function(value, key) {
                if (value.id === this.currentFilterId[this.module]) {
                    choiceElem[0].textContent = data[key].text;
                }
            }, this);
        }
    },

    /**
     * Fix the css for filter select2 dropdown on first render
     */
    fixDropdownCss: function() {
        var arrowElem = this.$('.select2-arrow') || [];
        var caratElem = this.$('.sicon.sicon-chevron-down') || [];
        var filterElem = this.$('.select2-chosen') || [];

        if (arrowElem.length !== 0) {
            arrowElem.remove();
        }

        if (caratElem.length === 0  && filterElem.length !== 0) {
            filterElem.append('<i class="sicon sicon-chevron-down"></i>');
        }
    },

    /**
     * Update the text for the selected filter and returns template
     *
     * @param {Object} item
     * @return {string} selection-partial template
     * */
    formatSelection: function(item) {
        var ctx = {};
        var safeString;
        var a11yLabel;
        var a11yTabindex = 0;

        //Don't remove this line. We want to update the selected filter name but don't want to change to the filter
        //name displayed in the dropdown
        item = _.clone(item);

        //Escape string to prevent XSS injection
        safeString = Handlebars.Utils.escapeExpression(item.text);
        a11yTabindex = 0;
        a11yLabel = app.lang.get('LBL_FILTER_EDIT_FILTER') + ' ' + safeString;

        // Update the text for the selected filter.
        this.$('.choice-filter-label')
            .html(safeString)
            .attr('aria-label', a11yLabel)
            .attr('tabindex', a11yTabindex);

        this.$('.choice-filter-close').toggle(item.id !== 'all_records');
        this.$('.choice-filter').toggleClass('with-close', item.id !== 'all_records');

        ctx.label = app.lang.get(this.labelDropdownTitle);
        ctx.enabled = this.filterDropdownEnabled;

        return this._select2formatSelectionTemplate(ctx);
    },

    /**
     * Returns template for all the select2 dropdown items
     *
     * @param {Object} option
     * @return {string} result-partial template
     * */
    formatResult: function(option) {
        if (option.id === this.currentFilterId[this.module]) {
            option.icon = 'sicon-check';
        } else {
            option.icon = undefined;
        }
        return this._select2formatResultTemplate(option);
    },

    /**
     * Adds a class to the first user custom filter (to add border top)
     *
     * @param {Object} item
     * @return {string} css class to attach
     */
    formatResultCssClass: function(item) {
        if (item.firstNonUserFilter) {
            return 'select2-result-border-top';
        }
    },

    /**
     * Fires the quick search.
     * @param {Event} event A keyup event.
     */
    throttledSearch: _.debounce(function(event) {
        this.applyQuickSearch(true, event.type);
    }, 400),

    /**
     * Wrapper to throttle search
     * @param {Event} event A keyup event.
     */
    search: function(event) {
        this.currentSearch = this.$('input.search-name').val();
        this.throttledSearch(event);
    },

    /**
     * Handler for clearing the quick search bar
     *
     * @param {Event} event A click event on the close button of search bar
     */
    clearQuickSearch: function(event) {
        this.currentSearch = '';
        this.$('input.search-name').val('');
        this.applyQuickSearch(true, event.type);
    },

    /**
     * Applies an updated filterdef with the current value on the quicksearch field.
     *
     * @param {boolean} force `true` to always trigger the `filter:apply`
     *   event, `false` otherwise. Defaults to `false`.
     * @param {string} evtType the type of event that triggered the quick search
     */
    applyQuickSearch: function(force, evtType) {
        force = !_.isUndefined(force) ? force : false;
        if (!force) {
            var searchElem = this.$('input.search-name');
            var newSearch = searchElem.val();

            if (this.currentSearch !== newSearch) {
                this.currentSearch = newSearch;
                force = true;
            }
        }

        if (force) {
            // an empty filterDef is the same as all_records filters, so we use that as our fallback
            var filterDef = this.buildFilterDefinition(this.currentFilterDef || [], this.currentSearch);

            this._displayDashlet(filterDef);

            // once the dashlet has loaded fix the filter and footer component
            this.collection.once('data:sync:complete', function() {
                this.toggleClearQuickSearchIcon(!_.isEmpty(this.currentSearch));

                var searchElem = this.$('input.search-name');
                if (!searchElem.val().length) {
                    searchElem.val(this.currentSearch);
                }

                // keep focus on input field for all keyup events only
                // not for click events on the clear button
                if (evtType === 'keyup') {
                    searchElem.focus();
                }

                this.checkFooterVisibility();
            }, this);
        }
    },

    /**
     * Append or remove an icon to the quicksearch input so the user can clear the search easily
     * @param {boolean} addIt TRUE if you want to add it, FALSE to remove
     */
    toggleClearQuickSearchIcon: function(addIt) {
        if (addIt && !this.$('.sicon-remove.add-on')[0]) {
            this.$('.filter-view.search').append('<i class="sicon sicon-close add-on"></i>');
        } else if (!addIt) {
            this.$('.sicon-remove.add-on').remove();
        }
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
     * @return {Array} The filter definition.
     */
    buildFilterDefinition: function(oSelectedFilter, searchTerm) {
        if (!app.metadata.getModule('Filters') || !this.layout.filters) {
            return [];
        }
        var filterBeanClass = app.data.getBeanClass('Filters').prototype;
        var selectedFilter = app.utils.deepCopy(oSelectedFilter);
        var searchTermFilter;
        var searchModule = this.module;

        selectedFilter = _.isArray(selectedFilter) ? selectedFilter : [selectedFilter];

        searchTermFilter = filterBeanClass.buildSearchTermFilter(searchModule, searchTerm);

        var isSelectedFilter = _.size(selectedFilter) > 0;
        var isSearchFilter = _.size(searchTermFilter) > 0;

        selectedFilter = this.filterSelectedFilter(selectedFilter);

        if (isSelectedFilter && isSearchFilter) {
            selectedFilter.push(searchTermFilter[0]);
            return [{'$and': selectedFilter}];
        } else if (isSelectedFilter) {
            return selectedFilter;
        } else if (isSearchFilter) {
            return searchTermFilter;
        }

        return [];
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
     * Handler for row click event
     * Switches to relevant module tab
     *
     * @param {Event} evt row click event
     */
    rowClicked: function(evt) {
        var loadModel = [];
        var rowId = evt.currentTarget && evt.currentTarget.dataset ? evt.currentTarget.dataset.id : '';
        if (!_.isEmpty(rowId) && this.collection) {
            loadModel = _.filter(this.collection.models, function(model) {
                if (model.id === rowId) {
                    return model;
                }
            }, this);
        }

        var dashboard = this.closestComponent('omnichannel-dashboard');

        // set current row as model for the dashboard tab and switch tab based on module
        if (dashboard && loadModel.length !== 0) {
            dashboard.setModel(dashboard.moduleTabIndex[this.module], loadModel[0]);
            dashboard.switchTab(dashboard.moduleTabIndex[this.module]);
        }
    },

    /**
     * In case the filter for the dashlet has been retrieved successfully
     * a filter definition based dashlet setup will be triggered.
     *
     *  @param {Object} data object from specified filter request.
     */
    triggerDashletSetup: function(data) {
        this.filterIsAccessible = true;
        this.currentFilterDef = data.filter_definition;
        this._displayDashlet(data.filter_definition);
    },

    /**
     * Must implement this method as a part of the contract with the Dashlet
     * plugin. Kicks off the various paths associated with a dashlet:
     * Configuration, preview, and display.
     *
     * @param {string} view The name of the view as defined by the `oninit`
     *   callback in {@link DashletView#onAttach}.
     */
    initDashlet: function(view) {
        if (this.meta.config) {
            // keep the display_columns and label fields in sync with the selected module when configuring a dashlet
            this.settings.on('change:module', function(model, moduleName) {
                model.set('label', app.lang.getModuleName(moduleName, {plural: true}));

                // Re-initialize the filterpanel with the new module.
                this.dashModel.set('module', moduleName);
                this.dashModel.set('filter_id', 'all_records');
                this.layout.trigger('dashlet:filter:reinitialize');

                this._updateDisplayColumns();
            }, this);
        }
        this._initializeSettings();
        this.metaFields = this._getColumnsForDisplay();
        this.moduleACLAccess = app.acl.hasAccess('view', this.settings.get('module'));

        this.before('render', function() {
            if (!this.moduleIsAvailable || !this.filterIsAccessible) {
                return false;
            }
        });

        // the pivot point for the various dashlet paths
        if (this.meta.config) {
            this._configureDashlet();
            this.listenTo(this.layout, 'init', this._addFilterComponent);
            this.listenTo(this.layout.context, 'filter:add', this.updateDashletFilterAndSave);
            this.layout.before('dashletconfig:save', function() {
                this.saveDashletFilter();
                // NOTE: This prevents the drawer from closing prematurely.
                return false;
            }, this);
        } else if (this.moduleIsAvailable) {
            var filterId = this.settings.get('filter_id');
            if (!filterId || this.meta.preview || !this.moduleACLAccess) {
                this._displayDashlet();
                return;
            }

            var filters = app.data.createBeanCollection('Filters');
            filters.setModuleName(this.settings.get('module'));
            filters.load({
                success: _.bind(function() {
                    if (this.disposed) {
                        return;
                    }

                    var filterModelArr = [];
                    // get filter details to show on dashlet view dropdown
                    if (!_.isUndefined(filters.collection.models)) {
                        _.each(filters.collection.models, function(model) {
                            filterModelArr.push({
                                id: model.get('id'),
                                text: filters.collection._getTranslatedFilterName(model),
                                firstNonUserFilter: model.get('id') === 'all_records'
                            });
                        }, this);

                        // add filters to filterModels so they can be retrieved based on the module
                        this.filterModels[this.module] = filterModelArr;

                        // set filters for the layout if not already present
                        if (_.isUndefined(this.layout.filters)) {
                            this.layout.filters = filters;
                        }
                    }

                    // get current filter id and def
                    var filter = filters.collection.get(filterId);
                    if (!_.isUndefined(filter)) {
                        this.currentFilterDef = filter.get('filter_definition');
                        this.currentFilterId[this.module] = filter.get('id');
                    }

                    // In case the filter assigned to the list-dashlet is NOT in the filters collection,
                    // as collection only contains certain number (= max_filters) of entries.
                    // Will make a separate api call to fetch the specified filter data.
                    if (!this.currentFilterDef) {
                        var url = app.api.buildURL('Filters/' + filterId, null, null);
                        app.api.call('read', url, null, {
                            success: _.bind(this.triggerDashletSetup, this)
                        });
                    } else if (_.isUndefined(this.currentFilterDef)) {
                        this.filterIsAccessible = false;
                    } else {
                        this._displayDashlet(this.currentFilterDef);
                    }
                }, this),
                error: _.bind(function() {
                    if (this.disposed) {
                        return;
                    }
                    this._displayDashlet();
                }, this)
            });
        }
    },

    /**
     * Adds a custom filter to display cases relevant to Current Contact for the Contacts Tab in omnichannel-dashboard.
     * Here we update the current filter just before it gets applied and fetches data, so we only get relevant data.
     * This approach makes sure that the actual filter models are not overwritten so that they show default behavior
     * in other cases.
     *
     * @param {Array} filterDef the original filter definition to be applied
     * @return {Array} updated filter definition for the Cases dashlet in Contacts tab
     */
    updateFilterForModule: function(filterDef) {
        var dashboard = this.closestComponent('omnichannel-dashboard') || null;

        if (dashboard !== null) {
            var dashboardComp = !_.isUndefined(dashboard) && !_.isUndefined(dashboard.getComponent('dashboard')) ?
                dashboard.getComponent('dashboard') : null;
            var activeTab = !_.isUndefined(dashboardComp) && dashboardComp.getComponent('omnichannel-dashboard') ?
                dashboardComp.getComponent('omnichannel-dashboard').activeTab : 0;

            // if Contacts tab is active and it is Cases Console List view dashlet
            if (!_.isUndefined(dashboard.moduleTabIndex) &&
                activeTab === dashboard.moduleTabIndex.Contacts && this.module === this.customFilterModuleList[0]) {
                // get the model Id for current contact
                var rowId = !_.isUndefined(dashboard.context) && !_.isUndefined(dashboard.context.get('rowModel')) ?
                    dashboard.context.get('rowModel').id : '';

                // get the field to filter result against
                var field = this.moduleFilterField.Contacts || '';

                // create a custom filter to get cases relevant to current Contact
                if (rowId && !_.isEmpty(field)) {
                    var customFilterDef = {};
                    customFilterDef[field] =  {$equals: rowId};
                    // append the custom filter to the existing filter
                    filterDef.push(customFilterDef);
                }
            }
        }

        return filterDef;
    },

    /**
     * @inheritdoc
     * Don't load data if dashlet filter is not accessible.
     */
    loadData: function(options) {
        if (!this.filterIsAccessible) {
            if (options && _.isFunction(options.complete)) {
                options.complete();
            }
            return;
        }
        this._super('loadData', [options]);
    },

    /**
     * Fetch the next pagination records.
     */
    showMoreRecords: function() {
        // Show alerts for this request
        this.getNextPagination();
    },

    /**
     * Returns a custom label for this dashlet.
     *
     * @return {string}
     */
    getLabel: function() {
        var module = this.settings.get('module') || this.context.get('module');
        var moduleName = app.lang.getModuleName(module, {plural: true});
        return app.lang.get(this.settings.get('label'), module, {module: moduleName});
    },

    /**
     * This function is invoked by the `dashletconfig:save` event. If the dashlet
     * we are saving is a dashlet console list, it initiates the save process for a new
     * filter on the appropriate module's list view, otherwise, it takes the
     * `currentFilterId` stored on the context, and saves it on the dashlet.
     */
    saveDashletFilter: function() {
        // Accessing the dashletconfiguration context.
        var context = this.layout.context;

        if (context.editingFilter) {
            // We are editing/creating a new filter
            if (!context.editingFilter.get('name')) {
                context.editingFilter.set('name', app.lang.get('LBL_DASHLET') +
                    ': ' + app.lang.get(this.settings.get('label'), this.settings.get('module')));
            }
            // Triggers the save on `filter-rows` which then triggers
            // `filter:add` which then calls `updateDashletFilterAndSave`
            context.trigger('filter:create:save');
        } else {
            // We are saving a dashlet with a predefined filter
            var filterId = context.get('currentFilterId');
            var obj = {id: filterId};
            this.updateDashletFilterAndSave(obj);
        }
    },

    /**
     * This function is invoked by the `filter:add` event. It saves the
     * filter ID on the dashlet model prior to saving it, for later reference.
     *
     * @param {Bean} filterModel The saved filter model.
     */
    updateDashletFilterAndSave: function(filterModel) {
        // We need to save the filter ID on the dashlet model before saving
        // the dashlet.
        var id = filterModel.id || filterModel.get('id');
        this.settings.set('filter_id', id);
        this.dashModel.set('filter_id', id);

        var componentType = this.dashModel.get('componentType') || 'view';

        // Adding a new dashlet requires componentType to be set on the model.
        if (!this.dashModel.get('componentType')) {
            this.dashModel.set('componentType', componentType);
        }

        app.drawer.close(this.dashModel);

        // The filter collection is not shared amongst views and therefore
        // changes to this collection on different contexts (list views and
        // dashlets) need to be kept in sync.
        app.events.trigger('dashlet:filter:save', this.dashModel.get('module'));
    },

    /**
     * Certain dashlet settings can be defaulted.
     *
     * Builds the available module cache by way of the
     * {@link BaseDashletConsoleListView#_setDefaultModule} call. The module is set
     * after "filter_id" because the value of "filter_id" could impact the value
     * of "label" when the label is set in response to the module change while
     * in configuration mode (see the "module:change" listener in
     * {@link BaseDashletConsoleListView#initDashlet}).
     *
     * @private
     */
    _initializeSettings: function() {
        if (!this.settings.get('limit')) {
            this.settings.set('limit', this._defaultSettings.limit);
        }
        if (this.getLastFilterId()) {
            this.settings.set('filter_id', this.getLastFilterId());
        }
        if (!this.settings.get('filter_id')) {
            this.settings.set('filter_id', this._defaultSettings.filter_id);
        }
        if (_.isUndefined(this.settings.get('freeze_first_column'))) {
            this.settings.set('freeze_first_column', this._defaultSettings.freeze_first_column);
        }
        this._setDefaultModule();
        if (!this.settings.get('label')) {
            this.settings.set('label', 'LBL_MODULE_NAME');
        }
    },

    /**
     * Sets the default module when a module isn't defined in the dashlet's
     * view definition.
     *
     * If the module was defined but it is not in the list of available modules
     * in config mode, then the view's module will be used.
     * @private
     */
    _setDefaultModule: function() {
        var availableModules = _.keys(this._getAvailableModules());
        var module = this.settings.get('module') || this.context.get('module');

        if (_.contains(availableModules, module)) {
            this.settings.set('module', module);
        } else if (this.meta.config) {
            module = _.first(availableModules);
            // On 'initialize' model is set to context's model - that model can have no access at all
            // and we'll result in 'no-access' template after render. So we change it to default model.
            this.model = app.data.createBean(module);
            this.settings.set('module', module);
        } else {
            this.moduleIsAvailable = false;
        }
    },

    /**
     * Update the display_columns attribute based on the current module defined
     * in settings.
     *
     * This will mark, as selected, all fields in the module's list view
     * definition. Any existing options will be replaced with the new options
     * if the "display_columns" DOM field ({@link EnumField}) exists.
     *
     * @private
     */
    _updateDisplayColumns: function() {
        var availableColumns = this._getAvailableColumns();
        this.settings.set('display_columns', _.keys(availableColumns));
    },

    /**
     * Perform any necessary setup before the user can configure the dashlet.
     *
     * Modifies the dashlet configuration panel metadata to allow it to be
     * dynamically primed prior to rendering.
     *
     * @private
     */
    _configureDashlet: function() {
        var availableModules = this._getAvailableModules();
        var availableColumns = this._getAvailableColumns();
        _.each(this.getFieldMetaForView(this.meta), function(field) {
            switch (field.name) {
                case 'module':
                    // load the list of available modules into the metadata
                    field.options = availableModules;
                    break;
                case 'display_columns':
                    // load the list of available columns into the metadata
                    field.options = availableColumns;
                    break;
            }
        });

        // From ConfigDrivenList Plugin
        this.filterConfigFieldsForDashlet();
    },

    /**
     * This function adds the `dashablelist-filter` component to the layout
     * (dashletconfiguration), if the component doesn't already exist.
     */
    _addFilterComponent: function() {
        var filterComponent = this.layout.getComponent('dashablelist-filter');
        if (filterComponent) {
            return;
        }

        this.layout.initComponents([{
            layout: 'dashablelist-filter'
        }]);
    },

    /**
     * Gets all of the modules the current user can see.
     *
     * This is used for populating the module select and list view columns
     * fields.
     *
     * @return {Object} {@link BaseDashletConsoleListView#_availableModules}
     * @private
     */
    _getAvailableModules: function() {
        return this._availableModules;
    },

    /**
     * Gets the correct list view metadata.
     *
     * Returns the correct module list metadata
     *
     * @param  {string} module
     * @return {Object}
     */
    _getListMeta: function(module) {
        return app.metadata.getView(module, 'list');
    },

    /**
     * Gets all of the fields from the list view metadata for the currently
     * chosen module.
     *
     * This is used for the populating the list view columns field and
     * displaying the list.
     *
     * @return {Object} columns A key value pair of all the available columns
     * @private
     */
    _getAvailableColumns: function() {
        var columns = {};
        var module = this.settings.get('module');
        if (!module) {
            return columns;
        }

        _.each(this.getFieldMetaForView(this._getListMeta(module)), function(field) {
            columns[field.name] = app.lang.get(field.label || field.name, module);
        });

        return columns;
    },

    /**
     * Perform any necessary setup before displaying the dashlet.
     *
     * @param {Array} filterDef The filter definition array.
     * @private
     */
    _displayDashlet: function(filterDef) {
        // Get the columns that are to be displayed and update the panel metadata.
        var columns = this._getColumnsForDisplay();
        this.meta.panels = [{fields: columns}];

        this.context.set('skipFetch', false);
        this.context.set('limit', this.settings.get('limit'));
        this.context.set('fields', this.getFieldNames());

        if (filterDef) {
            this._applyFilterDef(filterDef);
            this.context.reloadData({
                recursive: false,
            });
        } else {
            var listBottom = this.layout.getComponent('list-bottom');
            if (listBottom) {
                listBottom.render();
            }
        }

        this._startAutoRefresh();
    },

    /**
     * Hide the No data available footer if dashlet has rows
     */
    checkFooterVisibility: function() {
        var footerElem = this.$('.block-footer') || [];
        if (footerElem.length === 0) {
            return;
        }

        var row = this.$('.dashlet-console-list-row') || [];
        if (row.length !== 0) {
            footerElem.hide();
        }
    },

    /**
     * Sets the filter definition on the context collection to retrieve records
     * for the list view.
     *
     * @param {Array} filterDef The filter definition array.
     * @private
     */
    _applyFilterDef: function(filterDef) {
        if (filterDef) {
            filterDef = _.isArray(filterDef) ? filterDef : [filterDef];

            filterDef = this.filterSelectedFilter(filterDef);

            if (!_.isUndefined(this.context) && !_.isUndefined(this.context.get('collection'))) {
                // get custom filter def for Contacts tab if needed
                filterDef = this.updateFilterForModule(filterDef);

                this.context.get('collection').filterDef = filterDef;
            }
        }
    },

    /**
     * Gets the columns chosen for display for this dashlet list.
     *
     * The display_columns setting might not have been defined when the dashlet
     * is being displayed from a metadata definition, all columns for
     * the selected module are shown in these cases.
     *
     * @return {Object[]} Array of objects defining the field metadata for
     *   each column.
     * @private
     */
    _getColumnsForDisplay: function() {
        var columns = [];
        var fields = this.getFieldMetaForView(this._getListMeta(this.settings.get('module')));
        var moduleMeta = app.metadata.getModule(this.module);
        if (!this.settings.get('display_columns')) {
            this._updateDisplayColumns();
        }

        _.each(this.settings.get('display_columns'), function(name) {
            var field = _.find(fields, function(field) {
                return field.name === name;
            }, this);
            // It's possible that a column is on the dashlet and not on the
            // main list view (thus was never patched by metadata-manager).
            // We need to fix up the columns in that case.
            field = field || app.metadata._patchFields(this.module, moduleMeta, [name]);

            // Handle setting of the sortable flag on the list. This will not
            // always be true
            var sortableFlag;
            var fieldDef = app.metadata.getModule(this.module).fields[field.name];

            // If the module's field def says nothing about the sortability, then
            // assume it's ok to sort
            if (_.isUndefined(fieldDef) || _.isUndefined(fieldDef.sortable)) {
                sortableFlag = true;
            } else {
                // Get what the field def says it is supposed to do
                sortableFlag = fieldDef.sortable;
            }

            var column = _.extend({sortable: sortableFlag}, field);

            columns.push(column);
        }, this);
        return columns;
    },

    /**
     * Starts the automatic refresh of the dashlet.
     *
     * @private
     */
    _startAutoRefresh: function() {
        var refreshRate = parseInt(this.settings.get('auto_refresh'), 10);
        if (refreshRate) {
            this._stopAutoRefresh();
            this._timerId = setInterval(_.bind(function() {
                this.context.resetLoadFlag();
                this.layout.loadData();
            }, this), refreshRate * 1000 * 60);
        }
    },

    /**
     * Cancels the automatic refresh of the dashlet.
     *
     * @private
     */
    _stopAutoRefresh: function() {
        if (this._timerId) {
            clearInterval(this._timerId);
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this.currentSearch = this.$('input.search-name').val();
        if (!this.meta || !this.meta.config) {
            this._super('_render');
            this.scrollContainer = this.$el.find('.console-list-view-table');
        } else {
            this.action = 'list';
            this._super('_render');
        }
    },

    /**
     * Gets the fields metadata from a particular view's metadata.
     *
     * @param {Object} meta The view's metadata.
     * @return {Object[]} The fields metadata or an empty array.
     */
    getFieldMetaForView: function(meta) {
        meta = _.isObject(meta) ? meta : {};
        return !_.isUndefined(meta.panels) ? _.flatten(_.pluck(meta.panels, 'fields')) : [];
    },

    /**
     * @inheritdoc
     *
     * Calls {@link BaseDashletConsoleListView#_stopAutoRefresh} so that the refresh will
     * not continue after the view is disposed.
     *
     * @private
     */
    _dispose: function() {
        this._stopAutoRefresh();
        this._super('_dispose');
    },
})

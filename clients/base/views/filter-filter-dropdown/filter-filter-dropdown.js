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
 * View for the filter dropdown.
 *
 * Part of {@link View.Layouts.Base.FilterLayout}.
 *
 * @class View.Views.Base.FilterFilterDropdownView
 * @alias SUGAR.App.view.views.BaseFilterFilterDropdownView
 * @extends View.View
 */
({
    tagName: 'span',
    className: 'table-cell',

    events: {
        'click .choice-filter.choice-filter-clickable': 'handleEditFilter',
        'keydown .choice-filter.choice-filter-clickable': 'handleEditFilter',
        'click .choice-filter-close': 'handleClearFilter',
        'keydown .choice-filter-close': 'handleClearFilter',
    },

    /**
     * These labels are used in the filter dropdown
     *  - labelDropdownTitle        label used as the dropdown title. ie `Filter`
     *  - labelCreateNewFilter      label for create new filter action. ie `Create`
     *  - labelAllRecords           label used on record view when all related modules are selected. ie `All Records`
     *
     *  - labelAllRecordsFormatted  label used when all records are selected. ie `All <Module>s`
     *
     *                              It is set to null because already defined per module. However, some views are
     *                              allowed to override it because of the context. For instance, `dupecheck-list` view
     *                              wants to display `All duplicates` instead of `All <Module>s`
     */
    labelDropdownTitle: 'LBL_FILTER',
    labelCreateNewFilter: 'LBL_FILTER_CREATE_NEW',
    labelAllRecords: 'LBL_FILTER_ALL_RECORDS',
    labelAllRecordsFormatted: null,
    /**
     * @override
     * @param {Object} opts
     */
    initialize: function(opts) {
        app.view.View.prototype.initialize.call(this, opts);

        //Load partials
        this._select2formatSelectionTemplate = app.template.get('filter-filter-dropdown.selection-partial');
        this._select2formatResultTemplate = app.template.get('filter-filter-dropdown.result-partial');

        this.listenTo(this.layout, 'filter:select:filter', this.handleSelect);
        this.listenTo(this.layout, 'filter:change:module', this.handleModuleChange);
        this.listenTo(this.layout, 'filter:render:filter', this._renderHtml);
        this.listenTo(this.layout, 'filter:create:close', this.formatSelection);
        this.listenTo(this.context, 'filter:collapse', this.formatSelection);
    },

    /**
     * Truthy when filter dropdown is enabled.  Updated whenever the filter module changes.
     */
    filterDropdownEnabled: true,

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        if (!this.layout.filters) {
            return;
        }
        this._super('_renderHtml');
        this.getModelFilter();
        this.filterList = this.getFilterList();
        this._renderDropdown(this.filterList);
    },

    /**
     * Adds the actual filter to the dropdown
     *
     * @param {Object} model
     * */
    addModelFilterToLayoutFilters: function(model) {
        if (!_.isUndefined(this.layout) && !_.isEmpty(this.layout.filters)) {
            this.layout.filters.collection.add(model);
            this.layout.filters.collection.sort();
        }
        this.layout.selectFilter(model.id);
    },

    /**
     * When the filters collection not contains the model filter, fetch and add it to the collection
     */
    getModelFilter: function() {
        var modelFilterId = this.model && this.model.get('filter_id');

        if (modelFilterId) {
            var modelFilter = this.layout.filters.collection.findWhere({id: modelFilterId});

            if (!modelFilter) {
                var url = app.api.buildURL('Filters/' + modelFilterId, null, null);
                app.api.call('read', url, null, {
                    success: _.bind(this.addModelFilterToLayoutFilters, this),
                    error: function() {
                        app.logger.error('Filter can not be read, thus is not shared. Filter id: ' + modelFilterId);
                    }
                });
            }
        }
    },

    /**
     * Get the list of filters from layout.filters.collection for the dropdown
     *
     * @return {Array}
     * */
    getFilterList: function() {
        var filters = [];
        if (this.layout.canCreateFilter()) {
            filters.push({id: 'create', text: app.lang.get(this.labelCreateNewFilter)});
        }

        if (this.layout.filters && this.layout.filters.collection) {
            var allRecordsFilter = this.layout.filters.collection.get('all_records');
            if (allRecordsFilter && this.labelAllRecordsFormatted) {
                allRecordsFilter.set('name', this.labelAllRecordsFormatted);
                this.layout.filters.collection.sort();
            }
            // This flag is used to determine when we have to add the border top (to separate categories)
            var firstNonEditable = false;
            this.layout.filters.collection.each(function(model) {
                var creator = model.get('created_by');
                var filterApp = model.get('app');
                var isCreator = !creator || creator === app.user.get('id');
                var isPortal = app.controller.context.get('layout') === 'portaltheme-config' ||
                    app.config.platform === 'portal';
                // if filterApp is empty, show this filter in both portal and main app
                var isPortalFilter = filterApp === 'portal' || !filterApp;
                var isMainAppFilter = filterApp === 'base' || !filterApp;
                var isFilterValid = this._isFilterModelValid(model, false);

                // show only current user's filters in main app
                // show all portal filters in portal and portal config page
                if (((!isPortal && isMainAppFilter && isCreator) || (isPortal && isPortalFilter)) && isFilterValid) {
                    var opts = {
                        id: model.id,
                        text: this.layout.filters.collection._getTranslatedFilterName(model)
                    };
                    if (model.get('editable') === false && !firstNonEditable) {
                        opts.firstNonUserFilter = true;
                        firstNonEditable = true;
                    }
                    filters.push(opts);
                }
            }, this);
        }

        return filters;
    },

    /**
     * Validation checks against filter model
     *
     * @param {Data.Bean} model
     * @param {boolean} reset
     *
     * @private
     *
    * @return {boolean}
     */
    _isFilterModelValid: function(model, reset) {
        const hasDistanceFilter = !_.chain(model.get('filter_definition'))
            .pluck('$distance')
            .compact()
            .isEmpty()
            .value();

        if (!app.user.hasMapsLicense() && hasDistanceFilter) {
            if (reset) {
                this._fallbackToDefaultFilters();
            }

            return false;
        }

        return true;
    },

    /**
     * Reset filters to default
     *
     * @private
     */
    _fallbackToDefaultFilters: function() {
        this.layout.clearLastFilter(this.layout.layout.currentModule, this.layout.layoutType);
    },

    /**
     * Render select2 dropdown
     *
     * This function may be called even when this.render() is not because of
     * the "filter:render:filter" event listener.
     *
     * @private
     */
    _renderDropdown: function(data) {
        var self = this;
        this.filterNode = this.$('.search-filter');

        this.filterNode.select2({
            data: data,
            multiple: false,
            minimumResultsForSearch: 7,
            formatSelection: _.bind(this.formatSelection, this),
            formatResult: _.bind(this.formatResult, this),
            formatResultCssClass: _.bind(this.formatResultCssClass, this),
            dropdownCss: {width: 'auto'},
            dropdownCssClass: 'search-filter-dropdown',
            initSelection: _.bind(this.initSelection, this),
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
        });

        // the shortcut keys need to be registered anytime this function is
        // called, not just on render
        app.shortcuts.register({
            id: 'Filter:Create',
            keys: ['f c', 'mod+alt+8'],
            component: this,
            description: 'LBL_SHORTCUT_FILTER_CREATE',
            handler: function() {
                // trigger the change event to open the edit filter drawer
                this.filterNode.select2('val', 'create', true);
            }
        });
        app.shortcuts.register({
            id: 'Filter:Edit',
            keys: 'f e',
            component: this,
            description: 'LBL_SHORTCUT_FILTER_EDIT',
            handler: function() {
                this.$('.choice-filter.choice-filter-clickable').click();
            }
        });
        app.shortcuts.register({
            id: 'Filter:Show',
            keys: 'f m',
            component: this,
            description: 'LBL_SHORTCUT_FILTER_SHOW',
            handler: function() {
                this.filterNode.select2('open');
            }
        });

        if (!this.filterDropdownEnabled) {
            this.filterNode.select2('disable');
        }

        this.filterNode.off('change');
        this.filterNode.on('change',
            /**
             * Called when the user selects a filter in the dropdown
             *
             * @triggers filter:change:filter on filter layout to indicate a new
             *   filter has been selected.
             *
             * @param {Event} e The `change` event.
             */
            function(e) {
                self.layout.trigger('filter:change:filter', e.val);
            }
        );
    },

    /**
     * This handler is useful for other components that trigger
     * `filter:select:filter` in order to select the dropdown value.
     *
     * @param {String} id The id of the filter to select in the dropdown.
     */
    handleSelect: function(id) {
        this.filterNode.select2('val', id, true);
    },

    /**
     * Get the dropdown labels for the filter
     *
     * @param {Object} el
     * @param {Function} callback
     * */
    initSelection: function(el, callback) {
        var data;
        var model;
        var val = el.val();

        if (val === 'create') {
            //It should show `Create`
            data = {id: 'create', text: app.lang.get(this.labelCreateNewFilter)};

        } else {
            model = this.layout.filters.collection.get(val);

            //Fallback to `all_records` filter if not able to retrieve selected filter
            if (!model || !this._isFilterModelValid(model, true)) {
                data = {id: 'all_records', text: app.lang.get(this.labelAllRecords)};

            } else if (val === 'all_records') {
                data = this.formatAllRecordsFilter(null, model);
            } else {
                data = {id: model.id, text: this.layout.filters.collection._getTranslatedFilterName(model)};
            }
        }

        callback(data);
    },

    /**
     * Update the text for the selected filter and returns template
     *
     * @param {Object} item
     * @return {string}
     * */
    formatSelection: function(item) {
        var ctx = {};
        var safeString;
        var a11yLabel = app.lang.get('LBL_FILTER_CREATE_FILTER');
        var a11yTabindex = 0;

        //Don't remove this line. We want to update the selected filter name but don't want to change to the filter
        //name displayed in the dropdown
        item = _.clone(item);

        this.toggleFilterCursor(this.isFilterEditable(item.id));

        if (item.id === 'all_records') {
            item = this.formatAllRecordsFilter(item);
        }

        //Escape string to prevent XSS injection
        safeString = Handlebars.Utils.escapeExpression(item.text);
        if (item.id !== 'all_records') {
            if (this.isFilterEditable(item.id)) {
                a11yTabindex = 0;
                a11yLabel = app.lang.get('LBL_FILTER_EDIT_FILTER') + ' ' + safeString;
            } else {
                a11yTabindex = -1;
                a11yLabel = safeString + ' ' + app.lang.get('LBL_FILTER');
            }
        } else {
            a11yTabindex = this.isFilterEditable(item.id) ? 0 : -1;
        }

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
     * Returns template
     *
     * @param {Object} option
     * @return {string}
     * */
    formatResult: function(option) {
        if (option.id === this.layout.getLastFilter(this.layout.layout.currentModule, this.layout.layoutType)) {
            option.icon = 'sicon-check';
        } else if (option.id === 'create') {
            option.icon = 'sicon-plus';
        } else {
            option.icon = undefined;
        }
        return this._select2formatResultTemplate(option);
    },

    /**
     * Adds a class to the `Create Filter` item (to add border bottom)
     * and a class to first user custom filter (to add border top)
     *
     * @param {Object} item
     * @return {string} css class to attach
     */
    formatResultCssClass: function(item) {
        if (item.id === 'create') {return 'select2-result-border-bottom';}
        if (item.firstNonUserFilter) {return 'select2-result-border-top';}
    },

    /**
     * Determine if a filter is editable
     *
     * @param {String} id
     * @return {boolean} `true` if filter is editable, `false` otherwise
     */
    isFilterEditable: function(id) {
        if (!this.layout.canCreateFilter() || !this.filterDropdownEnabled || this.layout.showingActivities) {
            return false;
        }
        if (id === 'create' || id === 'all_records') {
            return true;
        } else {
            var filterModel = this.layout.filters.collection.get(id);
            if (filterModel) {
                var isEditable = filterModel.get('editable') !== false;
                var creator = filterModel.get('created_by');
                var hasOwnership = creator ? creator === app.user.get('id') : true;
                return isEditable && hasOwnership;
            } else {
                return true;
            }
        }
    },

    /**
     * Toggles cursor depending if the filter is editable or not.
     *
     * @param {boolean} active `true` for a pointer cursor, `false` for a not allowed cursor
     */
    toggleFilterCursor: function(editable) {
        this.$('.choice-filter')
            .css('cursor', editable ? 'pointer' : 'not-allowed')
            .toggleClass('choice-filter-clickable', editable);
    },

    /**
     * Formats label for `all_records` filter. When showing all subpanels, we expect `All records`
     *
     * @param {Object} item
     * @return {Object} item with formatted label
     */
    formatAllRecordsFilter: function(item, model) {
        item = item || {id: 'all_records'};

        //SP-1819: Seeing "All Leads" instead of "All Records" in sub panel
        //For the record view our Related means all subpanels (so should show `All Records`)
        var allRelatedModules = _.indexOf([this.module, 'all_modules'], this.layout.layout.currentModule) > -1;

        //If ability to create a filter
        if (this.isFilterEditable(item.id)) {
            item.text = app.lang.get(this.labelCreateNewFilter);
        } else if (this.layout.layoutType === 'record' && allRelatedModules) {
            item.text = app.lang.get(this.labelAllRecords);
            this.toggleFilterCursor(false);
        } else if (model) {
            item.text = this.layout.filters.collection._getTranslatedFilterName(model);
        }
        return item;
    },

    /**
     * Handler for when the user selects a filter in the filter bar,
     * or user clicks the filter button to create or edit.
     */
    handleEditFilter: function(evt) {
        if (evt && evt.type === 'keydown') {
            if (evt.keyCode !== $.ui.keyCode.SPACE && evt.keyCode !== $.ui.keyCode.ENTER) {
                return;
            }
            // Prevent scrolling page with space
            evt.preventDefault();
            evt.stopPropagation();
        }

        var filterId = this.filterNode.val();
        var filterModel;
        var a11yTabindex = 0;

        if (filterId === 'all_records') {
            // Figure out if we have an edit state.
            // This would mean user was editing the filter so we want him to retrieve
            // the filter form in the state he left it.
            this.layout.trigger('filter:select:filter', 'create');
            a11yTabindex = 0;
        } else {
            this.layout.trigger('filter:select:filter', filterId);
            filterModel = this.layout.filters.collection.get(filterId);
            a11yTabindex = -1;
        }

        if (filterModel && filterModel.get('editable') !== false) {
            this.layout.trigger('filter:create:open', filterModel);
            a11yTabindex = 0;
        }

        this.$('.choice-filter-label')
            .attr('aria-label', app.lang.get('LBL_FILTER_EDIT_FILTER'))
            .attr('tabindex', a11yTabindex);
    },

    /**
     * Handler for when the user selects a module in the filter bar.
     */
    handleModuleChange: function(linkModuleName, linkName) {
        this.filterDropdownEnabled = (linkName !== 'all_modules');
    },

    /**
     * When a click happens on the close icon, clear the last filter and trigger reinitialize
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
        this.layout.clearLastFilter(this.layout.layout.currentModule, this.layout.layoutType);
        var filterId;
        if (this.context.get('currentFilterId') === this.layout.filters.collection.defaultFilterFromMeta) {
            filterId = 'all_records';
        } else {
            filterId = this.layout.filters.collection.defaultFilterFromMeta;
        }
        this.layout.trigger('filter:select:filter', filterId);
    },

    /**
     * @override
     * @private
     */
    _dispose: function() {
        if (!_.isEmpty(this.filterNode)) {
            this.filterNode.select2('destroy');
        }
        app.view.View.prototype._dispose.call(this);
    }
});

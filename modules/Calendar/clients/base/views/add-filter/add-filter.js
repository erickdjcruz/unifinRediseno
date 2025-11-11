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
 * @class View.Views.Base.Calendar.AddFilterView
 * @alias SUGAR.App.view.views.BaseCalendarAddFilterView
 * @extends View.Views.Base.View
 */
({
    _moduleFilterList: [],
    _allModulesId: 'All',
    _selectedModule: null,
    _currentSearch: '',
    events: {
        'keyup .search-name': 'throttledSearch',
        'paste .search-name': 'throttledSearch',
        'click .add-on.sicon-close': 'clearInput'
    },

    /**
     * Converts the input field to a select2 field and adds the module filter for refining the search.
     *
     * @override
     */
    _render: function() {
        this._super('_render');
        this.buildModuleFilterList();
        this.buildFilter();
    },

    /**
     * Builds the list of allowed modules to provide the data to the select2 field.
     */
    buildModuleFilterList: function() {
        const allowedModules = this.collection.allowed_modules;

        this._moduleFilterList = [
            {id: this._allModulesId, text: app.lang.get('LBL_MODULE_ALL')}
        ];

        _.each(allowedModules, function(module) {
            this._moduleFilterList.push({
                id: module,
                text: app.lang.getModuleName(module, {plural: true})
            });
        }, this);
    },

    /**
     * Converts the input field to a select2 field and initializes the selected module.
     */
    buildFilter: function() {
        let $filter = this.getFilterField();
        if ($filter.length > 0) {
            $filter.select2({
                data: this._moduleFilterList,
                allowClear: false,
                multiple: false,
                minimumResultsForSearch: -1,
                formatSelection: _.bind(this.formatModuleSelection, this),
                formatResult: _.bind(this.formatModuleChoice, this),
                dropdownCss: {width: 'auto'},
                dropdownCssClass: 'search-filter-dropdown',
                initSelection: _.bind(this.initSelection, this),
                escapeMarkup: function(m) {
                    return m;
                },
                width: 'off'
            });

            $filter.off('change');
            $filter.on('change', _.bind(this.handleModuleSelection, this));
            this._selectedModule = this._selectedModule || this._allModulesId;
            $filter.select2('val', this._selectedModule);
        }
    },

    /**
     * Gets the filter DOM field.
     *
     * @return {jQuery} DOM Element
     */
    getFilterField: function() {
        return this.$('input.select2');
    },

    /**
     * Gets the module filter DOM field.
     *
     * @return {jQuery} DOM Element
     */
    getModuleFilter: function() {
        return this.$('span.choice-filter-label');
    },

    /**
     * Destroy the select2 plugin.
     */
    unbind: function() {
        let $filter = this.getFilterField();
        if ($filter.length > 0) {
            $filter.off();
            $filter.select2('destroy');
        }
        this._super('unbind');
    },

    /**
     * Performs a search once the user has entered a term.
     *
     * @param {Object} evt
     */
    throttledSearch: _.debounce(function(evt) {
        const newSearch = this.$(evt.currentTarget).val();
        if (this._currentSearch !== newSearch) {
            this._currentSearch = newSearch;
            this.applyFilter();
        }
    }, 400),

    /**
     * Initialize the module selection with the value for all modules.
     *
     * @param {jQuery} el
     * @param {Function} callback
     */
    initSelection: function(el, callback) {
        if (el.is(this.getFilterField())) {
            const module = _.findWhere(this._moduleFilterList, {id: el.val()});
            callback({id: module.id, text: module.text});
        }
    },

    /**
     * Format the selected module to display its name.
     *
     * @param {Object} item
     * @return {string}
     */
    formatModuleSelection: function(item) {
        // update the text for the selected module
        this.getModuleFilter().text(item.text);
        return '<span class=\'select2-choice-type\'>' +
            app.lang.get('LBL_MODULE') +
            '<i class=\'sicon sicon-caret-down\'></i></span>';
    },

    /**
     * Format the choices in the module select box.
     *
     * @param {Object} option
     * @return {string}
     */
    formatModuleChoice: function(option) {
        return '<div><span class=\'select2-match\'></span>' + option.text + '</div>';
    },

    /**
     * Handler for when the module filter dropdown value changes, either via a click or manually calling jQuery's
     * .trigger('change') event.
     *
     * @param {Object} evt jQuery Change Event Object
     * @param {string} overrideVal (optional) ID passed in when manually changing the filter dropdown value
     */
    handleModuleSelection: function(evt, overrideVal) {
        const module = overrideVal || evt.val || this._selectedModule || this._allModulesId;
        // only perform a search if the module is in the approved list
        if (!_.isEmpty(_.findWhere(this._moduleFilterList, {id: module}))) {
            this._selectedModule = module;
            this.getFilterField().select2('val', this._selectedModule);
            this.getModuleFilter().css('cursor', 'pointer');
            this.applyFilter();
        }
    },

    /**
     * Triggers an event that makes a call to search the user/team and filter the data set.
     */
    applyFilter: function() {
        const searchAllModules = (this._selectedModule === this._allModulesId);
        // pass an empty array when all modules are being searched
        const module = searchAllModules ? [] : [this._selectedModule];
        // determine if the filter is dirty so the 'clearQuickSearchIcon' can be added/removed appropriately
        const isDirty = !_.isEmpty(this._currentSearch);

        this._toggleClearQuickSearchIcon(isDirty);
        this.context.trigger('calendar:add:search', module, this._currentSearch);
    },

    /**
     * Append or remove an icon to the quicksearch input so the user can clear the search easily.
     *
     * @param {boolean} addIt TRUE if you want to add it, FALSE to remove
     */
    _toggleClearQuickSearchIcon: function(addIt) {
        if (addIt && !this.$('.add-on.sicon-close')[0]) {
            this.$('.filter-view.search').append('<i class=\'add-on sicon sicon-close\'></i>');
        } else if (!addIt) {
            this.$('.add-on.sicon-close').remove();
        }
    },

    /**
     * Clear input
     */
    clearInput: function() {
        let $filter = this.getFilterField();
        this._currentSearch = '';
        this._selectedModule = this._allModulesId;
        this.$('.search-name').val(this._currentSearch);
        if ($filter.length > 0) {
            $filter.select2('val', this._selectedModule);
        }
        this.applyFilter();
    }
});

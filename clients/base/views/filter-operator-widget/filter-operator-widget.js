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
 * @class View.Views.Base.HomeFilterOperatorWidget
 * @alias SUGAR.App.view.views.BaseFilterOperatorWidget
 * @extends View.View
 */
({
    plugins: ['FilterOperatorManager'],
    events: {
        'change [data-fieldname="runtime-qualifier"]': 'qualifierChanged',
        'change [data-fieldname="enum-single"]': 'enumSingleChanged',
        'change [data-fieldname="text"]': 'textChanged',
        'keyup [data-fieldname="text"]': 'textChanged',
        'change [data-fieldname="text-between-start"]': 'textChanged',
        'change [data-fieldname="text-between-end"]': 'textEndChanged',
        'change [data-fieldname="time-datetime"]': 'timeDatetimeChanged',
        'change [data-fieldname="time-datetime-start"]': 'timeDatetimeStartChanged',
        'change [data-fieldname="time-datetime-end"]': 'timeDatetimeEndChanged',
        'change [data-fieldname="select-single"]': 'selectSingleChanged',
        'change [data-fieldname="enum-multiple"]': 'enumMultipleChanged',
        'change [data-fieldname="select-multiple"]': 'selectMultipleChanged',
        'click input[name="select-all"]': 'selectAllClicked',
        'hide': 'handleHideDatePicker',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(true);
    },

    /**
     * Init properties
     *
     * @param {boolean} ignoreEvents
     */
    _initProperties: function(ignoreEvents) {
        const options = this.options;

        this._manager = options.manager || {};
        this._operators = options.operators || {};
        this._users = options.users || {};
        this._filterData = options.filterData || {};
        this._seedFieldDef = options.seedFieldDef || {};
        this._seedModule = options.seedModule || '';
        this._fieldType = options.fieldType || '';
        this._filterId = options.filterId || '';
        this._tooltipTitle = options.tooltipTitle || '';
        this._searchTerm = this._searchTerm || '';
        this._nbItemsSelected = this._nbItemsSelected || null;

        const optionsDOM = this._seedFieldDef.options;
        this._optionsList = this._optionsList || (optionsDOM ? app.lang.getAppListStrings(optionsDOM) : false);

        this._loading = false;
        this._lastItemClickedIdx = false;
        this._inputData = false;
        this._inputType = false;
        this._inputValue = false;
        this._inputValue1 = false;
        this._inputValue2 = false;
        this._inputValue3 = false;

        this.updateFilterInput();
        this._markSelectedItems();

        if (!ignoreEvents) {
            this.context.trigger('filter-operator-data-changed');
        }
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!_.isEmpty(this._searchTerm)) {
            this._setupSearchOptions();
        }

        this._super('_render');

        this.$('.runtime-filter-select').select2();

        this.$('.runtime-filter-select').off('select2-open');
        this.$('.runtime-filter-select').on('select2-open', (evt) => {
            this.context.trigger('dashboard-filter-group-selected', this._filterId);
        });

        this._createDatePicker();
        this.$('[data-type="time"]').timepicker();

        this.$('[data-type="date"]').click((evt) => {
            this.context.trigger('dashboard-filter-group-selected', this._filterId);
        });

        this._createSearchEngine();
        this._toggleLoadingScreen(this._loading);

        if (this._inputType === 'relate') {
            this.createTempRelateController();
        }

        this._markSelectedItems();
        this._manager.$('.runtime-filter-summary-text').html(this.getSummaryText());

        if (!_.isEmpty(this._searchTerm)) {
            const input = this.$('[data-fieldname="search-multiple"]');
            input.focus().val('').val(this._searchTerm);
        }
    },

    /**
    * Update the filter operator widget
    */
    updateFilterOperatorWidget: function() {
        if (this._manager && _.isFunction(this._manager.updateItemsCount)) {
            this._manager.updateItemsCount();
        }
    },

    /***
    * Get no of the selected values from multiselect
    *
    * @return {string}
    */
    getItemsCount: function() {
        if (!(this._inputType === 'enum-multiple' || this._inputType === 'select-multiple')) {
            return '';
        }

        let newCount = this._filterData.input_name0.length;
        newCount = newCount === 0 ?  '' : `(${newCount})`;

        return newCount;
    },

    /**
     * Handle update of qualifier
     *
     * @param {UIEvent} e
     */
    qualifierChanged: function(e) {
        this._filterData.qualifier_name = e.currentTarget.value;
        this._filterData.input_name0 = '';
        this._filterData.input_name1 = '';
        this._filterData.input_name2 = '';
        this._filterData.input_name3 = '';

        this._initProperties();
        this.render();
    },

    /**
     * Handle enum changed
     *
     * @param {UIEvent} e
     */
    enumSingleChanged: function(e) {
        this._filterData.input_name0 = [e.currentTarget.value];

        this._refreshWidget();
    },

    /**
     * Handle text changed
     *
     * @param {UIEvent} e
     */
    textChanged: function(e) {
        this._filterData.input_name0 = e.currentTarget.value;

        this._refreshWidget();
    },

    /**
     * Handle text end changed
     *
     * @param {UIEvent} e
     */
    textEndChanged: function(e) {
        this._filterData.input_name1 = e.currentTarget.value;

        this._refreshWidget();
    },

    /**
     * Handle time datetime changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeChanged: function(e) {
        this.changeFilterTime('input_name0', 'input_name1', e.currentTarget.value);
    },

    /**
     * Handle time datetime start changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeStartChanged: function(e) {
        this.changeFilterTime('input_name0', 'input_name1', e.currentTarget.value);
    },

    /**
     * Handle time datetime end changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeEndChanged: function(e) {
        this.changeFilterTime('input_name2', 'input_name3', e.currentTarget.value);
    },

    /**
     * Handle select single changed
     *
     * @param {UIEvent} e
     */
    selectSingleChanged: function(e) {
        this._filterData.input_name0 = [e.currentTarget.value];

        this._refreshWidget();
    },

    /**
     * Handle enum multiple changed
     *
     * @param {UIEvent} e
     */
    enumMultipleChanged: function(e) {
        const value = e.currentTarget.value;
        const insert = e.currentTarget.checked;

        if (!_.isArray(this._filterData.input_name0)) {
            this._filterData.input_name0 = [];
        }

        if (insert) {
            this._filterData.input_name0.push(value);
        } else {
            this._filterData.input_name0 = _.without(this._filterData.input_name0, value);

            if (_.isArray(this._filterData.input_name0) && this._filterData.input_name0.length === 0) {
                this._filterData.input_name0 = '';
            }
        }

        this._refreshWidget();
    },

    /**
     * Handle select multiple changed
     *
     * @param {UIEvent} e
     */
    selectMultipleChanged: function(e) {
        const value = e.currentTarget.value;
        const insert = e.currentTarget.checked;

        if (!_.isArray(this._filterData.input_name0)) {
            this._filterData.input_name0 = [];
        }

        if (insert) {
            this._filterData.input_name0.push(value);
        } else {
            this._filterData.input_name0 = _.without(this._filterData.input_name0, value);

            if (_.isArray(this._filterData.input_name0) && this._filterData.input_name0.length === 0) {
                this._filterData.input_name0 = '';
            }
        }

        this._refreshWidget();
    },

    /**
     * Date picker doesn't trigger a `change` event whenever the date value
     * changes we need to override this method and listen to the `hide` event.
     */
    handleHideDatePicker: function() {
        if (this._filterData.qualifier_name === 'between_dates') {
            this._betweenDatesChanged();
        } else if (this._filterData.qualifier_name === 'between_datetimes') {
            this._betweenDateTimesChanged();
        } else if (this._fieldType === 'datetimecombo') {
            this._dateDatetimeChanged();
        } else {
            this._dateChanged();
        }
    },

    /**
     * Create date picker widget
     */
    _createDatePicker: function() {
        const userDateFormat = app.user.getPreference('datepref');

        const options = {
            format: app.date.toDatepickerFormat(userDateFormat),
            weekStart: parseInt(app.user.getPreference('first_day_of_week'), 10),
        };

        let datePickerElements = this.$('[data-type="date"]').datepicker(options);
        if (datePickerElements.length > 0) {
            _.each(datePickerElements, function(datePickerElement) {
                const calendarObject = $.datepicker._getInst(datePickerElement);
                calendarObject.picker.addClass('reportFilter');
            });
        }
    },

    /**
     * Create engine for the search field
     */
    _createSearchEngine: function() {
        const delay = 100;

        this.$('[data-fieldname="search-multiple"]').on(
            'keyup',
            _.debounce(
                _.bind(this.searchMultipleOptions, this),
                delay
            )
        );

        if (_.contains(['enum-multiple', 'select-multiple'], this._inputType)) {
            this._setupSelectAll();
        }
    },

    /**
     * Select all handler
     *
     * @param {Event} evt
     */
    selectAllClicked: function(evt) {
        const selectAllChecked = _.isUndefined(this._lastIndeterminateState) ?
                                evt.currentTarget.checked :
                                this._lastIndeterminateState;

        this.$('[data-fieldname="enum-multiple"]').prop('checked', selectAllChecked);
        this.$('[data-fieldname="select-multiple"]').prop('checked', selectAllChecked);

        if (!this._filterData.input_name0) {
            this._filterData.input_name0 = [];
        }

        if (_.isEmpty(this._searchTerm)) {
            const inputData = _.chain(this._getInputData()).keys().filter((optionKey) => {
                return optionKey;
            }).value();

            this._filterData.input_name0 = selectAllChecked ? inputData : '';
        } else {
            const selectedInputData = _.keys(this._inputValue);
            const inputData = _.keys(this._inputValue1);

            this._filterData.input_name0 = selectAllChecked ?
                this._filterData.input_name0.concat(inputData) :
                this._filterData.input_name0.filter(option => !selectedInputData.includes(option));
        }

        this._refreshWidget();
    },

    /**
     * Marks selected items
     */
    _markSelectedItems: function() {
        _.each(this.$('.report-multiselect-options').find('input'), function(input) {
            if (this._filterData.input_name0.indexOf(input.value) === -1) {
                $(input).closest('.items-center').removeClass('item-selected');
            } else {
                $(input).closest('.items-center').addClass('item-selected');
            }
        }, this);
    },

    /**
     * Handle search param changed
     *
     * @param {UIEvent} e
     */
    searchMultipleOptions: function(e) {
        this._searchTerm = e.currentTarget.value;

        this._setupSearchOptions();
        this.render();

        const input = this.$('[data-fieldname="search-multiple"]');
        input.focus().val('').val(this._searchTerm);

        delete this._lastIndeterminateState;
    },

    /**
     * Only keep valid options
     */
    _setupSearchOptions: function() {
        this._inputValue = {};
        this._inputValue1 = {};

        const data = this._optionsList || this._users;

        _.each(this._filterData.input_name0, function getOptions(option) {
            const insensitiveSearchTerm = this._searchTerm.toLocaleLowerCase();
            const insensitiveOption = option.toLocaleLowerCase();
            if (!_.isEmpty(option) && (insensitiveOption.includes(insensitiveSearchTerm) ||
                (!_.isEmpty(data[option]) &&  data[option].toLocaleLowerCase().includes(insensitiveSearchTerm)))) {
                this._inputValue[option] = data[option];
            }
        }, this);

        _.each(data, function getOptions(option, key) {
            const insensitiveSearchTerm = this._searchTerm.toLocaleLowerCase();
            const insensitiveOption = option.toLocaleLowerCase();
            if (!_.has(this._inputValue, key) &&
                (insensitiveOption.includes(insensitiveSearchTerm) ||
                (!_.isEmpty(data[option]) &&  data[option].toLocaleLowerCase().includes(insensitiveSearchTerm)))) {
                this._inputValue1[key] = option;
            }
        }, this);
    },

    /**
     * Setup select all checkbox
     */
    _setupSelectAll: function() {
        const inputData = this._getInputData();

        const nrOfItems = _.filter(inputData, function(item) {
            return item !== '';
        }).length;

        const selectAllEl = this.$('input[name="select-all"]');

        if (_.isUndefined(this._filterData.input_name0)) {
            return;
        }

        if (this._filterData.input_name0.length === nrOfItems && nrOfItems !== 0) {
            delete this._lastIndeterminateState;

            selectAllEl.prop('indeterminate', false);
            selectAllEl.prop('checked', true);
        } else if (this._filterData.input_name0.length === 0) {
            delete this._lastIndeterminateState;

            selectAllEl.prop('indeterminate', false);
            selectAllEl.prop('checked', false);
        } else if (this._filterData.input_name0.length > 0) {
            this._lastIndeterminateState = true;
            selectAllEl.prop('indeterminate', true);
        }
    },

    /**
     * Get list of options used by the field
     *
     * @return {Mixed}
     */
    _getInputData: function() {
        return this._optionsList || this._users;
    },

    /**
     * Handle between dates changed
     */
    _betweenDatesChanged: function() {
        this._updateBetweenDateTimes({
            afterDateId: 'date-between-start',
            beforeDateId: 'date-between-end',
            afterDateKey: 'input_name0',
            beforeDateKey: 'input_name1',
        }, false);
    },

    /**
     * Handle between dates and times changed
     */
    _betweenDateTimesChanged: function() {
        this._updateBetweenDateTimes({
            afterDateId: 'date-datetime-start',
            beforeDateId: 'date-datetime-end',
            afterDateKey: 'input_name0',
            beforeDateKey: 'input_name2',
        }, true);
    },

    /**
     * Handle date datetime changed
     */
    _dateDatetimeChanged: function() {
        const dateEl = this.$('[data-fieldname="date-datetime"]');
        const val = dateEl.val();

        if (this.isValidDatePickerFormat(val)) {
            this._filterData.input_name0 = this.toFilterDate(val, true);

            dateEl.toggleClass('error', false);
            this._refreshWidget();
        } else {
            dateEl.toggleClass('error', true);
            this.showDatePrefMissmatchAlert();
        }
    },

    /**
     * Handle date changed
     */
    _dateChanged: function() {
        const dateEl = this.$('[data-fieldname="date"]');
        const date = dateEl.val();

        if (this.isValidDatePickerFormat(date)) {
            this._filterData.input_name0 = this.toFilterDate(date, false);

            dateEl.toggleClass('error', false);
            this._refreshWidget();
        } else {
            dateEl.toggleClass('error', true);
            this.showDatePrefMissmatchAlert();
        }
    },

    /**
     * Update both dates of a between operator
     *
     * @param {Object} datesMeta
     * @param {boolean} useTime
     */
    _updateBetweenDateTimes: function(datesMeta, useTime) {
        const afterEl = this.$(`[data-fieldname="${datesMeta.afterDateId}"]`);
        const beforeEl = this.$(`[data-fieldname="${datesMeta.beforeDateId}"]`);

        let after = afterEl.val();
        let before = beforeEl.val();
        let canRefreshWidget = true;

        if (after && this.isValidDatePickerFormat(after)) {
            this._filterData[datesMeta.afterDateKey] = this.toFilterDate(after, useTime);
            afterEl.toggleClass('error', false);
        } else if (after) {
            afterEl.toggleClass('error', true);
            canRefreshWidget = false;
            this.showDatePrefMissmatchAlert();
        }

        if (before && this.isValidDatePickerFormat(before)) {
            this._filterData[datesMeta.beforeDateKey] = this.toFilterDate(before, useTime, 'input_name3');
            beforeEl.toggleClass('error', false);
        } else if (before) {
            beforeEl.toggleClass('error', true);
            canRefreshWidget = false;
            this.showDatePrefMissmatchAlert();
        }

        if (canRefreshWidget) {
            this._refreshWidget();
        }
    },

    /**
     * Refresh UI
     */
    _refreshWidget: function() {
        this._initProperties();

        this._manager.$('.runtime-filter-summary-text').html(this.getSummaryText());
    },

    /**
     * Toggle loading screen
     *
     * @param {boolean} toggle
     */
    _toggleLoadingScreen: function(toggle) {
        this.$('[data-widget="filter-loading"]').toggleClass('hidden', !toggle);
        this.$('[data-container="main-filter-operator-container"]').toggleClass('hidden', toggle);
    },
});

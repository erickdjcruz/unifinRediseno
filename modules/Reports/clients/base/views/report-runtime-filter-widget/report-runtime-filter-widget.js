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
 * Action button configuration settings view
 *
 * @class View.Views.Base.AdministrationActionbuttonDisplaySettingsRecordView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonDisplaySettingsRecordView
 * @extends View.View
 */
({
    events: {
        'change [data-fieldname="runtime-qualifier"]': 'qualifierChanged',
        'change [data-fieldname="enum-single"]': 'enumSingleChanged',
        'change [data-fieldname="text"]': 'textChanged',
        'keyup [data-fieldname="text"]': 'textChanged',
        'change [data-fieldname="text-between-start"]': 'textStartChanged',
        'change [data-fieldname="text-between-end"]': 'textEndChanged',
        'change [data-fieldname="time-datetime"]': 'timeDatetimeChanged',
        'change [data-fieldname="time-datetime-start"]': 'timeDatetimeStartChanged',
        'change [data-fieldname="time-datetime-end"]': 'timeDatetimeEndChanged',
        'change [data-fieldname="select-single"]': 'selectSingleChanged',
        'change [data-fieldname="enum-multiple"]': 'enumMultipleChanged',
        'change [data-fieldname="select-multiple"]': 'selectMultipleChanged',
        'click [data-panelaction="toggleCollapse"]': 'toggleCollapse',
        'click .reports-runtime-widget-body': 'filterCollapse',
        'hide': 'handleHideDatePicker',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._reportData = options.reportData;
        this._filterData = options.filterData;
        this._runtimeFilterId = options.runtimeFilterId;
        this._users = this._reportData ? this._reportData.get('users') : [];
        this._isEnabled = true;

        if (!this._users) {
            this._users = options.users ? options.users : [];
        }
    },

    /**
     * Property initialization, nothing to do for this view
     *
     */
    _initProperties: function() {
        this._stayCollapsed = this.options.stayCollapsed;
        this._hideToolbar = this.options.hideToolbar;

        this._targetModule = this._getTargetModule();
        this._targetModuleLabel = this._getTargetModuleLabel();
        this._targetField = this._getTargetField();
        this._operators = this._getOperators();

        // ugly naming so we match the bwc reports naming(easier to read)
        this._tempRelateController = false;
        this._inputType = false;
        this._inputData = false;
        this._inputValue = false;
        this._inputValue1 = false;
        this._inputValue2 = false;
        this._inputValue3 = false;
        this._rendered = false;

        this._searchTerm = '';
        this._selectionType = {
            range: 'Range',
            caret: 'Caret',
        };

        this._updateFilterInput();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        if (!this._targetField) {
            this.$el.empty();

            app.alert.show('runtime-filter-no-permission', {
                level: 'warning',
                messages: app.lang.get('LBL_REPORT_FILTER_UNAVAILABLE', 'Reports'),
            });

            return;
        }

        // we need to take extra care of relate fields as we need to bypass acls
        // all users should be able to see the runtime filters in their entirety
        if (this._inputType === 'relate') {
            this._createTempRelateController();
        }

        this._rendered = true;

        this.$('.runtime-filter-select').select2();
        this.$('.runtime-filter-summary-text').html(this.getSummaryText());

        this._createDatePicker();
        this.$('[data-type="time"]').timepicker();

        const keydownTimer = 100;

        this.$('[data-fieldname="search-multiple"]').on(
            'keydown',
            _.debounce(
                _.bind(this.searchMultipleOptions, this),
                keydownTimer
            )
        );

        this.$('[data-tooltip="filter-summary"]').tooltip({
            delay: 1000,
            container: 'body',
            placement: 'bottom',
            title: _.bind(this._getTooltipText, this),
            html: true,
            trigger: 'hover',
        });

        if (this._stayCollapsed) {
            this.toggleCollapse();
        }

        if (this._isEnabled) {
            this.enableRuntimeFilter();
        } else {
            this.disableRuntimeFilter();
        }

        if (this._hideToolbar) {
            this.$('.dashlet-toolbar').empty();
        }

        if (this._inputType === 'enum-multiple' || this._inputType === 'select-multiple') {
            this.lastItemClickedIdx = false;
            this.$('.report-multiselect-options .flex-row').on('click',
                _.debounce(_.bind(this.listItemClicked, this), 0)
            );
            this.$('label input[name="select-all"]').on('click', _.debounce(_.bind(this.selectAllClicked, this), 0));
            this.$('.report-multiselect-options input').on('change',
                _.debounce(_.bind(this.updateItemsCount, this), 0)
            );

            this._setupSelectAll();

            this._markSelectedItems();

            this.updateItemsCount();
        }
    },

    /**
     * Setup select all checkbox
     */
    _setupSelectAll: function() {
        const inputData = this._getInputData();

        const nrOfItems = _.filter(inputData, function(item) {
            return item !== '';
        }).length;

        if (this._filterData.input_name0.length === nrOfItems && nrOfItems !== 0) {
            this.$('input[name="select-all"]').prop('indeterminate', false);
            this.$('input[name="select-all"]').prop('checked', true);
        } else if (this._filterData.input_name0.length === 0) {
            this.$('input[name="select-all"]').prop('indeterminate', false);
            this.$('input[name="select-all"]').prop('checked', false);
        } else if (this._filterData.input_name0.length > 0) {
            this.$('input[name="select-all"]').prop('indeterminate', true);
        }
    },

    /**
     * Get list of options used by the field
     *
     * @return {Mixed}
     */
    _getInputData: function() {
        let inputData;
        if (this._targetField.options || this._targetField.enumOptions) {
            inputData = this._inputData;
        } else {
            inputData = this._users;
        }

        return inputData;
    },

    /**
     * List item clicked
     *
     * @param {Event} evt
     */
    listItemClicked: function(evt) {
        this.itemClickedIdx = $(evt.target).closest('.flex-row').index();

        if (evt.target.tagName !== 'INPUT') {
            const clickEvent = $.Event('click');

            if (evt.shiftKey) {
                clickEvent.shiftKey = true;
            }
            if (evt.isRecursive) {
                clickEvent.isRecursive = true;
            }

            $(evt.target).find('input').trigger(clickEvent);
            return;
        }

        this._itemClicked(evt);

        if (!evt.isRecursive) {
            this.lastItemClickedIdx = this.itemClickedIdx;
        }
    },

    /**
     * Item clicked
     *
     * @param {Event} evt
     */
    _itemClicked: function(evt) {
        this._markSelectedItems();

        if (evt.isRecursive) {
            return;
        }

        if (evt.isSelectAllEvent) {
            this._setupSelectAll();
            return;
        }

        if (evt.isSelectAllEvent) {
            this._automaticallyClickItems(0, this.$('.report-multiselect-options input').length);
        } else if (this.lastItemClickedIdx !== this.itemClickedIdx && evt.shiftKey) {
            this._automaticallyClickItems(this.lastItemClickedIdx, this.itemClickedIdx);
        }

        this._setupSelectAll();
    },

    /**
     * Automatically click items
     *
     * @param {int} lastItemClickedIdx
     * @param {int} itemClickedIdx
     */
    _automaticallyClickItems: function(lastItemClickedIdx, itemClickedIdx) {
        let itemsToSelectStartIdx = lastItemClickedIdx;
        let itemsToSelectStopIdx = itemClickedIdx;

        if (lastItemClickedIdx > itemClickedIdx) {
            itemsToSelectStartIdx = itemClickedIdx;
            itemsToSelectStopIdx = lastItemClickedIdx;
        }

        let clickedCheckboxElement = this.$('.report-multiselect-options').find('input')[itemClickedIdx];

        for (let itemIdx = itemsToSelectStartIdx; itemIdx <= itemsToSelectStopIdx; itemIdx++) {
            let checkboxElement = this.$('.report-multiselect-options').find('input')[itemIdx];
            const clickEvent = $.Event('click');
            clickEvent.isRecursive = true;
            if ($(clickedCheckboxElement).is(':checked') && !$(checkboxElement).is(':checked')) {
                $(checkboxElement).trigger(clickEvent);
            } else if (!$(clickedCheckboxElement).is(':checked') && $(checkboxElement).is(':checked')) {
                $(checkboxElement).trigger(clickEvent);
            }
        }
    },

    /**
     * Select all handler
     *
     * @param {Event} evt
     */
    selectAllClicked: function(evt) {
        const selectAllChecked = evt.currentTarget.checked;

        const inputData = this._getInputData();

        if (selectAllChecked) {
            for (let itemIdx = 0; itemIdx < _.keys(inputData).length; itemIdx++) {
                let clickEvent = $.Event('click');
                clickEvent.isSelectAllEvent = true;

                let checkboxElement = this.$('.report-multiselect-options').find('input')[itemIdx];
                let itemKey = _.keys(inputData)[itemIdx];
                let itemValue = _.values(inputData)[itemIdx];

                if (!$(checkboxElement).is(':checked') && !(itemKey === '' && itemValue === '' &&
                    checkboxElement.value === '')) {
                    $(checkboxElement).trigger(clickEvent);
                }
            }
        } else {
            for (let itemIdx = 0; itemIdx < _.keys(inputData).length; itemIdx++) {
                let clickEvent = $.Event('click');
                clickEvent.isSelectAllEvent = true;

                let checkboxElement = this.$('.report-multiselect-options').find('input')[itemIdx];

                if ($(checkboxElement).is(':checked')) {
                    $(checkboxElement).trigger(clickEvent);
                }
            }
        }
    },

    /**
     * Update items count
     */
    updateItemsCount: function() {
        let newCount = this._filterData.input_name0.length;
        if (newCount === 0) {
            newCount = '';
        } else {
            newCount = `(${newCount})`;
        }

        this.$('.items-selected-nr').html(newCount);
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
     * Create a relate field controller
     */
    _createTempRelateController: function() {
        this._disposeTempRelateController();

        this._tempRelateController = app.view.createField({
            def: this._targetField,
            view: this,
            viewName: 'edit',
            model: this._inputValue,
        });

        // we want to bypass all acl rules
        // as on runtime filters the user should always be able to see the records
        this._tempRelateController._checkAccessToAction = function() {
            return true;
        };

        this._tempRelateController.render();

        this.$('[data-container="relate-input-container"]').append(this._tempRelateController.$el);
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
     * Handle collapse
     */
    toggleCollapse: function(e, forceCollapse) {
        const $el = this.$('.panel-toggle > i');
        const collapsed = forceCollapse || $el.is('.sicon-chevron-up');

        $el.toggleClass('sicon-chevron-down', collapsed);
        $el.toggleClass('sicon-chevron-up', !collapsed);

        this.$('[data-container="input-container"]').toggleClass('hide', collapsed);
        this.$('[data-container="operators-container"]').toggleClass('hide', collapsed);
        this.$el.toggleClass('collapsed-widget', collapsed);

        if (e) {
            e.stopPropagation();
        }
    },

    /**
     * Handle runtime filter collapse
     */
    filterCollapse: function(e) {
        const currentTarget = $(e.target);
        let shouldCollapse = currentTarget.children().attr('data-tooltip') === 'filter-summary' ||
                            currentTarget.closest('[data-tooltip="filter-summary"]').length ||
                            currentTarget.closest('.collapsed-widget').length;

        let selection = window.getSelection();

        if (shouldCollapse && selection.type !== this._selectionType.range) {
            this.toggleCollapse(e);
        }
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

        this._refreshWidget();
        this.render();
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
        } else if (this._targetField.type === 'datetimecombo') {
            this.dateDatetimeChanged();
        } else {
            this.dateChanged();
        }
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
     * Handle date changed
     */
    dateChanged: function() {
        const dateEl = this.$('[data-fieldname="date"]');
        const date = dateEl.val();

        if (this._isValidDatePickerFormat(date)) {
            this._filterData.input_name0 = this._toFilterDate(date, false);

            dateEl.toggleClass('error', false);
            this._refreshWidget();
        } else {
            dateEl.toggleClass('error', true);
            this._showDatePrefMissmatchAlert();
        }
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

        if (after && this._isValidDatePickerFormat(after)) {
            this._filterData[datesMeta.afterDateKey] = this._toFilterDate(after, useTime);
            afterEl.toggleClass('error', false);
        } else if (after) {
            afterEl.toggleClass('error', true);
            canRefreshWidget = false;
            this._showDatePrefMissmatchAlert();
        }

        if (before && this._isValidDatePickerFormat(before)) {
            this._filterData[datesMeta.beforeDateKey] = this._toFilterDate(before, useTime, 'input_name3');
            beforeEl.toggleClass('error', false);
        } else if (before) {
            beforeEl.toggleClass('error', true);
            canRefreshWidget = false;
            this._showDatePrefMissmatchAlert();
        }

        if (canRefreshWidget) {
            this._refreshWidget();
        }
    },

    /**
     * Handle date datetime changed
     */
    dateDatetimeChanged: function() {
        const dateEl = this.$('[data-fieldname="date-datetime"]');
        const val = dateEl.val();

        if (this._isValidDatePickerFormat(val)) {
            this._filterData.input_name0 = this._toFilterDate(val, true);

            dateEl.toggleClass('error', false);
            this._refreshWidget();
        } else {
            dateEl.toggleClass('error', true);
            this._showDatePrefMissmatchAlert();
        }
    },

    /**
     * Show date pref missmatch alert
     */
    _showDatePrefMissmatchAlert: function() {
        const dateFormatMapping = {
            'Y-m-d': 'YYYY-MM-DD',
            'm-d-Y': 'MM-DD-YYYY',
            'd-m-Y': 'DD-MM-YYYY',
            'Y/m/d': 'YYYY/MM/DD',
            'm/d/Y': 'MM/DD/YYYY',
            'd/m/Y': 'DD/MM/YYYY',
            'Y.m.d': 'YYYY.MM.DD',
            'd.m.Y': 'DD.MM.YYYY',
            'm.d.Y': 'MM.DD.YYYY',
        };

        let userFormat = app.user.getPreference('datepref');
        const displayFormat = dateFormatMapping[userFormat] || userFormat;

        app.alert.show('date-format-error', {
            level: 'warning',
            messages: app.lang.get('LBL_RUNTIME_FILTER_DATE_PREF_MISSMATCH') + displayFormat,
        });

        this.context.trigger('runtime:filter:broken');
    },

    /**
     * Handle time datetime changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeChanged: function(e) {
        this._changeFilterTime('input_name0', 'input_name1', e.currentTarget.value);
    },

    /**
     * Handle time datetime start changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeStartChanged: function(e) {
        this._changeFilterTime('input_name0', 'input_name1', e.currentTarget.value);
    },

    /**
     * Handle time datetime end changed
     *
     * @param {UIEvent} e
     */
    timeDatetimeEndChanged: function(e) {
        this._changeFilterTime('input_name2', 'input_name3', e.currentTarget.value);
    },

    /**
     * Update the time fragment of a datetime filter
     *
     * @param {string} dateKey
     * @param {string} timeKey
     * @param {string} newValue
     */
    _changeFilterTime: function(dateKey, timeKey, newValue) {
        const timeFragmentIdx = 1;
        const datetimeFragments = this._filterData[dateKey].split(' ');
        const timeFragment = datetimeFragments[timeFragmentIdx];

        const time = app.date(newValue, ['h:mma']).format('HH:mm:ss');

        this._filterData[dateKey] = this._filterData[dateKey].replace(timeFragment, time);
        this._filterData[timeKey] = newValue;

        this._refreshWidget();
    },

    /**
     * Handle text start changed
     *
     * @param {UIEvent} e
     */
    textStartChanged: function(e) {
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
     * Build tooltip description text
     *
     * @return {string}
     */
    _getTooltipText: function() {
        const tables = this._reportData.get('fullTableList');
        const targetTable = tables[this._filterData.table_key];
        const tableHierarchy = targetTable.name ? targetTable.name : app.lang.getModuleName(targetTable.value, {
            plural: true,
        });
        const fieldLabel = app.lang.get(this._targetField.vname, this._targetModule);
        const summaryText = this.getSummaryText();

        // tooltip text has to be html so we can meet the UX mocks
        const title = '<div class="runtime-filter-summary-tooltip">' +
                    tableHierarchy.replace(/\s\s+/g, ' ') +
                    ' ><b> ' + fieldLabel +
                    '</b><br>' +
                    summaryText +
                    '</div>';

        return title;
    },

    /**
     * Refresh UI
     */
    _refreshWidget: function() {
        this._initProperties();

        this._stayCollapsed = false;

        this.$('.runtime-filter-summary-text').html(this.getSummaryText());

        this.notifyRuntimeFilterChange();
    },

    /**
     * Notify every change the filter has supported
     */
    notifyRuntimeFilterChange: function() {
        this.context.trigger('runtime:filter:changed', {
            filterData: this._filterData,
            runtimeFilterId: this._runtimeFilterId,
        });
    },

    /**
     * Checks if the values are valid
     *
     * @return {boolean}
     */
    isValid: function() {
        const data = this._filterData;
        const fieldTypesRequiringDates = [
            'date',
            'date-between',
            'datetime-between',
            'after',
            'before',
            'on',
            'datetimecombo'
        ];

        if (_.contains(fieldTypesRequiringDates, this._inputType)) {
            let requiredValuesAreGiven;

            if (this._inputType === 'datetime-between') {
                requiredValuesAreGiven = data.input_name0 && data.input_name1 && data.input_name2 && data.input_name3;
            } else if (this._inputType === 'date-between' || this._inputType === 'datetimecombo') {
                requiredValuesAreGiven = data.input_name0 && data.input_name1;
            } else {
                requiredValuesAreGiven = !_.isEmpty(data.input_name0);
            }

            if (!requiredValuesAreGiven) {
                return false;
            }

            if (!_.isEmpty(data.input_name0) && !this._isValidDatePickerFormat(this._inputValue)) {
                return false;
            }

            if (!_.isEmpty(data.input_name1) &&
                (this._inputType === 'date-between' && !this._isValidDatePickerFormat(this._inputValue1)) ||
                (this._inputType === 'datetime-between' && !this._isValidTimePickerFormat(this._inputValue1))) {
                return false;
            }

            if (!_.isEmpty(data.input_name2) &&
                this._inputType !== 'datetimecombo' &&
                !this._isValidDatePickerFormat(this._inputValue2)) {
                return false;
            }

            if (!_.isEmpty(data.input_name3) &&
                (this._inputType === 'date-between' && !this._isValidDatePickerFormat(this._inputValue3)) ||
                (this._inputType === 'datetime-between' && !this._isValidTimePickerFormat(this._inputValue3))) {
                return false;
            }
        }

        if (!this._inputType || this._inputType === 'empty') {
            return true;
        }

        return (data.input_name0 || data.input_name1 || data.input_name2 || data.input_name3);
    },

    /**
     * Is valid date picker format
     *
     * @param {string} date
     *
     * @return {boolean}
     */
    _isValidDatePickerFormat: function(date) {
        const formattedDate = _.first(date.split(' '));
        const acceptedFormat = app.user.getPreference('datepref').toUpperCase();

        return moment(formattedDate, acceptedFormat, true).isValid();
    },

    /**
     * Is valid time picker format
     *
     * @param {string} time
     * @return {boolean}
     */
    _isValidTimePickerFormat: function(time) {
        const timeRegex = /^(?:(?:0?\d|1[0-2]):[0-5]\d)$/;
        const meridianLength = 2;
        const validTime = time.substring(0, time.length - meridianLength);

        return timeRegex.test(validTime);
    },

    /**
     * Disable the widget
     */
    disableRuntimeFilter: function() {
        this._isEnabled = false;

        this.$el.css({
            opacity: 0.5,
        });

        this.$('.reports-runtime-widget-body').css({
            'pointer-events': 'none',
        });

        this.$('.reports-runtime-widget').attr('rel', 'tooltip');

        this.$('[data-tooltip="runtime-filter-widget-container"]').tooltip({
            delay: 100,
            container: 'body',
            placement: 'bottom',
            title: 'This filter is already being used in a dashboard filter.',
            trigger: 'hover',
        });

        this.toggleCollapse(false, true);
    },

    /**
     * Enable the widget
     */
    enableRuntimeFilter: function() {
        this._isEnabled = true;

        this.$el.css({
            opacity: 1,
        });

        this.$('.reports-runtime-widget-body').css({
            'pointer-events': '',
        });

        this.$('.reports-runtime-widget').removeAttr('rel');
    },

    /**
     * Handle search param changed
     *
     * @param {UIEvent} e
     */
    searchMultipleOptions: function(e) {
        this._searchTerm = e.currentTarget.value;
        this._inputValue = {};
        this._inputValue1 = {};

        const data = (this._targetField.options || this._targetField.enumOptions) ? this._inputData : this._users;

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

        // we don't want the filter widget to collapse when we search for terms
        // so we keep the state, set it to false, and put the state back after render
        const stayCollapsed = this._stayCollapsed;
        this._stayCollapsed = false;

        this.render();

        this._stayCollapsed = stayCollapsed;

        const input = this.$('[data-fieldname="search-multiple"]');
        input.focus().val('').val(this._searchTerm);
    },

    /**
     * Handle relate field changed
     */
    relateChanged: function(model) {
        this._filterData.input_name0 = model.get(this._targetField.id_name);
        this._filterData.input_name1 = model.get(this._filterData.name);

        this._refreshWidget();
    },

    /**
     * Returns the filter's target module
     *
     * @return {Mixed}
     */
    _getTargetModule: function() {
        const tableKey = this._filterData ? this._filterData.table_key : false;
        const tables = this._reportData ? this._reportData.get('fullTableList') : false;
        const targetModule = tables ? tables[tableKey].module : false;

        return targetModule;
    },

    /**
     * Returns the filter's target module label
     *
     * @return {Mixed}
     */
    _getTargetModuleLabel: function() {
        const tableKey = this._filterData ? this._filterData.table_key : false;
        const tables = this._reportData ? this._reportData.get('fullTableList') : false;
        const targetModule = tables ? app.lang.getModuleName(tables[tableKey].label, {
            plural: true,
        }) : false;

        return targetModule;
    },

    /**
     * Returns the filter's target field
     *
     * @return {Mixed}
     */
    _getTargetField: function() {
        if (!this._targetModule) {
            return false;
        }

        if (this._targetField && this._targetField.type === 'enum' && this._targetField.function) {
            return this._targetField;
        }

        const targetField = app.utils.deepCopy(
            app.metadata.getField({
                module: this._targetModule,
                name: this._filterData.name,
            })
        );

        if (!targetField) {
            return false;
        }

        if (targetField.type === 'enum' && targetField.function) {
            const reportId = this.model.get('id');
            const url = app.api.buildURL('Reports/' + reportId + '/retrieveEnumFieldOptions');

            app.api.call('create', url, {
                targetField: targetField.name,
                targetModule: this._targetModule,
            }, {
                success: _.bind(this._updateTargetField, this),
            });
        }

        return targetField;
    },

    /**
     * Update the target field's options enum
     *
     * @param {Array} options
     */
    _updateTargetField: function(options) {
        if (this.disposed) {
            return;
        }

        options = _.extend({'': ''}, options);

        const qualifier = this._filterData ? this._filterData.qualifier_name : '';
        this._targetField.enumOptions = options;

        if (_.contains(['empty', 'not_empty'], qualifier)) {
            return;
        }

        this._updateEnumFilterData(qualifier);

        if (this._rendered) {
            this.render();
        }
    },

    /**
     * Returns all the operators available for this filter type
     *
     * @return {Mixed}
     */
    _getOperators: function() {
        let allOperators = this._reportData ? this._reportData.get('operators') : false;

        if (!allOperators) {
            allOperators = this.options.operators ? this.options.operators : false;
        }

        return allOperators ? allOperators[this._targetField.type] : false;
    },

    /**
     * Returns filter summary text
     *
     * @return {string}
     */
    getSummaryText: function() {
        let filterValue = this._filterData.input_name0;
        let filterValue2 = this._filterData.input_name1;
        const qualifierName = this._filterData.qualifier_name;

        if (_.isUndefined(filterValue) || filterValue === 'undefined') {
            filterValue = '';
        }

        if (_.isUndefined(filterValue2) || filterValue2 === 'undefined') {
            filterValue2 = '';
        }

        let prefixLabel = _.filter(this._operators, function getOperator(label, type) {
            return type === this._filterData.qualifier_name;
        }, this);

        let prefixEl = document.createElement('i');
        prefixEl.innerText = app.lang.get(_.first(prefixLabel), 'Reports');

        let prefix = prefixEl.outerHTML;

        if (qualifierName === filterValue) {
            return prefix;
        }

        if (_.isArray(filterValue)) {
            if (!this._targetField.options && !this._targetField.enumOptions) {
                const translatedValues = [];

                _.each(filterValue, function translateLabels(value) {
                    translatedValues.push(this._users[value] || value);
                }, this);

                filterValue = translatedValues;
            } else if (_.isString(this._targetField.options)) {
                const options = app.lang.getAppListStrings(this._targetField.options);

                filterValue = _.map(filterValue, function(value) {
                    return options[value];
                }, this);
            } else if (this._targetField.enumOptions) {
                let filterValueLabels = [];

                _.each(filterValue, function getLabels(filterValueKey) {
                    const filterValueLabel = this._targetField.enumOptions[filterValueKey];

                    if (filterValueLabel) {
                        filterValueLabels.push(filterValueLabel);
                    }
                }, this);

                filterValue = filterValueLabels;
            }

            return prefix + ' ' + _.escape(_.sortBy(filterValue).join(', '));
        }

        const fieldTypesRequiringDates = [
            'date',
            'date-between',
            'datetime-between',
            'after',
            'before',
            'on',
            'datetimecombo'
        ];

        if (_.contains(fieldTypesRequiringDates, this._inputType)) {
            filterValue = this._toDisplayDate(filterValue);
        }

        if (_.contains(['text-between', 'date-between'], this._inputType)) {
            return prefix + ' ' + _.escape(filterValue + ' ' + app.lang.get('LBL_AND') + ' ' + this._inputValue1);
        }

        if (_.contains(['datetimecombo'], this._inputType)) {
            return prefix + ' ' + _.escape(this._inputValue + ' ' + this._inputValue1);
        }

        if (_.contains(['datetime-between'], this._inputType)) {
            return prefix + ' ' + _.escape(this._inputValue + ' ' + this._inputValue1 + ' ' +
                app.lang.get('LBL_AND') + ' ' + this._inputValue2 + ' ' + this._inputValue3);
        }

        return prefix + ' ' + _.escape((this._inputType === 'relate' ? filterValue2 : filterValue));
    },

    /**
     * Setup proper input depending on qulifier type
     */
    _updateFilterInput: function() {
        const qualifierName = this._filterData ? this._filterData.qualifier_name : '';
        const fieldType = this._targetField ? this._targetField.type : '';

        this._inputValue = false;
        this._inputValue1 = false;
        this._inputValue2 = false;
        this._inputValue3 = false;

        // it is ugly, unfortunately we had to keep the logic of the bwc reports when deciding what input type to render
        if (qualifierName === 'anything') {
            this._inputType = false;
        } else if (qualifierName === 'between') {
            this._inputType = 'text-between';
            this._inputValue = this._filterData.input_name0;
            this._inputValue1 = this._filterData.input_name1;
        } else if (qualifierName === 'between_dates') {
            this._updateBetweenDatesFilterData();
        } else if (qualifierName === 'between_datetimes') {
            this._updateBetweenDatetimesFilterData();
        } else if (qualifierName.indexOf('_n_days') != -1) {
            this._inputType = 'text';
            this._inputValue = this._filterData.input_name0;
        } else if (_.contains(['empty', 'not_empty'], qualifierName)) {
            this._inputType = false;
        } else if (_.contains(['date', 'datetime'], fieldType)) {
            this._updateDatetimeFilterData(qualifierName);
        } else if (fieldType === 'datetimecombo') {
            this._updateDatetimecomboFilterData(qualifierName);
        } else if (_.contains(['id', 'name', 'fullname', 'relate'], fieldType)) {
            this._updateNameFilterData(qualifierName);
        } else if (_.contains(['username', 'assigned_user_name'], fieldType)) {
            this._updateUsernameFilterData(qualifierName);
        } else if (_.contains(
            ['enum', 'multienum', 'parent_type', 'radioenum', 'timeperiod', 'currency_id'],
            fieldType)
        ) {
            this._updateEnumFilterData(qualifierName);
        } else if (fieldType === 'bool') {
            this._inputType = 'enum-single';
            this._inputData = {
                yes: 'yes',
                no: 'no'
            };

            let _inputValue = _.first(this._filterData.input_name0);

            if (!_inputValue) {
                _inputValue = 'yes';

                this._filterData.input_name0 = [_inputValue];
            }

            this._inputValue = _inputValue;
        } else {
            this._inputType = 'text';
            this._inputValue = this._filterData ? this._filterData.input_name0 : '';
        }
    },

    /**
     * Update date filter data
     */
    _updateBetweenDatesFilterData: function() {
        this._inputType = 'date-between';
        this._inputValue = this._toDisplayDate(this._filterData.input_name0);
        this._inputValue1 = this._toDisplayDate(this._filterData.input_name1);
    },

    /**
     * Update datetime filter data
     */
    _updateBetweenDatetimesFilterData: function() {
        this._inputType = 'datetime-between';

        this._inputValue = this._toDisplayDate(this._filterData.input_name0);
        this._inputValue1 = this._filterData.input_name1;

        this._inputValue2 = this._toDisplayDate(this._filterData.input_name2);
        this._inputValue3 = this._filterData.input_name3;
    },

    /**
     * Update filter data
     *
     * @param {string} qualifierName
     */
    _updateDatetimeFilterData: function(qualifierName) {
        this._setDatetimeFilterData(qualifierName, 'date', false);
    },

    /**
     * Update filter data
     *
     * @param {string} qualifierName
     */
    _updateDatetimecomboFilterData: function(qualifierName) {
        this._setDatetimeFilterData(qualifierName, 'datetimecombo', true);
    },

    /**
     * Update input values of a date/datetime type
     *
     * @param {string} qualifierName
     * @param {string} dateType
     * @param {boolean} useTime
     */
    _setDatetimeFilterData: function(qualifierName, dateType, useTime) {
        if (qualifierName.indexOf('tp_') === 0) {
            this._inputType = 'empty';
        } else {
            this._inputType = dateType;
            this._inputValue = this._toDisplayDate(this._filterData.input_name0);

            if (useTime) {
                this._inputValue1 = this._filterData.input_name1;
            }
        }
    },

    /**
     * Transform user date into filter date
     *
     * @param {string} userDate
     * @param {boolean} useTime
     * @param {string} timeKey
     *
     * @return {string}
     */
    _toFilterDate: function(userDate, useTime, timeKey) {
        timeKey = timeKey || 'input_name1';

        const userDatePref = app.user.getPreference('datepref').toUpperCase();
        const unformattedDateTime = `${userDate} ${this._filterData[timeKey]}`;
        const tempFormat = `${userDatePref} h:mma`;

        const date = moment(unformattedDateTime, tempFormat);

        return useTime ? date.format('YYYY-MM-DD HH:mm:ss') : date.format('YYYY-MM-DD');
    },

    /**
     * Transform filter date into user date
     *
     * @param {string} filterDate
     *
     * @return {string}
     */
    _toDisplayDate(filterDate) {
        let dateFragments = filterDate.match(/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/);

        const yearIdx = 1;
        const monthIdx = 2;
        const dayIdx = 3;

        if (!dateFragments) {
            return filterDate;
        }

        let displayDate = app.user.getPreference('datepref');

        displayDate = displayDate.replace('Y', dateFragments[yearIdx]);
        displayDate = displayDate.replace('m', dateFragments[monthIdx]);
        displayDate = displayDate.replace('d', dateFragments[dayIdx]);

        return displayDate;
    },

    /**
     * Update filter data
     *
     * @param {string} qualifierName
     */
    _updateNameFilterData: function(qualifierName) {
        if (_.contains(['is', 'is_not'], qualifierName)) {
            this._inputType = 'relate';

            this._targetField.type = 'relate';
            this._targetField.id_name = 'id_relate_reports';
            this._targetField.module = this._targetField.ext2 ? this._targetField.ext2 : this._targetModule;

            this._inputValue = app.data.createBean(this._targetField.module);

            this._inputValue.set(this._targetField.id_name, this._filterData.input_name0);
            this._inputValue.set(this._targetField.name, this._filterData.input_name1);

            this.listenTo(this._inputValue, 'change:' + this._targetField.name, this.relateChanged, this);
        } else {
            this._inputType = 'text';
            this._inputValue = this._filterData.input_name0;
        }
    },

    /**
     * Update filter data
     *
     * @param {string} qualifierName
     */
    _updateUsernameFilterData: function(qualifierName) {
        if (_.contains(['one_of', 'not_one_of'], qualifierName)) {
            this._inputType = 'select-multiple';

            this._inputValue = {};
            this._inputValue1 = {};

            _.each(this._filterData.input_name0, function getOptions(option) {
                this._inputValue[option] = this._users[option];
            }, this);

            _.each(this._users, function getOptions(option, key) {
                if (!_.has(this._inputValue, key)) {
                    this._inputValue1[key] = option;
                }
            }, this);
        } else {
            this._inputType = 'select-single';
            this._inputValue = _.first(this._filterData.input_name0);
        }
    },

    /**
     * Update filter data
     *
     * @param {string} qualifierName
     */
    _updateEnumFilterData: function(qualifierName) {
        const optionsList = this._targetField.options;
        this._inputData = app.lang.getAppListStrings(optionsList);

        if (!_.isEmpty(this._targetField.enumOptions)) {
            this._inputData = this._targetField.enumOptions;
        }

        if (!_.isEmpty(this._inputData)) {
            delete this._inputData[''];
        }

        if (qualifierName === 'anything') {
            this.inputData = [];
            return;
        }

        if (_.contains(['one_of', 'not_one_of'], qualifierName)) {
            this._inputType = 'enum-multiple';

            this._inputValue = {};
            this._inputValue1 = {};

            _.each(this._filterData.input_name0, function getOptions(option) {
                this._inputValue[option] = this._inputData[option];
            }, this);

            _.each(this._inputData, function getOptions(option, key) {
                if (!_.has(this._inputValue, key) && option) {
                    this._inputValue1[key] = option;
                }
            }, this);
        } else {
            this._inputType = 'enum-single';

            const choices = this._filterData.input_name0;
            this._inputValue = _.first(choices);

            if (!this._inputValue) {
                const firstEnumChoice = _.chain(this._inputData).keys().first().value();

                this._inputValue = firstEnumChoice;
                this._filterData.input_name0 = [this._inputValue];
            }
        }
    },

    /**
     * Dispose the relate field controller
     */
    _disposeTempRelateController: function() {
        if (this._tempRelateController) {
            this._tempRelateController.dispose();

            this._tempRelateController = false;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeTempRelateController();

        this._super('_dispose');
    },
});

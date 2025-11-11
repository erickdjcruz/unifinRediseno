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
 * @class View.Fields.Base.Reports.VisibilityWidgetField
 * @alias SUGAR.App.view.fields.BaseReportsVisibilityWidgetField
 * @extends View.Views.Base.Field
 */
({
    events: {
        'click [data-action="change-visibility"]': 'changeVisibility',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit();
        this._super('initialize', [options]);
        this._registerEvents();
    },

    /**
     * Before init properties handling
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this.FILTERS = 'filters';
        this.TABLE = 'table';
        this.CHART = 'chart';

        this.SCREENS_MAPPING = {
            chart: 'firstScreen',
            table: 'secondScreen',
        };
        this.SCREENS_DEPENDENCY = {
            table: this.CHART,
            chart: this.TABLE,
        };

        this._canDisplayTable = true;
        this._canDisplayFilters = true;
        this._numberOfFilters = 0;

        this._widgetsVisibility = {
            filters: {
                onScreen: false,
                interactable: false,
            },
            table: {
                onScreen: false,
                interactable: false,
            },
            chart: {
                onScreen: false,
                interactable: false,
            },
        };
    },

    /**
     * Register related events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'report-layout-config-retrieved', this.setVisibilityState);
        this.listenTo(this.context, 'split-screens-orientation-change', this.setVisibilityState);
        this.listenTo(this.context, 'filters-container-content-loaded', this.setVisibilityState);
    },

    /**
     * Update Widgets Visibility
     */
    _updateWidgetsVisibility: function() {
        _.each(this._widgetsVisibility, function updateVisibility(visibility, widgetId) {
            const widgetEl = this.$(`#${widgetId}`);
            const hasContainerFn = `_has${app.utils.capitalize(widgetId)}`;
            const canShowContainer = this[hasContainerFn]();

            widgetEl.toggleClass('active', visibility.onScreen);
            widgetEl.toggleClass('disabled', !visibility.interactable || !canShowContainer);
            widgetEl.prop('disabled', !visibility.interactable || !canShowContainer);

            this._updateTooltipButton(widgetEl);

        }, this);

        this._updateNumberOfFilters();
    },

    /**
     * Update the displayed number of filters
     */
    _updateNumberOfFilters: function() {
        const formattedFiltersNumber = this._getNumberOfFiltersToDisplay();
        const filtersBadgeEl = this.$('[data-container="filters-badge"]');

        if (formattedFiltersNumber > 0 || _.isString(formattedFiltersNumber)) {
            filtersBadgeEl.toggleClass('hidden', false);
            filtersBadgeEl.text(formattedFiltersNumber);
        }
    },

    /**
     * Update tooltip for disabled buttons
     *
     * @param {Object} widgetEl
     */
    _updateTooltipButton: function(widgetEl) {
        if (this.model.get('report_type') === 'tabular') {
            this._updateTooltipButtonsForTabular();
            return;
        }

        if (widgetEl.is(':disabled')) {
            widgetEl.css('pointer-events', 'none');
            widgetEl.parent().attr('data-original-title', app.lang.get('LBL_ONE_VIEW_REQUIRED', this.module));
            widgetEl.parent().css('cursor', 'no-drop');
        } else {
            widgetEl.css('pointer-events', 'unset');
            widgetEl.parent().css('cursor', 'pointer');
            widgetEl.parent().attr('data-original-title', widgetEl.data('originalTitle'));
        }
    },

    /**
     * Update tooltip buttons for rows and columns
     *
     * Rows and columns report don't have charts so we need to update tooltips accordingly
     *
     */
    _updateTooltipButtonsForTabular: function() {
        let chartEl = this.$('#chart');
        let tableEl = this.$('#table');

        tableEl.css('pointer-events', 'none');
        chartEl.css('pointer-events', 'none');
        chartEl.parent().attr('data-original-title', app.lang.get('LBL_RC_NO_CHART', this.module));
        chartEl.parent().css('cursor', 'no-drop');
    },

    /**
     * Get a formatted display number
     *
     * @return {string}
     */
    _getNumberOfFiltersToDisplay: function() {
        const maxNumberOfFiltersDisplayed = 9;
        const maxNumberOfFiltersLabel = '9+';
        let formattedFiltersNumber = this._numberOfFilters;

        if (this._numberOfFilters > maxNumberOfFiltersDisplayed) {
            formattedFiltersNumber = maxNumberOfFiltersLabel;
        }

        return formattedFiltersNumber;
    },

    /**
     * Update the visibility State
     *
     * @param {string} widgetId
     * @param {Object} visibility
     */
    _updateVisibilityState: function(widgetId, visibility) {
        this._widgetsVisibility[widgetId] = _.extend({}, this._widgetsVisibility[widgetId], visibility);

        if (widgetId === this.FILTERS) {
            this.context.set('filtersActive', visibility);
        }

        const dependentScreenId = this.SCREENS_DEPENDENCY[widgetId];

        if (dependentScreenId) {
            this._widgetsVisibility[dependentScreenId].interactable = visibility.onScreen;
        }

        this._updateWidgetsVisibility();
    },

    /**
     * Check if chart is available
     *
     * @return {boolean}
     */
    _hasChart: function() {
        return this.model.get('chart_type') !== 'none' &&
            this.model.get('report_type') !== 'tabular';
    },

    /**
     * Check if table is available
     *
     * @return {boolean}
     */
    _hasTable: function() {
        return this._canDisplayTable;
    },

    /**
     * Check if filters is available
     *
     * @return {boolean}
     */
    _hasFilters: function() {
        return this._canDisplayFilters;
    },

    /**
     * Convert config to match resizable split screens config
     *
     * @return {Object}
     */
    _getConvertedConfig: function() {
        let hidden = false;

        if (!this._widgetsVisibility[this.TABLE].onScreen) {
            hidden = this.SCREENS_MAPPING[this.TABLE];
        }

        if (!this._widgetsVisibility[this.CHART].onScreen) {
            hidden = this.SCREENS_MAPPING[this.CHART];
        }

        const config = {
            hidden,
            firstScreenRatio: '50%',
            filtersActive: this._widgetsVisibility[this.FILTERS].onScreen,
            direction: this.context.get('resizeConfig') ? this.context.get('resizeConfig').direction : '',
        };

        return config;
    },

    /**
     * Change widget buttons visibility
     *
     * @param {jQuery} e
     */
    changeVisibility: function(e) {
        this._updateVisibilityState(e.currentTarget.id, {
            onScreen: !this._widgetsVisibility[e.currentTarget.id].onScreen,
        });

        const config = this._getConvertedConfig();

        this.context.trigger('split-screens-config-change', config, true);
        this.context.trigger('split-screens-visibility-change', config);
        this.context.trigger('split-screens-resized', config);
        this.context.trigger('orientation-visibility-change', config.hidden === false);
        this.context.trigger('container-resizing');
    },

    /**
     * Set the visibility state
     *
     * @param {Object} options
     */
    setVisibilityState: function(options) {
        const wV = this._widgetsVisibility;
        const config = _.extend({}, this.context.get('resizeConfig'), options);

        this._numberOfFilters = config.numberOfFilters || this._numberOfFilters || 0;

        let filtersOnScreen = config.filtersActive || false;
        if (_.isUndefined(config.filtersActive) && this._numberOfFilters > 0) {
            filtersOnScreen = true;
        }
        this.context.set('filtersActive', filtersOnScreen);

        const tableOnScreen = config.hidden !== this.SCREENS_MAPPING[this.TABLE] || wV.table.onScreen;
        const chartOnScreen = config.hidden !== this.SCREENS_MAPPING[this.CHART] || wV.chart.onScreen;

        this._widgetsVisibility = {
            filters: {
                onScreen: filtersOnScreen,
                interactable: this._hasFilters(),
            },
            table: {
                onScreen: tableOnScreen || !this._hasChart(),
                interactable: this._hasChart() && chartOnScreen,
            },
            chart: {
                onScreen: this._hasChart() && chartOnScreen,
                interactable: this._hasChart() && tableOnScreen,
            },
        };

        this._updateWidgetsVisibility();
    },
})

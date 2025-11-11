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
 * @class View.Views.Base.Reports.ReportFiltersToolbarView
 * @alias SUGAR.App.view.views.BaseReportsReportFiltersToolbarView
 * @extends View.Views.Base.Reports.ReportPanelToolbarView
 */
({
    extendsFrom: 'ReportsReportPanelToolbarView',

    className: 'dashlet-header flex flex-row items-center m-0.75',

    events: {
        'click [data-panelaction="toggleAdvancedFilters"]': 'toggleAdvancedFilters',
    },

    /**
     * @inheritdoc
     */
    _registerEvents: function() {
        this._super('_registerEvents');

        this.listenTo(this.context, 'button:reset:filters:click', this.resetToDefault, this);
        this.listenTo(this.context, 'button:copy:filters:click', this.copyFilters, this);
    },

    /**
     * Render
     *
     * Update button label
     */
    _render: function() {
        this._super('_render');
    },

    /**
     * Reset filters to default
     */
    resetToDefault: function() {
        this.context.trigger('reset:to:default:filters');
    },

    /**
     * Copy filters def to clipboard
     */
    copyFilters: function() {
        this.context.trigger('copy:filters:to:clipboard');
    },

    /**
     * Show advanced filters
     */
    toggleAdvancedFilters: function() {
        this.context.trigger('toggle:advanced:filters');
    },
})

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
 * @class View.Views.Base.Reports.ReportPanelToolbarView
 * @alias SUGAR.App.view.views.BaseReportsReportPanelToolbarView
 * @extends View.View
 */
 ({
    events: {
        'click .toggleGrooups': 'toggleGroups',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._isDetail = !this.context.get('previewMode');
        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initialize helper data
     */
    _initProperties: function() {
        this.collectionCount = null;

        if (_.isFunction(this.layout.getTitle)) {
            this.meta.label = this.layout.getTitle();
        }
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'report:panel-toolbar-visibility', this.setVisibility);
        this.listenTo(this.context, 'report:set-header-visibility', this.setHeaderVisibility);
    },

    /**
     * Handle the children visiblity
     *
     * @param {bool} isVisible
     */
    setVisibility: function(isVisible) {
        const child = this.$el.children();

        if (child.length !== 1) {
            return;
        }

        isVisible ? child.removeClass('hidden') : child.addClass('hidden');
    },

    /**
     * Used in summary tables to collapse/expand groups
     *
     * @param {Event} evt
     */
    toggleGroups: function(evt) {
        const shouldCollapse = _.contains(evt.target.classList, 'groups-collapse');
        const collapseLabel = app.lang.get('LBL_COLLAPSE_ALL', 'Reports');
        const expandLabel = app.lang.get('LBL_EXPAND_ALL', 'Reports');

        if (shouldCollapse) {
            this.$(evt.target).removeClass('groups-collapse');
            this.$(evt.currentTarget).text(expandLabel);
            this._showTables(false);
        } else {
            this.$(evt.target).addClass('groups-collapse');
            this.$(evt.currentTarget).text(collapseLabel);
            this._showTables(true);
        }
    },

    /**
     * Show/Hide the tables in a summation with details report
     *
     * @param {bool} shouldShow
     */
    _showTables: function(shouldShow) {
        const show = true;

        let reportComplexity = 0;
        let reportComplexities = [];

        if (this.context && this.context.get) {
            reportComplexities = this.context.get('reportComplexities');
            reportComplexity = this.context.get('reportComplexity');
        }

        let closestEl = 'table';
        let elementsToModify = [
            '.subgroup',
            'tbody',
        ];

        if (reportComplexity === reportComplexities.medium) {
            return this._toggleSimplifiedGroup(shouldShow);
        }

        let tables = this.layout.getComponent('report-table').$el.find(closestEl);

        const setElementsVisibility = function setVisibility(table, show) {
            _.each(elementsToModify, function each(element) {
                const targetEl = $(table).find(element);

                show ? targetEl.show() : targetEl.hide();
            });
        };

        for (const table of tables) {
            if (shouldShow) {
                setElementsVisibility(table, show);
                $(table).find('.sicon-arrow-left-double').switchClass('down', 'up');
            } else {
                setElementsVisibility(table, !show);
                $(table).find('.sicon-arrow-left-double').switchClass('up', 'down');
            }
        }
    },

    /**
     * Show/Hide the tables in a summation with details report for simplified group
     *
     * @param {bool} shouldShow
     */
    _toggleSimplifiedGroup: function(shouldShow) {
        const reportTable = this.layout.getComponent('report-table');
        const groupBody = reportTable.$el.find('[data-table="group-body"]');

        shouldShow ? groupBody.show() : groupBody.hide();

        const icons = reportTable.$el.find('.sicon-arrow-left-double');

        shouldShow ? icons.switchClass('down', 'up') : icons.switchClass('up', 'down');
    },

    /**
     * Hide/Show header bar
     *
     * @param {boolean} show
     */
    setHeaderVisibility: function(show) {
        // do not hide filters toolbar
        if (this.name === 'report-filters-toolbar') {
            return;
        }
        this.$el.toggleClass('hidden', show);
    },
})


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
 * @class View.Views.Base.Reports.ReportAdvancedFiltersView
 * @alias SUGAR.App.view.views.BaseReportsReportAdvancedFiltersView
 * @extends View.Views.Base.View
 */
 ({
    className: 'advanced-filters-container w-full h-full m-4',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Init properties
     */
    _initProperties: function() {
        this._flattenedFilters = {};
        this._filtersData = {};

        const elementId = app.utils.generateUUID();
        const tooltipId = app.utils.generateUUID();
        const reportData = this.context.get('reportData');
        const filtersData = reportData.get('filtersDef');

        if (_.isEmpty(filtersData)) {
            return;
        }

        this._hasFlattenFilters = true;

        this._flattenFilters(elementId, tooltipId, filtersData.Filter_1, this._flattenedFilters, 0, 5);

        this._flattenedFilters = _.sortBy(this._flattenedFilters, 'row');
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._createLines();
        this._recalculateContainerHeight();

        this.$('[data-widget="non-runtime-filter"]').tooltip({
            title: app.lang.get('LBL_ORIGINAL_DESIGN_FILTER'),
        });

        this._hideDrawerTabs();
    },

    /**
     * Create lines between linked elements
     */
    _createLines: function() {
        _.each(this._flattenedFilters, function createLine(widget) {
            let posY = false;
            let posX = false;

            _.each(widget.children, function buildLine(childId) {
                const color = widget.value === 'LBL_OR' ? '#ffd132' : '#00e0e0';
                const elWidget = this.$('#' + widget.elementId);

                const data = this._generateLineHtml(elWidget, this.$('#' + childId), 2, color, posY);

                this.$('[data-container="advanced-filters-widget-container"]').append(data.html);

                posX = data.posX;
                posY = data.posY;
            }, this);

            if (widget.type === 'operator' && widget.children.length > 1) {
                const pillHtml = this._generatePillHtml(widget, posX);
                this.$('[data-container="advanced-filters-widget-container"]').append(pillHtml);
            }

            if (widget.type === 'condition') {
                this.$(`#${widget.tooltipId}`).tooltip({
                    delay: 1000,
                    container: 'body',
                    placement: 'bottom',
                    title: widget.tooltipDescription,
                    html: true,
                    trigger: 'hover',
                });
            }
        }, this);
    },

    /**
     * Generate html code for the operator pill
     *
     * @param {Object} widget
     * @param {number} posX
     *
     * @return {string}
     */
    _generatePillHtml: function(widget, posX) {
        const pillWidth = 40;

        const firstChildId = _.first(widget.children);
        const lastChildId = _.last(widget.children);

        const firstChildTop = this.$('#' + firstChildId).position().top;
        const lastChildTop = this.$('#' + lastChildId).position().top;
        const parentTop = this.$('#' + widget.elementId).position().top;

        posX = posX - pillWidth / 2;

        const posY = (lastChildTop - parentTop) / 2 + firstChildTop;

        let pillEl = document.createElement('div');

        pillEl.style.left = `${posX}px`;
        pillEl.style.top = `${posY}px`;

        pillEl.innerText = app.lang.get(widget.value);
        pillEl.className = widget.operator === 'OR' ? 'advanced-operator-pill-or' : 'advanced-operator-pill-and';

        return pillEl.outerHTML;
    },

    /**
     * Recalculate container height base on panel height
     */
    _recalculateContainerHeight: function() {
        const offset = 50;
        const containerHeight = this.$('[data-container="advanced-container"]').height();

        this.$('[data-container="advanced-filters-widget-container"]').height(containerHeight - offset);
    },

    /**
     * Recursively parse filters and store them into a one dimensional object
     *
     * @param {string} elementId
     * @param {string} tooltipId
     * @param {Object} filterDefs
     * @param {Object} flattenedFilters
     * @param {number} row
     * @param {number} column
     *
     * @return {number}
     */
    _flattenFilters: function(elementId, tooltipId, filterDefs, flattenedFilters, row, column) {
        const marginTopOffset = 5;
        const marginLeftOffset = 15;

        if (filterDefs.operator) {
            const isOROperator = filterDefs.operator === 'OR';

            flattenedFilters[elementId] = {
                type: 'operator',
                operator: filterDefs.operator,
                value: isOROperator ? 'LBL_OR' : 'LBL_AND_UPPERCASE',
                description: isOROperator ? 'LBL_ADVANCED_OR_DESC' : 'LBL_ADVANCED_AND_DESC',
                startClass: isOROperator ? 'advanced-or-start' : 'advanced-and-start',
                endClass: isOROperator ? 'advanced-or-end' : 'advanced-and-end',
                children: [],
                elementId,
                tooltipId,
                row,
                column,
            };

            row = row + marginTopOffset;
            column = column + marginLeftOffset;

            _.each(filterDefs, function flatten(subFilterDefs, key) {
                if (key === 'operator') {
                    return;
                }

                const subElementId = app.utils.generateUUID();
                const subElementTooltipId = app.utils.generateUUID();

                row = this._flattenFilters(
                    subElementId,
                    subElementTooltipId,
                    subFilterDefs,
                    flattenedFilters,
                    row,
                    column
                );

                flattenedFilters[elementId].children.push(subElementId);
            }, this);
        } else {
            const filterDescription = this._getRuntimeFilterDescription(filterDefs);

            flattenedFilters[elementId] = {
                type: 'condition',
                value: filterDescription.summaryText,
                tooltipDescription: filterDescription.tooltipDescription,
                runtime: filterDefs.runtime === 1,
                elementId,
                tooltipId,
                row,
                column,
            };

            row = row + marginTopOffset;
        }

        return row;
    },

    /**
     * Creates a summary text from a filter def
     *
     * @param {Object} filterData
     *
     * @return {string}
     */
    _getRuntimeFilterDescription: function(filterData) {
        const runtimeFilterId = app.utils.generateUUID();

        const runtimeFilterWidget = app.view.createView({
            type: 'report-runtime-filter-widget',
            context: this.context,
            reportData: this.context.get('reportData'),
            filterData,
            runtimeFilterId,
        });

        if (!runtimeFilterWidget._targetField) {
            runtimeFilterWidget.dispose();

            return '';
        }
        const completeDescription = runtimeFilterWidget._getTooltipText();

        runtimeFilterDescription = {
            summaryText: completeDescription.replaceAll('<br>', ' '),
            tooltipDescription: completeDescription,
        };

        runtimeFilterWidget.dispose();

        return runtimeFilterDescription;
    },

    /**
     * Create the html of a line between two elements
     *
     * @param {jQuery} parent
     * @param {jQuery} child
     * @param {number} lineWidth
     * @param {string} color
     * @param {number} calculatedPosY
     *
     * @return {string}
     */
    _generateLineHtml: function(parent, child, lineWidth, color, calculatedPosY) {
        const posX = parseFloat(parent.css('margin-left')) + parseFloat(parent.css('width')) - lineWidth / 2;
        const posY = calculatedPosY ? calculatedPosY : child.position().top;

        const width = parseFloat(child.css('margin-left')) - posX - lineWidth / 2;
        const height = child.position().top - parent.position().top - parent.outerHeight() / 2 - lineWidth / 2;

        let lineEl = document.createElement('div');

        lineEl.style.position = 'absolute';
        lineEl.style.borderLeft = `solid ${lineWidth}px ${color}`;
        lineEl.style.borderBottom = `solid ${lineWidth}px ${color}`;
        lineEl.style.left = `${posX}px`;
        lineEl.style.top = `${posY}px`;
        lineEl.style.width = `${width}px`;
        lineEl.style.height = `${height}px`;

        return {
            html: lineEl.outerHTML,
            posX,
            posY,
        };
    },

    /**
     * Hide drawer tabs
     */
    _hideDrawerTabs: function() {
        let sideDrawer = this.closestComponent('side-drawer');
        sideDrawer.$('.drawer-tabs').hide();

        sideDrawer.$('button[data-action="drawerClose"]').click(function() {
            sideDrawer.$('.drawer-tabs').show();
        });
    },
})

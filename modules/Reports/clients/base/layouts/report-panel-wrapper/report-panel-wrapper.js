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
 * @class View.Layouts.Base.ReportsPanelWrapperLayout
 * @alias SUGAR.App.view.layouts.BaseReportsPanelWrapperLayout
 * @extends View.Layouts.Base.Layout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Before init properties
     *
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this._orderNumber = options.orderNumber;

        this._containerMeta = false;
        this._isCollapsed = false;
        this._minimized = false;

        this._reportWidget = null;

        this._animationDuration = 300;
        this._pillHeight = 26;
        let pillWidths = {
            table: 120,
            filters: 110
        };
        const pillWidthByChartType = {
            hBarF: 210,
            hGBarF: 215,
            vBarF: 192,
            vGBarF: 192,
            pieF: 60,
            funnelF: 85,
            lineF: 65,
            donutF: 80,
            treemapF: 100,
        };

        const chartType = options.layout.model.get('chart_type');
        pillWidths.chart = pillWidthByChartType[chartType];

        const panelId = options.layout._panelsDef[options.orderNumber].id;
        this._pillWidth = pillWidths[panelId];

        this._minimizedPos = -1;
        this._maximizedData = {};
    },

    /**
     * Property initialization
     *
     * @param {Object} options
     */
    _initProperties: function() {
        if (_.has(this, 'layout') && _.has(this.layout, 'layout')) {
            this._headerBar = this.layout.layout.getComponent('report-header');
        }
    },

    /**
     * Build report widget
     *
     * @param {Object} meta
     *
     * @returns {boolean}
     */
    tryAddWidget: function(meta) {
        if (!_.has(meta, 'layout')) {
            return;
        }

        this._containerMeta = meta;

        this.initComponents();
        this.render();
        this._disposeReportWidget();

        this._reportWidget = app.view.createLayout({
            name: meta.layout.type,
            layout: this,
            context: this.context
        });

        if (this._reportWidget.isValid && !this._reportWidget.isValid()) {
            return false;
        }

        this._reportWidget.initComponents();
        this._reportWidget.render();

        const widgetContainer = this.$('[widget-container="report-widget-container"]');
        widgetContainer.append(this._reportWidget.$el);
        widgetContainer.addClass(this.model.get('report_type'));

        this._reportWidget.loadData();

        this.listenTo(this._reportWidget, 'panel:widget:finished:loading', this.addPanelToGrid, this);

        return true;
    },

    /**
     * Add the panel wrapper to gridstack
     *
     * @param {boolean} minimized
     * @param {boolean} collapsed
     */
    addPanelToGrid: function(minimized, collapsed) {
        // uncomment this line if we want to only show widgets after loading is already done
        // this.context.trigger('panel:wrapper:finished:loading', this, this._containerMeta, this._orderNumber)
        if (this.context.get('previewMode')) {
            return;
        }

        if (collapsed) {
            this.collapse(true);
        }

        if (minimized) {
            this.minimize(true, false); // maybe change the second param to true so the animation is instant
        }
    },

    /**
     * Return container meta
     *
     * @return {Object}
     */
    getContainerMeta: function() {
        return this._containerMeta;
    },

    /**
     * Checks if the panel is minimized
     *
     * @returns {boolean}
     */
    isMinimized: function() {
        return this._minimized;
    },

    /**
     * Enlarge panel
     *
     * @param {boolean} enlarge
     */
    enlargePanel: function(enlarge) {
        this._containerMeta.enlarged = enlarge;
    },

    /**
     * Set initial height
     *
     * @param {number} height
     */
    setInitialHeight: function(height) {
        this._containerMeta.initialHeight = height;
    },

    /**
     * Get initial height
     *
     * @return {number}
     */
    getInitialHeight: function() {
        return this._containerMeta.initialHeight;
    },

    /**
     * Notify listeners that the size has changed
     *
     */
    manageSizeUpdated: function() {
        this.trigger('grid-panel:size:changed');
    },

    /**
     * Handle panel minimized
     *
     * @param {boolean} minimized
     * @param {boolean} instant
     */
    minimize: function(minimized, instant) {
        if (this.context.get('previewMode') || this._minimized === minimized) {
            return;
        }

        this._minimized = minimized;
        this._containerMeta.minimized = minimized;

        this.$el.toggleClass('collapsed', this._minimized || this._isCollapsed);
        this.$('.thumbnail').toggleClass('collapsed', this._minimized || this._isCollapsed);

        const grid = this.layout.getGrid();

        grid.resizable(this.$el, !this._minimized && !this._isCollapsed);
        grid.movable(this.$el, !this._minimized);

        if (this._minimized) {
            this._goTop(instant);
        } else {
            this._goRecord(instant);
        }

        this.trigger('panel:minimize', this._minimized, this._minimizedPos, this._containerMeta.id, instant);
        this.trigger('panel:collapse', this._minimized || this._isCollapsed);

        if (!minimized) {
            this._minimizedPos = -1;
        }
    },

    /**
     * Put Widget back to record view
     *
     * @param {boolean} instant
     */
    _goRecord: function(instant) {
        const headerBarHeight = this._headerBar.$('.headerpane').height();
        const topPos = headerBarHeight + parseInt(this._headerBar.$('.record-cell').css('padding-top')) / 2;

        this.$el.css({
            position: 'absolute',
            top: -topPos,
        });

        this.$el.animate(
            this._maximizedData,
            instant ? 0 : this._animationDuration,
            _.bind(this._resetWidgetProperties, this)
        );
    },

    /**
     * Move widget to top bar
     *
     * @param {boolean} instant
     */
    _goTop: function(instant) {
        this._minimizedPos = this.layout.getNumberOfMinimizedPanels() + 1;

        const rightMostButtonsWidth = this._headerBar.$('.btn-toolbar.pull-right').width();
        const headerBarHeight = this._headerBar.$('.headerpane').height();
        const paddingTop = this._headerBar.$('.record-cell').css('padding-top');

        const topPos = headerBarHeight + parseInt(paddingTop) / 2;

        const leftOffset = this._getLeftOffset();
        const leftPos = window.outerWidth - leftOffset - rightMostButtonsWidth;
        const parentWidth = this.$el.parent().width();

        this._maximizedData = {
            top: this.$el.css('top'),
            left: (parseInt(this.$el.css('left')) / parentWidth * 100) + '%',
            width: (this.$el.width() / parentWidth * 100) + '%',
            height: this.$el.css('height'),
        };

        const widgetOffset = this.$el.offset();

        this.$el.css({
            'z-index': '9999',
            'min-width': this._pillWidth,
            'min-height': this._pillHeight,
            position: 'fixed',
            top: widgetOffset.top,
            left: widgetOffset.left,
        });

        const sizeBeforeMinimize = {
            width: this.$el.width(),
            height: this.$el.height(),
        };

        this.$el.removeClass('grid-stack-item');

        this.$el.css(sizeBeforeMinimize);

        this.$el.animate({
                top: topPos,
                left: leftPos,
                width: this._pillWidth,
                height: this._pillHeight,
            },
            instant ? 0 : this._animationDuration,
            _.bind(this._notifyMinimize, this)
        );
    },

    /**
     * Get left offset
     *
     * @return {number}
     */
    _getLeftOffset: function() {
        let pillWidthsOnTheRight = 0;
        _.each(this.layout._panels, function(panel) {
            if (panel.isMinimized() && panel._minimizedPos < this._minimizedPos) {
                pillWidthsOnTheRight += panel._pillWidth;
            }
        }, this);
        let leftOffset = this._pillWidth + pillWidthsOnTheRight;

        return leftOffset;
    },

    /**
     * Whenever minimization is done we need to recalculate position
     */
    _notifyMinimize: function() {
        this._setScreenDependentLeftPos(true);
    },

    /**
     * Rearange top bar elements if one has been put back to record
     *
     * @param {number} panelsMinimized
     */
    recalculateLeftPos: function(panelsMinimized) {
        if (this._minimizedPos <= panelsMinimized) {
            return;
        }

        this._minimizedPos = Math.max(1, this._minimizedPos - 1);

        this._setScreenDependentLeftPos(false);
    },

    /**
     * Transform left attribute into a calculus so it fits all screen sizes
     *
     * @param {boolean} snap
     */
    _setScreenDependentLeftPos: function(snap) {
        const rightMostButtonsWidth = this._headerBar.$('.btn-toolbar.pull-right').width();

        if (snap) {
            const leftOffset = this._getLeftOffset() + rightMostButtonsWidth;

            this.$el.css('left', 'calc(100% - ' + leftOffset + 'px)');
        } else {
            const leftOffset = this._getLeftOffset();
            const leftPos = window.outerWidth - leftOffset - rightMostButtonsWidth;

            this.$el.animate({
                    left: leftPos,
                },
                this._animationDuration,
                _.bind(this._setScreenDependentLeftPos, this, true)
            );
        }
    },

    /**
     * As soon as the widget is back on record, remove all given attributes
     */
    _resetWidgetProperties: function() {
        this.$el.addClass('grid-stack-item');

        this.$el.css({
            'z-index': '',
            'min-width': '',
            'min-height': '',
            top: '',
            left: '',
            width: '',
            height: '',
            position: '',
        });
    },

    /**
     * Handle panel collapsed/not collapsed
     *
     * @param {boolean} collapsed
     * @param {number} previousHeight
     */
    collapse: function(collapsed, previousHeight) {
        if (this.context.get('previewMode') || this._isCollapsed === collapsed) {
            return;
        }

        this._isCollapsed = collapsed;

        this.$el.toggleClass('collapsed', collapsed);
        this.$('.thumbnail').toggleClass('collapsed', collapsed);

        this.collapseGrid(previousHeight);

        this.trigger('panel:collapse', collapsed);
    },

    /**
     * Is collapsed
     *
     * @return {boolean}
     */
    isCollapsed: function() {
        return this._isCollapsed;
    },

    /**
     * Collapse or show the whole grid
     *
     * @param {number} previousHeight
     */
    collapseGrid: function(previousHeight) {
        const grid = this.layout.getGrid();
        const el = this.$el;
        const isCollapsed = el.hasClass('collapsed');
        const node = el.data('_gridstack_node');

        if (previousHeight) {
            node.height = previousHeight;
        }

        if (isCollapsed) {
            el
                .data('expand-min-height', parseInt(node.minHeight))
                .data('expand-height', parseInt(node.height));

            grid
                .resizable(el, false)
                .minHeight(el, null)
                .resize(el, null, 0);
        } else {
            grid
                .resizable(el, true)
                .minHeight(el, parseInt(el.data('expand-min-height')))
                .resize(el, null, parseInt(el.data('expand-height')));
        }
    },

    /**
     * Toggle minimize button
     *
     * @param {boolean} show
     */
    toggleMinimizeButton: function(show) {
        this.trigger('toggle:minimize:button', show);
    },

    /**
     * Dispose subcomponent
     */
    _disposeReportWidget: function() {
        if (this._reportWidget) {
            this._reportWidget.dispose();
            this._reportWidget = null;
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeReportWidget();

        this._super('_dispose');
    },
})

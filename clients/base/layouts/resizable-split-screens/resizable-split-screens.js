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
 * @class View.Layouts.Base.ResizableSplitScreensLayout
 * @alias SUGAR.App.view.layouts.BaseResizableSplitScreensLayout
 * @extends View.Views.Base.Layout
 */
 ({
    className: 'flex flex-grow h-full overflow-y-scroll w-1',

    /**
     * @inheritdoc
     */
    events: {
        'mousedown [data-action="resizer"]': 'startResizing',
    },

    FIRST_SCREEN_ID: 'firstScreen',
    SECOND_SCREEN_ID: 'secondScreen',
    HORIZONTAL_DIRECTION: 'horizontal',
    VERTICAL_DIRECTION: 'vertical',
    DEFAULT_SCREEN_RATIO: '50%',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._beforeInit(options);

        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Quick initialization of field properties
     *
     * @param {Object} options
     *
     */
    _beforeInit: function(options) {
        this._isLoading = options.meta ? options.meta.isLoading : false;
    },

    /**
     * Init Properties
     */
    _initProperties: function() {
        this._elementsMoved = false;
        this._firstScreen = false;
        this._secondScreen = false;
        this._resizer = false;
        this._loadingScreen = false;
        this._dragHandle = false;
        this._mainContainer = false;
        this._isDragging = false;

        this._xPos = 0;
        this._yPos = 0;

        this._firstScreenInitialWidth = 0;
        this._firstScreenInitialHeight = 0;

        this._resizeConfig = {};

        this.setResizeConfig(this.meta.resizeConfig);

        this._firstScreenStyle = this.meta.firstScreenStyle;
        this._secondScreenStyle = this.meta.secondScreenStyle;
        this._handleDisabled = this.meta.handleDisabled;
    },

    /**
     * Listening to external events
     */
    _registerEvents: function() {
        this.listenTo(this.context, 'split-screens-config-change', this.setResizeConfig);

        $(document).on('mousemove', _.bind(this.resizingHandler, this));
        $(document).on('mouseup', _.bind(this.stopResizing, this));
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._arrangeElements();
    },

    /**
     * Move components to their correct div
     */
    _arrangeElements: function() {
        if (this._elementsMoved) {
            return;
        }

        this._elementsMoved = true;

        const elements = this.$el.children();
        const requiredNoOfElements = 4;

        // we only support a config of 2 screens, one splitter and one loading screen
        if (elements.length !== requiredNoOfElements) {
            this.$el.empty();
            return;
        }

        const firstScreenIndex = 2;
        const secondScreenIndex = 3;

        const firstScreenEl = $(elements[firstScreenIndex]);
        const secondScreenEl = $(elements[secondScreenIndex]);

        this._firstScreen = this.$('[data-container="first-split-screen"]');
        this._firstScreen.append(firstScreenEl.detach());
        if (this._firstScreenStyle) {
            this._firstScreen.css(this._firstScreenStyle);
        }

        this._secondScreen = this.$('[data-container="second-split-screen"]');
        this._secondScreen.append(secondScreenEl.detach());
        if (this._secondScreenStyle) {
            this._secondScreen.css(this._secondScreenStyle);
        }

        this._resizer = this.$('[data-action="resizer"]');
        this._mainContainer = this.$('[data-container="split-screens-container"]');
        this._loadingScreen = this.$('[data-widget="split-screens-loading"]');
        this._dragHandle = this.$('[data-action="drag-handle"]');

        this._updateUIElements();
    },

    /**
     * Update Screens and Resizer
     */
    _updateUIElements: function() {
        this._loadingScreen.show();
        this._mainContainer.hide();

        if (this._isLoading) {
            return;
        }

        this._loadingScreen.hide();
        this._mainContainer.show();

        const isVerticalDirection = this._direction === this.VERTICAL_DIRECTION;

        this._mainContainer.css({
            flexDirection: isVerticalDirection ? 'row' : 'column',
        });

        this._firstScreen.show();
        this._secondScreen.show();
        this._resizer.show();

        if (this._resizeConfig.hidden) {
            this._updateUIHiddenScreen();
        } else {
            this._updateUIBothScreens(isVerticalDirection);
        }

        this._updateUIStyle();
    },

    /**
     * Reset and update UI CSS according to direction
     */
    _updateUIStyle: function() {
        this._resizer.removeClass('split-screen-resizer-horizontal');
        this._resizer.removeClass('split-screen-resizer-vertical');

        this._dragHandle.removeClass('split-screens-handle-vertical');
        this._dragHandle.removeClass('split-screens-handle-horizontal');

        this._resizer.toggleClass(`split-screen-resizer-${this._direction}`, true);
        this._dragHandle.toggleClass(`split-screens-handle-${this._direction}`, true);

        if (this._handleDisabled) {
            this._resizer.toggleClass('disabled', true);
        }
    },

    /**
     * Update UI elements when only one screen is visible
     */
    _updateUIHiddenScreen: function() {
        const screenToBeHidden = `_${this._resizeConfig.hidden}`;

        if (this[screenToBeHidden]) {
            this[screenToBeHidden].hide();
            this._resizer.hide();

            this._firstScreen.css({
                height: '100%',
                width: '100%',
            });
        }
    },

    /**
     * Update UI elements when both screens are visible
     *
     * @param {boolean} isVerticalDirection
     */
    _updateUIBothScreens: function(isVerticalDirection) {
        let newRatio = this._resizeConfig.firstScreenRatio || this.DEFAULT_SCREEN_RATIO;

        if (!_.isString(newRatio)) {
            newRatio = `${newRatio}%`;
        }

        this._firstScreen.css({
            height: isVerticalDirection ? '100%' : newRatio,
            width: isVerticalDirection ? newRatio : '100%',
        });
    },

    /**
     * Handle mouse down event
     *
     * @param {jQuery} e
     */
    startResizing: function(e) {
        if (this.noResize || this._handleDisabled) {
            return;
        }

        this._isDragging = true;

        this._xPos = e.clientX;
        this._yPos = e.clientY;

        this._firstScreenInitialWidth = this._firstScreen.width();
        this._firstScreenInitialHeight = this._firstScreen.height();
    },

    /**
     * Resize screens accroding to mouse movement
     *
     * @param {jQuery} e
     */
    resizingHandler: function(e) {
        if (this.noResize) {
            return;
        }

        if (!this._isDragging) {
            return;
        }

        if (this._direction === this.HORIZONTAL_DIRECTION) {
            this._resizeScreen(
                e.clientY,
                this._yPos,
                this._firstScreenInitialHeight,
                'height'
            );
        } else {
            this._resizeScreen(
                e.clientX,
                this._xPos,
                this._firstScreenInitialWidth,
                'width'
            );
        }

        this.context.trigger('container-resizing');
        this._setResizeFeedback();
    },

    /**
     * Resize screen according to direction
     *
     * @param {number} clientPos
     * @param {number} currentPos
     * @param {number} initialSize
     * @param {string} axis
     */
    _resizeScreen: function(clientPos, currentPos, initialSize, axis) {
        const delta = clientPos - currentPos;
        const containerSize = this._mainContainer[axis]();

        let newSize = ((initialSize + delta) * 100) / containerSize;
        newSize = Math.min(Math.max(newSize, 0), 100);

        let newStyle = {};
        newStyle[axis] = `${newSize}%`;

        this._firstScreen.css(newStyle);
        this._resizeConfig.firstScreenRatio = newSize;
    },

    /**
     * Stop resizing screen, clear style
     */
    stopResizing: function() {
        if (this.noResize) {
            return;
        }

        if (!this._isDragging) {
            return;
        }

        this._setIdleFeedback();

        this._isDragging = false;

        this.context.trigger('split-screens-resized', this._resizeConfig);
    },

    /**
     * Set idle style
     */
    _setIdleFeedback: function() {
        this._resizer.css({
            cursor: '',
        });

        this._firstScreen.css({
            userSelect: '',
            pointerEvents: '',
        });

        this._secondScreen.css({
            userSelect: '',
            pointerEvents: '',
        });

        this._mainContainer.css({
            cursor: '',
        });
    },

    /**
     * Update UI elements so that it shows dragging feedback
     */
    _setResizeFeedback: function() {
        this._resizer.css({
            cursor: this._cursor,
        });

        this._mainContainer.css({
            cursor: this._cursor,
        });

        this._firstScreen.css({
            userSelect: 'none',
            pointerEvents: 'none',
        });

        this._secondScreen.css({
            userSelect: 'none',
            pointerEvents: 'none',
        });
    },

    /**
     * Update screens taking into account the new config
     *
     * @param {Object} resizeConfig
     * @param {boolean} updateUI
     */
    setResizeConfig: function(resizeConfig, updateUI) {
        this._resizeConfig = _.extend({}, {
            firstScreenRatio: this.DEFAULT_SCREEN_RATIO,
            direction: this.HORIZONTAL_DIRECTION,
            hidden: false,
        }, resizeConfig);

        this.context.set('resizeConfig', this._resizeConfig);

        this._direction = this._resizeConfig.direction || this.HORIZONTAL_DIRECTION;
        this._cursor = this._direction === this.HORIZONTAL_DIRECTION ? 'row-resize' : 'col-resize';

        if (updateUI) {
            this._isLoading = false;
            this._updateUIElements();
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(document).off('mousemove', _.bind(this.resizingHandler, this));
        $(document).off('mouseup', _.bind(this.stopResizing, this));

        this._super('_dispose');
    },

    /**
     * Disable the resizer
     *
     * @param {boolean} toggle
     */
    toggleResizer: function(toggle) {
        this.noResize = !toggle;

        this.$('[data-action=resizer]').toggleClass('resizer-disabled', !toggle);
    }
})

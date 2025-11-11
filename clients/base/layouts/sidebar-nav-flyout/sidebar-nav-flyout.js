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
 * @class View.Layouts.Base.SidebarNavFlyoutLayout
 * @alias SUGAR.App.view.layouts.BaseSidebarNavFlyoutLayout
 * @extends View.Layout
 */
({
    events: {
        'click [data-action=close]': 'close',
        'keydown': 'handleFlyoutKeydown'
    },

    /**
     * The element the bootstrap popover is attached to.
     */
    anchor: null,

    /**
     * Stores if the flyout is already open.
     */
    isOpen: false,

    /**
     * Stores the element id of the boostrap popover.
     */
    popoverID: null,

    /**
     * Stores if the close X should be displayed
     */
    showClose: false,

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
        this.anchor = options.anchor;
        this.showClose = options.showClose;
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.listenTo(app.router, 'route', this.close);
        this.listenTo(this, 'close', this.close);
        this.listenTo(this, 'reposition', this._repositionFlyout);
    },

    /**
     * Toggles displaying of the flyover.
     */
    toggle: function() {
        this.anchor.popover('toggle');
        this.popoverID = this.anchor.attr('aria-describedby');
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            this.trigger('popover:opened');
            this.bindOnOpen();
            let impersonation = $('.impersonation-banner');
            if (impersonation.length) {
                let impersonationHeight = impersonation.height();
                let flyout = this.anchor.siblings(`#${this.popoverID}`);
                let currentOffset = parseFloat(flyout.css('top'));
                flyout.css({
                    top: currentOffset + impersonationHeight
                });
            }
        } else {
            this.trigger('popover:closed');
            this.unbindOnClose();
        }
    },

    /**
     * Hides the flyout.
     */
    close: function() {
        if (this.isOpen) {
            this.toggle();
        }
    },

    /**
     * Displays the flyout
     */
    open: function() {
        if (!this.isOpen) {
            this.toggle();
        }
    },

    /**
     * Binds flyout events when it is opened.
     */
    bindOnOpen: function() {
        this.$el.on(`click.${this.cid}`, _.bind(this._handleInsideFlyoutClick, this));
        $(document).on(`click.${this.cid}`, _.bind(this._handleOutsideFlyoutClick, this));
        this.listenTo(app.bwc, 'clicked', this._handleOutsideFlyoutClick);
        this.anchor.parents('.sidebar-nav-item-group').on(`scroll.${this.cid}`, _.bind(this._repositionFlyout, this));
        $(window).on(`resize.${this.cid}`, _.bind(this._repositionFlyout, this));
        $(document).on(`keydown.${this.cid}`, _.bind(this.handleFlyoutKeydown, this));
        this.listenTo(app.events, 'sidebar-nav:expand:toggle', this._handleSidebarExpand);
    },

    /**
     * Close the flyout menu if sidebar was collapled
     *
     * @param {bool} expand
     * @private
     */
    _handleSidebarExpand: function(expand) {
        if (!expand) {
            this.close();
        }
    },

    /**
     * Handles when the user clicks inside the flyout. In this case, we do not
     * close the flyout unless they specifically clicked on an element that
     * should close it. We stop event propagation to prevent the event from
     * bubbling up to the document listener, which will close the flyout
     *
     * @param {Event} event the click event
     * @private
     */
    _handleInsideFlyoutClick: function(event) {
        if ($(event.target).closest('[data-flyout-action="close"]').length) {
            this.close();
        }
        event.stopPropagation();
    },

    /**
     * Handles when the user clicks outside the flyout. In this case, we close
     * the flyout. The only exception is when the anchor element is clicked,
     * otherwise the flyout will close immediately after opening
     *
     * @param {Event} event the click event
     * @private
     */
    _handleOutsideFlyoutClick: function(event) {
        if (!event || !$(event.target).closest(this.anchor).length) {
            this.close();
        }
    },

    /**
     * Resets the popover positioning relative to the anchor element
     *
     * @private
     */
    _repositionFlyout: function() {
        if (this.isOpen && this.anchor && this.anchor.popover) {
            this.anchor.popover('show');
            this.popoverID = this.anchor.attr('aria-describedby');
        }
    },

    /**
     * Unbinds flyout events when it is closed
     */
    unbindOnClose: function() {
        this.$el.off(`click.${this.cid}`);
        $(document).off(`click.${this.cid} keydown.${this.cid}`);
        this.stopListening(app.bwc, 'clicked');
        this.stopListening(app.events, 'sidebar-nav:expand:toggle');
        this.anchor.parents('.sidebar-nav-item-group').off(`scroll.${this.cid}`);
        $(window).off(`resize.${this.cid}`);
    },

    /**
     * Closes the flyout on pressing escape
     * @param {Event} event The keydown event
     */
    handleFlyoutKeydown(event) {
        if (event.keyCode === $.ui.keyCode.ESCAPE) {
            this.close();
        }
    }
})


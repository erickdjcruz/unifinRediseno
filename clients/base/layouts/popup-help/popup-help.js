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
 * The layout for the popup-help component.
 *
 * @class View.Layouts.Base.PopupHelpLayout
 * @alias SUGAR.App.view.layouts.PopupHelpLayout
 * @extends View.Layouts.Base.HelpLayout
 */
 ({
    extendsFrom: 'HelpLayout',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._registerEvents();
    },

    /**
     * Register custom layout events
     */
    _registerEvents: function() {
        const delay = 100;

        // reposition the popover when the window is resized
        $(window).on(`resize.${this.cid}`, _.debounce(
            _.bind(function reposition() {
                if (this.button) {
                    this.button.popover('show');
                }
            }, this),
            delay)
        );
    },

    /**
     * Initializes the popover plugin for the button given.
     *
     * @param {jQuery} button The jQuery button.
     */
    _initPopover: function(button) {
        const title = this.options.popupTitle;

        button.popover({
            title: app.lang.get(title) || app.lang.get('LBL_HELP'),
            content: _.bind(function returnEl() {
                return this.$el;
            }, this),
            html: true,
            placement: app.lang.direction === 'rtl' ? 'left' : 'right',
            template: '<div class="popover helpmodal fixed popup-help-modal" data-modal="popup-help">' +
                '<h3 class="popover-title bg-transparent"></h3>' +
                '<div class="popover-content p-0 bg-transparent"></div>' +
                '</div>'
        });
    },

    /**
     * Closes the Help modal if event target is outside of the Help modal.
     *
     * param {Object} evt jQuery event.
     */
    closeOnOutsideClick: function(evt) {
        const target = $(evt.target);
        const button = target.closest('.popup-help-button');
        const containerId = button.data('identifier');
        const buttonId = this.button.data('identifier');

        if (containerId !== buttonId) {
            this.toggle(false);
        }
    },

    /**
     * Updates the body of the popup
     *
     * @param {string} popupInfo
     */
    setPopupInfo: function(popupInfo) {
        const popupInfoController = this.getComponent('popup-info');

        if (!popupInfoController) {
            return;
        }

        popupInfoController.setPopupInfo(popupInfo);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        $(window).off('resize');

        this._super('_dispose');
    },
})

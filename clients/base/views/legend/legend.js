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
 * @class View.Views.Base.DashletFilterModeView
 * @alias SUGAR.App.view.views.BaseDashletFilterModeView
 * @extends View.View
 */
({
    className: 'w-full h-full',

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
        this._id = app.utils.generateUUID();
        this._legend = this.options.legendMeta;
        this._useMargin = this._legend.length > 1;
        this._inlineElements = [];
        this._dropdownElements = [];
        this._dropdownElementsNo = 0;
    },

    /**
     * Register events
     */
    _registerEvents: function() {
        // arrange elements when the container resizes
        this.listenTo(this.context, 'container-resizing', () => {
            this._arrangeElements();
        });

        this.listenTo(this.context, 'animation-completed', () => {
            this._arrangeElements();
        });

        this.listenTo(this.context, 'data-changed', () => {
            this._arrangeElements();
        });

        // hide elements when the window scrolls
        $('.report-dashlet-body-height').off(`scroll.${this.cid}`).on(`scroll.${this.cid}`, (el, targetId) => {
            this.tryHideElements(el, targetId);
        });

        $('.dashlets').off(`scroll.${this.cid}`).on(`scroll.${this.cid}`, (el, targetId) => {
            this.tryHideElements(el, targetId);
        });

        $('.report-chart-container').off(`scroll.${this.cid}`).on(`scroll.${this.cid}`, (el, targetId) => {
            this.tryHideElements(el, targetId);
        });

        // arrange elements when the window resizes
        $(window).off(`resize.${this.cid}`).on(`resize.${this.cid}`, () => {
            this._arrangeElements();
        });

        // close the dropdown when the user clicks outside of it
        $(document).off(`click.${this.cid}`).on(`click.${this.cid}`, (el, targetId) => {
            this.tryHideElements(el, targetId);
        });

        // register click events
        _.each(this._legend, (meta) => {
            const legendItem = this.$(`#${meta.id}`);

            legendItem.off('click').on('click', () => {
                meta.visible = !meta.visible;
                meta.callback();

                legendItem.find('#legend-item-label').css({
                    textDecoration: meta.visible ? 'unset' : 'line-through',
                });
            });
        });
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this._registerEvents();
        this._applyStyle();
        this._arrangeElements();
    },

    /**
     * Hide elements when needed
     *
     * @param {Object} el
     * @param {string} targetId
     */
    tryHideElements: function(el, targetId) {
        const dropdownElements = $(`#dropdown-elements${this._id}`);

        if (this.disposed) {
            dropdownElements.remove();
            $(document).off(`click.${this.cid}`);

            return;
        }

        const displayClass = dropdownElements.css('display');

        if ($(el.target).closest(`#dropdown-elements${this._id}`).length === 0 &&
            $(el.target).closest(`#dropdown-container${this._id}`).length === 0 &&
            (!targetId || targetId !== this._id) &&
            displayClass === 'block') {
            dropdownElements.css({
                display: 'none',
            });

            $(`#dropdown-container${this._id}`).removeClass('open').toggleClass('chart-legend-toggle', false);
        }
    },

    /**
     * Apply style to each legend item
     */
    _applyStyle: function() {
        _.each(this._legend, (legendItem) => {
            const canvasParent = this.$(`#${legendItem.id}`);
            const canvases = canvasParent.find('canvas');

            _.each(canvases, (canvasEl) => {
                const context = canvasEl.getContext('2d');

                context.fillStyle = legendItem.color;
                context.fillRect(0, 0, canvasEl.width, canvasEl.height);
            });
        });
    },

    /**
     * Manage legend items positioning
     */
    _arrangeElements: _.debounce(function() {
        if (this.disposed) {
            return;
        }

        // init elements
        const inlineElements = this.$('#inline-elements');
        const legendContainer = this.$('#legend-container');

        // reset all elements to their initial parent
        const dropdownElements = $(`#dropdown-elements${this._id}`);
        dropdownElements.children().appendTo(inlineElements);
        dropdownElements.hide();

        const dropdownContainer = this.$(`#dropdown-container${this._id}`);
        const dropdownToggle = dropdownContainer.find('#dropdown-toggle');

        const dropdownText = dropdownToggle.find('#dropdown-toggle-text');
        dropdownText.text(`0 ${app.lang.get('LBL_LINK_MORE')}`);

        dropdownContainer.removeClass('!hidden');

        inlineElements.css({
            justifyContent: 'flex-start',
        });

        const legendItemContainerId = `[data-container="legend-item-container${this._id}"]`;
        const inlineLegendId = '[data-container="inline-legend-item"]';
        const dropdownLegendId = '[data-container="dropdown-legend-item"]';

        // remove the overflow hidden class so that we get the full width
        inlineElements.removeClass('overflow-hidden');
        inlineElements.find(legendItemContainerId).removeClass('overflow-hidden');
        inlineElements.find(inlineLegendId).removeClass('hidden').removeClass('overflow-hidden');
        inlineElements.find(dropdownLegendId).addClass('hidden').removeClass('overflow-hidden');

        // property init
        const legendContainerWidth = legendContainer.width();
        const elementsWidth = inlineElements.outerWidth(true);
        const dropdownWidth = dropdownToggle.outerWidth(true);
        const minDisplayWidthOffset = 50;

        // as we now have the full width, we can bring the overflow-hidden class back
        inlineElements.addClass('overflow-hidden');

        if (elementsWidth > legendContainerWidth) {
            const maxWidthAvailable = legendContainerWidth - dropdownWidth - minDisplayWidthOffset;

            // go throught every legend item and if it is outside of the parent container
            // move it into the dropdown
            this.$(legendItemContainerId).each(function() {
                if ($(this).position().left + $(this).outerWidth(true) > maxWidthAvailable) {
                    // hide/show childrent depeding on the parent
                    $(this).nextAll().find(inlineLegendId).addClass('hidden');
                    $(this).nextAll().find(dropdownLegendId).removeClass('hidden');

                    // this element will now be able to display text ellipsis
                    $(this).find(inlineLegendId).addClass('overflow-hidden');
                    $(this).find(dropdownLegendId).addClass('overflow-hidden');
                    $(this).addClass('overflow-hidden');

                    // all the following elements are going into the dropdown
                    $(this).nextAll().appendTo(dropdownElements);

                    return false;
                }
            });

            // show/hide dropdown
            dropdownToggle.off('click').on('click', () => {
                const mainContainer = $('#sugarcrm');
                const togglePos = dropdownToggle.offset();
                const heightOffset = 6;

                dropdownElements.css({
                    top: togglePos.top + this.$('.dropdown-toggle').height() * 2 + heightOffset,
                    left: togglePos.left - dropdownElements.width() + dropdownContainer.width() / 2,
                });

                mainContainer.append(dropdownElements.detach());

                const newDropdown = $(`#dropdown-elements${this._id}`);

                newDropdown.css({
                    display: newDropdown.css('display') === 'block' ? 'none' : 'block',
                });

                _.debounce(() => {
                    dropdownContainer.removeClass('open');
                    dropdownContainer.toggleClass('chart-legend-toggle', newDropdown.css('display') === 'block');
                })();
                $(document).trigger('click', this._id);
            });
        }

        // manage the dropdown button (hide/show and update text)
        this._dropdownElementsNo = dropdownElements.children().length;

        dropdownContainer.toggleClass('!hidden', this._dropdownElementsNo === 0);

        dropdownText.text(`${this._dropdownElementsNo} ${app.lang.get('LBL_LINK_MORE')}`);

        // we either display the legend items in the center or at the start
        // depending if the container is smalled than all the items
        inlineElements.css({
            justifyContent: this._dropdownElementsNo === 0 ? 'center' : 'flex-start',
        });

        this._toggleTooltips();
    }),

    /**
     * hide and show tooltips only for the truncated elements
     */
    _toggleTooltips: _.debounce(function() {
        const legendItemContainerId = `[data-container="legend-item-container${this._id}"]`;
        const inlineLegendId = '[data-container="inline-legend-item"]';
        const dropdownLegendId = '[data-container="dropdown-legend-item"]';

        const toggleTooltip = (container) => {
            // we need to see the dropdown in order to have correct widths(the user will not see it)
            const closestDropdown = $(`#dropdown-elements${this._id}`);
            closestDropdown.css({
                display: 'block',
            });

            const labelEl = _.first(container.find('span'));

            // we only show tooltips if the element is truncated
            if (labelEl.offsetWidth < labelEl.scrollWidth) {
                container.attr('title', $(labelEl).text());
                container.attr('rel', 'tooltip');
            } else {
                container.removeAttr('title');
                container.removeAttr('rel');
            }

            // we hide the dropdown as we're done with it
            closestDropdown.css({
                display: 'none',
            });
        };

        $(legendItemContainerId).each(function() {
            toggleTooltip($(this).find(inlineLegendId));
            toggleTooltip($(this).find(dropdownLegendId));
        });

    }),

    /**
     * @inheritdoc
     */
    _dispose: function() {
        _.each(this._legend, (meta) => {
            this.$(`#${meta.id}`).off('click');
        });

        $(window).off(`resize.${this.cid}`);
        $('.report-dashlet-body-height').off(`scroll.${this.cid}`);
        $('.dashlets').off(`scroll.${this.cid}`);
        $('.report-chart-container').off(`scroll.${this.cid}`);

        this._super('_dispose');
    },
});


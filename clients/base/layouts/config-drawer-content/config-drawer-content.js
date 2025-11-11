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
 * @class View.Layouts.Base.ConfigDrawerContentLayout
 * @alias SUGAR.App.view.layouts.BaseConfigDrawerContentLayout
 * @extends View.Layout
 *
 * Triggered Events:
 *  config:howtoData:change - When a different accordion panel is clicked, a howtoData:change event will be triggered
 *      with the current how-to data for View.Views.Base.ConfigHowToView to listen for and update
 */
({
    events: {
        'click .accordion-toggle': 'onAccordionToggleClicked'
    },

    /**
     * The HTML ID of the Accordion divs
     */
    collapseDivId: 'config-accordion',

    /**
     * The currently-selected config panel
     */
    selectedPanel: undefined,

    /**
     * The current HowTo data Object
     */
    currentHowToData: undefined,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.currentHowToData = {};
        this._initHowTo();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        var $toggles;

        this._super('_render');

        //This is because backbone injects a wrapper element.
        this.$el.addClass(`accordion ${this.module}-config`);
        this.$el.attr('id', this.collapseDivId);
        $toggles = this.$('.accordion-toggle');
        // ignore the first accordion toggle
        $toggles.splice(0, 1);
        $toggles.addClass('collapsed');

        let firstPanel = _.first(this.meta.components).view;
        $(`.accordion-toggle[data-name=${firstPanel}]`).trigger('click');
    },

    /**
     * Used to select a specific panel by name
     * Correct names can be found in the specific view's hbs
     * Specifically found in the id attribute of '.accordion-heading a'
     *
     * @param {String} panelName The ID name of the panel to select
     */
    selectPanel: function(panelName) {
        this.selectedPanel = panelName;
        this.$('#' + panelName + 'Collapse').collapse('show');
    },

    /**
     * Event handler for 'click .accordion-toggle' event
     *
     * @param {jQuery.Event|undefined} evt
     */
    onAccordionToggleClicked: function(evt) {
        evt.preventDefault();

        var panelName = (evt) ? $(evt.currentTarget).data('name') : this.selectedPanel;
        var oldPanel;
        var newPanel;
        var currentEventViewColumns;
        var tmpIndex;

        if (evt && panelName === this.selectedPanel) {
            // dont allow closing the same tab
            return false;
        }

        this._switchHowToData(panelName);

        this.context.trigger('config:howtoData:change', this.currentHowToData);

        if (this.selectedPanel) {
            oldPanel = _.find(this._components, function(component) {
                return component.name === this.selectedPanel;
            }, this);

            if (oldPanel) {
                oldPanel.$('.accordion-toggle').addClass('collapsed');
                oldPanel.trigger('config:panel:hide');
            }
        }

        this.selectedPanel = panelName;

        newPanel = _.find(this._components, function(component) {
            return component.name === panelName;
        }, this);

        // This makes sure that the panel fields are correctly updated when not saved
        _.each(newPanel.panelFields, function(panelField) {
            panelField.currentState = 'unchecked';
        }, this);

        // Get current columns visible on the config accordion
        currentEventViewColumns = newPanel.model.get(newPanel.eventViewName);

        // For each visible column mark the corresponding panel field as checked
        _.each(currentEventViewColumns, function(headerField) {
            tmpIndex = _.findIndex(newPanel.panelFields, function(field) {
                return field.name === headerField.name;
            }, this);
            if (tmpIndex >= 0) {
                newPanel.panelFields[tmpIndex].currentState = 'checked';

                _.each(newPanel.panelFields[tmpIndex].relatedFields, function(relField) {
                    var tmpRelIndex = _.findIndex(newPanel.panelFields, function(field) {
                        return field.name === relField;
                    });

                    // Make sure that the related fields, if unchecked, are filled
                    if (tmpRelIndex >= 0 && (_.isUndefined(newPanel.panelFields[tmpRelIndex].currentState) ||
                        newPanel.panelFields[tmpRelIndex].currentState === 'unchecked')) {
                        newPanel.panelFields[tmpRelIndex].currentState = 'filled';
                    }
                }, this);
            }
        }, this);

        newPanel.trigger('config:panel:show');

        $('.accordion-toggle').addClass('collapsed');
        $('.bs-collapse').removeClass('show');

        let currToggle = $(evt.target).closest('.accordion-heading').find('.accordion-toggle');
        let name = currToggle.attr('data-name');
        let currBody = $(`[data-name=${name}].accordion-body`);

        currToggle.removeClass('collapsed');
        currBody.addClass('show');
    },

    /**
     * Function for child modules to initialize their own HowTo data
     *
     * @private
     */
    _initHowTo: function() {
    },

    /**
     * Handles switching the HowTo text and info by a specific accordion view being toggled
     *
     * @param {string} helpId The panel component name
     * @private
     */
    _switchHowToData: function(helpId) {
    },

    /**
     * Allows child config views with specific needs to be able to 'manually' update the HowTo text
     *
     * @param title
     * @param text
     */
    changeHowToData: function(title, text) {
        this.currentHowToData.title = title;
        this.currentHowToData.text = text;
        this.context.trigger('config:howtoData:change', this.currentHowToData);
    }
})

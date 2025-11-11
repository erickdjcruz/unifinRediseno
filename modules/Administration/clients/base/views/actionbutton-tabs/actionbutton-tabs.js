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
 * Action button tab view
 *
 * @class View.Views.Base.AdministrationActionbuttonTabsView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonTabsView
 * @extends View.View
 */
({
    events: {
        'click a[data-tabId]': 'tabButtonClicked',
        'click a[data-action="add"]': 'addButton',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.buttons = this.context.get('model').get('data').buttons;
        this._canDeleteButton = true;
        this._uiButtons = [];

        if (Object.keys(this.buttons).length === 0) {
            this._createButton();
        }
    },

    /**
     * Context event handling
     *
     */
    _registerEvents: function() {
        this.listenTo(this.context.get('model'), 'refresh:ui', this.render, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._buildButtonsUIData();
        this._super('_render');

        var removeTabIcon = this.$('[data-action="remove-tab"]');
        removeTabIcon.on('click', _.bind(this.deleteButton, this));

        if (Object.keys(this.buttons).length <= 1) {
            removeTabIcon.hide();
        }

        this._makeButtonsSortable();
    },

    /**
     * Handler for tab selection
     *
     * @param {UIEvent} e
     *
     */
    tabButtonClicked: function(e) {
        if (this._isTabValid()) {
            var buttonId = e.currentTarget.dataset.tabid;

            this._activateTabs(false);

            this.buttons[buttonId].active = true;
            this._updateActionButtons();

            this.context.get('model').trigger('update:button:view', buttonId);
            this.render();
        } else {
            e.stopImmediatePropagation();
        }
    },

    /**
     * Handler for new button creation event
     *
     */
    addButton: function() {
        if (this._isTabValid()) {
            this._createButton();
            this.render();
        }
    },

    /**
     * Handler for button removal event
     *
     * @param {UIEvent} e
     *
     */
    deleteButton: function(e) {
        if (this._canDeleteButton && Object.keys(this.buttons).length > 1) {
            app.alert.show('alert-actionbutton-delete', {
                level: 'confirmation',
                messages: app.lang.get('LBL_ACTIONBUTTON_DELETE_BUTTON'),
                autoClose: false,
                onConfirm: _.bind(function deletebutton() {
                    var buttonId = $(e.currentTarget).data('id');

                    this._activateTabs(false);

                    delete this.buttons[buttonId];

                    var firstButtonKey = Object.keys(this.buttons)[0];
                    this.buttons[firstButtonKey].active = true;

                    this._updateActionButtons();
                    this.context.get('model').trigger('update:button:view', firstButtonKey);

                    // rerender so that the tab view also goes away
                    this.render();
                }, this),
            });
        }
    },

    /**
     * Validation for button configuration
     *
     * @return {bool}
     */
    _isTabValid: function() {
        var headerPane = this.layout.layout.getComponent('actionbutton-headerpane');

        if (headerPane) {
            var isValid = headerPane.canSaveConfig(headerPane.layout);

            return isValid;
        }

        return false;
    },

    /**
     * Builds an array that will get itterated over in hbs
     */
    _buildButtonsUIData: function() {
        this._uiButtons = _.sortBy(this.buttons, 'orderNumber');
    },

    /**
     * Create a default button configuration for a new button
     *
     */
    _createButton: function() {
        var newButtonId = app.utils.generateUUID();

        this._activateTabs(false);

        this.buttons[newButtonId] = {
            active: true,
            buttonId: newButtonId,
            orderNumber: Object.keys(this.buttons).length,
            properties: {
                label: app.lang.get('LBL_ACTIONBUTTON_BUTTON'),
                description: '',
                showLabel: true,
                showIcon: true,
                colorScheme: 'primary',
                icon: 'sicon-settings',
                isDependent: false,
                stopOnError: false,
                formula: '',
            },
            actions: {},
        };

        this._updateActionButtons();
        this.context.get('model').trigger('update:button:view', newButtonId);
    },

    /**
     * Adds the sortability feature to created tabs
     */
    _makeButtonsSortable: function() {
        this.$('.nav-tabs').sortable({
            revert: true,
            cancel: '.ab-tab-add',
            items: '> li:not(.ab-tab-add)',
            start: _.bind(function blockRemoval(event, ui) {
                // if we drag buttons we need to block the delete functions
                this._canDeleteButton = false;
                var initialIndex = ui.item.index();

                ui.item.data('initialIndex', initialIndex);
            }, this),
            stop: _.bind(function allowRemoval(event, ui) {
                // when we release the button we can remove buttons once again
                this._canDeleteButton = true;
                this._reorderButtons(ui.item.data('initialIndex'), ui.item.index());
                this._updateActionButtons();
            }, this)
        });
    },

    /**
     * Reorders buttons list
     *
     * @param {number} initialOrderNumber
     * @param {number} finalOrderNumber
     */
    _reorderButtons: function(initialOrderNumber, finalOrderNumber) {
        this._unsetButtonOrder(initialOrderNumber);

        _.each(this.buttons, function orderButtons(buttonData) {
            if (buttonData.orderNumber !== -1) {
                if (
                    initialOrderNumber > finalOrderNumber &&
                    buttonData.orderNumber >= finalOrderNumber &&
                    buttonData.orderNumber <= initialOrderNumber
                ) {
                    buttonData.orderNumber = buttonData.orderNumber + 1;
                }

                if (
                    initialOrderNumber < finalOrderNumber &&
                    buttonData.orderNumber >= initialOrderNumber &&
                    buttonData.orderNumber <= finalOrderNumber
                ) {
                    buttonData.orderNumber = buttonData.orderNumber - 1;
                }
            }
        });

        _.each(this.buttons, function orderButtons(buttonData) {
            if (buttonData.orderNumber === -1) {
                buttonData.orderNumber = finalOrderNumber;
            }
        });
    },

    /**
     * Sets the orderNumber to -1
     *
     * @param {number} orderNumber
     */
    _unsetButtonOrder: function(orderNumber) {
        _.each(this.buttons, function unsetOrder(buttonData) {
            if (buttonData.orderNumber === orderNumber) {
                buttonData.orderNumber = -1;
            }
        });
    },

    /**
     * Toggles button active flag
     *
     * @param {bool} active
     *
     */
    _activateTabs: function(active) {
        _.each(this.buttons, function clearTab(buttonData, buttonId) {
            this.buttons[buttonId].active = active;
        }, this);
    },

    /**
     * Update context action button configuration
     *
     */
    _updateActionButtons: function() {
        var ctxModel = this.context.get('model');
        var buttonsData = ctxModel.get('data');
        buttonsData.buttons = this.buttons;

        // update button data into the main data container
        ctxModel.set('data', buttonsData);
        this.context.trigger('update-buttons-preview', buttonsData);
    },
});

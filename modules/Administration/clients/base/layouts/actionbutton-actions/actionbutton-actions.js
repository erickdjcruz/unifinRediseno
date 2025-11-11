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
 * Layout for entire list of actions
 *
 * @class View.Layouts.Base.AdministrationActionbuttonActionsLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationActionbuttonActionsLayout
 * @extends View.Layout
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
        this._registerEvents();
    },

    /**
     * Initial setup of `buttonData` property
     */
    _initProperties: function() {
        this.buttonData = this._getActiveButtonData();
        this._canAddDeleteAction = true;
    },

    /**
     * Context model event registration
     */
    _registerEvents: function() {
        var ctxModel = this.context.get('model');

        this.listenTo(ctxModel, 'update:button:view', this.refreshActions, this);
        this.listenTo(ctxModel, 'button:action:added', this.addNewAction, this);
        this.listenTo(ctxModel, 'button:action:removed', this.removeAction, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        this.refreshActions();
        this._applyCustomStyle();
        this._makeActionsSortable();
    },

    /**
     * Recreate action layouts
     */
    refreshActions: function() {
        // remove the actions of the previosuly selected button
        // create the actions of the current selected button
        this.buttonData = this._getActiveButtonData();

        if (Object.keys(this.buttonData.actions).length === 0) {
            this.$('.ab-admin-actions-container').empty();

            this._createAction({}, app.utils.generateUUID());
        } else {
            this._createActions();
        }
    },

    /**
     * Handles adding a new action
     */
    addNewAction: function() {
        if (this._canAddDeleteAction) {
            this._createAction({}, app.utils.generateUUID());
        }
    },

    /**
     * Handles re-rendering the actions when removing an action from the data object
     */
    removeAction: function() {
        this.refreshActions();
    },

    /**
     * Apply custom style class to parent layout
     */
    _applyCustomStyle: function() {
        this.layout.$el.addClass('ab-admin-main-left-pane');
        const mainPane = this.closestComponent('main-pane');
        if (mainPane) {
            mainPane.$el.addClass('overflow-y-auto');
        }
    },

    /**
     * Creating the sidecar layouts for each action
     */
    _createActions: function() {
        this._disposeSubComponents();

        this.$('.ab-admin-actions-container').empty();

        _.each(this.buttonData.actions, function setActionID(actionData, id) {
            actionData.id = id;
        });

        const _orderedActions = _.sortBy(this.buttonData.actions, 'orderNumber');

        _.each(_orderedActions, function createAction(actionData) {
            this._createAction(actionData, actionData.id);
        }, this);
    },

    /**
     * Create a specific layout for a given action
     *
     * @param {Object} actionData
     * @param {string} actionId
     *
     */
    _createAction: function(actionData, actionId) {
        if (!this._subComponents) {
            this._subComponents = [];
        }

        var defaultAction = 'create-record';

        var actionLayout = app.view.createLayout({
            name: 'actionbutton-action',
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            actionId: actionId,
            actionType: actionData.actionType ? actionData.actionType : defaultAction,
            actionData: actionData,
        });

        this._subComponents.push(actionLayout);

        this.$('.ab-admin-actions-container').append(actionLayout.$el);
        actionLayout.setup();
    },

    /**
     * Adds the sortability feature to created actions
     */
    _makeActionsSortable: function() {
        this.$('.ab-admin-actions-container').sortable({
            revert: true,
            start: _.bind(function blockRemoval(event, ui) {
                // if we drag buttons we need to block the delete functions
                this._canAddDeleteAction = false;
                var initialIndex = ui.item.index();

                ui.item.data('initialIndex', initialIndex);
            }, this),
            stop: _.bind(function allowRemoval(event, ui) {
                // when we release the button we can remove buttons once again
                this._canAddDeleteAction = true;
                this._reorderActions(ui.item.data('initialIndex'), ui.item.index());
            }, this)
        });
    },

    /**
     * Reorders actions list
     *
     * @param {number} initialOrderNumber
     * @param {number} finalOrderNumber
     */
    _reorderActions: function(initialOrderNumber, finalOrderNumber) {
        this._unsetActionOrder(initialOrderNumber);

        _.each(this._subComponents, function orderActions(action) {
            if (action._actionData.orderNumber !== -1) {
                if (
                    initialOrderNumber > finalOrderNumber &&
                    action._actionData.orderNumber >= finalOrderNumber &&
                    action._actionData.orderNumber <= initialOrderNumber
                ) {
                    action._actionData.orderNumber = action._actionData.orderNumber + 1;
                }

                if (
                    initialOrderNumber < finalOrderNumber &&
                    action._actionData.orderNumber >= initialOrderNumber &&
                    action._actionData.orderNumber <= finalOrderNumber
                ) {
                    action._actionData.orderNumber = action._actionData.orderNumber - 1;
                }
            }
        });

        _.each(this._subComponents, function orderButtons(action) {
            if (action._actionData.orderNumber === -1) {
                action._actionData.orderNumber = finalOrderNumber;
            }

            action.setup();
        });
    },

    /**
     * Sets the orderNumber to -1
     * @param {number} orderNumber
     */
    _unsetActionOrder: function(orderNumber) {
        _.each(this._subComponents, function unsetOrder(action) {
            if (action._actionData.orderNumber === orderNumber) {
                action._actionData.orderNumber = -1;
            }
        });
    },

    /**
     * Return configuration for active button
     * @return {Object}
     */
    _getActiveButtonData: function() {
        var buttons = this.context.get('model').get('data').buttons;

        var activeButton = _.filter(buttons, function getActiveButtonData(buttonData) {
            return buttonData.active === true;
        })[0];

        return activeButton;
    },

    /**
     * Dispose any loaded components
     */
    _disposeSubComponents: function() {
        _.each(this._subComponents, function(component) {
            component.dispose();
        }, this);

        this._subComponents = [];
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._disposeSubComponents();

        this._super('_dispose');
    },
});

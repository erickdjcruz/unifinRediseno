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
 * Layout for a single action configuration
 *
 * @class View.Layouts.Base.AdministrationActionbuttonActionLayout
 * @alias SUGAR.App.view.layouts.BaseAdministrationActionbuttonActionLayout
 * @extends View.Layout
 */
({
    /**
     * Actions available only on SELL/SERVE
     *
     * @var Object
     */
    selServeActions: {
        'document-merge': 'LBL_ACTIONBUTTON_DOCUMENT_MERGE',
    },

    events: {
        'click [data-action="remove"]': 'removeAction',
        'click [data-action="add"]': 'addNewAction',
        'change .ab-admin-action-selector select': 'actionChanged',
    },

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
     * Initialization of properties needed before calling the sidecar/backbone initialize method
     * @param {Object} options
     */
    _beforeInit: function(options) {
        this.isSellServe = app.user.hasSellServeLicense();

        let actions = options.context.get('model').get('actions');

        // we still need this into the actions object.
        // they wil be disabled into the select
        if (!this.isSellServe) {
            _.extend(actions, this.selServeActions);
        }

        this._actionId = options.actionId;
        this._actions = {
            label: 'LBL_ACTIONBUTTON_ACTION',
            id: 'actionsDropdown',
            value: options.actionType,
            options: actions,
            disabled: this.selServeActions[options.actionType] && !this.isSellServe ? true : false,
        };

        this._buttonData = this._getActiveButtonData(options);
        this._actionData = options.actionData;

        if (Object.keys(this._actionData).length === 0) {
            this._actionData = {
                actionType: options.actionType,
                orderNumber: Object.keys(this._buttonData.actions).length,
                properties: {}
            };
        }
    },

    /**
     * Clear out default functionality
     * @inheritdoc
     */
    _initProperties: function() {
    },

    /**
     * Clear out default functionality
     * @inheritdoc
     */
    _registerEvents: function() {
    },

    /**
     * Message parent to add a new action
     */
    addNewAction: function() {
        this.context.get('model').trigger('button:action:added');
    },

    /**
     * Message parent to remove current action
     */
    removeAction: function() {
        if (this.layout._canAddDeleteAction) {
            app.alert.show('alert-actionbutton-delete', {
                level: 'confirmation',
                messages: app.lang.get('LBL_ACTIONBUTTON_DELETE_ACTION'),
                autoClose: false,
                onConfirm: _.bind(function deletebutton() {
                    // remove the action from the button data
                    delete this._buttonData.actions[this._actionId];

                    var ctxModel = this.context.get('model');

                    var buttonsData = ctxModel.get('data');
                    buttonsData.buttons[this._buttonData.buttonId] = this._buttonData;

                    ctxModel.set('data', buttonsData);

                    // notify listeners
                    ctxModel.trigger('button:action:removed', this._actionId);
                }, this),
            });
        }
    },

    /**
     * Further initialization/update of layout/context properties
     */
    setup: function() {
        this._actionData.properties = this._createActionView();
        this._buttonData.actions[this._actionId] = this._actionData;

        var ctxModel = this.context.get('model');

        // as the actions changed, we have to store them into the main container
        var buttonsData = ctxModel.get('data');
        buttonsData.buttons[this._buttonData.buttonId] = this._buttonData;

        ctxModel.set('data', buttonsData);

        this._actions.value = this._actionData.actionType;

        this.$('.ab-admin-action-selector select').select2();
    },

    /**
     * Update properties based on action selection
     * @param {UIEvent} e
     */
    actionChanged: function(e) {
        this._actions.value = e.currentTarget.value;
        this._actionData = {
            actionType: this._actions.value,
            orderNumber: this._actionData.orderNumber,
            properties: {}
        };

        this.setup();
    },

    /**
     * Initialize and render inner action view
     * @return {Object}
     */
    _createActionView: function() {
        this._disposeSubComponents();

        var container = this.$('.ab-admin-action-view');

        container.empty();

        var actionView = app.view.createView({
            name: 'actionbutton-' + this._actionData.actionType,
            context: this.context,
            model: this.context.get('model'),
            layout: this,
            actionId: this._actionId,
            buttonId: this._buttonData.buttonId,
            actionData: this._actionData,
        });

        this._subComponents.push(actionView);

        container.append(actionView.$el);

        actionView.setup();
        actionView.render();

        return actionView.getProperties();
    },

    /**
     * Return active button properties
     * @param {Object} options
     */
    _getActiveButtonData: function(options) {
        var buttons = options.context.get('model').get('data').buttons;

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
            component.$('.select2-container').select2('close');
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

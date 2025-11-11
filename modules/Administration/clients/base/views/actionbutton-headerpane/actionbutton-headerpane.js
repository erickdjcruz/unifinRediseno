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
 * Action button configuratoin headerpane view
 *
 * @class View.Views.Base.AdministrationActionbuttonHeaderpaneView
 * @alias SUGAR.App.view.views.BaseAdministrationActionbuttonHeaderpaneView
 * @extends View.View
 */
({
    plugins: [
        'Editable',
    ],

    events: {
        'click [data-action=close]': 'closeDrawer',
        'click [data-action=save]': 'saveSettings',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties();
    },

    /**
     * Property initialization
     *
     */
    _initProperties: function() {
        this.actionButtonLabel = this.context.get('model').get('label');
        this._initialState = app.utils.deepCopy(this.context.get('model').get('data'));
    },

    /**
     * Close configuration drawer
     *
     */
    closeDrawer: function() {
        this.context.get('cancelCallback').call(this);

        app.drawer.close();
    },

    /**
     * Validate configured actions
     *
     * @param {View.Layout} layout
     *
     * @return {bool}
     */
    canSaveConfig: function(layout) {
        var canSave = true;

        // go throught all our components and their subcomponents and see if the have any canSave logic
        var allComponents = _.union(layout._components, layout._subComponents);

        _.each(allComponents, function getSave(component) {
            if (canSave === true) {
                if (component.canSave && component.canSave() === false) {
                    canSave = false;
                } else {
                    canSave = this.canSaveConfig(component);
                }
            }
        }, this);

        return canSave;
    },

    /**
     * Save action button configuration
     *
     */
    saveSettings: function() {
        if (this.canSaveConfig(this.layout) && this.isDropdownValid()) {
            this.context.get('saveCallback').call(this, this.context.get('model').get('data'));
            this.closeDrawer();
        }
    },

    /**
     * @inheritdoc
     */
    hasUnsavedChanges: function() {
        let currentState = this.context.get('model').get('data');

        return !_.isEqual(currentState, this._initialState);
    },

    /**
     * If the display type for button is dropdown, we are ensuring at least two buttons are configured.
     *
     * @return {bool}
     */
    isDropdownValid: function() {
        const data = this.model.get('data');

        if (data.settings.type === 'dropdown' && Object.keys(data.buttons).length < 2) {
            app.alert.show('alert_ab_min_two_buttons', {
                level: 'error',
                messages: app.lang.get('LBL_ACTIONBUTTON_INVALID_DROPDOWN_ERROR'),
                autoClose: true,
                autoCloseDelay: 5000
            });

            return false;
        }

        return true;
    }
});

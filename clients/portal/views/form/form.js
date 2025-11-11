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
 * This is a basic form view, providing utility functions for Portal views that
 * use forms.
 *
 * @class View.Views.Portal.FormView
 * @alias SUGAR.App.view.views.PortalFormView
 * @extends View.View
 */
({
    /**
     * Stores the function to be used as the form "submit" function for a given
     * Portal view
     */
    submitFunction: null,

    /**
     * @inheritdoc
     *
     * Sets a keypress handler to run when keys are pressed within a form input
     * field
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.events = _.extend(this.events || {}, {
            'keypress input': 'handleKeypress'
        });
    },

    /**
     * A general function to handle keypresses in Portal views
     * @param {Event} event The `keypress` event.
     */
    handleKeypress: function(event) {
        // If the enter/return button is pressed, prevent the form data from
        // auto-submitting and causing the page to reload. Instead, call the
        // function that the view uses to process the form
        if (event.keyCode === 13) {
            event.preventDefault();
            if (typeof this.submitFunction === 'function') {
                this.$('input').trigger('blur');
                this.submitFunction();
            }
        }
    }
});

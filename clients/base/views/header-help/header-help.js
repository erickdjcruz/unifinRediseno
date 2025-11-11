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
 * @class View.Views.Base.HeaderHelpView
 * @alias SUGAR.App.view.views.BaseHeaderHelpView
 * @extends View.View
 */
({
    plugins: ['Previewable'],

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._setPropertiesData();
    },

    /**
     * Set initial properties
     *
     * @private
     */
    _setPropertiesData: function() {
        this.buttonColor = null;
        this.label = app.config.newCaseMessage || this.meta.default_label;
        this.enabled = _.isUndefined(app.config.newCaseButton) || app.config.newCaseButton;
        if (this.enabled && app.config.newCaseButtonText && this.meta.buttons.length) {
            this.meta.buttons[0].label = app.config.newCaseButtonText;
        }
    }
})

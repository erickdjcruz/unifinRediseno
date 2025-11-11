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
 * @class View.Fields.Base.FocuscabField
 * @alias SUGAR.App.view.fields.BaseFocuscabField
 * @extends View.Fields.Base.CabField
 */
({
    extendsFrom: 'CabField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.type = 'cab';
    },

    /**
     * Handle the click event of focus icon
     */
    handleClick: function() {
        if (!_.isEmpty(app.sideDrawer)) {
            app.utils.openFocusDrawer(app.sideDrawer, this.module, this.view.model.get('id'), this.$el);
        }
    },

    /**
     * @inheritdoc
     */
    hasAccess: function() {
        return this.isFocusDrawerEnabled() && this._super('hasAccess');
    },

    /**
     * Check if the focus drawer can be used or not.
     *
     * @return {boolean} True if the focus drawer is ready for use.
     */
    isFocusDrawerEnabled: function() {
        return app.utils.isTruthy(app.config.enableLinkToDrawer);
    }
})

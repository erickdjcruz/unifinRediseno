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
 * @class View.Views.Base.DocuSignEnvelopes.RecipientSelectionHeaderpaneView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesRecipientSelectionHeaderpaneView
 * @extends View.Views.Base.DocusignRecipientSelectionHeaderpaneView
 */
({
    extendsFrom: 'DocusignRecipientSelectionHeaderpaneView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        if (_.isUndefined(options.context.get('templateDetails'))) {
            options = this._removeBackButton(options);
        }
        this._super('initialize', [options]);
    },

    /**
     * Remove back button
     *
     * @param {Object} options
     * @return {Object}
     */
    _removeBackButton: function(options) {
        options.meta.buttons = _.filter(options.meta.buttons, function(button) {
            return button.name !== 'back_button';
        });

        return options;
    },
});

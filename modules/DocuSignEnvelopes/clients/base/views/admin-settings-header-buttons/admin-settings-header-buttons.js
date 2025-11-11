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
 * @class View.Views.Base.DocuSignEnvelopes.AdminSettingsHeaderButtonsView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesAdminSettingsHeaderButtonsView
 * @extends View.Views.Base.ConfigHeaderButtonsView
 */
({
    extendsFrom: 'ConfigHeaderButtonsView',

    /**
     * Get save config url
     *
     * @return {string}
     */
    _getSaveConfigURL: function() {
        return app.api.buildURL('DocuSign', 'setGlobalConfig');
    },

    /**
     * Get save config attributes
     *
     * @return {Object}
     */
    _getSaveConfigAttributes: function() {
        const recipientSelection = this.model.get('recipientSelection');

        return {
            recipientSelection: recipientSelection,
        };
    },

    /**
     * Save config
     */
    _saveConfig: function() {
        app.api.call(
            'create',
            this._getSaveConfigURL(),
            this._getSaveConfigAttributes(),
            {
                success: _.bind(function(settings) {
                    if (_.isUndefined(app.config.docusign)) {
                        app.config.docusign = {};
                    }
                    app.config.docusign.recipientSelection = settings.recipientSelection;

                    this.showSavedConfirmation();

                    app.router.navigate(this.module, {trigger: true});
                }, this),
                error: _.bind(function() {
                    this.getField('save_button').setDisabled(false);
                }, this)
            }
        );
    },
})

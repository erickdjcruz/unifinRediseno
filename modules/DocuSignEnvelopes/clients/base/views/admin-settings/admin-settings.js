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
 * @class View.Views.Base.DocuSignEnvelopes.AdminSettingsView
 * @alias SUGAR.App.view.views.BaseDocuSignEnvelopesAdminSettingsView
 * @extends View.Views.Base.View
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        var url = app.api.buildURL('DocuSign', 'getGlobalConfig');
        app.api.call('read', url, {}, {
            success: function successCb(settings) {
                this.model.set('recipientSelection', settings.recipientSelection);

                this.render();
            }.bind(this),
            error: function() {
                app.log.error('Could not make the call to getGlobalConfig');
            }
        });
    },
})

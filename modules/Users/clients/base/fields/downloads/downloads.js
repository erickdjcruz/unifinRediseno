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
 * @class View.Fields.Base.Users.DownloadsField
 * @alias SUGAR.App.view.fields.BaseUsersDownloadsField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    showNoData: false,

    /**
     * @inheritdoc
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.fetchPlugins();
    },

    /**
     * Retrieves a list of available plugins from the server and
     * renders them in the field
     */
    fetchPlugins: function() {
        this.loading = true;
        let pluginsUrl = app.api.buildURL('me/plugins');
        app.api.call('read', pluginsUrl, null, {
            success: (result) => {
                this._pluginCategories = result;
            },
            complete: () => {
                this.loading = false;
                this.render();
            }
        });
    }
});

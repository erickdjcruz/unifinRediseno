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
 * @class View.Fields.Portal.RelateField
 * @alias SUGAR.App.view.fields.PortalRelateField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'RelateField',

    /**
     * @inheritdoc
     *
     * Verify if Contacts link's id is the same as current user id.
     */
    buildRoute: function(module, id) {
        this._super('buildRoute', [module, id]);
        if (!(module === 'Contacts' && id === app.user.get('id'))) {
            this.restrictHyperLinks(module);
        }
    },

    /**
     * Restricts hyperlinks on fields for Portal users.
     * @param {string} moduleName The related module.
     *
     * Sets the `href` attribute to `undefined`.
     */
    restrictHyperLinks: function(moduleName) {
        var hyperlinkRestrictedModules = ['Users', 'Employees', 'Contacts'];

        if (_.contains(hyperlinkRestrictedModules, moduleName)) {
            this.href = undefined;
        }
    },

    /**
     * @inheritdoc
     */
    _render() {
        if (this.view.name !== 'create') {
            this._super('_render');
        }
    }
})

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
 * @class View.Fields.Base.DocuSignEnvelopes.RecipientRoleField
 * @alias SUGAR.App.view.fields.BaseDocuSignEnvelopesRecipientRoleField
 * @extends View.Fields.Base.Field
 * @deprecated Use {@link View.Fields.Base.DocusignRecipientRoleField} instead.
 */
({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options = this.setupItems(options);

        this._super('initialize', [options]);
    },

    /**
     * Setup dropdown items
     *
     * @param {Object} options
     * @return {Object}
     */
    setupItems: function(options) {
        options.def.options = {'': ''};

        const templateDetails = options.context.get('templateDetails');

        let roles = templateDetails.roles;

        const firstRole = _.first(roles);

        if (roles.length > 0 && typeof firstRole.routing_order != 'undefined' && firstRole.routing_order != '') {
            roles = _.sortBy(roles, 'routing_order');
        }
        _.each(roles, function setRoles(role) {
            options.def.options[role.name] = role.name;
        });

        return options;
    }
});

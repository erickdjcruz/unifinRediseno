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
 * @class View.Fields.Base.Users.RoleAccessField
 * @alias SUGAR.App.view.fields.BaseUsersRoleAccessField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseField',

    showNoData: false,

    /**
     * Array of operations in the access control matrix
     */
    names: [],

    /**
     * Access control matrix data
     */
    categories: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._getAccessData();
    },

    /**
     * Get the users role access data defined by the admin in Roles
     *
     * @private
     */
    _getAccessData: function() {
        const url = app.api.buildURL('Users', 'userAccess', {id: this.model.id});

        this.loading = true;
        app.api.call('GET', url, null, {
            success: (results) => {
                this.names = results.names;
                this.categories = results.categories;
            },
            error: () => {
                app.logger.error('Unable to fetch the user role data.');
            },
            complete: () => {
                this.loading = false;
                this.render();
            }
        });
    }
})

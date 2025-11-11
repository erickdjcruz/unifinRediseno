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
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     * @return {boolean}
     */
    hasAccess: function() {
        if (this.def.acl_action !== 'manager') {
            return true;
        }

        return 'manager' === this.getUserCurrentRole();
    },

    /**
     * Gets user current role (the role under which the user is viewing the worksheet at the moment)
     * @return {string} "manager" or "seller"
     *
     */
    getUserCurrentRole: function() {
        let user = this.context.get('selectedUser') || app.user.toJSON();
        let forecastType = app.utils.getForecastType(user.is_manager, user.showOpps);

        return (forecastType === 'Direct') ? 'seller' : 'manager';
    },
})

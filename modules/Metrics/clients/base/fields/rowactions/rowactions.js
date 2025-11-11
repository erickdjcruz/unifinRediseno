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
/*
 * @class View.Fields.Base.Metrics.RowactionsField
 * @alias SUGAR.App.view.fields.MetricsRowactionsField
 * @extends View.Fields.Base.RowactionsField
 */
({
    extendsFrom: 'RowactionsField',

    /**
     * Checks if the user is an admin
     * @return {boolean} true if is an admin, false otherwise
     * @private
     */
    _isAdmin: function() {
        var acls = app.user.getAcls().Metrics;
        var isAdmin = !_.has(acls, 'admin');
        var isSysAdmin = (app.user.get('type') === 'admin');

        return (isSysAdmin || isAdmin);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!this._isAdmin()) {
            // Don't show if not admin
            return;
        }
        this._super('_render');
    }
})

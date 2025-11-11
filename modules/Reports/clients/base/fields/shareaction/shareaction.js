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
 * @class View.Fields.Base.Reports.ShareactionField
 * @alias SUGAR.App.view.fields.BaseReportsShareactionField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    _getShareParams: function(model) {
        var parentShareParams = this._super('_getShareParams', [model]);

        return _.extend({}, parentShareParams, {
            url: app.utils.getSiteUrl() + '#' + app.router.buildRoute('Reports', model.get('id')),
        });
    },
})

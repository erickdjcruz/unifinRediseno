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
 * Selection list to get Outbound Email accounts for SugarBPM
 *
 * @class View.Views.Base.OutboundEmail.SelectionListForBpmView
 * @alias SUGAR.App.view.views.BaseOutboundEmailSelectionListForBpmView
 * @extends View.Views.Base.SelectionListView
 */
({
    extendsFrom: 'SelectionListView',

    dataView: 'selection-list-for-bpm',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        // we need to set the endpoint to hit the pmse api code
        options.context.loadData = _.wrap(options.context.loadData, function(origFn, opts) {
            opts = opts || {};
            opts.endpoint = function(method, model, urlOpts, callbacks) {
                var url = app.api.buildURL('pmse_Project/CrmData/outboundEmailsAccounts', null, null, urlOpts.params);
                return app.api.call('read', url, {}, callbacks, urlOpts.apiOptions);
            };
            origFn.call(options.context, opts);
        });

        this._super('initialize', [options]);
    }
})

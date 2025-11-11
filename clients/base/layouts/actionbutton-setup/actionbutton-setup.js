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
 * @class View.Layouts.Base.ActionbuttonSetup
 * @alias SUGAR.App.view.layouts.BaseActionbuttonSetupLayout
 * @extends View.Layouts.Base.BaseLayout
 */
({
    /**
     * Options that should be available only on SELL and SERVE
     *
     * @var array
     */
    sellServeOptions: [
        'document-merge',
    ],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        let ctxModel = this.context.get('model');
        let actions = options.meta.actions;

        /**
         * If the license is not SELL or SERVE,
         * we need to remove some of the actions
         */
        actions = this._filterSellServeOptions(actions);

        ctxModel.set({
            actions: actions
        });
    },

    /**
     * Remove SELL/SERVE options from the actions
     *
     * @param {Object} actions
     */
    _filterSellServeOptions: function(actions) {
        if (!app.user.hasSellServeLicense()) {
            for (const option of this.sellServeOptions) {
                if (_.has(actions, option)) {
                    delete actions[option];
                }
            }
        }

        return actions;
    },
});

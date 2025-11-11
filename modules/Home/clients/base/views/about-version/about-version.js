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
 * @class View.Views.Base.Home.AboutVersionView
 * @alias SUGAR.App.view.views.BaseHomeAboutVersionView
 * @extends View.View
 */
({
    /**
     * Version info string
     */
    versionInfo: '',

    /**
     * Array of the user's products
     */
    products: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        let version = app.metadata.getServerInfo().version;
        let template = Handlebars.compile(app.lang.get('LBL_ABOUT_VERSION', 'Home'));
        this.versionInfo = template({
            version: version,
        });

        this.products = app.user.getProductNames();
    }
})

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
 * @class View.Views.Portal.Home.ModuleMenuView
 * @alias SUGAR.App.view.views.PortalHomeModuleMenuView
 * @extends View.Views.Base.HomeModuleMenuView
 */
({
    extendsFrom: 'BaseHomeModuleMenuView',

    /**
     * We want to display company logomark image on top left of header
     * and fallback to logomark image on Portal login screen when logo image isn't provided
     *
     * @override
     * @private
     */
    _setLogoImage: function() {
        var defaultSugarLogoMark = 'styleguide/assets/img/sugar-cube-black.svg';
        this.logoImage = app.config.logomarkURL || app.utils.buildUrl(defaultSugarLogoMark);
    }
})

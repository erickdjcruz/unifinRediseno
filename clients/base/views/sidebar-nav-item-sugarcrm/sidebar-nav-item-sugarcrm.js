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
 * @class View.Views.Base.SidebarNavItemSugarcrmView
 * @alias SUGAR.App.view.views.BaseSidebarNavItemSugarcrmView
 * @extends View.Views.Base.SidebarNavItemView
 */
({
    extendsFrom: 'SidebarNavItemView',

    /**
     * The URL the SugarCRM logo should navigate to on click
     */
    logoTargetUrl: 'https://www.sugarcrm.com',

    /**
     * The URL of the SugarCRM logo to use
     */
    logoImageUrl: 'themes/default/images/company_logo_dark.png',

    /**
     * Overrides the primary action click behavior to allow the standard
     * anchor behavior for the image link
     *
     * @override
     */
    primaryActionOnClick: function() {}
})

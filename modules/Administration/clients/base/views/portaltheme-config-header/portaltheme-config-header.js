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
 * @class View.Views.Base.AdministrationPortalThemeConfigHeaderView
 * @alias SUGAR.App.view.views.BasePortalThemeConfigHeaderView
 * @extends View.Views.Base.AdministrationConfigHeaderView
 */
({
    extendsFrom: 'AdministrationConfigHeaderView',

    /**
     * URL of main portal config page. Used to navigate back on clicking "cancel"
     */
    portalConfigUrl: '#bwc/index.php?module=ModuleBuilder&action=index&type=sugarportal',

    /**
     * Event handler for clicking "Cancel" button
     * @param evt
     */
    cancelConfig: function(evt) {
        evt.stopPropagation();
        window.location.href = this.portalConfigUrl;
    }
})

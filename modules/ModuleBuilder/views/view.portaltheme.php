<?php
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


use Sugarcrm\Sugarcrm\Security\ValueObjects\PlatformName;

class ViewPortalTheme extends SugarView
{
    /**
     * @see SugarView::_getModuleTitleParams()
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getModuleTitleParams($browserTitle = false)
    {
        global $mod_strings;

        return [
            translate('LBL_MODULE_NAME', 'Administration'),
            ModuleBuilderController::getModuleTitle(),
        ];
    }

    // DO NOT REMOVE - overrides parent ViewEdit preDisplay() which attempts to load a bean for a non-existent module
    public function preDisplay()
    {
    }

    /**
     * This function loads portal config vars from db and sets them for the view
     * @see SugarView::display() for more info
     */
    public function display()
    {
        global $current_user, $app_strings;

        $smarty = new Sugar_Smarty();
        $smarty->assign('mod', $GLOBALS['mod_strings']);
        $smarty->assign('token', session_id());
        $smarty->assign('siteURL', $GLOBALS['sugar_config']['site_url']);

        //Loading label
        $smarty->assign('LBL_LOADING', $app_strings['LBL_ALERT_TITLE_LOADING']);

        $theme = new SidecarTheme(PlatformName::base());
        $smarty->assign('css_url', $theme->getCSSURL());


        $ajax = new AjaxCompose();
        $ajax->addCrumb(translate('LBL_SUGARPORTAL', 'ModuleBuilder'), 'ModuleBuilder.main("sugarportal")');
        $ajax->addCrumb(ucwords(translate('LBL_PORTAL_THEME')), '');
        $ajax->addSection('center', translate('LBL_SUGARPORTAL', 'ModuleBuilder'), $smarty->fetch('modules/ModuleBuilder/tpls/portaltheme.tpl'));
        echo $ajax->getJavascript();
    }
}

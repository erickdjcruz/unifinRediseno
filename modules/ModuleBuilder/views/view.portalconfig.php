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

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class ViewPortalConfig extends SugarView
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
     * Strips any HTML tags from an array of User IDs => names to prevent XSS
     * attacks
     *
     * @param array $userList the list of User IDs => names to sanitize
     */
    public function sanitizeUserList(&$userList)
    {
        foreach ($userList as $id => $name) {
            $userList[$id] = SugarCleaner::stripTags($name, false);
        }
    }

    /**
     * Formats a list of Portal tabs (module names) to display correctly on the
     * Portal Config screen
     * @param array $tabsList the list of Portal module names to format
     * @return array the list of formatted Portal module name objects
     */
    public function formatTabsList($tabsList): array
    {
        // Remove the "Home" module from the list so that it isn't shown in the
        // config UI. It is re-inserted when the configuration is saved.
        $tabsList = array_diff($tabsList, ['Home']);
        array_walk($tabsList, function (&$moduleName) {
            $moduleName = [
                'module' => $moduleName,
                'label' => translate($moduleName),
            ];
        });
        return $tabsList;
    }

    /**
     * Decodes a Portal config setting, allowing both string and single-level
     * arrays of strings to be easily HTML decoded before passing into the
     * Portal config template
     * @param array|string $field the Portal config setting to decode
     * @return mixed the decoded field
     */
    public function decodeConfig($field)
    {
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                $field[$key] = html_entity_decode($value, ENT_COMPAT);
            }
        } else {
            $field = html_entity_decode($field, ENT_COMPAT);
        }
        return $field;
    }

    /**
     * This function loads portal config vars from db and sets them for the view
     * @see SugarView::display() for more info
     */
    public function display()
    {
        $isServe = PortalFactory::getInstance('Settings')->isServe();
        $portalFields = [
            'appStatus' => 'offline',
            'logoURL' => '',
            'logomarkURL' => '',
            'maxQueryResult' => '20',
            'maxSearchQueryResult' => '5',
            'defaultUser' => '',
            'caseDeflection' => $isServe ? 'enabled' : 'disabled',
            'showKBNotes' => 'enabled',
            'allowCloseCase' => $isServe ? 'allow' : 'disallow',
            'contactInfo' => [
                'contactPhone' => '',
                'contactEmail' => '',
                'contactURL' => '',
            ],
            'caseVisibility' => 'all',
            'emailVisibility' => 'related_contacts',
            'messageVisibility' => 'related_contacts',
            'enableSelfSignUp' => 'disabled',
        ];
        $userList = BeanFactory::newBean('Users')->getUserArray();
        $userList[''] = '';
        $this->sanitizeUserList($userList);

        $portalTabsList = TabController::getPortalTabs();
        $displayedPortalTabs = $this->formatTabsList($portalTabsList);
        $hiddenPortalTabs = $this->formatTabsList(array_diff(TabController::getAllPortalTabs(), $portalTabsList));

        $admin = Administration::getSettings();

        $portalConfig = $admin->getConfigForModule('portal', 'support', true);
        $portalConfig['appStatus'] = !empty($portalConfig['on']) ? 'online' : 'offline';
        $smarty = new Sugar_Smarty();
        $smarty->assign('displayedPortalTabs', array_values($displayedPortalTabs));
        $smarty->assign('hiddenPortalTabs', array_values($hiddenPortalTabs));
        foreach ($portalFields as $fieldName => $fieldDefault) {
            if (isset($portalConfig[$fieldName])) {
                $smarty->assign($fieldName, $this->decodeConfig($portalConfig[$fieldName]));
            } else {
                $smarty->assign($fieldName, $fieldDefault);
            }
        }
        $smarty->assign('userList', $userList);
        $smarty->assign('welcome', $GLOBALS['mod_strings']['LBL_SYNCP_WELCOME']);
        $smarty->assign('mod', $GLOBALS['mod_strings']);
        $smarty->assign('app', $GLOBALS['app_strings']);
        $smarty->assign('siteURL', $GLOBALS['sugar_config']['site_url']);
        $label = $this->request->getValidInputRequest('label');
        $smarty->assign('isServe', $isServe);
        if ($label !== null) {
            $smarty->assign('label', $label);
        }

        $ajax = new AjaxCompose();
        $ajax->addCrumb(translate('LBL_SUGARPORTAL', 'ModuleBuilder'), 'ModuleBuilder.main("sugarportal")');
        $ajax->addCrumb(ucwords(translate('LBL_PORTAL_CONFIGURE')), '');
        $ajax->addSection('center', translate('LBL_SUGARPORTAL', 'ModuleBuilder'), $smarty->fetch('modules/ModuleBuilder/tpls/portalconfig.tpl'));

        echo $ajax->getJavascript();
    }
}

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
/*********************************************************************************
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once 'modules/Administration/Forms.php';

class ViewConfiguretabs extends SugarView
{
    /**
     * @see SugarView::_getModuleTitleParams()
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getModuleTitleParams($browserTitle = false)
    {
        global $mod_strings;

        return [
            "<a href='#Administration'>" . $mod_strings['LBL_MODULE_NAME'] . '</a>',
            $mod_strings['LBL_CONFIG_TABS'],
        ];
    }

    /**
     * @see SugarView::preDisplay()
     */
    public function preDisplay()
    {
        global $current_user;

        if (!is_admin($current_user)) {
            sugar_die('Unauthorized access to administration.');
        }
    }

    /**
     * @see SugarView::display()
     */
    public function display()
    {
        global $mod_strings;
        global $app_list_strings;
        global $app_strings;

        $controller = new TabController();
        $tabs = $controller->get_tabs_system();
        // Remove Home module from UI.  We add it back to front of display tab list on save.
        if (isset($tabs[0]['Home'])) {
            unset($tabs[0]['Home']);
        }
        if (isset($tabs[1]['Home'])) {
            unset($tabs[1]['Home']);
        }
        $enabled = [];
        foreach ($tabs[0] as $key => $value) {
            $enabled[] = ['module' => $key, 'label' => translate($key)];
        }
        $disabled = [];
        foreach ($tabs[1] as $key => $value) {
            // only show accessible modules, remove nonaccessibles
            if (SugarACL::checkAccess($key, 'access')) {
                $disabled[] = ['module' => $key, 'label' => translate($key)];
            }
        }

        $users_pinned_modules = $controller->get_users_pinned_modules();
        $user_can_edit = $controller->get_users_can_edit();
        $number_pinned_modules = $controller->get_number_pinned_modules();

        $this->ss->assign([
            'APP' => $GLOBALS['app_strings'],
            'MOD' => $GLOBALS['mod_strings'],
            'users_pinned_modules' => $users_pinned_modules,
            'user_can_edit' => $user_can_edit,
            'number_pinned_modules' => $number_pinned_modules,
            'enabled_tabs' => json_encode($enabled),
            'disabled_tabs' => json_encode($disabled),
            'title' => $this->getModuleTitle(false),
        ]);

        //get list of all subpanels and panels to hide
        $mod_list_strings_key_to_lower = array_change_key_case($app_list_strings['moduleList']);
        $panels_arr = SubPanelDefinitions::get_all_subpanels();
        $hidpanels_arr = SubPanelDefinitions::get_hidden_subpanels();

        if (!$hidpanels_arr || !is_array($hidpanels_arr)) {
            $hidpanels_arr = [];
        }

        //create array of subpanels to show, used to create Drag and Drop widget
        $enabled = [];
        foreach ($panels_arr as $key) {
            if (empty($key)) {
                continue;
            }
            $key = strtolower($key);
            $enabled[] = ['module' => $key, 'label' => $mod_list_strings_key_to_lower[$key]];
        }

        //now create array of subpanels to hide for use in Drag and Drop widget
        $disabled = [];
        foreach ($hidpanels_arr as $key) {
            if (empty($key)) {
                continue;
            }
            $key = strtolower($key);
            // we need this here for with RLI's are disabled as they shouldn't be seen in the list
            if ($key == 'revenuelineitems' && safeInArray('RevenueLineItems', $GLOBALS['modInvisList'])) {
                continue;
            }
            $disabled[] = ['module' => $key, 'label' => $mod_list_strings_key_to_lower[$key]];
        }

        $this->ss->assign('enabled_panels', json_encode($enabled));
        $this->ss->assign('disabled_panels', json_encode($disabled));

        echo $this->ss->fetch('modules/Administration/templates/ConfigureTabs.tpl');
    }
}

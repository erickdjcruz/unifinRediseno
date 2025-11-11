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


class StudioWizard
{
    public $tplfile = 'modules/Studio/wizards/tpls/wizard.tpl';
    public $wizard = 'StudioWizard';
    public $status = '';
    public $assign = [];

    public function welcome()
    {
        return $GLOBALS['mod_strings']['LBL_SW_WELCOME'];
    }

    public function options()
    {
        $options = ['SelectModuleWizard' => $GLOBALS['mod_strings']['LBL_SW_EDIT_MODULE'],
            'EditDropDownWizard' => $GLOBALS['mod_strings']['LBL_SW_EDIT_DROPDOWNS'],
            'RenameTabs' => $GLOBALS['mod_strings']['LBL_SW_RENAME_TABS'],
            'ConfigureTabs' => $GLOBALS['mod_strings']['LBL_SW_EDIT_TABS'],
            'Portal' => $GLOBALS['mod_strings']['LBL_SW_EDIT_PORTAL'],
            'Workflow' => $GLOBALS['mod_strings']['LBL_SW_EDIT_WORKFLOW'],
            'MigrateCustomFields' => $GLOBALS['mod_strings']['LBL_SW_MIGRATE_CUSTOMFIELDS'],


        ];
        if (!empty($GLOBALS['license']->settings['license_num_portal_users'])) {
            $options['SugarPortal'] = $GLOBALS['mod_strings']['LBL_SW_SUGARPORTAL'];
        }
        return $options;
    }

    public function back()
    {
    }

    public function process($option)
    {
        switch ($option) {
            case 'SelectModuleWizard':
                require_once 'modules/Studio/wizards/' . $option . '.php';
                $newWiz = new $option();
                $newWiz->display();
                break;
            case 'EditDropDownWizard':
                require_once 'modules/Studio/wizards/' . $option . '.php';
                $newWiz = new $option();
                $newWiz->display();
                break;
            case 'RenameTabs':
                $script = navigateToSidecar(buildSidecarRoute('Administration', null, 'module-names-and-icons'));
                echo "<script>$script</script>";
                sugar_cleanup(true);
                // no break
            case 'ConfigureTabs':
                header('Location: index.php?module=Administration&action=ConfigureTabs');
                sugar_cleanup(true);
                // no break
            case 'Workflow':
                header('Location: index.php?module=WorkFlow&action=ListView');
                sugar_cleanup(true);
                // no break
            case 'Portal':
                header('Location: index.php?module=iFrames&action=index');
                sugar_cleanup(true);
                // no break
            case 'MigrateCustomFields':
                header('LOCATION: index.php?module=Administration&action=Development');
                sugar_cleanup(true);
                // no break
            case 'SugarPortal':
                header('LOCATION: index.php?module=Studio&action=Portal');
                sugar_cleanup(true);
                // no break
            case 'Classic':
                header('Location: index.php?module=DynamicLayout&action=index');
                sugar_cleanup(true);
                // no break
            default:
                $this->display();
        }
    }

    public function display($error = '')
    {
        echo $this->fetch($error);
    }

    public function fetch($error = '')
    {
        global $mod_strings;
        echo getClassicModuleTitle('StudioWizard', [$mod_strings['LBL_MODULE_TITLE']], false);
        $sugar_smarty = new Sugar_Smarty();
        $sugar_smarty->assign('welcome', $this->welcome());
        $sugar_smarty->assign('options', $this->options());
        $sugar_smarty->assign('MOD', $GLOBALS['mod_strings']);
        $sugar_smarty->assign('option', (!empty($_REQUEST['option']) ? $_REQUEST['option'] : ''));
        $sugar_smarty->assign('wizard', $this->wizard);
        $sugar_smarty->assign('error', $error);
        $sugar_smarty->assign('status', $this->status);
        $sugar_smarty->assign('mod', $mod_strings);
        foreach ($this->assign as $name => $value) {
            $sugar_smarty->assign($name, $value);
        }
        return $sugar_smarty->fetch($this->tplfile);
    }
}

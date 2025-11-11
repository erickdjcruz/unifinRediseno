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


class ViewPortalStyleUpload extends SugarView
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

    public function display()
    {
        $this->ss->assign('mod', $GLOBALS['mod_strings']);
        $this->ss->display('modules/ModuleBuilder/tpls/portalstyle_uploaded.tpl');
    }
}

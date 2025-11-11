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

class AdministrationViewCloudinsights extends SugarView
{
    /**
     * Insights configuration array
     * @var array
     */
    protected $insights = [];

    /**
     * @see SugarView::preDisplay()
     */
    public function preDisplay()
    {
        if (!is_admin($GLOBALS['current_user'])) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }

        $config = \SugarConfig::getInstance();
        $insights = $config->get('cloud_insight', []);

        if (empty($insights['enabled'])) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_CLOUD_INSTANCE']);
        }

        $this->insights = $insights;
    }

    /**
     * @see SugarView::display()
     */
    public function display()
    {

        $this->ss->assign('insights', $this->insights);
        $this->ss->assign('mod', $GLOBALS['mod_strings']);
        $this->ss->assign('APP', $GLOBALS['app_strings']);

        echo getClassicModuleTitle(
            'Administration',
            [
                "<a href='#Administration'>" . translate('LBL_MODULE_NAME') . '</a>',
                translate('LBL_VIEW_CLOUD_INSIGHTS'),
            ],
            false
        );

        echo $this->ss->fetch('modules/Administration/templates/CloudInsights.tpl');
    }
}

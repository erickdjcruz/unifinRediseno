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


use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureFlag;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\UserDownloadsHideOpiWpiPlugins;

class ViewPlugins extends ViewAjax
{
    /**
     * @see SugarView::display()
     */
    public function display()
    {
        global $app_strings;

        $sp = new SugarPlugins();

        $plugins = $sp->getPluginList();
        $pluginsCat = [
            'Outlook' => [
                'name' => $app_strings['LBL_PLUGIN_OUTLOOK_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_OUTLOOK_DESC'],
            ],
            'Word' => [
                'name' => $app_strings['LBL_PLUGIN_WORD_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_WORD_DESC'],
            ],
            'Excel' => [
                'name' => $app_strings['LBL_PLUGIN_EXCEL_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_EXCEL_DESC'],
            ],
        ];

        $features = Container::getInstance()->get(FeatureFlag::class);
        if ($features->isEnabled(UserDownloadsHideOpiWpiPlugins::getName())) {
            unset($pluginsCat['Outlook']);
            unset($pluginsCat['Word']);
        }

        $str = '<table cellpadding="0" cellspacing="0" class="detail view">';
        $str .= '<tr><th>';
        $str .= "<h4>{$app_strings['LBL_PLUGINS_TITLE']}</h4>";
        $str .= '</th></tr>';

        $str .= "<tr><td style='padding-left: 10px;'>{$app_strings['LBL_PLUGINS_DESC']}</td></tr>";

        foreach ($pluginsCat as $key => $value) {
            $pluginContents = '';

            foreach ($plugins as $plugin) {
                $raw_name = urlencode((string)$plugin['raw_name']);
                $display_name = str_replace('_', ' ', (string)$plugin['formatted_name']);
                if (strpos($display_name, $key) !== false) {
                    $pluginContents .= "<li><a href='index.php?module=Home&action=DownloadPlugin&plugin={$raw_name}'>{$display_name}</a></li>";
                }
            }

            //If we have pluginContents value, combine together
            if (!empty($pluginContents)) {
                $str .= "<tr><td valign='top' width='80' style='padding-right: 10px; padding-left: 10px;'>";
                $str .= "<b>{$value['name']}</b><br>";
                $str .= $value['desc'];
                $str .= '<ul id="pluginList">';
                $str .= $pluginContents;
                $str .= '</ul></td></tr>';
            }
        }

        $str .= '</table>';

        echo $str;
    }
}

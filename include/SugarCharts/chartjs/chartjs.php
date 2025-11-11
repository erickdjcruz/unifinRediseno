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

/**
 * Instance of SugarChart specifically for Sucrose library
 * @api
 */
class chartjs extends JsChart
{
    public $supports_image_export = true;

    /**
     * Get all of the Javascript resources that are needed to display a chart
     *
     * @return  string A concatenated list of script tags with compiled resource paths
     */
    public function getChartResources()
    {
        return '
        <script src="' . getJSPath('include/javascript/chartjs/chart.min.js') . '"></script>
        <script src="' . getJSPath('include/javascript/chartjs/chartjs-chart-treemap.min.js') . '"></script>
        <script src="' . getJSPath('include/javascript/chartjs/chartjs-plugin-datalabels.min.js') . '"></script>
        <script type="text/javascript" src="' . getJSPath('include/SugarCharts/chartjs/js/sugarCharts.js') . '"></script>
        <script src="' . getJSPath('include/javascript/chartjs/Chart_2_9_4.js') . '"></script>
        <script src="' . getJSPath('include/javascript/chartjs/chartjs-plugin-datalabels-v1.js') . '"></script>
        <script src="' . getJSPath('include/javascript/chartjs/chart.funnel.js') . '"></script>
        ';
    }

    /**
     * Display method to invoke Smarty instance with template variables
     *
     * @param string $name chart id assigned for template
     * @param string $xmlFile chart data in xml format to be processed
     * @param string $width default width of chart container
     * @param string $height default height of chart container
     * @param string $resize allow resizing of chart container (deprecated)
     * @return  string Smarty template instance with chart containers and source files
     */
    public function display($name, $xmlFile, $width = '320', $height = '480', $resize = false)
    {
        global $app_strings;

        parent::display($name, $xmlFile, $width, $height, $resize);
        $this->ss->assign('noDataString', $app_strings['LBL_NO_DATA_AVAILABLE']);
        return $this->ss->fetch('include/SugarCharts/chartjs/tpls/chart.tpl');
    }
}

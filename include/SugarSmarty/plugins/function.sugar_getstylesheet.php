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
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_getstylesheet} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_getstylesheet<br>
 * Purpose:  Creates script tag for filename with caching string. Caching is based on file edited date.
 *
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_sugar_getstylesheet($params, &$smarty)
{
    if (!isset($params['file'])) {
        $smarty->trigger_error($GLOBALS['app_strings']['ERR_MISSING_REQUIRED_FIELDS'] . 'file');
    }
    $ver = filemtime($params['file']);
    return '<link rel="stylesheet" href="' . $params['file'] . '?v=' . $ver . '" type="text/css">';
}

<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
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

if ($GLOBALS['sugar_config']['disable_export'] || (!empty($GLOBALS['sugar_config']['admin_export_only']) && !is_admin($current_user))) {
    die('Exports Disabled');
}
$export_object = BeanFactory::retrieveBean('DataSets', $_REQUEST['record']);
if ($export_object === null) {
    sugar_die($app_strings['ERROR_NO_RECORD']);
}
$csv_output = $export_object->export_csv();
$module = $export_object->getModuleName();
header('Pragma: cache');
header("Content-Disposition: inline; filename={$module}.csv");
header('Content-Type: text/csv; charset=UTF-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . TimeDate::httpTime());
header('Cache-Control: post-check=0, pre-check=0', false);
header('Content-Length: ' . strlen($csv_output));
print $csv_output;
sugar_cleanup();
exit;

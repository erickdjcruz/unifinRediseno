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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

/*
**this is the ajax call that is called from RebuildJSLang.php.  It processes
**the Request object in order to call correct methods for repairing/rebuilding JSfiles
*Note that minify.php has already been included as part of index.php, so no need to include again.
*/

//set default root directory
$from = getcwd();
$request = InputValidation::getService();
$rootDirectory = $request->getValidInputRequest('root_directory', ['Assert\File']);
$jsAdminRepair = $request->getValidInputRequest('js_admin_repair', ['Assert\Type' => (['type' => 'string'])]);

if (!empty($rootDirectory)) {
    $from = $rootDirectory;
}

//this script can take a while, change max execution time to 10 mins
$tmp_time = ini_get('max_execution_time');
ini_set('max_execution_time', '600');

//concatenate mode, call the files that will concatenate javascript group files
$_REQUEST['js_rebuild_concat'] = 'rebuild';
require_once 'jssource/minify.php';

//set execution time back to what it was
ini_set('max_execution_time', $tmp_time);

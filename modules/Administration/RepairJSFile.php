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

require_once 'include/SugarSmarty/plugins/function.sugar_csrf_form_token.php';
if (empty($_REQUEST['type']) || $_REQUEST['type'] !== 'concat') {
    sugar_die('Unsupported type');
}
if (!is_admin($current_user)) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}
global $mod_strings;

//echo out warning message and msgDiv
echo '<p>' . htmlspecialchars($mod_strings['LBL_REPAIR_JS_FILES_PROCESSING'], ENT_COMPAT) . '</p>';
echo '<div id="msgDiv"></div>';

//echo out script that will make an ajax call to process the files via callJSRepair.php
$postData = http_build_query([
    'module' => 'Administration',
    'action' => 'callJSRepair',
    'js_admin_repair' => 'concat',
    'root_directory' => getcwd(),
    'csrf_token' => smarty_function_sugar_csrf_form_token(['raw' => true], $smarty),
]);

$js = <<<JS
<script>
var ajxProgress;
var showMSG = 'true';
//when called, this function will make ajax call to rebuild/repair js files
function callJSRepair() {
    //begin main function that will be called
    ajaxCall = function(){
        //create success function for callback
        success = function() {              
            //turn off loading message
            ajaxStatus.hideStatus();
            var targetdiv = document.getElementById('msgDiv');
            targetdiv.textContent = SUGAR.language.get('Administration', 'LBL_REPAIR_JS_FILES_DONE_PROCESSING');
        }
        //set loading message and create url
        ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_PROCESSING_REQUEST'));
        postData = "{$postData}";
        //if this is a call already in progress, then just return               
        if (typeof ajxProgress != 'undefined') { 
            return;                            
        }
        //make asynchronous call to process js files
        var ajxProgress = YAHOO.util.Connect.asyncRequest(
            'POST',
            'index.php', 
            { 
                success: success, 
                failure: success
            }, 
            postData
        );
    };
    //show loading status and make ajax call
    window.setTimeout('ajaxCall()', 2000);
    return;
}
//call function, so it runs automatically    
callJSRepair();
</script>
JS;
echo $js;

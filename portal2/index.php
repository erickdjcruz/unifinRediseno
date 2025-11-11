<?php
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
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
$rootDir = dirname(getcwd());// up one
set_include_path(get_include_path() . PATH_SEPARATOR . $rootDir);
chdir($rootDir);
//initialize the various globals we use this is needed so modules.php properly registers the modules globals, otherwise they end up defined in wrong scope
global $sugar_config, $db, $fileName, $current_user, $locale, $current_language, $beanFiles, $beanList, $objectList, $moduleList, $modInvisList;
require_once 'include/entryPoint.php';
// Make sure the cache files exist
ensureJSCacheFilesExist();
$versionToken = getVersionedPath(null);
insert_csp_header();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Customer Self-Service Portal - Powered by SugarCRM.</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="SHORTCUT ICON" type="image/png"
          href="../include/images/sugar-favicon.png?v=<?php echo $versionToken; ?>">
    <!-- CSS -->
    <link rel="stylesheet" href="../styleguide/assets/css/build.tailwind.css?v=<?php echo $versionToken; ?>"
          type="text/css">
    <link rel="stylesheet" href="../styleguide/assets/css/loading.css?v=<?php echo $versionToken; ?>" type="text/css">
    <link rel="stylesheet" href="../styleguide/assets/css/gridstack.css?v=<?php echo $versionToken; ?>" type="text/css">
    <link rel="stylesheet" href="../styleguide/assets/css/gridstack-extra.css?v=<?php echo $versionToken; ?>"
          type="text/css">
    <link rel="stylesheet"
          href="lib/jquery-ui/css/smoothness/jquery-ui-1.11.4.custom.min.css?v=<?php echo $versionToken; ?>"
          type="text/css"/>
</head>
<body class="sugar-light-theme">
<div id="sidecar">
    <div id="portal">
        <div id="alerts" class="alert-top">
            <div class="alert-wrapper">
                <div class="alert alert-process">
                    <strong>
                        <div class="loading">
                            Loading<i class="l1">&#46;</i><i class="l2">&#46;</i><i class="l3">&#46;</i>
                        </div>
                    </strong>
                </div>
            </div>
        </div>
        <div id="header"></div>
        <div id="content"></div>
        <div id="drawers"></div>
    </div>
</div>

<script src="../include/javascript/modernizr.js?v=<?php echo $versionToken; ?>"></script>

<!-- Sidecar Scripts -->
<script src="../sidecar/minified/sidecar.min.js?v=<?php echo $versionToken; ?>"></script>
<script src="../cache/include/javascript/sugar_sidecar.min.js?v=<?php echo $versionToken; ?>"></script>

<!-- Portal specific JS -->
<script src="../cache/portal2/portal.min.js?v=<?php echo $versionToken; ?>"></script>
<script
    src="../cache/Expressions/functions_cache<?php if (!shouldResourcesBeMinified()) :
?>_debug<?php
endif; ?>.js?v=<?php echo $versionToken; ?>"></script>
<script src="config.js?v=<?php echo $versionToken; ?>"></script>
<script src="../cache/portal2/sugar_portal.min.js?v=<?php echo $versionToken; ?>"></script>

<script>
    var syncResult, view, layout, html;
    var App = SUGAR.App.init({
        el: '#sidecar',
        callback: function (app) {
            $('#alerts').empty();
            app.start();
        }
    });
    App.api.debug = App.config.debugSugarApi;
</script>
</body>
</html>

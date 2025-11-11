{*
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
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html {$langHeader} style="color-scheme: {$appearance|escape:'htmlall'};">
<head>
<link rel="SHORTCUT ICON" href="{$FAVICON_URL}">
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$SYSTEM_NAME}</title>
{$SUGAR_CSS}
<link rel='stylesheet' href='{sugar_getjspath file="styleguide/assets/css/sugar-theme-variables.css"}'/>
{if $AUTHENTICATED}
<link rel='stylesheet' href='{sugar_getjspath file="vendor/ytree/TreeView/css/folders/tree.css"}'/>
<link rel='stylesheet' href='{sugar_getjspath file="styleguide/assets/css/sucrose.css"}'/>
{/if}
{$SUGAR_JS}

{sugar_getscript file="include/javascript/mousetrap/mousetrap.min.js"}

<script type="text/javascript">
    <!--
    SUGAR.themes.theme_name      = '{$THEME}';
    SUGAR.themes.hide_image      = '{sugar_getimagepath file="hide.gif"}';
    SUGAR.themes.show_image      = '{sugar_getimagepath file="show.gif"}';
    SUGAR.themes.loading_image      = '{sugar_getimagepath file="img_loading.gif"}';
    if ( YAHOO.env.ua )
        UA = YAHOO.env.ua;
    -->

    if (window.parent && typeof(window.parent.SUGAR) !== 'undefined' && typeof(window.parent.SUGAR.App) !== 'undefined') {
        // update bwc context
        var app = window.parent.SUGAR.App;
        if (app.additionalComponents.sweetspot) {
            Mousetrap.bind('esc', function(e) {
                app.additionalComponents.sweetspot.hide()
                return false;
            });
            Mousetrap.bind('mod+shift+space', function(e) {
                app.additionalComponents.sweetspot.show()
                return false;
            });
        }
    }
</script>
</head>

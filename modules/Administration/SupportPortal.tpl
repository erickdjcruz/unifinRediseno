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


{if $helpFileExists}
<html {$langHeader}>
<head>
<title>{$title|escape:html}</title>
{$styleSheet}
<meta http-equiv="Content-Type" content="text/html; charset={$charset|escape:html}">
</head>
<body onLoad='window.focus();'>
<table width='100%'>
<tr>
    <td align='right'>
        <a href='javascript:window.print()'>{$MOD.LBL_HELP_PRINT|escape:html}</a> -
        <a href='mailto:?subject="{$MOD.LBL_SUGARCRM_HELP|escape:url}&body={$currentURL|escape:url}'>{$MOD.LBL_HELP_EMAIL|escape:html}</a> -
        <a href='#' onmousedown="createBookmarkLink('{$title|escape:javascript|escape:html}', '{$currentURL|escape:html}')">{$MOD.LBL_HELP_BOOKMARK|escape:html}</a>
    </td>
</tr>
</table>
<table class='edit view'>
<tr>
    <td>{include file="$helpPath"}</td>
</tr>
</table>
<script type="text/javascript" language="JavaScript">
<!--
function createBookmarkLink(title, url){
    if (document.all)
        window.external.AddFavorite(url, title);
    else if (window.sidebar)
        window.sidebar.addPanel(title, url, "")
}
-->
</script>
</body>
</html>	
{else}
<IFRAME frameborder="0" marginwidth="0" marginheight="0" bgcolor="#FFFFFF" SRC="{$iframeURL|escape:html}" TITLE="{$iframeURL|escape:html}" NAME="SUGARIFRAME" ID="SUGARIFRAME" WIDTH="100%" height="1000"></IFRAME>
{/if}
<?php
/* Smarty version 4.3.1, created on 2025-11-11 16:32:29
  from '/Applications/MAMP/htdocs/fourt/themes/RacerX/tpls/_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_6913651d410509_12481877',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9e655fafdde409d6438dc9f422313ac8f879f5ff' => 
    array (
      0 => '/Applications/MAMP/htdocs/fourt/themes/RacerX/tpls/_head.tpl',
      1 => 1711552720,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6913651d410509_12481877 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getjspath.php','function'=>'smarty_function_sugar_getjspath',),1=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getscript.php','function'=>'smarty_function_sugar_getscript',),2=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getimagepath.php','function'=>'smarty_function_sugar_getimagepath',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php echo $_smarty_tpl->tpl_vars['langHeader']->value;?>
 style="color-scheme: <?php echo htmlentities(mb_convert_encoding((string)$_smarty_tpl->tpl_vars['appearance']->value, 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8', true);?>
;">
<head>
<link rel="SHORTCUT ICON" href="<?php echo $_smarty_tpl->tpl_vars['FAVICON_URL']->value;?>
">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_CHARSET'];?>
">
<title><?php echo $_smarty_tpl->tpl_vars['SYSTEM_NAME']->value;?>
</title>
<?php echo $_smarty_tpl->tpl_vars['SUGAR_CSS']->value;?>

<link rel='stylesheet' href='<?php echo smarty_function_sugar_getjspath(array('file'=>"styleguide/assets/css/sugar-theme-variables.css"),$_smarty_tpl);?>
'/>
<?php if ($_smarty_tpl->tpl_vars['AUTHENTICATED']->value) {?>
<link rel='stylesheet' href='<?php echo smarty_function_sugar_getjspath(array('file'=>"vendor/ytree/TreeView/css/folders/tree.css"),$_smarty_tpl);?>
'/>
<link rel='stylesheet' href='<?php echo smarty_function_sugar_getjspath(array('file'=>"styleguide/assets/css/sucrose.css"),$_smarty_tpl);?>
'/>
<?php }
echo $_smarty_tpl->tpl_vars['SUGAR_JS']->value;?>


<?php echo smarty_function_sugar_getscript(array('file'=>"include/javascript/mousetrap/mousetrap.min.js"),$_smarty_tpl);?>


<?php echo '<script'; ?>
 type="text/javascript">
    <!--
    SUGAR.themes.theme_name      = '<?php echo $_smarty_tpl->tpl_vars['THEME']->value;?>
';
    SUGAR.themes.hide_image      = '<?php echo smarty_function_sugar_getimagepath(array('file'=>"hide.gif"),$_smarty_tpl);?>
';
    SUGAR.themes.show_image      = '<?php echo smarty_function_sugar_getimagepath(array('file'=>"show.gif"),$_smarty_tpl);?>
';
    SUGAR.themes.loading_image      = '<?php echo smarty_function_sugar_getimagepath(array('file'=>"img_loading.gif"),$_smarty_tpl);?>
';
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
<?php echo '</script'; ?>
>
</head>
<?php }
}

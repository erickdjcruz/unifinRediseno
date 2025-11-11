<?php
/* Smarty version 4.3.1, created on 2025-11-11 16:32:29
  from '/Applications/MAMP/htdocs/fourt/themes/RacerX/tpls/header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_6913651d3f3cf2_53669663',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a706a821dfd1bab85a171c1bfb68193992dffc7e' => 
    array (
      0 => '/Applications/MAMP/htdocs/fourt/themes/RacerX/tpls/header.tpl',
      1 => 1711552720,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:themes/RacerX/tpls/_head.tpl' => 1,
  ),
),false)) {
function content_6913651d3f3cf2_53669663 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:themes/RacerX/tpls/_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('theme_template'=>true), 0, false);
?>
<body class="yui-skin-sam sugar-<?php echo htmlentities(mb_convert_encoding((string)$_smarty_tpl->tpl_vars['appearance']->value, 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8', true);?>
-theme">
    <a name="top"></a>
    <div style="position:fixed;top:0;left:0;width:1px;height:1px;z-index:21;"></div>
    <div class="clear"></div>

<div id="main">
    <div id="content">
        <?php if ($_smarty_tpl->tpl_vars['use_table_container']->value) {?>
        <table style="width:100%" id="contentTable"><tr><td>
        <?php }
}
}

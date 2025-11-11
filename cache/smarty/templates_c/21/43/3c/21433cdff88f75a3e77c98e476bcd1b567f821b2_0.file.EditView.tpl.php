<?php
/* Smarty version 4.3.1, created on 2025-11-11 16:32:29
  from '/Applications/MAMP/htdocs/fourt/modules/Configurator/tpls/EditView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_6913651d570be5_47615318',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '21433cdff88f75a3e77c98e476bcd1b567f821b2' => 
    array (
      0 => '/Applications/MAMP/htdocs/fourt/modules/Configurator/tpls/EditView.tpl',
      1 => 1711552720,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6913651d570be5_47615318 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_csrf_form_token.php','function'=>'smarty_function_sugar_csrf_form_token',),1=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_help.php','function'=>'smarty_function_sugar_help',),2=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getimage.php','function'=>'smarty_function_sugar_getimage',),));
?>
<style>
    .company_logo_image_container {
        height: 24px;
        width: 180px;
    }
    .dark-bg {
        background-color: #2b2d2e;
    }
    .light-bg {
        background-color: #ffffff;
    }
    #company_logo_image,
    #company_logo_image_dark {
        max-height: 100%;
        max-width: 100%;
    }
</style>
<form name="ConfigureSettings" enctype='multipart/form-data' method="POST" action="index.php" onSubmit="return (add_checks(document.ConfigureSettings) && check_form('ConfigureSettings'));">
<?php echo smarty_function_sugar_csrf_form_token(array(),$_smarty_tpl);?>

<input type='hidden' name='action' value='SaveConfig'/>
<input type='hidden' name='module' value='Configurator'/>
<span class='error'><?php echo $_smarty_tpl->tpl_vars['error']->value['main'];?>
</span>
<table width="100%" cellpadding="0" cellspacing="1" border="0" class="actionsContainer">
<tr>

	<td>
		<input title="<?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_SAVE_BUTTON_TITLE'];?>
" accessKey="<?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_SAVE_BUTTON_KEY'];?>
" class="button primary" id="ConfigureSettings_save_button" type="submit"  name="save" value="  <?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_SAVE_BUTTON_LABEL'];?>
  " >
		<!-- &nbsp;<input title="<?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_SAVE_BUTTON_TITLE'];?>
"  id="ConfigureSettings_restore_button"  class="button"  type="submit" name="restore" value="  <?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_RESTORE_BUTTON_LABEL'];?>
  " > -->
		&nbsp;<input title="<?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_CANCEL_BUTTON_TITLE'];?>
" id="ConfigureSettings_cancel_button"   onclick="parent.SUGAR.App.router.navigate('#Administration', {trigger: true})" class="button"  type="button" name="cancel" value="  <?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_CANCEL_BUTTON_LABEL'];?>
  " > </td>
	<td align="right" nowrap>
		<span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span> <?php echo $_smarty_tpl->tpl_vars['APP']->value['NTC_REQUIRED'];?>

	</td>
</tr>
</table>


<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
<tr>
	<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['DEFAULT_SYSTEM_SETTINGS'];?>
</h4></th>
</tr>

	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LIST_ENTRIES_PER_LISTVIEW'];?>
: <?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['list_entries_per_listview_help']->value),$_smarty_tpl);?>
</td>
		<td  >
			<input type='text' size='4' id='ConfigureSettings_list_max_entries_per_page' name='list_max_entries_per_page' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['list_max_entries_per_page'];?>
'>
		</td>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LIST_ENTRIES_PER_SUBPANEL'];?>
: <?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['list_entries_per_subpanel_help']->value),$_smarty_tpl);?>
</td>
		<td  >
			<input type='text' size='4' id='ConfigureSettings_list_max_entries_per_subpanel' name='list_max_entries_per_subpanel' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['list_max_entries_per_subpanel'];?>
'>
		</td>
	</tr>
    <tr>
        <td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['FREEZE_FIRST_COLUMN'];?>
: <?php ob_start();
echo $_smarty_tpl->tpl_vars['MOD']->value['FREEZE_FIRST_COLUMN_HELP'];
$_prefixVariable1 = ob_get_clean();
echo smarty_function_sugar_help(array('text'=>$_prefixVariable1),$_smarty_tpl);?>
</td>
        <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['allow_freeze_first_column'])) {?>
            <?php $_smarty_tpl->_assignInScope('allow_freeze_first_column_checked', 'CHECKED');?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('allow_freeze_first_column_checked', '');?>
        <?php }?>
        <td >
            <input type='hidden' name='allow_freeze_first_column' value='false'>
            <input name='allow_freeze_first_column'  type="checkbox" class="checkbox" value="true" <?php echo $_smarty_tpl->tpl_vars['allow_freeze_first_column_checked']->value;?>
>
        </td>
    </tr>
	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['DISPLAY_RESPONSE_TIME'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['calculate_response_time'])) {?>
			<?php $_smarty_tpl->_assignInScope('calculate_response_time_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('calculate_response_time_checked', '');?>
		<?php }?>
		<td ><input type='hidden' name='calculate_response_time' value='false'><input name='calculate_response_time'  type="checkbox" value="true" <?php echo $_smarty_tpl->tpl_vars['calculate_response_time_checked']->value;?>
></td>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_MODULE_FAVICON'];?>
 &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_MODULE_FAVICON_HELP']),$_smarty_tpl);?>
 </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['default_module_favicon'])) {?>
			<?php $_smarty_tpl->_assignInScope('default_module_favicon', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('default_module_favicon', '');?>
		<?php }?>
		<td >
			<input type='hidden' name='default_module_favicon' value='false'>
			<input name='default_module_favicon'  type="checkbox" value="true" <?php echo $_smarty_tpl->tpl_vars['default_module_favicon']->value;?>
>
		</td>
	</tr>
	<tr>
		<td scope="row" width='15%' nowrap><?php echo $_smarty_tpl->tpl_vars['MOD']->value['SYSTEM_NAME'];?>
 </td>
		<td width='35%'>
			<input type='text' name='system_name' value='<?php echo $_smarty_tpl->tpl_vars['settings']->value['system_name'];?>
'>
		</td>


        <td  scope="row" nowrap><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_USE_REAL_NAMES'];?>
: &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_USE_REAL_NAMES_DESC']),$_smarty_tpl);?>
</td>
        <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['use_real_names'])) {?>
            <?php $_smarty_tpl->_assignInScope('use_real_names', 'CHECKED');?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('use_real_names', '');?>
        <?php }?>
        <td>
            <input type='hidden' name='use_real_names' value='false'>
            <input name='use_real_names'  type="checkbox" value="true" <?php echo $_smarty_tpl->tpl_vars['use_real_names']->value;?>
>
        </td>
    </tr>
    <tr>
        <td  scope="row" width='12%' nowrap>
        <?php echo $_smarty_tpl->tpl_vars['MOD']->value['CURRENT_LOGO'];?>
&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['CURRENT_LOGO_HELP']),$_smarty_tpl);?>

        </td>
        <td width="35%">
            <div class="company_logo_image_container light-bg">
                <img id="company_logo_image" src="<?php echo $_smarty_tpl->tpl_vars['company_logo']->value;?>
"
                     alt="<?php echo $_smarty_tpl->tpl_vars['mod_strings']->value['LBL_LOGO'];?>
" />
            </div>
        </td>
        <td  scope="row"> <?php echo $_smarty_tpl->tpl_vars['MOD']->value['SHOW_DOWNLOADS_TAB'];?>
: &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['SHOW_DOWNLOADS_TAB_HELP']),$_smarty_tpl);?>
 </td>
		<?php if (!(isset($_smarty_tpl->tpl_vars['config']->value['show_download_tab'])) || !empty($_smarty_tpl->tpl_vars['config']->value['show_download_tab'])) {?>
			<?php $_smarty_tpl->_assignInScope('show_download_tab_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('show_download_tab_checked', '');?>
		<?php }?>
		<td ><input type='hidden' name='show_download_tab' value='false'><input name='show_download_tab'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['show_download_tab_checked']->value;?>
></td>
    </tr>
    <tr>
        <td  scope="row" width='12%' nowrap>
            <?php echo $_smarty_tpl->tpl_vars['MOD']->value['NEW_LOGO'];?>
&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['NEW_LOGO_HELP_NO_SPACE']),$_smarty_tpl);?>

        </td>
        <td  width='35%'>
            <div id="container_upload"></div>
            <input type="text" id="commit_company_logo" name="commit_company_logo" style="display:none"/>
        </td>
    </tr>
    <tr>
        <td scope="row" nowrap>
            <?php echo $_smarty_tpl->tpl_vars['MOD']->value['CURRENT_LOGO_DARK'];?>
&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['CURRENT_LOGO_DARK_HELP']),$_smarty_tpl);?>

        </td>
        <td>
            <div class="company_logo_image_container dark-bg">
                <img id="company_logo_image_dark" src="<?php echo $_smarty_tpl->tpl_vars['company_logo_dark']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['mod_strings']->value['LBL_LOGO_DARK'];?>
" />
            </div>
        </td>
    </tr>
    <tr>
        <td scope="row">
            <?php echo $_smarty_tpl->tpl_vars['MOD']->value['NEW_LOGO_DARK'];?>
&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['NEW_LOGO_HELP_NO_SPACE']),$_smarty_tpl);?>

        </td>
        <td>
            <div id="container_upload_dark"></div>
            <input type="text" id="commit_company_logo_dark" name="commit_company_logo_dark" style="display:none"/>
        </td>
    </tr>
    <tr>
        <td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LEAD_CONV_OPTION'];?>
:&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LEAD_CONV_OPT_HELP']),$_smarty_tpl);?>
</td>
        <td><select name="lead_conv_activity_opt"><?php echo $_smarty_tpl->tpl_vars['lead_conv_activities']->value;?>
</select></td>
        <td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['COLLAPSE_SUBPANELS'];?>
: &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_COLLAPSE_SUBPANELS_DESC']),$_smarty_tpl);?>
</td>
        <td>
            <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['collapse_subpanels'])) {?>
                <?php $_smarty_tpl->_assignInScope('collapse_subpanels_checked', 'CHECKED');?>
            <?php } else { ?>
                <?php $_smarty_tpl->_assignInScope('collapse_subpanels_checked', '');?>
            <?php }?>
            <input type='hidden' name='collapse_subpanels' value='false'>
            <input type='checkbox' name='collapse_subpanels' value='true' <?php echo $_smarty_tpl->tpl_vars['collapse_subpanels_checked']->value;?>
>
        </td>
    </tr>
    <tr>
        <td  scope="row" nowrap><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ENABLE_ACTION_MENU'];?>
: &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_ENABLE_ACTION_MENU_DESC']),$_smarty_tpl);?>
</td>
    <?php if ((isset($_smarty_tpl->tpl_vars['config']->value['enable_action_menu'])) && $_smarty_tpl->tpl_vars['config']->value['enable_action_menu'] != "true") {?>
        <?php $_smarty_tpl->_assignInScope('enable_action_menu', '');?>
        <?php } else { ?>
        <?php $_smarty_tpl->_assignInScope('enable_action_menu', 'CHECKED');?>
    <?php }?>
        <td>
            <input type='hidden' name='enable_action_menu' value='false'>
            <input name='enable_action_menu'  type="checkbox" value="true" <?php echo $_smarty_tpl->tpl_vars['enable_action_menu']->value;?>
>
        </td>

        <td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LOCK_SUBPANELS'];?>
: &nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_LOCK_SUBPANELS_DESC']),$_smarty_tpl);?>
</td>
        <td  >
            <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['lock_subpanels'])) {?>
                <?php $_smarty_tpl->_assignInScope('lock_subpanels_checked', 'CHECKED');?>
            <?php } else { ?>
                <?php $_smarty_tpl->_assignInScope('lock_subpanels_checked', '');?>
            <?php }?>
            <input type='hidden' name='lock_subpanels' value='false'>
            <input type='checkbox' name='lock_subpanels' value='true' <?php echo $_smarty_tpl->tpl_vars['lock_subpanels_checked']->value;?>
>
        </td>
    </tr>
</table>

<?php if ($_smarty_tpl->tpl_vars['proxy_visible']->value) {?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">

	<tr>
	<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_TITLE'];?>
</h4></th>
	</tr>
	<tr>
	<td width="25%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_ON'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_ON_DESC']),$_smarty_tpl);?>
</td>
		<?php if (!empty($_smarty_tpl->tpl_vars['settings']->value['proxy_on'])) {?>
		<?php $_smarty_tpl->_assignInScope('proxy_on_checked', 'CHECKED');?>
	<?php } else { ?>
		<?php $_smarty_tpl->_assignInScope('proxy_on_checked', '');?>
	<?php }?>
	<td width="75%" align="left"  valign='middle' colspan='3'><input type='hidden' name='proxy_on' value='0'><input name="proxy_on" id="proxy_on" value="1" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['proxy_on_checked']->value;?>
 onclick='toggleDisplay_2("proxy_config_display")'></td>
	</tr><tr>
	<td colspan="4">
	<div id="proxy_config_display" style='display:<?php echo $_smarty_tpl->tpl_vars['PROXY_CONFIG_DISPLAY']->value;?>
'>
		<table width="100%" cellpadding="0" cellspacing="1"><tr>
		<td width="15%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_HOST'];?>
<span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td width="35%" ><input type="text" id="proxy_host" name="proxy_host" size="25"  value="<?php echo $_smarty_tpl->tpl_vars['settings']->value['proxy_host'];?>
" tabindex='1' ></td>
		<td width="15%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_PORT'];?>
<span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td width="35%" ><input type="text" id="proxy_port" name="proxy_port" size="6"  value="<?php echo $_smarty_tpl->tpl_vars['settings']->value['proxy_port'];?>
" tabindex='1' ></td>
		</tr><tr>
		<td width="15%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_AUTH'];?>
</td>
	<?php if (!empty($_smarty_tpl->tpl_vars['settings']->value['proxy_auth'])) {?>
		<?php $_smarty_tpl->_assignInScope('proxy_auth_checked', 'CHECKED');?>
	<?php } else { ?>
		<?php $_smarty_tpl->_assignInScope('proxy_auth_checked', '');?>
	<?php }?>
		<td width="35%" align="left"  valign='middle' ><input type='hidden' name='proxy_auth' value='0'><input id="proxy_auth" name="proxy_auth" value="1" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['proxy_auth_checked']->value;?>
 onclick='toggleDisplay_2("proxy_auth_display")'> </td>
		</tr></table>

		<div id="proxy_auth_display" style='display:<?php echo $_smarty_tpl->tpl_vars['PROXY_AUTH_DISPLAY']->value;?>
'>

		<table width="100%" cellpadding="0" cellspacing="1"><tr>
		<td width="15%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_USERNAME'];?>
<span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>

		<td width="35%" ><input type="text" id="proxy_username" name="proxy_username" size="25"  value="<?php echo $_smarty_tpl->tpl_vars['settings']->value['proxy_username'];?>
" tabindex='1' ></td>
		<td width="15%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PROXY_PASSWORD'];?>
<span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
        <td width="35%" >
           <?php if (!empty($_smarty_tpl->tpl_vars['settings']->value['proxy_password'])) {?>
               <input type="password" id="proxy_password" name="proxy_password" size="25"  disabled tabindex="1" style="display: none">
               <a href="javascript:void(0)" id="proxy_password_link" onClick="SUGAR.util.setEmailPasswordEdit('proxy_password')"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_CHANGE_PASSWORD'];?>
</a>
           <?php } else { ?>
               <input type="password" id="proxy_password" name="proxy_password" size="25"  value="" tabindex='1' autocomplete="off">
           <?php }?>
        </td>
		</tr></table>
		</div>
	</div>
  </td>
  </tr>
 </table>
<?php }?>


<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
	<tr>
    <th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_DIALOUT_TITLE'];?>
</h4></th>
	</tr>
	<tr>
    <td width="25%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_DIALOUT_ON'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_DIALOUT_ON_DESC'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
	<?php if (!empty($_smarty_tpl->tpl_vars['settings']->value['system_skypeout_on'])) {?>
		<?php $_smarty_tpl->_assignInScope('system_skypeout_on_checked', 'CHECKED');?>
	<?php } else { ?>
		<?php $_smarty_tpl->_assignInScope('system_skypeout_on_checked', '');?>
	<?php }?>
	<td width="75%" align="left"  valign='middle'><input type='hidden' name='system_skypeout_on' value='0'><input name="system_skypeout_on" value="1" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['system_skypeout_on_checked']->value;?>
></td>
	</tr>
 </table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
    <tr>
        <th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_TWEETTOCASE_TITLE'];?>
</h4></th>
    </tr>
    <tr>
        <td width="25%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_TWEETTOCASE_ON'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_TWEETTOCASE_ON_DESC'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
        <?php if (!empty($_smarty_tpl->tpl_vars['settings']->value['system_tweettocase_on'])) {?>
            <?php $_smarty_tpl->_assignInScope('system_tweettocase_on_checked', 'CHECKED');?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('system_tweettocase_on_checked', '');?>
        <?php }?>
        <td width="75%" align="left"  valign='middle'><input type='hidden' name='system_tweettocase_on' value='0'><input name="system_tweettocase_on" value="1" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['system_tweettocase_on_checked']->value;?>
></td>
    </tr>
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
    <tr>
        <th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PREVIEW_SETTINGS'];?>
</h4></th>
    </tr>
    <tr>
        <td width="25%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_PREVIEW_EDIT'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_PREVIEW_EDIT_HELP'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
        <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['preview_edit'])) {?>
            <?php $_smarty_tpl->_assignInScope('preview_edit_checked', 'CHECKED');?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('preview_edit_checked', '');?>
        <?php }?>
        <td width="75%" align="left"  valign='middle'>
            <input type='hidden' name='preview_edit' value='false'>
            <input name="preview_edit" value="true" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['preview_edit_checked']->value;?>
>
        </td>
    </tr>
</table>

	<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
		<tr>
			<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ACTIVITY_STREAMS_SETTINGS_TITLE'];?>
</h4></th>
		</tr>
		<tr>
			<td width="25%" scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ACTIVITY_STREAMS_SETTINGS_EDIT'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_ACTIVITY_STREAMS_SETTINGS_EDIT_HELP'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
            <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['activity_streams_enabled'])) {?>
                <?php $_smarty_tpl->_assignInScope('activity_streams_enabled_checked', 'CHECKED');?>
            <?php } else { ?>
                <?php $_smarty_tpl->_assignInScope('activity_streams_enabled_checked', '');?>
            <?php }?>
			<td width="75%" align="left"  valign='middle'>
				<input type='hidden' name='activity_streams_enabled' value='false'>
				<input name="activity_streams_enabled" value="true" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['activity_streams_enabled_checked']->value;?>
>
			</td>
		</tr>
	</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
	<tr>
        <!-- This heading is hard coded because it is NOT intended to be translatable or dynamic -->
        <th align="left" scope="row" colspan="4"><h4>SugarBPM<sup class="heading">TM</sup></h4></th>
	</tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_AUTO_SAVE_INTERVAL'];?>
:&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_AUTO_SAVE_INTERVAL_HELP']),$_smarty_tpl);?>
</td>
		<td><select name="processes_auto_save_interval"><?php echo $_smarty_tpl->tpl_vars['processes_auto_save_options']->value;?>
</select></td>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_SAVE'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_SAVE_HELP'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
	    <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['processes_auto_validate_on_autosave'])) {?>
	        <?php $_smarty_tpl->_assignInScope('processes_auto_validate_on_autosave_checked', 'CHECKED');?>
	    <?php } else { ?>
	        <?php $_smarty_tpl->_assignInScope('processes_auto_validate_on_autosave_checked', '');?>
	    <?php }?>
	    <td width="25%">
			<input type='hidden' name='processes_auto_validate_on_autosave' value='false'>
			<input name="processes_auto_validate_on_autosave" value="true" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['processes_auto_validate_on_autosave_checked']->value;?>
>
		</td>
	</tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_IMPORT'];?>
&nbsp<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_IMPORT_HELP'],'WIDTH'=>400),$_smarty_tpl);?>
</td>
	    <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['processes_auto_validate_on_import'])) {?>
	        <?php $_smarty_tpl->_assignInScope('processes_auto_validate_on_import_checked', 'CHECKED');?>
	    <?php } else { ?>
	        <?php $_smarty_tpl->_assignInScope('processes_auto_validate_on_import_checked', '');?>
	    <?php }?>
	    <td width="25">
			<input type='hidden' name='processes_auto_validate_on_import' value='false'>
			<input name="processes_auto_validate_on_import" value="true" class="checkbox" tabindex='1' type="checkbox" <?php echo $_smarty_tpl->tpl_vars['processes_auto_validate_on_import_checked']->value;?>
>
		</td>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_ADVANCED_WORKFLOW_SETTINGS_CYCLES'];?>
  <span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td > <input name="error_number_of_cycles" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['error_number_of_cycles'];?>
"></td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
	<tr>
		<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_COMMENT_LOG_SETTINGS'];?>
</h4></th>
	</tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_COMMENT_LOG_MAX_CHARS'];?>
</td>
		<td> <input name="commentlog_maxchars" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['commentlog']['maxchars'];?>
"></td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
	<tr>
		<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_DOC_MERGE_REGION_CONFIG'];?>
</h4></th>
	</tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_DOC_MERGE_REGION'];?>
</td>
		<td> <select name="document_merge_service_urls_default" <?php echo $_smarty_tpl->tpl_vars['doc_merge_disabled_region']->value;?>
>
			<?php echo $_smarty_tpl->tpl_vars['doc_merge_default_region_url']->value;?>

		</select></td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
	<tr>
	<th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['ADVANCED'];?>
</h4></th>
	</tr>
	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['VERIFY_CLIENT_IP'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['verify_client_ip'])) {?>
			<?php $_smarty_tpl->_assignInScope('verify_client_ip_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('verify_client_ip_checked', '');?>
		<?php }?>
		<td  ><input type='hidden' name='verify_client_ip' value='false'><input name='verify_client_ip'  type="checkbox" value="1" <?php echo $_smarty_tpl->tpl_vars['verify_client_ip_checked']->value;?>
></td>

		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LOG_MEMORY_USAGE'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['log_memory_usage'])) {?>
			<?php $_smarty_tpl->_assignInScope('log_memory_usage_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('log_memory_usage_checked', '');?>
		<?php }?>
		<td  ><input type='hidden' name='log_memory_usage' value='false'><input name='log_memory_usage'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['log_memory_usage_checked']->value;?>
></td>

	</tr>
	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LOG_SLOW_QUERIES'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['dump_slow_queries'])) {?>
			<?php $_smarty_tpl->_assignInScope('dump_slow_queries_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('dump_slow_queries_checked', '');?>
		<?php }?>
		<td ><input type='hidden' name='dump_slow_queries' value='false'><input name='dump_slow_queries'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['dump_slow_queries_checked']->value;?>
></td>

		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['SLOW_QUERY_TIME_MSEC'];?>
: </td>
		<td  >
			<input type='text' size='5' name='slow_query_time_msec' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['slow_query_time_msec'];?>
'>
		</td>

	</tr>
	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['UPLOAD_MAX_SIZE'];?>
: </td>
		<td  >
			<input type='text' size='8' name='upload_maxsize' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['upload_maxsize'];?>
'>&nbsp;<?php echo $_smarty_tpl->tpl_vars['MOD']->value['UPLOAD_MAXSIZE_UNITS'];?>

		</td>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['STACK_TRACE_ERRORS'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['stack_trace_errors'])) {?>
			<?php $_smarty_tpl->_assignInScope('stack_trace_errors_checked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('stack_trace_errors_checked', '');?>
		<?php }?>
		<td ><input type='hidden' name='stack_trace_errors' value='false'><input name='stack_trace_errors'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['stack_trace_errors_checked']->value;?>
></td>



	</tr>

	<tr>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['SESSION_TIMEOUT'];?>
:&nbsp;<?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_SESSION_TIMEOUT_TOOLTIP']),$_smarty_tpl);?>
</td>
		<td  >
			<input type='text' size='8' name='system_session_timeout' value='<?php echo $_smarty_tpl->tpl_vars['SESSION_TIMEOUT']->value;?>
'>&nbsp;<?php echo $_smarty_tpl->tpl_vars['MOD']->value['SESSION_TIMEOUT_UNITS'];?>

		</td>
		<?php if ($_smarty_tpl->tpl_vars['developer_mode_visible']->value) {?>
		<td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['DEVELOPER_MODE'];?>
: </td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['developerMode'])) {?>
			<?php $_smarty_tpl->_assignInScope('developerModeChecked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('developerModeChecked', '');?>
		<?php }?>
		<td ><input type='hidden' name='developerMode' value='false'><input name='developerMode'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['developerModeChecked']->value;?>
></td>
		<?php }?>
	</tr>
	<tr>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_VCAL_PERIOD'];?>
 <?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['vCAL_HELP']),$_smarty_tpl);?>
</td>
		<td >
			<input type='text' size='4' name='vcal_time' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['vcal_time'];?>
'>
		</td>
        <td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_IMPORT_MAX_RECORDS'];?>
 <?php echo smarty_function_sugar_help(array('text'=>$_smarty_tpl->tpl_vars['MOD']->value['LBL_IMPORT_MAX_RECORDS_HELP']),$_smarty_tpl);?>
</td>
		<td >
			<input type='text' size='4' name='import_max_records_total_limit' value='<?php echo $_smarty_tpl->tpl_vars['config']->value['import_max_records_total_limit'];?>
'>
		</td>

	</tr>
    <tr>
        <td  scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_NO_PRIVATE_TEAM_UPDATE'];?>
: </td>
        <?php if (!empty($_smarty_tpl->tpl_vars['config']->value['noPrivateTeamUpdate'])) {?>
            <?php $_smarty_tpl->_assignInScope('noPrivateTeamUpdateChecked', 'CHECKED');?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('noPrivateTeamUpdateChecked', '');?>
        <?php }?>
        <td ><input type='hidden' name='noPrivateTeamUpdate' value='false'><input name='noPrivateTeamUpdate'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['noPrivateTeamUpdateChecked']->value;?>
></td>
    </tr>




</table>

<table  width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
<?php if ($_smarty_tpl->tpl_vars['logger_visible']->value) {?>
<tr>
<th align="left" scope="row" colspan="6"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER'];?>
</h4></th>
</tr>
	<tr>
		<td  scope="row" valign='middle'><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_FILENAME'];?>
 <span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td   valign='middle' ><input type='text' name = 'logger_file_name'  value="<?php echo $_smarty_tpl->tpl_vars['config']->value['logger']['file']['name'];?>
"></td>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_FILENAME_SUFFIX'];?>
</td>
		<td ><select name = "logger_file_suffix" selected='<?php echo $_smarty_tpl->tpl_vars['config']->value['logger']['file']['suffix'];?>
'><?php echo $_smarty_tpl->tpl_vars['filename_suffix']->value;?>
</select></td>
	</tr>
	<tr>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_MAX_LOG_SIZE'];?>
  <span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td > <input name="logger_file_maxSize" size="4" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['logger']['file']['maxSize'];?>
"></td>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_DEFAULT_DATE_FORMAT'];?>
</td>
		<td  ><input name ="logger_file_dateFormat" type="text" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['logger']['file']['dateFormat'];?>
"></td>
	</tr>
	<tr>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_LOG_LEVEL'];?>
 </td>
		<td > <select name="logger_level"><?php echo $_smarty_tpl->tpl_vars['log_levels']->value;?>
</select></td>
		<td scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGGER_MAX_LOGS'];?>
  <span class="required"><?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_REQUIRED_SYMBOL'];?>
</span></td>
		<td > <input name="logger_file_maxLogs" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['logger']['file']['maxLogs'];?>
"></td>
	</tr>
<?php }?>
	<tr>
	    <td><a href="index.php?module=Configurator&action=LogView" target="_blank"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_LOGVIEW'];?>
</a></td>
	</tr>
</table>

<?php if ($_smarty_tpl->tpl_vars['SHOW_CATALOG_CONFIG']->value) {?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
    <tr>
        <th align="left" scope="row" colspan="4"><h4><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_SUGAR_CATALOG_SETTINGS'];?>
</h4></th>
    </tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_SUGAR_CATALOG_ENABLED'];?>
</td>
		<?php if (!empty($_smarty_tpl->tpl_vars['config']->value['catalog_enabled'])) {?>
			<?php $_smarty_tpl->_assignInScope('catalogChecked', 'CHECKED');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('catalogChecked', '');?>
		<?php }?>
		<td><input type='hidden' name='catalog_enabled' value='false'><input name='catalog_enabled'  type="checkbox" value='true' <?php echo $_smarty_tpl->tpl_vars['catalogChecked']->value;?>
></td>
	</tr>
	<tr>
		<td width="25%" scope="row"><?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_SUGAR_CATALOG_URL'];?>
</td>
		<td><input style="width:40%" name="catalog_url" value="<?php echo $_smarty_tpl->tpl_vars['config']->value['catalog_url'];?>
"></td>
	</tr>
</table>
<?php }?>

<div style="padding-top: 2px;">
<input title="<?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_SAVE_BUTTON_TITLE'];?>
" class="button primary"  type="submit" name="save" value="  <?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_SAVE_BUTTON_LABEL'];?>
  " class="button primary"/>
		<!-- &nbsp;<input title="<?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_SAVE_BUTTON_TITLE'];?>
"  class="button"  type="submit" name="restore" value="  <?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_RESTORE_BUTTON_LABEL'];?>
 " /> -->
		&nbsp;<input title="<?php echo $_smarty_tpl->tpl_vars['MOD']->value['LBL_CANCEL_BUTTON_TITLE'];?>
"  onclick="parent.SUGAR.App.router.navigate('#Administration', {trigger: true})" class="button"  type="button" name="cancel" value="  <?php echo $_smarty_tpl->tpl_vars['APP']->value['LBL_CANCEL_BUTTON_LABEL'];?>
  " />
</div>
<?php echo $_smarty_tpl->tpl_vars['JAVASCRIPT']->value;?>


</form>
<div id='upload_panel' style="display:none">
    <form id="upload_form" name="upload_form" method="POST" action='index.php' enctype="multipart/form-data">
        <?php echo smarty_function_sugar_csrf_form_token(array(),$_smarty_tpl);?>

        <input type="file" id="my_file_company" name="file_1" size="20" onchange="uploadCheck(false)"/>
        <?php echo smarty_function_sugar_getimage(array('name'=>"sqsWait",'ext'=>".gif",'alt'=>$_smarty_tpl->tpl_vars['mod_strings']->value['LBL_LOADING'],'other_attributes'=>'id="loading_img_company" style="display:none" '),$_smarty_tpl);?>

    </form>
</div>
<div id='upload_panel_dark' style="display:none">
    <form id="upload_form_dark" name="upload_form_dark" method="POST" action='index.php' enctype="multipart/form-data">
        <?php echo smarty_function_sugar_csrf_form_token(array(),$_smarty_tpl);?>

        <input type="file" id="my_file_company_dark" name="file_dark" size="20" onchange="uploadCheck(true)"/>
        <?php echo smarty_function_sugar_getimage(array('name'=>"sqsWait",'ext'=>".gif",'alt'=>$_smarty_tpl->tpl_vars['mod_strings']->value['LBL_LOADING'],'other_attributes'=>'id="loading_img_company_dark" style="display:none" '),$_smarty_tpl);?>

    </form>
</div>
<?php echo '<script'; ?>
 type='text/javascript'>
function init_logo(){
    var uploadPanel = document.getElementById('upload_panel');
    var uploadPanelDark = document.getElementById('upload_panel_dark');
    uploadPanel.style.display = 'inline';
    uploadPanel.style.position = 'absolute';
    uploadPanelDark.style.display = 'inline';
    uploadPanelDark.style.position = 'absolute';
    YAHOO.util.Dom.setX('upload_panel', YAHOO.util.Dom.getX('container_upload'));
    YAHOO.util.Dom.setY('upload_panel', YAHOO.util.Dom.getY('container_upload') - 1);
    YAHOO.util.Dom.setX('upload_panel_dark', YAHOO.util.Dom.getX('container_upload_dark'));
    YAHOO.util.Dom.setY('upload_panel_dark', YAHOO.util.Dom.getY('container_upload_dark') - 1);
}

// Initialize the file upload container placement on page load and resize
$(window).on('load resize', init_logo);
$(window).unload(function() {
    $(window).off('load resize', init_logo);
});

function toggleDisplay_2(div_string){
    toggleDisplay(div_string);
    init_logo();
}
 function uploadCheck(isDarkMode){
    //AJAX call for checking the file size and comparing with php.ini settings.
    var fileElName = isDarkMode ? 'my_file_company_dark' : 'my_file_company';
    var commitCompanyLogoName = isDarkMode ? 'commit_company_logo_dark' : 'commit_company_logo';
    var companyLogoImageName = isDarkMode ? 'company_logo_image_dark' : 'company_logo_image';
    var uploadFormName = isDarkMode ? 'upload_form_dark' : 'upload_form';
    var loadingImageName = isDarkMode ? 'loading_img_company_dark' : 'loading_img_company';

    var callback = {
        upload:function(r) {
            var file_type = JSON.parse(r.responseText);
            var bad_image = SUGAR.language.get('Configurator', 'LBL_ALERT_TYPE_IMAGE');

            document.getElementById('loading_img_company').style.display = 'none';
            document.getElementById('loading_img_company_dark').style.display = 'none';

            switch (file_type['data']) {
                case 'other':
                    alert(bad_image);
                    document.getElementById(fileElName).value = '';
                    break;
                case 'size':
                    alert(SUGAR.language.get('Configurator', 'LBL_ALERT_SIZE_RATIO'));
                    document.getElementById(commitCompanyLogoName).value = '1';
                    document.getElementById(companyLogoImageName).src = file_type['url'];
                    break;
                case 'file_error':
                    alert(SUGAR.language.get('Configurator', 'ERR_ALERT_FILE_UPLOAD'));
                    document.getElementById(fileElName).value = '';
                    break;
                //File good
                case 'ok':
                    document.getElementById(commitCompanyLogoName).value = '1';
                    document.getElementById(companyLogoImageName).src = file_type['url'];
                    break;
                //error in getimagesize because unsupported type
                default:
                    alert(bad_image);
                    document.getElementById(fileElName).value = '';
            }
        },
        failure:function(r){
            alert(SUGAR.language.get('app_strings','LBL_AJAX_FAILURE'));
        }
    }

    document.getElementById(commitCompanyLogoName).value = '';
    document.getElementById(loadingImageName).style.display="inline";

    var file_name = document.getElementById(fileElName).value;
    var postData = '&entryPoint=UploadFileCheck&csrf_token=' + SUGAR.csrf.form_token;
    YAHOO.util.Connect.setForm(document.getElementById(uploadFormName), true, true);
    if (file_name) {
        if (postData.substring(0,1) == '&') {
            postData=postData.substring(1);
        }
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
    }
}
<?php echo '</script'; ?>
>
<?php }
}

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
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
<script>
addForm('popup_form');
</script>

<form name='popup_form' id='popup_form_id' onsubmit='return false;'>
{sugar_csrf_form_token}
<input type='hidden' name='module' value='ModuleBuilder'>
<input type='hidden' name='action' value='{$action}'>
<input type='hidden' name='new_dropdown' value=''>
<input type='hidden' name='to_pdf' value='true'>
<input type='hidden' name='view_module' value='{$module->name}'>
{if $isNew}
<input type='hidden' name='is_new' value='1'>
{/if}
{if isset($package->name)}
    <input type='hidden' name='view_package' value='{$package->name}'>
{/if}
<input type='hidden' name='is_update' value='true'>
	{if $hideLevel < 5}
	    &nbsp;
	    <input type='button' class='button' name='fsavebtn' value='{$mod_strings.LBL_BTN_SAVE}' 
			onclick='if(validate_type_selection() && check_form("popup_form")){ {$preSave} ModuleBuilder.submitForm("popup_form_id"); }'>
	    <input type='button' name='cancelbtn' value='{$mod_strings.LBL_BTN_CANCEL}' 
			onclick='ModuleBuilder.tabPanel.removeTab(ModuleBuilder.findTabById("east"));' class='button'>
	    {if !empty($vardef.name)}
	        {if $hideLevel < 3}

	            &nbsp;<input type='button' class='button' name='fdeletebtn' value='{$mod_strings.LBL_BTN_DELETE}' onclick='if(confirm("{$mod_strings.LBL_CONFIRM_FIELD_DELETE}")){ document.popup_form.action.value="DeleteField";ModuleBuilder.submitForm("popup_form_id"); }'>

	        {/if}
	        {if !$no_duplicate}

	        &nbsp;<input type='button' class='button' name='fclonebtn' value='{$mod_strings.LBL_BTN_CLONE}' onclick='document.popup_form.action.value="CloneField";ModuleBuilder.submitForm("popup_form_id");'>

	    {/if}
	    {/if}
	
	{else}

	     <input type='button' class='button' name='lsavebtn' value='{$mod_strings.LBL_BTN_SAVE}' onclick='if(check_form("popup_form")){ this.form.action.value = "{$action}";ModuleBuilder.submitForm("popup_form_id") };'>


	        &nbsp;<input type='button' class='button' name='fclonebtn' value='{$mod_strings.LBL_BTN_CLONE}' onclick='document.popup_form.action.value="CloneField";ModuleBuilder.submitForm("popup_form_id");'>


        {if !$no_duplicate}

                &nbsp;<input type='button' class='button' name='cancel' value='{$mod_strings.LBL_BTN_CANCEL}' onclick='ModuleBuilder.tabPanel.get("activeTab").close()'>

        {/if}
	        
{/if}
<hr>

<table width="400px" >
<tr>
    <td class="mbLBL" style="width:92px;">{sugar_translate module="DynamicFields" label="COLUMN_TITLE_DATA_TYPE"}:</td>
    <td >{if empty($vardef.name) && $isClone == 0}
                {html_options name="type" id="type"  options=$field_types selected=$vardef.type onchange='ModuleBuilder.moduleLoadField("", this.options[this.selectedIndex].value);'}
                {sugar_help text=$mod_strings.LBL_POPHELP_FIELD_DATA_TYPE FIXX=250 FIXY=80}
            {else}
                {if !empty($display_type)}
                    {$display_type}
                {else}
                    {$field_types[$vardef.type]}
                {/if}
            {/if}
            {if empty($field_types[$vardef.type]) && !empty($vardef.type)}({$vardef.type}){/if}
            <input type='hidden' name='type' value={$vardef.type} />
    </td>
</tr>
</table>
{$fieldLayout}
</form>

<script>

function validate_type_selection(){
    var typeSel = document.getElementById('type');
    if(typeSel && typeSel.options){
        if(typeSel.options[typeSel.selectedIndex].value == ''){
            alert('{sugar_translate module="DynamicFields" label="ERR_SELECT_FIELD_TYPE"}');
            return false;
        }
    }
    if (document.getElementById("customTypeValidate")){
        return document.getElementById("customTypeValidate").onchange(); 
    }
    return true;
}

ModuleBuilder.helpSetup('fieldsEditor','{$help_group}');
</script>
{* <script>//Need this to work in FF4. Bug where last script isn't executed.</script> *}

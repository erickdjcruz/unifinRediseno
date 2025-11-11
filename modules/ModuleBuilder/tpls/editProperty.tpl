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
<form name="editProperty" id="editProperty" onsubmit='return false;'>
{sugar_csrf_form_token}
<input type='hidden' name='module' value='ModuleBuilder'>
<input type='hidden' name='action' value='saveProperty'>
<input type='hidden' name='view_module' value="{$view_module|escape:'html'}">
{if isset($view_package)}<input type='hidden' name='view_package' value="{$view_package|escape:'html'}">{/if}
<input type='hidden' name='subpanel' value="{$subpanel|escape:'html'}">
<input type='hidden' name='to_pdf' value='true'>

{if isset($MB)}
<input type='hidden' name='MB' value="{$MB|escape:'html'}">
<input type='hidden' name='view_package' value="{$view_package|escape:'html'}">
{/if}

<script>
    function saveAction() {
        var widthUnit = '{$widthUnit|escape:'javascript'}';
        for(var i=0, l=document.editProperty.elements.length; i<l; i++) {
            var field = document.editProperty.elements[i];
            if (field.className.indexOf('save') != -1 )
            {
                if (field.value != 'no_change')
                {
                    var id = field.id.substring('editProperty_'.length);
                    var fieldSpan = document.getElementById(id);
                    var value = YAHOO.lang.escapeHTML(field.value);

                    // If editing a width on list layouts, update the unit
                    if (field.name.toLowerCase().indexOf('width') != -1) {
                        value = value.replace('px', '').replace('%', '').trim();
                        fieldSpan.nextElementSibling.innerHTML = field.value == '' || isNaN(+value) ? '' : widthUnit;
                    }
                    fieldSpan.innerHTML = value;
                }
            }
        }
    }

	function switchLanguage( language )
	{
        var request = 'module=ModuleBuilder&action=editProperty&view_module={$editModule|escape:'url'}&selected_lang=' + language ;
        {foreach from=$properties key='key' item='property'}
                request += '&id_{$key|escape:'url'}={$property.id|escape:'url'}&name_{$key|escape:'url'}={$property.name|escape:'url'}&title_{$key|escape:'url'}={$property.title|escape:'url'}&label_{$key|escape:'url'}={$property.label|escape:'url'}' ;
        {/foreach}
        ModuleBuilder.getContent( request ) ;
    }

</script>

<table style="width:100%">

	{foreach from=$properties key='key' item='property'}
	<tr>
        <td width="25%" align='right'>{if isset($property.title)}{$property.title|escape:'html'}{else}{$property.name|escape:'html'}{/if}:</td>
		<td width="75%">
            <input class='save' type='hidden' name="{$property.name|escape:'html'}" id="editProperty_{$id|escape:'html'}{$property.id|escape:'html'}" value='no_change'>
			{if isset($property.hidden)}
                {$property.value|escape:'html'}
			{else}
				{if $key == 'width'}
                    <select id="selectWidthClass_{$id|escape:'html'}{$property.id|escape:'html'}" onchange="handleClassSelection(this)">
						<option value="" selected="selected">default</option>
                        {foreach from=$defaultWidths item='width'}
                            <option value="{$width|escape:'html'}">{$width|escape:'html'}</option>
                        {/foreach}
						<option value="custom">custom</option>
					</select>
                    <input id="widthValue_{$id|escape:'html'}{$property.id|escape:'html'}" onchange="handleWidthChange(this.value)" value="{$property.value|escape:'html'}" style="display:none">

                    <script>
                    var propertyValue, widthValue, saveWidthProperty, selectWidthClass;


                    propertyValue = '{$property.value|escape:'javascript'}';
                    saveWidthProperty = document.getElementById('editProperty_{$id|escape:'javascript'}{$property.id|escape:'javascript'}');
                    widthValue = document.getElementById('widthValue_{$id|escape:'javascript'}{$property.id|escape:'javascript'}');
                    selectWidthClass = document.getElementById('selectWidthClass_{$id|escape:'javascript'}{$property.id|escape:'javascript'}');


                    if (propertyValue != '') {
                        if (isNaN(propertyValue)) {
                            selectWidthClass.value = propertyValue;
                            widthValue.style.display = 'none';
                            widthValue.value = '';
                        } else {
                            selectWidthClass.value = 'custom';
                            widthValue.style.display = 'inline';
                            widthValue.value = isNaN(propertyValue) ? '' : propertyValue;
                        }
                    }
                    function handleClassSelection(el) {
                        var selected = el.options[el.selectedIndex].value;

                        if (selected === 'custom') {
                            widthValue.style.display = 'inline';
                            widthValue.value = isNaN(propertyValue) ? '' : propertyValue;
                        } else {
                            widthValue.style.display = 'none';
                            widthValue.value = '';
                            saveWidthProperty.value = selected;
                        }
                    }

                    function handleWidthChange(w) {
                        saveWidthProperty.value = w;
                    }
                    </script>

				{else}
                    <input onchange="document.getElementById('editProperty_{$id|escape:'javascript'|escape:'html':'UTF-8'}{$property.id|escape:'javascript'|escape:'html':'UTF-8'}').value = this.value" value="{$property.value|escape:'html'}">
                {/if}
			{/if}
		</td>
	</tr>
	{/foreach}
	<tr>
		<td><input class="button" type="Button" name="save" value="{$APP.LBL_SAVE_BUTTON_LABEL|escape:'html'}" onclick="saveAction(); ModuleBuilder.submitForm('editProperty'); ModuleBuilder.closeAllTabs();"></td>
	</tr>
</table>
</form>

<script>
ModuleBuilder.helpSetup('layoutEditor','property', 'east');
</script>



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

<table width="100%">
<tr>
	<td class='mbLBL' width='30%' >{sugar_translate module="DynamicFields" label="COLUMN_TITLE_NAME"}:</td>
	<td>
	{if $hideLevel == 0}
		<input id="field_name_id" maxlength={if isset($package->name) && $package->name != "studio"}30{else}28{/if} type="text" name="name" value="{$vardef.name}"
		  onchange="
		document.getElementById('label_key_id').value = 'LBL_'+document.getElementById('field_name_id').value.toUpperCase();
		document.getElementById('label_value_id').value = document.getElementById('field_name_id').value.replace(/\_+/g,' ').replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		document.getElementById('field_name_id').value = document.getElementById('field_name_id').value.toLowerCase();" />
	{else}
		<input id= "field_name_id" type="hidden" name="name" value="{$vardef.name}"
		  onchange="
		document.getElementById('label_key_id').value = 'LBL_'+document.getElementById('field_name_id').value.toUpperCase();
		document.getElementById('label_value_id').value = document.getElementById('field_name_id').value.replace(/\_+/g,' ').replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		document.getElementById('field_name_id').value = document.getElementById('field_name_id').value.toLowerCase();"/>
		{$vardef.name}
	{/if}
        <script>
            addToValidateCallback("popup_form", "name", "callback", true, "{sugar_translate module="DynamicFields" label="COLUMN_TITLE_NAME"}", (function(nameExceptions, existingFields) {
                return function(formName, fieldName, index) {
                    var el = document.forms[formName].elements[fieldName],
                        value = el.value, i, arrValue;

                    // will be already validated as required
                    if (value === "") {
                        return true;
                    }

                    if (!isDBName(value)) {
                        validate[formName][index][msgIndex] = "{sugar_translate module="DynamicFields" label="ERR_FIELD_NAME_NON_DB_CHARS"}";
                        return false;
                    }

                    value = value.toUpperCase();

                    // check where field name is in the list of exceptions
                    for (i = 0; i < nameExceptions.length; i++) {
                        arrValue = nameExceptions[i];
                        if (arrValue == value) {
                            validate[formName][index][msgIndex] = "{sugar_translate module="DynamicFields" label="ERR_RESERVED_FIELD_NAME"}";
                            return false;
                        }
                    }

                    {if $hideLevel == 0}
                    // check where field name is in the list of existing fields
                    for (i = 0; i < existingFields.length; i++) {
                        arrValue = existingFields[i];
                        if (arrValue == value) {
                            validate[formName][index][msgIndex] = "{sugar_translate module="DynamicFields" label="ERR_FIELD_NAME_ALREADY_EXISTS"}";
                            return false;
                        }
                    }
                    {/if}

                    return true;
                }
            })({$field_name_exceptions}, {$existing_field_names}));
        </script>
	</td>
</tr>
<tr>
	<td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_DISPLAY_LABEL"}:</td>
	<td>
		<input id="label_value_id" type="text" name="labelValue" value="{$lbl_value}">
	</td>
</tr>
<tr>
	<td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_LABEL"}:</td>
	<td>
    {if $hideLevel < 1}
	    <input id ="label_key_id" type="text" name="label" value="{$vardef.vname|escape:'html'}">
	{else}
		<input type="text" readonly value="{$vardef.vname|escape:'html'}" disabled=1>
		<input id ="label_key_id" type="hidden" name="label" value="{$vardef.vname|escape:'html'}">
	{/if}
	</td>
</tr>
<tr>
	<td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_HELP_TEXT"}:</td><td>{if $hideLevel < 5 }<input type="text" name="help" value="{$vardef.help}">{else}<input type="hidden" name="help" value="{$vardef.help}">{$vardef.help}{/if}
	</td>
</tr>
<tr>
    <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_COMMENT_TEXT"}:</td><td>{if $hideLevel < 5 }<input type="text" name="comments" value="{$vardef.comments}">{else}<input type="hidden" name="comment" value="{$vardef.comment}">{$vardef.comment}{/if}
    </td>
</tr>

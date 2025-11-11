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

{include file="modules/DynamicFields/templates/Fields/Forms/coreTop.tpl"}

<tr>
    <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_MAX_SIZE"}:</td>
    <td>
	{if $hideLevel < 5}
	    <input type='text' name='len' id='int_len' value='{$vardef.len|default:11}'></td>
	    <script>addToValidate('popup_form', 'len', 'int', false,'{sugar_translate module="DynamicFields" label="COLUMN_TITLE_MAX_SIZE"}' );</script>
	{else}
	    <input type='hidden' name='len' id='int_len' value='{$vardef.len}'>{$vardef.len}
	{/if}
    </td>
</tr>

{if $range_search_option_enabled}
    <tr>
        <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_ENABLE_RANGE_SEARCH"}:</td>
        <td>
            <input type='checkbox' name='enable_range_search' value='1' {if !empty($vardef.enable_range_search) }checked{/if} {if $hideLevel > 5}disabled{/if} />
            {if $hideLevel > 5}<input type='hidden' name='enable_range_search' value='{$vardef.enable_range_search}'>{/if}
        </td>
    </tr>
{/if}

<tr>
    <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_TITLE_AUTOINC_NEXT"}:</td>
    <td>
        <input type='hidden' name='auto_increment' id='auto_increment' value='true'>
	<input type='text' name='autoinc_next' id='autoinc_next' value='{$vardef.autoinc_next|default:1}' {if $MB}disabled=1{/if}>
        <script>addToValidateMoreThan('popup_form', 'autoinc_next', 'int', false,'{sugar_translate module="DynamicFields" label="COLUMN_TITLE_AUTOINC_NEXT"}', {$vardef.autoinc_next|default:1});</script>
        <input type='hidden' name='autoinc_val_changed' id='autoinc_val_changed' value='false'>
    </td>
</tr>

<tr>
    <td class='mbLBL'>{sugar_translate module="DynamicFields" label="COLUMN_DISABLE_NUMBER_FORMAT"}:</td>
    <td>
        <input type='checkbox' name='ext3' value='1' {if !empty($vardef.disable_num_format) }checked{/if} {if $hideLevel > 5}disabled{/if} />
        {if $hideLevel > 5}<input type='hidden' name='ext3' value='{$vardef.disable_num_format}'>{/if}
    </td>
</tr>

{include file="modules/DynamicFields/templates/Fields/Forms/coreBottom.tpl"}

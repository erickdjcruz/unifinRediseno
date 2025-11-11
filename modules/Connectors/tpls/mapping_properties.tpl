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
<div id="{$source_id|escape:'html':'UTF-8'}_add_tables" class="sources_table_div">
{foreach from=$display_data key=module item=data}

<table border="0" width="100%">
<tr>
<td colspan="2"><span><font size="3">{sugar_translate label=$module}</font></span></td></tr>
<tr>
<td width="150px"><b>{$mod.LBL_CONNECTOR_FIELDS|escape:'html':'UTF-8'}</b></td>
<td><b>{$mod.LBL_MODULE_FIELDS|escape:'html':'UTF-8'}</b></td>
</tr>
</table>

<table border="0" name="{$module|escape:'html':'UTF-8'}" id="{$module|escape:'html':'UTF-8'}" class="mapping_table" width="100%">
<tr>
<td colspan="2">
{foreach from=$data.field_keys key=field_id item=field}
{if $field_id != 'id'}
<div id="{$source_id|escape:'html':'UTF-8'}:{$module|escape:'html':'UTF-8'}:{$field|escape:'html':'UTF-8'}_div" style="width:500px; display:block; cursor:pointer">
<table border="0" cellpadding="1" cellspacing="1">
<tr>
<td width="150px">
{$field|escape:'html':'UTF-8'}
</td>
<td>
<select id="{$source_id|escape:'html':'UTF-8'}:{$module|escape:'html':'UTF-8'}:{$field_id|escape:'html':'UTF-8'}">
<option value="">---</option>
{foreach from=$data.available_fields key=available_field_id item=available_field}
<option value="{$available_field_id|escape:'html':'UTF-8'}" {if $data.field_mapping.$field_id == $available_field_id}SELECTED{/if}>{$available_field|escape:'html':'UTF-8'}</option>
{/foreach}
</select>
</td>
</tr>
</table>
</div>
{/if}
{/foreach}
</td>
</tr>
</table>

<hr/>
{/foreach}
</div>

{if $empty_mapping}
<h3>{$mod.ERROR_NO_SEARCHDEFS_DEFINED|escape:'html':'UTF-8'}</h3>
{/if}

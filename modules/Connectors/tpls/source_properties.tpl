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

<br/>
{if $no_connector}
<span class="error workflow-sunset">{$mod.ERROR_NO_CONNECTOR|escape:'html':'UTF-8'}</span>
<br />
{else}
{if !empty($connector_language.LBL_LICENSING_INFO)}
{$connector_language.LBL_LICENSING_INFO}
{/if}
<br/>
<table width="100%" border="0" cellspacing="1" cellpadding="0" >
    {if !empty($properties)}
        {foreach from=$properties key=name item=value}
            {assign var="cbBlockName" value=""}
            {foreach from=$visibilityCheckBoxConfigForFields key=cb_name item=cb_fields}
                {foreach from=$cb_fields key=cb_field_index item=cb_field}
                    {if $cb_field === $name}
                        {assign var="cbBlockName" value=$cb_name}
                        {assign var="cbBlockVisible" value=$properties[$cb_name]}
                    {/if}
                {/foreach}
            {/foreach}
            {if isset($visibilityCheckBoxConfigForFields[$name])}
                {assign var="isCheckBox" value=true}
                {if $value !== false}
                    {assign var="cbChecked" value=" checked"}
                {else}
                    {assign var="cbChecked" value=""}
                {/if}
            {else}
                {assign var="isCheckBox" value=false}
            {/if}
            <tr{if !empty($cbBlockName)} data-cb-block="{$cbBlockName}"{if !$cbBlockVisible} style="display:none"{/if}{/if}>
                <td class="dataLabel" width="35%">
                    {$connector_language[$name]|escape:'html':'UTF-8'}:&nbsp;
                    {if isset($required_properties[$name])}
                        <span class="required">*</span>
                    {/if}
                </td>
                <td class="dataLabel" width="65%">
                    {if $isCheckBox}
                        <input type="checkbox" id="{$source_id|escape:'html':'UTF-8'}_{$name|escape:'html':'UTF-8'}"
                               name="{$source_id|escape:'html':'UTF-8'}_{$name|escape:'html':'UTF-8'}"{$cbChecked}
                               data-cb="{$name}"
                               onclick="toggleConfigBlock(this)"
                        />
                    {else}
                        <input type="text" id="{$source_id|escape:'html':'UTF-8'}_{$name|escape:'html':'UTF-8'}"
                               name="{$source_id|escape:'html':'UTF-8'}_{$name|escape:'html':'UTF-8'}" size="75"
                               value="{$value|escape:'html':'UTF-8'}" />
                    {/if}
                </td>
            </tr>
        {/foreach}
        {if $hasTestingEnabled}
            <tr>
                <td class="dataLabel" colspan="2">
                    <input id="{$source_id|escape:'html':'UTF-8'}_test_button" type="button" class="button"
                           value="{$mod.LBL_TEST_SOURCE|escape:'html':'UTF-8'}"
                           onclick="run_test('{$source_id|escape:javascript}');">
                </td>
            </tr>
            <tr>
                <td class="dataLabel" colspan="2">
                    <span id="{$source_id|escape:'html':'UTF-8'}_result">&nbsp;</span>
                </td>
            </tr>
        {/if}
    {else}
        <tr>
            <td class="dataLabel" colspan="2">&nbsp;</td>
            <td class="dataLabel" colspan="2">{$mod.LBL_NO_PROPERTIES|escape:'html':'UTF-8'}</td>
        </tr>
    {/if}
</table>

<script type="text/javascript">
{foreach from=$required_properties key=id item=label}
addToValidate("ModifyProperties", "{$source_id|escape:javascript}_{$id|escape:javascript}", "alpha", true, "{$label|escape:javascript}");
{/foreach}
</script>
{/if}

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

<form name="StudioWizard">
	<input type="hidden" name="action" value="wizard">
	<input type="hidden" name="module" value="Studio">
	<input type="hidden" name="wizard" value="{$wizard|escape:'html':'UTF-8'}">
	<input type="hidden" name="option" value="">
	<table class="tabform" width="100%" cellpadding="4">
		<tr>
			<td colspan="16">{$welcome}</td>
		</tr>

		{counter name='optionCounter' assign='optionCounter' start=0}
		{foreach from=$options item='display' key='key'}
		{if ($optionCounter > 0) && ($optionCounter % 8 == 0)}
		</tr>
		<tr>
			{else}{if $optionCounter != 0 }
				<td nowrap width="1">|</td>
			{/if}{/if}

			<td nowrap>
				<a href="#" onclick="document.StudioWizard.option.value='{$key|escape:javascript}'; document.StudioWizard.submit();">{$display|escape:'html':'UTF-8'}</a>
			</td>
			{counter name='optionCounter'}
			{/foreach}
		</tr>

		<tr>
			<td>
				{if $wizard != 'StudioWizard'}<input type="submit" class="button" name="back" value="{$MOD.LBL_BTN_BACK|escape:'html':'UTF-8'}">{/if}
			</td>
			<td colspan="16">
			</td>
			<td width="100%" >
			</td>
		</tr>
	</table>
</form> 

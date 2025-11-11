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
<div class="listViewBody nosearch">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list view">
{if ($show_pagination neq "")}
{$pagination_data}
{/if}
<tr height="20">
{if ($isSummaryCombo)}
<th scope="col" align='center'  valign=middle nowrap>&nbsp;</th>
{/if}
{if ($isSummaryComboHeader)}
<td><span id="img_{$divId|escape:'html':'UTF-8'}"><a href="javascript:expandCollapseComboSummaryDiv('{$divId|escape:javascript}')"><img width="8" height="8" border="0" absmiddle="" alt="{$mod_strings.LBL_SHOW}" src="{$image_path}advanced_search.gif"/></a></span></td>
{/if}
{php}
	$count = 0;
	$_smarty_tpl->assign('count', $count);
{/php}
{foreach from=$header_row key=module item=cell}
	{if (($args.group_column_is_invisible != "") && ($args.group_pos eq $count))}
{php}	
	$count = $count + 1;
	$_smarty_tpl->assign('count', $count);
{/php}
	{else}
		{if strtolower($field_types[$module]) == 'currency' || strtolower($field_types[$module]) == 'int' ||strtolower($field_types[$module]) == 'float' || strtolower($field_types[$module]) == 'double' || strtolower($field_types[$module]) == 'decimal'}
			<th scope="num" align='center'  valign=middle nowrap>
		{else}
	<th scope="col" align='center'  valign=middle nowrap>	
		{/if}
	{$cell}
	{/if}
{/foreach}
</tr>

{php}
require_once('modules/Reports/templates/templates_reports.php');
$reporter = $_smarty_tpl->getTemplateVars('reporter');
$args = $_smarty_tpl->getTemplateVars('args');
$got_row = 0;
while (( $row = $reporter->get_next_row() ) != 0 ) {
	$got_row = 1;
	template_list_row($row, true, false, '', $_smarty_tpl);
{/php}
<tr height=20 class="{$row_class[$module]|escape:'html':'UTF-8'}" onmouseover="setPointer(this, '{$rownum|escape:javascript}', 'over', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');" onmouseout="setPointer(this, '{$rownum|escape:javascript}', 'out', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');" onmousedown="setPointer(this, '{$rownum|escape:javascript}', 'click', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');">
{if ($isSummaryComboHeader)}
<td><span id="img_{$divId|escape:'html':'UTF-8'}"><a href="javascript:expandCollapseComboSummaryDiv('{$divId|escape:javascript}')"><img width="8" height="8" border="0" absmiddle="" alt="{$mod_strings.LBL_SHOW}" src="{$image_path}advanced_search.gif"/></a></span></td>
{/if}
{php}
	$count = 0;
	$_smarty_tpl->assign('count', $count);
{/php}
{assign var='scope_row' value=true}
{foreach from=$column_row.cells key=module item=cell}
	{if (($column_row.group_column_is_invisible != "") && ($count|in_array:$column_row.group_pos)) }
{php}	
	$count = $count + 1;
	$_smarty_tpl->assign('count', $count);
{/php}
	{else}
	<td width="{$width|escape:'html':'UTF-8'}%" valign=TOP class="{$row_class[$module]|escape:'html':'UTF-8'}" bgcolor="{$bg_color|escape:'html':'UTF-8'}" {if $scope_row} scope='row' {/if}>
	
	{if $cell eq '' }
   		&nbsp;
   	{else}
		{$cell}
	{/if}
		
	{/if}
	{assign var='scope_row' value=false}
{/foreach}
</tr>

{php}
}
if (!$got_row) {
	echo template_list_view_no_results($args);
} // if
echo template_pagination_row($args);
echo template_end_table($args);
echo "</div>";
template_query_table($reporter);
{/php}

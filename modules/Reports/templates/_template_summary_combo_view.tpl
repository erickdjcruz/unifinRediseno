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

{php}
	global $mod_strings;
{/php}

<br/>

<input type="hidden" name="expandAllState" id="expandAllState" value="{$expandAll|escape:'html':'UTF-8'}">
<input class="button" name="expandCollapse" id="expandCollapse" title="{$mod_strings.LBL_REPORT_COLLAPSE_ALL}"
    type="button"
    value="{$mod_strings.LBL_REPORT_COLLAPSE_ALL}" 
    onclick="expandCollapseAll('false');">
<br/><br/>
{php}
require_once('modules/Reports/templates/templates_reports.php');
$reporter = $_smarty_tpl->getTemplateVars('reporter');
$args = $_smarty_tpl->getTemplateVars('args');
$header_row = $_smarty_tpl->getTemplateVars('header_row');
$columns_row = $_smarty_tpl->getTemplateVars('columns_row');
$countKeyIndex = $_smarty_tpl->getTemplateVars('countKeyIndex');
$count = 0;
$totalGroupByCount = 0;
$topLevelGroupByCount = 0;
$previousRow = array();
$counterArray = array();
$indexOfGroupByStart = 0;
$rowIdToCountArray = array();
$forLoopIndexForGroupBy;
$topLevelGroupColumnNameId = "";
$got_row = 0;                                                                                   
$divCounter = 0;
while (( $row = $reporter->get_summary_next_row()) != 0 ) {
	$got_row = 1;                                                                                   
	$startTable = true;
	$indexOfGroupByStart = whereToStartGroupByRowSummaryCombo($reporter, $count, $previousRow, $row);
	if ($indexOfGroupByStart != 0) {
		$startTable = false;
	} // if
	$previousRow = $row;
	template_header_row($header_row, $args, true, $_smarty_tpl);
{/php}
{php}
if ($count != 0 && $startTable) {
{/php}
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportGroupBySpaceTableView">
				<tr height=1>
					<td width="3%">&nbsp;

					</td>
				</tr>
			</table>
{php}
} // if
{/php}
{php}
if ($startTable) {
	$spanId = "img_combo_summary_div_" . $divCounter;
	$divId = "combo_summary_div_" . $divCounter;
	$counterArray = getCounterArrayForGroupBy($reporter, $topLevelGroupByCount);
	$rowId = generateIdForGroupByIndex($counterArray, 0);
	$rowIdToCountArray[$rowId] = 0;
	$groupByColumnName = getGroupByColumnName($reporter, $indexOfGroupByStart, $header_row, $row);
	$indexOfGroupByStart++;
	$topLevelGroupClass = "reportGroupNByTable";
	if (count($reporter->report_def['group_defs']) > 1) {
		$topLevelGroupClass = "reportGroup1ByTable";
	} // if
	$_smarty_tpl->assign('topLevelGroupClass', $topLevelGroupClass);
	$_smarty_tpl->assign('groupByColumnName', $groupByColumnName);
	$_smarty_tpl->assign('rowId', $rowId);
	$_smarty_tpl->assign('spanId', $spanId);
	$_smarty_tpl->assign('divId', $divId);
	$topLevelGroupByCount++;
{/php}
<table id="{$divId|escape:'html':'UTF-8'}" width="100%" border="0" cellpadding="0" cellspacing="0" class="reportGroupViewTable">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="{$topLevelGroupClass|escape:'html':'UTF-8'}">
				<tr height="20" >				
				  <th align='left' id = "{$rowId|escape:'html':'UTF-8'}" name= "{$rowId|escape:'html':'UTF-8'}" class="reportGroup1ByTableEvenListRowS1" valign=middle nowrap><span id="{$spanId|escape:'html':'UTF-8'}"><a href="javascript:expandCollapseComboSummaryDivTable('{$divId|escape:javascript}')"><img width="8" height="8" border="0" absmiddle="" alt="{$mod_strings.LBL_ALT_SHOW}" src="{sugar_getimagepath file='basic_search.gif'}"/></a></span>&nbsp;{$groupByColumnName}
				  </th>
				</tr>
			</table>
		</td>
	</tr>
{php}
$totalGroupByCount++;
$divCounter++;
} // if
{/php}
{if ($show_pagination neq "")}
{$pagination_data}
{/if}

{php}
for ($forLoopIndexForGroupBy = $indexOfGroupByStart ; $forLoopIndexForGroupBy < count($reporter->report_def['group_defs']) ; $forLoopIndexForGroupBy++) {
	$groupByColumnName = getGroupByColumnName($reporter, $indexOfGroupByStart, $header_row, $row);
	$spaces = "&nbsp;&nbsp;&nbsp;";
	$reportGroupByClass = "reportGroupNByTable";
	for ($spacesCount = 0 ; $spacesCount < $indexOfGroupByStart ; $spacesCount++) {
		$spaces = $spaces . $spaces;
	} // for
	$rowId = generateIdForGroupByIndex($counterArray, $forLoopIndexForGroupBy);
	if (array_key_exists($rowId, $rowIdToCountArray)) {
		$counterArray[$forLoopIndexForGroupBy] = $counterArray[$forLoopIndexForGroupBy] + 1;	
		$rowId = generateIdForGroupByIndex($counterArray, $forLoopIndexForGroupBy);
	} 
	$rowIdToCountArray[$rowId] = 0;
	$newRowId = $rowId;
	if ($forLoopIndexForGroupBy < (count($reporter->report_def['group_defs']) -1)) {
		$reportGroupByClass = "reportGroup1ByTable";
	} // if
	if ($forLoopIndexForGroupBy == (count($reporter->report_def['group_defs']) -1)) {
		if ($countKeyIndex != -1) {
			$newRowId = "";
		} // if
	} // if
	$_smarty_tpl->assign('reportGroupByClass', $reportGroupByClass);
	$_smarty_tpl->assign('spaces', $spaces);
	$indexOfGroupByStart++;
	$_smarty_tpl->assign('groupByColumnName', $groupByColumnName);
	$_smarty_tpl->assign('rowId', $newRowId);
{/php}
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="{$reportGroupByClass|escape:'html':'UTF-8'}">
				<tr height="20" class="reportGroupNByTableEvenListRowS1">
				  <td align='left' id = "{$rowId|escape:'html':'UTF-8'}" name= "{$rowId|escape:'html':'UTF-8'}" width="30%" nowrap class="reportGroupNByTableEvenListRowS1">{$spaces}{$groupByColumnName}</td>
				</tr>
			</table>
		</td>
	</tr>
{php}
} // for
{/php}

{php}
	//echo template_end_table($args);
	//echo "<div id='". $divId ."' style='padding-left: 30px;display:none'>";
	template_header_row($columns_row, $args, false, $_smarty_tpl);
{/php}

	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportGroupByDataTableHeader">
				<tr>
					<td>
						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportDataChildtablelistView">
						{if ($show_pagination neq "")}
						{$pagination_data}
						{/if}
							<tr height="20">
{if ($isSummaryCombo)}
								<th scope="col" align='center' class="reportGroupByDataChildTablelistViewThS1" valign=middle nowrap>&nbsp;</th>
{/if}

{php}
	$count1 = 0;
	$_smarty_tpl->assign('count1', $count1);
{/php}
{foreach from=$header_row key=module item=cell}
	{if (($args.group_column_is_invisible != "") && ($args.group_pos eq $count1))}
{php}	
	$count1 = $count1 + 1;
	$_smarty_tpl->assign('count1', $count1);
{/php}
	{else}
	{if $cell eq ""}
{php}	
	$cell = "&nbsp;";
	$_smarty_tpl->assign('cell', $cell);
{/php}
	{/if}	
								<th scope="col" align='center' class="reportGroupByDataChildTablelistViewThS1" valign=middle nowrap>	
	
	{$cell}
								</th>
	{/if}
{/foreach}
							</tr>
{php}
  		if ($reporter->current_summary_row_count > 0) {
            setCountForRowId($rowIdToCountArray, $rowId, $row, $countKeyIndex);
  			for($i=0; $i < $reporter->current_summary_row_count; $i++ ) {
				if (($column_row = $reporter->get_next_row() ) != 0 ) {
					template_list_row($column_row, true, false, '', $_smarty_tpl);
{/php}
<tr height=20>
{if ($isSummaryComboHeader)}

{/if}
{php}
	$count1 = 0;
	$_smarty_tpl->assign('count1', $count1);
{/php}
{foreach from=$column_row.cells key=module item=cell}
	{if (($column_row.group_column_is_invisible != "") && ($count1|in_array:$column_row.group_pos)) }
{php}	
	$count1 = $count1 + 1;
	$_smarty_tpl->assign('count1', $count1);
{/php}
	{else}
	{if $cell eq ""}
{php}	
	$cell = "&nbsp;";
	$_smarty_tpl->assign('cell', $cell);
{/php}
	{/if}	
									<td width="{$width|escape:'html':'UTF-8'}%" valign=TOP class="{$row_class[$module]|escape:'html':'UTF-8'}" bgcolor="{$bg_color|escape:'html':'UTF-8'}" scope="row">
	
	{$cell}
									</td>
	{/if}
{/foreach}
								</tr>
				
{php}
			   } else {
			     break;
			   } // else
  			} // for
{/php}							
  								</table>

								</td>
							</tr>
						</table>
					</td>
				</tr>

{php}

  		} else {
			echo template_no_results();
  		}
		//echo template_end_table($args);
		//echo "</div>";
		$count++;
} // while
if (!$got_row) {
	echo template_summary_combo_view_no_results($args);
	echo template_end_table($args);
} // if	
$_smarty_tpl->assign('divCounter', $divCounter);
global $global_json;
if (count($reporter->report_def['group_defs']) > 1) {
	$_smarty_tpl->assign('totalGroupCountArrayString', $global_json->encode($rowIdToCountArray));
}
{/php}
<script language="javascript">
var totalGroupCountArrayString = '{$totalGroupCountArrayString|escape:javascript}';
var totalDivCounter = {$divCounter|escape:javascript};
var groupCountObject = new Object();

if (totalGroupCountArrayString != '') {
	groupCountObject = YAHOO.lang.JSON.parse(totalGroupCountArrayString);
} // if

function displayGroupCount() {
	for (i in groupCountObject) {
		elem = document.getElementById(i);
		if (elem != null) {
			elem.innerHTML = trim(elem.innerHTML) + ', ' + SUGAR.language.get('app_strings','LBL_REPORT_NEWREPORT_COLUMNS_TAB_COUNT') +' = ' + groupCountObject[i];
		}
	} // for
} // fn

function showHideTableRows(divId, toShow) {
	var tableElm = document.getElementById(divId);
	for (i = 1 ; i < tableElm.rows.length ; i++) {
		if (toShow) {
			tableElm.rows[i].style.display = "";
		} else {
			tableElm.rows[i].style.display = "none";
		} // else
	} // for
}

function expandCollapseAll(expandAll, makeAjaxCall) {
	expandCollapseButton = document.getElementById('expandCollapse');
	if (expandAll == 'false') {
		if (makeAjaxCall == null) {
			saveReportOptionsState("expandAll", "0");
		}
		expandCollapseButton.title = "{$mod_strings.LBL_REPORT_EXPAND_ALL}";
		expandCollapseButton.value = "{$mod_strings.LBL_REPORT_EXPAND_ALL}";
		expandCollapseButton.onclick = function() { expandCollapseAll('true') };
	} else {
		if (makeAjaxCall == null) {
			saveReportOptionsState("expandAll", "1");
		} // if
		expandCollapseButton.title = "{$mod_strings.LBL_REPORT_COLLAPSE_ALL}";
		expandCollapseButton.value = "{$mod_strings.LBL_REPORT_COLLAPSE_ALL}";
		expandCollapseButton.onclick = function() { expandCollapseAll('false') };
	} // else
	for (var i = 0 ; i < totalDivCounter ; i++) {
		expandCollapseComboSummaryDivTable(("combo_summary_div_" + i), expandAll);
	} // for
} // fn


function doExpandCollapseAll() {
	var expandAllState = document.getElementById('expandAllState');
	if (expandAllState != null && (expandAllState.value == "0")) {
		expandCollapseAll('false', false);
	} // if
} // fn

function expandCollapseComboSummaryDiv(divId) {

}

function expandCollapseComboSummaryDivTable(divId, expandAll) {
	if (document.getElementById(divId)) {
		var searchReturn = document.getElementById("img_" + divId).innerHTML.search(/advanced_search/);
		if (expandAll != null) {
			if (expandAll == 'true') {
				searchReturn = 1;
			} else {
				searchReturn = -1;
			} // else
		} // if
		if (searchReturn != -1) {
			showHideTableRows(divId, true);
			//document.getElementById(divId).style.display = "";
			document.getElementById("img_" + divId).innerHTML = 
			document.getElementById("img_" + divId).innerHTML.replace(/advanced_search/,"basic_search");
			document.getElementById('expanded_combo_summary_divs').value += divId + " ";
		} else {
			//document.getElementById(divId).style.display = "none";
			showHideTableRows(divId, false);
			document.getElementById("img_" + divId).innerHTML = 			
			document.getElementById("img_" + divId).innerHTML.replace(/basic_search/,"advanced_search");
			document.getElementById('expanded_combo_summary_divs').value = 
			document.getElementById('expanded_combo_summary_divs').value.replace(divId,"");

		} // else
	}
}


</script>
{php}
if ( ! isset($header_row[0]['norows'])) {
	echo get_form_header( $mod_strings['LBL_GRAND_TOTAL'],"", false); 
} // if
if ( $reporter->has_summary_columns()) {
	// start template_total_table code
	global $mod_strings;
	$total_header_row = $reporter->get_total_header_row(); 
	$total_row = $reporter->get_summary_total_row();
	if ( isset($total_row['group_pos'])) {
		$args['group_pos'] = $total_row['group_pos'];
	} // if
	if ( isset($total_row['group_column_is_invisible'])) {
		$args['group_column_is_invisible'] = $total_row['group_column_is_invisible'];
	} // if
 	$reporter->layout_manager->setAttribute('no_sort',1);
  	echo get_form_header( $mod_strings['LBL_GRAND_TOTAL'],"", false); 
  	template_header_row($total_header_row, $args, false, $_smarty_tpl);
{/php}
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list view">
	{if ($show_pagination neq "")}
	{$pagination_data}
	{/if}
	<tr height="20">
	{if ($isSummaryCombo)}
	<th scope="col" align='left'  valign=middle nowrap>&nbsp;</th>
	{/if}
	{if ($isSummaryComboHeader)}
	<th><span id="img_{$divId|escape:'html':'UTF-8'}"><a href="javascript:expandCollapseComboSummaryDiv('{$divId|escape:javascript}')"><img width="8" height="8" border="0" absmiddle="" alt="{$mod_strings.LBL_ALT_SHOW}" src="{$image_path}advanced_search.gif"/></a></span></th>
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
		{if $cell eq ""}
	{php}	
		$cell = "&nbsp;";
		$_smarty_tpl->assign('cell', $cell);
	{/php}
		{/if}		
		<td scope="col" align='left'  valign=middle nowrap>	
		
		{$cell}
		{/if}
	{/foreach}
	</tr>
{php}
  	if (!empty($total_row)) {
    	template_list_row($total_row, false, false, '', $_smarty_tpl);
{/php}
		<tr height=20 class="{$row_class}" onmouseover="setPointer(this, '{$rownum|escape:javascript}', 'over', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');" onmouseout="setPointer(this, '{$rownum|escape:javascript}', 'out', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');" onmousedown="setPointer(this, '{$rownum|escape:javascript}', 'click', '{$bg_color|escape:javascript}', '{$hilite_bg|escape:javascript}', '{$click_bg|escape:javascript}');">
		{if ($isSummaryComboHeader)}
		<td><span id="img_{$divId|escape:'html':'UTF-8'}"><a href="javascript:expandCollapseComboSummaryDiv('{$divId|escape:javascript}')"><img width="8" height="8" border="0" absmiddle="" alt="{$mod_strings.LBL_ALT_SHOW}" src="{$image_path}advanced_search.gif"/></a></span></td>
		{/if}
		{php}
			$count = 0;
			$_smarty_tpl->assign('count', $count);
		{/php}
		{foreach from=$column_row.cells key=module item=cell}
			{if (($column_row.group_column_is_invisible != "") && ($count|in_array:$column_row.group_pos)) }
		{php}	
			$count = $count + 1;
			$_smarty_tpl->assign('count', $count);
		{/php}
			{else}
			{if $cell eq ""}
		{php}	
			$cell = "&nbsp;";
			$_smarty_tpl->assign('cell', $cell);
		{/php}
			{/if}
			
			<td width="{$width|escape:'html':'UTF-8'}%" align="left" valign=TOP class="{$row_class}" bgcolor="{$bg_color|escape:'html':'UTF-8'}" scope="row">
			
			{$cell}
			{/if}
		{/foreach}
		</tr>
		
{php}
  	} else {
		echo template_no_results();
  	}
	echo template_end_table($args);
  	// end template_total_table code
  //template_total_table($reporter);
} // if
template_query_table($reporter); 
{/php}

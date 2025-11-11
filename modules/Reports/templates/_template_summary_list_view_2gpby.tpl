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
	require_once('modules/Reports/templates/templates_reports.php');
	$reporter = $_smarty_tpl->getTemplateVars('reporter');
	$args = $_smarty_tpl->getTemplateVars('args');
	$header_row = $_smarty_tpl->getTemplateVars('header_row');
	$got_row = 0;
	$maximumCellSize = 0;
	$rowsAndColumnsData = array();
	$legend = array();
	$columnDataFor2ndGroup = array();
	$columnDataArray = getColumnDataAndFillRowsFor2By2GPBY($reporter, $header_row, $rowsAndColumnsData, $columnDataFor2ndGroup, 1, $maximumCellSize, $legend);
	$headerColumnNameArray = getHeaderColumnNamesForMatrix($reporter, $header_row, $columnDataFor2ndGroup);
	$columnNameArray = getColumnNamesForMatrix($reporter, $header_row, $columnDataFor2ndGroup);
	$totalColumns = count($headerColumnNameArray) + count($columnDataFor2ndGroup) - 1;
	$_smarty_tpl->assign('legend', $legend);
	$maximumCellSize = $maximumCellSize - $reporter->addedColumns;
	$_smarty_tpl->assign('legend', implode(",", $legend));
{/php}

<B>{$mod_strings.LBL_REPORT_DATA_COLUMN_ORDERS}</B> {$legend|escape:'html':'UTF-8'}
<br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportlistView">
<tr height="20">
{php}
	for ($i = 0 ; $i < count($headerColumnNameArray) ; $i++) {
		$_smarty_tpl->assign('headerColumnName', $headerColumnNameArray[$i]);
		$headerColumnClassName = "reportlistViewMatrixThS1";
		if ($i == (count($headerColumnNameArray) - 1)) {
			$headerColumnClassName = "reportlistViewMatrixThS2";
		} // if
		$_smarty_tpl->assign('headerColumnClassName', $headerColumnClassName);
		if ($i == 1) {
		$_smarty_tpl->assign('topLevelColSpan', count($columnDataFor2ndGroup));
{/php}
	<th scope="col" align='center' colspan="{$topLevelColSpan|escape:'html':'UTF-8'}" class="{$headerColumnClassName|escape:'html':'UTF-8'}" valign=middle wrap>{$headerColumnName|escape:'html':'UTF-8'}</td>
{php}
		} else {
		$rowSpan = 2;
		if (count($columnDataFor2ndGroup) == 0) {
			$rowSpan = 1;
		} // if
		$_smarty_tpl->assign('topLevelRowSpan', $rowSpan);
{/php}
	<th scope="col" align='center' rowspan="{$topLevelRowSpan|escape:'html':'UTF-8'}" class="{$headerColumnClassName|escape:'html':'UTF-8'}" valign=middle wrap>{$headerColumnName|escape:'html':'UTF-8'}</td>

{php}
		} // else
	} // for
{/php}
</tr>
{php}
	if ($totalColumns > 2) {
{/php}
<tr height="20">
{php}
	$count = 0;
	for ($i = 0 ; $i < $totalColumns ; $i++) {
		if ($i == 0 || $i == ($totalColumns -1)) {
			continue;
		}  else {
		$headerColumn2ClassName = "reportlistViewMatrixThS1";
		$_smarty_tpl->assign('headerColumn2ClassName', $headerColumn2ClassName);
		$cellData = $columnDataFor2ndGroup[$count];
		if (empty($cellData)) {
			$cellData = " ";
		} // if
		$_smarty_tpl->assign('columnDataFor2ndGroup', $cellData);
		$count++;
{/php}
	<th scope="col" align='center' class="{$headerColumn2ClassName|escape:'html':'UTF-8'}" valign=middle wrap>{$columnDataFor2ndGroup}</td>
{php}
		} // else
	} // for
{/php}
</tr>
{php}
	} // if
{/php}
{php}
	// iteration for the group by data
	for ($i = 0 ; $i < count($rowsAndColumnsData) ; $i++) {
		$rowData = $rowsAndColumnsData[$i];
		for ($k = 0 ; $k < $maximumCellSize ; $k++) {
{/php}
		<tr height="20">
{php}
		for ($j = 0 ; $j < count($columnNameArray) ; $j++) {
			$cellData = " ";
			if ($j == 0 ) {
				if ($k != 0) {
					continue;
				} // if
				if (isset($rowData[$columnNameArray[$j]])) {
					$cellData = $rowData[$columnNameArray[$j]];
				} // if
				if (empty($cellData)) {
					$cellData = " ";
				} // if
				$_smarty_tpl->assign('cellData', $cellData);
				$_smarty_tpl->assign('rowSpanForData', $maximumCellSize);
				$dataCellClass = "reportlistViewMatrixRightEmptyData";
				if ($i == (count($rowsAndColumnsData)-1)) {
					$dataCellClass = "reportlistViewMatrixRightEmptyData1";
				} // if
				$_smarty_tpl->assign('dataCellClass', $dataCellClass);
{/php}
	<th scope="col" ROWSPAN='{$rowSpanForData|escape:'html':'UTF-8'}' align='center' class="{$dataCellClass|escape:'html':'UTF-8'}" valign=middle wrap>{$cellData}</td>
{php}
			} else {
				$cellData = " ";
				if (isset($rowData[$columnNameArray[$j]])) {
					$cellDataArray = $rowData[$columnNameArray[$j]];
					if (is_array($cellDataArray)) {
						$arrayKeys = array_keys($cellDataArray);
						$cellData = $cellDataArray[$arrayKeys[$k]];
						if ($j == 1) {
							//$cellData = $arrayKeys[$k] . " = " . $cellData;
						} // if
						//$cellData = " ";
					} else {
						$cellData = " ";
					} // else
				} // if
				$_smarty_tpl->assign('cellData', $cellData);
				$dataCellClass = "reportGroupByDataMatrixEvenListRowS1";
				if ($j == (count($columnNameArray)-1)) {
					$dataCellClass = "reportGroupByDataMatrixEvenListRowS2";
				} // if
				if ($i == (count($rowsAndColumnsData)-1)) {
					if ($k == ($maximumCellSize -1)) {
						$dataCellClass = "reportGroupByDataMatrixEvenListRowS3";
						if ($j == (count($columnNameArray)-1)) {
							$dataCellClass = "reportGroupByDataMatrixEvenListRowS4";
						} // if
					} // if
				} // if

				$_smarty_tpl->assign('dataCellClass', $dataCellClass);
{/php}
	<td scope="col" align='center' class="{$dataCellClass|escape:'html':'UTF-8'}" valign=middle wrap>{$cellData}</td>
{php}
			} // else
		} // for
{/php}
</tr>
{php}
		} // for
{/php}
{php}
	} // for
	if (empty($rowsAndColumnsData)) {
{/php}
<tr height="20">
{php}
		$emptyRowColumns = 2; // This is for 1 group by and 1 grand total
		if (count($columnDataFor2ndGroup) == 0) {
			$emptyRowColumns = $emptyRowColumns + 1;
		} else {
			$emptyRowColumns = $emptyRowColumns + count($columnDataFor2ndGroup);
		} // else
		for ($j = 0 ; $j < $emptyRowColumns ; $j++) {
{/php}
<td scope="col" align='center' class="reportGroupByDataMatrixEvenListRowS4" valign=middle wrap>No results</td>
{php}
		} // for
	} // if
{/php}
</tr>
</table>
{php}
template_query_table($reporter);
{/php}


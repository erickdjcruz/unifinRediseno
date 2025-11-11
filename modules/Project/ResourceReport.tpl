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
<!-- BEGIN: main -->
<script type="text/javascript" src="{$CALENDAR_LANG_FILE|escape:'html':'UTF-8'}"></script>
<table width="100%" cellpadding="1" cellspacing="1" border="0" >
	<tr>
		<td style="padding-bottom: 2px;" colspan=6>
			<form name="ResourceView" id="ResourceView" method="post" action="index.php">
{sugar_csrf_form_token}
				<input type="hidden" name="module" value="Project" />
				<input type="hidden" name="sugar_body_only" id="sugar_body_only" value="1">
                <input type="hidden" name="record" id="record" value="{$ID|escape:'html':'UTF-8'}" />
				<input type="hidden" name="action" id="action" value="ResourceReport" />
		</td>
	</tr>
	<tr>
		<td width="15%">{$MOD.LBL_LIST_RESOURCE|escape:'html':'UTF-8'}:<span class="required">*</span></td>
		<td><select id='resource' name='resource'>
			<option value="">----</option>
			{foreach from=$RESOURCES item="RESOURCE"}
				{if $SELECTED_RESOURCE == $RESOURCE->id}
					<option value="{$RESOURCE->id|escape:'html':'UTF-8'}" selected>{$RESOURCE->full_name|escape:'html':'UTF-8'}</option>
				{else}
					<option value="{$RESOURCE->id|escape:'html':'UTF-8'}">{$RESOURCE->full_name|escape:'html':'UTF-8'}</option>
				{/if}
			{/foreach}		
			</select>
		</td>
	</tr>
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_START|escape:'html':'UTF-8'}:<span class="required">*</span></td>
        <td>
            <input name="date_start" id="date_start" tabindex="2" size="11" maxlength="10" type="text" value="{$DATE_START|escape:'html':'UTF-8'}" />
            {sugar_getimage name="jscalendar" ext=".gif" alt="$USER_DATEFORMAT" other_attributes="align='absmiddle' id='date_start_trigger' onclick='parseDate(document.getElementById(\'date_start\'), \'$CALENDAR_DATEFORMAT\');' "}&nbsp;</td>
        </td>
	</tr>
	<tr>
		<td>{$MOD.LBL_FILTER_DATE_RANGE_FINISH|escape:'html':'UTF-8'}:<span class="required">*</span></td>
        <td>
            <input name="date_finish" id="date_finish" type="input" tabindex="2" size="11" maxlength="10" value="{$DATE_FINISH|escape:'html':'UTF-8'}" />
            {sugar_getimage name="jscalendar" ext=".gif" alt="$USER_DATEFORMAT" other_attributes="align='absmiddle' id='date_finish_trigger' onclick='parseDate(document.getElementById(\'date_finish\'), \'$CALENDAR_DATEFORMAT\');' "}&nbsp;
        </td>
    </tr>
	<tr>
		<td colspan=2><input class="button" type="button" name="button" value="{$MOD.LBL_REPORT|escape:'html':'UTF-8'}"
			onclick="submitForm()"  />
		</td>
	</tr>

</form>
</table>
<br/>
<h2>{$MOD.LBL_DAILY_REPORT|escape:'html':'UTF-8'}</h2>
<table id="resourceTable" cellspacing="1" class="other view" width="25%">
	<tr>
		<th width="10%">{$MOD.LBL_DATE|escape:'html':'UTF-8'}</th>
		<th width="10%">{$MOD.LBL_PERCENT_BUSY|escape:'html':'UTF-8'}</th>
	</tr>	
	{foreach from=$DATE_RANGE_ARRAY item="PERCENT" key="DATE"}
	<tr scope="row">
		<td>{$DATE|escape:'html':'UTF-8'}</td>
		{if $PERCENT >= 0}
			<td>{$PERCENT|escape:'html':'UTF-8'}</td>
		{else}
			<td>{$MOD.LBL_HOLIDAY|escape:'html':'UTF-8'}</td>
		{/if}
	</tr>
	{/foreach}
	
</table>
<br/>
<h2>{$MOD.LBL_PROJECT_TASK_SUBPANEL_TITLE|escape:'html':'UTF-8'}</h2>
<table id="resourceTable" cellspacing="1" class="other view">
	<tr>
		<th width="3%">{$MOD.LBL_TASK_ID|escape:'html':'UTF-8'}</th>
		<th width="15%" nowrap>{$MOD.LBL_MODULE_NAME|escape:'html':'UTF-8'}</th>
		<th width="25%" nowrap>{$MOD.LBL_TASK_NAME|escape:'html':'UTF-8'}</th>
		<th width="5%">{$MOD.LBL_PERCENT_COMPLETE|escape:'html':'UTF-8'}</th>
		<th width="5%">{$MOD.LBL_DURATION|escape:'html':'UTF-8'}</th>
		<th width="5%">{$MOD.LBL_START|escape:'html':'UTF-8'}</th>
		<th width="5%">{$MOD.LBL_FINISH|escape:'html':'UTF-8'}</th>
	</tr>	
	{foreach from=$TASKS item="TASK"}
	<tr>
		{assign var=project_id value=$TASK->project_id}
		<td>{$TASK->project_task_id|escape:'html':'UTF-8'}</td>
		<td>{$PROJECTS[$project_id]->name|escape:'html':'UTF-8'}</td>
		<td>{$TASK->name|escape:'html':'UTF-8'}</td>
		<td>{$TASK->percent_complete|escape:'html':'UTF-8'}</td>
		<td>{$TASK->duration|escape:'html':'UTF-8'} {$TASK->duration_unit|escape:'html':'UTF-8'}</td>
		<td>{$TASK->date_start|escape:'html':'UTF-8'}</td>
		<td>{$TASK->date_finish|escape:'html':'UTF-8'}</td>
	</tr>
	{/foreach}
	
</table>
<br/>
<h2>{$MOD.LBL_HOLIDAYS_TITLE|escape:'html':'UTF-8'}</h2>
<table id="holidaysTable" cellspacing="1" class="other view" width="50%">
	<tr>
		<th width="5%">{$MOD.LBL_DATE|escape:'html':'UTF-8'}</th>
		<th width="45%">{$MOD.LBL_MODULE_NAME|escape:'html':'UTF-8'}</th>
	</tr>	
	{foreach from=$HOLIDAYS item="HOLIDAY" key="i"}
	<tr>
		<td>{$HOLIDAY.holidayDate|escape:'html':'UTF-8'}</td>
		<td>{$HOLIDAY.projectName|escape:'html':'UTF-8'}</td>
	</tr>
	{/foreach}
</table>
<script type="text/javascript">
Calendar.setup ({
	inputField : "date_start", ifFormat : '{$CALENDAR_DATEFORMAT|escape:javascript}', showsTime : false, button : "date_start_trigger", singleClick : true, step : 1, weekNumbers:false});
Calendar.setup ({
	inputField : "date_finish", ifFormat : '{$CALENDAR_DATEFORMAT|escape:javascript}', showsTime : false, button : "date_finish_trigger", singleClick : true, step : 1, weekNumbers:false});

function submitForm() {ldelim}
	if (trim(document.getElementById('date_start').value) == '' ||
		trim(document.getElementById('date_finish').value) == '' ||
		document.getElementById('resource').value == '') {ldelim}
		alert(requiredTxt);
		return;
	{rdelim}
	document.getElementById('ResourceView').submit();	
{rdelim}	
</script>

<!-- END: main -->

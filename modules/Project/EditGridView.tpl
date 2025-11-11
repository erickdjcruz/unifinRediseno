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
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Project/gantt.css'}" />

<script type="text/javascript" src="{sugar_getjspath file='include/javascript/yui/ext/yui-ext.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/popup_parent_helper.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='modules/Project/gantt.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='modules/Project/grid.js'}"></script>

<script type="text/javascript">

function initUI(){
	var view = Get_Cookie("project_management_view");
	if (view == "grid_only") {
		SUGAR.gantt.gridOnly();
	}
	else if (view == "gantt_only") {
		SUGAR.gantt.ganttOnly();
	}
	else {
	    var YSB = YAHOO.ext.SplitBar;
	    var resizer = new YSB('resizer', 'grid_space', null, YSB.LEFT);
	    resizer.setAdapter(new YSB.AbsoluteLayoutAdapter("project_container"));
	    resizer.minSize = 100;
	    resizer.maxSize = 900;
	    resizer.onMoved.subscribe(adjustGantt);
	    resizer.setCurrentSize(700);

    	YAHOO.util.Dom.setStyle('grid_space', 'width', '700px');
    	YAHOO.util.Dom.setStyle('gantt_area', 'margin-left', '704px');
    	YAHOO.util.Dom.setStyle('gantt_area', 'margin-right', '0px');
    }

}
function adjustGantt(splitbar, newSize){
    if(splitbar.el.id == 'resizer'){
        YAHOO.util.Dom.setStyle('gantt_area', 'margin-left', newSize+4 + "px");
    }
}

YAHOO.util.Event.on(window, 'load', initUI);
var resources = new Array();

</script>

<h2>{$MOD.LBL_VIEW_GANTT_TITLE|escape:'html':'UTF-8'}</h2>
<table width="100%" cellpadding="0" cellspacing="0" border="0" >
<tr>
<td>

<form name="EditViewGrid" id="EditViewGrid" method="post" action="index.php">
{sugar_csrf_form_token}
    <input type="hidden" name="module" value="Project" />
    <input type="hidden" name="record" value="{$ID|escape:'html':'UTF-8'}" />
    <input type="hidden" name="team_id" value="{$TEAM|escape:'html':'UTF-8'}" />
    <input type="hidden" name="is_template" value="{$IS_TEMPLATE|escape:'html':'UTF-8'}" />
    <input type="hidden" name="to_pdf" id="to_pdf" value="1">
    <input type="hidden" name="action" id="action" value="SaveGrid" />
    <input type="hidden" name="numRowsToSave"  id="numRowsToSave" value="">
    <input type="hidden" name="project_id" value="{$ID|escape:'html':'UTF-8'}">
    <input type="hidden" name="project_name" value="{$PROJECT->name|escape:'html':'UTF-8'}">
    <input type="hidden" name="pdfclass"  id="pdfclass" value="{$PDF_CLASS|escape:'html':'UTF-8'}">
    <input type="hidden" name="sugarpdf"  id="sugarpdf" value="projectgrid">
    <input type="hidden" name="project_start" value="{$PROJECT->estimated_start_date|escape:'html':'UTF-8'}">
    <input type="hidden" name="project_owner" value="{$PROJECT->assigned_user_id|escape:'html':'UTF-8'}">
    <input type="hidden" name="project_end" value="{$PROJECT->estimated_end_date|escape:'html':'UTF-8'}">
    <input type="hidden" name="deletedRows"  id="deletedRows" value="">

<input type="hidden" name="layout" value="ProjectGrid">
    <input name="calendar_start" id="calendar_start" onblur="parseDate(this, '{$CALENDAR_DATEFORMAT|escape:javascript}');SUGAR.gantt.createTable('biweek', this.value, '{$BG_COLOR|escape:javascript}');" type="hidden" tabindex='2' value="{$PROJECT->estimated_start_date|escape:'html':'UTF-8'}" /><br />
    <input name="gantt_chart_start_date" id="gantt_chart_start_date" type="hidden" onChange="SUGAR.gantt.createTable(document.getElementById('gantt_chart_view').value, this.value, '{$BG_COLOR|escape:javascript}');" value="{$formatted_start_date|escape:'html':'UTF-8'}" />
    <input name="gantt_chart_view" id="gantt_chart_view" type="hidden" value="biweek" />

    
<div id="projectButtonsDiv">
    <span id = "grid_buttons_span">
    {if $SELECTED_VIEW <= 1 && $CANEDIT}
        <a valign="bottom" title="{$MOD.LBL_INSERT_BUTTON|escape:'html':'UTF-8'}" id="gantt_button_insert_row">{sugar_getimage name="ProjectInsertRows" ext=".gif" alt=$mod_strings.LBL_INSERTROWS other_attributes='onclick="javascript:SUGAR.grid.insertRow()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_INDENT_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectIndent" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.indentSelectedRows()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_OUTDENT_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectOutdent" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.addToOutdent()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_COPY_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectCopy" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.copyRow()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_CUT_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectCut" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.cutRow()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_PASTE_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectPaste" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.pasteRow()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_DELETE_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectDelete" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.deleteRows()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_EXPAND_ALL_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectExpandAll" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.expandAll()" '}</img></a>
        <a valign="bottom" title="{$MOD.LBL_COLLAPSE_ALL_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectCollapseAll" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.collapseAll()" '}</img></a>
    {/if}
        <a id="saveGridLink" title="{$MOD.LBL_SAVE_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectSave" ext=".gif" other_attributes='onclick="javascript:SUGAR.grid.save()" '}</img></a>
    </span>

        <span id = "gantt_buttons_span">
        {capture assign="attributes"}onclick="SUGAR.gantt.createTable('week', document.getElementById('gantt_chart_start_date').value, '{$BG_COLOR|escape:javascript}');"{/capture}
        <a title="{$MOD.LBL_WEEK_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectWeek" ext=".gif" other_attributes="$attributes"}</img></a>
        {capture assign="attributes"}onclick="SUGAR.gantt.createTable('biweek', document.getElementById('gantt_chart_start_date').value, '{$BG_COLOR|escape:javascript}');"{/capture}
        <a title="{$MOD.LBL_BIWEEK_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="Project2Weeks" ext=".gif" other_attributes="$attributes"}</img></a>
        {capture assign="attributes"}onclick="SUGAR.gantt.createTable('month', document.getElementById('gantt_chart_start_date').value, '{$BG_COLOR|escape:javascript}');"{/capture}
        <a title="{$MOD.LBL_MONTH_BUTTON|escape:'html':'UTF-8'}">{sugar_getimage name="ProjectMonth" ext=".gif" other_attributes="$attributes"}</img></a>
    </span>

        <input type="hidden" name="selected_view" id="selected_view" value="{$SELECTED_VIEW|escape:'html':'UTF-8'}" />
        <select id="gridViewSelect" onChange="SUGAR.grid.changeView()">
            <option value=0>{$MOD.LBL_FILTER_VIEW|escape:'html':'UTF-8'}...</option>
            <option value=1>{$MOD.LBL_FILTER_ALL_TASKS|escape:'html':'UTF-8'}</option>
            <option value=2>{$MOD.LBL_FILTER_COMPLETED_TASKS|escape:'html':'UTF-8'}</option>
            <option value=3>{$MOD.LBL_FILTER_INCOMPLETE_TASKS|escape:'html':'UTF-8'}</option>
            <option value=4>{$MOD.LBL_FILTER_MILESTONES|escape:'html':'UTF-8'}</option>
            <option value=5>{$MOD.LBL_FILTER_RESOURCE|escape:'html':'UTF-8'}</option>
            <option value=6>{$MOD.LBL_FILTER_DATE_RANGE|escape:'html':'UTF-8'}</option>
            <option value=7>{$MOD.LBL_LIST_OVERDUE_TASKS|escape:'html':'UTF-8'}</option>
            <option value=8>{$MOD.LBL_LIST_UPCOMING_TASKS|escape:'html':'UTF-8'}</option>
            <option value=9>{$MOD.LBL_FILTER_MY_TASKS|escape:'html':'UTF-8'}</option>
            <option value=10>{$MOD.LBL_FILTER_MY_OVERDUE_TASKS|escape:'html':'UTF-8'}</option>
            <option value=11>{$MOD.LBL_FILTER_MY_UPCOMING_TASKS|escape:'html':'UTF-8'}</option>

        </select>
    <input {if $SELECTED_VIEW != 5 }style="display:none" {/if} type="input" {if $SELECTED_VIEW == 5} value="{$VIEW_FILTER_RESOURCE|escape:'html':'UTF-8'}"{/if} name="view_filter_resource" id="view_filter_resource" value="" />

    <span id="view_filter_6_start_span" {if $SELECTED_VIEW != 6}style="display:none"{/if}>{$MOD.LBL_FILTER_DATE_RANGE_START|escape:'html':'UTF-8'}:</span>
    <input name="view_filter_date_start" id="view_filter_date_start" onblur="parseDate(this, '{$CALENDAR_DATEFORMAT|escape:javascript}');" {if $SELECTED_VIEW != 6 } style="display:none" {/if} type="input" tabindex="2" size="11" maxlength="10" value="{$VIEW_FILTER_DATE_START|escape:'html':'UTF-8'}" />
    <span id="view_filter_6_finish_span" {if $SELECTED_VIEW != 6}style="display:none"{/if}>{$MOD.LBL_FILTER_DATE_RANGE_FINISH|escape:'html':'UTF-8'}: </span>
    <input name="view_filter_date_finish" id="view_filter_date_finish" onblur="parseDate(this, '{$CALENDAR_DATEFORMAT|escape:javascript}');" {if $SELECTED_VIEW !=  6 } style="display:none" {/if} type="input" tabindex="2" size="11" maxlength="10" value="{$VIEW_FILTER_DATE_FINISH|escape:'html':'UTF-8'}" />

    {if $SELECTED_VIEW == 6}
        <script type="text/javascript">
            Calendar.setup ({
                inputField : "view_filter_date_start", ifFormat : '{$CALENDAR_DATEFORMAT|escape:javascript}', showsTime : false, button : "view_filter_date_start", singleClick : true, step : 1, weekNumbers:false});
            Calendar.setup ({
                inputField : "view_filter_date_finish", ifFormat : '{$CALENDAR_DATEFORMAT|escape:javascript}', showsTime : false, button : "view_filter_date_finish", singleClick : true, step : 1, weekNumbers:false});
        </script>
    {/if}
    <span id="view_filter_button_span"></span>
    {if $SELECTED_VIEW == 5 || $SELECTED_VIEW == 6}
        <input class="button" type=button id="view_filter_button" value="{$MOD.LBL_FILTER_VIEW|escape:'html':'UTF-8'}" onclick="document.forms['EditViewGrid'].action.value='EditGridView';document.forms['EditViewGrid'].to_pdf.value	= '0';document.getElementById('EditViewGrid').submit();" />
    {/if}
    <span id="exportToPDFSpan">
        <input class="button" type="button" name="button" value="{$MOD.LBL_EXPORT_TO_PDF|escape:'html':'UTF-8'}" onclick="SUGAR.grid.exportToPDF();"/>
    </span>
    <input class="button" type="button" name="button" value="{$MOD.LNK_PROJECT_RESOURCE_REPORT|escape:'html':'UTF-8'}" onclick="window.open('index.php?module=Project&show_js=1&action=ResourceReport&record={$ID}','','width=700,height=700,resizable=1,scrollbars=1')" />
    <input class="button" type="button" name="button" value="{$MOD.LBL_GRID_ONLY|escape:'html':'UTF-8'}" onclick="javascript:SUGAR.gantt.gridOnly()" />
    <input class="button" type="button" name="button" value="{$MOD.LBL_GANTT_ONLY|escape:'html':'UTF-8'}" onclick="javascript:SUGAR.gantt.ganttOnly()" />
    <input class="button" type="button" name="button" value="{$MOD.LBL_GRID_GANTT|escape:'html':'UTF-8'}" onclick="javascript:SUGAR.gantt.gridGanttView()" />
</div>

    <p/>

    <div id="project_container">
        <div id="grid_space">
            <div>
                <table id="projectTable" border="1" cellpadding="0" cellspacing="0">
                    <tr align=center height="50">
                        <th width="8%">{$MOD.LBL_TASK_ID|escape:'html':'UTF-8'}</th>
                        <th width="5%">{$MOD.LBL_PERCENT_COMPLETE|escape:'html':'UTF-8'}</th>
                        <th width="30%" nowrap>{$MOD.LBL_TASK_NAME|escape:'html':'UTF-8'}</th>
                        <th width="5%" colspan="2">{$MOD.LBL_DURATION|escape:'html':'UTF-8'}</th>
                        <th width="5%">{$MOD.LBL_START|escape:'html':'UTF-8'}</th>
                        <th width="5%">{$MOD.LBL_FINISH|escape:'html':'UTF-8'}</th>
                        <th width="8%">{$MOD.LBL_PREDECESSORS|escape:'html':'UTF-8'}</th>
                        <th width="8%">{$MOD.LBL_RESOURCE_NAMES|escape:'html':'UTF-8'}</th>
                        <th id="optional_0" width="4%">{$MOD.LBL_ACTUAL_DURATION|escape:'html':'UTF-8'}</th>
                    </tr>
                    {foreach from=$TASKS item="TASK" }
                        {assign var=taskEditAccess value=$TASK->ACLAccess('edit')}
                        <tr id="project_task_row_{$TASK->project_task_id|escape:'html':'UTF-8'}" height="23">
                            <td style="cursor:default" scope="row" align="center" {if $taskEditAccess}onClick="SUGAR.grid.clickedRow({$TASK->project_task_id}, event);"{/if}>
                                <div id="id_{$TASK->project_task_id|escape:'html':'UTF-8'}_divlink" style="display:inline;">{$TASK->project_task_id|escape:'html':'UTF-8'}{if $TASK->milestone_flag == 1}*{/if}</div>
                                <input type="hidden" name="mapped_row_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="mapped_row_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->project_task_id|escape:'html':'UTF-8'}">
                                <input type="hidden" name="parent_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="parent_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->parent_task_id|escape:'html':'UTF-8'}">
                                <input type="hidden" name="obj_id_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="obj_id_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->id|escape:'html':'UTF-8'}">
                                <input type="hidden" name="is_milestone_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="is_milestone_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->milestone_flag|escape:'html':'UTF-8'}">
                                <input type="hidden" name="time_start_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="time_start_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->time_start|escape:'html':'UTF-8'}">
                                <input type="hidden" name="time_finish_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="time_finish_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->time_finish|escape:'html':'UTF-8'}">
                            </td>
                            <td>
                                <input {if $CURRENT_USER != $TASK->resource_id && !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} type="text" size="5" style="border:0" name="percent_complete_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="percent_complete_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->percent_complete|escape:'html':'UTF-8'}"
                                       onBlur="if (SUGAR.grid.validatePercentComplete(this)) {
                            SUGAR.grid.updateAncestorsPercentComplete('{$TASK->project_task_id|escape:javascript}');
                            SUGAR.gantt.changeTask('{$TASK->project_task_id|escape:javascript}');
                            } ">
                            </td>
                            <td nowrap ondblclick="SUGAR.grid.toggle('description_{$TASK->project_task_id|escape:javascript}_div', 'description_{$TASK->project_task_id|escape:'html':'UTF-8'}', 'description_{$TASK->project_task_id|escape:javascript}_divlink');">
                                <div id="description_{$TASK->project_task_id|escape:'html':'UTF-8'}_div" style="display:none;">
                                    <input {if !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} name="description_{$TASK->project_task_id|escape:'html':'UTF-8'}" type="text" maxlength="{ $NAME_LENGTH|escape:'html':'UTF-8' }" style="border:0"  size="40" id="description_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->name|escape:'html':'UTF-8'}"
                                           onblur="SUGAR.grid.blurEvent('{$TASK->project_task_id|escape:javascript}', 'description_{$TASK->project_task_id|escape:javascript}_div','description_{$TASK->project_task_id|escape:javascript}', 'description_{$TASK->project_task_id|escape:javascript}_divlink');">
                                    <input type="hidden" name="description_divlink_input_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="description_divlink_input_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->name|escape:'html':'UTF-8'}">

                                </div>
                                <div id="description_{$TASK->project_task_id|escape:'html':'UTF-8'}_divlink" style="display:inline;width:250px">{$TASK->name|escape:'html':'UTF-8'}</div>
                            </td>
                            <td>
                                <input  {if !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} type="text"  style="border:0;" size="3" name="duration_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="duration_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->duration|escape:'html':'UTF-8'}"
                                        onBlur="if (SUGAR.grid.validateDuration(this)) {
                            SUGAR.grid.calculateEndDate(document.getElementById('date_start_{$TASK->project_task_id|escape:javascript}').value, this.value, '{$TASK->project_task_id|escape:javascript}');
                            SUGAR.grid.updateAllDependentsAfterDateChanges('{$TASK->project_task_id|escape:javascript}');
                            //SUGAR.grid.updateAncestorsTimeData('{$TASK->project_task_id|escape:javascript}');
                            SUGAR.gantt.changeTask('{$TASK->project_task_id|escape:javascript}');
                            } ">
                            </td>
                            <td>
                                <!--Need the hidden duration unit so that even when a resource logs in and the duration_unit select is disabled, we can still export to PDF via this hidden field-->
                                <input type='hidden' name="duration_unit_hidden_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="duration_unit_hidden_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->duration_unit|escape:'html':'UTF-8'}">
                                <select  {if !$CANEDIT || !$taskEditAccess}disabled{/if} style="border:0" name="duration_unit_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="duration_unit_{$TASK->project_task_id|escape:'html':'UTF-8'}" onChange="SUGAR.grid.changeDurationUnits('{$TASK->project_task_id|escape:javascript}')">
                                    {foreach from=$DURATION_UNITS item="DURATION_UNIT" key="DURATION_UNIT_KEY"}
                                        {if $DURATION_UNIT_KEY == $TASK->duration_unit}
                                            <option value="{$DURATION_UNIT_KEY|escape:'html':'UTF-8'}" selected>{$DURATION_UNIT|escape:'html':'UTF-8'}</option>
                                        {else}
                                            <option value="{$DURATION_UNIT_KEY|escape:'html':'UTF-8'}">{$DURATION_UNIT|escape:'html':'UTF-8'}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <input {if !$CANEDIT || !$taskEditAccess}readonly="readonly" {/if} id="date_start_{$TASK->project_task_id|escape:'html':'UTF-8'}" name="date_start_{$TASK->project_task_id|escape:'html':'UTF-8'}" style="border:0" type="text" tabindex="2" size="11" maxlength="10" value="{$TASK->display_date_start|escape:'html':'UTF-8'}">
                            </td>
                            <td>
                                <input {if !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} name="date_finish_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="date_finish_{$TASK->project_task_id|escape:'html':'UTF-8'}"
                                       style="border:0" onchange="parseDate(this, '{$CALENDAR_DATEFORMAT|escape:javascript}'); SUGAR.grid.processFinishDate(this, '{$TASK->project_task_id|escape:javascript}');"
                                       type="text" tabindex="2" size="11" maxlength="10" value="{$TASK->display_date_finish|escape:'html':'UTF-8'}">
                            </td>
                            <td>
                                <input  {if !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} type="text" size="10"  style='border:0' name="predecessors_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="predecessors_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->predecessors|escape:'html':'UTF-8'}"
                                        onblur="if (SUGAR.grid.validatePredecessors(this)){
                                                SUGAR.grid.setDependencyCheckRow('{$TASK->project_task_id|escape:javascript}');
                                                SUGAR.grid.validateDependencyCycles('{$TASK->project_task_id|escape:javascript}');
                                                SUGAR.grid.validatePredecessorIsNotAncestorOrChild('{$TASK->project_task_id|escape:javascript}');
                                                if (SUGAR.grid.dependencyCheckFail != 1){
                                                    SUGAR.grid.updatePredecessorsAndDependents('{$TASK->project_task_id|escape:javascript}');
                                                    SUGAR.grid.calculateDatesAfterAddingPredecessors('{$TASK->project_task_id|escape:javascript}');
                                                    SUGAR.grid.updateAllDependentsAfterDateChanges('{$TASK->project_task_id|escape:javascript}');
                                                    //SUGAR.grid.updateAncestorsTimeData('{$TASK->project_task_id|escape:javascript}');
                                                    SUGAR.gantt.changeTask('{$TASK->project_task_id|escape:javascript}');
                                                    }
                                                }
                                                SUGAR.grid.dependencyCheckFail = 0;
                                                //SUGAR.grid.calculateEndDate(document.getElementById('date_start_{$TASK->project_task_id|escape:javascript}').value, document.getElementById('duration_{$TASK->project_task_id|escape:javascript}').value, '{$TASK->project_task_id|escape:javascript}');">
                            </td>
                            <td>
                                <input type='hidden' name="resource_full_name_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="resource_full_name_{$TASK->project_task_id|escape:'html':'UTF-8'}">
                                <input type='hidden' name="resource_type_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="resource_type_{$TASK->project_task_id|escape:'html':'UTF-8'}">
                                <select  {if !$CANEDIT || !$taskEditAccess}disabled{/if} style="border:0" name="resource_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="resource_{$TASK->project_task_id|escape:'html':'UTF-8'}"
                                         onChange="SUGAR.grid.updateResourceFullNameAndType('{$TASK->project_task_id|escape:javascript}');
                            SUGAR.grid.calculateEndDate(document.getElementById('date_start_{$TASK->project_task_id|escape:javascript}').value, document.getElementById('duration_{$TASK->project_task_id|escape:javascript}').value, '{$TASK->project_task_id|escape:javascript}');
                            SUGAR.grid.calculateDatesAfterAddingPredecessors('{$TASK->project_task_id|escape:javascript}');
                            SUGAR.grid.updateAllDependentsAfterDateChanges('{$TASK->project_task_id|escape:javascript}');
                            //SUGAR.grid.updateAncestorsTimeData('{$TASK->project_task_id|escape:javascript}');
                            SUGAR.gantt.changeTask('{$TASK->project_task_id|escape:javascript}');">
                                    <option value="">----</option>
                                    {foreach from=$RESOURCES item="RESOURCE"}
                                        {if $RESOURCE->id == $TASK->resource_id}
                                            <option value="{$RESOURCE->id|escape:'html':'UTF-8'}" selected>{$RESOURCE->full_name|escape:'html':'UTF-8'}</option>
                                            {assign var=resource_full_name value=$RESOURCE->full_name}
                                            {assign var=resource_type value=$RESOURCE->object_name}
                                        {else}
                                            <option value="{$RESOURCE->id|escape:'html':'UTF-8'}">{$RESOURCE->full_name|escape:'html':'UTF-8'}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </td>
                            <td id="optional_{$TASK->project_task_id|escape:'html':'UTF-8'}"><input {if $CURRENT_USER != $TASK->resource_id && !$CANEDIT || !$taskEditAccess}readonly="readonly"{/if} type="text" size="10" style="border:0" name="actual_duration_{$TASK->project_task_id|escape:'html':'UTF-8'}" id="actual_duration_{$TASK->project_task_id|escape:'html':'UTF-8'}" value="{$TASK->actual_duration|escape:'html':'UTF-8'}" onBlur="SUGAR.grid.validateDuration(this);">
                            </td>
                        </tr>
                        <script type="text/javascript">
                            document.getElementById('resource_full_name_{$TASK->project_task_id|escape:javascript}').value="{$resource_full_name|escape:javascript}";
                            document.getElementById('resource_type_{$TASK->project_task_id|escape:javascript}').value="{$resource_type|escape:javascript}";
                            SUGAR.grid.setUpIndentedRow('{$TASK->project_task_id|escape:javascript}', '{$TASK->parent_task_id|escape:javascript}');
                        </script>
                        {assign var=resource_full_name value=""}
                        {assign var=resource_type value=""}
                    {/foreach}
                </table>
                <script type="text/javascript">
                    document.getElementById('gridViewSelect').selectedIndex = document.getElementById('selected_view').value;
                </script>

                <br /><br />
            </div>
        </div>

        <div id="gantt_area">
            <div id="gantt_space"></div>
        </div>
        <br/>
        <br/>

        <div id="resizer"></div>

    </div>
</form>

<div id="task_detail_area_div" name="task_detail_area_div" width="100%" style="display:none;border:1px">
    {sugar_getimage name="img_loading" ext=".gif" other_attributes='border="0" '}
</div>
<script type="text/javascript">
{foreach from=$RESOURCES item="RESOURCE"}
    var resourceObj = new Object();
    resourceObj.id = '{$RESOURCE->id|escape:javascript}';
    resourceObj.full_name = '{$RESOURCE->full_name|escape:javascript}';
    resourceObj.type = '{$RESOURCE->object_name|escape:javascript}';
    resources.push(resourceObj);
{/foreach}
var durationOptions = new Array();
{foreach from=$DURATION_UNITS item="DURATION_UNIT" key= "DURATION_UNIT_KEY"}
    var durationOptionObj = new Object();
    durationOptionObj.key = "{$DURATION_UNIT_KEY|escape:javascript}"
    durationOptionObj.value = "{$DURATION_UNIT|escape:javascript}"
    durationOptions.push(durationOptionObj);
{/foreach}
var holidays = new Array();
{foreach from=$HOLIDAYS item="HOLIDAY"}
	var holidayObj = new Object();
	holidayObj.resource = '{$HOLIDAY->person_id}';
	holidayObj.date = '{$HOLIDAY->holiday_date}';
	holidays.push(holidayObj);
{/foreach}

SUGAR.grid.gridNotLoaded();
SUGAR.grid.setupCalendar(null,  "{$CALENDAR_DATEFORMAT|escape:javascript}", "{$BG_COLOR|escape:javascript}", "projectTable","{sugar_getimagepath file='ProjectMinus.gif'}","{sugar_getimagepath file='ProjectPlus.gif'}");
SUGAR.grid.setUpCurrentUser('{$CURRENT_USER}', '{$OWNER}');
SUGAR.grid.setupHolidays(holidays);
SUGAR.grid.setupResourceOptions(resources);
SUGAR.grid.setupDurationUnitsOptions(durationOptions);
SUGAR.grid.adjustInitialIndentedRows();
SUGAR.grid.buildPredecessorsAndDependents();
SUGAR.grid.gridLoaded();
SUGAR.gantt.createTable('biweek', document.getElementById('calendar_start').value, "{$BG_COLOR|escape:javascript}");

SUGAR.grid.onAfterInsertRow = function(num) {ldelim}
    YAHOO.util.Dom.setAttribute(YAHOO.util.Selector.query('input[size="40"]'), 'maxlength', {$NAME_LENGTH|escape:javascript});
    {rdelim}

{if $TASKCOUNT == 0}
for (i = 0; i < 2; i++)
    SUGAR.grid.insertRow();
{/if}

</script>
{$JAVASCRIPT}

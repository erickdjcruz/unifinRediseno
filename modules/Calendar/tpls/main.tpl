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
<link type="text/css" href="{sugar_getjspath file="modules/Calendar/Cal.css"}" rel="stylesheet" />
{sugar_getscript file="modules/Calendar/Cal.js"}
<script type="text/javascript">

	YAHOO.util.Event.onDOMReady(function(){
		dom_loaded = true;
	});
	
	function check_cal_loaded(){
		return (typeof cal_loaded != 'undefined' && cal_loaded == true && typeof dom_loaded != 'undefined' && dom_loaded == true);
	}
	

	SUGAR.util.doWhen(check_cal_loaded, function(){
	
		CAL.view = "{$view|escape:'javascript':'UTF-8'}";
		CAL.style = "{$style|escape:'javascript':'UTF-8'}";
		CAL.t_step = {$t_step|escape:'javascript':'UTF-8'};
		CAL.current_user_id = "{$current_user_id|escape:'javascript':'UTF-8'}";
		CAL.current_user_name = "{$current_user_name|escape:'javascript':'UTF-8'}";
		CAL.time_format = "{$time_format|escape:'javascript':'UTF-8'}";
		CAL.enable_repeat = "{$enable_repeat|escape:'javascript':'UTF-8'}";
		CAL.items_draggable = "{$items_draggable|escape:'javascript':'UTF-8'}";
		CAL.items_resizable = "{$items_resizable|escape:'javascript':'UTF-8'}";
		CAL.cells_per_day = {$cells_per_day|escape:'javascript':'UTF-8'};
		CAL.current_params = {};
		CAL.grid_start_ts = {$grid_start_ts|escape:'javascript':'UTF-8'};
		CAL.scroll_slot = {$scroll_slot|escape:'javascript':'UTF-8'};
		CAL.basic.min_height = {$basic_min_height|escape:'javascript':'UTF-8'};

		CAL.lbl_create_new = "{$MOD.LBL_CREATE_NEW_CALL|escape:'javascript':'UTF-8'}";
		CAL.lbl_edit = "{$MOD.LBL_EDIT_CALL|escape:'javascript':'UTF-8'}";
		CAL.lbl_saving = "{$MOD.LBL_SAVING|escape:'javascript':'UTF-8'}";
		CAL.lbl_loading = "{$MOD.LBL_LOADING|escape:'javascript':'UTF-8'}";
		CAL.lbl_sending = "{$MOD.LBL_SENDING_INVITES|escape:'javascript':'UTF-8'}";
		CAL.lbl_confirm_remove = "{$MOD.LBL_CONFIRM_REMOVE|escape:'javascript':'UTF-8'}";
		CAL.lbl_confirm_remove_all_recurring = "{$MOD.LBL_CONFIRM_REMOVE_ALL_RECURRING|escape:'javascript':'UTF-8'}";
		
		CAL.lbl_error_saving = "{$MOD.LBL_ERROR_SAVING|escape:'javascript':'UTF-8'}";
		CAL.lbl_error_loading = "{$MOD.LBL_ERROR_LOADING|escape:'javascript':'UTF-8'}";
		CAL.lbl_repeat_limit_error = "{$MOD.LBL_RECURRING_LIMIT_ERROR|escape:'javascript':'UTF-8'}";

		CAL.lbl_no_access = "{$MOD.LBL_NO_ACCESS|escape:'javascript':'UTF-8'}";

		CAL.year = {$year|escape:'javascript':'UTF-8'};
		CAL.month = {$month|escape:'javascript':'UTF-8'};
		CAL.day = {$day|escape:'javascript':'UTF-8'};

		CAL.print = {$isPrint|escape:'javascript':'UTF-8'};
		

		var scrollable = CAL.get("cal-scrollable");
		if(scrollable){

            //grab all scrollable divs and set the scrollTop value on each one
            var scrollableDivs = CAL.query("#cal-grid #cal-scrollable");
            var sharedScrollTop = (CAL.slot_height + 1) * CAL.scroll_slot - 1;
            for (var i = 0; i < scrollableDivs.length; i++) {
                scrollableDivs[i].scrollTop =  sharedScrollTop;
            }

            if(CAL.view == "day") {
                scrollable.scrollTop++;
            }
		}


		{if $view == "shared"}
			{counter name="un" start=0 print=false assign="un"}
			{foreach name="shared" from=$shared_ids key=k item=member_id}				
				CAL.shared_users['{$member_id|escape:'javascript':'UTF-8'}'] = '{$un|escape:'javascript':'UTF-8'}';
				{counter name="un" print=false}
			{/foreach}
			CAL.shared_users_count = "{$shared_users_count|escape:'javascript':'UTF-8'}";
		{/if}
	
		CAL.field_list = new Array();
		CAL.field_disabled_list = new Array();			

		CAL.activity_colors = [];				
		{foreach name=colors from=$activity_colors key=module item=v}
			CAL.activity_colors['{$module|escape:'javascript':'UTF-8'}'] = [];
			CAL.activity_colors['{$module|escape:'javascript':'UTF-8'}']['border'] = '{$v.border|escape:'javascript':'UTF-8'}';
			CAL.activity_colors['{$module|escape:'javascript':'UTF-8'}']['body'] = '{$v.body|escape:'javascript':'UTF-8'}'
		{/foreach}

		CAL.act_types = [];
		CAL.act_types['Meetings'] = 'meeting';
		CAL.act_types['Calls'] = 'call';
		CAL.act_types['Tasks'] = 'task';



		if(CAL.items_draggable){			
			var target_slots = [];			
			var slots = CAL.query('#cal-grid div.slot');
			var cnt = 0;
			CAL.each(
				slots,
				function(i,v){					
					target_slots[i] = new YAHOO.util.DDTarget(slots[i].id,"cal");
					cnt++;
				}
			);
			slots = CAL.query('#cal-grid div.basic_slot');
			CAL.each(
				slots,
				function(i,v){
					target_slots[cnt + i] = new YAHOO.util.DDTarget(slots[i].id,"basic_cal");
				}
			);				
		}	
		
		var nodes = CAL.query("#cal-grid div.slot, #cal-grid div.basic_slot");
		CAL.each(nodes, function(i,v){
			YAHOO.util.Event.on(nodes[i],"mouseover",function(){
				if(CAL.records_openable && !CAL.disable_creating)
					this.style.backgroundColor = "#D1DCFF";							
				if(!this.childNodes.length)	
					this.setAttribute("title",this.getAttribute("time"));
			});
			YAHOO.util.Event.on(nodes[i],"mouseout",function(){
				this.style.backgroundColor = "";
				this.removeAttribute("title");
			});
			YAHOO.util.Event.on(nodes[i],"click",function(){
				if(!CAL.disable_creating){							
					CAL.dialog_create(this);
				}
			});
		});				
		
		CAL.init_edit_dialog({
			width: "{$editview_width|escape:'javascript':'UTF-8'}",
			height: "{$editview_height|escape:'javascript':'UTF-8'}"
		});
		
		YAHOO.util.Event.on(window, 'resize', function(){
			CAL.fit_grid();
			CAL.update_dd.fire();
		});		
				
		YAHOO.util.Event.on("btn-save","click",function(){																	
			if(!CAL.check_forms())
				return false;											
			CAL.dialog_save();	
		});
		
		YAHOO.util.Event.on("btn-send-invites","click",function(){																				
			if(!CAL.check_forms())
				return false;	
			CAL.get("send_invites").value = "1";							
			CAL.dialog_save();	
		});		
				
		YAHOO.util.Event.on("btn-delete","click",function(){
			if(CAL.get("record").value != "")
				if(confirm(CAL.lbl_confirm_remove))
					CAL.dialog_remove();
						
		});	
	
		YAHOO.util.Event.on("btn-cancel","click",function(){			
			document.schedulerwidget.reset();
            if(document.getElementById('empty-search-message')) {
                document.getElementById('empty-search-message').style.display = 'none';
            }
            CAL.editDialog.cancel();						
		}); 
		
		YAHOO.util.Event.on("btn-full-form","click",function(){			
			CAL.full_form();						
		}); 

		CAL.select_tab("cal-tab-1");

		YAHOO.util.Event.on(CAL.get("btn-cancel-settings"), 'click', function(){
			CAL.settingsDialog.cancel();	
		});
		
		YAHOO.util.Event.on(CAL.get("btn-save-settings"), 'click', function(){			
			CAL.get("form_settings").submit();
		});
		

				
		var calendar_items = {$a_str};
					

		CAL.each(calendar_items, function(i,v){
			CAL.add_item_to_grid(calendar_items[i]);
		});

		
		{if $view != "year"}
		CAL.arrange_advanced();
		CAL.basic.populate_grid();		
		CAL.fit_grid();
		CAL.update_dd.fire();
		{/if}
		
		cal_loaded = null;	
	});
</script>
			
<div id="cal-edit" style="display: none;">
	
	<div class="hd"><span id="title-cal-edit"></span></div>
	<div class="bd" id="edit-dialog-content">
		<div id="cal-tabs" class="yui-navset yui-navset-top yui-content" style="height: auto; padding: 0 2px;">
			<ul class="yui-nav">
				<li id="tab_general"><a tabname="cal-tab-1" id="cal-tab-1-link"><em>{$MOD.LBL_GENERAL_TAB|escape:'html':'UTF-8'}</em></a></li>
				<li id="tab_invitees"><a tabname="cal-tab-2" id="cal-tab-2-link"><em>{$MOD.LBL_PARTICIPANTS_TAB|escape:'html':'UTF-8'}</em></a></li>
				{if $enable_repeat}
				<li id="tab_repeat"><a tabname="cal-tab-3" id="cal-tab-3-link"><em>{$MOD.LBL_REPEAT_TAB|escape:'html':'UTF-8'}</em></a></li>
				{/if}
			</ul>
			<div id="cal-tab-1" class="yui-content">
				{include file=$form}
			</div>				
			<div id="cal-tab-2" class="yui-content">
				<div class="h3Row" id="scheduler"></div>
			</div>
			{if $enable_repeat}
			<div id="cal-tab-3" class="yui-content">
				{include file=$repeat}
			</div>
			{/if}
		</div>
	</div>	
	<div id="cal-edit-buttons" class="ft">
		<button id="btn-save" class="button" type="button">{$MOD.LBL_SAVE_BUTTON|escape:'html':'UTF-8'}</button>
		<button id="btn-cancel" class="button" type="button">{$MOD.LBL_CANCEL_BUTTON|escape:'html':'UTF-8'}</button>
		<button id="btn-delete" class="button" type="button">{$MOD.LBL_DELETE_BUTTON|escape:'html':'UTF-8'}</button>
		<button id="btn-send-invites" class="button" type="button">{$MOD.LBL_SEND_INVITES|escape:'html':'UTF-8'}</button>
		<button id="btn-full-form" class="button" type="button">{$APP.LBL_FULL_FORM_BUTTON_LABEL|escape:'html':'UTF-8'}</button>
	</div>
</div>

{if $settings}
{include file=$settings}
{/if}
	
<script type="text/javascript">	
{$GRjavascript}
</script>
	
<script type="text/javascript">	

YAHOO.util.Event.onDOMReady(function(){	
	var schedulerLoader = new YAHOO.util.YUILoader({
		require : ["jsclass_scheduler"],
		onSuccess: function(){
			var root_div = document.getElementById('scheduler');
			var sugarContainer_instance = new SugarContainer(document.getElementById('scheduler'));
			sugarContainer_instance.start(SugarWidgetScheduler);
		}
	});
	schedulerLoader.addModule({
		name :"jsclass_scheduler",
		type : "js",

		fullpath: "{sugar_getjspath file='modules/Meetings/jsclass_scheduler.js'}",

		varName: "global_rpcClient",
		requires: []
	});
	schedulerLoader.insert();
});	

</script>
	
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/jsclass_base.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/jsclass_async.js'}"></script>	
	
<style type="text/css">

	.schedulerDiv h3{
		display: none;
	}
	.schedulerDiv{
		width: auto !important;
	}

</style>	
{if $view == 'day'}
<style type="text/css">

	#cal-grid div.col, #cal-grid div.left_col{
		border-top: 1px solid silver;
		border-bottom: 1px solid silver;
	}

</style>
{/if}

<div id="cal-width-helper" style="width: auto;"></div>


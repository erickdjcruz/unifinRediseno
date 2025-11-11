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

{if !isset($config.enable_autocomplete) || $config.enable_autocomplete==false}
	<select name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}" 
	id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}" 
	title='{{$vardef.help|escape:"hexentity"}}' {{if !empty($tabindex)}} tabindex="{{$tabindex}}" {{/if}}
    {{if !empty($displayParams.accesskey)}} accesskey='{{$displayParams.accesskey}}' {{/if}}  {{$displayParams.field}}
	{{if isset($displayParams.javascript)}}{{$displayParams.javascript}}{{/if}}>

	{html_options options={{sugarvar key='options' string=true}} selected={{sugarvar key='value' string=true}}}
	</select>
{else}
	{assign var="field_options" value={{sugarvar key='options' string="true"}} }
	{capture name="field_val"}{{sugarvar key='value'}}{/capture}
	{assign var="field_val" value=$smarty.capture.field_val}
	{capture name="ac_key"}{{sugarvar key='name'}}{/capture}
	{assign var="ac_key" value=$smarty.capture.ac_key}

	{{if empty($vardef.autocomplete_ajax)}}
		<select style='display:none' name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}" 
		id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}" 
		title='{{$vardef.help|escape:"hexentity"}}' {{if !empty($tabindex)}} tabindex="{{$tabindex}}" {{/if}}
        {{if !empty($displayParams.accesskey)}} accesskey='{{$displayParams.accesskey}}' {{/if}} {{$displayParams.field}}
		{{if isset($displayParams.javascript)}}{{$displayParams.javascript}}{{/if}}>

		{html_options options={{sugarvar key='options' string=true}} selected={{sugarvar key='value' string=true}}}
		</select>
	{{else}}
		<input type="hidden"
		    id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
		    name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
		    value="{{sugarvar key='value'}}">
	{{/if}}

	<input
		id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
		name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
		size="30"
		value="{$field_val|lookup:$field_options}"
		type="text" style="vertical-align: top;">

		
	<span class="id-ff multiple">
	    <button type="button"><img src="{sugar_getimagepath file="id-ff-down.png"}" id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-image"></button><button type="button"
	        id="btn-clear-{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
	        title="Clear"
	        onclick="SUGAR.clearRelateField(this.form, '{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input', '{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}');sync_{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}()"><img src="{sugar_getimagepath file="id-ff-clear.png"}"></button>
	</span>

	<script>
	SUGAR.AutoComplete.{$ac_key} = [];

	{{if empty($vardef.autocomplete_ajax)}}
		(function (){
			var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");
			
			if (typeof select_defaults =="undefined")
				select_defaults = [];
			
			select_defaults[selectElem.id] = { key:selectElem.value, text:''};

			//get default
			for (i=0;i<selectElem.options.length;i++){
				if (selectElem.options[i].value==selectElem.value)
					select_defaults[selectElem.id].text = selectElem.options[i].innerHTML;
			}

			//SUGAR.AutoComplete.{$ac_key}.ds = 
			//get options array from vardefs
			var options = SUGAR.AutoComplete.getOptionsArray("{{$vardef.autocomplete_options}}");

			YUI().use('datasource', 'datasource-jsonschema',function (Y) {
				SUGAR.AutoComplete.{$ac_key}.ds = new Y.DataSource.Function({
				    source: function (request) {
				    	var ret = [];
				    	for (i=0;i<selectElem.options.length;i++)
				    		if (!(selectElem.options[i].value=='' && selectElem.options[i].innerHTML==''))
				    			{literal}ret.push({ 'key': selectElem.options[i].value, 'text': selectElem.options[i].innerHTML });{/literal}
				    	return ret;
				    }
				});
			});
		})();
	{{else}}
		// Create a new YUI instance and populate it with the required modules.
		YUI().use('datasource', 'datasource-jsonschema',function (Y) {
			// DataSource is available and ready for use.
			SUGAR.AutoComplete.{$ac_key}.ds = new Y.DataSource.Get({
				source: 'index.php?module=Accounts&action=ajaxautocomplete&to_pdf=1'
			});
			SUGAR.AutoComplete.{$ac_key}.ds.plug(Y.Plugin.DataSourceJSONSchema, {
				schema: {
					resultListLocator: "option_items",
					resultFields: ["text", "key"],
					matchKey: "text",
				}
			});
		});
	{{/if}}
		YUI().use("autocomplete", "autocomplete-filters", "autocomplete-highlighters", "node","node-event-simulate", function (Y) {

	SUGAR.AutoComplete.{$ac_key}.inputNode = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input');
	SUGAR.AutoComplete.{$ac_key}.inputImage = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-image');
	SUGAR.AutoComplete.{$ac_key}.inputHidden = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}');
	
	{{if empty($vardef.autocomplete_ajax)}}
			function SyncToHidden(selectme){
				var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");
				var doSimulateChange = false;
				
				if (selectElem.value!=selectme)
					doSimulateChange=true;
				
				selectElem.value=selectme;

				for (i=0;i<selectElem.options.length;i++){
					selectElem.options[i].selected=false;
					if (selectElem.options[i].value==selectme)
						selectElem.options[i].selected=true;
				}

				if (doSimulateChange)
					SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('change');
			}

			//global variable 
			sync_{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}} = function(){
				SyncToHidden();
			}
			function syncFromHiddenToWidget(){

				var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");

				//if select no longer on page, kill timer
				if (selectElem==null || selectElem.options == null)
					return;

				var currentvalue = SUGAR.AutoComplete.{$ac_key}.inputNode.get('value');

				SUGAR.AutoComplete.{$ac_key}.inputNode.simulate('keyup');

				for (i=0;i<selectElem.options.length;i++){

					if (selectElem.options[i].value==selectElem.value && document.activeElement != document.getElementById('{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input'))
						SUGAR.AutoComplete.{$ac_key}.inputNode.set('value',selectElem.options[i].innerHTML);
				}
			}

            YAHOO.util.Event.onAvailable("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}", syncFromHiddenToWidget);

		SUGAR.AutoComplete.{$ac_key}.minQLen = 0;
		SUGAR.AutoComplete.{$ac_key}.queryDelay = 0;
		SUGAR.AutoComplete.{$ac_key}.numOptions = {$field_options|@count};
		if(SUGAR.AutoComplete.{$ac_key}.numOptions >= 300) {
			SUGAR.AutoComplete.{$ac_key}.minQLen = 1;
			SUGAR.AutoComplete.{$ac_key}.queryDelay = 200;
		}
		if(SUGAR.AutoComplete.{$ac_key}.numOptions >= 3000) {
			SUGAR.AutoComplete.{$ac_key}.minQLen = 1;
			SUGAR.AutoComplete.{$ac_key}.queryDelay = 500;
		}
	{{else}}
		function SyncToHidden(e){
			SUGAR.AutoComplete.{$ac_key}.inputHidden.set('value', e);
		}

		SUGAR.AutoComplete.{$ac_key}.minQLen = 1;
		SUGAR.AutoComplete.{$ac_key}.queryDelay = 500;
	{{/if}}
	
	SUGAR.AutoComplete.{$ac_key}.optionsVisible = false;
	
	SUGAR.AutoComplete.{$ac_key}.inputNode.plug(Y.Plugin.AutoComplete, {
		activateFirstItem: true,
		minQueryLength: SUGAR.AutoComplete.{$ac_key}.minQLen,
		queryDelay: SUGAR.AutoComplete.{$ac_key}.queryDelay,
		zIndex: 99999,

		{{if !empty($vardef.autocomplete_ajax)}}
				requestTemplate: '&options={{$vardef.autocomplete_options}}&q={literal}{query}{/literal}',
		{{/if}}
		
		source: SUGAR.AutoComplete.{$ac_key}.ds,
		
		resultTextLocator: 'text',
		resultHighlighter: 'phraseMatch',
		resultFilters: 'phraseMatch',
	});

	SUGAR.AutoComplete.{$ac_key}.expandHover = function(ex){
		var hover = YAHOO.util.Dom.getElementsByClassName('dccontent');
		if(hover[0] != null){
			if (ex) {
				var h = '1000px';
				hover[0].style.height = h;
			}
			else{
				hover[0].style.height = '';
			}
		}
	}
		
	if(SUGAR.AutoComplete.{$ac_key}.minQLen == 0){
		// expand the dropdown options upon focus
		SUGAR.AutoComplete.{$ac_key}.inputNode.on('focus', function () {
			SUGAR.AutoComplete.{$ac_key}.inputNode.ac.sendRequest('');
			SUGAR.AutoComplete.{$ac_key}.optionsVisible = true;
		});
	}

	{{if empty($vardef.autocomplete_ajax)}}
		SUGAR.AutoComplete.{$ac_key}.inputNode.on('click', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('click');
		});
		
		SUGAR.AutoComplete.{$ac_key}.inputNode.on('dblclick', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('dblclick');
		});

		SUGAR.AutoComplete.{$ac_key}.inputNode.on('focus', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('focus');
		});

		SUGAR.AutoComplete.{$ac_key}.inputNode.on('mouseup', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('mouseup');
		});

		SUGAR.AutoComplete.{$ac_key}.inputNode.on('mousedown', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('mousedown');
		});

		SUGAR.AutoComplete.{$ac_key}.inputNode.on('blur', function(e) {
			SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('blur');
			SUGAR.AutoComplete.{$ac_key}.optionsVisible = false;
			var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");
			//if typed value is a valid option, do nothing
			for (i=0;i<selectElem.options.length;i++)
				if (selectElem.options[i].innerHTML==SUGAR.AutoComplete.{$ac_key}.inputNode.get('value'))
					return;
			
			//typed value is invalid, so set the text and the hidden to blank
			SUGAR.AutoComplete.{$ac_key}.inputNode.set('value', select_defaults[selectElem.id].text);
			SyncToHidden(select_defaults[selectElem.id].key);
		});
	{{else}}		
		// when they focus away from the field...
		SUGAR.AutoComplete.{$ac_key}.inputNode.on('blur', function(e) {
			if (SUGAR.AutoComplete.{$ac_key}.inputNode.get('value') != '') { // value entered
				if (SUGAR.AutoComplete.{$ac_key}.inputHidden.get('value') == '') { // none selected, we clear their text and hide
					SUGAR.AutoComplete.{$ac_key}.inputNode.set('value', '');
				}
				else{ // they have something selected, we accept their selection and contract
				}
			}
			SUGAR.AutoComplete.{$ac_key}.optionsVisible = false;
		});
	{{/if}}

	// when they click on the arrow image, toggle the visibility of the options
	SUGAR.AutoComplete.{$ac_key}.inputImage.ancestor().on('click', function () {
		if (SUGAR.AutoComplete.{$ac_key}.optionsVisible) {
			SUGAR.AutoComplete.{$ac_key}.inputNode.blur();
		} else {
			SUGAR.AutoComplete.{$ac_key}.inputNode.focus();
		}
	});

	SUGAR.AutoComplete.{$ac_key}.inputNode.ac.on('query', function () {
		SUGAR.AutoComplete.{$ac_key}.inputHidden.set('value', '');
	});

	SUGAR.AutoComplete.{$ac_key}.inputNode.ac.on('visibleChange', function (e) {
		SUGAR.AutoComplete.{$ac_key}.expandHover(e.newVal); // expand
	});

	// when they select an option, set the hidden input with the KEY, to be saved
	SUGAR.AutoComplete.{$ac_key}.inputNode.ac.on('select', function(e) {
		SyncToHidden(e.result.raw.key);
	});
 
});
</script> 



{/if}

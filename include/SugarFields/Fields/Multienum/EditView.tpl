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

	<input type="hidden" id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}_multiselect"
	name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}_multiselect" value="true">
	{multienum_to_array string={{sugarvar key='value' string=true}} default={{sugarvar key='default' string=true}} assign="values"}
	<select id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
	name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}[]"
	multiple="true" size='{{$displayParams.size|default:6}}' style="width:150" title='{{$vardef.help|escape:"hexentity"}}' tabindex="{{$tabindex}}" {{$displayParams.field}}
    {{if !empty($displayParams.accesskey)}} accesskey='{{$displayParams.accesskey}}' {{/if}}
 	{{if isset($displayParams.javascript)}}{{$displayParams.javascript}}{{/if}}>
	{html_options options={{sugarvar key='options' string=true}} selected=$values}
	</select>

{else}

	{assign var="field_options" value={{sugarvar key='options' string="true"}} }
	{capture name="field_val"}{{sugarvar key='value'}}{/capture}
	{assign var="field_val" value=$smarty.capture.field_val}
	{capture name="ac_key"}{{sugarvar key='name'}}{/capture}
	{assign var="ac_key" value=$smarty.capture.ac_key}

	{{if empty($vardef.autocomplete_ajax)}}
		<input type="hidden" id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}_multiselect"
		name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}_multiselect" value="true">
		{multienum_to_array string={{sugarvar key='value' string=true}} default={{sugarvar key='default' string=true}} assign="values"}
		<select style='display:none' id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
		name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}[]"
		multiple="true" size='{{$displayParams.size|default:6}}' style="width:150" title='{{$vardef.help|escape:"hexentity"}}' tabindex="{{$tabindex}}" {{$displayParams.field}} 
		{{if !empty($displayParams.accesskey)}} accesskey='{{$displayParams.accesskey}}' {{/if}}
        {{if isset($displayParams.javascript)}}{{$displayParams.javascript}}{{/if}}>
		{html_options options={{sugarvar key='options' string=true}} selected=$values}
		</select>

		<input
	    id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
	    name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
	    size="60"
	    type="text" style="vertical-align: top;">
	{{else}}
		<input type="hidden"
		    id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
		    name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}"
		    value="{{sugarvar key='value'}}">

		<input
		    id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
		    name="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
		    size="60"
		    type="text" style="vertical-align: top;">
	{{/if}}

	<span class="id-ff multiple">
	    <button type="button">
	    	<img src="{sugar_getimagepath file="id-ff-down.png"}" id="{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-image">
	    	</button>
	    	<button type="button"
	        id="btn-clear-{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"
	        title="Clear"
	        onclick="SUGAR.clearRelateField(this.form, '{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input', '{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}};');SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden()"><img src="{sugar_getimagepath file="id-ff-clear.png"}"></button>
	</span>

	<script>
	SUGAR.AutoComplete.{$ac_key} = [];

	{{if empty($vardef.autocomplete_ajax)}}

		YUI().use('datasource', 'datasource-jsonschema', function (Y) {
					SUGAR.AutoComplete.{$ac_key}.ds = new Y.DataSource.Function({
					    source: function (request) {
					    	var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");
					    	var ret = [];
					    	for (i=0;i<selectElem.options.length;i++)
					    		if (!(selectElem.options[i].value=='' && selectElem.options[i].innerHTML==''))
					    			{literal}ret.push({'key':selectElem.options[i].value,'text':selectElem.options[i].innerHTML});{/literal}
					    	return ret;
					    }
					});
				});

	{{else}}

		// Create a new YUI instance and populate it with the required modules.
		YUI().use('datasource', 'datasource-jsonschema', function (Y) {
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


	YUI().use("autocomplete", "autocomplete-filters", "autocomplete-highlighters","node-event-simulate", function (Y) {

		
	    SUGAR.AutoComplete.{$ac_key}.inputNode = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input');
	    SUGAR.AutoComplete.{$ac_key}.inputImage = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-image');
	    SUGAR.AutoComplete.{$ac_key}.inputHidden = Y.one('#{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}');

		{{if empty($vardef.autocomplete_ajax)}}
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
			SUGAR.AutoComplete.{$ac_key}.minQLen = 1;
			SUGAR.AutoComplete.{$ac_key}.queryDelay = 500;
		{{/if}}
		
		{{if empty($vardef.autocomplete_ajax)}}

	    SUGAR.AutoComplete.{$ac_key}.inputNode.plug(Y.Plugin.AutoComplete, {
	        activateFirstItem: true,
	        allowTrailingDelimiter: true,

	        minQueryLength: SUGAR.AutoComplete.{$ac_key}.minQLen,
	        queryDelay: SUGAR.AutoComplete.{$ac_key}.queryDelay,
	        queryDelimiter: ',',
	        zIndex: 99999,

			{{if !empty($vardef.autocomplete_ajax)}}
				requestTemplate: '&options={{$vardef.autocomplete_options}}&q={literal}{query}{/literal}',
			{{/if}}

			source: SUGAR.AutoComplete.{$ac_key}.ds,
			
	        resultTextLocator: 'text',
	        resultHighlighter: 'phraseMatch',
	        resultFilters: 'phraseMatch',
	        // Chain together a startsWith filter followed by a custom result filter
	        // that only displays tags that haven't already been selected.
	        resultFilters: ['phraseMatch', function (query, results) {
		        // Split the current input value into an array based on comma delimiters.
	        	var selected = SUGAR.AutoComplete.{$ac_key}.inputNode.get('value').split(/\s*,\s*/);
	        
	            // Convert the array into a hash for faster lookups.
	            selected = Y.Array.hash(selected);

	            // Filter out any results that are already selected, then return the
	            // array of filtered results.
	            return Y.Array.filter(results, function (result) {
	               return !selected.hasOwnProperty(result.text);
	            });
	        }]
	    });
		{{else}}
			SUGAR.AutoComplete.{$ac_key}.inputNode.plug(Y.Plugin.AutoComplete, {
	        activateFirstItem: true,
	        allowTrailingDelimiter: true,

	        minQueryLength: SUGAR.AutoComplete.{$ac_key}.minQLen,
	        queryDelay: SUGAR.AutoComplete.{$ac_key}.queryDelay,
	        queryDelimiter: ',',
	        zIndex: 99999,
				requestTemplate: '&options={{$vardef.autocomplete_options}}&q={literal}{query}{/literal}',

			source: SUGAR.AutoComplete.{$ac_key}.ds,
			
	        resultTextLocator: 'text',
	        resultHighlighter: 'phraseMatch',
	        resultFilters: 'phraseMatch',
	        // Chain together a startsWith filter followed by a custom result filter
	        // that only displays tags that haven't already been selected.
	        resultFilters: ['phraseMatch', function (query, results) {
	            // Split the current input value into an array based on comma delimiters.
	            var selected = SUGAR.AutoComplete.{$ac_key}.inputNode.ac.get('value').split(/\s*,\s*/);

	            // Pop the last item off the array, since it represents the current query
	            // and we don't want to filter it out.
	            //selected.pop();

	            // Convert the array into a hash for faster lookups.
	            selected = Y.Array.hash(selected);

	            // Filter out any results that are already selected, then return the
	            // array of filtered results.
	            return Y.Array.filter(results, function (result) {
	               return !selected.hasOwnProperty(result.text);
	            });
	        }]
	    });
		{{/if}}
		if(SUGAR.AutoComplete.{$ac_key}.minQLen == 0){
		    // expand the dropdown options upon focus
		    SUGAR.AutoComplete.{$ac_key}.inputNode.on('focus', function () {
		        SUGAR.AutoComplete.{$ac_key}.inputNode.ac.sendRequest('');
		    });
		}

		{{if empty($vardef.autocomplete_ajax)}}
		    SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden = function() {
				var index_array = SUGAR.AutoComplete.{$ac_key}.inputNode.get('value').split(/\s*,\s*/);

				var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");

				var oTable = {};
		    	for (i=0;i<selectElem.options.length;i++){
		    		if (selectElem.options[i].selected)
		    			oTable[selectElem.options[i].value] = true;
		    	}

				for (i=0;i<selectElem.options.length;i++){
					selectElem.options[i].selected=false;
				}

				var nTable = {};

				for (i=0;i<index_array.length;i++){
					var key = index_array[i];
					for (c=0;c<selectElem.options.length;c++)
						if (selectElem.options[c].innerHTML == key){
							selectElem.options[c].selected=true;
							nTable[selectElem.options[c].value]=1;
						}
				}

				//the following two loops check to see if the selected options are different from before.
				//oTable holds the original select. nTable holds the new one
				var fireEvent=false;
				for (n in nTable){
					if (n=='')
						continue;
		    		if (!oTable.hasOwnProperty(n))
		    			fireEvent = true; //the options are different, fire the event
		    	}
		    	
		    	for (o in oTable){
		    		if (o=='')
		    			continue;
		    		if (!nTable.hasOwnProperty(o))
		    			fireEvent=true; //the options are different, fire the event
		    	}

		    	//if the selected options are different from before, fire the 'change' event
		    	if (fireEvent){
		    		SUGAR.AutoComplete.{$ac_key}.inputHidden.simulate('change');
		    	}
		    };

		    SUGAR.AutoComplete.{$ac_key}.inputNode.updateText = function() {
		    	//get last option typed into the input widget
		    	SUGAR.AutoComplete.{$ac_key}.inputNode.ac.set(SUGAR.AutoComplete.{$ac_key}.inputNode.get('value'));
				var index_array = SUGAR.AutoComplete.{$ac_key}.inputNode.get('value').split(/\s*,\s*/);
				//create a string ret_string as a comma-delimited list of text from selectElem options.
				var selectElem = document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}");
				var ret_array = [];

                if (selectElem==null || selectElem.options == null)
					return;

				for (i=0;i<selectElem.options.length;i++){
					if (selectElem.options[i].selected && selectElem.options[i].innerHTML!=''){
						ret_array.push(selectElem.options[i].innerHTML);
					}
				}

				//index array - array from input
				//ret array - array from select

				var sorted_array = [];
				var notsorted_array = [];
				for (i=0;i<index_array.length;i++){
					for (c=0;c<ret_array.length;c++){
						if (ret_array[c]==index_array[i]){
							sorted_array.push(ret_array[c]);
							ret_array.splice(c,1);
						}
					}
				}
				ret_string = ret_array.concat(sorted_array).join(', ');
				if (ret_string.match(/^\s*$/))
					ret_string='';
				else
					ret_string+=', ';
				
				//update the input widget
				SUGAR.AutoComplete.{$ac_key}.inputNode.ac.set('value', ret_string);
		    };

		    function updateTextOnInterval(){
		    	if (document.activeElement != document.getElementById("{{if empty($displayParams.idName)}}{{sugarvar key='name'}}{{else}}{{$displayParams.idName}}{{/if}}-input"))
		    		SUGAR.AutoComplete.{$ac_key}.inputNode.updateText();
		    	setTimeout(updateTextOnInterval,100);
		    }

		    updateTextOnInterval();
		{{else}}
		    SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden = function() {
				var index_array = SUGAR.MultiEnumAutoComplete.getMultiSelectKeysFromValues("{{$vardef.autocomplete_options}}", SUGAR.AutoComplete.{$ac_key}.inputNode.get('value'));
				SUGAR.AutoComplete.{$ac_key}.inputHidden.set('value', index_array.join("^,^"));
		    };

		    SUGAR.AutoComplete.{$ac_key}.inputNode.updateText = function() {
				var index_array = SUGAR.MultiEnumAutoComplete.getMultiSelectValuesFromKeys("{{$vardef.autocomplete_options}}", SUGAR.AutoComplete.{$ac_key}.inputHidden.get('value'));
				if(index_array.length < 1){
					SUGAR.AutoComplete.{$ac_key}.inputNode.set('value', '');
				}
				else{
					SUGAR.AutoComplete.{$ac_key}.inputNode.set('value', index_array.join(", ") + ", ");
				}
		    };	
		    SUGAR.AutoComplete.{$ac_key}.inputNode.updateText();
		{{/if}}

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
			});
		{{/if}}

		SUGAR.AutoComplete.{$ac_key}.inputNode.on('blur', function () {
			SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden();
			SUGAR.AutoComplete.{$ac_key}.inputNode.updateText();
		});
	
	    // when they click on the arrow image, toggle the visibility of the options
	    SUGAR.AutoComplete.{$ac_key}.inputImage.on('click', function () {
			if(SUGAR.AutoComplete.{$ac_key}.minQLen == 0){
				SUGAR.AutoComplete.{$ac_key}.inputNode.ac.sendRequest('');
				SUGAR.AutoComplete.{$ac_key}.inputNode.ac.show();
			}
			else{
				SUGAR.AutoComplete.{$ac_key}.inputNode.focus();
			}
	    });
	
		if(SUGAR.AutoComplete.{$ac_key}.minQLen == 0){
		    // After a tag is selected, send an empty query to update the list of tags.
		    SUGAR.AutoComplete.{$ac_key}.inputNode.ac.after('select', function () {
		      SUGAR.AutoComplete.{$ac_key}.inputNode.ac.sendRequest('');
		      SUGAR.AutoComplete.{$ac_key}.inputNode.ac.show();
			  SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden();
			  SUGAR.AutoComplete.{$ac_key}.inputNode.updateText();
		    });
		} else {
		    // After a tag is selected, send an empty query to update the list of tags.
		    SUGAR.AutoComplete.{$ac_key}.inputNode.ac.after('select', function () {
			  SUGAR.AutoComplete.{$ac_key}.inputNode.updateHidden();
			  SUGAR.AutoComplete.{$ac_key}.inputNode.updateText();
		    });
		}
	});
	</script>

{/if}

<?php

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

/*********************************************************************************
 * Description: view handler for step 1 of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
class ImportViewExtStep1 extends ImportView
{
    protected $pageTitleKey = 'LBL_CONFIRM_EXT_TITLE';
    protected $currentFormID = 'extstep1';
    protected $previousAction = 'Step1';
    protected $nextAction = 'extdupcheck';

    /**
     * @see SugarView::display()
     */
    public function display()
    {

        $source = $this->request->getValidInputRequest('external_source', null, '');
        $importModule = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName', '');
        global $mod_strings, $app_strings, $current_user, $sugar_config;

        // Clear out this user's last import
        $seedUsersLastImport = BeanFactory::newBean('Import_2');
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();

        $mappingFile = $this->getMappingFile($source);
        if ($mappingFile == null) {
            $this->_showImportError($mod_strings['ERR_MISSING_MAP_NAME'], $importModule, 'Step1');
            return;
        }
        $extSourceToSugarFieldMapping = $mappingFile->getMapping($importModule);

        // get list of required fields
        $required = [];
        foreach (array_keys($this->bean->get_import_required_fields()) as $name) {
            $properties = $this->bean->getFieldDefinition($name);
            if (!empty($properties['vname'])) {
                $required[$name] = str_replace(':', '', translate($properties['vname'], $this->bean->module_dir));
            } else {
                $required[$name] = str_replace(':', '', translate($properties['name'], $this->bean->module_dir));
            }
        }

        $mappedRows = $this->getMappingRows($importModule, $extSourceToSugarFieldMapping);
        $this->ss->assign('MODULE_TITLE', $this->getModuleTitle(false));
        $this->ss->assign('rows', $mappedRows);
        $this->ss->assign('COLUMNCOUNT', safeCount($mappedRows));
        $this->ss->assign('IMPORT_MODULE', $importModule);
        $this->ss->assign('JAVASCRIPT', $this->getJS($required));
        $this->ss->assign('CSS', $this->getCSS());
        $this->ss->assign('CURRENT_STEP', $this->currentStep);

        $this->ss->assign('RECORDTHRESHOLD', $sugar_config['import_max_records_per_file']);
        $this->ss->assign('ENABLED_DUP_FIELDS', json_encode($this->getFieldsForDuplicateCheck()));
        $content = $this->ss->fetch('modules/Import/tpls/extstep1.tpl');
        $this->ss->assign('CONTENT', $content);
        $out = $this->ss->fetch('modules/Import/tpls/wizardWrapper.tpl');
        echo $out;
    }

    private function getFieldsForDuplicateCheck()
    {
        return ['email1', ['first_name', 'last_name']];
    }


    private function getMappingRows($module, $extSourceToSugarFieldMapping)
    {
        global $app_strings, $current_language;
        $columns = [];
        $mappedFields = [];
        $mod_strings = return_module_language($current_language, $module);
        $import_mod_strings = return_module_language($current_language, 'Import');
        $ignored_fields = [];

        foreach ($extSourceToSugarFieldMapping as $externalKey => $sugarMapping) {
            // See if we have any field map matches
            $defaultValue = $externalKey;

            // build string of options
            $fields = $this->bean->get_importable_fields();
            $options = [];
            $defaultField = '';
            foreach ($fields as $fieldname => $properties) {
                // get field name
                if (!empty($properties['vname'])) {
                    $displayname = str_replace(':', '', translate($properties['vname'], $this->bean->module_dir));
                } else {
                    $displayname = str_replace(':', '', translate($properties['name'], $this->bean->module_dir));
                }
                // see if this is required
                $req_mark = '';
                $req_class = '';
                if (array_key_exists($fieldname, $this->bean->get_import_required_fields())) {
                    $req_mark = ' ' . $app_strings['LBL_REQUIRED_SYMBOL'];
                    $req_class = ' class="required" ';
                }
                // see if we have a match
                $selected = '';
                if (!empty($defaultValue) && !in_array($fieldname, $mappedFields) && !in_array($fieldname, $ignored_fields)) {
                    if (strtolower($fieldname) == strtolower($sugarMapping['sugar_key'])) {
                        $selected = ' selected="selected" ';
                        $defaultField = $fieldname;
                        $mappedFields[] = $fieldname;
                    }
                }
                // get field type information
                $fieldtype = '';
                if (isset($properties['type'])
                    && isset($mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])])) {
                    $fieldtype = ' [' . $mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])] . '] ';
                }

                $comment = $properties['comments'] ?? $properties['comment'] ?? '';
                if (!empty($comment)) {
                    $fieldtype .= ' - ' . $comment;
                }

                $options[$displayname . $fieldname] = '<option value="' . $fieldname . '" title="' . $displayname . htmlentities($fieldtype, ENT_COMPAT) . '"'
                    . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }

            // get default field value
            $defaultFieldHTML = '';
            if (!empty($defaultField)) {
                $defaultFieldHTML = getControl(
                    $module,
                    $defaultField,
                    $fields[$defaultField],
                    ($default_values[$defaultField] ?? '')
                );
            }

            if (isset($default_values[$defaultField])) {
                unset($default_values[$defaultField]);
            }

            // Bug 27046 - Sort the column name picker alphabetically
            ksort($options);

            $help_text = isset($sugarMapping['sugar_help_key']) ? $import_mod_strings[$sugarMapping['sugar_help_key']] : '';
            $rowLabel = $mod_strings[$sugarMapping['sugar_label']] ?? $sugarMapping['default_label'];
            $columns[] = [
                'field_choices' => implode('', $options),
                'default_field' => $defaultFieldHTML,
                'cell1' => str_replace(':', '', $rowLabel),
                'show_remove' => false,
                'ext_key' => $externalKey,
                'help_text' => $help_text,
            ];
        }

        return $columns;
    }

    private function getMappingFile($source)
    {
        $classname = 'ImportMap' . ucfirst(strtolower($source));
        if (!SugarAutoLoader::requireWithCustom("modules/Import/maps/{$classname}.php")) {
            SugarAutoLoader::requireWithCustom('modules/Import/maps/ImportMapOther.php');
            $classname = 'ImportMapOther';
            $importSource = 'other';
        }

        if (class_exists($classname)) {
            $mapping_file = new $classname();
            return $mapping_file;
        } else {
            return null;
        }
    }

    private function getImportableExternalEAPMs()
    {

        return ExternalAPIFactory::getModuleDropDown('Import', false, false);
    }

    protected function getCSS()
    {
        return <<<EOCSS
            <style>
                textarea { width: 20em }
				.detail tr td[scope="row"] {
					text-align:left
				}
                span.collapse{
                    background: transparent url('index.php?entryPoint=getImage&themeName=Sugar&themeName=Sugar&imageName=sugar-yui-sprites.png') no-repeat 0 -90px;
                    padding-left: 10px;
                    cursor: pointer;
                }

                span.expand{
                    background: transparent url('index.php?entryPoint=getImage&themeName=Sugar&themeName=Sugar&imageName=sugar-yui-sprites.png') no-repeat -0 -110px;
                    padding-left: 10px;
                     cursor: pointer;
                }
                .removeButton{
                    border: none !important;
                    background-image: none !important;
                    background-color: transparent;
                    padding: 0px;
                }

                #importNotes ul{
                	margin: 0px;
                	margin-top: 10px;
                	padding-left: 20px;
                }

            </style>
EOCSS;
    }

    /**
     * Returns JS used in this view
     *
     * @param array $required fields that are required for the import
     * @return string HTML output with JS code
     */
    protected function getJS($required)
    {
        global $mod_strings;

        $print_required_array = '';
        foreach ($required as $name => $display) {
            $print_required_array .= "required['$name'] = '" . sanitize($display) . "';\n";
        }
        $sqsWaitImage = SugarThemeRegistry::current()->getImageURL('sqsWait.gif');
        $removeButtonImage = 'index.php?entryPoint=getImage&themeName=Sugar&imageName=id-ff-remove.png&v=' . getVersionedPath('');
        return <<<EOJAVASCRIPT

    document.getElementById('goback').onclick = function()
    {
        document.getElementById('{$this->currentFormID}').action.value = '{$this->previousAction}';
        //bug #48960: CSS didn't load when use click back in the step2 (external sources are selected for contacts)
        //need to unset 'to_pdf' in extstep1.tpl
        if (document.getElementById('{$this->currentFormID}').to_pdf)
        {
            document.getElementById('{$this->currentFormID}').to_pdf.value = '';
        }
        return true;
    }

ImportView = {

    validateMappings : function()
    {
        // validate form
        clear_all_errors();
        var form = document.getElementById('{$this->currentFormID}');
        var hash = new Object();
        var required = new Object();
        $print_required_array
        var isError = false;
        for ( i = 0; i < form.length; i++ ) {
            if ( form.elements[i].name.indexOf("colnum",0) == 0) {
                if ( form.elements[i].value == "-1") {
                    continue;
                }
                if ( hash[ form.elements[i].value ] == 1) {
                    isError = true;
                    add_error_style('{$this->currentFormID}',form.elements[i].name,"{$mod_strings['ERR_MULTIPLE']}");
                }
                hash[form.elements[i].value] = 1;
            }
        }

        // check for required fields
        for(var field_name in required) {
            // contacts hack to bypass errors if full_name is set
            if (field_name == 'last_name' &&
                    hash['full_name'] == 1) {
                continue;
            }
            if ( hash[ field_name ] != 1 ) {
                isError = true;
                add_error_style('{$this->currentFormID}',form.colnum_0.name,
                    "{$mod_strings['ERR_MISSING_REQUIRED_FIELDS']} " + required[field_name]);
            }
        }

        // return false if we got errors
        if (isError == true) {
            return false;
        }


        return true;

    }

}

if( document.getElementById('gonext') )
{
    document.getElementById('gonext').onclick = function(){

        if( ImportView.validateMappings() )
        {
            // Move on to next step
            document.getElementById('{$this->currentFormID}').action.value = '{$this->nextAction}';
            return true;
        }
        else
            return false;
    }
}

// handle adding new row
document.getElementById('addrow').onclick = function(){

    toggleDefaultColumnVisibility(false);
    rownum = document.getElementById('{$this->currentFormID}').columncount.value;
    newrow = document.createElement("tr");

    column0 = document.getElementById('row_0_col_0').cloneNode(true);
    column0.id = 'row_' + rownum + '_col_0';
    for ( i = 0; i < column0.childNodes.length; i++ ) {
        if ( column0.childNodes[i].name == 'colnum_0' ) {
            column0.childNodes[i].name = 'colnum_' + rownum;
            column0.childNodes[i].onchange = function(){
                var module    = document.getElementById('{$this->currentFormID}').import_module.value;
                var fieldname = this.value;
                var matches   = /colnum_([0-9]+)/i.exec(this.name);
                var fieldnum  = matches[1];
                if ( fieldname == -1 ) {
                    document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = '';
                    return;
                }
                document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = '<img src="{$sqsWaitImage}" />'
                YAHOO.util.Connect.asyncRequest('GET', 'index.php?module=Import&action=GetControl&import_module='+module+'&field_name='+fieldname,
                    {
                        success: function(o)
                        {
                        	document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = o.responseText;
                            SUGAR.util.evalScript(o.responseText);
                            enableQS(true);
                        },
                        failure: function(o) {/*failure handler code*/}
                    });
            }
        }
    }

    var removeButton = document.createElement("button");
    removeButton.title = "{$mod_strings['LBL_REMOVE_ROW']}";
    removeButton.id = 'deleterow_' + rownum;
    removeButton.className = "removeButton";
    var imgButton = document.createElement("img");
    imgButton.src = "{$removeButtonImage}";
    removeButton.appendChild(imgButton);


    if ( document.getElementById('row_0_header') ) {
        column1 = document.getElementById('row_0_header').cloneNode(true);
        column1.innerHTML = '&nbsp;';
        column1.style.textAlign = "right";
        newrow.appendChild(column1);
        column1.appendChild(removeButton);
    }

    newrow.appendChild(column0);



    column3 = document.createElement('td');
    column3.className = 'tabDetailViewDL';
    if ( !document.getElementById('row_0_header') ) {
        column3.colSpan = 2;
    }

    newrow.appendChild(column3);

    column2 = document.getElementById('defaultvaluepicker_0').cloneNode(true);
    column2.id = 'defaultvaluepicker_' + rownum;
    newrow.appendChild(column2);

    document.getElementById('{$this->currentFormID}').columncount.value = parseInt(document.getElementById('{$this->currentFormID}').columncount.value) + 1;

    document.getElementById('row_0_col_0').parentNode.parentNode.insertBefore(newrow,this.parentNode.parentNode);

    document.getElementById('deleterow_' + rownum).onclick = function(){
        this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
    }


}

function toggleDefaultColumnVisibility(hide)
{
    if( typeof(hide) != 'undefined' && typeof(hide) == 'boolean')
    {
        var currentStyle = hide ? '' : 'none';
    }
    else
    {
        var currentStyle = YAHOO.util.Dom.getStyle('default_column_header_span', 'display');
    }
    if(currentStyle == 'none')
    {
        var newStyle = '';
        var bgColor = '#eeeeee';
        YAHOO.util.Dom.addClass('hide_default_link', 'collapse');
        YAHOO.util.Dom.removeClass('hide_default_link', 'expand');
        var col2Rowspan = "1";
    }
    else
    {
        var newStyle = 'none';
        var bgColor = '#dddddd';
        YAHOO.util.Dom.addClass('hide_default_link', 'expand');
        YAHOO.util.Dom.removeClass('hide_default_link', 'collapse');
        var col2Rowspan = "2";
    }

    YAHOO.util.Dom.setStyle('default_column_header_span', 'display', newStyle);
    YAHOO.util.Dom.setStyle('default_column_header', 'backgroundColor', bgColor);

    //Toggle all rows.
    var columnCount = document.getElementById('{$this->currentFormID}').columncount.value;
    for(i=0;i<columnCount;i++)
    {
        YAHOO.util.Dom.setStyle('defaultvaluepicker_' + i, 'display', newStyle);
        YAHOO.util.Dom.setAttribute('row_'+i+'_col_2', 'colspan', col2Rowspan);
    }
}

var notesEl = document.getElementById('toggleNotes');
if(notesEl)
{
    notesEl.onclick = function() {
        if (document.getElementById('importNotes').style.display == 'none'){
            document.getElementById('importNotes').style.display = '';
            document.getElementById('toggleNotes').value='{$mod_strings['LBL_HIDE_NOTES']}';
        }
        else {
            document.getElementById('importNotes').style.display = 'none';
            document.getElementById('toggleNotes').value='{$mod_strings['LBL_SHOW_NOTES']}';
        }
    }
}


YAHOO.util.Event.onDOMReady(function(){
    toggleDefaultColumnVisibility();
    YAHOO.util.Event.addListener('hide_default_link', "click", toggleDefaultColumnVisibility);

    var selects = document.getElementsByTagName('select');
    for (var i = 0; i < selects.length; ++i ){
        if (selects[i].name.indexOf("colnum_") != -1 ) {
            // fetch the field input control via ajax
            selects[i].onchange = function(){
                var module    = document.getElementById('{$this->currentFormID}').import_module.value;
                var fieldname = this.value;
                var matches   = /colnum_([0-9]+)/i.exec(this.name);
                var fieldnum  = matches[1];
                if ( fieldname == -1 ) {
                    document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = '';
                    return;
                }

                document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = '<img src="{$sqsWaitImage}" />'
                YAHOO.util.Connect.asyncRequest('GET', 'index.php?module=Import&action=GetControl&import_module='+module+'&field_name='+fieldname,
                    {
                        success: function(o)
                        {
                            document.getElementById('defaultvaluepicker_'+fieldnum).innerHTML = o.responseText;
                            SUGAR.util.evalScript(o.responseText);
                            enableQS(true);
                        },
                        failure: function(o) {/*failure handler code*/}
                    });
            }
        }
    }
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; ++i ){
        if (inputs[i].id.indexOf("deleterow_") != -1 ) {
            inputs[i].onclick = function(){
                this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
            }
        }
    }
});

enableQS(false);


EOJAVASCRIPT;
    }
}

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

use Sugarcrm\Sugarcrm\Entitlements\Subscription;

/**
 * Description: view handler for step 3 of the import process
 */
class ImportViewStep3 extends ImportView
{
    protected $pageTitleKey = 'LBL_STEP_3_TITLE';
    protected $currentFormID = 'importstep3';
    protected $previousAction = 'Confirm';
    protected $nextAction = 'dupcheck';


    /**
     * @see SugarView::display()
     */
    public function display()
    {
        $fields = [];
        global $mod_strings, $app_strings, $current_user, $sugar_config, $app_list_strings, $locale, $current_language;

        $import_module = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName', false);
        $this->ss->assign('IMPORT_MODULE', $import_module);
        $has_header = (isset($_REQUEST['has_header']) ? 1 : 0);
        $sugar_config['import_max_records_per_file'] = (empty($sugar_config['import_max_records_per_file']) ? 1000 : $sugar_config['import_max_records_per_file']);
        $this->ss->assign('CURRENT_STEP', $this->currentStep);
        $this->ss->assign('idm_update_mode_only', $this->isLimitedForModuleInIdmMode($import_module));
        // attempt to lookup a preexisting field map
        // use the custom one if specfied to do so in step 1
        $mapping_file = BeanFactory::newBean('Import_1');
        $field_map = $mapping_file->set_get_import_wizard_fields();
        $default_values = [];
        $ignored_fields = [];

        if (!empty($_REQUEST['source_id'])) {
            $GLOBALS['log']->info('Loading import map properties.');
            $mapping_file = BeanFactory::getBean('Import_1', $_REQUEST['source_id'], ['encode' => false]);
            $_REQUEST['source'] = $mapping_file->source;
            $has_header = $mapping_file->has_header;
            if (isset($mapping_file->delimiter)) {
                $_REQUEST['custom_delimiter'] = $mapping_file->delimiter;
            }
            if (isset($mapping_file->enclosure)) {
                $_REQUEST['custom_enclosure'] = htmlentities($mapping_file->enclosure, ENT_COMPAT);
            }
            $field_map = $mapping_file->getMapping();
            //print_r($field_map);die();
            $default_values = $mapping_file->getDefaultValues();
            $this->ss->assign('MAPNAME', $mapping_file->name);
            $this->ss->assign('CHECKMAP', 'checked="checked" value="on"');
        } else {
            $classname = $this->getMappingClassName(ucfirst($_REQUEST['source']));

            //Set the $_REQUEST['source'] to be 'other' for ImportMapOther special case
            if ($classname == 'ImportMapOther') {
                $_REQUEST['source'] = 'other';
            }

            if (class_exists($classname)) {
                $mapping_file = new $classname();
                $ignored_fields = $mapping_file->getIgnoredFields($import_module);
                $field_map2 = $mapping_file->getMapping($import_module);
                $field_map = array_merge($field_map, $field_map2);
            }
        }

        $delimiter = $this->getDelimiterValue();
        $customEnclosure = $this->request->getValidInputRequest('custom_enclosure', null, '');

        $delimiters = $this->getDelimitersFromRequest();
        $this->ss->assign('CUSTOM_DELIMITER', $delimiters['custom']);
        $this->ss->assign('CUSTOM_DELIMITER_OTHER', $delimiters['other']);
        $this->ss->assign('CUSTOM_ENCLOSURE', htmlentities($customEnclosure, ENT_COMPAT));

        //populate import locale  values from import mapping if available, these values will be used througout the rest of the code path

        $uploadFileName = $this->request->getValidInputRequest('file_name', null, '');

        // Now parse the file and look for errors
        $importFile = new ImportFile($uploadFileName, $delimiter, html_entity_decode($customEnclosure, ENT_QUOTES), false);

        if (!$importFile->fileExists()) {
            $this->_showImportError($mod_strings['LBL_CANNOT_OPEN'], $import_module, 'Step2');
            return;
        }

        // retrieve first 3 rows
        $rows = [];

        //Keep track of the largest row count found.
        $maxFieldCount = 0;
        for ($i = 0; $i < 3; $i++) {
            $rows[] = $importFile->getNextRow();
            $maxFieldCount = $importFile->getFieldCount() > $maxFieldCount ? $importFile->getFieldCount() : $maxFieldCount;
        }
        $ret_field_count = $maxFieldCount;

        // Bug 14689 - Parse the first data row to make sure it has non-empty data in it
        $isempty = true;
        if ($rows[(int)$has_header] != false) {
            foreach ($rows[(int)$has_header] as $value) {
                if (strlen(trim($value)) > 0) {
                    $isempty = false;
                    break;
                }
            }
        }

        if ($isempty || $rows[(int)$has_header] == false) {
            $this->_showImportError($mod_strings['LBL_NO_LINES'], $import_module, 'Step2');
            return;
        }

        // save first row to send to step 4
        $this->ss->assign('FIRSTROW', base64_encode(serialize($rows[0])));

        // Now build template
        $this->ss->assign('TMP_FILE', $uploadFileName);
        $this->ss->assign('SOURCE', $_REQUEST['source']);
        $this->ss->assign('TYPE', $this->request->getValidInputRequest('type', ['Assert\Choice' => ['choices' => ['import', 'update', '']]], ''));
        $this->ss->assign('DELETE_INLINE_PNG', SugarThemeRegistry::current()->getImage('basic_search', 'align="absmiddle" alt="' . $app_strings['LNK_DELETE'] . '" border="0"'));
        $this->ss->assign('PUBLISH_INLINE_PNG', SugarThemeRegistry::current()->getImage('advanced_search', 'align="absmiddle" alt="' . $mod_strings['LBL_PUBLISH'] . '" border="0"'));

        if ($this->isLimitedForModuleInIdmMode($import_module)) {
            $productCodes = $current_user->getProductCodes();

            $instruction = $this->instruction = string_format(
                $mod_strings['LBL_IDM_SELECT_MAPPING_INSTRUCTION'] . '<br/><br/>' .
                $mod_strings['LBL_IDM_SELECT_MAPPING_FIELDS_INSTRUCTION'],
                [
                    get_help_url(
                        $GLOBALS['sugar_flavor'],
                        $GLOBALS['sugar_version'],
                        $current_language,
                        'UserFields',
                        '',
                        '',
                        '',
                        '',
                        implode(',', $productCodes)
                    ),
                ]
            );
        } else {
            $this->instruction = 'LBL_SELECT_MAPPING_INSTRUCTION';
            $instruction = $this->getInstruction();
        }

        $this->ss->assign('INSTRUCTION', $instruction);

        $this->ss->assign('MODULE_TITLE', $this->getModuleTitle(false));
        $this->ss->assign(
            'STEP4_TITLE',
            strip_tags(str_replace("\n", '', getClassicModuleTitle(
                $mod_strings['LBL_MODULE_NAME'],
                [$mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_STEP_4_TITLE']],
                false
            )))
        );
        $this->ss->assign('HEADER', $app_strings['LBL_IMPORT'] . ' ' . $mod_strings['LBL_MODULE_NAME']);

        // we export it as email_address, but import as email1
        $field_map['email_address'] = 'email1';

        // build each row; row count is determined by the the number of fields in the import file
        $columns = [];
        $mappedFields = [];

        // this should be populated if the request comes from a 'Back' button click
        $importColumns = $this->getImportColumns();
        $column_sel_from_req = false;
        if (!empty($importColumns)) {
            $column_sel_from_req = true;
        }

        $import_module = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName');

        for ($field_count = 0; $field_count < $ret_field_count; $field_count++) {
            // See if we have any field map matches
            $defaultValue = '';
            // Bug 31260 - If the data rows have more columns than the header row, then just add a new header column
            if (!isset($rows[0][$field_count])) {
                $rows[0][$field_count] = '';
            }
            // See if we can match the import row to a field in the list of fields to import
            $firstrow_name = trim(str_replace(':', '', $rows[0][$field_count]));
            if ($has_header && isset($field_map[$firstrow_name])) {
                $defaultValue = $field_map[$firstrow_name];
            } elseif (isset($field_map[$field_count])) {
                $defaultValue = $field_map[$field_count];
            } elseif (empty($_REQUEST['source_id'])) {
                $defaultValue = trim($rows[0][$field_count]);
            }

            // build string of options
            $fields = $this->bean->get_importable_fields();
            $options = [];
            $defaultField = '';
            global $current_language;
            $moduleStrings = return_module_language($current_language, $this->bean->module_dir);

            foreach ($fields as $fieldname => $properties) {
                // get field name
                if (!empty($moduleStrings['LBL_EXPORT_' . strtoupper($fieldname)])) {
                    $displayname = str_replace(':', '', $moduleStrings['LBL_EXPORT_' . strtoupper($fieldname)]);
                } elseif (!empty($properties['vname'])) {
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
                if ($column_sel_from_req && isset($importColumns[$field_count])) {
                    if ($fieldname == $importColumns[$field_count]) {
                        $selected = ' selected="selected" ';
                        $defaultField = $fieldname;
                        $mappedFields[] = $fieldname;
                    }
                } else {
                    if (!empty($defaultValue) && !in_array($fieldname, $mappedFields)
                        && !in_array($fieldname, $ignored_fields)) {
                        if (strtolower($fieldname) == strtolower($defaultValue)
                            || strtolower($fieldname) == str_replace(' ', '_', strtolower($defaultValue))
                            || strtolower($displayname) == strtolower($defaultValue)
                            || strtolower($displayname) == str_replace(' ', '_', strtolower($defaultValue))) {
                            $selected = ' selected="selected" ';
                            $defaultField = $fieldname;
                            $mappedFields[] = $fieldname;
                        }
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

                if ($this->isLimitedForFieldInIdmMode($import_module, $properties)) {
                    continue;
                }

                $options[$displayname . $fieldname] = '<option value="' . $fieldname . '" title="' . $displayname
                    . htmlentities($fieldtype, ENT_COMPAT, 'UTF-8') . '"'
                    . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }

            // get default field value
            $defaultFieldHTML = '';
            if (!empty($defaultField)) {
                $defaultFieldHTML = getControl(
                    $import_module,
                    $defaultField,
                    $fields[$defaultField],
                    ($default_values[$defaultField] ?? ''),
                    ['idName' => "default_value_$defaultField"]
                );
            }

            if (isset($default_values[$defaultField])) {
                unset($default_values[$defaultField]);
            }

            // Bug 27046 - Sort the column name picker alphabetically
            ksort($options);

            $cellOneData = $rows[0][$field_count] ?? '';
            $cellTwoData = $rows[1][$field_count] ?? '';
            $cellThreeData = $rows[2][$field_count] ?? '';
            $columns[] = [
                'field_choices' => implode('', $options),
                'default_field' => $defaultFieldHTML,
                'cell1' => strip_tags($cellOneData),
                'cell2' => strip_tags($cellTwoData),
                'cell3' => strip_tags($cellThreeData),
                'show_remove' => false,
                'defaultFieldName' => $defaultField,
                'hidden' => false,
            ];
        }

        // add in extra defaulted fields if they are in the mapping record
        if (safeCount($default_values) > 0) {
            foreach ($default_values as $field_name => $default_value) {
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
                    if (strtolower($fieldname) == strtolower($field_name)
                        && !in_array($fieldname, $mappedFields)
                        && !in_array($fieldname, $ignored_fields)) {
                        $selected = ' selected="selected" ';
                        $defaultField = $fieldname;
                        $mappedFields[] = $fieldname;
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

                    if ($this->isLimitedForFieldInIdmMode($import_module, $properties)) {
                        continue;
                    }
                    $options[$displayname . $fieldname] =
                        '<option value="' . $fieldname . '" title="' . $displayname
                        . htmlentities($fieldtype, ENT_COMPAT, 'UTF-8') . '"'
                        . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
                }

                // get default field value
                $defaultFieldHTML = '';
                if (!empty($defaultField)) {
                    $defaultFieldHTML = getControl(
                        $import_module,
                        $defaultField,
                        $fields[$defaultField],
                        $default_value
                    );
                }

                // Bug 27046 - Sort the column name picker alphabetically
                ksort($options);

                $columns[] = [
                    'field_choices' => implode('', $options),
                    'default_field' => $defaultFieldHTML,
                    'show_remove' => true,
                    'defaultFieldName' => $defaultField,
                    'hidden' => false,
                ];

                $ret_field_count++;
            }
        }

        $idpConfig = $this->getIdpConfig();
        if ($this->isLimitedForModuleInIdmMode($import_module)) {
            foreach ($columns as $key => $value) {
                $fields = $this->bean->get_importable_fields();
                foreach ($fields as $field => $properties) {
                    if (empty($properties['idm_mode_disabled']) ||
                        ($properties['name'] === 'license_type' && !$idpConfig->getUserLicenseTypeIdmModeLock())) {
                        continue;
                    }
                    if (isset($value['defaultFieldName']) && $properties['name'] === $value['defaultFieldName']) {
                        $columns[$key]['hidden'] = true;
                    }
                }
            }
        }


        $this->ss->assign('COLUMNCOUNT', $ret_field_count);
        $this->ss->assign('rows', $columns);

        $this->ss->assign('datetimeformat', $GLOBALS['timedate']->get_cal_date_time_format());

        // handle building index selector
        global $dictionary, $current_language;

        // show notes
        if ($this->bean instanceof Person) {
            $module_key = 'LBL_CONTACTS_NOTE_';
        } elseif ($this->bean instanceof Company) {
            $module_key = 'LBL_ACCOUNTS_NOTE_';
        } else {
            $module_key = 'LBL_' . strtoupper($import_module) . '_NOTE_';
        }
        $notetext = '';
        for ($i = 1; isset($mod_strings[$module_key . $i]); $i++) {
            $notetext .= '<li>' . $mod_strings[$module_key . $i] . '</li>';
        }

        if ($this->isLimitedForModuleInIdmMode($import_module)) {
            $notetext = '';
        }

        $this->ss->assign('NOTETEXT', $notetext);
        $this->ss->assign('HAS_HEADER', ($has_header ? 'on' : 'off'));

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
        // include anything needed for quicksearch to work
        // Bug #46879 : createQuickSearchCode() function in IBM RTC call function getQuickSearchDefaults() to get instance and then getQSDLookup() function
        // if we call this function as static it replaces context and use ImportViewStep3 as $this in getQSDLookup()
        $template_handler = new TemplateHandler();
        $quicksearch_js = $template_handler->createQuickSearchCode($fields, $fields, 'importstep3');

        $this->ss->assign('QS_JS', $quicksearch_js);
        $this->ss->assign('JAVASCRIPT', $this->_getJS($required));

        $this->ss->assign('required_fields', implode(', ', $required));
        $this->ss->assign('CSS', $this->_getCSS());

        $content = $this->ss->fetch($this->getCustomFilePathIfExists('modules/Import/tpls/step3.tpl'));
        $this->ss->assign('CONTENT', $content);
        $this->ss->display($this->getCustomFilePathIfExists('modules/Import/tpls/wizardWrapper.tpl'));
    }


    /**
     * getMappingClassName
     *
     * This function returns the name of a mapping class used to generate the mapping of an import source.
     * It first checks to see if an equivalent custom source map exists in custom/modules/Imports/maps directory
     * and returns this class name if found.  Searches are made for sources with a ImportMapCustom suffix first
     * and then ImportMap suffix.
     *
     * If no such custom file is found, the method then checks the modules/Imports/maps directory for a source
     * mapping file.
     *
     * Lastly, if a source mapping file is still not located, it checks in
     * custom/modules/Import/maps/ImportMapOther.php file exists, it uses the ImportMapOther class.
     *
     * @param string $source String name of the source mapping prefix
     * @return string name of the mapping class name
     * @see display()
     */
    protected function getMappingClassName($source)
    {
        // Try to see if we have a custom mapping we can use
        // based upon the where the records are coming from
        // and what module we are importing into
        $name = 'ImportMap' . $source;
        $customName = 'ImportMapCustom' . $source;

        if (SugarAutoLoader::requireWithCustom("modules/Import/maps/{$customName}.php")) {
            return $customName;
        }
        if (SugarAutoLoader::requireWithCustom("modules/Import/maps/{$name}.php")) {
            return $name;
        }

        SugarAutoLoader::requireWithCustom('modules/Import/maps/ImportMapOther.php');
        return 'ImportMapOther';
    }


    protected function getImportColumns()
    {
        $importColumns = [];
        foreach ($_REQUEST as $name => $value) {
            // only look for var names that start with "fieldNum"
            if (strncasecmp($name, 'colnum_', 7) != 0) {
                continue;
            }

            // pull out the column position for this field name
            $pos = substr($name, 7);

            // now mark that we've seen this field
            $importColumns[$pos] = $value;
        }

        return $importColumns;
    }

    protected function _getCSS()
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
    protected function _getJS($required)
    {
        global $mod_strings, $sugar_config;

        $importModule = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName', '');
        $customEnclosure = $this->request->getValidInputRequest('custom_enclosure', null, '');
        $importModuleJs = json_encode($importModule);

        $has_header = isset($_REQUEST['has_header']) && $_REQUEST['has_header'] === 'on';
        $uploadFileName = $this->request->getValidInputRequest('file_name', null, '');
        $splitter = new ImportFileSplitter($uploadFileName, $sugar_config['import_max_records_per_file']);
        $delimiter = $this->getDelimiterValue();
        $splitter->splitSourceFile($delimiter, html_entity_decode($customEnclosure, ENT_QUOTES), $has_header);
        $count = $splitter->getFileCount() - 1;
        $recCount = $splitter->getRecordCount();

        $stepTitle4 = $mod_strings['LBL_IMPORT_RECORDS'];

        $dateTimeFormat = $GLOBALS['timedate']->get_cal_date_time_format();

        $type = $this->request->getValidInputRequest(
            'type',
            ['Assert\Choice' => ['choices' => ['import', 'update', '']]],
            ''
        );
        $typeJs = json_encode($type);

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

    if (document.getElementById('importnow')) {
        document.getElementById('importnow').onclick = function(){
            var form = document.getElementById('importstep3');
            // Move on to next step
            document.getElementById('importstep3').action.value = 'Step4';
            ProcessImport.begin();
        }

        ProcessImport = new function()
        {
            /*
             * number of file to process processed
             */
            this.fileCount         = 0;

            /*
             * total files to processs
             */
            this.fileTotal         = {$count};

            /*
             * total records to process
             */
            this.recordCount       = {$recCount};

            /*
             * maximum number of records per file
             */
            this.recordThreshold   = {$sugar_config['import_max_records_per_file']};

            this.type = {$typeJs};

            this.importModule = {$importModuleJs};

            /*
             * submits the form
             */
            this.submit = function()
            {
                document.getElementById("importstep3").tmp_file.value =
                    document.getElementById("importstep3").tmp_file_base.value + '-' + this.fileCount;
                YAHOO.util.Connect.setForm(document.getElementById("importstep3"));
                YAHOO.util.Connect.asyncRequest('POST', 'index.php',
                    {
                        success: function(o) {
                             var locationStr = "index.php?" + SUGAR.util.paramsToUrl({
                                module: "Import",
                                action: "Last",
                                current_step: document.getElementById("importstep3").current_step.value,
                                type: ProcessImport.type,
                                import_module: ProcessImport.importModule,
                                has_header: document.getElementById("importstep3").has_header.value
                             });

                                if ( ProcessImport.fileCount >= ProcessImport.fileTotal ) {
                                    YAHOO.SUGAR.MessageBox.updateProgress(1,'{$mod_strings['LBL_IMPORT_COMPLETED']}');
                                    SUGAR.util.hrefURL(locationStr);
                                }
                                else {
                                    ProcessImport.fileCount++;
                                    ProcessImport.submit();
                                }
                        },
                        failure: function(o) {
                            YAHOO.SUGAR.MessageBox.minWidth = 500;
                            YAHOO.SUGAR.MessageBox.show({
                                type:  "alert",
                                title: '{$mod_strings['LBL_IMPORT_ERROR']}',
                                msg:   o.responseText,
                                fn: function() { window.location.reload(true); }
                            });
                        }
                    }
                );
                var move = 0;
                if ( this.fileTotal > 0 ) {
                    move = this.fileCount/this.fileTotal;
                }
                YAHOO.SUGAR.MessageBox.updateProgress( move,
                    "{$mod_strings['LBL_IMPORT_RECORDS']} " + ((this.fileCount * this.recordThreshold) + 1)
                                + " {$mod_strings['LBL_IMPORT_RECORDS_TO']} " + Math.min(((this.fileCount+1) * this.recordThreshold),this.recordCount)
                                + " {$mod_strings['LBL_IMPORT_RECORDS_OF']} " + this.recordCount );
            }

            /*
             * begins the form submission process
             */
            this.begin = function()
            {
                datestarted = '{$mod_strings['LBL_IMPORT_STARTED']} ' +
                        YAHOO.util.Date.format('{$dateTimeFormat}');
                YAHOO.SUGAR.MessageBox.show({
                    title: '{$stepTitle4}',
                    msg: datestarted,
                    width: 500,
                    type: "progress",
                    closable:false,
                    animEl: 'importnow'
                });
                this.submit();
            }
        }
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
        var bgColor = 'var(--primary-content-active)';
        YAHOO.util.Dom.addClass('hide_default_link', 'collapse');
        YAHOO.util.Dom.removeClass('hide_default_link', 'expand');
        var col2Rowspan = "1";
    }
    else
    {
        var newStyle = 'none';
        var bgColor = 'var(--primary-content-background)';
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

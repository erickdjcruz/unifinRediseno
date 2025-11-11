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
 * Description:  Creates the runtime database connection.
 ********************************************************************************/
class javascript
{
    public $formname = 'form';
    public $script = '';
    public $sugarbean = null;

    public function setFormName($name)
    {
        $this->formname = $name;
    }

    public function __construct()
    {
        global $app_strings, $current_user, $sugar_config;

        // Bug 24730 - default initialize the bean object in case we never set it to the current bean object
        $this->sugarbean = new stdClass();
        $this->sugarbean->field_defs = [];
        $this->sugarbean->module_dir = '';
    }

    public function setSugarBean($sugar)
    {
        $this->sugarbean = $sugar;
    }

    public function addRequiredFields($prefix = '')
    {
        if (isset($this->sugarbean->required_fields)) {
            foreach ($this->sugarbean->required_fields as $field => $value) {
                $this->addField($field, 'true', $prefix);
            }
        }
    }

    public function addSpecialField($dispField, $realField, $type, $required, $prefix = '')
    {
        if (isset($this->sugarbean->field_defs[$realField]['vname'])) {
            $this->addFieldGeneric(
                $dispField,
                'date',
                $this->sugarbean->field_defs[$realField]['vname'],
                $required,
                $prefix
            );
        }
    }

    public function addField($field, $required, $prefix = '', $displayField = '', $translate = false)
    {
        if ($field == 'id') {
            return;
        }
        if (isset($this->sugarbean->field_defs[$field]['vname'])) {
            $vname = $this->sugarbean->field_defs[$field]['vname'];
            if ($translate) {
                $vname = $this->buildStringToTranslateInSmarty($this->sugarbean->field_defs[$field]['vname']);
            }
            if (empty($required)) {
                if (!empty($this->sugarbean->field_defs[$field]['required'])) {
                    $required = 'true';
                } else {
                    $required = 'false';
                }
                if (isset($this->sugarbean->required_fields[$field]) && $this->sugarbean->required_fields[$field]) {
                    $required = 'true';
                }
                if ($field == 'id') {
                    $required = 'false';
                }
            }
            if (isset($this->sugarbean->field_defs[$field]['validation'])) {
                switch ($this->sugarbean->field_defs[$field]['validation']['type']) {
                    case 'range':
                        $min = false;
                        $max = false;
                        if (isset($this->sugarbean->field_defs[$field]['validation']['min'])) {
                            $min = filter_var(
                                $this->sugarbean->field_defs[$field]['validation']['min'],
                                FILTER_VALIDATE_INT
                            );
                        }
                        if (isset($this->sugarbean->field_defs[$field]['validation']['max'])) {
                            $max = filter_var(
                                $this->sugarbean->field_defs[$field]['validation']['max'],
                                FILTER_VALIDATE_INT
                            );
                        }
                        if ($min !== false && $max !== false && $min > $max) {
                            $max = $min;
                        }
                        if (!empty($displayField)) {
                            $dispField = $displayField;
                        } else {
                            $dispField = $field;
                        }
                        $this->addFieldRange(
                            $dispField,
                            $this->sugarbean->field_defs[$field]['type'],
                            $vname,
                            $required,
                            $prefix,
                            $min,
                            $max
                        );
                        break;
                    case 'isbefore':
                        $compareTo = $this->sugarbean->field_defs[$field]['validation']['compareto'];
                        if (!empty($displayField)) {
                            $dispField = $displayField;
                        } else {
                            $dispField = $field;
                        }
                        if (!empty($this->sugarbean->field_defs[$field]['validation']['blank'])) {
                            $this->addFieldDateBeforeAllowBlank(
                                $dispField,
                                $this->sugarbean->field_defs[$field]['type'],
                                $vname,
                                $required,
                                $prefix,
                                $compareTo
                            );
                        } else {
                            $this->addFieldDateBefore(
                                $dispField,
                                $this->sugarbean->field_defs[$field]['type'],
                                $vname,
                                $required,
                                $prefix,
                                $compareTo
                            );
                        }
                        break;
                        // Bug #47961 Adding new type of validation: through callback function
                    case 'callback':
                        $dispField = $displayField ?: $field;
                        $this->addFieldCallback(
                            $dispField,
                            $this->sugarbean->field_defs[$field]['type'],
                            $vname,
                            $required,
                            $prefix,
                            $this->sugarbean->field_defs[$field]['validation']['callback']
                        );
                        break;
                    default:
                        if (!empty($displayField)) {
                            $dispField = $displayField;
                        } else {
                            $dispField = $field;
                        }

                        $type = !empty($this->sugarbean->field_defs[$field]['custom_type'])
                            ? $this->sugarbean->field_defs[$field]['custom_type']
                            : $this->sugarbean->field_defs[$field]['type'];

                        $this->addFieldGeneric($dispField, $type, $vname, $required, $prefix);
                        break;
                }
            } else {
                if (!empty($displayField)) {
                    $dispField = $displayField;
                } else {
                    $dispField = $field;
                }

                $type = !empty($this->sugarbean->field_defs[$field]['custom_type'])
                    ? $this->sugarbean->field_defs[$field]['custom_type']
                    : $this->sugarbean->field_defs[$field]['type'];

                if (!empty($this->sugarbean->field_defs[$dispField]['isMultiSelect'])) {
                    $dispField .= '[]';
                }
                $this->addFieldGeneric($dispField, $type, $vname, $required, $prefix);
            }
        } else {
            $GLOBALS['log']->debug('No VarDef Label For ' . $field . ' in module ' . $this->sugarbean->module_dir);
        }
    }


    public function stripEndColon($modString)
    {
        if (substr($modString, -1, 1) == ':') {
            $modString = substr($modString, 0, (strlen($modString) - 1));
        }
        if (substr($modString, -2, 2) == ': ') {
            $modString = substr($modString, 0, (strlen($modString) - 2));
        }
        return $modString;
    }

    public function addFieldGeneric($field, $type, $displayName, $required, $prefix = '')
    {
        $this->script .= "addToValidate('" . $this->formname . "', '" . $prefix . $field . "', '" . $type . "', {$this->getRequiredString($required)},'"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "' );\n";
    }

    // Bug #47961 Generator of callback validator
    public function addFieldCallback($field, $type, $displayName, $required, $prefix, $callback)
    {
        $this->script .= 'addToValidateCallback("'
            . $this->formname . '", "'
            . $prefix . $field . '", "'
            . $type . '", '
            . $this->getRequiredString($required) . ', "'
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . '", '
            . $callback
            . ');' . "\n";
    }

    public function addFieldRange(string $field, string $type, string $displayName, $required, string $prefix, $min, $max): void
    {
        $this->script .= 'addToValidateRange('
            . "'" . $this->formname . "', "
            . "'" . $prefix . $field . "', '"
            . $type . "', "
            . $this->getRequiredString($required) . ", '"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', "
            . ($min === false ? 'false' : $min) . ', '
            . ($max === false ? 'false' : $max)
            . ");\n";
    }

    public function addFieldIsValidDate($field, $type, $displayName, $msg, $required, $prefix = '')
    {
        $name = $prefix . $field;
        $req = $this->getRequiredString($required);
        $this->script .= "addToValidateIsValidDate('{$this->formname}', '{$name}', '{$type}', {$req}, '{$msg}');\n";
    }

    public function addFieldIsValidTime($field, $type, $displayName, $msg, $required, $prefix = '')
    {
        $name = $prefix . $field;
        $req = $this->getRequiredString($required);
        $this->script .= "addToValidateIsValidTime('{$this->formname}', '{$name}', '{$type}', {$req}, '{$msg}');\n";
    }

    public function addFieldDateBefore(string $field, string $type, string $displayName, $required, string $prefix, string $compareTo): void
    {
        $this->script .= "addToValidateDateBefore('" . $this->formname . "', '" . $prefix . $field . "', '" . $type . "', {$this->getRequiredString($required)},'"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', '$compareTo' );\n";
    }

    public function addFieldDateBeforeAllowBlank(string $field, string $type, string $displayName, $required, string $prefix, string $compareTo, string $allowBlank = 'true'): void
    {
        $this->script .= "addToValidateDateBeforeAllowBlank('" . $this->formname . "', '" . $prefix . $field . "', '" . $type . "', {$this->getRequiredString($required)},'"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', '$compareTo', '$allowBlank' );\n";
    }

    public function addToValidateBinaryDependency(string $field, string $type, string $displayName, $required, string $prefix, string $compareTo): void
    {
        $this->script .= "addToValidateBinaryDependency('" . $this->formname . "', '" . $prefix . $field . "', '" . $type . "', {$this->getRequiredString($required)},'"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', '$compareTo' );\n";
    }

    public function addToValidateComparison(string $field, string $type, string $displayName, $required, string $prefix, string $compareTo): void
    {
        $this->script .= "addToValidateComparison('" . $this->formname . "', '" . $prefix . $field . "', '" . $type . "', {$this->getRequiredString($required)},'"
            . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', '$compareTo' );\n";
    }

    public function addFieldIsInArray($field, $type, $displayName, $required, $prefix, $arr, $operator)
    {
        $name = $prefix . $field;
        $req = $this->getRequiredString($required);
        $json = getJSONobj();
        $arr = $json->encode($arr);
        $this->script .= "addToValidateIsInArray('{$this->formname}', '{$name}', '{$type}', {$req}, '" . $this->stripEndColon(translate($displayName, $this->sugarbean->module_dir)) . "', '{$arr}', '{$operator}');\n";
    }

    public function addAllFields($prefix, $skip_fields = null, $translate = false)
    {
        if (!isset($skip_fields)) {
            $skip_fields = [];
        }
        foreach ($this->sugarbean->field_defs as $field => $value) {
            if (!isset($skip_fields[$field])) {
                if (isset($value['type']) && ($value['type'] == 'datetimecombo' || $value['type'] == 'datetime')) {
                    $isRequired = (isset($value['required']) && $value['required']) ? 'true' : 'false';
                    $this->addSpecialField($value['name'] . '_date', $value['name'], 'datetime', $isRequired);
                    if ($value['type'] != 'link' && isset($this->sugarbean->field_defs[$field]['validation'])) {
                        //datetime should also support the isbefore or other type of validate
                        $this->addField($field, '', $prefix, '', $translate);
                    }
                } elseif (isset($value['type'])) {
                    if ($value['type'] != 'link') {
                        $this->addField($field, '', $prefix, '', $translate);
                    }
                }
            }
        }
    }

    public function addActionMenu()
    {
        $this->script .= '$(document).ready(SUGAR.themes.actionMenu);';
    }

    public function getScript($showScriptTag = true, $clearValidateFields = true)
    {
        $tempScript = $this->script;
        $this->script = '';
        if ($showScriptTag) {
            $this->script = "<script type=\"text/javascript\">\n";
        }

        if ($clearValidateFields) {
            $this->script .= "addForm('{$this->formname}');";
        }

        $this->script .= $tempScript;

        if ($showScriptTag) {
            $this->script .= '</script>';
        }
        return $this->script;
    }

    public function buildStringToTranslateInSmarty(
        $string
    ) {


        if (is_array($string)) {
            $returnstring = '';
            foreach ($string as $astring) {
                $returnstring .= $this->buildStringToTranslateInSmarty($astring);
            }
            return $returnstring;
        }
        return "{/literal}{sugar_translate label='$string' module='{$this->sugarbean->module_dir}' for_js=true}{literal}";
    }

    protected function getRequiredString($required)
    {
        if (is_string($required) && strtolower($required) == 'false') {
            return 'false';
        }
        return empty($required) ? 'false' : 'true';
    }
}

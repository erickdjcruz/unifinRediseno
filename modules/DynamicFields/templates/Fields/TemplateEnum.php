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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;

class TemplateEnum extends TemplateText
{
    /**
     * @var array<string, class-string<\trigger>>|array<string, string>|mixed
     */
    public $localVardefMap;
    /**
     * @var mixed|mixed[]
     */
    public $trigger;
    /**
     * @var mixed|mixed[]
     */
    public $action;
    /** @var array */
    public $visibility_grid;

    public $max_size = 100;
    public $len = 100;
    public $type = 'enum';
    public $ext1 = '';
    public $default_value = '';
    public $dependency; // any dependency information
    public $supports_unified_search = true;
    public $massupdate = 1;

    /**
     * Ctor
     */
    public function __construct()
    {
        // ensure that the field dependency information is read in from any _REQUEST
        $this->localVardefMap = [
            'trigger' => 'trigger',
            'action' => 'action',
            'visibility_grid' => 'visibility_grid',
        ];
        $this->vardef_map = array_merge($this->vardef_map, $this->localVardefMap);

        // default_value field is an array of strings
        $this->vardefMapValidation['default_value'] = [
            'Assert\All' => [
                'constraints' => ['Assert\Type' => ['type' => 'string']],
            ],
        ];
    }

    public function populateFromPost(Request $request = null)
    {
        if (!$request) {
            $request = InputValidation::getService();
        }

        parent::populateFromPost($request);
        // Handle empty massupdate checkboxes
        $this->massupdate = !empty($_REQUEST['massupdate']);
        if (empty($this->visibility_grid)) {
            $this->visibility_grid = [];
        } elseif (is_string($this->visibility_grid)) {
            $this->visibility_grid = json_decode(html_entity_decode($this->visibility_grid, ENT_COMPAT), true);
        }
        // now convert trigger,action pairs into a dependency array representation
        // we expect the dependencies in the following format:
        // trigger = [ trigger for action 1 , trigger for action 2 , ... , trigger for action n ]
        // action = [ action 1 , action 2 , ... , action n ]

        // check first if we have the component parts of a dependency
        $dependencyPresent = true;
        foreach ($this->localVardefMap as $def) {
            $dependencyPresent &= isset($this->$def);
        }

        if ($dependencyPresent) {
            $dependencies = [];

            if (is_array($this->trigger) && is_array($this->action)) {
                for ($i = 0; $i < safeCount($this->action); $i++) {
                    $dependencies [$this->trigger [$i]] = $this->action [$i];
                }
                $this->dependency = $dependencies;
            } else {
                if (!is_array($this->trigger) && !is_array($this->action)) {
                    $this->dependency = [$this->trigger => $this->action];
                }
            }
            // tidy up
            unset($this->trigger);
            unset($this->action);
        }
    }

    public function get_xtpl_edit()
    {
        $returnXTPL = [];
        $name = $this->name;
        $value = '';
        if (isset($this->bean->$name)) {
            $value = $this->bean->$name;
        } else {
            if (empty($this->bean->id)) {
                $value = $this->default_value;
            }
        }
        if (!empty($this->help)) {
            $returnXTPL[strtoupper($this->name . '_help')] = translate($this->help, $this->bean->module_dir);
        }

        global $app_list_strings;
        $returnXTPL = [];
        $returnXTPL[strtoupper($this->name)] = $value;
        if (empty($this->ext1)) {
            $this->ext1 = $this->options;
        }
        $returnXTPL[strtoupper('options_' . $this->name)] = get_select_options_with_id($app_list_strings[$this->ext1], $value);

        return $returnXTPL;
    }

    public function get_xtpl_search()
    {
        $searchFor = '';
        if (!empty($_REQUEST[$this->name])) {
            $searchFor = $_REQUEST[$this->name];
        }
        global $app_list_strings;
        $returnXTPL = [];
        $returnXTPL[strtoupper($this->name)] = $searchFor;
        if (empty($this->ext1)) {
            $this->ext1 = $this->options;
        }
        $returnXTPL[strtoupper('options_' . $this->name)] = get_select_options_with_id(add_blank_option($app_list_strings[$this->ext1]), $searchFor);
        return $returnXTPL;
    }

    public function get_field_def()
    {
        $def = parent::get_field_def();
        $def['options'] = !empty($this->options) ? $this->options : $this->ext1;
        $def['default'] = !empty($this->default) ? $this->default : $this->default_value;

        // this class may be extended, so only do the unserialize for genuine TemplateEnums
        if (get_class($this) == 'TemplateEnum' && empty($def['dependency'])) {
            $def['dependency'] = isset($this->ext4) ?
                @unserialize(html_entity_decode($this->ext4, ENT_COMPAT), ['allowed_classes' => false])
                : null;
        }
        $def['visibility_grid'] = empty($this->visibility_grid) ? [] : $this->visibility_grid;

        return $def;
    }

    public function get_xtpl_detail()
    {
        $name = $this->name;

        // awu: custom fields are not being displayed on the detail view because $this->ext1 is always empty, adding this to get the options
        if (empty($this->ext1)) {
            if (!empty($this->options)) {
                $this->ext1 = $this->options;
            }
        }

        if (isset($this->bean->$name)) {
            $key = $this->bean->$name;
            global $app_list_strings;
            if (preg_match('/&amp;/s', $key)) {
                $key = str_replace('&amp;', '&', $key);
            }
            if (isset($app_list_strings[$this->ext1])) {
                if (isset($app_list_strings[$this->ext1][$key])) {
                    return $app_list_strings[$this->ext1][$key];
                }

                if (isset($app_list_strings[$this->ext1][$this->bean->$name])) {
                    return $app_list_strings[$this->ext1][$this->bean->$name];
                }
            }
        }
        return '';
    }

    public function save($df)
    {
        if (!empty($this->default_value) && is_array($this->default_value)) {
            $this->default_value = $this->default_value[0];
        }
        if (!empty($this->default) && is_array($this->default)) {
            $this->default = $this->default[0];
        }
        if (empty($this->visibility_grid)) {
            $this->visibility_grid = [];
        } elseif (is_string($this->visibility_grid)) {
            $this->visibility_grid = json_decode($this->visibility_grid, true);
        }
        parent::save($df);
    }

    /**
     * @param DynamicField $df
     */
    public function delete($df)
    {
        //If a dropdown uses the field that is being delted as a parent dropdown, we need to remove that dependency
        $moduleName = $df->getModuleName();
        $seed = BeanFactory::newBean($moduleName);
        if ($seed) {
            $fields = $seed->field_defs;
            foreach ($fields as $field => $def) {
                if (!empty($def['visibility_grid']['trigger']) && $def['visibility_grid']['trigger'] == $this->name) {
                    $field = get_widget($def ['type']);
                    unset($def['visibility_grid']);
                    $field->populateFromRow($def);
                    if (isset($def['source']) && $def['source'] == 'custom_fields') {
                        $field->save($df);
                    } else {
                        //Out of the box field that we need to use a StandardField rather than DynamicFIeld object to save
                        $sf = new StandardField($df->getModuleName());
                        $sf->setup($seed);
                        $field->module = $seed;
                        $field->save($sf);
                    }
                }
            }
        }

        // Remove any dropdown-based views based on this field
        $dropdownLayoutPaths = 'custom/modules/' . $moduleName . '/clients/*/views/*/dropdowns/';

        if (!check_file_name($dropdownLayoutPaths)) {
            $GLOBALS['log']->fatal("Path traversal attack vector detected: '$dropdownLayoutPaths'.");
            throw new \Exception('Path traversal attack vector detected');
        }

        $dropdownFolders = glob($dropdownLayoutPaths);
        foreach ($dropdownFolders as $dropdownFolder) {
            if (is_dir($dropdownFolder) && is_dir($dropdownFolder . $field)) {
                rmdir_recursive($dropdownFolder);
            }
        }

        parent::delete($df);
    }
}

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
 * Description:
 ********************************************************************************/


require_once 'include/workflow/workflow_utils.php';
require_once 'include/workflow/field_utils.php';
require_once 'include/utils/expression_utils.php';


require_once 'modules/Expressions/MetaArray.php';

// Expression is a general object for expressions, filters, and calculations
class Expression extends SugarBean
{
    // Stored fields
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;

    //construction
    public $name;


    public $lhs_type;
    public $lhs_field;
    public $lhs_module;
    public $lhs_value;

    public $lhs_group_type;
    public $operator;
    public $rhs_group_type;

    public $rhs_type;
    public $rhs_field;
    public $rhs_module;
    public $rhs_value;

    public $parent_id;
    public $exp_type;
    public $exp_order;

    public $parent_exp_id;
    public $parent_exp_side;

    public $parent_type = 'filter';

    public $ext1;
    public $ext2;
    public $ext3;

    public $return_prefix;

    //used for the selector popups
    public $show_field = false;            //show the block for selecting field
    public $seed_object;


    ///for display text
    public $target_bean;
    public $display_array;


    public $selector_popup_fields = [
        'lhs_module'
        , 'lhs_field'
        , 'return_prefix'
        , 'rhs_value'
        , 'parent_type'
        , 'operator',
    ];


    public $table_name = 'expressions';
    public $module_dir = 'Expressions';
    public $object_name = 'Expression';

    public $new_schema = true;

    public $column_fields = ['id'
        , 'name'
        , 'date_entered'
        , 'date_modified'
        , 'modified_user_id'
        , 'created_by'

        , 'lhs_type'
        , 'lhs_field'
        , 'lhs_module'
        , 'lhs_value'

        , 'lhs_group_type'
        , 'operator'
        , 'rhs_group_type'

        , 'rhs_type'
        , 'rhs_field'
        , 'rhs_module'
        , 'rhs_value'

        , 'parent_id'
        , 'exp_type'
        , 'exp_order'

        , 'parent_exp_id'
        , 'parent_exp_side'

        , 'parent_type'

        , 'ext1'
        , 'ext2'
        , 'ext3',
    ];


    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];

    // This is the list of fields that are in the lists.
    public $list_fields = [];
    // This is the list of fields that are required
    public $required_fields = [];

    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
    }


    public function get_summary_text()
    {
        return "$this->name";
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
    }


    public function mark_relationships_deleted($id)
    {
    }

    public function fill_in_additional_list_fields()
    {
    }

    public function fill_in_additional_detail_fields()
    {
    }


    public function get_list_view_data($filter_fields = [])
    {
    }


    public function clear_deleted($id)
    {
    }


    public function get_selector_array($type, $value, $dom_name, $text_only_array = false, $meta_filter_name = '', $only_related_modules = false, $trigger_type = '', $only_plural = false)
    {
        $select_array = [];
        global $app_list_strings;
        global $current_language;

        if ($type == 'assigned_user_id' || $type == 'assigned_user_name') {
            $select_array = get_user_array(true, 'Active', '', true, null, ' AND is_group=0 ');
        }
        if ($type == 'team_list') {
            $select_array = get_team_array();
        }
        if ($type == 'role') {
            $select_array = get_bean_select_array(true, 'ACLRole', 'name');
        }
        if ($type == 'dom_array') {
            if (!empty($app_list_strings[$dom_name])) {
                $select_array = $app_list_strings[$dom_name];
            }
            ksort($select_array);
        }

        if ($type == 'field') {
            $temp_module = BeanFactory::newBean($dom_name);
            if (!is_object($temp_module)) {
                $GLOBALS['log']->fatal("get_selector_array: Unknown module: $dom_name");
                return null;
            }
            if (isset($trigger_type) && !empty($trigger_type)) {
                global $process_dictionary;
                include_once 'modules/WorkFlowTriggerShells/MetaArray.php';
                if (array_key_exists('trigger_type_override', $process_dictionary['TriggersCreateStep1']['elements'][$trigger_type])) {
                    //we have found an override
                    $meta_filter_name = $process_dictionary['TriggersCreateStep1']['elements'][$trigger_type]['trigger_type_override'];
                }
            }
            $temp_module->call_vardef_handler($meta_filter_name);
            if ($_GET['opener_id'] == 'rel_module') {
                $temp_select_array = $temp_module->vardef_handler->get_vardef_array(false, false, true, false, true);
                $select_array = getDuplicateRelationListWithTitle($temp_select_array, $temp_module->vardef_handler->module_object->field_defs, $temp_module->vardef_handler->module_object->module_dir);
            } else {
                $select_array = $temp_module->vardef_handler->get_vardef_array(true, false, false, false, true);
                $select_array = array_unique($select_array);
                asort($select_array);
            }

            //end if type is field
        }

        if ($type == 'module_list') {
            if ($only_related_modules) {
                global $beanList;
                $temp_module = BeanFactory::newBean($dom_name);
                $temp_module->call_vardef_handler('rel_filter');

                $select_array = $temp_module->vardef_handler->get_vardef_array(true, true, true, true, true);
            } elseif ($meta_filter_name == 'singular') {
                $select_array = convert_module_to_singular(get_module_map(false));
            } else {
                $select_array = get_module_map();
            }

            unset($select_array['Forecasts']);
            unset($select_array['Products']);
            unset($select_array['Documents']);
            asort($select_array);
            //end if type is module_list
        }

        if (!empty($select_array)) {
            if ($text_only_array == true) {
                return $select_array;
            } else {
                return get_select_options_with_id($select_array, $value);
            }
        } else {
            return null;
        }
        //end get_selector_array
    }


    public function build_field_filter($base_module, $target_field, $enum_multi = false)
    {

        ////Begin - New Code call to workflow_utils
        $temp_module = BeanFactory::newBean($base_module);
        //Build Selector Array
        $selector_array = [
            'value' => $this->rhs_value,
            'operator' => $this->operator,
            'time' => $this->ext1,
            'field' => $target_field,
            'target_field' => $this->lhs_field,
        ];

        $meta_array = [
            'parent_type' => $this->parent_type,
            'enum_multi' => $enum_multi,
        ];


        $output_array = get_field_output($temp_module, $selector_array, $meta_array);
        return $output_array;


        //end function build_field_filter
    }


///////////////////////////////////Display label functions///////////////


    public function get_display_array_using_name($target_module = '')
    {
        //use this if you don't have the module name.
        //you can either build using lhs_module or override with your own

        if ($target_module == '') {
            $target_bean = BeanFactory::newBean($this->lhs_module);
        } else {
            $target_bean = BeanFactory::newBean($target_module);
        }

        return $this->get_display_array($target_bean);

        //end function get_display_array_using_name
    }

    public function get_display_array(&$target_bean)
    {
        global $app_strings;
        $this->target_bean = $target_bean;
        $this->display_array = [];

        //Grab label for lhs_field
        $this->display_array['lhs_field'] = translate_label_from_bean($target_bean, $this->lhs_field);


        //Grab label for operator
        $this->display_array['operator'] = $this->get_display_operator();


        //check for enum multi
        if ($this->operator == 'in' || $this->operator == 'not_in') {
            //foreach loop on the in values
            $selected_array = unencodeMultienum($this->rhs_value);
            $multi_text = '';
            $selected_count = safeCount($selected_array);
            $the_counter = 1;
            foreach ($selected_array as $key => $value) {
                if ($multi_text != '') {
                    if ($the_counter != $selected_count) {
                        $multi_text .= ', ';
                    } else {
                        if ($selected_count > 2) {
                            $multi_text .= ", {$app_strings['LBL_LOWER_OR']} ";
                        } else {
                            $multi_text .= " {$app_strings['LBL_LOWER_OR']} ";
                        }
                    }
                    //end if multi is not blank
                }

                $multi_text .= $this->get_display_rhs_value($value);
                ++$the_counter;
            }

            $this->display_array['rhs_value'] = $multi_text;
            //end if enum multi
        } else {
            //Grab lable for rhs_value
            $this->display_array['rhs_value'] = $this->get_display_rhs_value($this->rhs_value);
            //end if not enum multi value
        }

        return $this->display_array;

        //end function get_display_array
    }


    public function get_display_rhs_value($rhs_value)
    {

        if ($this->exp_type == 'assigned_user_name' || $this->exp_type == 'team_list') {
            $text_array = $this->get_selector_array($this->exp_type, $rhs_value, '', true);

            return $text_array[$rhs_value];

            //end if team or assigned_user
        }

        if ($this->exp_type == 'bool') {
            global $app_list_strings;
            if ($rhs_value == 'bool_true') {
                return $app_list_strings['bselect_type_dom']['bool_true'] ?? 'Yes';
            }
            if ($rhs_value == 'bool_false') {
                return $app_list_strings['bselect_type_dom']['bool_false'] ?? 'No';
            }
            return '';
            //end if target_type is bool
        }
        //if enum and reaching here
        if ($this->exp_type == 'enum' || $this->exp_type == 'multienum') {
            return translate_option_name_from_bean($this->target_bean, $this->lhs_field, $rhs_value);
            //end if enum
        }


        return $rhs_value;

        //end function get_display_rhs_value
    }


    public function get_display_operator()
    {
        global $app_list_strings, $app_strings, $mod_strings;
        if ($this->operator == 'in') {
            $operator_text = $app_strings['LBL_OPERATOR_IN_TEXT'];
        } elseif ($this->operator == 'not_in') {
            $operator_text = $app_strings['LBL_OPERATOR_NOT_IN_TEXT'];
        } elseif (!empty($app_list_strings['dselect_type_dom'][$this->operator])) {
            $operator_text = $app_list_strings['dselect_type_dom'][$this->operator];
        } else {
            $operator_text = $this->operator;
        }

        return $operator_text;

        //end function get_display_operator
    }


    public function handleSave($prefix, $parent_type, $exp_id = '')
    {

        if ($exp_id != '') {
            $this->retrieve($exp_id);
        }


        foreach ($this->column_fields as $field) {
            if (isset($_POST[$prefix . '' . $field])) {
                $this->$field = $_POST[$prefix . '' . $field];
            }
        }

        if (!empty($_POST[$prefix . 'time_int'])) {
            $this->ext1 = $_POST[$prefix . 'time_int'];
        }


        $this->parent_type = $parent_type;
        $this->save();

        //end function handleSave
    }

    /**
     * This function will determine whether the given input string is plural
     *
     * @param string to check
     *
     * @return true or false
     */
    public function isPlural($text)
    {
        $pattern = '/s$/i';
        preg_match($pattern, $text, $matches);
        return (safeCount($matches) > 0);
    }

//end class Expression
}

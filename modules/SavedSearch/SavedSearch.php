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
require_once 'include/templates/TemplateGroupChooser.php';

class SavedSearch extends SugarBean
{
    // Stored fields
    public $id;
    public $date_entered;
    public $date_modified;
    public $assigned_user_id;
    public $assigned_user_name;
    public $modified_by_name;
    public $team_id;
    public $team_name;
    public $name;
    public $description;
    public $contents;
    public $search_module;

    public $object_name = 'SavedSearch';
    public $table_name = 'saved_search';

    public $module_dir = 'SavedSearch';
    public $field_defs = [];
    public $field_defs_map = [];

    public $columns;

    public function __construct()
    {
        parent::__construct();
        $args = func_get_args();
        if (safeCount($args) > 0) {
            // old ctor, pass to init
            call_user_func_array([$this, 'init'], $args);
        }
    }

    public function init($columns = [], $orderBy = null, $sortOrder = 'DESC')
    {
        $this->columns = $columns;
        $this->orderBy = $orderBy;
        $this->sortOrder = $sortOrder;
        $this->setupCustomFields('SavedSearch');
        foreach ($this->field_defs as $field) {
            $this->field_defs[$field['name']] = $field;
        }
    }

    // Saved Search Form
    public function getForm($module, $inline = true)
    {
        $savedSearchArray = [];
        global $db, $current_user, $currentModule, $current_language, $app_strings;
        $json = getJSONobj();

        $saved_search_mod_strings = return_module_language($current_language, 'SavedSearch');

        $query = 'SELECT id, name FROM saved_search
				  WHERE
					deleted = \'0\' AND
				  	assigned_user_id = \'' . $current_user->id . '\' AND
					search_module =  \'' . $module . '\'
				  ORDER BY name';
        $result = $db->query($query, true, 'Error filling in saved search list: ');

        $savedSearchArray['_none'] = $app_strings['LBL_NONE'];
        while ($row = $db->fetchByAssoc($result, false)) {
            $savedSearchArray[$row['id']] = htmlspecialchars($row['name'], ENT_QUOTES);
        }
        $sugarSmarty = new Sugar_Smarty();
        $sugarSmarty->assign('SEARCH_MODULE', $module);
        $sugarSmarty->assign('MOD', $saved_search_mod_strings);
        $sugarSmarty->assign('DELETE', $app_strings['LBL_DELETE_BUTTON_LABEL']);
        $sugarSmarty->assign('UPDATE', $app_strings['LBL_UPDATE']);
        $sugarSmarty->assign('SAVE', $app_strings['LBL_SAVE_BUTTON_LABEL']);

        // Column Chooser
        $chooser = new TemplateGroupChooser();

        $chooser->args['id'] = 'edit_tabs';
        $chooser->args['left_size'] = 7;
        $chooser->args['right_size'] = 7;
        $chooser->args['values_array'][0] = [];
        $chooser->args['values_array'][1] = [];

        if (isset($_REQUEST['saved_search_select']) && $_REQUEST['saved_search_select'] != '_none') {
            $this->retrieveSavedSearch($_REQUEST['saved_search_select']);
        }

        if ((!empty($_REQUEST['displayColumns']) && $_REQUEST['displayColumns'] != 'undefined') || (isset($this->contents['displayColumns']) && $this->contents['displayColumns'] != 'undefined')) {
            // columns to display
            if (!empty($_REQUEST['displayColumns']) && $_REQUEST['displayColumns'] != 'undefined') {
                $temp_displayColumns = $_REQUEST['displayColumns'];
            } else {
                $temp_displayColumns = $this->contents['displayColumns'];
            }
            foreach (explode('|', $temp_displayColumns) as $num => $name) {
                if (!isset($this->columns[$name])) {
                    // Ignore any column that is not on the list.
                    continue;
                }
                $chooser->args['values_array'][0][$name] = trim(translate($this->columns[$name]['label'], $module), ':');
            }
            // columns not displayed
            foreach (array_diff(array_keys($this->columns), array_values(explode('|', $temp_displayColumns))) as $num => $name) {
                $chooser->args['values_array'][1][$name] = trim(translate($this->columns[$name]['label'], $module), ':');
            }
        } else {
            foreach ($this->columns as $name => $val) {
                if (!empty($val['default']) && $val['default']) {
                    $chooser->args['values_array'][0][$name] = trim(translate($val['label'], $module), ':');
                } else {
                    $chooser->args['values_array'][1][$name] = trim(translate($val['label'], $module), ':');
                }
            }
        }

        if (!empty($_REQUEST['sortOrder'])) {
            $this->sortOrder = $_REQUEST['sortOrder'];
        }
        if (!empty($_REQUEST['orderBy'])) {
            $this->orderBy = $_REQUEST['orderBy'];
        }

        $chooser->args['left_name'] = 'display_tabs';
        $chooser->args['right_name'] = 'hide_tabs';
        $chooser->args['alt_tip'] = $app_strings['LBL_SORT'];

        $chooser->args['left_label'] = $app_strings['LBL_DISPLAY_COLUMNS'];
        $chooser->args['right_label'] = $app_strings['LBL_HIDE_COLUMNS'];
        $chooser->args['title'] = '';
        $sugarSmarty->assign('columnChooser', $chooser->display());

        $sugarSmarty->assign('selectedOrderBy', $this->orderBy);
        if (empty($this->sortOrder)) {
            $this->sortOrder = 'ASC';
        }
        $sugarSmarty->assign('selectedSortOrder', $this->sortOrder);

        $lastSavedView = (empty($_SESSION['LastSavedView'][$module]) ? '' : $_SESSION['LastSavedView'][$module]);
        $sugarSmarty->assign('columnsMeta', $json->encode($this->columns));
        $sugarSmarty->assign('lastSavedView', $lastSavedView);
        $sugarSmarty->assign('SAVED_SEARCHES_OPTIONS', get_select_options_with_id($savedSearchArray, $lastSavedView));

        $json = getJSONobj();

        return $sugarSmarty->fetchCustom('modules/SavedSearch/SavedSearchForm.tpl');
    }

    public function getSelect($module)
    {


        $savedSearchArray = [];
        global $db, $current_user, $currentModule, $current_lang, $app_strings;
        $saved_search_mod_strings = return_module_language($current_lang, 'SavedSearch');

        $query = 'SELECT id, name FROM saved_search
                  WHERE
                    deleted = \'0\' AND
                    assigned_user_id = ' . $db->quoted($current_user->id) . ' AND
                    search_module = ' . $db->quoted($module) . '
                  ORDER BY name';
        $result = $db->query($query, true, 'Error filling in saved search list: ');

        $savedSearchArray['_none'] = $app_strings['LBL_NONE'];
        while ($row = $db->fetchByAssoc($result, false)) {
            $savedSearchArray[$row['id']] = htmlspecialchars($row['name'], ENT_QUOTES);
        }

        $sugarSmarty = new Sugar_Smarty();
        $sugarSmarty->assign('SEARCH_MODULE', $module);
        $sugarSmarty->assign('MOD', $saved_search_mod_strings);

        if (!empty($_SESSION['LastSavedView'][$module]) && (($_REQUEST['action'] == 'ListView') || ($_REQUEST['action'] == 'index'))) {
            $selectedSearch = $_SESSION['LastSavedView'][$module];
        } else {
            $selectedSearch = '';
        }

        $sugarSmarty->assign('SAVED_SEARCHES_OPTIONS', get_select_options_with_id($savedSearchArray, $selectedSearch));

        return $sugarSmarty->fetch('modules/SavedSearch/SavedSearchSelects.tpl');
    }

    public function returnSavedSearch($id, $searchFormTab = 'advanced_search', $showDiv = 'no')
    {
        global $db, $current_user, $currentModule;
        $this->retrieveSavedSearch($id);

        if (empty($_SESSION['LastSavedView'])) {
            $_SESSION['LastSavedView'] = [];
        }
        $_SESSION['LastSavedView'][$this->contents['search_module']] = $id;
        $orderBy = empty($this->contents['orderBy']) ? 'name' : $this->contents['orderBy'];
        //Reduce the params to avoid the problems caused by URL max length in IE.
        $location = 'index.php?' . http_build_query([
                'action' => 'index',
                'module' => $this->contents['search_module'],
                'saved_search_select' => $id,
                'saved_search_select_name' => $this->name,
                'orderBy' => $orderBy,
                'sortOrder' => $this->contents['sortOrder'],
                'query' => $this->contents['query'],
                'searchFormTab' => $this->contents['searchFormTab'],
                'showSSDIV' => $showDiv,
            ]);
        header("Location: $location");
    }

    protected function handleDelete($id)
    {
        $this->mark_deleted($id);
        $location = 'index.php?' . http_build_query([
                'action' => 'index',
                'module' => $_REQUEST['search_module'],
                'advanced' => $_REQUEST['advanced'],
                'query' => 'true',
                'clear_query' => 'true',
            ]);
        header("Location: $location");
    }

    /**
     * @param $prefix
     * @param bool $redirect default true
     * @param false $useRequired default false
     * @param null $id default null
     * @param $searchModuleBean
     * @return void
     */
    public function handleSave(string $prefix, bool $redirect, bool $useRequired, ?string $id, SugarBean $searchModuleBean): void
    {
        global $current_user, $timedate;

        $focus = BeanFactory::newBean('SavedSearch');
        if ($id) {
            $focus->retrieve($id);
        }

        if ($useRequired && !checkRequired($prefix, array_keys($focus->required_fields))) {
            return;
        }

        $ignored_inputs = ['PHPSESSID', 'module', 'action', 'saved_search_name', 'saved_search_select', 'advanced', 'Calls_divs', 'ACLRoles_divs'];

        $contents = $_REQUEST;
        if ($id == null) {
            $focus->name = $contents['saved_search_name'];
        }
        $focus->search_module = $contents['search_module'];

        foreach ($contents as $input => $value) {
            if (in_array($input, $ignored_inputs)) {
                unset($contents[$input]);
                continue;
            }

            //Filter date fields to ensure it is saved to DB format, but also avoid empty values
            if (!empty($value) && preg_match('/^(start_range_|end_range_|range_)?(.*?)(_advanced|_basic)$/', $input, $match)) {
                $field = $match[2];
                if (isset($searchModuleBean->field_defs[$field]['type']) && empty($searchModuleBean->field_defs[$field]['disable_num_format'])) {
                    $type = $searchModuleBean->field_defs[$field]['type'];

                    //Avoid macro values for the date types
                    if (($type == 'date' || $type == 'datetime' || $type == 'datetimecombo') && !preg_match('/^\[.*?\]$/', $value)) {
                        $db_format = $timedate->to_db_date($value, false);
                        $contents[$input] = $db_format;
                    } elseif ($type == 'int' || $type == 'currency' || $type == 'decimal' || $type == 'float') {
                        if (preg_match('/[^\d]/', $value)) {
                            require_once 'modules/Currencies/Currency.php';
                            $contents[$input] = unformat_number($value);
                            //Flag this value as having been unformatted
                            $contents[$input . '_unformatted_number'] = true;
                            //If the type is of currency and there was a currency symbol (non-digit), save the symbol
                            if ($type == 'currency' && preg_match('/^([^\d])/', $value, $match)) {
                                $contents[$input . '_currency_symbol'] = $match[1];
                            }
                        } else {
                            //unset any flags
                            if (isset($contents[$input . '_unformatted_number'])) {
                                unset($contents[$input . '_unformatted_number']);
                            }

                            if (isset($contents[$input . '_currency_symbol'])) {
                                unset($contents[$input . '_currency_symbol']);
                            }
                        }
                    }
                }
            }
        }

        $contents['advanced'] = true;

        $focus->contents = base64_encode(serialize($contents));

        $focus->assigned_user_id = $current_user->id;
        $focus->new_schema = true;

        $saved_search_id = $focus->save();

        $GLOBALS['log']->debug('Saved record with id of ' . $focus->id);
        $orderBy = empty($contents['orderBy']) ? 'name' : $contents['orderBy'];
        $search_query_params = [
            'orderBy' => $orderBy,
            'sortOrder' => $contents['sortOrder'],
            'query' => $_REQUEST['query'],
            'searchFormTab' => $_REQUEST['searchFormTab'],
            'showSSDIV' => $contents['showSSDIV'],
            'advanced' => 'true',
        ];
        if ($redirect) {
            $this->handleRedirect($focus->search_module, $search_query_params, $saved_search_id);
        }
    }

    protected function handleRedirect(string $return_module, array $search_query_params, string $saved_search_id): void
    {
        $_SESSION['LastSavedView'][$return_module] = $saved_search_id;
        $return_action = 'index';
        $ajaxLoad = empty($_REQUEST['ajax_load']) ? '' : $_REQUEST['ajax_load'];
        //Reduce the params to avoid the problems caused by URL max length in IE ( the reduced params can be get from saved search according to saved_search_id).
        $query_data = array_merge([
            'action' => $return_action,
            'module' => $return_module,
            'saved_search_select' => $saved_search_id,
            'ajax_load' => $ajaxLoad,
        ], $search_query_params);
        $location = 'index.php?' . http_build_query($query_data);
        header("Location: $location");
        die();
    }

    public function fill_in_additional_list_fields()
    {
        global $app_list_strings;
        // Fill in the assigned_user_name
        $this->search_module = $app_list_strings['moduleList'][$this->contents['search_module']];
        $this->fill_in_additional_detail_fields();
    }

    public function retrieveSavedSearch($id)
    {
        parent::retrieve($id);
        $this->contents = unserialize(base64_decode($this->contents), ['allowed_classes' => false]);
    }

    public function populateRequest()
    {

        global $timedate;

        if (isset($this->contents['search_module'])) {
            $searchModuleBean = BeanFactory::newBean($this->contents['search_module']);
        }

        foreach ($this->contents as $key => $val) {
            if ($key != 'advanced' && $key != 'module' && !strpos($key, '_ORDER_BY') && $key != 'lvso') {
                if (isset($searchModuleBean) && !empty($val) && preg_match('/^(start_range_|end_range_|range_)?(.*?)(_advanced|_basic)$/', $key, $match)) {
                    $field = $match[2];
                    if (isset($searchModuleBean->field_defs[$field]['type']) && empty($searchModuleBean->field_defs[$field]['disable_num_format'])) {
                        $type = $searchModuleBean->field_defs[$field]['type'];

                        //Avoid macro values for the date types
                        if (($type == 'date' || $type == 'datetime' || $type == 'datetimecombo') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) && !preg_match('/^\[.*?\]$/', $val)) {
                            $val = $timedate->to_display_date($val, false);
                        } elseif (($type == 'int' || $type == 'currency' || $type == 'decimal' || $type == 'float') && isset($this->contents[$key . '_unformatted_number']) && preg_match('/^\d+$/', $val)) {
                            require_once 'modules/Currencies/Currency.php';
                            $val = format_number($val);
                            if ($type == 'currency' && isset($this->contents[$key . '_currency_symbol'])) {
                                $val = $this->contents[$key . '_currency_symbol'] . $val;
                            }
                        }
                    }
                }

                $_REQUEST[$key] = $val;
                $_GET[$key] = $val;
            }
        }
    }
}

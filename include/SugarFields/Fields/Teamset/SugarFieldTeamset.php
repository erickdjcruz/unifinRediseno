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

require_once 'modules/Teams/TeamSetManager.php';

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;

/**
 * SugarFieldTeamset.php
 *
 * This class handles the processing of the new Team selection widget.
 * The main thing to note is that the getDetailViewSmarty, getEditViewSmarty and
 * getSearchViewSmarty methods are called from the cached .tpl files that are generated
 * via the MVC/Metadata framework.  The cached .tpl files include Smarty code rendered from
 * the include/SugarFields/Fields/SugarFieldTeamset/Teamset.tpl file which in turn
 * calls this file.  When the plugin function is run (see include/SugarSmarty/plugins/function.sugarvar_teamset.php),
 * it will call SugarFieldTeamset's render method.  From there, the corresponding method is invoked.
 *
 * For the MassUpdate section, there is no cached .tpl file created so the contents are rendered without
 * using the Teamset.tpl approach.
 *
 * For classic views (where PHP files use the XTemplate processing) we provide the
 * getClassicView method.  Also note, the getClassicViewQS method.  For some classic views,
 * we use this method in situations where the quick search sections need to be generated
 * separately from the widget code.
 *
 */
class SugarFieldTeamset extends SugarFieldBase
{
    public $needsSecondaryQuery = true;

    //the name of the field, defaults to team_name
    public $field_name = 'team_name';

    //reference to the smarty class
    public $smarty = null;

    //reference to the ViewSugarFieldTeamsetCollection instance
    public $view = null;

    public $params = null;

    public $fields;

    public $add_user_private_team = true;

    /**
     * @var Request
     */
    protected $request;

    /**
     * SugarFieldTeamset constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->request = InputValidation::getService();
        return parent::__construct($type);
    }

    /*
     * render
     *
     * This method is called from function.sugarvar_teamset.php and determines which view to output
     * and the appropriate html to output.  This is called at runtime.
     *
     * @param $params Array of runtime parameters
     * @param $smarty Smarty object instance used to render the widget
     */
    public function render($params, &$smarty)
    {
        $this->params = $params;
        $this->smarty = $smarty;
        $method = $this->params['displayType'];
        return $this->$method();
    }


    public function initialize()
    {

        $this->fields = $this->smarty->get_template_vars('fields');
        $team_name_vardef = $this->fields["{$this->field_name}"];
        $this->view = new ViewSugarFieldTeamsetCollection();
        $this->view->displayParams = $this->params;
        $this->view->vardef = $team_name_vardef;
        $formName = $this->params['formName'];

        if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'SubpanelCreates') || preg_match('/quickcreate/i', $formName)) {
            $this->view->action_type = 'quickcreate';
        } else {
            $this->view->action_type = strtolower($formName);
        }

        $this->view->module_dir = $this->request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');

        if ($this->view->module_dir == 'Import') {
            $this->view->module_dir = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName');
        } elseif ($this->view->action_type == 'quickcreate') {
            $this->view->module_dir = isset($_REQUEST['target_module']) ? $this->request->getValidInputRequest('target_module', 'Assert\Mvc\ModuleName') : $this->view->module_dir;
        }

        $this->view->form_name = $formName;

        //rather than retrieve the bean try to pull the values from the form
        if (!empty($this->fields['team_set_id'])) {
            if (!empty($this->fields['team_set_id']['value'])) {
                $this->view->team_set_id = $this->fields['team_set_id']['value'];
            } else {
                $this->view->team_set_id = $GLOBALS['current_user']->team_set_id;
            }
        }
        if (!empty($this->fields['acl_team_set_id'])) {
            if (!empty($this->fields['acl_team_set_id']['value'])) {
                $this->view->acl_team_set_id = $this->fields['acl_team_set_id']['value'];
            }
        }
        if (!empty($this->fields['team_id']) && !empty($this->fields['team_id']['value'])) {
            $this->view->team_id = $this->fields['team_id']['value'];
            $this->view->add_user_private_team = false;
        }

        $this->view->populate();
        $this->view->setup();
    }

    /**
     * process
     *
     * This method is used to call the view object's process method and init_tpl methods
     * We separated it from the display method so that the renderEditView, renderDetailView
     * and renderSearchView method may provide some additional functionality before the process
     * method is made to the view object.
     *
     */
    public function process()
    {
        $this->view->process();
        $this->view->init_tpl();
    }

    public function display()
    {
        echo $this->view->display();
    }


    /**
     * renderEditView
     * This method is called to display the edit view of the teams widget
     *
     */
    public function renderEditView()
    {
        $this->initialize();

        $request = InputValidation::getService();
        // GET does not always contain the ID.
        $record = $request->getValidInputRequest('record', 'Assert\Guid');
        // Draw the default selected team set in case of creation and skip for edit.
        if (!$record) {
            $this->view->acl_team_set_id = $GLOBALS['current_user']->acl_team_set_id;
            $this->view->setup();
        }

        //Get the team_arrow_value user preference and set it
        //This user preference is used to remember whether or not the display of the
        //teams widget should be collapsed or expanded
        /*
         Removing this functionality for now so as to avoid user confusion as per PM.
        global $current_user;
        $arrow_value = $current_user->getPreference('team_arrow_value');
        $this->view->displayParams['arrow'] = isset($arrow_value) ? $arrow_value : 'hide';
        */
        $keys = $this->getAccessKey($this->view->vardef, 'TEAMSET', '');
        $this->view->displayParams['accessKeySelect'] = $keys['accessKeySelect'];
        $this->view->displayParams['accessKeySelectLabel'] = $keys['accessKeySelectLabel'];
        $this->view->displayParams['accessKeySelectTitle'] = $keys['accessKeySelectTitle'];
        $this->view->displayParams['accessKeyClear'] = $keys['accessKeyClear'];
        $this->view->displayParams['accessKeyClearLabel'] = $keys['accessKeyClearLabel'];
        $this->view->displayParams['accessKeyClearTitle'] = $keys['accessKeyClearTitle'];

        $this->view->displayParams['arrow'] = 'hide';

        $this->process();
        return $this->display();
    }

    /**
     * renderDetailView
     * This method is called to display the detail view of the teams widget
     */
    public function renderDetailView()
    {
        $this->initialize();
        $this->process();
        return TeamSetManager::getFormattedTeamsFromSet($this->view, true);
    }

    /**
     * renderSearchView
     *
     */
    public function renderSearchView()
    {
        //override the field_name since this widget is shown on the advanced tab section
        $this->field_name = 'team_name_advanced';
        if (empty($this->params['formName'])) {
            $this->params['formName'] = 'search_form';
        }
        $this->initialize();
        $this->view->displayParams['formName'] = $this->params['formName'];
        if ($this->view->displayParams['formName'] == 'popup_query_form') {
            $this->view->displayParams['clearOnly'] = true;
        }
        $this->process();
        return $this->display();
    }

    /**
     * renderImportView
     *
     */
    public function renderImportView()
    {
        $this->fields = $this->smarty->get_template_vars('fields');
        $team_name_vardef = $this->fields["{$this->field_name}"];
        $this->view = new ViewSugarFieldTeamsetCollection();
        $this->view->displayParams = $this->params;
        $this->view->vardef = $team_name_vardef;
        $this->view->module_dir = $this->request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');

        if ($this->view->module_dir == 'Import') {
            $this->view->module_dir = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName');
        }

        $formName = $this->params['formName'];
        $this->view->action_type = strtolower($formName);
        $this->view->form_name = $formName;
        $this->view->populate();
        $this->view->related_module = 'Teams';
        $this->view->value_name = 'team_set_id_values';
        $this->view->vardef['name'] = 'team_name';

        if (!empty($this->view->vardef['value'])) {
            $secondaries = [];
            $primary = false;
            $json = getJSONobj();
            $vals = $json->decode($this->view->vardef['value'], true);
            $this->view->displayParams['primaryChecked'] = true;
            if (is_iterable($vals)) {
                foreach ($vals as $id => $name) {
                    if (!$primary) {
                        $this->view->bean->{$this->view->value_name} = ['primary' => ['id' => $id, 'name' => $name]];
                        $primary = true;
                        $this->view->add_user_private_team = false;
                    } else {
                        $secondaries['secondaries'][] = ['id' => $id, 'name' => $name];
                    }
                } //foreach
            }
            if (!empty($this->view->bean)) {
                $this->view->bean->{$this->view->value_name} = array_merge((array)$this->view->bean->{$this->view->value_name}, $secondaries);
            }
        }

        $this->view->skipModuleQuickSearch = true;
        $this->view->showSelectButton = false;
        $this->process();
        return $this->display();
    }

    public function initClassicView($fields, $formName = 'EditView')
    {
        $displayParams = [];
        $this->view = new ViewSugarFieldTeamsetCollection();
        if (!$this->add_user_private_team) {
            $this->view->add_user_private_team = false;
        } else {
            if (empty($_REQUEST['record'])) {
                // fixing bug #40003: Teams revert to self when Previewing a report
                // check if there are teams in POST
                $teams = self::getTeamsFromRequest($this->field_name, $_POST);
                if (empty($teams)) {
                    $this->view->acl_team_set_id = !empty($GLOBALS['current_user']->acl_team_set_id) ?
                        $GLOBALS['current_user']->acl_team_set_id : '';
                    $this->view->team_set_id = !empty($GLOBALS['current_user']->team_set_id) ? $GLOBALS['current_user']->team_set_id : '';
                    $this->view->team_id = !empty($GLOBALS['current_user']->team_id) ? $GLOBALS['current_user']->team_id : '';
                }
            }
        }
        $this->view->form_name = $formName;
        $displayParams['formName'] = $formName;
        $displayParams['primaryChecked'] = true;
        $this->view->displayParams = $displayParams;
        $this->view->vardef = $fields['team_name'];
        $this->request = InputValidation::getService();
        $this->view->module_dir = $this->request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');
        $this->view->action_type = strtolower($formName);
        $this->view->populate();
        $this->view->setup();
        $this->view->process();
        $this->view->init_tpl();
    }

    /**
     * getClassicView
     *
     * @param $field Array of the SugarBean's field definitions
     * @param $formName String value of the form name to insert team set field to, default is EditView
     */
    public function getClassicView($fields = [], $formName = 'EditView')
    {
        if (is_null($this->view)) {
            $this->initClassicView($fields, $formName);
        }
        return $this->view->display();
    }

    public function getClassicViewQS()
    {
        return $this->view->createQuickSearchCode(false);
    }

    /**
     * getEditViewSmarty
     * Returns the Smarty code portion to render the edit view of the field
     * @param $parentFieldArray String value of the Smarty variable name that contains the fields Array
     * @param $vardef Array of the field definition entry
     * @param $diaplayParams Array of optional display parameters passed in from metadata
     * @param $tabindex Integer value of the tabindex that should be assigned to the HTML output for this field
     * @param $searchView boolean value indicating whether or not to use the search form rendering
     *
     */
    public function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex, $searchView = false)
    {
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        $this->ss->assign('renderView', 'renderEditView');
        return $this->fetch($this->findTemplate('Teamset'));
    }

    /**
     * getDetailViewSmarty
     * Override for the SugarFieldCollection class to set the vardef name from team_name to team_set_id
     */
    public function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex, $searchView = false)
    {
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        $this->ss->assign('renderView', 'renderDetailView');
        return $this->fetch($this->findTemplate('Teamset'));
    }


    /**
     * getMassUpdateViewSmarty
     *
     */
    public function getMassUpdateViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex, $searchView = false)
    {
        $_REQUEST['bean_id'] = $_REQUEST['record'] ?? '';
        $this->view = new MassUpdateSugarFieldTeamsetCollection();
        $displayParams['formName'] = 'MassUpdate';
        $this->view->displayParams = $displayParams;
        $this->view->vardef = $vardef;
        $this->request = InputValidation::getService();
        $this->view->module_dir = $this->request->getValidInputRequest('module', 'Assert\Mvc\ModuleName');

        $this->view->acl_team_set_id = !empty($GLOBALS['current_user']->acl_team_set_id) ?
            $GLOBALS['current_user']->acl_team_set_id : '';
        $this->view->team_set_id = !empty($GLOBALS['current_user']->team_set_id) ? $GLOBALS['current_user']->team_set_id : '';
        $this->view->team_id = !empty($GLOBALS['current_user']->team_id) ? $GLOBALS['current_user']->team_id : '';

        $this->view->populate();
        $this->view->setup();
        $this->view->process();
        $this->view->init_tpl();
        return $this->view->display();
    }

    public function getPopupViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        $displayParams['formName'] = 'popup_query_form';
        return $this->getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
    }

    public function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        if (empty($displayParams['formName'])) {
            $displayParams['formName'] = 'search_form';
        }
        if (!empty($this->view) && !empty($displayParams)) {
            $this->view->displayParams = $displayParams;
        }
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        $this->ss->assign('renderView', 'renderSearchView');
        return $this->fetch($this->findTemplate('Teamset'));
    }

    public function getListViewSmarty($parentFieldArray, $vardef, $displayParams, $col)
    {
        $tabindex = 1;
        $parentFieldArray = $this->setupFieldArray($parentFieldArray, $vardef);
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        $this->ss->assign('rowData', $parentFieldArray);
        $this->ss->assign('col', $vardef['name']);
        return $this->fetch($this->findTemplate('TeamsetListView'));
    }

    public function getImportViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        $this->ss->assign('renderView', 'renderImportView');
        return $this->fetch($this->findTemplate('Teamset'));
    }

    /**
     * getTeamIdSearchField
     *
     */
    public function getTeamIdSearchField($field)
    {

        if (isset($_REQUEST[$field])) {
            $primary_team_index = $_REQUEST[$field];
            if (preg_match('/(team_name_.*?_collection)$/', $field, $matches)) {
                $field = 'id_' . $matches[1];
            }
            $primary_team_id = $_REQUEST["{$field}_{$primary_team_index}"];
            return ['query_type' => 'format',
                'value' => $primary_team_id,
                'operator' => '=',
                'db_field' => ['team_id']];
        }
    }

    /**
     * getTeamSetIdSearchField
     *
     * @param $field
     * @return unknown_type
     */
    public function getTeamSetIdSearchField($field, $type = 'any', $teams = [], $params = [])
    {

        if (empty($teams)) {
            $teams = self::getTeamsFromRequest($field, $params);
        }
        $teams = array_keys($teams);
        $team_count = safeCount($teams);

        if (!empty($_REQUEST["{$field}_type"])) {
            $type = $_REQUEST["{$field}_type"];
        }

        if ($type == 'exact') {
            //Calculate the team_md5 value
            sort($teams, SORT_STRING);

            $team_md5 = '';
            $uniqueTeams = [];
            foreach ($teams as $team_id) {
                if (!in_array($team_id, $uniqueTeams)) {
                    $team_md5 .= $team_id;
                    $uniqueTeams[] = $team_id;
                }
            }

            $team_md5 = md5($team_md5);

            return ['query_type' => 'format',
                'value' => $team_md5,
                'operator' => 'subquery',
                'subquery' => "SELECT id FROM team_sets WHERE team_md5 = '{0}'",
                'db_field' => ['team_set_id']];
        } elseif ($type == 'all' && $team_count > 1) {
            return ['query_type' => 'format',
                'value' => $teams,
                'subquery' => "SELECT team_set_id FROM team_sets_teams WHERE team_id IN ({0}) GROUP BY team_set_id HAVING COUNT(team_set_id) = {$team_count}",
                'operator' => 'subquery',
                'db_field' => ['team_set_id']];
        } else {
            return ['query_type' => 'format',
                'value' => $teams,
                'operator' => 'subquery',
                'subquery' => 'SELECT team_set_id FROM team_sets_teams WHERE team_id IN ({0})',
                'db_field' => ['team_set_id']];
        }
    }

    /**
     * Obtain the set of teams selected from the REQUEST
     *
     * @param string $field the name of the field on the UI
     * @return array        array of team ids
     */
    public static function getTeamsFromRequest($field, $vars = [])
    {
        if (empty($vars)) {
            $vars = $_REQUEST;
        }
        $team_ids = [];
        if (is_array($vars)) {
            foreach ($vars as $name => $value) {
                if (!empty($value)) {
                    if (strpos($name, $field . '_collection_') !== false) {
                        $num = substr($name, strrpos($name, '_') + 1); //Get everything after the last "_" character
                        if (is_numeric($num)) {
                            settype($num, 'int');
                            if ($name == 'id_' . $field . '_collection_' . $num) {
                                $team_ids[$value] = $vars[$field . '_collection_' . $num];
                            }
                        }
                    }
                }
            }
        }

        return $team_ids;
    }

    /**
     * Given the REQUEST, return the selected primary team id, if none found, then return ''
     *
     * @param string $field the name of the field on the UI
     * @param array $vars array of REQUEST params to look at
     * @return string           the primary team id or empty
     */
    public static function getPrimaryTeamIdFromRequest($field, $vars)
    {
        if (isset($vars['primary_' . $field . '_collection'])) {
            $primary = $vars['primary_' . $field . '_collection'];
            $key = 'id_' . $field . '_collection_' . $primary;
            return $vars[$key];
        }
        return '';
    }


    /**
     * Given the REQUEST, return the Team-Based selected team ids, if none found, then return array()
     *
     * @param string $field the name of the field on the UI
     * @param array $vars array of REQUEST params to look at
     * @return array            array of Team-Based selected team ids or empty array
     */
    public static function getSelectedTeamIdsFromRequest($field, $vars)
    {
        $selectedTeamIds = [];
        foreach (array_keys($vars) as $key) {
            if (strpos($key, 'selected_' . $field . '_collection_') !== false) {
                $num = substr($key, strrpos($key, '_') + 1);
                $prefixSelected = 'selected';
                if (isset($vars['selected_' . $field . '_collection_' . $num]) &&
                    !empty($vars['id_' . $field . '_collection_' . $num])
                ) {
                    $prefixSelected = 'id';
                }
                $selectedTeamIds[] = $vars[$prefixSelected . '_' . $field . '_collection_' . $num];
            }
        }
        return $selectedTeamIds;
    }

    /**
     * Given the bean and the REQUEST attempt to save the selected team ids to the bean
     *
     * @param SugarBean $bean
     * @param unknown_type $params
     * @param string $field
     * @param unknown_type $properties
     */
    public function save($bean, $params, $field, $properties, $prefix = '')
    {
        $save = false;
        $value_name = $field . '_values';

        $team_ids = [];
        $teams = self::getTeamsFromRequest($field, $params);
        $team_ids = array_keys($teams);

        $primaryTeamId = self::getPrimaryTeamIdFromRequest($field, $params);
        //if the team id here is blank then let's not set it as the team_id on the bean
        if (!empty($primaryTeamId)) {
            $bean->team_id = $primaryTeamId;
        }

        $additionalValues = [];
        if (!empty($team_ids)) {
            $selectedTeamIds = static::getSelectedTeamIdsFromRequest($field, $params);
            if (!empty($selectedTeamIds)) {
                $additionalValues['selected_teams'] = $selectedTeamIds;
            } else {
                $bean->acl_team_set_id = '';
            }
        }

        if (!empty($team_ids)) {
            $bean->load_relationship('teams');
            $method = 'replace';
            if (!empty($params[$field . '_type'])) {
                $method = $params[$field . '_type'];
            }

            $bean->teams->$method($team_ids, $additionalValues, false);
        }
    }

    /**
     * @see SugarFieldBase::importSanitize()
     */
    public function importSanitize(
        $value,
        $vardef,
        $focus,
        ImportFieldSanitize $settings
    ) {


        static $teamBean;
        if (!isset($teamBean)) {
            $teamBean = BeanFactory::newBean('Teams');
        }

        if (!is_array($value)) {
            // We will need to break it apart to put test it.
            $value = explode(',', $value);
            if (!is_array($value)) {
                $value = [$value];
            }
        }
        $team_ids = [];
        foreach ($value as $val) {
            //1) check if this is a team id
            $val = trim($val);
            if (empty($val)) {
                continue;
            }
            if (!$this->_isTeamId($val, 'Teams')) {
                //2) check if it is a team name
                $fieldname = $vardef['rname'];
                $teamid = $teamBean->retrieve_team_id($val);
                if ($teamid !== false) {
                    $team_ids[] = $teamid;
                    continue;
                } else {
                    continue;
                }
                //3) ok we did not find the id, so we need to create a team.
                $newbean = BeanFactory::newBean('Teams');
                if ($newbean->ACLAccess('save')) {
                    $newbean->{$vardef['rname']} = $val;

                    if (!isset($focus->assigned_user_id) || $focus->assigned_user_id == '') {
                        $newbean->assigned_user_id = $GLOBALS['current_user']->id;
                    } else {
                        $newbean->assigned_user_id = $focus->assigned_user_id;
                    }

                    if (!isset($focus->modified_user_id) || $focus->modified_user_id == '') {
                        $newbean->modified_user_id = $GLOBALS['current_user']->id;
                    } else {
                        $newbean->modified_user_id = $focus->modified_user_id;
                    }

                    $newbean->save(false);
                    $team_ids[] = $newbean->id;
                }
            } else {
                $team_ids[] = $val;
            }
        }

        if (!empty($team_ids)) {
            if ($vardef['name'] == $this->field_name) {
                $focus->load_relationship('teams');
                $focus->teams->replace($team_ids, [], true);
                $focus->team_id = $team_ids[0];
            } else {
                $teamSet = BeanFactory::newBean('TeamSets');
                $selectedTeamSet = Team::$nameTeamsetMapping[$vardef['name']];
                $focus->$selectedTeamSet = $teamSet->addTeams($team_ids);
            }
            // Set default team only for team_set_id.
        } elseif ($vardef['name'] == $this->field_name) {
            $focus->setDefaultTeam();
        }
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    private function _isTeamId($value, $module)
    {
        $checkfocus = BeanFactory::newBean($module);
        if ($checkfocus && is_null($checkfocus->retrieve($value))) {
            return false;
        }
        return true;
    }

    // Here are the functions used by the REST API

    /**
     * This function will pull out the various teams in this teamset and return them in a collection
     *
     * {@inheritDoc}
     */
    public function apiFormatField(
        array       &$data,
        SugarBean   $bean,
        array       $args,
        $fieldName,
        $properties,
        array       $fieldList = null,
        ServiceBase $service = null
    ) {

        $this->ensureApiFormatFieldArguments($fieldList, $service);

        if ($fieldName !== $this->field_name) {
            return;
        }

        $selectedTeamIds = array_map(function ($el) {
            return $el['id'];
        }, TeamSetManager::getTeamsFromSet($bean->acl_team_set_id));

        if (empty($bean->teamList)) {
            $teamList = TeamSetManager::getUnformattedTeamsFromSet($bean->team_set_id);
            if (!is_array($teamList)) {
                // No teams on this bean yet.
                $teamList = [];
            }
        } else {
            $teamList = $bean->teamList;
        }

        foreach ($teamList as $idx => $team) {
            // Check team name as well for cases in which team_name is selected
            // but team_id is not
            if ($team['id'] == $bean->team_id || $team['name'] == $bean->team_name) {
                $teamList[$idx]['primary'] = true;
            } else {
                $teamList[$idx]['primary'] = false;
            }
            $teamList[$idx]['selected'] = in_array($team['id'], $selectedTeamIds) ? true : false;
        }
        $data[$fieldName] = $teamList;

        // These are just confusing to people on the other side of the API
        unset($data['acl_team_set_id']);
        unset($data['team_set_id']);
        unset($data['team_id']);
    }

    /**
     * This function handles turning the API's version of a teamset into what we actually store
     * @param SugarBean $bean - the bean performing the save
     * @param array $params - an array of paramester relevant to the save, which will be an array passed up to the API
     * @param string $fieldName - The name of the field to save (the vardef name, not the form element name)
     * @param array $properties - Any properties for this field
     */
    public function apiSave(SugarBean $bean, array $params, $fieldName, $properties)
    {
        // Find the primary team id, or the first one, if nothing is set to primary
        $teamList = $params[$fieldName];
        $ret = $this->fixupTeamList($teamList);
        $teamIds = $ret['teamIds'];
        $selectedTeamIds = $ret['selectedTeamIds'];
        $primaryTeamId = $ret['primaryTeamId'];

        if (safeCount($teamIds) == 0) {
            // There are no teams being set, set the defaults and move on
            $bean->setDefaultTeam();
            return;
        }

        if (empty($primaryTeamId)) {
            // They didn't specify a primary team, so I'm just going to set it to the first one
            $primaryTeamId = $teamIds[0];
        }

        $bean->team_id = $primaryTeamId;

        if ($bean->load_relationship('teams')) {
            $bean->teams->replace($teamIds, [], false);
        }

        // Handle MassUpdate "replace". (see MassUpdateApi::handleTypeAdjustments())
        if (!empty($selectedTeamIds)) {
            $teamSet = BeanFactory::newBean('TeamSets');
            $bean->acl_team_set_id = $teamSet->addTeams($selectedTeamIds);
        } else {
            $bean->acl_team_set_id = '';
        }
    }

    public function apiMassUpdate(SugarBean $bean, array $params, $fieldName, $properties)
    {
        // Check if we are replacing, if so, just use the normal save
        if (isset($params[$fieldName . '_type']) && $params[$fieldName . '_type'] === 'replace') {
            return $this->apiSave($bean, $params, $fieldName, $properties);
        }

        $teamList = $params[$fieldName];
        $ret = $this->fixupTeamList($teamList);
        $teamIds = $ret['teamIds'];
        $primaryTeamId = $ret['primaryTeamId'];
        $selectedTeamIds = $ret['selectedTeamIds'];

        if (isset($primaryTeamId)) {
            $bean->team_id = $primaryTeamId;
        }
        $bean->load_relationship('teams');
        $bean->teams->add($teamIds, [], false);

        // Handle "add" case. (see MassUpdateApi::handleTypeAdjustments())
        if (!empty($selectedTeamIds)) {
            $teamSet = BeanFactory::newBean('TeamSets');
            $currentSelectedIds = $teamSet->getTeamIds($bean->acl_team_set_id);
            $bean->acl_team_set_id = $teamSet->addTeams(array_unique(
                array_merge($currentSelectedIds, $selectedTeamIds)
            ));
        }
    }

    protected function fixupTeamList($teamList)
    {
        $primaryTeamId = null;
        if (!is_array($teamList)) {
            $teamList = [];
        }
        $teamIds = [];
        $selectedTeamIds = [];
        foreach ($teamList as $idx => $team) {
            //For empty array
            if (!isset($team['id'])) {
                continue;
            }
            if (isset($team['primary']) && $team['primary'] == true) {
                $primaryTeamId = $team['id'];
            }
            if (!empty($team['selected'])) {
                $selectedTeamIds[] = $team['id'];
            }
            $teamIds[] = $team['id'];
        }

        return [
            'teamIds' => $teamIds,
            'selectedTeamIds' => $selectedTeamIds,
            'primaryTeamId' => $primaryTeamId,
        ];
    }

    /**
     * Run a secondary query and populate the results into the array of beans
     *
     * @overrides SugarFieldBase::runSecondaryQuery
     */
    public function runSecondaryQuery($fieldName, SugarBean $seed, array $beans)
    {
        if (empty($beans)) {
            return;
        }

        $teamsetToBean = [];
        foreach ($beans as $bean) {
            if (empty($bean->team_set_id)) {
                continue;
            }
            $teamsetToBean[$bean->team_set_id][] = $bean->id;
        }

        if (!$teamsetToBean) {
            return;
        }

        $tsb = BeanFactory::newBean('TeamSets');

        $query = new SugarQuery();
        $query->from($tsb);
        $query->join('teams', ['alias' => 'teams']);
        $query->select(
            ['id',
                ['teams.id', 'team_id'],
                ['teams.name', 'name'],
                ['teams.name_2', 'name_2'],
            ]
        );
        $query->where()->in('id', array_keys($teamsetToBean));

        $rows = $query->execute();

        $teamsets = [];
        foreach ($rows as $row) {
            $row = $tsb->convertRow($row);
            $team = ['id' => $row['team_id']];
            $team['name'] = !empty($row['name']) ? $row['name'] : '';
            $team['name_2'] = !empty($row['name_2']) ? $row['name_2'] : '';
            $teamsets[$row['id']][] = $team;
        }

        foreach ($teamsetToBean as $teamSetId => $beansWithTeam) {
            foreach ($beansWithTeam as $beanId) {
                if (isset($teamsets[$teamSetId])) {
                    $beans[$beanId]->teamList = $teamsets[$teamSetId];
                }
            }
        }
    }
}

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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\WebHook;

class CustomPostBodyVariables
{
    private $webHook;

    public function __construct(\CJ_WebHook $webHook)
    {
        $this->webHook = $webHook;
    }

    /**
     * Get all the variables in an array and find out the relevant modules then
     * it will return the array of all the necessary information belonging to module
     * @param array $bodyVariables
     * @param array $data
     */
    public function parseModule(array $bodyVariables, array $data)
    {
        if (empty($bodyVariables) || empty($data)) {
            return;
        }

        //This array will contain the "module label" as a key and array of information related to the
        //module as a value.
        $moduleFields = [];

        foreach ($bodyVariables as $key => $value) {
            $textVariable = $value[0];
            $originalVariable = $textVariable;
            $textVariable = trim($textVariable, '{}');  //remove the brackets
            if (substr($textVariable, 0, 1) == '$') {  //It should be Smarty compatible
                $textVariable = ltrim($textVariable, '$');  //remove the extra characters

                if (strpos($textVariable, '.') !== false) { //Variable belongs to some module
                    $moduleInfo = explode('.', $textVariable);
                    $info = $this->getVariablesCompleteInfo($moduleInfo, $data, $originalVariable);
                } else { //Independent variable
                    if ($textVariable === 'site_url') {
                        $value = \SugarConfig::getInstance()->get('site_url');
                        $info = ['module' => '',
                            'field' => $textVariable,
                            'field_value' => $value,
                            'original_variable' => $originalVariable];
                    }
                }
                array_push($moduleFields, $info);
            }
        }
        return $moduleFields;
    }

    /**
     * it will return the array of all the necessary information belonging to module
     * @param array $moduleInfo
     * @param array $data
     * @param string $originalVariable
     *
     * @return array
     */
    private function getVariablesCompleteInfo(array $moduleInfo, array $data, string $originalVariable)
    {
        $info = null;
        //list of all the modules which user can use.
        $enabledModules = ['parent', 'journey', 'stage', 'activity', 'current_user', 'assigned_user'];
        $moduleName = $moduleInfo[0];
        $fieldName = $moduleInfo[1];

        if (in_array($moduleName, $enabledModules)) { //Mentioned module is valid
            switch ($moduleName) {
                case 'parent':
                    $info = ['module' => $data['parent_module'],
                        'field' => $fieldName,
                        'field_value' => $data['parent'][$fieldName] ?? $this->getRelateValueForModules($data['parent_module'], $fieldName, $originalVariable, $data['parent']['id']),
                        'original_variable' => $originalVariable,
                        'id' => $data['parent']['id']];
                    break;
                case 'journey':
                    $info = ['module' => 'DRI_Workflows',
                        'field' => $fieldName,
                        'field_value' => $data['journey'][$fieldName] ?? $originalVariable,
                        'original_variable' => $originalVariable,
                        'id' => $data['journey']['id']];
                    break;
                case 'current_user':
                case 'assigned_user':
                    $info = $this->handleUser($data, $moduleName, $fieldName, $originalVariable);
                    break;
                case 'stage':
                    $info = ['module' => 'DRI_SubWorkflows',
                        'field' => $fieldName,
                        'field_value' => $data['stage'][$fieldName] ?? $this->getRelateValueForStage('DRI_SubWorkflows', $fieldName, $originalVariable, $data['journey']['name']),
                        'original_variable' => $originalVariable];
                    break;
                case 'activity':
                    $info = ['module' => '',
                        'field' => $fieldName,
                        'field_value' => $data['activity'][$fieldName] ?? $originalVariable,
                        'original_variable' => $originalVariable];
                    break;
            }
        }
        return $info;
    }

    /**
     * Handles the variables of Users Module
     * @param array $data
     * @param string $moduleName
     * @param string $fieldName
     * @param string $originalVariable
     */
    private function handleUser(array $data, string $moduleName, string $fieldName, string $originalVariable)
    {
        switch ($moduleName) {
            case 'current_user':
                return ['module' => 'Users',
                    'field' => $fieldName,
                    'field_value' => $GLOBALS['current_user']->{$fieldName},
                    'original_variable' => $originalVariable];
                break;
            case 'assigned_user':
                $assigned_user_id = $this->getAssignedUserId($this->webHook->parent_type, $data);
                if (!empty($assigned_user_id)) {
                    return $this->getUserValue($assigned_user_id, $fieldName, $originalVariable);
                }
                break;
        }
    }

    /**
     * Get the field value of the user
     * @param string $id
     * @param string $fieldName
     * @param string $originalVariable
     */
    private function getUserValue(string $id, string $fieldName, string $originalVariable)
    {
        $userBean = \BeanFactory::retrieveBean('Users', $id);

        if (!empty($userBean->id)) {
            return ['module' => 'Users',
                'field' => $fieldName,
                'field_value' => (property_exists($userBean, $fieldName)) ? $userBean->{$fieldName} : $originalVariable,
                'original_variable' => $originalVariable,
                'id' => $id];
        }
    }

    /**
     * Returns the assigned_user_id of the related stage or activity
     *
     * @param string $webHookParentType
     * @param array $data
     * @return string
     */
    private function getAssignedUserId(string $webHookParentType, array $data)
    {
        if ($webHookParentType === 'DRI_Workflow_Templates') {
            return $data['journey']['assigned_user_id'];
        } elseif ($webHookParentType === 'DRI_SubWorkflow_Templates') {
            return $data['stage']['assigned_user_id'];
        } elseif ($webHookParentType === 'DRI_Workflow_Task_Templates') {
            return $data['activity']['assigned_user_id'];
        }
    }

    /**
     * If field type is relate then return Single Module Name otherwise return false
     * @param string $moduleName
     * @param string $fieldName
     * @return bool
     */
    private function typeIsRelate(string $moduleName, string $fieldName)
    {
        if (empty($moduleName) || empty($fieldName)) {
            return false;
        }

        global $dictionary, $app_list_strings;

        if ($moduleName === 'DRI_SubWorkflows') {
            $singleModuleName = 'DRI_SubWorkflow';
        } else {
            $singleModuleName = $app_list_strings['moduleListSingular'][$moduleName];
        }
        $type = $dictionary[$singleModuleName]['fields'][$fieldName]['type'];

        if ($type === 'relate') {
            return $singleModuleName;
        }

        return false;
    }

    /**
     * For related journey of stage if field is not empty then return journey name otherwise
     * return original variable
     * @param string $moduleName
     * @param string $fieldName
     * @param string $originalVar
     * @param string $journeyName
     * @return string
     */
    private function getRelateValueForStage(string $moduleName, string $fieldName, string $originalVar, string $journeyName)
    {
        $singleModuleName = $this->typeIsRelate($moduleName, $fieldName);

        if ($singleModuleName === 'DRI_SubWorkflow' && $fieldName === 'dri_workflow_nam') {
            return $journeyName;
        }

        return $originalVar;
    }

    /**
     * Returns relate field value if exist otherwise returns variable as it is
     * @param string $moduleName
     * @param string $fieldName
     * @param string $originalVar
     * @param string $id
     * @return string
     */
    private function getRelateValueForModules(string $moduleName, string $fieldName, string $originalVar, string $id)
    {
        if (empty($moduleName) || empty($fieldName)) {
            return $originalVar;
        }

        global $dictionary, $app_list_strings;

        $singleModuleName = $this->typeIsRelate($moduleName, $fieldName);
        if (empty($singleModuleName)) {
            return $originalVar;
        }

        $joinName = $dictionary[$singleModuleName]['fields'][$fieldName]['join_name'];

        if (!empty($joinName)) {
            $SugarQuery = new \SugarQuery();
            $SugarQuery->from(\BeanFactory::newBean($moduleName));
            $relate = $SugarQuery->join($joinName)->joinName();
            $SugarQuery->select(["$relate.name"]);
            $SugarQuery->where()
                ->equals('deleted', 0)
                ->equals('id', $id)
                ->equals("$relate.deleted", 0)
                ->equals("$relate.contact_id", $id);
            $data = $SugarQuery->execute();

            return $data[0]['name'];
        }

        return $originalVar;
    }
}

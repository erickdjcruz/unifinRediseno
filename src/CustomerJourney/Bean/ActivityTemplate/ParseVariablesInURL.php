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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate;

class ParseVariablesInURL
{
    /**
     * Get all the variables in an array and find out the relevant modules then
     * it will return the array of all the necessary information belonging to module
     * @param array $bodyVariables
     * @param array $data
     */
    public function parseModule($bodyVariables, $data)
    {
        $parentBean = null;
        if (empty($bodyVariables) || empty($data)) {
            return;
        }

        //Create Parent Bean
        if (!empty($data->parent_type) && !empty($data->parent_id)) {
            $parentBean = \BeanFactory::retrieveBean($data->parent_type, $data->parent_id);
        }

        //Create Stage Bean
        if (!empty($data->dri_subworkflow_id)) {
            $stageBean = \BeanFactory::retrieveBean('DRI_SubWorkflows', $data->dri_subworkflow_id);
            if (!empty($stageBean->id)) {
                $journeyBean = \DRI_Workflow::getById($stageBean->dri_workflow_id);
                $journeyBean->name = $parentBean->name . ' - ' . $journeyBean->name;
            }
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
                    $info = $this->getVariablesCompleteInfo($moduleInfo, $data, $originalVariable, $parentBean, $journeyBean, $stageBean);
                } else { //Independent variable
                    if ($textVariable == 'site_url') {
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
     * @param \SugarBean $parentBean
     * @param \SugarBean $journeyBean
     * @param \SugarBean $stageBean
     *
     * @return array
     */
    private function getVariablesCompleteInfo(array $moduleInfo, array $data, string $originalVariable, \SugarBean $parentBean, \SugarBean $journeyBean, \SugarBean $stageBean)
    {
        //list of all the modules which user can use.
        $modules = ['parent', 'journey', 'stage', 'activity', 'current_user'];
        $moduleName = $moduleInfo[0];
        $fieldName = $moduleInfo[1];

        $info = [];
        if (in_array($moduleName, $modules)) { //Mentioned module is valid
            switch ($moduleName) {
                case 'parent':
                    $info = ['module' => '',
                        'field' => $fieldName,
                        'field_value' => $parentBean->{$fieldName},
                        'original_variable' => $originalVariable];
                    break;
                case 'journey':
                    $info = ['module' => 'DRI_Workflows',
                        'field' => $fieldName,
                        'field_value' => $journeyBean->{$fieldName} ?? '',
                        'original_variable' => $originalVariable];
                    break;
                case 'current_user':
                    $info = ['module' => 'Users',
                        'field' => $fieldName,
                        'field_value' => $GLOBALS['current_user']->{$fieldName},
                        'original_variable' => $originalVariable];
                    break;
                case 'stage':
                    $info = ['module' => 'DRI_SubWorkflows',
                        'field' => $fieldName,
                        'field_value' => $stageBean->{$fieldName} ?? '',
                        'original_variable' => $originalVariable];
                    break;
                case 'activity':
                    $info = ['module' => '',
                        'field' => $fieldName,
                        'field_value' => $data->{$fieldName} ?? '',
                        'original_variable' => $originalVariable];
                    break;
            }
        }
        return $info;
    }
}

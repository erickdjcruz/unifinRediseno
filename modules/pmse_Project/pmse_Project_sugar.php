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

use Sugarcrm\Sugarcrm\ProcessManager;

class pmse_Project_sugar extends Basic
{
    public $new_schema = true;
    public $module_name = 'pmse_Project';
    public $module_dir = 'pmse_Project';
    public $object_name = 'pmse_Project';
    public $table_name = 'pmse_project';
    public $importable = false;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $activities;
    public $team_id;
    public $team_set_id;
    public $team_count;
    public $team_name;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $prj_uid;
    public $prj_target_namespace;
    public $prj_expression_language;
    public $prj_type_language;
    public $prj_exporter;
    public $prj_exporter_version;
    public $prj_author;
    public $prj_author_version;
    public $prj_original_source;
    public $prj_status;
    public $prj_module;


    public function __construct()
    {
        parent::__construct();
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Save related beans so that data is consistent between tables
     */
    public function saveRelatedBeans()
    {
        //Create a Diagram row
        $diagramBean = BeanFactory::newBean('pmse_BpmnDiagram')
            ->retrieve_by_string_fields(['prj_id' => $this->id]);

        if (empty($diagramBean)) {
            $diagramBean = BeanFactory::newBean('pmse_BpmnDiagram');
            $diagramBean->dia_uid = PMSEEngineUtils::generateUniqueID();
        }
        $diagramBean->name = $this->name;
        $diagramBean->description = $this->description;
        $diagramBean->assigned_user_id = $this->assigned_user_id;
        $diagramBean->prj_id = $this->id;
        $dia_id = $diagramBean->save();

        //Create a Process row
        $processBean = BeanFactory::newBean('pmse_BpmnProcess')
            ->retrieve_by_string_fields(['prj_id' => $this->id]);

        if (empty($processBean)) {
            $processBean = BeanFactory::newBean('pmse_BpmnProcess');
            $processBean->pro_uid = PMSEEngineUtils::generateUniqueID();
        }
        $processBean->name = $this->name;
        $processBean->description = $this->description;
        $processBean->assigned_user_id = $this->assigned_user_id;
        $processBean->prj_id = $this->id;
        $processBean->dia_id = $dia_id;
        $pro_id = $processBean->save();

        //Create a ProcessDefinition row
        $processDefinitionBean = BeanFactory::newBean('pmse_BpmProcessDefinition')
            ->retrieve_by_string_fields(['prj_id' => $this->id]);

        if (empty($processDefinitionBean)) {
            $processDefinitionBean = BeanFactory::newBean('pmse_BpmProcessDefinition');
            $processDefinitionBean->id = $pro_id;
            $processDefinitionBean->new_with_id = true;
        }
        $processDefinitionBean->prj_id = $this->id;
        $processDefinitionBean->pro_module = $this->prj_module;
        $processDefinitionBean->pro_status = $this->prj_status;
        $processDefinitionBean->assigned_user_id = $this->assigned_user_id;
        $processDefinitionBean->save();

        $relDepStatus = $this->prj_status == 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        while ($relatedDepBean = BeanFactory::newBean('pmse_BpmRelatedDependency')
            ->retrieve_by_string_fields(['pro_id' => $pro_id, 'pro_status' => $relDepStatus])
        ) {
            $relatedDepBean->pro_status = $this->prj_status;
            $relatedDepBean->save();
        }

        $keysArray = ['prj_id' => $this->id, 'pro_id' => $pro_id];
        $dynaF = BeanFactory::newBean('pmse_BpmDynaForm')
            ->retrieve_by_string_fields(['prj_id' => $this->id, 'pro_id' => $pro_id, 'name' => 'Default']);
        $editDyna = !empty($dynaF);
        $dynaForm = ProcessManager\Factory::getPMSEObject('PMSEDynaForm');
        $dynaForm->generateDefaultDynaform($processDefinitionBean->pro_module, $keysArray, $editDyna);
    }
}

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


$wrapperPath = 'modules/pmse_Project/clients/base/api/wrappers/';
require_once $wrapperPath . 'PMSEProjectWrapper.php';
require_once $wrapperPath . 'PMSECrmDataWrapper.php';
require_once $wrapperPath . 'PMSEActivityDefinitionWrapper.php';
require_once $wrapperPath . 'PMSEEventDefinitionWrapper.php';
require_once $wrapperPath . 'PMSEGatewayDefinitionWrapper.php';
require_once $wrapperPath . 'PMSEDynaForm.php';
require_once $wrapperPath . 'PMSEObservers/PMSEEventObserver.php';
require_once $wrapperPath . 'PMSEObservers/PMSEProcessObserver.php';

$enginePath = 'modules/pmse_Inbox/engine/';
require_once $enginePath . 'PMSEProjectImporter.php';
require_once $enginePath . 'PMSEProjectExporter.php';
require_once $enginePath . 'PMSELogger.php';

use Sugarcrm\Sugarcrm\ProcessManager;

class PMSEProjectApi extends ModuleApi
{
    /**
     * PMSEProjectWrapper object
     * @var PMSEProjectWrapper
     */
    protected $projectWrapper;

    /**
     * PMSECrmDataWrapper object
     * @var PMSECrmDataWrapper
     */
    protected $crmDataWrapper;

    /**
     * PMSEActivityDefinitionWrapper object
     * @var PMSEActivityDefinitionWrapper
     */
    protected $activityDefinitionWrapper;

    /**
     * PMSEEventDefinitionWrapper object
     * @var PMSEEventDefinitionWrapper
     */
    protected $eventDefinitionWrapper;

    /**
     * PMSEGatewayDefinitionWrapper object
     * @var PMSEGatewayDefinitionWrapper
     */
    protected $gatewayDefinitionWrapper;

    public function __construct()
    {
        parent::__construct();
        $this->projectWrapper = ProcessManager\Factory::getPMSEObject('PMSEProjectWrapper');
        $this->crmDataWrapper = ProcessManager\Factory::getPMSEObject('PMSECrmDataWrapper');
        $this->activityDefinitionWrapper = ProcessManager\Factory::getPMSEObject('PMSEActivityDefinitionWrapper');
        $this->eventDefinitionWrapper = ProcessManager\Factory::getPMSEObject('PMSEEventDefinitionWrapper');
        $this->gatewayDefinitionWrapper = ProcessManager\Factory::getPMSEObject('PMSEGatewayDefinitionWrapper');
    }

    /**
     *
     * @return type
     */
    public function registerApiRest()
    {
        return [
            'readCustomProject' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'project', '?'],
                'pathVars' => ['module', 'customAction', 'record'],
                'method' => 'retrieveCustomProject',
                'acl' => 'view',
                'shortHelp' => 'Retrieves the schema data to be used by the Process Definition designer',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_project_get_help.html',
            ],
            'updateCustomProject' => [
                'reqType' => 'PUT',
                'path' => ['pmse_Project', 'project', '?'],
                'pathVars' => ['module', 'customAction', 'record'],
                'method' => 'updateCustomProject',
                'acl' => 'create',
                'shortHelp' => 'Updates the schema data from the Process Definition designer',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_project_put_help.html',
            ],
            'readBRFields' => [
                'reqType' => 'GET',
                'path' => ['ProcessBusinessRules', 'fields', '?'],
                'pathVars' => ['module', 'data', 'filter'],
                'method' => 'getBRFields',
                'acl' => 'view',
//                'shortHelp' => 'Returns information about Fields to be exposed in the Business Rules designer.',
            ],
            'readCrmData' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'CrmData', '?', '?'],
                'pathVars' => ['module', '', 'data', 'filter'],
                'method' => 'getCrmData',
                'acl' => 'view',
                'shortHelp' => 'Retrieves information about Fields, Modules, Users, Roles, etc.',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_crm_data_get_help.html',
            ],
            'validateCrmData' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'validateCrmData', '?', '?'],
                'pathVars' => ['module', '', 'data', 'filter'],
                'method' => 'validateCrmData',
                'acl' => 'view',
                'shortHelp' => 'Validates whether BPM data exists in the system (Fields, Modules, Users, Roles, etc.)',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_validate_crm_data_get_help.html',
                'minVersion' => '11.10',
            ],
            'updateCrmData' => [
                'reqType' => 'PUT',
                'path' => ['pmse_Project', 'CrmData', '?', '?'],
                'pathVars' => ['module', '', 'record', 'filter'],
                'method' => 'putCrmData',
                'acl' => 'create',
                'shortHelp' => 'Updates information about Fields, Modules, Users, Roles, etc.',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_crm_data_put_help.html',
            ],
            'readCrmDataWithoutFilters' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'CrmData', '?'],
                'pathVars' => ['module', '', 'data'],
                'method' => 'getCrmData',
                'acl' => 'view',
//                'shortHelp' => 'Returns information without send filter about Fields, Modules, Users, Roles,',
            ],
            'validateCrmDataWithoutFilters' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'validateCrmData', '?'],
                'pathVars' => ['module', '', 'data'],
                'method' => 'validateCrmData',
                'acl' => 'view',
                'shortHelp' => 'Validates whether BPM data exists in the system (Fields, Modules, Users, Roles, etc.)',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_validate_crm_data_get_help.html',
                'minVersion' => '11.10',
            ],
            'readActivityDefinition' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'ActivityDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'getActivityDefinition',
                'acl' => 'view',
                'shortHelp' => 'Retrieves the definition data for an activity',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_activity_get_help.html',
            ],
            'updateActivityDefinition' => [
                'reqType' => 'PUT',
                'path' => ['pmse_Project', 'ActivityDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'putActivityDefinition',
                'acl' => 'create',
                'shortHelp' => 'Updates the definition data for an activity',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_activity_put_help.html',
            ],
            'readEventDefinition' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'EventDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'getEventDefinition',
                'acl' => 'view',
                'shortHelp' => 'Retrieves the definition data for an event',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_event_get_help.html',
            ],
            'updateEventDefinition' => [
                'reqType' => 'PUT',
                'path' => ['pmse_Project', 'EventDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'putEventDefinition',
                'acl' => 'create',
                'shortHelp' => 'Updates the definition data for an event',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_event_put_help.html',
            ],
            'readGatewayDefinition' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', 'GatewayDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'getGatewayDefinition',
                'acl' => 'view',
                'shortHelp' => 'Retrieves the definition data for a gateway',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_gateway_get_help.html',
            ],
            'updateGatewayDefinition' => [
                'reqType' => 'PUT',
                'path' => ['pmse_Project', 'GatewayDefinition', '?'],
                'pathVars' => ['module', '', 'record'],
                'method' => 'putGatewayDefinition',
                'acl' => 'create',
                'shortHelp' => 'Updates the definition data for a gateway',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_gateway_put_help.html',
            ],
            'verifyRunningProcess' => [
                'reqType' => 'GET',
                'path' => ['pmse_Project', '?', 'verify'],
                'pathVars' => ['module', 'record', 'verify'],
                'method' => 'verifyRunningProcess',
                'acl' => 'view',
                'shortHelp' => 'Verifies whether the Process Definition has any pending processes',
                'longHelp' => 'modules/pmse_Project/clients/base/api/help/project_record_verify_help.html',
            ],
        ];
    }

    public function retrieveCustomProject(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $api->action = 'read';
        $this->requireArgs($args, ['record']);

        return $this->projectWrapper->retrieveProject($args['record']);
    }

    public function updateCustomProject(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $api->action = 'update';
        $this->requireArgs($args, ['record']);

        return $this->projectWrapper->updateProject($args['record'], $args);
    }

    /**
     * Shared method from create and update process that handles records that
     * might not pass visibility checks. This method assumes the API has validated
     * the authorization to create/edit records prior to this point.
     *
     * @param ServiceBase $api The service object
     * @param array $args Request arguments
     * @return array Array of formatted fields
     */
    protected function getLoadedAndFormattedBean(ServiceBase $api, array $args)
    {
        $bean = null;
        $addNoAccessAcl = false;
        // Load the bean fresh to ensure the cache entry from the create process
        // doesn't get in the way of visibility checks
        try {
            $bean = $this->loadBean($api, $args, 'view', ['use_cache' => false]);
        } catch (SugarApiExceptionNotAuthorized $e) {
            // If there was an exception thrown from the load process then strip
            // the field list down and return only id and date_modified. This will
            // happen on new records created with visibility rules that conflict
            // with the current user or from edits made to records that do the same
            // thing.
            $args['fields'] = 'id,date_modified';
            $addNoAccessAcl = true;
        }

        $api->action = 'view';
        $data = $this->formatBean($api, $args, $bean);

        if ($addNoAccessAcl) {
            $data['_acl'] = [
                'access' => 'no',
                'view' => 'no',
            ];
        }

        return $data;
    }

    /**
     *
     * @param ServiceBase $api
     * @param array $args
     * @return type
     */
    public function getBRFields(ServiceBase $api, array $args)
    {
        $args['module'] = 'pmse_Project';
        $args['data'] = 'oneToOneRelated';
        $args['filter'] = $args['base_module'];
        return $this->getCrmData($api, $args);
    }

    /**
     *
     * @param ServiceBase $api
     * @param array $args
     * @return type
     */
    public function getCrmData(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $this->crmDataWrapper->setService($api);
        return $this->crmDataWrapper->_get($args, $this);
    }

    /**
     *
     * @param ServiceBase $api
     * @param array $args
     * @return type
     */
    public function putCrmData(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $processObserver = ProcessManager\Factory::getPMSEObject('PMSEProcessObserver');
        $this->crmDataWrapper->attach($processObserver);
        return $this->crmDataWrapper->_put($args);
    }

    /**
     *
     * @param ServiceBase $api
     * @param array $args
     * @return type
     */
    public function getActivityDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        return $this->activityDefinitionWrapper->_get($args);
    }

    public function putActivityDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        return $this->activityDefinitionWrapper->_put($args);
    }

    /**
     *
     * @param ServiceBase $api
     * @param array $args
     * @return type
     */
    public function getEventDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        return $this->eventDefinitionWrapper->_get($args);
    }

    public function putEventDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        $observer = ProcessManager\Factory::getPMSEObject('PMSEEventObserver');
        $this->eventDefinitionWrapper->attach($observer);
        $this->eventDefinitionWrapper->_put($args);
    }

    public function getGatewayDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        return $this->gatewayDefinitionWrapper->_get($args);
    }

    public function putGatewayDefinition(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        return $this->gatewayDefinitionWrapper->_put($args);
    }

    public function verifyRunningProcess(ServiceBase $api, array $args)
    {
        ProcessManager\AccessManager::getInstance()->verifyAccess($api, $args);
        if (empty($args['baseModule'])) {
            $projectBean = BeanFactory::getBean(
                $args['module'],
                $args['record'],
                ['strict_retrieve' => true, 'disable_row_level_security' => true]
            );
            $processBean = BeanFactory::newBean('pmse_BpmnProcess')->retrieve_by_string_fields(['prj_id' => $projectBean->id]);
            $casesBean = BeanFactory::newBean('pmse_Inbox');
            $sql = new SugarQuery();
            $sql->select('id');
            $sql->from($casesBean);
            $sql->where()
                ->queryAnd()
                ->equals('pro_id', $processBean->id)
                ->notEquals('cas_status', 'COMPLETED')
                ->notEquals('cas_status', 'TERMINATED')
                ->notEquals('cas_status', 'CANCELLED');
            if ($sql->execute()) {
                return true;
            }
        } else {
            switch ($args['baseModule']) {
                case 'pmse_Business_Rules':
                    $bean = BeanFactory::newBean('pmse_BpmActivityDefinition');
                    $where = 'act_fields';
                    break;
                case 'pmse_Emails_Templates':
                    $bean = BeanFactory::newBean('pmse_BpmEventDefinition');
                    $where = 'evn_criteria';
                    break;
                default:
                    return false;
            }
            $id = $args['record'];
            $sql = new SugarQuery();
            $sql->select(['pro_id']);
            $sql->from($bean);
            $sql->where()->equals($where, $id);
            $processes = $sql->execute();
            if (!empty($processes)) {
                foreach ($processes as $process) {
                    $process_definition = BeanFactory::getBean('pmse_BpmProcessDefinition', $process['pro_id']);
                    if ($process_definition->pro_status == 'ACTIVE') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Validates that a record with the given ID/key exists in the system. Similar
     * to getCrmData, but instead of returning a list of data, returns a simple
     * boolean with the result
     *
     * @param ServiceBase $api The service object
     * @param array $args The request arguments
     * @return array containing the boolean result of the validation
     *              ['result' => {true if valid, false otherwise}]
     * @throws SugarApiExceptionMissingParameter
     */
    public function validateCrmData(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['key']);
        $results = $this->getCrmData($api, $args);
        if (!empty($results['result'])) {
            foreach ($results['result'] as $result) {
                if (!empty($result['value']) && $result['value'] === $args['key']) {
                    return [
                        'result' => true,
                    ];
                }
            }
        }
        return [
            'result' => false,
        ];
    }
}

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

require_once 'modules/ACLRoles/ACLRole.php';

use Sugarcrm\Sugarcrm\ProcessManager;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/**
 * Class PMSEWrapperCrmData
 *
 * The ADAMWrapperCrmData is a class part of the family of wrapper classes
 * that encapsulates a certain amount of logic in order to read records in
 * BpmActivityDefinition, BpmProcessDefinition, BpmProcess, BpmProject
 *
 * @package PMSE
 */
class PMSECrmDataWrapper implements PMSEObservable
{
    /**
     *
     * @var type
     */
    protected $defaultDynaform;

    /**
     *
     * @var type
     */
    protected $beanFactory;

    /**
     *
     * @var type
     */
    protected $studioBrowser;

    /**
     *
     * @var type
     */
    protected $processDefinition;

    /**
     *
     * @var type
     */
    protected $activityDefinitionBean;

    /**
     *
     * @var type
     */
    protected $dynaformBean;

    /**
     *
     * @var type
     */
    protected $teamsBean;

    /**
     *
     * @var type
     */
    protected $projectBean;

    /**
     *
     * @var type
     */
    protected $processBean;

    /**
     *
     * @var type
     */
    protected $activityBean;

    /**
     *
     * @var type
     */
    protected $ruleSetBean;

    /**
     *
     * @var type
     */
    protected $emailTemplateBean;

    /**
     *
     * @var type
     */
    protected $inboxBean;

    /**
     *
     * @var type
     */
    protected $usersBean;

    /**
     *
     * @var type
     */
    protected $emailBean;

    /**
     *
     * @var type
     */
    protected $inboundEmailBean;

    /**
     *
     * @var type
     */
    protected $sugarQueryObject;

    /**
     *
     * @var type
     *
     */
    protected $beanList;

    /**
     *
     * @var type
     */
    protected $db;

    /**
     *
     * @var array
     */
    protected $observers;

    protected $pmseRelatedModule;

    /**
     *
     * @var PMSELogger
     */
    protected $logger;

    /**
     * @var ServiceBase
     */
    private $apiService;

    /**
     * @global type $beanList
     * @global type $db
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        global $beanList;
        $this->defaultDynaform = ProcessManager\Factory::getPMSEObject('PMSEDynaForm');
        $this->teamsBean = BeanFactory::newBean('Teams');
        $this->usersBean = BeanFactory::newBean('Users');
        $this->sugarQueryObject = new SugarQuery();
        $this->pmseRelatedModule = ProcessManager\Factory::getPMSEObject('PMSERelatedModule');


        $this->beanList = $beanList;
        $this->observers = [];
        $this->db = DBManagerFactory::getInstance();

        $this->logger = PMSELogger::getInstance();
    }

    /**
     * Set global variable $db.
     * @codeCoverageIgnore
     * @param object $db
     * @return void
     */
    public function setDB($db)
    {
        $this->db = $db;
    }

    /**
     * Set the bean list
     * @codeCoverageIgnore
     * @param array
     */
    public function setBeanList($beanList)
    {
        $this->beanList = $beanList;
    }

    /**
     * Gets the beanList from this object
     * @codeCoverageIgnore
     * @return array
     */
    public function getBeanList()
    {
        return $this->beanList;
    }

    /**
     * Checks if the given element is a bean element
     * @param string $element String name of a bean module to check
     * @return boolean
     */
    public function isBeanElement(string $element)
    {
        return isset($this->beanList[$element]);
    }

    /**
     * Get class BeanFactory.
     * @codeCoverageIgnore
     * @return object
     */
    public function getBeanFactory()
    {
        if (!isset($this->beanFactory) || empty($this->beanFactory)) {
            $this->beanFactory = new BeanFactory();
        }
        return $this->beanFactory;
    }

    /**
     * Set class BeanFactory.
     * @codeCoverageIgnore
     * @param object $beanFactory
     * @return void
     * @deprecated since version pmse2
     */
    public function setBeanFactory($beanFactory)
    {
        $this->beanFactory = $beanFactory;
    }


    /**
     * Get custom bean class.
     * @return object
     * @codeCoverageIgnore
     */
    public function getModuleFilter($newModuleFilter)
    {
        return BeanFactory::newBean($newModuleFilter);
    }

    /**
     * Get class StudioBrowser.
     * @codeCoverageIgnore
     * @return object
     */
    public function getStudioBrowser()
    {
        if (!isset($this->studioBrowser) || empty($this->studioBrowser)) {
            $this->studioBrowser = new StudioBrowser();
        }
        return $this->studioBrowser;
    }

    /**
     * Get class SugarQuery.
     * @codeCoverageIgnore
     * @return object
     */
    public function getSugarQueryObject()
    {
        if (!isset($this->sugarQueryObject) || empty($this->sugarQueryObject)) {
            $this->sugarQueryObject = new SugarQuery();
        }
        return $this->sugarQueryObject;
    }

    /**
     * Set class SugarQuery.
     * @codeCoverageIgnore
     */
    public function setSugarQueryObject($sugarQueryObject)
    {
        $this->sugarQueryObject = $sugarQueryObject;
    }

    /**
     * Set class StudioBrowser.
     * @codeCoverageIgnore
     * @param object $studioBrowser
     * @return void
     */
    public function setStudioBrowser($studioBrowser)
    {
        $this->studioBrowser = $studioBrowser;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getTeamsBean()
    {
        return $this->teamsBean;
    }

    /**
     *
     * @param type $teamsBean
     * @codeCoverageIgnore
     */
    public function setTeamsBean($teamsBean)
    {
        $this->teamsBean = $teamsBean;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getUsersBean()
    {
        return $this->usersBean;
    }

    /**
     *
     * @param type $usersBean
     * @codeCoverageIgnore
     */
    public function setUsersBean($usersBean)
    {
        $this->usersBean = $usersBean;
    }


    /**
     * Get class DynaFormBean.
     * @codeCoverageIgnore
     * @return object
     */
    public function getDynaformBean()
    {
        if (!isset($this->dynaformBean) || empty($this->dynaformBean)) {
            $this->dynaformBean = BeanFactory::newBean('pmse_BpmDynaForm'); //new BpmDynaForm();
        }
        return $this->dynaformBean;
    }

    /**
     * Set class DynaFormBean.
     * @codeCoverageIgnore
     * @param object $dynaformBean
     * @return void
     */
    public function setDynaformBean($dynaformBean)
    {
        $this->dynaformBean = $dynaformBean;
    }

    /**
     * Get class BPMNProject.
     * @codeCoverageIgnore
     * @return object
     */
    public function getProjectBean()
    {
        if (!isset($this->projectBean) || empty($this->projectBean)) {
            $this->projectBean = BeanFactory::newBean('pmse_Project'); //new BpmnProject();
        }
        return $this->projectBean;
    }

    /**
     * Set class BPMNProject.
     * @codeCoverageIgnore
     * @param object $projectBean
     * @return void
     */
    public function setProjectBean($projectBean)
    {
        $this->projectBean = $projectBean;
    }

    /**
     * Get class BPMNProcess.
     * @codeCoverageIgnore
     * @return object
     */
    public function getProcessBean()
    {
        if (!isset($this->processBean) || empty($this->processBean)) {
            $this->processBean = BeanFactory::newBean('pmse_BpmnProcess'); //new BpmnProcess();
        }
        return $this->processBean;
    }

    /**
     * Set class BPMNProcess.
     * @codeCoverageIgnore
     * @param object $processBean
     * @return void
     */
    public function setProcessBean($processBean)
    {
        $this->processBean = $processBean;
    }

    /**
     * Get class BPMNActivity.
     * @codeCoverageIgnore
     * @return object
     */
    public function getActivityBean()
    {
        if (!isset($this->activityBean) || empty($this->activityBean)) {
            $this->activityBean = BeanFactory::newBean('pmse_BpmnActivity'); //new BpmnActivity();
        }
        return $this->activityBean;
    }

    /**
     * Set class BPMNActivity.
     * @codeCoverageIgnore
     * @param object $activityBean
     * @return void
     */
    public function setActivityBean($activityBean)
    {
        $this->activityBean = $activityBean;
    }

    /**
     * Get class BPMNProcessDefinition.
     * @codeCoverageIgnore
     * @return object
     */
    public function getProcessDefinition()
    {
        if (!isset($this->processDefinition) || empty($this->processDefinition)) {
            $this->processDefinition = BeanFactory::newBean(
                'pmse_BpmProcessDefinition'
            ); //new BpmProcessDefinition();
        }
        return $this->processDefinition;
    }

    /**
     * Set class BPMNProcessDefinition.
     * @codeCoverageIgnore
     * @param object $processDefinitionBean
     * @return void
     */
    public function setProcessDefinition($processDefinitionBean)
    {
        $this->processDefinition = $processDefinitionBean;
    }

    /**
     * Get class BPMNActivityDefinition.
     * @codeCoverageIgnore
     * @return object
     */
    public function getActivityDefinitionBean()
    {
        if (!isset($this->activityDefinitionBean) || empty($this->activityDefinitionBean)) {
            $this->activityDefinitionBean = BeanFactory::newBean(
                'pmse_BpmActivityDefinition'
            ); //new BpmActivityDefinition();
        }
        return $this->activityDefinitionBean;
    }

    /**
     * Set class BPMNActivityDefinition.
     * @codeCoverageIgnore
     * @param object $activityDefinitionBean
     * @return void
     */
    public function setActivityDefinitionBean($activityDefinitionBean)
    {
        $this->activityDefinitionBean = $activityDefinitionBean;
    }

    /**
     * Get class BPMRuleSet.
     * @codeCoverageIgnore
     * @return object
     */
    public function getRuleSetBean()
    {
        if (!isset($this->ruleSetBean) || empty($this->ruleSetBean)) {
            $this->ruleSetBean = BeanFactory::newBean('pmse_Business_Rules'); //new BpmRuleSet();
        }
        return $this->ruleSetBean;
    }

    /**
     * Set class BPMRuleSet.
     * @codeCoverageIgnore
     * @param object $ruleSetBean
     * @return void
     */
    public function setRuleSetBean($ruleSetBean)
    {
        $this->ruleSetBean = $ruleSetBean;
    }

    /**
     * Get class BPMEmailTemplate.
     * @codeCoverageIgnore
     * @return object
     */
    public function getEmailTemplateBean()
    {
        if (!isset($this->emailTemplateBean) || empty($this->emailTemplateBean)) {
            $this->emailTemplateBean = BeanFactory::newBean('pmse_Emails_Templates'); //new BpmEmailTemplate();
        }
        return $this->emailTemplateBean;
    }

    /**
     * Set class BPMEmailTemplate.
     * @codeCoverageIgnore
     * @param object $emailTemplateBean
     * @return void
     */
    public function setEmailTemplateBean($emailTemplateBean)
    {
        $this->emailTemplateBean = $emailTemplateBean;
    }

    /**
     * Get class BPMInbox.
     * @codeCoverageIgnore
     * @return object
     */
    public function getInboxBean()
    {
        if (!isset($this->inboxBean) || empty($this->inboxBean)) {
            $this->inboxBean = BeanFactory::newBean('pmse_Inbox'); //new BpmInbox();
        }
        return $this->inboxBean;
    }

    /**
     * Set class BPMInbox.
     * @codeCoverageIgnore
     * @param object $inboxBean
     * @return void
     */
    public function setInboxBean($inboxBean)
    {
        $this->inboxBean = $inboxBean;
    }

    /**
     * Get class Email.
     * @codeCoverageIgnore
     * @return object
     */
    public function getEmailBean()
    {
        if (!isset($this->emailBean) || empty($this->emailBean)) {
            $this->emailBean = new Email();
        }
        return $this->emailBean;
    }

    /**
     * Set class Email.
     * @codeCoverageIgnore
     * @return object
     */
    public function setEmailBean($emailBean)
    {
        $this->emailBean = $emailBean;
    }

    /**
     * Get class InboundEmail.
     * @codeCoverageIgnore
     * @return object
     */
    public function getInboundEmailBean()
    {
        if (!isset($this->inboundEmailBean) || empty($this->inboundEmailBean)) {
            $this->inboundEmailBean = new InboundEmail();
        }
        return $this->inboundEmailBean;
    }

    /**
     * Set class InboundEmail.
     * @codeCoverageIgnore
     * @return object
     */
    public function setInboundEmailBean($inboundEmailBean)
    {
        $this->inboundEmailBean = $inboundEmailBean;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getDefaultDynaform()
    {
        return $this->defaultDynaform;
    }

    /**
     *
     * @param type $defaultDynaform
     * @codeCoverageIgnore
     */
    public function setDefaultDynaform($defaultDynaform)
    {
        $this->defaultDynaform = $defaultDynaform;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     *
     * @param type $observers
     * @codeCoverageIgnore
     */
    public function setObservers($observers)
    {
        $this->observers = $observers;
    }

    /**
     * Get the api service
     * @return ServiceBase|null
     */
    public function getService()
    {
        return $this->apiService;
    }

    /**
     * Set the api service
     * @param ServiceBase $api
     */
    public function setService(ServiceBase $api)
    {
        $this->apiService = $api;
    }

    // @codingStandardsIgnoreStart

    /**
     * @codeCoverageIgnore
     */
    public function _get(array $args, ModuleApi $moduleApi)
    {
        // @codingStandardsIgnoreEnd
        $output = null;
        $data = $args['data'];
        $filter = $args['filter'] ?? '';
        $type = $args['call_type'] ?? '';
        $baseModule = $args['base_module'] ?? '';
        $orderBy = $args['order_by'] ?? '';

        $outputType = 0;
        switch ($data) {
            case 'emails':
                $output = $this->retrieveEmails($filter);
                break;
            case 'teams':
                $output = $this->retrieveTeams($filter, $args);
                break;
            case 'fields':
                $output = $this->retrieveFields($filter, $moduleApi, $type, $baseModule);
                $outputType = 1;
                break;
            case 'relatedfields':
                $output = $this->retrieveFields($filter, $moduleApi, $type, $baseModule);
                $outputType = 1;
                break;
            case 'allFields':
                $output = $this->getTargetAndRelatedFields($filter);
                $outputType = 1;
                break;
            case 'oneToOneFields':
                $output = $this->getTargetAndRelatedFields($filter, 'one-to-one');
                $outputType = 1;
                break;
            case 'oneToManyFields':
                $output = $this->getTargetAndRelatedFields($filter, 'one-to-many');
                $outputType = 1;
                break;
            case 'fields_ruleset':
                $output = $this->retrieveFields($filter);
                break;
            case 'users':
                $output = $this->retrieveUsers($filter, $args);
                break;
            case 'modules':
                $output = $this->retrieveModules($filter);
                break;
            case 'related_modules':
                $output = $this->retrieveRelatedModules($filter);
                break;
            case 'dynaforms':
                $output = $this->retrieveDynaforms($filter);
                break;
            case 'activities':
                $output = $this->retrieveActivities($filter);
                break;
            case 'process_definition':
                $output = $this->retrieveProcessDefinition($filter);
                break;
            case 'related':
                $cardinality = $args['cardinality'] ?? 'all';
                $removeTarget = $args['removeTarget'] ?? false;
                $output = $this->retrieveRelatedBeans($filter, $cardinality, $removeTarget);
                $outputType = 1;
                break;
            case 'oneToOneRelatedModules':
                $output = $this->retrieveRelatedBeans($filter, 'one-to-one');
                $outputType = 1;
                break;
            case 'rulesets':
                $output = $this->retrieveRuleSets($filter, $orderBy);
                break;
            case 'businessrules':
                $output = $this->retrieveBusinessRules($filter);
                break;
            case 'emailtemplates':
                $output = $this->retrieveEmailTemplates($filter);
                break;
            case 'validateProjectName':
                $output = $this->validateProjectName($filter);
                $outputType = 1;
                break;
            case 'validateEmailTemplateName':
                $output = $this->validateEmailTemplateName($filter, $args['id']);
                break;
            case 'validateBusinessRuleName':
                $output = $this->validateBusinessRuleName($filter);
                break;
            case 'defaultUsersList':
                $output = $this->defaultUsersList();
                break;
            case 'rolesList':
                $output = $this->rolesList();
                break;
            case 'dateFields':
                $output = $this->retrieveDateFields($args['module']);
                $outputType = 1;
                break;
            case 'dateFieldsOfModule':
                $output = $this->retrieveDateFields($args['filter'], false);
                $outputType = 1;
                break;
            case 'validateReclaimCase':
                $output = $this->validateReclaimCase($args['cas_id'], $args['cas_index']);
                $outputType = 1;
                break;
            case 'addRelatedRecord':
                $output = $this->retrieveFields($filter, $moduleApi, 'AC', $baseModule);
                //$output = $this->addRelatedRecord($filter);
                $outputType = 1;
                break;
            case 'allRelated':
                $output = $this->getAllRelated($filter, $moduleApi, 'all', $baseModule, $type);
                $outputType = 1;
                break;
            case 'oneToOneRelated':
                $output = $this->getAllRelated($filter, $moduleApi, 'one-to-one', $baseModule, $type);
                $outputType = 1;
                break;
            case 'oneToManyRelated':
                $output = $this->getAllRelated($filter, $moduleApi, 'one-to-many', $baseModule, $type);
                $outputType = 1;
                break;
            case 'outboundEmailsAccounts':
                $output = $this->getOutboundEmailAccounts($args);
                $outputType = 1;
                break;
                //case 'Log':
                //    $output = $this->retrieveHistoryLog($filter);
                //    $outputType = 1;
                //    break;
            default:
                $outputType = 2;
        }
        switch ($outputType) {
            case 0:
                $out = $this->retrieveCrmData($output, $filter);
                break;
            case 1:
                $out = $output;
                break;
            case 2:
                $out = $this->invalidRequest();
                break;
        }
        return $out;
    }

    /**
     * Return the structure of an invalid request.
     * @codeCoverageIgnore
     */
    public function invalidRequest()
    {
        $response = ['success' => false, 'message' => 'Invalid Request'];
        return $response;
    }

//    public function retrieveHistoryLog($filter)
//    {
////        if (!isset($this->wrapperHistoryLog) || empty($this->wrapperHistoryLog)) {
//            $wrapperHistoryLog = new PMSEHistoryLogWrapper();
////        }
//
//        //return $this->wrapperHistoryLog;
//        $result = $wrapperHistoryLog->pppppp($filter);
//        return $result;
//    }

    /**
     * @codeCoverageIgnore
     */
    public function retrieveEmails($filter)
    {
        if ($filter === '') {
            return $this->invalidRequest();
        }
        $out = [];

        $email = $this->getEmailBean();
        $email->email2init();
        $ie = $this->getInboundEmailBean();
        $ie->email = $email;

        // When querying person modules, we want to also be able to query by the
        // person's full name. Since we don't store that in the DB, we need to
        // query by concatenating the first and last name
        $db = DBManagerFactory::getInstance();
        $usersFullName = $db->concat('users', ['first_name', 'last_name']);
        $contactsFullName = $db->concat('contacts', ['first_name', 'last_name']);
        $leadsFullName = $db->concat('leads', ['first_name', 'last_name']);
        $prospectsFullName = $db->concat('prospects', ['first_name', 'last_name']);

        $query = <<<SQL
SELECT users.id, users.first_name, users.last_name, eabr.primary_address, ea.email_address, 'Users' module
FROM users
JOIN email_addr_bean_rel eabr ON (users.id = eabr.bean_id AND eabr.deleted=0)
JOIN email_addresses ea ON(eabr.email_address_id = ea.id)
WHERE (users.deleted = 0 AND eabr.primary_address = 1)
AND (first_name LIKE :filter OR last_name LIKE :filter OR $usersFullName LIKE :filter OR email_address LIKE :filter)
UNION ALL
SELECT contacts.id, contacts.first_name, contacts.last_name, eabr.primary_address, ea.email_address, 'Contacts' module
FROM contacts
JOIN email_addr_bean_rel eabr ON(contacts.id = eabr.bean_id AND eabr.deleted=0)
JOIN email_addresses ea ON(eabr.email_address_id = ea.id)
WHERE (contacts.deleted = 0 AND eabr.primary_address = 1)
AND (first_name LIKE :filter OR last_name LIKE :filter OR $contactsFullName LIKE :filter OR email_address LIKE :filter)
UNION ALL
SELECT leads.id, leads.first_name, leads.last_name, eabr.primary_address, ea.email_address, 'Leads' module
FROM leads
JOIN email_addr_bean_rel eabr ON(leads.id = eabr.bean_id AND eabr.deleted=0)
JOIN email_addresses ea ON(eabr.email_address_id = ea.id)
WHERE (leads.deleted = 0 AND eabr.primary_address = 1)
AND (first_name LIKE :filter OR last_name LIKE :filter OR $leadsFullName LIKE :filter OR email_address LIKE :filter)
UNION ALL
SELECT prospects.id, prospects.first_name, prospects.last_name, eabr.primary_address, ea.email_address,
'Prospects' module FROM prospects 
JOIN email_addr_bean_rel eabr ON(prospects.id = eabr.bean_id AND eabr.deleted=0)
JOIN email_addresses ea ON(eabr.email_address_id = ea.id)
WHERE (prospects.deleted = 0 AND eabr.primary_address = 1)
AND (first_name LIKE :filter OR last_name LIKE :filter OR $prospectsFullName LIKE :filter OR email_address LIKE :filter)
UNION ALL
SELECT accounts.id, '' first_name, accounts.name last_name, eabr.primary_address, ea.email_address, 'Accounts' module
FROM accounts
JOIN email_addr_bean_rel eabr ON (accounts.id = eabr.bean_id AND eabr.deleted=0)
JOIN email_addresses ea ON(eabr.email_address_id = ea.id)
WHERE (accounts.deleted = 0 AND eabr.primary_address = 1) AND (email_address LIKE :filter OR name LIKE :filter)
SQL;
        $query = $this->db->getConnection()
            ->getDatabasePlatform()
            ->modifyLimitQuery($query, 25, 0);

        $result = $this->db->getConnection()
            ->executeQuery(
                $query,
                ['filter' => $filter . '%']
            );

        foreach ($result->iterateAssociative() as $a) {
            $person = [];
            $person['fullName'] = $a['first_name'] . ' ' . $a['last_name'];
            $person['emailAddress'] = $a['email_address'];
            $person['id'] = $a['id'];
            $person['module'] = $a['module'];
            $out[] = $person;
        }

        return $out;
    }

    /**
     * Returns the name of the module related to the target module based on the relationship
     * @param $linkField Relationship Key
     * @param $targetModule Target Module
     */
    public function getRelatedModule($linkField, $targetModule)
    {
        $baseModule = BeanFactory::newBean($targetModule);
        $baseModule->load_relationship($linkField);
        if (isset($baseModule->$linkField)) {
            $moduleName = $baseModule->$linkField->getRelatedModuleName();
        } else {
            $moduleName = $targetModule;
        }
        return $moduleName;
    }

    /**
     * Retrieve list of Teams
     * @param string $filter
     * @param array $args
     * @return object
     */
    public function retrieveTeams($filter = '', $args = [])
    {
        $beansTeams = $this->getTeamsBean();
        $output = [];

        $q = $this->sugarQueryObject;
        $q->from($beansTeams, ['add_deleted' => true]);
        $q->distinct(false);
        $fields = [
            'id',
            'name',
            'name2',
        ];

        if ($filter == 'public' || $filter == 'reassign') {
            $q->where()
                ->equals('private', 0);
        } else {
            if ($filter == 'private') {
                $q->where()
                    ->equals('private', 1);
            }
        }
        if (!empty($args['key'])) {
            $q->where()
                ->equals('id', $args['key']);
        }


        $q->orderBy('id', 'ASC');
        $q->select($fields);

        $teamsData = $q->execute();
        foreach ($teamsData as $team) {
            $teamTmp = [];
            $teamTmp['value'] = $team['id'];
            $teamTmp['text'] = $team['name'];
            if (($team['id'] != 'current_team') || ($team['id'] == 'current_team' && $filter == 'reassign')) {
                $output[] = $teamTmp;
            }
        }

        if ($filter === 'all') {
            $output[] = [
                'value' => 'assigned_teams',
                'text' => translate('LBL_PMSE_EMAILPICKER_ALL_ASSIGNED_TEAMS', 'pmse_Project'),
            ];
        }

        return $output;
    }

    /**
     * Retrieve list of Users
     * @param string $filter
     * @param array $args
     * @return array
     */
    public function retrieveUsers($filter = '', $args = [])
    {
        $users = BeanFactory::newBean('Users');
        $teams = BeanFactory::newBean('Teams');

        $q = new SugarQuery();
        $q->from($users);
        $q->select([
            'id',
            'first_name',
            'last_name',
        ]);
        $q->orderBy('first_name', 'ASC')
            ->orderBy('last_name', 'ASC');

        $q->where()
            ->equals('status', 'Active')
            ->notEquals('is_group', 1)
            ->notEquals('portal_only', 1);

        if (!empty($filter)) {
            $q->where()
                ->queryOr()
                ->contains('first_name', $filter)
                ->contains('last_name', $filter)
                ->contains('user_name', $filter);
        }
        if (!empty($args['key'])) {
            $q->where()
                ->equals('id', $args['key']);
        }

        $result = [];
        $rows = $q->execute();
        foreach ($rows as $row) {
            $result[] = [
                'value' => $row['id'],
                'text' => $teams->getDisplayName(
                    $row['first_name'],
                    $row['last_name']
                ),
            ];
        }

        return $result;
    }


    /**
     * @codeCoverageIgnore
     */
    public function getRelationshipData($relationName)
    {
        return SugarRelationshipFactory::getInstance()->getRelationshipDef($relationName);
    }

    /**
     * Retrieve the target and a related module based on a relationship
     * then retrieves a list of fields of both modules.
     * @param type $filter
     * @param type $relationship
     * @return array
     */
    public function getTargetAndRelatedFields($filter = '', $relationship = 'all')
    {
        $result = [];
        //        $bean = BeanFactory::getBean($args['module'], $args['id']);
        $result['success'] = true;
        //        $result['base_module'] = $bean->base_module;
        //        $result['name'] = $bean->name;
        //        $result['description'] = $bean->description;
        //        $result['subject'] = $bean->subject;
        //        $result['body_html'] = $bean->body_html;
        //        $result['body'] = $bean->body;
        //        $result['text_only'] = $bean->text_only == 1 ? "checked" : "";
        //        $result['from_name'] = $bean->from_name;
        //        $result['from_address'] = $bean->from_address;

        $fields = $this->retrieveFields($filter);
        if ($fields['success']) {
            $result['fields'] = $fields['result'];
        } else {
            $result['fields'] = [];
        }
        $related_modules = $this->retrieveRelatedBeans($filter, $relationship);
        if ($related_modules['success']) {
            for ($i = 0; $i < safeCount($related_modules['result']); $i++) {
                $fields = $this->retrieveFields($related_modules['result'][$i]['value']);
                $related_modules['result'][$i]['fields'] = $fields['result'];
            }
            $result['related_modules'] = $related_modules['result'];
        } else {
            $result['related_modules'] = [];
        }
        return $result;
    }

    /**
     * Retrieve list of modules installed in SugarCRM
     * @param string $filter
     * @return object
     */
    public function retrieveModules($filter = '')
    {
        $moduleList = PMSEEngineUtils::getModules();
        $output = [];
        foreach ($moduleList as $module) {
            if (!empty($module->name) && (empty($filter) || stripos($module->name, $filter) !== false)) {
                $tmpField = [];
                $tmpField['value'] = $module->module;
                $tmpField['text'] = $module->name;
                $output[] = $tmpField;
            }
        }

        $value = [];
        $text = [];
        foreach ($output as $key => $row) {
            $value[$key] = $row['value'];
            $text[$key] = $row['text'];
        }
        // Sort the data with volume descending, edition ascending.
        // Add $data as the last parameter, to sort by the common key.
        array_multisort($text, SORT_ASC, $output);
        //$res->result = $output;
        return $output;
    }

    /**
     * Retrieve list of DynaForms
     * @param string $filter
     * @return object
     */
    public function retrieveDynaforms($filter = '')
    {
        $dynaformBean = $this->getDynaformBean();
        $projectBean = $this->getProjectBean();
        $processBean = $this->getProcessBean();

        $output = [];
        if ($projectBean->retrieve($filter)) {
            $processBean->retrieve_by_string_fields(['prj_id' => $projectBean->id]);

            $where = '';
            if (!empty($filter)) {
                $where = "pmse_bpm_dynamic_forms.prj_id='" . $projectBean->id . "'";
            }
            $dynaformList = $dynaformBean->get_full_list('', $where);
            foreach ($dynaformList as $dynaform) {
                $tmpDynaform = [];
                $tmpDynaform['value'] = $dynaform->dyn_uid;
                $tmpDynaform['text'] = $dynaform->name;
                $output[] = $tmpDynaform;
            }
        }
        return $output;
    }

    /**
     * Retrieve list of Activities (USERTASK|SCRIPTTASK).
     * @param string $filter
     * @return object
     */
    public function retrieveActivities($filter = '')
    {
        $activityBean = $this->getActivityBean();
        $fields = [
            'act_uid',
            'name',
        ];
        $this->sugarQueryObject->select($fields);
        $this->sugarQueryObject->from($activityBean, ['alias' => 'a']);

        switch (strtolower($filter)) {
            case 'user':
                $this->sugarQueryObject->joinTable('pmse_bpm_activity_definition', ['alias' => 'b'])
                    ->on()->equalsField('b.id', 'a.id');
                $this->sugarQueryObject->joinTable('pmse_bpm_dynamic_forms', ['alias' => 'c'])
                    ->on()->equalsField('c.dyn_uid', 'b.act_type');
                $this->sugarQueryObject
                    ->where()
                    ->equals('a.act_task_type', 'USERTASK');
                break;
            case 'script':
                $this->sugarQueryObject->joinTable('pmse_bpm_activity_definition', ['alias' => 'b'])
                    ->on()->equalsField('b.id', 'a.id');
                $this->sugarQueryObject
                    ->where()
                    ->equals('a.act_task_type', 'SCRIPTTASK');
                break;
            case '':
                break;
            default:
                $this->sugarQueryObject
                    ->where()
                    ->equals('a.act_task_type', 'USERTASK')
                    ->equals('a.prj_id', $filter);
                break;
        }
        // TODO add debug log here
        $activityList = $this->sugarQueryObject->execute();

        $output = [];
        foreach ($activityList as $activity) {
            $tmpACtivity = [];
            $tmpACtivity['value'] = $activity['act_uid'];
            $tmpACtivity['text'] = $activity['name'];
            $output[] = $tmpACtivity;
        }
        return $output;
    }

    /**
     * Retrieve JSON with Process Definition.
     * @param $filter
     * @return object
     * @codeCoverageIgnore
     * @deprecated since version branch sugar7bwc
     * TODO: mark as deprecated since the getSelectRows is no longer required and present in the CrmData class
     */
    public function retrieveProcessDefinition($filter)
    {
        //$processDefinitionBean = new BpmProcessDefinition();
        $processDefinitionBean = $this->getProcessDefinition();
        //        $res = new stdClass();
        //        $res->search = $filter;
        //        $res->success = true;
        $where = 'pmse_project.id=\'' . $filter . '\'';
        $joinedTables = [
            ['INNER', 'pmse_bpmn_process', 'pmse_bpmn_process.id=pmse_bpm_process_definition.id'],
            ['INNER', 'pmse_project', 'pmse_project.id=pmse_bpmn_process.prj_id'],
        ];

        $processDefinitionList = $this->getSelectRows(
            $processDefinitionBean,
            '',
            $where,
            0,
            -1,
            -1,
            [],
            $joinedTables
        );
        $processDefinitionList = $processDefinitionList['rowList'];
        $output = [];
        foreach ($processDefinitionList as $processDefinition) {
            $output[] = $processDefinition;
        }
        $output[0]['prj_name'] = $output[0]['name'];
        //$res->result = $output;
        return $output;
    }

    /**
     * Encode the relationships for this module for display in the Ext grid layout
     * @param $relationships
     * @return array
     */
    public function getAjaxRelationships($relationships)
    {
        $ajaxrels = [];
        $relationshipList = $relationships->getRelationshipList();
        foreach ($relationshipList as $relationshipName) {
            $rel = $relationships->get($relationshipName)->getDefinition();
            $rel ['lhs_module'] = translate($rel['lhs_module']);
            $rel ['rhs_module'] = translate($rel['rhs_module']);

            //#28668  , translate the relationship type before render it .
            switch ($rel['relationship_type']) {
                case 'one-to-one':
                    $rel['relationship_type_render'] = translate('LBL_ONETOONE');
                    break;
                case 'one-to-many':
                    $rel['relationship_type_render'] = translate('LBL_ONETOMANY');
                    break;
                case 'many-to-one':
                    $rel['relationship_type_render'] = translate('LBL_MANYTOONE');
                    break;
                case 'many-to-many':
                    $rel['relationship_type_render'] = translate('LBL_MANYTOMANY');
                    break;
                default:
                    $rel['relationship_type_render'] = '';
            }
            $rel ['name'] = $relationshipName;
            if ($rel ['is_custom'] && isset($rel ['from_studio']) && $rel ['from_studio']) {
                $rel ['name'] = $relationshipName . '*';
            }
            $ajaxrels [] = $rel;
        }
        return $ajaxrels;
    }

    /**
     * Retrieve related Bean
     * @param $filter
     * @return object
     */
    public function retrieveRelatedBeans(string $filter, $relationship = 'all', $removeTarget = false): array
    {
        return $this->pmseRelatedModule->getRelatedBeans($filter, $relationship, $removeTarget);
    }

    /**
     * Retrieve related modules
     * @param $filter
     * @return object
     */
    public function retrieveRelatedModules($filter)
    {
        $newModuleFilter = $this->getBeanModuleName($filter);
        $output_11 = [];
        $output_1m = [];
        $output = [];
        $moduleBean = $this->getModuleFilter($newModuleFilter);

        $relationshipType = ['one-to-one', 'one-to-many', 'many-to-many'];
        $ajaxRelationships = '';
        if (is_object($moduleBean)) {
            $relationships = new DeployedRelationships($newModuleFilter);
            $ajaxRelationships = $this->getAjaxRelationships($relationships);
            if ('ProjectTask' == $newModuleFilter) {
                $newModuleFilter = 'Project Tasks';
            }
            foreach ($ajaxRelationships as $related) {
                if (($newModuleFilter == $related['lhs_module'] || strtolower(
                    $newModuleFilter
                ) == $related['lhs_table']) && safeInArray($related['relationship_type'], $relationshipType)
                ) {
                    $tmpField = [];
                    $tmpField['value'] = $related['rhs_module'];
                    $tmpField['text'] = $related['rhs_module'];
                    $output[] = $tmpField;
                }
            }
        }
        $moduleName = (translate('LBL_MODULE_NAME', $filter) == 'LBL_MODULE_NAME') ? $filter : translate('LBL_MODULE_NAME', $filter);
        $filterArray = ['value' => $filter, 'text' => '<' . $moduleName . '>'];
        array_unshift($output, $filterArray);

        //$res->result = $output;
        return $output;
    }

    /**
     * @codeCoverageIgnore
     */
    public function _put(array $args)
    {
        $output = null;
        $data = $args['record'];
        $filter = $args['filter'] ?? '';
        switch ($data) {
            case 'project':
                $args['data']['filter'] = $args['filter'];
                $output = $this->updateProcessDefinitions($args['data']);
                break;
            case 'putData': //TODO: needs a change o message
                $args['data']['filter'] = $args['filter'];
                $output = $this->clearAccordingProcessDefinitions($args['data']);
                break;
            case 'clearEventCriteria':
                $output = $this->clearEventCriteria($filter);
                break;
            default:
                $output = $this->invalidRequest();
        }
        return $output;
    }

    /**
     * @codeCoverageIgnore
     */
    public function _post(array $args)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function _delete(array $args)
    {
    }

    /**
     * Function to update the definition of a process
     * @param $args
     * @return object
     */
    public function updateProcessDefinitions($args)
    {
        $res = []; //new stdClass();
        $res['search'] = $args['filter'];
        $res['success'] = false;

        $processDefinitionBean = $this->getProcessDefinition();
        $projectBean = $this->getProjectBean();
        $processBean = $this->getProcessBean();

        $updateDefaultForm = false;

        if ($projectBean->retrieve($args['filter'])) {
            unset($args['prj_uid']);
            $args['prj_id'] = $projectBean->id;
            $processBean->retrieve_by_string_fields(['prj_id' => $projectBean->id]);
            unset($args['pro_uid']);
            $args['pro_id'] = $processBean->id;
            $processDefinitionBean->retrieve_by_string_fields(
                ['prj_id' => $projectBean->id, 'id' => $processBean->id]
            );
            // If there are group fields (e.g: billing_address) in the definition then we need to expand the group field
            // to lock the individual fields which make up the group field.
            $args['pro_locked_variables'] = $this->getMultiLockedFieldsFromGroupField(
                $processDefinitionBean->pro_module,
                $args['pro_locked_variables']
            );

            foreach ($args as $key => $value) {
                if ($key == 'pro_module' && ($processDefinitionBean->$key != $value)) {
                    $updateDefaultForm = true;
                }

                $processDefinitionBean->$key = $value;
                $projectBean->$key = $value;
                $processBean->$key = $value;
            }

            if (isset($args['name']) || isset($args['description'])) {
                $processBean->name = $projectBean->name;
                $projectBean->save();
                $processBean->save();
            }

            $processDefinitionBean->save();
            $this->setProcessDefinition($processDefinitionBean);
            $res['success'] = true;
        }
        if ($updateDefaultForm) {
            $this->defaultDynaform->generateDefaultDynaform($processDefinitionBean->pro_module, $args, true);
        }
        $this->notify();
        return $res;
    }

    // Reference queries
    //$query_bpm_flow = "select flo_id,flo_uid,prj_id,flo_type,flo_name,flo_element_origin,flo_element_origin_type,flo_element_dest,flo_element_dest_type,flo_is_inmediate from bpmn_flow where prj_id=1;";
    //$query_bpm_activity_definition = "select act_field_module,act_fields,act_id,pro_id,act_type from bpm_activity_definition where act_id = 3;"; //pro_id
    //$query_event = "select * from bpm_event_definition;";
    //$queryfield_event = "select evn_module,evn_criteria from bpm_event_definition where pro_id = 1;";
    //select pro_id,evn_id,evn_message from bpmn_event;
    //select act_id,act_name,act_type from bpmn_activity;
    public function clearAccordingProcessDefinitions($args)
    {
        /* TODO: change the hardcoded queries to Bean procedures */
        $res = new stdClass();
        $res->targetModule = $args['pro_old_module'] ?? '';
        $res->newModule = $args['pro_module'] ?? '';
        if (empty($res->newModule)) {
            $res->newModule = $args['pro_new_module'] ?? '';
        }
        $res->targetProcess = $args['filter'] ?? '';
        $res->success = false;

        if (empty($res->targetModule) || empty($res->newModule) || empty($res->targetProcess)) {
            return $res;
        }

        try {
            global $db;

            $projId = $db->getConnection()
                ->executeQuery(
                    'SELECT id FROM pmse_project WHERE id=?',
                    [$res->targetProcess]
                )->fetchOne();

            $proId = $db->getConnection()
                ->executeQuery(
                    'SELECT id FROM pmse_bpmn_process WHERE prj_id=?',
                    [$projId]
                )->fetchOne();

            $actIdsStmt = $db->getConnection()
                ->executeQuery(
                    'SELECT id FROM pmse_bpm_activity_definition WHERE pro_id = ?',
                    [$proId]
                );

            $activity = [];
            while (false !== ($actId = $actIdsStmt->fetchOne())) {
                $activity[$actId] = '';
            }

            $actTypesResult = $db->getConnection()
                ->executeQuery(
                    'SELECT id, act_script_type FROM pmse_bpmn_activity WHERE pro_id = ?',
                    [$proId]
                );

            foreach ($actTypesResult->iterateAssociative() as $row) {
                if (isset($activity[$row['id']])) {
                    $activity[$row['id']] = $row['act_script_type'];
                }
            }

            $cleanActivityFiels = '';
            foreach ($activity as $actId => $actType) {
                if ($actType == 'BUSINESS_RULE') {
                    $cleanActivityFiels = 'NULL';
                } else {
                    if ($actType == 'CHANGE_FIELD') {
                        //getting the new fields to be load 'n show
                        $result = $this->retrieveFields($res->newModule);
                        $cleanActivityFiels = json_encode($result->result);
                    } else {
                        if ($actType == 'ASSIGN_USER') {
                            $cleanActivityFiels = 'NULL'; // ?
                        } else {
                            if ($actType == 'ASSIGN_TEAM') {
                                $cleanActivityFiels = 'NULL'; // ?
                            }
                        }
                    }
                }

                $db->getConnection()
                    ->update(
                        'pmse_bpm_activity_definition',
                        [
                            'act_field_module' => $res->newModule,
                            'act_fields' => $cleanActivityFiels,
                            'act_readonly_fields' => '',
                            'act_required_fields' => '',
                        ],
                        [
                            'pro_id' => $proId,
                            'id' => $actId,
                        ]
                    );
            }
            //cleaning gateways
            $updateSQL = <<<SQL
UPDATE pmse_bpmn_flow
SET flo_condition = ''
WHERE flo_element_origin IN (
  SELECT id FROM pmse_bpmn_gateway WHERE id = ?
)
SQL;

            $db->getConnection()
                ->executeUpdate(
                    $updateSQL,
                    [$projId]
                );

            //cleaning other events not email type
            $updateSQL = <<<SQL
UPDATE pmse_bpm_event_definition 
SET evn_module = ?, evn_criteria = '', evn_params = '' 
WHERE id IN (
  SELECT id 
  FROM pmse_bpmn_event
  WHERE evn_behavior = 'CATCH' AND prj_id = ? AND evn_type = 'INTERMEDIATE'
)
SQL;
            $db->getConnection()
                ->executeUpdate(
                    $updateSQL,
                    [$res->newModule, $projId]
                );
            $res->success = true;
        } catch (Exception $ex) {
            $res->error = $ex->getMessage();
        }
        return $res;
    }

    /**
     * Retrieve list of fields
     * @param string $filter
     * @param array $additionalArgs
     * @return object
     * @codeCoverageIgnore
     */
    public function retrieveFields($filter = '', ModuleApi $moduleApi = null, $type = '', $baseModule = '')
    {
        $newModuleFilter = $this->isBeanElement($filter) ?
            $filter :
            $this->pmseRelatedModule->getRelatedModuleName($baseModule, $filter);

        $res = [];
        new stdClass();

        $res['name'] = $newModuleFilter;

        //$module = explode('_', $filter);// pull the related module out.
        //$filter = ucfirst(isset($module[1])?$module[1]:$module[0]);
        //$primal_module = ucfirst(isset($module[0])?$module[0]:'');
        $res['search'] = $filter;
        $res['success'] = true;
        $module_strings = return_module_language('en_us', 'ModuleBuilder');

        $fieldTypes = $module_strings['fieldTypes'];
        //add datetimecombo type field from the vardef overrides to point to Datetime type
        $fieldTypes['datetime'] = $fieldTypes['datetimecombo'];
        $fieldTypes['name'] = 'Name';

        global $app_list_strings;
        $output = [];
        $groupFields = [];
        $groupFieldsMap = [];

        $moduleBean = $this->getModuleFilter($newModuleFilter);
        $fieldsData = $moduleBean->field_defs ?? [];

        foreach ($fieldsData as $field) {
            $tmpField = [];
            if (isset($field['vname']) && (PMSEEngineUtils::isValidField($field, $type)) &&
                AccessControlManager::instance()->allowFieldAccess($newModuleFilter, $field['name']) &&
                PMSEEngineUtils::isSupportedField($moduleBean->object_name, $field['name'], $type)) {
                // If this is a locked field list AND this field is part of a group
                if ($type === 'RR' && !empty($field['group'])) {
                    // If the group field for this field exists in vardefs then we can skip this field
                    // since it will be covered by the other field
                    // example : phone_alternate has phone_office as its group field and phone_office is defined
                    // in vardefs
                    if (!empty($fieldsData[$field['group']])) {
                        $groupFields[$field['group']] = true;
                    }
                    // If this group field has not been covered before then process it
                    if (empty($groupFields[$field['group']])) {
                        $tmpField['value'] = $field['group'];
                        if (!empty($field['group_label'])) {
                            // use group_label as the label if defined
                            $tmpField['text'] = $this->getFormattedFieldLabel($field['group_label'], $newModuleFilter);
                        } else {
                            // If there is no label defined at all then default to group field as being the label
                            $tmpField['text'] = $field['group'];
                        }
                        // mark the group as being covered so that if any other field has the same group then we
                        // can skip it
                        $groupFields[$field['group']] = true;
                    }
                    $groupFieldsMap[$field['name']] = $field['group'];
                } else {
                    // If the field is relate type then get its id_name as well. This needs to be done before
                    // the replaceItemsValues() call, which changes the type property, below
                    // since some relate type fields are also special fields
                    if ($field['type'] === 'relate' && isset($field['id_name'])) {
                        $tmpField['id_name'] = $field['id_name'];
                    }

                    if (PMSEEngineUtils::specialFields($field, $type)) {
                        $field = array_merge($field, $this->replaceItemsValues($field));
                    }
                    $tmpField['value'] = $field['name'];
                    $tmpField['text'] = $this->getFormattedFieldLabel($field['vname'], $newModuleFilter);

                    // Handle field typing, starting with the vardef type for this field
                    $tmpField['type'] = $field['type'];

                    // If there is a known type for this type, use THAT
                    if (isset($fieldTypes[$field['type']])) {
                        $tmpField['type'] = $fieldTypes[$field['type']];
                    }

                    $tmpField['optionItem'] = 'none';
                    if (in_array($field['type'], ['enum', 'radioenum', 'multienum', 'parent_type'])) {
                        if (!isset($field['options']) || !isset($app_list_strings[$field['options']])) {
                            if (PMSEEngineUtils::specialFields($field, $type)) {
                                $tmpField['optionItem'] = $this->gatewayModulesMethod($field);
                            } elseif ($moduleApi !== null) {
                                $options = getOptionsFromVardef($field);
                                if ($options === false) {
                                    throw ProcessManager\Factory::getException('InvalidData', "No list options found for {$field['name']} in module {$beanModule->getModuleName()}", 1);
                                }
                                $tmpField['optionItem'] = $options;
                            }
                        } else {
                            $tmpField['optionItem'] = $app_list_strings[$field['options']];
                        };
                    }

                    if ($field['type'] == 'bool') {
                        $tmpField['optionItem'] = ['TRUE' => true, 'FALSE' => false];
                    }

                    if (isset($field['required']) && empty($field['required_formula']) && in_array($type, ['CF', 'AC'])) {
                        $tmpField['required'] = $field['required'];
                    }
                    if (isset($field['len'])) {
                        $tmpField['len'] = $field['len'];
                    }
                }

                // For dependent field relationships like a dependent parent child dropdown pass some additional
                // information along so that we are able to lock the parent field as well when the child is locked.
                if (isset($field['visibility_grid']) && (isset($field['visibility_grid']['trigger']))) {
                    $tmpField['trigger'] = $field['visibility_grid']['trigger'];
                }

                if (!empty($tmpField)) {
                    $output[] = $tmpField;
                }
            }
        }

        $text = [];
        foreach ($output as $key => $row) {
            $text[$key] = strtolower($row['text']);
        }
        array_multisort($text, SORT_ASC, $output);

        $res['result'] = $output;
        $res['groupFieldsMap'] = $groupFieldsMap;
        return $res;
    }

    /**
     * Method to display related modules fields
     * @param string $filter
     * @param array $additionalArgs
     * @return object
     */
    public function addRelatedRecord($filter = '', $additionalArgs = [])
    {
        if ($this->isBeanElement($filter)) {
            $newModuleFilter = $filter;
        } else {
            $related = $this->getRelationshipData($filter);
            $newModuleFilter = $related['rhs_module'];
        }

        $res = [];

        $res['name'] = $newModuleFilter;

        $res['search'] = $filter;
        $res['success'] = true;
        $module_strings = return_module_language('en_us', 'ModuleBuilder');

        $fieldTypes = $module_strings['fieldTypes'];
        //add datetimecombo type field from the vardef overrides to point to Datetime type
        $fieldTypes['datetime'] = $fieldTypes['datetimecombo'];

        global $app_list_strings;
        $output = [];
        $moduleBean = $this->getModuleFilter($newModuleFilter);
        $fieldsData = $moduleBean->field_defs ?? [];
        foreach ($fieldsData as $field) {
            $retrieveId = isset($additionalArgs['retrieveId']) && !empty($additionalArgs['retrieveId']) && $field['name'] == 'id' ? $additionalArgs['retrieveId'] : false;
            if (isset($field['vname']) && (PMSEEngineUtils::isValidField($field, 'AC') || $retrieveId)) {
                $tmpField = [];
                $tmpField['value'] = $field['name'];
                $tmpField['text'] = $this->getFormattedFieldLabel($field['vname'], $newModuleFilter);
                $tmpField['type'] = $fieldTypes[$field['type']] ?? ucfirst(
                    $field['type']
                );
                $tmpField['type'] = (isset($tmpField['relationship']) && stristr($tmpField['relationship'], 'email'))
                || stristr($tmpField['value'], 'email') ? 'email' : $tmpField['type'];
                $tmpField['optionItem'] = 'none';
                if ($field['type'] == 'enum') {
                    if (!isset($field['options']) || !isset($app_list_strings[$field['options']])) {
                        $tmpField['optionItem'] = null;
                    } else {
                        $tmpField['optionItem'] = $app_list_strings[$field['options']];
                    }
                }
                if (isset($field['required'])) {
                    $tmpField['required'] = $field['required'];
                }
                if (isset($field['len'])) {
                    $tmpField['len'] = $field['len'];
                }
                $output[] = $tmpField;
            }
        }
        $arrayModules = $this->returnArrayModules($newModuleFilter);
        $customfields = false;
        if (safeCount($arrayModules) > 0) {
            $output = [];
            $customfields = true;
        } else {
            $arrayModules = $this->returnArrayModules('All');
        }
        if (safeCount($arrayModules) > 0) {
            foreach ($fieldsData as $field) {
                $newfield = $this->dataFieldPersonalized($field, $arrayModules, $customfields);
                if (isset($field['vname']) && isset($newfield)) {
                    $tmpField = [];
                    $tmpField['value'] = $newfield['value'] ?? $field['name'];
                    $tmpField['text'] = $newfield['text'] ?? $this->getFormattedFieldLabel($field['vname'], $newModuleFilter);
                    $tmpField['type'] = $fieldTypes[$newfield['type']] ?? ucfirst(
                        $newfield['type']
                    );
                    $tmpField['optionItem'] = 'none';
                    if ($newfield['type'] == 'enum') {
                        $tmpField['optionItem'] = null;
                        if (isset($newfield['method']) && $newfield['method'] != 'default') {
                            $tmpField['optionItem'] = $this->gatewayModulesMethod($newfield['method']);
                        } elseif (isset($newfield['method']) && $newfield['method'] == 'default') {
                            $tmpField['optionItem'] = $app_list_strings[$field['options']];
                        }
                    }
                    if (isset($field['required']) || isset($newfield['required'])) {
                        $tmpField['required'] = $newfield['required'] ?? $field['required'];
                    }
                    if (isset($field['len'])) {
                        $tmpField['len'] = $field['len'];
                    }
                    $output[] = $tmpField;
                }
            }
        }
        $text = [];
        foreach ($output as $key => $row) {
            $text[$key] = strtolower($row['text']);
        }
        array_multisort($text, SORT_ASC, $output);

        $res['result'] = $output;
        return $res;
    }

    /**
     * Method that returns a list of type date fields
     * @param $filter
     * @param $includeCurrent
     * @return object
     */
    public function retrieveDateFields($filter, $includeCurrent = true)
    {
        if (isset($this->beanList[$filter])) {
            $newModuleFilter = $filter;
        } else {
            $related = $this->getRelationshipData($filter);
            $newModuleFilter = $related['rhs_module'];
        }
        $res = []; //new stdClass();
        $res['name'] = $newModuleFilter;
        $res['search'] = $filter;
        $res['success'] = true;

        $output = [];
        $moduleBean = $this->getModuleFilter($newModuleFilter);
        $fieldsData = $moduleBean->field_defs ?? [];
        foreach ($fieldsData as $field) {
            if (isset($field['vname']) && PMSEEngineUtils::isValidField($field)) {
                if ($field['type'] == 'date' || $field['type'] == 'datetimecombo' || $field['type'] == 'datetime') {
                    $tmpField = [];
                    $tmpField['value'] = $field['name'];
                    $tmpField['text'] = $this->getFormattedFieldLabel($field['vname'], $newModuleFilter);
                    $output[] = $tmpField;
                }
            }
        }

        if ($includeCurrent) {
            $arr_Now = [];
            $arr_Now['value'] = 'current_date_time';
            $arr_Now['text'] = 'Current Date Time';
            array_unshift($output, $arr_Now);
        }

        $res['result'] = $output;
        return $res;
    }

    /**
     * Retrieve definition of a Business Rules
     * @param $filter
     * @return object
     */
    public function retrieveRuleSets($filter, $orderBy = 'name')
    {
        $q = new SugarQuery();
        $q->from($this->getRuleSetBean(), 'b');

        // This is the pattern that the client expects
        $q->select([
            ['id', 'value'],
            ['name', 'text'],
        ]);

        // Order is important
        $q->orderBy('name', 'ASC');

        // Join the business rules table on the project table
        $q->joinTable(
            $this->getProjectBean()->getTableName(),
            [
                'alias' => 'p',
            ]
        )->on()->equalsField('p.prj_module', 'b.rst_module');

        // And filter all of it based on the process definition requesting this
        $q->where()->equals('p.id', $filter);
        return $q->execute();
    }

    /**
     * Retrieve list of Business Rules
     * @param $filter
     * @return object
     */
    public function retrieveBusinessRules($filter)
    {
        $processDefinitionBean = $this->getProcessDefinition();
        $activityDefinitionBean = $this->getActivityDefinitionBean();
        $projectBean = $this->getProjectBean();
        $output = [];

        if ($projectBean->retrieve($filter)) {
            $processDefinitionBean->retrieve($projectBean->id);

            $this->sugarQueryObject->select(['id', 'name']);
            $this->sugarQueryObject->from($activityDefinitionBean, ['alias' => 'a']);
            $this->sugarQueryObject->joinTable('pmse_bpmn_activity', ['alias' => 'b'])
                ->on()->equalsField('b.id', 'a.id');
            $this->sugarQueryObject->where()->queryAnd()
                ->addRaw(
                    "b.prj_id='" . $projectBean->id . "' AND b.act_script_type = 'BUSINESS_RULE'"
                );
            $this->sugarQueryObject->select->fieldRaw('b.name');
            $rows = $this->sugarQueryObject->execute();

            foreach ($rows as $key => $definition) {
                $tmpArray = [];
                $tmpArray['value'] = $definition['id'];
                $tmpArray['text'] = $definition['name'];
                $output[] = $tmpArray;
            }
        }
        return $output;
    }

    /**
     * Retrieve list of Emails Templates
     * @param $module
     * @return array
     */
    public function retrieveEmailTemplates($module)
    {
        // Empty modules means no data
        if (empty($module)) {
            return [];
        }

        $q = new SugarQuery();
        $q->from($this->getEmailTemplateBean());

        // This is the pattern that the client expects
        $q->select([
            ['id', 'value'],
            ['name', 'text'],
        ]);

        // Order is important
        $q->orderBy('name', 'ASC');

        // And filter all of it based on the process definition requesting this
        $q->where()->equals('base_module', $module);
        return $q->execute();
    }

    public function getBeanModuleName($beanName)
    {
        return isset($this->beanList[$beanName]) ? $beanName : array_search($beanName, $this->beanList);
    }

    /**
     * Validate the project name to avoid duplicate entries
     * @param $projectName
     * @return object
     */
    public function validateProjectName($projectName)
    {
        $res = [];
        $res['success'] = true;
        $projectObject = $this->getProjectBean();
        $result = true;
        $rsProject = $projectObject->retrieve_by_string_fields(['name' => $projectName]);
        if (!is_null($rsProject)) {
            $result = false;
            $res['message'] = sprintf(
                translate('LBL_PMSE_MESSAGE_THEPROCESSNAMEALREADYEXISTS', 'pmse_Inbox'),
                $projectName
            );
        }
        $res['result'] = $result;
        return $res;
    }

    /**
     * Validate the email template name to avoid duplicate entries
     * @param $emailName
     * @param $id
     * @return object
     */
    public function validateEmailTemplateName($emailName, $id)
    {
        $res = new stdClass();
        $res->success = true;
        //        $emailObject = new BpmEmailTemplate();
        $emailObject = $this->getEmailTemplateBean();
        $result = true;
        $where = "name ='" . $emailName . "'" . (isset($id) ? " and id != '" . $id . "'" : '');
        $rsEmail = $emailObject->get_full_list('', $where);

        if (!empty($rsEmail)) {
            $result = false;
            $res->message = sprintf(
                translate('LBL_PMSE_MESSAGE_THEEMAILTEMPLATENAMEALREADYEXISTS', 'ProcessMaker'),
                $emailName
            );
        }
        $res->result = $result;
        return $res;
    }

    /**
     * Validate the business rules name to avoid duplicate entries
     * @param $brName
     * @param null $brId
     * @return object
     */
    public function validateBusinessRuleName($brName, $brId = null)
    {
        $res = []; //new stdClass();
        $res['success'] = true;
        //        $brObject = new BpmRuleSet();
        $brObject = $this->getRuleSetBean();
        $result = true;
        $where = "rst_name ='" . $brName . "'" . (isset($brId) ? " AND rst_uid !='" . $brId . "'" : '');
        $rsBr = $brObject->get_full_list('', $where);
        if (!empty($rsBr)) {
            $result = false;
            $res['message'] = sprintf(
                translate('LBL_PMSE_MESSAGE_BUSINESSRULENAMEALREADYEXISTS', 'ProcessMaker'),
                $brName
            );
        }
        $res['result'] = $result;
        return $res;
    }


    /**
     * Method return array of users (current_user, supervisor, owner)
     * @return object
     */
    public function defaultUsersList()
    {
        //        $res = new stdClass();
        //        $res->success = true;
        $tmpArray = [
            ['value' => 'current_user', 'text' => translate('LBL_PMSE_FORM_OPTION_CURRENT_USER', 'pmse_Project')],
            ['value' => 'supervisor', 'text' => translate('LBL_PMSE_FORM_OPTION_SUPERVISOR', 'pmse_Project')],
            ['value' => 'owner', 'text' => translate('LBL_PMSE_FORM_OPTION_RECORD_OWNER', 'pmse_Project')],
        ];
        //$res->result = $tmpArray;
        return $tmpArray;
    }

    /**
     * Method that returns the user roles as Sugar
     * @return object
     */
    public function rolesList()
    {
        $userRoles = ACLRole::getAllRoles(true);
        $tmpArray = [];
        $tmpArray[] = [
            'value' => 'is_admin',
            'text' => translate('LBL_PMSE_FORM_OPTION_ADMINISTRATOR', 'pmse_Project'),
        ];
        foreach ($userRoles as $role) {
            $tmpArray[] = ['value' => $role['id'], 'text' => $role['name']];
        }
        return $tmpArray;
    }

    /**
     * Method to validate whether a case been claimed
     * @param $casID
     * @param $casIndex
     * @return object
     */
    public function validateReclaimCase($casID, $casIndex)
    {
        //TODO Review functionality
        $res = []; //new stdClass();
        $res['success'] = true;
        $res['result'] = false;
        //$caseBean = new BpmInbox();
        $caseBean = $this->getInboxBean();
        $this->sugarQueryObject->select(['a.cas_id']);
        $this->sugarQueryObject->from($caseBean, ['alias' => 'a']);
        $this->sugarQueryObject->joinTable('pmse_bpm_flow', ['joinType' => 'LEFT', 'alias' => 'b'])
            ->on()->equalsField('a.cas_id', 'b.cas_id');
        $this->sugarQueryObject->where()
            ->equals('b.cas_id', $casID)
            ->equals('a.cas_index', $casIndex);

        $rows = $this->sugarQueryObject->execute();


        $caseData = $rows[0];
        $res['message'] = translate('LBL_PMSE_LABEL_ERROR_INVALIDCLAIM', 'pmse_Project');
        if ($caseData['cas_start_date'] == '') {
            $res['result'] = true;
        }

        return $res;
    }

    /**
     * @codeCoverageIgnore
     */
    private function returnArrayModules($module)
    {
        $arraymodules = [
            'Notes' => [
                ['name' => 'name', 'type' => 'name'],
                [
                    'name' => 'assigned_user_name',
                    'type' => 'enum',
                    'value' => 'assigned_user_id',
                    'method' => 'assignedUsers',
                ], //TYPE original relate
                ['name' => 'description', 'type' => 'text'],
                ['name' => 'portal_flag', 'type' => 'bool', 'required' => false],
                //array('name'=>'contact_name', 'type'=>'relate')
            ],
            'Tasks' => [
                ['name' => 'name', 'type' => 'name'],
                ['name' => 'date_start', 'type' => 'datetimecombo'],
                ['name' => 'date_due', 'type' => 'datetimecombo'],
                ['name' => 'priority', 'type' => 'enum', 'method' => 'default'],
                ['name' => 'description', 'type' => 'text'],
                ['name' => 'status', 'type' => 'enum', 'method' => 'default'],
                //array('name'=>'contact_name', 'type'=>'relate'),
                [
                    'name' => 'assigned_user_name',
                    'type' => 'enum',
                    'value' => 'assigned_user_id',
                    'method' => 'assignedUsers',
                ], //TYPE original relate
            ],
            'Meetings' => [
                ['name' => 'name', 'type' => 'name'],
                ['name' => 'type', 'type' => 'enum', 'method' => 'default'],
                ['name' => 'date_start', 'type' => 'datetimecombo'],
                ['name' => 'date_end', 'type' => 'datetimecombo'],
                ['name' => 'duration_hours', 'type' => 'int'],
                ['name' => 'reminder_time', 'type' => 'enum', 'text' => 'Reminder Popup', 'method' => 'default'],
                [
                    'name' => 'email_reminder_time',
                    'type' => 'enum',
                    'text' => 'Reminder Email all invitees',
                    'method' => 'default',
                ],
                ['name' => 'status', 'type' => 'enum', 'method' => 'default'],
                ['name' => 'location', 'type' => 'varchar'],
                [
                    'name' => 'assigned_user_name',
                    'type' => 'enum',
                    'value' => 'assigned_user_id',
                    'method' => 'assignedUsers',
                ], //TYPE original relate
            ],
            'Calls' => [
                ['name' => 'name', 'type' => 'name'],
                ['name' => 'date_start', 'type' => 'datetimecombo'],
                ['name' => 'duration_hours', 'type' => 'int'],
                ['name' => 'description', 'type' => 'text'],
                ['name' => 'status', 'type' => 'enum', 'method' => 'default'],
                ['name' => 'reminder_time', 'type' => 'enum', 'text' => 'Reminder Popup', 'method' => 'default'],
                [
                    'name' => 'email_reminder_time',
                    'type' => 'enum',
                    'text' => 'Reminder Email all invitees',
                    'method' => 'default',
                ],
                [
                    'name' => 'assigned_user_name',
                    'type' => 'enum',
                    'value' => 'assigned_user_id',
                    'method' => 'assignedUsers',
                ], //TYPE original relate
            ],
            'All' => [
                [
                    'name' => 'assigned_user_name',
                    'type' => 'enum',
                    'value' => 'assigned_user_id',
                    'method' => 'assignedUsers',
                ], //TYPE original relate
            ],
        ];
        return $arraymodules[$module] ?? [];
    }

    /**
     * Insert field personalized
     * @codeCoverageIgnore
     * @param $field
     * @param $arrayModules
     * @param bool $customfields
     * @return mixed|null
     */
    private function dataFieldPersonalized($field, $arrayModules, $customfields = false)
    {
        $row = array_shift($arrayModules);
        while (is_array($row)) {
            if (isset($field['name']) && $field['name'] == $row['name']) {
                return $row;
                break;
            } elseif ($customfields && isset($field['source']) && $field['source'] == 'custom_fields') {
                return $field;
                break;
            }
            $row = array_shift($arrayModules);
        }
        return null;
    }

    /**
     * @codeCoverageIgnore
     */
    private function gatewayModulesMethod($def)
    {
        $values = '';
        switch ($def['name']) {
            case 'assigned_user_id':
                $users = $this->retrieveUsers();
                $values = $users;
                break;
            case 'created_by':
            case 'modified_user_id':
                $users = $this->retrieveUsers();
                foreach ($users as $rows) {
                    $newUsers[$rows['value']] = $rows['text'];
                }
                $values = $newUsers;
                break;
            default:
                $values = null;
                break;
        }
        return $values;
    }

    /**
     * @codeCoverageIgnore
     */
    private function replaceItemsValues($def)
    {
        $field = [];
        switch ($def['name']) {
            case 'assigned_user_id':
                $field['type'] = 'user';
                $field['vname'] = 'LBL_ASSIGNED_TO';
                $field['required'] = true;
                break;
            case 'created_by_name':
            case 'modified_by_name':
            case 'reports_to_name':
                $field['type'] = 'user';
                break;
            case 'teams':
                $field['type'] = 'team_list';
                break;
            default:
                $result = $field;
        }
        return $field;
    }

    /**
     * Returns the array required when the event, gateway or activity definition is required
     * @param $filter Request type
     * @return array Related Array
     */
    public function getRelatedSearch($filter, $args)
    {
        $out = [];
        switch ($filter) {
            case 'modules':
                $out = $this->retrieveModules('');
                break;
            case 'fields':
                break;
        }
        return $out;
    }

    /**
     * Puts CRM Data endpoint results in format to be used by the client
     * @param $result Set of results
     * @param $filter Filter or search query
     * @return array
     */
    private function retrieveCrmData($result, $filter)
    {
        $output = [];
        $output['success'] = true;
        $output['search'] = $filter;
        $output['result'] = $result;
        return $output;
    }

    /**
     *
     * @param PMSEObserver $observer
     */
    public function attach($observer)
    {
        $i = array_search($observer, $this->observers);
        if ($i === false) {
            $this->observers[] = $observer;
        }
    }

    /**
     *
     * @param PMSEObserver $observer
     */
    public function detach($observer)
    {
        if (!empty($this->observers)) {
            $i = array_search($observer, $this->observers);
            if ($i !== false) {
                unset($this->observers[$i]);
            }
        }
    }

    /**
     *
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * @param $filter
     * @param ModuleApi $moduleApi
     * @param string $relationship default 'all'
     * @param $baseModule
     * @param string $type
     * @return array
     */
    private function getAllRelated($filter, ModuleApi $moduleApi, string $relationship, string $baseModule, $type = '')
    {
        $result = [];
        $result['success'] = true;
        $res = $this->retrieveRelatedBeans($filter, $relationship);
        $arr = [];
        if (is_array($res['result']) && !empty($res['result'])) {
            foreach ($res['result'] as $key => $value) {
                //$aux = $this->addRelatedRecord($value['value']);
                $aux = $this->retrieveFields($value['value'], $moduleApi, $type, $baseModule);
                $value['fields'] = $aux['result'];
                $arr[] = $value;
            }
        } else {
            $result['success'] = false;
        }
        $result['result'] = $arr;
        return $result;
    }

    /**
     * Gets OutboundEmail accounts for bpm to use. By setting the `bpm_request` registry value,
     * OutboundEmail ignores visibility and acls to retrieve all accounts except the Portal user's account
     * and the Email archiver account
     *
     * @param array $args REST API arguments.
     * @return array OutboundEmail accounts
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function getOutboundEmailAccounts(array $args)
    {
        // indicate we are coming from SugarBPM
        ProcessManager\Registry\Registry::getInstance()->set('bpm_request', true, true);
        $outboundFilterApi = new PMSEFilterOutboundEmailsApi();
        $args['module'] = 'OutboundEmail';
        $api = $this->getService();
        if (empty($api)) {
            throw new Exception('Could not find the request api service');
        }
        $ret = $outboundFilterApi->filterList($api, $args);
        ProcessManager\Registry\Registry::getInstance()->set('bpm_request', false, true);
        return $ret;
    }

    /**
     * Clear event criteria (evn_criteria) from the database table
     * @param event id (evn_uid)
     */
    public function clearEventCriteria($eventUid)
    {
        $res = new stdClass();
        $res->success = false;

        if (empty($eventUid)) {
            return $res;
        };

        $q = $this->sugarQueryObject;
        $q->select(['id']);
        $q->from(BeanFactory::newBean('pmse_BpmnEvent'));
        $q->where()->equals('evn_uid', $eventUid);
        $result = $q->execute();

        if (is_array($result)) {
            if (safeCount($result) > 0) {
                $eventDefinitionId = $result[0]['id'];
                $sql = "UPDATE pmse_bpm_event_definition
                    SET evn_criteria = ''
                    WHERE id = " . $this->db->quoted($eventDefinitionId);
                $res->success = (bool)$this->db->Query($sql);
            } else {
                // cannot run update since the row is not there
                $res->success = true;
            }
        }

        return $res;
    }

    /*
     * Given a group field get the multiple fields which are part of that group
     * @param string $module module name
     * @param string $proLockedVariables locked fields json string
     * @return string json string of expanded locked fields
     */
    public function getMultiLockedFieldsFromGroupField($module, $proLockedVariables)
    {
        $groupFieldsArray = [];
        $newLockedFieldsStr = '';

        $moduleBean = BeanFactory::newBean($module);
        $fieldsDataArray = $moduleBean->field_defs ?? [];
        $lockedVarsArray = json_decode($proLockedVariables);
        foreach ($fieldsDataArray as $key => $fieldsArray) {
            if (!empty($fieldsArray['group']) && (safeInArray($fieldsArray['group'], $lockedVarsArray))) {
                $groupFieldsArray[$fieldsArray['group']][] = $fieldsArray['name'];
                if ((!empty($fieldsDataArray[$fieldsArray['group']])) &&
                    (array_search($fieldsArray['group'], $groupFieldsArray) === false)) {
                    $groupFieldsArray[$fieldsArray['group']][] = $fieldsArray['group'];
                }
            }
        }
        if (!empty($groupFieldsArray)) {
            foreach ($groupFieldsArray as $groupField => $comboFieldsArray) {
                $index = array_search($groupField, $lockedVarsArray);
                if ($index !== false) {
                    unset($lockedVarsArray[$index]);
                }
                foreach ($comboFieldsArray as $comboField) {
                    $lockedVarsArray[] = $comboField;
                }
            }
        }

        if (!empty($lockedVarsArray)) {
            $newLockedFieldsStr = json_encode(array_values($lockedVarsArray));
        }


        return $newLockedFieldsStr;
    }

    public function getFormattedFieldLabel($fieldLabel, $module)
    {
        return str_replace(':', '', translate($fieldLabel, $module));
    }
}

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

use Sugarcrm\Sugarcrm\Util\Files\FileLoader;

/**
 * Class RelatedActivitiesApi
 */
class RelatedActivitiesApi extends HistoryApi
{
    /**
     * {@inheritDoc}
     */
    protected $moduleList = [];

    /**
     * {@inheritDoc}
     */
    protected $moduleFilters = [];

    /**
     * {@inheritDoc}
     */
    protected $validFields = [];

    /**
     * Create event Id
     * @var string
     */
    protected $createEventId = '';

    /**
     * {@inheritDoc}
     */
    public function registerApiRest()
    {
        return [
            /**
             * @deprecated 'recordListView' endpoint is deprecated and will be removed in a future release.
             * Use 'activitiesList' endpoint instead.
             */
            'recordListView' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'link', 'related_activities'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'getRelatedActivities',
                'minVersion' => '11.7',
                'shortHelp' => 'Deprecated api kept for backward compatibility',
                'longHelp' => 'include/api/help/related_activities.html',
            ],
            'activitiesList' => [
                'reqType' => 'POST',
                'path' => ['<module>', '?', 'related_activities'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'getRelatedActivities',
                'minVersion' => '11.23',
                'shortHelp' => 'Get the related activity records for a specific record',
                'longHelp' => 'include/api/help/related_activities.html',
            ],
        ];
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @param string $acl
     * @return array
     */
    public function getRelatedActivities(ServiceBase $api, array $args, string $acl = 'list'): array
    {
        global $timedate;

        $this->requireArgs($args, [
            'module',
            'record',
        ]);

        $this->getEnabledModules($api, $args);

        if (!empty($args['module_filters'])) {
            $this->moduleFilters = $args['module_filters'];
        }

        if (!empty($args['module_list'])) {
            $moduleList = explode(',', $args['module_list']);
            if (safeInArray('Audit', $moduleList)) {
                $this->auditSetup($api, $args);
            }
        }

        // add extra field for sort stability
        if (!empty($args['order_by'])) {
            $args['order_by'] .= ',id:desc';
        }

        // get a list of relate records ordered by date
        $data = $this->filterModuleList($api, $args, $acl);
        // get all changes
        $changes = $this->getAuditRecords($api, $args);
        $records = [];

        foreach ($data['records'] as $record) {
            $module = $record['_module'];
            if ($module === 'Audit') {
                $index = array_search($record['id'], array_column($changes, 'id'));
                if ($index !== false) {
                    $changes[$index] = $this->formatAuditRecord($args, $changes[$index]);
                    $changes[$index]['_module'] = $module;
                    $records[] = $changes[$index];
                }
            } else {
                $fields = $args['field_list'] ?? [];
                $moduleFields = $fields[$module] ?? '';
                $newRecord = $this->getFullRecord($api, $record, $moduleFields);
                if ($record['date_linked'] ?? '') {
                    $date = $timedate->fromDbType($record['date_linked'], 'datetime');
                    $newRecord['date_linked'] = $timedate->asIso($date);
                }
                if (!empty($record['link_name'])) {
                    $newRecord['_is_external_link'] = $this->isExternalLink(
                        $args['module'],
                        $args['record'],
                        $record['link_name'],
                        $module,
                        $record['id']
                    ) ? 1 : 0;
                }
                $records[] = $newRecord;
            }
        }

        $data['records'] = $records;
        return $data;
    }

    /**
     * Checks if a record is linked to an external user.
     * @param string $module
     * @param string $record
     * @param string $linkName
     * @param string $linkModule
     * @param string $linkRecord
     * @return boolean
     */
    protected function isExternalLink(
        string $module,
        string $record,
        string $linkName,
        string $linkModule,
        string $linkRecord
    ): bool {
        $bean = BeanFactory::getBean($module, $record);
        if ($bean && $bean->load_relationship($linkName)) {
            // return 'is_external_link' for records created by external users
            $relObj = $bean->$linkName->getRelationshipObject();
            if ($relObj && get_class($relObj) === 'PersonM2MRelationship') {
                $linkBean = BeanFactory::getBean($linkModule, $linkRecord);
                if ($linkBean) {
                    return !$relObj->relationship_exists($bean, $linkBean);
                }
            }
        }
        return false;
    }

    /**
     * Get timeline settings.
     * @return array
     */
    protected function getTimelineSettings()
    {
        $admin = BeanFactory::getBean('Administration');
        return $admin->retrieveSettings('timeline', true)->settings;
    }

    /**
     * Get enabled modules and links for a module's timeline.
     * @param ServiceBase $api
     * @param array $args
     */
    protected function getEnabledModules(ServiceBase $api, array $args)
    {
        $this->moduleList = [];
        $module = $args['module'];
        $timelineConfig = $this->getTimelineSettings();
        $timelineModuleConfig = $timelineConfig['timeline_' . $module] ?? [];

        if (empty($timelineModuleConfig['enabledModules'])) {
            $this->moduleList = $this->getDefaultModuleList($module);
        } else {
            $bean = BeanFactory::getBean($args['module'], $args['record']);

            if (empty($bean)) {
                throw new SugarApiExceptionNotFound(
                    sprintf(
                        'Could not find parent record %s in module: %s',
                        $args['record'],
                        $args['module']
                    )
                );
            };
            foreach ($timelineModuleConfig['enabledModules'] as $linkName) {
                if ($bean->load_relationship($linkName)) {
                    $linkModule = $bean->$linkName->getRelatedModuleName();
                    $this->moduleList[$linkName] = $linkModule;
                }
            }
        }
        $this->moduleList['changes'] = 'Audit';
    }

    /**
     * Get a list of default modules enabled for timeline.
     * @param string $module
     * @return array
     */
    protected function getDefaultModuleList(string $module): array
    {
        $meta = new MetaDataManager();
        $bean = BeanFactory::getBean($module);
        return $meta->getDefaultTimelineModules($bean);
    }

    /**
     * Get related module name given a link.
     * @param string $module
     * @param string $link
     * @return SugarBean|NULL
     */
    protected function getRelatedModule(string $module, string $link)
    {
        $bean = BeanFactory::getBean($module);
        if ($bean && $bean->load_relationship($link)) {
            return $bean->$link->getRelatedModuleName();
        }
        return null;
    }

    /**
     * Setup configs for Audit query.
     * @param ServiceBase $api
     * @param array $args
     */
    protected function auditSetup(ServiceBase $api, array $args)
    {
        // need extra fields for query
        $this->validFields = [
            'assigned_user_id',
            'event_id',
        ];

        // add filter to exclude changes from 'create' event
        $this->moduleFilters['Audit'] = $this->moduleFilters['Audit'] ?? [];
        $eventId = $this->createEventId = $this->getCreateEventId($args['record']);

        if ($eventId) {
            $this->moduleFilters['Audit'][] = [
                'event_id' => [
                    '$not_equals' => $eventId,
                ],
            ];

            if (($args['add_create_record'] ?? 0) === 1) {
                $bean = $this->getBeanFromArgs($args);
                $createId = $this->getFirstCreateId($bean, $eventId);
                if ($createId) {
                    $this->moduleFilters['AuditCreate'][] = [
                        'id' => [
                            '$equals' => $createId,
                        ],
                    ];
                }
            }
        }

        // add filter to get changes for selected fields
        $fields = $this->getAuditFields($api, $args);

        if (!empty($fields)) {
            $this->moduleFilters['Audit'][] = [
                'field_name' => [
                    '$in' => $fields,
                ],
            ];
        }
    }

    /**
     * Get field_list for Audit and check access
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    protected function getAuditFields(ServiceBase $api, array $args): array
    {
        $fields = [];

        if (isset($args['field_list']) && !empty($args['field_list']['Audit'])) {
            $fields = explode(',', $args['field_list']['Audit']);
            $module = $args['module'];
            $fields = array_filter($fields, function ($field) use ($module) {
                return SugarACL::checkField($module, $field, 'access');
            });
        }

        return $fields;
    }

    /**
     * Get audit event id for the 'create' event for a bean
     * @param string $beanId
     * @return string
     */
    protected function getCreateEventId(string $beanId): string
    {
        $id = '';
        $qb = DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        // 'create' event should be the first 'update' event for a bean
        $query = $qb->select('id')
            ->from('audit_events')
            ->where($qb->expr()->eq('parent_id', $qb->expr()->literal($beanId)))
            ->andWhere($qb->expr()->eq('type', $qb->expr()->literal('update')))
            ->orderBy('date_created', 'ASC')
            ->setMaxResults(1);
        $result = $query->execute();

        if ($result) {
            $row = $result->fetchAssociative();
            $id = $row['id'] ?? '';
        }

        return $id;
    }

    /**
     * Get the first audit record id for the 'create' event for a bean
     * @param SugarBean $bean
     * @param string $eventId
     * @return string
     */
    protected function getFirstCreateId(SugarBean $bean, string $eventId): string
    {
        $id = '';
        $auditTable = $bean->getTableName() . '_audit';
        $qb = DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        $query = $qb->select('id')
        ->from($auditTable)
        ->where($qb->expr()->eq('event_id', $qb->expr()->literal($eventId)))
        ->orderBy('date_created', 'ASC')
        ->setMaxResults(1);
        $result = $query->execute();
        if ($result) {
            $row = $result->fetchAssociative();
            $id = $row['id'] ?? '';
        }
        return $id;
    }

    /**
     * Get full data for a bean
     * @param ServiceBase $api
     * @param array $record
     * @param array $fields
     * @return array
     */
    protected function getFullRecord(ServiceBase $api, array $record, string $fields = ''): array
    {
        $moduleApi = new ModuleApi();
        $metadataManager = MetaDataManager::getManager($api->platform);
        // use the same logic as frontend to decide which view to return data for
        $module = $record['_module'];
        $view = empty($metadataManager->getModuleView($module, 'preview')) ? 'record' : 'preview';
        $args = [
            'module' => $module,
            'record' => $record['id'],
            'view' => $view,
            'erased_fields' => true,
        ];
        if (!empty($fields)) {
            $args['fields'] = $fields;
        }
        return $moduleApi->retrieveRecord($api, $args);
    }

    /**
     * Get audit records for a bean
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    protected function getAuditRecords(ServiceBase $api, array $args): array
    {
        // global $focus is needed by some Audit functions
        global $focus;
        $focus = BeanFactory::getBean($args['module'], $args['record']);
        $audit = BeanFactory::getBean('Audit');
        $records = $audit->getAuditLog($focus);
        unset($focus);
        return $records;
    }

    /**
     * Format array of records
     *
     * @param array $args
     * @param array $record
     * @return array
     */
    protected function formatAuditRecord(array $args, array $record) : array
    {
        $record['event_action'] = 'update';

        if (isset($record['event_id'])) {
            if ($record['event_id'] === $this->createEventId) {
                $record['event_action'] = 'create';
            }

            if (isset($record['created_by'])) {
                $userBean = $this->retrieveUserBean($record['created_by']);
                if ($userBean) {
                    $record['created_by_name'] = $userBean->full_name;
                }
            } else {
                $focus = $this->getBeanFromArgs($args);
                if ($focus && $focus->created_by) {
                    $record['created_by_name'] = $focus->created_by_name;
                    $record['created_by'] = $focus->created_by;
                }
            }
        }

        return $record;
    }

    /**
     * @param string $id
     * @return SugarBean|null
     */
    protected function retrieveUserBean(string $id): ?SugarBean
    {
        return BeanFactory::retrieveBean('Users', $id);
    }

    /**
     * Return SugarBean for module
     *
     * @param array $args
     * @return SugarBean|null
     */
    protected function getBeanFromArgs(array $args) : ?SugarBean
    {
        return BeanFactory::getBean($args['module'], $args['record']);
    }

    /**
     * {@inheritDoc}
     * @see RelateApi::getLinkBean()
     */
    protected function getLinkBean(SugarBean $record, string $linkName): SugarBean
    {
        // 'changes' is purposely not added to vardefs to avoid being picked up accidently by other processes
        if ($linkName === 'changes') {
            if (!$record->$linkName) {
                // add link field
                $relName = strtolower($record->getModuleName()) . '_audit';
                if (!SugarRelationshipFactory::getInstance()->relationshipExists($relName)) {
                    throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $relName);
                }
                $linkDef = [
                    'name' => 'changes',
                    'type' => 'link',
                    'relationship' => $relName,
                    'source' => 'non-db',
                    'vname' => 'LBL_CHANGES',
                ];
                $record->$linkName = new Link2($linkName, $record, $linkDef);
            }
            $linkModuleName = 'Audit';
            $linkSeed = BeanFactory::newBean($linkModuleName);
            $linkSeed->table_name = $record->get_audit_table_name();
            if (!$linkSeed->ACLAccess('list')) {
                throw new SugarApiExceptionNotAuthorized('No access to list records for module: ' . $linkModuleName);
            }
        } else {
            $linkSeed = parent::getLinkBean($record, $linkName);
        }
        return $linkSeed;
    }
}

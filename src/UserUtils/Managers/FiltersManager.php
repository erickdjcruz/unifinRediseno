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

namespace Sugarcrm\Sugarcrm\UserUtils\Managers;

use BeanFactory;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerFiltersPayload;
use Sugarcrm\Sugarcrm\Util\Uuid;
use SugarQuery;

class FiltersManager extends Manager
{
    /**
     * The source user
     * @var string
     */
    private $sourceUser;

    /**
     * The destination users
     *
     * @var array
     */
    private $destinationUsers;

    /**
     * The filters
     *
     * @var array
     */
    private $filters;

    /**
     * The modules
     *
     * @var mixed
     */
    private $modules;

    /**
     * Shows if should use a schedule job
     *
     * @var bool
     */
    protected $useScheduledJob = false;

    /**
     * Constructor
     *
     * @param InvokerFiltersPayload $payload
     */
    public function __construct(InvokerFiltersPayload $payload)
    {
        $this->payload = $payload;
        $this->sourceUser = $payload->getSourceUser();
        $this->destinationUsers = $payload->getDestinationUsers();
        $this->filters = $payload->getFilters();
        $this->modules = $payload->getModules();

        if (safeCount($this->destinationUsers) > self::MAX_USER) {
            $this->useScheduledJob = true;
        }
    }

    /**
     * Clones filters.
     */
    public function clone(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destinationUserId) {
            $this->deleteFilters($destinationUserId, $this->modules);
            $this->cloneFilters($this->sourceUser, $destinationUserId, $this->modules);
        }
    }

    /**
     * Copy filters
     */
    public function copy(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destinationUserId) {
            $this->copyFilters($destinationUserId, $this->filters);
        }
    }

    /**
     * Delete filters
     */
    public function delete(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        if (safeCount($this->modules) > 0) {
            foreach ($this->destinationUsers as $destinationUserId) {
                $this->deleteFilters($destinationUserId, $this->modules);
            }
        }

        if (safeCount($this->filters) > 0) {
            foreach ($this->filters as $filterId) {
                $this->deleteFilter($filterId);
            }
        }
    }

    /**
     * Copy Filters
     *
     * @param string $sourceUser
     * @param string $destinationUser
     * @param array $filters
     */
    private function copyFilters(string $destinationUser, array $filters): void
    {
        foreach ($filters as $filterId) {
            $filter = self::getFilter($filterId);

            if (!$filter) {
                continue;
            }

            $hasFilter = self::hasFilter($destinationUser, $filter);
            if ($hasFilter) {
                continue;
            }

            $this->copyFilter($destinationUser, $filter);
        }
    }

    /**
     * Clones filters for a user
     *
     * @param string $sourceUser
     * @param string $destinationUserId
     * @param null|array $modules
     */
    private function cloneFilters(string $sourceUser, string $destinationUser, ?array $modules): void
    {
        if (safeCount((array)$modules) > 0) {
            foreach ($modules as $module) {
                $this->cloneFiltersFor($sourceUser, $destinationUser, $module);
            }
        } else {
            $this->cloneFiltersFor($sourceUser, $destinationUser, null);
        }
    }


    /**
     * Copy filters for a user
     *
     * @param string $sourceUser
     * @param string $destinationUser
     * @param null|string $module
     */
    private function cloneFiltersFor(string $sourceUser, string $destinationUser, ?string $module): void
    {
        $sourceFilters = $this->getFilters($sourceUser, $module);

        foreach ($sourceFilters as $filter) {
            $hasFilter = self::hasFilter($destinationUser, $filter);
            if ($hasFilter) {
                continue;
            }
            $this->copyFilter($destinationUser, $filter);
        }
    }

    /**
     * Copy filter.
     *
     * @param string $destinationUser
     * @param array $filter
     */
    private function copyFilter(string $destinationUser, array $filter): void
    {
        $db = \DBManagerFactory::getInstance();

        $user = BeanFactory::retrieveBean('Users', $destinationUser);

        if (!$user) {
            return;
        }

        $filter['id'] = Uuid::uuid4();
        $filter['created_by'] = $destinationUser;
        $filter['assigned_user_id'] = $destinationUser;
        $filter['modified_user_id'] = $destinationUser;
        $filter['team_id'] = $user->team_id;
        $filter['team_set_id'] = $user->team_set_id;

        $filterBean = BeanFactory::newBean('Filters');
        $filterBean->fromArray($filter);
        $db->insert($filterBean);
    }

    /**
     * Gets filters for a user
     *
     * @param string $sourceUser
     * @param null|string $module
     * @return array
     */
    public function getFilters(string $sourceUser, ?string $module): array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('Filters'), ['team_security' => false]);
        $sugarQuery->where()->equals('created_by', $sourceUser);

        if ($module) {
            $sugarQuery->where()->equals('module_name', $module);
        }

        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Delete filters for a user
     *
     * @param string $userId
     * @param null|array $modules
     */
    private function deleteFilters(string $userId, ?array $modules): void
    {
        if (safeCount((array)$modules) > 0) {
            foreach ($modules as $module) {
                $this->deleteFiltersFor($userId, $module);
            }
        } else {
            $this->deleteFiltersFor($userId, null);
        }
    }

    /**
     * Delete filters for a certain user
     *
     * @param string $userId
     * @param null|string $module
     */
    private function deleteFiltersFor(string $userId, ?string $module): void
    {
        $db = \DBManagerFactory::getInstance();

        $where = [
            'created_by' => $userId,
            'module_name' => $module,
        ];

        if (!$module) {
            $where = [
                'created_by' => $userId,
            ];
        }

        $dashboardsBean = \BeanFactory::newBean('Filters');
        $fieldDefs = $dashboardsBean->getFieldDefinitions();
        $db->updateParams($dashboardsBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], $where);
    }

    /**
     * Deletes a filter
     *
     * @param string $filterId
     */
    private function deleteFilter(string $filterId): void
    {
        $db = \DBManagerFactory::getInstance();

        $filterBean = \BeanFactory::newBean('Filters');
        $fieldDefs = $filterBean->getFieldDefinitions();
        $db->updateParams($filterBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], [
            'id' => $filterId,
        ]);
    }


    /**
     * Returns a filter data
     *
     * @param string $filterId
     * @return array|null
     */
    public static function getFilter(string $filterId): ?array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('Filters'), ['team_security' => false]);
        $sugarQuery->where()->equals('id', $filterId);
        $result = $sugarQuery->execute();

        if (is_array($result) && safeCount($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * Checks if the user already ahas this filter
     *
     * @param string $userId
     * @param null|array $filter
     * @return bool
     */
    public static function hasFilter(string $userId, ?array $filter): bool
    {
        $db = \DBManagerFactory::getInstance();

        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('Filters'), ['team_security' => false]);
        $sugarQuery->where()->equals('created_by', $userId);
        $sugarQuery->where()->equals('name', $filter['name']);
        $sugarQuery->where()->equals($db->convert('filter_template', 'text2char'), $filter['filter_template']);
        $sugarQuery->where()->equals($db->convert('filter_definition', 'text2char'), $filter['filter_definition']);
        $result = $sugarQuery->execute();

        if (safeCount($result) > 0) {
            return true;
        }

        return false;
    }
}

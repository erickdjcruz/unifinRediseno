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
use Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads\InvokerDashboardsPayload;
use Sugarcrm\Sugarcrm\Util\Uuid;
use SugarQuery;

/**
 * The DashboardManager class handles dashboard actions
 */
class DashboardManager extends Manager
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
     * The dashboards in the command
     *
     * @var array
     */
    private $dashboards;

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
     * @param InvokerDashboardsPayload $payload
     */
    public function __construct(InvokerDashboardsPayload $payload)
    {
        $this->payload = $payload;
        $this->sourceUser = $payload->getSourceUser();
        $this->destinationUsers = $payload->getDestinationUsers();
        $this->dashboards = $payload->getDashboards();
        $this->modules = $payload->getModules();

        if (safeCount($this->destinationUsers) > self::MAX_USER) {
            $this->useScheduledJob = true;
        }
    }

    /**
     * Clones dashboards.
     */
    public function clone(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destinationUserId) {
            $this->deleteDashboards($destinationUserId, $this->modules);
            $this->cloneDashboards($this->sourceUser, $destinationUserId, $this->modules);
        }
    }

    /**
     * Copies dashboards.
     */
    public function copy(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        foreach ($this->destinationUsers as $destinationUserId) {
            $this->copyDashboards($destinationUserId, $this->dashboards);
        }
    }

    /**
     * Deletes dashboards
     */
    public function delete(): void
    {
        if ($this->useScheduledJob) {
            $this->cloneWithScheduledJob();
            return;
        }

        if (safeCount($this->modules) > 0) {
            foreach ($this->destinationUsers as $destinationUserId) {
                $this->deleteDashboards($destinationUserId, $this->modules);
            }
        }

        if (safeCount($this->dashboards) > 0) {
            foreach ($this->dashboards as $dashboardId) {
                $this->deleteDashboard($dashboardId);
            }
        }
    }

    /**
     * Copy dashboards from a user to another
     *
     * @param string $destinationUser
     * @param array $dashboards
     */
    private function copyDashboards(string $destinationUser, array $dashboards): void
    {
        foreach ($dashboards as $dashboardId) {
            $this->copyDashboard($destinationUser, $dashboardId);
        }
    }

    /**
     * Copy one dashboard to a certain user
     *
     * @param string $destinationUser
     * @param string|array $dashboard
     */
    private function copyDashboard(string $destinationUser, $dashboard): void
    {
        $db = \DBManagerFactory::getInstance();

        if (is_string($dashboard)) {
            $dashboard = $this->retrieveDashboard($dashboard);
        }

        if (!is_array($dashboard)) {
            return;
        }

        $destinationUserBean = BeanFactory::retrieveBean('Users', $destinationUser);
        $sourceUserId = $dashboard['assigned_user_id'];
        $initialDashboardId = $dashboard['id'];

        if (!$destinationUser) {
            return;
        }

        $metadata = $this->copyDashboardFilters($dashboard['metadata'], $destinationUser);

        $dashboard['id'] = Uuid::uuid4();
        $dashboard['metadata'] = $metadata;
        $dashboard['assigned_user_id'] = $destinationUser;
        $dashboard['created_by'] = $destinationUser;
        $dashboard['modified_user_id'] = $destinationUser;
        $dashboard['team_id'] = $destinationUserBean->team_id;
        $dashboard['team_set_id'] = $destinationUserBean->team_set_id;

        $dashboardBean = BeanFactory::newBean('Dashboards');
        $dashboardBean->fromArray($dashboard);
        $db->insert($dashboardBean);

        $isFavorite = $this->isFavorite($initialDashboardId, $sourceUserId);
        if ($isFavorite) {
            $this->favoriteDashboard($dashboard['id'], $destinationUser);
        }
    }

    /**
     * Checks if the dashboard is favorited
     *
     * @param string $dashboardId
     * @param string $sourceUserId
     *
     * @return bool
     */
    private function isFavorite(string $dashboardId, ?string $sourceUserId): bool
    {
        if (!$sourceUserId) {
            return false;
        }

        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('SugarFavorites'), ['team_security' => false]);
        $sugarQuery->where()->equals('assigned_user_id', $sourceUserId);
        $sugarQuery->where()->equals('module', 'Dashboards');
        $sugarQuery->where()->equals('record_id', $dashboardId);
        $sugarQuery->where()->equals('deleted', 0);
        $result = $sugarQuery->execute();

        if (safeCount($result) > 0) {
            return true;
        }

        return false;
    }

    /**
     * After the dashboard is being copied to a user, add that to favorites also
     *
     * @param string $dashboardId
     * @param string $userId
     */
    private function favoriteDashboard(string $dashboardId, string $userId): void
    {
        $db = \DBManagerFactory::getInstance();

        $favoritesBean = \BeanFactory::newBean('SugarFavorites');
        $fieldDefs = $favoritesBean->getFieldDefinitions();

        $favorite = [
            'id' => Uuid::uuid4(),
            'assigned_user_id' => $userId,
            'created_by' => $userId,
            'modified_user_id' => $userId,
            'module' => 'Dashboards',
            'record_id' => $dashboardId,
        ];

        $db->insertParams($favoritesBean->getTableName(), $fieldDefs, $favorite);
    }

    /**
     * Delete all dashboards for a user, on the specified modules.
     * If the modules are empty the delete all dashboards.
     *
     * @param string $userId
     * @param array $modules
     */
    private function deleteDashboards(string $userId, array $modules): void
    {
        if (!$modules) {
            $this->deleteDashboardsFor($userId, null);
            return;
        }

        foreach ($modules as $module) {
            $this->deleteDashboardsFor($userId, $module);
        }
    }


    /**
     * Deletes a dashboard
     *
     * @param string $dashboardId
     */
    private function deleteDashboard(string $dashboardId): void
    {
        $db = \DBManagerFactory::getInstance();

        $dashboardsBean = \BeanFactory::newBean('Dashboards');
        $fieldDefs = $dashboardsBean->getFieldDefinitions();
        $db->updateParams($dashboardsBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], [
            'id' => $dashboardId,
        ]);
    }

    /**
     * Deletes dashboards for a user on a module
     *
     * @param string $userId
     * @param string $module : null
     */
    private function deleteDashboardsFor(string $userId, ?string $module): void
    {
        $db = \DBManagerFactory::getInstance();

        $where = [
            'assigned_user_id' => $userId,
            'dashboard_module' => $module,
        ];

        if (!$module) {
            $where = [
                'assigned_user_id' => $userId,
            ];
        }

        $dashboardsBean = \BeanFactory::newBean('Dashboards');
        $fieldDefs = $dashboardsBean->getFieldDefinitions();
        $db->updateParams($dashboardsBean->getTableName(), $fieldDefs, [
            'deleted' => '1',
        ], $where);
    }

    /**
     * Adds dashboards from source user to destination user on specified modules.
     * If modules is empty, then add dashboards from all modules.
     *
     * @param string $sourceUser
     * @param string $destinationUser
     * @param array|null $modules
     */
    private function cloneDashboards(string $sourceUser, string $destinationUser, ?array $modules): void
    {
        if (safeCount((array)$modules) > 0) {
            foreach ($modules as $module) {
                $this->cloneDashboardsFor($sourceUser, $destinationUser, $module);
            }
        } else {
            $this->cloneDashboardsFor($sourceUser, $destinationUser, null);
        }
    }

    /**
     * Clones dashboards for a user on a certain module.
     *
     * @param string $sourceUser
     * @param string $destinationUser
     * @param string|null $module
     */
    private function cloneDashboardsFor(string $sourceUser, string $destinationUser, ?string $module): void
    {
        $sourceDashboards = $this->getDashboards($sourceUser, $module);
        foreach ($sourceDashboards as $sourceDashboard) {
            $this->copyDashboard($destinationUser, $sourceDashboard);
        }
    }

    /**
     * Retrieves dashboards for a user.
     *
     * @param string $userId
     * @param null|string module
     * @return array
     */
    public function getDashboards(string $userId, ?string $module): array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('Dashboards'), ['team_security' => false]);
        $sugarQuery->where()->equals('assigned_user_id', $userId);
        if ($module) {
            $sugarQuery->where()->equals('dashboard_module', $module);
        }
        $sugarQuery->where()->equals('default_dashboard', 0);
        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Retrieves a dashboard
     *
     * @param mixed $dashboardId
     * @return array|null
     */
    private function retrieveDashboard($dashboardId): ?array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('Dashboards'), ['team_security' => false]);
        $sugarQuery->where()->equals('id', $dashboardId);
        $result = $sugarQuery->execute();

        if (is_array($result) && safeCount($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * If the dashlet has a filter, then clone that filter on the user and return its id.
     *
     * @param null|string $metadata
     * @param string $userId
     * @return string|null
     */
    private function copyDashboardFilters(?string $metadata, string $userId): ?string
    {
        if (!is_string($metadata)) {
            return null;
        }

        $metadata = json_decode($metadata);

        foreach ($metadata->dashlets as $dashlet) {
            $view = $dashlet->view;
            $filterId = $view->filter_id;

            if (!$filterId) {
                continue;
            }

            $newFilterId = $this->copyFilter($userId, $filterId);
            $view->filter_id = $newFilterId;
        }

        return json_encode($metadata);
    }

    /**
     * Copies a filter to a user
     *
     * @param string $userId
     * @param string $filterId
     * @return string
     */
    private function copyFilter(string $userId, string $filterId): string
    {
        $db = \DBManagerFactory::getInstance();

        $filter = FiltersManager::getFilter($filterId);

        if (!$filter) {
            // might be a default filter
            return $filterId;
        }

        $userHasFilter = FiltersManager::hasFilter($userId, $filter);

        if ($userHasFilter) {
            return $filterId;
        }

        $user = BeanFactory::retrieveBean('Users', $userId);

        $id = Uuid::uuid4();
        $filter['id'] = $id;
        $filter['created_by'] = $userId;
        $filter['assigned_user_id'] = $userId;
        $filter['team_id'] = $user->team_id;
        $filter['team_set_id'] = $user->team_set_id;

        $filterBean = BeanFactory::newBean('Filters');
        $filterBean->fromArray($filter);
        $db->insert($filterBean);

        return $id;
    }
}

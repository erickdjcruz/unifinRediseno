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

namespace Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads;

use BeanFactory;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\InvokerPayload;
use SugarQuery;

/**
 * The InvokerPayload class is used to give some structure to the input for UserUtils commands
 */
class InvokerBasePayload extends InvokerPayload
{
    protected $dashboards;
    protected $filters;
    protected $userSettings;

    /**
     * the command type
     *
     * @var string
     */
    protected $type;

    /**
     * The source user
     *
     * @var string
     */
    protected $sourceUser;

    /**
     * The destination users
     *
     * @var array
     */
    protected $destinationUsers;

    /**
     * The destination teams
     *
     * @var array
     */
    protected $destinationTeams;

    /**
     * The destination roles
     *
     * @var array
     */
    private $destinationRoles;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->type = array_key_exists('type', $options) ? $options['type'] : '';
        $this->sourceUser = $options['sourceUser'] ?? null;
        $this->destinationUsers = $options['destinationUsers'] ?? [];
        $this->destinationRoles = $options['destinationRoles'] ?? [];
        $this->destinationTeams = $options['destinationTeams'] ?? [];
    }

    /**
     * Setter for source user
     *
     * @param string $userId
     */
    public function setSourceUser(string $userId): void
    {
        $this->sourceUser = $userId;
    }

    /**
     * Setter for destination users
     *
     * @param array $destinationUsers
     */
    public function setDestinationUsers(array $destinationUsers): void
    {
        $this->destinationUsers = $destinationUsers;
    }

    /**
     * Setter for destination teams
     *
     * @param array $destinationTeams
     * @return void
     */
    public function setDestinationTeams(array $destinationTeams): void
    {
        $this->destinationTeams = $destinationTeams;
    }

    /**
     * Setter for destination roles
     *
     * @param array $destinationRoles
     */
    public function setDestinationRoles(array $destinationRoles): void
    {
        $this->destinationRoles = $destinationRoles;
    }

    /**
     * Getter for source user
     *
     * @return string
     */
    public function getSourceUser(): string
    {
        return $this->sourceUser;
    }

    /**
     * Gets all the users involved in the Command
     *
     * @return array
     */
    public function getDestinationUsers(): array
    {
        $usersFromRoles = $this->getUsersFromRoles($this->destinationRoles);
        $usersFromTeams = $this->getUsersFromTeams($this->destinationTeams);

        $users = array_unique(array_merge($this->destinationUsers, $usersFromRoles, $usersFromTeams));
        if ($this->sourceUser) {
            $users = array_merge(array_diff($users, [$this->sourceUser]));
        }
        return $users;
    }

    /**
     * Serialize the payload
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            'type' => $this->type,
            'sourceUser' => $this->sourceUser,
            'destinationUsers' => $this->destinationUsers,
            'dashboards' => $this->dashboards,
            'filters' => $this->filters,
            'userSettings' => $this->userSettings,
        ]);
    }

    /**
     * Retrieves users associated to a role
     *
     * @param array $roles
     * @return array|null
     */
    public function getUsersFromRoles(?array $roles): array
    {
        $roleIds = [];

        if (!$roles) {
            return $roleIds;
        }

        foreach ($roles as $roleId) {
            $roleIds = array_merge($roleIds, $this->getIdsForRole($roleId));
        }

        return array_unique($roleIds);
    }

    /**
     * Retrieves user ids from a certain role
     * @param string $roleId
     * @return array
     */
    private function getIdsForRole(string $roleId): array
    {
        $role = BeanFactory::retrieveBean('ACLRoles', $roleId);

        $sugarQuery = new SugarQuery();
        $sugarQuery->select->fieldRaw('aru.user_id');
        $sugarQuery->from($role, ['alias' => 'ar']);
        $sugarQuery->joinTable('acl_roles_users', ['alias' => 'aru'])
            ->on()
            ->equalsField('ar.id', 'aru.role_id');
        $sugarQuery->where()->equals('aru.role_id', $roleId);
        $sugarQuery->where()->equals('aru.deleted', 0);
        $result = $sugarQuery->execute();

        return array_map(function ($item) {
            return $item['user_id'];
        }, $result);
    }

    /**
     * Retrieves users associated to a team
     *
     * @param array $teams
     * @return array|null
     */
    public function getUsersFromTeams(?array $teams): array
    {
        $teamIds = [];
        if (!$teams) {
            return $teamIds;
        }

        foreach ($teams as $teamId) {
            $teamIds = array_merge($teamIds, $this->getIdsForTeam($teamId));
        }

        return array_unique($teamIds);
    }

    /**
     * Gets user ids from a team
     *
     * @param string $teamId
     * @return array
     */
    private function getIdsForTeam(string $teamId): array
    {
        $team = BeanFactory::retrieveBean('Teams', $teamId);
        if (!$team) {
            return [];
        }

        $teamUsers = $team->get_team_members();

        return array_map(function ($item) {
            return $item->id;
        }, $teamUsers);
    }
}

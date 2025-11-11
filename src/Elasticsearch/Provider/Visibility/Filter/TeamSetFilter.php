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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Filter;

use Doctrine\DBAL\Result;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Queue\QueueManager;
use TeamSet;
use User;

/**
 *
 * Team set filter
 *
 */
class TeamSetFilter implements FilterInterface
{
    use FilterTrait;

    private static $teamSetIdsByUser = [];

    /**
     * @var string
     */
    protected $defaultField = 'team_set_id.set';

    /**
     * common fields array, this is referring to the field was create in "Common__" format
     * @var array
     */
    protected $commonFields = [
        'acl_team_set_id.set',
    ];

    /**
     * {@inheritdoc}
     */
    public function buildFilter(array $options = [])
    {
        $teamSetIds = $this->getTeamSetIds($options['user']);
        $field = !empty($options['field']) ? $options['field'] : $this->defaultField;
        if (in_array($field, $this->commonFields)) {
            $field = Mapping::PREFIX_COMMON . $field;
        } else {
            $field = $options['module'] . Mapping::PREFIX_SEP . $field;
        }
        return new \Elastica\Query\Terms($field, $teamSetIds);
    }

    /**
     * Get team set ids for given user
     * @param User $user
     * @return array
     */
    protected function getTeamSetIds(User $user)
    {
        if (QueueManager::isLargeTeamsets()) {
            return $this->getTeamSetIdsForLargeTeamSets($user);
        }
        return TeamSet::getTeamSetIdsForUser($user->id);
    }

    /**
     * Get team set ids for given user where Global Team is not part of the teamSet
     * @param User $user
     * @return array
     */
    protected function getTeamSetIdsForLargeTeamSets(User $user)
    {
        if (empty(static::$teamSetIdsByUser[$user->id])) {
            global $db;
            //Add condition to not select teams set ids where Global Team is part of teamSet
            $sql = 'SELECT tst.team_set_id from team_sets_teams tst
            INNER JOIN team_memberships team_memberships ON tst.team_id = team_memberships.team_id
            AND team_memberships.user_id = ? AND team_memberships.deleted=0
            WHERE NOT EXISTS(SELECT NULL FROM team_sets_teams tst1 WHERE tst1.id = tst.id AND tst1.team_id = 1)
            group by tst.team_set_id';
            /** @var Result $stmt */
            $stmt = $GLOBALS['db']->getConnection()->executeQuery($sql, [$user->id]);
            $results = $stmt->iterateColumn();
            //Initialize with Global team set id by default
            $newResults = ['1'];
            foreach ($results as $result) {
                $newResults[] = $db->fromConvert($result, 'id');
            }
            static::$teamSetIdsByUser[$user->id] = $newResults;
        }
        return static::$teamSetIdsByUser[$user->id];
    }
}

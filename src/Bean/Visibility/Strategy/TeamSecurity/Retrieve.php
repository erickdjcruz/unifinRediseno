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

namespace Sugarcrm\Sugarcrm\Bean\Visibility\Strategy\TeamSecurity;

use DBManager;
use Sugarcrm\Sugarcrm\Bean\Visibility\Strategy;
use SugarQuery;
use User;

/**
 * Team security visibility implementation which uses denormalized team-set to user data
 */
final class Retrieve implements Strategy
{
    /**
     * @var string
     */
    private $beanId;

    /**
     * @var User
     */
    private $user;

    /**
     * Constructor
     *
     * @param string $bean_id The ID of bean that is being retrieved
     * @param User $user The user to filter the records for
     */
    public function __construct(User $user, string $bean_id)
    {
        $this->beanId = $bean_id;
        $this->user = $user;
    }

    public function applyToQuery(SugarQuery $query, $table)
    {
        $emptyBean = \BeanFactory::newBean($query->from->getModuleName());
        $emptyBean->disable_row_level_security = true;
        $prefetchQuery = new \SugarQuery();
        $prefetchQuery->from($emptyBean);
        $prefetchQuery->select(['team_set_id']);
        $prefetchQuery->where()->equals('id', $this->beanId);
        $teamSetId = $prefetchQuery->compile()->execute()->fetchOne();
        if (!empty($teamSetId)) {
            $team_table_alias = 'team_memberships';
            $table_alias = $query->from->table_name;

            $tf_alias = $query->getDBManager()->getValidDBName($table_alias . '_tf', true, 'alias');
            $conn = $query->getDBManager()->getConnection();
            $subQuery = $conn->createQueryBuilder();
            $subQuery
                ->select('tst.team_set_id')
                ->from('team_sets_teams', 'tst')
                ->join(
                    'tst',
                    'team_memberships',
                    $team_table_alias,
                    $subQuery->expr()->and(
                        $team_table_alias . '.team_id = tst.team_id',
                        $team_table_alias . '.user_id = ' . $subQuery->createPositionalParameter($this->user->id),
                        $team_table_alias . '.deleted = 0'
                    )
                )
                ->groupBy('tst.team_set_id');
            $subQuery->where('tst.team_set_id = ?');
            $subQuery->createPositionalParameter($teamSetId);
            $query->joinTable(
                $subQuery,
                [
                    'alias' => $tf_alias,
                ]
            )->on()->equalsField($tf_alias . '.team_set_id', $table_alias . '.team_set_id');
        } else {
            $query->where()->addRaw('1 != 1');
        }
    }

    public function applyToFrom(DBManager $db, $query, $table)
    {
        throw new \LogicException(self::class . '::' . __METHOD__ . ' should not be called.');
    }

    public function applyToWhere(DBManager $db, $query, $table)
    {
        throw new \LogicException(self::class . '::' . __METHOD__ . ' should not be called.');
    }
}

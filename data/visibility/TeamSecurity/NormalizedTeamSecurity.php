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

use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\StrategyInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Visibility;
use Sugarcrm\Sugarcrm\Elasticsearch\Analysis\AnalysisBuilder;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Document;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;
use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

/**
 * Team security visibility
 */
class NormalizedTeamSecurity extends SugarVisibility implements StrategyInterface
{
    /**
     * $sugar_config base key for performance profile
     * @var string
     */
    public const CONFIG_PERF_KEY = 'perfProfile.TeamSecurity.%s';

    /**
     * Default teamSet prefetch count
     * @var integer
     */
    public const TEAMSET_PREFETCH_MAX = 500;

    /**
     * The SQL hint that can be used inside a select subquery (MySQL specific)
     * https://dev.mysql.com/doc/refman/8.0/en/optimizer-hints.html#optimizer-hints-subquery
     */
    public const SUBQUERY_OPTIMIZER_HINT = '/*+ SUBQUERY(MATERIALIZATION) */';

    /**
     * Get team security `join` as a `IN()` condition.
     * @param string $current_user_id
     * @return string
     */
    protected function getCondition($current_user_id)
    {
        $team_table_alias = 'team_memberships';
        $table_alias = $this->getOption('table_alias');
        if (!empty($table_alias)) {
            $team_table_alias = $this->bean->db->getValidDBName($team_table_alias . $table_alias, true, 'table');
        } else {
            $table_alias = $this->bean->table_name;
        }

        $inClause = $this->getInCondition($current_user_id, $team_table_alias);
        return " {$table_alias}.team_set_id IN ({$inClause}) ";
    }

    /**
     * Get IN condition clause
     * @param string $currentUserId Current user id
     * @param string $teamTableAlias Team table alias
     * @return string IN clause
     */
    protected function getInCondition($currentUserId, $teamTableAlias)
    {
        $count = null;
        $usePrefetch = false;
        if ($this->getOption('teamset_prefetch')) {
            $teamSets = TeamSet::getTeamSetIdsForUser($currentUserId);
            $count = safeCount($teamSets);
            if ($count <= $this->getOption('teamset_prefetch_max', self::TEAMSET_PREFETCH_MAX)) {
                $usePrefetch = true;
            } else {
                $this->log->warn("TeamSetPrefetch max reached for user {$currentUserId} --> {$count}");
            }
        }

        if ($usePrefetch) {
            if ($count) {
                $quotedTeamSets = [];
                foreach ($teamSets as $teamSet) {
                    $quotedTeamSets[] = $this->bean->db->quoted($teamSet);
                }
                return implode(',', $quotedTeamSets);
            } else {
                return 'NULL';
            }
        } else {
            $escapedCurrentUserId = $this->bean->db->quote($currentUserId);
            $subqueryOptimizerHint = $this->useSubqueryOptimizerHint() ? ' ' . self::SUBQUERY_OPTIMIZER_HINT : '';
            return "select{$subqueryOptimizerHint} tst.team_set_id from team_sets_teams tst
                    INNER JOIN team_memberships {$teamTableAlias} ON tst.team_id = {$teamTableAlias}.team_id
                    AND {$teamTableAlias}.user_id = '$escapedCurrentUserId'
                    AND {$teamTableAlias}.deleted = 0";
        }
    }

    /**
     * Get team security as a JOIN clause
     * @param string $current_user_id
     * @return string
     *
     * @see static::join(), should be kept synced
     */
    protected function getJoin($current_user_id)
    {
        $team_table_alias = 'team_memberships';
        $table_alias = $this->getOption('table_alias');
        $table_name = $this->bean->table_name;

        if ($table_alias && $table_alias !== $table_name) {
            $team_table_alias = $this->bean->db->getValidDBName($team_table_alias . $table_alias, true, 'table');
        } else {
            $table_alias = $table_name;
        }

        $tf_alias = $this->bean->db->getValidDBName($table_alias . '_tf', true, 'alias');
        $query = ' INNER JOIN (select tst.team_set_id from team_sets_teams tst';
        $query .= " INNER JOIN team_memberships {$team_table_alias} ON tst.team_id = {$team_table_alias}.team_id
                    AND {$team_table_alias}.user_id = '$current_user_id'
                    AND {$team_table_alias}.deleted=0 group by tst.team_set_id) {$tf_alias} on {$tf_alias}.team_set_id  = {$table_alias}.team_set_id ";
        if ($this->getOption('join_teams')) {
            $query .= " INNER JOIN teams ON teams.id = {$team_table_alias}.team_id AND teams.deleted=0 ";
        }
        return $query;
    }

    /**
     * Joins visibility condition to the query
     *
     * @param SugarQuery $query
     * @param string $user_id
     *
     * @throws SugarQueryException
     * @throws Exception
     * @see static::getJoin(), should be kept synced
     */
    protected function join(SugarQuery $query, $user_id)
    {
        $team_table_alias = 'team_memberships';
        $table_alias = $this->getOption('table_alias');
        if (!empty($table_alias)) {
            $team_table_alias = $this->bean->db->getValidDBName(
                $team_table_alias . $table_alias,
                true,
                'table'
            );
        } else {
            $table_alias = $this->bean->table_name;
        }

        $tf_alias = $this->bean->db->getValidDBName($table_alias . '_tf', true, 'alias');
        $conn = $this->bean->db->getConnection();
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
                    $team_table_alias . '.user_id = ' . $subQuery->createPositionalParameter($user_id),
                    $team_table_alias . '.deleted = 0'
                )
            )
            ->groupBy('tst.team_set_id');

        $query->joinTable(
            $subQuery,
            [
                'alias' => $tf_alias,
            ]
        )->on()->equalsField($tf_alias . '.team_set_id', $table_alias . '.team_set_id');

        if ($this->getOption('join_teams')) {
            $query->joinTable('teams')
                ->on()
                ->equalsField('teams.id', $team_table_alias . '.team_id')
                ->equals('teams.deleted', 0);
        }
    }

    /**
     * Check if we need WHERE condition
     * @return boolean
     */
    protected function useCondition()
    {
        return $this->getOption('as_condition') || $this->getOption('where_condition');
    }

    /**
     * MySQL feature that appeared to be useful for the team visibility:
     * Adds a DB subquery optimizer hint to define "MATERIALIZATION" strategy -
     * this forces DB to materialize and cache a subquery result between requests.
     * https://dev.mysql.com/doc/refman/8.0/en/optimizer-hints.html#optimizer-hints-subquery
     */
    protected function useSubqueryOptimizerHint(): bool
    {
        if (($this->bean->db instanceof MysqlManager)
            && !$this->getOption('disable_subquery_optimizer_hint')
            && !$this->getOption('prefetch_for_retrieve')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addVisibilityFrom(&$query)
    {
        // We'll get it on where clause
        if ($this->useCondition()) {
            return $query;
        }

        $this->addVisibility($query, false);
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function addVisibilityWhere(&$query)
    {
        if (!$this->useCondition()) {
            return $query;
        }

        $this->addVisibility($query, true);
        return $query;
    }

    /**
     * Add visibility query
     *
     * @param string $query
     * @param bool|null $useWhereCondition
     * @see static::addVisibilityQuery(), should be kept synced
     */
    protected function addVisibility(&$query, bool $useWhereCondition = null)
    {
        if (!$this->isTeamSecurityApplicable()) {
            return;
        }

        $current_user_id = empty($GLOBALS['current_user']) ? '' : $GLOBALS['current_user']->id;

        if (null === $useWhereCondition) {
            $useWhereCondition = $this->useCondition();
        }
        if ($useWhereCondition) {
            $cond = $this->getCondition($current_user_id);
            if ($query) {
                $query .= ' AND ' . ltrim($cond);
            } else {
                $query = $cond;
            }
        } else {
            $query .= $this->getJoin($current_user_id);
        }
    }

    /**
     * Add visibility query
     *
     * @param SugarQuery $query
     *
     * @see static::addVisibility(), should be kept synced
     */
    public function addVisibilityQuery(SugarQuery $query)
    {
        // Support portal will never respect Teams, even if they do earn more than them even while raising the teamsets
        if (PortalFactory::getInstance('Session')->isActive()) {
            return;
        }

        if (!$this->isTeamSecurityApplicable()) {
            return;
        }

        $current_user_id = empty($GLOBALS['current_user']) ? '' : $GLOBALS['current_user']->id;

        if ($this->useCondition() || $this->useSubqueryOptimizerHint()) {
            $cond = $this->getCondition($current_user_id);
            $query->whereRaw($cond);
        } else {
            $this->join($query, $current_user_id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addVisibilityFromQuery(SugarQuery $sugarQuery, array $options = [])
    {
        // We'll get it on where clause
        if ($this->useCondition() || $this->useSubqueryOptimizerHint()) {
            return $sugarQuery;
        }

        $this->addVisibilityQuery($sugarQuery);

        return $sugarQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function addVisibilityWhereQuery(SugarQuery $sugarQuery, array $options = [])
    {
        if (!$this->useCondition() && !$this->useSubqueryOptimizerHint()) {
            return $sugarQuery;
        }

        $cond = '';
        $this->addVisibility($cond, true);
        if (!empty($cond)) {
            $sugarQuery->whereRaw($cond);
        }
        return $sugarQuery;
    }

    /**
     * Verifies if team security needs to be applied. Note that if the
     * $current_user is not set we still apply team security. This does
     * not make any sense by itself as the result will always be negative
     * (no access).
     * @return bool True if team security needs to be applied
     */
    protected function isTeamSecurityApplicable()
    {
        global $current_user;

        // Support portal will never respect Teams, even if they do earn more than them even while raising the teamsets
        if (PortalFactory::getInstance('Session')->isActive()) {
            return false;
        }

        if ($this->bean->module_dir == 'WorkFlow'  // copied from old team security clause
            || $this->bean->disable_row_level_security
            || (!empty($current_user) && $current_user->isAdminForModule($this->bean->module_dir))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Override for performance tuning per module using `$sugar_config`.
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        // Ability to skip perf profile - use with caution
        if (!$this->getOption('skip_perf_profile')) {
            $options = empty($this->options) ? [] : $this->options;
            $this->options = $this->getTuningOptions($options);
        }

        return $this;
    }

    /**
     * BETA functionality - use at your own risk
     *
     * Get performance tuning options from $sugar_config. If non
     * available fallback to default tuning options using DBAL.
     * @param array $options
     * @return array
     */
    protected function getTuningOptions(array $options)
    {
        // module specific config
        $configKey = sprintf(self::CONFIG_PERF_KEY, $this->bean->module_dir);
        $tune = SugarConfig::getInstance()->get($configKey, []);

        // if no module specific config, try default config
        $configKey = sprintf(self::CONFIG_PERF_KEY, 'default');
        if (empty($tune)) {
            $tune = SugarConfig::getInstance()->get($configKey, []);
        }

        // if still empty use stock DBAL profile
        if (empty($tune)) {
            $tune = $this->bean->db->getDefaultPerfProfile('TeamSecurity');
        }

        // passed in $options will win from tuning config
        return array_merge($tune, $options);
    }

    /**
     * Override getOption to make sure we use any performance tuning defined in $sugar_config.
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        //if parameter is not defined, make sure the tuning options have been loaded prior to calling parent
        if (!isset($this->options[$name])) {
            //send in the defined options or a blank array.
            $options = !empty($this->options) ? $this->options : [];
            $this->options = $this->getTuningOptions($options);
        }

        return parent::getOption($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function elasticBuildAnalysis(AnalysisBuilder $analysisBuilder, Visibility $provider)
    {
        // no special analyzers needed
    }

    /**
     * {@inheritdoc}
     */
    public function elasticBuildMapping(Mapping $mapping, Visibility $provider)
    {
        $property = new MultiFieldProperty();
        $property->setType('keyword');
        $mapping->addModuleField('team_set_id', 'set', $property);
    }

    /**
     * {@inheritdoc}
     */
    public function elasticProcessDocumentPreIndex(Document $document, SugarBean $bean, Visibility $provider)
    {
        // team_set_id is retrieved as a bean field directly, nothing to do here
    }

    /**
     * {@inheritdoc}
     */
    public function elasticGetBeanIndexFields($module, Visibility $provider)
    {
        // nominate team_set_id field to be retrievable directly
        return ['team_set_id' => 'id'];
    }

    /**
     * {@inheritdoc}
     */
    public function elasticAddFilters(User $user, \Elastica\Query\BoolQuery $filter, Visibility $provider)
    {
        if ($this->isTeamSecurityApplicable()) {
            $filter->addMust($provider->createFilter('TeamSet', [
                'user' => $user,
                'module' => $this->bean->module_name,
            ]));
        }
    }
}

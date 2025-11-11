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

use Doctrine\DBAL\Connection;

/**
 * ActivityStreamCleaner.php
 *
 * This class provides the necessary methods to purge activity stream records
 *
 * Any change needed to the defaults can be specified in the config_override.php as below (examples):
 *      $sugar_config['activitystreamcleaner']['keep_all_relationships_activities'] = false;
 *      $sugar_config['activitystreamcleaner']['months_to_keep'] = 6;
 *
 */
class ActivityStreamCleaner
{
    protected $tables = [
        'activities',
        'activities_users',
    ];

    protected $default_limit = 25000;
    protected $max_in_condition_limit = 500;
    protected $default_months_to_keep = 6;
    protected $default_keep_all_relationships_activities = false;

    protected $activity_type_to_keep = [
        'post',
    ];

    protected $activity_type_relationships = [
        'link',
        'unlink',
    ];

    /**
     * Get the months_to_keep value from Sugar config if it exists, otherwise return the default. Use that value
     * to compute the Datetime string to be used in the Query for testing the activity date_entered value.
     * @return string
     */
    protected function getFilterDate()
    {
        // retrieve months to keep from config, or set default
        if (isset($GLOBALS['sugar_config']['activitystreamcleaner']['months_to_keep'])) {
            $months_to_keep = (int)$GLOBALS['sugar_config']['activitystreamcleaner']['months_to_keep'];
        } else {
            $months_to_keep = (int)$this->default_months_to_keep;
        }

        if ($months_to_keep < 0) {
            // process up to now
            $months_to_keep = 0;
        }

        $dtm = new DateTime('now', new DateTimeZone('UTC'));
        $month_interval = sprintf('P%dM', $months_to_keep);
        $dtm->sub(new DateInterval($month_interval));

        // set the prune after date for this execution
        return $dtm->format(TimeDate::DB_DATETIME_FORMAT);
    }

    /**
     * Get Database instance
     * @return DBManager
     */
    protected function db()
    {
        return DBManagerFactory::getInstance();
    }

    /**
     * Delete all activities records that have been soft-deleted (deleted = 1)
     */
    public function purgeSoftDeletedRecords()
    {
        $qb = $this->db()->getConnection()->createQueryBuilder();
        $qb->delete('activities');
        $qb->where('deleted = ' . $qb->createPositionalParameter(1));
        $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
        $qb->execute();

        $qb = $this->db()->getConnection()->createQueryBuilder();
        $qb->delete('activities_users');
        $qb->where('deleted = ' . $qb->createPositionalParameter(1));
        $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
        $qb->execute();

        $qb = $this->db()->getConnection()->createQueryBuilder();
        $qb->delete('comments');
        $qb->where('deleted = ' . $qb->createPositionalParameter(1));
        $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
        $qb->execute();
    }

    /**
     * Delete the supplied activity ids
     * @param array $ids An array of Activity Ids to be deleted
     */
    private function purgeActivitiesTableInIds($ids)
    {
        if (!empty($ids)) {
            $qb = $this->db()->getConnection()->createQueryBuilder();
            $qb->delete('activities');
            $qb->where(
                $qb->expr()->in(
                    'id',
                    $qb->createPositionalParameter((array)$ids, Connection::PARAM_STR_ARRAY)
                )
            );
            $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
            $qb->execute();
        }
    }

    /**
     * Get an array of the activity types to keep.
     * @return array
     */
    private function getActivityTypesToKeep()
    {
        if (isset($GLOBALS['sugar_config']['activitystreamcleaner']['keep_all_relationships_activities'])) {
            $keep_relationships_activities = (bool)$GLOBALS['sugar_config']['activitystreamcleaner']['keep_all_relationships_activities'];
        } else {
            $keep_relationships_activities = (bool)$this->default_keep_all_relationships_activities;
        }

        if ($keep_relationships_activities) {
            return array_merge($this->activity_type_to_keep, $this->activity_type_relationships);
        } else {
            return $this->activity_type_to_keep;
        }
    }

    /**
     * Perform the actual activity record deletion.
     * @param bool $limited The number to purge considers the Sugar Config override values if true.
     */
    public function purgeOldActivitiesRecords($limited = true)
    {
        $limit = 0;
        $in_condition_limit = $this->max_in_condition_limit;

        if ($limited) {
            // retrieve limit from config
            if (isset($GLOBALS['sugar_config']['activitystreamcleaner']['limit_scheduler_run'])) {
                $limit = (int)$GLOBALS['sugar_config']['activitystreamcleaner']['limit_scheduler_run'];
            } else {
                $limit = (int)$this->default_limit;
            }

            if ($limit <= 0) {
                $limit = 0;
            }

            // in condition limit
            $in_condition_limit = $this->max_in_condition_limit;
            if ($limit < $in_condition_limit) {
                $in_condition_limit = $limit;
            }
        }

        $date_entered_keep = $this->getFilterDate();
        $GLOBALS['log']->info(__METHOD__ . ' Cleaning Activity Streams created before ' . $date_entered_keep . ' with query limit ' . $limit);

        // delete all non posts, that are old and that do not have an existing comment
        $qbSub = $this->db()->getConnection()->createQueryBuilder();
        $qbSub->select('parent_id');
        $qbSub->from('comments');

        $qb = $this->db()->getConnection()->createQueryBuilder();

        if ($limit > 0) {
            // if $limit > 0, proceed in $limit chunks and in $this->max_in_condition_limit subchunks
            $qb->select('id');
            $qb->from('activities');
        } else {
            // if zero, delete all
            $qb->delete('activities');
        }

        // find records without specifics activity_type
        $qb->where(
            $qb->expr()->notIn(
                'activity_type',
                $qb->createPositionalParameter((array)$this->getActivityTypesToKeep(), Connection::PARAM_STR_ARRAY)
            )
        );

        $qb->andWhere('date_entered < ' . $qb->createPositionalParameter($date_entered_keep));
        $qb->andWhere($qb->expr()->notIn('id', $qbSub->getSQL()));

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
        $res = $qb->execute();

        if ($limit > 0) {
            // if the limit is > 0 it means we need to execute the delete in chunks/subchunks
            $ids = [];

            while ($row = $res->fetchAssociative()) {
                $ids[] = $row['id'];

                if (safeCount($ids) == $in_condition_limit) {
                    // complete one chunk
                    $this->purgeActivitiesTableInIds($ids);
                    $ids = [];
                }
            }

            // process last chunk if any
            $this->purgeActivitiesTableInIds($ids);
        }

        // delete all activities_users where id is not in activities (after the trimming)
        $qbSub = $this->db()->getConnection()->createQueryBuilder();
        $qbSub->select('id');
        $qbSub->from('activities');

        $qb = $this->db()->getConnection()->createQueryBuilder();
        $qb->delete('activities_users');
        $qb->where($qb->expr()->notIn('activity_id', $qbSub->getSQL()));
        $GLOBALS['log']->info(__METHOD__ . ' ' . $qb->getSQL() . ' ' . print_r($qb->getParameters(), true));
        $qb->execute();
    }
}

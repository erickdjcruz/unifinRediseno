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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Scheduler;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;

/**
 * This class will update the momentum of all journey's
 */
class MomentumUpdater
{
    public function run()
    {
        if (!hasAutomateLicense()) {
            return;
        }

        $activity_modules = $GLOBALS['app_list_strings']['dri_workflow_task_templates_activity_type_list'];

        if ($activity_modules) {
            foreach ($activity_modules as $module_name => $label) {
                $activities = $this->getActivitesAgainstModule($module_name);

                if (!empty($activities)) {
                    foreach ($activities as $activity) {
                        $this->updateJourneyMomentum($module_name, $activity);
                    }
                }
            }
            return true;
        }
    }

    /**
     * Get the ids of all the activities having cj_momentum_start_date set
     * and cj_momentum_due_date is empty
     *
     * @param string $module_name
     * @return array
     */
    private function getActivitesAgainstModule($module_name)
    {
        $activityBean = \BeanFactory::newBean($module_name);
        $query = new \SugarQuery();
        $query->from($activityBean, [
            'alias' => 'activity',
            'team_security' => false,
        ]);

        $query->joinTable('dri_workflow_templates', [
            'joinType' => 'INNER',
            'alias' => 'dri_wt',
            'linkingTable' => true,
        ])
            ->on()
            ->equalsField('activity.dri_workflow_template_id', 'dri_wt.id');

        $handler = ActivityHandlerFactory::factory($module_name);

        $query->select('activity.id');
        $query->where()
            ->isNull('activity.cj_momentum_end_date')
            ->notNull('activity.cj_momentum_start_date')
            ->notNull('activity.dri_workflow_task_template_id')
            ->notEquals('activity.status', $handler->getCompletedStatus($activityBean))
            ->notEquals('activity.status', $handler->getNotApplicableStatus($activityBean))
            ->equals('activity.deleted', 0)
            ->equals('dri_wt.deleted', 0);

        $result = $query->execute();

        if (!empty($result)) {
            return $result;
        }
    }

    /**
     * Update the momentum scores and momentum ratio of activity
     *
     * @param string $module_name
     * @param string $id
     * @return string
     */
    private function updateActivity($module_name, $id)
    {
        $bean = $this->getModuleBean($module_name, $id);

        if (!empty($bean) && !empty($bean->id)) {
            $handler = ActivityHandlerFactory::factory($bean->module_dir);

            $ratio = (float)$bean->cj_momentum_ratio;
            $score = (int)$bean->cj_momentum_score;

            $handler->calculateMomentum($bean);

            if ($ratio !== (float)$bean->cj_momentum_ratio || $score !== (int)$bean->cj_momentum_score) {
                $GLOBALS['log']->debug(
                    sprintf(
                        'Updating %s with ratio %s (%s) and score %s (%s) id=%s',
                        $bean->object_name,
                        $ratio,
                        (float)$bean->cj_momentum_ratio,
                        $score,
                        (int)$bean->cj_momentum_score,
                        $bean->id
                    )
                );

                $table = $bean->getTableName();

                $query = 'UPDATE ' . $table . ' SET cj_momentum_ratio = ? , cj_momentum_score = ? WHERE id = ?';
                $conn = $GLOBALS['db']->getConnection();
                $stmt = $conn->executeQuery($query, ["$bean->cj_momentum_ratio", "$bean->cj_momentum_score", "$id"]);
                return $bean->dri_subworkflow_id;
            }
        }
    }

    /**
     * Update the Journey/Stage with the updated momentum scores and momentum ratio
     *
     * @param string $stage_id
     * @return string
     */
    private function updateStageOrJourney($recordId, $moduleName)
    {
        $bean = $this->getModuleBean($moduleName, $recordId);

        if (!empty($bean) && !empty($bean->id)) {
            $score = 0;
            $points = 0;

            if ($moduleName === 'DRI_SubWorkflows') {
                $bean->reloadActivities();
                //Get all the activities which are related to this stage
                foreach ($bean->getActivities() as $activity) {
                    $score += $activity->cj_momentum_score;
                    $points += $activity->cj_momentum_points;
                }
            } elseif ($moduleName === 'DRI_Workflows') {
                //Get all the stages which are related to this journey
                foreach ($bean->getStages() as $stage) {
                    $score += $stage->momentum_score;
                    $points += $stage->momentum_points;
                }
            }
            $ratio = $points > 0 ? $score / $points : 1;

            if ($ratio !== (float)$bean->momentum_ratio ||
                $score !== (int)$bean->momentum_score ||
                $points !== (int)$bean->momentum_points) {
                $GLOBALS['log']->debug(sprintf(
                    "Updating $moduleName with ratio %s (%s) and score %s (%s) of points %s (%s) id=%s",
                    $ratio,
                    (float)$bean->momentum_ratio,
                    $score,
                    (int)$bean->momentum_score,
                    $points,
                    (int)$bean->momentum_points,
                    $bean->id
                ));

                $this->updateScoresAndRatio($bean->getTableName(), $score, $ratio, $points, $bean->id);

                if ($moduleName === 'DRI_Workflows') {
                    $this->updateScoresAndRatio($bean->getTableName(), $score, $ratio, $points, $bean->id);
                } else {
                    return $bean->dri_workflow_id;
                }
            }
        }
    }

    /**
     * Return the bean object
     *
     * @param string $module
     * @param string $id
     * @return object
     */
    private function getModuleBean($module, $id)
    {
        if (empty($module) || empty($id)) {
            return;
        }

        return \BeanFactory::retrieveBean($module, $id, ['use_cache' => false]);
    }

    /**
     * Update CJ Momentum Scores and CJ Momentum Ratio
     *
     * @param string $table
     * @param string $score
     * @param string $ratio
     * @param string $points
     * @param string $id
     */
    private function updateScoresAndRatio($table, $score, $ratio, $points, $id)
    {
        if (empty($table) || empty($id)) {
            return;
        }
        $query = 'UPDATE ' . $table . ' SET cj_momentum_ratio = ? , cj_momentum_score = ? , momentum_points = ? WHERE id = ?';
        $conn = $GLOBALS['db']->getConnection();
        $stmt = $conn->executeQuery($query, ["$ratio", "$score", "$points", "$id"]);
    }

    /**
     * This function updates activity, stage and journey momenum
     */
    private function updateJourneyMomentum($module_name, $activity)
    {
        $dri_subworkflow_id = $this->updateActivity($module_name, $activity['id']);

        if (!empty($dri_subworkflow_id)) {
            $dri_workflow_id = $this->updateStageOrJourney($dri_subworkflow_id, 'DRI_SubWorkflows');

            if (!empty($dri_workflow_id)) {
                $this->updateStageOrJourney($dri_workflow_id, 'DRI_Workflows');
            }
        }
    }
}

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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;
use Sugarcrm\Sugarcrm\CustomerJourney\Exception as CJException;

/**
 * This class here to provide functions for the
 * momentum processing on Activities
 */
class MomentumHelper
{
    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\parentHelper
     */
    private $parentHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityHelper
     */
    private $activityHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\childActivityHelper
     */
    private $childActivityHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\statusHelper
     */
    private $statusHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\stageHelper
     */
    private $stageHelper;

    /**
     * @var Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\activityDatesHelper
     */
    private $activityDatesHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parentHelper = new ParentHelper();
        $this->activityHelper = ActivityHelper::getInstance();
        $this->childActivityHelper = new ChildActivityHelper();
        $this->statusHelper = new StatusHelper();
        $this->stageHelper = new StageHelper();
        $this->activityDatesHelper = new ActivityDatesHelper();
    }

    /**
     * Get the momentum start date from the parent field
     *
     * @param \SugarBean $activity
     * @param \DRI_Workflow_Task_Template $template
     * @return array
     */
    public function getMomentumStartDateFromParentField(\SugarBean $activity, \DRI_Workflow_Task_Template $template)
    {
        $stage = $this->stageHelper->getStage($activity);

        try {
            $parent = $stage->getParent($template->momentum_start_module);
        } catch (CJException\CustomerJourneyException $e) {
            return null;
        }

        $def = $parent->getFieldDefinition($template->momentum_start_field);
        $value = $parent->{$template->momentum_start_field};

        return $this->activityDatesHelper->formatGivenDate($def['type'], $value);
    }

    /**
     * Calculate the Momentum
     *
     * @param \SugarBean $activity
     */
    public function calculateMomentum(\SugarBean $activity)
    {
        $timeDate = \TimeDate::getInstance();
        $handler = ActivityHandlerFactory::factory($activity->module_dir);

        if ($this->statusHelper->isStatusChanged($activity) &&
            $this->statusHelper->isCompleted($activity) &&
            empty($activity->cj_momentum_end_date)) {
            $activity->cj_momentum_end_date = $timeDate->asDb($timeDate->getNow());
        } elseif (!$this->statusHelper->isCompleted($activity)) {
            $activity->cj_momentum_end_date = '';
        }

        $template = $handler->getActivityTemplate($activity);

        //If it is parent activity and new one.
        if (($this->parentHelper->isParent($activity) && empty($activity->cj_momentum_points)) ||
            empty($activity->cj_momentum_start_date)) {
            $activity->cj_momentum_points = $template->momentum_points;
            $activity->cj_momentum_score = $template->momentum_points;
            $activity->cj_momentum_ratio = 1;
        } elseif (!empty($activity->cj_momentum_start_date) && !$this->parentHelper->isParent($activity)) {
            //For child activity having momentum
            $activity->cj_momentum_points = $template->momentum_points;
            $startDate = $timeDate->fromDb($activity->cj_momentum_start_date);
            $endDate = $timeDate->fromDb($activity->cj_momentum_end_date);

            if (empty($activity->cj_momentum_end_date)) {
                $currentDate = $timeDate->nowDb();
                $currentDate2 = $timeDate->fromDb($currentDate);
                $diff = $startDate->diff($currentDate2);
            } else {
                $diff = $startDate->diff($endDate);
            }

            $actualHours = $diff->days * 24 + $diff->h + ($diff->i / 60);
            $dueDays = !empty($template->momentum_due_days) ? (int)$template->momentum_due_days : 0;
            $expectedHours = !empty($template->momentum_due_hours) ? (int)$template->momentum_due_hours : 0;
            $expectedHours += $dueDays * 24;

            if ($actualHours > 0) {
                $ratio = $expectedHours / $actualHours;
            } else {
                $ratio = $expectedHours === 0 && $actualHours !== 0 ? 0 : 1;
            }

            $activity->cj_momentum_ratio = $ratio >= 1 ? 1 : ($ratio < 0 ? 0 : $ratio);
            $activity->cj_momentum_score = round($template->momentum_points * $activity->cj_momentum_ratio);
        }
    }

    /**
     * Check if activity has momentum
     *
     * @param \SugarBean $activity
     * @return boolean
     */
    public function hasMomentum(\SugarBean $activity)
    {
        if (!$this->activityHelper->hasActivityTemplate($activity)) {
            return false;
        }

        $template = $this->activityHelper->getActivityTemplate($activity);
        return !empty($template->momentum_start_type);
    }

    /**
     * Set momentum start Date from the parent field
     *
     * @param \SugarBean $activity
     */
    public function setMomentumStartDateFromParentField(\SugarBean $activity)
    {
        if (!$this->activityHelper->hasActivityTemplate($activity)) {
            return;
        }

        $timeDate = \TimeDate::getInstance();
        $template = $this->activityHelper->getActivityTemplate($activity);
        $date = $this->getMomentumStartDateFromParentField($activity, $template);

        if (!empty($date)) {
            $activity->cj_momentum_start_date = $timeDate->asUser($date);
        }
    }

    /**
     * Retrieves the momentum points from a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function getMomentumPoints(\SugarBean $activity)
    {
        return (int)$activity->cj_momentum_points;
    }

    /**
     * Retrieves the mometum score from a activity
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function getMomentumScore(\SugarBean $activity)
    {
        return (int)$activity->cj_momentum_score;
    }

    /**
     * Set the Momentum points
     *
     * @param \SugarBean $activity
     * @pram int $points
     */
    public function setMomentumPoints(\SugarBean $activity, $points)
    {
        $activity->cj_momentum_points = $points;
    }

    /**
     * Calculate the momentum points
     *
     * @param \SugarBean $activity
     * @return int
     */
    public function calculateMomentumPoints(\SugarBean $activity)
    {
        $momentum_points = 0;

        if ($this->parentHelper->isParent($activity)) {
            foreach ($this->childActivityHelper->getChildren($activity) as $child) {
                $momentum_points += $this->getMomentumPoints($child);
            }
        }

        return $momentum_points;
    }
}

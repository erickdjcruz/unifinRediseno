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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\ActivityHandlerFactory;

class StateCalculator
{
    /**
     * @var \DRI_SubWorkflow
     */
    private $stage;

    /**
     * @var \SugarBean[]
     */
    private $activities;

    /**
     * StateCalculator constructor.
     * @param \DRI_SubWorkflow $stage
     */
    public function __construct(\DRI_SubWorkflow $stage)
    {
        $this->stage = $stage;
    }

    /**
     * Load the activities against a stage
     */
    public function load()
    {
        if (is_null($this->activities)) {
            $this->activities = $this->stage->getActivities();
        }
    }

    /**
     * Calculate the state
     */
    public function calculate()
    {
        // if stage is already cancelled then don't calculate it's stage
        if ($this->stage->state !== \DRI_SubWorkflow::STATE_CANCELLED) {
            $this->stage->state = $this->getState();
        }
    }

    /**
     * Check if state is changed or not
     *
     * @return bool
     */
    public function isStateChanged()
    {
        return $this->stage->isFieldChanged('state');
    }

    /**
     * Get the State
     *
     * @return string
     */
    public function getState()
    {
        $this->load();

        $this->activities = (array)$this->activities;
        $count = safeCount($this->activities);
        $notStarted = 0;
        $completed = 0;
        $cancelled = 0;

        foreach ($this->activities as $activity) {
            if ($activity->deleted) {
                $count--;
            }
        }

        if ($count === 0) {
            return $this->noActivities();
        }

        foreach ($this->activities as $activity) {
            $activityHandler = ActivityHandlerFactory::factory($activity->module_dir);

            if ($activityHandler->isNotStarted($activity)) {
                $notStarted++;
            } elseif ($activityHandler->isCompleted($activity)) {
                $completed++;
            }
        }

        if ($completed === $count) {
            return $this->completed();
        }

        if ($notStarted === $count) {
            return $this->notStarted();
        }

        if ($this->stage->hasStartedLaterStages()) {
            return \DRI_SubWorkflow::STATE_NOT_COMPLETED;
        }

        return \DRI_SubWorkflow::STATE_IN_PROGRESS;
    }

    /**
     * Check if there are activities or not
     * against a stage
     *
     * @return string
     */
    protected function noActivities()
    {
        if ($this->stage->isLastStage()) {
            if ($this->stage->isAllPreviousStagesCompleted()) {
                return \DRI_SubWorkflow::STATE_COMPLETED;
            }

            return \DRI_SubWorkflow::STATE_NOT_STARTED;
        }

        if ($this->stage->isNextStageStarted()) {
            if ($this->stage->isAllPreviousStagesCompleted()) {
                return \DRI_SubWorkflow::STATE_COMPLETED;
            }

            return \DRI_SubWorkflow::STATE_NOT_COMPLETED;
        }

        if ($this->stage->isFirstStage()) {
            return \DRI_SubWorkflow::STATE_IN_PROGRESS;
        }

        if ($this->stage->isAllPreviousStagesCompleted()) {
            return \DRI_SubWorkflow::STATE_COMPLETED;
        }

        return \DRI_SubWorkflow::STATE_NOT_STARTED;
    }

    /**
     * Check if any stage has been started
     *
     * @return string
     */
    protected function notStarted()
    {
        if ($this->stage->hasStartedLaterStages()) {
            return \DRI_SubWorkflow::STATE_NOT_COMPLETED;
        }

        if ($this->stage->isAllPreviousStagesCompleted() || $this->stage->isFirstStage()) {
            return \DRI_SubWorkflow::STATE_IN_PROGRESS;
        }

        return \DRI_SubWorkflow::STATE_NOT_STARTED;
    }

    /**
     * Check if it is completed
     *
     * @return string
     */
    protected function completed()
    {
        return \DRI_SubWorkflow::STATE_COMPLETED;
    }
}

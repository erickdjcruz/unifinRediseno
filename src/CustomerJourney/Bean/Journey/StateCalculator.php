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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Journey;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage\StateCalculator as StageStateCalculator;

class StateCalculator
{
    /**
     * @var \DRI_Workflow
     */
    private $journey;

    /**
     * @var \DRI_SubWorkflow[]
     */
    private $stages;

    /**
     * StateCalculator constructor.
     * @param \DRI_Workflow $journey
     */
    public function __construct(\DRI_Workflow $journey)
    {
        $this->journey = $journey;
    }

    /**
     * @param \DRI_SubWorkflow[] $stages
     */
    public function setStages(array $stages)
    {
        $this->stages = $stages;
    }

    /**
     * Load the stages against journey
     */
    public function load()
    {
        if (is_null($this->stages)) {
            $this->stages = $this->journey->getStages();
        }
    }

    /**
     * Calculate the state
     *
     * @param bool $save
     */
    public function calculate($save = true)
    {
        // Since the sub workflows are dependant on each other both forward and backwards in the chain,
        // we need to calculate the state twice in order to set the states properly.
        // This is not a optimal solution though but works for now.
        $this->calculateStageStates($save);
        $this->calculateStageStates($save);

        if ($this->journey->state === \DRI_SubWorkflow::STATE_CANCELLED) {
            return;
        }
        $this->journey->state = $this->getState();
    }

    /**
     * Calculate the Stage's state
     *
     * @param bool $save
     */
    public function calculateStageStates($save = true)
    {
        $this->load();

        foreach ($this->stages as $stage) {
            $calculator = $this->calculateStageState($stage);

            if ($save && $calculator->isStateChanged()) {
                $stage->save();
            }
        }
    }

    /**
     * @param $stage
     * @return \DRI_SubWorkflows\StateCalculator
     */
    private function calculateStageState($stage)
    {
        $calculator = new StageStateCalculator($stage);
        $calculator->calculate();

        return $calculator;
    }

    /**
     * @return string
     */
    public function getState()
    {
        $this->load();

        $count = safeCount((array)$this->stages);
        $notStarted = 0;
        $completed = 0;
        $deferred = 0;

        foreach ($this->stages as $stage) {
            switch ($stage->state) {
                case \DRI_SubWorkflow::STATE_COMPLETED:
                    $completed++;
                    break;
                case \DRI_SubWorkflow::STATE_NOT_STARTED:
                    $notStarted++;
                    break;
                case \DRI_SubWorkflow::STATE_CANCELLED:
                    $deferred++;
                    break;
            }
        }

        if ($completed === $count) {
            return \DRI_Workflow::STATE_COMPLETED;
        }

        if ($notStarted === $count) {
            return \DRI_Workflow::STATE_NOT_STARTED;
        }

        if ($deferred + $completed === $count) {
            return \DRI_Workflow::STATE_CANCELLED;
        }

        return \DRI_Workflow::STATE_IN_PROGRESS;
    }
}

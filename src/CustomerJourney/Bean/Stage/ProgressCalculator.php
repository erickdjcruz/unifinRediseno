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

class ProgressCalculator
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
     * @param \DRI_SubWorkflow $subWorkflow
     */
    public function __construct(\DRI_SubWorkflow $subWorkflow)
    {
        $this->stage = $subWorkflow;
    }

    /**
     * Load the actitives against a stage
     */
    public function load()
    {
        if (is_null($this->activities)) {
            $this->activities = $this->stage->getActivities();
        }
    }

    /**
     * Calculate the progress
     * @return array
     */
    public function calculate()
    {
        $this->load();

        $this->activities = (array)$this->activities;
        $count = safeCount($this->activities);

        $this->stage->points = 0;
        $this->stage->score = 0;

        foreach ($this->activities as $activity) {
            if ($activity->deleted) {
                $count--;
            }
        }

        if ($count === 0) {
            $this->stage->progress = $this->stage->state === \DRI_SubWorkflow::STATE_COMPLETED ? 1 : 0;
            return [0, 0];
        }

        foreach ($this->activities as $activity) {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);
            $this->stage->points += $handler->getPoints($activity);
            $this->stage->score += $handler->getScore($activity);
        }

        $this->stage->progress = $this->stage->points > 0 ? $this->stage->score / $this->stage->points : 0;

        return [$this->stage->score, $this->stage->points];
    }

    /**
     * Check if progress is changed or not
     *
     * @return bool
     */
    public function isProgressChanged()
    {
        $isProgressChanged = false;
        $fieldsToBeCheck = ['progress', 'score', 'points'];
        foreach ($fieldsToBeCheck as $field) {
            if ($this->stage->isFieldChanged($field)) {
                $isProgressChanged = true;
                break;
            }
        }
        return $isProgressChanged;
    }
}

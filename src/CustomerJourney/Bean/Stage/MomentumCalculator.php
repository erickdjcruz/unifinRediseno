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

class MomentumCalculator
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
     * Load the activities against a stage
     */
    public function load()
    {
        if (is_null($this->activities)) {
            $this->activities = $this->stage->getActivities();
        }
    }

    /**
     * Calculate the Momentum
     *
     * @return array
     */
    public function calculate()
    {
        $this->load();

        $this->activities = (array)$this->activities;
        $count = safeCount($this->activities);

        $this->stage->momentum_points = 0;
        $this->stage->momentum_score = 0;

        foreach ($this->activities as $activity) {
            if ($activity->deleted) {
                $count--;
            }
        }

        foreach ($this->activities as $activity) {
            $handler = ActivityHandlerFactory::factory($activity->module_dir);
            $this->stage->momentum_points += $handler->getMomentumPoints($activity);
            $this->stage->momentum_score += $handler->getMomentumScore($activity);
        }

        $this->stage->momentum_ratio = $this->stage->momentum_points > 0 ? $this->stage->momentum_score / $this->stage->momentum_points : 1;

        return [$this->stage->momentum_score, $this->stage->momentum_points];
    }

    /**
     * Check if momentum is changed or not
     *
     * @return bool
     */
    public function isMomentumChanged()
    {
        $isMomentumChanged = false;
        $fieldsToBeCheck = ['momentum_points', 'momentum_score', 'momentum_ratio'];
        foreach ($fieldsToBeCheck as $field) {
            if ($this->stage->isFieldChanged($field)) {
                $isMomentumChanged = true;
                break;
            }
        }
        return $isMomentumChanged;
    }
}

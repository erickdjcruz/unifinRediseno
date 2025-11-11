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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage\MomentumCalculator as StagesMomentumCalculator;

class MomentumCalculator
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
     * Load the stage against the Journey
     */
    public function load()
    {
        if (is_null($this->stages)) {
            $this->stages = $this->journey->getStages();
        }
    }

    /**
     * Calculate the Momentum
     *
     * @param bool $save
     * @throws \NotFoundException
     * @throws \SugarApiExceptionError
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function calculate($save = true)
    {
        $this->load();

        $this->journey->momentum_score = 0;
        $this->journey->momentum_points = 0;

        foreach ($this->stages as $stage) {
            $calculator = new StagesMomentumCalculator($stage);

            [$score, $total] = $calculator->calculate();

            if ($save && $calculator->isMomentumChanged()) {
                $stage->save();
            }

            $this->journey->momentum_score += $score;
            $this->journey->momentum_points += $total;
        }

        $this->journey->momentum_ratio = $this->journey->momentum_points > 0 ? $this->journey->momentum_score / $this->journey->momentum_points : 1;
    }
}

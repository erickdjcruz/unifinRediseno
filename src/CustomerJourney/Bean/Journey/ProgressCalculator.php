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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Stage\ProgressCalculator as StageProgressCalculator;

class ProgressCalculator
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
     * load the stages against journey
     */
    public function load()
    {
        if (is_null($this->stages)) {
            $this->stages = $this->journey->getStages();
        }
    }

    /**
     * Calculate the Progress
     *
     * @param bool $save
     * @throws \NotFoundException
     * @throws \SugarApiExceptionError
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function calculate($save = true)
    {
        $this->load();

        $this->journey->score = 0;
        $this->journey->points = 0;

        foreach ($this->stages as $stage) {
            $calculator = new StageProgressCalculator($stage);

            [$score, $total] = $calculator->calculate();

            if ($save && $calculator->isProgressChanged()) {
                $stage->save();
            }

            $this->journey->score += $score;
            $this->journey->points += $total;
        }

        $this->journey->progress = $this->journey->points > 0 ? floor(($this->journey->score / $this->journey->points) * 100) / 100 : 1;
    }
}

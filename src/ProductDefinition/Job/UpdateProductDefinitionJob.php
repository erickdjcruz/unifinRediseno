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

namespace Sugarcrm\Sugarcrm\ProductDefinition\Job;

use RunnableSchedulerJob;
use SchedulersJob;
use Sugarcrm\Sugarcrm\ProductDefinition\Config\Config;

/**
 * Update product definition job
 */
class UpdateProductDefinitionJob implements RunnableSchedulerJob
{
    /**
     * @var SchedulersJob
     */
    private $job;

    /**
     * @var Config
     */
    private $productDefinitionConfig;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productDefinitionConfig = new Config(\SugarConfig::getInstance());
    }

    /**
     * setter
     * @param Config $productDefinitionConfig
     * @return UpdateProductDefinitionJob
     */
    public function setProductDefinitionConfig(Config $productDefinitionConfig): UpdateProductDefinitionJob
    {
        $this->productDefinitionConfig = $productDefinitionConfig;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * update product definition
     * @param $data
     * @return bool
     */
    public function run($data)
    {
        $result = $this->productDefinitionConfig->updateProductDefinition();
        if ($result) {
            return $this->job->succeedJob('Successfully update product definition');
        }
        return $this->job->failJob('Can not update update product definition');
    }
}

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

namespace Sugarcrm\Sugarcrm\SugarConnect\Configuration;

/**
 * @deprecated Will be removed in the next release.
 */
trait ConfigurationAwareTrait
{
    /**
     * The SugarConnect configuration.
     *
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * Sets the publisher's configuration.
     *
     * @param ConfigurationInterface $config The SugarConnect configuration.
     *
     * @return void
     */
    public function setConfiguration(ConfigurationInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * Returns the publisher's configuration.
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->config;
    }
}

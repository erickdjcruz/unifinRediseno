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

namespace Sugarcrm\Sugarcrm\SugarConnect\LogicHooks;

use Sugarcrm\Sugarcrm\SugarConnect\Publisher;
use Sugarcrm\Sugarcrm\SugarConnect\Bean\SugarBean;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\ConfigurationAwareInterface;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\ConfigurationAwareTrait;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\Locator;

/**
 * @deprecated Will be removed in the next release.
 */
final class Handler implements Publisher, ConfigurationAwareInterface
{
    use ConfigurationAwareTrait;

    /**
     * This is the entry point for logic hooks.
     */
    public function __construct()
    {
        // The configuration dependency can't be injected because Logic Hooks
        // weren't designed with constructor or setter injection features.
        $config = Locator::get();
        $this->setConfiguration($config);
    }

    /**
     * Triggered by logic hooks to begin publishing bean changes to the Sugar
     * Connect webhook.
     *
     * @param \SugarBean $bean The bean that was saved.
     * @param string $event The event type.
     * @param array $args Additional arguments.
     *
     * @return void
     */
    public function publish(\SugarBean $bean, string $event, array $args): void
    {
        $config = $this->getConfiguration();

        // Stop before you start.
        if (!$config->isEnabled()) {
            return;
        }

        // Don't let any exceptions bubble up.
        try {
            SugarBean::getInstance($config, $bean)->publish($bean, $event, $args);
        } catch (\Exception $e) {
            $log = \LoggerManager::getLogger();
            $log->fatal("sugar connect: logic hooks: {$e->getMessage()}");
        }
    }
}

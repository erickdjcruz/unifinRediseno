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

namespace Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Console;

use Sugarcrm\Sugarcrm\Console\CommandRegistry\Mode\InstanceModeInterface;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\TeamsetPruner;
use Symfony\Component\Console\Command\Command as Command;
use Symfony\Component\Console\Output\OutputInterface;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\State;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;

abstract class TeamSetPruneCommand extends Command implements InstanceModeInterface
{
    protected function configure()
    {
    }

    /**
     * Check the config table for denormalization being enabled, the table being available and rebuild is not running.
     *
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function preflightCheck(OutputInterface $output)
    {
        $state = Container::getInstance()->get(State::class);

        if ($state->isEnabled()) {
            if (!$state->isAvailable()) {
                $msg = 'The Team Security Denormalization table is not available. It may not exist. '
                    . 'You can create it with the team-security:rebuild command.';
                throw new \Exception($msg);
            }

            if ($state->isRebuildRunning()) {
                $msg = 'The Team Security Denormalization table is currently being rebuilt. '
                    . 'We cannot prune until the rebuild is complete';
                throw new \Exception($msg);
            }
        }
    }

    /**
     * @return TeamsetPruner
     */
    protected function getPruner(): TeamsetPruner
    {
        return new TeamsetPruner();
    }
}
